<?php

namespace App\Http\Controllers;

use App\Models\Sugestao;
use Illuminate\Http\Request;

class SugestaoController extends Controller
{
    /**
     * Listar sugestões.
     * Administrador vê todas.
     * Demais usuários veem as sugestões da própria empresa.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Sugestao::orderByDesc('created_at');

        if ($usuario->tipo !== 'admin') {
            $query->where('id_empresa', $usuario->id_empresa);
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Cadastrar sugestão.
     */
    public function store(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->tipo !== 'admin') {
            return response()->json([
                'mensagem' => 'Você não tem permissão para cadastrar sugestões.'
            ], 403);
        }

        $dados = $request->validate([
            'id_empresa' => 'required|exists:empresas,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'status' => 'nullable|in:pendente,em andamento,concluida'
        ]);

        $dados['status'] = $dados['status'] ?? 'pendente';

        $sugestao = Sugestao::create($dados);

        return response()->json([
            'mensagem' => 'Sugestão cadastrada com sucesso!',
            'sugestao' => $sugestao
        ], 201);
    }

    /**
     * Exibir uma sugestão específica.
     */
    public function show(Request $request, Sugestao $sugestao)
    {
        $usuario = $request->user();

        if (
            $usuario->tipo !== 'admin' &&
            $sugestao->id_empresa != $usuario->id_empresa
        ) {
            return response()->json([
                'mensagem' => 'Você não tem permissão para visualizar esta sugestão.'
            ], 403);
        }

        return response()->json($sugestao, 200);
    }

    /**
     * Atualizar sugestão.
     */
    public function update(Request $request, Sugestao $sugestao)
    {
        $usuario = $request->user();

        if ($usuario->tipo !== 'admin') {
            return response()->json([
                'mensagem' => 'Você não tem permissão para atualizar sugestões.'
            ], 403);
        }

        $dados = $request->validate([
            'id_empresa' => 'sometimes|required|exists:empresas,id',
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:pendente,em andamento,concluida'
        ]);

        $sugestao->update($dados);

        return response()->json([
            'mensagem' => 'Sugestão atualizada com sucesso!',
            'sugestao' => $sugestao->fresh()
        ], 200);
    }

    /**
     * Excluir sugestão.
     */
    public function destroy(Request $request, Sugestao $sugestao)
    {
        $usuario = $request->user();

        if ($usuario->tipo !== 'admin') {
            return response()->json([
                'mensagem' => 'Você não tem permissão para excluir sugestões.'
            ], 403);
        }

        $sugestao->delete();

        return response()->json([
            'mensagem' => 'Sugestão excluída com sucesso!'
        ], 200);
    }
}
