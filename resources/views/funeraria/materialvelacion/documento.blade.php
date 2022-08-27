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

        .tabla_dato {
            border-collapse: collapse;
        }

        .tabla_dato .border-bottom {
            border-bottom: 1px solid #ddd;
        }

        .datos_tabla {
            border-collapse: collapse;
        }

        .datos_tabla tr th,
        .datos_tabla td {
            border: 1px solid #ddd;
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
                                Número Servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['servicio_id'] }}
                            </p>

                            <div style=""></div>
                            <div class="control bg-gray">
                                Tipo Servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['tipo_solicitud_texto'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <p class="fecha  right">
        <span class="bg-gray bold uppercase texto-sm  pl-2 pr-1">{{ $empresa->ciudad }}, {{ $empresa->estado }} a
            {{ fecha_only(now()) }}.</span>
    </p>
    <h1 class="center mt-5 size-22px">ENTREGA DE EQUIPO DE VELACIÓN</h1>

    <div class="contenido w-100 mt-5">
        <p class="justificar line-lg size-18px">
            Yo, @if (isset($datos['operacion']['cliente']))
            {{ $datos['operacion']['cliente']['nombre']}}
            @else
            N/A
            @endif. Por medio de la presente, certifico que recibí de Aeternus Funerales el siguiente equipo de
            velación:
        </p>

        <table class="w-100 texto-base mt-5 datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold">Descripción</td>
                    <td class="bg-gray bold">Nota</td>
                    <td class="bg-gray bold">Cantidad</td>
                </tr>
            </thead>
            @if (count($datos['materialrentado'] )>0)
                @foreach ($datos['materialrentado'] as $material)
                    <tr class="size-15px center">
                        <td class="w-35 py-1 px-2">{{$material['descripcion']}}</td>
                        <td class="w-50 px-2">{{ $material['nota'] }}</td>
                        <td class="w-15 px-2">{{ $material['cantidad'] }}</td>
                    </tr>
                @endforeach
            @else
            <tr class="size-15px center">
                <td class="w-100 py-1 px-2" colspan="3">No se ha agregado material de velación a este contrato</td>
               
            </tr>
            @endif
            
        </table>


        <div class="w-100 center">
            <div class="w-50 ml-auto mr-auto mt-5">
                  <img src="{{ $firmas['cliente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class=" pb-1"><span class="texto-base bold">
                            @if (isset($datos['operacion']['cliente']))
                            {{ $datos['operacion']['cliente']['nombre']}}
                            @else
                            N/A
                            @endif</span></div>
                </div>
                <span class="texto-base">Firma</span>
            </div>

        </div>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>