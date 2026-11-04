{{--
 Resumen diario imprimible en carta vertical, solo PDF; servicios en pares, secciones compactas y tabla
 de ventas para impresión.
 Dónde ajustar: Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print
 pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.
 Relacionado: VisorReportes.vue; controlador del reporte; exportador Excel equivalente
 Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
--}}
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $datos['titulo'] }}</title>
<style>
*{box-sizing:border-box}body{margin:0;font:13px Arial,sans-serif;color:#343a40;background:white}.reporte{padding:12px;width:100%}.cabecera{width:100%;border-bottom:2px solid #b18b1e;margin-bottom:15px}.cabecera td{padding:0 0 12px;border:0}.logo{width:185px;height:auto}.empresa{text-align:right;font-size:11px;line-height:1.5}h1{font-size:20px;margin:0 0 6px}h2{font-size:15px;margin:18px 0 8px}.nota{font-size:11px;color:#70757b;line-height:1.5}.indicadores{width:100%;table-layout:fixed;margin:12px 0}.indicadores td{padding:12px;text-align:center;background:#f6f4ee;color:#80672c;border:3px solid white}.indicadores strong{display:block;font-size:20px;margin-top:5px}.aviso{margin:10px 0;padding:10px 12px;background:#fff4d6;color:#70530f;border-left:4px solid #b18b1e;font-weight:bold;line-height:1.5}.tabla{overflow:auto}table.datos{width:100%;border-collapse:collapse;font-size:12px}table.datos th{background:#343a40;color:white;padding:10px 8px}table.datos td{padding:10px 7px;text-align:center;border-bottom:1px solid #eee}table.datos tbody tr:nth-child(even){background:#f3f4f5}.total{font-weight:bold;background:#f6f4ee}.detalle td:nth-child(n+11){white-space:nowrap}.estado{color:#b74438;white-space:nowrap}.pie{border-top:1px solid #ddd;padding-top:10px;margin-top:10px;color:#777;font-size:10px}
@media print{@page{size:A4 landscape;margin:10mm}.reporte{padding:0}body{font-size:10px;print-color-adjust:exact;-webkit-print-color-adjust:exact}.logo{width:145px}h1{font-size:17px}.empresa,.nota{font-size:9px}.indicadores td{padding:7px}.indicadores strong{font-size:16px}table.datos{font-size:9px}table.datos td,table.datos th{padding:6px 4px}.tabla{overflow:visible}thead{display:table-header-group}tr,.aviso{page-break-inside:avoid}.aviso{font-size:10px}}
.seccion-titulo{padding:8px 10px;border-left:4px solid #b18b1e;background:#f6f4ee;color:#685727}.icono-seccion{display:inline-block;margin-right:8px;font-size:15px}.servicio{margin:10px 0;padding:12px;border:1px solid #e3dfd5;page-break-inside:avoid}.servicio h3{margin:0 0 8px;color:#685727;font-size:14px}.servicio-meta{color:#777;font-size:11px;line-height:1.5}.servicio table{width:100%;border-collapse:collapse;margin-top:8px}.servicio td{padding:6px;font-size:11px;border-bottom:1px solid #eee}.si{color:#34785d;font-weight:bold}.saldo{color:#ae5748;font-weight:bold}.seccion-bloque{margin-bottom:15px}.datos td:first-child{text-align:left}.datos td:last-child{text-align:right}.servicio-cabecera{background:#faf8f1;padding:8px}.nota-alcance{font-size:10px;color:#777}
.seccion-titulo{margin:10px 0 6px;padding:6px 9px;font-size:14px}.servicio{padding:8px;margin:7px 0}.servicio td{padding:5px 7px}.datos td{padding:6px 8px}.reporte{padding:8px}.seccion-bloque{margin-bottom:9px}.detalle-resumen{table-layout:fixed}.detalle-resumen td{width:50%;text-align:left!important;vertical-align:middle}.detalle-resumen strong{display:inline-block;margin-left:8px}.dato-icono{color:#967316;margin-right:6px;font-weight:bold}.servicio-meta{font-size:11px}
.resumen-cards{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:7px 6px}.resumen-card-celda{width:50%;padding:0;vertical-align:top;border:0}.resumen-card{border:1px solid #e4e5e8;background:#fff;padding:10px 12px;min-height:65px;page-break-inside:avoid}.resumen-card-icono{display:inline-block;color:#967316;background:#f6f4ee;padding:3px 7px;font-size:13px;margin-bottom:5px}.resumen-card-titulo{font-size:11px;color:#777;margin-bottom:5px}.resumen-card-valor{font-size:16px;font-weight:bold;color:#685727;line-height:1.4;overflow-wrap:break-word}.resumen-card-alerta{color:#ae5748}@media print{.resumen-card{padding:8px 10px}.resumen-card-valor{font-size:13px}.resumen-cards{border-spacing:5px}.resumen-card-titulo{font-size:10px}}
.resumen-card{background:#fff;border-color:#e5e7e9;border-left:3px solid #b9aa7d}.resumen-card-titulo{color:#6d7378}.resumen-card-valor{color:#454d55}.resumen-card-icono{background:#f4f1e8;color:#8b773e}.resumen-card--alerta{background:#fcf8f7;border-left-color:#bc847a}.resumen-card--alerta .resumen-card-valor{color:#a46155}.resumen-card--alerta .resumen-card-icono{background:#f5eae6;color:#a46155}.resumen-card--positivo{background:#f8fbf9;border-left-color:#87a895}.resumen-card--positivo .resumen-card-valor{color:#467762}.resumen-card--positivo .resumen-card-icono{background:#eaf2ed;color:#467762}.resumen-card--azul{background:#f9fafc;border-left-color:#8aa0b4}.resumen-card--azul .resumen-card-valor{color:#526d84}.resumen-card--azul .resumen-card-icono{background:#eaf0f5;color:#526d84}.seccion-titulo{background:#f5f4f0;border-left-color:#b2a06c;color:#615b4c}.aviso{background:#faf6e9;border-left-color:#b5a16a;color:#746748}
@media print{.resumen-card{background:#fff!important;border:1px solid #d8d8d8;border-left:2px solid #999}.resumen-card-valor{font-size:14px;color:#333!important}.resumen-card-titulo{color:#555}.seccion-titulo{background:#f5f5f5;color:#333}.resumen-cards tr{page-break-inside:avoid}}
.servicios-filas{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:6px 0}.servicios-columna{width:50%;vertical-align:top;padding:0;border:0}.servicios-columna .servicio{margin:4px 0 7px}.servicios-filas>tbody>tr{page-break-inside:avoid}.servicios-columna .servicio h3{overflow-wrap:break-word}
/* El borde vive en la celda: ambas fichas comparten la altura real de la fila. */
.servicios-filas{border-spacing:7px 8px}.servicios-filas .servicios-columna{padding:8px;border:1px solid #e3dfd5;background:#fff;vertical-align:top}.servicios-filas .servicios-columna .servicio{border:0;padding:0;margin:0;min-height:0}.servicios-columna .servicio-cabecera{min-height:48px;box-sizing:border-box;padding:8px}.servicios-columna .servicio table{table-layout:fixed}.servicios-columna .servicio td{vertical-align:middle;overflow-wrap:break-word}.servicios-columna .servicio h3{margin:0 0 6px;line-height:1.35}.servicios-columna .servicio-meta{line-height:1.45}
.resumen-secciones{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:7px 0}.resumen-seccion-columna{width:50%;vertical-align:top;padding:0;border:0}.resumen-secciones>tbody>tr{page-break-inside:avoid}.resumen-secciones .resumen-cards{border-spacing:4px}.resumen-secciones .resumen-card{padding:7px 8px}.resumen-secciones .resumen-card-valor{font-size:13px}@media print{.resumen-secciones{border-spacing:4px 0}.resumen-secciones .resumen-card{padding:4px 5px}.resumen-secciones .resumen-card-valor{font-size:9px}.resumen-secciones .resumen-card-titulo{font-size:8px}}
</style>@include('reportes.temporales_visor_excel.compartidos.paginacion')
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'resumen_dia', 'altoMarcaAgua' => 850])
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
<?php
$fechaHumana=function($valor,$hora=false){
 if(!$valor)return 'No disponible';
 try{$f=\Carbon\Carbon::parse($valor,'America/Mazatlan')->locale('es');return $f->translatedFormat('j \d\e F \d\e Y').($hora?', '.$f->format('g:i').' '.($f->hour<12?'a. m.':'p. m.'):'');}catch(\Throwable $e){return $valor;}
};
$generado=$datos['dashboard']['meta']['generadoEn']??null;
?>
<h1>{{ $datos['titulo'] }}</h1><p class="nota">{{ $datos['periodo'] }} · Consultado al {{ $fechaHumana($generado,true) }}</p>

<h2 class="seccion-titulo"><span class="icono-seccion">✦</span>Servicios funerarios en proceso</h2>
<p class="nota-alcance">Servicios incluidos actualmente en el dashboard. El saldo corresponde a la operación vigente relacionada.</p>
<table class="servicios-filas"><tbody>
@forelse(array_chunk($datos['dashboard']['servicios_funerarios'] ?? [],2) as $pareja)
<tr>
@foreach($pareja as $servicio)
<td class="servicios-columna" @if(count($pareja)===1) colspan="2" @endif>
<div class="servicio">
<div class="servicio-cabecera"><h3>{{ $servicio['nombre_afectado'] ?? 'Nombre no disponible' }}</h3>
<div class="servicio-meta">Núm. servicio: {{ $servicio['id'] ?? 'No disponible' }} · Velación: {{ !empty($servicio['velacion_b']) ? ($servicio['lugarvelacion']['lugar'] ?? $servicio['direccion_velacion'] ?? 'Lugar por definir') : 'Sin velación' }}</div></div>
<table><tbody>
<tr><td colspan="2">{{ !empty($servicio['contratante_temporal_reporte']) ? 'Contratante temporal' : 'Cliente' }}: <b>{{ trim($servicio['cliente_reporte']['nombre'] ?? '') ?: 'No disponible' }}</b></td></tr>
@if(!empty($servicio['contratante_temporal_reporte']) && !empty($servicio['cliente_reporte']['parentesco']))
<tr><td colspan="2">Parentesco: <b>{{ $servicio['cliente_reporte']['parentesco'] }}</b></td></tr>
@endif
<tr><td colspan="2">Contacto: <b>{{ implode(' / ',array_unique(array_filter([trim($servicio['cliente_reporte']['celular'] ?? ''),trim($servicio['cliente_reporte']['telefono'] ?? '')]))) ?: 'No disponible' }}</b></td></tr>
<tr><td>✦ Cremación: <b>{{ !empty($servicio['cremacion_b']) ? 'Sí' : 'No' }}</b></td><td>◇ Inhumación: <b>{{ !empty($servicio['inhumacion_b']) ? 'Sí' : 'No' }}</b></td></tr>
<tr><td>➜ Traslado: <b>{{ !empty($servicio['traslado_b']) ? 'Sí' : 'No' }}</b></td><td>✝ Ceremonia religiosa: <b>{{ !empty($servicio['misa_b']) ? 'Sí' : 'No' }}</b></td></tr>
<tr><td>▤ Contrato: <b>{{ !empty($servicio['datosoperacion']) ? 'Con operación vigente' : 'Sin contrato vigente' }}</b></td><td class="{{ ($servicio['datosoperacion']['saldo'] ?? 0)>0 ? 'saldo' : 'si' }}">$ Saldo pendiente: <b>{{ isset($servicio['datosoperacion']['saldo']) ? '$ '.number_format($servicio['datosoperacion']['saldo'],2).' MXN' : 'No disponible' }}</b></td></tr>
@foreach(['fechahora_solicitud'=>'Solicitud','fechahora_cremacion'=>'Cremación programada','fechahora_inhumacion'=>'Inhumación programada','fechahora_traslado'=>'Traslado programado','fechahora_misa'=>'Ceremonia programada'] as $campo=>$nombre)
@if(!empty($servicio[$campo]))<tr><td colspan="2">◷ {{ $nombre }}: {{ $fechaHumana($servicio[$campo],true) }}</td></tr>@endif
@endforeach
</tbody></table>
</div></td>
@endforeach
</tr>
@empty
<tr><td><p class="nota">No hay servicios en proceso para mostrar.</p></td></tr>
@endforelse
</tbody></table>
<?php
$seccionesResumen=\App\Exports\ResumenDashboardExport::secciones($datos);
$ordenResumen=['Inventario','Facturación','Seguimientos','Pendientes','Ventas y saldos'];
?>
<table class="resumen-secciones"><tbody>
@foreach($ordenResumen as $titulo)
<?php $filas=$seccionesResumen[$titulo]??[]; ?>
@if(in_array($titulo,['Inventario','Seguimientos','Ventas y saldos']))<tr>@endif
<td class="resumen-seccion-columna" @if($titulo==='Ventas y saldos') colspan="2" @endif>
<section class="seccion-bloque"><h2 class="seccion-titulo"><span class="icono-seccion">{{ ['Inventario'=>'▣','Facturación'=>'▤','Seguimientos'=>'◷','Ventas y saldos'=>'$','Pendientes'=>'!'][$titulo] ?? '•' }}</span>{{ $titulo }}</h2>
@if($titulo === 'Ventas y saldos')<p class="nota-alcance">Ventas y por cobrar del mes actual · Vencido acumulado histórico a la consulta. La fila Todas las operaciones es el total y no se suma a las individuales.</p>@endif
@if($titulo === 'Ventas y saldos')
<table class="ventas-impresion-compacta"><thead><tr><th>Operación</th><th>Ventas del mes</th><th>Por cobrar del mes</th><th>Vencido histórico</th></tr></thead><tbody>
@foreach($datos['dashboard']['ventasSaldos'] ?? [] as $operacion)
<tr class="{{ (string)$operacion['id']==='todas' ? 'ventas-total' : '' }}"><th>{{ $operacion['nombre'] }}</th>@foreach(['ventas','porCobrar','vencido'] as $campo)<td>$ {{ number_format($operacion[$campo],2,'.',',') }} <small>MXN</small></td>@endforeach</tr>
@endforeach
</tbody></table>
<div class="ventas-tarjetas-pantalla">
@foreach($datos['dashboard']['ventasSaldos'] ?? [] as $operacion)
<div style="page-break-inside:avoid;margin-bottom:9px"><h3 style="font-size:12px;margin:6px 8px;color:#555">{{ $operacion['nombre'] }}</h3>
<table class="resumen-cards"><tr>
@foreach(['ventas'=>'Ventas del mes','porCobrar'=>'Por cobrar del mes','vencido'=>'Vencido acumulado histórico'] as $campo=>$nombre)
<td style="width:33.333%;padding:0;vertical-align:top"><div class="resumen-card {{ $campo==='vencido' ? 'resumen-card--alerta' : 'resumen-card--neutro' }}"><div class="resumen-card-titulo">{{ $nombre }}</div><div class="resumen-card-valor">$ {{ number_format($operacion[$campo],2,'.',',') }} <small style="font-size:9px">MXN</small></div></div></td>
@endforeach
</tr></table></div>
@endforeach
</div>
@else
<table class="resumen-cards"><tbody>
@foreach(array_chunk($filas,2) as $par)<tr>
@foreach($par as $fila)
<td class="resumen-card-celda"><div class="resumen-card {{ strpos(mb_strtolower($fila[0]),'vencido')!==false ? 'resumen-card--alerta' : (strpos(mb_strtolower($fila[0]),'cubierto')!==false || strpos(mb_strtolower($fila[0]),'realizados')!==false ? 'resumen-card--positivo' : ($titulo==='Facturación' ? 'resumen-card--azul' : 'resumen-card--neutro')) }}">
<span class="resumen-card-icono">{{ strpos(mb_strtolower($fila[0]),'vencido')!==false ? '!' : (strpos(mb_strtolower($fila[0]),'factur')!==false ? '▤' : '▣') }}</span>
<div class="resumen-card-titulo">{{ $fila[0] }}</div>
<div class="resumen-card-valor {{ strpos(mb_strtolower($fila[0]),'vencido')!==false ? 'resumen-card-alerta' : '' }}">{{ in_array($fila[0],['Firma electrónica válida hasta','Último ajuste']) ? $fechaHumana($fila[1]) : $fila[1] }}</div>
</div></td>
@endforeach
@if(count($par)===1)<td class="resumen-card-celda"></td>@endif
</tr>@endforeach</tbody></table>
@endif
</section></td>
@if(in_array($titulo,['Facturación','Pendientes','Ventas y saldos']))</tr>@endif
@endforeach
</tbody></table>
<footer class="pie">{{ \App\Exports\NombreEmpresaReportes::formatear($e['nombre_comercial'] ?? null) }} · Resumen del día · {{ $fechaHumana($generado,true) }}</footer>
</main></div></body></html>
<style>@media print{@page{size:letter portrait;margin:7mm}.reporte{padding:0}body{font-size:9px}.cabecera{margin-bottom:5px}.cabecera td{padding-bottom:5px}.logo{width:115px}h1{font-size:15px;margin:0}.empresa,.nota,.nota-alcance{font-size:8px}.seccion-titulo{margin:6px 0 3px;padding:4px 7px;font-size:11px}.servicios-columna .servicio td{font-size:8px;padding:3px}.servicios-columna .servicio h3{font-size:10px}.servicios-columna .servicio-meta{font-size:8px}.servicios-columna .servicio-cabecera{min-height:0;padding:5px}.resumen-card{padding:5px 7px;min-height:0}.resumen-card-icono{display:none}.resumen-card-titulo{font-size:8px;margin-bottom:2px}.resumen-card-valor{font-size:10px}.resumen-cards{border-spacing:3px}.seccion-bloque{margin-bottom:4px}.aviso{padding:5px 7px;font-size:8px}.pie{font-size:8px;margin-top:5px}}</style><style>
.ventas-impresion-compacta{display:none}
@media print{
 .ventas-tarjetas-pantalla{display:none!important}
 .ventas-impresion-compacta{display:table;width:100%;border-collapse:collapse;table-layout:fixed;font:9px Arial,sans-serif;margin:3px 0}
 .ventas-impresion-compacta th,.ventas-impresion-compacta td{padding:4px 5px;border-bottom:1px solid #ddd;vertical-align:middle}
 .ventas-impresion-compacta th{text-align:left;font-weight:normal}
 .ventas-impresion-compacta td{text-align:right;white-space:nowrap}
 .ventas-impresion-compacta th:first-child{width:32%}
 .ventas-impresion-compacta thead{background:#f3f3f3;display:table-header-group}
 .ventas-impresion-compacta thead th{font-weight:bold}
 .ventas-impresion-compacta .ventas-total{background:#f6f4ee;font-weight:bold}
 .ventas-impresion-compacta .ventas-total th{font-weight:bold}
 .ventas-impresion-compacta small{font-size:7px;color:#666}
 .ventas-impresion-compacta tr{page-break-inside:avoid}
}
</style><style>
@media print{
 .ventas-impresion-compacta thead th:not(:first-child){text-align:right}
 .ventas-impresion-compacta th:first-child{width:32%;text-align:left}
 .ventas-impresion-compacta th:not(:first-child),.ventas-impresion-compacta td{width:22.666%;padding-left:6px;padding-right:6px}
 .ventas-impresion-compacta td{font-variant-numeric:tabular-nums}
 .ventas-impresion-compacta small{display:inline-block;min-width:17px;text-align:left;margin-left:2px}
}
</style>