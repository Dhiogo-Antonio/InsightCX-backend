<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ]);

        $usuario = Usuario::where('email', $validatedData['email'])->first();

        if (!$usuario || !Hash::check($validatedData['senha'], $usuario->senha)) {
            return response()->json([
                'message' => 'E-mail ou senha incorretos.'
            ], 401);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'token' => $token,
            'tipo_token' => 'Bearer',
            'usuario' => [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'tipo' => $usuario->tipo,
                'id_empresa' => $usuario->id_empresa,
            ]
        ], 200);
    }



    public function cadastrar(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'cpf' => 'required|string|size:11|unique:usuarios,cpf',
            'telefone' => 'required|string|max:30',
            'senha' => 'required|string|min:8|max:255',
            'empresa' => 'required|string|max:255',
            'cnpj' => 'required|string|size:14|unique:empresas,cnpj',
        ]);

        try {
            $usuario = DB::transaction(function () use ($validatedData) {

                // Cadastra a empresa
                $empresa = Empresa::create([
                    'nome' => $validatedData['empresa'],
                    'cnpj' => $validatedData['cnpj'],
                    'email' => $validatedData['email'],
                ]);

                // Cadastra o usuário vinculado à empresa
                return Usuario::create([
                    'nome' => $validatedData['nome'],
                    'email' => $validatedData['email'],
                    'cpf' => $validatedData['cpf'],
                    'telefone' => $validatedData['telefone'],
                    'senha' => Hash::make($validatedData['senha']),
                    'tipo' => 'cliente',
                    'id_empresa' => $empresa->id,
                ]);
            });

            return response()->json([
                'message' => 'Cadastro realizado com sucesso!',
                'usuario' => [
                    'id' => $usuario->id,
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'tipo' => $usuario->tipo,
                    'id_empresa' => $usuario->id_empresa,
                ]
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Não foi possível realizar o cadastro.'
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!'
        ], 200);
    }
}
