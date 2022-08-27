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


        .banco-logo {
            display: block;
        }

        .logo {
            display: block;
            margin-right: auto;
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

        .table-modulo,
        .table-modulo tr,
        .table-modulo th,
        .table-modulo td {
            border-collapse: collapse;
            border: 1px solid black;
        }

        .tabla-cuadriplex,
        .tabla-cuadriplex tr,
        .tabla-cuadriplex th,
        .tabla-cuadriplex td {
            border-collapse: collapse;
            border: 1px solid black;
        }

        .table-modulo .titular td,
        .table-modulo .titular tr,
        .table-modulo .titular th {
            border-collapse: collapse;
            border: none;
        }

        .keep-together {
            page-break-inside: avoid;
        }

        .break-before {
            page-break-before: always;
        }

        .break-after {
            page-break-after: always;
        }

        /*
        So I can force:
        That a particular container content is not spread over two pages (if it fits one page). When using the keep-together class a page break is created before the container if necessary.
        That a page break is forced before a particular container.
        That a page break is forced after a particular container.
        */
        .tabla-cuadriplex {
            height: 400px !important;
        }

        .vendida {
            text-decoration: line-through;
            color: #EA5455;
        }

        .venta-en-terraza {
            padding: 7px;
            border: 1px solid #000;
            margin-top: 7px;
        }

    </style>
</head>

<body>
    @include('layouts.estilos')

    <div class="pagos relative">
        <table class="w-100">
            <thead>
                <tr>
                    <th class="w-25">
                        <img class="logo logos -mt-6" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                    </th>
                    <th class="w-40">

                    </th>
                    <th class="w-35">
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                Descripción
                            </div>
                            <p class="control-valor">
                                {{ $cementerio['filtracion']['nombre_reporte'] }}
                            </p>
                            <div class="control bg-header size-13px">
                                Fecha de reporte
                            </div>
                            <p class="control-valor">
                                {{ fecha_abr(today()) }}
                            </p>
                        </div>
                    </th>
                </tr>
            </thead>
        </table>
        <div class="w-100">
            <table class="w-100">
                <tr>
                    <td class="w-50">

                    </td>
                    <td class="w-50">

                    </td>
                </tr>
            </table>
        </div>
        <div class="py-3 ">
            <span class="uppercase bold size-15px">Desglose del cementerio:</span>
        </div>
        @include('cementerios.cementerio_mapa.estado_general',['datos'=>$cementerio['cementerio'],'filtracion'=>$cementerio['filtracion']])
    </div>
</body>

</html>
