<?php

namespace App;

use App\SatMetodosPagoModel;
use App\TipoComprobantes;
use Illuminate\Database\Eloquent\Model;

class Cfdis extends Model
{
    protected $table   = 'cfdis';
    protected $appends = ['monto_relacionado_total'];

    public function getMontoRelacionadoTotalAttribute()
    {
        // si no está cargado, no vamos a hacer N+1 queries
        if (! $this->relationLoaded('cfdis_relacionados_pagos_egresos')) {
            return null;
        }

        return round(
            $this->cfdis_relacionados_pagos_egresos->sum('monto_relacion'),
            2 // número de decimales
        );
    }

    public function cliente()
    {
        return $this->belongsTo('App\Clientes', 'clientes_id', 'id');
    }

    public function timbro()
    {
        return $this->belongsTo('App\User', 'timbro_id', 'id');
    }

    public function cfdis_operaciones()
    {
        return $this->hasMany('App\CfdisOperaciones', 'cfdis_id', 'id')->select('*')->distinct('operaciones_id');
    }

    public function pagos_asociados()
    {
        /**relacion por pago*/
        return $this
            ->hasMany('App\CfdisTipoRelacion', 'cfdis_id_relacionado', 'id')
            ->join('cfdis', 'cfdis.id', '=', 'cfdis_tipo_relacion.cfdis_id')
            ->select('cfdis_id', 'tipo_relacion_id', 'cfdis.uuid', 'tipo_relacion_id', 'cfdis_tipo_relacion.sat_metodos_pago_id', 'monto_relacion', 'cfdis.status', 'sat_tipo_comprobante_id', 'cfdis_tipo_relacion.cfdis_id_relacionado')
            ->where('tipo_relacion_id', '=', 2);
        /**solo pagos */
    }

    public function egresos_asociados()
    {
        /**relacion por nota de egreso*/
        return $this
            ->hasMany('App\CfdisTipoRelacion', 'cfdis_id_relacionado', 'id')
            ->join('cfdis', 'cfdis.id', '=', 'cfdis_tipo_relacion.cfdis_id')
            ->select('cfdis_id', 'tipo_relacion_id', 'cfdis.uuid', 'tipo_relacion_id', 'cfdis_tipo_relacion.sat_metodos_pago_id', 'monto_relacion', 'cfdis.status', 'sat_tipo_comprobante_id', 'cfdis_tipo_relacion.cfdis_id_relacionado')
            ->where('tipo_relacion_id', '=', 3);
        /**solo egresos */
    }

    public function cfdis_relacionados()
    {
        /**relacion por relacion de comprobante sat */
        return $this->hasMany('App\CfdisTipoRelacion', 'cfdis_id', 'id')->where('tipo_relacion_id', '=', 1);
    }

    public function cfdis_pagados()
    {
        /**relacion de cfdis que cubre este pago*/
        return $this->hasMany('App\CfdisTipoRelacion', 'cfdis_id', 'id')
            ->join('cfdis', 'cfdis.id', '=', 'cfdis_tipo_relacion.cfdis_id_relacionado')
            ->select('tipo_relacion_id', 'uuid', 'tipo_relacion_id', 'cfdis_tipo_relacion.sat_metodos_pago_id', 'monto_relacion', 'cfdis.status', 'sat_tipo_comprobante_id', 'cfdis_tipo_relacion.cfdis_id', 'cfdis_tipo_relacion.cfdis_id_relacionado')
            ->where('tipo_relacion_id', '=', 2);
        /**solo de tipo pago */
    }

    public function cfdis_egresados()
    {
        /**relacion de cfdis que egresó este comprobante*/
        return $this->hasMany('App\CfdisTipoRelacion', 'cfdis_id', 'id')
            ->join('cfdis', 'cfdis.id', '=', 'cfdis_tipo_relacion.cfdis_id_relacionado')
            ->select('tipo_relacion_id', 'uuid', 'tipo_relacion_id', 'cfdis_tipo_relacion.sat_metodos_pago_id', 'monto_relacion', 'cfdis.status', 'sat_tipo_comprobante_id', 'cfdis_tipo_relacion.cfdis_id', 'cfdis_tipo_relacion.cfdis_id_relacionado')
            ->where('tipo_relacion_id', '=', 3);
        /**solo de tipo egreso */
    }

    public function servicios_funerarios()
    {
        /**relacion de cfdis que egresó este comprobante*/
        return $this->hasMany('App\CfdisOperaciones', 'cfdis_id', 'id');
        /**solo de tipo egreso */
    }

    public function operaciones()
    {
        return $this->belongsToMany(
            Operaciones::class,
            'cfdis_operaciones',
            'cfdis_id',
            'operaciones_id'
        );
    }

    public function metodoPago()
    {
        return $this->belongsTo(SatMetodosPagoModel::class, 'sat_metodos_pago_id', 'id')
            ->select('id', 'clave');
    }

    public function tipoComprobante()
    {
        return $this->belongsTo(TipoComprobantes::class, 'sat_tipo_comprobante_id', 'id')
            ->select('id', 'clave', 'tipo');
    }

    public function tipoComprobanteRelacionado()
    {
        return $this->belongsTo(TipoComprobantes::class, 'sat_tipo_comprobante_id', 'id')
            ->select('id', 'clave', 'tipo');
    }

    public function cfdis_relacionados_pagos_egresos()
    {
        /**relacion de cfdis que cubre este pago*/
        return $this
            ->hasMany('App\CfdisTipoRelacion', 'cfdis_id_relacionado', 'id')
            ->join('cfdis', 'cfdis.id', '=', 'cfdis_tipo_relacion.cfdis_id')
            ->join('sat_tipo_comprobante', 'sat_tipo_comprobante.id', '=', 'cfdis.sat_tipo_comprobante_id')
            ->join('sat_formas_pago', 'sat_formas_pago.id', '=', 'cfdis.sat_formas_pago_id')
            ->select(
                'tipo as tipo_comprobante',
                //'cfdis_id',
                'cfdis_tipo_relacion.cfdis_id_relacionado',
                'sat_formas_pago.forma as forma_pago',
                //'tipo_relacion_id',
                'cfdis.uuid',
                //'cfdis_tipo_relacion.sat_metodos_pago_id',
                'cfdis_tipo_relacion.monto_relacion',
                //'cfdis.status',
                //'cfdis.sat_tipo_comprobante_id',
            )
            ->where('cfdis.status', '>', 0)
            ->whereIn('tipo_relacion_id', [2, 3]);
    }
}
