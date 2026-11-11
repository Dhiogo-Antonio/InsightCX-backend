<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Models\Servico;


class FeedbackController extends Controller
{
    /**
     * Listar feedbacks.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Feedback::query();

        if ($usuario->tipo !== 'admin') {
            $query->where('id_usuario', $usuario->id);
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Cadastrar feedback.
     */

    public function store(Request $request)
    {
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

        // Busca o serviço pelo model.
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

        $feedback = Feedback::create($validatedData);

        return response()->json([
            'message' => 'Feedback criado com sucesso!',
            'feedback' => $feedback
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
}
