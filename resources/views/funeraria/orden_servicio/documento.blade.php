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
    <h1 class="center mt-4 size-22px">ÓRDEN DE SERVICIO</h1>

    <div class="center border-black-1 w-80 ml-auto mr-auto p-1 line-lg size-18px">
        {{ $datos['nombre_afectado'] }}
    </div>


    @if ($datos['embalsamar_b']==1)
    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">servicio de embalsamado</div>
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Médico Responsable</td>
                    <td class="bg-gray bold w-50">Preparador</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    {{ $datos['medico_responsable_embalsamado'] }}
                </td>
                <td class="px-1">
                    {{ $datos['preparador'] }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    @if ($datos['velacion_b']==1)
    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">Velación del cuerpo</div>
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Lugar</td>
                    <td class="bg-gray bold w-50">Dirección</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    @if ($datos['lugares_servicios_id']==1)
                    DOMICILIO PARTICULAR
                    @elseif ($datos['lugares_servicios_id']==2)
                    Funeraria Aeternus | Sala la piedad
                    @elseif ($datos['lugares_servicios_id']==3)
                    Funeraria Aeternus | Sala la misericordia
                    @elseif ($datos['lugares_servicios_id']==4)
                    Funeraria Aeternus | Sala la resurreción
                    @endif
                </td>
                <td class="px-1">
                    {{ $datos['direccion_velacion'] }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    @if ($datos['misa_b']==1)
    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">ceremonia religiosa</div>
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Iglesia</td>
                    <td class="bg-gray bold w-50">Fecha y Hora</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    {{ $datos['iglesia_misa'] }}
                </td>
                <td class="px-1">
                    {{ fechahora($datos['fechahora_misa']) }}
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1 bg-gray bold" colspan="2">
                    Dirección del templo
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1" colspan="2">
                    {{ $datos['direccion_iglesia'] }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    @if ($datos['cremacion_b']==1)
    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">Servicio de Cremación</div>
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Fecha de Cremación</td>
                    <td class="bg-gray bold w-50">Fecha de Entrega de Cenizas</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    {{ fechahora($datos['fechahora_cremacion']) }}
                </td>
                <td class="px-1">
                    {{ fechahora($datos['fechahora_entrega_cenizas']) }}
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1 bg-gray bold" colspan="2">
                    Descripción de la Urna
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1" colspan="2">
                    {{ $datos['descripcion_urna'] }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    @if ($datos['inhumacion_b']==1)
    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">Servicio de Inhumación</div>
        @if ($datos['cementerios_servicio_id']==1)
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Fecha de Inhumación</td>
                    <td class="bg-gray bold w-50">Cementerio</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    {{ fechahora($datos['fechahora_inhumacion']) }}
                </td>
                <td class="px-1">
                    Cementerio Aeternus
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1 bg-gray bold" colspan="2">
                    Ubicación
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1" colspan="2">
                    {{ $datos['terreno']['ubicacion_servicio'] }}
                </td>
            </tr>
        </table>
        @else
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Fecha de Inhumación</td>
                    <td class="bg-gray bold w-50">Cementerio</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    {{ fechahora($datos['fechahora_inhumacion']) }}
                </td>
                <td class="px-1">
                    @if ($datos['cementerios_servicio_id']==2)
                    OTRO CEMENTERIO
                    @else
                    FOSA COMÚN
                    @endif
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1 bg-gray bold" colspan="2">
                    Ubicación
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1" colspan="2">
                    {{ $datos['nota_ubicacion'] }}
                </td>
            </tr>
        </table>
        @endif
    </div>
    @endif

    @if ($datos['traslado_b']==1)
    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">Servicio de Traslado</div>
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Fecha de Traslado</td>
                    <td class="bg-gray bold w-50">Destino</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    {{ fechahora($datos['fechahora_traslado']) }}
                </td>
                <td class="px-1">
                    {{ $datos['destino_traslado'] }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    @if ($datos['aseguradora_b']==1)
    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">Servicio con Aseguradora</div>
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Aseguradora</td>
                    <td class="bg-gray bold w-50">Número de Convenio</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    {{ $datos['aseguradora'] }}
                </td>
                <td class="px-1">
                    {{ $datos['numero_convenio_aseguradora'] }}
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1 bg-gray bold" colspan="2">
                    Teléfono
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1" colspan="2">
                    {{ $datos['telefono_aseguradora'] }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    @if ($datos['custodia_b']==1)
    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase pb-2">Servicio con Aseguradora</div>
        <table class="w-100 texto-base datos_tabla uppercase">
            <thead>
                <tr class="center">
                    <td class="bg-gray bold w-50">Responsable de Custodia</td>
                    <td class="bg-gray bold w-50">Folio de Custodia</td>
                </tr>
            </thead>
            <tr class="size-15px center">
                <td class="py-1 px-1">
                    {{ $datos['responsable_custodia'] }}
                </td>
                <td class="px-1">
                    {{ $datos['folio_custodia'] }}
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1 bg-gray bold" colspan="2">
                    Folio de Liberación
                </td>
            </tr>
            <tr class="size-15px center">
                <td class="py-1 px-1" colspan="2">
                    {{ $datos['folio_liberacion'] }}
                </td>
            </tr>
        </table>
    </div>
    @endif


    <div class="contenido w-100 mt-4">
        <div class="center size-18px uppercase bg-gray bold">NOTA/OBSERVACIONES</div>
        <p>
            @if ($datos['nota_servicio']!=null)
            {{ $datos['nota_servicio'] }}
            @else
            N/A
            @endif
        </p>
    </div>

</body>

</html>
<span class="uppercase bold texto-sm"></span>