{{-- Mapa físico: filas/columnas descendentes, límites reales por fila y propiedades únicas.
     Solo presentación. get_mapeado heredado no se modifica. Papel carta horizontal. --}}
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Mapa del cementerio</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#edf0f2;color:#343a40;font:11px Arial,sans-serif}.reporte{width:1050px;max-width:none;margin:20px auto;padding:28px;background:white;min-height:700px}.cabecera{width:100%;border-collapse:collapse;border-bottom:2px solid #a58828;margin-bottom:14px}.cabecera td{padding-bottom:12px;vertical-align:middle}.logo{width:155px;max-height:68px;object-fit:contain}.empresa{text-align:right;font-size:9px;line-height:1.4}h1{font-size:21px;margin:8px 0 5px}.nota{font-size:10px;line-height:1.45;color:#6f777d}.area{margin-top:16px}.area+.area{page-break-before:always;padding-top:5px}.area h2{padding:9px 10px;background:#f5f3ec;border-left:3px solid #a58828;font-size:15px;margin:12px 0}.resumen{width:100%;border-collapse:collapse;table-layout:fixed;margin:9px 0}.resumen td{padding:7px 9px;background:#f7f7f6;border-bottom:1px solid #ddd;font-size:10px}.resumen b{display:block;font-size:16px;margin-top:4px}.leyenda{font-size:10px;margin:10px 0;line-height:1.6}.leyenda span{margin-right:16px}.muestra{display:inline-block;width:12px;height:12px;border:1px solid #aaa;vertical-align:middle;margin-right:4px}.m-vendida{background:#fff1f1;border-color:#b44e50}.m-servicio{background:#e6eee9;border-color:#6f8b78}.m-noexiste{background:#ddd}.mapa{width:100%;table-layout:fixed;border-collapse:collapse;margin:9px 0 14px;font-size:10px;page-break-inside:avoid}.mapa th,.mapa td{border:1px solid #555;text-align:center;height:25px;padding:3px 1px}.mapa th{width:25px;background:#555e62;color:white;font-weight:normal}.mapa .vendida{color:#b4484c;text-decoration:line-through;background:#fffafa}.mapa .con-servicio{background:#e6eee9;color:#345a44;font-weight:bold}.mapa .inexistente{background:#ddd}.detalle{width:100%;table-layout:fixed;border-collapse:collapse;margin:12px 0 16px;font-size:10px}.detalle thead{display:table-header-group}.detalle th{padding:7px 5px;background:#42494c;color:white;text-align:center}.detalle td{padding:7px 5px;border-bottom:1px solid #ddd;vertical-align:top;overflow-wrap:break-word;word-wrap:break-word}.detalle tr:nth-child(even){background:#f7f7f7}tr{page-break-inside:avoid}.contacto{font-size:9px;color:#656f76;line-height:1.4}.derecha{text-align:right}.centro{text-align:center}.personas{margin:0;padding-left:14px;font-size:10px;line-height:1.5}.pie{margin-top:16px;border-top:1px solid #ddd;padding-top:7px;font-size:9px;color:#777}.modo-pdf{background:white}.modo-pdf .reporte{width:auto;margin:0;padding:0;min-height:0}.modo-pdf .pie{display:none}
@media print{@page{size:letter landscape;margin:10mm 10mm 14mm}body{background:white;-webkit-print-color-adjust:exact;print-color-adjust:exact}.reporte{width:auto;max-width:none;margin:0;padding:0;min-height:0}.mapa{font-size:9px}.mapa th,.mapa td{height:22px}.area+.area{padding-top:0}}
/* Impresión económica: áreas en flujo continuo, sin forzar una hoja por sección.
   Encabezado/resumen/mapa permanecen juntos siempre que quepan; el detalle puede
   continuar en otra hoja repitiendo sus columnas y manteniendo cada venta completa. */
.area{margin-top:10px}.area+.area{page-break-before:auto;padding-top:0}.mapa-bloque{page-break-inside:avoid;break-inside:avoid}.area h2{margin:8px 0;padding:6px 9px;font-size:14px}h3{margin:9px 0 5px;font-size:12px;page-break-after:avoid;break-after:avoid}
.cabecera{margin-bottom:9px}.cabecera td{padding-bottom:8px}.logo{width:140px;max-height:55px}h1{font-size:19px;margin:6px 0 4px}.resumen{margin:6px 0}.resumen td{padding:5px 7px}.resumen b{font-size:14px;margin-top:2px}.leyenda{margin:6px 0;font-size:9px}.mapa{margin:6px 0 9px}.mapa th,.mapa td{height:21px;padding:2px 1px}.detalle{margin:7px 0 10px;font-size:9px;line-height:1.3}.detalle th{padding:5px 4px}.detalle td{padding:5px 4px}.contacto{font-size:8px;line-height:1.35}.personas{padding:0;margin:0;font-size:9px;line-height:1.35;list-style:none}.personas li+li{margin-top:3px;padding-top:3px;border-top:1px solid #e5e5e5}.nota{margin:6px 0;font-size:9px}
@media print{.area+.area{page-break-before:auto}.mapa th,.mapa td{height:19px}.detalle td{padding:4px}.personas{font-size:9px}}
/* Mapas lineales: no estirar una única columna a todo el ancho de la hoja. */
.distribucion-lineal{width:100%;table-layout:fixed;border-collapse:collapse;page-break-inside:avoid}.distribucion-lineal>tbody>tr>td{vertical-align:top;padding:0;border:0}.distribucion-lineal .columna-posiciones{width:11%;padding-right:12px}.distribucion-lineal .columna-datos{width:89%}.columna-posiciones .mapa th{width:20px}.columna-posiciones .mapa{font-size:9px}.columna-posiciones .mapa td,.columna-posiciones .mapa th{height:18px}.lineal-corto{page-break-inside:avoid;break-inside:avoid}.posiciones-primero{width:140px;max-width:100%}.posiciones-primero .mapa th{width:25px}.lineal-largo{page-break-before:always}.lineal-largo:first-of-type{page-break-before:auto}.detalle-siguiente{page-break-before:always}.detalle-siguiente h2{margin-top:0}.columna-datos .detalle{margin-top:5px}.columna-datos h3{margin-top:9px}
@media print{.distribucion-lineal .columna-posiciones{width:11%}.distribucion-lineal .columna-datos{width:89%}.columna-posiciones .mapa td,.columna-posiciones .mapa th{height:17px}}
/* Posiciones y desglose comparten filas reales; no hay saltos forzados entre ambos. */
.lineal-integrada{table-layout:fixed}.lineal-integrada thead{display:table-header-group}.lineal-integrada td{vertical-align:middle}.lineal-integrada .lineal-lateral{padding:3px 1px;text-align:center;background:#555e62;color:white;font-size:9px}.lineal-integrada .lineal-posicion{text-align:center;border:1px solid #aaa;font-size:10px}.lineal-integrada .vendida{color:#b4484c;text-decoration:line-through;background:#fffafa}.lineal-integrada .con-servicio{background:#e6eee9;color:#345a44;font-weight:bold}.lineal-persona{font-size:9px;line-height:1.35}.lineal-persona+.lineal-persona{margin-top:3px;padding-top:3px;border-top:1px solid #e5e5e5}
/* Cada sección lineal comienza en hoja nueva. El thead identifica la sección
   en todas sus hojas; no se fuerza este salto en la vista previa continua. */
.lineal-integrada .lineal-seccion th{background:#f5f3ec;color:#343a40;text-align:left;border-left:3px solid #a58828;padding:6px 8px;font-size:11px}
.modo-pdf .area-lineal{page-break-before:always;break-before:page}
.modo-pdf .area-lineal:first-of-type{page-break-before:auto;break-before:auto}
@media print{.area.area-lineal{page-break-before:always;break-before:page}.area.area-lineal:first-of-type{page-break-before:auto;break-before:auto}}
/* Solo disponibilidad: hasta tres mapas lineales juntos, sin dividir la
   tarjeta entre hojas. Si falta altura, la sección completa pasa a otra hoja.
   El desglose de ventas/servicios conserva su inicio de sección en hoja nueva. */
.area-disponibilidad-lineal{display:inline-block;vertical-align:top;width:32%;margin-right:1%;page-break-inside:avoid;break-inside:avoid;page-break-before:auto}
.area-disponibilidad-lineal .posiciones-primero{width:100%}
.area-disponibilidad-lineal .resumen td{padding:5px 3px;font-size:8px}
.area-disponibilidad-lineal .resumen b{font-size:12px}
.area-disponibilidad-lineal .leyenda{font-size:8px;line-height:1.5}
.area-disponibilidad-lineal .leyenda span{display:inline-block;margin-right:7px}
.area-disponibilidad-lineal .mapa{width:100%}
@media screen and (max-width:760px){.area-disponibilidad-lineal{width:48%}}
@media print{.area.area-disponibilidad-lineal{width:32%;page-break-before:auto;break-before:auto;page-break-inside:avoid;break-inside:avoid}}
</style>
@include('reportes.temporales_visor_excel.compartidos.zoom_listados')
</head><body class="{{ !empty($modoPdf) ? 'modo-pdf' : '' }}"><main class="reporte">
<table class="cabecera"><tr><td>
@if($logo)
<img class="logo" src="{{ $logo }}" alt="Aeternus Funerales">
@endif
</td><td class="empresa"><b>{{ \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null) }}</b><br>{{ $empresa['razon_social'] ?? '' }}<br>{{ \App\Exports\DireccionReportes::formatear($empresa) }}<br>Tel. {{ $empresa['telefono'] ?? '' }} · {{ mb_strtolower($empresa['email'] ?? '', 'UTF-8') }}</td></tr></table>
<h1>Mapa visual del cementerio</h1><div class="nota"><b>{{ $modalidad }}</b> · Consulta: {{ $fecha }}</div>
<p class="nota">Disponibilidad para venta de propiedades. Una propiedad vendida puede tener espacio para más de una persona.</p>
@forelse($areas as $area)
<section class="area {{ !$area['terraza'] ? ($contenido === 'disponibilidad' ? 'area-disponibilidad-lineal' : 'area-lineal') : '' }}"><div class="mapa-bloque"><h2>{{ $area['nombre'] }}</h2>
<table class="resumen"><tr><td>Propiedades del área<b>{{ number_format($area['total']) }}</b></td><td>Vendidas<b>{{ number_format($area['vendidos']) }}</b></td><td>Disponibles para venta<b>{{ number_format($area['disponibles']) }}</b></td><td>Con servicios registrados<b>{{ number_format($area['conServicios']) }}</b></td></tr></table>
<div class="leyenda"><span><i class="muestra"></i>Disponible</span><span><i class="muestra m-vendida"></i>Número tachado: vendida</span><span><i class="muestra m-servicio"></i>Con servicios registrados</span><span><i class="muestra m-noexiste"></i>Posición inexistente</span></div>
@if(!$area['terraza'] && $contenido !== 'disponibilidad')
</div>
@include('reportes.temporales_visor_excel.cementerio.mapa_lineal')
@else
<div class="{{ !$area['terraza'] ? 'posiciones-primero' : '' }}">
@include('reportes.temporales_visor_excel.cementerio.mapa_cuadricula')
</div>
</div>
@include('reportes.temporales_visor_excel.cementerio.mapa_detalle')
@endif
</section>
@empty
<p>No se encontraron áreas con la selección indicada.</p>
@endforelse
<div class="pie">{{ $dominio }} · Generado: {{ $fecha }}</div>
</main></body></html>
