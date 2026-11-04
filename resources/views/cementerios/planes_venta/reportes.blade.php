{{-- Comparativo de precios: conserva los importes, descripciones y disponibilidad
     de get_financiamientos. No recalcula mensualidades ni consulta la BD. --}}
<!DOCTYPE html>
<html lang="{{ $idioma === 'en' ? 'en' : 'es' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('cementerio/lista_precios.titulo_reporte') }}</title>
    <style>
        body.planes-comparativo { font-family:Arial,sans-serif!important; color:#303438!important; font-size:12px; }
        .planes-comparativo table { width:100%; border-collapse:collapse; }
        .planes-comparativo .encabezado { border-bottom:2px solid #ad8b2a; margin-bottom:20px; }
        .planes-comparativo .encabezado td { padding-bottom:12px; vertical-align:middle; }
        .planes-comparativo .marca { width:145px; height:auto; }
        .planes-comparativo .empresa { text-align:right; font-size:9px; line-height:1.5; }
        .planes-comparativo h1 { font-size:19px; margin:0 0 6px; }
        .planes-comparativo .contexto { font-size:10px; color:#737373; margin-bottom:20px; }
        .planes-comparativo .grupo { margin-bottom:20px; }
        .planes-comparativo h2 { background:#f6f4ee; border-left:3px solid #ad8b2a; font-size:11px; padding:8px 9px; margin:0 0 7px; }
        .planes-comparativo h2 span { font-size:9px; font-weight:normal; }
        /* Aviso solo para propiedades sin precios habilitados: texto e icono
           imprimibles, sin depender de fuentes de iconos ni JavaScript. */
        .planes-comparativo .aviso-sin-precios { float:right; color:#b13c36; font-size:9px; font-weight:bold; }
        .planes-comparativo .icono-aviso { display:inline-block; width:12px; height:12px; line-height:12px; border:1px solid #b13c36; background:#b13c36; color:#fff; border-radius:50%; text-align:center; font-weight:bold; margin-right:4px; }
        /* Carta vertical: las columnas de texto reciben más espacio; importes
           y encabezados compactos permanecen en una línea sin recortar datos. */
        .planes-comparativo .precios { table-layout:fixed; }
        .planes-comparativo .precios th { background:#43484b; color:#fff; text-align:center; font-size:9px; font-weight:normal; padding:7px 3px; white-space:nowrap; }
        .planes-comparativo .precios td { text-align:center; font-size:9px; line-height:12px; padding:6px 3px; border-bottom:1px solid #e3e3e3; white-space:nowrap; }
        .planes-comparativo .precios tbody tr:nth-child(even) { background:#fafafa; }
        .planes-comparativo .dinero { white-space:nowrap; font-variant-numeric:tabular-nums; }
        .planes-comparativo thead { display:table-header-group; }
        .planes-comparativo tr { page-break-inside:avoid; }
        .planes-comparativo h2 { page-break-after:avoid; }
        /* Los grupos breves permanecen juntos. Los extensos pueden fluir:
           no forzar toda una tabla grande a una hoja ni reducir la letra. */
        .planes-comparativo .grupo-breve { page-break-inside:avoid; break-inside:avoid-page; }

        /* Marca repetida: el contenido se pinta en blanco y las leyendas solo
           aparecen en el espacio sobrante. No agrega altura ni filas al documento. */
        /* wkhtmltopdf limita el fondo al alto del documento: reservar una hoja
           en cada sección corta para cubrir también el sobrante de la última página. */
        /* Reservar el alto imprimible permite pintar también el sobrante final. */
        body.planes-comparativo { min-height:0; }
        html, body { background-image:none!important; }
        /* Cada hoja tiene altura propia: wkhtmltopdf pinta la trama hasta abajo
           incluso en la última página de un catálogo largo. */
        .planes-hoja { min-height:1190px; page-break-before:always; }
        .planes-hoja--primera { page-break-before:auto; }
        html, body { background-image:@if(config('reportes.marca_agua.planes_venta', true))url(data:image/png;base64,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)@else none @endif!important; background-position:center top!important; background-repeat:repeat!important; }
        .solicitud-visor-contenido .planes-hoja { min-height:0!important; page-break-before:auto; }


        /* El navegador ya pinta el fondo de la hoja: no reservar altura allí
           para evitar saltos extra. El selector solo existe en el visor. */
        body:has(.solicitud-visor-contenido), .solicitud-visor-contenido .cuotas-gerencia > section { min-height:0!important; }
        .solicitud-visor-pie { background:#fff!important; }
        /* Extender el fondo blanco a los márgenes laterales del contenido.
           Los bordes compensan los márgenes negativos: no cambia el ancho de las tablas. */
        .reporte-contenido-limpio { background:#fff; display:flow-root; margin-left:-16px; margin-right:-16px; border-left:16px solid #fff; border-right:16px solid #fff; }
        .reporte-contenido-limpio::after { content:""; display:table; clear:both; }
        /* Solo el visor agrega esta envoltura. Imprimir ignora la regla screen;
           el PDF conserva la trama porque se captura antes de agregar la envoltura. */
        @media screen {
            html:has(.solicitud-visor-contenido), body:has(.solicitud-visor-contenido) { background-image:none!important; }
        }
        @media print { html, body { -webkit-print-color-adjust:exact; print-color-adjust:exact; } }
    </style>
    @include('reportes.temporales_visor_excel.compartidos.paginacion')
</head>
<body class="planes-comparativo">
    @php
        // El idioma es explícito para mantener iguales la vista, descarga y correo.
        $ingles = $idioma === 'en';
        $gruposVisibles = [];
        foreach ($financiamientos as $grupo) {
            if (trim((string) $id_tipo_propiedad) !== '' && (int) $grupo['id'] !== (int) $id_tipo_propiedad) continue;
            $grupo['precios_visibles'] = array_values(array_filter($grupo['precios'], function ($precio) {
                return (int) $precio['status'] !== 0;
            }));
            $gruposVisibles[] = $grupo;
        }
        // Presupuesto conservador en carta vertical y filas de una línea.
        // Cortar grupos extensos en bloques evita tablas más altas que una hoja.
        $paginas = [[]]; $pagina = 0; $ocupado = 160;
        foreach ($gruposVisibles as $grupoPagina) {
            $bloques = array_chunk($grupoPagina['precios_visibles'], 24) ?: [[]];
            foreach ($bloques as $bloque) {
                $altoEstimado = 62 + max(1, count($bloque)) * 28;
                if ($ocupado + $altoEstimado > 1050 && count($paginas[$pagina])) {
                    $pagina++; $paginas[$pagina] = []; $ocupado = 0;
                }
                $grupoPagina['precios_visibles'] = $bloque;
                $paginas[$pagina][] = $grupoPagina;
                $ocupado += $altoEstimado;
            }
        }
    @endphp
    @foreach ($paginas as $indicePagina => $gruposPagina)
    <section class="planes-hoja {{ $indicePagina === 0 ? 'planes-hoja--primera' : '' }}"><div class="reporte-contenido-limpio">
    @if ($indicePagina === 0)
    <table class="encabezado"><tr>
        <td style="width:35%"><img class="marca" src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></td>
        <td class="empresa">
            <strong>{{ \App\Exports\NombreEmpresaReportes::formatear($empresa->nombre_comercial ?? null) }}</strong><br>
            {{ $empresa->razon_social }}<br>{{ \App\Exports\DireccionReportes::formatear($empresa) }}<br>
            @if(!empty($empresa->telefono)) {{ $ingles ? 'Phone' : 'Teléfono' }}: {{ $empresa->telefono }} @endif
            @if(!empty($empresa->fax)) · Fax: {{ $empresa->fax }} @endif
        </td>
    </tr></table>
    <h1>{{ $ingles ? 'Cemetery sales plans' : 'Planes de venta en cementerio' }}</h1>
    <div class="contexto">{{ $ingles ? 'Price list · Amounts in MXN · Generated on' : 'Lista de precios · Importes en MXN · Generado el' }} {{ fecha_abr(\Carbon\Carbon::now('America/Mazatlan')) }}</div>
    @endif
    @forelse($gruposPagina as $grupo)
        <section class="grupo {{ count($grupo['precios_visibles']) <= 6 ? 'grupo-breve' : '' }}">
            <h2>
                @if (empty($grupo['precios_visibles']))
                    <span class="aviso-sin-precios"><span class="icono-aviso" aria-hidden="true">!</span>{{ $ingles ? 'No available prices or financing' : 'Sin precios ni financiamientos disponibles' }}</span>
                @endif
                {{ $grupo['tipo'] }} <span> · {{ __('cementerio/lista_precios.capacidad') }}: {{ $grupo['capacidad'] }} {{ __('cementerio/lista_precios.personas') }}</span></h2>
            <table class="precios">
                <colgroup><col style="width:25%"><col style="width:14%"><col style="width:14%"><col style="width:14%"><col style="width:33%"></colgroup>
                <thead><tr><th>{{ $ingles ? 'Payment plan / term' : 'Modalidad / plazo' }}</th><th>{{ __('cementerio/lista_precios.pago_incial') }}</th><th>{{ __('cementerio/lista_precios.pago_mensual') }}</th><th>{{ __('cementerio/lista_precios.costo_neto') }}</th><th>{{ __('cementerio/lista_precios.descripcion') }}</th></tr></thead>
                <tbody>
                    @forelse($grupo['precios_visibles'] as $precio)
                        <tr><td>{{ $ingles ? $precio['tipo_financiamiento_ingles'] : $precio['tipo_financiamiento'] }}</td>
                            <td class="dinero">$ {{ number_format($precio['pago_inicial'] ?? 0, 2) }}</td>
                            <td class="dinero">$ {{ number_format($precio['pago_mensual'] ?? 0, 2) }}</td>
                            <td class="dinero"><strong>$ {{ number_format($precio['costo_neto'] ?? 0, 2) }}</strong></td>
                            <td>{{ $ingles ? $precio['descripcion_ingles'] : $precio['descripcion'] }}</td></tr>
                    @empty
                        <tr><td colspan="5">{{ $ingles ? 'No available plans.' : 'Sin planes disponibles.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    @empty
        <p>{{ $ingles ? 'No properties found.' : 'No se encontraron propiedades.' }}</p>
    @endforelse
    </div></section>
    @endforeach
</body>
</html>
