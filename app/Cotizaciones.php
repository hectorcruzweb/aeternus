<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cotizaciones extends Model
{
    protected $table = 'cotizaciones';

    public function vendedor()
    {
        return $this->belongsTo('App\User', 'vendedor_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }

    public function registro()
    {
        return $this->belongsTo('App\User', 'registro_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }

    public function modifico()
    {
        return $this->belongsTo('App\User', 'modifico_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }

    public function cancelo()
    {
        return $this->belongsTo('App\User', 'cancelo_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }

    public function conceptos()
    {
        return $this->hasMany('App\ConceptosCotizacion', 'cotizaciones_id', 'id');
    }

    public function predefinidos()
    {
        return $this->hasMany('App\CotizacionesPredefinidas', 'cotizaciones_id', 'id');
    }
}
