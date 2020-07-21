<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadenaSuministro extends Model
{
    protected $fillable = ['id', 'descripcion', 'estado', 'unidad_negocio_id'];
}
