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
    <h1 class="left">formato de solicitud de servicios(archivo digital)</h1>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-header text-white py-1 px-2 bold mb-1">
            informaci??n del titular
        </div>
        <table class="w-100 center">
            <tr>
                <td class="w-55 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-15 left">
                            <span class="bold uppercase texto-sm">
                                titular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-85 center">
                            {{ $datos['nombre'] }}
                        </div>
                    </div>
                </td>
                <td class="w-45 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-45 left">
                            <span class="bold uppercase texto-sm">
                                fecha de la operaci??n:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-55 center">
                            {{ fecha_no_day($datos['fecha_operacion']) }}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-100 px-2 py-2" colspan="2">
                    <div class="left">
                        <div class="float-left w-19 left">
                            <span class="bold uppercase texto-sm">
                                domicilio part??cular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-81 center">
                            {{ $datos['direccion'] }},
                            {{ $datos['ciudad'] }}
                            {{ $datos['estado'] }}.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100 center">
            <tr>
                <td class="w-35 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-53 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                tel. dom. part??cular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-47 center">
                            {{ $datos['telefono']!=''?$datos['telefono']:'No capturado' }}
                        </div>
                    </div>
                </td>
                <td class="w-30 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-28 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                celular:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-72 center">
                            {{ $datos['celular']!=''?$datos['celular']:'No capturado' }}
                        </div>
                    </div>
                </td>

                <td class="w-35 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-32 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                Tel. Extra:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-68 center">
                            {{ $datos['telefono_extra']!=''?$datos['telefono_extra']:'No capturado' }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="w-100 center">
            <tr>
                <td class="w-21 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-22 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                rfc:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-78 center">
                            {{ $datos['rfc']!=''?$datos['rfc']:'No capturado' }}
                        </div>
                    </div>
                </td>
                <td class="w-40 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-18 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                email:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-82 center lowercase">
                            {{ $datos['email']!=''?$datos['email']:'No capturado' }}
                        </div>
                    </div>
                </td>

                <td class="w-24 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-45 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                fecha nac.:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-55 center">
                            @if (trim($datos['fecha_nac'])!='')
                            {{ fecha_abr(($datos['fecha_nac'])) }}
                            @else
                            N/A
                            @endif
                        </div>
                    </div>
                </td>
                <td class="w-15 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-40 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                edad:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-60 center">
                            @if (trim($datos['fecha_nac'])!='')
                            {{ calculaedad((String)($datos['fecha_nac'])) }} a??os
                            @else
                            N/A a??os
                            @endif

                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-header text-white py-1 px-2 bold mb-1">
            informaci??n del titular Sustituto
        </div>
        <table class="w-100 center">
            <tr>
                <td class="w-100 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-8 left">
                            <span class="bold uppercase texto-sm">
                                nombre:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-92 center">
                            {{ $datos['titular_sustituto'] }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="w-100 center">
            <tr>
                <td class="w-50 px-2 py-2" colspan="2">
                    <div class="left">
                        <div class="float-left w-23 left">
                            <span class="bold uppercase texto-sm">
                                parentesco:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-77 center">
                            {{ $datos['parentesco_titular_sustituto'] }}
                        </div>
                    </div>
                </td>
                <td class="w-50 px-2 py-2" colspan="2">
                    <div class="left">
                        <div class="float-left w-19 left">
                            <span class="bold uppercase texto-sm">
                                tel??fono:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-81 center">
                            {{ $datos['telefono_titular_sustituto'] }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>



    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-header text-white py-1 px-2 bold mb-1">
            informaci??n del plan funerario
        </div>
        <table class="w-100 center">
            <tr>
                <td class="w-100 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-20 left">
                            <span class="bold uppercase texto-sm">
                                plan funerario:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-80 center">
                            {{ $datos['venta_plan']['nombre_original'] }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100">
            <tr>
                <td class="w-40 px-2 py-2">
                    <div class="uppercase bg-header text-white bold mb-1 center">
                        costos acordados
                    </div>
                    <table class="w-100">
                        <tr>
                            <td class="w-55 py-1 left bg-nada">

                                <span class="bold"> plan de venta:</span>
                            </td>
                            <td class="w-45 py-1 right bg-gray">
                                {{ $datos['tipo_financimiento_texto']}}

                            </td>
                        </tr>
                        <tr>
                            <td class="w-55 py-1 left bg-nada">
                                <span class="bold"> costo:</span>

                            </td>
                            <td class="w-45 py-1 right bg-gray">
                                $ {{ number_format( $datos['subtotal'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-55 py-1 left bg-nada">

                                <span class="bold">iva:</span>
                            </td>
                            <td class="w-45 py-1 right bg-gray">
                                $ {{ number_format( $datos['impuestos'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-55 py-1 left bg-nada">
                                <span class="bold">descuento:</span>

                            </td>
                            <td class="w-45 py-1 right bg-gray">
                                $ {{ number_format( $datos['descuento'],2)}}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-55 py-1 left bg-nada">
                                <span class="bold">importe total:</span>

                            </td>
                            <td class="w-45 py-1 right bg-gray">
                                $ {{ number_format( $datos['total'],2)}}
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="w-60 px-2 py-2">
                    <div class="radius-5 border-black-1 p-2">
                        <div class="uppercase bg-header text-white bold mb-1 center">
                            sobre los anticipos a cuenta del contrato
                        </div>

                        <div class="uppercase mt-3  px-2 justificar texto-xs line-lg">
                            Los anticipos que se entreguen durante la venta, se deber??n de entregar en la caja de <span
                                class="bold">{{$empresa->razon_social}}</span>. en un plazo menor de
                            <span class="bold">48 hrs.</span> a partir de la fecha de la venta.
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="w-40 px-2 center">
                     <img src="{{ $firmas['cliente'] }}" class="firma">
                    <span>{{$datos['nombre']}}</span>
                    <div class="w-80 mr-auto ml-auto border-top-black-1 pt-1">
                        firma del cliente
                    </div>
                </td>
                <td class="w-60 px-2 center">
                   <img src="{{ $firmas['vendedor'] }}" class="firma">
                    <span>{{$datos['venta_plan']['vendedor']['nombre']}}</span>
                    <div class="w-80 mr-auto ml-auto border-top-black-1 pt-1">
                        nombre y firma del vendedor
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="border-black-1 radius-5 texto-xs2  px-3 py-2 justificar">
        El contratante, dentro de un plazo de 5 d??as h??biles, contados a partir del siguiente d??a h??bil de la firma del
        contrato, puede cancelar la contrataci??n de los servicios funerarios, sin que sufra menoscabo de los pagos que
        haya realizado a la fecha, sin perjuicio de que el prestador de los servicios pueda ampliar este plazo.
    </div>
    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-header text-white py-1 px-2 bold mb-1">
            programaci??n de pagos
        </div>
        @if ($datos['num_pagos_programados_vigentes']>0)
        <table class="w-100">
            <tr>
                <td class="w-30 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-53 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                {{ $datos['pagos_programados'][0]['concepto_texto'] }}
                            </span>
                        </div>
                        <div class="float-right bg-gray w-47 center">
                            {{number_format($datos['pagos_programados'][0]['monto_programado'],2)}} mxn
                        </div>
                    </div>
                </td>
                <td class="w-38 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-50 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                el resto a cubrir en:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-50 center">
                            @if ($datos['num_pagos_programados_vigentes']==1)
                            N/A
                            @else
                            {{$datos['financiamiento']}} Pagos
                            @endif
                        </div>
                    </div>
                </td>
                <td class="w-32 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-38 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                a pagos de:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-62 center">
                            @if ($datos['num_pagos_programados_vigentes']==1)
                            N/A
                            @else
                            {{number_format($datos['pagos_programados'][1]['monto_programado'],2)}} mxn
                            @endif

                        </div>
                    </div>
                </td>
            </tr>
            @if ($datos['num_pagos_programados_vigentes']>1)
            <tr>
                <td colspan="3">
                    <div class="w-100">
                        @foreach ($datos['pagos_programados'] as $key=>$programado)
                        @if ($programado['status']==1)
                        <div class="w-48 px-2 py-1 {{($key%2>0)?'float-right':'float-left'}}">
                            <div class="left">
                                <div class="float-left w-20 left bg-nada">
                                    <div class="bold uppercase texto-sm center">
                                        {{ $programado['num_pago'] }}
                                    </div>
                                </div>
                                <div class="float-right bg-nada w-40">
                                    <div class="bold uppercase texto-sm center">
                                        {{number_format($programado['monto_programado'],2)}} mxn
                                    </div>
                                </div>
                                <div class="float-right bg-gray w-40">
                                    <div class="bold uppercase texto-sm center">
                                        {{ fecha_abr(($programado['fecha_programada'])) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </td>
            </tr>
            @endif
        </table>
        @else
        <div class="uppercase  py-1 px-2 bold mb-1">
            Este contrato no cuenta con pagos progrmados
        </div>
        @endif
    </div>

    <div class="border-black-1 radius-5 uppercase texto-sm  px-3 py-2">
        <div class="uppercase bg-header text-white py-1 px-2 bold mb-1">
            beneficiarios
        </div>
        <table class="w-100">
            @if (count($datos['beneficiarios'])>0)
            @foreach ($datos['beneficiarios'] as $key=>$beneficiario)
            <tr>
                <td class="w-40 px-2 py-2">
                    <div class="left">
                        <div class="float-left w-19 left bg-nada">
                            <span class="bold uppercase texto-sm mr-1">
                                nombre:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-81 center">
                            {{$beneficiario['nombre']}}
                        </div>
                    </div>
                </td>
                <td class="w-30 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-35 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                parentesco:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-65 center">
                            {{$beneficiario['parentesco']}}
                        </div>
                    </div>
                </td>
                <td class="w-30 px-2 py-2">
                    <div class="right">
                        <div class="float-left w-35 left bg-nada">
                            <span class="bold uppercase texto-sm">
                                tel??fono:
                            </span>
                        </div>
                        <div class="float-right bg-gray w-65 center">
                            {{$beneficiario['telefono']}}
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <div class="uppercase bold pl-2">
                no se han registrado beneficiarios
            </div>
            @endif
        </table>
    </div>
    <div class="border-black-1 radius-5 texto-sm  px-3 py-2">
        <p class="texto-xs justificar line-base">
            <span class="- bold texto-sm pr-2 uppercase">Nota.- </span>
            Debo y pagar?? incondicionalmente por este pagar?? a la orden de <span
                class="bold uppercase">{{$empresa->razon_social}}</span> en Mazatl??n, Sinaloa o en cualquier otra
            ciudad que se me requiera el pago por la cantidad de: $ <span
                class="bold uppercase">{{number_format($datos['total'],2)}}</span>({{ NumerosEnLetras::convertir($datos['total'],'Pesos MXN',false) }}).
            Este pagar?? tendr?? vencimiento los d??as <span
                class="bold uppercase">{{dia_numero($datos['fecha_operacion'])}}</span> de cada mes hasta
            cubrir la
            totalidad
            de este documento. El primer pago vencer?? el
            @if ($datos['num_pagos_programados_vigentes']>0)
            <span class="bold uppercase">{{fecha_no_day($datos['pagos_programados'][0]['fecha_programada'])}}</span>
            ({{$datos['pagos_programados'][0]['concepto_texto']}})
            @else
            N/A
            @endif


            , en caso de falta
            de pago de
            <span
                class="bold uppercase">{{ $datos['ajustes_politicas']['maximo_pagos_vencidos'] }}({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['maximo_pagos_vencidos'],'',false) }})</span>
            o m??s vencimientos sucesivos, se entender??
            pagadero este documento a la vista por el saldo insoluto del mismo, en los
            t??rminos se??alados por el art??culo 79 de la Ley General de T??tulos y Operaciones
            de cr??dito. Este pagar?? es causal y no negociable desde la fecha del primer
            vencimiento no pagado hasta el d??a de la soluci??n del adeudo. Este causar?? inter??s
            moratorio a tasa mensual del <span
                class="bold uppercase">{{($datos['ajustes_politicas']['tasa_fija_anual']/12)}}%</span> en Mazatl??n,
            Sinaloa <span class="bold lowercase capitalize">{{fecha_no_day($datos['fecha_operacion'])}}</span>.
        </p>
        <p class="texto-xs justificar line-base">
            El contratante se obliga a pagar a la agencia funeraria las parcialidades contratadas dentro
            de los primeros <span class="bold">{{$datos['ajustes_politicas']['dias_antes_vencimiento']}}</span> d??as
            h??biles naturales a la fecha de vencimiento mensual que le
            corresponda.
        </p>

        <p class="texto-xs justificar line-base">
            El monto a pagar a la Agencia Funeraria comprende todas las cantidades y conceptos que
            el contratante se ha obligado a cubrir, no existiendo ning??n gasto, inter??s o cualquier
            cargo adicional, siempre que el contratante pague en tiempo las parcialidades convenidas.
            En caso de retraso mensual, el contratante se obliga a pagar a la agencia funeraria inter??s
            moratorio del <span class="bold">{{$datos['ajustes_politicas']['tasa_fija_anual']}}</span>%
            ({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['tasa_fija_anual'],'',false) }} por ciento) fija
            anual, la que se calcular?? y liquidar?? sobre
            cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
            calcular??n multiplicando el monto de lo que adeude el contratante por la tasa de inter??s
            anual, dividida entre 365, este resultado se multiplica por el n??mero de d??as transcurridos
            entre la fecha de pago que debi?? ser hecho y la fecha que el contratante liquide el adeudo.
        </p>

        <p class="texto-xs justificar line-base">
            En caso de que el retraso supere los <span
                class="bold">{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> d??as, la agencia funeraria
            podr?? elegir entre exigir
            el pago de todas las mensualidades aun no pagadas por el contratante y los intereses
            moratorios acumulados o bien rescindir el contrato y aplicar como pena convencional por
            incumplimiento el <span
                class="bold">{{$datos['ajustes_politicas']['porcentaje_pena_convencional_minima']}}%</span> del monto
            pagado por el contratante, debiendo a la Agencia
            Funeraria regresar las cantidades en exceso y que sobren de dicha pena al contratante. En
            caso de que el retraso en el pago sea superior a los <span
                class="bold">{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> d??as, la Agencia Funeraria
            podr??
            igualmente rescindir el Contrato y aplicar como pena convencional la totalidad de los
            pagos efectuados por el contratante.
        </p>

        <p class="texto-xs justificar line-base">
            En los t??rminos de los dispuestos por el art??culo 71 de la Ley Federal de Protecci??n al
            consumidor, cuando el contratante haya pagado m??s de la tercera parte del precio o
            n??mero total de los pagos convenidos ante la notificaci??n de rescisi??n que le realice la
            Agencia Funeraria, el contratante podr?? optar porque se aplique el mecanismo indicado
            en el p??rrafo anterior o bien pagar el saldo del contrato m??s los intereses moratorios
            generados por su incumplimiento. En el primer caso (rescisi??n con penalidad) solo si el
            retraso fuera menor a <span class="bold">{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> d??as,
            la agencia funeraria devolver?? al contratante la cantidad
            que corresponda una vez aplicada la penalidad y los intereses moratorios. En el segundo
            caso (pago total de saldo insoluto), la Agencia Funeraria entregar?? al contratante el recibo
            de finiquito correspondiente solo si este paga la cantidad total adeudada (saldo insoluto
            contratado m??s los intereses moratorios).
        </p>

        <p class="texto-xs justificar line-base">
            S??lo podr??n reconocerse los pagos de mensualidades por los recibos firmados y sellados
            por la empresa, cuando se efect??en en cajas de la Agencia Funeraria, o los recibos
            firmados por el Banco Santander (M??xico), S.A. a la cuenta <span class="bold">{{$empresa['cuenta']}}</span>
            a m??s tardar en
            la fecha l??mite establecida. Ninguna otra persona est?? autorizada para recibir pagos y, por
            lo tanto, estos no podr??n ser reconocidos por la Agencia Funeraria.
        </p>

        <p class="texto-xs justificar line-base">
            El contratante o Titular Sustituto ser?? quien al requerir los servicios para el usuario que se
            solicitaron, deber?? entregar el contrato, recibos de pagos efectuados o en su caso liquidar el
            saldo total que persista hasta la fecha y cualquier otro adeudo del servicio contratado.
        </p>

        @php
        /*
        @endphp
        <div class="lista pl-11 -mt-1">
            <p class="texto-xs justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    El convenio que ampara dicho servicio de inhumaci??n
                </span>
            </p>
            <p class="texto-xs justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    El certificado de defunci??n de la persona que recibir?? el servicio amparado por el convenio en
                    cuesti??n
                </span>
            </p>
            <p class="texto-xs justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    Cualquier otra documentaci??n que sea necesaria para efectuar los tr??mites correspondientes y/o
                    conseguir los permisos necesarios para la realizaci??n de dicho servicio.
                </span>
            </p>
        </div>
        @php
        */
        @endphp
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>