<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class VentasTerrenos extends Model
{
    protected $table = 'ventas_terrenos';

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



    public function tipo_propiedad()
    {
        return $this->belongsTo('App\tipoPropiedades', 'tipo_propiedades_id', 'id')
            ->select(
                'id',
                'tipo',
                'capacidad'
            );
    }


    public function servicios_por_terreno()
    {
        return $this->hasMany('App\ServiciosFunerarios', 'ventas_terrenos_id', 'id')
            ->select(
                'id',
                'nombre_afectado',
                'fechahora_inhumacion',
                'ventas_terrenos_id',
                DB::raw(
                    '(NULL) AS fecha_inhumacion_texto'
                )
            )
            ->where('inhumacion_b', 1)
            ->orderBy('fechahora_inhumacion', 'desc');
    }
}
