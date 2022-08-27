<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modulos extends Model
{
    protected $table = 'modulos';


    public function permisos()
    {
        return $this->hasMany('App\Permisos', 'modulos_id', 'id');
    }
}