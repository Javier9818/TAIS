<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['descripcion', 'imagen', 'ruc', 'empresa_id', 'nombre'];
    protected $table = 'clientes';
}
