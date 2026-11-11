<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{

    protected $fillable = [
        'id_usuario',
        'servico',
        'assunto',
        'comentario',
        'categoria',
        'prioridade',
        'status',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
