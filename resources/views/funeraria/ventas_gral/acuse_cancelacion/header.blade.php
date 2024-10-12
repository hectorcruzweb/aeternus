<!DOCTYPE html>
<html>

<head>
    @include('layouts.estilos')
</head>

<body style="border:0; margin: 0;" class="relative">
    @if ($datos['operacion_status'] == 0)
        <span class="watermark watermark-danger mt-50 left-30 px-5 uppercase size-2 w-normal absolute">
            acuse de cancelado
        </span>
    @endif
</body>

</html>
