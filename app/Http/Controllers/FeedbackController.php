<?php

namespace App\Http\Controllers;

use App\Services\AnaliseFeedbackIA;
use App\Services\ChatbotService;
use Illuminate\Support\Facades\Log;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Models\Servico;
use App\Models\AnaliseChatbot;
use App\Services\AnalisadorFeedback;

class FeedbackController extends Controller
{
    /**
     * Listar feedbacks.
     */

    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Feedback::query()
            ->with([
                'servico:id,nome',
                'usuario:id,nome,email',
                'analisesChatbot' => function ($query) {
                    $query->orderByDesc('created_at');
                }
            ])
            ->orderByDesc('created_at');

        if ($usuario->tipo !== 'admin') {
            $query->where('id_usuario', $usuario->id);
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Cadastrar feedback.
     */



    
public function store(
    Request $request,
    AnalisadorFeedback $analisador,
    ChatbotService $chatbot,
    AnaliseFeedbackIA $analiseIA
) {
    $usuario = $request->user();

    $regras = [
        'id_servico' => 'required|exists:servicos,id',
        'assunto' => 'required|string|max:255',
        'comentario' => 'required|string',
        'categoria' => 'required|in:elogio,reclamacao',
    ];

    if ($usuario->tipo === 'admin') {
        $regras['id_usuario'] = 'required|exists:usuarios,id';
        $regras['status'] = 'sometimes|in:pendente,em analise,resolvido';
        $regras['prioridade'] = 'required|in:baixa,media,alta';
    } else {
        $regras['prioridade'] = 'sometimes|in:baixa,media,alta';
    }

    $validatedData = $request->validate($regras);

    $servico = Servico::findOrFail($validatedData['id_servico']);

    if (
        $usuario->tipo !== 'admin' &&
        $servico->id_empresa != $usuario->id_empresa
    ) {
        return response()->json([
            'message' => 'Você não pode registrar feedback para este serviço.'
        ], 403);
    }

    if ($usuario->tipo !== 'admin') {
        $validatedData['id_usuario'] = $usuario->id;
    }

    $validatedData['status'] = $validatedData['status'] ?? 'pendente';
    $validatedData['prioridade'] = $validatedData['prioridade'] ?? 'media';

    // 1. Salva o feedback no banco.
    $feedback = Feedback::create($validatedData);

    // 2. Faz uma análise inicial de segurança.
    // Se a OpenAI falhar, o sistema ainda terá uma classificação básica.
    $resultadoBase = $analisador->analisar($feedback);

    // 3. Tenta realizar a análise inteligente.
    try {
        $resultadoIA = $analiseIA->analisar($feedback);

        $resultado = [
            'analise' => $resultadoIA['analise'],
            'palavras_chave' => $resultadoIA['palavras_chave'],
            'prioridade_identificada' => $resultadoIA['prioridade'],
            'sentimento' => $resultadoIA['sentimento'],
            'aspectos_positivos' => $resultadoIA['aspectos_positivos'],
            'aspectos_negativos' => $resultadoIA['aspectos_negativos'],
            'problemas_identificados' => $resultadoIA['problemas_identificados'],
            'recomendacoes' => $resultadoIA['recomendacoes'],
            'confianca_analise' => $resultadoIA['confianca'],
            'resumo_gerencial' => $resultadoIA['resumo_gerencial']
        ];

        $iaDisponivel = true;

    } catch (\Throwable $e) {
        Log::error('Falha na análise inteligente do feedback', [
            'feedback_id' => $feedback->id,
            'erro' => $e->getMessage()
        ]);

        // Fallback: mantém a análise anterior por palavras-chave.
        $resultado = [
            'analise' => $resultadoBase['analise'],
            'palavras_chave' => $resultadoBase['palavras_chave'],
            'prioridade_identificada' => $resultadoBase['prioridade_identificada'],
            'sentimento' => null,
            'aspectos_positivos' => [],
            'aspectos_negativos' => [],
            'problemas_identificados' => [],
            'recomendacoes' => [],
            'confianca_analise' => null,
            'resumo_gerencial' => 'Análise inteligente indisponível. É necessária avaliação manual.'
        ];

        $iaDisponivel = false;
    }

    // 4. Gera uma resposta ao cliente, usando a prioridade identificada.
    try {
        $respostaIA = $chatbot->responder(
            $feedback,
            $resultado['prioridade_identificada']
        );
    } catch (\Throwable $e) {
        Log::error('Erro ao gerar resposta do chatbot', [
            'feedback_id' => $feedback->id,
            'erro' => $e->getMessage()
        ]);

        $respostaIA = null;
        $iaDisponivel = false;
    }

    // 5. Define se o atendimento precisa de intervenção humana.
    $encaminharAdministrador =
        $resultado['prioridade_identificada'] === 'alta'
        || !$iaDisponivel;

    $tipoAtendimento = $encaminharAdministrador
        ? 'manual'
        : 'automatico';

    $statusAtendimento = $encaminharAdministrador
        ? 'aguardando_administrador'
        : 'respondido_automaticamente';

    // 6. Salva todos os resultados da análise.
    $analise = AnaliseChatbot::create([
        'id_feedback' => $feedback->id,
        'analise' => $resultado['analise'],
        'palavras_chave' => $resultado['palavras_chave'],
        'prioridade_identificada' => $resultado['prioridade_identificada'],
        'tipo_atendimento' => $tipoAtendimento,
        'resposta_gerada' => $respostaIA,
        'status_atendimento' => $statusAtendimento,
        'resposta_enviada_em' => $respostaIA !== null ? now() : null,

        'sentimento' => $resultado['sentimento'],
        'aspectos_positivos' => $resultado['aspectos_positivos'],
        'aspectos_negativos' => $resultado['aspectos_negativos'],
        'problemas_identificados' => $resultado['problemas_identificados'],
        'recomendacoes' => $resultado['recomendacoes'],
        'confianca_analise' => $resultado['confianca_analise'],
        'resumo_gerencial' => $resultado['resumo_gerencial']
    ]);

    // 7. Atualiza a prioridade do feedback.
    $feedback->update([
        'prioridade' => $resultado['prioridade_identificada']
    ]);

    return response()->json([
        'message' => $iaDisponivel
            ? 'Feedback cadastrado e analisado com sucesso!'
            : 'Feedback cadastrado. Será necessária uma análise manual.',

        'feedback' => $feedback->fresh(),

        'analise_chatbot' => $analise
    ], 201);
}
    

    /**
     * Exibir um feedback específico.
     */
    public function show(Request $request, Feedback $feedback)
    {
        $usuario = $request->user();

        if (
            $usuario->tipo !== 'admin' &&
            $feedback->id_usuario != $usuario->id
        ) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar este feedback.'
            ], 403);
        }

        return response()->json($feedback, 200);
    }

    /**
     * Atualizar feedback.
     */
    public function update(Request $request, Feedback $feedback)
    {
        $usuario = $request->user();

        // Cliente só pode alterar o próprio feedback.
        if (
            $usuario->tipo !== 'admin' &&
            $feedback->id_usuario != $usuario->id
        ) {
            return response()->json([
                'message' => 'Você não tem permissão para alterar este feedback.'
            ], 403);
        }

        if ($usuario->tipo === 'admin') {
            $validatedData = $request->validate([
                'id_usuario' => 'sometimes|required|exists:usuarios,id',
                'id_servico' => 'sometimes|required|exists:servicos,id',
                'assunto' => 'sometimes|required|string|max:255',
                'comentario' => 'sometimes|required|string',
                'categoria' => 'sometimes|required|in:elogio,reclamacao',
                'prioridade' => 'sometimes|required|in:baixa,media,alta',
                'status' => 'sometimes|required|in:pendente,em analise,resolvido',
            ]);
        } else {
            // Cliente não pode alterar status, prioridade ou proprietário.
            $validatedData = $request->validate([
                'assunto' => 'sometimes|required|string|max:255',
                'comentario' => 'sometimes|required|string',
                'categoria' => 'sometimes|required|in:elogio,reclamacao',
            ]);
        }

        // Se o administrador trocar o serviço, verifica se ele existe.
        if (isset($validatedData['id_servico'])) {
            $servico = Servico::findOrFail($validatedData['id_servico']);

            if (
                $usuario->tipo !== 'admin' &&
                $servico->id_empresa != $usuario->id_empresa
            ) {
                return response()->json([
                    'message' => 'Este serviço não pertence à sua empresa.'
                ], 403);
            }
        }

        $feedback->update($validatedData);

        return response()->json([
            'message' => 'Feedback atualizado com sucesso!',
            'feedback' => $feedback->fresh()
        ], 200);
    }

    /**
     * Excluir feedback.
     */
    public function destroy(Request $request, Feedback $feedback)
    {
        $usuario = $request->user();

        if ($usuario->tipo !== 'admin') {
            return response()->json([
                'message' => 'Somente administradores podem excluir feedbacks.'
            ], 403);
        }

        $feedback->delete();

        return response()->json([
            'message' => 'Feedback excluído com sucesso!'
        ], 200);
    }



    public function responder(Request $request, AnaliseChatbot $analise)
    {
        // Verificar se o usuário é administrador
        if ($request->user()->tipo !== 'admin') {
            return response()->json([
                'message' => 'Apenas administradores podem responder aos feedbacks.'
            ], 403);
        }

        // Validar a resposta
        $dados = $request->validate([
            'resposta_administrador' => 'required|string|min:3|max:5000'
        ]);

        // Registrar a resposta do administrador
        $analise->update([
            'resposta_administrador' => $dados['resposta_administrador'],
            'respondido_administrador_em' => now(),
            'status_atendimento' => 'finalizado'
        ]);

        return response()->json([
            'message' => 'Resposta registrada com sucesso!',
            'analise_chatbot' => $analise->fresh()
        ], 200);
    }
}
