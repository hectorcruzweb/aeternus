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

        /*fin de parrafos*/
    </style>
</head>

<body>
    @include('layouts.estilos')
    <header id="header">
        <section>
            <table>
                <tr>
                    <td style="width:23%;">
                        <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo">
                    </td>
                    <td style="width:53%;">
                        <h1>
                            {{ $empresa->razon_social }}
                        </h1>
                        <p class="datos-header">
                            {{ strtolower($empresa->calle) }} Núm. Ext {{ $empresa->num_ext }}
                        </p>
                        <p class="datos-header">
                            Col. {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
                            {{ $empresa->ciudad }}
                            {{ $empresa->estado }}
                        </p>
                        <p class="datos-header">
                            Tel. {{ $empresa->telefono }}, fax {{ $empresa->fax }}
                        </p>
                    </td>
                    <td style="width:25%;">
                        <div class="numeros-contrato">
                            <div class="control bg-gray">
                                solicitud de servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_solicitud_texto'] }}
                            </p>

                            <div style=""></div>
                            <div class="control bg-gray">
                                Número de convenio
                            </div>
                            <p class="control-valor">
                                {{ $datos['numero_convenio'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <p class="fecha  right">
        {{ $empresa->ciudad }}, {{ $empresa->estado }} a <span
            class="bg-gray bold uppercase texto-sm  pl-2 pr-1">{{ fecha_only(now()) }}</span>.
    </p>
    <div class="contenido parrafo1">
        <p class="texto-base justificar line-base">
            Por medio de la presente se hace constar que el (la) C. <span class="bold uppercase"><span
                    class="texto-sm">{{ $datos['nombre'] }}</span></span>, No. De convenio <span
                class="bold uppercase"><span class="texto-sm">{{ $datos['numero_convenio'] }}</span></span>, finiquitó
            (1) un
            Servicio de <span class="bold uppercase"><span
                    class="texto-sm">{{ $datos['venta_plan']['nombre_original'] }}</span></span> a futuro.
        </p>
        @if(count($datos['venta_plan']['conceptos_originales'])>0)
        <?php
                 $seccion='';
                 for ($id_seccion=1; $id_seccion < 5; $id_seccion++) { 
                    if($id_seccion==1){
                        $seccion="incluye";
                    }elseif($id_seccion==2){
                        $seccion="en caso de inhumación";
                    }elseif($id_seccion==3){
                        $seccion="en caso de cremación";
                    }elseif($id_seccion==4){
                        $seccion="en caso de velación";
                    }
            $mostrar=false;
                foreach ($datos['venta_plan']['conceptos_originales'] as $index => $concepto) {
                   if($concepto['seccion_id']==$id_seccion){
                       $mostrar=true;
                   break;
                   }
                }//en foreach
            
                if($mostrar==true){
            ?>
        <p class="texto-sm justificar line-base uppercase bold">
            {{ $seccion }}
        </p>
        <div class="lista pl-11 -mt-1">
            @foreach($datos['venta_plan']['conceptos_originales'] as $index=>$concepto)
            @if ($concepto['seccion_id']==$id_seccion)
            <p class="texto-base justificar line-base">
                <span class="lowercase bold texto-sm -ml-6">{{ letra_alfabeto($index) }}) </span>
                <span class="ml-2">
                    {{ $concepto['concepto'] }}
                </span>
            </p>
            @endif

            @endforeach
        </div>
        <?php
                }//end if mostrar
                 }//en for
                 ?>

        @else
        <p class="texto-base justificar line-base center uppercase bg-gray bold">
            no se han capturado servicios/artículos para este convenio.
        </p>
        @endif
    </div>
    <div class="contenido parrafo2">
        <p class="texto-base justificar line-base">
            <span class="texto-base justificar line-base center uppercase bold">NOTA:
                {{ $datos['venta_plan']['nota_original'] }}</span>

        </p>

        <p class="texto-base justificar line-base">
            A petición del interesado, se extiende la presente constancia en Mazatlán, Sinaloa,
            <span class="uppercase">{{ fecha_completa() }}</span>.
        </p>
    </div>


    <div class="w-100 center mt-10">
        <div class="w-50 ml-auto mr-auto mt-30">
             <img src="{{ $firmas['gerente'] }}" class="firma">
            <div class="w-90 mr-auto ml-auto border-top">
                <div class="pt-3 pb-1"><span class="uppercase  texto-sm">{{ $empresa->razon_social }}</span></div>
                <span class="uppercase bold texto-sm">"la empresa"</span>
            </div>
        </div>
    </div>


</body>

</html>
<span class="uppercase bold texto-sm"></span>