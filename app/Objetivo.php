<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objetivo extends Model
{
    protected $fillable = ['id', 'proceso_id', 'descripcion'];
    protected $table = 'objetivos';
}
