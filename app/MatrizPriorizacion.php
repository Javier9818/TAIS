<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MatrizPriorizacion extends Model
{
    protected $fillable = ['id', 'proceso_id', 'criterio_id', 'puntaje', 'escala_id'];
    protected $table = 'matriz_priorizacion';
}
