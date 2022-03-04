<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta_alineamiento_empresa extends Model
{
    protected $table = 'meta_alineamiento_empresa';
    protected $fillable = [
        'id',
        'alineamiento_fk',
        'empresa_fk',
        'version_fk'
    ];

    public function alineamiento()
    {
        return $this->belongsTo('App\Meta_alineamiento', 'alineamiento_fk', 'id');
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
