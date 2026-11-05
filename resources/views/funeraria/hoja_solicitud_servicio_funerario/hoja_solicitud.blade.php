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

        /* Mantener ambas firmas juntas cuando quepan en una página; limitar
           la imagen evita una hoja adicional dedicada solo a las firmas. */
        .firmas-solicitud { display: table; page-break-inside: avoid; break-inside: avoid; }
        .firmas-solicitud .firma { max-width: 150px; max-height: 48px; width: auto; height: auto; }
        /* Tamaño compacto sin suprimir información; las observaciones extensas
           pueden continuar y las dos firmas se mantienen juntas. */
        .solicitud-servicio-documento .datos_tabla .size-15px { font-size: 14px !important; line-height: 1.5; }
        .solicitud-servicio-documento .datos_tabla td { padding-top: 6px !important; padding-bottom: 6px !important; }
        .solicitud-servicio-documento .datos_tabla .pb-60 { padding-bottom: 24px !important; }
        .solicitud-servicio-documento .datos_tabla { margin-top: 14px !important; }
        .firmas-solicitud > div { margin-top: 14px !important; }
        /* Altura útil para observaciones, incluso cuando no hay texto.
           Puede crecer con su contenido; no limita ni recorta lo capturado. */
        .solicitud-observaciones { min-height: 3cm; box-sizing: border-box; }

    </style>
{{-- Altura carta con márgenes de 12.4mm; no forzar otra hoja vacía. --}}
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'servicios_funerarios', 'altoMarcaAgua' => 1170])
</head>

<body class="solicitud-servicio-documento">
    <div class="marca-agua-contenido">
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
                <div class="solicitud-observaciones">
                <div class="bold">
                    observaciones al recoger el cuerpo (pertenencias, documentos, etc.)
                </div>
                <div class="pt-2">
                    {{ $datos['nota_al_recoger'] }}
                </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="w-100 center mt-2 firmas-solicitud">
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
    </div>
</body>

</html>
