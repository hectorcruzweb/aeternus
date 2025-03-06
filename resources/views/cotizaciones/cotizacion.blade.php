<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
</head>

<body>
    @include('layouts.estilos')
    @if (count($datos['conceptos']))
        <table class="w-100 datos_tabla uppercase table-collapsed texto-sm bg-gray">
            <tr>
                <td class="w-100 px-2 py-2 center "><span class="bold">COTIZACIÓN PERSONALIZADA | DESGLOCE DE
                        CONCEPTOS</span>
                </td>
            </tr>
        </table>
        <div class={{ $datos['status'] == 0 ? 'conceptos-content-300px' : 'conceptos-content-500px' }}>
            <table class="w-100 datos_tabla uppercase texto-xs mt-2">
                <tr>
                    <td class="py-1 px-1 bg-gray center"><span class="bold">#</span></td>
                    <td class="px-1 px-1 center bg-gray"><span class="bold">concepto</span></td>
                    <td class="px-1 px-1 center bg-gray"><span class="bold">$ Costo</span></td>
                    <td class="px-1 px-1 center bg-gray"><span class="bold">$ Descuento x Unidad</span></td>
                    <td class="py-1 px-1 bg-gray center"><span class="bold">Cant.</span></td>
                    <td class="px-1 px-1 center bg-gray"><span class="bold">$ Importe</span></td>
                </tr>
                @for ($i = 0; $i < 1; $i++)
                    @foreach ($datos['conceptos'] as $index => $concepto)
                        <tr class="texto-sm">
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
        <table class="w-100 datos_tabla uppercase texto-xs">
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
        @include('cotizaciones.nota', ['nota' => $datos['nota']])
    @endif

    @if (count($datos['predefinidos']))
        @foreach ($datos['predefinidos'] as $index => $predefinido)
            <div class="page-break-always">
                <table class="w-100  uppercase texto-sm bg-gray">
                    <tr>
                        <td class="w-100 px-2 py-2 center "><span class="bold">cotización {{ $predefinido['tipo'] }}
                                | {{ $predefinido['label'] }}</span>
                        </td>
                    </tr>
                </table>
                <div class="py-2">
                    @foreach ($predefinido['secciones'] as $index_seccion => $seccion)
                        <table class="w-100 datos_tabla uppercase mt-1">
                            @if (count($seccion['conceptos']))
                                <tr>
                                    <td class="px-2 py-1 center bg-gray texto-xs "><span
                                            class="bold">{{ $index_seccion == 0 ? 'Incluye' : 'En caso de ' . $seccion['seccion'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-2 texto-sm">
                                        <ol>
                                            @foreach ($seccion['conceptos'] as $index_concepto => $concepto)
                                                <li>{{ $concepto }}</li>
                                            @endforeach
                                        </ol>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    @endforeach
                </div>
                <div class="">
                    <table class="w-100 datos_tabla uppercase  center">
                        <tr>
                            <td class="px-2 py-2 center bg-gray texto-sm" colspan="4"><span
                                    class="bold">Financiamientos</span>
                            </td>
                        </tr>
                        <tr>
                        <tr class=" bold texto-xs">
                            <td class="px-2 py-2">
                                Modalidad
                            </td>
                            <td class="px-2 py-2">
                                $ Costo
                            </td>
                            <td class="px-2 py-2">
                                $ PAgo Inicial
                            </td>
                            <td class="px-2 py-2">
                                $ Pago Mensual
                            </td>
                        </tr>
                        </tr>
                        @foreach ($predefinido['financiamientos'] as $index_financiamiento => $financiamiento)
                            <tr class="texto-xs">
                                <td class="px-2 py-2">
                                    {{ $financiamiento['financiamiento'] }}
                                </td>
                                <td class="px-2 py-2">
                                    $ {{ number_format($financiamiento['costo_neto'], 2) }}
                                </td>
                                <td class="px-2 py-2">
                                    $ {{ number_format($financiamiento['pago_inicial'], 2) }}
                                </td>
                                <td class="px-2 py-2">
                                    $ {{ number_format($financiamiento['pago_mensual'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                @include('cotizaciones.nota', ['datos' => $datos])
            </div>
        @endforeach
    @endif
</body>

</html>
