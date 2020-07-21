<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadNegocio extends Model
{
    protected $fillable = ['id', 'producto', 'descripcion', 'estado', 'empresa_id'];
    protected $table = 'unidad_negocio';
}
