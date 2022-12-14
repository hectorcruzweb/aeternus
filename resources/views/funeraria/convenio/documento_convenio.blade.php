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
            Convenio de servicio a futuro de <span class="bold uppercase"><span
                    class="texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span></span>,
            que celebran por una parte <span class="bold uppercase"><span
                    class="texto-sm">{{ $empresa->razon_social }}</span></span>,
            con domicilio en
            <span class="uppercase texto-sm bold">{{ $empresa->calle }}, {{ $empresa->num_ext }}, Col.
                {{ $empresa->colonia }}
                C.P {{ $empresa->cp }}</span>, de esta ciudad; a quien en lo sucesivo se le denominar?? la <span
                class="bold uppercase texto-sm">"La Empresa"</span>,
            y por la otra parte, por su propio derecho, El (La) C.
            <span class="uppercase texto-sm bold bg-gray px-1">{{ $datos['nombre'] }}</span>,
            quien en lo sucesivo se denominar?? <span class="uppercase texto-sm bold">"El cliente"</span> y ser?? el
            Titular del presente convenio,
            el cual ambas partes se comprometen a firmar, de conformidad con las siguientes declaraciones y
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

        <p class="texto-base justificar line-base">
            <span class="uppercase bold">II. </span>
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
            <span class="uppercase bold">III. </span>
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
            La empresa se compromete a efectuar, a petici??n del ???Cliente???, un <span
                class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span>, el cual
            comprender?? los
            siguientes conceptos:
        </p>
        @if(count($datos['venta_plan']['conceptos_originales'])>0)
        <?php
     $seccion='';
     for ($id_seccion=1; $id_seccion < 5; $id_seccion++) { 
        if($id_seccion==1){
            $seccion="incluye";
        }elseif($id_seccion==2){
            $seccion="en caso de inhumaci??n";
        }elseif($id_seccion==3){
            $seccion="en caso de cremaci??n";
        }elseif($id_seccion==4){
            $seccion="en caso de velaci??n";
        }
$mostrar=false;
    foreach ($datos['venta_plan']['conceptos_originales'] as $index => $concepto) {
       if($concepto['seccion_id']==$id_seccion){
           $mostrar=true;
       break;
       }
    }//en foreach

    if($mostrar==true){
?>
        <p class="texto-sm justificar line-base uppercase bold">
            {{ $seccion }}
        </p>
        <div class="lista pl-11 -mt-1">
            @foreach($datos['venta_plan']['conceptos_originales'] as $index=>$concepto)
            @if ($concepto['seccion_id']==$id_seccion)
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">{{ letra_alfabeto($index) }}) </span>
                <span class="ml-2">
                    {{ $concepto['concepto'] }}
                </span>
            </p>
            @endif

            @endforeach
        </div>
        <?php
    }//end if mostrar
     }//en for
     ?>

        @else
        <p class="texto-base justificar line-base center uppercase bg-gray bold">
            no se han capturado servicios/art??culos para este convenio.
        </p>
        @endif

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Segunda.- </span>
            A fin de recibir el <span
                class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span> motivo de este
            convenio. ???El Cliente??? entregar?? en las
            instalaciones de ???La
            Empresa???, ubicadas en <span class="uppercase bold texto-sm">
                {{ strtolower($empresa->calle) }} N??m. Ext {{ $empresa->num_ext }}, Col.
                {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
            </span> de esta ciudad:
        </p>
        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    El convenio original que ampara dicho <span
                        class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span>.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    El certificado de defunci??n de la persona que recibir?? el <span
                        class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span> amparado
                    por el
                    convenio en
                    cuesti??n.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    La autorizaci??n para llevar a cabo el <span
                        class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span> de la
                    persona fallecida, firmada por
                    el familiar
                    directamente responsable.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">d) </span>
                <span class="ml-2">
                    Cualquier otra documentaci??n que sea necesaria para efectuar los tr??mites correspondientes y/o
                    conseguir los permisos
                    necesarios para la realizaci??n de dicho servicio(<span
                        class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span>).
                </span>
            </p>
        </div>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Tercera.- </span>
            En caso de que ???El Cliente??? solicite a ???La Empresa??? servicios adicionales a los ofrecidos por este convenio,
            el importe
            de los mismos deber?? ser cubierto en su totalidad al momento de concluir el <span
                class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span> amparado
            por este
            convenio.
            </span>.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Cuarta.- </span>
            ???La Empresa??? programar?? los servicios para llevar a cabo el <span
                class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span> de acuerdo al orden
            en
            que hayan sido
            solicitados, motivos por el cual ???El Cliente??? deber?? solicitar el servicio con cuando menos tres horas de
            anticipaci??n.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Quinta.- </span>
            ???La Empresa??? ofrecer?? servicios de cremaci??n de lunes a domingo, de 09:00 a 17:00 horas.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">sexta.- </span>
            ???La Empresa??? se reserva el derecho de no otorgar el <span
                class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span> cuando existan
            impedimentos
            legales, f??sicos
            y/o sanitarios.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">s??ptima.- </span>
            Ambas partes acuerdan que en caso de imposibilidad de efectuar el <span
                class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span> por causas ajenas a
            la voluntad
            de ambos, como son: fallas de energ??a el??ctrica, combustible o fallas mec??nicas en el equipo, el <span
                class="uppercase bold texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span>
            se efectuar??
            el primer d??a en que estas hayan sido corregidas, en el orden en que se presentaron las solicitudes
            correspondientes.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Octava.- </span>
            En caso de cambio de domicilio por parte de ???El Cliente??? se conviene que, en este, en un plazo m??ximo de
            <span class="uppercase bold texto-sm">quince d??as</span>, despu??s de efectuado el cambio, deber?? comunicarlo
            por escrito a ???La Empresa??? con acuse de
            recibo por parte de este.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">Novena.- </span>
            ???El Cliente??? tendr?? la facultad de ceder los derechos de uso de este convenio, mediante su renuncia a la
            membres??a de ???La Empresa???, lo cual podr?? hacer mediante la entrega de un escrito a ???La Empresa??? con acuse de
            recibo, siempre y cuando este al corriente de sus pagos. En dicho escrito deber?? especificar el nombre de la
            persona que recibir?? los derechos del convenio.
        </p>


        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima.- </span>
            Para la cesi??n de los derechos de uso de este convenio, ???El Cliente??? deber?? pagar en las instalaciones de
            ???La Empresa??? un cargo por concepto de la realizaci??n de dicho tr??mite. El importe del mismo ser?? el que
            conste en lista de precios de ???La Empresa??? al momento de realizar la cesi??n antes mencionada. El adquiriente
            de
            los derechos de uso de un convenio transferido, estar?? obligado a cubrir las aportaciones pendientes de
            pagar en los planes establecidos en el convenio transferido.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima primera.- </span>
            En caso de que al realizar la cesi??n de derechos de este convenio, fuera necesario cubrir alg??n tipo de
            cuota,
            derecho, impuesto, etc. A entidades ajenas a ???La Empresa???, tales como la secretaria de salud, Gobierno
            Municipal, etc., ???El Cliente??? ser?? responsable de pago de las mismas.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima segunda.- </span>
            ???El Cliente??? podr?? designar un titular sustituto, el cual debe estar plenamente facultado para decidir sobre
            la utilizaci??n de los servicios funerarios motivo de este convenio, cuando el cliente este imposibilitado
            para hacerlo. Asimismo, podr?? modificar esta designaci??n en cualquier momento, oblig??ndose en ambos casos a
            hacer a notificaci??n por escrito con acuse de recibo, tanto ???La Empresa??? como el titular sustituto, en un
            plazo de <span class="uppercase bold texto-sm">quince d??as</span> naturales despu??s de efectuada dicha
            designaci??n o modificaci??n, anexando el escrito de
            aceptaci??n por parte del titular sustituto.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima tercera.- </span>
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
                        class="uppercase bold texto-sm letter-spacing-3">{{$beneficiario['nombre']}}</span>
                </td>
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
            <span class="uppercase bold texto-sm underline pr-2">D??cima cuarta.- </span>
            En caso de que ???El Cliente??? quisiera cambiar a los beneficiarios designados inicialmente, lo podr?? hacer
            mediante un escrito a la ???Empresa??? con acuse de recibo.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima quinta.- </span>
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
                        class="bold">{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> d??as,
                    la agencia
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
                    Si ???El Cliente??? grava en cualquier forma los derechos que este convenio le confiere.
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
            <span class="uppercase bold texto-sm underline pr-2">D??cima sexta.- </span>
            ???El Cliente??? tendr?? el derecho de rescindir este convenio dentro de los <span
                class="uppercase bold texto-sm">10
                d??as</span> h??biles siguientes a su
            firma, sin menoscabo de las aportaciones realizadas, comprometi??ndose ???La Empresa??? a devolver ??ntegramente
            de las mismas en un plazo no mayor a los <span class="uppercase bold texto-sm">10 d??as</span> h??biles
            siguientes a la fecha en que le sea notificada
            por escrito con acuse de recibo dicha cancelaci??n.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima s??ptima.- </span>
            Pasado el plazo mencionado en la cl??usula anterior, en los casos de cancelaci??n del presente convenio a
            solicitud de ???El Cliente???, bajo ning??n concepto o circunstancia ???La Empresa??? quedar?? obligada a reintegrarle
            a ???El Cliente???, el monto de los pagos que haya entregado a ???La Empresa??? en el cumplimiento de las
            operaciones celebradas en el presente convenio.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima octava.- </span>
            Toda documentaci??n adicional que se firme por ambas partes en relaci??n y con motivo de este convenio,
            constituir?? un anexo del mismo, y, por lo tanto, formar?? parte integral del presente convenio.
        </p>

        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">D??cima novena.- </span>
            Para la interpretaci??n, cumplimiento cualquier controversia con motivo del presente convenio, las partes se
            someten a la jurisdicci??n y competencia de los tribunales del fuero com??n de <span
                class="uppercase bold texto-sm">{{ $empresa->registro_publico['ciudad_np'] }}</span>,
            <span class="uppercase bold texto-sm">{{ $empresa->registro_publico['estado_np'] }}</span>
            y renunciar
            desde luego a cualquier otro fuero que por la raz??n de su domicilio podr??a convenir.
        </p>



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