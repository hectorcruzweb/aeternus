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

        .banco {
            border: 2px solid #E5E8E8 !important;
        }


        .banco-logo {
            display: block;
        }

        .logo {
            display: block;
            margin-right: auto;
        }

        .santander {
            color: #D31413 !important;
        }

        .digito {
            padding: 3px 7px 3px 7px;
            border: 1px solid #dae1e7;
            font-size: 1em;
            line-height: 1.5em;
        }

        .barcode div {
            min-height: 40px !important;
        }

        .bg-total {
            background-color: #FE0000;
            color: #fff;
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
    </style>
</head>

<body>
    @include('layouts.estilos')




    <div class="pagos relative">
        <table class="w-100">
            <thead>
                <tr>
                    <th class="w-25">
                        <img class="logo logos -mt-6" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                    </th>
                    <th class="w-40">

                    </th>
                    <th class="w-35">
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                Comprobante de Pago (Clave)
                            </div>
                            <p class="control-valor">
                                {{ $datos['id'] }}
                            </p>
                            <div class="control bg-header size-13px">
                                Fecha de Pago
                            </div>
                            <p class="control-valor">
                                {{ $datos['fecha_pago_texto'] }} {{ hora($datos['fecha_pago']) }}
                            </p>
                        </div>
                    </th>

                </tr>
            </thead>
        </table>
        <div class="w-100">
            <table class="w-100">
                <tr>
                    <td class="w-50">
                        <table class="w-100">
                            <tr>
                                <td class="w-100 left">
                                    <span class="uppercase bold size-15px letter-spacing-1">comprobante de
                                        pago:</span><br><br>
                                    <span class="bold">Cobrador:</span> {{ $datos['cobrador']['nombre'] }}<br>
                                    <span class="bold">Registró:</span> {{ $datos['registro']['nombre'] }}<br>
                                    <span class="bold">Forma de Pago:</span> ({{ $datos['forma_pago']['clave'] }})
                                    {{ $datos['forma_pago']['forma'] }}<br>
                                    <span class="bold">Fecha de Registro:</span>
                                    {{ fecha_abr($datos['fecha_registro']) }} {{ hora($datos['fecha_registro']) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="w-50">
                        <table class="w-100">
                            <tr>
                                <td class="w-100 right">
                                    <span class="uppercase bold size-15px letter-spacing-1">cliente:</span><br><br>
                                    <span class="bold">Nombre:</span>
                                    {{ $datos['referencias_cubiertas'][0]['operacion_del_pago']['cliente']['nombre'] }}<br>
                                    <span class="bold">Tipo de Operación:</span>
                                    {{ $datos['tipo_operacion_texto'] }}<br>
                                    <span class="bold">Clave Operación:</span>
                                    @if ($datos['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 1)
                                        <!--es de terrenos--->
                                        {{ $datos['referencias_cubiertas'][0]['operacion_del_pago']['ventas_terrenos_id'] }}<br>
                                    @elseif($datos['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 4)
                                        <!--es de planes a futuro--->
                                        {{ $datos['referencias_cubiertas'][0]['operacion_del_pago']['ventas_planes_id'] }}<br>
                                    @elseif($datos['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 3)
                                        <!--es de planes a futuro--->
                                        {{ $datos['referencias_cubiertas'][0]['operacion_del_pago']['servicios_funerarios_id'] }}<br>
                                    @elseif($datos['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 5)
                                        <!--es de ventas en gral.--->
                                        {{ $datos['referencias_cubiertas'][0]['operacion_del_pago']['ventas_generales_id'] }}<br>
                                    @endif
                                    @if ($fallecido != 'N/A')
                                        <span class="bold">Fallecido:</span>
                                        {{ $fallecido }}<br>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="py-3 ">
            <span class="uppercase bold size-15px">desgloce de pagos cubiertos:</span>
        </div>
        <div class="w-100">
            <table class="w-100 size-15px pagos_tabla">
                <thead>
                    <tr>
                        <td><span class="bold uppercase px-2">Clave Pago</span></td>
                        <td><span class="bold uppercase px-2">Concepto</span></td>
                        <td class="py-1"><span class="bold uppercase px-2">ref. pago</span></td>
                        <td class="right"><span class="bold uppercase px-2">$ monto cubierto</span></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datos['referencias_cubiertas'] as $referencia)
                        <tr>
                            <td class="py-1 center"><span class="px-2 uppercase">{{ $datos['id'] }}</span>
                            </td>
                            <td class="py-1"><span
                                    class="px-2 capitalize">{{ $datos['movimientos_pagos_texto'] }}</span>
                            </td>
                            <td class="py-1"><span class="px-2">{{ $referencia['referencia_pago'] }}</span></td>
                            <td class="py-1 right"><span class="px-2">
                                    $ {{ number_format($referencia['pagos_cubiertos']['monto'], 2) }}
                                </span>
                            </td>
                        </tr>
                        @foreach ($datos['subpagos'] as $referencia_subpago)
                            @foreach ($referencia_subpago['referencias_cubiertas'] as $subpago)
                                @if ($subpago['referencia_pago'] == $referencia['referencia_pago'])
                                    <tr>
                                        <td class="py-1 center"><span
                                                class="px-2 uppercase">{{ $referencia_subpago['parent_pago_id'] }}>{{ $referencia_subpago['id'] }}</span>
                                        </td>

                                        <td class="py-1"><span class="px-2 capitalize pl-6">
                                                @if ($referencia_subpago['movimientos_pagos_id'] == 2)
                                                    <img class="w-6" src="{{ public_path('images/increase.png') }}"
                                                        alt="">
                                                @else
                                                    <img class="w-5" src="{{ public_path('images/decrease.png') }}"
                                                        alt="">
                                                @endif
                                                {{ $referencia_subpago['movimientos_pagos_texto'] }}
                                            </span></td>
                                        <td class="py-1"><span
                                                class="px-2 pl-6">{{ $subpago['referencia_pago'] }}</span></td>
                                        <td class="py-1 right"><span class="px-2">
                                                $ {{ number_format($subpago['pagos_cubiertos']['monto'], 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                    <tr>
                        <td rowspan="6" colspan="2" class="w-50">
                            <div class="w-full">
                                <span class="px-2 uppercase size-12px bold">
                                    nota/observación:
                                </span>
                                <div class="px-2 py-1">
                                    <span class="uppercase">
                                        {{ $datos['nota'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-full py-1">
                                <span class="px-2 uppercase size-12px bold">
                                    referencia de depósito:
                                </span>
                                <div class="px-2 pt-1">
                                    <span class="uppercase">
                                        {{ $datos['referencia'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-full py-1">
                                <span class="px-2 uppercase size-12px bold">
                                    banco:
                                </span>
                                <div class="px-2 pt-1">
                                    <span class="uppercase">
                                        {{ $datos['banco'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-full py-1">
                                <span class="px-2 uppercase size-12px bold">
                                    cantidad del movimiento en letra:
                                </span>
                                <div class="px-2 pt-1">
                                    <span class="uppercase">
                                        ({{ numeros_a_letras($datos['total_pago']) }})
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                deuda cubierta
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['deuda_cubierta'], 2) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                <img class="w-10" src="{{ public_path('images/increase.png') }}" alt="">
                                intereses aplicados
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['intereses_aplicados'], 2) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                <img class="w-8" src="{{ public_path('images/decrease.png') }}" alt="">
                                descuento x pronto pago
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['descuento_pronto_pago_aplicado'], 2) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center bg-666">
                            <span class="px-2 uppercase size-12px bold">
                                total del pago
                            </span>
                        </td>
                        <td class="right bg-666">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['total_pago'], 2) }}
                            </span>
                        </td>
                    </tr>
                    <tr>

                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                cantidad con que pagó
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['pago_con_cantidad'], 2) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                cambio a devolver
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['cambio_pago'], 2) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="instrucciones">
            @if ($datos['status'] == 0)
                <br>
                <span class="uppercase bold size-15px text-danger">detalle de cancelación:</span><br>
                <span class="bold">Canceló:</span> {{ $datos['cancelador']['nombre'] }}<br>
                <span class="bold">Motivo:</span> {{ $datos['motivos_cancelacion_texto'] }}<br>
                <span class="bold">Fecha de Cancelación:</span> {{ $datos['fecha_cancelacion_texto'] }}
                {{ hora($datos['fecha_cancelacion']) }}<br>
                <span class="bold">Nota:</span> {{ $datos['nota_cancelacion'] }}<br>
            @endif
            <div class="mt-3">
                <span class="bold uppercase">nota importante:</span>
            </div>

            <p class="texto-sm justificar line-base">
                Este documento no es válido si no cuenta con la firma del
                personal
                de
                <span class="capitalize bold">{{ $empresa->nombre_comercial }}</span> que haya registrado el pago.
            </p>

            <p class="texto-sm justificar line-base">
                El titular de esta operación(convenio, servicio funerario, compra, etc.) deberá guardar las copias de
                los recibos para futuras aclaraciones.
            </p>
            <p class="texto-sm justificar line-base">
                Para solicitar su factura, debe acudir a las instalaciones de <span
                    class="capitalize bold">{{ $empresa->nombre_comercial }}</span> a más tardar a fin de mes que haya
                realizado la
                operación de pago.
            </p>
        </div>


        @if ($datos['cancelo_id'] != null)
            <div class="w-100 center mt-20">
                <div class="w-50 float-left">
                    <img src="{{ $firmas['cobrador'] }}" class="firma">
                    <div class="w-90 mr-auto ml-auto border-top">
                        <div class="pt-3 pb-1"><span
                                class="uppercase  texto-sm">{{ $datos['cobrador']['nombre'] }}</span></div>
                        <span class="uppercase bold texto-sm">"Recibió el pago"</span>
                    </div>
                </div>
                <div class="w-50 float-right">
                    <img src="{{ $firmas['cancelo'] }}" class="firma">
                    <div class="w-90 mr-auto ml-auto border-top">
                        <div class="pt-3 pb-1"><span
                                class="uppercase  texto-sm">{{ $datos['cobrador']['nombre'] }}</span></div>
                        <span class="uppercase bold texto-sm">"Canceló pago"</span>
                    </div>
                </div>
            </div>
        @else
            <div class="w-100 center mt-20">
                <div class="w-50 mr-auto ml-auto">
                    <img src="{{ $firmas['cobrador'] }}" class="firma">
                    <div class="w-90 mr-auto ml-auto border-top">
                        <div class="pt-3 pb-1"><span
                                class="uppercase  texto-sm">{{ $datos['cobrador']['nombre'] }}</span></div>
                        <span class="uppercase bold texto-sm">"Recibió el pago"</span>
                    </div>
                </div>
            </div>
        @endif




</body>

</html>
