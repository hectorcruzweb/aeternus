<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Nota de Venta en General</title>
<style>
/* Comprobante comercial aprobado: equivalentes en tablas para WebKit y navegador.
   Mantener concepto y cierre completos; no escalar toda la página ni truncar textos. */
body{font-family:Arial,sans-serif;color:#414b51;font-size:13.2px;line-height:1.55;margin:0}
table{width:100%;border-collapse:collapse}td{vertical-align:top}h1{font-size:28.6px;line-height:1.25;margin:0 0 7.04px;font-weight:600;color:#343f45}h2{font-size:14.3px;color:#8e7125;margin:0 0 13.2px;font-weight:600}
.cabecera{border-bottom:1.76px solid #ac8a29}.cabecera td{padding-bottom:18.48px;vertical-align:middle}.empresa{text-align:right;font-size:9.9px;line-height:1.65;color:#768187}.logo{width:176.0px;max-width:100%;height:auto}
.titulo{background:#f5f3ed;border-left:3.52px solid #ad8b28;margin:22.0px 0 23.76px}.titulo td{padding:15.84px;vertical-align:middle}.kicker{font-size:11.0px;letter-spacing:0.88px;color:#9a7b25;margin-bottom:5.28px}.folio{width:30%;color:#808a90;font-size:12.1px}.numero{display:block;font-size:28.6px;line-height:1.2;color:#947421}.muted{font-size:11.44px;color:#808a90}.derecha{text-align:right}.badge{display:inline-block;color:#54806a;background:#edf5f0;padding:4.4px 9.68px;font-size:11.0px;margin-top:8.8px;letter-spacing:0.352px}.cancelado{color:#ae353b;background:#fbefef}
.cliente{border:0;border-bottom:0.88px solid #e0e5e8;padding:0 0 16.72px;margin-bottom:22.0px;page-break-inside:avoid}.cliente td{padding:0 10.56px 10.56px 0}.cliente label{display:block;color:#8c969d;font-size:11.0px;margin-bottom:2.64px}.icono-cliente{width:16.72px;height:16.72px;vertical-align:middle;margin-right:5.28px}.vendedor{margin:0 0 10.56px;font-size:11.0px;color:#616f77}
.concepto{border:0.88px solid #dfe4e7;padding:16.72px;margin-bottom:8.8px;page-break-inside:avoid;break-inside:avoid}.concepto .nombre{font-size:15.4px;overflow-wrap:break-word}.nombre strong{display:block;font-weight:600}.etiqueta{display:block;font-size:11.0px;color:#8b969d;font-weight:normal}.concepto .precio{font-size:22.88px;font-weight:600;color:#8d7022;text-align:right;white-space:nowrap;vertical-align:middle;padding-left:13.2px}.detalle{border-top:0.88px solid #e6e9eb;margin-top:14.08px;font-size:12.1px}.detalle td{width:25%;padding-top:12.32px}.detalle small{display:block;color:#929da4;font-size:9.9px}
.cierre{page-break-inside:avoid;break-inside:avoid;margin-top:19.36px}.letras{font-size:11.0px;color:#7c888f;padding-top:9.68px}.letras strong{display:block;color:#4d5c64;font-weight:400;margin-bottom:7.04px}.totales{font-size:12.1px}.totales td{padding:6.16px 8.8px}.gran-total{border-top:1.76px solid #ad8b28;background:#f6f2e7}.gran-total td{padding:10.56px 9.68px}.gran-total td:last-child{font-size:20.9px;color:#8e6e1d;font-weight:bold;white-space:nowrap}.gran-total small{font-size:9.9px;color:#8e969b;font-weight:normal}.nota{border-top:0.88px solid #e1e5e7;margin-top:23.76px;padding:14.08px 0;font-size:12.1px;page-break-inside:avoid}.nota span{display:block;font-size:11.0px;color:#919ba2;margin-top:7.92px}.acuse{margin-top:13.2px;border:0.88px solid #ddd;padding:10.56px;page-break-inside:avoid}.acuse p{margin:3.52px 0}.firma{max-width:149.6px;max-height:57.2px}
.marca-agua-contenido{position:relative;background:#fff!important}.marca-agua-contenido>table,.marca-agua-contenido>div:not(.marca-logo){position:relative;z-index:1}.marca-logo{position:absolute;top:60mm;right:0;width:49%;z-index:0}.marca-logo svg{width:100%;height:150mm}.concepto,.cliente,.cierre,.nota,.acuse{background-color:transparent}
.concepto{border:1px solid #dfe4e7}.cliente{border-bottom:1px solid #e0e5e8}.detalle{border-top:1px solid #e6e9eb}.nota{border-top:1px solid #e1e5e7}
/* Cinco o más conceptos: ahorrar separaciones manteniendo tipografía y estructura. */
.compacto .titulo{margin-top:13.2px;margin-bottom:13.2px}.compacto .titulo td{padding:10.56px 15.84px}.compacto .cliente{margin-bottom:10.56px;padding-bottom:7.04px}.compacto .cliente td{padding-bottom:5.28px}.compacto .concepto{padding:7.92px 14.08px;margin-bottom:5.28px}.compacto .detalle{margin-top:6.16px}.compacto .detalle td{padding-top:5.28px}.compacto .cierre{margin-top:10.56px}.compacto .nota{margin-top:10.56px;padding:7.92px 0}
@media screen{body:has(.solicitud-visor-pie){display:flex!important;flex-direction:column;min-height:250mm!important}.solicitud-visor-contenido{flex:1 0 auto}.solicitud-visor-pie{position:static!important;clear:both;margin-top:17.6px!important;flex:0 0 auto}}
/* Compensación de escala Chrome/Qt; conserva los tamaños relativos del diseño. */
@supports(display:grid){body{font-size:10.56px}h1{font-size:22.88px}h2{font-size:11.44px}.logo{width:140.8px}.empresa{font-size:7.92px}.kicker,.etiqueta,.cliente label,.vendedor,.letras,.nota span,.badge{font-size:8.8px}.folio,.totales,.nota,.detalle{font-size:9.68px}.numero{font-size:22.88px}.concepto .nombre{font-size:12.32px}.concepto .precio{font-size:18.48px}.detalle small{font-size:7.92px}.gran-total td:last-child{font-size:16.72px}.gran-total small{font-size:7.92px}}
/* Estado liquidado destacado, independiente de las etiquetas de entrega/cancelación. */
.badge.pagada{background:#287457;color:#fff;border:1px solid #206247;font-size:16px;font-weight:bold;padding:7px 14px;letter-spacing:1px;white-space:nowrap}
.badge.pagada svg{width:20px;height:20px;stroke:#fff;stroke-width:2}
@supports(display:grid){.badge.pagada{font-size:12.8px;padding:5.6px 11.2px}.badge.pagada svg{width:16px;height:16px}}
/* Cotizaciones: los paquetes y financiamientos mantienen todos sus datos, sin cortes de filas. */
.predefinido{margin-top:20px}.predefinido h2{background:#f5f3ed;border-left:3px solid #ad8b28;padding:12px}.financiamientos{font-size:11px;margin-top:12px}.financiamientos th{background:#434e54;color:#fff;padding:7px;font-weight:normal}.financiamientos td{padding:7px;border-bottom:1px solid #dfe4e7;text-align:right}.financiamientos td:first-child{text-align:left}.financiamientos tr{page-break-inside:avoid}.financiamientos thead{display:table-header-group}.seccion-paquete{page-break-inside:avoid}.cancelacion{color:#a43d43}.badge.aceptada{background:#287457;color:white;font-weight:bold}.badge.vencida{background:#faeeee;color:#a43d43}.modalidad{padding:10px 0;font-size:12px}.predefinido h2{page-break-after:avoid}
/* Distribución ejecutiva: priorizar una hoja sin zoom global ni pérdida de contenido.
   A partir de 11 filas se reducen separaciones, conservando el mismo tamaño de letra.
   Las descripciones pueden envolver; cantidades e importes permanecen alineados. */
.ejecutiva{font-size:13px;margin:14px 0 22px;table-layout:fixed}
.ejecutiva th{background:#434e54;color:#fff;padding:11px 7px;text-align:right;font-size:11px;font-weight:normal}
.ejecutiva td{padding:15px 7px;border-bottom:1px solid #dde2e5;text-align:right;vertical-align:middle;font-variant-numeric:tabular-nums;white-space:nowrap}
.ejecutiva th:nth-child(2),.ejecutiva td:nth-child(2){text-align:left;white-space:normal;word-wrap:break-word}
.ejecutiva th:first-child,.ejecutiva td:first-child{text-align:center}
.ejecutiva tbody tr:nth-child(even){background:#f7f8f8}
.ejecutiva thead{display:table-header-group}.ejecutiva tr{page-break-inside:avoid;break-inside:avoid}
.alta-densidad .ejecutiva td{padding-top:3px;padding-bottom:3px}
.alta-densidad .ejecutiva{margin-bottom:12px}
.alta-densidad .cabecera td{padding-bottom:10px}
.alta-densidad .titulo{margin-top:10px;margin-bottom:10px}
.alta-densidad .cliente h2{margin-bottom:7px}
.alta-densidad .nota p{margin:4px 0}
@supports(display:grid){.ejecutiva{font-size:10.4px}.ejecutiva th{font-size:8.8px}}
</style></head><body class="compacto {{ count($datos['conceptos']) > 10 ? 'alta-densidad' : '' }}">
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua'=>'ventas_saldos','altoMarcaAgua'=>1100])
<div class="marca-agua-contenido"><div class="marca-logo" aria-hidden="true">{!! \App\Exports\MarcaTituloReportes::svg() !!}</div>
<table class="cabecera"><tr><td style="width:32%"><img class="logo" src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></td><td class="empresa"><strong>{{ $empresa->razon_social }}</strong><br>RFC: {{ $empresa->rfc }}<br>{{ mb_convert_case($empresa->calle, MB_CASE_TITLE, 'UTF-8') }} {{ $empresa->num_ext }} · {{ mb_convert_case($empresa->colonia, MB_CASE_TITLE, 'UTF-8') }}<br>{{ $empresa->cp }} · {{ $empresa->ciudad }}, {{ $empresa->estado }}<br>Tel. {{ $empresa->telefono }} · Fax {{ $empresa->fax }} · {{ strtolower($empresa->email) }}<br>Régimen: {{ $empresa['regimen']['regimen'] }}</td></tr></table>
<table class="titulo"><tr><td><div class="kicker">PROPUESTA COMERCIAL</div><h1>Cotización</h1><div>Fecha de cotización: <strong>{{ $datos['fecha_texto'] }}</strong></div><div>Válida hasta: <strong>{{ $datos['fecha_vencimiento_texto'] }}</strong></div></td><td class="derecha folio"><span class="muted">Número de cotización</span><strong class="numero">{{ $datos['id'] }}</strong><span class="badge {{ $datos['status'] == 0 ? 'cancelado' : ($datos['status'] == 3 ? 'vencida' : ($datos['status'] == 2 ? 'aceptada' : '')) }}">{{ mb_strtoupper($datos['status_texto'], 'UTF-8') }}</span></td></tr></table>
<div class="cliente"><h2><svg class="icono-cliente" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#8e7125" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg> Datos del cliente</h2><table><tr><td style="width:60%"><label>Nombre</label><strong>{{ $datos['cliente_nombre'] }}</strong></td><td><label>Teléfono(s)</label>{{ $datos['cliente_telefono'] ?: 'No disponible' }}</td></tr><tr><td colspan="2"><label>Email</label>{{ strtolower($datos['cliente_email'] ?: 'No disponible') }}</td></tr></table></div>
<table class="vendedor"><tr><td>Vendedor: {{ $datos['vendedor']['nombre'] }}</td><td class="derecha">Importes en MXN</td></tr></table>
{{-- Importes originales de cotización: no se añaden impuestos o subtotales derivados. --}}
@if(count($datos['conceptos']))
<h2>Cotización personalizada</h2>
{{-- Tabla ejecutiva aprobada: valores originales y filas completas en cada página. --}}
<table class="ejecutiva"><thead><tr><th style="width:4%">#</th><th style="width:34%">Concepto</th><th style="width:9%">Cantidad</th><th style="width:18%">Costo unitario</th><th style="width:17%">Desc. por unidad</th><th style="width:18%">Importe</th></tr></thead><tbody>
@foreach($datos['conceptos'] as $indice=>$concepto)
<tr><td>{{ $indice+1 }}</td><td>{{ $concepto['descripcion'] }}</td><td>{{ $concepto['cantidad'] }}</td><td>$ {{ number_format($concepto['costo_neto_normal'],2) }}</td><td>$ {{ number_format($concepto['descuento'],2) }}</td><td><strong>$ {{ number_format($concepto['importe'],2) }}</strong></td></tr>
@endforeach
</tbody></table>
<div class="cierre"><table><tr><td style="width:57%;padding-right:16px"><div class="modalidad"><span class="etiqueta">Modalidad de pago</span>{{ $datos['descripcion_pagos'] }}</div></td><td style="width:43%"><table class="totales"><tr><td>Descuento</td><td class="derecha">$ {{ number_format($datos['descuento'],2) }}</td></tr><tr class="gran-total"><td>Total</td><td class="derecha">$ {{ number_format($datos['total'],2) }} <small>MXN</small></td></tr></table></td></tr></table></div>
@endif
{{-- Los paquetes son alternativas cotizadas; no se suman al total personalizado. --}}
@foreach($datos['predefinidos'] as $predefinido)
<section class="predefinido"><h2>Cotización {{ $predefinido['tipo'] }} · {{ $predefinido['label'] }}</h2>
@foreach($predefinido['secciones'] as $indice=>$seccion)
@if(count($seccion['conceptos']))<div class="seccion-paquete"><h3>{{ $indice == 0 ? 'Incluye' : 'En caso de '.$seccion['seccion'] }}</h3><ul>@foreach($seccion['conceptos'] as $concepto)<li>{{ $concepto }}</li>@endforeach</ul></div>@endif
@endforeach
<table class="financiamientos"><thead><tr><th>Modalidad</th><th>Costo</th><th>Pago inicial</th><th>Pago mensual</th></tr></thead><tbody>@foreach($predefinido['financiamientos'] as $financiamiento)<tr><td>{{ $financiamiento['financiamiento'] }}</td><td>$ {{ number_format($financiamiento['costo_neto'],2) }}</td><td>$ {{ number_format($financiamiento['pago_inicial'],2) }}</td><td>$ {{ number_format($financiamiento['pago_mensual'],2) }}</td></tr>@endforeach</tbody></table></section>
@endforeach
<div class="nota">Nota / Comentarios<span>{!! $datos['nota'] !!}</span></div>
@if($datos['status'] == 0)<div class="nota cancelacion"><strong>Nota de cancelación</strong><span>Canceló: {{ $datos['cancelo']['nombre'] }} · {{ $datos['fechahora_cancelacion_texto'] }}</span><div>{!! $datos['nota_cancelacion'] !!}</div></div>@endif
</div></body></html>
