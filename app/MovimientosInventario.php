<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MovimientosInventario extends Model
{
    protected $table = 'movimientos_inventario';

    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function cancelo()
    {
        return $this->hasOne('App\User', 'id', 'cancelo_id');
    }

    public function operacion()
    {
        return $this->belongsTo('App\Operaciones', 'operaciones_id', 'id');
    }

    public function detalles()
    {
        return $this->hasMany('App\AjusteInventarioDetalle', 'movimientos_inventario_id', 'id')
            ->select(
                '*',
                /**0- resto, 1- igual, 3- sumo */
                DB::raw(
                    '(NULL) AS resultado_ajuste'
                ),
                DB::raw(
                    '(NULL) AS resultado_ajuste_texto'
                ),
                DB::raw(
                    '(0) AS diferencia'
                )
            )
            ->orderBy('ajuste_detalle.articulos_id', 'asc');
    }

    public function articulosserviciofunerario()
    {
        return $this->hasMany('App\VentaDetalle', 'movimientos_inventario_id', 'id');
    }

    public function venta_gral_detalle()
    {
        return $this->hasMany('App\VentaDetalle', 'movimientos_inventario_id', 'id');
    }

    public function articulos_operacion()
    {
        return $this->hasMany('App\VentaDetalle', 'movimientos_inventario_id', 'id')
            ->join('articulos', 'articulos.id', '=', 'venta_detalle.articulos_id')
            ->join('sat_productos_servicios', 'articulos.sat_productos_servicios_id', '=', 'sat_productos_servicios.id')
            ->join('sat_unidades', 'articulos.sat_unidades_venta', '=', 'sat_unidades.id')
        ;
    }


    public function proveedor()
    {
        return $this->hasOne('App\Proveedores', 'id', 'proveedores_id');
    }


    public function compra_detalle()
    {
        return $this->hasMany('App\CompraDetalle', 'movimientos_inventario_id', 'id')
            ->select(
                'articulos.id as id_articulo',
                'articulos.descripcion',
                'compra_detalle.articulos_id',
                'compra_detalle.movimientos_inventario_id',
                'compra_detalle.cantidad',
                'compra_detalle.costo_neto',
                'compra_detalle.costo_neto_descuento',
                'compra_detalle.descuento_b',
                'compra_detalle.facturable_b'
            )
            ->join('articulos', 'articulos.id', '=', 'compra_detalle.articulos_id')
        ;
    }


    public function costos_incurridos()
    {
        return $this->hasMany('App\CostosIncurridos', 'movimientos_inventario_id', 'id');
    }


    public function detalle_ajuste_reporte()
    {
        return $this->hasMany('App\AjusteInventarioDetalle', 'movimientos_inventario_id', 'id')
            ->orderBy('ajuste_detalle.articulos_id', 'asc');
    }
}
