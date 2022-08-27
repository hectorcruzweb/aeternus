<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('css/w3.css') }}">
    <title>Reportes</title>
    <style>
        #header,
        #header section table {
            width: 100% !important;
            padding-top: 0px;
        }

        #header section table {
            border-collapse: collapse !important;
        }

        .logo {
            max-width: 100% !important;
        }


        h1 {
            font-size: 1em;
            line-height: .8em !important;
            text-transform: uppercase;
            text-align: center;
        }

        .datos-header {
            text-align: center !important;
            font-size: .9em;
            line-height: 0.7em !important;
            text-transform: uppercase !important;
        }

        .numeros-contrato {
            width: 100% !important;
        }

        .numeros-contrato .control {
            text-align: center;
            text-transform: uppercase !important;
            font-size: .8em;
            line-height: 1.9em !important;
            font-weight: 600 !important;
        }

        .control-valor {
            text-align: center;
            font-size: .9em;
            line-height: .3em !important;
            text-transform: uppercase;
        }

        /*parrafos**/
        .contenido {
            padding: 10px 0 0 0 !important;
            margin: 0 !important;
        }

        /*fin de parrafos*/
    </style>
</head>

<body>
    @include('layouts.estilos')
    <header id="header">
        <section>
            <table>
                <tr>
                    <td style="width:23%;">
                        <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo">
                    </td>
                    <td style="width:53%;">
                        <h1>
                            {{ $empresa->razon_social }}
                        </h1>
                        <p class="datos-header">
                            {{ strtolower($empresa->calle) }} Núm. Ext {{ $empresa->num_ext }}
                        </p>
                        <p class="datos-header">
                            Col. {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
                            {{ $empresa->ciudad }}
                            {{ $empresa->estado }}
                        </p>
                        <p class="datos-header">
                            Tel. {{ $empresa->telefono }}, fax {{ $empresa->fax }}
                        </p>
                    </td>
                    <td style="width:25%;">
                        <div class="numeros-contrato">
                            <div class="control bg-gray">
                                solicitud de servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_solicitud_texto'] }}
                            </p>

                            <div style=""></div>
                            <div class="control bg-gray">
                                Número de convenio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_convenio'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-header text-white py-1 px-2 bold mb-1 texto-sm">
            estado de cuenta a la fecha {{fechahora_completa()}}
        </div>
        <table class="w-100 center">
            <tr>
                <td class="w-55 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-15 left">
                            <span class="bold uppercase texto-sm">
                                titular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-85 center">
                            {{ $datos['nombre'] }}
                        </div>
                    </div>
                </td>
                <td class="w-45 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-45 left">
                            <span class="bold uppercase texto-sm">
                                fecha de la venta:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-55 center">
                            {{ fecha_no_day($datos['fecha_operacion']) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100 center">
            <tr>
                <td class="w-100 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-20 left">
                            <span class="bold uppercase texto-sm">
                                plan funerario:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-80 center">
                            {{ $datos['venta_plan']['nombre_original'] }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100">
            <tr>
                <td class="w-45 px-0 py-2">
                    <div class="uppercase bg-header text-white py-1 px-2 bold mb-1 texto-sm">
                        costos acordados
                    </div>
                    <table class="w-100">
                        <tr>
                            <td class="w-50 py-1 left bg-nada">

                                <span class="bold"> plan de venta:</span>
                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                {{ $datos['tipo_financimiento_texto'] }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 py-1 left bg-nada">
                                <span class="bold"> sub-total propiedad:</span>

                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $ {{ number_format( $datos['subtotal'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 py-1 left bg-nada">

                                <span class="bold">iva:</span>
                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $ {{ number_format( $datos['impuestos'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 py-1 left bg-nada">
                                <span class="bold">descuento:</span>

                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $ {{ number_format( $datos['descuento'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 py-1 left bg-nada">

                                <span class="bold">costo neto propiedad:</span>
                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $ {{ number_format( $datos['total'],2)}}
                            </td>
                        </tr>

                        <tr>
                            <td class="w-50 py-1 left bg-nada">
                                <span class="bold">estatus del contrato: </span>

                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                <!--se hace una condicion para determinar que estatus mandar segun si la cuenta va al corriente o no-->
                                @if ($datos['pagos_vencidos']>0)
                                @if ($datos['dias_vencidos']>$datos['ajustes_politicas']['maximo_dias_retraso'])
                                <span class="text-danger bold">adeudo crítico</span>
                                @else
                                <span class="text-danger bold">falta de pago</span>
                                @endif
                                @else
                                <!--se esta al corriente-->
                                <span class="bold">al corriente</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="w-55 pl-3">
                    <div class="uppercase bg-header text-white py-1 px-2 bold mb-1 texto-sm">
                        resumen de saldos y estado
                    </div>
                    <table class="w-100">
                        <tr>
                            <td class="w-50 py-1 left bg-nada">

                                <span class="bold">intereses generados:</span>
                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $ {{ number_format( $datos['intereses']+$datos['abonado_intereses'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 py-1 left bg-nada">

                                <span class="bold">intereses abonados:</span>
                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $ {{ number_format( $datos['abonado_intereses'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 py-1 left bg-nada">
                                <span class="bold">descuentos realizados:</span>

                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $
                                {{ number_format( $datos['descontado_pronto_pago']+$datos['descontado_capital']+$datos['complementado_cancelacion'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 py-1 left bg-nada">
                                <span class="bold">pagos vencidos: </span>
                            </td>

                            <td class="w-50 py-1 right bg-gray">
                                @if ($datos['pagos_vencidos']>0)
                                {{ $datos['pagos_vencidos']}} Pagos ({{ $datos['dias_vencidos']}} días.)
                                @else
                                0
                                @endif

                            </td>
                        </tr>

                        <tr>
                            <td class="w-50 py-1 left bg-nada">
                                <span class="bold">total cubierto(s/interés):</span>

                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $
                                {{ number_format($datos['total_cubierto'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 py-1 left bg-nada">
                                <span class="bold">saldo actual:</span>

                            </td>
                            <td class="w-50 py-1 right bg-gray">
                                $
                                {{ number_format( $datos['saldo_neto'],2)}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-header text-white py-1 px-2 bold mb-1 texto-base">
            detalle de movimientos realizados
        </div>
        @foreach ($datos['pagos_programados'] as $programado)
        <div class="pt-4">
            <table class="w-100 w3-table w3-table-all">
                <thead>
                    <tr>
                        <th class=" center py-1 bg-header text-white"><span class="bold px-2">#</th>
                        <th class=" center py-1 bg-header text-white"><span class="bold px-2">Estatus</th>
                        <th class=" center py-1 bg-header text-white"><span class="bold px-2">Ref. Pago</th>
                        <th class=" center py-1 bg-header text-white"><span class="bold px-2">Concepto</th>
                        <th class=" right py-1 bg-header text-white"><span class="bold">Monto</th>
                        <th class=" right py-1 bg-header text-white"><span class="bold">intereses</th>
                        <th class=" right py-1 bg-header text-white"><span class="bold">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center capitalize">
                            <span class="bold texto-sm">{{$programado['num_pago']}}</span>
                        </td>
                        <td class="center py-2 capitalize">
                            {{ $programado['status_pago_texto'] }}
                        </td>
                        <td class="center py-2">{{$programado['referencia_pago']}}</td>
                        <td class="center py-2 capitalize">{{$programado['concepto_texto']}}</td>
                        <td class="right py-2 px-2">
                            $ {{number_format($programado['monto_programado'],2)}}
                        </td>
                        <td class="right py-2 px-2">
                            $ {{number_format($programado['intereses']+$programado['abonado_intereses'],2)}}
                        </td>
                        <td class="right py-2 px-2">
                            $ {{number_format($programado['saldo_neto'],2)}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!--checando que tenga movimientos que mostrar-->
        @php
        $mostrar=false;
        @endphp

        @foreach ($pagos_operacion as $pago)
        @foreach ($pago['referencias_cubiertas'] as $cubierta)
        @if ($cubierta['referencia_pago']==$programado['referencia_pago'])
        @php
        $mostrar=true;
        @endphp
        @break
        @endif
        @endforeach
        @endforeach

        @if ($mostrar==true)
        <div class="w-100">
            <div class="bold uppercase py-2">
                movimientos sobre esta referencia
            </div>
            <table class="w3-table w3-striped w3-small">
                <thead>
                    <tr>
                        <td class="center">
                            <span class="px-2 bold">Clave del movimiento</span>
                        </td>
                        <td class="center">
                            <span class="px-2 bold">Fecha</span>
                        </td>
                        <td class="center">
                            <span class="px-2 bold">Concepto</span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">Monto</span>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagos_operacion as $pago)
                    @foreach ($pago['referencias_cubiertas'] as $cubierta)
                    @if ($cubierta['referencia_pago']==$programado['referencia_pago'])
                    <tr>
                        <td class="center">
                            <span class="px-2 capitalize">{{ $pago['id'] }}</span>
                        </td>
                        <td class="center">
                            <span class="px-2 capitalize">{{ $pago['fecha_pago_texto'] }}</span>
                        </td>
                        <td class="center">
                            <span class="px-2 capitalize">{{ $pago['movimientos_pagos_texto'] }}</span>
                        </td>
                        <td class="center right">
                            <span class="px-2 capitalize">$
                                {{number_format($cubierta['pagos_cubiertos']['monto'],2)}}
                            </span>
                        </td>
                    </tr>
                    <!--mostrando subpagos-->
                    @if (count($pago['subpagos'])>0)
                    @foreach ($pago['subpagos'] as $subpago)
                    @foreach ($subpago['referencias_cubiertas'] as $sub_cubierto)
                    @if ($sub_cubierto['referencia_pago']==$cubierta['referencia_pago'])
                    <tr>
                        <td class="center">
                            <span class="px-2 capitalize">{{ $subpago['parent_pago_id'] }} >
                                {{ $subpago['id'] }}</span>
                        </td>
                        <td class="center">
                            <span class="px-2 capitalize">{{ $subpago['fecha_pago_texto'] }}</span>
                        </td>
                        <td class="center">
                            <span class="px-2 capitalize">{{ $subpago['movimientos_pagos_texto'] }}</span>
                        </td>
                        <td class="center right">
                            <span class="px-2 capitalize">$
                                {{number_format($sub_cubierto['pagos_cubiertos']['monto'],2)}}
                            </span>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    @endforeach
                    <!--end foreach subpagos-->
                    @endif
                    <!--end if count subpagos-->
                    @endif
                    @endforeach
                    @endforeach
                    <tr>
                        <td class="right" colspan="3">
                            <span class="px-2 capitalize bold">Total abonado a capital</span>
                        </td>
                        <td class="center right">
                            <span class="px-2 capitalize">$ {{number_format($programado['abonado_capital'],2)}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="right" colspan="3">
                            <span class="px-2 capitalize bold">Total descontado x pronto pago</span>
                        </td>
                        <td class="center right">
                            <span class="px-2 capitalize">$
                                {{number_format($programado['descontado_pronto_pago'],2)}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="right" colspan="3">
                            <span class="px-2 capitalize bold">Total abonado a intereses</span>
                        </td>
                        <td class="center right">
                            <span class="px-2 capitalize">$ {{number_format($programado['abonado_intereses'],2)}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="right" colspan="3">
                            <span class="px-2 capitalize bold">Total descontado a capital</span>
                        </td>
                        <td class="center right">
                            <span class="px-2 capitalize">$
                                {{number_format($programado['descontado_capital'],2)}}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        @endforeach
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>