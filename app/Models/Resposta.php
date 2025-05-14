<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    protected $fillable = ['tentativa_exame_id', 'questao_id', 'alternativa_id', 'exame_id',];




    public function tentativa()
    {
        return $this->belongsTo(TentativaExame::class, 'tentativa_exame_id');
    }

    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }



    public function alternativa()
    {
        return $this->belongsTo(Alternativa::class);
    }

    public function estudante()
    {
        return $this->belongsTo(User::class, 'estudante_id');
    }
}
