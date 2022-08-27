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
            padding: 0px 0 0 0 !important;
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
        {{ $empresa['nombre_comercial'] }}
    </h1>


    <h2 class="size-17px uppercase w-normal center">
        {{ __('funeraria/pdf_plan.planes_funerarios') }}
    </h2>
    <?php
    foreach ($datos as $plan_id => $plan) {
        if($plan['status']==1){
        
        if(count($plan['precios'])>0) {
    ?>
    <h3 class="size-15px uppercase bold center">
        @if (App::isLocale('es'))
        {{ $plan['plan'] }}
        @else
        {{ $plan['plan_ingles'] }}
        @endif
    </h3>
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
                    foreach ($plan['precios'] as $precio_key => $precio) {
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
    }//end if status==1
        }
        }//end foreach
        ?>
    <div class="contenido">
        <h4 class="uppercase">
            {{ __('funeraria/pdf_plan.nota') }}:
        </h4>
        <p class="parrafo1">
            ** {{ __('funeraria/pdf_plan.parrafo1') }}
        </p>
        <p class="parrafo1">
            ** {{ __('funeraria/pdf_plan.parrafo2') }}
        </p>
        <p class="parrafo1">
            ** {{ __('funeraria/pdf_plan.parrafo3') }}
        </p>
        <p class="parrafo1">
            ** {{ __('funeraria/pdf_plan.parrafo4') }}
        </p>
        <!--agregando la parte de publicidad-->
        <div class="w-100 pb-3">
            <div class="w-40 float-left">
                <img width="50" src="{{ base_path('resources/assets/images/callus.png') }}"
                    class="img-block ml-auto mr-5">
            </div>
            <div class="w-60 float-right">
                <p class="size-20px capitalize bold mt-1">
                    {{ __('funeraria/pdf_plan.comunicate') }}
                </p>
            </div>
        </div>
    </div>

    <div class="w-100 pt-8">
        <h1 class="bold size-23px">
            {{ $empresa->telefono }} ó {{ $empresa->telefono }}
        </h1>
        <p class="capitalize center mt-4 size-18px">
            {{ $empresa->calle }} No. {{ $empresa->num_ext }}, Col. {{ $empresa->colonia }}
        </p>
    </div>
    @if ($empresa->facebook!='')
    <div class="w-100 pb-3">
        <div class="w-40 float-left">
            <img width="60" src="{{ base_path('resources/assets/images/facebook.png') }}" class="img-block ml-auto">
        </div>
        <div class="w-60 float-right">
            <p class="size-18px capitalize bold mt-3 ml-2">
                {{ $empresa->facebook }}
            </p>
        </div>
    </div>
    @endif


</body>

</html>