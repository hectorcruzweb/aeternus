<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body { margin: 0; color: #343a40; font: 12px Arial, sans-serif; }
        header { border-bottom: 2px solid #a58828; padding-bottom: 10px; margin-bottom: 16px; }
        header img { width: 155px; vertical-align: middle; }
        header span { float: right; color: #756022; }
        h1 { margin: 0 0 6px; font-size: 22px; }
        h2 { margin: 18px 0 8px; font-size: 15px; color: #80641a; page-break-after: avoid; }
        .meta { color: #626970; margin: 0 0 10px; }
        .resumen { background: #f6f4ee; border-left: 3px solid #a58828; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 7px 6px; text-align: left; vertical-align: top; }
        .datos th { background: #404649; color: white; }
        .datos td { border-bottom: 1px solid #ddd; }
        .datos tr:nth-child(even) td { background: #f6f6f6; }
        .importe { text-align: right; white-space: nowrap; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
<header>
    @if($logo && is_file($logo))
        <img src="{{ $logo }}" alt="Aeternus Funerales">
    @endif
    <span>Aeternus Funerales · Facturación</span>
</header>
<h1>{{ $titulo }}</h1>
<p class="meta">Consultado el {{ $fechaConsulta }} · Folio {{ $cfdi->id }}</p>
<table class="resumen">
    <tr><td colspan="2"><strong>UUID</strong> {{ $cfdi->uuid ?: 'No disponible' }}</td></tr>
    <tr>
        <td><strong>Receptor</strong> {{ $cfdi->nombre_receptor ?: (optional($cfdi->cliente)->nombre ?: 'No disponible') }}</td>
        <td><strong>RFC</strong> {{ $cfdi->rfc_receptor ?: 'No disponible' }}</td>
    </tr>
    <tr>
        <td><strong>Timbrado</strong> {{ $cfdi->fecha_timbrado ? fecha_abr($cfdi->fecha_timbrado) : 'No disponible' }}</td>
        <td><strong>Estado</strong> {{ [0 => 'Cancelado', 1 => 'Vigente', 3 => 'En proceso de cancelación'][$cfdi->status] ?? 'No disponible' }}</td>
    </tr>
    <tr>
        <td><strong>Tipo</strong> {{ $tipoCfdi ?: 'No disponible' }} · <strong>Método de pago</strong> {{ $metodoPago ?: 'No aplica' }}</td>
        <td><strong>Total CFDI</strong> {{ number_format($cfdi->total, 2) }} {{ $moneda ?: '' }}</td>
    </tr>
</table>
<h2>Operaciones relacionadas ({{ $operaciones->count() }})</h2>
@if($operaciones->isEmpty())
    <p>Este CFDI no tiene operaciones relacionadas.</p>
@else
    <table class="datos">
        <thead><tr><th>Tipo y referencia</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th class="importe">Total MXN</th><th class="importe">Saldo MXN</th></tr></thead>
        <tbody>
            @foreach($operaciones as $operacion)
                @php
                    $tipo = (int) $operacion->empresa_operaciones_id;
                    $referencia = [1 => $operacion->ventas_terrenos_id, 2 => $operacion->ventas_terrenos_id,
                        3 => $operacion->servicios_funerarios_id, 4 => $operacion->ventas_planes_id,
                        5 => $operacion->ventas_generales_id][$tipo] ?? null;
                @endphp
                <tr>
                    <td><strong>{{ $tiposOperacion->get($tipo, 'Operación') }}</strong><br>
                        Ref. {{ $referencia ?: 'No disponible' }}
                        @if($tipo === 2 && $operacion->cuotas_cementerio_id)
                            · Cuota {{ $operacion->cuotas_cementerio_id }}
                        @endif
                    </td>
                    <td>{{ $operacion->cliente ?: 'No disponible' }}</td>
                    <td>{{ $operacion->fecha_operacion ? fecha_abr($operacion->fecha_operacion) : 'No disponible' }}</td>
                    <td>{{ [0 => 'Cancelada', 1 => 'Por pagar', 2 => 'Pagada'][$operacion->status] ?? 'No disponible' }}</td>
                    <td class="importe">$ {{ number_format($operacion->total, 2) }}</td>
                    <td class="importe">$ {{ number_format($operacion->saldo, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
</body>
</html>