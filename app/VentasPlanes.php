<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasPlanes extends Model
{
    public function vendedor()
    {
        return $this->belongsTo('App\User', 'vendedor_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }

    public function entrego_convenio()
    {
        return $this->belongsTo('App\User', 'registro_id_convenio', 'id')
            ->select(
                'id',
                'nombre'
            );
    }


    public function conceptos_originales()
    {
        return $this->hasMany('App\PlanConceptosOriginal', 'ventas_planes_id', 'id')->orderBy('seccion_id', 'asc');
    }
}
