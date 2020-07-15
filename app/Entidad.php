<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    protected $table = 'entidad';  
    protected $fillable = ['ruc', 'nombre', 'email', 'descripcion', 'celular', 'empresa_id'];
}
