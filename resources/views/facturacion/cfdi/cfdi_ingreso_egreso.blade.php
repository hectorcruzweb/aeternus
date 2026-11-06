<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Factura</title>
<style>
/* Factura comercial por conceptos. Importes, QR, cadenas y certificados provienen
   de los mismos datos que el formato anterior. Sin min-height artificial ni recortes.
   La clase densa reduce separaciones, no elimina partidas ni campos fiscales. */
body{font-family:Arial,sans-serif;color:#3e494f;font-size:13px;line-height:1.35;margin:0}table{width:100%;border-collapse:collapse}td{vertical-align:top}.right{text-align:right}.cabecera{border-bottom:2px solid #a98726;margin-bottom:12px}.cabecera td{vertical-align:middle;padding-bottom:12px}.logo{width:210px;max-width:100%}.empresa{text-align:right;font-size:10px;line-height:1.5}.empresa strong{font-size:12px}.titulo{background:#f5f3ed;border-left:3px solid #a98726;margin:12px 0}.titulo td{padding:13px 15px;vertical-align:middle}.kicker{font-size:10px;color:#a98726;letter-spacing:.6px}h1{font-size:30px;margin:3px 0 7px}h2{font-size:14px;color:#a98726;margin:0 0 8px}.folio{font-size:23px;font-weight:bold;color:#a98726}.muted{font-size:10px;color:#78858c}.estado{display:inline-block;padding:4px 8px;margin-top:5px;font-weight:bold;font-size:11px}.pagada{background:#287457;color:#fff}.cancelada,.invalida{color:#af3e43}.receptor{border-bottom:1px solid #dbe1e4;margin-bottom:8px;padding:6px 0 8px;page-break-inside:avoid}.receptor td{padding:3px 0}.uuid{font-size:11px;padding:5px 0;word-wrap:break-word}.contexto{font-size:10px;margin:4px 0;page-break-inside:avoid}.contexto td{padding:3px}.concepto{border:1px solid #dbe1e4;padding:9px 12px;margin:7px 0;page-break-inside:avoid;break-inside:avoid}.concepto .descripcion{font-size:13px;font-weight:bold;word-wrap:break-word}.concepto .importe{font-size:21px;color:#a98726;font-weight:bold;white-space:nowrap;vertical-align:middle;padding-left:12px}.concepto .meta{font-size:10px;color:#78858c;padding-top:8px}.cierre{page-break-inside:avoid;margin:10px 0}.cierre td{padding:3px 0}.totales{font-size:12px}.totales td{padding:4px 7px}.total{background:#f6f2e7;border-top:2px solid #a98726}.total td{padding:8px}.total .valor{font-size:21px;color:#a98726;font-weight:bold;white-space:nowrap}.total small{font-size:10px}.pago{margin-top:10px;font-size:11px;line-height:22px}.fiscal{border-top:1px solid #dbe1e4;padding-top:8px;page-break-inside:avoid}.qr{width:18%;padding-right:10px}.qr svg,.qr img{width:125px!important;height:125px!important;max-width:100%}.sellos{font-size:7px;line-height:1.25;word-break:break-all;word-wrap:break-word}.sellos strong{display:block;font-size:8px;margin:3px 0}.certificados{font-size:8px;border-top:1px solid #dbe1e4;margin-top:6px;table-layout:fixed}.certificados td{width:25%;padding:5px 4px;word-wrap:break-word}.certificados small{display:block;font-size:7px;color:#78858c;margin-bottom:4px}.leyenda{font-size:9px;margin-top:6px}.densa .titulo td{padding:7px 12px}.densa .cabecera td{padding-bottom:7px}.densa .concepto{padding:5px 10px;margin:4px 0}.densa .concepto .meta{padding-top:4px}.densa .receptor td{padding:1px 0}.densa h1{font-size:25px}.densa .cierre{margin:6px 0}
@supports(display:grid){body{font-size:10.4px}.logo{width:168px}.empresa{font-size:8px}.empresa strong{font-size:9.6px}h1{font-size:24px}.folio{font-size:18.4px}.concepto .descripcion{font-size:10.4px}.concepto .importe,.total .valor{font-size:16.8px}.concepto .meta,.contexto,.muted,.kicker{font-size:8px}.sellos{font-size:5.6px}.sellos strong,.certificados{font-size:6.4px}.certificados small{font-size:5.6px}.totales{font-size:9.6px}.pago,.uuid{font-size:8.8px}.leyenda{font-size:7.2px}.densa h1{font-size:20px}}
/* El fondo blanco delimita los datos: la trama solo ocupa el sobrante de la hoja.
   El logo se coloca en otra capa, tenue, sin modificar QR, sellos ni firmas. */
.marca-agua-contenido{position:relative;background:#fff!important;margin-left:0!important;margin-right:0!important;border-left:0!important;border-right:0!important}
.marca-agua-contenido>table,.marca-agua-contenido>div:not(.marca-logo){position:relative;z-index:1}
.marca-logo{position:absolute;top:45mm;right:0;width:48%;z-index:0;pointer-events:none}
.marca-logo svg{width:100%;height:150mm}
.cabecera,.qr,.sellos,.certificados,.leyenda{background:#fff}
/* Encabezado compacto: el sello ocupa el centro geométrico de la franja.
   Tabla de ancho automático: reparte el espacio libre entre los cinco campos. */
.factura-encabezado{position:relative;background:#f5f3ed;border-left:3px solid #a98726;margin:8px 0;padding:0 10px}
.factura-encabezado .titulo{border:0;margin:0;background:transparent}
.factura-encabezado .titulo td,.densa .factura-encabezado .titulo td{padding:3px 4px;height:75px;vertical-align:middle}
.factura-encabezado h1,.densa .factura-encabezado h1{font-size:27px;margin:3px 0 0;line-height:1.1}
.factura-encabezado .kicker{font-size:9px}
.aviso-local{font-size:9px;font-weight:bold;margin-top:4px}
.metadatos-cfdi{width:100%;table-layout:auto;border-top:1px solid #dedbd2;font-size:8px;line-height:1.4}
.metadatos-cfdi td{padding:5px 6px;white-space:nowrap;vertical-align:middle;text-align:center}
.metadatos-cfdi td:first-child{padding-left:0;text-align:left}.metadatos-cfdi td:last-child{padding-right:0;text-align:right}
.metadatos-cfdi span{color:#78858c}
.sello-circular{position:absolute;left:50%;top:50%;margin-left:-32px;margin-top:-32px;width:64px;height:64px;border:3px double;border-radius:50%;box-sizing:border-box;z-index:3;background:#f5f3ed}
.sello-circular table{width:100%;height:100%}.sello-circular td{padding:0;text-align:center;vertical-align:middle}
.sello-leyenda{display:block;font-size:5px;font-weight:bold;line-height:1.4;margin-bottom:3px}
.sello-circular strong{display:block;font-size:10px;line-height:1.2}.sello-circular .texto-pendiente{font-size:7px}
.sello-linea{display:block;width:26px;margin:4px auto 0;border-top:1px solid}
.sello-pagado{color:#287457;border-color:#287457}.sello-pendiente{color:#ba791d;border-color:#ba791d}.sello-cancelado{color:#af3e43;border-color:#af3e43}
.mono-luto{width:10px;height:15px;vertical-align:middle;margin-right:4px}
@supports(display:grid){.factura-encabezado h1,.densa .factura-encabezado h1{font-size:21.6px}.factura-encabezado .kicker,.aviso-local{font-size:7.2px}.metadatos-cfdi{font-size:6.4px}.mono-luto{width:8px;height:12px}}
/* Receptor a todo el ancho: separación uniforme y valores bajo su etiqueta. */
.receptor-organizado{padding:5px 0 7px}
.receptor-organizado h2{margin-bottom:5px}
.receptor-identidad,.receptor-fiscal{table-layout:fixed;width:100%}
.receptor-organizado td,.densa .receptor-organizado td{padding:3px 14px 4px 0;vertical-align:top;word-wrap:break-word}
.receptor-organizado td:last-child{padding-right:0}
.receptor-label{display:block;color:#78858c;font-size:9px;line-height:1.3;margin-bottom:2px;font-weight:normal}
.receptor-direccion{padding:3px 0 4px;word-wrap:break-word}
@supports(display:grid){.receptor-label{font-size:7.2px}}
.importe-letra{white-space:nowrap;line-height:1.5;word-break:normal;overflow:visible}
/* Sello del estado superpuesto como marca de agua sobre las cadenas fiscales.
   No ocupa altura ni altera el QR, las cadenas, los certificados o sus valores. */
.cfdi-pie-fiscal .sellos{position:relative}
.cfdi-pie-fiscal .sello-circular{position:absolute;top:50%;left:50%;margin:-36.4px 0 0 -36.4px;width:72.8px;height:72.8px;background:transparent;opacity:.96;z-index:3;pointer-events:none}
.cfdi-pie-fiscal .sello-circular table,.cfdi-pie-fiscal .sello-circular td{background:transparent}
.cfdi-pie-fiscal .sello-circular table{height:66.8px}
.cfdi-pie-fiscal .sello-circular td{vertical-align:middle}
.cfdi-pie-fiscal .sello-circular strong{font-size:11.7px;line-height:1.3;letter-spacing:.3px}
.cfdi-pie-fiscal .sello-circular .texto-pendiente{font-size:8.45px}
.factura-encabezado .titulo td,.densa .factura-encabezado .titulo td{height:auto;padding:4px 3px}
.factura-encabezado h1,.densa .factura-encabezado h1{font-size:24px;line-height:1.05;margin:2px 0 0}
.factura-encabezado .folio{font-size:21px;line-height:1.1}
.factura-encabezado .kicker{font-size:8px;line-height:1.2}
.factura-encabezado .aviso-local{margin-top:2px;font-size:8px;line-height:1.1}
.factura-encabezado .metadatos-cfdi td{padding-top:3px;padding-bottom:3px}
.factura-encabezado{margin-top:5px;margin-bottom:6px}
@supports(display:grid){.factura-encabezado h1,.densa .factura-encabezado h1{font-size:19.2px}.factura-encabezado .folio{font-size:16.8px}.factura-encabezado .kicker,.factura-encabezado .aviso-local{font-size:6.4px}}
.concepto .muted{display:block;line-height:16px}.concepto .descripcion{line-height:18px}
/* Sello 30 % mayor; contraste reforzado conservando el fondo transparente. */
.cfdi-pie-fiscal .sello-circular{border-width:3.9px}
.cfdi-pie-fiscal .sello-leyenda{font-size:6.5px;margin-bottom:3.9px}
.cfdi-pie-fiscal .sello-linea{width:33.8px;margin-top:5.2px;border-top-width:1.3px}
.cfdi-pie-fiscal .sello-pagado{color:#17623f;border-color:#17623f}
.cfdi-pie-fiscal .sello-pendiente{color:#ad6308;border-color:#ad6308}
/* UUID centrado en la franja principal, sin competir con folio y título.
   El sello no participa en el flujo: conserva la altura del bloque fiscal. */
.factura-encabezado .uuid-destacado{width:44%;text-align:center;white-space:nowrap}
.uuid-destacado span{display:block;color:#78858c;font-size:9px;margin-bottom:3px}
.uuid-destacado strong{display:block;font-size:12px;letter-spacing:.1px;color:#3e494f}
.sello-control{position:absolute;left:50%;top:50%;width:118.3px;height:118.3px;margin:-59.15px 0 0 -59.15px;z-index:3;pointer-events:none;background:transparent}
.sello-control svg{display:block;width:118.3px;height:118.3px}
@supports(display:grid){.uuid-destacado strong{font-size:9.6px}.uuid-destacado span{font-size:7.2px}}
/* Legibilidad de datos secundarios: incremento moderado y gris más oscuro.
   Qt y Chromium mantienen su proporción física; el bloque fiscal reserva su
   altura real antes del ajuste automático del cuerpo a una página. */
.empresa,.muted,.contexto,.concepto .meta{font-size:11px}
.muted,.concepto .meta,.receptor-label,.metadatos-cfdi span,.uuid-destacado span,.certificados small{color:#535f66}
.receptor-label,.uuid-destacado span{font-size:10.5px}
.metadatos-cfdi{font-size:10px;color:#3e494f}
.factura-encabezado .kicker{font-size:9px;color:#80661e}
.factura-encabezado .aviso-local{font-size:9px}
/* Cadenas fiscales discretas: menor tamaño y gris suave; el sello de estado SVG conserva su contraste. */
.sellos{font-size:7px;line-height:1.35;color:#737d83}
.sellos strong{font-size:8px;color:#616c73}
.certificados{font-size:9.5px;color:#354047}
.certificados small{font-size:9px}
.leyenda{font-size:10px}
@supports(display:grid){
 .empresa,.muted,.contexto,.concepto .meta{font-size:8.8px}
 .receptor-label,.uuid-destacado span{font-size:8.4px}
 .metadatos-cfdi{font-size:8px}
 .factura-encabezado .kicker,.factura-encabezado .aviso-local{font-size:7.2px}
 .sellos{font-size:5.6px}.sellos strong{font-size:6.4px}
 .certificados{font-size:7.6px}.certificados small{font-size:7.2px}
 .leyenda{font-size:8px}
}
/* Placa de estado aprobada: fondo tenue, barra lateral e icono circular.
   Medidas proporcionales en Qt y navegador; sin alterar datos del receptor. */
.receptor-distribucion{table-layout:fixed;width:100%}
.receptor-organizado .receptor-distribucion>tbody>tr>td.receptor-datos,.receptor-organizado .receptor-distribucion>tr>td.receptor-datos{width:74%;padding:0 16px 0 0}
.receptor-organizado .receptor-distribucion>tbody>tr>td.receptor-estado,.receptor-organizado .receptor-distribucion>tr>td.receptor-estado{width:26%;padding:0;vertical-align:middle}
.placa-pago{border:1px solid;border-left-width:4px;padding:12px 8px}
.placa-pagada{background:#f0f6f2;border-color:#d5e4dc;border-left-color:#226c4b;color:#226c4b}
.placa-pendiente{background:#fff7e9;border-color:#efdbb8;border-left-color:#ad6b18;color:#ad6b18}
.placa-cancelada{background:#fcf1f2;border-color:#ebd6d8;border-left-color:#af3e43;color:#af3e43}
.receptor-organizado .placa-pago td{padding:0;vertical-align:middle;word-wrap:normal}
.placa-icono{width:30px}.placa-icono svg{display:block}
.placa-texto span{display:block;font-size:10px;color:#626e74;margin-bottom:4px}
.placa-texto strong{display:block;font-size:13px;white-space:nowrap;line-height:1.3}
.placa-pagada .placa-texto strong{font-size:19px}
@supports(display:grid){.placa-pago{padding:9.6px 6.4px}.placa-texto span{font-size:8px}.placa-texto strong{font-size:10.4px}.placa-pagada .placa-texto strong{font-size:15.2px}.placa-icono{width:24px}.placa-icono svg{width:18.4px;height:18.4px}}
/* Placa vertical: icono, etiqueta y estado apilados; ancho reservado al receptor. */
.placa-pago{width:145px;margin:0 0 0 auto;padding:10px 8px;border-left-width:1px;border-top-width:4px;box-sizing:border-box}
.placa-pagada{border-top-color:#226c4b}.placa-pendiente{border-top-color:#ad6b18}.placa-cancelada{border-top-color:#af3e43}
.receptor-organizado .placa-pago .placa-icono{width:auto;padding-bottom:7px;text-align:center}
.placa-icono svg{margin:0 auto}
.placa-texto{text-align:center}.placa-texto strong{white-space:normal}
@supports(display:grid){.placa-pago{width:116px;padding:8px 6.4px}.receptor-organizado .placa-pago .placa-icono{padding-bottom:5.6px}}
/* Texto girado compatible con Qt: transformación explícita, sin writing-mode.
   La columna estrecha devuelve espacio a los datos del receptor. */
.receptor-organizado .receptor-distribucion>tbody>tr>td.receptor-datos,.receptor-organizado .receptor-distribucion>tr>td.receptor-datos{width:92%}
.receptor-organizado .receptor-distribucion>tbody>tr>td.receptor-estado,.receptor-organizado .receptor-distribucion>tr>td.receptor-estado{width:8%}
.placa-pago{position:relative;width:56px;height:155px;padding:0}
.receptor-organizado .placa-pago .placa-icono{position:absolute;top:7px;left:0;width:100%;padding:0}
.placa-texto-girado{position:absolute;left:50%;top:61%;width:115px;text-align:center;transform:translate(-50%,-50%) rotate(90deg);-webkit-transform:translate(-50%,-50%) rotate(90deg)}
.placa-texto strong{white-space:nowrap;font-size:10px}.placa-pagada .placa-texto strong{font-size:16px}
.placa-texto span{font-size:9px;margin-bottom:3px}
@supports(display:grid){.placa-pago{width:44.8px;height:124px;padding:0}.placa-texto-girado{width:92px}.placa-texto strong{font-size:8px}.placa-pagada .placa-texto strong{font-size:12.8px}.placa-texto span{font-size:7.2px}}
/* Estado con letras derechas, una por línea, según la referencia aprobada.
   La altura depende del texto: no se ocultan letras de estados largos. */
.placa-pago{height:auto;min-height:155px;padding:8px 0}
.receptor-organizado .placa-pago .placa-icono{position:static;width:auto;padding-bottom:7px}
.placa-letras{text-align:center;font-weight:bold;font-size:11px;line-height:1.15}
.placa-letras span{display:block}
.placa-palabra+.placa-palabra{margin-top:5px}
.placa-pagada .placa-letras{font-size:17px;line-height:1.3}
@supports(display:grid){.placa-pago{height:auto;min-height:124px;padding:6.4px 0}.placa-letras{font-size:8.8px}.placa-pagada .placa-letras{font-size:13.6px}}
/* Encabezado de altura conservada: la cápsula usa el espacio del título,
   sin alterar la posición del UUID, folio ni la fila de metadatos. */
.factura-encabezado .titulo td:first-child{position:relative}
.factura-encabezado .titulo td:first-child .kicker{position:relative;top:-5px}
.factura-encabezado h1,.densa .factura-encabezado h1{position:relative;top:-5px;font-size:21px;margin-bottom:6px}
.estado-capsula{position:absolute;left:3px;bottom:0;border:1px solid;border-radius:10px;padding:1px 6px;white-space:nowrap;font-size:8px;font-weight:bold;line-height:12px}
.estado-capsula svg{display:inline-block;vertical-align:middle;margin-right:3px;width:10px;height:10px}
.estado-capsula span{vertical-align:middle}
.capsula-pagada{color:#226c4b;border-color:#226c4b;background:#eef5f0}
.capsula-pendiente{color:#aa6716;border-color:#aa6716;background:#fff5e5}
.capsula-cancelada{color:#af3e43;border-color:#af3e43;background:#fcf1f2}
@supports(display:grid){.factura-encabezado h1,.densa .factura-encabezado h1{font-size:16.8px}.estado-capsula{font-size:6.4px;line-height:9.6px}.estado-capsula svg{width:8px;height:8px}}
/* Estado en la misma línea que Factura: libera el espacio inferior del título. */
.factura-encabezado .titulo td:first-child .kicker{top:0}
.factura-encabezado h1,.densa .factura-encabezado h1{display:inline-block;vertical-align:middle;top:0;margin:2px 8px 0 0}
.estado-capsula{position:static;display:inline-block;vertical-align:middle;margin-top:2px}
.pago-desglose{table-layout:fixed;margin-top:8px;font-size:11px}.pago-desglose td{padding:4px 6px 0 0}.documento-pago .descripcion{font-size:12px}
@supports(display:grid){.pago-desglose{font-size:8.8px}.documento-pago .descripcion{font-size:9.6px}}
</style></head><body class="{{ count($datos['Conceptos'] ?? []) > 4 ? 'densa' : '' }}">
@php
    // La vista de pago comparte la presentación, pero usa exclusivamente los importes del complemento.
    $esCfdiPago = $esCfdiPago ?? false;
    // Formato visual únicamente: conservar la hora escrita en el XML, sin convertir zona.
    $fechaHumanaCfdi = function ($valor) {
        if (!$valor) return 'No disponible';
        try {
            $fecha = \Carbon\Carbon::parse($valor)->locale('es');
            return $fecha->translatedFormat('j \d\e F \d\e Y').', '.$fecha->format('g:i').' '.($fecha->format('a') === 'am' ? 'a. m.' : 'p. m.');
        } catch (\Throwable $e) { return $valor; }
    };
    // Ajuste solo visual del importe con letra. Normalizar espacios/saltos y usar
    // una escala conservadora para la columna del 57%, sin alterar el importe.
    // Se calcula en PHP para que funcione también en el iframe sin JavaScript.
    $importeLetraCfdi = preg_replace('/\s+/u', ' ', trim('Son '.$datos['Sistema']['total_letra']));
    $escalaLetraCfdi = min(1, 55 / max(1, mb_strwidth($importeLetraCfdi, 'UTF-8')));
@endphp
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua'=>'facturacion','altoMarcaAgua'=>1100])
<div class="cfdi-pagina"><section class="cfdi-hoja" data-ajuste-una-pagina><div class="marca-agua-contenido" data-contenido-ajustable><div class="marca-logo" aria-hidden="true">{!! \App\Exports\MarcaTituloReportes::svg() !!}</div>
<table class="cabecera"><tr><td style="width:32%"><img class="logo" src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></td><td class="empresa"><strong>{{ $empresa->razon_social }}</strong><br>RFC {{ $empresa->rfc }} · Régimen {{ $datos['Emisor']['RegimenFiscal'] }}<br>{{ mb_convert_case($empresa->calle,MB_CASE_TITLE,'UTF-8') }} {{ $empresa->num_ext }} · {{ mb_convert_case($empresa->colonia,MB_CASE_TITLE,'UTF-8') }} · C. P. {{ $empresa->cp }}<br>{{ $empresa->ciudad }}, {{ $empresa->estado }} · Tel. {{ $empresa->telefono }} · Fax {{ $empresa->fax }}<br>{{ strtolower($empresa->email) }}</td></tr></table>
{{-- Franja compacta con datos del comprobante en una sola línea; sello en el pie fiscal.
     El estado usa el mismo saldo/estatus del CFDI; solo cambia su presentación. --}}
<div class="factura-encabezado">
<table class="titulo"><tr><td style="width:32%"><div class="kicker">COMPROBANTE FISCAL DIGITAL · VERSIÓN {{ $datos['Comprobante']['Version'] }}</div><h1>{{ $esCfdiPago ? 'Complemento de pago' : ($datos['Sistema']['tipo_comprobante_id'] == 2 ? 'Nota de crédito' : 'Factura') }}</h1>
{{-- Cápsula de estado bajo el título: posición absoluta dentro de la altura
     existente. No agrega una fila ni modifica las reglas de saldo/cancelación. --}}
@if($cfdi['status'] == 0 || $cfdi['sat_tipo_comprobante_id'] == 1)
<div class="estado-capsula {{ $cfdi['status'] == 0 ? 'capsula-cancelada' : ($cfdi['saldo_cfdi'] <= 0 ? 'capsula-pagada' : 'capsula-pendiente') }}">
<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><g fill="none" stroke="{{ $cfdi['status'] == 0 ? '#af3e43' : ($cfdi['saldo_cfdi'] <= 0 ? '#226c4b' : '#aa6716') }}" stroke-width="1.7"><circle cx="12" cy="12" r="10"/>
@if($cfdi['status'] == 0)<path d="M8 8L16 16M16 8L8 16"/>
@elseif($cfdi['saldo_cfdi'] <= 0)<path d="M6 12L10 16L18 7"/>
@else<path d="M12 5V13M12 16V18"/>@endif
</g></svg><span>{{ $cfdi['status'] == 0 ? 'CANCELADA' : ($cfdi['saldo_cfdi'] <= 0 ? 'PAGADA' : 'PAGO PENDIENTE') }}</span></div>
@endif
</td><td class="uuid-destacado"><span>UUID</span><strong>{{ $datos['Complemento']['TimbreFiscalDigital']['UUID'] }}</strong></td><td class="right" style="width:24%"><span class="muted">Folio / Serie</span><div class="folio">{{ $datos['Comprobante']['Folio'] }} / {{ $datos['Comprobante']['Serie'] }}</div>@if(ENV('APP_ENV') == 'local')<div class="invalida aviso-local">CFDI NO VÁLIDO</div>@endif</td></tr></table>

<table class="metadatos-cfdi"><tr><td><span>Emisión:</span> {{ $fechaHumanaCfdi($datos['Comprobante']['Fecha']) }}</td><td><span>Tipo:</span> {{ $datos['Comprobante']['TipoDeComprobante'] }}</td><td><span>Facturó:</span> {{ $datos['Sistema']['timbro_nombre'] }}</td><td><span>Expedido en:</span> Mazatlán, Sin.</td></tr></table>
</div>
{{-- Receptor: identidad y domicilio fiscal distribuidos por filas completas.
     Tablas de ancho fijo evitan que nombres/direcciones largos desplacen columnas. --}}
<div class="receptor receptor-organizado"><h2>Datos del receptor</h2>

<table class="receptor-identidad"><colgroup><col style="width:50%"><col style="width:28%"><col style="width:22%"></colgroup><tr><td><span class="receptor-label">Nombre o razón social</span><strong>{{ $datos['Receptor']['Nombre'] }}</strong></td><td><span class="receptor-label">RFC</span><strong>{{ $datos['Receptor']['Rfc'] }}</strong></td><td><span class="receptor-label">Código postal fiscal</span>{{ $datos['Receptor']['DomicilioFiscalReceptor'] ?? 'No disponible' }}</td></tr></table>
<div class="receptor-direccion"><span class="receptor-label">Dirección</span>{{ $datos['Sistema']['cliente_direccion'] }}</div>
<table class="receptor-fiscal"><colgroup><col style="width:60%"><col style="width:40%"></colgroup><tr><td><span class="receptor-label">Régimen fiscal</span>{{ $datos['Receptor']['RegimenFiscalReceptor'] ?? 'No disponible' }}</td><td><span class="receptor-label">Uso del CFDI</span>{{ $datos['Receptor']['UsoCFDI'] }}</td></tr></table>
</div>
    @foreach ($cfdi['servicios_funerarios'] as $servicio)
        @if (isset($servicio['operacion_factura']))
            @foreach ($servicio['operacion_factura'] as $operacion)
                @if ($operacion['empresa_operaciones_id'] == 3)
                    <table class="contexto">
                        <tr>
                            <td class="w-65 py-1 px-2 "><span class="bold">
                                    <!--Reparacion temporal para marcar que es una extremidad y no un fallecido.-->
                                    @if ($datos['Comprobante']['Folio'] == 1382)
                                        Extremidad
                                    @else
<img class="mono-luto" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAtCAYAAAC53tuhAAAE1klEQVR4nJ2YeYxeYxTGfx1qmTFFE5GWjKi1iomiSsQSia1qKYaqvWqJhDaWiJ0/SFFCUJVp7FHLpKhtYsmIWmNvo00jZaIYMdVa0hE6UzmT523OnLz33m/mSb6Z77733ve573nPfc5zvmFNTU3UgF2Ao4HxwJ7ADsD2OtcF/AwsA74A2oHvqyYcVkE8GrgNuADYpJYnBNYD84FbgF+LLqormeBiYAVw0SBIDZsClwDfARcOhnhL4GVgHtDA0LGVVr5Qc5aGegTwJnBwZiLbw1bgB+1h2sed3ceitHvm3g+B44A/i4g/AA4JN/UANwFzalzptcCtmVW+BxyRC/WMDOnHwK6DIDXcpXs+DeOHA+fHFdsDrAJGuQvteD+gm6FhO+DrMGentmRDWvGx4YIevbeJdDNgCnAf8DmwFujTx75/BtwLnAgM1z2/AZM0V8JOmrc/9Q3TGYinlEwHAJcCpyvxctga2F+fWXqQF4C5wJfAAumA39L2FOoup0SGc6VSV9p2MDRYNO6WFsx347+YMCXivkCwouC1GAqWS2YTLPT1aY//ChdXkb4O7As063sZPKnhX/uTiH+kGCYYq8PY1cAS4BvgmnCuu2K+zlqIX9GqesN4Y0guj15F442COVd54o1S5vAccJLOdYRz17vvVoU8OpTZ9iq1Zeb92xPHUP4HnOOO7w/n7YEuA64DjgnnHtf/DVKqGK0l9idl9RZKsPReG44MK30WOJNymEwe5I5PVnXyGd3glesf4EkGYlbmuEw+bdJpYSwm3iOKxIAi8VC4yORvgjvu0or7b8xguoq/X60vOn3Ag+nAEye/5GHK4/EO8EmG9AFtRYI5ltnhmqeBlTliw+3h+LCwr6azE8M19irODGNXBRHqBW6sMnvtqYIIvwO7AXsB77rqY/hD4+YyvSP9VhUt4VH5sFLiscBX4cZXtV8jQzJZDiz182krDnRjVh73ANZUmT0rhzeEsRMCqSXYWYE0SaknRSVxTa329h75ryKYWr0Uxi6X7fF4DHgtN0GZoR8jlakP40Z4ShgbJfEfHpJuXKbyVRr6pgzp6oxI2L4+H0gN2wRzURPxaE1G2NfJwLowbvlwaGaORlW3+lqJTa9flEv0aAE+CmMT1FsVYaz8W03EczKdxGw9TAxlW5hjnToRjyky+aXEpwFXhLGOUH8TngF2zOj1qfJZHndKBbPE5o2eCDf8pBCbwHuYhz4+U4cXaNWTpGqeZ6ESdgCxdXaLQiKsV8E35fEYlymZnXqPE1Yqer6SjRT55p64Vf2Ox0x1DR4NobCX4e2MLRqfSqMJyMRMtlrIphbsq0llDq2qXhGLJLkezXXBW6HKElsaw3klpOiXA2t1IqYFg2A4uy5YVST8MZmsujyc2VdLvrhqa8wItXhZGNvWiBeHwRaVRSNLRrAto0AtMgn+IUdI8ZJpbFarOjncu9j2uFFJZMU+YoYSwqxsNHFWwZBy3RzO3yFdzzX0S82Jpuo0Rs4jZnYObwWHYni/QK8jlgNHmWOpc+9dc6YwRHQVJNhUdQ9lmKdfGPptUtTZM0p+m+qRKHQX9ENFGb9Wem0Nvvn3fuSKhLkGSywLaYKFyDxXmSuxJs3Cbd1lgiXl3jnRqfpJ0X6ztEyNr0MV9lFIY0+2Ef8DzwMQzZMMiV8AAAAASUVORK5CYII=" alt="Moño de luto"> Nombre del Fallecido:
                                    @endif
                                </span>
                                <span>{{ $operacion['servicio_funerario']['nombre_afectado'] }}</span>
                            </td>
                            <!--Reparacion temporal para marcar que es una extremidad y no un fallecido.-->

                            <td class="w-35 px-1 px-2 "><span class="bold">

                                    <!--Reparacion temporal para marcar que es una extremidad y no un fallecido.-->
                                    @if ($datos['Comprobante']['Folio'] == 1382)
                                        Fecha de servicio:
                                    @else
                                        Fecha de fallecimiento:
                                    @endif
                                </span>
                                <span>{{ $operacion['servicio_funerario']['fecha_defuncion_texto'] }}</span>
                            </td>
                        </tr>
                    </table>
                @endif
            @endforeach
        @endif
    @endforeach




    @if (isset($datos['CfdisRelacionados']))
        <table class="contexto px-2">
            <tr>
                <td class="w-100 py-1 px-2 left">
                    <span class="bold">cfdis relacionados, tipo de relación:</span>
                    <span>{{ $datos['CfdisRelacionados']['TipoRelacionTexto'] }}</span>
                </td>
            </tr>
            @foreach ($datos['CfdisRelacionados']['CfdiRelacionado'] as $relacionado)
                <tr>
                    <td class="w-100 py-1 px-2 left">
                        <span class="bold">uuid:</span>
                        <span>{{ $relacionado['UUID'] }}</span>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
{{-- Bloques comerciales: todas las columnas originales se conservan como metadatos.
     El importe conserva la fórmula previa; esta remodelación no cambia cálculos. --}}
@if($esCfdiPago)
{{-- Importes y documentos originales del complemento: no se recalculan saldos
     ni se toma el Total del comprobante (es cero para un CFDI de pago). --}}
<h2>Documentos relacionados con el pago</h2>
@foreach($datos['Complemento']['Pago']['DoctoRelacionado'] as $indice => $pago)
<div class="concepto documento-pago"><table><tr><td><span class="muted">Factura relacionada · Folio {{ $pago['Folio'] }}</span><div class="descripcion">{{ $pago['IdDocumento'] }}</div></td><td class="right importe">$ {{ number_format($pago['ImpPagado'],2) }}</td></tr></table>
<table class="pago-desglose"><tr><td><span class="receptor-label">Moneda</span>{{ $pago['MonedaDR'] }}</td><td><span class="receptor-label">Parcialidad</span>{{ $pago['NumParcialidad'] }}</td><td><span class="receptor-label">Saldo anterior</span>$ {{ number_format($pago['ImpSaldoAnt'],2) }}</td><td><span class="receptor-label">Importe pagado</span>$ {{ number_format($pago['ImpPagado'],2) }}</td><td class="right"><span class="receptor-label">Saldo insoluto</span>$ {{ number_format($pago['ImpSaldoInsoluto'],2) }}</td></tr></table></div>
@endforeach
<div class="cierre"><table><tr><td style="width:65%;padding-right:15px"><span class="muted">Importe con letra</span><div class="importe-letra" style="font-size:{{ number_format($escalaLetraCfdi,4,'.','') }}em">{{ $importeLetraCfdi }}</div><div class="pago">Método: {{ $datos['Comprobante']['MetodoPago'] }}<br>Forma: {{ $datos['Complemento']['Pago']['FormaDePagoP'] }} · Moneda: {{ $datos['Complemento']['Pago']['MonedaP'] }}
@if(!empty($datos['Complemento']['Pago']['FechaPago']))<br>Fecha del pago: {{ $fechaHumanaCfdi($datos['Complemento']['Pago']['FechaPago']) }}@endif
</div></td><td style="width:35%"><table class="totales"><tr class="total"><td>Total recibido</td><td class="right valor">$ {{ number_format($datos['Complemento']['Pago']['Monto'],2) }} <small>{{ $datos['Complemento']['Pago']['MonedaP'] }}</small></td></tr></table></td></tr></table></div>
@else
@foreach($datos['Conceptos'] as $indice=>$concepto)
<div class="concepto"><table><tr><td><span class="muted">Concepto {{ $indice+1 }}</span><div class="descripcion">{{ $concepto['Descripcion'] }}</div></td><td class="right importe">$ {{ number_format($concepto['Impuestos']['Traslados'][0]['Importe'] + $concepto['ValorUnitario'] - $concepto['Descuento'],2) }}</td></tr><tr><td colspan="2" class="meta">Cantidad: {{ $concepto['Cantidad'] }} · U. M.: {{ $concepto['ClaveUnidad'] }} · SAT: {{ $concepto['ClaveProdServ'] }} · Costo unitario: $ {{ number_format($concepto['ValorUnitario'],2) }} · Desc.: $ {{ number_format($concepto['Descuento'],2) }} · IVA: $ {{ number_format($concepto['Impuestos']['Traslados'][0]['Importe'],2) }}</td></tr></table></div>
@endforeach
<div class="cierre"><table><tr><td style="width:57%;padding-right:15px"><span class="muted">Importe con letra</span><div class="importe-letra" style="font-size:{{ number_format($escalaLetraCfdi, 4, '.', '') }}em">{{ $importeLetraCfdi }}</div><div class="pago">Método: {{ $datos['Comprobante']['MetodoPago'] }}<br>Forma: {{ $datos['Comprobante']['FormaPago'] }} · Moneda: {{ $datos['Comprobante']['Moneda'] }}</div></td><td style="width:43%"><table class="totales"><tr><td>Subtotal</td><td class="right">$ {{ number_format($datos['Comprobante']['SubTotal'],2) }}</td></tr><tr><td>Descuento</td><td class="right">$ {{ number_format($datos['Comprobante']['Descuento'],2) }}</td></tr><tr><td>IVA ({{ $cfdi['tasa_iva'] }} %)</td><td class="right">$ {{ number_format($datos['Impuestos']['TotalImpuestosTrasladados'],2) }}</td></tr><tr class="total"><td>Total</td><td class="right valor">$ {{ number_format($datos['Comprobante']['Total'],2) }} <small>{{ $datos['Comprobante']['Moneda'] }}</small></td></tr></table></td></tr></table></div>
@endif
</div></section><div class="cfdi-pie-fiscal"><div class="fiscal"><table><tr><td class="qr">{!! $datos['CodigoQr'] !!}</td><td class="sellos"><strong>Sello digital del emisor</strong>{{ $datos['Comprobante']['Sello'] }}<strong>Sello digital del SAT</strong>{{ $datos['Complemento']['TimbreFiscalDigital']['SelloSAT'] }}<strong>Cadena original del complemento de certificación del SAT</strong>{{ $datos['CadenaOriginal'] }}
</td></tr></table>
<table class="certificados"><tr><td><small>Fecha y hora de certificación</small>{{ $fechaHumanaCfdi($datos['Complemento']['TimbreFiscalDigital']['FechaTimbrado']) }}</td><td><small>No. serie del certificado del SAT</small>{{ $datos['Complemento']['TimbreFiscalDigital']['NoCertificadoSAT'] }}</td><td><small>No. serie del certificado del contribuyente</small>{{ $datos['Comprobante']['NoCertificado'] }}</td><td><small>RFC del proveedor de certificación</small>{{ $datos['Complemento']['TimbreFiscalDigital']['RfcProvCertif'] }}</td></tr></table></div>
<div class="leyenda {{ ENV('APP_ENV') == 'local' ? 'invalida' : '' }}">Este documento es una representación impresa de un CFDI {{ ENV('APP_ENV') == 'local' ? 'no válido ' : '' }}versión {{ $datos['Comprobante']['Version'] }}.</div>
</div></div>
<style>
/* Carta con dos áreas: el cuerpo puede reducirse y el bloque fiscal permanece
   anclado abajo, con QR legible. La zona intermedia deja visible la trama del html.
   Qt usa altura física equivalente; Chromium usa grid y el ajuste del visor. */
.cfdi-pagina{position:relative;width:935px;height:1240px}
.cfdi-hoja[data-ajuste-una-pagina]{position:relative;width:100%;height:990px;overflow:hidden}
.cfdi-hoja [data-contenido-ajustable]{position:absolute;left:0;top:0;width:100%;transform-origin:top left;-webkit-transform-origin:top left}
.cfdi-pie-fiscal{position:absolute;bottom:0;left:0;width:100%;background:#fff;z-index:2}
.cfdi-hoja [data-contenido-ajustable]>*:not(.marca-logo){position:relative;z-index:1}
.cfdi-hoja[data-ajuste-una-pagina] *{page-break-before:auto!important;page-break-after:auto!important;page-break-inside:auto!important;break-inside:auto!important}
@supports(display:grid){
 .cfdi-pagina{width:191.1mm;height:254mm;display:grid;grid-template-rows:minmax(0,1fr) auto;row-gap:3mm}
 .cfdi-hoja[data-ajuste-una-pagina]{width:100%;height:100%;min-height:0}
 .cfdi-pie-fiscal{position:static;align-self:end}
}
</style>
<script>(function(){
 function ajustar(){
  var hoja=document.querySelector('[data-ajuste-una-pagina]');
  if(!hoja)return;
  // Reservar la altura real de sellos/certificados antes de ajustar el cuerpo.
  var pagina=document.querySelector('.cfdi-pagina');
  var pie=document.querySelector('.cfdi-pie-fiscal');
  if(pagina && pie && !(window.CSS && window.CSS.supports && window.CSS.supports('display','grid'))){
   hoja.style.height=Math.max(100,pagina.clientHeight-pie.offsetHeight-12)+'px';
  }
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
  // El cuerpo es absoluto y no contiene saltos internos. Transformar su capa
  // mantiene las métricas de texto; el bloque fiscal queda fuera y no se reduce.
  contenido.style.zoom=1;
  contenido.style.webkitTransform='scale('+escala+')';
  contenido.style.transform='scale('+escala+')';
 }
 if(document.readyState==='complete')ajustar();
 else window.addEventListener('load',ajustar);
 window.addEventListener('beforeprint',ajustar);
})();
</script></body></html>
