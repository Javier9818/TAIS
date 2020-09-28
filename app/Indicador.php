<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $fillable = ['id', 'proceso_id', 'descripcion'];
    protected $table = 'indicador';
}
