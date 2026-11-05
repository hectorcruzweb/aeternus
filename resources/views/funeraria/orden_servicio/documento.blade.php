<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <style>
        #header,
        #header section table {
            width: 100% !important;
            padding-top: 0px;
        }

        #header section table {
            border-collapse: collapse !important;
        }



        .logo {
            max-width: 100% !important;
        }


        h1 {
            font-size: 1em;
            line-height: .8em !important;
            text-transform: uppercase;
            text-align: center;
        }

        .datos-header {
            text-align: center !important;
            font-size: .9em;
            line-height: 1em !important;
            text-transform: uppercase !important;
        }

        .numeros-contrato {
            width: 100% !important;
        }

        .numeros-contrato .control {
            text-align: center;
            text-transform: uppercase !important;
            font-size: .8em;
            line-height: 1.9em !important;
            font-weight: 600 !important;
        }

        .control-valor {
            text-align: center;
            font-size: .9em;
            line-height: .3em !important;
            text-transform: uppercase;
        }

        /*parrafos**/
        .contenido {
            padding: 10px 0 0 0 !important;
            margin: 0 !important;
        }

        .tabla_dato {
            border-collapse: collapse;
        }

        .tabla_dato .border-bottom {
            border-bottom: 1px solid #ddd;
        }

        .datos_tabla {
            border-collapse: collapse;
        }

        .datos_tabla tr th,
        .datos_tabla td {
            border: 1px solid #ddd;
        }

        /*fin de parrafos*/
    </style>
</head>

<body class="orden-institucional">
    @include('layouts.estilos')
<style>
/* Orden institucional: estilos locales, todas las secciones condicionales del
   formato anterior se conservan. Cada bloque permanece completo al paginar. */
body.orden-institucional{font-family:Arial,sans-serif!important;font-size:15px;margin:0;color:#30383b!important}
.orden-institucional header{width:100%;display:table;padding-bottom:18px;border-bottom:2px solid #ac8a27}
.orden-institucional header>div{display:table-cell;vertical-align:middle}
.orden-institucional .marca{width:34%}.orden-institucional .marca img{width:210px;max-width:100%}
.orden-institucional .empresa{text-align:right;font-size:12px;line-height:1.6}.orden-institucional .empresa span{display:block}
.orden-institucional .fecha{font-size:12px!important;color:#727272;margin:16px 0}
.orden-institucional .fecha span{background:transparent!important;font-size:12px!important;text-transform:none!important}
.orden-titulo{width:100%;border-collapse:collapse;margin:18px 0}.orden-titulo small{font-size:11px;letter-spacing:1px;color:#8c722a}
.orden-titulo h1{font-size:29px!important;text-align:left;line-height:1.3!important;text-transform:none;margin:5px 0}
.orden-folio{text-align:right;vertical-align:middle}.orden-folio span{display:block;font-size:12px}.orden-folio strong{font-size:27px;color:#8c722a}
.orden-persona{border:1px solid #c9c9c9;padding:19px;text-align:center;font-size:21px;font-weight:bold;margin:20px 0}
.orden-institucional .contenido{padding:0!important;margin-top:24px!important;page-break-inside:avoid}
.orden-institucional .contenido>div:first-child{background:#444b4f!important;color:white!important;text-align:left!important;padding:10px 14px!important;font-size:17px!important;font-weight:bold}
.orden-institucional .datos_tabla{width:100%;table-layout:fixed}
.orden-institucional .datos_tabla td{padding:9px 12px!important;line-height:1.5;font-size:15px!important;overflow-wrap:break-word}
.orden-institucional .datos_tabla thead td,.orden-institucional .datos_tabla .bg-gray{background:#f5f5f3!important;color:#6a6f72;font-weight:normal!important;font-size:13px!important}
.orden-institucional .contenido:last-child{border:1px solid #ddd;background:#f6f6f5}
.orden-institucional .contenido:last-child>div:first-child{background:#f6f6f5!important;color:#786124!important}
.orden-institucional .contenido:last-child p{padding:0 14px 14px;line-height:1.8;min-height:75px}
/* Escala de navegador equivalente al PDF Qt, sin afectar el zoom del visor. */
@supports(display:grid){
.orden-institucional header{padding-bottom:14px}
.orden-institucional .marca img{width:168px}
.orden-institucional .empresa,.orden-institucional .fecha span{font-size:9.6px!important}
.orden-titulo h1{font-size:23.2px!important}.orden-folio strong{font-size:21.6px}
.orden-persona{font-size:16.8px;padding:15px;margin:16px 0}
.orden-institucional .contenido{margin-top:19px!important}
.orden-institucional .contenido>div:first-child{font-size:13.6px!important;padding:8px 11px!important}
.orden-institucional .datos_tabla td{font-size:12px!important;padding:7px 9px!important}
.orden-institucional .datos_tabla thead td,.orden-institucional .datos_tabla .bg-gray{font-size:10.4px!important}
.orden-institucional .contenido:last-child p{font-size:12px}
}

/* Etiquetas y valores en filas: reproduce la orden institucional propuesta.
   Las condiciones Blade de cada servicio permanecen en las celdas originales. */
.orden-datos{width:100%;border-collapse:collapse;table-layout:fixed;border:1px solid #ddd}
.orden-datos th,.orden-datos td{padding:17px 16px;border-bottom:1px solid #ddd;vertical-align:middle;text-align:left;line-height:1.6;overflow-wrap:break-word}
.orden-datos .orden-etiqueta{width:29%;font-size:13px;font-weight:normal;color:#737373;background:white}
.orden-datos .orden-valor{font-size:16px;font-weight:bold;background:white}
.orden-persona{display:table;width:100%;box-sizing:border-box}
.orden-persona>div{display:inline-block;text-align:center;vertical-align:middle}
.orden-persona small{display:block;font-size:12px;font-weight:normal;color:#777;margin-bottom:5px}
.orden-mono{width:25px;height:30px;vertical-align:middle;margin-right:13px}
@supports(display:grid){
.orden-datos th,.orden-datos td{padding:13.6px 12.8px}
.orden-datos .orden-etiqueta{font-size:10.4px}
.orden-datos .orden-valor{font-size:12.8px}
.orden-persona small{font-size:9.6px}
.orden-mono{width:20px;height:24px}
}

/* Iconos del catálogo visual existente, incrustados como PNG para evitar
   diferencias de compatibilidad SVG entre Qt, impresión y navegador. */
.orden-icono-servicio{display:inline-block;width:21px;height:21px;object-fit:contain;vertical-align:middle;margin-right:10px}
@supports(display:grid){.orden-icono-servicio{width:17px;height:17px;margin-right:8px}}
</style>
    <div class="orden-hoja-unica" data-ajuste-una-pagina><div class="marca-agua-contenido" data-contenido-ajustable><header><div class="marca"><img src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="empresa"><strong>{{ $empresa->razon_social }}</strong><span>{{ mb_convert_case(mb_strtolower($empresa->calle, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} núm. ext. {{ $empresa->num_ext }}</span><span>@if(trim($empresa->colonia))Col. {{ mb_convert_case(mb_strtolower($empresa->colonia, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} · @endif C. P. {{ $empresa->cp }} · {{ $empresa->ciudad }}, {{ $empresa->estado }}</span><span>Tel. {{ $empresa->telefono }} · Fax {{ $empresa->fax }}</span></div></header>
    <p class="fecha  right">
        <span class="bg-gray bold texto-sm pl-2 pr-1">{{ $empresa->ciudad }}, {{ $empresa->estado }}, a
            {{ \Carbon\Carbon::now('America/Mazatlan')->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.</span>
    </p>
    <table class="orden-titulo"><tr><td><small>COORDINACIÓN DEL SERVICIO</small><h1>Orden de servicio</h1></td><td class="orden-folio"><span>{{ $datos['tipo_solicitud_texto'] }}</span><strong>{{ $datos['servicio_id'] }}</strong></td></tr></table>

    <div class="orden-persona"><img class="orden-mono" src="data:image/png;base64,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" alt=""><div><small>Nombre del fallecido</small>
        {{ $datos['nombre_afectado'] }}
    </div></div>



    @php
        // Catálogo de presentación: siempre muestra los ocho servicios.
        // Un servicio no contratado no expone valores residuales de su captura.
        // Los campos vacíos de servicios incluidos también dicen No disponible.
        $valorOrden = function ($valor) {
            return is_scalar($valor) && trim((string) $valor) !== '' ? $valor : 'No disponible';
        };
        $fechaOrden = function ($campo) use ($datos, $valorOrden) {
            return !empty($datos[$campo]) ? $valorOrden(fechahora($datos[$campo])) : 'No disponible';
        };
        $salasOrden = [1 => 'Domicilio particular', 2 => 'Funeraria Aeternus | Sala La Piedad', 3 => 'Funeraria Aeternus | Sala La Misericordia', 4 => 'Funeraria Aeternus | Sala La Resurrección'];
        $cementerioOrden = [1 => 'Cementerio Aeternus', 2 => 'Otro cementerio', 3 => 'Fosa común'];
        $iconosOrden = ['data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAoCAYAAADkDTpVAAACGElEQVR4nK2XTWtUMRSGn7n9oMVPEKEqolJ/gHsX3YngRlz4C/wPLtwo/ggRF126EhTcuBGlCxWFDrQbRZQqiorFT6ZVaZUDSX2NuSHJve9iciaZOc/kzc1JZnD6xCyBzgFXgSYcyNAl4KJ2xJJcrkxuOk+g8eD9fmDGxT+A9WD8N7AjTOJ+0ACYAo4AL9sAFyT+BewiT5sOYLoCnFSy6ozE2zOTh3nm2gb2ij1fKdeGa82mfTGArv5YBWAg8XwMoPZsqwA0MZuaiD1fqNdGaFMTsSd8smo1r4Cu9sTWbs4D+rInapMBjm8NwXf6A5jOGuAef/Wc7lqT+KYBPgNvXcdkD4AxmcmKX+RF127twA6adu1He/GAoWsPA6OeZvBMAX4GaKmtkJV4rwdtgE89AW4r4IVYo0WrVPrdhbBAPXHtHuo1FW5YBXibZt0JVaMJcaQVYB96VZF8XeLHKYDpfUfAnRhg2R30SFsiu3F43YoBzPclF++kfoFHWvDCW4W36VCHEvFaO9sAdh96U5BcLR2mAMPKkrEm8d0UYLFl25ccMjdSgJGvgoVn86RrfwKrKYDO4mABwC/wu3AgBTgAfMhIvil5lnMAutArhTt4IQfwSOJvhY/o9RyAXgImMgCNHvI5AF2HmdJDPtR4AnAKOArcD8aOSWwFbreLn5YA/ELbEfjPP5aEHsY62yyyX/3fM52Q+X8tNvAHUxBtEpL7Z5gAAAAASUVORK5CYII=','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAoCAYAAACWwljjAAAByElEQVR4nO3XT6hNQRzA8c+7/oXNY+FhoUSykZWFRG+ht5CFlI0UO3+KsBIpoVi9koXYoGQh/+olCaVIslSslLKRBWWDF6HR3Drdru6c+845s3C/i9v9zZlz5tvvzJmZ39CWsWUqpoXXuI5T/dxcNTuwEscwP7fQDJyN/2fhaG6h3VhUiPdjJKfQ8Y54Jg7lElqCBV3a1+YS+ogfHW0/8TaX0CSOdLR9L0zyJKarlnG8w+oYX41x1nXoDqbhaVmZuoQCe7FVH1T1ykaxPmYmMAdrcCLGX3AL73s9aKiCvWw8ca2ZjOIv6nxlc0ssfGFbOdyr01SFfqmYqQp9w5kSY52uWyjwXBphDr3K9dl347cE/muhJAZCvRhkqBeDDJXJ0AE8wWVsk1noAc5hcTwi3MC9XELbMYY9WIGlOIhNsdBrXGgzPuBioT1k6zFOYl7TQqGWGu5yLcyp4ZQjQ9VCoUqYjUtYWLj2BuexD6uaEmqfqa9gp/xcaFcdu/AIy7t0WoeNDQl9KpZB1/7RaUODQhMpW8czfG1A5jNethIri7upJ74+CWPcLrO5ToQPoEahVhwjWeh+jTJtHoafVKFQm/+9oSZuxhrPH/LpSe8fjX75AAAAAElFTkSuQmCC','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAoCAYAAACSN4jeAAACHUlEQVR4nO2Xv2sUQRTHPzued55iE+EwhaAcBARrJSqSNLGxuIAggoiF2liKnf4lNqIBYxVCGrVICFaJvaRSyP9wqHebnAx855hbDm9/3g3oF5b9MfPe+/B23tvZqLPSpiS9BgbA0zKcmRJ8nAWWgPPAvK7ts0KqlQB2Gdjx7u8AV4GvswbbB6IQX2UlKiNjTm1ljJAytgYsAjeAd6Fk7D3QAU7p/q783p9VxmrApgd1rMNer2psJmAbwIoHFek4AhrAbWBrmmAngU8JKOOBndCzhuZ8nAZYA/is7l73oMb5PdKcZWC7arC3qr6/QTm5zNm514EPVYItKGuToHz/bs0tVAlmctjZzGVWXrDKe2bWQHXyy1ZzZWD9jPNz22YBuwnMkV/n5KMUsAvAK+AH8AVoFQBrycd34KV8ZwJrAg+AXeAQeA5c1FhcAMzZXgJeyPe2PvY25ogi/YzYT8kt4BFwT+vhtFdJbp8VFQAb56cH/FKcdeCNsjoEs/v2b8DPBH1PRn01yUEBOGf7WxUaJ6q8C5wRy0HyVTYF56CsoRFUWWrIZ10xUEwLNZQDc1mIvYwV6Vlp5WI0vTUY+WCDCv4BsmpkPSfBgpEpqdr+PbDgZHT+v8ZSaKRdBCdDoDIEKkOgqgEPgWsp50cFYqW1fQbsWbDHAutOyKAbO/YCTQo28FpR0n6c7NgT4ErN24JMYzdBShkLVeTPpyrFfwA5f1rPsoEe4AAAAABJRU5ErkJggg==','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAABjklEQVR4nN2Yu07DMBRAT26j8BAzGxOMsLB1YkAw9wdg4CcYYGBkg5WVDfENDPANLPwGSAhKHsiSKxkrcdsoTq44kqXr1HGPbOfGTjI53qYlG8AHcckF3aTaBUk77KuyJcSPmbY5bdYAiSGY2BJixZYQUyCzcaF+igXlCMoRlCMoR1COoBxBOYJyBOUIyhGUIyhH+IeCq8AO/TBqI3gHPDp7tqgsu2E9B07tzvmWHlhmBPeBaxubnfMJigTNuntY4MwxmOCFfTD8M0cOlAwsaMQuA2s4YWDB+5qpLZw4YUDBQ2DsSZgpHXntKuIw99h549W/HFlXKokkGUzUR8CeU69scnYFp0QmJHjm1UuvvYmz2GtRAnlv4l1rmsKciDQJjq3kjO/AazF14rIvwS0vlYQ++FQxR7NJcNN+KluEwomzvgTfvD8rA6OTeu16EXyueWJnIia1fC7ZX2uaOnwHrhp+MyO7XnO9JAJmVA4actgL8Ars1uRA/yFJOhy9P/0Ywaead2vwJo+uE7S7pvNfNP46JNvAEYEAAAAASUVORK5CYII=','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAACwElEQVR4nM3ZX4hVVRTH8c9cLxWYFk0qkdaD2UsYMhCKjin0h0AIUbGQSEUiXywKfEp6CR8EEfRBpIdAjfyDOmAwgyaJiBT1IJYPkhOjZEmiPfiQSpaxYx04XM4ZZ+bec6YfXNjr7D/3y7r7rL3Xul1LX52pZs3CTszFTXyJD3GraHCjZriXcBYvYxKexHp8i8fHG/A19GNi2D/gt2g/j2/wxHgBTsd+PBB2H3rQm4N8BqcxYzwA1+KRaB/CSvyNIczDpehLL8T3sU9rBZyVa2/E3Zz9Cxbg57Cn4QyeqxNwMNdeVdCffuZFOcgpOJX2ZF2Ae3Ne24wPCsb8ivm4EHY3ttcFOISPcvY2bCoYdw0LIz4mza0zzAy27L1PsKNg3PUATXq0UWMM/ALNlucb8GnO7sKBCDlJPzZqguvDgyX970SMfBYDEYJEGNrYrAnuoYK+v8KjyWtvxCdT2gpvp9OlSg/2DgOXLgbL8W6A5vUn3sS+ZDQrhBsogbuDpTge9jm8F0dcepG24KdscLNCuIdL4Fbl4JK+w1tlizXGAe7IaBZs/J/hOgnYWwVcpwB7qoLrBGAPviqBu9su3Fjf4qfji48F3GMlcOvahbsf4ISIT4vxBz7DFZwMyI9L4lwGt6dduOEAJ0esSqlhpjXhsQSnDriyPTg5brN5uEyvxMVSHXBFgBncHOVKuezVlmf/VAHXCjgSuEwpf73Rss7sTsNlC4u72okRwsnlDDdySc/uKgG34oUxzO8Or6eSxnkVAT4V9ZGxagHuqUgJcHWb165mxMvKAF/swDpTVAj4X4mhTX2tQsDCutwodBS7VAh4u435x7BMhWoMc3SNBG5J5K+VAl4cw7z+OuAywM+NTofxeh1wGWBfVJ/up9/xPlbUBSeCbMrsU7Em1UTSjSSVYadGSeJyFLvTXwUHC6oAletf6r+SFkend7gAAAAASUVORK5CYII=','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAkCAYAAAD7PHgWAAADr0lEQVR4nM2YS0hUYRTHf+MjH2mZlE1WphVpIYlFUWmLWki7iqCIFlG0qKBFL6hli6AHQcseizZBQRD02kREWQlhGZn2sswITcnKUskyLQ78Ry7D3Lkz4+h44DJz7/e4//s/5/y/832+dVVziKMVAH+A9nhNmBKviYBzQAXwG7gLHBhLALcAm4AJuu8FqoDbw53YFycXfwYmAs+AASAf+A4sGe7ESXEAtxKYBHQAg0C6/puVjAWAu4E0oBCoFGsrgB5g/VgAWAn8DfF8CrB8LCRJnss88wQyoQxOB/65tKUC3fqAUQFoAV8W9GwnkBxmzC9ge9CzMrEbVxc/AkrV/wzwHDgG+E2qwozrA44qkfYrXncoZuuAVbECTFc2vpSLciXCjcA+aZ1djQLuZt3qY+xfBMYBDRpjWjkb+AEsAB5E6uLVQCtwU5MXiqVWvahXbrWXGUPhzOLzm+Ix0L8Y+Ki2YpFwA/gqefIEuA3oEmPG8CVgrsTYgI139PV5AByQeDs9Y2CnAUViNUmrUJfc7+nibFUjfWIvRcAyY0gyn8tzYxMB+wk8VRWU6wXQgn4+MEOTeCWRL4J2NxlCH26eKVe8GjlTHUvlEAPWcB5okTszPcC9Ae7LVeFsUF6wvk1h+iUppIqF4bgwDYG4DizVvZvb2gUsX7FUK4bywjBp7D0BPgEbgddip0RMOc2n+LT375JerrGbzaI4M4TodurrG5Qc7yTOxspBZWM4s/lfqHi1anuPxti76oFqZbnTksXeImCDMXZEjASsS6mfIZ2yCU8Ct0IA6IsgDp1SdEfXVmCtitxysfxX+pilvjnA4RSle5N0KFdZdUWrR20UL3dzsVufa7qQ/lWqbEtVsWuJWmoATwH9QDNQoziL1DojAGjrsZfV6Doh1y4GrNTvN4CHiN2aPdrTI/iIYKvTFZdyq8WjPWO4W9DhAuxQeLhZllN0EwGwLWitDbacRDPYK3kIxWK/wHtl+ohvmpocYmsK8Nbx38SYRAOsFlv2O1NL2EPpal2iARYBCwUwX+5s07KYoW3nrEQAtOy8oEMivyqRPAHzi8UCrQb3gNNBy+mIAqyQQNupQaEqk3otU2kCa2y+0u6tUDu79yF2hSMC8LLKMquGA+Z3FKbJWt4MZMCyxe7V0ThZyJW7rMb7IKDmykDm9qnonazTrh5VRf5YCImFwS/KUDPL0r0CY/tlE2Ur187qmdWB1sdKMqtQ7JguKovlfND2LBZnj6Mct0wgo6mW+A8VlMdszAkMogAAAABJRU5ErkJggg==','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAADQklEQVR4nM2YSWxOURTHf9+nplLTBml0oSQSFAk1xRi6smgskHRHYiNITJFYirDAwooFEZo2urEkuqEbEjZqTrUVX6vVgeqklFaOnK95ed54323rn3zDu/e+//2/e+4795yTKi0pxCJOAUPARVuEObaIgNPAeQfvBRukaQsck4Byhzj0f7mNBUgqcD7wCCjz6CvTvrnjIXACcAx4B6wLGLdBxxw2nSvuTWKyXcAL4BKQF+GemcAV4DmwUx8u1oSLgV6gxYdc+tcCJcAOIBczLAMeAH1ANXAfeArUAT2usSndPtNSpSWFlcA+oB94CXyXDkD8z2zGBl+AetUwFViuv5U5unroyhQzPpijHzd606r+f0WnrGC7BaIe4JluDdRUaxzXpmgTgZ0JST4AU4BtrvaPQBeQn4C7Q0zcllBgFzDPo73AwsO3pfXtMUUdsDKgvwjIJOB/n9ZJfhkStEcY88mQewBoEIG/gTeGJLkRxkw25BafPJx2XJigEOgO6O/Wk8gEtfKVFVhjSJIHvAUGPfoGdX+bupq/mrLxmpyNpigGXgE/gIV6jjaq+Vcl4K12CqxXvyWuwQRLXddBb3YUvM6+XM6IV6KLg4aEvfqQ3/R6lu5PU/Pe84oHKwyIujVqFnOuADbrp0ijkYcaXsXFHS+BsimbYpA06CmyxSfwlbatGow0x+Bt1DjxH4HDwK2IJINq1oIIYxcAX2MIlGRrBO4nv6FCw1CjZowTTT+JME7mvhkkUDZ6VQQir+AyDDNCR0Clbp0ReO2dsyEkGUM3skR9ZRDOuRu8BIrTvR1A0q/OOC7SPolZFtfV/4UKRHNed6ZlAz992sUbnPDq8CtNdAAngaseffma43phKCTXXuTTfkRFRhYouAZsB/a42qerU7aFiqAtFVZZ2K+li6gYIh4kzDsQNCBMYJ8mQ5lRKKU0qoUGkhK26PnabHEFM8rZbuuJJbVcrS4oKV+t5sxNtk3SCqwH7iZYwSpgI/B5tMpv4ht3A8d9xKR87pPE7Ciw11ELGhWBWVzWl6fV1e4VaIgpN2mNcExLwBLRSA5yxmEy5wqKMDkdJKt7bDqJ1AcTaBzBROCQ1qVlFcXJS9gkpk2EP+GerbsuFfbaAAAAAElFTkSuQmCC','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAoCAYAAACSN4jeAAABLklEQVR4nM3XsStFURzA8c87vRcSRVHK5g8wKbNBsZBS+BOYmCwG/4HdIMPLYGMy2k0Go0F5NiZKeHTr0QlPecr9fYfbvffU6VP3nDq3Mj89Bv3YwRwesIdtvCipKio4wWTr3SC20If1smAJExkqbw09SoSNthmr/TD2L7AzNL8Zu8GlEtfYFTZaiz9vFcMdzHmLx7/CKq1dOYKGOI0XnzJaFziPCNsvLtFgr6hHhJ3iOiKs/n4TCfaEg4iwI9xHhNXzhyiwOxxHhB3iOSLsS1Fgi58tUWADmI0IK1oWFLaAroiwbsxHhBWtRIXNoDcirIaliLCP3RkRNoWh4vdNftzIfsE2O5y48c18v61Zzc7aecXEu0osCVoStCRoSdCSoCVBS4KWBC0JWiob0K43kocql+GoTR8AAAAASUVORK5CYII='];
        $serviciosOrden = [
            ['Embalsamado', 'embalsamar_b', ['Médico responsable' => $datos['medico_responsable_embalsamado'] ?? null, 'Preparador' => $datos['preparador'] ?? null]],
            ['Velación del cuerpo', 'velacion_b', ['Lugar' => $salasOrden[$datos['lugares_servicios_id'] ?? 0] ?? null, 'Dirección' => $datos['direccion_velacion'] ?? null]],
            ['Ceremonia religiosa', 'misa_b', ['Iglesia' => $datos['iglesia_misa'] ?? null, 'Fecha y hora' => $fechaOrden('fechahora_misa'), 'Dirección del templo' => $datos['direccion_iglesia'] ?? null]],
            ['Cremación', 'cremacion_b', ['Fecha de cremación' => $fechaOrden('fechahora_cremacion'), 'Entrega de cenizas' => $fechaOrden('fechahora_entrega_cenizas'), 'Urna' => $datos['descripcion_urna'] ?? null]],
            ['Inhumación', 'inhumacion_b', ['Fecha y hora' => $fechaOrden('fechahora_inhumacion'), 'Cementerio' => $cementerioOrden[$datos['cementerios_servicio_id'] ?? 0] ?? null, 'Ubicación' => ($datos['cementerios_servicio_id'] ?? null) == 1 ? ($datos['terreno']['ubicacion_servicio'] ?? null) : ($datos['nota_ubicacion'] ?? null)]],
            ['Traslado', 'traslado_b', ['Fecha y hora' => $fechaOrden('fechahora_traslado'), 'Destino' => $datos['destino_traslado'] ?? null]],
            ['Aseguradora', 'aseguradora_b', ['Aseguradora' => $datos['aseguradora'] ?? null, 'Convenio' => $datos['numero_convenio_aseguradora'] ?? null, 'Teléfono' => $datos['telefono_aseguradora'] ?? null]],
            ['Custodia', 'custodia_b', ['Responsable' => $datos['responsable_custodia'] ?? null, 'Folio de custodia' => $datos['folio_custodia'] ?? null, 'Folio de liberación' => $datos['folio_liberacion'] ?? null]],
        ];
    @endphp
    @foreach ($serviciosOrden as $indiceOrden => $servicioOrden)
        @php $incluidoOrden = ($datos[$servicioOrden[1]] ?? 0) == 1; @endphp
        <div class="contenido orden-servicio-fila">
            <div><img class="orden-icono-servicio" alt="" src="{{ $iconosOrden[$indiceOrden] }}">{{ $servicioOrden[0] }}
                @if (!$incluidoOrden)<span class="orden-no-incluido" title="Servicio no incluido" aria-label="Servicio no incluido">×</span>@endif
            </div>
            <div class="orden-campos">
                @foreach ($servicioOrden[2] as $etiquetaOrden => $datoOrden)
                    <div class="orden-campo"><div class="orden-campo-label">{{ $etiquetaOrden }}</div><div class="orden-campo-valor">{{ $incluidoOrden ? $valorOrden($datoOrden) : 'No disponible' }}</div></div>
                @endforeach
            </div>
        </div>
    @endforeach
    <div class="contenido orden-notas">
        <div class="center size-18px uppercase bg-gray bold">NOTA/OBSERVACIONES</div>
        <p>
            @if ($datos['nota_servicio']!=null)
            {{ $datos['nota_servicio'] }}
            @else
            N/A
            @endif
        </p>
    </div>

</div>
</div>{{-- Cierra el área de ajuste; el pie del visor debe quedar fuera. --}}
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'servicios_funerarios', 'altoMarcaAgua' => 1050])
<style>
/* Área útil carta menos márgenes y holgura. Solo esta orden reduce su escala.
   No se ocultan filas ni se limita la longitud de los textos. */
.orden-hoja-unica{position:relative;height:210mm;overflow:hidden;width:179mm}
.orden-hoja-unica *{page-break-inside:auto!important;page-break-before:auto!important;page-break-after:auto!important}
.orden-hoja-unica>[data-contenido-ajustable]{position:absolute;top:0;left:0;width:100%;box-sizing:border-box;transform-origin:top left;-webkit-transform-origin:top left;margin:0!important;border-left:0!important;border-right:0!important;word-wrap:break-word}
</style>
<script>
// Qt ejecuta este ajuste antes de generar el PDF. El iframe mantiene sus
// scripts bloqueados: VisorReportes aplica la misma medición desde el padre.
(function(){
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
/* Cuadro operativo compacto: una fila por servicio y datos en dos columnas.
   Se conserva todo el Blade condicional; las filas vacías no se inventan.
   La hoja reserva su área propia y el pie queda fuera de la escala. */
.orden-institucional .orden-servicio-fila{display:table;table-layout:fixed;width:100%;border:1px solid #ddd;margin-top:0!important;border-collapse:collapse}
.orden-institucional .orden-servicio-fila>div:first-child{display:table-cell;width:25%;vertical-align:middle;background:#f5f4ef!important;color:#705b24!important;font-size:13px!important;padding:11px!important;line-height:1.5}
.orden-institucional .orden-servicio-fila>.orden-campos{display:table-cell;width:75%;vertical-align:top;padding:8px 12px;background:white}
.orden-campo{display:inline-block;vertical-align:top;width:48%;box-sizing:border-box;padding:5px 10px 5px 0;line-height:1.4;word-wrap:break-word}
.orden-campo:last-child:nth-child(odd){width:100%}
.orden-campo-label{font-size:11px;color:#777;margin-bottom:3px}
.orden-campo-valor{font-size:13px;color:#30383b;font-weight:normal}
.orden-icono-servicio{width:18px;height:18px;filter:brightness(0);opacity:.6;margin-right:6px}
.orden-institucional .orden-persona{padding:11px;margin:13px 0;font-size:18px}
.orden-institucional .orden-titulo{margin:12px 0}
.orden-institucional .orden-titulo h1{font-size:25px!important}
.orden-institucional .orden-notas{display:block!important;margin-top:13px!important}
.orden-institucional .orden-notas>div:first-child{font-size:13px!important;padding:9px 12px!important}
.orden-institucional .orden-notas p{font-size:13px;min-height:0!important;line-height:1.6!important;padding:0 12px 10px!important;margin:0}
@supports(display:grid){
.orden-institucional .orden-servicio-fila{box-sizing:border-box;border-collapse:separate;border-spacing:0;width:calc(100% - 2px)!important}
/* Chrome puede recortar el borde colapsado al escalar el contenedor.
   La línea interior permanece dentro del área visible, también al imprimir. */
.orden-institucional .orden-servicio-fila>.orden-campos{box-shadow:inset -1px 0 0 #ddd}
.orden-institucional .orden-servicio-fila>div:first-child{font-size:10.4px!important;padding:8.8px!important}
.orden-institucional .orden-servicio-fila>.orden-campos{padding:6.4px 9.6px}
.orden-campo-label{font-size:8.8px}.orden-campo-valor{font-size:10.4px}
.orden-campo{padding:4px 8px 4px 0}
.orden-icono-servicio{width:14.4px;height:14.4px}
.orden-institucional .orden-persona{padding:9px;margin:10px 0;font-size:14.4px}
.orden-institucional .orden-titulo h1{font-size:20px!important}
.orden-institucional .orden-notas p{font-size:10.4px}
}
</style><style>.orden-no-incluido{display:inline-block;color:#bd4040!important;font-size:15px;font-weight:bold;line-height:1;margin-left:6px;vertical-align:middle}</style></body>

<style>
/* El área fija ya reserva la página. El mínimo común de la marca de agua
   no debe crear una segunda hoja ni intervenir en la medición de Qt. */
html,body.orden-institucional{min-height:0!important}
</style>
</html>
