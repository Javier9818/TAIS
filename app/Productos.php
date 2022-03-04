<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $fillable = ['precio', 'cantidad', 'idUnidad', 'idCategoria', 'descripcion', 'estado'];
    protected $table = 'productos';
}
