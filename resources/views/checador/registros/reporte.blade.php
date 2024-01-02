<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <style>
        .logos {
            min-width: 200px;
            max-width: 200px;
        }

        .banco {
            border: 2px solid #E5E8E8 !important;
        }


        .banco-logo {
            display: block;
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
    <div class="pagos relative">
        <table class="w-100">
            <thead>
                <tr>
                    <th class="w-25">
                        <img class="logo logos -mt-6" src="{{ public_path(env('LOGOJPG')) }}" alt="">
                    </th>
                    <th class="w-40">
                    </th>
                    <th class="w-45">
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                Rango de Fechas
                            </div>
                            <p class="control-valor">
                                {{ $datos['rango_fechas'] }}
                            </p>
                        </div>
                        <div class="numeros-contrato">
                            <div class="control bg-header size-13px">
                                Empleado
                            </div>
                            <p class="control-valor">
                                {{ $datos['empleado'] }}
                            </p>
                        </div>
                    </th>
                </tr>
            </thead>
        </table>
    </div>
    <span class="uppercase bold size-16px letter-spacing-1">Registros del Checador</span>
    <table class="w-100 texto-base mt-5 pagos_tabla center">
        <thead>
            <tr>
                <td class="py-2 bold">Fecha y Hora</td>
                <td><span class="bold">Empleado</span></td>
                <td><span class="bold">Tipo de Registro</span></td>
                <td><span class="bold">Forma de Registro</span></td>
                <td><span class="bold">Estatus</span></td>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos['datos'] as $registro)
                <tr>
                    <td class="py-2"><span class="bold">{{ $registro['fecha_registro_texto'] }} /
                            {{ $registro['hora_registro'] }}</span></td>
                    <td>{{ $registro['usuario']['nombre'] }}</td>
                    <td>{{ $registro['tipo_registro_texto'] }}</td>
                    <td>{{ $registro['forma_registro'] }}</td>
                    <td>{{ $registro['status_texto'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
