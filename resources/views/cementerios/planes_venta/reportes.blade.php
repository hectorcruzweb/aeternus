<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
        rel="stylesheet">
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
                    <img class="logo logos -mt-6" src="{{asset('images/aeternus/LogoEmp.jpg')}}" alt="">
                </th>
                <th class="w-80">
                    <div class="center uppercase -mt-3">
                        <p class="line-xxs size-20px">{{ $empresa->nombre_comercial }}</p>
                        <p class="line-xxs size-20px">{{ __('cementerio/lista_precios.titulo_reporte') }}</p>
                    </div>
                </th>
                <th class="w-10">
                </th>
            </tr>
        </thead>
    </table>
    @foreach ($financiamientos as $financiamiento)
    @if ($financiamiento['id']==$id_tipo_propiedad || trim($id_tipo_propiedad)=='')
    <div class="propiedad mt-8 center uppercase bg-gray-light py-1 semibold size-16px color-semidark">
        {{ __('cementerio/lista_precios.tipo_de_propiedad') }} {{ $financiamiento['tipo'] }},
        {{ __('cementerio/lista_precios.capacidad') }} {{ $financiamiento['capacidad'] }}
        {{ __('cementerio/lista_precios.personas') }}
    </div>
    <table class="w-100 tablas-collapsed center">
        <thead>
            <tr class="bg-table-header text-white w-normal capitalize">
                <th>
                    #
                </th>
                <th>
                    {{ __('cementerio/lista_precios.pago_incial') }} ($)
                </th>
                <th>
                    {{ __('cementerio/lista_precios.costo_neto') }} ($)
                </th>

                <th>
                    {{ __('cementerio/lista_precios.pago_mensual') }} ($)
                </th>

                <th>
                    {{ __('cementerio/lista_precios.financiamiento') }}
                </th>
                <th>
                    {{ __('cementerio/lista_precios.descripcion') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($financiamiento['precios'] as $key => $precios)
            @if ($precios['status']!=0)
            <tr>
                <td class="py-1">{{ $key+1 }}</td>
                <td>$ {{number_format($precios['pago_inicial'],2) }}</td>
                <td>$ {{number_format($precios['costo_neto'],2) }}</td>
                <td>$ {{number_format($precios['pago_mensual'],2) }}</td>
                <td>
                    @if (App::isLocale('es'))
                    {{ $precios['tipo_financiamiento'] }}
                    @else
                    {{ $precios['tipo_financiamiento_ingles'] }}
                    @endif

                </td>
                <td>
                    @if (App::isLocale('es'))
                    {{ $precios['descripcion'] }}
                    @else
                    {{ $precios['descripcion_ingles'] }}
                    @endif

                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif
    @endforeach


</body>

</html>
