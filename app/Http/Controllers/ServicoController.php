<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Servico::query();

        if ($usuario->tipo !== 'admin') {
            $query->where('id_empresa', $usuario->id_empresa);
        }

        return response()->json($query->get(), 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'id_empresa' => 'required|exists:empresas,id',
            'descricao' => 'required|string',
            'status' => 'required|in:ativo,inativo',
        ]);

        try {
            $servico = Servico::create($validatedData);

            return response()->json([
                'message' => 'Serviço criado com sucesso!',
                'servico' => $servico
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao criar serviço',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Servico $servico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servico $servico)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servico $servico)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'id_empresa' => 'required|exists:empresas,id',
            'descricao' => 'required|string',
            'status' => 'required|in:ativo,inativo',
        ]);

        try {
            $servico->update($validatedData);

            return response()->json([
                'message' => 'Serviço atualizado com sucesso!',
                'servico' => $servico
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao atualizar serviço',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servico $servico)
    {
        try {

            $possuiFeedbacks = DB::table('feedback')
                ->where('id_servico', $servico->id)
                ->exists();

            if ($possuiFeedbacks) {
                return response()->json([
                    'message' => 'Não é possível excluir este serviço, pois existem feedbacks vinculados a ele.'
                ], 409);
            }

            $servico->delete();

            return response()->json([
                'message' => 'Serviço excluído com sucesso!'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao excluir serviço',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
