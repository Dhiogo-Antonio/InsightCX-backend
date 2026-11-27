<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\AnaliseChatbot;
use App\Services\AnalisadorFeedback;
use App\Services\AnaliseFeedbackIA;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Listar feedbacks.
     * Administrador vê todos.
     * Cliente vê somente os próprios.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Feedback::with([
            'usuario:id,nome,email',
            'analisesChatbot' => function ($query) {
                $query->orderByDesc('created_at');
            }
        ])->orderByDesc('created_at');

        if ($usuario->tipo !== 'admin') {
            $query->where('id_usuario', $usuario->id);
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Cadastrar feedback e gerar as duas respostas da IA:
     * 1. Resposta ao cliente.
     * 2. Análise gerencial para o administrador.
     */
    public function store(
        Request $request,
        AnalisadorFeedback $analisador,
        ChatbotService $chatbot,
        AnaliseFeedbackIA $analiseIA
    ) {
        $usuario = $request->user();

        $regras = [
            'servico' => 'required|string|max:255',
            'assunto' => 'required|string|max:255',
            'comentario' => 'required|string',
            'categoria' => 'required|in:elogio,reclamacao',
        ];

        if ($usuario->tipo === 'admin') {
            $regras['id_usuario'] = 'required|exists:usuarios,id';
            $regras['status'] = 'sometimes|in:pendente,em analise,resolvido';
        }

        $dados = $request->validate($regras);

        if ($usuario->tipo !== 'admin') {
            $dados['id_usuario'] = $usuario->id;
        }

        $dados['status'] = $dados['status'] ?? 'pendente';
        $dados['prioridade'] = 'media';

        // 1. Salvar o feedback.
        $feedback = Feedback::create($dados);

        // 2. Análise básica, utilizada como alternativa
        // caso a análise inteligente falhe.
        $resultadoBase = $analisador->analisar($feedback);

        // 3. Análise gerencial da IA.
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
                'resumo_gerencial' => $resultadoIA['resumo_gerencial'],
            ];

        } catch (\Throwable $e) {
            Log::error('Erro na análise gerencial da IA', [
                'feedback_id' => $feedback->id,
                'erro' => $e->getMessage()
            ]);

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
                'resumo_gerencial' => 'A análise inteligente não ficou disponível. É necessária avaliação manual.'
            ];
        }

        // 4. Gerar uma resposta para o cliente,
        // independentemente de ser elogio ou reclamação.
        try {
            $respostaIA = $chatbot->responder(
                $feedback,
                $resultado['prioridade_identificada']
            );

            $respostaIA = trim((string) $respostaIA);

            if ($respostaIA === '') {
                $respostaIA = null;
            }

        } catch (\Throwable $e) {
            Log::error('Erro ao gerar resposta para o cliente', [
                'feedback_id' => $feedback->id,
                'erro' => $e->getMessage()
            ]);

            $respostaIA = null;
        }

        // 5. Definir os status da resposta.
        $respostaGerada = !empty($respostaIA);

        $prioridadeAlta =
            $resultado['prioridade_identificada'] === 'alta';

        if ($respostaGerada) {
            $statusAprovacao = 'aprovada';

            // Mesmo quando é alta, a resposta fica disponível
            // para o cliente, mas o caso exige acompanhamento humano.
            $statusAtendimento = $prioridadeAlta
                ? 'em_atendimento'
                : 'respondido_automaticamente';

            $tipoAtendimento = 'automatico';
            $respostaEnviadaEm = now();

        } else {
            $statusAprovacao = 'nao_gerada';
            $statusAtendimento = 'aguardando_administrador';
            $tipoAtendimento = 'manual';
            $respostaEnviadaEm = null;
        }

        // 6. Salvar análise gerencial e resposta ao cliente.
        $analise = AnaliseChatbot::create([
            'id_feedback' => $feedback->id,

            'analise' => $resultado['analise'],
            'palavras_chave' => $resultado['palavras_chave'],
            'prioridade_identificada' => $resultado['prioridade_identificada'],

            'tipo_atendimento' => $tipoAtendimento,
            'resposta_gerada' => $respostaIA,
            'resposta_administrador' => null,

            'status_atendimento' => $statusAtendimento,
            'resposta_enviada_em' => $respostaEnviadaEm,
            'respondido_administrador_em' => null,

            'status_aprovacao' => $statusAprovacao,
            'avaliado_por' => null,
            'avaliado_em' => null,
            'motivo_recusa' => null,

            'sentimento' => $resultado['sentimento'],
            'aspectos_positivos' => $resultado['aspectos_positivos'],
            'aspectos_negativos' => $resultado['aspectos_negativos'],
            'problemas_identificados' => $resultado['problemas_identificados'],
            'recomendacoes' => $resultado['recomendacoes'],
            'confianca_analise' => $resultado['confianca_analise'],
            'resumo_gerencial' => $resultado['resumo_gerencial'],
        ]);

        // 7. Atualizar prioridade do feedback.
        $feedback->update([
            'prioridade' => $resultado['prioridade_identificada']
        ]);

        return response()->json([
            'message' => $respostaGerada
                ? 'Feedback analisado e resposta automática disponibilizada ao cliente.'
                : 'Feedback analisado, mas a resposta automática não foi gerada. O administrador deverá responder.',

            'feedback' => $feedback->fresh([
                'usuario',
                'analisesChatbot'
            ]),

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

        $feedback->load([
            'usuario:id,nome,email',
            'analisesChatbot'
        ]);

        return response()->json($feedback, 200);
    }

    /**
     * Atualizar feedback.
     */
    public function update(Request $request, Feedback $feedback)
    {
        $usuario = $request->user();

        if (
            $usuario->tipo !== 'admin' &&
            $feedback->id_usuario != $usuario->id
        ) {
            return response()->json([
                'message' => 'Você não tem permissão para alterar este feedback.'
            ], 403);
        }

        if ($usuario->tipo === 'admin') {
            $dados = $request->validate([
                'id_usuario' => 'sometimes|required|exists:usuarios,id',
                'servico' => 'sometimes|required|string|max:255',
                'assunto' => 'sometimes|required|string|max:255',
                'comentario' => 'sometimes|required|string',
                'categoria' => 'sometimes|required|in:elogio,reclamacao',
                'prioridade' => 'sometimes|required|in:baixa,media,alta',
                'status' => 'sometimes|required|in:pendente,em analise,resolvido',
            ]);

            $feedback->update($dados);

            return response()->json([
                'message' => 'Feedback atualizado com sucesso!',
                'feedback' => $feedback->fresh([
                    'usuario',
                    'analisesChatbot'
                ])
            ], 200);
        }
    }

    /**
     * Excluir feedback.
     */
    public function destroy(Request $request, Feedback $feedback)
    {
        if ($request->user()->tipo !== 'admin') {
            return response()->json([
                'message' => 'Somente administradores podem excluir feedbacks.'
            ], 403);
        }

        $feedback->delete();

        return response()->json([
            'message' => 'Feedback excluído com sucesso!'
        ], 200);
    }

    /**
     * Registrar uma resposta manual do administrador.
     */
    
/**
 * Registrar uma resposta manual do administrador.
 */
public function responder(Request $request, AnaliseChatbot $analise)
{
    if ($request->user()->tipo !== 'admin') {
        return response()->json([
            'message' => 'Apenas administradores podem responder aos feedbacks.'
        ], 403);
    }

    if (!in_array($analise->status_aprovacao, ['recusada', 'nao_gerada'])) {
        return response()->json([
            'message' => 'A resposta manual não está disponível para esta análise.'
        ], 422);
    }

    $dados = $request->validate([
        'resposta_administrador' => 'required|string|min:3|max:5000'
    ]);

    $analise->update([
        'resposta_administrador' => $dados['resposta_administrador'],
        'respondido_administrador_em' => now(),
        'resposta_enviada_em' => now(),
        'status_atendimento' => 'finalizado'
    ]);

    return response()->json([
        'message' => 'Resposta manual registrada com sucesso!',
        'analise_chatbot' => $analise->fresh()
    ], 200);
}


    /**
     * Aprovar uma resposta da IA.
     * Mantido para respostas antigas que ainda estejam aguardando.
     */
    public function aprovar(Request $request, AnaliseChatbot $analise)
    {
        if ($request->user()->tipo !== 'admin') {
            return response()->json([
                'message' => 'Apenas administradores podem aprovar respostas.'
            ], 403);
        }

        if (
            $analise->status_aprovacao !== 'aguardando' ||
            empty($analise->resposta_gerada)
        ) {
            return response()->json([
                'message' => 'Esta resposta não está disponível para aprovação.'
            ], 422);
        }

        $analise->update([
            'status_aprovacao' => 'aprovada',
            'avaliado_por' => $request->user()->id,
            'avaliado_em' => now(),
            'status_atendimento' => 'respondido_automaticamente',
            'resposta_enviada_em' => now()
        ]);

        return response()->json([
            'message' => 'Resposta da IA aprovada e liberada para o cliente!',
            'analise_chatbot' => $analise->fresh()
        ], 200);
    }

    /**
     * Recusar uma resposta da IA.
     */
    public function recusar(Request $request, AnaliseChatbot $analise)
    {
        if ($request->user()->tipo !== 'admin') {
            return response()->json([
                'message' => 'Apenas administradores podem recusar respostas.'
            ], 403);
        }

        if ($analise->status_aprovacao !== 'aguardando') {
            return response()->json([
                'message' => 'Esta resposta não está aguardando aprovação.'
            ], 422);
        }

        $dados = $request->validate([
            'motivo_recusa' => 'nullable|string|max:1000'
        ]);

        $analise->update([
            'status_aprovacao' => 'recusada',
            'avaliado_por' => $request->user()->id,
            'avaliado_em' => now(),
            'motivo_recusa' => $dados['motivo_recusa'] ?? null,
            'status_atendimento' => 'em_atendimento'
        ]);

        return response()->json([
            'message' => 'Resposta recusada. O administrador poderá escrever uma resposta manual.',
            'analise_chatbot' => $analise->fresh()
        ], 200);
    }
}
