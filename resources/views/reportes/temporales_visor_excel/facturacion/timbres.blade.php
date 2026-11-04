{{--
 Vista de recargas/disponibles y consumos asociados a cada lote cuando se incluye detalle.
 Dónde ajustar: Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print
 pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.
 Relacionado: VisorReportes.vue; controlador del reporte; exportador Excel equivalente
 Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
--}}
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $datos['titulo'] }}</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#fff;color:#343a40;font:13px Arial,sans-serif}.reporte{width:100%;padding:14px}.cabecera{width:100%;border-collapse:collapse;border-bottom:2px solid #b18b1e}.cabecera td{border:0;background:#fff;padding:0 0 10px;text-align:right}.cabecera td:first-child{width:200px;text-align:left}.logo{width:185px;height:auto}h1{margin:0 0 5px;font-size:20px}.empresa{font-weight:bold}.meta,.nota{font-size:11px;color:#70757b;margin:5px 0}.indicadores{width:100%;border-collapse:separate;border-spacing:6px;margin:12px 0}.indicadores td{text-align:center;padding:10px;background:#f6f4ee;color:#80672c}.indicadores strong{display:block;font-size:22px;margin-top:4px}h2{font-size:15px;margin:18px 0 8px}.tabla{overflow:auto}table.datos{width:100%;border-collapse:collapse;font-size:12px}.datos th{background:#343a40;color:#fff;padding:10px 6px;text-align:center;vertical-align:middle}.datos td{padding:9px 6px;text-align:center;vertical-align:middle;border-bottom:1px solid #eee;word-wrap:break-word;overflow-wrap:anywhere}.datos tbody tr:nth-child(even){background:#f3f4f5}.datos tfoot{font-weight:bold;background:#f6f4ee}a{color:#967316;text-decoration:underline}.alerta{padding:9px 12px;border-left:3px solid #b18b1e;background:#faf8f1;font-size:12px}.pie{border-top:1px solid #ddd;margin-top:16px;padding-top:8px;font-size:10px;color:#777}.uuid{font-size:10px}thead{display:table-header-group}tr{page-break-inside:avoid}
@media(max-width:700px){.reporte{padding:8px}.logo{width:125px}h1{font-size:16px}.datos{min-width:900px}}
@media print{@page{size:A4 landscape;margin:10mm}.reporte{padding:0}h1{font-size:16px}.logo{width:150px}.datos{table-layout:fixed;font-size:9px}.datos th,.datos td{padding:5px 3px}.uuid{font-size:8px}.indicadores td{padding:6px}.indicadores strong{font-size:16px}.tabla{overflow:visible}a{color:#343a40;text-decoration:none}.accion{display:none}body{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>@include('reportes.temporales_visor_excel.compartidos.paginacion')
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'facturacion', 'altoMarcaAgua' => 850])
</head><body><div class="marca-agua-contenido" @if(empty($modoPdf)) data-marca-visor @endif><main class="reporte">
<table class="cabecera"><tr><td>@if($logo)<img class="logo" src="{{ $logo }}" alt="Aeternus Funerales">@endif</td><td><h1>{{ $datos['titulo'] }}</h1><div class="empresa">{{ \App\Exports\NombreEmpresaReportes::formatear($datos['empresa']['nombre_comercial'] ?? null) }}</div><div class="meta">{{ $datos['empresa']['razon_social'] ?? '' }} · {{ $datos['empresa']['rfc'] ?? '' }}</div><div class="meta">{{ \App\Exports\DireccionReportes::formatear($datos['empresa']) }}</div><div class="meta">{{ $datos['empresa']['telefono'] ?? '' }} · {{ mb_strtolower($datos['empresa']['email'] ?? '', 'UTF-8') }}</div><div class="meta">Datos consultados: {{ $datos['fecha'] }}</div><div class="meta">Vigencia de la Firma Electrónica: {{ !empty($datos['fechaCaducidadFiel']) ? explode(' ', \App\Exports\TimbresRecargasExport::fecha($datos['fechaCaducidadFiel']))[0] : 'No disponible' }}</div></td></tr></table>
<table class="indicadores"><tr>@foreach([['Recargados','recargados'],['Consumidos','consumidos'],['Disponibles','disponibles']] as $ind)<td>{{ $ind[0] }}<strong>{{ number_format(array_sum(array_column($datos['recargas'], $ind[1]))) }}</strong></td>@endforeach</tr></table>
<p class="alerta">Los timbres consumidos incluyen CFDI cancelados. Las asignaciones históricas a recargas son estimadas.</p>
@if(empty($datos['recargas']))<p>No hay recargas registradas. El historial todavía no se ha cargado.</p>@endif
@if($datos['recarga_id'] === null && empty($modoPdf))<p class="nota accion">Selecciona «Ver historial» para consultar los CFDI de cada paquete. El Excel detallado completo incluye todos los paquetes y sus consumos.</p>@endif
@foreach($secciones as $sec)
<h2>{{ $sec['nombre'] }}</h2><div class="tabla"><table class="datos"><thead><tr>@foreach($sec['columnas'] as $col)<th>{{ $col }}</th>@endforeach @if($sec['nombre'] === 'Recargas' && $datos['recarga_id'] === null && empty($modoPdf))<th class="accion">Historial</th>@endif</tr></thead><tbody>
@forelse($sec['filas'] as $n=>$fila)<tr>@foreach($fila as $i=>$valor)<td class="{{ $sec['columnas'][$i] === 'UUID' ? 'uuid' : '' }}">{{ is_numeric($valor) && in_array($i,$sec['sumar']) ? number_format($valor) : $valor }}</td>@endforeach
@if($sec['nombre'] === 'Recargas' && $datos['recarga_id'] === null && empty($modoPdf))<td class="accion"><a href="#" data-reporte-relacionado="recarga-{{ $sec['lotes'][$n] }}">Ver historial</a></td>@endif</tr>
@empty<tr><td colspan="{{ count($sec['columnas']) }}">No hay registros para mostrar.</td></tr>@endforelse
</tbody>@if($sec['sumar'])<tfoot><tr>@foreach($sec['columnas'] as $i=>$col)<td>{{ $i === 0 ? 'TOTAL' : (in_array($i,$sec['sumar']) ? number_format(array_sum(array_column($sec['filas'],$i))) : '') }}</td>@endforeach @if($datos['recarga_id'] === null && empty($modoPdf))<td class="accion"></td>@endif</tr></tfoot>@endif</table></div>
@endforeach
<div class="pie">Aeternus Funerales · Control de timbres</div></main></div></body></html>
