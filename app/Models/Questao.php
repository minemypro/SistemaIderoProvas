<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questao extends Model
{
    protected $fillable = [
        'exame_id', 'texto', 'imagem', 'comentario_explicativo', 'pontuacao'
    ];

    public function exame()
    {
        return $this->belongsTo(Exame::class);
    }

    public function alternativas()
    {
        return $this->hasMany(Alternativa::class);
    }

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }
}
