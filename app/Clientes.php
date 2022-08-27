<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'clientes';

    public function nacionalidad()
    {
        return $this->hasOne('App\Nacionalidades', 'id', 'nacionalidades_id');
    }

    public function regimen()
    {
        return $this->hasOne('App\SATRegimenes', 'id', 'regimen_fiscal_id');
    }

    public function genero()
    {
        return $this->hasOne('App\Generos', 'id', 'generos_id');
    }
}