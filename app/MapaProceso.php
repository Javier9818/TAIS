<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MapaProceso extends Model
{
    protected $fillable = ['id', 'unidad_negocio_id', 'proceso_maestro', 'objeto', 'mega'];
    protected $table = 'mapa_proceso';
}
