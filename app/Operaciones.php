<?php
namespace App;

use App\Cfdis;
use App\Cuotas;
use App\VentasGral;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Operaciones extends Model
{
    protected $table = 'operaciones';

    public function venta_terreno()
    {
        return $this->belongsTo('App\VentasTerrenos', 'ventas_terrenos_id', 'id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS tipo_financiamiento_texto'
                ),
                DB::raw(
                    '(NULL) AS area_nombre'
                ),
                DB::raw(
                    '(NULL) AS tipo_texto'
                ),
                DB::raw(
                    '(NULL) AS fila_raw'
                ),
                DB::raw(
                    '(NULL) AS lote_raw'
                ),
                DB::raw(
                    '(NULL) AS ubicacion_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_convenio_entrega_texto'
                )
            );
    }

    public function servicio_funerario()
    {
        return $this->belongsTo('App\ServiciosFunerarios', 'servicios_funerarios_id', 'id')
            ->select(
                'id',
                'fechahora_defuncion',
                'nombre_afectado',
                DB::raw(
                    '(NULL) AS fecha_defuncion_texto'
                )
            );
    }

    public function sepultados()
    {
        return $this->hasMany(
            'App\ServiciosFunerarios',
            'ventas_terrenos_id',
            'ventas_terrenos_id'
        )->where('status', '<>', 0)->select(
            'ventas_terrenos_id',
            'nombre_afectado',
            'fechahora_defuncion',
            'fechahora_inhumacion',
            'id as servicios_funerarios_id',
            DB::raw(
                '(NULL) AS fecha_defuncion_texto'
            ),
            DB::raw(
                '(NULL) AS fecha_inhumacion_texto'
            )
        );
    }

    public function cuota_cementerio()
    {
        return $this->belongsTo('App\Cuotas', 'cuotas_cementerio_id', 'id');
    }

    public function cuota_cementerio_terreno()
    {
        return $this->hasMany('App\Operaciones', 'ventas_terrenos_id', 'ventas_terrenos_id')->select("*", "operaciones.id as operacion_id")->where('empresa_operaciones_id', 2);
    }

    /**la venta tiene uno o muchos pagos programados */
    public function pagosProgramados()
    {
        return $this->hasMany('App\PagosProgramados', 'operaciones_id', 'id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS fecha_programada_abr'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar_abr'
                ),
                DB::raw(
                    '(0) AS status_pago' //0-vencido-1-pendiente,2-vencido
                ),
                DB::raw(
                    '(0) AS status_pago_texto' //0-vencido-1-pendiente,2-pagado
                ),
                DB::raw(
                    '(0) AS intereses'
                ),
                DB::raw(
                    '(0) AS abonado_capital'
                ),
                DB::raw(
                    '(0) AS abonado_intereses'
                ),
                DB::raw(
                    '(0) AS descontado_pronto_pago'
                ),
                DB::raw(
                    '(0) AS descontado_capital'
                ),
                DB::raw(
                    '(0) AS complementado_cancelacion'
                ),
                DB::raw(
                    '(NULL) AS total_cubierto'
                ),
                DB::raw(
                    '(0) AS saldo_neto'
                ),
                DB::raw(
                    '(0) AS dias_vencido'
                ),
                DB::raw(
                    '(0) AS monto_pronto_pago'
                ),
                DB::raw(
                    '(NULL) AS concepto_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_ultimo_pago'
                ),
                DB::raw(
                    '(NULL) AS fecha_ultimo_pago_abr'
                ),

                DB::raw(
                    '(0) AS aplica_pronto_pago_b'
                ),
                DB::raw(
                    '(0) AS descuento_pronto_pago'
                )
            )
            ->orderBy('id', 'asc');
    }

    public function beneficiarios()
    {
        return $this->hasMany('App\Beneficiarios', 'operaciones_id', 'operacion_id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }

    public function usuarios_plan_futuro()
    {
        return $this->hasMany('App\ServiciosFunerarios', 'ventas_planes_id', 'ventas_planes_id')->select(
            "*"
        )->where('status', 1);
    }

    public function AjustesPoliticas()
    {
        return $this->hasOne('App\AjustesPoliticasOperacion', 'operaciones_id', 'operacion_id');
    }

    /**pagos programados para relacion al consultar pagos  desde pagos controller get_pagos*/
    public function get_pagos_pagos_programados()
    {
        return $this->hasMany('App\PagosProgramados', 'operaciones_id', 'id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS fecha_programada_abr'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar_abr'
                ),
                DB::raw(
                    '(0) AS status_pago' //0-vencido-1-pendiente,2-vencido
                ),
                DB::raw(
                    '(0) AS status_pago_texto' //0-vencido-1-pendiente,2-pagado
                ),
                DB::raw(
                    '(0) AS intereses'
                ),
                DB::raw(
                    '(0) AS abonado_capital'
                ),
                DB::raw(
                    '(0) AS abonado_intereses'
                ),
                DB::raw(
                    '(0) AS descontado_pronto_pago'
                ),
                DB::raw(
                    '(0) AS descontado_capital'
                ),
                DB::raw(
                    '(0) AS complementado_cancelacion'
                ),
                DB::raw(
                    '(NULL) AS total_cubierto'
                ),
                DB::raw(
                    '(0) AS saldo_neto'
                ),
                DB::raw(
                    '(0) AS dias_vencido'
                ),
                DB::raw(
                    '(0) AS monto_pronto_pago'
                ),
                DB::raw(
                    '(NULL) AS concepto_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_ultimo_pago'
                ),
                DB::raw(
                    '(NULL) AS fecha_ultimo_pago_abr'
                ),

                DB::raw(
                    '(0) AS aplica_pronto_pago_b'
                ),
                DB::raw(
                    '(0) AS descuento_pronto_pago'
                )
            )
            ->orderBy('id', 'asc');
    }

    public function cliente()
    {
        return $this->belongsTo('App\Clientes', 'clientes_id', 'id');
    }

    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function modifico()
    {
        return $this->hasOne('App\User', 'id', 'modifico_id');
    }

    public function cancelador()
    {
        return $this->hasOne('App\User', 'id', 'cancelo_id');
    }

    /**relacion con venta de planes funerarios */
    public function venta_plan()
    {
        return $this->belongsTo('App\VentasPlanes', 'ventas_planes_id', 'id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS tipo_financiamiento_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_convenio_entrega_texto'
                )
            );
    }

    public function movimientoinventario()
    {
        return $this->belongsTo('App\MovimientosInventario', 'id', 'operaciones_id');
    }

    public function movimiento_operacion_inventario()
    {
        return $this->hasOne('App\MovimientosInventario', 'operaciones_id', 'id')->select('operaciones_id', 'id');
    }

    public function venta_general()
    {
        return $this->belongsTo('App\VentasGral', 'ventas_generales_id', 'id')
            ->select(
                '*',
                DB::raw(
                    'if(entregado_b=1,"Entregado","Por Entregar") AS status_entregado_texto'
                )
            );
    }

    public function conceptos_temporales()
    {
        return $this->hasMany('App\ArticulosTemporales', 'operaciones_id', 'id')
            ->select(
                "cantidad",
                "articulos.descripcion",
                "articulos_id",
                "costo_neto_normal",
                "costo_neto_descuento",
                "descuento_b",
                "facturable_b",
                "operaciones_id",
                "sat_productos_servicios.id as sat_producto_servicio_id",
                "sat_productos_servicios.descripcion as sat_producto_servicio_descripcion",
                "sat_productos_servicios.clave as sat_producto_servicio_clave",
                "sat_unidades_venta",
                "sat_unidades.id as sat_unidad_id",
                "sat_unidades.clave as sat_unidad_clave",
                "sat_unidades.unidad as sat_unidad"
            )
            ->join('articulos', 'articulos.id', '=', 'articulos_venta_general_temporal.articulos_id')
            ->join('sat_productos_servicios', 'articulos.sat_productos_servicios_id', '=', 'sat_productos_servicios.id')
            ->join('sat_unidades', 'articulos.sat_unidades_venta', '=', 'sat_unidades.id');
    }

    // Each operación can link to one of these depending on empresa_operaciones_id
    public function ventasTerrenos()
    {
        return $this->belongsTo(VentasTerrenos::class, 'ventas_terrenos_id');
    }

    public function cuotasCementerios()
    {
        return $this->belongsTo(Cuotas::class, 'cuotas_cementerio_id');
    }

    public function serviciosFunerarios()
    {
        return $this->belongsTo(ServiciosFunerarios::class, 'servicios_funerarios_id');
    }

    public function ventasPlanes()
    {
        return $this->belongsTo(VentasPlanes::class, 'ventas_planes_id');
    }

    public function ventasGenerales()
    {
        return $this->belongsTo(VentasGral::class, 'ventas_generales_id');
    }

    public function cfdis()
    {
        return $this->belongsToMany(Cfdis::class, 'cfdis_operaciones', 'operaciones_id', 'cfdis_id')
            ->where('cfdis.status', '>', 0)             // Solo Activos
            ->where('cfdis.sat_tipo_comprobante_id', 1) //Tipo Ingreso
                                                    // ->where('cfdis.id', 3083)                   //id especifico
            ->where('cfdis.sat_metodos_pago_id', 2)     //metodo de pago especifico 1 PUE 2 PPD
            ->join('sat_tipo_comprobante', 'sat_tipo_comprobante.id', '=', 'cfdis.sat_tipo_comprobante_id')
            ->join('sat_metodos_pago', 'sat_metodos_pago.id', '=', 'cfdis.sat_metodos_pago_id')
            ->join('sat_formas_pago', 'sat_formas_pago.id', '=', 'cfdis.sat_formas_pago_id')
            ->join('clientes', 'clientes.id', '=', 'cfdis.clientes_id')
            ->select(
                'sat_tipo_comprobante.tipo as tipo_comprobante',
                'sat_metodos_pago.clave as metodo_pago',
                'sat_formas_pago.forma as forma_pago',
                'cfdis.id',
                'cfdis.uuid',
                'cfdis.rfc_receptor',
                'clientes.nombre as cliente_nombre',
                //'cfdis.sat_tipo_relacion_id',
                'cfdis.sat_tipo_comprobante_id',
                'cfdis.sat_metodos_pago_id',
                'sat_formas_pago_id',
                'total',
                // --- NEW FIELD ---
                DB::raw('cfdis.total - COALESCE((
        SELECT SUM(r.monto_relacion)
        FROM cfdis_tipo_relacion r
        JOIN cfdis c2 ON c2.id = r.cfdis_id
        WHERE r.cfdis_id_relacionado = cfdis.id
            AND r.tipo_relacion_id IN (2,3)
            AND c2.status > 0
    ), 0) AS saldo_pendiente_facturacion'),
                'cfdis.status'
            ) // Solo los campos necesarios;
            ->with('cfdis_relacionados_pagos_egresos');
    }

}
