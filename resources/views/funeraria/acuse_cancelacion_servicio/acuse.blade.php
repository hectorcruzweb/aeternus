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
                        <img class="logo logos -mt-6" src="{{asset('images/aeternus/LogoEmp.jpg')}}" alt="">
                    </th>
                    <th class="w-40">

                    </th>
                    <th class="w-35">
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                clave de servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['id'] }}
                                <div class="control bg-header size-13px">
                                    Fecha de Cancelaci??n
                                </div>
                                <p class="control-valor">
                                    {{ fecha_abr($datos['operacion']['fecha_cancelacion_operacion']) }}
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
                                    <span class="uppercase bold size-15px letter-spacing-1">Acuse de Cancelaci??n de
                                        Servicio Funerario:</span><br><br>
                                    <span class="bold uppercase">Registr?? acuse:</span> <span
                                        class="uppercase">{{ $datos['operacion']['cancelador']['nombre'] }}</span><br>
                                    <span class="bold uppercase">Cliente:</span>
                                    {{ $datos['operacion']['cliente']['nombre'] }}<br>
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
                la cancelaci??n del contrato con n??mero de servicio: <span
                    class="capitalize bold">{{$datos['id']}}</span>.
            </p>

            <p class="texto-base justificar line-base">
                Evento producido por el siguiente motivo: <span
                    class="capitalize bold">{{ $datos['operacion']['motivos_cancelacion_texto'] }}</span>. Para lo cual
                <span class="capitalize bold">{{$empresa->nombre_comercial}}</span> acord?? con el
                solicitante de
                cancelaci??n que se entregar?? como devoluci??n la cantidad de <span class="capitalize bold">$
                    {{ number_format($datos['operacion']['cantidad_a_regresar_cancelacion'],2)}}</span>
                ({{ numeros_a_letras($datos['operacion']['cantidad_a_regresar_cancelacion']) }}) y el resto en
                caso de
                existir se tomar?? como penalizaci??n por cancelaci??n de contrato seg??n las pol??ticas de la
                empresa.
            </p>

            <p class="texto-base justificar line-base">
                La cancelaci??n de dicho contrato se lleva a cabo en <span class="lowercase bold capitalize">Mazatl??n,
                    Sinaloa
                    M??xico a
                    <span
                        class="lowercase">{{ fecha_only($datos['operacion']['fecha_cancelacion_operacion']) }}</span></span>.
            </p>

            <p class="texto-base justificar line-base">
                <span class="uppercase bold">Nota / comentarios</span>.
            </p>

            <p class="texto-base justificar line-base">
                {{ $datos['operacion']['nota_cancelacion'] }}
            </p>
        </div>

        <div class="w-100 center mt-5">
            <div class="w-50 ml-auto mr-auto float-right">
                  <img src="{{ $firmas['cliente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top">
                    <div class="pt-3 pb-1"><span
                            class="uppercase  texto-sm">{{ $datos['operacion']['cliente']['nombre'] }}</span></div>
                    <span class="uppercase bold texto-sm">"el cliente"</span>
                </div>
            </div>
            <div class="w-50 ml-auto mr-auto float-left ">
                  <img src="{{ $firmas['gerente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top">
                    <div class="pt-3 pb-1"><span class="uppercase  texto-sm">{{ $empresa->razon_social }}</span></div>
                    <span class="uppercase bold texto-sm">"la empresa"</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>