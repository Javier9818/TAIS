<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    protected $fillable = ['unidad_negocio_id', 'id', 'comentario', 'contenido'];
    protected $table = 'Historial';
}
