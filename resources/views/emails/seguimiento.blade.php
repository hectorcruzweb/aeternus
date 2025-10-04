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
            {{ env('MAIL_FROM_NAME') }} | Seguimiento a Clientes
        </div>
        <div class="content">
            <p>Hola {{ $seguimiento['cliente'] ?? 'Cliente' }},</p>
            <p>Se ha {{ $seguimiento['action'] ?? 'agregado' }} un seguimiento con los siguientes detalles:</p>
            <ul>
                <li><strong>Fecha a contactar:</strong> {{ $seguimiento['fecha'] ?? 'N/A' }}</li>
                <li><strong>Motivo:</strong> {{ $seguimiento['motivo'] ?? 'N/A' }}</li>
                <li><strong>Medio:</strong> {{ $seguimiento['medio'] ?? 'N/A' }}</li>
                <li><strong>Comentario:</strong> {{ $seguimiento['comentario'] ?? 'N/A' }}</li>
            </ul>
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
