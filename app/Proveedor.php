<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = ['entidad_id'];
    protected $table = 'proveedores';
}
