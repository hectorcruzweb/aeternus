<!DOCTYPE html>
<html lang="es">

<head>
    @include('layouts.estilos')
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 14px !important;
        }

        .header {
            background-color: #666666;
            color: white;
            padding: 15px !important;
            text-align: center;
            font-size: 20px;
        }

        .footer {
            background-color: #ecf0f1;
            color: #7f8c8d;
            padding: 15px !important;
            text-align: center;
            font-size: 12px;
        }

        .content {
            margin: 20px 0 !important;
        }

        ul li {
            padding: 7px 0px !important;
        }

        .info-contacto {
            padding: 14px 0 !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            {{ config('mail.from.name') }} | Seguimiento a Clientes
        </div>
        <div class="content">
            <p>Hola {{ $seguimiento['cliente'] }},</p>
            <p>Se ha
                @if ($seguimiento['tipo'] === 'programar seguimiento')
                    agendado un seguimiento programado
                @elseif ($seguimiento['tipo'] === 'reprogramar seguimiento')
                    actualizado un seguimiento programado
                @elseif ($seguimiento['tipo'] === 'cancelar seguimiento programado')
                    cancelado un seguimiento programado
                @elseif ($seguimiento['tipo'] === 'atender seguimiento')
                    atendido un seguimiento programado
                @elseif ($seguimiento['tipo'] === 'registrar seguimiento')
                    registrado un seguimiento
                @elseif ($seguimiento['tipo'] === 'modificar seguimiento')
                    actualizado un seguimiento
                @elseif ($seguimiento['tipo'] === 'cancelar seguimiento realizado')
                    cancelado un seguimiento
                @else
                    registrado un seguimiento
                @endif con los siguientes detalles:
            </p>
            <!--Contenido del mensaje, segun sea el caso.-->
            @if (
                $seguimiento['tipo'] === 'atender seguimiento' ||
                    $seguimiento['tipo'] === 'registrar seguimiento' ||
                    $seguimiento['tipo'] === 'modificar seguimiento' ||
                    $seguimiento['tipo'] === 'cancelar seguimiento realizado')
                <ul>
                    <li><strong>Fecha de atención:</strong> {{ $seguimiento['fechahora_seguimiento_texto'] ?? 'N/A' }}
                    </li>
                    @if (!$seguimiento['programado_b'])
                        <li><strong>Motivo:</strong> {{ $seguimiento['motivo'] ?? 'N/A' }}</li>
                    @endif


                    <li><strong>Medio de Contacto:</strong> {{ $seguimiento['medio_seguimiento_texto'] ?? 'N/A' }}</li>
                    @if (!$seguimiento['programado_b'])
                        @if (isset($seguimiento['operacion']))
                            <li><strong>Operación en Cuestión:</strong> {{ $seguimiento['operacion'] ?? 'N/A' }}</li>
                        @endif
                    @endif
                    <li><strong>Resultado Obtenido:</strong> {{ $seguimiento['resultado_texto'] ?? 'N/A' }}</li>
                    <li><strong>Fecha de registro:</strong>
                        {{ $seguimiento['fechahora_registro_seguimiento'] ?? 'N/A' }}
                    </li>
                </ul>
                @if ($seguimiento['programado_b'] === 1)
                    <p>Datos del Seguimiento Programado</p>
                @endif
            @endif
            @if (
                $seguimiento['tipo'] === 'programar seguimiento' ||
                    $seguimiento['tipo'] === 'reprogramar seguimiento' ||
                    $seguimiento['tipo'] === 'cancelar seguimiento programado' ||
                    $seguimiento['programado_b'] === 1)
                <ul>
                    <li><strong>Fecha a contactar:</strong> {{ $seguimiento['fechahora_programada'] ?? 'N/A' }}</li>
                    <li><strong>Motivo:</strong> {{ $seguimiento['motivo'] ?? 'N/A' }}</li>
                    <li><strong>Posible Medio de Contacto:</strong> {{ $seguimiento['medio'] ?? 'N/A' }}</li>
                    @if (isset($seguimiento['operacion']))
                        <li><strong>Operación en Cuestión:</strong> {{ $seguimiento['operacion'] ?? 'N/A' }}</li>
                    @endif
                    <li><strong>Fecha de registro:</strong>
                        {{ $seguimiento['fechahora_registro_programado'] ?? 'N/A' }}
                    </li>
                </ul>
            @endif
            <div class="info-contacto size-14px text-black">
                Para dudas o más información puedes contactarnos a través de los siguientes datos:
                <div>
                    <span style="text-transform: uppercase">{{ $empresa->nombre_comercial }},
                        {{ $empresa->razon_social }}</span>
                    <span>
                        {{ $empresa->calle }} Núm. Ext {{ $empresa->num_ext }} , Col.
                        {{ $empresa->colonia }}. {{ $empresa->ciudad }},
                        {{ $empresa->estado }}. Teléfono {{ $empresa->telefono }}.
                    </span>
                </div>
            </div>
            <div class="footer">
                Este es un correo automático. No responda a este mensaje.<br>
                <div style="padding-top:7px;">
                    © {{ date('Y') }} {{ env('MAIL_FROM_NAME') }}. Todos los derechos reservados.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
