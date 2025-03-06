<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    @include('layouts.estilos')
    <style>
        /*fin de parrafos*/
    </style>
</head>

<body style="" class="relative">
    @if ($datos['status'] == 0)
        <span class="watermark watermark-danger mt-45 left-40 px-5 uppercase size-2 w-normal absolute">
            cancelado
        </span>
    @endif

    @if ($datos['status'] == 3)
        <span class="watermark watermark-danger mt-40 left-33 px-5 uppercase size-2 w-normal absolute">
            vencido
        </span>
    @endif
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

    <table class="w-100 mt-5 datos_tabla uppercase texto-xs">
        <tr>
            <td class="w-60 py-1 px-2 bg-gray"><span class="bold">Datos del Cliente</span></td>
            <td class="w-20 px-2 center bg-gray"><span class="bold">Núm. de Cotización</span></td>
            <td class="w-20 px-2 center bg-gray"><span class="bold">Cotización</span></td>
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
                            <span class="bold">email:</span> <span class="light lowercase">
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
            <td colspan="2" class="center py-2 texto-xs2">
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
</body>

</html>
