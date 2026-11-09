<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Listado de servicios funerarios</title><style>
*{box-sizing:border-box}body{margin:0;background:#edf0f2;color:#343a40;font:10px Arial,sans-serif}.reporte{width:794px;margin:15px auto;padding:28px;background:white}.cabecera{width:100%;border-collapse:collapse;border-bottom:2px solid #a58828;margin-bottom:12px}.cabecera td{padding:0 0 10px;vertical-align:middle}.logo{width:145px}.empresa{text-align:right;font-size:9px;line-height:1.4}h1{font-size:20px;margin:10px 0 5px}.nota{font-size:9px;color:#70777c;line-height:1.5}.filtros{padding:8px 10px;background:#f5f3ec;border-left:2px solid #a58828;font-size:9px;line-height:1.5;margin:12px 0}.resumen{width:100%;table-layout:fixed;border-collapse:collapse;margin:10px 0}.resumen td{padding:7px;background:#f6f4ee;border-top:1px solid #c5b783}.resumen b{display:block;font-size:14px;margin-top:4px}.detalle{width:100%;table-layout:fixed;border-collapse:collapse;font-size:9px}.detalle thead{display:table-header-group}.detalle th{padding:6px 4px;background:#42494c;color:white;text-align:center}.detalle td{padding:6px 4px;border-bottom:1px solid #ddd;vertical-align:top;overflow-wrap:break-word;word-wrap:break-word}.detalle tr{page-break-inside:avoid}.detalle tr:nth-child(even){background:#f7f7f7}.secundario{font-size:8px;color:#747c82;line-height:1.35;margin-top:3px}.importe{text-align:right;white-space:nowrap}.pendiente{color:#ad622c}.pagada{color:#337458}.pie{border-top:1px solid #ddd;margin-top:14px;padding-top:7px;font-size:8px;color:#777}.modo-pdf{background:white}.modo-pdf .reporte{width:auto;margin:0;padding:0}.modo-pdf .pie{display:none}@media print{@page{size:letter portrait;margin:10mm 10mm 14mm}body{background:white}.reporte{width:auto;margin:0;padding:0}body{-webkit-print-color-adjust:exact;print-color-adjust:exact}.pie{display:none}}
</style>
@include('reportes.temporales_visor_excel.compartidos.zoom_listados')
</head><body class="{{ !empty($modoPdf) ? 'modo-pdf' : '' }}"><main class="reporte"><table class="cabecera"><tr><td>
@if($logo)
<img class="logo" src="{{ $logo }}" alt="Aeternus Funerales">
@endif
</td><td class="empresa"><b>{{ \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null) }}</b><br>{{ $empresa['razon_social'] ?? '' }}<br>{{ \App\Exports\DireccionReportes::formatear($empresa) }}<br>Tel. {{ $empresa['telefono'] ?? '' }} · {{ mb_strtolower($empresa['email'] ?? '', 'UTF-8') }}</td></tr></table>
<h1>Listado de servicios funerarios</h1><p class="nota">Consulta: {{ $fecha }} · Importes en MXN</p>
<div class="filtros"><b>Filtros aplicados:</b> {{ implode(' · ', $filtros) }}</div>
<table class="resumen"><tr><td>Servicios listados<b>{{ number_format($resumen['total']) }}</b></td><td>Con contrato<b>{{ number_format($resumen['contratos']) }}</b></td><td>Saldo de contratos vigentes<b>$ {{ number_format($resumen['saldo'],2) }} <small>MXN</small></b></td></tr></table>
<table class="detalle"><colgroup><col style="width:22%"><col style="width:22%"><col style="width:14%"><col style="width:24%"><col style="width:8%"><col style="width:10%"></colgroup><thead><tr><th>Servicio / fallecido</th><th>Cliente / contacto</th><th>Fechas</th><th>Servicios y destino</th><th>Estado</th><th>Saldo MXN</th></tr></thead><tbody>
@forelse($filas as $fila)
<tr><td><b>Servicio {{ $fila['servicio'] }}</b><br>{{ $fila['fallecido'] }}</td><td>{{ $fila['cliente'] }}
@if(!$fila['conContrato'])
<div class="secundario">Contratante temporal</div>
@endif
<div class="secundario">{{ $fila['contacto'] ?: 'Sin teléfono' }}<br>{{ $fila['email'] ?: 'Sin correo' }}</div></td><td><span class="secundario">Solicitud</span><br>{{ $fila['solicitud'] }}<div class="secundario">Contratación</div>{{ $fila['contratacion'] }}</td><td>{{ $fila['servicios'] }}</td><td class="{{ $fila['estado'] === 'Pagada' ? 'pagada' : ($fila['estado'] === 'Por pagar' ? 'pendiente' : '') }}">{{ $fila['estado'] }}</td><td class="importe">$ {{ number_format($fila['saldo'],2) }}</td></tr>
@empty
<tr><td colspan="6">No hay servicios para los filtros seleccionados.</td></tr>
@endforelse
</tbody></table><p class="nota">El saldo corresponde a los contratos vigentes del listado. Las solicitudes sin contrato y los servicios cancelados no aportan saldo al resumen.</p><div class="pie">{{ $dominio }} · Generado: {{ $fecha }}</div></main></body></html>
