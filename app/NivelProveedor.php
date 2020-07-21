<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NivelProveedor extends Model
{
    protected $fillable = ['id', 'cadena_suministro_id', 'numero'];
    protected $table = 'nivel_proveedores';
}
