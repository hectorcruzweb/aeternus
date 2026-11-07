{{-- Correo de recuperación: diseño propio con la identidad del visor.
     $resetUrl proviene de PasswordResetNotification; no reconstruir ni registrar
     el token en esta vista. Contacto desde empresa; imágenes embebidas por CID. --}}
@php
    $rutaLogo = public_path('images/logo/aeternus.jpg');
    $logoCorreo = isset($message) && is_file($rutaLogo) ? $message->embed($rutaLogo) : asset('images/logo/aeternus.jpg');
    // PNG de candado generado desde trazos simples: compatible con clientes sin SVG.
    $candadoPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAABN0lEQVR4nO3ayY0CMRAF0O8vx0FYncycJxnCmkTg1BIawSx2bejXOwJyVXlXN0BrrQkbGUGvn5fbq++Oj6/QnEaVwrM6gihY/MrvVw3vABaFeM6GgUJrPWNvmAj2UyHnd1HT33UPeFbEX0fxv7OjXAdcN4qP7gQiwOr6jTgKad2g9/q1bp9wtjuK3rOAEEfP6Wk1et/bsVwGhDhCHCGO2Qm01lpLNHYbiHx48crOjZPvXvxuHswI6mE1H0YG87aS17QIHP02x3IwCHGEOEIcIY4QNzODP+7gWSfJrHJ0nZ9FdwQhjtUuLtG3TEIcIY7RAX/b5HoTVDgGjyd/hcm6BxDiCHGEOEIcIW5mBs98lngixBHiCHGEuGnRSNU3RW4z4Ciwe1vlxchgnlbzYUZQa1XyaK3h7dwBp65+Is4ZiWAAAAAASUVORK5CYII=');
    $candadoCorreo = isset($message) ? $message->embedData($candadoPng, 'seguridad-cuenta.png', 'image/png') : 'data:image/png;base64,'.base64_encode($candadoPng);
    $nombreEmpresa = \App\Exports\NombreEmpresaReportes::formatear($empresa->nombre_comercial ?? null);
    $telefonoContacto = trim($empresa->telefono ?? '');
    $emailContacto = strtolower(trim($empresa->email ?? ''));
    $direccionContacto = \App\Exports\DireccionReportes::formatear($empresa);
@endphp
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Recuperación de contraseña</title>
<style>
body,table,td,a{font-family:Arial,Helvetica,sans-serif}table{border-collapse:collapse}
@media only screen and (max-width:600px){.correo-exterior{padding:16px 8px!important}.correo-cabecera{padding:22px 20px!important}.correo-cuerpo{padding:26px 20px!important}.correo-logo{max-width:170px!important}.correo-titulo{font-size:24px!important}.correo-contacto{display:block!important;width:100%!important;padding:4px 0!important;text-align:center!important}.correo-etiqueta{font-size:9px!important}.correo-firma{padding:0 20px 26px!important}.reset-boton{padding:14px 18px!important;font-size:15px!important}.reset-icono{width:45px!important}}
</style></head>
<body style="margin:0;padding:0;background-color:#f5f5f4;color:#343a40;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f5f5f4"><tr><td class="correo-exterior" align="center" style="padding:32px 16px;">
<!--[if mso]><table role="presentation" width="640" align="center"><tr><td><![endif]-->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="max-width:640px;border:1px solid #dedede;background-color:#ffffff;">
<tr><td class="correo-cabecera" style="padding:26px 30px;border-bottom:2px solid #b18b1e;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
<td width="55%" style="vertical-align:middle;"><img class="correo-logo" src="{{ $logoCorreo }}" width="210" alt="Aeternus Funerales" border="0" style="display:block;width:100%;max-width:210px;height:auto;border:0;"></td>
<td class="correo-etiqueta" width="45%" align="right" style="vertical-align:middle;font-size:10px;line-height:1.5;letter-spacing:1px;font-weight:bold;color:#967a25;">SEGURIDAD DE LA CUENTA</td>
</tr></table></td></tr>
<tr><td class="correo-cuerpo" style="padding:30px 32px 24px;">
<p style="margin:0 0 12px;color:#967a25;font-size:11px;letter-spacing:1.5px;line-height:1.5;">DEPARTAMENTO DE SISTEMAS</p>
<h1 class="correo-titulo" style="margin:0 0 24px;font-size:28px;line-height:1.25;font-weight:bold;color:#262a30;">Recupera el acceso a tu cuenta</h1>
<p style="margin:0 0 16px;font-size:16px;line-height:1.6;">Hola{{ !empty($nombreDestinatario) ? ', '.$nombreDestinatario : '' }}.</p>
<p style="margin:0 0 24px;font-size:15px;line-height:1.7;">Usa el siguiente botón para establecer una nueva contraseña.</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#f8f5ed" style="background-color:#f8f5ed;border-left:3px solid #b18b1e;"><tr>
<td class="reset-icono" width="65" style="padding:18px 0 18px 16px;vertical-align:middle;"><img src="{{ $candadoCorreo }}" width="32" height="32" alt="" border="0" style="display:block;border:0;"></td>
<td style="padding:18px 16px 18px 10px;vertical-align:middle;"><p style="margin:0 0 5px;font-size:15px;line-height:1.5;font-weight:bold;">Solicitud de restablecimiento</p><p style="margin:0;font-size:13px;line-height:1.6;color:#656c73;">El cambio se realizará únicamente al crear tu nueva contraseña.</p></td>
</tr></table>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:26px 0 22px;">
<table role="presentation" cellpadding="0" cellspacing="0"><tr><td bgcolor="#ad891c" align="center" style="background-color:#ad891c;border:1px solid #ad891c;">
<a class="reset-boton" href="{{ $resetUrl }}" style="display:inline-block;padding:15px 28px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.4;font-weight:bold;color:#ffffff;text-decoration:none;text-align:center;">Crear nueva contraseña</a>
</td></tr></table></td></tr></table>
<p style="margin:0 0 22px;font-size:14px;line-height:1.7;">¿No solicitaste este cambio? Puedes ignorar este correo.</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#f1f2f3" style="background-color:#f1f2f3;table-layout:fixed;"><tr><td style="padding:14px 16px;word-break:break-all;overflow-wrap:anywhere;">
<p style="margin:0 0 6px;font-size:12px;line-height:1.5;font-weight:bold;color:#656c73;">Enlace alternativo</p>
<p style="margin:0;font-size:12px;line-height:1.7;word-break:break-all;"><a href="{{ $resetUrl }}" style="color:#626b73;text-decoration:underline;word-break:break-all;">{{ $resetUrl }}</a></p>
</td></tr></table>
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
    <p style="margin:14px 0 0;padding-top:12px;border-top:1px solid #eeeeee;font-size:11px;line-height:1.7;color:#888d93;text-align:justify;"><strong>Confidencialidad:</strong> Este correo puede contener información confidencial y datos personales, y están dirigidos exclusivamente a su destinatario. Si lo recibiste por error, por favor no lo uses, copies ni compartas; elimina el mensaje y notifícalo a través de los datos de contacto indicados arriba.</p>
  </td></tr></table>
</td></tr>
</table>
<!--[if mso]></td></tr></table><![endif]-->
<p style="margin:16px 0 0;font-size:10px;line-height:1.5;color:#999da2;">{{ $nombreEmpresa }}</p>
</td></tr></table>
</body></html>
