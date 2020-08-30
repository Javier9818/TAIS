<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MapaProcesoDetalle extends Model
{
    protected $fillable = ['id', 'proceso_from', 'proceso_to', 'mapa_proceso_id'];
    protected $table = 'mapa_proceso_detalle';
}
