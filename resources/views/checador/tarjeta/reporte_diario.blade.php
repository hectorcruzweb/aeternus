<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Tarjeta de asistencia</title>
    <style>
        body { margin:0; color:#293139; font:11px Arial,sans-serif; }
        table { width:100%; border-collapse:collapse; }
        .tarjeta + .tarjeta { page-break-before:always; break-before:page; }
        .cabecera { border-bottom:2px solid #a58828; }
        .cabecera td { padding:0 0 8px; vertical-align:middle; }
        .cabecera img { width:150px; }
        .empresa { text-align:right; line-height:1.4; }
        h1 { font-size:22px; margin:9px 0 4px; }
        h2 { font-size:14px; margin:10px 0 5px; }
        .identidad { margin:0 0 8px; color:#505961; }
        .dia, .cierre, .encabezado { page-break-inside:avoid; break-inside:avoid; }
        .dias { table-layout:fixed; }
        .dias > tbody > tr { page-break-inside:avoid; break-inside:avoid; }
        .columna-dia { width:50%; vertical-align:top; padding:0 3px 5px; }
        .dia { margin:0; table-layout:fixed; }
        .dia th { background:#f5f3ed; text-align:left; padding:4px 6px; font-size:11px; }
        .dia th:first-child { border-left:3px solid #a58828; white-space:nowrap; }
        .dia td { padding:4px 6px; border-bottom:1px solid #ddd; vertical-align:middle; }
        .dia .derecha { text-align:right; }
        .alerta { color:#943e3e; }
        .dia .firma { text-align:center; vertical-align:bottom; }
        .firma span { display:block; border-top:1px solid #888; margin:18px 0 0; padding-top:2px; font-size:9px; }
        .indicadores { table-layout:fixed; background:#f5f3ed; border-top:1px solid #c0ac69; }
        .indicadores td { padding:6px; font-size:10px; }
        .indicadores strong { display:block; font-size:13px; margin-top:2px; }
        .balance { margin:5px 0 0; color:#505961; }
        .cierre { table-layout:fixed; }
        .resumen-periodo { vertical-align:top; }
        .celda-firma-periodo { vertical-align:bottom; padding:0 0 0 12px; }
        .firma-periodo { margin:10mm 0 0; text-align:center; border-top:1px solid #888; padding-top:3px; }
        .espacio-sin-contenido { margin:5mm 0 0; border-top:1px solid #ddd; color:#a8adb0; text-align:center; letter-spacing:2px; font-size:10px; }
        .espacio-sin-contenido td { vertical-align:middle; }
        @media screen { .tarjeta + .tarjeta { margin-top:28px; border-top:1px solid #ddd; padding-top:16px; } }
    </style>
</head>
<body>
@foreach ($datos['tarjetas'] as $empleado)
    <section class="tarjeta{{ count($empleado['tarjeta_checador']) <= 8 ? ' tarjeta-corta' : '' }}">
        <div class="encabezado">
            <table class="cabecera">
                <tr>
                    <td>@if($logo)<img src="{{ $logo }}" alt="Aeternus Funerales">@endif</td>
                    <td class="empresa">
                        <strong>{{ \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null) }}</strong><br>
                        {{ $empresa['razon_social'] ?? '' }}<br>
                        {{ $fecha_del_reporte }}
                    </td>
                </tr>
            </table>
            <h1>Tarjeta de asistencia</h1>
            <p class="identidad"><strong>#{{ $empleado['id'] }} · {{ $empleado['empleado'] }}</strong>
                · Área: {{ $empleado['area'] }} · {{ $empleado['rol']['rol'] ?? 'Sin rol asignado' }} · {{ $empleado['genero'] }}</p>
            <h2>Detalle por día</h2>
        </div>
        @php
            $dias = count($empleado['tarjeta_checador']);
        @endphp
        <table class="dias" role="presentation">
        <tbody>
        @foreach (array_chunk($empleado['tarjeta_checador'], 2) as $fila)
            <tr>
            @foreach ($fila as $tarjeta)
                <td class="columna-dia">
            @php
                $dia = $tarjeta['indicadores_tarjeta'];
                $asistio = $dia['asistido'] === 'Si';
                $estado = $asistio
                    ? ($dia['cumplio_jornada'] === 'Si' ? 'Jornada cumplida' : 'Jornada incompleta')
                    : ($dia['asistido'] === 'No' ? 'Falta' : $dia['asistido']);
            @endphp
            <table class="dia"><colgroup><col style="width:43%"><col style="width:34%"><col style="width:23%"></colgroup>
                <tr>
                    <th>{{ $tarjeta['dia'] }}</th>
                    <th colspan="2" class="derecha {{ in_array($estado, ['Falta', 'Jornada incompleta']) ? 'alerta' : '' }}">{{ $estado }}</th>
                </tr>
                <tr>
                    <td>
                        @foreach ($tarjeta['registros'] as $registro)
                            @if ($registro['registro_valido'])
                                <div>{{ $registro['jornada_texto'] }} · {{ $registro['horas'] }}</div>
                            @endif
                        @endforeach
                        @if ($dia['dia_descanso_obligatorio'] !== 'No')
                            <div>Descanso obligatorio · {{ $dia['dia_descanso_obligatorio'] }}</div>
                        @endif
                    </td>
                    <td class="derecha">
                        Trabajadas {{ $dia['tiempo_trabajado'] }}<br>Extra {{ $dia['horas_extra'] }}<br>
                        Faltantes {{ $dia['tiempo_faltante_jornada'] }}
                    </td>
                    <td class="firma" @if ($dias <= 8) style="height:{{ round(($dias <= 5 ? 132 : 147) / ceil($dias / 2) - 6, 1) }}mm" @endif><span>Firma de conformidad</span></td>
                </tr>
            </table>
                </td>
            @endforeach
            @if (count($fila) === 1)
                <td class="columna-dia"></td>
            @endif
            </tr>
        @endforeach
        </tbody>
        </table>
        @php($resumen = $empleado['indicadores_empleado'])
        <table class="cierre" role="presentation">
            <colgroup><col style="width:75%"><col style="width:25%"></colgroup>
            <tr>
                <td class="resumen-periodo">
                    <h2>Resumen del periodo</h2>
                    <table class="indicadores">
                        <tr>
                            <td>Horas requeridas<strong>{{ $resumen['horas_requeridas'] }}</strong></td>
                            <td>Horas trabajadas<strong>{{ $resumen['tiempo_trabajado'] }}</strong></td>
                            <td>Horas faltantes<strong>{{ $resumen['tiempo_faltante_jornada'] }}</strong></td>
                            <td>Tiempo extra<strong>{{ $resumen['horas_extra'] }}</strong></td>
                            <td>Asistencia<strong>{{ $resumen['porcentaje_asistencia'] }} %</strong></td>
                        </tr>
                    </table>
                    <p class="balance">Faltas {{ $resumen['faltas'] }} · Descansos programados {{ $resumen['dias_a_descansar'] }}
                        · Descansos tomados {{ $resumen['dias_descansados'] }} · Jornada general cumplida {{ $resumen['cumplio_jornada'] }}</p>
                </td>
                <td class="celda-firma-periodo">
                    <div class="firma-periodo">Firma de conformidad del periodo</div>
                </td>
            </tr>
        </table>
        @if ($dias <= 5)
            <table class="espacio-sin-contenido" role="presentation">
                <tr><td style="height:10mm">ESPACIO SIN CONTENIDO</td></tr>
            </table>
        @endif
    </section>
@endforeach
</body>
</html>
