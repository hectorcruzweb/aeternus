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
                                Descripción
                            </div>
                            <p class="control-valor">
                                {{ $cuota['descripcion'] }}
                            </p>
                            <div class="control bg-header size-13px">
                                Periodo de Mantenimiento
                            </div>
                            <p class="control-valor">
                                {{ $cuota['periodo'] }}
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
                                    <span class="uppercase bold size-15px letter-spacing-1">Clave de
                                        cuota: {{ $cuota['id'] }}</span><br><br>
                                    <span class="bold">Registró:{{ $cuota['registro']['nombre'] }}</span> <br>
                                    <span class="bold">Status: {{ $cuota['status_texto'] }}</span>
                                    <br>
                                    <span class="bold">Monto Cuota: $ {{ number_format($cuota['cuota_total'], 2) }}
                                    </span>

                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="w-50">
                        <table class="w-100">
                            <tr>
                                <td class="w-100 right">
                                    <span class="uppercase bold size-15px letter-spacing-1">Cuota:</span><br><br>
                                    <span class="bold">Propiedades a cobrar:
                                        {{ $cuota['num_pagos_programados_vigentes'] }}</span>
                                    <br>
                                    <span class="bold">Total a cobrar: $
                                        {{ number_format($cuota['total_x_cuota'], 2) }}</span>
                                    <br>
                                    <span class="bold">Total cobrado: $
                                        {{ number_format($cuota['total_cubierto'], 2) }}</span>
                                    <br>
                                    <span class="bold">Saldo neto: $
                                        {{ number_format($cuota['saldo_neto'], 2) }}</span>
                                    <br>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="py-3 ">
            <span class="uppercase bold size-15px">Desglose de pagos vencidos de cuota en cementerio:</span>
        </div>

        <div class="w-100">
            <table class="w-100 size-14px pagos_tabla">
                <thead>
                    <tr>
                        <td class="center"><span class="bold uppercase px-2">#</span></td>
                        <td class="center"><span class="bold uppercase px-2">Núm. Venta</span></td>
                        <td class="center"><span class="bold uppercase px-2">Cliente</span></td>
                        <td class="center"><span class="bold uppercase px-2">Tél.</span></td>
                        <td class="center"><span class="bold uppercase px-2">Ubicación</span></td>
                        <td class="center"><span class="bold uppercase px-2">$ Pagado.</span></td>
                        <td class="center"><span class="bold uppercase px-2">Saldo</span></td>
                        <td class="center"><span class="bold uppercase px-2">Días de vencido</span></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cuota['propiedades'] as $index => $propiedad)
                        @if ($propiedad['saldo_neto'] > 0 && $propiedad['get_pagos_pagos_programados'][0]['status_pago'] == 0)
                            <tr>
                                <td class="center"><span class="bold uppercase px-2">{{ $index + 1 }}</span></td>
                                <td class="center"><span
                                        class=" uppercase px-2">{{ $propiedad['ventas_terrenos_id'] }}</span></td>
                                <td class="center"><span
                                        class=" uppercase px-2">{{ $propiedad['cliente']['nombre'] }}</span></td>
                                <td class="center"><span
                                        class=" uppercase px-2">{{ $propiedad['cliente']['celular'] }} /
                                        {{ $propiedad['cliente']['telefono'] }}</span></td>
                                <td class="center"><span
                                        class=" uppercase px-2">{{ $propiedad['venta_terreno']['ubicacion_texto'] }}</span>
                                </td>
                                <td class="center"><span class=" uppercase px-2">$
                                        {{ number_format($propiedad['total_cubierto'], 2) }}</span>
                                </td>
                                <td class="center"><span class=" uppercase px-2">$
                                        {{ number_format($propiedad['saldo_neto'], 2) }}</span>
                                </td>
                                <td class="center"><span
                                        class=" uppercase px-2">{{ $propiedad['get_pagos_pagos_programados'][0]['dias_vencido'] }}</span>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>


</body>

</html>
