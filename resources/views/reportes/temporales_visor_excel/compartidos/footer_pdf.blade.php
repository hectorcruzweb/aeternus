{{--
 Pie PDF común: paginación abreviada, dominio y fecha de generación.
 Dónde ajustar: Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print
 pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.
 Relacionado: VisorReportes.vue; controlador del reporte; exportador Excel equivalente
 Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
--}}
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
html,body{margin:0;padding:0;font-family:Arial,sans-serif;font-size:7pt;font-weight:normal;color:#777}
table{width:100%;border-collapse:collapse}td{padding:3px 0 0;vertical-align:bottom;line-height:1.3}
.paginas{text-align:right;white-space:nowrap;width:20%}
</style>
<script>
// wkhtmltopdf entrega los números de página al footer mediante parámetros locales.
function completarPaginas(){
  var valores={}, partes=window.location.search.substring(1).split('&');
  for(var i=0;i<partes.length;i++){var par=partes[i].split('=');valores[decodeURIComponent(par[0])]=decodeURIComponent(par.slice(1).join('='));}
  document.getElementById('pagina').textContent=valores.page||'';
  document.getElementById('total').textContent=valores.topage||'';
}
</script>
@include('reportes.temporales_visor_excel.compartidos.paginacion')
</head>
<body onload="completarPaginas()"><table><tr>
<td>{{ $dominio }} · Generado: {{ $fechaImpresion }}</td>
<td class="paginas">Pág. <span id="pagina"></span>/<span id="total"></span></td>
</tr></table></body>
</html>
