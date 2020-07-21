<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadenaCliente extends Model
{
    protected $fillable = ['id', 'cliente_id', 'cliente_padre', 'nivel', 'cadena_suministro_id'];
    protected $table = 'cadena_clientes';
}
