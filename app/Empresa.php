<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = ['ruc', 'nombre', 'direccion', 'descripcion', 'celular', 'estado'];

    public function clientes()
    {
        return $this->hasMany('App\Cliente', 'empresa_id');
    }

    public function proveedores()
    {
        return $this->hasMany('App\Proveedor', 'empresa_id');
    }
}
