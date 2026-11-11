<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarTipo
{
     public function handle(Request $request, Closure $next, string ...$tipos): Response
    {
        $usuario = $request->user();

        if (!$usuario || !in_array($usuario->tipo, $tipos)) {
            return response()->json([
                'message' => 'Você não tem permissão para acessar este recurso.'
            ], 403);
        }

        return $next($request);
    }
}
