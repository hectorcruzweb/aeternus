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
            {{ fecha_only(now()) }}.</span>
    </p>
    <h1 class="center mt-5 size-22px">CONSTANCIA DE EMBALSAMIENTO</h1>

    <div class="contenido w-100 mt-5">
        <p class="justificar line-lg size-18px">
            Con base en los artículos: 62, 65, 71, 72, 100, 105, 106 y 107 del reglamento de la ley general de salud, en
            materia de
            control sanitario de la disposición de órganos, tejido y cadáveres de seres humanos, se hace constar que el
            cuerpo sin
            vida de quien llevara el nombre del <span class="bold uppercase ">
                @if (isset($datos['titulo']['titulo']))
                    {{ $datos['titulo']['titulo'] }}
                @else
                    N/A
                @endif. {{ $datos['nombre_afectado'] }}
            </span> a
            quien corresponde el
            certificado de defunción
            No.<span class="bold uppercase ">
                {{ $datos['folio_certificado'] }}
            </span>
            ha sido embalsamado en la sala de preparación de esta funeraria, misma que se localiza en
            {{ strtolower($empresa->calle) }} Núm. Ext {{ $empresa->num_ext }}
            Col. {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
            {{ $empresa->ciudad }}
            {{ $empresa->estado }}.
        </p>

        <p class="justificar line-lg size-18px">
            La técnica utilizada para el embalsamiento consistió en:
        </p>
        <div class="lista pl-11 -mt-1">
            <p class="justificar line-base size-18px">
                <span class=" bold size-20px -ml-6"></span>
                <span class="ml-1">
                    • Inyección de líquidos por la vía intra-arterial a base de formol diluido en agua, glicerina,
                    colorantes, etc.
                </span>
            </p>
            <p class="justificar line-base size-18px">
                <span class=" bold size-20px -ml-6"></span>
                <span class="ml-1">
                    • Se utilizó como base de drenado la vena yugular.
                </span>
            </p>
            <p class="justificar line-base size-18px">
                <span class=" bold size-20px -ml-6"></span>
                <span class="ml-1">
                    • Se realizó la extracción de líquidos y gases de las cavidades del tórax y abdomen por medio de un
                    troquer conectado a
                    un aparato de succión para posteriormente depositar las sustancias preservativas en las mismas
                    cavidades.
                </span>
            </p>
        </div>

        <p class="justificar line-lg size-18px">
            Como consecuencia de dicha técnica, el cuerpo sin vida del <span class="bold uppercase ">
                @if (isset($datos['titulo']['titulo']))
                    {{ $datos['titulo']['titulo'] }}
                @else
                    N/A
                @endif. {{ $datos['nombre_afectado'] }}
            </span> ha quedado en condiciones satisfactoriamente para
            su velación, inhumación y/o traslado.

        </p>


        <div class="w-100 center">
            <!--
            <div class="w-50 float-left">
                  <img src="{{ $firmas['medico'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class=" pb-1"><span class="texto-base bold">
                            @if ($datos['embalsamar_b'] == 1)
Dr.
@endif
                            {{ $datos['medico_responsable_embalsamado'] }}</span></div>
                </div>
                <span class="texto-base">Médico Responsable</span>
            </div>
        -->
            <div class="w-50 ml-auto mr-auto">
                <img src="{{ $firmas['embalsamador'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class="pb-1"><span class="texto-base bold">
                            @if ($datos['embalsamador_id'] > 2)
                                    {{ $datos['embalsamador']["nombre"] }}
                                    <div>
                                        {{ $datos['embalsamador']["cedula"] }}
                                    </div>
                            @else
                                {{ $datos['preparador'] }}
                                <div>
                                    {{ $datos['cedula_embalsamador'] }}
                                </div>
                            @endif
                        </span>
                    </div>
                    <span class="texto-base">Técnico Embalsamador</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>
