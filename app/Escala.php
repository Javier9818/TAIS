<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    protected $fillable = ['id', 'descripcion', 'puntaje', 'criterio_id'];
    protected $table = 'escalas';
}
