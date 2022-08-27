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
                        <img class="logo logos -mt-6" src="{{public_path(env('LOGOJPG'))}}" alt="">
                    </th>
                    <th class="w-40">

                    </th>
                    <th class="w-35">
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                Num de Compra
                            </div>
                            <p class="control-valor">
                                {{ $datos['num_compra'] }}
                            </p>
                            <div class="control bg-header size-13px">
                                Fecha de Compra
                            </div>
                            <p class="control-valor">
                                {{ $datos['fecha_compra_texto'] }}
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
                                    <span class="uppercase bold size-15px letter-spacing-1">Nota de
                                        Compra:</span><br><br>
                                    <span class="bold">Registró:</span> {{ $datos['registro']['nombre'] }}<br>
                                    <span class="bold">Status:</span> {{ $datos['status_texto'] }}
                                    <br>
                                    <span class="bold">Fecha de Registro:</span>
                                    {{ $datos['fecha_registro_texto'] }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="w-50">
                        <table class="w-100">
                            <tr>
                                <td class="w-100 right">
                                    <span class="uppercase bold size-15px letter-spacing-1">Provedor:</span><br><br>
                                    <span class="bold">Razón Social:</span>
                                    {{ $datos['razon_social'] }}<br>
                                    <span class="bold">Nombre Comercial:</span>
                                    {{ $datos['nombre_comercial'] }}<br>
                                    <span class="bold">Nombre del Contacto:</span>
                                    {{ $datos['nombre_contacto'] }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="py-3 ">
            <span class="uppercase bold size-15px">Desglose de artículos de la compra:</span>
        </div>

        <div class="w-100">
            <table class="w-100 size-15px pagos_tabla">
                <thead>
                    <tr>
                        <td class="center"><span class="bold uppercase px-2">#</span></td>
                        <td class="center"><span class="bold uppercase px-2">Descripción</span></td>
                        <td class="center"><span class="bold uppercase px-2">$ Subt.</span></td>
                        <td class="center"><span class="bold uppercase px-2">$ Desc.</span></td>
                        <td class="center"><span class="bold uppercase px-2">$ IVA</span></td>
                        <td class="center"><span class="bold uppercase px-2">Cant.</span></td>
                        <td class="center"><span class="bold uppercase px-2">$ Imp.</span></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datos['compra_detalle'] as $index => $detalle)
                    <tr>
                        <td class="py-1 center"><span class="px-2 uppercase">{{ $index+1 }}</span>
                        </td>
                        <td class="py-1 center"><span class="px-2 uppercase">{{ $detalle['descripcion'] }}</span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                {{ number_format($detalle['subtotal'],2)}}
                            </span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                {{ number_format($detalle['descuento'],2)}}
                            </span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                {{ number_format($detalle['iva'],2)}}
                            </span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                {{ $detalle['cantidad']}}
                            </span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                {{ number_format($detalle['importe'],2)}}
                            </span>
                        </td>
                    </tr>
                    @endforeach


                    @foreach ($datos['costos_incurridos'] as $index => $costo)
                    <tr>
                        <td class="py-1 center"><span class="px-2 uppercase">{{ $index+1 }}</span>
                        </td>
                        <td class="py-1 center"><span class="px-2 uppercase">{{ $costo['costo_detalle'] }}</span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                {{ number_format($costo['subtotal'],2)}}
                            </span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                0,00
                            </span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                {{ number_format($costo['iva'],2)}}
                            </span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                1
                            </span>
                        </td>
                        <td class="py-1 center">
                            <span class="px-2 capitalize">
                                {{ number_format($costo['costo_neto'],2)}}
                            </span>
                        </td>
                    </tr>
                    @endforeach



                    <tr>
                        <td rowspan="10" colspan="5" class="w-50">
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
                                    total de la compra:
                                </span>
                                <div class="px-2 pt-1">
                                    <span class="uppercase">
                                        {{ $datos['total_compra_texto'] }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                TASA IVA
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                {{ $datos['iva_porcentaje'] }} %
                            </span>
                        </td>
                    </tr>


                    <tr>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                SUBTOTAL
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['subtotal'],2)}}
                            </span>
                        </td>
                    </tr>
                    <tr>

                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                DESCUENTO
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['descuento'],2)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                IVA
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['iva'],2)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                TOTAL
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['total'],2)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center bg-666" colspan="2">
                            <span class="px-2 uppercase size-12px bold">
                                total pagado
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center ">
                            <span class="px-2 uppercase size-12px bold">
                                efectivo
                            </span>
                        </td>
                        <td class="right ">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['pago_efectivo'],2)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center ">
                            <span class="px-2 uppercase size-12px bold">
                                cheque
                            </span>
                        </td>
                        <td class="right ">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['pago_cheque'],2)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center ">
                            <span class="px-2 uppercase size-12px bold">
                                tarjeta
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['pago_tarjeta'],2)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 capitalize center">
                            <span class="px-2 uppercase size-12px bold">
                                transferencia
                            </span>
                        </td>
                        <td class="right">
                            <span class="px-2 bold">
                                $ {{ number_format($datos['pago_transferencia'],2)}}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


</body>

</html>