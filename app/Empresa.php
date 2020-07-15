<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = ['ruc', 'nombre', 'direccion', 'descripcion', 'celular', 'estado'];

    public function entidades()
    {
        return $this->hasMany('App\Entidad', 'empresa_id');
    }
}
