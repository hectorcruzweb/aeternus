<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Listado de pagos</title><style>
*{box-sizing:border-box}body{margin:0;background:#edf0f2;color:#343a40;font:10px Arial,sans-serif}.reporte{width:794px;margin:15px auto;padding:28px;background:white}.cabecera{width:100%;border-collapse:collapse;border-bottom:2px solid #a58828;margin-bottom:12px}.cabecera td{padding:0 0 10px;vertical-align:middle}.logo{width:145px}.empresa{text-align:right;font-size:9px;line-height:1.4}h1{font-size:20px;margin:10px 0 5px}h2{font-size:12px;margin:14px 0 6px}.nota{font-size:9px;color:#70777c;line-height:1.5}.filtros{padding:8px 10px;background:#f5f3ec;border-left:2px solid #a58828;font-size:9px;line-height:1.5;margin:12px 0}.indicadores{width:100%;table-layout:fixed;border-collapse:collapse;margin:10px 0}.indicadores td{padding:7px;background:#f6f4ee;border-top:1px solid #c5b783;text-align:center}.indicadores b{display:block;font-size:15px;margin-top:4px}.detalle{width:100%;table-layout:fixed;border-collapse:collapse;font-size:9px}.detalle thead{display:table-header-group}.detalle th{padding:6px 4px;background:#42494c;color:white;text-align:center}.detalle td{padding:6px 4px;border-bottom:1px solid #ddd;vertical-align:top;word-wrap:break-word;overflow-wrap:break-word}.detalle tr{page-break-inside:avoid}.detalle tr:nth-child(even){background:#f7f7f7}.secundario{font-size:8px;color:#747c82;line-height:1.35;margin-top:3px}.importe{text-align:right;white-space:nowrap}.activo{color:#337458}.cancelado{color:#ac4545}.referencia{letter-spacing:.25px}.resumen-tipos{page-break-inside:avoid;margin-bottom:14px}.resumen-tipos table{font-size:9px}.pie{border-top:1px solid #ddd;margin-top:14px;padding-top:7px;font-size:8px;color:#777}.modo-pdf{background:white}.modo-pdf .reporte{width:auto;margin:0;padding:0}.modo-pdf .pie{display:none}@media print{@page{size:letter portrait;margin:10mm 10mm 14mm}body{background:white;-webkit-print-color-adjust:exact;print-color-adjust:exact}.reporte{width:auto;margin:0;padding:0}.pie{display:none}}
</style>
@include('reportes.temporales_visor_excel.compartidos.zoom_listados')
</head><body class="{{ !empty($modoPdf) ? 'modo-pdf' : '' }}"><main class="reporte"><div class="marca-agua-contenido">
<table class="cabecera"><tr><td>@if($logo)<img class="logo" src="{{ $logo }}" alt="Aeternus Funerales">@endif</td><td class="empresa"><b>{{ \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null) }}</b><br>{{ $empresa['razon_social'] ?? '' }}<br>{{ \App\Exports\DireccionReportes::formatear($empresa) }}<br>Tel. {{ $empresa['telefono'] ?? '' }} · {{ mb_strtolower($empresa['email'] ?? '', 'UTF-8') }}</td></tr></table>
<h1>Listado de pagos</h1><p class="nota">Consulta: {{ $fecha }} · Importes en MXN</p>
<div class="filtros"><b>Filtros aplicados:</b> {{ implode(' · ', $filtros) }}</div>
<table class="indicadores"><tr><td>Movimientos listados<b>{{ number_format($resumen['total']) }}</b></td><td>Activos<b class="activo">{{ number_format($resumen['activos']) }}</b></td><td>Cancelados<b class="cancelado">{{ number_format($resumen['cancelados']) }}</b></td></tr></table>
<section class="resumen-tipos"><h2>Importes de movimientos activos</h2><table class="detalle"><thead><tr><th>Tipo de movimiento</th><th style="width:18%">Cantidad</th><th style="width:23%">Importe MXN</th></tr></thead><tbody>
@foreach($resumen['tipos'] as $tipo)
<tr><td>{{ $tipo['nombre'] }}</td><td style="text-align:center">{{ number_format($tipo['cantidad']) }}</td><td class="importe">$ {{ number_format($tipo['importe'],2) }}</td></tr>
@endforeach
</tbody></table><p class="nota">Los descuentos se presentan por separado y no representan dinero recibido. Los movimientos cancelados no se incluyen en estos importes.</p></section>
{{-- Tablas acotadas: Qt pagina cada bloque sin recalcular una tabla de miles
     de filas. No hay salto forzado: aprovecha el espacio disponible del papel. --}}
@foreach(array_chunk($filas ?: [null], 100) as $bloquePagos)
<table class="detalle"><colgroup><col style="width:10%"><col style="width:11%"><col style="width:22%"><col style="width:22%"><col style="width:17%"><col style="width:8%"><col style="width:10%"></colgroup><thead><tr><th>Núm. pago</th><th>Fecha</th><th>Cliente / venta o servicio</th><th>Referencias</th><th>Movimiento</th><th>Estado</th><th>Importe MXN</th></tr></thead><tbody>
@foreach($bloquePagos as $fila)
@if($fila !== null)
<tr><td><b>{{ $fila['id'] }}</b>@if($fila['parent_pago_id'])<div class="secundario">Sobre pago {{ $fila['parent_pago_id'] }}</div>@endif</td><td>{{ $fila['fecha_pago_texto'] }}</td><td>{{ $fila['cliente_texto'] }}<div class="secundario">{{ $fila['operaciones_texto'] }}</div></td><td class="referencia">{{ $fila['referencias_texto'] ?: 'Sin referencia' }}</td><td>{{ $fila['movimientos_pagos_texto'] }}</td><td class="{{ (int)$fila['status'] === 1 ? 'activo' : 'cancelado' }}">{{ (int)$fila['status'] === 1 ? 'Activo' : 'Cancelado' }}</td><td class="importe">$ {{ number_format($fila['monto_pago'],2) }}</td></tr>
@else
<tr><td colspan="7">No hay pagos para los filtros seleccionados.</td></tr>
@endif
@endforeach
</tbody></table>
@endforeach
<div class="pie">{{ $dominio }} · Generado: {{ $fecha }}</div></div></main>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'pagos', 'altoMarcaAgua' => 1050])
</body></html>
