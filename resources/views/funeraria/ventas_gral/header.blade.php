<!DOCTYPE html>
<html>

<head>
    @include('layouts.estilos')
</head>

<body style="border:0; margin: 0;" class="relative">
    @if ($datos['saldo'] == 0)
        <span class="watermark watermark-success mt-35 left-30 px-5 uppercase size-2 w-normal absolute">
            pagado
        </span>
    @endif

    @if ($datos['venta_general']['entregado_b'] == 1)
        <span class="watermark watermark-success mt-40 left-33 px-5 uppercase size-2 w-normal absolute">
            entregado
        </span>
    @endif

    @if ($datos['operacion_status'] == 0)
        <span class="watermark watermark-danger mt-45 left-40 px-5 uppercase size-2 w-normal absolute">
            cancelado
        </span>
    @endif
</body>

</html>
