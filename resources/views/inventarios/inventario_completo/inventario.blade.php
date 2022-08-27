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
                    <img class="logo logos -mt-6" src="{{public_path(env('LOGOJPG'))}}" alt="">
                </th>
                <th class="w-80">
                    <div class="center uppercase -mt-3">
                        <p class="line-xxs size-20px">{{ $empresa->nombre_comercial }}</p>
                        <p class="line-xxs size-20px">Inventario General de Artículos y Servicios</p>
                        <p class="line-xxs size-12px">
                            {{fechahora_completa()}}
                        </p>
                    </div>
                </th>
                <th class="w-10">
                </th>
            </tr>
        </thead>
    </table>
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
                    Tipo
                </th>
                <th>
                    ($) Precio Compra/Producción
                </th>
                <th>
                    ($) Precio Venta
                </th>
                <th>
                    Existencia Sistema
                </th>
                <th>
                    Estado
                </th>
                <th>
                    Mínimo
                </th>
                <th>
                    Máximo
                </th>
                <th>
                    Inventario
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventario as $articulo)
            <tr>
                <td class="py-1">{{ $articulo['id'] }}</td>
                <td>{{ $articulo['codigo_barras'] }}</td>
                <td>{{ $articulo['descripcion'] }}</td>
                <td>{{ $articulo['tipo_articulo']['tipo'] }}</td>
                <td>$ {{number_format($articulo['precio_compra'],2) }}</td>
                <td>$ {{number_format($articulo['precio_venta'],2) }}</td>
                <td>{{ $articulo['existencia'] }}</td>
                <td>{{ $articulo['estatus_texto'] }}</td>
                <td>{{ $articulo['minimo'] }}</td>
                <td>{{ $articulo['maximo'] }}</td>
                <td>
                    @if ($articulo['estatus_inventario_b']!=1)
                    <span class="text-danger">
                        {{ $articulo['estatus_inventario_texto'] }}
                    </span>
                    @else
                    {{ $articulo['estatus_inventario_texto'] }}
                    @endif

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>