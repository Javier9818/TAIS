<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta_alineamiento extends Model
{
    protected $table = 'metas_alineamiento';
    protected $fillable = ['id', 'nombre', 'sigla'];
}
