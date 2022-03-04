<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta_empresarial_empresa extends Model
{
    protected $table = 'meta_empresarial_empresa';
    protected $fillable = ['id', 'meta_empresarial_fk', 'version_fk', 'empresa_fk', 'objetivo_fk'];

    public function meta()
    {
        return $this->belongsTo('App\Meta_empresarial', 'meta_empresarial_fk', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'empresa_fk', 'id');
    }

    public function objetivo()
    {
        return $this->belongsTo('App\Objetivo_estrategico', 'objetivo_fk', 'id');
    }

}
