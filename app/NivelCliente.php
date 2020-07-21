<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NivelCliente extends Model
{
    protected $fillable = ['id', 'cadena_suministro_id', 'numero'];
    protected $table = 'nivel_clientes';
}
