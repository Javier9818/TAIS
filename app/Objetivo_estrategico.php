<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objetivo_estrategico extends Model
{
    protected $table = 'objetivos_estrategicos';
    protected $fillable = [
        "id",
        "nombre",
        "descripcion",
        "perspectiva_fk",
        "empresa_fk",
        "version_fk",
        'IS_PRIO'
    ];

    public function perspectiva()
    {
        return $this->belongsTo('App\Perspectiva', 'id', 'perspectiva_fk');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'id', 'perspectiva_fk');
    }
}
