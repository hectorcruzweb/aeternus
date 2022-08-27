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

        .logo {
            display: block;
            margin-right: auto;
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


        .no-overlap tr {
            page-break-inside: avoid;
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
                        <p class="line-xxs size-20px">Inventario General Ordenado por Lotes</p>
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
    @foreach ($articulos as $articulo)
        <table class="no-overlap w-100">
            <tr>
                <td class="w-100">
                    <table class="w-100 center mt-5 bg-gray-light no-overlap">
                        <thead>
                            <tr class="w-normal capitalize ">
                                <th>
                                    Id. Artículo
                                </th>
                                <th>
                                    Código de Barras
                                </th>
                                <th>
                                    Descripción
                                </th>
                                <th>
                                    Categoría
                                </th>
                                <th>
                                    Existencia Sistema
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $articulo['id'] }}</td>
                                <td>{{ $articulo['codigo_barras'] }}</td>
                                <td>{{ $articulo['descripcion'] }}</td>
                                <td>{{ $articulo['categoria']['categoria'] }}</td>
                                <td>{{ $articulo['existencia'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="w-50 float-right">
                        <div class="pb-20">
                            <p class="pl-2 bold">Observación:</p>
                        </div>
                    </div>
                    <div class="w-50 float-left py-3">
                        <table class="w-100 pagos_tabla center no-overlap">
                            <thead>
                                <tr class="w-normal capitalize ">
                                    <th>
                                        Número de Lote
                                    </th>
                                    <th>
                                        Existencia Sistema
                                    </th>
                                    <th>
                                        Existencia Física
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articulo['inventario'] as $inventario)
                                    <tr>
                                        <td class="py-1">{{ $inventario['num_lote_inventario'] }}</td>
                                        <td class="py-1">{{ $inventario['existencia'] }}</td>
                                        <td class="py-1"></td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="py-1">Fuera de Lote</td>
                                    <td class="py-1">0</td>
                                    <td class="py-1"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>


    @endforeach

</body>

</html>
