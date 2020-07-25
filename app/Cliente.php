<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['id', 'entidad_id'];
    protected $table = 'clientes';
}
