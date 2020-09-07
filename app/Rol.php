<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $fillable = ['id','descripcion', 'unidad_negocio_id'];
    protected $table = 'rol';
}
