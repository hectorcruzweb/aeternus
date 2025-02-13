<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
            line-height: 1em !important;
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

        .tabla_dato {
            border-collapse: collapse;
        }

        .tabla_dato .border-bottom {
            border-bottom: 1px solid #ddd;
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
                                Número Servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['servicio_id'] }}
                            </p>

                            <div style=""></div>
                            <div class="control bg-gray">
                                Tipo Servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['tipo_solicitud_texto'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <p class="fecha  right">
        <span class="bg-gray bold uppercase texto-sm  pl-2 pr-1">{{ $empresa->ciudad }}, {{ $empresa->estado }} a
            {{ fecha_only($datos['fechahora_solicitud']) }}.</span>
    </p>
    <h1 class="center mt-5 size-22px">formato de autorización</h1>

    <div class="contenido w-100 mt-5">
        <table class="w-100 tabla_dato line-lg size-19px">
            <tr>
                <td class="w-1">Yo</td>
                <td class="w-85 border-bottom center ml-auto mr-auto uppercase">
                    @if (isset($datos['operacion']['cliente']))
                        @if (!is_null($datos['operacion']['cliente']))
                            {{ $datos['operacion']['cliente']['nombre'] }}
                        @endif
                    @else
                        <!--aqui va el nombre del contratante provisional-->
                        {{ $datos['nombre_contratante_temp'] }}
                    @endif
                </td>
                <td class="w-5">, por</td>
            </tr>
        </table>
        <p class="justificar line-lg size-19px">
            medio de la presente, otorgo poder amplio y suficiente para que a mi nombre y representación,
            <span class="bold">
                Aeternus
                Funerales
            </span>
            , a
            través de las personas que tengan a bien designar, efectúen todos los trámites necesarios ante las
            autoridades
            correspondientes con el fin de lograr la: <br> <span class="bold">
                ( @if ($datos['embalsamar_b'] != 0)
                    __X__
                @else
                    ____
                @endif )
                preparación</span>,
            <span class="bold">(
                @if ($datos['inhumacion_b'] != 0)
                    __X__
                @else
                    ____
                @endif )
                inhumación
                <!--condiciono resto de texto-->
                @if ($datos['inhumacion_b'] != 0)
                    @if ($datos['estado_cuerpo']['id'] == 4 /**cenizas*/ || $datos['cremacion_b'] == 1)
                        de cenizas
                    @elseif($datos['estado_cuerpo']['id'] == 5 /**restos aridos*/)
                        de restos áridos
                    @endif
                    <!--FIN condiciono resto de texto-->
                @endif
            </span>,
            <span class="bold">( @if ($datos['cremacion_b'] != 0)
                    __X__
                @else
                    ____
                @endif ) cremación
                @if ($datos['cremacion_b'] != 0)
                    <!--condiciono resto de texto-->
                    @if ($datos['estado_cuerpo']['id'] == 5 /**restos aridos*/)
                        de restos áridos
                    @endif
                    <!--FIN condiciono resto de texto-->
                @endif
                , y/o
                ( @if ($datos['traslado_b'] != 0)
                    __X__
                @else
                    ____
                @endif) traslado
                @if ($datos['traslado_b'] != 0)
                    <!--condiciono resto de texto-->
                    @if ($datos['estado_cuerpo']['id'] == 4 /**cenizas*/ || $datos['cremacion_b'] == 1)
                        de cenizas
                    @elseif($datos['estado_cuerpo']['id'] == 5 /**restos aridos*/)
                        de restos áridos
                    @else
                        del cuerpo
                    @endif
                    <!--FIN condiciono resto de texto-->
                @endif
            </span>
            del (la)
        </p>
        <table class="w-100 tabla_dato size-19px">
            <tr>
                <td class="w-2">
                    C.
                </td>
                <td class="w-98 border-bottom center ml-auto mr-auto uppercase">{{ $datos['nombre_afectado'] }}
                </td>
            </tr>
        </table>

        <table class="w-100 tabla_dato size-19px mt-8">
            <tr>
                <td class="w-35">Otorgo este poder en calidad de</td>
                <td class="w-40 border-bottom center ml-auto mr-auto uppercase">

                    @if (isset($datos['parentesco_contratante']))
                        {{ $datos['parentesco_contratante'] }}
                    @else
                        <!--aqui va el nombre del contratante provisional-->
                        {{ $datos['parentesco_contratante_temp'] }}
                    @endif

                </td>
                <td class="25 right">
                    de la persona fallecida,
                </td>
            </tr>
        </table>
        <table class="w-100 tabla_dato size-19px mt-3">
            <tr>
                <td class="w-40">manifestando tener mi domicilio en:</td>
            </tr>
        </table>

        <table class="w-100 tabla_dato size-19px mt-4">
            <tr>
                <td class="w-100 border-bottom center ml-auto mr-auto uppercase">
                    @if (isset($datos['operacion']['cliente']))
                        @if (!is_null($datos['operacion']['cliente']))
                            {{ $datos['operacion']['cliente']['direccion'] }}
                        @endif
                    @else
                        <!--aqui va la direccion del contratante provisional-->
                        {{ $datos['direccion_contratante_temp'] }}
                    @endif
                </td>
            </tr>
        </table>

        <table class="w-100 tabla_dato size-19px mt-4">
            <tr>
                <td class="w-20">Siendo mi número de teléfono:</td>
                <td class="w-40 border-bottom center ml-auto mr-auto uppercase">
                    @if (isset($datos['operacion']['cliente']))
                        @if (!is_null($datos['operacion']['cliente']))
                            {{ $datos['operacion']['cliente']['celular'] }}
                        @endif
                    @else
                        <!--aqui va el tel del contratante provisional-->
                        {{ $datos['telefono_contratante_temp'] }}
                    @endif
                </td>
            </tr>
        </table>

        <div class="w-100 center">
            <div class="w-50 float-left">
                <img src="{{ $firmas['otorgante'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class=" pb-1"><span class="texto-base bold">Otorgante</span></div>
                </div>
                <span class="texto-base">(Nombre y Firma)</span>
            </div>
            <div class="w-50 float-right">
                <img src="{{ $firmas['aceptante'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class="pb-1"><span class="texto-base bold">Aceptante</span>
                    </div>
                    <span class="texto-base">(Nombre y Firma)</span>
                </div>
            </div>
        </div>

        <div class="w-100 center">
            <div class="w-50 float-left">
                <img src="{{ $firmas['testigo1'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class=" pb-1"><span class="texto-base bold">Testigo</span></div>
                </div>
                <span class="texto-base">(Nombre y Firma)</span>
            </div>
            <div class="w-50 float-right">
                <img src="{{ $firmas['testigo2'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class="pb-1"><span class="texto-base bold">Testigo</span>
                    </div>
                    <span class="texto-base">(Nombre y Firma)</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>
