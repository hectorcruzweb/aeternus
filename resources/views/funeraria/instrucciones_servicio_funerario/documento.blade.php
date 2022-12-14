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
                                N??mero Servicio
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


    <div class="contenido parrafo4">
        <h1 class="center mt-5 size-22px">gu??a del <span class="bold">Cliente</span></h1>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">1.- </span>
            Una vez recibida la solicitud por parte del <span class="bold">Cliente</span>, <span class="bold">Aeternus
                Funerales</span>, a trav??s del personal que disponga, efectuar?? los tramites y procedimientos necesarios
            para atender los requerimientos del <span class="bold">Cliente</span> y trasladar al fallecido a las
            instalaciones de <span class="bold">Aeternus Funerales</span>.
        </p>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">2.- </span>
            El personal de <span class="bold">Aeternus Funerales</span> entregar?? al <span class="bold">Cliente</span>
            los formatos de autorizaci??n e informaci??n necesaria para llevar a cabo la elaboraci??n del certificado de
            defunci??n (en caso de aplicar), con el cual <span class="bold">Aeternus Funerales</span> podr?? llevar a cabo
            la elaboraci??n del contrato para la prestaci??n del servicio funerario que el <span
                class="bold">Cliente</span> haya solicitado.
        </p>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">3.- </span>
            Una vez el cuerpo del fallecido haya sido trasladado a las instalaciones de <span class="bold">Aeternus
                Funerales</span>, la persona que se har?? cargo de llevar a cabo el contrato para recibir el servicio por
            parte de <span class="bold">Aeternus Funerales</span> deber?? presentarse en las instalaciones de <span
                class="bold">Aeternus Funerales</span> para firmar la autorizaci??n del contrato e indicar los servicios
            y art??culos que se incluir??n en dicho contrato.
        </p>

        <p class="texto-base justificar line-base">
            <span class="bg-gray px-2">
                El cliente deber?? entregar la siguiente documentaci??n para elaborar el acta de defunci??n:
            </span>
        </p>
        <div class="lista pl-11 -mt-1">
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Certificado m??dico de defunci??n
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">b) </span>
                <span class="ml-2">
                    Copia del acta de nacimiento del fallecido
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">c) </span>
                <span class="ml-2">
                    Copia de identificaci??n oficial del fallecido
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">d) </span>
                <span class="ml-2">
                    Copia de Acta de matrimonio en caso de ser casado (a).
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Copia de Acta de divorcio en caso de ser divorciado (a)
                </span>
            </p>
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">a) </span>
                <span class="ml-2">
                    Copia de Acta de defunci??n del c??nyuge en caso de ser viudo (a).
                </span>
            </p>
        </div>
        <p class="texto-base justificar line-base">
            <span class="uppercase bold texto-sm underline pr-2">4.- </span>
            Copia de identificaci??n oficial de la persona quien realiza el tr??mite del acta de defunci??n
        </p>
    </div>


</body>

</html>
<span class="uppercase bold texto-sm"></span>