<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamentos extends Model
{
    protected $table = 'departamentos';

    public function categorias()
    {
        return $this->hasMany('App\Categorias', 'departamentos_id', 'id');
    }
}