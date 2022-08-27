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

        .datos-tabla {
            border-collapse: collapse;
        }


        .datos-tabla tr:nth-child(odd) {
            background-color: #f5f5f5;
        }

        .datos-tabla td,
        .datos-tabla th {
            border: 1px solid #ddd;
            padding: 8px;
        }
    </style>
</head>

<body>
    @include('layouts.estilos')
    <table class="w-100">
        <thead>
            <tr>
                <th class="w-10">
                    <img class="logo logos" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                </th>
                <th class="w-90">
                    <div class="w-100 right uppercase">
                        <p class="line-xxs texto-sm light">{{ $empresa->nombre_comercial }}</p>
                        <p class="line-xxs texto-sm  bold">{{ $datos['name_pdf'] }} al día <span
                                class="bold">{{ $datos['fecha'] }}</span>
                        </p>
                        <p class="line-xxs texto-sm light">
                            fecha de impresión {{ fechahora_completa() }}
                        </p>
                        <p class="line-xxs texto-sm light">
                            ÁLMACEN DE FUNERARIA
                        </p>

                        <p class="line-xxs texto-sm bold mt-4">
                            <span class="bg-gray">COSTO TOTAL DEL INVENTARIO
                                $ {{  number_format($datos['costo_inventario'],2)}}</span>
                        </p>
                    </div>
                </th>
            </tr>
        </thead>
    </table>



    @foreach ($datos['articulos'] as $articulo)
    <table class="w-100 mt-8">
        <tr class="bg-gray">
            <th class="justificar px-2">
                Nombre (Producto)
            </th>
            <th class="right light px-2">
                {{ $articulo['descripcion'] }}
            </th>
        </tr>
    </table>

    <table class="w-80 ml-auto">
        <tr class="">
            <th class="center">
                Lote
            </th>
            <th class="center">
                Existencia
            </th>
            <th class="right">
                $ Costo
            </th>
            <th class="right">
                $ Costo Total x Lote
            </th>
        </tr>
        @foreach ($articulo['inventario_existencia_cero'] as $lote)
        @if ($lote['ver_inventario_b']==1)
        <tr class="">
            <td class="center">
                {{ $lote['num_lote_inventario'] }}
            </td>
            <td class="center">
                {{ $lote['existencia'] }}
            </td>
            <td class="right">
                {{  number_format($lote['costo_costeado'],2)}}
            </td>
            <td class="right">
                {{ number_format($lote['total_costo_lote'],2) }}
            </td>
        </tr>
        @endif
        @endforeach
        <tr class="bg-gray">
            <td class="center bold" colspan="2">
                Total por artículo
            </td>
            <td class="right">
                $ {{  number_format($articulo['total_costo_articulo'],2)}}
            </td>
            <td class="right">
                $ {{ number_format($articulo['total_costo_articulo'],2) }}
            </td>
        </tr>
    </table>

    @endforeach

</body>

</html>