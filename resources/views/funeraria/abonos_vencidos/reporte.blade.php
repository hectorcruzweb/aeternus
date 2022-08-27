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
                                Abonos vencidos de planes funerarios por cobrar
                            </p>
                            <div class="control bg-header size-13px">
                                Fecha de reporte
                            </div>
                            <p class="control-valor">
                                {{ fecha_abr(today()) }}
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

                    </td>
                    <td class="w-50">

                    </td>
                </tr>
            </table>
        </div>
        <div class="py-3 ">
            <span class="uppercase bold size-15px">Desglose de abonos vencidos de planes funerarios:</span>
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
                        <td class="center"><span class="bold uppercase px-2">Pagos vencidos</span></td>
                        <td class="center"><span class="bold uppercase px-2">Saldo</span></td>
                        <td class="center"><span class="bold uppercase px-2">Intereses</span></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index_r = 1;
                    $pagos_vencidos = 0;
                    $saldo_neto = 0;
                    $intereses = 0;
                    ?>
                    @foreach ($ventas as $index => $venta)
                        @if ($venta['saldo_neto'] > 0 && $venta['operacion_status'] != 0)
                            <?php
                            $pagos_vencidos += $venta['pagos_vencidos'];
                            $saldo_neto += $venta['saldo_neto'];
                            $intereses += $venta['intereses'];
                            ?>
                            @if ($venta['financiamiento'] == 1)
                                <!--es de uso inmendiato-->
                                <tr>
                                    <td class="center"><span class="bold uppercase px-2">{{ $index_r }}</span></td>
                                    <td class="center"><span
                                            class="uppercase px-2">{{ $venta['ventas_planes_id'] }}</span>
                                    </td>
                                    <td class="center"><span class="uppercase px-2">{{ $venta['nombre'] }}</span></td>
                                    <td class="center"><span class="uppercase px-2">{{ $venta['celular'] }} /
                                            {{ $venta['telefono'] }}</span></td>
                                    <td class="center"><span
                                            class="uppercase px-2">{{ $venta['venta_plan']['nombre_original'] }}
                                        </span></td>
                                    <td class="center"><span class="uppercase px-2">N/A</span>
                                    </td>
                                    <td class="center"><span class="uppercase px-2">$
                                            {{ number_format($venta['saldo_neto'], 2) }}</span></td>
                                    <td class="center"><span class="uppercase px-2">$
                                            {{ number_format($venta['intereses'], 2) }}</span></td>
                                </tr>
                            @else
                                <!--es de abonos-->
                                @if ($venta['pagos_vencidos'] > 0)
                                    <tr>
                                        <td class="center"><span class="bold uppercase px-2">{{ $index_r }}</span>
                                        </td>
                                        <td class="center"><span
                                                class="uppercase px-2">{{ $venta['ventas_planes_id'] }}</span>
                                        </td>
                                        <td class="center"><span class="uppercase px-2">{{ $venta['nombre'] }}</span>
                                        </td>
                                        <td class="center"><span class="uppercase px-2">{{ $venta['celular'] }} /
                                                {{ $venta['telefono'] }}</span></td>
                                        <td class="center"><span
                                                class="uppercase px-2">{{ $venta['venta_plan']['nombre_original'] }}
                                            </span></td>
                                        <td class="center"><span
                                                class="uppercase px-2 bold">{{ $venta['pagos_vencidos'] }}</span>
                                        </td>
                                        <td class="center"><span class="uppercase px-2">$
                                                {{ number_format($venta['saldo_neto'], 2) }}</span></td>
                                        <td class="center"><span class="uppercase px-2">$
                                                {{ number_format($venta['intereses'], 2) }}</span></td>
                                    </tr>
                                @endif
                            @endif
                            <?php
                            $index_r++;
                            ?>
                        @endif
                    @endforeach
                    <tr>
                        <td class="py-3 right" colspan="5"><span class="bold uppercase px-2">Totales :
                            </span>
                        </td>

                        <td class="center"><span class="uppercase px-2 bold">{{ $pagos_vencidos }}</span>
                        </td>
                        <td class="center"><span class="uppercase px-2">
                                $ {{ number_format($saldo_neto, 2) }}</span></td>
                        <td class="center"><span class="uppercase px-2">
                                $ {{ number_format($intereses, 2) }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
