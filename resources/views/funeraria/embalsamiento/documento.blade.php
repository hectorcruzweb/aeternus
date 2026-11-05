<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Constancia de embalsamiento</title></head><body class="embalsamiento-secciones">
<!-- Iconos Lucide v0.468.0 rasterizados para compatibilidad Qt.
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
html body.embalsamiento-secciones{font-family:Arial,sans-serif!important;font-size:15px;margin:0;color:#30383b!important}
.embalsamiento-secciones header{display:table;width:100%;border-bottom:2px solid #ac8a27;padding-bottom:16px}
.embalsamiento-secciones header>div{display:table-cell;vertical-align:middle}
.embalsamiento-secciones .marca{width:33%}.embalsamiento-secciones .marca img{width:210px;max-width:100%}
.embalsamiento-secciones .empresa{text-align:right;font-size:12px;line-height:1.6}.embalsamiento-secciones .empresa span{display:block}
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
@media screen{html body.embalsamiento-secciones{height:auto!important;min-height:279.4mm!important;box-sizing:border-box}.embalsamiento-secciones .solicitud-visor-pie{top:auto!important;bottom:5mm!important}}
@supports(display:grid){
.embalsamiento-secciones .marca img{width:168px}.embalsamiento-secciones .empresa{font-size:9.6px}
.constancia-titulo h1{font-size:20.8px}.constancia-folio strong{font-size:20px}
.constancia-identidad td{padding:9.6px}.constancia-identidad strong{font-size:12px}
.constancia-identidad small{font-size:8.8px}.constancia-fecha{font-size:9.6px}
.constancia-seccion h2{font-size:12.8px}.constancia-seccion p,.constancia-seccion .size-18px{font-size:12px!important}
.constancia-firma .texto-base{font-size:10.4px!important}
}
</style>
<div class="marca-agua-contenido">
{{-- Misma silueta y transparencia del título de propiedad y de la guía.
     Capa decorativa sin espacio en el flujo: no mueve datos, firmas ni pie. --}}
<div class="documento-marca-titulo" aria-hidden="true">{!! \App\Exports\MarcaTituloReportes::svg() !!}</div>
<header><div class="marca"><img src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="empresa"><strong>{{ $empresa->razon_social }}</strong><span>{{ mb_convert_case(mb_strtolower($empresa->calle, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} núm. ext. {{ $empresa->num_ext }}</span><span>@if(trim($empresa->colonia))Col. {{ mb_convert_case(mb_strtolower($empresa->colonia, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} · @endif C. P. {{ $empresa->cp }} · {{ $empresa->ciudad }}, {{ $empresa->estado }}</span><span>Tel. {{ $empresa->telefono }} · Fax {{ $empresa->fax }}</span></div></header>
<table class="constancia-titulo"><tr><td><h1>Constancia de embalsamiento</h1></td><td class="constancia-folio">{{ $datos['tipo_solicitud_texto'] }}<strong>{{ $datos['servicio_id'] }}</strong></td></tr></table>
<div class="constancia-fecha">{{ $empresa->ciudad }}, {{ $empresa->estado }}, a {{ \Carbon\Carbon::now('America/Mazatlan')->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.</div>
<table class="constancia-identidad"><tr><td><small><img class="constancia-mono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAtCAYAAAC53tuhAAAE1klEQVR4nJ2YeYxeYxTGfx1qmTFFE5GWjKi1iomiSsQSia1qKYaqvWqJhDaWiJ0/SFFCUJVp7FHLpKhtYsmIWmNvo00jZaIYMdVa0hE6UzmT523OnLz33m/mSb6Z77733ve573nPfc5zvmFNTU3UgF2Ao4HxwJ7ADsD2OtcF/AwsA74A2oHvqyYcVkE8GrgNuADYpJYnBNYD84FbgF+LLqormeBiYAVw0SBIDZsClwDfARcOhnhL4GVgHtDA0LGVVr5Qc5aGegTwJnBwZiLbw1bgB+1h2sed3ceitHvm3g+B44A/i4g/AA4JN/UANwFzalzptcCtmVW+BxyRC/WMDOnHwK6DIDXcpXs+DeOHA+fHFdsDrAJGuQvteD+gm6FhO+DrMGentmRDWvGx4YIevbeJdDNgCnAf8DmwFujTx75/BtwLnAgM1z2/AZM0V8JOmrc/9Q3TGYinlEwHAJcCpyvxctga2F+fWXqQF4C5wJfAAumA39L2FOoup0SGc6VSV9p2MDRYNO6WFsx347+YMCXivkCwouC1GAqWS2YTLPT1aY//ChdXkb4O7As063sZPKnhX/uTiH+kGCYYq8PY1cAS4BvgmnCuu2K+zlqIX9GqesN4Y0guj15F442COVd54o1S5vAccJLOdYRz17vvVoU8OpTZ9iq1Zeb92xPHUP4HnOOO7w/n7YEuA64DjgnnHtf/DVKqGK0l9idl9RZKsPReG44MK30WOJNymEwe5I5PVnXyGd3glesf4EkGYlbmuEw+bdJpYSwm3iOKxIAi8VC4yORvgjvu0or7b8xguoq/X60vOn3Ag+nAEye/5GHK4/EO8EmG9AFtRYI5ltnhmqeBlTliw+3h+LCwr6azE8M19irODGNXBRHqBW6sMnvtqYIIvwO7AXsB77rqY/hD4+YyvSP9VhUt4VH5sFLiscBX4cZXtV8jQzJZDiz182krDnRjVh73ANZUmT0rhzeEsRMCqSXYWYE0SaknRSVxTa329h75ryKYWr0Uxi6X7fF4DHgtN0GZoR8jlakP40Z4ShgbJfEfHpJuXKbyVRr6pgzp6oxI2L4+H0gN2wRzURPxaE1G2NfJwLowbvlwaGaORlW3+lqJTa9flEv0aAE+CmMT1FsVYaz8W03EczKdxGw9TAxlW5hjnToRjyky+aXEpwFXhLGOUH8TngF2zOj1qfJZHndKBbPE5o2eCDf8pBCbwHuYhz4+U4cXaNWTpGqeZ6ESdgCxdXaLQiKsV8E35fEYlymZnXqPE1Yqer6SjRT55p64Vf2Ox0x1DR4NobCX4e2MLRqfSqMJyMRMtlrIphbsq0llDq2qXhGLJLkezXXBW6HKElsaw3klpOiXA2t1IqYFg2A4uy5YVST8MZmsujyc2VdLvrhqa8wItXhZGNvWiBeHwRaVRSNLRrAto0AtMgn+IUdI8ZJpbFarOjncu9j2uFFJZMU+YoYSwqxsNHFWwZBy3RzO3yFdzzX0S82Jpuo0Rs4jZnYObwWHYni/QK8jlgNHmWOpc+9dc6YwRHQVJNhUdQ9lmKdfGPptUtTZM0p+m+qRKHQX9ENFGb9Wem0Nvvn3fuSKhLkGSywLaYKFyDxXmSuxJs3Cbd1lgiXl3jnRqfpJ0X6ztEyNr0MV9lFIY0+2Ef8DzwMQzZMMiV8AAAAASUVORK5CYII=">Nombre del fallecido</small><strong>{{ $datos['nombre_afectado'] }}</strong></td><td><small>Certificado de defunción</small><strong>{{ $datos['folio_certificado'] }}</strong></td></tr></table>
<section class="constancia-seccion"><h2><img class="constancia-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACcAAAAsCAYAAADmZKH2AAAB8klEQVR4nO2YPS8EQRiAn9tcixKVhuD+gegkFHQoacSJj1ajkkvcP2AvudP5LAgqBX6CKMgR0fqIH6AhTjaZTcaY3dvb7Oxs4UkmuZnsJE/e+Xjn3tzaXB8BzAKTwBDQSTLcA2VgL8rHec1Yl5g8QvIMArtAO1CJI3cEDGMWF/gCamEfOUp/PgUxnyqwSAtyU5ooekuRi9maUQkTVOW8zS+zDjyQHAsBgsUoch1Kv06y1AIEqzpBVS4NalEFbchFFrQlF0lQd88lyZu41H0Kyj6uCQdXI/hhOnI3Sr8E9GlO65Jmbtm0XFXpTwOPQENpulTWY1ruFLiKO1mVqxu442aAyyTk/IxwL34ndShGxRPsBHiJOjEX8p6zQUPu2LznmvIvF5f/yKUduRngDrgFxjFEnMTv5UE5UW8CvcA3liOnihHxv4JxuWWNmMeaiajJchPSHvL2k05sK2D8EIN7Li8efd1ibAdok54xSyFiFVNivtyfnKYsn2tDzJfzygKrwD6/0UmlJibvuQNgheaspCWmnlZXRKXV00paV0klQDC1pWx2z/m1i0/RijbEwtLXtmhWccgwDhnGIcM4ZIeC0n/NityAqKPIXOc1tYwNoB/7uGrkShkROwPOs7KsSFz4RUVdreTJgtAzcCzqKWPAuzf4A85VYoKRXvpNAAAAAElFTkSuQmCC">Declaración</h2><p>
            Con base en los artículos: 62, 65, 71, 72, 100, 105, 106 y 107 del reglamento de la ley general de salud, en
            materia de
            control sanitario de la disposición de órganos, tejido y cadáveres de seres humanos, se hace constar que el
            cuerpo sin
            vida de quien llevara el nombre del <span class="bold uppercase ">
                @if (isset($datos['titulo']['titulo']))
                    {{ $datos['titulo']['titulo'] }}
                @else
                    N/A
                @endif. {{ $datos['nombre_afectado'] }}
            </span> a
            quien corresponde el
            certificado de defunción
            No.<span class="bold uppercase ">
                {{ $datos['folio_certificado'] }}
            </span>
            ha sido embalsamado en la sala de preparación de esta funeraria, misma que se localiza en
            {{ strtolower($empresa->calle) }} Núm. Ext {{ $empresa->num_ext }}
            Col. {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
            {{ $empresa->ciudad }}
            {{ $empresa->estado }}.
        </p></section>
<section class="constancia-seccion"><h2><img class="constancia-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAsCAYAAAANUxr1AAABzUlEQVR4nO2Yu0oDQRSGvyz2FmkkfTCJpZXaKQreEKKkUpEgPoIQhIiF4gPYWdspKkRE1FKsxErdPEC8FRF9gcjILIxD1p2N62Yj+8Gwczkz598zk8NmEqViGgNmgFWgD+jGH+/AHbAFnHgZWwYLTgFHwGALYpBzxNwKMBGEoBLBsRaEoN5gtJitpQtaAA6AGtCQJanZJHwWlaSybk36mlcNuuSzB9gDhgmPFJCXZVEG48WJ0H7IYnRGZUC+tmwZGKL9jABLQtCsNiCilVXOQF0bb/gsKnVl3az0pZIXgga0znXAVtoPBIe6li19qfRbTZLdvdbeDlDQpoevlEkeqgDTwDXw0YKIN+AKmAROvYydn72JqAohYBKhUIkFeRFHKIgIFWQSs2XdbdwrY7vN/0aiVEzr6V3/ZLCV75gqkPlh3Itm8xsdf4bK8s2qsu427oXbfN9b9td0/paFSizoX0aoEGfqKGEZ2JTjTB0lLCKGRcSwiBgWERT0rPXlQvSf09pPQtCt1rkBGN0V/5KM9KVyI/7b7wLjSuecLO1gR0ToELik/RwDZ86hFjehF20Ucw6siIojSBxscfEobkJFxB5DEOFcCwufY8Cr6PwEUpOA4tIPCHkAAAAASUVORK5CYII=">Técnica utilizada</h2><div class="lista pl-11 -mt-1">
            <p class="justificar line-base size-18px">
                <span class=" bold size-20px -ml-6"></span>
                <span class="ml-1">
                    • Inyección de líquidos por la vía intra-arterial a base de formol diluido en agua, glicerina,
                    colorantes, etc.
                </span>
            </p>
            <p class="justificar line-base size-18px">
                <span class=" bold size-20px -ml-6"></span>
                <span class="ml-1">
                    • Se utilizó como base de drenado la vena yugular.
                </span>
            </p>
            <p class="justificar line-base size-18px">
                <span class=" bold size-20px -ml-6"></span>
                <span class="ml-1">
                    • Se realizó la extracción de líquidos y gases de las cavidades del tórax y abdomen por medio de un
                    troquer conectado a
                    un aparato de succión para posteriormente depositar las sustancias preservativas en las mismas
                    cavidades.
                </span>
            </p>
        </div></section>
<section class="constancia-seccion constancia-resultado"><h2><img class="constancia-icono" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAYAAAAehFoBAAAEaklEQVR4nMWZXYxdUxTHf3OYGVr1lSBSghji68EQXmi1qb4oQYgE5WEQ2r74aColikioiK+EO4QiNKoJJYSOphWm9UJ8JLQ80EpLiUxQTcdHJ8jif5JtZZ9z9j1z5vaf7OTudffZ+3/PXvt/11q7a/HAcTSEq4CrgaOBw2X7HtgCPAusaGKRrIE5LgO2Ac8D5wJ9wGS1PmA28CKwFbhwTxN+FVgJHJEw9kjgNZGvjb3bHG9vrB84FjgPmFZjzcvlMm8DXwOfAF81SfhA4FHgImD/irHmp+8Aa4EuucMsuU2IGWo5ftVu3Qj8UrZAV8Whm64tnFr5s/7zz9cLvrsYWJUwx3fAFcBwHR++FXgvgewuYGYJWfT27ECOUo6pWnNxuy5hfrY0Yv8ZeB/4AjhR234/sJ5qrAPmAAuBv4M5zpbbhbgX+CYmhTHC/dLNEPZm7gAe1mJ18a6a32Xz3XuASYHdOHwOfOYHezwI9DrbGcBD4yRbhL80t60Rold2yggf6k6v4U5gExOPTcBdzmYKc1gZ4enyyxxb5E+dwlJgc9A3LmeVEbY4IMRbwBidwx/AGmc7qozwXq7fQ+fR4/pZGeGDXH8fOo9e1z+4SNaWAQNu8O90Hr+5/m3S6QXhG14YIWv+NNggkbmBrlrgVARbc7ezzVf7N5Yw9j8C3cEAO2gXAEMNkZ0HtJz69EmDY7BA62V3pmy3D8mUJXiyl0wgWZx0xmBx85XuB9l5ujRT4BJiVUUg0w7mR8ii4Kbo7eZYGZG4mZmC8RAWWKdgTuCTcwvIPl5gfylxDc+lz1TieGd8I2Eie+6pINl8AZgSHNJ5JWQHSceaGGGvxVVblcMHQuHWtxogG5O47kzZbAjv0zGMSQo9Wg2SzWObEFuN8LfOmJpYrsjFvAILxqHnnvC2LOLYA5EQswitXNDbVIkUzJa0hRgywk+6XGs/abA9kILBAtJ13SBXoDddXLETeMYI/wTc4h7olVr0t0H6Ov2l7tbnumTPBF5xf2aGG+wQ5grxhIIfT9rkKRVPKzTs0ee6uD4SsT2SV4xCSbtWGXAIq491Gvu6viWnN+WdrEL39kQA3+36/4vcskhIWfZwJ9Dt+n+WEd4Y0cGqelqTmBLR3o1lhNc5ibOUaQmdw90uTRtVcbGQsA14ztlM8k5m4nFSeLiC6s9oVeXHTuWIs32gclITFXsiwfzNwIfOPhIprERraz/ovmJ1YJukutoSFQM/VQ3Dtu8BVRxTMA1YpBqwFftOVaHEZ+uIw0hq9XJI2aqv+tjE56vlmKGkcjiB7OpEbb+9KEUr2+L7gHOA7RWTT9bkVgcrwiyNqSK7XeFtYXmsyieHVcNdnvDvZNcEG7Tlx6gtkm2tK6XGsFwHz5dj27oyCHEAcApwgm6N7FboGuphma7KLBb/UrnhjqZvkXbowFnL8bEubFLnGZN0PUZNjFemWvLPsERahM3yz9pkDe3e08Vgfm6lgtNVLbJ49jR9ZztgGm6x9UcNrMU/gLnkdb4ae3IAAAAASUVORK5CYII=">Resultado</h2><p>
            Como consecuencia de dicha técnica, el cuerpo sin vida del <span class="bold uppercase ">
                @if (isset($datos['titulo']['titulo']))
                    {{ $datos['titulo']['titulo'] }}
                @else
                    N/A
                @endif. {{ $datos['nombre_afectado'] }}
            </span> ha quedado en condiciones satisfactoriamente para
            su velación, inhumación y/o traslado.

        </p></section>
{{-- Mantener selección del técnico/cédula y firma del documento original. --}}
<div class="constancia-firma"><div class="w-100 center">
            
            <div class="w-50 ml-auto mr-auto">
                <img src="{{ $firmas['embalsamador'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class="pb-1"><span class="texto-base bold">
                            @if ($datos['embalsamador_id'] > 2)
                                    {{ $datos['embalsamador']["nombre"] }}
                                    <div>
                                        {{ $datos['embalsamador']["cedula"] }}
                                    </div>
                            @else
                                {{ $datos['preparador'] }}
                                <div>
                                    {{ $datos['cedula_embalsamador'] }}
                                </div>
                            @endif
                        </span>
                    </div>
                    <span class="texto-base">Técnico Embalsamador</span>
                </div>
            </div>
        </div>
    </div>
</div>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'servicios_funerarios', 'altoMarcaAgua' => 1050])
<style>
/* Iconos del diseño aprobado, incrustados para visor, impresión y Qt PDF. */
.constancia-icono{width:17px;height:17px;object-fit:contain;vertical-align:middle;margin-right:8px}
.constancia-mono{width:12px;height:15px;object-fit:contain;vertical-align:middle;margin-right:5px}
.constancia-seccion .bold{display:inline!important}
@supports(display:grid){.constancia-icono{width:14px;height:14px}.constancia-mono{width:10px;height:12px}}
</style><style>
body .marca-agua-contenido{position:relative}
body .marca-agua-contenido>.documento-marca-titulo{position:absolute;top:30mm;right:0;width:58%;z-index:0;pointer-events:none}
.documento-marca-titulo svg{display:block;width:100%;height:180mm}
.marca-agua-contenido>header,.marca-agua-contenido>table,.marca-agua-contenido>section,.marca-agua-contenido>.constancia-fecha,.marca-agua-contenido>.cremacion-fecha,.marca-agua-contenido>.entrega-fecha{position:relative;z-index:1}
</style>
</body></html>
