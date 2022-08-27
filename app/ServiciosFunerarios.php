<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ServiciosFunerarios extends Model
{
    protected $table = 'servicios_funerarios';


    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function recogio()
    {
        return $this->hasOne('App\User', 'id', 'recogio_id');
    }

    public function operacion()
    {
        return $this->hasOne('App\Operaciones', 'servicios_funerarios_id', 'id')->select(
            'id',
            'id as operacion_id',
            'clientes_id',
            'subtotal',
            'descuento',
            'impuestos',
            'tasa_iva',
            'total',
            'servicios_funerarios_id',
            'fecha_operacion',
            'fecha_registro',
            'fecha_modificacion',
            'modifico_id',
            'registro_id',
            'fecha_cancelacion',
            'motivos_cancelacion_id',
            'cantidad_a_regresar_cancelacion',
            'cancelo_id',
            'nota_cancelacion',
            'status',
            'status as operacion_status',
            DB::raw(
                '(0) AS num_pagos_programados'
            ),
            DB::raw(
                '(0) AS abonado_capital'
            ),
            DB::raw(
                '(0) AS descontado_capital'
            ),
            DB::raw(
                '(0) AS complementado_cancelacion'
            ),
            DB::raw(
                '(0) AS saldo_neto'
            ),
            DB::raw(
                '(0) AS total_cubierto'
            ),

            DB::raw(
                '(0) AS pagos_realizados'
            ),
            DB::raw(
                '(0) AS pagos_vigentes'
            ),
            DB::raw(
                '(0) AS num_pagos_programados_vigentes'
            ),
            DB::raw(
                '(0) AS pagos_cancelados'
            ),
            DB::raw(
                '(0) AS pagos_programados_cubiertos'
            ),
            DB::raw(
                '(null) AS status_texto'
            ),
            DB::raw(
                'DATE(operaciones.fecha_cancelacion) as fecha_cancelacion_operacion'
            ),
            DB::raw(
                '(NULL) AS motivos_cancelacion_texto'
            )
        );
    }

    public function nacionalidad()
    {
        return $this->hasOne('App\Nacionalidades', 'id', 'nacionalidades_id');
    }

    public function estado_civil()
    {
        return $this->hasOne('App\EstadosCiviles', 'id', 'estados_civiles_id');
    }

    public function escolaridad()
    {
        return $this->hasOne('App\Escolaridades', 'id', 'escolaridades_id');
    }

    public function terreno()
    {
        return $this->hasOne('App\VentasTerrenos', 'id', 'ventas_terrenos_id')
            ->select(
                'ubicacion',
                'operaciones.ventas_terrenos_id',
                'operaciones.id as id_operacion',
                'ventas_terrenos.id',
                'clientes_id',
                'nombre',
                DB::raw(
                    '(NULL) as ubicacion_servicio'
                ),
                DB::raw(
                    '(NULL) as status_operacion'
                ),
                DB::raw(
                    '(NULL) as status_operacion_texto'
                )
            )
            ->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
            ->join('clientes', 'operaciones.clientes_id', '=', 'clientes.id');
    }

    public function materialrentado()
    {
        return $this->hasMany('App\MaterialRentado', 'servicios_funerarios_id', 'id')
            ->select('servicios_funerarios_id', 'articulos_id', 'descripcion', 'cantidad', 'material_rentado.nota')
            ->join('articulos', 'material_rentado.articulos_id', '=', 'articulos.id');
    }


    public function titulo()
    {
        return $this->hasOne('App\Titulos', 'id', 'titulos_id');
    }


    public function servicio_exhumado()
    {
        return $this->hasMany('App\ServiciosFunerarios','servicios_funerarios_exhumado_id','id');
    }
}