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
                                solicitud de servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_solicitud_texto'] }}
                            </p>

                            <div style=""></div>
                            <div class="control bg-gray">
                                Número de convenio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_convenio'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <br><br>
    <h1 class="left">Servicios sepultados en propiedad | {{ $datos['venta_terreno']['ubicacion_texto'] }}</h1><br>
    <h1 class="left">Titular | {{ $datos['nombre'] }}</h1>
    <br>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <table class="w-100 center">
            @if (count($datos['sepultados'])==0)
                PROPIEDAD SIN SERVICIOS ASIGNADOS
            @else
            @foreach ($datos['sepultados'] as $sepultado)
                <tr>
                    <td class="w-55 px-2 py-2">
                        <div class="left">
                            <div class="float-left w-15 left">
                                <span class="bold uppercase texto-sm">
                                    Finado(a):
                                </span>
                            </div>
                            <div class="float-right bg-gray w-85 center">
                                {{ $sepultado['nombre_afectado'] }}
                            </div>
                        </div>
                    </td>
                    <td class="w-45 px-2 py-2">
                        <div class="right">
                            <div class="float-left w-45 left">
                                <span class="bold uppercase texto-sm">
                                    fecha de defunción:
                                </span>
                            </div>
                            <div class="float-right bg-gray w-55 center">
                                {{ $sepultado['fecha_defuncion_texto'] }}
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            @endif
        </table>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>