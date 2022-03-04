<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Iniciativas extends Model
{
    protected $fillable = ['id', 'nombre', 'descripcion', 'control_empresa_fk'];
    protected $table = 'iniciativas';

    public function controlEmpresa()
    {
        return $this->belongsTo('App\Objetivo_control_empresa', 'id', 'control_empresa_fk');
    }
}
