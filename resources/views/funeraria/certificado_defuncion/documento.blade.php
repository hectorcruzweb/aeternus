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
            line-height: 0.7em !important;
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

        /*
        estilos de la tabla de datos
        **/
        .datos_tabla {
            border-collapse: collapse;
        }

        .datos_tabla tr th,
        .datos_tabla td {
            border: 1px solid #ddd;
        }

        .alineador tr th,
        .alineador td {
            border: none !important;
        }

        .opcion {
            width: 20px !important;
            height: 20px !important;
            display: inline-block;
            text-align: center !important;
            border: 1px solid #ddd !important;
            font-weight: bold !important;
        }

        .display {
            visibility: visible !important;
        }

        .hidden {
            visibility: hidden !important;
        }
    </style>
</head>

<body class="certificado-defuncion-documento">
    @include('layouts.estilos')
    <style>
        /* Ficha bilingüe en carta vertical. Estas reglas se cargan después de
           los estilos comunes para compactar únicamente este documento.
           Se conservan los campos y todas las opciones; no se recorta texto
           ni se fija una altura que pueda ocultar información extensa. */
        body.certificado-defuncion-documento {
            font-family: Arial, sans-serif !important;
            font-size: 12px;
            margin: 0;
        }
        .certificado-defuncion-documento .datos_tabla .size-13px {
            font-size: 12px !important;
            line-height: 1.2;
        }
        .certificado-defuncion-documento .datos_tabla td {
            padding-top: 2px;
            padding-bottom: 2px;
            vertical-align: middle;
        }
        .certificado-defuncion-documento .datos_tabla .opcion {
            width: 14px !important;
            height: 14px !important;
            line-height: 14px;
            vertical-align: middle;
        }
        .certificado-defuncion-documento .mt-1 { margin-top: 2px !important; }
        .certificado-defuncion-documento .mt-2 { margin-top: 6px !important; }
        .certificado-defuncion-documento .mt-3 { margin-top: 8px !important; }
        .certificado-defuncion-documento .mb-3 { margin-bottom: 3px !important; }
        .certificado-defuncion-documento .datos_tabla tr { page-break-inside: avoid; }
        /* La firma ya estaba invisible, pero visibility:hidden reservaba su
           altura y originaba una hoja vacía. No forma parte del contenido
           visible del formato original; ahora tampoco ocupa espacio. */
        .certificado-defuncion-documento .firma-informante-no-visible { display: none; }
    </style>
    {{-- Hoja única: el visor y Qt ajustan la escala del contenido completo.
         No se trunca ningún valor; se desactivan los saltos internos de la tabla. --}}
    <div class="certificado-hoja-unica" data-ajuste-una-pagina><div data-contenido-ajustable>
    <header id="header">
        <section>
            <table>
                <tr>
                    <td style="width:25%;">
                        <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo">
                    </td>
                    <td style="width:48%;">
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
    <h1 class="center mt-2">información necesaria para elaborar el certificado médico de defunción
        <div class="mt-2 w-normal">(information worksheet for medical death certificate)</div>
    </h1>
    
    {{-- Ficha de dos columnas compatible con el motor Qt de wkhtmltopdf.
         Se reutilizan literalmente los campos, condiciones y opciones del
         formato anterior. Los valores extensos pueden crecer sin recorte. --}}
    <style>
    body.certificado-defuncion-documento { font-size:14px; }
    .ficha-columnas { width:100%; table-layout:fixed; border-collapse:separate; border-spacing:12px 10px; }
    .ficha-columna { width:50%; vertical-align:top; padding:0; border:1px solid #d5d8da; }
    .ficha-columna + .ficha-columna { padding:0; }
    .ficha-bloque { border:0; padding:12px; margin:0; box-sizing:border-box; page-break-inside:avoid; }
    .ficha-bloque h2 { font: bold 15px Arial,sans-serif; color:#79621d; background:#f5f3ed; margin:-12px -12px 8px; padding:9px 12px; border-bottom:1px solid #ded9ca; }
    .ficha-bloque h2 small { display:block; font-size:10px; font-weight:normal; color:#737373; margin-top:3px; }
    /* Las filas de tabla reparten la altura disponible incluso en Qt antiguo. */
    /* Reserva para datos extensos: se reduce el espacio vacío de las fichas,
       manteniendo las dos columnas, las opciones y la tipografía legible. */
    .ficha-campos { display:table; width:100%; border-collapse:collapse; }
    .ficha-campo { display:table-row; }
    .ficha-contenido { display:table-cell; vertical-align:middle; border-bottom:1px solid #e8e8e8; padding:5px 0; }
    .ficha-campo:last-child .ficha-contenido { border-bottom:0; }
    .ficha-identidad .ficha-campos, .ficha-antecedentes .ficha-campos { height:490px; }
    .ficha-contacto .ficha-campos, .ficha-defuncion .ficha-campos { height:430px; }
    .ficha-campo:last-child { border-bottom:0; }
    /* Formato visual solamente: mayúscula inicial en etiquetas y valores.
       No se altera el contenido guardado ni las condiciones de las opciones. */
    .ficha-etiqueta, .ficha-valor { text-transform:capitalize; }
    .ficha-etiqueta { font-size:12px; font-weight:bold; color:#4b5156; }
    .ficha-etiqueta .w-normal { display:inline; font-size:10px; color:#777; }
    /* Valores de BD: mismo máximo que las opciones de afiliación (SEMAR/SEDENA). */
    .ficha-valor { font-size:10px; line-height:1.35; min-height:18px; padding-top:3px; overflow-wrap:break-word; }
    .ficha-valor table { width:100%; table-layout:fixed; border-collapse:collapse; font-size:10px; }
    .ficha-valor table td { vertical-align:top; padding:4px 1px; }
    .certificado-defuncion-documento .ficha-valor .opcion { width:11px!important; height:11px!important; line-height:11px; font-size:10px; vertical-align:middle; }
    /* Alturas mínimas parejas: ocupan el área útil de carta sin estirar la
       tipografía. Si un dato crece, su ficha aumenta en vez de truncarlo. */
    .ficha-identidad, .ficha-antecedentes { min-height:490px; }
    .ficha-contacto, .ficha-defuncion { min-height:490px; }
    /* Chrome necesita su propia compensación de escala. Qt no interpreta
       @supports y conserva las medidas del PDF del servidor. Las celdas
       comparten la altura, mientras el contenido permanece libre de crecer. */
    @supports (display: grid) {
        html body.certificado-defuncion-documento .ficha-bloque { margin-top:0 !important; }
        html body.certificado-defuncion-documento .ficha-contacto,
        html body.certificado-defuncion-documento .ficha-defuncion { min-height:370px !important; }
        html body.certificado-defuncion-documento .ficha-contacto .ficha-campos,
        html body.certificado-defuncion-documento .ficha-defuncion .ficha-campos { height:310px !important; }
    }
    </style>
{{-- Cada par comparte fila y bordes en las celdas: aunque un texto
crezca, ambos contenedores conservan el mismo inicio y fin sin recortarse. --}}
<table class="ficha-columnas"><tr><td class="ficha-columna"><section class="ficha-bloque ficha-identidad"><h2>Datos de identificación del fallecido<small>Personal information</small></h2><div class="ficha-campos"><div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                Folio del Certificado
                <div class="w-normal">
                    (Certificate Folio)
                </div>
            </div><div class="ficha-valor">{{ $datos['folio_certificado'] }}</div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                nombre completo del fallecido
                <div class="w-normal">
                    (full name of the deceased)
                </div>
            </div><div class="ficha-valor">{{ $datos['nombre_afectado'] }}</div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                sexo
                <div class="w-normal">
                    (gender)
                </div>
            </div><div class="ficha-valor">
                <div class="opcion">
                    <span class="<?php if (!is_null($datos['generos_id']) && $datos['generos_id'] == 1) {
                        echo 'display';
                    } else {
                        echo 'hidden';
                    } ?>">
                        x
                    </span>
                </div>
                Hombre/Male
                <div class="opcion ml-3">
                    <span class="<?php if (!is_null($datos['generos_id']) && $datos['generos_id'] == 2) {
                        echo 'display';
                    } else {
                        echo 'hidden';
                    } ?>">
                        x
                    </span>
                </div>
                Mujer/Female
            </div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                fecha de nacimiento
                <div class="w-normal">
                    (date of birth)
                </div>
            </div><div class="ficha-valor">
                @if (!is_null($datos['fecha_nacimiento']))
                    {{ $datos['fecha_nacimiento_texto'] }}
                @endif
            </div></div></div><div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                edad
                <div class="w-normal">
                    (age)
                </div>
            </div><div class="ficha-valor">calcular</div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                lugar de nacimiento
                <div class="w-normal">
                    (place of birth)
                </div>
            </div><div class="ficha-valor">{{ $datos['lugar_nacimiento'] }}</div></div></div><div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                nacionalidad
                <div class="w-normal">
                    (nationality)
                </div>
            </div><div class="ficha-valor">
                @if (!is_null($datos['nacionalidades_id']))
                    {{ $datos['nacionalidad']['nacionalidad'] }}
                @endif
            </div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                Estado civil
                <div class="w-normal">
                    (marital status)
                </div>
            </div><div class="ficha-valor">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id'] == 1) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Soltero(a)/Single
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id'] == 2) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Casado(a)/Married
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id'] == 4) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Divorciado(a)/Divorced
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion mt-1">
                                <span class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id'] == 6) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Separado(a)/Separated
                        </td>
                        <td>
                            <div class="opcion mt-1 ">
                                <span class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id'] == 3) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Viudo(a)/Widowed
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id'] == 5) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Unión Libre/Civil Union
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion mt-1">
                                <span class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id'] == 7) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Se ignora/Unknown
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                domicilio actual
                <div class="w-normal">
                    (last known address)
                </div>
            </div><div class="ficha-valor">{{ $datos['direccion_fallecido'] }}</div></div></div></div></section></td><td class="ficha-columna"><section class="ficha-bloque ficha-antecedentes"><h2>Antecedentes<small>Background information</small></h2><div class="ficha-campos"><div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                escolaridad
                <div class="w-normal">
                    (academic degree)
                </div>
            </div><div class="ficha-valor">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id'] == 1) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Ninguna/None
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id'] == 2) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Preescolar/Preschool
                        </td>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id'] == 3) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Primaria/elementary
                        </td>
                    </tr>
                </table>
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion mt-1">
                                <span class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id'] == 4) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            secundaria/middle school
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id'] == 5) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            preparatoria/high school
                        </td>
                    </tr>
                </table>
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion mt-1">
                                <span class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id'] == 6) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            profesional/college
                        </td>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id'] == 7) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            posgrado/master
                        </td>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id'] == 8) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            se ignora/unknown
                        </td>
                    </tr>
                </table>


            </div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                Afiliación a
                <div class="w-normal">
                    (AFFILIATION TO)
                </div>
            </div><div class="ficha-valor">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id'] == 1) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Ninguna/None
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id'] == 2) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            IMSS
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id'] == 3) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            PEMEX
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id'] == 7) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            SEMAR
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id'] == 5) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            IMSS Prospera
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id'] == 6) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            ISSSTE
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion mt-1">
                                <span class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id'] == 8) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            SEDENA
                        </td>
                        <td colspan="2">
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id'] == 9) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            otros
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="mb-3">
                                <span class="bold">
                                    otra afiliación/affiliation to:
                                </span> {{ $datos['afiliacion_nota'] }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                ocupación
                <div class="w-normal">
                    (occupation)
                </div>
            </div><div class="ficha-valor">
                {{ $datos['ocupacion'] }}
            </div></div></div></div></section></td></tr><tr><td class="ficha-columna"><section class="ficha-bloque ficha-contacto"><h2>Informante y certificación<small>Informant and medical examiner</small></h2><div class="ficha-campos"><div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                nombre del informante
                <div class="w-normal">
                    (informant's name)
                </div>
            </div><div class="ficha-valor">{{ $datos['certificado_informante'] }}</div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                parentesco con el fallecido
                <div class="w-normal">
                    (relationship with the deceased)
                </div>
            </div><div class="ficha-valor">{{ $datos['certificado_informante_parentesco'] }}</div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                teléfono
                <div class="w-normal">
                    (telephone number)
                </div>
            </div><div class="ficha-valor">{{ $datos['certificado_informante_telefono'] }}</div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                médico que certifica
                <div class="w-normal">
                    (medical examiner)
                </div>
            </div><div class="ficha-valor">{{ $datos['medico_legista'] }}</div></div></div></div></section></td><td class="ficha-columna"><section class="ficha-bloque ficha-defuncion"><h2>Datos de la defunción<small>Death information</small></h2><div class="ficha-campos"><div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                lugar del fallecimiento
                <div class="w-normal">
                    (place of death)
                </div>
            </div><div class="ficha-valor">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id'] == 1) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            domicilio/Home address
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id'] == 2) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Hospital IMSS
                        </td>
                        <td>
                            <div class="opcion ">
                                <span class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id'] == 3) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            PEMEX
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id'] == 4) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            hospital privado/private hospital
                        </td>
                        <td>
                            <div class="opcion  mt-1">
                                <span class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id'] == 6) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Hospital ISSSTE
                        </td>
                        <td>
                            <div class="opcion  mt-1">
                                <span class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id'] == 7) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Hospital SEMAR
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id'] == 8) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            Hospital SEDENA
                        </td>
                        <td>
                            <div class="opcion mt-1">
                                <span class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id'] == 9) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            otro/other
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="mb-3">
                                <span class="bold">
                                    dirección/address:
                                </span> {{ $datos['lugar_muerte'] }}
                            </div>
                        </td>
                    </tr>
                </table>

            </div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                fecha y hora de muerte
                <div class="w-normal">
                    (date and time of death)
                </div>
            </div><div class="ficha-valor">
                @if (!is_null($datos['fechahora_defuncion']))
                    {{ $datos['fecha_muerte_texto'] }}
                @endif
            </div></div></div><div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                ¿atención médica antes de morir?
                <div class="w-normal">
                    (medical attention before death?)
                </div>
            </div><div class="ficha-valor">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span class="<?php if (!is_null($datos['atencion_medica_b']) && $datos['atencion_medica_b'] == 1) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            si/yes
                        </td>
                        <td>
                            <div class="opcion mt-1">
                                <span class="<?php if (!is_null($datos['atencion_medica_b']) && $datos['atencion_medica_b'] == 0) {
                                    echo 'display';
                                } else {
                                    echo 'hidden';
                                } ?>">
                                    x
                                </span>
                            </div>
                            no
                        </td>
                    </tr>
                </table>
            </div></div></div>
<div class="ficha-campo"><div class="ficha-contenido"><div class="ficha-etiqueta">
                causa de muerte
                <div class="w-normal">
                    (death cause)
                </div>
            </div><div class="ficha-valor">{{ $datos['causa_muerte'] }}</div></div></div></div></section></td></tr></table></div></div>
<style>
html,body.certificado-defuncion-documento{min-height:0!important}
.certificado-hoja-unica{position:relative;width:190mm;height:268mm;overflow:hidden}
.certificado-hoja-unica>[data-contenido-ajustable]{position:absolute;left:0;top:0;width:100%;transform-origin:top left}
.certificado-hoja-unica *{page-break-before:auto!important;page-break-after:auto!important;page-break-inside:auto!important;break-inside:auto!important}
</style>
<script>(function(){
 function ajustar(){
  var hoja=document.querySelector('[data-ajuste-una-pagina]');
  if(!hoja)return;
  var contenido=hoja.querySelector('[data-contenido-ajustable]');
  contenido.style.webkitTransform='none';contenido.style.transform='none';contenido.style.zoom=1;
  // Buscar la mayor escala que cabe. Ampliar el ancho interno compensa la
  // transformación, mantiene el ancho visible y redistribuye los párrafos.
  var bajo=0.000001,alto=1,escala=bajo;
  for(var i=0;i<22;i++){
   var prueba=(bajo+alto)/2;
   contenido.style.width=(hoja.clientWidth/prueba)+'px';
   var cabe=Math.max(contenido.scrollHeight,contenido.offsetHeight)*prueba<=hoja.clientHeight-4;
   if(cabe){escala=prueba;bajo=prueba;}else{alto=prueba;}
  }
  // Reserva adicional para redondeos de filas y bordes del motor Qt.
  escala=escala*0.94;
  contenido.style.width=(hoja.clientWidth/escala)+'px';
  // Zoom participa en la paginación de Qt; transform dejaba saltos internos
  // calculados a tamaño natural y podía empujar las notas fuera del área.
  contenido.style.zoom=escala;
 }
 if(document.readyState==='complete')ajustar();
 else window.addEventListener('load',ajustar);
 window.addEventListener('beforeprint',ajustar);
})();
</script>
<style>
/* Encabezado distribuido y espacio real entre líneas, sin superponer el título. */
.certificado-defuncion-documento #header table{table-layout:fixed}
.certificado-defuncion-documento #header td{vertical-align:middle;padding:0 10px}
.certificado-defuncion-documento #header td:first-child{padding-left:0}
.certificado-defuncion-documento #header td:last-child{padding-right:0;width:27%!important}
.certificado-defuncion-documento #header h1{line-height:1.45!important;margin:0 0 4px!important}
.certificado-defuncion-documento .datos-header{line-height:1.5!important;margin:2px 0!important;font-size:11px}
.certificado-defuncion-documento .control{line-height:1.5!important;padding:3px 0}
.certificado-defuncion-documento .control-valor{line-height:1.5!important;margin:5px 0!important;font-size:11px}
.certificado-defuncion-documento [data-contenido-ajustable]>h1{line-height:1.4!important;margin:12px 0 8px!important;font-size:13px}
.certificado-defuncion-documento [data-contenido-ajustable]>h1>div{margin-top:2px!important}
</style></body></html>
