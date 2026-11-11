<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empresas = Empresa::all();
        return response()->json($empresas);
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
            'cnpj' => 'required|string|max:20|unique:empresas',
            'email' => 'required|string|email|max:255|unique:empresas',
        ]);

        try {
            $empresa = Empresa::create($validatedData);

            return response()->json([
                'message' => 'Empresa criada com sucesso!',
                'empresa' => $empresa
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao criar empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:empresas,cnpj,' . $empresa->id,
            'email' => 'required|string|email|max:255|unique:empresas,email,' . $empresa->id,
        ]);

        try {
            $empresa->update($validatedData);

            return response()->json([
                'message' => 'Empresa atualizada com sucesso!',
                'empresa' => $empresa
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao atualizar empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        try {
            $possuiUsuarios = DB::table('usuarios')
                ->where('id_empresa', $empresa->id)
                ->exists();

            if ($possuiUsuarios) {
                return response()->json([
                    'message' => 'Não é possível excluir esta empresa, pois existem usuários vinculados a ela.'
                ], 409);
            }

            $empresa->delete();

            return response()->json([
                'message' => 'Empresa excluída com sucesso!'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao excluir empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
