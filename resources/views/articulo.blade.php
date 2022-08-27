@extends('layouts.lista_pdf_layout')
@section('title', 'REPORTE DE ARTICULO')
@section('contenido')
<style>
.proveedor tbody tr td {
    text-align: left;
    padding: 2px 4px;
    font-size: 12pt;
}

.proveedor tbody tr td:nth-child(1) {
    font-weight: bold;
    color: #{{env('maincolor')}};
}

.proveedor {
    text-transform: uppercase;
}

.header td, .header th{
    color: white !important;
    text-align: left;
    font-size: 15pt;
    font-weight: bold;
    padding: 2px 8px;
    background-color: #{{env('maincolor')}};
}

.proveedor.center tr td, .proveedor.center tr th{
    text-align: center;
}

</style>
  <section class="data">
    <table class="proveedor">
            <tbody>
                <tr class="header">
                    <td colspan="2">Información del articulo</td>
                </tr>
                <tr>
                    <td>Codigo de barras:</td>
                    <td>{{ $articulo->codigo_barras }}</td>
                </tr>
                <tr>
                    <td>Nombre:</td>
                    <td>{{ $articulo->nombre }}</td>
                </tr>
                <tr>
                    <td>Tipo de producto:</td>
                    <td>{{ $articulo->tipoProducto->tipo }}</td>
                </tr>
                <tr>
                    <td>Tipo de producto SAT:</td>
                    <td>{{ $articulo->satProductoServicio->descripcion }}</td>
                </tr>
                <tr>
                    <td>Grupo Profeco:</td>
                    <td>{{ $articulo->grupoProfeco->ver_nombre }}</td>
                </tr>
                <tr>
                    <td>Categoria:</td>
                    <td>{{ $articulo->familia->categoria->categoria }}</td>
                </tr>
                <tr>
                    <td>Familia:</td>
                    <td>{{ $articulo->familia->familia }}</td>
                </tr>
                @if ($articulo->tipos_producto_id == 1)
                    <tr>
                        <td>Almacen:</td>
                        <td>{{ $articulo->almacen->almacen }}</td>
                    </tr>
                    <tr>
                        <td>Localizacion:</td>
                        <td>{{ $articulo->localizacion }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Cuenta Predial:</td>
                    <td>{{ $articulo->cuenta_predial }}</td>
                </tr>
                @if ($articulo->tipos_producto_id == 1)
                    <tr>
                        <td>Cantidad minima en inventario:</td>
                        <td>{{ $articulo->minimo }}</td>
                    </tr>
                    <tr>
                        <td>Cantidad maxima en inventario:</td>
                        <td>{{ $articulo->maximo }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Facturable:</td>
                    <td>{{ $articulo->facturable == 1 ? 'Si' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Caduca:</td>
                    <td>{{ $articulo->caduca == 1 ? 'Si' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Rentable:</td>
                    <td>{{ $articulo->rentable == 1 ? 'Si' : 'No' }}</td>
                </tr>
                <tr class="header">
                    <td colspan="2">Precios de venta</td>
                </tr>
                <tr>
                    <td>Precio de compra neto:</td>
                    <td>{{ $articulo->costo_neto }}</td>
                </tr>
                <tr>
                    <td>Unidad de compra:</td>
                    <td>{{ $articulo->unidadCompra->unidad }}</td>
                </tr>
                <tr>
                    <td>Unidad de venta:</td>
                    <td>{{ $articulo->unidadCompra->unidad }}</td>
                </tr>
                @if ($articulo->tipos_producto_id == 1)
                <tr>
                    <td>Factor:</td>
                    <td>{{ $articulo->factor }}</td>
                </tr>
                @endif
                @foreach ($articulo->precios as $precio)
                <tr>
                    <td>{{ $precio->precioParent->precio }}:</td>
                    <td>{{ $precio->precio }}</td>
                </tr>
                @endforeach
                <tr>
                    <td>Impuestos:</td>
                    @if (count($articulo->impuestos))
                        <td>
                        @foreach ($articulo->impuestos as $impuesto)
                            {{ $impuesto->satImpuesto->impuesto }}&nbsp;&nbsp;
                        @endforeach
                        </td>
                    @else
                        <td>Sin impuestos</td>
                    @endif
                </tr>
                <tr>
                    <td>Retenciones:</td>
                    @if (count($articulo->retenciones))
                        <td>
                        @foreach ($articulo->retenciones as $impuesto)
                            {{ $impuesto->satImpuesto->impuesto }}&nbsp;&nbsp;
                        @endforeach
                        </td>
                    @else
                        <td>Sin retenciones</td>
                    @endif
                </tr>
            </tbody>
        </table>
                
        @if ($articulo->tipos_producto_id === 3)
            <table class="proveedor center">
                <thead>
                    <tr class="header">
                        <th colspan="3">Articulos del paquete</th>
                    </tr>
                    <tr>
                        <th>Cantidad</th>
                        <th>Codigo de barras</th>
                        <th>Nombre del producto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articulo->paquete as $item)
                    <tr>
                        <td>{{ $item->cantidad }}</td>
                        <td>{{ $item->articulo->codigo_barras }}</td>
                        <td>{{ $item->articulo->nombre }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
  </section>
@endsection