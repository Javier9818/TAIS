<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alineamiento_control extends Model
{
    protected $table = 'alineamiento_control';
    protected $fillable = ['id', 'simbolo', 'peso', 'control_fk', 'alineamiento_fk'];

    public function control()
    {
        return $this->belongsTo('App\Objetivo_control', 'control_fk', 'id');
    }

    public function alineamiento()
    {
        return $this->belongsTo('App\Meta_alineamiento', 'alineamiento_fk', 'id');
    }
}
