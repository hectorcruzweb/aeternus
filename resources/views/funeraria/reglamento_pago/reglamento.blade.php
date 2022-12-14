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


        .santander {
            color: #D31413 !important;
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
                    <td style="width:25%;">
                        <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo">
                    </td>
                    <td style="width:75%;">

                    </td>
                </tr>
            </table>
        </section>
    </header>
    <h1 class="size-20px uppercase bold pt-2">
        reglamento de pagos para ventas a futuro
    </h1>


    <div class="contenido parrafo4">
        <h1 class="texto-base bold left">
            1.- pago de abonos/liquidaci??n
        </h1>
        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Las cuotas por liquidaci??n, enganche o abono de la venta se pagar??n en la modalidad que haya elegido
                    el titular al
                    momento de la compra del plan funerario a futuro en <span
                        class="bold uppercase">{{ $empresa->nombre_comercial }}</span> y deber??n ser
                    cubiertas a
                    m??s tardar en la fecha programada
                    de cada pago para no
                    generar recargos por intereses moratorios, seg??n las pol??ticas de la empresa.
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    Seg??n la cla??sula <span class="uppercase bold texto-sm">Vig??sima sexta</span>,
                    ???El Cliente??? tendr?? el derecho de rescindir este convenio dentro de los <span
                        class="uppercase bold texto-sm">5
                        d??as</span> h??biles siguientes a su
                    firma, sin menoscabo de las aportaciones realizadas, comprometi??ndose ???La Empresa??? a devolver
                    ??ntegramente
                    de las mismas en un plazo no mayor a los <span class="uppercase bold texto-sm">5 dias</span> h??biles
                    siguientes a la fecha en que le sea notificada
                    por escrito con acuse de recibo dicha cancelaci??n.
                </span>
            </p>
        </div>


        <h1 class="texto-base bold left pt-5">
            2.- descuentos
        </h1>
        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Por el pago anticipado de las cuotas (antes de la fecha programada), la empresa podr?? ofrecer los
                    descuentos que
                    considere seg??n las t??rminos en que se realiz?? la venta.
                </span>
            </p>
        </div>


        <h1 class="texto-base bold left pt-5">
            3.- formas de pago
        </h1>
        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    El titular podr?? solicitar las fichas de pago con el monto a pagar al personal de
                    <span class="bold uppercase">{{ $empresa->nombre_comercial }}</span>, con dicho documento
                    (tambi??n conocido como talonario de pago) tendr?? cada referencia para poder efectuar
                    su pago correspondiente en los bancos <span class="santander">Santander</span>.
                </span>
            </p>

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    Los pagos tambi??n se podr??n efectuar en las instalaciones de <span
                        class="bold uppercase">{{ $empresa->nombre_comercial }}</span>, por medio de tarjeta de cr??dito,
                    d??bito o efectivo en los
                    horarios laborales de la empresa.
                </span>
            </p>

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    Cada pago a realizar tendr?? un d??a l??mite de pago de acuerdo al n??mero de pago y la fecha que
                    corresponde,
                    posterior a dicha fecha se deber?? solicitar un nuevo talonario de pagos, mismo que ya incluir?? los
                    recargos por
                    intereses moratorios seg??n los t??rminos de la venta.
                </span>
            </p>

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">d) </span>
                <span class="ml-2">
                    Favor de conservar todos los comprobantes de pago para cualquier aclaraci??n.
                </span>
            </p>
        </div>

        <h1 class="texto-base bold left pt-5">
            4.- recargos por intereses moratorios
        </h1>
        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">

                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    En caso de retrasarse en el pago mensual de las aportaciones, ???El Cliente??? deber?? cubrir una pena
                    convencional sobre el total del monto de adeudado,
                    importe que se considerar?? como aportaci??n
                    adicional complementaria al cliente. El contratante se obliga a pagar a la agencia funeraria inter??s
                    moratorio del <span class="bold">{{$datos['ajustes_politicas']['tasa_fija_anual']}}</span>%
                    ({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['tasa_fija_anual'],'',false) }} por
                    ciento)
                    fija
                    anual, la que se calcular?? y liquidar?? sobre
                    cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
                    calcular??n multiplicando el monto de lo que adeude el contratante por la tasa de inter??s
                    anual, dividida entre 365, este resultado se multiplica por el n??mero de d??as transcurridos
                    entre la fecha de pago que debi?? ser hecho y la fecha que el contratante liquide el adeudo.
                </span>
            </p>
        </div>


        <h1 class="texto-base bold left pt-5">
            5.- consecuencias
        </h1>
        <div class="lista pl-11 -mt-1">
            ???La Empresa??? podr?? cancelar el convenio, si este incurriera en cualquiera de los
            supuestos siguientes:

            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    El incumplimiento del pago de <span
                        class="uppercase bold texto-sm">{{$datos['ajustes_politicas']['maximo_pagos_vencidos']}}</span>
                    de las aportaciones en
                    forma consecutiva.
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
                        class="bold">{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> d??as, la
                    agencia
                    funeraria devolver?? al contratante la cantidad
                    que corresponda una vez aplicada la penalidad y los intereses moratorios. En el segundo
                    caso (pago total de saldo insoluto), la Agencia Funeraria entregar?? al contratante el recibo
                    de finiquito correspondiente solo si este paga la cantidad total adeudada (saldo insoluto
                    contratado m??s los intereses moratorios).
                </span>
            </p>
        </div>
    </div>

     <div class="w-100 center">
            <div class="w-50 float-left mt-20">
                <img src="{{ $firmas['gerente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top">
                    <div class="">
                        <span class="uppercase  texto-sm">{{ $empresa->razon_social }}</span>
                    </div>
                    <span class="uppercase bold texto-sm">"la empresa"</span>
                </div>
            </div>
            <div class="w-50 float-right mt-20">
                  <img src="{{ $firmas['cliente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top">
                    <div class="">
                        <span class="uppercase  texto-sm">{{ $datos['nombre'] }}</span>
                    </div>
                    <span class="uppercase bold texto-sm">"el cliente"</span>
                </div>
            </div>
        </div>
</body>

</html>