<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    protected $table = 'categorias';


    public function departamento()
    {
        return $this->belongsTo('App\Departamentos', 'departamentos_id', 'id');
    }
}