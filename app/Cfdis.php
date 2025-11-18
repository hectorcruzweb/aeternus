<?php
namespace App;

use App\SatMetodosPagoModel;
use App\TipoComprobantes;
use Illuminate\Database\Eloquent\Model;

class Cfdis extends Model
{
    protected $table = 'cfdis';

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

}
