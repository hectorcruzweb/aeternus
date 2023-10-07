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
    <p class="fecha  right">
        {{ $empresa->ciudad }}, {{ $empresa->estado }} a <span
            class="bg-gray bold uppercase texto-sm  pl-2 pr-1">{{ fecha_only($datos['fecha_operacion']) }}</span>.
    </p>
    <div class="contenido parrafo1">
        <p class="texto-base justificar line-base">
            Convenio para el otorgamiento del derecho de uso mortuorio a perpetuidad con reserva de dominio,
            que celebran por una parte <span class="bold uppercase"><span
                    class="texto-sm">{{ $empresa->razon_social }}</span></span>,
            con domicilio en
            <span class="uppercase texto-sm bold">{{ $empresa->calle }}, {{ $empresa->num_ext }}, Col.
                {{ $empresa->colonia }}
                C.P {{ $empresa->cp }}</span>, de esta ciudad; a quien en lo sucesivo se le denominara la <span
                class="bold uppercase texto-sm">"La Empresa"</span>,
            y por la otra parte, por su propio derecho, El (La) C.
            <span class="uppercase texto-sm bold bg-gray px-1">{{ $datos['nombre'] }}</span>,
            quien en lo sucesivo se denominara <span class="uppercase texto-sm bold">"El cliente"</span> y será el
            Titular del presente convenio,
            el cual ambas partes se comprometen a firmar, de conformidad con las siguiente declaraciones y
            cláusulas:
        </p>
    </div>



    <div class="contenido parrafo2">
        <h1 class="texto-base bold underline">
            declaraciones
        </h1>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold">I. </span> Declara el representante legal de “La empresa”, que su representada
            está legalmente constituida conforme a las leyes mexicanas,
            según consta en escritura pública número <span
                class="bold texto-sm">{{ $empresa->registro_publico['t_nep'] }}</span> (<span
                class="uppercase bold texto-sm">{{ NumerosEnLetras::convertir($empresa->registro_publico['t_nep']) }}</span>)
            del volumen
            <span class="uppercase bold texto-sm">xxxix</span> (<span class="uppercase bold texto-sm">trigésimo
                noveno</span>), pasada en la ciudad de <span
                class="uppercase bold texto-sm">{{ $empresa->registro_publico['ciudad_np'] }}</span>,
            <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['estado_np'] }}</span>,
            ante el protocolo a cargo del notario público número
            {{ $empresa->registro_publico['num_np'] }} (<span
                class="uppercase bold texto-sm">{{ NumerosEnLetras::convertir($empresa->registro_publico['num_np']) }}</span>),
            licenciado <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['fe_lic'] }}</span>.
        </p>

        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold">II. </span>
                Sigue declarando “La empresa” que los servicios de inhumación amparados por este convenio,
                se realizaran en el cementerio denominado <span
                    class="uppercase bold texto-sm">{{ $empresa->cementerio['cementerio'] }}</span>,
                ubicado en <span class="uppercase bold texto-sm">{{ $empresa->cementerio->calle }},
                    #.
                    {{ $empresa->cementerio->num_ext != 0 ? $empresa->cementerio->num_ext : 'N/A' }}
                    , Col.
                    {{ $empresa->cementerio->colonia }} C.P {{ $empresa->cementerio->cp }}</span>,
                en el municipio de
                <span class="uppercase bold texto-sm">{{ $empresa->cementerio->ciudad }},
                    {{ $empresa->cementerio->estado }}</span>.
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold">II. </span>
                Sigue declarando “La empresa” que los servicios de inhumación de cenizas amparados por este convenio,
                se realizaran en el cementerio denominado <span
                    class="uppercase bold texto-sm">{{ $empresa->cementerio['cementerio'] }}</span>,
                ubicado en <span class="uppercase bold texto-sm">{{ $empresa->cementerio->calle }},
                    #.
                    {{ $empresa->cementerio->num_ext != 0 ? $empresa->cementerio->num_ext : 'N/A' }}
                    , Col.
                    {{ $empresa->cementerio->colonia }} C.P {{ $empresa->cementerio->cp }}</span>,
                en el municipio de
                <span class="uppercase bold texto-sm">{{ $empresa->cementerio->ciudad }},
                    {{ $empresa->cementerio->estado }}</span>.
            </p>
        @endif


        <p class="texto-base justificar line-base">
            <span class="uppercase bold">III. </span>
            Declara “El Cliente” tener el interés y capacidad legal para celebrar este convenio, y declara tener
            @if (trim($datos['fecha_nac']) != '')
                <span class="bg-gray px-1 mr-1">
                    (<span class="uppercase bold texto-sm">{{ calculaedad((string) $datos['fecha_nac']) }}</span>)
                    años de
                    edad
                </span>
            @else
                (<span class="uppercase bold texto-sm">N/A</span>)
                años de
                edad
            @endif
            y su domicilio en: <span class="uppercase bold texto-sm">
                {{ $datos['direccion'] }}</span>, Tel. <span class="uppercase bold texto-sm">
                {{ $datos['telefono'] != '' ? $datos['telefono'] : '"No registrado"' }}</span>,
            Cel. <span class="uppercase bold texto-sm">{{ $datos['celular'] }}</span> y correo
            electrónico <span
                class="lowercase bold">{{ $datos['email'] != '' ? $datos['email'] : '"No registrado"' }}</span>
            para efecto de notificaciones y demás efectos legales de este convenio.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold">IV. </span>
            Declara “El Cliente” tener el interés y capacidad legal para ceder derechos para actuar en su nombre al
            titular sustituto de este convenio al C.
            <span class="bg-gray px-1 mr-1">
                <span class="uppercase bold texto-sm">{{ $datos['titular_sustituto'] }}</span>
            </span>
            en su carácter de: <span class="uppercase bold texto-sm">
                {{ $datos['parentesco_titular_sustituto'] }}</span>, quién se puede contactar al Tel. <span
                class="uppercase bold texto-sm">
                {{ $datos['telefono_titular_sustituto'] != '' ? $datos['telefono_titular_sustituto'] : '"No registrado"' }}</span>.

        </p>
    </div>


    <div class="contenido parrafo3">
        <p class="texto-base justificar line-base">
            Hechas las aclaraciones anteriores. “La Empresa” y “El Cliente” proceden a la celebración del presente
            convenio, al tenor de las siguientes:
        </p>
    </div>

    <div class="contenido parrafo4">
        <h1 class="texto-base bold underline">
            cláusulas
        </h1>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">primera.- </span>
            “El Cliente" adquiere de "La Empresa", el derecho de uso mortuorio a perpetuidad con reserva de
            dominio de <span class="uppercase bold texto-sm">1</span> Espacio <span class="uppercase bold texto-sm">
            </span>, ubicado en la
            <span class="uppercase bold texto-sm bg-gray px-2">
                {{ $datos['venta_terreno']['ubicacion_texto'] }}
            </span>,
            en el <span class="uppercase bold texto-sm">{{ $empresa->cementerio['cementerio'] }}</span>,
            con una capacidad de
            @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
                <span
                    class="uppercase bold texto-sm">{{ $datos['venta_terreno']['tipo_propiedad']['capacidad'] }}</span>
                gaveta(s)
            @else
                <span class="uppercase bold texto-sm">4 </span>
                urnas medida estándar
            @endif
            .
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Segunda.- </span>
            “La Empresa” se compromete a:
        </p>
        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Proporcionar un título de aportación que otorga el derecho de uso mortuorio a perpetuidad al titular
                    de este convenio,
                    o en caso del fallecimiento de este, a cualquiera de los beneficiarios del mismo,
                    dentro de los treinta días siguientes a aquel en que se haya cubierto en forma total el pago de las
                    aportaciones mencionadas en la cláusula tercera de este convenio.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    Garantizar que las gavetas mencionadas en la cláusula primera de este convenio fueron construidas
                    con
                    los materiales aprobados por las autoridades competentes, y cuenten con los cierres y sellamientos
                    necesarios.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    Arreglar el lugar del sepelio, proporcionando el equipo necesario y adecuado para el mismo.
                </span>
            </p>
            @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
                <p class="texto-base justificar line-base">
                    <span class="lowercase bold texto-sm -ml-6">d) </span>
                    <span class="ml-2">
                        Proporcionar e instalar en el espacio mortuorio amparado por este convenio una lápida de mármol,
                        en el que se grabara su nombre, el año de nacimiento y el año de fallecimiento de
                        cada una de las personas a inhumarse en el lote mencionado en la primera
                        cláusula de dicho convenio. (<span class="texto-xs bold italic">solo aplica a espacios Dúplex y
                            Cuádruplex</span>).
                    </span>
                </p>
                <p class="texto-base justificar line-base">
                    <span class="lowercase bold texto-sm -ml-6">e) </span>
                    <span class="ml-2">
                        Conservar y mantener el parque funerario.
                    </span>
                </p>
            @else
                <p class="texto-base justificar line-base">
                    <span class="lowercase bold texto-sm -ml-6">d) </span>
                    <span class="ml-2">
                        Conservar y mantener el parque funerario.
                    </span>
                </p>
            @endif

        </div>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Tercera.- </span>
            En contraparte, “El Cliente”, se compromete a pagar por concepto de aportaciones la cantidad de $ <span
                class="bold texto-sm bg-gray px-2 uppercase">
                {{ number_format($datos['total'], 2) }} (
                {{ NumerosEnLetras::convertir($datos['total'], 'Pesos m.n', false) }}
                )
            </span>.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Cuarta.- </span>
            “El Cliente” se obliga a cubrir sus aportaciones sin necesidad de cobro acudiendo para tal efecto a las
            oficinas de “La Empresa”
            localizadas en <span class="uppercase texto-sm bold">{{ $empresa->calle }}, {{ $empresa->num_ext }},
                Col.
                {{ $empresa->colonia }} C.P {{ $empresa->cp }}. {{ $empresa->ciudad }}, {{ $empresa->estado }}
            </span>; o a las
            de los bancos que para estos determine la misma, dentro de los primeros diez días de cada mes.
            Mediante la siguiente forma:
        </p>
        <div class="lista pl-11 -mt-1">
            @if ($datos['num_pagos_programados_vigentes'] > 0)
                @if ($datos['num_pagos_programados_vigentes'] == 1)
                    <p class="texto-base justificar line-base">
                        <span class="lowercase bold texto-sm -ml-6">a) </span>
                        <span class="ml-2">
                            Una aportación (Pago Único) de $ <span
                                class="bg-gray bold px-2 uppercase texto-sm">{{ number_format($datos['pagos_programados'][0]['monto_programado'], 2) }}
                                ({{ NumerosEnLetras::convertir($datos['pagos_programados'][0]['monto_programado'], 'Pesos m.n', false) }})</span>.
                        </span>
                    </p>
                @else
                    <p class="texto-base justificar line-base">
                        <span class="lowercase bold texto-sm -ml-6">a) </span>
                        <span class="ml-2">
                            Una aportación inicial de $ <span
                                class="bg-gray bold px-2 uppercase texto-sm">{{ number_format($datos['pagos_programados'][0]['monto_programado'], 2) }}
                                ({{ NumerosEnLetras::convertir($datos['pagos_programados'][0]['monto_programado'], 'Pesos m.n', false) }})</span>.
                        </span>
                    </p>

                    <p class="texto-base justificar line-base">
                        <span class="lowercase bold texto-sm -ml-6">b) </span>
                        <span class="ml-2">
                            Y un saldo de $ <span class="bg-gray bold px-2 uppercase texto-sm">
                                {{ number_format($datos['total'] - $datos['pagos_programados'][0]['monto_programado'], 2) }}
                                (
                                {{ NumerosEnLetras::convertir($datos['total'] - $datos['pagos_programados'][0]['monto_programado'], 'Pesos m.n', false) }})
                            </span>. En <span
                                class="bg-gray bold px-2 uppercase texto-sm">{{ $datos['financiamiento'] }}</span>
                            abonos consecutivos.
                        </span>
                    </p>
                @endif
            @endif
        </div>
        <p class="texto-base justificar line-base">
            El contratante se obliga a pagar a la agencia funeraria las parcialidades contratadas dentro
            de los primeros <span class="bold">{{ $datos['ajustes_politicas']['dias_antes_vencimiento'] }}</span>
            días
            hábiles naturales a la fecha de vencimiento mensual que le
            corresponda.
        </p>

        <p class="texto-base justificar line-base">
            Sólo podrán reconocerse los pagos de mensualidades por los recibos firmados y sellados
            por la empresa, cuando se efectúen en cajas de la Agencia Funeraria, o los recibos
            firmados por el Banco Santander (México), S.A. a la cuenta <span
                class="bold texto-xs">{{ $empresa['cuenta'] }}</span> a más tardar en
            la fecha límite establecida. Ninguna otra persona está autorizada para recibir pagos y, por
            lo tanto, estos no podrán ser reconocidos por la Agencia Funeraria.
        </p>

        <p class="texto-base justificar line-base">
            El contratante o Titular Sustituto será quien al requerir los servicios para el usuario que se
            solicitaron, deberá entregar el contrato, recibos de pagos efectuados o en su caso liquidar el
            saldo total que persista hasta la fecha y cualquier otro adeudo del servicio contratado.
        </p>

        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Quinta.- </span>
                Cuando “El Cliente” requiera un servicio de inhumación, deberá CUBRIR en las oficinas
                de “La Empresa” un <span class="uppercase bold texto-sm">cargo</span> por los <span
                    class="uppercase bold texto-sm">derechos de apertura</span>, así como por las <span
                    class="uppercase bold texto-sm">lozas</span> necesarias para el servicio.
                El monto de dicho cargo será por el que conste en las listas de precios de “La Empresa” en el momento de
                solicitar el servicio.
            </p>
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">sexta.- </span>
                A fin de que “La Empresa” esté en posibilidad de arreglar el lugar donde se realizará el servicio de
                inhumación motivo de este convenio, así como de proporcionar el equipo adecuado y necesario para el
                mismo,
                “El Cliente” se compromete a solicitar dicho servicio con un mínimo de <span
                    class="uppercase bold texto-sm">cinco</span> horas de anticipación.
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Quinta.- </span>
                Cuando “El Cliente” requiera un servicio de inhumación de cenizas, deberá acudir a las oficinas
                de “La Empresa” para solicitar una cita para el depósito de cenizas.
            </p>
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">sexta.- </span>
                A fin de que “La Empresa” esté en posibilidad de arreglar el lugar donde se realizará el servicio de
                inhumación de cenizas motivo de este convenio, así como de proporcionar el equipo adecuado y necesario
                para
                el mismo,
                “El Cliente” se compromete a solicitar dicho servicio con un mínimo de <span
                    class="uppercase bold texto-sm">cinco</span> horas de anticipación.
            </p>
        @endif

        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">séptima.- </span>
                “La Empresa” ofrecerá el servicio de inhumación amparado por este convenio de <span
                    class="uppercase bold texto-sm">lunes a domingo de, {{ $empresa->cementerio->hora_apertura }} a
                    {{ $empresa->cementerio->hora_cierre }} horas</span>.
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">séptima.- </span>
                “La Empresa” ofrecerá el servicio de inhumación de cenizas amparado por este convenio de <span
                    class="uppercase bold texto-sm">lunes a domingo de, {{ $empresa->cementerio->hora_apertura }} a
                    {{ $empresa->cementerio->hora_cierre }} horas</span>.
            </p>
        @endif

        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Octava.- </span>
                “La Empresa” se reserva el dominio del derecho de inhumación a perpetuidad hasta en tanto no hayan sido
                cubierta en forma total el pago de las aportaciones especificadas en la cláusula tercera.
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Octava.- </span>
                “La Empresa” se reserva el dominio del derecho de inhumación de cenizas a perpetuidad hasta en tanto no
                hayan sido
                cubierta en forma total el pago de las aportaciones especificadas en la cláusula tercera.
            </p>
        @endif


        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Novena.- </span>
            “El Cliente” se compromete a pagar a “La Empresa” a más tardar el <span class="uppercase bold texto-sm ">
                día {{ $empresa->cementerio->dia_maximo_pago }} de
                {{ mes($empresa->cementerio->mes_maximo_pago) }}</span> de cada año, una cuota por
            concepto de mantenimiento del parque funerario. El monto de dicho cargo será el equivalente a <span
                class="uppercase bold texto-sm">
                {{ $datos['venta_terreno']['salarios_minimos'] }}
                ({{ NumerosEnLetras::convertir($datos['venta_terreno']['salarios_minimos'], '', false) }})
            </span>
            salarios mínimos del distrito Federal (CDMX), vigentes al día <span class="uppercase bold texto-sm">15
                (QUINCE)</span> del mes de enero de cada año. La empresa no se hará responsable del mantenimiento del
            lote del espacio mencionado en la cláusula primera, si el
            cliente no se encuentra al corriente con el pago de la cuota por concepto de mantenimiento del parque
            funerario
        </p>

        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Décima primera.- </span>
                Ambas partes acuerdan expresamente que el destino del lote mencionado en la cláusula primera será
                únicamente
                para la inhumación de los restos humanos que “El Cliente” señale por escrito a “La Empresa”, y que una
                vez
                ocupadas las gavetas respectivas, los restos solamente podrán ser exhumados cuando se hayan cumplido las
                disposiciones establecidas por las autoridades competentes.
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Décima primera.- </span>
                Ambas partes acuerdan expresamente que el destino del lote mencionado en la cláusula primera será
                únicamente
                para la inhumación de cenizas que “El Cliente” señale por escrito a “La Empresa”, y que una vez
                ocupadas las gavetas respectivas, los restos solamente podrán ser exhumados cuando se hayan cumplido las
                disposiciones establecidas por las autoridades competentes.
            </p>
        @endif





        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Décima segunda.- </span>
                A fin de recibir el servicio de inhumación amparado por este convenio, “El Cliente” entregara en las
                instalaciones de “La Empresa”, ubicadas en <span
                    class="uppercase texto-sm bold">{{ $empresa->calle }},
                    {{ $empresa->num_ext }},
                    Col.
                    {{ $empresa->colonia }} C.P {{ $empresa->cp }}. {{ $empresa->ciudad }}, {{ $empresa->estado }}
                </span>, de esta ciudad:
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Décima segunda.- </span>
                A fin de recibir el servicio de inhumación de cenizas amparado por este convenio, “El Cliente” entregara
                en
                las
                instalaciones de “La Empresa”, ubicadas en <span
                    class="uppercase texto-sm bold">{{ $empresa->calle }},
                    {{ $empresa->num_ext }},
                    Col.
                    {{ $empresa->colonia }} C.P {{ $empresa->cp }}. {{ $empresa->ciudad }}, {{ $empresa->estado }}
                </span>, de esta ciudad:
            </p>
        @endif



        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>

                @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
                    <span class="ml-2">
                        El convenio que ampara dicho servicio de inhumación
                    </span>
                @else
                    <span class="ml-2">
                        El convenio que ampara dicho servicio de inhumación de cenizas
                    </span>
                @endif

            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    El título de propiedad
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    El certificado de defunción de la persona que recibirá el servicio amparado por el convenio en
                    cuestión
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">d) </span>
                <span class="ml-2">
                    Cualquier otra documentación que sea necesaria para efectuar los trámites correspondientes y/o
                    conseguir los permisos necesarios para la realización de dicho servicio.
                </span>
            </p>
        </div>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Décima tercera.- </span>
            “El Cliente” declara haber leído y conocer perfectamente el reglamento interior de el <span
                class="uppercase bold texto-sm">"{{ $empresa->cementerio->cementerio }}"</span>, el cual
            acepta y se obliga a respetar y a hacer respetar, así como se compromete a cumplir todas las disposiciones
            legales que en el se establezcan o que en el futuro se adopten por parte de las autoridades competentes.
            Copia de dicho reglamento se les entrega a “El Cliente” en este acto.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Décima cuarta.- </span>
            En caso de cambio de domicilio por parte de “El Cliente” se conviene que, en este, en un plazo máximo de
            <span class="uppercase bold texto-sm">quince días</span>, después de efectuado el cambio, deberá
            comunicarlo
            por escrito a “La Empresa” con acuse de
            recibo por parte de este.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Décima quinta.- </span>
            Los servicios amparados por este convenio únicamente podrán ser ofrecidos a “El Cliente” o a uno de los
            beneficiarios establecidos en la cláusula Décima Sexta.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Décima sexta.- </span>
            “El Cliente” designa como beneficiario(s) a las siguiente(s) persona(s):
        </p>
        @if (count($datos['beneficiarios']) > 0)
            <table class="w-100 center">
                <thead>
                    <tr>
                        <th><span class="uppercase bold texto-sm bg-gray px-3">#</span></th>
                        <th><span class="uppercase bold texto-sm bg-gray px-3">Nombre</span></th>
                        <th><span class="uppercase bold texto-sm bg-gray px-3">Parentesco</span></th>
                        <th><span class="uppercase bold texto-sm bg-gray px-3">teléfono</span></th>
                    </tr>
                </thead>
                @php
                    $num = 1;
                @endphp
                @foreach ($datos['beneficiarios'] as $beneficiario)
                    <tr>
                        <td class="pt-1 pb-1"><span
                                class="uppercase bold texto-sm letter-spacing-3 bg-gray px-2">{{ $num }}</span>
                        </td>
                        <td class="pt-1 pb-1"><span
                                class="uppercase bold texto-sm letter-spacing-3">{{ $beneficiario['nombre'] }}</span>
                        </td>
                        <td class="pt-1 pb-1"><span
                                class="uppercase bold texto-sm letter-spacing-3">{{ $beneficiario['parentesco'] }}</span>
                        </td>
                        <td class="pt-1 pb-1"><span
                                class="uppercase bold texto-sm letter-spacing-3">{{ $beneficiario['telefono'] }}</span>
                        </td>
                    </tr>
                    @php
                        $num++;
                    @endphp
                @endforeach
            </table>
        @else
            <p class="texto-base justificar line-base center uppercase bg-gray bold">
                no se han capturado beneficiarios hasta la fecha.
            </p>
        @endif

        <p class="texto-base justificar line-base">
            En caso de que “El Cliente” quisiera cambiar a los beneficiarios designados inicialmente, lo podrá hacer
            mediante un escrito a la “Empresa” con acuse de recibo.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Décima séptima.- </span>
            “El Cliente” tendrá la facultad de ceder los derechos de uso de este convenio, mediante su renuncia a la
            membresía de “La Empresa”, lo cual podrá hacer mediante la entrega de un escrito a “La Empresa” con acuse de
            recibo, siempre y cuando este al corriente de sus pagos. En dicho escrito deberá especificar el nombre de la
            persona que recibirá los derechos del convenio.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Décima octava.- </span>
            Para la cesión de los derechos de uso de este convenio, “El Cliente” deberá pagar en las instalaciones de
            “La Empresa” un cargo por concepto de la realización de dicho trámite. El importe del mismo será el que
            conste en lista de precios de “La Empresa” al momento de realizar la cesión antes mencionada. El adquiriente
            de
            los derechos de uso de un convenio transferido, estará obligado a cubrir las aportaciones pendientes de
            pagar en los planes establecidos en el convenio transferido.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Décima novena.- </span>
            En caso de que al realizar la cesión de derechos de este convenio, fuera necesario cubrir algún tipo de
            cuota,
            derecho, impuesto, etc. A entidades ajenas a “La Empresa”, tales como la secretaria de salud, Gobierno
            Municipal, etc., “El Cliente” será responsable de pago de las mismas.
        </p>

        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Vigésima.- </span>
                “El Cliente” podrá designar un titular sustituto, el cual debe estar plenamente facultado para decidir
                sobre
                la utilización de los servicios funerarios motivo de este convenio, cuando el cliente este
                imposibilitado
                para hacerlo. Asimismo, podrá modificar esta designación en cualquier momento, obligándose en ambos
                casos a
                hacer a notificación por escrito con acuse de recibo, tanto “La Empresa” como el titular sustituto, en
                un
                plazo de <span class="uppercase bold texto-sm">quince días</span> naturales después de efectuada dicha
                designación o modificación, anexando el escrito de
                aceptación por parte del titular sustituto.
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Vigésima.- </span>
                “El Cliente” podrá designar un titular sustituto, el cual debe estar plenamente facultado para decidir
                sobre
                la utilización del servicio de inhumación de cenizas motivo de este convenio, cuando el cliente este
                imposibilitado
                para hacerlo. Asimismo, podrá modificar esta designación en cualquier momento, obligándose en ambos
                casos a
                hacer a notificación por escrito con acuse de recibo, tanto “La Empresa” como el titular sustituto, en
                un
                plazo de <span class="uppercase bold texto-sm">quince días</span> naturales después de efectuada dicha
                designación o modificación, anexando el escrito de
                aceptación por parte del titular sustituto.
            </p>
        @endif






        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Vigésima primera.- </span>
                El importe de los gastos, derecho y/o erogaciones efectuados por “La Empresa” por cuenta de “El
                Cliente”,
                por
                los conceptos adicionales a los ofrecidos por este convenio, deberán ser cubiertos en las oficinas de
                “La
                Empresa” a más tardar dos horas antes de que se realice el servicio de inhumación.
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Vigésima primera.- </span>
                El importe de los gastos, derecho y/o erogaciones efectuados por “La Empresa” por cuenta de “El
                Cliente”,
                por
                los conceptos adicionales a los ofrecidos por este convenio, deberán ser cubiertos en las oficinas de
                “La
                Empresa” a más tardar dos horas antes de que se realice el servicio de inhumación de cenizas.
            </p>
        @endif



        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vigésima segunda.- </span>
            En el supuesto incumplimiento en el pago de las aportaciones mencionadas en la cláusula tercera de este
            convenio; y/o en el pago de los gastos, derechos y/o erogaciones mencionadas en la cláusula vigésima
            primera, “El Cliente” se obligará a pagar a “La Empresa”, los gastos de administración, cobranza y
            comisiones pagadas comprobables y, en su caso, los gastos y costos judiciales.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vigésima tercera.- </span>
            En caso de retrasarse en el pago mensual de las aportaciones, “El Cliente” deberá cubrir una pena
            convencional sobre el total del monto de la mensualidad vencida,
            importe que se considerará como aportación
            adicional complementaria al cliente. El contratante se obliga a pagar a la agencia funeraria interés
            moratorio del <span class="bold">{{ $datos['ajustes_politicas']['tasa_fija_anual'] }}</span>%
            ({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['tasa_fija_anual'], '', false) }} por ciento)
            fija
            anual, la que se calculará y liquidará sobre
            cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
            calcularán multiplicando el monto de lo que adeude el contratante por la tasa de interés
            anual, dividida entre 365, este resultado se multiplica por el número de días transcurridos
            entre la fecha de pago que debió ser hecho y la fecha que el contratante liquide el adeudo.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vigésima cuarta.- </span>
            Queda establecido que “La Empresa” podrá cancelar el convenio, si este incurriera en cualquiera de los
            supuestos siguientes:
        </p>

        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    El incumplimiento del pago de <span
                        class="uppercase bold texto-sm">{{ $datos['ajustes_politicas']['maximo_pagos_vencidos'] }}</span>
                    de las aportaciones en
                    forma consecutiva
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    En caso de que el retraso supere los <span
                        class="bold">{{ $datos['ajustes_politicas']['maximo_dias_retraso'] }}</span> días, la
                    agencia
                    funeraria podrá elegir entre exigir
                    el pago de todas las mensualidades aun no pagadas por el contratante y los intereses
                    moratorios acumulados o bien rescindir el contrato y aplicar como pena convencional por
                    incumplimiento el <span
                        class="bold">{{ $datos['ajustes_politicas']['porcentaje_pena_convencional_minima'] }}%</span>
                    del
                    monto pagado por el contratante, debiendo a la Agencia
                    Funeraria regresar las cantidades en exceso y que sobren de dicha pena al contratante. En
                    caso de que el retraso en el pago sea superior a los <span
                        class="bold">{{ $datos['ajustes_politicas']['maximo_dias_retraso'] }}</span> días, la
                    Agencia
                    Funeraria podrá
                    igualmente rescindir el Contrato y aplicar como pena convencional la totalidad de los
                    pagos efectuados por el contratante.
                </span>
            </p>

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    En los términos de los dispuestos por el artículo 71 de la Ley Federal de Protección al
                    consumidor, cuando el contratante haya pagado más de la tercera parte del precio o
                    número total de los pagos convenidos ante la notificación de rescisión que le realice la
                    Agencia Funeraria, el contratante podrá optar porque se aplique el mecanismo indicado
                    en el párrafo anterior o bien pagar el saldo del contrato más los intereses moratorios
                    generados por su incumplimiento. En el primer caso (rescisión con penalidad) solo si el
                    retraso fuera menor a <span
                        class="bold">{{ $datos['ajustes_politicas']['maximo_dias_retraso'] }}</span> días, la
                    agencia
                    funeraria devolverá al contratante la cantidad
                    que corresponda una vez aplicada la penalidad y los intereses moratorios. En el segundo
                    caso (pago total de saldo insoluto), la Agencia Funeraria entregará al contratante el recibo
                    de finiquito correspondiente solo si este paga la cantidad total adeudada (saldo insoluto
                    contratado más los intereses moratorios).
                </span>
            </p>

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">d) </span>
                <span class="ml-2">
                    Si “El Cliente” cede en favor de tercera persona los derechos de uso de este convenio sin aceptación
                    por escrito de parte de “La Empresa”.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">e) </span>
                <span class="ml-2">
                    Si “El Cliente” grava en cualquier forma los derechos que este convenio le confiere
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">f) </span>
                <span class="ml-2">
                    Si “El Cliente” dejare de cumplir algunas de las obligaciones que contrae en este convenio distintos
                    a los estipulados anteriormente, de tal manera graves, que conduzcan a la recisión de este convenio
                    o a que “La Empresa” haga exigible anticipadamente el saldo a cargo de “El Cliente”.
                </span>
            </p>
        </div>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vigésima quinta.- </span>
            En los casos estipulados den la cláusula anterior, y siempre y cuando no haya sido utilizado el lote materia
            de este convenio, “La Empresa” quedará facultada para hacer uso de dicho lote, estando la empresa obligada
            en avisar a “El Cliente” que ha incurrido en las cláusulas de recisión de
            este convenio. En caso de que el lote en cuestión se encuentre utilizado, queda facultada “La Empresa” para
            exhumar los restos humanos conforme a las aportaciones a las disposiciones legales correspondientes.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vigésima sexta.- </span>
            “El Cliente” tendrá el derecho de rescindir este convenio dentro de los <span
                class="uppercase bold texto-sm">10 días</span> hábiles siguientes a su
            firma, sin menoscabo de las aportaciones realizadas, comprometiéndose “La Empresa” a devolver íntegramente
            de las mismas en un plazo no mayor a los <span class="uppercase bold texto-sm">10 días</span> hábiles
            siguientes a la fecha en que le sea notificada
            por escrito con acuse de recibo dicha cancelación.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vigésima séptima.- </span>
            Pasado el plazo mencionado en la cláusula anterior, en los casos de cancelación del presente convenio a
            solicitud de “El Cliente”, bajo ningún concepto o circunstancia “La Empresa” quedará obligada a reintegrarle
            a “El Cliente”, el monto de los pagos que haya entregado a “La Empresa” en el cumplimiento de las
            operaciones celebradas en el presente convenio.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vigésima octava.- </span>
            Se anexa hoja de reglamento interno del Panteón Aeternus. Toda documentación adicional que se firme por
            ambas partes en relación y con motivo de este convenio,
            constituirá un anexo del mismo, y, por lo tanto, formará parte integral del presente convenio.
        </p>


        @if ($datos['venta_terreno']['tipo_propiedades_id'] != 3)
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Vigésima novena.- </span>
                Para ofrecer servicios de inhumación en cripta el cuerpo deberá ser debidamente embalsamado en la
                funeraria
                que le está ofreciendo el servicio.
            </p>
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Trigésima.- </span>
                Para la interpretación, cumplimiento cualquier controversia con motivo del presente convenio, las partes
                se
                someten a la jurisdicción y competencia de los tribunales del fuero común de <span
                    class="uppercase bold texto-sm">{{ $empresa->registro_publico['ciudad_np'] }}</span>,
                <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['estado_np'] }}</span>
                y renunciar
                desde luego a cualquier otro fuero que por la razón de su domicilio podría convenir.
            </p>
        @else
            <p class="texto-base justificar line-base">
                <span class="uppercase bold texto-sm underline pr-2">Vigésima novena.- </span>
                Para la interpretación, cumplimiento cualquier controversia con motivo del presente convenio, las partes
                se
                someten a la jurisdicción y competencia de los tribunales del fuero común de <span
                    class="uppercase bold texto-sm">{{ $empresa->registro_publico['ciudad_np'] }}</span>,
                <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['estado_np'] }}</span>
                y renunciar
                desde luego a cualquier otro fuero que por la razón de su domicilio podría convenir.
            </p>
        @endif

        <div class="w-100 center">
            <div class="w-50 float-left mt-50">
                <img src="{{ $firmas['gerente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top">
                    <div class="">
                        <span class="uppercase  texto-sm">{{ $empresa->razon_social }}</span>
                    </div>
                    <span class="uppercase bold texto-sm">"la empresa"</span>
                </div>
            </div>
            <div class="w-50 float-right mt-50">
                <img src="{{ $firmas['cliente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top">
                    <div class="">
                        <span class="uppercase  texto-sm">El (La) C. {{ $datos['nombre'] }}</span>
                    </div>
                    <span class="uppercase bold texto-sm">"el cliente"</span>
                </div>
            </div>
        </div>
    </div>


</body>

</html>
<span class="uppercase bold texto-sm"></span>
