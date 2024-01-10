<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <style>
        .logos {
            min-width: 150px;
            max-width: 150px;
        }

        .logo {
            display: block;
            padding-top: 0px;
        }

        .line {
            border-bottom: 1px solid #000;
            width: 100%;
            padding: 10px;
        }

        .registros {
            border-collapse: collapse;
        }

        .registros .tr_line {
            border-top: 1px solid;
        }

        .registros td {
            max-width: 50px;
            width: auto;
            min-width: 10px;
            text-align: center !important;
        }

        .page-break {
            page-break-inside: avoid;
        }

        .page-break-empleado {
            page-break-before: always;
        }


        .bg-indicadores {
            background-color: #dee2e6;
        }
    </style>
</head>

<body>
    @include('layouts.estilos')
    @foreach ($datos['tarjetas'] as $empleado)
        <table class="w-100 line page-break-empleado">
            <tr>
                <td class=""><img class="logo logos -mt-4" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                </td>
                <td class="right">
                    <div class="">
                        <span class="uppercase size-14px bold">{{ $fecha_del_reporte }}</span>
                    </div>
                </td>
            </tr>
        </table>
        <div class="w-100 center mt-2 size-16px bold">
            # {{ $empleado['id'] }}. {{ $empleado['empleado'] }}
        </div>
        <table class="w-100 ">
            <tr>
                <td class="w-100 py-2 center"><span class="bold">Rol de Empleado</span>: {{ $empleado['rol']['rol'] }}
                    / <span class="bold">Género</span>:
                    {{ $empleado['genero'] }}
                </td>
            </tr>
        </table>
        @foreach ($empleado['tarjeta_checador'] as $tarjeta)
            <table class="w-100 texto-base registros page-break">
                <thead>
                    <tr>
                        <td class="bold  w-20">Fecha</td>
                        <td><span class="bold">Asistido</span></td>
                        <td><span class="bold ">Jornada Cumplida</span></td>
                        <td><span class="bold">T. Extra</span></td>
                        <td><span class="bold">T. Faltante</span></td>
                        <td><span class="bold">Inhábil</span></td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="tr_line">
                        <td class="pt-1"><span class="bold bg-indicadores">{{ $tarjeta['dia'] }}</span></td>
                        <td class="">
                            @if ($tarjeta['indicadores_tarjeta']['asistido'] == 'No')
                                <span class="text-danger">{{ $tarjeta['indicadores_tarjeta']['asistido'] }}</span>
                            @else
                                <span class="">{{ $tarjeta['indicadores_tarjeta']['asistido'] }}</span>
                            @endif
                        </td>
                        <td class="">
                            @if ($tarjeta['indicadores_tarjeta']['cumplio_jornada'] == 'No')
                                <span
                                    class="text-danger">{{ $tarjeta['indicadores_tarjeta']['cumplio_jornada'] }}</span>
                            @else
                                <span class="">{{ $tarjeta['indicadores_tarjeta']['cumplio_jornada'] }}</span>
                            @endif
                        </td>
                        <td class="">{{ $tarjeta['indicadores_tarjeta']['horas_extra'] }}</td>
                        <td class="">{{ $tarjeta['indicadores_tarjeta']['tiempo_faltante_jornada'] }}</td>
                        <td class="">{{ $tarjeta['indicadores_tarjeta']['dia_descanso_obligatorio'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <table class="w-100">
                                <tr>
                                    <td class="w-75">
                                        @if ($tarjeta['indicadores_tarjeta']['asistido'] == 'Si')
                                            <table class="w-100 texto-base  center">
                                                <thead>
                                                    <tr>
                                                        <td class=" bold">Registro</td>
                                                        <td><span class="bold">Horas Laboradas</span></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($tarjeta['registros'] as $registro)
                                                        @if ($registro['registro_valido'])
                                                            <tr class="">
                                                                <td class="w-70"><span
                                                                        class="">{{ $registro['jornada_texto'] }}</span>
                                                                </td>
                                                                <td class="w-30">{{ $registro['horas'] }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                        <table class="w-100 texto-base center mb-2">
                                            <tr>
                                                <td class="w-70 bold center"><span class="">Total de Horas
                                                        Laboradas</span>
                                                </td>
                                                <td class="w-30 bold">
                                                    {{ $tarjeta['indicadores_tarjeta']['tiempo_trabajado'] }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="w-25">
                                        <div class="w-90 ml-auto right mt-8 bold">
                                            <div class=" line my-2"> </div>
                                            <div>
                                                Firma de Conformidad
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
        @endforeach
        </tbody>
        </table>

        <div class="mt-6 center w-100 bg-0 uppercase bold size-16px bg-indicadores py-1">
            <span>
                Resumen de indicadores por periodo de {{ $datos['parametros']['dias_tarjeta'] }} días
                ({{ $fecha_del_reporte }}).
        </div>


        <table class="w-100 texto-base mt-5  center">
            <thead>
                <tr>
                    <td class=""><span class="bold">Horas Requeridas</span></td>
                    <td class="w-25"><span class="bold">Horas Trabajadas</span></td>
                    <td class="w-25"><span class="bold">Horas Faltantes</span></td>
                    <td class="w-25"><span class="bold">Cumplió Jornada en Gral.</span></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $empleado['indicadores_empleado']['horas_requeridas'] }}</td>
                    <td>{{ $empleado['indicadores_empleado']['tiempo_trabajado'] }}</td>
                    <td>{{ $empleado['indicadores_empleado']['tiempo_faltante_jornada'] }}</td>
                    <td>{{ $empleado['indicadores_empleado']['cumplio_jornada'] }}</td>
                </tr>
            </tbody>
        </table>

        <table class="w-100 texto-base center">
            <thead>
                <tr>
                    <td class=""><span class="bold">Días a descansar</span></td>
                    <td class="w-25"><span class="bold">Días Descansados</span></td>
                    <td class="w-25"><span class="bold">Faltas</span></td>
                    <td class="w-25"><span class="bold">Tiempo Extra</span></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $empleado['indicadores_empleado']['dias_a_descansar'] }}</td>
                    <td>{{ $empleado['indicadores_empleado']['dias_descansados'] }}</td>
                    <td>{{ $empleado['indicadores_empleado']['faltas'] }}</td>
                    <td>{{ $empleado['indicadores_empleado']['horas_extra'] }}</td>
                </tr>
            </tbody>
        </table>

        <table class="w-100 uppercase center mt-5 size-20px bold">
            <tr>
                <td class="w-30 center ">Porcentaje de asistencia:
                    [{{ $empleado['indicadores_empleado']['porcentaje_asistencia'] }}] / 100 %</td>
            </tr>
        </table>

        <div class="w-40 ml-auto mr-auto center mt-24">
            <div>
                {{ $empleado['empleado'] }}
            </div>
            <div class=" line"> </div>
            <div class="bold mt-1">
                Firma de Conformidad
            </div>
        </div>
    @endforeach

</body>

</html>
