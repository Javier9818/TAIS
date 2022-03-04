<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objetivo_control_empresa extends Model
{
    protected $table = 'objetivos_control_empresa';
    protected $fillable = [
        'id',
        'control_fk',
        'empresa_fk',
        'version_fk'
    ];

    public function control()
    {
        return $this->belongsTo('App\Objetivo_control', 'control_fk', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'empresa_fk', 'id');
    }

    public function version()
    {
        return $this->belongsTo('App\Version_cobit', 'version_fk', 'id');
    }
}
