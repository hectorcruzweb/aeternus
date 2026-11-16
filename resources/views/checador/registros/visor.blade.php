<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
*{box-sizing:border-box}body{margin:0;background:#edf0f2;color:#343a40;font:10px Arial,sans-serif}
.reporte{width:794px;max-width:100%;margin:15px auto;padding:28px;background:#fff}
.cabecera,.resumen,.detalle{width:100%;border-collapse:collapse;table-layout:fixed}
.cabecera{border-bottom:2px solid #a58828}.cabecera td{padding:0 0 10px;vertical-align:middle}
.logo{width:145px;max-width:100%}.empresa{text-align:right;line-height:1.45}
h1{font-size:22px;margin:12px 0 5px}h2{font-size:13px;margin:14px 0 6px}
.filtros{background:#f5f3ec;border-left:2px solid #a58828;padding:8px 10px;margin:10px 0;line-height:1.5}
.resumen td{background:#f6f4ee;border-top:1px solid #c5b783;text-align:center;padding:6px}
.resumen b{display:block;font-size:15px;margin-top:3px}
.detalle{font-size:9px}.detalle thead{display:table-header-group}.detalle tr{page-break-inside:avoid}
.detalle th{background:#42494c;color:#fff}.detalle th,.detalle td{padding:5px 4px;text-align:left;word-wrap:break-word}
.detalle td{border-bottom:1px solid #ddd}.detalle tbody tr:nth-child(even){background:#f8f8f8}
.detalle td:nth-child(1),.detalle th:nth-child(1){width:17%}.detalle th:nth-child(2){width:30%}
.detalle th:nth-child(3){width:10%}.detalle th:nth-child(4){width:9%}.detalle th:nth-child(5){width:13%}.detalle th:nth-child(6){width:7%}
.empleado{white-space:nowrap;word-wrap:normal}.cancelado{color:#a43d3d}.incidencia{color:#795c1b;font-weight:bold}.pie{margin-top:12px;color:#777;font-size:9px}.modo-pdf{background:#fff}
.modo-pdf .reporte{width:auto;margin:0;padding:0}.modo-pdf .pie{display:none}
@media print{@page{size:letter portrait;margin:10mm 10mm 14mm}body{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}.reporte{width:auto;margin:0;padding:0}}
</style>
</head>
<body class="{{ !empty($modoPdf) ? 'modo-pdf' : '' }}">
<main class="reporte">
    <table class="cabecera"><tr>
        <td>@if($logo)<img class="logo" src="{{ $logo }}" alt="Aeternus Funerales">@endif</td>
        <td class="empresa">
            <strong>{{ \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null) }}</strong><br>
            {{ $empresa['razon_social'] ?? '' }}<br>
            {{ \App\Exports\DireccionReportes::formatear($empresa) }}<br>
            {{ $empresa['telefono'] ?? '' }}
        </td>
    </tr></table>
    <h1>{{ $titulo }}</h1>
    <div class="filtros"><strong>{{ $periodo }}</strong> · Empleado: {{ $empleado }} · Área: {{ $area }}</div>
    <table class="resumen"><tr>
        <td>Registros<b>{{ count($filas) }}</b></td>
        <td>Entradas<b>{{ $entradas }}</b></td>
        <td>Salidas<b>{{ $salidas }}</b></td>
        <td>Cancelados<b>{{ $cancelados }}</b></td>
        <td>Incidencias<b>{{ $incidencias }}</b></td>
    </tr></table>
    <h2>Detalle de registros</h2>
    <table class="detalle">
        <thead><tr>@foreach($columnas as $columna)<th scope="col">{{ $columna }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse($filas as $fila)
            <tr>@foreach($fila as $indice => $valor)
                <td @if($indice === 1) class="empleado" style="font-size:{{ min(9, 360 / max(1, mb_strlen($valor, 'UTF-8'))) }}px"
                    @else class="{{ $indice === 5 && $valor === 'Cancelado' ? 'cancelado' : ($indice === 6 && $valor !== '' ? 'incidencia' : '') }}" @endif>{{ $valor }}</td>
            @endforeach</tr>
        @empty
            <tr><td colspan="7">No se encontraron registros con estos filtros.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="pie">{{ $dominio }} · Generado: {{ $fecha }}</div>
</main>
</body>
</html>
