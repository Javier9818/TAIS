<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = ['descripcion', 'ruc', 'razon_social', 'empresa_id'];
    protected $table = 'proveedores';
}
