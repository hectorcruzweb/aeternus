<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
        rel="stylesheet">
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
                        <img class="logo logos -mt-6" src="{{asset('images/aeternus/LogoEmp.jpg')}}" alt="">
                    </th>
                    <th class="w-40">

                    </th>
                    <th class="w-35">
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                clave de venta
                            </div>
                            <p class="control-valor">
                                {{ $datos['venta_terreno']['id'] }}
                                <div class="control bg-header size-13px">
                                    Fecha de Cancelación
                                </div>
                                <p class="control-valor">
                                    {{ fecha_abr($datos['fecha_cancelacion_operacion']) }}
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
                                    <span class="uppercase bold size-15px letter-spacing-1">Acuse de Cancelación de
                                        Venta de Terreno:</span><br><br>
                                    <span class="bold uppercase">Registró acuse:</span> <span
                                        class="uppercase">{{ $datos['cancelador']['nombre'] }}</span><br>
                                    <span class="bold uppercase">Cliente:</span> {{ $datos['nombre'] }}<br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="instrucciones mt-15">
            <p class="texto-base justificar line-base">
                Por medio de la presente, <span class="capitalize bold">{{$empresa->nombre_comercial}}</span> notifica
                la cancelación del contrato con número de convenio: <span
                    class="capitalize bold">{{$datos['numero_convenio']}}</span> por concepto de venta de
                terreno con las siguientes características: tipo propiedad <span
                    class="capitalize bold">{{$datos['venta_terreno']['tipo_texto']}}</span>, ubicación <span
                    class="capitalize bold">{{$datos['venta_terreno']['ubicacion_texto']}}</span> y capacidad de (<span
                    class="capitalize bold">{{$datos['venta_terreno']['tipo_propiedad']['capacidad']}}</span>)
                Gaveta(s).
            </p>

            <p class="texto-base justificar line-base">
                Evento producido por el siguiente motivo: <span
                    class="capitalize bold">{{ $datos['motivos_cancelacion_texto'] }}</span>. Para lo cual
                <span class="capitalize bold">{{$empresa->nombre_comercial}}</span> acordó con el
                solicitante de
                cancelación que se entregará como devolución la cantidad de <span class="capitalize bold">$
                    {{ number_format($datos['cantidad_a_regresar_cancelacion'],2)}}</span>
                ({{ numeros_a_letras($datos['cantidad_a_regresar_cancelacion']) }}) y el resto en
                caso de
                existir se tomará como penalización por cancelación de contrato según las políticas de la
                empresa.
            </p>

            <p class="texto-base justificar line-base">
                La cancelación de dicho contrato se lleva a cabo en <span class="lowercase bold capitalize">Mazatlán,
                    Sinaloa
                    México a
                    <span class="lowercase">{{ fecha_only($datos['fecha_cancelacion_operacion']) }}</span></span>.
            </p>

            <p class="texto-base justificar line-base">
                <span class="uppercase bold">Nota / comentarios</span>.
            </p>

            <p class="texto-base justificar line-base">
                {{ $datos['nota_cancelacion'] }}
            </p>
        </div>

        <div class="w-100 center">
            <div class="w-50 float-left mt-20">
                <img src="{{ $firmas['gerente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top">
                    <div class="">
                        <span class="uppercase  texto-sm">{{ $empresa->razon_social }}</span>
                    </div>
                    <span class="uppercase bold texto-sm">"la empresa"</span>
                </div>
            </div>
            <div class="w-50 float-right mt-20">
                  <img src="{{ $firmas['cliente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top">
                    <div class="">
                        <span class="uppercase  texto-sm">{{ $datos['nombre'] }}</span>
                    </div>
                    <span class="uppercase bold texto-sm">"el cliente"</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>