<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/usuarios', [App\Http\Controllers\UsuarioController::class, 'index']);
Route::get('/servicos', [App\Http\Controllers\ServicoController::class, 'index']);
Route::get('/empresas', [App\Http\Controllers\EmpresaController::class, 'index']);
Route::get('/feedback', [App\Http\Controllers\FeedbackController::class, 'index']);