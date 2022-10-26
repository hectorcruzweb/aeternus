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

        .datos_tabla {
            border-collapse: collapse;
        }

        .datos_tabla tr th,
        .datos_tabla td {
            border: 1px solid #ddd;
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
    <p class="fecha  right mt-7">
        <span class="bg-gray bold uppercase texto-sm  pl-2 pr-1">{{ $empresa->ciudad }}, {{ $empresa->estado }} a
            {{ fecha_only(now()) }}.</span>
    </p>
    <h1 class="center mt-10 size-22px">CONSTANCIA DE CREMACIÓN</h1>

    <div class="ml-auto mtp-5 mr-auto p-1 line-lg size-18px">
        A quien corresponda, <br>
        <div class="ml-7">
            Por medio de la presente y a petición del interesado, hacemos constar lo siguiente:
        </div>
        Que se otorgó un servicio de cremación a quien en vida llevara el nombre de :
    </div>

    <div class="center border-black-1 w-80 ml-auto mr-auto p-1 line-lg size-18px mt-4">
        DEP: {{ $datos['nombre_afectado'] }}
    </div>

    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">Datos del Servicio</div>
        <table class="w-100 texto-base datos_tabla uppercase">
            <tr class="size-15px center">
                <td class="w-30 py-1 px-1 bg-gray bold">
                    Fecha de Nacimiento:
                </td>
                <td class="w-70 py-1 px-1">
                    {{ fecha_abr($datos['fecha_nacimiento']) }}
                </td>
            </tr>
        </table>
        <table class=" mt-1 w-100 texto-base datos_tabla uppercase">
            <tr class="size-15px center">
                <td class="w-30 py-1 px-1 bg-gray bold">
                    Fecha de Defunción:
                </td>
                <td class="w-70 py-1 px-1">
                    {{ fecha_abr($datos['fechahora_defuncion']) }}
                </td>
            </tr>
        </table>
        <table class=" mt-1 w-100 texto-base datos_tabla uppercase">
            <tr class="size-15px center">
                <td class="w-30 py-1 px-1 bg-gray bold">
                    No. De Servicio:
                </td>
                <td class="w-70 py-1 px-1">
                    {{ $datos['id'] }}
                </td>
            </tr>
        </table>
        <table class=" mt-1 w-100 texto-base datos_tabla uppercase">
            <tr class="size-15px center">
                <td class="w-30 py-1 px-1 bg-gray bold">
                    Autorizado por:
                </td>
                <td class="w-70 py-1 px-1">
                    {{ $datos['nombre_contratante_temp'] }}
                </td>
            </tr>
        </table>
        <table class=" mt-1 w-100 texto-base datos_tabla uppercase">
            <tr class="size-15px center">
                <td class="w-30 py-1 px-1 bg-gray bold">
                    No. de Certificado:
                </td>
                <td class="w-70 py-1 px-1">
                    {{ $datos['folio_certificado'] }}
                </td>
            </tr>
        </table>
        <table class=" mt-1 w-100 texto-base datos_tabla uppercase">
            <tr class="size-15px center">
                <td class="w-30 py-1 px-1 bg-gray bold">
                    Descripción de la urna:
                </td>
                <td class="w-70 py-1 px-1">
                    {{ $datos['descripcion_urna'] }}
                </td>
            </tr>
        </table>
    </div>


    <div class="w-100">
        <div class="w-50 mr-auto ml-auto mt-15">
            <!--<img src="{{ $firmas['gerente'] }}" class="firma">-->
            <div class="w-90 mr-auto ml-auto  pt-1">
                <div class=" pb-1"><span class="texto-base bold uppercase center">
                        <p class=" line-17 size-13px">
                            ATENTAMENTE
                        </p>
                        <p class=" line-17 size-13px mt-3">
                            Administración
                        </p>

                    </span></div>
            </div>
        </div>
    </div>


</body>

</html>
<span class="uppercase bold texto-sm"></span>
