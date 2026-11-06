<!doctype html><html lang="{{ App::isLocale('es') ? 'es' : 'en' }}"><head><meta charset="UTF-8"><title>{{ App::isLocale('es') ? $datos['plan'] : $datos['plan_ingles'] }}</title></head><body class="catalogo-fichas">
@include('layouts.estilos')
<style>
html body.catalogo-fichas{font:13px Arial,sans-serif!important;color:#394246;margin:0}
.catalogo-fichas .cabecera,.catalogo-fichas .titulo,.catalogo-fichas .contado{display:table;width:100%;box-sizing:border-box}
.catalogo-fichas .cabecera>div,.catalogo-fichas .titulo>div,.catalogo-fichas .contado>div{display:table-cell;vertical-align:middle}
.catalogo-fichas .cabecera{border-bottom:2px solid #ad8b28;padding-bottom:16px}
.catalogo-fichas .logo{width:205px;height:auto;max-width:100%}
.catalogo-fichas .empresa{text-align:right;font-size:11px;line-height:1.6}
.catalogo-fichas .empresa span{display:block}
.catalogo-fichas .titulo{padding:20px 0}
.catalogo-fichas .etiqueta{font-size:11px;letter-spacing:1px;color:#987822}
.catalogo-fichas h1{font-size:28px;line-height:1.3;margin:5px 0;text-transform:none}
.catalogo-fichas .fecha{text-align:right;font-size:11px;color:#737d82}
.catalogo-fichas .ficha{border:1px solid #dfe3e5;padding:13px;margin-bottom:14px;page-break-inside:avoid;break-inside:avoid;background:#fff}
.catalogo-fichas h2{font-size:19px;line-height:1.4;margin:0 0 10px;color:#394246;text-transform:none;page-break-after:avoid}
.catalogo-fichas .contado{background:#f6f3e9;padding:12px 14px;margin-bottom:12px}
.catalogo-fichas .contado-detalle{font-size:11px;color:#6c7579;line-height:1.7}
.catalogo-fichas .contado-precio{text-align:right;white-space:nowrap;font-size:25px;font-weight:bold;color:#8b6d22}
.catalogo-fichas .contado-precio small{font-size:11px;font-weight:normal}
.catalogo-fichas .precios{width:100%;table-layout:fixed;border-collapse:collapse;font-size:12px}
.catalogo-fichas .precios th{font-size:11px;font-weight:normal;background:#f0f2f3;color:#566368;text-align:right;padding:8px 6px}
.catalogo-fichas .precios td{padding:6px 6px;border-bottom:1px solid #e4e7e9;text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums}
.catalogo-fichas .precios th:first-child,.catalogo-fichas .precios td:first-child{text-align:left;width:32%;white-space:normal}
.catalogo-fichas .precios td:nth-child(2){font-weight:bold}
.catalogo-fichas .precios tbody tr:nth-child(even){background:#f8f9fa}
.catalogo-fichas .precios thead{display:table-header-group}
.catalogo-fichas .precios tr,.catalogo-fichas .notas p{page-break-inside:avoid;break-inside:avoid}
.catalogo-fichas .cierre-catalogo{page-break-inside:avoid;break-inside:avoid}
.catalogo-fichas .notas{border-top:1px solid #dedbd3;padding-top:12px;margin-top:20px;font-size:12px;color:#667177}
.catalogo-fichas .notas h3{font-size:14px;color:#8b6d22;margin:0 0 8px;page-break-after:avoid}
.catalogo-fichas .notas p{font-size:12px;line-height:1.6;margin:6px 0;orphans:3;widows:3}
.catalogo-fichas .contacto{width:100%;border-collapse:collapse;border-top:1px solid #dedbd3;margin-top:16px;page-break-inside:avoid}
.catalogo-fichas .contacto td{vertical-align:middle;padding:12px 5px;font-size:12px}
.catalogo-fichas .contacto td:last-child{text-align:right}
.catalogo-fichas .contacto strong{font-size:18px;color:#8b6d22}
.catalogo-fichas .icono-contacto{width:25px;height:auto;vertical-align:middle;margin-right:8px}
.catalogo-fichas .red-social{font-size:12px;margin-top:8px;page-break-inside:avoid}
.catalogo-fichas .red-social img{width:22px;height:auto;vertical-align:middle;margin-right:8px}
/* Compensación visual del navegador frente al motor Qt del PDF. */
@supports(display:grid){.catalogo-fichas .logo{width:164px}.catalogo-fichas .empresa,.catalogo-fichas .fecha,.catalogo-fichas .etiqueta{font-size:8.8px}.catalogo-fichas h1{font-size:22.4px}.catalogo-fichas h2{font-size:15.2px}.catalogo-fichas .precios,.catalogo-fichas .notas p,.catalogo-fichas .contacto td{font-size:9.6px}.catalogo-fichas .precios th,.catalogo-fichas .contado-detalle{font-size:8.8px}.catalogo-fichas .contado-precio{font-size:20px}.catalogo-fichas .contado-precio small{font-size:8.8px}}
.catalogo-fichas .contacto-destacado{margin-top:22px;padding:16px 20px;background:#f6f2e7;border-top:2px solid #ad8b28;text-align:center;page-break-inside:avoid}
.catalogo-fichas .contacto-destacado span{display:inline-block;font-size:14px;color:#6b7478;vertical-align:middle}
.catalogo-fichas .contacto-destacado strong{display:block;margin-top:5px;font-size:30px;line-height:1.3;color:#8b6d22;letter-spacing:1px}
@supports(display:grid){.catalogo-fichas .contacto-destacado span{font-size:11.2px}.catalogo-fichas .contacto-destacado strong{font-size:24px}}
</style><style>
.catalogo-fichas .marca-agua-contenido{background-color:transparent!important;border-color:transparent!important}
.catalogo-fichas [data-contenido-ajustable]{background-color:#fff}
.catalogo-fichas .plan-hoja{position:relative;width:935px;height:300mm;overflow:hidden}
.catalogo-fichas [data-contenido-ajustable]{position:absolute;left:0;top:0;width:100%;transform-origin:top left;z-index:1}
.catalogo-fichas .marca-logo{position:absolute;top:35mm;right:0;width:55%;z-index:-1;pointer-events:none}
.catalogo-fichas .marca-logo svg{width:100%;height:180mm;display:block}
.catalogo-fichas .seccion-plan{padding:15px 0}
.catalogo-fichas .seccion-plan h2{font-size:18px;color:#8b6d22;margin-bottom:12px}
.catalogo-fichas .conceptos{width:100%;border-collapse:collapse;table-layout:fixed}
.catalogo-fichas .conceptos td{width:50%;vertical-align:top;padding:7px 15px 7px 0;font-size:14px;line-height:1.6}
.catalogo-fichas .conceptos img{width:15px;height:15px;vertical-align:middle;margin-right:7px}
.catalogo-fichas .nota-plan{padding:12px;background:#f7f6f2;font-size:12px;line-height:1.6;margin-bottom:15px}
.catalogo-fichas .precios{margin-top:12px;font-size:13px}
.catalogo-fichas .contado{margin-top:15px}
.catalogo-fichas .plan-hoja *{page-break-before:auto!important;page-break-after:auto!important;page-break-inside:auto!important}
@supports(display:grid){.catalogo-fichas .plan-hoja{width:187mm;height:232mm}.catalogo-fichas .conceptos td{font-size:11.2px}.catalogo-fichas .seccion-plan h2{font-size:14.4px}.catalogo-fichas .precios{font-size:10.4px}}
</style>{{-- Ficha de un plan: conserva todas las secciones, conceptos y precios originales.
El ajuste compartido reduce la escala solo cuando el contenido excede una carta.
El logo queda detrás del contenido; la leyenda de espacio libre usa planes_venta. --}}
<div class="marca-agua-contenido"><article class="plan-hoja" data-ajuste-una-pagina><div data-contenido-ajustable>
<div class="marca-logo" aria-hidden="true">{!! \App\Exports\MarcaTituloReportes::svg() !!}</div>
<header class="cabecera"><div><img class="logo" src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="empresa"><strong>{{ mb_strtoupper($empresa->nombre_comercial, 'UTF-8') === 'AETERNUS FUNERALES' ? 'Aeternus Funerales' : $empresa->nombre_comercial }}</strong><span>{{ $empresa->razon_social }}</span><span>{{ mb_convert_case(mb_strtolower($empresa->calle, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} {{ $empresa->num_ext }}@if(trim($empresa->colonia)) · {{ mb_convert_case(mb_strtolower($empresa->colonia, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }}@endif</span><span>{{ $empresa->ciudad }}, {{ $empresa->estado }} · {{ $empresa->telefono }}</span></div></header><div class="titulo"><div><span class="etiqueta">{{ App::isLocale('es') ? 'PLAN FUNERARIO · INFORMACIÓN COMERCIAL' : 'FUNERAL PLAN · COMMERCIAL INFORMATION' }}</span><h1>{{ App::isLocale('es') ? $datos['plan'] : $datos['plan_ingles'] }}</h1><div>{{ App::isLocale('es') ? 'Modalidades de pago · Importes en MXN' : 'Payment options · Amounts in MXN' }}</div></div><div class="fecha">{{ App::isLocale('es') ? 'Consulta' : 'Prepared on' }}<br>{{ \Carbon\Carbon::now('America/Mazatlan')->locale(App::getLocale())->isoFormat('D/MMM/YYYY') }}</div></div>
@foreach($datos['secciones'] as $seccion)
@if(count($seccion['conceptos'])>0)
<section class="seccion-plan"><h2>{{ App::isLocale('es') ? ($seccion['seccion'] !== 'incluye' ? 'En caso de ' : '').ucfirst($seccion['seccion']) : ($seccion['seccion'] !== 'incluye' ? 'In case of ' : '').ucfirst($seccion['seccion_ingles']) }}</h2>
<table class="conceptos">
@foreach(array_chunk($seccion['conceptos'],2) as $fila)
<tr>@foreach($fila as $concepto)<td><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACcAAAAsCAYAAADmZKH2AAAB8klEQVR4nO2YPS8EQRiAn9tcixKVhuD+gegkFHQoacSJj1ajkkvcP2AvudP5LAgqBX6CKMgR0fqIH6AhTjaZTcaY3dvb7Oxs4UkmuZnsJE/e+Xjn3tzaXB8BzAKTwBDQSTLcA2VgL8rHec1Yl5g8QvIMArtAO1CJI3cEDGMWF/gCamEfOUp/PgUxnyqwSAtyU5ooekuRi9maUQkTVOW8zS+zDjyQHAsBgsUoch1Kv06y1AIEqzpBVS4NalEFbchFFrQlF0lQd88lyZu41H0Kyj6uCQdXI/hhOnI3Sr8E9GlO65Jmbtm0XFXpTwOPQENpulTWY1ruFLiKO1mVqxu442aAyyTk/IxwL34ndShGxRPsBHiJOjEX8p6zQUPu2LznmvIvF5f/yKUduRngDrgFxjFEnMTv5UE5UW8CvcA3liOnihHxv4JxuWWNmMeaiajJchPSHvL2k05sK2D8EIN7Li8efd1ibAdok54xSyFiFVNivtyfnKYsn2tDzJfzygKrwD6/0UmlJibvuQNgheaspCWmnlZXRKXV00paV0klQDC1pWx2z/m1i0/RijbEwtLXtmhWccgwDhnGIcM4ZIeC0n/NityAqKPIXOc1tYwNoB/7uGrkShkROwPOs7KsSFz4RUVdreTJgtAzcCzqKWPAuzf4A85VYoKRXvpNAAAAAElFTkSuQmCC" alt="">{{ App::isLocale('es') ? $concepto['concepto'] : $concepto['concepto_ingles'] }}</td>@endforeach @if(count($fila)===1)<td></td>@endif</tr>
@endforeach
</table></section>
@endif
@endforeach
<?php
    $contado = array_filter($datos['precios'], function ($precio) { return isset($precio['financiamiento']) && (int) $precio['financiamiento'] === 1; });
    $financiados = array_filter($datos['precios'], function ($precio) { return !isset($precio['financiamiento']) || (int) $precio['financiamiento'] !== 1; });
?>

@foreach($contado as $precio)
<div class="contado"><div><strong>{{ App::isLocale('es') ? $precio['tipo_financiamiento'] : $precio['tipo_financiamiento_ingles'] }}</strong></div><div class="contado-precio">$ {{ number_format($precio['costo_neto'],2) }} <small>MXN</small></div></div>
@endforeach
@if(count($financiados)>0)
<table class="precios"><thead><tr><th>{{ __('funeraria/pdf_plan.financiamiento') }}</th><th>{{ __('funeraria/pdf_plan.costo_neto') }}</th><th>{{ __('funeraria/pdf_plan.enganche') }}</th><th>{{ __('funeraria/pdf_plan.saldo_restante') }}</th><th>{{ __('funeraria/pdf_plan.abono_mensual') }}</th></tr></thead><tbody>
@foreach($financiados as $precio)
<tr><td>{{ App::isLocale('es') ? $precio['tipo_financiamiento'] : $precio['tipo_financiamiento_ingles'] }}</td><td>{{ number_format($precio['costo_neto'],2) }}</td><td>{{ number_format($precio['pago_inicial'],2) }}</td><td>{{ number_format($precio['costo_neto']-$precio['pago_inicial'],2) }}</td><td>{{ number_format($precio['pago_mensual'],2) }}</td></tr>
@endforeach
</tbody></table>
@endif
{{-- Contacto comercial destacado: únicamente el teléfono registrado en la empresa. --}}
<div class="contacto-destacado"><img class="icono-contacto" src="{{ base_path('resources/assets/images/callus.png') }}" alt=""><span>{{ App::isLocale('es') ? 'Información y contratación' : 'Information and enrollment' }}</span><strong>{{ $empresa->telefono }}</strong></div>
@if($empresa->facebook!='')<div class="red-social"><img src="{{ base_path('resources/assets/images/facebook.png') }}" alt="Facebook">{{ $empresa->facebook }}</div>@endif
</div></article></div>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'planes_venta', 'altoMarcaAgua' => 1100])
<script>(function(){
 function ajustar(){
  var hoja=document.querySelector('[data-ajuste-una-pagina]');
  if(!hoja)return;
  var contenido=hoja.querySelector('[data-contenido-ajustable]');
  contenido.style.webkitTransform='none';contenido.style.transform='none';contenido.style.zoom=1;
  // Buscar la mayor escala que cabe. Ampliar el ancho interno compensa la
  // transformación, mantiene el ancho visible y redistribuye los párrafos.
  var bajo=0.000001,alto=1,escala=bajo;
  for(var i=0;i<22;i++){
   var prueba=(bajo+alto)/2;
   contenido.style.width=(hoja.clientWidth/prueba)+'px';
   var cabe=Math.max(contenido.scrollHeight,contenido.offsetHeight)*prueba<=hoja.clientHeight-4;
   if(cabe){escala=prueba;bajo=prueba;}else{alto=prueba;}
  }
  // Reserva adicional para redondeos de filas y bordes del motor Qt.
  escala=escala*0.995;
  contenido.style.width=(hoja.clientWidth/escala)+'px';
  // Zoom participa en la paginación de Qt; transform dejaba saltos internos
  // calculados a tamaño natural y podía empujar las notas fuera del área.
  contenido.style.zoom=escala;
 }
 if(document.readyState==='complete')ajustar();
 else window.addEventListener('load',ajustar);
 window.addEventListener('beforeprint',ajustar);
})();
</script><style>
/* Mayor legibilidad del plan individual; el ajuste de una página sigue activo. */
.catalogo-fichas .conceptos td{font-size:17px;line-height:1.65;padding-top:10px;padding-bottom:10px}
.catalogo-fichas .seccion-plan h2{font-size:21px}
.catalogo-fichas .precios{font-size:16px}
.catalogo-fichas .precios th{font-size:14px;padding-top:11px;padding-bottom:11px;white-space:normal;line-height:1.4}
.catalogo-fichas .precios td{padding-top:12px;padding-bottom:12px}
@supports(display:grid){
.catalogo-fichas .conceptos td{font-size:13.6px}
.catalogo-fichas .seccion-plan h2{font-size:16.8px}
.catalogo-fichas .precios{font-size:12.8px}
.catalogo-fichas .precios th{font-size:11.2px}
}
</style></body></html>