{{--
 Pendientes históricos de atención y atraso en lenguaje humano.
 Dónde ajustar: Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print
 pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.
 Relacionado: VisorReportes.vue; controlador del reporte; exportador Excel equivalente
 Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
--}}
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $datos['titulo'] }}</title>
<style>
*{box-sizing:border-box}body{margin:0;font:13px Arial,sans-serif;color:#343a40;background:white}.reporte{padding:12px;width:100%}.cabecera{width:100%;border-bottom:2px solid #b18b1e;margin-bottom:15px}.cabecera td{padding:0 0 12px;border:0}.logo{width:185px;height:auto}.empresa{text-align:right;font-size:11px;line-height:1.5}h1{font-size:20px;margin:0 0 6px}h2{font-size:15px;margin:18px 0 8px}.nota{font-size:11px;color:#70757b;line-height:1.5}.indicadores{width:100%;table-layout:fixed;margin:12px 0}.indicadores td{padding:12px;text-align:center;background:#f6f4ee;color:#80672c;border:3px solid white}.indicadores strong{display:block;font-size:20px;margin-top:5px}.aviso{margin:10px 0;padding:10px 12px;background:#fff4d6;color:#70530f;border-left:4px solid #b18b1e;font-weight:bold;line-height:1.5}.tabla{overflow:auto}table.datos{width:100%;border-collapse:collapse;font-size:12px}table.datos th{background:#343a40;color:white;padding:10px 8px}table.datos td{padding:10px 7px;text-align:center;border-bottom:1px solid #eee}table.datos tbody tr:nth-child(even){background:#f3f4f5}.total{font-weight:bold;background:#f6f4ee}.detalle td:nth-child(n+11){white-space:nowrap}.estado{color:#b74438;white-space:nowrap}.pie{border-top:1px solid #ddd;padding-top:10px;margin-top:10px;color:#777;font-size:10px}
@media print{@page{size:A4 landscape;margin:10mm}.reporte{padding:0}body{font-size:10px;print-color-adjust:exact;-webkit-print-color-adjust:exact}.logo{width:145px}h1{font-size:17px}.empresa,.nota{font-size:9px}.indicadores td{padding:7px}.indicadores strong{font-size:16px}table.datos{font-size:9px}table.datos td,table.datos th{padding:6px 4px}.tabla{overflow:visible}thead{display:table-header-group}tr,.aviso{page-break-inside:avoid}.aviso{font-size:10px}}
</style></head><body><main class="reporte">
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
<h1>{{ $datos['titulo'] }}</h1><p class="nota">{{ $datos['periodo'] }} · Consultado al {{ $datos['fecha'] }}</p>
<table class="indicadores"><tr>
@foreach(['total'=>'Vencidos pendientes','antiguos'=>'Más de 7 días de atraso','clientes'=>'A clientes registrados','cotizaciones'=>'Bajo cotización'] as $k=>$titulo)
<td>{{ $titulo }}<strong>{{ number_format($datos['resumen'][$k]) }}</strong></td>
@endforeach
</tr></table><div class="aviso">{{ $datos['avisoSaldos'] }}</div>
<h2>Seguimientos vencidos pendientes</h2><p class="nota">Ordenados del más antiguo al más reciente. Se muestra el correo previsto para el seguimiento; si falta, el del cliente o prospecto.</p>
<div class="tabla"><table class="datos"><thead><tr><th>Fecha programada</th><th>Tipo de cliente</th><th>Cliente / prospecto</th><th>Teléfono / celular</th><th>Email de contacto</th><th>Motivo</th><th>Medio previsto</th><th>Programó</th><th>Comentarios</th><th>Atraso</th><th>Estado</th></tr></thead><tbody>
@forelse($datos['seguimientos'] as $f)
<tr><td>{{ $f['fecha_texto'] }}</td><td>{{ $f['tipo_cliente'] }}</td><td>{{ trim($f['nombre_cliente'] ?? '') ?: 'No disponible' }}</td><td>{{ $f['contacto'] ?: 'No disponible' }}</td><td>{{ $f['email'] ?: 'No disponible' }}</td><td>{{ $f['motivo'] }}</td><td>{{ $f['medio'] }}</td><td>{{ trim($f['programo'] ?? '') ?: 'No disponible' }}</td><td>{{ $f['comentario'] ?: 'Sin comentarios' }}</td><td>{{ $f['atraso_texto'] }}</td><td class="estado">{{ $f['estado'] }}</td></tr>
@empty
<tr><td colspan="11">No hay seguimientos vencidos pendientes a la fecha de consulta.</td></tr>
@endforelse
</tbody></table></div><footer class="pie">{{ \App\Exports\NombreEmpresaReportes::formatear($e['nombre_comercial'] ?? null) }} · Histórico de seguimientos vencidos · {{ $datos['fecha'] }}</footer>
</main></body></html>
