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

        .table-borderless tr td {
            border: none !important;
        }

        .td-noborder {
            border-left: none !important;
            border-right: none !important;
        }

        .table-collapsed {
            border: 1px solid #ddd;
            border-collapse: collapse;
        }

        .table-collapsed tr td {
            border: none !important;
        }


        .qr {
            display: inline-block;
        }

        .crop {
            word-break: break-all;
        }

        .conceptos-content {
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    @include('layouts.estilos')
    <header id="header">
        <section>
            <table class="texto-xs2">
                <tr>
                    <td class="w-40 px-2">
                        <table class="left">
                            <tr>
                                <td class="w-100">
                                    <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo w-65">
                                </td>
                            </tr>
                            <tr>
                                <td class="w-100">
                                    @if (ENV('APP_ENV') == 'local')
                                        <span class="texto-xs2 semibold line-0 uppercase text-danger">
                                            acuse de solicitud de cancelacion
                                            de cfdi no válido
                                        </span>
                                    @else
                                        <span class="texto-xs2 semibold line-0 uppercase">
                                            acuse de solicitud de cancelacion
                                            de cfdi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="w-60 px-2">
                        <table class="center">
                            <tr>
                                <td class="w-100">
                                    <span
                                        class="texto-lg2 semibold line-base uppercase">{{ $empresa->razon_social }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-100">
                                    <span class="texto-lg semibold line-0 uppercase">R.F.C. {{ $empresa->rfc }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-100">
                                    <span class="texto-xs2  line-base uppercase">
                                        {{ strtolower($empresa->calle) }} Núm. Ext {{ $empresa->num_ext }} Col.
                                        {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
                                        {{ $empresa->ciudad }}
                                        {{ $empresa->estado }}. Tel. {{ $empresa->telefono }}, fax {{ $empresa->fax }},
                                        Email {{ $empresa->email }}.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-100">
                                    <span class="texto-base semibold line-0 uppercase">régimen
                                        {{ $datos['Emisor']['RegimenFiscal'] }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </section>
    </header>


    <table class="w-100 mt-5 datos_tabla uppercase texto-xs3">
        <tr>
            <td class="w-60 py-1 px-2 bg-gray"><span class="bold">cliente</span></td>
            <td class="w-20 px-1 px-2 center bg-gray"><span class="bold">Folio/Serie</span></td>
            <!--
            <td class="w-20 px-1 px-2 center bg-gray"><span class="bold">fecha y hora solicitud</span></td>
            -->
        </tr>
        <tr>
            <td class="w-60 py-1 px-2" rowspan="3">
                <table class="w-100 table-borderless">
                    <tr>
                        <td class="w-100" colspan="2">
                            <span class="bold">nombre:</span> <span
                                class="light">{{ $datos['Receptor']['Nombre'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-30">
                            <span class="bold">r.f.c:</span> <span
                                class="light">{{ $datos['Receptor']['Rfc'] }}</span>
                        </td>
                        <td class="w-70">
                            <span class="bold">uso cfdi:</span> <span
                                class="light">{{ $datos['Receptor']['UsoCFDI'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-100" colspan="2">
                            <span class="bold">dirección:</span> <span
                                class="light">{{ $datos['Sistema']['cliente_direccion'] }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="w-20 px-1 px-2 center py-2">
                <span
                    class="light">{{ $datos['Comprobante']['Folio'] . ' / ' . $datos['Comprobante']['Serie'] }}</span>
            </td>
            <!--
            <td class="w-20 px-1 px-2 center py-2">
                <span class="light">{{ $datos['Comprobante']['Fecha'] }}</span>
            </td>
        -->
        </tr>
        <tr>
            <td colspan="2" class="center bg-gray bold">folio fiscal</td>
        </tr>
        <tr>
            <td colspan="2" class="center py-2">{{ $datos['Complemento']['TimbreFiscalDigital']['UUID'] }}</td>
        </tr>
    </table>
    <div class="conceptos-content">
        <table class="w-100 mt-5 datos_tabla uppercase texto-xs3">
            <tr>
                <td class="w-25 py-4 bg-gray px-2">Estado cfdi</td>
                <td class=" px-2">{{ $status_sat['estado'] }}</td>
            </tr>
            <tr>
                <td class="w-25 py-4 bg-gray px-2">Estado de Cancelación</td>
                <td class=" px-2">{{ $status_sat['estatusCancelacion'] }}</td>
            </tr>
            <tr>
                <td class="w-25 py-4 bg-gray px-2">Fecha de Cancelación</td>
                <td class=" px-2">{{ $cfdi->fecha_cancelacion }}</td>
            </tr>
            <!--
            <tr>
                <td class="w-25 py-4 bg-gray px-2">Sello Digital Sat</td>
                <td class=" px-2 crop">{{ $datos['Complemento']['TimbreFiscalDigital']['SelloCFD'] }}</td>
            </tr>
        -->
        </table>
    </div>
</body>

</html>
