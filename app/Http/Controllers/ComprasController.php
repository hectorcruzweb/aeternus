<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Compras;
use App\DetalleCompra;
use App\Productos;
use App\DetallePagoCompra;
use Illuminate\Support\Facades\DB;

class ComprasController extends ApiController
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function save(Request $request) {
        DB::transaction(function () use  ($request) {
            $compra = new Compras;
    
            $compra->fecha_compra = $request->fecha_compra;
            $compra->referencia_factura = $request->referencia_factura;
            $compra->metodos_pago_id = $request->metodos_pago_id;
            $compra->total_neto = $request->total_neto;
            $compra->proveedores_id = $request->proveedores_id;
            $compra->registro_id = auth()->user()->id;
            $compra->save();
            foreach($request->productos as $producto) {
                $detalle = new DetalleCompra;
                $detalle->compras_id = $compra->id;
                $detalle->productos_id = $producto['id'];
                $detalle->precio_neto = $producto['precio_neto'];
                $detalle->cantidad_compra = $producto['cantidad'];
                $detalle->save();
    
                $productObj = Productos::find($producto['id']);
                $productObj->existencia = $productObj->existencia + $producto['cantidad'];
                $productObj->save();
            }
    
            $detallePago = new DetallePagoCompra;
            $detallePago->num_cheque = $request->detalle_pago_compra['num_cheque'];
            $detallePago->digitos = $request->detalle_pago_compra['digitos'];
            $detallePago->banco = $request->detalle_pago_compra['banco'];
            $detallePago->num_transferencia = $request->detalle_pago_compra['num_transferencia'];
            $detallePago->compras_id = $compra->id;
            $detallePago->save();
        });

        return response()->json(['message' => 'Compra creada'], 201);
    }
}
