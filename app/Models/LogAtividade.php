<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAtividade extends Model
{
    protected $fillable = [
        'tentativa_exame_id',
        'evento',
        'ip',
        'detalhes',
        'user_id',
    ];
}
