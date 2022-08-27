<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <style>
        .logos {
            min-width: 200px;
            max-width: 200px;
        }


        .datos-tabla {
            border-collapse: collapse;
        }


        .datos-tabla tr:nth-child(odd) {
            background-color: #f5f5f5;
        }

        .datos-tabla td,
        .datos-tabla th {
            border: 1px solid #ddd;
            padding: 8px;
        }
    </style>
</head>

<body>
    @include('layouts.estilos')
    <table class="w-100">
        <thead>
            <tr>
                <th class="w-10">
                    <img class="logo logos" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                </th>
                <th class="w-90">
                    <div class="w-100 right uppercase">
                        <p class="line-xxs texto-sm light">{{ $empresa->nombre_comercial }}</p>
                        <p class="line-xxs texto-sm  bold">{{ $datos['name_pdf'] }} <span
                                class="bold">{{ $datos['fecha'] }}</span>
                        </p>
                        <p class="line-xxs texto-sm light">
                            fecha de impresión {{ fechahora_completa() }}
                        </p>
                        <p class="line-xxs texto-sm light">
                            ÁLMACEN DE FUNERARIA
                        </p>

                        <p class="line-xxs texto-sm bold mt-4">
                            <span class="">Total de Movimientos {{ $datos['numero_movimientos'] }}
                            </span>
                        </p>
                        <p class="line-xxs texto-sm bold mt-4">
                            <span class="bg-gray">Salida de Inventario $
                                {{ number_format($datos['totales_generales']['total_salidas_general'],2) }}
                            </span>
                        </p>
                        <p class="line-xxs texto-sm bold mt-4">
                            <span class="bg-gray">Ingreso de Inventario $
                                {{ number_format($datos['totales_generales']['total_entradas_general'],2) }}
                            </span>
                        </p>
                    </div>
                </th>
            </tr>
        </thead>
    </table>


    @foreach ($datos['movimientos'] as $movimiento)
    <!--mostrando segun el tipo de movimiento se crea una tabla diferente-->
    @if ($movimiento['tipo_movimientos_id']==2)
    <!--Ingreso de lotes que no estan en el inventario, inicio del inventario-->
    <table class="w-100 mt-8">
        <tr class="bg-gray">
            <th class="justificar px-2">
                {{ $movimiento['tipo_movimiento_texto'] }}
            </th>
            <th class="right light px-2">
                {{ $movimiento['fecha_movimiento_texto'] }}
            </th>
        </tr>
        <tr class="mt-2">
            <td class="justificar px-2" colspan="2">
                Ingreso de mercancía Lote {{ $movimiento['num_lote_ingreso'] }}
            </td>
        </tr>
    </table>
    <table class="w-100 ml-auto px-1 mt-2 datos-tabla">
        <tr class="">
            <th class="left">
                Artículo
            </th>
            <th class="center">
                Cantidad
            </th>
            <th class="right">
                $ Costo
            </th>
            <th class="right">
                $ Importe
            </th>
        </tr>
        @foreach ($movimiento['ingreso_mercancia'] as $lote)
        <tr class="">
            <td class="left">
                {{ $lote['articulo'] }}
            </td>
            <td class="center">
                {{ $lote['cantidad'] }}
            </td>
            <td class="right">
                $ {{ number_format($lote['costo'],2) }}
            </td>
            <td class="right">
                $ {{ number_format($lote['importe'],2) }}
            </td>
        </tr>
        @endforeach
        <tr class="">
            <td class="right bold" colspan="3">
                Total de Entradas
            </td>
            <td class="right">
                $ {{ number_format($movimiento['total_entradas'],2) }}
            </td>
        </tr>
    </table>
    @elseif ($movimiento['tipo_movimientos_id']==1)
    <!--Ingreso o salida de lotes por ajuste de inventario-->
    <table class="w-100 mt-8">
        <tr class="bg-gray">
            <th class="justificar px-2">
                {{ $movimiento['tipo_movimiento_texto'] }}
            </th>
            <th class="right light px-2">
                {{ $movimiento['fecha_movimiento_texto'] }}
            </th>
        </tr>
    </table>


    <table class="w-100 ml-auto px-1 mt-2 datos-tabla">
        <tr class="">
            <th class="left">
                Artículo
            </th>
            <th class="left">
                Lote
            </th>
            <th class="center">
                Existencia anterior
            </th>
            <th class="right">
                Movimiento
            </th>
            <th class="center">
                Cantidad
            </th>
            <th class="right">
                Costo
            </th>
            <th class="right">
                Importe
            </th>
        </tr>
        @foreach ($movimiento['ajuste_inventario'] as $ajuste)
        <tr class="">
            <td class="left">
                {{ $ajuste['articulo'] }}
            </td>
            <td class="center">
                {{ $ajuste['num_lote_inventario'] }}
            </td>
            <td class="center">
                {{ $ajuste['antigua_existencia'] }}
            </td>
            <td class="center <?= $ajuste['tipo_b']==0?'text-danger':'';?>">
                {{ $ajuste['tipo'] }}
            </td>
            <td class="center">
                {{ $ajuste['cantidad'] }}
            </td>
            <td class="right">
                $ {{ number_format($ajuste['costo'],2) }}
            </td>
            <td class="right <?= $ajuste['tipo_b']==0?'text-danger':'';?>">
                $ {{ number_format($ajuste['importe'],2) }}
            </td>
        </tr>
        @endforeach
        <tr class="">
            <td class="right bold" colspan="6">
                Total de Salidas
            </td>
            <td class="right <?= $ajuste['tipo_b']==0?'text-danger':'';?>">
                $ {{ number_format($movimiento['total_salidas'],2) }}
            </td>
        </tr>
        <tr class="">
            <td class="right bold" colspan="6">
                Total de Entradas
            </td>
            <td class="right">
                $ {{ number_format($movimiento['total_entradas'],2) }}
            </td>
        </tr>
    </table>

    @elseif ($movimiento['tipo_movimientos_id']==3)
    <!--Ingreso por compra de mercancias a proveedor-->
    <table class="w-100 mt-8">
        <tr class="bg-gray">
            <th class="justificar px-2">
                {{ $movimiento['tipo_movimiento_texto'] }}
            </th>
            <th class="right light px-2">
                {{ $movimiento['fecha_movimiento_texto'] }}
            </th>
        </tr>
        <tr class="mt-2">
            <td class="justificar px-2">
                Ingreso de mercancía Lote {{ $movimiento['num_lote_ingreso'] }}
            </td>
            <td class="right light px-2">
                <div class="mt-2">
                    <span class="bold">Nota / Factura</span> {{ $movimiento['folio_referencia'] }}
                </div>
            </td>
        </tr>
        <tr class="mt-2">
            <td class="justificar px-2 <?= $movimiento['status']==0?'text-danger':'';?>">
                Compra {{ $movimiento['status_texto'] }}
            </td>
            <td class="right light px-2">
                <div class="mt-2">
                    <span class="bold">Proveedor</span> {{ $movimiento['proveedor']['razon_social'] }}
                </div>
            </td>
        </tr>
    </table>


    <table class="w-100 ml-auto px-2 mt-2 datos-tabla">
        <tr class="">
            <th class="left">
                Artículo
            </th>
            <th class="center">
                Cantidad
            </th>
            <th class="right">
                $ Costo
            </th>
            <th class="right">
                $ Impuestos
            </th>
            <th class="right">
                $ Importe
            </th>
        </tr>
        @foreach ($movimiento['ingreso_compra'] as $compra)
        <tr class="">
            <td class="left">
                {{ $compra['articulo'] }}
            </td>
            <td class="center">
                {{ $compra['cantidad'] }}
            </td>
            <td class="right">
                $ {{ number_format($compra['costo'],2) }}
            </td>
            <td class="right">
                $ {{ number_format($compra['impuestos'],2) }}
            </td>
            <td class="right">
                $ {{ number_format($compra['importe'],2) }}
            </td>
        </tr>
        @endforeach
        <tr class="bold">
            <td class="right" colspan="4">
                Total
            </td>
            <td class="right">
                $ {{ number_format($movimiento['total_movimiento'],2) }}
            </td>
        </tr>
    </table>



    @elseif ($movimiento['tipo_movimientos_id']==9)
    <!--Salida por venta de mercancia en servicios funerarios o vntas en general-->
    <table class="w-100 mt-8">
        <tr class="bg-gray">
            <th class="justificar px-2">
                {{ $movimiento['tipo_movimiento_texto'] }}
            </th>
            <th class="right light px-2">
                {{ $movimiento['fecha_movimiento_texto'] }}
            </th>
        </tr>
        <tr class="mt-2">
            <td class="justificar px-2 <?= $movimiento['operacion']['status']==0?'text-danger':'';?>">
                Servicio {{ $movimiento['operacion']['status_texto'] }}
            </td>
            <td class="right light px-2">
                <div class="mt-2">
                    {{ $movimiento['operacion']['operacion_texto'] }} Núm.
                    {{ $movimiento['operacion']['numero_servicio'] }}, Cliente:
                    {{ $movimiento['operacion']['cliente']['nombre'] }}
                </div>
            </td>
        </tr>
    </table>


    <table class="w-100 ml-auto px-1 mt-2 datos-tabla">
        <tr class="">
            <th class="left">
                Artículo
            </th>
            <th class="center">
                Cantidad
            </th>
            <th class="center">
                Lote
            </th>
            <th class="right">
                $ Costo
            </th>
            <th class="right">
                $ Precio Venta
            </th>
        </tr>
        @foreach ($movimiento['salida_venta'] as $salida)
        <tr class="">
            <td class="left">
                {{ $salida['articulo'] }}
            </td>
            <td class="center">
                {{ $salida['cantidad'] }}
            </td>
            <td class="center">
                {{ $salida['num_lote_inventario'] }}
            </td>
            <td class="right">
                $ {{ number_format($salida['costo'],2) }}
            </td>
            <td class="right">
                $ {{ number_format($salida['precio_venta'],2) }}
            </td>
        </tr>
        @endforeach
        <tr class="">

            <td class="center bold">
                Totales:
            </td>
            <td class="center">
                {{ $movimiento['cantidad_movimiento'] }}
            </td>
            <td class="right">

            </td>
            <td class="right">
                $ {{ number_format($movimiento['total_movimiento_costos'],2) }}
            </td>
            <td class="right">
                $ {{ number_format($movimiento['total_movimiento'],2) }}
            </td>
        </tr>
    </table>

    @endif
    @endforeach

</body>

</html>