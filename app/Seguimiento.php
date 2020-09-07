<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $fillable = ['id','actividad', 'flujo', 'tiempo', 'proceso_id', 'rol_id'];
    protected $table = 'seguimiento';
}
