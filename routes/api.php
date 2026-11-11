<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResources([
    'usuarios' => App\Http\Controllers\UsuarioController::class,
    'servicos' => App\Http\Controllers\ServicoController::class,
    'empresas' => App\Http\Controllers\EmpresaController::class,
    'feedback' => App\Http\Controllers\FeedbackController::class,
]);