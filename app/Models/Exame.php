<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exame extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'disciplina',
        'navegacao',
        'tentativas_permitidas',
        'feedback',
        'inicio',
        'fim',
        'pontuacao_total', // <-- Adicione isso
        'ativo',
         'user_id' // <- Adicione isso aqui
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
        'ativo' => 'boolean',
    ];


    // App\Models\Exame.php

    public function questoes()
{
    return $this->hasMany(Questao::class);
}



    public function tentativas()
    {
        return $this->hasMany(TentativaExame::class);
    }

    // Método para calcular a pontuação total
    public function calcularPontuacaoTotal()
    {
        return $this->questoes->sum('pontuacao');
    }



    // app/Models/User.php (adiciona relacionamento)
    public function matriculas()
    {
        return $this->hasMany(MatriculaExame::class, 'estudante_id');
    }
}
