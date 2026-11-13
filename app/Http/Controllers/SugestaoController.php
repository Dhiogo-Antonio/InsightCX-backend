<?php

namespace App\Http\Controllers;

use App\Models\Sugestao;
use App\Models\Servico;
use Illuminate\Http\Request;

class SugestaoController extends Controller
{
    // Listar sugestões
    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Sugestao::with('servico:id,nome')
            ->orderByDesc('created_at');

        // Cliente visualiza somente sugestões da própria empresa
        if ($usuario->tipo !== 'admin') {
            $query->where('id_empresa', $usuario->id_empresa);
        }

        return response()->json($query->get(), 200);
    }

    // Cadastrar sugestão
    public function store(Request $request)
    {
        $usuario = $request->user();

        // Somente administrador pode cadastrar
        if ($usuario->tipo !== 'admin') {
            return response()->json([
                'mensagem' => 'Você não tem permissão para cadastrar sugestões.'
            ], 403);
        }

        $dados = $request->validate([
            'id_empresa' => 'required|exists:empresas,id',
            'id_servico' => 'required|exists:servicos,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'status' => 'nullable|in:pendente,em andamento,concluida'
        ]);

        // Verifica se o serviço pertence à empresa informada
        $servico = Servico::where('id', $dados['id_servico'])
            ->where('id_empresa', $dados['id_empresa'])
            ->first();

        if (!$servico) {
            return response()->json([
                'mensagem' => 'O serviço informado não pertence à empresa selecionada.'
            ], 422);
        }

        $dados['status'] = $dados['status'] ?? 'pendente';

        $sugestao = Sugestao::create($dados);

        return response()->json([
            'mensagem' => 'Sugestão cadastrada com sucesso!',
            'sugestao' => $sugestao->load('servico:id,nome')
        ], 201);
    }

    // Visualizar uma sugestão
    public function show(Request $request, Sugestao $sugestao)
    {
        $usuario = $request->user();

        // Cliente só pode visualizar sugestões da própria empresa
        if (
            $usuario->tipo !== 'admin' &&
            $sugestao->id_empresa != $usuario->id_empresa
        ) {
            return response()->json([
                'mensagem' => 'Você não tem permissão para visualizar esta sugestão.'
            ], 403);
        }

        return response()->json(
            $sugestao->load('servico:id,nome'),
            200
        );
    }

    // Atualizar sugestão
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
            'id_servico' => 'sometimes|required|exists:servicos,id',
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:pendente,em andamento,concluida'
        ]);

        $idEmpresa = $dados['id_empresa'] ?? $sugestao->id_empresa;
        $idServico = $dados['id_servico'] ?? $sugestao->id_servico;

        $servico = Servico::where('id', $idServico)
            ->where('id_empresa', $idEmpresa)
            ->first();

        if (!$servico) {
            return response()->json([
                'mensagem' => 'O serviço informado não pertence à empresa selecionada.'
            ], 422);
        }

        $sugestao->update($dados);

        return response()->json([
            'mensagem' => 'Sugestão atualizada com sucesso!',
            'sugestao' => $sugestao->load('servico:id,nome')
        ], 200);
    }

    // Excluir sugestão
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
