<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios);
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
            'email' => 'required|string|email|max:255|unique:usuarios',
            'senha' => 'required|string|max:255',
            'telefone' => 'required|string|max:30',
            'tipo' => 'required|in:admin,cliente',
            'id_empresa' => 'required|exists:empresas,id',
        ]);

        try {
           $validatedData['senha'] = Hash::make($validatedData['senha']);
            $usuario = Usuario::create($validatedData);

            return response()->json([
                'message' => 'Usuário criado com sucesso!',
                'usuario' => $usuario
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao criar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usuario $usuario)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email,' . $usuario->id,
            'senha' => 'nullable|string|min:8|max:255',
            'telefone' => 'required|string|max:30',
            'tipo' => 'required|in:admin,cliente',
            'id_empresa' => 'required|exists:empresas,id',
        ]);

        try {
            
            if (!empty($validatedData['senha'])) {
                $validatedData['senha'] = \Illuminate\Support\Facades\Hash::make(
                    $validatedData['senha']
                );
            } else {
                unset($validatedData['senha']);
            }

            $usuario->update($validatedData);

            return response()->json([
                'message' => 'Usuário atualizado com sucesso!',
                'usuario' => $usuario
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao atualizar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        try {
            $possuiFeedbacks = DB::table('feedback')
                ->where('id_usuario', $usuario->id)
                ->exists();

            if ($possuiFeedbacks) {
                return response()->json([
                    'message' => 'Não é possível excluir este usuário, pois existem feedbacks vinculados a ele.'
                ], 409);
            }

            $usuario->delete();

            return response()->json([
                'message' => 'Usuário excluído com sucesso!'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao excluir usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
