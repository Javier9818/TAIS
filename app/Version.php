<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $fillable = ['id','descripcion', 'procesos', 'unidad_negocio_id', 'mapa_proceso', 'priorizacion', 'seguimiento'];
    protected $table = 'version';
}
