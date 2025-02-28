<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasGral extends Model
{
    protected $table = 'ventas_generales';

    public function vendedor()
    {
        return $this->belongsTo('App\User', 'vendedores_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }

    public function entrego()
    {
        return $this->belongsTo('App\User', 'entrego_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }
}
