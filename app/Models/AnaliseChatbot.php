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
        'respondido_administrador_em',

        
        'sentimento',
        'aspectos_positivos',
        'aspectos_negativos',
        'problemas_identificados',
        'recomendacoes',
        'confianca_analise',
        'resumo_gerencial',

    
        'status_aprovacao',
        'avaliado_por',
        'avaliado_em',
        'motivo_recusa'
    ];

    protected $casts = [
        'palavras_chave' => 'array',
        'aspectos_positivos' => 'array',
        'aspectos_negativos' => 'array',
        'problemas_identificados' => 'array',
        'recomendacoes' => 'array',
        'confianca_analise' => 'float',
        'resposta_enviada_em' => 'datetime',
        'respondido_administrador_em' => 'datetime',
        'avaliado_em' => 'datetime'
    ];

    public function feedback()
    {
        return $this->belongsTo(
            Feedback::class,
            'id_feedback'
        );
    }

    public function administrador()
    {
        return $this->belongsTo(
            Usuario::class,
            'avaliado_por'
        );
    }
}
