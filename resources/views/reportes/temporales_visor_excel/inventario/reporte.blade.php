{{--
 Vista HTML/PDF de inventario; recibe columnas/filas del exportador para mantener el mismo contenido.
 Dónde ajustar: Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print
 pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.
 Relacionado: VisorReportes.vue; controlador del reporte; exportador Excel equivalente
 Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
--}}
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $resumen['reporte'] }}</title>
<style>
*{box-sizing:border-box}html{min-height:100%}body{margin:0;padding:0;min-height:100vh;font:13px Arial,sans-serif;color:#343a40;background:#f5f5f5}.reporte{width:100%;max-width:none;min-height:100vh;margin:0;background:#fff;padding:12px}
.cabecera{display:flex;align-items:center;justify-content:space-between;gap:24px;border-bottom:2px solid #b18b1e;padding-bottom:12px}.logo{display:block;width:190px;height:auto;flex-shrink:0}.titulo{text-align:right}h1{font-size:20px;margin:0 0 6px}.fecha,.nota{color:#70757b;font-size:11px;margin:4px 0}
.resumen{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px;margin:14px 0}.indicador{padding:9px 12px;text-align:center;background:#f6f4ee;color:#80672c}.indicador strong{display:block;font-size:19px;margin-top:4px}.articulos{background:#f1f4f7;color:#50667b}.servicios{background:#f4f2f6;color:#6d617b}.bajo{background:#faf1f1;color:#89565b}.sobre{background:#faf6ec;color:#826b42}
.valor{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin:12px 0}.valor strong{color:#967316;font-size:17px}.tabla{overflow:auto}table{width:100%;border-collapse:collapse;min-width:850px;font-size:12px}th{background:#343a40;color:white;padding:10px 7px}td{padding:8px 7px;text-align:center;border-bottom:1px solid #eee;overflow-wrap:anywhere}tbody tr:nth-child(even){background:#f3f4f5}td.estado{font-weight:bold}.estado-bajo{color:#89565b}.estado-sobre{color:#826b42}.estado-rango{color:#4d705c}.estado-servicio{color:#6d617b}.pie{margin-top:14px;padding-top:9px;border-top:1px solid #ddd;display:flex;justify-content:space-between;font-size:10px;color:#777}thead{display:table-header-group}
@media(max-width:700px){body{padding:0}.reporte{padding:8px}.cabecera{align-items:flex-start;gap:12px}.logo{width:135px}.titulo h1{font-size:16px}.resumen{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media print{@page{size:A4 landscape;margin:10mm}body{background:white;padding:0;min-height:0;font-size:10px;print-color-adjust:exact;-webkit-print-color-adjust:exact}.reporte{max-width:none;min-height:0;padding:0}.cabecera{padding-bottom:8px}.logo{width:150px}h1{font-size:16px}.resumen{grid-template-columns:repeat(5,minmax(0,1fr));margin:10px 0}.indicador{padding:6px}.indicador strong{font-size:15px}.valor strong{font-size:13px}.tabla{overflow:visible}table{min-width:0;table-layout:fixed;font-size:9px}th,td{padding:5px 4px}tr{break-inside:avoid;page-break-inside:avoid}.cabecera,.resumen,.valor{break-inside:avoid}.nota{font-size:9px}}
/* wkhtmltopdf usa un motor antiguo: tablas y flotantes en lugar de Grid/Flex. */
.modo-pdf .cabecera{display:block;position:relative;height:70px}
.modo-pdf .logo-contenedor{float:left;width:25%}
.modo-pdf .titulo{float:right;width:73%}
.modo-pdf .resumen{clear:both;display:table;width:100%;border-spacing:4px 0;table-layout:fixed}
.modo-pdf .indicador{display:table-cell;width:20%;vertical-align:middle}
.modo-pdf .valor{display:block;min-height:45px}
.modo-pdf .valor>div{float:left;width:73%}
.modo-pdf .valor>strong{float:right;text-align:right;width:25%;padding-top:3px}
.modo-pdf .tabla{clear:both}
.modo-pdf .pie{display:block}
.modo-pdf .pie span:last-child{float:right}
</style>
@include('reportes.temporales_visor_excel.compartidos.paginacion')
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'inventario', 'altoMarcaAgua' => 850])
</head>
<body class="{{ !empty($modoPdf) ? 'modo-pdf' : '' }}"><div class="marca-agua-contenido" @if(empty($modoPdf)) data-marca-visor @endif><main class="reporte">
<header class="cabecera"><div class="logo-contenedor"><img class="logo" src="{{ $logo }}" alt="Aeternus Funerales"></div><div class="titulo"><h1>{{ $resumen['reporte'] }}</h1><p class="fecha">Datos consultados: {{ $fecha }} · Moneda: MXN</p></div></header>
<section class="resumen" aria-label="Resumen del inventario">
@foreach ([['Conceptos','total_articulos',''],['Artículos','tipo_articulo','articulos'],['Servicios','tipo_servicio','servicios'],['Bajo mínimo','bajo_minimo','bajo'],['Sobre máximo','sobre_maximo','sobre']] as $indicador)
<div class="indicador {{ $indicador[2] }}">{{ $indicador[0] }}<strong>{{ number_format($resumen[$indicador[1]] ?? 0, 0, '.', ',') }}</strong></div>
@endforeach
</section>
<div class="valor"><div><b>Valor de artículos inventariables a precio de venta</b><p class="nota">Existencias × precio de venta. No incluye servicios.</p></div><strong>$ {{ number_format($resumen['costo_total_inventario'] ?? 0, 2, '.', ',') }}</strong></div>
<div class="tabla"><table><colgroup><col style="width:6%"><col style="width:20%"><col style="width:9%"><col style="width:6%"><col style="width:6%"><col style="width:15%"><col style="width:14%"><col style="width:14%"></colgroup><thead><tr>@foreach ($columnas as $columna)<th scope="col">{{ $columna }}</th>@endforeach</tr></thead><tbody>
@forelse ($filas as $fila)
<tr>@foreach ($fila as $indice => $valor)
@if ($indice === 5)
<td class="estado {{ ['Bajo mínimo'=>'estado-bajo','Sobre máximo'=>'estado-sobre','En rango'=>'estado-rango','Servicio'=>'estado-servicio'][$valor] ?? '' }}">{{ $valor }}</td>
@elseif ($indice >= 6)
<td>$ {{ number_format($valor ?? 0, 2, '.', ',') }}</td>
@elseif ($indice >= 2 && $indice <= 4)
<td>{{ number_format($valor ?? 0, ($valor ?? 0) == floor($valor ?? 0) ? 0 : 2, '.', ',') }}</td>
@else
<td>{{ $valor }}</td>
@endif
@endforeach</tr>
@empty
<tr><td colspan="8">No hay conceptos para el filtro seleccionado.</td></tr>
@endforelse
</tbody></table></div>
<footer class="pie"><span>Aeternus Funerales · Control de inventario</span><span>{{ count($filas) }} registros</span></footer>
</main></div></body></html>
