<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = ['id', 'version_id', 'descripcion', 'flag_red', 'nombre', 'type', 'proceso_id'];
    protected $table = 'documento';
}
