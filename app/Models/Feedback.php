<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{

    protected $fillable = [
        'id_usuario',
        'id_servico',
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

      // O feedback pertence a um serviço
    public function servico()
    {
        return $this->belongsTo(Servico::class, 'id_servico');
    }
}
