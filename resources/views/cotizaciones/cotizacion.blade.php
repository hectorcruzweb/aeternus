<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
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

        .page-break {
            page-break-inside: avoid;
        }

        .page-break-always {
            page-break-before: always;
        }
    </style>
</head>

<body>
    @include('layouts.estilos')
    @if (count($datos['conceptos']))
        <table class="w-100 datos_tabla uppercase table-collapsed texto-xs3 bg-gray">
            <tr>
                <td class="w-100 px-2 py-2 center "><span class="bold">COTIZACIÓN PERSONALIZADA | DESGLOCE DE
                        CONCEPTOS</span>
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
        <table class="w-100 datos_tabla uppercase texto-xs3">
            <tr>
                <td class="w-75 py-2 px-2 left"><span class="bold">Modalidad de Pago: </span>
                    <span>{{ $datos['descripcion_pagos'] }} </span>
                </td>
                <td class="w-25 px-2 center">
                    <table class="w-100  uppercase table-borderless">
                        <tr>
                            <td class="w-50 left"><span class="bold">descuento: </span></td>
                            <td class="w-50 right">$ {{ number_format($datos['descuento'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 left"><span class="bold">total: </span></td>
                            <td class="w-50 right">$ {{ number_format($datos['total'], 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif



    @if (count($datos['predefinidos']))
        @foreach ($datos['predefinidos'] as $index => $predefinido)
            <div class="page-break-always">
                <table class="w-100 datos_tabla uppercase table-collapsed texto-xs3 bg-gray">
                    <tr>
                        <td class="w-100 px-2 py-2 center "><span class="bold">{{ $predefinido['label'] }}</span>
                        </td>
                    </tr>
                </table>
                <div class="conceptos-content">
                    <table class="w-100 datos_tabla uppercase texto-xs3 mt-2">
                        <tr>
                            <td class="py-1 px-1 bg-gray center"><span class="bold">#</span></td>
                            <td class="px-1 px-1 center bg-gray"><span class="bold">Concepto</span></td>
                        </tr>

                    </table>
                </div>
            </div>
        @endforeach
    @endif

    <table class="w-100 datos_tabla uppercase texto-xs3 comentario-content">
        <tr>
            <td class="w-100 py-2 px-2 "><span class="bold">
                    NOTA / COMENTARIOS: </span>
                <span>{!! $datos['nota'] !!}</span>
            </td>
        </tr>
    </table>
</body>

</html>
