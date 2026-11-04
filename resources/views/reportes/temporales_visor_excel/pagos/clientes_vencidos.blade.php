{{--
 Desglose de pagos vencidos agrupados por cliente, con operación, referencia y contacto.
 Dónde ajustar: Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print
 pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.
 Relacionado: VisorReportes.vue; controlador del reporte; exportador Excel equivalente
 Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
--}}
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $datos['titulo'] }}</title>
<style>
*{box-sizing:border-box}body{margin:0;font:13px Arial,sans-serif;color:#343a40;background:white}.reporte{padding:12px;width:100%}.cabecera{width:100%;border-bottom:2px solid #b18b1e;margin-bottom:15px}.cabecera td{padding:0 0 12px;border:0}.logo{width:185px;height:auto}.empresa{text-align:right;font-size:11px;line-height:1.5}h1{font-size:20px;margin:0 0 6px}h2{font-size:15px;margin:18px 0 8px}.nota{font-size:11px;color:#70757b;line-height:1.5}.indicadores{width:100%;table-layout:fixed;margin:12px 0}.indicadores td{padding:12px;text-align:center;background:#f6f4ee;color:#80672c;border:3px solid white}.indicadores strong{display:block;font-size:20px;margin-top:5px}.aviso{margin:10px 0;padding:10px 12px;background:#fff4d6;color:#70530f;border-left:4px solid #b18b1e;font-weight:bold;line-height:1.5}.tabla{overflow:auto}table.datos{width:100%;border-collapse:collapse;font-size:12px}table.datos th{background:#343a40;color:white;padding:10px 8px}table.datos td{padding:10px 7px;text-align:center;border-bottom:1px solid #eee}table.datos tbody tr:nth-child(even){background:#f3f4f5}.total{font-weight:bold;background:#f6f4ee}.detalle td:nth-child(n+11){white-space:nowrap}.estado{color:#b74438;white-space:nowrap}.cliente-bloque{margin-top:18px}.cliente-cabecera{padding:10px 12px;background:#f6f4ee;border-left:3px solid #b18b1e}.cliente-cabecera h3{font-size:14px;margin:0 0 5px}.cliente-cabecera p{margin:0;line-height:1.5;font-size:11px;color:#656970}.cliente-bloque .datos{margin-top:7px}.pie{border-top:1px solid #ddd;padding-top:10px;margin-top:10px;color:#777;font-size:10px}
@media print{@page{size:A4 landscape;margin:10mm}.reporte{padding:0}body{font-size:10px;print-color-adjust:exact;-webkit-print-color-adjust:exact}.logo{width:145px}h1{font-size:17px}.empresa,.nota{font-size:9px}.indicadores td{padding:7px}.indicadores strong{font-size:16px}table.datos{font-size:9px}table.datos td,table.datos th{padding:6px 4px}.tabla{overflow:visible}thead{display:table-header-group}tr,.aviso,.cliente-cabecera{page-break-inside:avoid}.cliente-cabecera{page-break-after:avoid}.aviso{font-size:10px}}
</style>@include('reportes.temporales_visor_excel.compartidos.paginacion')
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'pagos', 'altoMarcaAgua' => 850])
</head><body><div class="marca-agua-contenido" @if(empty($modoPdf)) data-marca-visor @endif><main class="reporte">
@php
$e = $datos['empresa'] ?? [];
$ruta = public_path(env('LOGOJPG'));
$mime = is_file($ruta) ? (new \finfo(FILEINFO_MIME_TYPE))->file($ruta) : null;
$logo = in_array($mime, ['image/jpeg','image/png','image/gif'], true) ? 'data:'.$mime.';base64,'.base64_encode(file_get_contents($ruta)) : null;
$dinero = function($v) { return '$ '.number_format($v,2,'.',','); };
@endphp
<table class="cabecera"><tr><td>
@if($logo)
<img class="logo" src="{{ $logo }}" alt="Aeternus Funerales">
@endif
</td><td class="empresa"><b>{{ \App\Exports\NombreEmpresaReportes::formatear($e['nombre_comercial'] ?? null) }}</b><br>{{ $e['razon_social'] ?? '' }} · {{ $e['rfc'] ?? '' }}<br>{{ \App\Exports\DireccionReportes::formatear($e) }}<br>{{ $e['telefono'] ?? '' }} · {{ mb_strtolower(trim($e['email'] ?? ''),'UTF-8') }}</td></tr></table>
<h1>{{ $datos['titulo'] }}</h1><p class="nota">Periodo consultado: {{ $datos['periodo'] }} · Datos consultados al {{ $datos['fecha'] }} · Moneda: MXN</p>
<table class="indicadores"><tr><td>Clientes con pagos vencidos<strong>{{ number_format($datos['resumen']['clientes']) }}</strong></td><td>Pagos vencidos pendientes<strong>{{ number_format($datos['resumen']['cantidad']) }}</strong></td><td>{{ $datos['etiquetaImporte'] ?? 'Por cobrar hoy' }}<strong>{{ $dinero($datos['resumen']['pendiente']) }} MXN</strong></td></tr></table>
<div class="aviso">{{ $datos['avisoSaldos'] }}</div>
<h2>Resumen por tipo de operación</h2>
<table class="datos"><thead><tr><th>Tipo de operación</th><th>Pagos pendientes</th><th>Programado</th><th>Aplicado a capital</th><th>{{ $datos['etiquetaImporte'] ?? 'Por cobrar hoy' }}</th></tr></thead><tbody>
@foreach($datos['resumen']['tipos'] as $f)
<tr><td>{{ $f['nombre'] }}</td><td>{{ number_format($f['cantidad']) }}</td><td>{{ $dinero($f['programado']) }}</td><td>{{ $dinero($f['aplicado']) }}</td><td>{{ $dinero($f['pendiente']) }}</td></tr>
@endforeach
<tr class="total"><td>TOTAL</td><td>{{ number_format($datos['resumen']['cantidad']) }}</td><td>{{ $dinero($datos['resumen']['programado']) }}</td><td>{{ $dinero($datos['resumen']['aplicado']) }}</td><td>{{ $dinero($datos['resumen']['pendiente']) }}</td></tr></tbody></table>
<h2>Desglose por cliente</h2><p class="nota">Cada cliente aparece una sola vez con todos sus pagos vencidos, ordenados del más antiguo al más reciente. El número de venta de Cuotas de Mantenimiento corresponde a la propiedad en cementerio.</p>
@forelse($datos['clientes'] as $c)
<section class="cliente-bloque">
<div class="cliente-cabecera"><h3>{{ $c['cliente'] }}</h3><p>Teléfono / celular: {{ implode(' / ',array_unique(array_filter([$c['telefono'],$c['celular']]))) ?: 'No disponible' }} · Email: {{ $c['email'] ?: 'No disponible' }}<br>{{ $c['operaciones'] }} operaciones · {{ $c['cantidad'] }} pagos vencidos · Pendiente: <b>{{ $dinero($c['pendiente']) }} MXN</b></p></div>
<div class="tabla"><table class="datos detalle"><thead><tr><th>Tipo de operación</th><th>Núm. venta / servicio</th><th>Ubicación</th><th>Fallecido</th><th>Referencia</th><th>Concepto del pago</th><th>Fecha programada</th><th>Programado</th><th>Aplicado a capital</th><th>Pendiente</th><th>Estado</th></tr></thead><tbody>
@foreach($c['pagos_indices'] as $indice)
@php $f=$datos['pagos'][$indice]; @endphp
<tr><td>{{ $f['tipo_operacion'] }}</td><td>{{ $f['numero_venta'] }}</td><td>{{ $f['ubicacion_texto'] }}</td><td>{{ (int)$f['empresa_operaciones_id'] === 3 ? (trim($f['fallecido'] ?? '') ?: 'No disponible') : '—' }}</td><td>{{ $f['referencia_pago'] }}</td><td>{{ $f['concepto_pago'] }}</td><td>{{ $f['fecha_programada_texto'] }}</td><td>{{ $dinero($f['monto_programado']) }}</td><td>{{ $dinero($f['capital_cubierto']) }}</td><td>{{ $dinero($f['pendiente']) }}</td><td class="estado">{{ $f['estado_pago'] }}</td></tr>
@endforeach
<tr class="total"><td colspan="9">TOTAL DEL CLIENTE</td><td>{{ $dinero($c['pendiente']) }}</td><td></td></tr>
</tbody></table></div></section>
@empty
<p class="nota">No hay clientes con pagos vencidos.</p>
@endforelse
<table class="datos"><tr class="total"><td>TOTAL GENERAL · {{ $datos['resumen']['clientes'] }} clientes · {{ $datos['resumen']['cantidad'] }} pagos vencidos</td><td>{{ $dinero($datos['resumen']['pendiente']) }} MXN</td></tr></table>
<footer class="pie">{{ \App\Exports\NombreEmpresaReportes::formatear($e['nombre_comercial'] ?? null) }} · {{ $datos['etiquetaDetalle'] ?? 'Pagos que vencen hoy' }} · {{ $datos['fecha'] }}</footer>
</main></div></body></html>
