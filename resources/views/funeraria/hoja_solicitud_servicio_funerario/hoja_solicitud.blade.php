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

        /*
        estilos de la tabla de datos
        **/
        .datos_tabla {
            border-collapse: collapse;
        }

        .datos_tabla tr th,
        .datos_tabla td {
            border: 1px solid #ddd;
        }

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
    <h1 class="center mt-5">reporte de solicitud de servicios (realizado por {{ $datos['llamada_texto'] }})</h1>
    <table class="w-100 texto-base mt-5 datos_tabla uppercase">
        <tr class="size-15px">
            <td class="w-40 py-2 bold px-2">nombre del fallecido</td>
            <td class="w-60 px-2">{{ $datos['nombre_afectado'] }}</td>
        </tr>
        <tr class="size-15px">
            <td class="w-40 py-2 bold px-2">ubicación donde recoger el cuerpo</td>
            <td class="w-60 px-2">{{ $datos['ubicacion_recoger'] }}</td>
        </tr>
        <tr class="size-15px">
            <td class="w-40 py-2 bold px-2">nombre del informante</td>
            <td class="w-60 px-2">{{ $datos['nombre_informante'] }}</td>
        </tr>
        <tr class="size-15px">
            <td class="w-40 py-2 bold px-2">teléfono</td>
            <td class="w-60 px-2">{{ $datos['telefono_informante'] }}</td>
        </tr>
        <tr class="size-15px">
            <td class="w-40 py-2 bold px-2">parentesco con el fallecido</td>
            <td class="w-60 px-2">{{ $datos['parentesco_informante'] }}</td>
        </tr>

        <tr class="size-15px">
            <td class="w-40 py-2 bold px-2">responsable de recoger cuerpo</td>
            <td class="w-60 px-2">{{ $datos['recogio']['nombre'] }}</td>
        </tr>
        <tr class="size-15px">
            <td class="w-40 py-2 bold px-2">fecha y hora de la solicitud</td>
            <td class="w-60 px-2">{{ fechahora($datos['fechahora_solicitud']) }}</td>
        </tr>
        <tr class="size-15px">
            <td class="w-40 py-2 bold px-2">solicitud atendida por</td>
            <td class="w-60 px-2">{{ $datos['registro']['nombre'] }}</td>
        </tr>
        <tr class="size-15px">
            <td class="w-40 py-2 px-2
            <?php
            if(is_null($datos['nota_al_recoger'])){
                echo 'pb-60';
            }
            ?>
            " colspan="2">
                <div class="bold">
                    observaciones al recoger el cuerpo (pertenencias, documentos, etc.)
                </div>
                <div class="pt-2">
                    {{ $datos['nota_al_recoger'] }}
                </div>
            </td>
        </tr>
    </table>

    <div class="w-100 center mt-2">
        <div class="w-50 float-left mt-5">
             <img src="{{ $firmas['entrega_pertenencias'] }}" class="firma">
            <div class="w-90 mr-auto ml-auto border-top">
                <span class="uppercase bold texto-sm">FIRMA DE QUE SE ENTREGARON PERTENENCIAS AL MOMENTO DE
                    RECOGER EL CUERPO</span>
            </div>
        </div>
        <div class="w-50 float-right mt-5">
              <img src="{{ $firmas['no_portaba'] }}" class="firma">
            <div class="w-90 mr-auto ml-auto border-top">
                <span class="uppercase bold texto-sm">FIRMA DE QUE NO PORTABA PERTENENCIAS AL MOMENTO DE
                    RECOGER EL CUERPO</span>
            </div>
        </div>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>
