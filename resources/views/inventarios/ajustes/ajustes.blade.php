<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <style>
        .logos {
            min-width: 200px;
            max-width: 200px;
        }

        .banco {
            border: 2px solid #E5E8E8 !important;
        }

        .logo {
            display: block;
            margin-right: auto;
        }

        .santander {
            color: #D31413 !important;
        }

        .digito {
            padding: 3px 7px 3px 7px;
            border: 1px solid #dae1e7;
            font-size: 1em;
            line-height: 1.5em;
        }

        .barcode div {
            min-height: 40px !important;
        }

        .bg-total {
            background-color: #FE0000;
            color: #fff;
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

    </style>
</head>

<body>
    @include('layouts.estilos')
    <table class="w-100">
        <thead>
            <tr>
                <th class="w-10">
                    <img class="logo logos -mt-6" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                </th>
                <th class="w-80">
                    <div class="center uppercase -mt-3">
                        <p class="line-xxs size-20px">{{ $empresa->nombre_comercial }}</p>
                        <p class="line-xxs size-20px">Inventario General de Artículos y Servicios</p>
                        <p class="line-xxs size-12px">
                            {{ fechahora_completa() }}
                        </p>
                    </div>
                </th>
                <th class="w-10">
                </th>
            </tr>
        </thead>
    </table>
    @foreach ($ajustes as $ajuste)
        <div class="propiedad mt-8 center uppercase bg-gray-light py-1 semibold size-16px color-semidark">
            Detalle de artículos afectados
        </div>
        <p class="line-xxs">
            <span class="bg-gray-light mr-2">Ajuste Clave:</span> {{ $ajuste['id'] }}
        </p>
        <p class="line-xxs">
            <span class="bg-gray-light mr-2">Tipo:</span> {{ $ajuste['tipo_ajuste_texto'] }}
        </p>
        <p class="line-xxs">
            <span class="bg-gray-light mr-2">Realizado por:</span> {{ $ajuste['registro']['nombre'] }}
        </p>
        <p class="line-xxs">
            <span class="bg-gray-light mr-2">Nota:</span> {{ $ajuste['nota'] != '' ? $ajuste['nota'] : 'N/A' }}
        </p>
        <table class="w-100 pagos_tabla center mt-5">
            <thead>
                <tr class="bg-table-header text-white w-normal capitalize ">
                    <th>
                        Id
                    </th>
                    <th>
                        Cód. Barras
                    </th>
                    <th>
                        Descripción
                    </th>
                    <th>
                        Lote
                    </th>
                    <th>
                        Existencia Sistema
                    </th>
                    <th>
                        Existencia Física
                    </th>
                    <th>
                        Diferencia
                    </th>
                    <th>
                        Resultado
                    </th>
                    <th>
                        Nota
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ajuste['detalles'] as $detalle)
                    <tr>
                        <td class="py-1">{{ $detalle['articulos_id'] }}</td>
                        <td>{{ $detalle['articulos']['codigo_barras'] }}</td>
                        <td>{{ $detalle['articulos']['descripcion'] }}</td>
                        <td>{{ $detalle['num_lote_inventario'] }}</td>
                        <td>{{ $detalle['existencia_sistema'] }}</td>
                        <td>{{ $detalle['existencia_fisica'] }}</td>
                        <td>{{ $detalle['diferencia'] }}</td>
                        <td>
                            @if ($detalle['resultado_ajuste'] != 0)
                                {{ $detalle['resultado_ajuste_texto'] }}
                            @else
                                <span class="text-danger">
                                    {{ $detalle['resultado_ajuste_texto'] }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $detalle['nota'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>

</html>
