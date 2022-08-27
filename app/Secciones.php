<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Secciones extends Model
{
    protected $table = 'secciones';


    public function modulos()
    {
        return $this->hasMany('App\Modulos', 'secciones_id', 'id');
    }
}