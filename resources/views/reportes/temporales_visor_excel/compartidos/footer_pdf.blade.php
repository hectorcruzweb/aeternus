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
</head>
<body onload="completarPaginas()"><table><tr>
<td>{{ $dominio }} · Generado: {{ $fechaImpresion }}</td>
<td class="paginas">Pág. <span id="pagina"></span>/<span id="total"></span></td>
</tr></table></body>
</html>
