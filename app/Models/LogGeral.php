<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogGeral extends Model
{
    public $timestamps = false;

    protected $table = 'log_geral';

    protected $fillable = [
        'user_id', 'acao', 'entidade', 'entidade_id', 'detalhes', 'ip', 'registrado_em'
    ];
}
