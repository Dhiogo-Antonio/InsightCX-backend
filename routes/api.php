
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\FeedbackController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Dados do usuário autenticado
    Route::get('/me', function (Request $request) {
        $usuario = $request->user();

        return response()->json([
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'tipo' => $usuario->tipo,
            'id_empresa' => $usuario->id_empresa,
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Rotas compartilhadas: administrador e cliente
    Route::get('/servicos', [ServicoController::class, 'index']);

    Route::get('/feedbacks', [FeedbackController::class, 'index']);
    Route::post('/feedbacks', [FeedbackController::class, 'store']);
    Route::get('/feedbacks/{feedback}', [FeedbackController::class, 'show']);

    // Somente administrador
    Route::middleware('tipo:admin')->group(function () {

        Route::apiResource('empresas', EmpresaController::class);
        Route::apiResource('usuarios', UsuarioController::class);

        // O index é compartilhado; as demais operações são administrativas.
        Route::apiResource('servicos', ServicoController::class)
            ->except(['index']);

        // Index, store e show são compartilhados.
        Route::apiResource('feedbacks', FeedbackController::class)
            ->except(['index', 'store', 'show']);
    });
});
