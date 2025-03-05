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

        .crop {
            word-break: break-all;
        }

        .conceptos-content-full {
            min-height: 750px !important;
            border: 1px solid #ddd;
        }

        .conceptos-content {
            min-height: 550px !important;
            border: 1px solid #ddd;
        }

        .comentario-content {
            min-height: 70px !important;
            height: 70px;
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
                                    <span class="texto-lg semibold line-0 uppercase">R.F.C.
                                        {{ $empresa->rfc }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-100">
                                    <span class="texto-xs2  line-base uppercase">
                                        {{ strtolower($empresa->calle) }} Núm. Ext {{ $empresa->num_ext }} Col.
                                        {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
                                        {{ $empresa->ciudad }}
                                        {{ $empresa->estado }}. Tel. {{ $empresa->telefono }}, fax
                                        {{ $empresa->fax }},
                                        Email {{ $empresa->email }}.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-100">
                                    <span class="texto-base semibold line-0 uppercase">régimen
                                        {{ $empresa['regimen']['regimen'] }}</span>
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
            <td class="w-60 py-1 px-2 bg-gray"><span class="bold">Datos del Cliente</span></td>
            <td class="w-20 px-1 px-2 center bg-gray"><span class="bold">Número de Cotización.</span></td>
            <td class="w-20 px-1 px-2 center bg-gray"><span class="bold">Cotización</span></td>
        </tr>
        <tr>
            <td class="w-60 py-1 px-2" rowspan="2">
                <table class="w-100 table-borderless">
                    <tr>
                        <td class="w-100 py-1">
                            <span class="bold">nombre:</span> <span
                                class="light">{{ $datos['cliente_nombre'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-100 py-1">
                            <span class="bold">email:</span> <span class="light">
                                @if (trim($datos['cliente_email']))
                                    {{ $datos['cliente_email'] }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-100 py-1">
                            <span class="bold">teléfono:</span> <span class="light">
                                @if (trim($datos['cliente_telefono']))
                                    {{ $datos['cliente_telefono'] }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="w-20  px-2 center py-2">
                <span class="light">{{ $datos['id'] }}</span>
            </td>
            <td class="w-20  px-2 center py-2">
                <span class="light">{{ $datos['fecha_texto'] }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="center py-2">
                <h1>Formato de Cotización</h1>
            </td>
        </tr>
        <tr>
            <td class="w-50 px-2 py-2">
                <span class="bold">vendedor:</span> <span class="light">{{ $datos['vendedor']['nombre'] }}</span>
            </td>
            <td class="w-50 center py-2" colspan="2">
                <span class="bold {{ $datos['status'] == 3 ? 'text-danger' : '' }}">fecha de vencimiento:</span> <span
                    class="light">{{ $datos['fecha_vencimiento_texto'] }}</span>
            </td>
        </tr>
    </table>
    <table class="w-100 datos_tabla uppercase table-collapsed texto-xs3 bg-gray">
        <tr>
            <td class="w-100 px-2 py-2 center "><span class="bold">DESGLOCE DE CONCEPTOS</span>
            </td>
        </tr>
    </table>

    <div class="conceptos-content">
        <table class="w-100 datos_tabla uppercase texto-xs3 mt-2">
            <tr>
                <td class="py-1 px-1 bg-gray center"><span class="bold">#</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">concepto</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">$ Costo</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">$ Descuento</span></td>
                <td class="py-1 px-1 bg-gray center"><span class="bold">Cant.</span></td>
                <td class="px-1 px-1 center bg-gray"><span class="bold">$ Importe</span></td>
            </tr>
            @for ($i = 0; $i < 1; $i++)
                @foreach ($datos['conceptos'] as $index => $concepto)
                    <tr>
                        <td class="py-1 px-1 center"><span class="light">{{ $index + 1 }}</span></td>
                        <td class="py-1 px-1 center"><span class="light">{{ $concepto['descripcion'] }}</span>
                        </td>
                        <td class="px-1 px-1 center"><span class="light">$
                                {{ number_format($concepto['costo_neto_normal'], 2) }}</span></td>
                        <td class="px-1 px-1 center"><span class="light">$
                                {{ number_format($concepto['descuento'], 2) }}</span></td>
                        <td class="px-1 px-1 center"><span class="light">{{ $concepto['cantidad'] }}</span>
                        <td class="px-1 px-1 center"><span class="light">$
                                {{ number_format($concepto['importe'], 2) }}</span></td>
                    </tr>
                @endforeach
            @endfor
        </table>
    </div>
</body>

</html>
