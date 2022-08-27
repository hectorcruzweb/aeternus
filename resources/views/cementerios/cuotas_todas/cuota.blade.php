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
                                Reporte
                            </div>
                            <p class="control-valor">
                                Cuotas de cementerio
                            </p>
                        </div>
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                Fecha de impresión
                            </div>
                            <p class="control-valor">
                                {{ fecha_abr(today()) }}
                            </p>
                        </div>
                    </th>

                </tr>
            </thead>
        </table>

        <div class="pt-7 pb-3">
            <span class="uppercase bold size-15px">Desglose de cuotas de mantenimiento programadas:</span>
        </div>

        <div class="w-100">
            <table class="w-100 size-14px pagos_tabla">
                <thead>
                    <tr>
                        <td class="center"><span class="bold uppercase px-2">Núm. Cuota</span></td>
                        <td class="center"><span class="bold uppercase px-2">Descripción</span></td>
                        <td class="center"><span class="bold uppercase px-2">Periodo</span></td>
                        <td class="center"><span class="bold uppercase px-2">MONTO CUOTA</span></td>
                        <td class="center"><span class="bold uppercase px-2">TOTAL DE PROPIEDADES</span></td>
                        <td class="center"><span class="bold uppercase px-2">TOTAL a cobrar</span></td>
                        <td class="center"><span class="bold uppercase px-2">TOTAL Cobrado</span></td>
                        <td class="center"><span class="bold uppercase px-2">Saldo neto</span></td>
                        <td class="center"><span class="bold uppercase px-2">estatus</span></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cuotas as $index => $cuota)
                        <tr>
                            <td class="center"><span class=" uppercase px-2">{{ $cuota['id'] }}</span></td>
                            <td class="center"><span class=" uppercase px-2">{{ $cuota['descripcion'] }}</span></td>
                            <td class="center"><span class=" uppercase px-2">{{ $cuota['periodo'] }} </span></td>
                            <td class="center"><span class=" uppercase px-2">$
                                    {{ number_format($cuota['cuota_total'], 2) }}</span></td>
                            <td class="center"><span
                                    class=" uppercase px-2">{{ $cuota['num_pagos_programados_vigentes'] }}</span>
                            </td>
                            <td class="center"><span class=" uppercase px-2">$
                                    {{ number_format($cuota['total_x_cuota'], 2) }}</span>
                            </td>
                            <td class="center"><span class=" uppercase px-2">$
                                    {{ number_format($cuota['total_cubierto'], 2) }}</span>
                            </td>
                            <td class="center"><span class=" uppercase px-2">$
                                    {{ number_format($cuota['saldo_neto'], 2) }}</span>
                            </td>
                            <td class="center"><span class=" uppercase px-2">
                                    {{ $cuota['status_texto'] }}</span>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
        </div>


</body>

</html>
