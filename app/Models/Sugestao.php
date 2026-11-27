<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sugestao extends Model
{
    protected $table = 'sugestoes';

    protected $fillable = [
        'id_empresa',
        'titulo',
        'descricao',
        'status'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
