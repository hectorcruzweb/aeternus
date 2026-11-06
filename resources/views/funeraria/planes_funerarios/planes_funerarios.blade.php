<!doctype html><html lang="{{ App::isLocale('es') ? 'es' : 'en' }}"><head><meta charset="UTF-8"><title>{{ __('funeraria/pdf_plan.planes_funerarios') }}</title></head><body class="catalogo-fichas">
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
/* La envoltura completa no debe tapar la trama entre fichas o tras un salto.
   Solo el contenido real se pinta de blanco. */
.catalogo-fichas .marca-agua-contenido{background-color:transparent!important;border-color:transparent!important}
.catalogo-fichas .cabecera,.catalogo-fichas .titulo,.catalogo-fichas .cierre-catalogo{background-color:#fff}
</style>
{{-- Mismas reglas originales: planes activos con precios. get_planes() define
     financiamiento=1 como pago único. El pago único muestra solo modalidad y precio total, sin datos de enganche
     ni mensualidades. Las restantes modalidades conservan todos sus importes
     en la tabla de financiamiento.
     No se filtran precios ni se cambian los cálculos del controlador. --}}
<div class="marca-agua-contenido">
<header class="cabecera"><div><img class="logo" src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="empresa"><strong>{{ mb_strtoupper($empresa->nombre_comercial, 'UTF-8') === 'AETERNUS FUNERALES' ? 'Aeternus Funerales' : $empresa->nombre_comercial }}</strong><span>{{ $empresa->razon_social }}</span><span>{{ mb_convert_case(mb_strtolower($empresa->calle, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} {{ $empresa->num_ext }}@if(trim($empresa->colonia)) · {{ mb_convert_case(mb_strtolower($empresa->colonia, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }}@endif</span><span>{{ $empresa->ciudad }}, {{ $empresa->estado }} · {{ $empresa->telefono }}</span></div></header>
<div class="titulo"><div><span class="etiqueta">{{ App::isLocale('es') ? 'PREVISIÓN FUNERARIA' : 'FUNERAL PRE-PLANNING' }}</span><h1>{{ __('funeraria/pdf_plan.planes_funerarios') }}</h1><div>{{ App::isLocale('es') ? 'Modalidades de pago y financiamiento · Importes en MXN' : 'Payment and financing options · Amounts in MXN' }}</div></div><div class="fecha">{{ App::isLocale('es') ? 'Consulta' : 'Prepared on' }}<br>{{ \Carbon\Carbon::now('America/Mazatlan')->locale(App::getLocale())->isoFormat('D/MMM/YYYY') }}</div></div>
@foreach($datos as $plan)
@if($plan['status']==1 && count($plan['precios'])>0)
<?php
    $contado = array_filter($plan['precios'], function ($precio) { return isset($precio['financiamiento']) && (int) $precio['financiamiento'] === 1; });
    $financiados = array_filter($plan['precios'], function ($precio) { return !isset($precio['financiamiento']) || (int) $precio['financiamiento'] !== 1; });
?>
<section class="ficha"><h2>{{ App::isLocale('es') ? $plan['plan'] : $plan['plan_ingles'] }}</h2>
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
</section>
@endif
@endforeach
<div class="cierre-catalogo"><aside class="notas"><h3>{{ __('funeraria/pdf_plan.nota') }}</h3>
@foreach(['parrafo1','parrafo2','parrafo3','parrafo4'] as $nota)
<p>• {{ __('funeraria/pdf_plan.'.$nota) }}</p>
@endforeach
</aside>
<div class="contacto-destacado"><img class="icono-contacto" src="{{ base_path('resources/assets/images/callus.png') }}" alt=""><span>{{ App::isLocale('es') ? 'Información y contratación' : 'Information and enrollment' }}</span><strong>{{ $empresa->telefono }}</strong></div>
@if($empresa->facebook!='')<div class="red-social"><img src="{{ base_path('resources/assets/images/facebook.png') }}" alt="Facebook">{{ $empresa->facebook }}</div>@endif
</div></div>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'planes_venta', 'altoMarcaAgua' => 1100])

<script>(function(){
 function completar(){
  if(document.querySelector('.solicitud-visor-pie'))return;
  // Qt informa ancho cero antes de paginar; fijarlo evita medir las fichas
  // como columnas estrechas y crear hojas adicionales por una altura incorrecta.
  if(!document.body.clientWidth)document.body.style.width='935px';
  var ancho=document.body.clientWidth||935;
  var contenido=document.querySelector('.marca-agua-contenido');
  if(!contenido)return;
  var altoPagina=ancho*(238.6/175.1);
  var limite=contenido.offsetTop+contenido.offsetHeight;
  var alto=Math.ceil(limite/altoPagina)*altoPagina-8;
  document.documentElement.style.minHeight=alto+'px';document.body.style.minHeight=alto+'px';
 }
 if(document.readyState==='complete')setTimeout(completar,100);
 else window.addEventListener('load',function(){setTimeout(completar,100);});
})();</script></body></html>
