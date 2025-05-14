<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TentativaExame extends Model
{
    protected $fillable = [
        'user_id',
        'exame_id',
        'tentativa_numero',
        'inicio',
        'fim',
        'nota_final',
        'status',
        'ip',
        'user_agent'
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
        'nota_final' => 'float'
    ];


    public function verificarExpiracao()
    {
        if ($this->fim && now()->greaterThan($this->fim)) {
            $this->status = 'expirado';
            $this->save();
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
   // Método para atualizar o status para "finalizado"
    public function finalizar()
    {
        $this->status = 'finalizado';
        $this->save();
    }
    public function exame()
    {
        return $this->belongsTo(Exame::class);
    }

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }
    // app/Models/TentativaExame.php
    public function calcularFim($duracaoMinutos)
    {
        $this->fim = $this->inicio->copy()->addMinutes($duracaoMinutos);
        $this->save();
    }

    public function logs()
    {
        return $this->hasMany(LogAtividade::class);
    }
}
