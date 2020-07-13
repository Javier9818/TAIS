<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [ 'names', 'last_name_pat', 'last_name_mat', 'phone', 'address', 'dni'];
    protected $table = 'persons';
}
