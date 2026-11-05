<!doctype html><html lang="es"><head><meta charset="UTF-8"><title>Constancia de no embalsamiento</title><style>.no-embalsamiento-secciones .constancia-enfasis{display:inline!important;font-weight:bold;}</style></head><body class="no-embalsamiento-secciones">
<!-- Iconos Lucide v0.468.0, rasterizados para compatibilidad Qt.
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
/* Constancia por secciones. Conserva declaración, técnica, resultado y firma
   originales. No modifica consultas ni el contenido de los datos guardados. */
html body.no-embalsamiento-secciones{font-family:Arial,sans-serif!important;font-size:15px;margin:0;color:#30383b!important}
.no-embalsamiento-secciones header{display:table;width:100%;border-bottom:2px solid #ac8a27;padding-bottom:16px}
.no-embalsamiento-secciones header>div{display:table-cell;vertical-align:middle}
.no-embalsamiento-secciones .marca{width:33%}.no-embalsamiento-secciones .marca img{width:210px;max-width:100%}
.no-embalsamiento-secciones .empresa{text-align:right;font-size:12px;line-height:1.6}.no-embalsamiento-secciones .empresa span{display:block}
.constancia-titulo{width:100%;margin:20px 0 12px;border-collapse:collapse}.constancia-titulo h1{font-size:26px;margin:0;text-align:left;line-height:1.3}
.constancia-folio{text-align:right;font-size:12px;vertical-align:middle;width:22%}.constancia-folio strong{display:block;color:#8b7128;font-size:25px}
.constancia-fecha{font-size:12px;color:#757575;text-align:right;margin-bottom:18px}
.constancia-identidad{width:100%;border-collapse:collapse;background:#f7f5ee;border-left:3px solid #ad8c2b;margin:0 0 18px}
.constancia-identidad td{padding:12px;width:50%;vertical-align:top}.constancia-identidad small{display:block;font-size:11px;color:#777;margin-bottom:4px}.constancia-identidad strong{font-size:15px}
.constancia-seccion{margin:15px 0 0}.constancia-seccion h2{font-size:16px;color:#796225;margin:0 0 8px;padding-bottom:5px;border-bottom:1px solid #ddd}
.constancia-seccion p,.constancia-seccion .size-18px{font-size:15px!important;line-height:1.65!important;margin:0 0 9px;text-align:justify}
.constancia-seccion .bold{font-weight:bold!important;white-space:normal!important}.constancia-seccion p{word-wrap:break-word;overflow-wrap:break-word}.constancia-seccion .lista{padding-left:17px!important;margin:0!important}.constancia-seccion .lista p{margin:0 0 9px}.constancia-seccion .lista span{margin-left:0!important}
.constancia-resultado{background:#f7f7f5;padding:12px 15px;border-left:2px solid #b7a775;page-break-inside:avoid;box-sizing:border-box}.constancia-resultado p{width:auto!important}
.constancia-firma{margin-top:12px;text-align:center;page-break-inside:avoid}
.constancia-firma .firma{width:auto;max-width:150px;height:auto;max-height:42px;margin:0 auto 5px;display:block}
.constancia-firma .texto-base{font-size:13px!important}.constancia-firma .w-50{width:55%!important}
/* El pie del visor es hermano del contenido; mantiene su espacio de hoja. */
@media screen{html body.no-embalsamiento-secciones{height:auto!important;min-height:279.4mm!important;box-sizing:border-box}.no-embalsamiento-secciones .solicitud-visor-pie{top:auto!important;bottom:5mm!important}}
@supports(display:grid){
.no-embalsamiento-secciones .marca img{width:168px}.no-embalsamiento-secciones .empresa{font-size:9.6px}
.constancia-titulo h1{font-size:20.8px}.constancia-folio strong{font-size:20px}
.constancia-identidad td{padding:9.6px}.constancia-identidad strong{font-size:12px}
.constancia-identidad small{font-size:8.8px}.constancia-fecha{font-size:9.6px}
.constancia-seccion h2{font-size:12.8px}.constancia-seccion p,.constancia-seccion .size-18px{font-size:12px!important}
.constancia-firma .texto-base{font-size:10.4px!important}
}

/* No embalsamiento por secciones: mantiene declaración y deslinde originales,
   firma de contratante y los iconos del diseño aprobado. */
.constancia-icono{width:17px;height:17px;object-fit:contain;vertical-align:middle;margin-right:8px}
.constancia-mono{width:12px;height:15px;object-fit:contain;vertical-align:middle;margin-right:5px}
.constancia-deslinde{padding:17px;background:#f7f7f5;border-left:2px solid #b8a87a;page-break-inside:avoid}
.constancia-firma{width:66%;margin:30px auto 20px}.constancia-firma h2{font-size:13px;color:#777}
.constancia-firma-linea{min-height:65px;border-bottom:1px solid #666;margin-bottom:9px}
.constancia-firma strong{font-size:14px}.constancia-seccion .bold{display:inline!important}
@media screen{html body.no-embalsamiento-secciones{min-height:279.4mm!important}.no-embalsamiento-secciones .solicitud-visor-pie{top:246mm!important;bottom:auto!important}}
@supports(display:grid){.constancia-icono{width:14px;height:14px}.constancia-mono{width:10px;height:12px}.constancia-firma strong{font-size:11.2px}}
</style><div class="marca-agua-contenido">
{{-- Misma silueta y transparencia del título de propiedad y de la guía.
     Capa decorativa sin espacio en el flujo: no mueve datos, firmas ni pie. --}}
<div class="documento-marca-titulo" aria-hidden="true">{!! \App\Exports\MarcaTituloReportes::svg() !!}</div>
@php
    // Formatea únicamente los datos configurados de la empresa; no inventa la colonia.
    // Omite puntos o espacios guardados como sustituto de un dato vacío.
    $coloniaConstancia = trim((string) $empresa->colonia, " \t\n\r\0\x0B.,");
    $direccionConstancia = mb_convert_case(trim((string) $empresa->calle), MB_CASE_TITLE, 'UTF-8')
        . ' núm. ext. ' . trim((string) $empresa->num_ext);
    if ($coloniaConstancia !== '') {
        $direccionConstancia .= ', Col. ' . mb_convert_case($coloniaConstancia, MB_CASE_TITLE, 'UTF-8');
    }
    $direccionConstancia .= ', C. P. ' . trim((string) $empresa->cp)
        . ', ' . trim((string) $empresa->ciudad) . ', ' . trim((string) $empresa->estado);
@endphp
<header><div class="marca"><img src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="empresa"><strong>{{ $empresa->razon_social }}</strong><span>{{ mb_convert_case(mb_strtolower($empresa->calle, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} núm. ext. {{ $empresa->num_ext }}</span><span>@if(trim($empresa->colonia))Col. {{ mb_convert_case(mb_strtolower($empresa->colonia, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} · @endif C. P. {{ $empresa->cp }} · {{ $empresa->ciudad }}, {{ $empresa->estado }}</span><span>Tel. {{ $empresa->telefono }} · Fax {{ $empresa->fax }}</span></div></header>
<table class="constancia-titulo"><tr><td><h1>Constancia de no embalsamiento</h1></td><td class="constancia-folio">{{ $datos['tipo_solicitud_texto'] }}<strong>{{ $datos['servicio_id'] }}</strong></td></tr></table>
<div class="constancia-fecha">{{ $empresa->ciudad }}, {{ $empresa->estado }}, a {{ \Carbon\Carbon::now('America/Mazatlan')->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.</div>
<table class="constancia-identidad"><tr><td><small><img class="constancia-mono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAtCAYAAAC53tuhAAAE1klEQVR4nJ2YeYxeYxTGfx1qmTFFE5GWjKi1iomiSsQSia1qKYaqvWqJhDaWiJ0/SFFCUJVp7FHLpKhtYsmIWmNvo00jZaIYMdVa0hE6UzmT523OnLz33m/mSb6Z77733ve573nPfc5zvmFNTU3UgF2Ao4HxwJ7ADsD2OtcF/AwsA74A2oHvqyYcVkE8GrgNuADYpJYnBNYD84FbgF+LLqormeBiYAVw0SBIDZsClwDfARcOhnhL4GVgHtDA0LGVVr5Qc5aGegTwJnBwZiLbw1bgB+1h2sed3ceitHvm3g+B44A/i4g/AA4JN/UANwFzalzptcCtmVW+BxyRC/WMDOnHwK6DIDXcpXs+DeOHA+fHFdsDrAJGuQvteD+gm6FhO+DrMGentmRDWvGx4YIevbeJdDNgCnAf8DmwFujTx75/BtwLnAgM1z2/AZM0V8JOmrc/9Q3TGYinlEwHAJcCpyvxctga2F+fWXqQF4C5wJfAAumA39L2FOoup0SGc6VSV9p2MDRYNO6WFsx347+YMCXivkCwouC1GAqWS2YTLPT1aY//ChdXkb4O7As063sZPKnhX/uTiH+kGCYYq8PY1cAS4BvgmnCuu2K+zlqIX9GqesN4Y0guj15F442COVd54o1S5vAccJLOdYRz17vvVoU8OpTZ9iq1Zeb92xPHUP4HnOOO7w/n7YEuA64DjgnnHtf/DVKqGK0l9idl9RZKsPReG44MK30WOJNymEwe5I5PVnXyGd3glesf4EkGYlbmuEw+bdJpYSwm3iOKxIAi8VC4yORvgjvu0or7b8xguoq/X60vOn3Ag+nAEye/5GHK4/EO8EmG9AFtRYI5ltnhmqeBlTliw+3h+LCwr6azE8M19irODGNXBRHqBW6sMnvtqYIIvwO7AXsB77rqY/hD4+YyvSP9VhUt4VH5sFLiscBX4cZXtV8jQzJZDiz182krDnRjVh73ANZUmT0rhzeEsRMCqSXYWYE0SaknRSVxTa329h75ryKYWr0Uxi6X7fF4DHgtN0GZoR8jlakP40Z4ShgbJfEfHpJuXKbyVRr6pgzp6oxI2L4+H0gN2wRzURPxaE1G2NfJwLowbvlwaGaORlW3+lqJTa9flEv0aAE+CmMT1FsVYaz8W03EczKdxGw9TAxlW5hjnToRjyky+aXEpwFXhLGOUH8TngF2zOj1qfJZHndKBbPE5o2eCDf8pBCbwHuYhz4+U4cXaNWTpGqeZ6ESdgCxdXaLQiKsV8E35fEYlymZnXqPE1Yqer6SjRT55p64Vf2Ox0x1DR4NobCX4e2MLRqfSqMJyMRMtlrIphbsq0llDq2qXhGLJLkezXXBW6HKElsaw3klpOiXA2t1IqYFg2A4uy5YVST8MZmsujyc2VdLvrhqa8wItXhZGNvWiBeHwRaVRSNLRrAto0AtMgn+IUdI8ZJpbFarOjncu9j2uFFJZMU+YoYSwqxsNHFWwZBy3RzO3yFdzzX0S82Jpuo0Rs4jZnYObwWHYni/QK8jlgNHmWOpc+9dc6YwRHQVJNhUdQ9lmKdfGPptUtTZM0p+m+qRKHQX9ENFGb9Wem0Nvvn3fuSKhLkGSywLaYKFyDxXmSuxJs3Cbd1lgiXl3jnRqfpJ0X6ztEyNr0MV9lFIY0+2Ef8DzwMQzZMMiV8AAAAASUVORK5CYII=">Nombre del fallecido</small><strong>{{ $datos['nombre_afectado'] }}</strong></td><td><small>Certificado de defunción</small><strong>{{ trim($datos['folio_certificado'] ?? '') !== '' ? $datos['folio_certificado'] : 'N/A' }}</strong></td></tr></table>
{{-- Se conserva el contenido original de ambos párrafos; cambios solo de presentación. --}}
<section class="constancia-seccion"><h2><img class="constancia-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACcAAAAsCAYAAADmZKH2AAAB8klEQVR4nO2YPS8EQRiAn9tcixKVhuD+gegkFHQoacSJj1ajkkvcP2AvudP5LAgqBX6CKMgR0fqIH6AhTjaZTcaY3dvb7Oxs4UkmuZnsJE/e+Xjn3tzaXB8BzAKTwBDQSTLcA2VgL8rHec1Yl5g8QvIMArtAO1CJI3cEDGMWF/gCamEfOUp/PgUxnyqwSAtyU5ooekuRi9maUQkTVOW8zS+zDjyQHAsBgsUoch1Kv06y1AIEqzpBVS4NalEFbchFFrQlF0lQd88lyZu41H0Kyj6uCQdXI/hhOnI3Sr8E9GlO65Jmbtm0XFXpTwOPQENpulTWY1ruFLiKO1mVqxu442aAyyTk/IxwL34ndShGxRPsBHiJOjEX8p6zQUPu2LznmvIvF5f/yKUduRngDrgFxjFEnMTv5UE5UW8CvcA3liOnihHxv4JxuWWNmMeaiajJchPSHvL2k05sK2D8EIN7Li8efd1ibAdok54xSyFiFVNivtyfnKYsn2tDzJfzygKrwD6/0UmlJibvuQNgheaspCWmnlZXRKXV00paV0klQDC1pWx2z/m1i0/RijbEwtLXtmhWccgwDhnGIcM4ZIeC0n/NityAqKPIXOc1tYwNoB/7uGrkShkROwPOs7KsSFz4RUVdreTJgtAzcCzqKWPAuzf4A85VYoKRXvpNAAAAAElFTkSuQmCC">Declaración</h2><p>
            Con base en los artículos: 62, 65, 71, 72, 100, 105, 106 y 107 del reglamento de la ley general de salud, en
            materia de
            control sanitario de la disposición de órganos, tejido y cadáveres de seres humanos, se hace constar que el
            cuerpo sin
            vida de quien llevara el nombre del <span class="constancia-enfasis">
                @if (isset($datos['titulo']['titulo']))
                    {{ $datos['titulo']['titulo'] }}
                @else
                    N/A
                @endif. {{ $datos['nombre_afectado'] }}
            </span> a
            quien corresponde el
            certificado de defunción
            No. <span class="constancia-enfasis">
                @if (trim($datos['folio_certificado']) == '')
                    N/A
                @else
                    {{ $datos['folio_certificado'] }}
                @endif
            </span>&nbsp;
            <span class="constancia-enfasis">no ha sido embalsamado en esta funeraria</span>, misma que se localiza en
            {{ $direccionConstancia }}.
        </p></section>
<section class="constancia-seccion constancia-deslinde"><h2><img class="constancia-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAsCAYAAAAXb/p7AAAC8klEQVR4nO2ZTYhNYRjHf3fMAomQSWzHzBg2LEQWamqIoXxMzALpmo8YBsliykczKQtJxqB8hsSUz4h8ZmEnyYYZFlY+spGUFa6eei7H432dc2fOOXeUX53ufZ9z7jn/+b/Pc96PybRnKwiwClgMzAQmMDC+AI+Bh8A+4HN/blKqn+OBc0AN8TEcmKNHI5AF7hR6kxL9vBizOMtE4CbQQD8Eyl83m+QZApwHVhcqcJmJiZtTgMwAjzFAq+OZp7W7IwucZWK7gOcMnI/AYaAO+GrOHY/qpAgcZWJxiAviyr2MOrmCiEWSNJdU5HcT3xOmIS2BQg+wBsjxu5MMFoHCWaCJX2x1uFpUgcKJQKVfJoRiCCyI/wL/ZQdHAp3AA+AT0KtFVOuazaTNUuAIUGYEVwIrdbhtltGoGAKbgKMh19SreUvS7uINHnF9OsENIhPnhpKUxR10xNuAKqAceGXOtaQlsNUjbl0g/k6HwiDlaeRgG3DAEW/UUSXIC9MekbSDmz3imh3ihC2m/aw04Zzbb2I57cYznureYWJPk3Jwkyfnmj3i1juqW5apXUk4WONwTlgLnPQUULfH0ddJCOwyE9Gwbu32ON2TxFg8XVeEVkTUbs3Hj+UbcQtcaNpXPdW6ETjkeS/KGP2TuLu4zrQveKpb0oAoaRCng2XAjED7my45g2zzVHeLJw1idXCeaT/SV8VkYJEuO6c5ftekC3mSFrjAtMcCL4FJf/mNa7gruIuX62w3p0evxiyypxhkaoi4bJi4qAI7daabp1JjlmFE4726fSrKxXEWyd6/nHurL+Ra3Su8FfWmUXJwp3GxT2MW2eYdquPwON3+vQFcl0GffpJpz1bkbIx0qQd26/ftumAaVAv3Du2dSv2OFShJG6Q6PW1Um+eNdgm0+dER8nqIiyqHY09cRSIzh/kmJ+QoBt0uB68A9yk+14DbNpgvEtluuEfxuKuT1D/IC5RCkZeo/CtMHJUXa9K80b1reeZc4IProh/3DosAZASO0AAAAABJRU5ErkJggg==">Manifestación del contratante</h2><p>
            A través de este documento, y en mi caracter de "Contratante" deslindo a Aeternus Funerales de toda
            responsabilidad y consecuencias por signos de descomposición que se puedan presentar.
        </p></section>
<section class="constancia-firma"><h2><img class="constancia-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAqCAYAAADBNhlmAAADJElEQVR4nNWYW4hOURTHf/P5PIiJXHNJJppJkXIZMm5Fcik1xSSDMk15YB6Qy1AjcsnlQWOSZHiYPCjhwbgUDzIuY1ymqBmRKPfMCOPyotFifXUs+3y+M3Pm+OZfu87aZ+29/2fvtdZe62SUFmUTInoCS4C5wFggE6gDaoG9wMegE8ZDJLcUOAj0Mv0zta3QdinIpLGQyOUDVQ5yXgwELuqHREowEzgSQF8+ZESUBNcAfT3yN92lDGCAvv9ixqyN0gYXGnkjcEKf3wEHgB9AuUdnRlQ72AcYbXbvqEPvlJFHRkVwqpFvKkmLrkZujorgJCPf8NFbZeSnURGcYORbDp2twAbTdyEqJxln5HojH9Pg7IXcJoei2MHherV57eql0ZEbxKIQeBMFwfH/2D1Bg+e5BVgEVAdZJB7i8d5x6JQAu9SzNwGvgi4SD9FB7jl0HuuutRntOeKJRr5LB6CtBEcB3TzyZ+AJaURwupGv0UEIi+BN0oigpFGzoyIYN7JkxOuAXGCMBmIJwE3aPgCtJkBHRlAKneOaZHoxSJsfrgJfO/qI5wPnHeRStcdWbbVan4RKUJLOypDmywVOAzVadoZCsNDs3Gtgmfb1ALJ04Tm6uK0vXMjTwJ0fhg1ONn2rlUgCQuiZBmapyLp73p0DGpW8BG8LmWcxcLI9OzjF9F3x0S0G+nnk75rrrde6RNL/h45xFXoSLuwEXmhxtd+PYBfTZ2W0rJTM2Aux2/ceuUaJljjGbnHMeRjYDAzWD5fwdlt94g+C9SkkmZV2ILAPNyr0P0yy0jQROVwZUp23sBeC941SuRbe/YGhancLjM424Dn+sCm91zQSGOIzNkvD1S/fyCgtyh4GPEhiJzgSg2kp6DUBvWkfZsXUQ5enOEBsriBF3YYAROp9/u9UJW6SM8A84G2SSc5qHZJqwbM7AEFxlpVaFsiNlEBzzNSq8ktiB3BZE4NG/beSp0E3md1ZVKvtSiLx6a+3v+e/rs6SqJP36Am16NrFYoOkM2KkOWKkOWJ0AoIFapCtadYahZsQ3A7kkH7IEW6d4ojLgEf/m4gDwqnsJ0kcuLStE4KvAAAAAElFTkSuQmCC">Contratante</h2><div class="constancia-firma-linea">@if(!empty($firmas['contratante']))<img class="firma" src="{{ $firmas['contratante'] }}" alt="">@endif</div><strong>{{ $datos['operacion']['cliente']['nombre'] }}</strong></section>
</div>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'servicios_funerarios', 'altoMarcaAgua' => 1050])
<style>
body .marca-agua-contenido{position:relative}
body .marca-agua-contenido>.documento-marca-titulo{position:absolute;top:30mm;right:0;width:58%;z-index:0;pointer-events:none}
.documento-marca-titulo svg{display:block;width:100%;height:180mm}
.marca-agua-contenido>header,.marca-agua-contenido>table,.marca-agua-contenido>section,.marca-agua-contenido>.constancia-fecha,.marca-agua-contenido>.cremacion-fecha,.marca-agua-contenido>.entrega-fecha{position:relative;z-index:1}
</style>
</body></html>