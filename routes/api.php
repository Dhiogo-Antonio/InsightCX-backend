
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SugestaoController;

Route::post('/cadastrar', [AuthController::class, 'cadastrar']);

// Login público
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
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

    // Serviços
    Route::get('/servicos', [ServicoController::class, 'index']);

    // Feedbacks
    Route::get('/feedbacks', [FeedbackController::class, 'index']);
    Route::post('/feedbacks', [FeedbackController::class, 'store']);
    Route::get('/feedbacks/{feedback}', [FeedbackController::class, 'show']);

    // Sugestões
    Route::get('/sugestoes', [SugestaoController::class, 'index']);
    Route::get('/sugestoes/{sugestao}', [SugestaoController::class, 'show']);

    // Somente administrador
    Route::middleware('tipo:admin')->group(function () {

        Route::apiResource('usuarios', UsuarioController::class);
        Route::apiResource('empresas', EmpresaController::class);

        Route::apiResource('servicos', ServicoController::class)
            ->except(['index']);

        Route::apiResource('feedbacks', FeedbackController::class)
            ->except(['index', 'store', 'show']);

        // Gerenciamento de sugestões
        Route::post('/sugestoes', [SugestaoController::class, 'store']);
        Route::put('/sugestoes/{sugestao}', [SugestaoController::class, 'update']);
        Route::delete('/sugestoes/{sugestao}', [SugestaoController::class, 'destroy']);
    });
});
