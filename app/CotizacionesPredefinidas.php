<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CotizacionesPredefinidas extends Model
{
    protected $table = 'cotizaciones_predefinidas';


    public function conceptos()
    {
        return $this->hasMany('App\ConceptosPredefinidas', 'cotizacion_predefinida_id', 'id');
    }

    public function financiamientos()
    {
        return $this->hasMany('App\FinanciamientosPredefinidas', 'cotizacion_predefinida_id', 'id');
    }
}
