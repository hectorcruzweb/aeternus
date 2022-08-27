<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Propiedades extends Model
{
    protected $table = 'propiedades';


    public function tipoPropiedad()
    {
        return $this->belongsTo('App\tipoPropiedades', 'tipo_propiedades_id', 'id');
    }

    public function filas_columnas()
    {
        return $this->hasMany('App\columnasFilas', 'propiedades_id', 'id');
    }


    public function ventas()
    {
        return $this->hasMany('App\VentasTerrenos', 'propiedades_id', 'id')->select(
                '*',
                DB::raw(
                    '(NULL) AS fila_raw'
                ),
                DB::raw(
                    '(NULL) AS lote_raw'
                )
            )->where('status', 1);
    }
}