<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    protected $table = 'entidad';  
    protected $fillable = ['ruc', 'nombre', 'email', 'descripcion', 'celular', 'foto','empresa_id'];

    public static function setImagen($data, $actual = false){
        if($data){
            if($actual){
                Storage::disk('public')->delete("images/entidad/$actual");
            }
            $imageName=Str::random(20).'.jpg';
            $imagen=Image::make($data)->encode('jpg',90);
            $imagen->resize(200,200,function($constraint){
                $constraint->upsize();
            });
            Storage::disk('public')->put("images/entidad/$imageName",$imagen->stream());
            return $imageName;
        }else{
            return false;
        }
    }

}
