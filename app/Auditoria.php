<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $fillable = ['id', 'terminal', 'navegador', 'tabla', 'accion', 'user_fk'];
    protected $table = 'auditoria';

    public function user()
    {
        return $this->belongsTo('App\User', 'user_fk', 'id');
    }
}
