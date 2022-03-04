<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta_empresarial extends Model
{
    protected $table = 'metas_empresariales';
    protected $fillable = ['id', 'nombre', 'sigla', 'perspectiva_fk'];

    public function perspectiva()
    {
        return $this->belongsTo('App\Perspectiva', 'perspectiva_fk', 'id');
    }
}
