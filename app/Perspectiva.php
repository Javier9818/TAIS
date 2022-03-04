<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perspectiva extends Model
{
    protected $table ='perspectivas';
    protected $fillable = ["id", "nombre"];
}
