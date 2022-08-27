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
            min-height: 550px;
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
                                    @if (ENV('APP_ENV')=='local')
                                    <span class="texto-xs2 semibold line-0 uppercase text-danger">este
                                        documento es una
                                        representación impresa de un cfdi no válido  versión {{$datos['Comprobante']['Version']}}
                                    </span>
                                    @else
                                    <span class="texto-xs2 semibold line-0 uppercase">este
                                        documento es una representación impresa de un cfdi {{$datos['Comprobante']['Version']}}
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
            <td class="w-60 py-1 px-2 bg-gray"><span class="bold">Datos del Receptor</span></td>
            <td class="w-20 px-1 px-2 center bg-gray"><span class="bold">Folio/Serie</span></td>
            <td class="w-20 px-1 px-2 center bg-gray"><span class="bold">fecha y hora</span></td>
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
                            <span class="bold">r.f.c:</span> <span class="light">{{ $datos['Receptor']['Rfc'] }}</span>
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
                    <tr>
                        <td class="w-100" colspan="2">
                            <span class="bold">Régimen Fiscal del Receptor:</span> <span
                                class="light">{{ $datos['Receptor']['RegimenFiscalReceptor'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-100" colspan="2">
                            <span class="bold">CP Domicilio Fiscal:</span> <span
                                class="light">{{ $datos['Receptor']['DomicilioFiscalReceptor'] }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="w-20 px-1 px-2 center py-2">
                <span class="light">{{ $datos['Comprobante']['Folio'].' / '.$datos['Comprobante']['Serie'] }}</span>
            </td>
            <td class="w-20 px-1 px-2 center py-2">
                <span class="light">{{ $datos['Comprobante']['Fecha'] }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="center bg-gray bold">folio fiscal</td>
        </tr>
        <tr>
            <td colspan="2" class="center py-2">{{ $datos['Complemento']['TimbreFiscalDigital']['UUID'] }}</td>
        </tr>
    </table>

   @foreach ($cfdi['servicios_funerarios'] as $servicio)
            @if (isset($servicio['operacion_factura']))
            @foreach ($servicio['operacion_factura'] as $operacion)
            @if ($operacion['empresa_operaciones_id'] == 3)
            <table class="w-100 datos_tabla uppercase table-collapsed texto-xs3">
                <tr>
                    <td class="w-65 py-1 px-2 "><span class="bold">Nombre del Fallecido: </span>
                        <span>{{ $operacion['servicio_funerario']['nombre_afectado'] }}</span></td>
                    <td class="w-35 px-1 px-2 "><span class="bold">Fecha de Defunción: </span>
                        <span>{{ $operacion['servicio_funerario']['fecha_defuncion_texto'] }}</span></td>
                </tr>
            </table>
            @endif
            @endforeach
            @endif
   @endforeach

    


    <table class="w-100 datos_tabla uppercase table-collapsed texto-xs3">
        <tr>
            <td class="w-40 py-1 px-2 center"><span class="bold">facturó: </span>
                <span>{{ $datos['Sistema']['timbro_nombre'] }}</span></td>
            <td class="w-25 px-1 px-2 center "><span class="bold">Expedido en:
                </span><span>Mazatlán, Sin.</span></td>
            <td class="w-35 px-1 px-2 center"><span class="bold">tipo de comprobante: </span>
                <span>{{ $datos['Comprobante']['TipoDeComprobante'] }}</span></td>
        </tr>
    </table>
    @if (isset($datos['CfdisRelacionados']))
    <table class="w-100 datos_tabla uppercase table-collapsed texto-xs3 px-2">
        <tr>
            <td class="w-100 py-1 px-2 left">
                <span class="bold">cfdis relacionados, tipo de relación:</span>
                <span>{{ $datos['CfdisRelacionados']['TipoRelacionTexto'] }}</span>
            </td>
        </tr>
        @foreach ($datos['CfdisRelacionados']['CfdiRelacionado'] as $relacionado)
        <tr>
            <td class="w-100 py-1 px-2 left">
                <span class="bold">uuid:</span>
                <span>{{ $relacionado['UUID'] }}</span>
            </td>
        </tr>
        @endforeach
    </table>
    @endif
    <div class="conceptos-content">
        <table class="w-100 datos_tabla uppercase texto-xs3">
            <tr>
                <td class="py-1 px-1 bg-gray center"><span class="bold">cant.</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">u.m.</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">descripción</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">cód. sat</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">$costo uni.</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">$desc.</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">$iva</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">$importe</span></td>
            </tr>
            @foreach ($datos['Conceptos'] as $concepto)
            <tr>
                <td class="py-1 px-1 center"><span class="light">{{ $concepto['Cantidad'] }}</span></td>
                <td class="px-1 px-1 center"><span class="light">{{ $concepto['ClaveUnidad'] }}</span></td>
                <td class="px-1 px-1 center"><span class="light">{{ $concepto['Descripcion'] }}</span></td>
                <td class="px-1 px-1 center"><span class="light">{{ $concepto['ClaveProdServ'] }}</span></td>
                <td class="px-1 px-1 center"><span
                        class="light">{{number_format( $concepto['ValorUnitario'],2) }}</span>
                </td>
                <td class="px-1 px-1 center"><span class="light">{{number_format( $concepto['Descuento'],2) }}</span>
                <td class="px-1 px-1 center"><span
                        class="light">{{number_format($concepto['Impuestos']['Traslados'][0]['Importe'],2) }}</span>
                </td>
                <td class="px-1 px-1 center"><span
                        class="light">{{ number_format($concepto['Impuestos']['Traslados'][0]['Importe']+$concepto['ValorUnitario']-$concepto['Descuento'],2) }}</span>
                </td>
            </tr>
            @endforeach
        </table>
    </div>


    <table class="w-100 datos_tabla uppercase  texto-xs3">
        <tr>
            <td class="w-75 py-2 px-2 left"><span class="bold">importe con letra: </span>
                <span>son {{ $datos['Sistema']['total_letra'] }}</span></td>
            <td class="w-25 px-2 center" rowspan="2">
                <table class="w-100  uppercase table-borderless">
                    <tr>
                        <td class="w-50 left"><span class="bold">subtotal: </span></td>
                        <td class="w-50 right">$ {{ number_format($datos['Comprobante']['SubTotal'],2) }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 left"><span class="bold">descuento: </span></td>
                        <td class="w-50 right">$ {{ number_format($datos['Comprobante']['Descuento'],2) }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 left"><span class="bold">i.v.a ({{ $cfdi['tasa_iva'] }}%): </span></td>
                        <td class="w-50 right">$ {{ number_format($datos['Impuestos']['TotalImpuestosTrasladados'],2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="w-50 left"><span class="bold">total: </span></td>
                        <td class="w-50 right">$ {{ number_format($datos['Comprobante']['Total'],2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="w-75 px-2">
                <table class="w-100  uppercase table-borderless">
                    <tr>
                        <td class="w-70"><span class="bold">método de pago:
                            </span><span>{{ $datos['Comprobante']['MetodoPago'] }}</span></td>
                        <td class="w-30"><span class="bold">moneda: </span>
                            <span>{{ $datos['Comprobante']['Moneda'] }}</span></td>
                    </tr>
                    <tr>
                        <td class="w-70"><span class="bold" colspan='2'>forma de pago: </span>
                            <span>{{ $datos['Comprobante']['FormaPago'] }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            </td>
        </tr>
    </table>

    <table class="w-100 datos_tabla table-collapsed texto-xs2">
        <tr>
            <td class="w-25 py-1 px-2 center">
                <div class="qr p-4">
                    <?php echo $datos['CodigoQr'];?>
                </div>
            </td>
            <td class="w-75 py-1 px-2 left">
                <div>
                    <span class="bold uppercase texto-xs2">sello digital del emisor</span>
                </div>
                <div class="crop  texto-xs2">{{ $datos['Comprobante']['Sello'] }}</div>
                <div>
                    <span class="bold uppercase texto-xs2">sello digital del sat</span>
                </div>
                <div class="crop  texto-xs2">{{ $datos['Complemento']['TimbreFiscalDigital']['SelloSAT'] }}</div>
                <div>
                    <span class="bold uppercase texto-xs2">cadena original del complemento de certificación del
                        sat</span>
                </div>
                <div class="crop  texto-xs2">{{ $datos['CadenaOriginal'] }}</div>
            </td>
        </tr>
    </table>

    <table class="w-100 datos_tabla uppercase table-collapsed">
        <tr>
            <td class="w-20 py-1 px-1 center texto-xs4">
                <span class="bold">fecha y hora de certificación</span>
            </td>
            <td class="w-25 px-1 center texto-xs4">
                <span class="bold">no. serie del certificado del sat</span>
            </td>
            <td class="w-25 py-1 px-2 center texto-xs4">
                <span class="bold">no. serie del certificado del contribuyente</span>
            </td>
            <td class="w-25 px-1 center texto-xs4">
                <span class="bold">rfc del proveedor de certificación</span>
            </td>
        </tr>
        <tr>
            <td class="w-20 px-1 center texto-xs3">
                <span class="">{{ $datos['Complemento']['TimbreFiscalDigital']['FechaTimbrado'] }}</span>
            </td>
            <td class="w-25 center texto-xs3">
                <span class="">{{ $datos['Complemento']['TimbreFiscalDigital']['NoCertificadoSAT'] }}</span>
            </td>
            <td class="w-25 px-2 center texto-xs3">
                <span class="">{{ $datos['Comprobante']['NoCertificado'] }}</span>
            </td>
            <td class="w-25 center texto-xs3">
                <span class="">{{ $datos['Complemento']['TimbreFiscalDigital']['RfcProvCertif'] }}</span>
            </td>
        </tr>
    </table>
</body>

</html>
