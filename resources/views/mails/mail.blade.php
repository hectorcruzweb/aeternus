{{--
 Template corporativo del correo: logo, saludo, mensaje, adjunto y contacto de empresa.
 Dónde ajustar: Modificar aquí estética del cuerpo, dirección y leyendas de no
 responder/confidencialidad. También lo usa el envío antiguo pdf_email: comprobar ambas variantes.
 Relacionado: EmailController; NombreEmpresaReportes; DireccionReportes
 Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
--}}
@php
    // La imagen viaja dentro del correo (CID), sin depender de la URL local de la aplicación.
    $rutaLogo = public_path('images/logo/aeternus.jpg');
    $logoCorreo = isset($message) && is_file($rutaLogo) ? $message->embed($rutaLogo) : asset('images/logo/aeternus.jpg');
    $archivo = $name_pdf ?? 'Documento adjunto';
    $extension = strtoupper(pathinfo($archivo, PATHINFO_EXTENSION));
    $esExcel = in_array($extension, ['XLSX', 'XLS', 'CSV']);
    $colorArchivo = $esExcel ? '#217346' : '#a53c3c';
    $nombreDocumento = preg_replace('/\s+\d{4}-\d{2}-\d{2}\s+\d{2}-\d{2}-\d{2}$/', '', pathinfo($archivo, PATHINFO_FILENAME));
    $esReporte = stripos(($subject ?? '') . ' ' . $archivo, 'reporte') !== false;
    $nombreEmpresa = \App\Exports\NombreEmpresaReportes::formatear($empresa->nombre_comercial ?? null);
    $telefonoContacto = trim($empresa->telefono ?? '');
    $emailContacto = trim($empresa->email ?? '');
    $direccionContacto = \App\Exports\DireccionReportes::formatear($empresa);
@endphp
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject ?? 'Documento de Aeternus Funerales' }}</title>
<style>
body,table,td,a{font-family:Arial,Helvetica,sans-serif}table{border-collapse:collapse}.mensaje-usuario p{margin:0 0 14px}.mensaje-usuario a{color:#8d7528}.mensaje-usuario ul,.mensaje-usuario ol{padding-left:24px}
@media only screen and (max-width:600px){.correo-exterior{padding:16px 8px!important}.correo-cabecera{padding:22px 20px!important}.correo-cuerpo{padding:26px 20px!important}.correo-logo{max-width:170px!important}.correo-titulo{font-size:24px!important}.correo-contacto{display:block!important;width:100%!important;padding:4px 0!important;text-align:center!important}.correo-etiqueta{font-size:9px!important}.correo-firma{padding:0 20px 26px!important}}
</style>
</head>
<body style="margin:0;padding:0;background-color:#f5f5f4;color:#343a40;font-family:Arial,Helvetica,sans-serif;">
<!-- Maquetación por tablas y estilos inline para clientes de correo. -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f5f5f4"><tr><td class="correo-exterior" align="center" style="padding:32px 16px;">
<!--[if mso]><table role="presentation" width="640" align="center"><tr><td><![endif]-->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="max-width:640px;border:1px solid #dedede;background-color:#ffffff;">
<tr><td class="correo-cabecera" style="padding:26px 30px;border-bottom:2px solid #b18b1e;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
    <td width="60%" align="left" style="vertical-align:middle;"><img class="correo-logo" src="{{ $logoCorreo }}" width="210" alt="Aeternus Funerales" border="0" style="display:block;width:100%;max-width:210px;height:auto;border:0;"></td>
    <td class="correo-etiqueta" width="40%" align="right" style="vertical-align:middle;font-size:10px;line-height:1.5;letter-spacing:1px;font-weight:bold;color:#967a25;">DOCUMENTO ADJUNTO</td>
  </tr></table>
</td></tr>
<tr><td class="correo-cuerpo" style="padding:30px 32px 24px;">
  <p style="margin:0 0 12px;color:#967a25;font-size:11px;letter-spacing:1.5px;line-height:1.5;">{{ strtoupper($nombreEmpresa) }}</p>
  <h1 class="correo-titulo" style="margin:0 0 24px;font-size:28px;line-height:1.25;font-weight:bold;color:#262a30;">{{ $esReporte ? 'Tu reporte está listo' : 'Tu documento está listo' }}</h1>
  <p style="margin:0 0 20px;font-size:16px;line-height:1.6;color:#343a40;">Hola{{ !empty($client_name) ? ', ' . $client_name : '' }}.</p>
  @if (!empty($mensaje_html))
  {{-- mensaje_html únicamente proviene del purificador del controlador de correo. --}}
  <div class="mensaje-usuario" style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.7;color:#555b63;overflow-wrap:break-word;">{!! $mensaje_html !!}</div>
  @elseif (!empty($mensaje_usuario) && trim($mensaje_usuario) !== '')
  <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#555b63;">{!! nl2br(e($mensaje_usuario)) !!}</p>
  @else
  <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#555b63;">Te compartimos el documento solicitado para su revisión.</p>
  @endif
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:22px;border:1px solid #dedede;background-color:#f8f8f7;"><tr>
    <td width="58" style="padding:18px 0 18px 16px;vertical-align:middle;">
      <table role="presentation" cellpadding="0" cellspacing="0"><tr><td align="center" bgcolor="{{ $colorArchivo }}" style="padding:11px 6px;background-color:{{ $colorArchivo }};color:#ffffff;font-size:10px;font-weight:bold;line-height:1.2;">{{ $extension ?: 'DOC' }}</td></tr></table>
    </td>
    <td style="padding:18px 16px 18px 12px;vertical-align:middle;word-break:break-word;">
      <p style="margin:0 0 5px;font-size:14px;line-height:1.5;font-weight:bold;color:#343a40;">{{ $nombreDocumento }}</p>
      <p style="margin:0;font-size:12px;line-height:1.5;color:#777d84;">Archivo adjunto · {{ $extension ?: 'Documento' }}</p>
    </td>
  </tr></table>
  <p style="margin:12px 0 0;font-size:12px;line-height:1.6;color:#777d84;">Encontrarás los archivos adjuntos en este correo.</p>
</td></tr>
<tr><td class="correo-firma" style="padding:0 32px 30px;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="padding-top:22px;border-top:1px solid #e2e2e2;">
    <p style="margin:0 0 10px;font-size:13px;font-weight:bold;line-height:1.5;color:#555b63;">¿Necesitas ayuda?</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
      @if (!empty($telefonoContacto))<td class="correo-contacto" width="45%" align="left" style="text-align:left;padding:4px 12px 4px 0;font-size:12px;line-height:1.6;color:#737981;">Tel. <a href="tel:{{ preg_replace('/[^0-9+]/', '', $telefonoContacto) }}" style="color:#737981;text-decoration:none;">{{ $telefonoContacto }}</a></td>@endif
      @if (!empty($emailContacto))<td class="correo-contacto" width="55%" align="right" style="text-align:right;padding:4px 0;font-size:12px;line-height:1.6;color:#737981;word-break:break-word;">@if (filter_var($emailContacto, FILTER_VALIDATE_EMAIL))<a href="mailto:{{ $emailContacto }}" style="color:#737981;text-decoration:underline;">{{ $emailContacto }}</a>@else{{ $emailContacto }}@endif</td>@endif
    </tr></table>
    @if (!empty($direccionContacto))<p align="center" style="margin:16px 0 0;font-size:11px;line-height:1.7;color:#888d93;text-align:center;">Dirección: {{ $direccionContacto }}</p>@endif
    <p style="margin:18px 0 0;font-size:11px;line-height:1.7;color:#888d93;text-align:center;">Este mensaje fue enviado automáticamente. Por favor, no respondas a este correo. Para recibir ayuda, utiliza los datos de contacto anteriores.</p>
    <p style="margin:14px 0 0;padding-top:12px;border-top:1px solid #eeeeee;font-size:11px;line-height:1.7;color:#888d93;text-align:justify;"><strong>Confidencialidad:</strong> Este correo y sus archivos adjuntos pueden contener información confidencial y datos personales, y están dirigidos exclusivamente a su destinatario. Si lo recibiste por error, por favor no lo uses, copies ni compartas; elimina el mensaje y sus adjuntos y notifícalo a través de los datos de contacto indicados arriba.</p>
  </td></tr></table>
</td></tr>
</table>
<!--[if mso]></td></tr></table><![endif]-->
<p style="margin:16px 0 0;font-size:10px;line-height:1.5;color:#999da2;">{{ $nombreEmpresa }}</p>
</td></tr></table>
</body></html>
