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



        .texto-dia {
            background-color: #ccc;
        }
    </style>
</head>

<body>
    @include('layouts.estilos')


    @foreach ($datos as $empleado)
        <table class="w-100 line page-break-empleado">
            <tr>
                <td class=""><img class="logo logos -mt-4" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                </td>
                <td class="right">
                    <div class="">
                        <span class="uppercase size-14px bold">Del 28 de Diciembre del 2023 al 04 de Enero
                            del 2024</span>
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
            <table class="w-100 texto-base  registros page-break">
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
                        <td class="py-1"><span class="bold texto-dia">{{ $tarjeta['dia'] }}</span></td>
                        <td class="">
                            @if ($tarjeta['indicadores']['asistido'] == 'No')
                                <span class="text-danger">{{ $tarjeta['indicadores']['asistido'] }}</span>
                            @else
                                <span class="">{{ $tarjeta['indicadores']['asistido'] }}</span>
                            @endif
                        </td>
                        <td class="">
                            @if ($tarjeta['indicadores']['cumplio_jornada'] == 'No')
                                <span class="text-danger">{{ $tarjeta['indicadores']['cumplio_jornada'] }}</span>
                            @else
                                <span class="">{{ $tarjeta['indicadores']['cumplio_jornada'] }}</span>
                            @endif
                        </td>
                        <td class="">{{ $tarjeta['indicadores']['horas_extra'] }}</td>
                        <td class="">{{ $tarjeta['indicadores']['tiempo_faltante_jornada'] }}</td>
                        <td class="">{{ $tarjeta['indicadores']['dia_descanso_obligatorio'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <table class="w-100">
                                <tr>
                                    <td class="w-75">
                                        @if ($tarjeta['indicadores']['asistido'] == 'Si')
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
                                        <table class="w-100 texto-base center mb-4">
                                            <tr>
                                                <td class="w-70 bold center"><span class="">Total de Horas
                                                        Laboradas</span>
                                                </td>
                                                <td class="w-30">
                                                    {{ $tarjeta['indicadores']['tiempo_trabajado'] }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="w-25">
                                        <div class="w-90 ml-auto right mt-8">
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
    @endforeach

</body>

</html>
