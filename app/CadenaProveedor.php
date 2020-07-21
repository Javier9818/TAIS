<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadenaProveedor extends Model
{
    protected $fillable = ['id', 'proveedor_id', 'proveedor_padre', 'nivel', 'cadena_suministro_id'];
    protected $table = 'cadena_proveedores';
}
