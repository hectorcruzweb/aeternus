{{-- Reporte gerencial de cuotas: resumen de activas en la primera hoja y detalle
     por cuota en la siguiente. Recibe empresa/cuotas de get_cuota_pdf_todas;
     comparte el mismo Blade para HTML, PDF e impresión. No consulta la BD. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuotas de mantenimiento · Resumen gerencial</title>
    <style>
        /* Tablas compatibles con Chromium y wkhtmltopdf. Solo presentación:
           no se recalculan los pagos ni los saldos recibidos de get_cuotas. */
        .cuotas-gerencia { font-family:Arial,sans-serif; color:#303238; font-size:11px; }
        .cuotas-gerencia table { width:100%; border-collapse:collapse; }
        .cuotas-gerencia .encabezado { border-bottom:2px solid #b18b1e; margin-bottom:15px; }
        .cuotas-gerencia .encabezado td { padding:0 0 12px; vertical-align:middle; }
        .cuotas-gerencia .logo { width:145px; height:auto; display:block; }
        .cuotas-gerencia .empresa { text-align:right; font-size:9px; line-height:1.5; }
        .cuotas-gerencia h1 { font-size:20px; line-height:1.25; font-weight:bold; margin:0 0 5px; }
        .cuotas-gerencia h2 { font-size:13px; line-height:1.3; margin:0 0 10px; font-weight:bold; }
        .cuotas-gerencia .secundario { font-size:10px; line-height:1.5; color:#696969; }
        .cuotas-gerencia .indicadores { table-layout:fixed; margin:16px 0 12px; }
        .cuotas-gerencia .indicadores > tbody > tr > td { width:33.33%; vertical-align:top; padding:0 5px; }
        .cuotas-gerencia .indicadores > tbody > tr > td:first-child { padding-left:0; }
        .cuotas-gerencia .indicadores > tbody > tr > td:last-child { padding-right:0; }
        .cuotas-gerencia .indicador { border-top:2px solid #cbbd95; background:#f6f5f1; padding:10px 8px; }
        .cuotas-gerencia .etiqueta { font-size:11px; color:#626262; }
        .cuotas-gerencia .importe { font-size:17px; line-height:1.3; font-weight:bold; white-space:nowrap; margin-top:6px; }
        .cuotas-gerencia .moneda { font-size:10px; font-weight:normal; color:#777; }
        .cuotas-gerencia .datos-clave { table-layout:fixed; margin:10px 0 16px; border-top:1px solid #ddd; border-bottom:1px solid #ddd; }
        .cuotas-gerencia .datos-clave td { width:33.33%; padding:9px 0; font-size:11px; }
        .cuotas-gerencia .datos-clave strong { font-size:16px; font-weight:bold; }
        .cuotas-gerencia .analisis { table-layout:fixed; }
        .cuotas-gerencia .analisis > tbody > tr > td { vertical-align:top; }
        .cuotas-gerencia .grupos { width:56%; padding-right:22px; }
        .cuotas-gerencia .prioridad { width:44%; background:#f7f5ef; padding:14px; }
        .cuotas-gerencia .grupo { margin-bottom:14px; page-break-inside:avoid; }
        .cuotas-gerencia .grupo td { font-size:12px; padding:0 0 5px; }
        .cuotas-gerencia .numero { text-align:right; white-space:nowrap; font-variant-numeric:tabular-nums; }
        .cuotas-gerencia .pista { height:4px; background:#eae7de; }
        .cuotas-gerencia .avance { height:4px; background:#ae9139; }
        .cuotas-gerencia .porcentaje-principal { color:#7b6527; font-size:26px; font-weight:bold; margin:4px 0; }
        .cuotas-gerencia .cuotas-prioridad td { padding:10px 0; border-bottom:1px solid #ddd8cb; font-size:9px; white-space:nowrap; }
        .cuotas-gerencia .cuotas-prioridad td:first-child { padding-right:10px; }
        .cuotas-gerencia .recuperacion { margin-top:16px; page-break-inside:avoid; }
        .cuotas-gerencia .recuperacion td { padding-bottom:7px; font-size:11px; }
        .cuotas-gerencia .nota { margin:10px 0 0; font-size:10px; line-height:1.5; color:#666; }
        .cuotas-gerencia .salto-hoja { page-break-before:always; }
        /* Siete columnas compactas en carta vertical; sin recortar descripciones ni importes. */
        .cuotas-gerencia .tabla-cuotas { table-layout:fixed; margin-top:12px; }
        .cuotas-gerencia .tabla-cuotas th { background:#454a4e; color:#fff; font-size:9px; font-weight:normal; white-space:nowrap; padding:7px 3px; text-align:center; }
        .cuotas-gerencia .tabla-cuotas td { font-size:9px; padding:8px 3px; white-space:nowrap; border-bottom:1px solid #ddd; text-align:center; }
        .cuotas-gerencia .tabla-cuotas tbody tr:nth-child(even) { background:#fafafa; }
        .cuotas-gerencia .tabla-cuotas tfoot td { background:#f6f3ea; font-weight:bold; border-top:1px solid #c8b77d; }
        .cuotas-gerencia .tabla-cuotas td.pendiente { font-weight:bold; }
        .cuotas-gerencia .no-activas { margin-top:14px; }
        /* Ahorra espacio vertical al imprimir sin alterar la fuente ni los márgenes de la hoja. */
        @media print {
            .cuotas-gerencia .tabla-cuotas td { padding-top:6px; padding-bottom:6px; }
            .cuotas-gerencia .no-activas h2 { margin-bottom:4px; }
        }
        .cuotas-gerencia .estado-cancelada { color:#9c5550; }
        .cuotas-gerencia thead { display:table-header-group; }
        .cuotas-gerencia tr { page-break-inside:avoid; }
        /* El visor es continuo: mostrar la empresa una sola vez. El selector
           requiere su envoltura exclusiva; PDF e impresión conservan ambos encabezados. */
        @media screen {
            .solicitud-visor-contenido .cuotas-gerencia .salto-hoja { margin-top:24px; padding-top:20px; border-top:1px solid #ddd; }
            .solicitud-visor-contenido .cuotas-gerencia .salto-hoja > .reporte-contenido-limpio > .encabezado { display:none; }
        }

        /* Marca repetida: el contenido se pinta en blanco y las leyendas solo
           aparecen en el espacio sobrante. No agrega altura ni filas al documento. */
        /* wkhtmltopdf limita el fondo al alto del documento: reservar una hoja
           en cada sección corta para cubrir también el sobrante de la última página. */
        /* Reservar el alto imprimible permite pintar también el sobrante final. */
        .cuotas-gerencia > section { min-height:1190px; }
        html, body { background-image:@if(config('reportes.marca_agua.cuotas', true))url(data:image/png;base64,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)@else none @endif!important; background-position:center top!important; background-repeat:repeat!important; }

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
</head>
<body>
    @include('layouts.estilos')
    @php
        // Sumar centavos del listado original; solo status 1 aporta al resumen.
        $activas = [];
        $noActivas = [];
        $totales = ['programado' => 0, 'cubierto' => 0, 'pendiente' => 0, 'asignaciones' => 0];
        $anioConsulta = (int) \Carbon\Carbon::now('America/Mazatlan')->format('Y');
        $grupos = [
            'actual' => ['nombre' => 'Cuotas '.$anioConsulta, 'saldo' => 0],
            'recientes' => ['nombre' => 'Cuotas '.($anioConsulta - 3).'–'.($anioConsulta - 1), 'saldo' => 0],
            'anteriores' => ['nombre' => 'Cuotas hasta '.($anioConsulta - 4), 'saldo' => 0],
            'otros' => ['nombre' => 'Otros años', 'saldo' => 0],
        ];
        foreach ($cuotas as $cuota) {
            $propiedadesCuota = [];
            if (isset($cuota['propiedades'])) {
                // Contar una propiedad una sola vez dentro de su cuota aunque
                // tenga varios programados; no implica un total global de clientes únicos.
                foreach ($cuota['propiedades'] as $propiedad) {
                    foreach ($propiedad['get_pagos_pagos_programados'] ?? [] as $programado) {
                        if ((int) ($programado['status'] ?? 0) === 1 && !empty($propiedad['ventas_terrenos_id'])) {
                            $propiedadesCuota[(int) $propiedad['ventas_terrenos_id']] = true;
                            break;
                        }
                    }
                }
            }
            $fechaInicio = $cuota['fecha_inicio'] ?? '';
            $anioCuota = $fechaInicio ? (int) substr($fechaInicio, 0, 4) : null;
            if (!$anioCuota && preg_match('/(?:19|20)\d{2}/', $cuota['descripcion'] ?? '', $anioEncontrado)) $anioCuota = (int) $anioEncontrado[0];
            $fila = [
                'descripcion' => trim($cuota['descripcion'] ?? '') ?: 'Sin descripción',
                'estado' => (int) ($cuota['status'] ?? -1),
                'cuota' => (int) round((float) ($cuota['cuota_total'] ?? 0) * 100),
                'propiedades' => isset($cuota['propiedades']) ? count($propiedadesCuota) : (int) ($cuota['num_pagos_programados_vigentes'] ?? 0),
                'programado' => (int) round((float) ($cuota['total_x_cuota'] ?? 0) * 100),
                'cubierto' => (int) round((float) ($cuota['total_cubierto'] ?? 0) * 100),
                'pendiente' => (int) round((float) ($cuota['saldo_neto'] ?? 0) * 100),
            ];
            $fila['porcentaje'] = $fila['programado'] > 0 ? $fila['cubierto'] * 100 / $fila['programado'] : 0;
            if ($fila['estado'] !== 1) { $noActivas[] = $fila; continue; }
            $activas[] = $fila;
            foreach (['programado', 'cubierto', 'pendiente'] as $campo) $totales[$campo] += $fila[$campo];
            $totales['asignaciones'] += $fila['propiedades'];
            $grupo = $anioCuota === $anioConsulta ? 'actual' : ($anioCuota && $anioCuota < $anioConsulta && $anioCuota >= $anioConsulta - 3 ? 'recientes' : ($anioCuota && $anioCuota < $anioConsulta - 3 ? 'anteriores' : 'otros'));
            $grupos[$grupo]['saldo'] += $fila['pendiente'];
        }
        // Ordenar solo una copia para identificar prioridades de cobranza.
        $prioridad = array_values(array_filter($activas, function ($fila) { return $fila['pendiente'] > 0; }));
        usort($prioridad, function ($a, $b) { return $b['pendiente'] <=> $a['pendiente']; });
        $prioridad = array_slice($prioridad, 0, 3);
        $saldoPrioridad = array_sum(array_column($prioridad, 'pendiente'));
        $concentracion = $totales['pendiente'] > 0 ? $saldoPrioridad * 100 / $totales['pendiente'] : 0;
        $porcentajeCubierto = $totales['programado'] > 0 ? $totales['cubierto'] * 100 / $totales['programado'] : 0;
        // Formatear al mostrar, después de realizar las sumas en centavos.
        $importe = function ($centavos) { return '$ '.number_format($centavos / 100, 2); };
    @endphp
    <div class="cuotas-gerencia">
        @foreach (['resumen', 'detalle'] as $seccion)
            <section class="{{ $seccion === 'detalle' ? 'salto-hoja' : '' }}">
                <div class="reporte-contenido-limpio">
                <table class="encabezado"><tr>
                    <td style="width:40%;"><img class="logo" src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></td>
                    {{-- Datos corporativos del mismo objeto recibido por el PDF; sin consultas adicionales. --}}
                    <td class="empresa" style="width:60%;">
                        <strong>{{ \App\Exports\NombreEmpresaReportes::formatear($empresa->nombre_comercial ?? null) }}</strong><br>
                        {{ $empresa->razon_social }}<br>
                        {{ \App\Exports\DireccionReportes::formatear($empresa) }}<br>
                        @if (!empty($empresa->telefono)) Teléfono: {{ $empresa->telefono }} @endif
                        @if (!empty($empresa->fax)) · Fax: {{ $empresa->fax }} @endif<br>
                        Cuotas de mantenimiento · MXN · Consulta: {{ fecha_abr(today()) }}
                    </td>
                </tr></table>
                @if ($seccion === 'resumen')
                    <h1>Cuotas de mantenimiento</h1>
                    <div class="secundario">Resumen gerencial · Solo cuotas activas · Capital sin intereses</div>
                    <table class="indicadores"><tr>
                        <td><div class="indicador"><div class="etiqueta">Capital programado</div><div class="importe">{{ $importe($totales['programado']) }} <span class="moneda">MXN</span></div></div></td>
                        <td><div class="indicador"><div class="etiqueta">Capital cubierto</div><div class="importe">{{ $importe($totales['cubierto']) }} <span class="moneda">MXN</span></div></div></td>
                        <td><div class="indicador"><div class="etiqueta">Saldo pendiente</div><div class="importe">{{ $importe($totales['pendiente']) }} <span class="moneda">MXN</span></div></div></td>
                    </tr></table>
                    <table class="datos-clave"><tr>
                        <td>Cuotas activas<br><strong>{{ number_format(count($activas)) }}</strong></td>
                        <td>Asignaciones de cuotas<br><strong>{{ number_format($totales['asignaciones']) }}</strong></td>
                        <td>Capital cubierto<br><strong>{{ number_format($porcentajeCubierto, 1) }} %</strong></td>
                    </tr></table>
                    <table class="analisis"><tr>
                        <td class="grupos">
                            <h2>Saldo pendiente por año de cuota</h2>
                            @foreach ($grupos as $claveGrupo => $grupo)
                                @if ($claveGrupo !== 'otros' || $grupo['saldo'] != 0)
                                    @php $participacion = $totales['pendiente'] > 0 ? $grupo['saldo'] * 100 / $totales['pendiente'] : 0; @endphp
                                    <div class="grupo">
                                        <table><tr><td>{{ $grupo['nombre'] }}</td><td class="numero">{{ $importe($grupo['saldo']) }}</td></tr></table>
                                        <div class="pista"><div class="avance" style="width:{{ max(0, min(100, $participacion)) }}%;"></div></div>
                                        <div class="secundario" style="margin-top:4px;">{{ number_format($participacion, 1) }} % del saldo pendiente</div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="secundario">Agrupación por año de la cuota, no por días de atraso.</div>
                        </td>
                        <td class="prioridad">
                            <h2>Concentración del pendiente</h2>
                            <div class="porcentaje-principal">{{ number_format($concentracion, 1) }} %</div>
                            <div class="secundario">del saldo se concentra en {{ count($prioridad) }} cuotas con mayor pendiente</div>
                            <table class="cuotas-prioridad">
                                @forelse ($prioridad as $fila)
                                    <tr><td>{{ $fila['descripcion'] }}</td><td class="numero">{{ $importe($fila['pendiente']) }}</td></tr>
                                @empty
                                    <tr><td>No hay saldo pendiente en cuotas activas.</td></tr>
                                @endforelse
                            </table>
                        </td>
                    </tr></table>
                    <div class="recuperacion">
                        <table><tr><td>Avance de recuperación del capital</td><td class="numero">{{ number_format($porcentajeCubierto, 1) }} % cubierto</td></tr></table>
                        <div class="pista"><div class="avance" style="width:{{ max(0, min(100, $porcentajeCubierto)) }}%;"></div></div>
                    </div>
                    <p class="nota">Una propiedad puede estar asignada a diferentes cuotas. El capital cubierto puede incluir descuentos aplicados; el saldo pendiente comprende importes vencidos y por vencer, sin intereses.</p>
                    <p class="nota">El desglose de cada cuota se presenta en la siguiente hoja. {{ count($noActivas) }} cuotas no activas se muestran aparte y quedan fuera del resumen.</p>
                @else
                    <h1>Desglose por cuota</h1>
                    <div class="secundario">Cuotas activas · Importes en MXN · Capital sin intereses</div>
                    <table class="tabla-cuotas">
                        <colgroup><col style="width:24%;"><col style="width:9%;"><col style="width:11%;"><col style="width:15%;"><col style="width:15%;"><col style="width:15%;"><col style="width:11%;"></colgroup>
                        <thead><tr><th>Descripción</th><th>Cuota</th><th>Propiedades</th><th>Capital programado</th><th>Capital cubierto</th><th>Saldo pendiente</th><th>% cubierto</th></tr></thead>
                        <tbody>
                            @forelse ($activas as $fila)
                                <tr><td>{{ $fila['descripcion'] }}</td><td>{{ $importe($fila['cuota']) }}</td><td>{{ number_format($fila['propiedades']) }}</td><td>{{ $importe($fila['programado']) }}</td><td>{{ $importe($fila['cubierto']) }}</td><td class="pendiente">{{ $importe($fila['pendiente']) }}</td><td>{{ number_format($fila['porcentaje'], 1) }} %</td></tr>
                            @empty
                                <tr><td colspan="7">No hay cuotas activas registradas.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot><tr><td colspan="3">Total de cuotas activas</td><td>{{ $importe($totales['programado']) }}</td><td>{{ $importe($totales['cubierto']) }}</td><td>{{ $importe($totales['pendiente']) }}</td><td>{{ number_format($porcentajeCubierto, 1) }} %</td></tr></tfoot>
                    </table>
                    <p class="nota">Cuota: importe individual de referencia. Capital programado: importe total asignado. Capital cubierto: parte ya aplicada al capital. Saldo pendiente: importe que queda por cubrir.</p>
                    @if (count($noActivas))
                        <div class="no-activas">
                            <h2>Cuotas no activas · Fuera de los totales</h2>
                            {{-- La aclaración acompaña al título para que no origine una hoja al terminar la tabla. --}}
                            <p class="nota" style="margin:0 0 6px;page-break-after:avoid;">Estos registros son históricos y no se consideran deuda vigente en este resumen.</p>
                            <table class="tabla-cuotas">
                                <colgroup><col style="width:34%;"><col style="width:17%;"><col style="width:17%;"><col style="width:17%;"><col style="width:15%;"></colgroup>
                                <thead><tr><th>Descripción</th><th>Capital programado</th><th>Capital cubierto</th><th>Saldo registrado</th><th>Estado</th></tr></thead>
                                <tbody>@foreach ($noActivas as $fila)<tr><td>{{ $fila['descripcion'] }}</td><td>{{ $importe($fila['programado']) }}</td><td>{{ $importe($fila['cubierto']) }}</td><td>{{ $importe($fila['pendiente']) }}</td><td class="estado-cancelada">{{ $fila['estado'] === 0 ? 'Cancelada' : 'No activa' }}</td></tr>@endforeach</tbody>
                            </table>
                        </div>
                    @endif
                @endif
                </div>
            </section>
        @endforeach
    </div>
</body>
</html>
