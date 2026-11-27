
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SugestaoController;

// Cadastro
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

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Feedbacks

    // Histórico de feedbacks respondidos
    // Deve ficar antes da rota dinâmica /feedbacks/{feedback}
    Route::get('/feedbacks/historico-respondidos', [
        FeedbackController::class,
        'historicoRespondidos'
    ]);

    Route::get('/feedbacks', [FeedbackController::class, 'index']);

    Route::post('/feedbacks', [FeedbackController::class, 'store']);

    Route::get('/feedbacks/{feedback}', [FeedbackController::class, 'show']);

    // Sugestões
    Route::get('/sugestoes', [SugestaoController::class, 'index']);

    Route::get('/sugestoes/{sugestao}', [SugestaoController::class, 'show']);

    // Somente administrador
    Route::middleware('tipo:admin')->group(function () {

        // Usuários
        Route::apiResource('usuarios', UsuarioController::class);

        // Empresas
        Route::apiResource('empresas', EmpresaController::class);

        // Gerenciamento de feedbacks
        Route::apiResource('feedbacks', FeedbackController::class)
            ->except(['index', 'store', 'show']);

        // Gerenciamento de sugestões
        Route::post('/sugestoes', [SugestaoController::class, 'store']);

        Route::put('/sugestoes/{sugestao}', [SugestaoController::class, 'update']);

        Route::delete('/sugestoes/{sugestao}', [SugestaoController::class, 'destroy']);

        // Respostas dos feedbacks pelo administrador

        Route::put(
            '/analises-chatbot/{analise}/aprovar',
            [FeedbackController::class, 'aprovar']
        );

        Route::put(
            '/analises-chatbot/{analise}/recusar',
            [FeedbackController::class, 'recusar']
        );

        Route::put(
            '/analises-chatbot/{analise}/responder',
            [FeedbackController::class, 'responder']
        );
    });
});
