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
                            {{ strtolower($empresa->calle) }} N??m. Ext {{ $empresa->num_ext }}
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
                                N??mero de convenio
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
            quien en lo sucesivo se denominara <span class="uppercase texto-sm bold">"El cliente"</span> y ser?? el
            Titular del presente convenio,
            el cual ambas partes se comprometen a firmar, de conformidad con las siguiente declaraciones y
            cl??usulas:
        </p>
    </div>



    <div class="contenido parrafo2">
        <h1 class="texto-base bold underline">
            declaraciones
        </h1>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold">I. </span> Declara el representante legal de ???La empresa???, que su representada
            est?? legalmente constituida conforme a las leyes mexicanas,
            seg??n consta en escritura p??blica n??mero <span
                class="bold texto-sm">{{ $empresa->registro_publico['t_nep'] }}</span> (<span
                class="uppercase bold texto-sm">{{ NumerosEnLetras::convertir($empresa->registro_publico['t_nep']) }}</span>)
            del volumen
            <span class="uppercase bold texto-sm">xxxix</span> (<span class="uppercase bold texto-sm">trig??simo
                noveno</span>), pasada en la ciudad de <span
                class="uppercase bold texto-sm">{{ $empresa->registro_publico['ciudad_np'] }}</span>,
            <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['estado_np'] }}</span>,
            ante el protocolo a cargo del notario p??blico n??mero
            {{ $empresa->registro_publico['num_np'] }} (<span
                class="uppercase bold texto-sm">{{ NumerosEnLetras::convertir($empresa->registro_publico['num_np']) }}</span>),
            licenciado <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['fe_lic'] }}</span>.
        </p>

        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold">II. </span>
            Sigue declarando ???La empresa??? que los servicios de inhumaci??n amparados por este convenio,
            se realizaran en el cementerio denominado <span
                class="uppercase bold texto-sm">{{ $empresa->cementerio['cementerio'] }}</span>,
            ubicado en <span class="uppercase bold texto-sm">{{ $empresa->cementerio->calle }},
                #.
                {{ ($empresa->cementerio->num_ext)!=0 ? $empresa->cementerio->num_ext:'N/A' }}
                , Col.
                {{ $empresa->cementerio->colonia }} C.P {{ $empresa->cementerio->cp }}</span>,
            en el municipio de
            <span class="uppercase bold texto-sm">{{ $empresa->cementerio->ciudad }},
                {{ $empresa->cementerio->estado }}</span>.
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold">II. </span>
            Sigue declarando ???La empresa??? que los servicios de inhumaci??n de cenizas amparados por este convenio,
            se realizaran en el cementerio denominado <span
                class="uppercase bold texto-sm">{{ $empresa->cementerio['cementerio'] }}</span>,
            ubicado en <span class="uppercase bold texto-sm">{{ $empresa->cementerio->calle }},
                #.
                {{ ($empresa->cementerio->num_ext)!=0 ? $empresa->cementerio->num_ext:'N/A' }}
                , Col.
                {{ $empresa->cementerio->colonia }} C.P {{ $empresa->cementerio->cp }}</span>,
            en el municipio de
            <span class="uppercase bold texto-sm">{{ $empresa->cementerio->ciudad }},
                {{ $empresa->cementerio->estado }}</span>.
        </p>
        @endif


        <p class="texto-base justificar line-base">
            <span class="uppercase bold">III. </span>
            Declara ???El Cliente??? tener el inter??s y capacidad legal para celebrar este convenio, y declara tener
            @if (trim($datos['fecha_nac'])!='')
            <span class="bg-gray px-1 mr-1">
                (<span class="uppercase bold texto-sm">{{ calculaedad((String)($datos['fecha_nac'])) }}</span>)
                a??os de
                edad
            </span>
            @else
            (<span class="uppercase bold texto-sm">N/A</span>)
            a??os de
            edad
            @endif
            y su domicilio en: <span class="uppercase bold texto-sm">
                {{ $datos['direccion'] }}</span>, Tel. <span class="uppercase bold texto-sm">
                {{ ($datos['telefono'])!='' ? ($datos['telefono']):'"No registrado"' }}</span>,
            Cel. <span class="uppercase bold texto-sm">{{ ($datos['celular']) }}</span> y correo
            electr??nico <span
                class="lowercase bold">{{ ($datos['email'])!='' ? $datos['email']:'"No registrado"' }}</span>
            para efecto de notificaciones y dem??s efectos legales de este convenio.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold">IV. </span>
            Declara ???El Cliente??? tener el inter??s y capacidad legal para ceder derechos para actuar en su nombre al
            titular sustituto de este convenio al C.
            <span class="bg-gray px-1 mr-1">
                <span class="uppercase bold texto-sm">{{ $datos['titular_sustituto'] }}</span>
            </span>
            en su car??cter de: <span class="uppercase bold texto-sm">
                {{ $datos['parentesco_titular_sustituto'] }}</span>, qui??n se puede contactar al Tel. <span
                class="uppercase bold texto-sm">
                {{ ($datos['telefono_titular_sustituto'])!='' ? ($datos['telefono_titular_sustituto']):'"No registrado"' }}</span>.

        </p>
    </div>


    <div class="contenido parrafo3">
        <p class="texto-base justificar line-base">
            Hechas las aclaraciones anteriores. ???La Empresa??? y ???El Cliente??? proceden a la celebraci??n del presente
            convenio, al tenor de las siguientes:
        </p>
    </div>

    <div class="contenido parrafo4">
        <h1 class="texto-base bold underline">
            cl??usulas
        </h1>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">primera.- </span>
            ???El Cliente" adquiere de "La Empresa", el derecho de uso mortuorio a perpetuidad con reserva de
            dominio de <span class="uppercase bold texto-sm">1</span> Terreno(s) <span class="uppercase bold texto-sm">
            </span>, ubicado en la
            <span class="uppercase bold texto-sm bg-gray px-2">
                {{ $datos['venta_terreno']['ubicacion_texto'] }}
            </span>,
            en el <span class="uppercase bold texto-sm">{{ $empresa->cementerio['cementerio'] }}</span>,
            con una capacidad de <span
                class="uppercase bold texto-sm">{{ $datos['venta_terreno']['tipo_propiedad']['capacidad'] }}</span>
            gaveta(s).
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Segunda.- </span>
            ???La Empresa??? se compromete a:
        </p>
        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Proporcionar un t??tulo de aportaci??n que otorga el derecho de uso mortuorio a perpetuidad al titular
                    de este convenio,
                    o en caso del fallecimiento de este, a cualquiera de los beneficiarios del mismo,
                    dentro de los treinta d??as siguientes a aquel en que se haya cubierto en forma total el pago de las
                    aportaciones mencionadas en la cl??usula tercera de este convenio.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    Garantizar que las gavetas mencionadas en la cl??usula primera de este convenio fueron construidas
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
            @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">d) </span>
                <span class="ml-2">
                    Proporcionar e instalar en el espacio mortuorio amparado por este convenio una l??pida de m??rmol,
                    en el que se grabara su nombre, el a??o de nacimiento y el a??o de fallecimiento de
                    cada una de las personas a inhumarse en el lote mencionado en la primera
                    cl??usula de dicho convenio. (<span class="texto-xs bold italic">solo aplica a terrenos D??plex y
                        Cu??druplex</span>).
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
            En contraparte, ???El Cliente???, se compromete a pagar por concepto de aportaciones la cantidad de $ <span
                class="bold texto-sm bg-gray px-2 uppercase">
                {{ number_format($datos['total'],2) }} (
                {{ NumerosEnLetras::convertir($datos['total'],'Pesos m.n',false) }}
                )
            </span>.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Cuarta.- </span>
            ???El Cliente??? se obliga a cubrir sus aportaciones sin necesidad de cobro acudiendo para tal efecto a las
            oficinas de ???La Empresa???
            localizadas en <span class="uppercase texto-sm bold">{{ $empresa->calle }}, {{ $empresa->num_ext }},
                Col.
                {{ $empresa->colonia }} C.P {{ $empresa->cp }}. {{ $empresa->ciudad }}, {{ $empresa->estado }}
            </span>; o a las
            de los bancos que para estos determine la misma, dentro de los primeros diez d??as de cada mes.
            Mediante la siguiente forma:
        </p>
        <div class="lista pl-11 -mt-1">
            @if($datos['num_pagos_programados_vigentes']>0)
            @if($datos['num_pagos_programados_vigentes']==1)
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Una aportaci??n (Pago ??nico) de $ <span
                        class="bg-gray bold px-2 uppercase texto-sm">{{ number_format($datos['pagos_programados'][0]['monto_programado'],2) }}
                        ({{ NumerosEnLetras::convertir($datos['pagos_programados'][0]['monto_programado'],'Pesos m.n',false) }})</span>.
                </span>
            </p>
            @else
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Una aportaci??n inicial de $ <span
                        class="bg-gray bold px-2 uppercase texto-sm">{{ number_format($datos['pagos_programados'][0]['monto_programado'],2) }}
                        ({{ NumerosEnLetras::convertir((($datos['pagos_programados'][0]['monto_programado'])),'Pesos m.n',false) }})</span>.
                </span>
            </p>

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    Y un saldo de $ <span class="bg-gray bold px-2 uppercase texto-sm">
                        {{ number_format($datos['total']-$datos['pagos_programados'][0]['monto_programado'],2) }}
                        (
                        {{ NumerosEnLetras::convertir((($datos['total']-$datos['pagos_programados'][0]['monto_programado'])),'Pesos m.n',false) }})
                    </span>. En <span class="bg-gray bold px-2 uppercase texto-sm">{{ $datos['financiamiento'] }}</span>
                    abonos consecutivos.
                </span>
            </p>
            @endif
            @endif
        </div>
        <p class="texto-base justificar line-base">
            El contratante se obliga a pagar a la agencia funeraria las parcialidades contratadas dentro
            de los primeros <span class="bold">{{$datos['ajustes_politicas']['dias_antes_vencimiento']}}</span> d??as
            h??biles naturales a la fecha de vencimiento mensual que le
            corresponda.
        </p>

        <p class="texto-base justificar line-base">
            S??lo podr??n reconocerse los pagos de mensualidades por los recibos firmados y sellados
            por la empresa, cuando se efect??en en cajas de la Agencia Funeraria, o los recibos
            firmados por el Banco Santander (M??xico), S.A. a la cuenta <span
                class="bold texto-xs">{{$empresa['cuenta']}}</span> a m??s tardar en
            la fecha l??mite establecida. Ninguna otra persona est?? autorizada para recibir pagos y, por
            lo tanto, estos no podr??n ser reconocidos por la Agencia Funeraria.
        </p>

        <p class="texto-base justificar line-base">
            El contratante o Titular Sustituto ser?? quien al requerir los servicios para el usuario que se
            solicitaron, deber?? entregar el contrato, recibos de pagos efectuados o en su caso liquidar el
            saldo total que persista hasta la fecha y cualquier otro adeudo del servicio contratado.
        </p>

        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Quinta.- </span>
            Cuando ???El Cliente??? requiera un servicio de inhumaci??n, deber?? CUBRIR en las oficinas
            de ???La Empresa??? un <span class="uppercase bold texto-sm">cargo</span> por los <span
                class="uppercase bold texto-sm">derechos de apertura</span>, as?? como por las <span
                class="uppercase bold texto-sm">lozas</span> necesarias para el servicio.
            El monto de dicho cargo ser?? por el que conste en las listas de precios de ???La Empresa??? en el momento de
            solicitar el servicio.
        </p>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">sexta.- </span>
            A fin de que ???La Empresa??? est?? en posibilidad de arreglar el lugar donde se realizar?? el servicio de
            inhumaci??n motivo de este convenio, as?? como de proporcionar el equipo adecuado y necesario para el mismo,
            ???El Cliente??? se compromete a solicitar dicho servicio con un m??nimo de <span
                class="uppercase bold texto-sm">cinco</span> horas de anticipaci??n.
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Quinta.- </span>
            Cuando ???El Cliente??? requiera un servicio de inhumaci??n de cenizas, deber?? acudir a las oficinas
            de ???La Empresa??? para solicitar una cita para el dep??sito de cenizas.
        </p>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">sexta.- </span>
            A fin de que ???La Empresa??? est?? en posibilidad de arreglar el lugar donde se realizar?? el servicio de
            inhumaci??n de cenizas motivo de este convenio, as?? como de proporcionar el equipo adecuado y necesario para
            el mismo,
            ???El Cliente??? se compromete a solicitar dicho servicio con un m??nimo de <span
                class="uppercase bold texto-sm">cinco</span> horas de anticipaci??n.
        </p>
        @endif

        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">s??ptima.- </span>
            ???La Empresa??? ofrecer?? el servicio de inhumaci??n amparado por este convenio de <span
                class="uppercase bold texto-sm">lunes a domingo de, {{ $empresa->cementerio->hora_apertura }} a
                {{ $empresa->cementerio->hora_cierre }} horas</span>.
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">s??ptima.- </span>
            ???La Empresa??? ofrecer?? el servicio de inhumaci??n de cenizas amparado por este convenio de <span
                class="uppercase bold texto-sm">lunes a domingo de, {{ $empresa->cementerio->hora_apertura }} a
                {{ $empresa->cementerio->hora_cierre }} horas</span>.
        </p>
        @endif

        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Octava.- </span>
            ???La Empresa??? se reserva el dominio del derecho de inhumaci??n a perpetuidad hasta en tanto no hayan sido
            cubierta en forma total el pago de las aportaciones especificadas en la cl??usula tercera.
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Octava.- </span>
            ???La Empresa??? se reserva el dominio del derecho de inhumaci??n de cenizas a perpetuidad hasta en tanto no
            hayan sido
            cubierta en forma total el pago de las aportaciones especificadas en la cl??usula tercera.
        </p>
        @endif


        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Novena.- </span>
            ???El Cliente??? se compromete a pagar a ???La Empresa??? a m??s tardar el <span class="uppercase bold texto-sm ">
                d??a {{ $empresa->cementerio->dia_maximo_pago }} de
                {{ mes($empresa->cementerio->mes_maximo_pago) }}</span> de cada a??o, una cuota por
            concepto de mantenimiento del parque funerario. El monto de dicho cargo ser?? el equivalente a <span
                class="uppercase bold texto-sm">
                {{ $datos['venta_terreno']['salarios_minimos'] }}
                ({{ NumerosEnLetras::convertir($datos['venta_terreno']['salarios_minimos'],'',false) }})
            </span>
            salarios m??nimos del distrito Federal (CDMX), vigentes al d??a <span class="uppercase bold texto-sm">15
                (QUINCE)</span> del mes de enero de cada a??o. La empresa no se har?? responsable del mantenimiento del
            lote del terreno mencionado en la cl??usula primera, si el
            cliente no se encuentra al corriente con el pago de la cuota por concepto de mantenimiento del parque
            funerario
        </p>

        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima primera.- </span>
            Ambas partes acuerdan expresamente que el destino del lote mencionado en la cl??usula primera ser?? ??nicamente
            para la inhumaci??n de los restos humanos que ???El Cliente??? se??ale por escrito a ???La Empresa???, y que una vez
            ocupadas las gavetas respectivas, los restos solamente podr??n ser exhumados cuando se hayan cumplido las
            disposiciones establecidas por las autoridades competentes.
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima primera.- </span>
            Ambas partes acuerdan expresamente que el destino del lote mencionado en la cl??usula primera ser?? ??nicamente
            para la inhumaci??n de cenizas que ???El Cliente??? se??ale por escrito a ???La Empresa???, y que una vez
            ocupadas las gavetas respectivas, los restos solamente podr??n ser exhumados cuando se hayan cumplido las
            disposiciones establecidas por las autoridades competentes.
        </p>
        @endif





        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima segunda.- </span>
            A fin de recibir el servicio de inhumaci??n amparado por este convenio, ???El Cliente??? entregara en las
            instalaciones de ???La Empresa???, ubicadas en <span class="uppercase texto-sm bold">{{ $empresa->calle }},
                {{ $empresa->num_ext }},
                Col.
                {{ $empresa->colonia }} C.P {{ $empresa->cp }}. {{ $empresa->ciudad }}, {{ $empresa->estado }}
            </span>, de esta ciudad:
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima segunda.- </span>
            A fin de recibir el servicio de inhumaci??n de cenizas amparado por este convenio, ???El Cliente??? entregara en
            las
            instalaciones de ???La Empresa???, ubicadas en <span class="uppercase texto-sm bold">{{ $empresa->calle }},
                {{ $empresa->num_ext }},
                Col.
                {{ $empresa->colonia }} C.P {{ $empresa->cp }}. {{ $empresa->ciudad }}, {{ $empresa->estado }}
            </span>, de esta ciudad:
        </p>
        @endif



        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>

                @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
                <span class="ml-2">
                    El convenio que ampara dicho servicio de inhumaci??n
                </span>
                @else
                <span class="ml-2">
                    El convenio que ampara dicho servicio de inhumaci??n de cenizas
                </span>
                @endif

            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    El t??tulo de propiedad
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    El certificado de defunci??n de la persona que recibir?? el servicio amparado por el convenio en
                    cuesti??n
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">d) </span>
                <span class="ml-2">
                    Cualquier otra documentaci??n que sea necesaria para efectuar los tr??mites correspondientes y/o
                    conseguir los permisos necesarios para la realizaci??n de dicho servicio.
                </span>
            </p>
        </div>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima tercera.- </span>
            ???El Cliente??? declara haber le??do y conocer perfectamente el reglamento interior de el <span
                class="uppercase bold texto-sm">"{{ $empresa->cementerio->cementerio }}"</span>, el cual
            acepta y se obliga a respetar y a hacer respetar, as?? como se compromete a cumplir todas las disposiciones
            legales que en el se establezcan o que en el futuro se adopten por parte de las autoridades competentes.
            Copia de dicho reglamento se les entrega a ???El Cliente??? en este acto.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima cuarta.- </span>
            En caso de cambio de domicilio por parte de ???El Cliente??? se conviene que, en este, en un plazo m??ximo de
            <span class="uppercase bold texto-sm">quince d??as</span>, despu??s de efectuado el cambio, deber?? comunicarlo
            por escrito a ???La Empresa??? con acuse de
            recibo por parte de este.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima quinta.- </span>
            Los servicios amparados por este convenio ??nicamente podr??n ser ofrecidos a ???El Cliente??? o a uno de los
            beneficiarios establecidos en la cl??usula D??cima Sexta.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima sexta.- </span>
            ???El Cliente??? designa como beneficiario(s) a las siguiente(s) persona(s):
        </p>
        @if(count($datos['beneficiarios'])>0)
        <table class="w-100 center">
            <thead>
                <tr>
                    <th><span class="uppercase bold texto-sm bg-gray px-3">#</span></th>
                    <th><span class="uppercase bold texto-sm bg-gray px-3">Nombre</span></th>
                    <th><span class="uppercase bold texto-sm bg-gray px-3">Parentesco</span></th>
                    <th><span class="uppercase bold texto-sm bg-gray px-3">tel??fono</span></th>
                </tr>
            </thead>
            @php
            $num=1;
            @endphp
            @foreach($datos['beneficiarios'] as $beneficiario)
            <tr>
                <td class="pt-1 pb-1"><span
                        class="uppercase bold texto-sm letter-spacing-3 bg-gray px-2">{{$num}}</span>
                </td>
                <td class="pt-1 pb-1"><span
                        class="uppercase bold texto-sm letter-spacing-3">{{$beneficiario['nombre']}}</span></td>
                <td class="pt-1 pb-1"><span
                        class="uppercase bold texto-sm letter-spacing-3">{{$beneficiario['parentesco']}}</span></td>
                <td class="pt-1 pb-1"><span
                        class="uppercase bold texto-sm letter-spacing-3">{{$beneficiario['telefono']}}</span>
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
            En caso de que ???El Cliente??? quisiera cambiar a los beneficiarios designados inicialmente, lo podr?? hacer
            mediante un escrito a la ???Empresa??? con acuse de recibo.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima s??ptima.- </span>
            ???El Cliente??? tendr?? la facultad de ceder los derechos de uso de este convenio, mediante su renuncia a la
            membres??a de ???La Empresa???, lo cual podr?? hacer mediante la entrega de un escrito a ???La Empresa??? con acuse de
            recibo, siempre y cuando este al corriente de sus pagos. En dicho escrito deber?? especificar el nombre de la
            persona que recibir?? los derechos del convenio.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima octava.- </span>
            Para la cesi??n de los derechos de uso de este convenio, ???El Cliente??? deber?? pagar en las instalaciones de
            ???La Empresa??? un cargo por concepto de la realizaci??n de dicho tr??mite. El importe del mismo ser?? el que
            conste en lista de precios de ???La Empresa??? al momento de realizar la cesi??n antes mencionada. El adquiriente
            de
            los derechos de uso de un convenio transferido, estar?? obligado a cubrir las aportaciones pendientes de
            pagar en los planes establecidos en el convenio transferido.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima novena.- </span>
            En caso de que al realizar la cesi??n de derechos de este convenio, fuera necesario cubrir alg??n tipo de
            cuota,
            derecho, impuesto, etc. A entidades ajenas a ???La Empresa???, tales como la secretaria de salud, Gobierno
            Municipal, etc., ???El Cliente??? ser?? responsable de pago de las mismas.
        </p>

        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima.- </span>
            ???El Cliente??? podr?? designar un titular sustituto, el cual debe estar plenamente facultado para decidir sobre
            la utilizaci??n de los servicios funerarios motivo de este convenio, cuando el cliente este imposibilitado
            para hacerlo. Asimismo, podr?? modificar esta designaci??n en cualquier momento, oblig??ndose en ambos casos a
            hacer a notificaci??n por escrito con acuse de recibo, tanto ???La Empresa??? como el titular sustituto, en un
            plazo de <span class="uppercase bold texto-sm">quince d??as</span> naturales despu??s de efectuada dicha
            designaci??n o modificaci??n, anexando el escrito de
            aceptaci??n por parte del titular sustituto.
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima.- </span>
            ???El Cliente??? podr?? designar un titular sustituto, el cual debe estar plenamente facultado para decidir sobre
            la utilizaci??n del servicio de inhumaci??n de cenizas motivo de este convenio, cuando el cliente este
            imposibilitado
            para hacerlo. Asimismo, podr?? modificar esta designaci??n en cualquier momento, oblig??ndose en ambos casos a
            hacer a notificaci??n por escrito con acuse de recibo, tanto ???La Empresa??? como el titular sustituto, en un
            plazo de <span class="uppercase bold texto-sm">quince d??as</span> naturales despu??s de efectuada dicha
            designaci??n o modificaci??n, anexando el escrito de
            aceptaci??n por parte del titular sustituto.
        </p>
        @endif






        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima primera.- </span>
            El importe de los gastos, derecho y/o erogaciones efectuados por ???La Empresa??? por cuenta de ???El Cliente???,
            por
            los conceptos adicionales a los ofrecidos por este convenio, deber??n ser cubiertos en las oficinas de ???La
            Empresa??? a m??s tardar dos horas antes de que se realice el servicio de inhumaci??n.
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima primera.- </span>
            El importe de los gastos, derecho y/o erogaciones efectuados por ???La Empresa??? por cuenta de ???El Cliente???,
            por
            los conceptos adicionales a los ofrecidos por este convenio, deber??n ser cubiertos en las oficinas de ???La
            Empresa??? a m??s tardar dos horas antes de que se realice el servicio de inhumaci??n de cenizas.
        </p>
        @endif



        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima segunda.- </span>
            En el supuesto incumplimiento en el pago de las aportaciones mencionadas en la cl??usula tercera de este
            convenio; y/o en el pago de los gastos, derechos y/o erogaciones mencionadas en la cl??usula vig??sima
            primera, ???El Cliente??? se obligar?? a pagar a ???La Empresa???, los gastos de administraci??n, cobranza y
            comisiones pagadas comprobables y, en su caso, los gastos y costos judiciales.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima tercera.- </span>
            En caso de retrasarse en el pago mensual de las aportaciones, ???El Cliente??? deber?? cubrir una pena
            convencional sobre el total del monto de la mensualidad vencida,
            importe que se considerar?? como aportaci??n
            adicional complementaria al cliente. El contratante se obliga a pagar a la agencia funeraria inter??s
            moratorio del <span class="bold">{{$datos['ajustes_politicas']['tasa_fija_anual']}}</span>%
            ({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['tasa_fija_anual'],'',false) }} por ciento) fija
            anual, la que se calcular?? y liquidar?? sobre
            cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
            calcular??n multiplicando el monto de lo que adeude el contratante por la tasa de inter??s
            anual, dividida entre 365, este resultado se multiplica por el n??mero de d??as transcurridos
            entre la fecha de pago que debi?? ser hecho y la fecha que el contratante liquide el adeudo.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima cuarta.- </span>
            Queda establecido que ???La Empresa??? podr?? cancelar el convenio, si este incurriera en cualquiera de los
            supuestos siguientes:
        </p>

        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    El incumplimiento del pago de <span
                        class="uppercase bold texto-sm">{{$datos['ajustes_politicas']['maximo_pagos_vencidos']}}</span>
                    de las aportaciones en
                    forma consecutiva
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    En caso de que el retraso supere los <span
                        class="bold">{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> d??as, la agencia
                    funeraria podr?? elegir entre exigir
                    el pago de todas las mensualidades aun no pagadas por el contratante y los intereses
                    moratorios acumulados o bien rescindir el contrato y aplicar como pena convencional por
                    incumplimiento el <span
                        class="bold">{{$datos['ajustes_politicas']['porcentaje_pena_convencional_minima']}}%</span> del
                    monto pagado por el contratante, debiendo a la Agencia
                    Funeraria regresar las cantidades en exceso y que sobren de dicha pena al contratante. En
                    caso de que el retraso en el pago sea superior a los <span
                        class="bold">{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> d??as, la Agencia
                    Funeraria podr??
                    igualmente rescindir el Contrato y aplicar como pena convencional la totalidad de los
                    pagos efectuados por el contratante.
                </span>
            </p>

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    En los t??rminos de los dispuestos por el art??culo 71 de la Ley Federal de Protecci??n al
                    consumidor, cuando el contratante haya pagado m??s de la tercera parte del precio o
                    n??mero total de los pagos convenidos ante la notificaci??n de rescisi??n que le realice la
                    Agencia Funeraria, el contratante podr?? optar porque se aplique el mecanismo indicado
                    en el p??rrafo anterior o bien pagar el saldo del contrato m??s los intereses moratorios
                    generados por su incumplimiento. En el primer caso (rescisi??n con penalidad) solo si el
                    retraso fuera menor a <span
                        class="bold">{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> d??as, la agencia
                    funeraria devolver?? al contratante la cantidad
                    que corresponda una vez aplicada la penalidad y los intereses moratorios. En el segundo
                    caso (pago total de saldo insoluto), la Agencia Funeraria entregar?? al contratante el recibo
                    de finiquito correspondiente solo si este paga la cantidad total adeudada (saldo insoluto
                    contratado m??s los intereses moratorios).
                </span>
            </p>

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">d) </span>
                <span class="ml-2">
                    Si ???El Cliente??? cede en favor de tercera persona los derechos de uso de este convenio sin aceptaci??n
                    por escrito de parte de ???La Empresa???.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">e) </span>
                <span class="ml-2">
                    Si ???El Cliente??? grava en cualquier forma los derechos que este convenio le confiere
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">f) </span>
                <span class="ml-2">
                    Si ???El Cliente??? dejare de cumplir algunas de las obligaciones que contrae en este convenio distintos
                    a los estipulados anteriormente, de tal manera graves, que conduzcan a la recisi??n de este convenio
                    o a que ???La Empresa??? haga exigible anticipadamente el saldo a cargo de ???El Cliente???.
                </span>
            </p>
        </div>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima quinta.- </span>
            En los casos estipulados den la cl??usula anterior, y siempre y cuando no haya sido utilizado el lote materia
            de este convenio, ???La Empresa??? quedar?? facultada para hacer uso de dicho lote, estando la empresa obligada
            en avisar a ???El Cliente??? que ha incurrido en las cl??usulas de recisi??n de
            este convenio. En caso de que el lote en cuesti??n se encuentre utilizado, queda facultada ???La Empresa??? para
            exhumar los restos humanos conforme a las aportaciones a las disposiciones legales correspondientes.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima sexta.- </span>
            ???El Cliente??? tendr?? el derecho de rescindir este convenio dentro de los <span
                class="uppercase bold texto-sm">10 d??as</span> h??biles siguientes a su
            firma, sin menoscabo de las aportaciones realizadas, comprometi??ndose ???La Empresa??? a devolver ??ntegramente
            de las mismas en un plazo no mayor a los <span class="uppercase bold texto-sm">10 d??as</span> h??biles
            siguientes a la fecha en que le sea notificada
            por escrito con acuse de recibo dicha cancelaci??n.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima s??ptima.- </span>
            Pasado el plazo mencionado en la cl??usula anterior, en los casos de cancelaci??n del presente convenio a
            solicitud de ???El Cliente???, bajo ning??n concepto o circunstancia ???La Empresa??? quedar?? obligada a reintegrarle
            a ???El Cliente???, el monto de los pagos que haya entregado a ???La Empresa??? en el cumplimiento de las
            operaciones celebradas en el presente convenio.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima octava.- </span>
            Se anexa hoja de reglamento interno del Pante??n Aeternus. Toda documentaci??n adicional que se firme por
            ambas partes en relaci??n y con motivo de este convenio,
            constituir?? un anexo del mismo, y, por lo tanto, formar?? parte integral del presente convenio.
        </p>


        @if ($datos['venta_terreno']['tipo_propiedades_id']!=3)
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima novena.- </span>
            Para ofrecer servicios de inhumaci??n en cripta el cuerpo deber?? ser debidamente embalsamado en la funeraria
            que le est?? ofreciendo el servicio.
        </p>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Trig??sima.- </span>
            Para la interpretaci??n, cumplimiento cualquier controversia con motivo del presente convenio, las partes se
            someten a la jurisdicci??n y competencia de los tribunales del fuero com??n de <span
                class="uppercase bold texto-sm">{{ $empresa->registro_publico['ciudad_np'] }}</span>,
            <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['estado_np'] }}</span>
            y renunciar
            desde luego a cualquier otro fuero que por la raz??n de su domicilio podr??a convenir.
        </p>
        @else
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Vig??sima novena.- </span>
            Para la interpretaci??n, cumplimiento cualquier controversia con motivo del presente convenio, las partes se
            someten a la jurisdicci??n y competencia de los tribunales del fuero com??n de <span
                class="uppercase bold texto-sm">{{ $empresa->registro_publico['ciudad_np'] }}</span>,
            <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['estado_np'] }}</span>
            y renunciar
            desde luego a cualquier otro fuero que por la raz??n de su domicilio podr??a convenir.
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
