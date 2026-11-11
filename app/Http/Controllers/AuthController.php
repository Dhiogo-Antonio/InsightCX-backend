<?php

namespace App\Http\Controllers;

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
                'empresa' => $usuario->empresa,
            ]
        ], 200);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!'
        ], 200);
    }
}
