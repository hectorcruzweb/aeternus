<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    protected $table = 'pagos';

    public function pagos_pagos_programados()
    {
        return $this->belongsToMany('App\PagosProgramados', 'pagos_pagos_programados', 'pagos_id', 'pagos_programados_id')->as('pagos_cubiertos')
            ->select(
                '*',
                DB::raw(
                    '(0) AS pago_total'
                )
            )
            ->withPivot('monto', 'movimientos_pagos_id');
    }





    public function referencias_cubiertas()
    {
        return $this->belongsToMany('App\PagosProgramados', 'pagos_pagos_programados', 'pagos_id', 'pagos_programados_id')->as('pagos_cubiertos')
            ->select(
                '*'
            )
            ->orderBy('pagos_pagos_programados.pagos_programados_id', 'asc')
            ->withPivot('monto', 'movimientos_pagos_id');
    }

    public function forma_pago()
    {
        return $this->hasOne('App\SatFormasPago', 'id', 'sat_formas_pago_id');
    }


    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function cobrador()
    {
        return $this->hasOne('App\User', 'id', 'cobrador_id');
    }

    public function cancelador()
    {
        return $this->hasOne('App\User', 'id', 'cancelo_id');
    }

    public function sat_moneda()
    {
        return $this->hasOne('App\SATMonedas', 'id', 'sat_monedas_id');
    }

    public function subpagos()
    {
        return $this->hasMany('App\Pagos', 'parent_pago_id', 'id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS movimientos_pagos_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_pago_texto'
                ),
                DB::raw(
                    '(NULL) AS status_texto'
                )
            );
    }

    public function parent_pago()
    {
        return $this->hasMany('App\Pagos', 'id', 'parent_pago_id');
    }
}