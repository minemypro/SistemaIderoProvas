<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alternativa extends Model
{
    protected $fillable = ['questao_id', 'texto', 'correta'];

    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }
}
