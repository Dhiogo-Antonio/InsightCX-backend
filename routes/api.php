<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/usuarios', [App\Http\Controllers\UsuarioController::class, 'index']);
Route::get('/servicos', [App\Http\Controllers\ServicoController::class, 'index']);