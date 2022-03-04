<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unidades extends Model
{
    protected $fillable = ['descripcion', 'estado'];
    protected $table = 'unidades';
}
