<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <style>
        #header,
        #header section table {
            width: 100% !important;
            padding-top: 0px;
        }

        #header section table {
            border-collapse: collapse !important;
        }



        .logo {
            max-width: 100% !important;
        }


        h1 {
            font-size: 1em;
            line-height: .8em !important;
            text-transform: uppercase;
            text-align: center;
        }

        .datos-header {
            text-align: center !important;
            font-size: .9em;
            line-height: 1em !important;
            text-transform: uppercase !important;
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

        /*parrafos**/
        .contenido {
            padding: 10px 0 0 0 !important;
            margin: 0 !important;
        }


        .santander {
            color: #D31413 !important;
        }

        /*fin de parrafos*/
    </style>
</head>

<body>
    @include('layouts.estilos')
    <header id="header">
        <section>
            <table class="w-100">
                <tr>
                    <td class="w-100">
                        <img src="{{ public_path(env('LOGOJPG')) }}" class="img-center w-40">
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <h1 class="size-20px uppercase bold pt-2">
        @if (App::isLocale('es'))
        {{ $datos['plan'] }}
        @else
        {{ $datos['plan_ingles'] }}
        @endif
    </h1>
    <?php
    $alfabeto=[
    "A",
    "B",
    "C",
    "D",
    "E",
    "F",
    "G",
    "H",
    "I",
    "J",
    "K",
    "L",
    "M",
    "N",
    "Ñ",
    "O",
    "P",
    "Q",
    "R",
    "S",
    "T",
    "U",
    "V",
    "X",
    "Y",
    "Z",
    "AA",
    "AB",
    "AC",
    "AD",
    "AE",
    "AF",
    "AG",
    "AH",
    "AI",
    "AJ",
    "AK",
    "AL",
    "AM",
    "AN",
    "AÑ",
    "AO",
    "AP",
    "AQ",
    "AR",
    "AS",
    "AT",
    "AU",
    "AV",
    "AX",
    "AY",
    "AZ"
];
    foreach ($datos['secciones'] as $seccion_id => $seccion) {
        if(count($seccion['conceptos'])>0){
      ?>
    <div class="contenido">
        <h1 class="texto-lg bold left">
            @if (App::isLocale('es'))
            @if ($seccion['seccion']!='incluye')
            En caso de
            @endif {{ $seccion['seccion'] }}:
            @else
            @if ($seccion['seccion']!='incluye')
            in case of
            @endif{{ $seccion['seccion_ingles'] }}:
            @endif
        </h1>
        <div class="lista pl-11 -mt-1">
            @foreach ($seccion['conceptos'] as $key_concepto=>$concepto)
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-lg -ml-6">{{ $alfabeto[$key_concepto] }}) </span>
                <span class="ml-2 texto-lg line-base">
                    @if (App::isLocale('es'))
                    {{ $concepto['concepto'] }}
                    @else
                    {{ $concepto['concepto_ingles'] }}
                    @endif
                </span>
            </p>
            @endforeach
        </div>
    </div>
    <?php
        }//fin end if conceptos >0
    }//end foreach
    ?>

    <?php
                    if(count($datos['precios'])>0) {
                ?>
    <h1 class="size-20px uppercase bold pt-2 left">
        @if (App::isLocale('es'))
        Precios:
        @else
        Prices:
        @endif
    </h1>
    <div class="contenido">
        <table class="w-100 center pagos_tabla">
            <thead>
                <tr class="bg-table-header text-white w-normal">
                    <th>
                        <span class="bold uppercase texto-sm">{{ __('funeraria/pdf_plan.financiamiento') }} ($)</span>
                    </th>
                    <th>
                        <span class="bold uppercase texto-sm">{{ __('funeraria/pdf_plan.costo_neto') }} ($)</span>
                    </th>
                    <th>
                        <span class="bold uppercase texto-sm">{{ __('funeraria/pdf_plan.enganche') }} ($)</span>
                    </th>
                    <th>
                        <span class="bold uppercase texto-sm">{{ __('funeraria/pdf_plan.saldo_restante') }} ($)</span>
                    </th>
                    <th>
                        <span class="bold uppercase texto-sm">{{ __('funeraria/pdf_plan.abono_mensual') }} ($)</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($datos['precios'] as $precio_key => $precio) {
                ?>
                <tr>
                    <td class="py-1">
                        @if (App::isLocale('es'))
                        {{ $precio['tipo_financiamiento'] }}
                        @else
                        {{ $precio['tipo_financiamiento_ingles'] }}
                        @endif
                    </td>
                    <td>$ {{number_format($precio['costo_neto'],2) }}</td>
                    <td>$ {{number_format($precio['pago_inicial'],2) }}</td>
                    <td>$ {{number_format($precio['costo_neto']-$precio['pago_inicial'],2) }}</td>
                    <td>$ {{number_format($precio['pago_mensual'],2) }}</td>
                </tr>
                <?php
                }//end foreach
                ?>
            </tbody>
        </table>
    </div>
    <?php
                    }//end if precios count > 0
                    ?>




</body>

</html>