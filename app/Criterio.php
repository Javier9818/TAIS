<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Criterio extends Model
{
    protected $fillable = ['id', 'nombre', 'descripcion', 'peso', 'unidad_negocio_id', 'estado'];
    protected $table = 'criterios';
}
