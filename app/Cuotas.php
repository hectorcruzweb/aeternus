<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuotas extends Model
{
    protected $table = 'cuotas_cementerio';


    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function modifico()
    {
        return $this->hasOne('App\User', 'id', 'modifico_id');
    }

    public function cancelador()
    {
        return $this->hasOne('App\User', 'id', 'cancelo_id');
    }

    public function propiedades()
    {
        return $this->hasMany('App\Operaciones', 'cuotas_cementerio_id', 'id')->orderBy('clientes_id', 'asc');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }
}
