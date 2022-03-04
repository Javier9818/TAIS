<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Version_cobit extends Model
{
    protected $table = 'version_cobit';
    protected $fillable = ['id', 'descripcion'];
}
