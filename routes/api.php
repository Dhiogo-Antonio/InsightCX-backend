<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::apiResources([
    'usuarios' => App\Http\Controllers\UsuarioController::class,
    'servicos' => App\Http\Controllers\ServicoController::class,
    'empresas' => App\Http\Controllers\EmpresaController::class,
    'feedback' => App\Http\Controllers\FeedbackController::class,
]);

Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

});