<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{

    use HasApiTokens, Notifiable;
    protected $fillable = [
        'nome',
        'email',
        'senha',
        'telefone',
        'tipo',
        'id_empresa'
    
    ];

    protected $hidden = [
        'senha',
    ];
}
