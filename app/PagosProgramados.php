<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PagosProgramados extends Model
{
    protected $table = 'pagos_programados';

    public function pagados()
    {
        return $this->belongsToMany('App\Pagos', 'pagos_pagos_programados', 'pagos_programados_id', 'pagos_id')->as('pagos_cubiertos')
            ->select(
                '*',
                DB::raw(
                    '(0) AS pago_total'
                )
            )
            ->withPivot('monto', 'movimientos_pagos_id')->orderBy('fecha_pago', 'asc');
    }



    public function pagados_para_get_pagos()
    {
        return $this->belongsToMany('App\Pagos', 'pagos_pagos_programados', 'pagos_programados_id', 'pagos_id')->as('pagos_cubiertos')
            ->select(
                '*',
                DB::raw(
                    '(0) AS pago_total'
                )
            )
            ->withPivot('monto', 'movimientos_pagos_id')->orderBy('fecha_pago', 'asc');
    }



    public function operacion_del_pago()
    {
        return $this->belongsTo('App\Operaciones', 'operaciones_id', 'id')
            ->select(
                '*'
            );
    }
}