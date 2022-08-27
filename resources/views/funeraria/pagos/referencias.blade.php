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
                        <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo logos -mt-6">
                    </th>
                    <th class="w-50">

                    </th>
                    <th class="w-25">
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                Número de convenio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_convenio'] }}
                            </p>
                            <div class="control bg-header size-13px">
                                Número de venta (Plan Funerario a Futuro)
                            </div>
                            <p class="control-valor">
                                {{ $datos['ventas_planes_id'] }}
                            </p>
                        </div>
                    </th>

                </tr>
            </thead>
        </table>
        <div class="ficha">
            <span class="uppercase bold size-16px letter-spacing-1">Talonario de Pagos</span>
            <p class="texto-base justificar line-base">
                Puede realizar el pago en la sucursal de <span
                    class="bold uppercase">{{$empresa->nombre_comercial}}</span>
                llevando este documento o en
                cualquier sucursal
                <span class="santander">Santander</span> (depósito o transferencia), debiendo tomar en cuenta
                los plazos y cantidades establecidas según su contrato.
            </p>

            <table class="w-100 texto-base">
                <tbody>
                    <tr>
                        <td width="21%">
                            <div class="py-1 bg-header">
                                <div class="px-7px bold"> Nombre del Titular</div>
                            </div>
                        </td>
                        <td width="48%">
                            <div class="px-1px">
                                <div class="bg-gray px-7px py-1 center uppercase">
                                    {{$datos['nombre']}}
                                </div>
                            </div>
                        </td>
                        <td width="16%">
                            <div class="px-1px">
                                <div class="bg-header px-7px py-1 center bold">
                                    Fecha Venta
                                </div>
                            </div>

                        </td>
                        <td width="15%">
                            <div class="bg-gray px-6px py-1 center uppercase">
                                {{fecha_abr($datos['fecha_operacion'])}}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>



            <table class="w-100 texto-base pagos_tabla center mt-4">
                <thead>
                    <tr>
                        <td class="py-2"><span class="bold">Banco para depósitos</span></td>
                        <td><span class="bold">Número de cuenta</span></td>
                        <td><span class="bold">Tranferencias electrónicas</span></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="20%">
                            <img class="logo logos ml-auto mr-auto" src="{{public_path('images/santander.png')}}"
                                alt="">
                        </td>

                        <td width="40%">
                            <span class="letter-spacing-2 size-16px">
                                {{$empresa->cuenta}}
                            </span>
                        </td>
                        <td width="40%">
                            <div class="px-1 py-1 uppercase"> <span class="bold size-14px">Clabe: </span> <span
                                    class="letter-spacing-1">
                                    {{$empresa->clabe}}
                                </span>
                            </div>
                            <div class="px-1 py-2 uppercase"> <span class="bold size-14px">Beneficiario:</span>
                                {{$empresa->razon_social}}</div>
                        </td>
                    </tr>
                </tbody>
            </table>





            <table class="w-100 texto-base mt-5 pagos_tabla center">
                <thead>
                    <tr>
                        <td class="py-2 bold">#</td>
                        <td><span class="bold">Referencia Pago</span></td>
                        <td><span class="bold">Concepto</span></td>
                        <td><span class="bold">Fecha Máxima de Pago</span></td>
                        <td><span class="bold">Estatus</span></td>
                        <td><span class="bold">$ Monto a Pronto Pago</span></td>
                        <td><span class="bold">$ Monto Normal</span></td>
                        <td><span class="bold">$ Pagado</span></td>
                        <td><span class="bold">$ Intereses</span></td>
                        <td><span class="bold">$ Saldo</span></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datos['pagos_programados'] as $pago)
                    @if ($pago['status']!=0)
                    <tr>
                        <td class="py-2"><span class="bold">{{ $pago['num_pago'] }}</span></td>
                        <td>{{ $pago['referencia_pago'] }}</td>
                        <td>{{ $pago['concepto_texto'] }}</td>
                        <td>
                            @if ($pago['status_pago']==0)
                            {{ fecha_abr($pago['fecha_a_pagar']) }}
                            @elseif($pago['status_pago']==1)
                            {{ fecha_abr($pago['fecha_programada']) }}
                            @else
                            {{ fecha_abr($pago['fecha_ultimo_pago']) }}
                            @endif

                        </td>
                        <td>{{ $pago['status_pago_texto'] }}</td>
                        <td>
                            @if ($pago['conceptos_pagos_id']==1 || $pago['conceptos_pagos_id']==3)
                            $ {{ number_format($pago['monto_programado'],2)}}
                            @else
                            $ {{ number_format($pago['monto_pronto_pago'],2)}}
                            @endif

                        </td>
                        <td>$ {{ number_format($pago['monto_programado'],2)}}</td>
                        <td>$ {{ number_format($pago['total_cubierto'],2)}}</td>
                        <td>$ {{ number_format($pago['intereses'],2)}}</td>
                        <td>$ {{ number_format($pago['saldo_neto'],2)}}</td>
                    </tr>
                    @endif
                    @endforeach
                    <tr>
                        <td class="py-2 right" colspan="5"><span class="bold pr-4">Totales</span></td>
                        <td>$ {{ number_format($datos['costo_neto_pronto_pago'],2)}}</td>
                        <td>$ {{ number_format($datos['total'],2)}}</td>
                        <td>$ {{ number_format($datos['total_cubierto'],2)}}</td>
                        <td>$ {{ number_format($datos['intereses'],2)}}</td>
                        <td>$ {{ number_format($datos['saldo_neto'],2)}}</td>
                    </tr>
                </tbody>
            </table>


            <div class="instrucciones">
                <div class="mt-3">
                    <span class="bold uppercase">nota importante:</span>
                </div>

                <p class="texto-sm justificar line-base">
                    Este documento no es un comprobante de pago si no cuenta con el sello del banco o la firma del
                    personal
                    de
                    <span class="capitalize bold">{{$empresa->nombre_comercial}}</span> que haya registrado el pago.
                </p>
                <p class="texto-sm justificar line-base">
                    El titular deberá hacer llegar una copia del comprobante de pago al personal responsable de <span
                        class="capitalize bold">{{$empresa->nombre_comercial}}</span>.
                </p>
                <p class="texto-sm justificar line-base">
                    El titular de este convenio deberá guardar las copias de los recibos para futuras aclaraciones.
                </p>
                <p class="texto-sm justificar line-base">
                    Para solicitar su factura, debe acudir a las instalaciones de <span
                        class="capitalize bold">{{$empresa->nombre_comercial}}</span> a más tardar a fin de mes que haya
                    realizado la
                    operación de pago.
                </p>


                <p class="texto-sm justificar line-base">
                    <span class="uppercase bold texto-sm underline pr-2">cláusula Vigésima tercera.- </span>
                    En caso de retrasarse en el pago mensual de las aportaciones, “El Cliente” deberá cubrir una pena
                    convencional sobre el total del monto de la mensualidad vencida,
                    importe que se considerará como aportación
                    adicional complementaria al cliente. El contratante se obliga a pagar a la agencia funeraria interés
                    moratorio del <span class="bold">{{$datos['ajustes_politicas']['tasa_fija_anual']}}</span>%
                    ({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['tasa_fija_anual'],'',false) }} por
                    ciento)
                    fija
                    anual, la que se calculará y liquidará sobre
                    cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
                    calcularán multiplicando el monto de lo que adeude el contratante por la tasa de interés
                    anual, dividida entre 365, este resultado se multiplica por el número de días transcurridos
                    entre la fecha de pago que debió ser hecho y la fecha que el contratante liquide el adeudo.
                </p>

            </div>
        </div>
</body>

</html>