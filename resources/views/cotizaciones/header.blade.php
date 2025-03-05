<!DOCTYPE html>
<html>

<head>
    @include('layouts.estilos')
</head>

<body style="border:0; margin: 0;" class="relative">
    @if ($datos['status'] == 0)
        <span class="watermark watermark-danger mt-45 left-40 px-5 uppercase size-2 w-normal absolute">
            cancelado
        </span>
    @endif

    @if ($datos['status'] == 3)
        <span class="watermark watermark-danger mt-40 left-33 px-5 uppercase size-2 w-normal absolute">
            vencido
        </span>
    @endif
</body>

</html>
