<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class tipoPropiedades extends Model
{
    protected $table = 'tipo_propiedades';

    public function propiedades()
    {
        return $this->hasMany('App\Propiedades', 'tipo_propiedades_id', 'id');
    }

    //defino los precios que tienen cada tipo de propiedad
    public function precios()
    {
        return $this->hasMany('App\PreciosPropiedades', 'tipo_propiedades_id', 'id')->select(
            '*',
            DB::raw(
                '(NULL) AS tipo_financiamiento'
            ),
            DB::raw(
                '(NULL) AS descuento_x_pago'
            ),
            DB::raw(
                '(NULL) AS tipo_financiamiento_ingles'
            ),
            DB::raw(
                '(NULL) AS pago_mensual'
            ),
            DB::raw(
                '(NULL) AS porcentaje_pronto_pago'
            )
        )->orderBy('financiamiento', 'asc');
    }
}