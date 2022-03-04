<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresarial_alineamiento extends Model
{
    protected $table = 'empresarial_alineamiento';
    protected $fillable = ['id', 'simbolo', 'peso', 'meta_fk', 'alineamiento_fk'];

    public function meta()
    {
        return $this->belongsTo('App\Meta_empresarial', 'meta_fk', 'id');
    }

    public function alineamiento()
    {
        return $this->belongsTo('App\Meta_alineamiento', 'alineamiento_fk', 'id');
    }
}
