<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objetivo_control extends Model
{
    protected $table = 'objetivos_control';
    protected $fillable = [
        'id',
        'nombre',
        'sigla',
    ];
}
