<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'email', 'password', 'isAdmin', 'isCustomer', 'photo', 'person_id', 'empresa_id',
    ];

    protected $hidden = [
        'password',
    ];

}
