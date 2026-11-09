<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Guía del cliente</title></head><body class="guia-cliente-documento">
@include('layouts.estilos')
<style>
.guia-cliente-documento .stage{min-height:1100px;padding:12px 0;box-sizing:border-box}
.guia-cliente-documento .hoja{background:#fff;color:#30383b;font:14px/1.55 Arial,sans-serif;padding:30px;box-sizing:border-box;min-height:1040px;display:flex;flex-direction:column;border:1px solid #e1e1e1}
.guia-cliente-documento header{display:flex;justify-content:space-between;gap:18px;align-items:center;padding-bottom:20px;border-bottom:2px solid #ad8b28}
.guia-cliente-documento .marca{font-size:25px;letter-spacing:4px;color:#242424}.guia-cliente-documento .marca small{display:block;font-size:9px;letter-spacing:3px;text-align:center;color:#777}
.guia-cliente-documento .empresa{text-align:right;font-size:11px;line-height:1.6}.guia-cliente-documento .empresa span{display:block}
.guia-cliente-documento .titulo{display:flex;justify-content:space-between;align-items:center;gap:15px;padding:22px 0}.guia-cliente-documento .titulo span{font-size:10px;letter-spacing:1px;color:#8b712c}.guia-cliente-documento h2{margin:4px 0;font-size:27px;color:#343b3e}.guia-cliente-documento .folio{text-align:right;min-width:110px;font-size:12px}.guia-cliente-documento .folio strong{color:#8b712c;font-size:22px;margin-left:5px}.guia-cliente-documento .folio small{display:block;color:#737373}
.guia-cliente-documento h3{font-size:16px;display:flex;align-items:center;gap:8px;margin:0 0 16px;color:#6e5a23}.guia-cliente-documento h3 svg{width:18px;height:18px;flex-shrink:0}.guia-cliente-documento h4{margin:0 0 4px;font-size:14px}.guia-cliente-documento p{margin:0;font-size:13px;line-height:1.65}
.guia-cliente-documento .paso{display:flex;gap:13px;margin-bottom:20px}.guia-cliente-documento .paso b{width:27px;height:27px;flex-shrink:0;border-radius:50%;background:#f1eddf;color:#897024;text-align:center;line-height:27px}
.guia-cliente-documento .documentos{margin-top:24px;padding:20px;background:#f7f6f2;border-top:2px solid #b69b51}.guia-cliente-documento .documentos-grupo{margin-top:14px}.guia-cliente-documento .documentos-grupo h4{color:#5c6265;font-size:12px}.guia-cliente-documento .documento{display:flex;gap:9px;margin:9px 0;font-size:13px}.guia-cliente-documento .casilla{width:12px;height:12px;border:1px solid #aaa;background:#fff;flex-shrink:0;margin-top:4px}.guia-cliente-documento .documento small{display:block;color:#757575;font-size:11px}.guia-cliente-documento footer{margin-top:auto;padding-top:24px;display:flex;justify-content:space-between;font-size:10px;color:#7b7b7b}
.guia-cliente-documento .pasos .documentos{display:grid;grid-template-columns:1fr 1fr;gap:0 22px}.guia-cliente-documento .pasos .documentos h3,.guia-cliente-documento .pasos .responsable{grid-column:1/-1}
.guia-cliente-documento .lista .contenido{display:grid;grid-template-columns:1fr 1.1fr;gap:24px}.guia-cliente-documento .lista .documentos{margin-top:0;padding:17px}.guia-cliente-documento .lista .paso{display:block;padding-bottom:20px;border-bottom:1px solid #e6e6e6;margin-bottom:20px}.guia-cliente-documento .lista .paso b{display:block;margin-bottom:8px}.guia-cliente-documento .lista p{font-size:13px}

/* Carta vertical: tablas de presentación compatibles con Qt antiguo.
   Contenido y condiciones originales; casillas para revisión manual. */
html body.guia-cliente-documento {font-family:Arial,sans-serif!important;font-size:14px;margin:0;color:#30383b!important}
.guia-cliente-documento .hoja{display:block;border:0;padding:0;min-height:0;background:white}
.guia-cliente-documento header{display:table;width:100%;padding-bottom:16px}
.guia-cliente-documento header>div{display:table-cell;vertical-align:middle}
.guia-cliente-documento .marca{width:35%}
.guia-cliente-documento .marca img{width:210px;max-width:100%}
.guia-cliente-documento .empresa{font-size:13px}
.guia-cliente-documento .titulo{display:table;width:100%;padding:22px 0}
.guia-cliente-documento .titulo>div{display:table-cell;vertical-align:middle}
.guia-cliente-documento .folio{width:25%;font-size:14px}
.guia-cliente-documento h2{font-size:30px}
.guia-cliente-documento h3{display:block;font-size:19px}
.guia-cliente-documento h4{font-size:17px}
.guia-cliente-documento p{font-size:16px;line-height:1.65}
.guia-cliente-documento .paso{display:table;width:100%;margin-bottom:22px;page-break-inside:avoid}
.guia-cliente-documento .paso>b{display:table-cell;width:34px;height:auto;background:transparent;vertical-align:top;color:#897024;font-size:22px}
.guia-cliente-documento .paso>div{display:table-cell;padding-left:10px}
.guia-cliente-documento .documentos{margin-top:25px;page-break-inside:avoid}
.guia-cliente-documento .documentos-columnas{width:100%;table-layout:fixed;border-collapse:collapse}
.guia-cliente-documento .documentos-columnas td{width:50%;vertical-align:top;padding-right:18px}
.guia-cliente-documento .documento{display:block;font-size:16px}
.guia-cliente-documento .documento>div{display:inline}
.guia-cliente-documento .documento small{display:block;margin-left:20px;font-size:13px}
.guia-cliente-documento .casilla{display:inline-block;margin-right:7px}
.guia-cliente-documento .responsable{border-top:1px solid #dedbd2;padding-top:12px}
.guia-cliente-documento .icono-guia{display:inline-block;width:12px;height:14px;border:1px solid #9b813d;margin-right:8px;vertical-align:middle;position:relative}
.guia-cliente-documento .icono-guia:after{content:'';position:absolute;left:2px;right:2px;top:4px;height:3px;border-top:1px solid #9b813d;border-bottom:1px solid #9b813d}
/* Compensación visual del navegador, independiente de las medidas de Qt. */
@supports(display:grid){
.guia-cliente-documento .empresa{font-size:10.4px}
.guia-cliente-documento .marca img{width:168px}
.guia-cliente-documento h2{font-size:24px}
.guia-cliente-documento h3{font-size:15.2px}
.guia-cliente-documento h4{font-size:13.6px}
.guia-cliente-documento p,.guia-cliente-documento .documento{font-size:12.8px}
.guia-cliente-documento .documento small{font-size:10.4px}
.guia-cliente-documento .paso{margin-bottom:17.6px}
}
</style>
{{-- Empresa y servicio provienen de los datos originales. Sin consultas adicionales. --}}
<div class="marca-agua-contenido"><article class="hoja">
{{-- Reutiliza el trazado y alfa del título de propiedad. Cambiar watermark.png
     actualiza ambos documentos; esta capa no ocupa espacio ni altera el texto. --}}
<div class="guia-marca-titulo" aria-hidden="true">{!! \App\Exports\MarcaTituloReportes::svg() !!}</div>
<div class="guia-contenido-frente">
<header><div class="marca"><img src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="empresa"><strong>{{ $empresa->razon_social }}</strong><span>{{ mb_convert_case(mb_strtolower($empresa->calle, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} núm. ext. {{ $empresa->num_ext }}</span><span>@if(trim($empresa->colonia))Col. {{ mb_convert_case(mb_strtolower($empresa->colonia, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} · @endif C. P. {{ $empresa->cp }} · {{ $empresa->ciudad }}, {{ $empresa->estado }}</span><span>Tel. {{ $empresa->telefono }} · Fax {{ $empresa->fax }}</span></div></header>
<div class="titulo"><div><span>ACOMPAÑAMIENTO A LA FAMILIA</span><h2>Guía del cliente</h2></div><div class="folio"><small>{{ $datos['tipo_solicitud_texto'] }}</small><strong>{{ $datos['servicio_id'] }}</strong></div></div>
<div class="contenido"><section class="proceso"><h3><span class="icono-guia" aria-hidden="true"></span> Cómo se atenderá su servicio</h3>
@if(isset($escenarioGuia))
@foreach($escenarioGuia['pasos'] as $indice => $paso)
<div class="paso"><b>{{ $indice + 1 }}</b><div><h4>{{ $paso['titulo'] }}</h4><p>{{ $paso['texto'] }}</p></div></div>
@endforeach
@else
<div class="paso"><b>1</b><div><h4>Recepción y traslado</h4><p>Una vez recibida su solicitud, Aeternus Funerales realizará, a través del personal que disponga, los trámites y procedimientos necesarios para atender sus requerimientos y trasladar al fallecido a nuestras instalaciones.</p></div></div>
<div class="paso"><b>2</b><div><h4>Autorizaciones y certificado</h4><p>Nuestro personal le entregará los formatos de autorización y la información necesaria para elaborar el certificado de defunción, cuando corresponda. Con este documento se podrá elaborar el contrato del servicio funerario solicitado.</p></div></div>
<div class="paso"><b>3</b><div><h4>Formalización del servicio</h4><p>Una vez trasladado el fallecido, la persona responsable de contratar deberá acudir a nuestras instalaciones para firmar la autorización del contrato e indicar los servicios y artículos que se incluirán.</p></div></div>
@endif
</section><section class="documentos"><h3><span class="icono-guia" aria-hidden="true"></span> {{ isset($escenarioGuia) ? 'Documentación de referencia' : 'Documentos para el acta de defunción' }}</h3>@if(isset($escenarioGuia))<p style="font-size:12px;margin-bottom:12px">La siguiente lista corresponde al trámite del acta de defunción. Revise con el personal cuáles documentos aplican a su caso.</p>@endif<table class="documentos-columnas"><tr><td><div class="documentos-grupo"><h4>Del fallecido</h4>
<div class="documento"><span class="casilla"></span><div>Certificado médico de defunción</div></div>
<div class="documento"><span class="casilla"></span><div>Copia del acta de nacimiento</div></div>
<div class="documento"><span class="casilla"></span><div>Copia de identificación oficial</div></div></div>
</td><td><div class="documentos-grupo"><h4>Según su estado civil</h4><div class="documento"><span class="casilla"></span><div>Copia del acta de matrimonio<small>Si era casado(a).</small></div></div>
<div class="documento"><span class="casilla"></span><div>Copia del acta de divorcio<small>Si era divorciado(a).</small></div></div>
<div class="documento"><span class="casilla"></span><div>Copia del acta de defunción del cónyuge<small>Si era viudo(a).</small></div></div></div>
</td></tr></table><div class="documentos-grupo responsable"><h4>De quien realiza el trámite</h4><div class="documento"><span class="casilla"></span><div>Copia de identificación oficial de la persona que tramita el acta de defunción</div></div></div>
</section></div>

</div><!-- /guia-contenido-frente --></article></div>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'servicios_funerarios', 'altoMarcaAgua' => 1100])
<style>
.guia-cliente-documento .hoja{position:relative}
.guia-cliente-documento .guia-marca-titulo{position:absolute;top:30mm;right:0;width:58%;z-index:0;pointer-events:none}
.guia-cliente-documento .guia-marca-titulo svg{display:block;width:100%;height:180mm}
.guia-cliente-documento .guia-contenido-frente{position:relative;z-index:1}
</style>
</body></html>
