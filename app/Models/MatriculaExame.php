<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TentativaExame;

class MatriculaExame extends Model
{
    protected $table = 'matriculas_exames';

    protected $fillable = [
        'estudante_id',
        'exame_id',
        'status',
        'tentativas_permitidas',
        'nota_final',
    ];

    public function estudante()
    {
        return $this->belongsTo(User::class, 'estudante_id');
    }

    public function exame()
    {
        return $this->belongsTo(Exame::class);
    }

    public function tentativas()
    {
        return $this->hasMany(TentativaExame::class, 'matricula_exame_id');
    }
}
