<!doctype html><html lang="es"><head><meta charset="UTF-8"><title>Entrega de cenizas</title></head><body class="entrega-secciones">
<!-- Iconos Lucide v0.468.0, rasterizados para compatibilidad con Qt.
ISC License

Copyright (c) for portions of Lucide are held by Cole Bemis 2013-2022 as part of Feather (MIT). All other copyright (c) for Lucide are held by Lucide Contributors 2022.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.

-->
@include('layouts.estilos')
<style>
/* Autorización por secciones: datos y firma provienen del documento original.
   Tablas de presentación compatibles con Qt; estilos locales para no afectar
   otros documentos. El pie se agrega fuera de .marca-agua-contenido. */
html body.entrega-secciones{font-family:Arial,sans-serif!important;font-size:16px;margin:0;color:#30383b!important}
.entrega-secciones header{display:table;width:100%;border-bottom:2px solid #aa8a29;padding-bottom:18px}
.entrega-secciones header>div{display:table-cell;vertical-align:middle}
.entrega-secciones .marca{width:34%}.entrega-secciones .marca img{width:210px;max-width:100%}
.entrega-secciones .empresa{text-align:right;font-size:12px;line-height:1.65}.entrega-secciones .empresa span{display:block}
.entrega-titulo{width:100%;border-collapse:collapse;margin:22px 0 12px}.entrega-titulo h1{font-size:28px;margin:0;line-height:1.3}
.entrega-folio{text-align:right;width:22%;font-size:12px}.entrega-folio strong{display:block;font-size:26px;color:#8c7229}
.entrega-fecha{text-align:right;font-size:12px;color:#777;margin-bottom:24px}
.entrega-secciones h2{font-size:17px;color:#796226;margin:0 0 12px}.entrega-icono{width:18px;height:18px;vertical-align:middle;margin-right:8px;object-fit:contain}
.entrega-declaracion p{font-size:16px;line-height:1.8;margin:0}
.entrega-persona{padding:16px;background:#f7f5ee;border-left:3px solid #ad8b2a;margin:23px 0}.entrega-persona small{display:block;color:#737373;font-size:12px;margin-bottom:6px}.entrega-persona strong{font-size:19px}
.entrega-mono{width:14px;height:18px;vertical-align:middle;margin-right:6px}
.entrega-duo{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0}.entrega-duo td{width:50%;vertical-align:top}.entrega-duo td:first-child{padding-right:8px}.entrega-duo td:last-child{padding-left:8px}
.entrega-campo{border:1px solid #dedede;padding:16px;word-wrap:break-word;box-sizing:border-box}.entrega-campo label{display:block;font-size:12px;color:#737373;margin-bottom:9px}.entrega-campo strong{font-size:16px;font-weight:normal;line-height:1.6}
.entrega-contagioso{color:#af4840;font-weight:bold!important}.entrega-causa{margin-top:16px;background:#fafafa}
.entrega-firma{width:66%;margin:30px auto 20px;text-align:center;page-break-inside:avoid}.entrega-firma h2{font-size:13px;color:#777}.entrega-firma img.firma{width:auto;max-width:160px;max-height:65px;height:auto;margin:0 auto 7px;display:block}
.entrega-firma-linea{border-bottom:1px solid #666;min-height:65px;margin-bottom:9px}.entrega-firma strong{font-size:14px}.entrega-firma span{display:block;font-size:12px;color:#777}
@media screen{html body.entrega-secciones{min-height:279.4mm!important}.entrega-secciones .solicitud-visor-pie{top:246mm!important;bottom:auto!important}}
@supports(display:grid){
.entrega-secciones .marca img{width:168px}.entrega-secciones .empresa{font-size:9.6px}.entrega-titulo h1{font-size:22.4px}.entrega-folio strong{font-size:20.8px}.entrega-fecha{font-size:9.6px}
.entrega-secciones h2{font-size:13.6px}.entrega-declaracion p{font-size:12.8px}.entrega-persona strong{font-size:15.2px}.entrega-persona small,.entrega-campo label{font-size:9.6px}
.entrega-campo strong{font-size:12.8px}.entrega-icono{width:14px;height:14px}.entrega-firma strong{font-size:11.2px}.entrega-firma span{font-size:9.6px}
}

/* Reutiliza la presentación por secciones, con casillas de cantidad y urna.
   La cantidad 1 (UNA), el receptor y la firma se conservan del formato original. */
.entrega-urna{border:1px solid #ddd;padding:17px;margin-bottom:23px}
.entrega-urna table{width:100%;border-collapse:collapse}.entrega-urna td{vertical-align:top}
.entrega-urna td:first-child{width:25%}.entrega-urna label{display:block;font-size:12px;color:#777;margin-bottom:7px}
.entrega-urna strong{font-size:16px}.entrega-declaracion p{word-wrap:break-word;overflow-wrap:break-word}
.entrega-declaracion .bold{font-weight:bold!important;display:inline!important;white-space:normal!important}
@supports(display:grid){.entrega-urna label{font-size:9.6px}.entrega-urna strong{font-size:12.8px}}
</style>
<div class="marca-agua-contenido">
{{-- Misma silueta y transparencia del título de propiedad y de la guía.
     Capa decorativa sin espacio en el flujo: no mueve datos, firmas ni pie. --}}
<div class="documento-marca-titulo" aria-hidden="true">{!! \App\Exports\MarcaTituloReportes::svg() !!}</div>
<header><div class="marca"><img src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="empresa"><strong>{{ $empresa->razon_social }}</strong><span>{{ mb_convert_case(mb_strtolower($empresa->calle, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} núm. ext. {{ $empresa->num_ext }}</span><span>@if(trim($empresa->colonia))Col. {{ mb_convert_case(mb_strtolower($empresa->colonia, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} · @endif C. P. {{ $empresa->cp }} · {{ $empresa->ciudad }}, {{ $empresa->estado }}</span><span>Tel. {{ $empresa->telefono }} · Fax {{ $empresa->fax }}</span></div></header>
<table class="entrega-titulo"><tr><td><h1>Entrega de cenizas</h1></td><td class="entrega-folio">{{ $datos['tipo_solicitud_texto'] }}<strong>{{ $datos['servicio_id'] }}</strong></td></tr></table>
<div class="entrega-fecha">{{ $empresa->ciudad }}, {{ $empresa->estado }}, a {{ \Carbon\Carbon::now('America/Mazatlan')->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.</div>
<section class="entrega-persona"><small><img class="entrega-mono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAtCAYAAAC53tuhAAAE1klEQVR4nJ2YeYxeYxTGfx1qmTFFE5GWjKi1iomiSsQSia1qKYaqvWqJhDaWiJ0/SFFCUJVp7FHLpKhtYsmIWmNvo00jZaIYMdVa0hE6UzmT523OnLz33m/mSb6Z77733ve573nPfc5zvmFNTU3UgF2Ao4HxwJ7ADsD2OtcF/AwsA74A2oHvqyYcVkE8GrgNuADYpJYnBNYD84FbgF+LLqormeBiYAVw0SBIDZsClwDfARcOhnhL4GVgHtDA0LGVVr5Qc5aGegTwJnBwZiLbw1bgB+1h2sed3ceitHvm3g+B44A/i4g/AA4JN/UANwFzalzptcCtmVW+BxyRC/WMDOnHwK6DIDXcpXs+DeOHA+fHFdsDrAJGuQvteD+gm6FhO+DrMGentmRDWvGx4YIevbeJdDNgCnAf8DmwFujTx75/BtwLnAgM1z2/AZM0V8JOmrc/9Q3TGYinlEwHAJcCpyvxctga2F+fWXqQF4C5wJfAAumA39L2FOoup0SGc6VSV9p2MDRYNO6WFsx347+YMCXivkCwouC1GAqWS2YTLPT1aY//ChdXkb4O7As063sZPKnhX/uTiH+kGCYYq8PY1cAS4BvgmnCuu2K+zlqIX9GqesN4Y0guj15F442COVd54o1S5vAccJLOdYRz17vvVoU8OpTZ9iq1Zeb92xPHUP4HnOOO7w/n7YEuA64DjgnnHtf/DVKqGK0l9idl9RZKsPReG44MK30WOJNymEwe5I5PVnXyGd3glesf4EkGYlbmuEw+bdJpYSwm3iOKxIAi8VC4yORvgjvu0or7b8xguoq/X60vOn3Ag+nAEye/5GHK4/EO8EmG9AFtRYI5ltnhmqeBlTliw+3h+LCwr6azE8M19irODGNXBRHqBW6sMnvtqYIIvwO7AXsB77rqY/hD4+YyvSP9VhUt4VH5sFLiscBX4cZXtV8jQzJZDiz182krDnRjVh73ANZUmT0rhzeEsRMCqSXYWYE0SaknRSVxTa329h75ryKYWr0Uxi6X7fF4DHgtN0GZoR8jlakP40Z4ShgbJfEfHpJuXKbyVRr6pgzp6oxI2L4+H0gN2wRzURPxaE1G2NfJwLowbvlwaGaORlW3+lqJTa9flEv0aAE+CmMT1FsVYaz8W03EczKdxGw9TAxlW5hjnToRjyky+aXEpwFXhLGOUH8TngF2zOj1qfJZHndKBbPE5o2eCDf8pBCbwHuYhz4+U4cXaNWTpGqeZ6ESdgCxdXaLQiKsV8E35fEYlymZnXqPE1Yqer6SjRT55p64Vf2Ox0x1DR4NobCX4e2MLRqfSqMJyMRMtlrIphbsq0llDq2qXhGLJLkezXXBW6HKElsaw3klpOiXA2t1IqYFg2A4uy5YVST8MZmsujyc2VdLvrhqa8wItXhZGNvWiBeHwRaVRSNLRrAto0AtMgn+IUdI8ZJpbFarOjncu9j2uFFJZMU+YoYSwqxsNHFWwZBy3RzO3yFdzzX0S82Jpuo0Rs4jZnYObwWHYni/QK8jlgNHmWOpc+9dc6YwRHQVJNhUdQ9lmKdfGPptUtTZM0p+m+qRKHQX9ENFGb9Wem0Nvvn3fuSKhLkGSywLaYKFyDxXmSuxJs3Cbd1lgiXl3jnRqfpJ0X6ztEyNr0MV9lFIY0+2Ef8DzwMQzZMMiV8AAAAASUVORK5CYII=">Nombre del fallecido</small><strong>{{ $datos['nombre_afectado'] }}</strong></section>
<section class="entrega-urna"><h2><img class="entrega-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAsCAYAAAB2d9g5AAAC4ElEQVR4nMWYS2gUQRCGv51kD4IHPSi6IDmYqDEXwZP4iCgexCfxhfhAgnjxLEaiiYqiCCJBiOBNRQ9RQXMxohJ84E30okZcMUIiMRFEUJCAu1JLDXTKnnV3Mrv+0Mn23931T9fUVDWdOtI6jwhsB04C8ykP74AOoMc3GBRZGEcMXSNrKVewIgiKjHWoe4jpUi9qiUaPtrzhU6ZvxxcUsfn/XboHuA0M65Pbpy8FeW3Damu3z6WzgOvAKpJDBmjRtlc38yXc4a2ExSzW6IYKLt0PLKXyWA3sE8EtZkB226jRaCOyFKS0NaotFy0iuMiQ8g0NMHkMAJ2GW+z7LOLsqlSkRPClIU8ADfp7kxn76jFiuQ1OAhBbLl7IZ3EZWOuQW7X58MjD9QPbnH4v0eiWHd6JMGQxArR5+HZgrIT1d4F74TuUbPCwyORvwHJg0DP2HmgGvhdZ/wA4ID8C5+mPATnP5CywUP9H4a2+s6xn7LdG/qgrmFHfu1H7UzNEgz7QvzCicyUefjl8jebUma7gUWCGMymvCaGYm6PQB+wwiT+j77ogmAZ2mkXyAPeJj15PEZbknRbBzcA0Z2AIOMPkcVqqg9OfDqwTwZVm4o2YddAiH1YIB80iWGfIpwmIRdmq8wl+IjkM+gTnGjJbacG8IacmKJg2/SmBJ9s3JSjYZPqj1RYcE8E3VRR8HWg9q5Zgv09wCVCfgFi92nLRJ4IfgVcmss4nIHjBROnzMGgEZ83kjVpU42IFsN5wXfInFLypSdtFd8wTXA1wyXBD4Rk1FMxpSXIhVf5iDMEuXeuiPTxNuBX+iieADgKHyhA7rGtcPAGuhh17EJaDzg/DnfMUaB92eWJBbLW6ROBJ3HImGTf8NXVLEPHOjquHXIyrrQ8u6TPwTA+2OWP0FPAYmO3wc3R+p84JkVMbMjYBtWXexywDPpcwL9CD71+XDcEk7mNi3d8EVRCaABGMex9TLgou/QNDKZM6myPz6wAAAABJRU5ErkJggg==">Urna entregada</h2><table><tr><td><label>Cantidad</label><strong>1 (una)</strong></td><td><label>Descripción</label><strong>{{ $datos['descripcion_urna'] ?? 'N/A' }}</strong></td></tr></table></section>
<section class="entrega-declaracion"><h2><img class="entrega-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACcAAAAsCAYAAADmZKH2AAAB8klEQVR4nO2YPS8EQRiAn9tcixKVhuD+gegkFHQoacSJj1ajkkvcP2AvudP5LAgqBX6CKMgR0fqIH6AhTjaZTcaY3dvb7Oxs4UkmuZnsJE/e+Xjn3tzaXB8BzAKTwBDQSTLcA2VgL8rHec1Yl5g8QvIMArtAO1CJI3cEDGMWF/gCamEfOUp/PgUxnyqwSAtyU5ooekuRi9maUQkTVOW8zS+zDjyQHAsBgsUoch1Kv06y1AIEqzpBVS4NalEFbchFFrQlF0lQd88lyZu41H0Kyj6uCQdXI/hhOnI3Sr8E9GlO65Jmbtm0XFXpTwOPQENpulTWY1ruFLiKO1mVqxu442aAyyTk/IxwL34ndShGxRPsBHiJOjEX8p6zQUPu2LznmvIvF5f/yKUduRngDrgFxjFEnMTv5UE5UW8CvcA3liOnihHxv4JxuWWNmMeaiajJchPSHvL2k05sK2D8EIN7Li8efd1ibAdok54xSyFiFVNivtyfnKYsn2tDzJfzygKrwD6/0UmlJibvuQNgheaspCWmnlZXRKXV00paV0klQDC1pWx2z/m1i0/RijbEwtLXtmhWccgwDhnGIcM4ZIeC0n/NityAqKPIXOc1tYwNoB/7uGrkShkROwPOs7KsSFz4RUVdreTJgtAzcCzqKWPAuzf4A85VYoKRXvpNAAAAAElFTkSuQmCC">Constancia de recepción</h2><p>
            Yo, <span class="bold">
                @if (isset($datos['operacion']['cliente']))
                    {{ $datos['operacion']['cliente']['nombre'] }}
                @else
                    N/A
                @endif
            </span>. Por medio de la presente, certifico que recibí de Aeternus Funerales 1 (UNA) urna hecha de <span
                class="bold">
                @if ($datos['descripcion_urna'] == null)
                    N/A
                @else
                    {{ $datos['descripcion_urna'] }}
                @endif
            </span>
            ,
            misma que contiene las cenizas correspondientes a el/la ( @if (!isset($datos['titulo']['titulo']))
                N/A
            @else
                {{ $datos['titulo']['titulo'] }}
            @endif). <span class="bold">{{ $datos['nombre_afectado'] }}</span>.
        </p></section>
{{-- Se conserva la firma del área 26 de la solicitud y el nombre del cliente. --}}
<section class="entrega-firma"><h2><img class="entrega-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAqCAYAAADBNhlmAAADJElEQVR4nNWYW4hOURTHf/P5PIiJXHNJJppJkXIZMm5Fcik1xSSDMk15YB6Qy1AjcsnlQWOSZHiYPCjhwbgUDzIuY1ymqBmRKPfMCOPyotFifXUs+3y+M3Pm+OZfu87aZ+29/2fvtdZe62SUFmUTInoCS4C5wFggE6gDaoG9wMegE8ZDJLcUOAj0Mv0zta3QdinIpLGQyOUDVQ5yXgwELuqHREowEzgSQF8+ZESUBNcAfT3yN92lDGCAvv9ixqyN0gYXGnkjcEKf3wEHgB9AuUdnRlQ72AcYbXbvqEPvlJFHRkVwqpFvKkmLrkZujorgJCPf8NFbZeSnURGcYORbDp2twAbTdyEqJxln5HojH9Pg7IXcJoei2MHherV57eql0ZEbxKIQeBMFwfH/2D1Bg+e5BVgEVAdZJB7i8d5x6JQAu9SzNwGvgi4SD9FB7jl0HuuutRntOeKJRr5LB6CtBEcB3TzyZ+AJaURwupGv0UEIi+BN0oigpFGzoyIYN7JkxOuAXGCMBmIJwE3aPgCtJkBHRlAKneOaZHoxSJsfrgJfO/qI5wPnHeRStcdWbbVan4RKUJLOypDmywVOAzVadoZCsNDs3Gtgmfb1ALJ04Tm6uK0vXMjTwJ0fhg1ONn2rlUgCQuiZBmapyLp73p0DGpW8BG8LmWcxcLI9OzjF9F3x0S0G+nnk75rrrde6RNL/h45xFXoSLuwEXmhxtd+PYBfTZ2W0rJTM2Aux2/ceuUaJljjGbnHMeRjYDAzWD5fwdlt94g+C9SkkmZV2ILAPNyr0P0yy0jQROVwZUp23sBeC941SuRbe/YGhancLjM424Dn+sCm91zQSGOIzNkvD1S/fyCgtyh4GPEhiJzgSg2kp6DUBvWkfZsXUQ5enOEBsriBF3YYAROp9/u9UJW6SM8A84G2SSc5qHZJqwbM7AEFxlpVaFsiNlEBzzNSq8ktiB3BZE4NG/beSp0E3md1ZVKvtSiLx6a+3v+e/rs6SqJP36Am16NrFYoOkM2KkOWKkOWJ0AoIFapCtadYahZsQ3A7kkH7IEW6d4ojLgEf/m4gDwqnsJ0kcuLStE4KvAAAAAElFTkSuQmCC">Persona que recibe</h2><div class="entrega-firma-linea">@if(!empty($firmas['cliente']))<img class="firma" alt="" src="{{ $firmas['cliente'] }}">@endif</div><strong>{{ $datos['operacion']['cliente']['nombre'] ?? 'N/A' }}</strong><span>Firma</span></section>
</div>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'servicios_funerarios', 'altoMarcaAgua' => 1050])
<style>
body .marca-agua-contenido{position:relative}
body .marca-agua-contenido>.documento-marca-titulo{position:absolute;top:30mm;right:0;width:58%;z-index:0;pointer-events:none}
.documento-marca-titulo svg{display:block;width:100%;height:180mm}
.marca-agua-contenido>header,.marca-agua-contenido>table,.marca-agua-contenido>section,.marca-agua-contenido>.constancia-fecha,.marca-agua-contenido>.cremacion-fecha,.marca-agua-contenido>.entrega-fecha{position:relative;z-index:1}
</style>
</body></html>