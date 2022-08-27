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
                        <p class="line-xxs texto-sm light hidden">{{ $empresa->nombre_comercial }}</p>
                        <p class="line-base texto-sm  bold">{{ $datos['name_pdf'] }} <span
                                class="bold">{{ $datos['fecha'] }}</span>
                        </p>
                        <p class="line-xxs texto-sm light">
                            fecha de impresión {{ fechahora_completa() }}
                        </p>
                        <p class="line-xxs texto-sm light">
                            ÁLMACEN DE FUNERARIA
                        </p>
                        <p class="line-xxs texto-sm bold mt-4">
                            <span class="bg-gray">Método de Costeo PEPS
                            </span>
                        </p>
                    </div>
                </th>
            </tr>
        </thead>
    </table>



    <table class="w-100 ml-auto px-1 mt-2 datos-tabla">
        <tr class="">
            <th class="center">
                Artículo
            </th>
            <th class="center">
                $ Inventario Inicial
            </th>
            <th class="center">
                $ Entradas
            </th>
            <th class="center">
                $ Salidas
            </th>
            <th class="center">
                $ Costo Total
            </th>
            <th class="center">
                Rotación de Inventario
            </th>
        </tr>
        @foreach ($datos['articulos'] as $articulo)
        <tr class="">
            <td class="left">
                {{  $articulo['descripcion']}}
            </td>
            <td class="center">
                {{  number_format($articulo['costo_inventario_inicial'],2)}}
            </td>
            <td class="right ">
                {{  number_format($articulo['costo_entradas'],2)}}
            </td>
            <td class="right">
                {{  number_format($articulo['costo_salidas'],2)}}
            </td>
            <td class="right">
                {{  number_format($articulo['costo_inventario_final'],2)}}
            </td>
            <td class="center">
                {{  $articulo['rotacion']}}
            </td>
        </tr>
        @endforeach
        <tr class="bg-gray bold">
            <td class="center bold">
                TOTALES
            </td>
            <td class="center">
                {{  number_format($datos['totales']['total_inventario_inicial'],2)}}
            </td>
            <td class="right">
                {{  number_format($datos['totales']['total_entradas'],2)}}
            </td>
            <td class="right">
                {{  number_format($datos['totales']['total_salidas'],2)}}
            </td>
            <td class="right">
                {{  number_format($datos['totales']['total_costo_total'],2)}}
            </td>
            <td class="center">
                N/A
            </td>
        </tr>
    </table>
</body>

</html>