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
    <h1 class="center mt-2">información necesaria para elaborar el certificado médico de defunción
        <div class="mt-2 w-normal">(information worksheet for medical death certificate)</div>
    </h1>
    <table class="w-100 texto-base mt-3 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-34 bold px-2 ">
                Folio del Certificado
                <div class="w-normal">
                    (Certificate Folio)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['folio_certificado'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-34 bold px-2 ">
                nombre completo del fallecido
                <div class="w-normal">
                    (full name of the deceased)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['nombre_afectado'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-34 bold px-2 ">
                sexo
                <div class="w-normal">
                    (gender)
                </div>
            </td>
            <td class="w-75 px-2">
                <div class="opcion">
                    <span
                        class="<?php if (!is_null($datos['generos_id']) && $datos['generos_id']==1){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Hombre/Male
                <div class="opcion ml-3">
                    <span
                        class="<?php if (!is_null($datos['generos_id']) && $datos['generos_id']==2){echo 'display';}else{echo 'hidden';}?>">
                        x
                    </span>
                </div>
                Mujer/Female
            </td>
        </tr>
    </table>


    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                fecha de nacimiento
                <div class="w-normal">
                    (date of birth)
                </div>
            </td>
            <td class="w-35 px-2">
                @if (!is_null($datos['fecha_nacimiento']))
                {{ $datos['fecha_nacimiento_texto'] }}
                @endif
            </td>
            <td class="w-10  bold px-2 ">
                edad
                <div class="w-normal">
                    (age)
                </div>
            </td>
            <td class="w-30 px-2">calcular</td>
        </tr>
    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                lugar de nacimiento
                <div class="w-normal">
                    (place of birth)
                </div>
            </td>
            <td class="w-35 px-2">{{ $datos['lugar_nacimiento'] }}</td>
            <td class="w-15  bold px-2 ">
                nacionalidad
                <div class="w-normal">
                    (nationality)
                </div>
            </td>
            <td class="w-25 px-2">
                @if (!is_null($datos['nacionalidades_id']))
                {{ $datos['nacionalidad']['nacionalidad'] }}
                @endif
            </td>
        </tr>
    </table>


    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-25 bold px-2 ">
                Estado civil
                <div class="w-normal">
                    (marital status)
                </div>
            </td>
            <td class="w-75 px-2">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==1){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Soltero(a)/Single
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==2){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Casado(a)/Married
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==4){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Divorciado(a)/Divorced
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion mt-1">
                                <span
                                    class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==6){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Separado(a)/Separated
                        </td>
                        <td>
                            <div class="opcion mt-1 ">
                                <span
                                    class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==3){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Viudo(a)/Widowed
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==5){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Unión Libre/Civil Union
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion mt-1">
                                <span
                                    class="<?php if (!is_null($datos['estados_civiles_id']) && $datos['estados_civiles_id']==7){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Se ignora/Unknown
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                domicilio actual
                <div class="w-normal">
                    (last known address)
                </div>
            </td>
            <td class="w-75 px-2" colspan="3">{{ $datos['direccion_fallecido'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                escolaridad
                <div class="w-normal">
                    (academic degree)
                </div>
            </td>
            <td class="w-75 px-2" colspan="3">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==1){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Ninguna/None
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==2){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Preescolar/Preschool
                        </td>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==3){echo 'display';}else{echo 'hidden';}?>">
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
                                <span
                                    class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==4){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            secundaria/middle school
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==5){echo 'display';}else{echo 'hidden';}?>">
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
                                <span
                                    class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==6){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            profesional/college
                        </td>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==7){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            posgrado/master
                        </td>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['escolaridades_id']) && $datos['escolaridades_id']==8){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            se ignora/unknown
                        </td>
                    </tr>
                </table>


            </td>
        </tr>
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                Afiliación a
                <div class="w-normal">
                    (AFFILIATION TO)
                </div>
            </td>
            <td class="w-75 px-2" colspan="3">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==1){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            Ninguna/None
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==2){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            imss
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==3){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            pemex
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==7){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            semar
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==5){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            imss prospera
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==6){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            issste
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion mt-1">
                                <span
                                    class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==8){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            sedena
                        </td>
                        <td colspan="2">
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['afiliaciones_id']) && $datos['afiliaciones_id']==9){echo 'display';}else{echo 'hidden';}?>">
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
            </td>
        </tr>
        <tr class="size-13px">
            <td class="w-25  bold px-2 ">
                ocupación
                <div class="w-normal">
                    (occupation)
                </div>
            </td>
            <td class="w-25 px-2">
                {{ $datos['ocupacion'] }}
            </td>
        </tr>
        <tr class="size-13px">
            <td class="w-27  bold px-2 ">
                lugar del fallecimiento
                <div class="w-normal">
                    (place of death)
                </div>
            </td>
            <td class="w-65 px-2" colspan="3">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==1){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            domicilio/Home address
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==2){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            hospital imss
                        </td>
                        <td>
                            <div class="opcion ">
                                <span
                                    class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==3){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            pemex
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==4){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            hospital privado/private hospital
                        </td>
                        <td>
                            <div class="opcion  mt-1">
                                <span
                                    class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==6){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            hospital issste
                        </td>
                        <td>
                            <div class="opcion  mt-1">
                                <span
                                    class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==7){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            hospital semar
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==8){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            hospital sedena
                        </td>
                        <td>
                            <div class="opcion mt-1">
                                <span
                                    class="<?php if (!is_null($datos['sitios_muerte_id']) && $datos['sitios_muerte_id']==9){echo 'display';}else{echo 'hidden';}?>">
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

            </td>
        </tr>
    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        </tr>
        <tr class="size-13px">
            <td class="w-21  bold px-2 ">
                fecha y hora de muerte
                <div class="w-normal">
                    (date and time of death)
                </div>
            </td>
            <td class="w-20 px-2">
                @if (!is_null($datos['fechahora_defuncion']))
                {{ $datos['fecha_muerte_texto'] }}
                @endif
            </td>
            <td class="w-25  bold px-2 ">
                ¿atención médica antes de morir?
                <div class="w-normal">
                    (medical attention before death?)
                </div>
            </td>
            <td class="w-15 px-2">
                <table class="w-100 alineador">
                    <tr>
                        <td>
                            <div class="opcion">
                                <span
                                    class="<?php if (!is_null($datos['atencion_medica_b']) && $datos['atencion_medica_b']==1){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            si/yes
                        </td>
                        <td>
                            <div class="opcion mt-1">
                                <span
                                    class="<?php if (!is_null($datos['atencion_medica_b']) && $datos['atencion_medica_b']==0){echo 'display';}else{echo 'hidden';}?>">
                                    x
                                </span>
                            </div>
                            no
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
                ¿padecía alguna enfermedad?
                <div class="w-normal">
                    (did he/she have any disease?)
                </div>
            </td>
            <td class="w-65 px-2" colspan="3">{{ $datos['enfermedades_padecidas'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
               causa de muerte
                <div class="w-normal">
                    (death cause)
                </div>
            </td>
            <td class="w-65 px-2" colspan="3">{{ $datos['causa_muerte'] }}</td>
        </tr>
    </table>
    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-30  bold px-2 ">
                nombre del informante
                <div class="w-normal">
                    (informant's name)
                </div>
            </td>
            <td class="w-70 px-2" colspan="3">{{ $datos['certificado_informante'] }}</td>
        </tr>
    </table>


    <table class="w-100 texto-base mt-1 datos_tabla uppercase">
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
                parentesco con el fallecido
                <div class="w-normal">
                    (relationship with the deceased)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['certificado_informante_parentesco'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
                teléfono
                <div class="w-normal">
                    (telephone number)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['certificado_informante_telefono'] }}</td>
        </tr>
        <tr class="size-13px">
            <td class="w-35  bold px-2 ">
                médico legista
                <div class="w-normal">
                    (medical examiner)
                </div>
            </td>
            <td class="w-75 px-2">{{ $datos['medico_legista'] }}</td>
        </tr>
    </table>

    <div class="w-100 center hidden">
        <div class="w-50  mr-auto ml-auto">
             <img src="{{ $firmas['informante'] }}" class="firma" >
            <div class="w-90 border-top">
                <div class="pt-1 pb-1"><span class="uppercase  texto-sm"></span></div>
                <span class="uppercase bold texto-sm">firma del informante</span>
                <div class=""><span class="uppercase  texto-sm">(signature)</span></div>
            </div>
        </div>
    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>