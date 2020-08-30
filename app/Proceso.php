<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'unidad_negocio_id', 'proceso_padre', 'flag_prio', 'estado'];
    protected $table = 'procesos';
}
