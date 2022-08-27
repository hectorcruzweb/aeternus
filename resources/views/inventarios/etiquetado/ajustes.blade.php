<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Etiqueta</title>
    <style>
        .barcode div {
            min-height: 40px !important;
        }

        .label {
            min-height: 76.2mm !important;
            max-height: 76.2mm !important;
        }

        .loteno {
            font-size: 16mm !important;
            min-width: 100mm !important;
            max-width: 100mm !important;
        }

        .impresion {
            font-size: 3mm !important;
        }

        .crop {
            word-break: break-all;
        }

        .no-overlap {
            page-break-inside: avoid;
        }

    </style>
</head>

<body>
    @include('layouts.estilos')
    @foreach ($etiquetas as $etiqueta)

        <div class="w-100 border-black-1 radius-3 label no-overlap">
            <div>
                <table class="w-100 mt-4 px-2">
                    <tr>
                        <td class="w-100 center bold uppercase">
                            <div class="">
                                {{ $etiqueta['descripcion'] }}
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="w-100 mt-2 px-2">
                    <tr>
                        <td class="w-100 center">
                            <div class="">
                                Lote #
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-100 center bold border-black-1 mt-2">
                            <div class="loteno crop">
                                {{ $etiqueta['num_lote_inventario'] }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-100 center">
                            <div class="impresion mt-2">
                                Impresión, {{ fechahora_completa() }}.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
</body>

</html>
