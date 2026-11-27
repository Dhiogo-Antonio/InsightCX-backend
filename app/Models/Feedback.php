<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'id_usuario',
        'servico',
        'assunto',
        'comentario',
        'categoria',
        'prioridade',
        'status'
    ];

    protected $casts = [
        'palavras_chave' => 'array',
        'requer_atendimento_humano' => 'boolean',
        'resposta_enviada_em' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function analisesChatbot()
    {
        return $this->hasMany(AnaliseChatbot::class, 'id_feedback');
    }
}