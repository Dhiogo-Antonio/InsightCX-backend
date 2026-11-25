<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnaliseChatbot extends Model
{
    protected $table = 'analises_chatbot';

    protected $fillable = [
        'id_feedback',
        'analise',
        'palavras_chave',
        'prioridade_identificada',
        'tipo_atendimento',
        'resposta_gerada',
        'resposta_administrador',
        'status_atendimento',
        'resposta_enviada_em',
        'respondido_administrador_em'
    ];

    protected $casts = [
        'palavras_chave' => 'array',
        'resposta_enviada_em' => 'datetime',
        'respondido_administrador_em' => 'datetime'
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class, 'id_feedback');
    }
}
