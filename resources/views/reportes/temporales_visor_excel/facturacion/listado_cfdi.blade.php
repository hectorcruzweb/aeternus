<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>{{ empty($excel) ? $titulo : "Listado de CFDI" }}</title>
<style>
body{margin:0;color:#343a40;font:10px Arial,sans-serif}
table{width:100%;border-collapse:collapse;table-layout:fixed}
th,td{padding:3px 5px;border-bottom:1px solid #ddd;vertical-align:middle;overflow-wrap:break-word;word-wrap:break-word}
thead{display:table-header-group}tr{page-break-inside:avoid}
.importe{text-align:right;white-space:nowrap}
.meta{color:#565e64}
.cabecera{border-bottom:1px solid #b4a06a;margin-bottom:6px}
.cabecera td{border:0}.cabecera img{width:130px}.empresa{text-align:right;line-height:1.35}
.indicadores{margin:14px 0 5px}
.indicadores td{padding:5px 8px;text-align:left;border-bottom:0}
.indicadores td+td{border-left:8px solid white}
.indicadores td.indicador-valor{font-size:17px;padding-bottom:9px}
.indicador-valor small{font-size:9px;font-weight:normal;color:#626970}
.indicadores-secundarios{margin:8px 0 12px;border-top:1px solid #ddd;border-bottom:1px solid #ddd}
.indicadores-secundarios td{border:0;padding:3px 8px}
.indicadores-secundarios tr+tr td{font-size:13px;font-weight:bold;padding-bottom:6px}
.titulo-detalle{font-size:17px;margin:12px 0 6px;page-break-after:avoid}
.detalle{font-size:9px}.detalle td,.detalle th{padding:3px 4px}
.detalle th:nth-child(5),.detalle th:nth-child(6),
.detalle td:nth-child(5),.detalle td:nth-child(6){text-align:center}
.detalle th:nth-child(n+7){text-align:right}
.detalle tbody tr:first-child td{border-top:1px solid #c6c8ca}
.detalle td:nth-child(2){word-break:break-all}
.detalle tbody td{border-bottom-color:#e8e8e8}
.pie-listado{margin-top:10px;border-top:1px solid #ddd;padding-top:5px;color:#626970}
@media print{.pie-listado{display:none}}
</style>
</head>
<body>

@php
    $excel = $excel ?? false;
    $nombreEmpresa = \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null);
    $direccion = \App\Exports\DireccionReportes::formatear($empresa);
    $contacto = implode(' · ', array_filter([$empresa['telefono'] ?? '', $empresa['email'] ?? '']));
    $etiquetas = ['folio'=>'Folio', 'uuid'=>'UUID', 'cliente'=>'Cliente', 'rfc'=>'RFC', 'status'=>'Estado',
        'metodo_pago'=>'Método de pago', 'desde'=>'Timbrado desde', 'hasta'=>'Timbrado hasta',
        'operaciones_relacionadas'=>'Operaciones relacionadas', 'tipos_servicio'=>'Tipos de operación',
        'numero_servicio'=>'Núm. de venta o servicio', 'saldo_pendiente'=>'Saldo'];
    $vinculosPorMoneda = [];
    foreach ($cfdis as $comprobante) {
        $codigo = $monedas->get($comprobante->sat_monedas_id) ?: 'Sin moneda';
        $vinculosPorMoneda[$codigo] = ($vinculosPorMoneda[$codigo] ?? 0)
            + $operaciones->get($comprobante->id, collect())->count();
    }
    $estados = [0=>'Cancelado', 1=>'Vigente', 3=>'En proceso de cancelación'];
    $filtrosTexto = ['Tipo: '.$tipoTexto];
    foreach ($filtros as $campo => $valor) {
        if ($valor === null || $valor === '' || $valor === []) continue;
        if ($campo === 'saldo_pendiente') $valor = 'Con saldo pendiente';
        if ($campo === 'status') $valor = $estados[$valor] ?? $valor;
        if ($campo === 'tipos_servicio') $valor = $tiposOperacion->only($valor)->implode(', ');
        if ($campo === 'operaciones_relacionadas') $valor = $valor === 'con' ? 'Con operaciones' : 'Sin operaciones';
        if (in_array($campo, ['desde', 'hasta'], true)) $valor = fecha_abr($valor);
        $filtrosTexto[] = $etiquetas[$campo].': '.$valor;
    }
@endphp
<table class="cabecera">
    <tr>
        <td colspan="3">@if(!$excel && $logo && is_file($logo))<img src="{{ $logo }}" alt="{{ $nombreEmpresa }}">@elseif(!$excel){{ $nombreEmpresa }}@endif</td>
        <td colspan="5" class="empresa" style="text-align:right;vertical-align:middle" data-type="s"><strong>{{ $nombreEmpresa }}</strong><br>
            {{ $empresa['razon_social'] ?? '' }} · RFC {{ $empresa['rfc'] ?? '' }}<br>
            {{ $direccion }}<br>{{ $contacto }}
        </td>
    </tr>
</table>
<table style="margin-bottom:6px">
    <tr><td colspan="8" style="font-size:15pt;font-weight:bold;color:#80641a;border-bottom:2px solid #a58828">{{ $titulo }}</td></tr>
    <tr><td colspan="8">Consulta {{ $fecha }} · {{ $cfdis->count() }} CFDI · {{ $operaciones->flatten(1)->count() }} vínculos a operaciones</td></tr>
    <tr><td colspan="8" class="meta" data-type="s">{{ implode(' · ', $filtrosTexto) }}</td></tr>
</table>
@foreach($resumen as $codigo => $totales)
    @php
        $cubierto = round($totales['total'] - $totales['saldo'], 2);
        $porcentaje = $totales['total'] > 0 ? $cubierto / $totales['total'] : 0;
        $formatoResumen = '#,##0.00 "'.$codigo.'"';
        $tarjetas = ['Total facturado' => $totales['total'], 'Importe cubierto' => $cubierto,
            'Saldo pendiente' => $totales['saldo']];
        if (!$esIngreso) $tarjetas = ['Total en comprobantes' => $totales['total'], 'Importe aplicado' => $totales['aplicado']];
    @endphp
    <table class="indicadores">
        <tr>
            @foreach($tarjetas as $etiqueta => $importe)
                <td colspan="{{ $excel ? ($esIngreso ? ($loop->first ? 2 : 3) : 4) : 1 }}" style="text-align:left;background-color:#f4f3ef;border-top:2px solid #b4a06a">{{ $etiqueta }}</td>
            @endforeach
        </tr>
        <tr>
            @foreach($tarjetas as $importe)
                <td colspan="{{ $excel ? ($esIngreso ? ($loop->first ? 2 : 3) : 4) : 1 }}" class="indicador-valor"
                    style="text-align:left;background-color:#f4f3ef;font-weight:bold;font-size:14pt" data-type="n" data-format="{{ $formatoResumen }}">{{ $excel ? round($importe, 2) : '$ '.number_format($importe, 2) }}@if(!$excel) <small>{{ $codigo }}</small>@endif</td>
            @endforeach
        </tr>
    </table>
    <table class="indicadores-secundarios">
        <tr>
            <td style="text-align:left" colspan="{{ $excel ? 2 : 1 }}">CFDI</td>
            <td style="text-align:left" colspan="{{ $excel ? ($esIngreso ? 3 : 6) : 1 }}">Vínculos a operaciones</td>
            @if($esIngreso)<td style="text-align:left" colspan="{{ $excel ? 3 : 1 }}">Porcentaje cubierto</td>@endif
        </tr>
        <tr>
            <td style="text-align:left" colspan="{{ $excel ? 2 : 1 }}" data-type="n">{{ $totales['cantidad'] }}</td>
            <td style="text-align:left" colspan="{{ $excel ? ($esIngreso ? 3 : 6) : 1 }}" data-type="n">{{ $vinculosPorMoneda[$codigo] ?? 0 }}</td>
            @if($esIngreso)<td style="text-align:left" colspan="{{ $excel ? 3 : 1 }}" data-type="n" data-format="0.0%">{{ $excel ? $porcentaje : number_format(100 * $porcentaje, 1).' %' }}</td>@endif
        </tr>
    </table>
@endforeach
@if(!$excel)<h2 class="titulo-detalle">Desglose de CFDI</h2>@endif
<table class="detalle">
    <thead>
        <tr>
            @foreach(['Folio / método'=>10,'UUID / referencia'=>24,'Receptor / cliente'=>17,'RFC / tipo'=>13,'Timbrado / fecha'=>10,'Estado'=>8,'Total / aplicado'=>10,($esIngreso ? 'Saldo' : 'Aplicado')=>8] as $label=>$ancho)
                <th style="width:{{ $ancho }}%;background-color:#404649;color:#ffffff">{{ $label }}</th>
            @endforeach
        </tr>
    </thead>
    @forelse($cfdis as $cfdi)
        @php
            $metodo = $metodos->get($cfdi->sat_metodos_pago_id);
            $moneda = $monedas->get($cfdi->sat_monedas_id);
            $vinculadas = $operaciones->get($cfdi->id, collect());
            $pagos = $esIngreso ? $cfdi->pagos_asociados : collect();
            $egresos = $esIngreso ? $cfdi->egresos_asociados : collect();
            $saldo = $esIngreso ? $cfdi->saldo_reporte : ($cfdi->importe_aplicado_reporte ?? 0);
            $formatoMoneda = '#,##0.00'.($moneda ? ' "'.$moneda.'"' : '');
        @endphp
        <tbody>
            <tr style="background-color:{{ $loop->even ? '#f8f8f8' : '#ffffff' }};font-weight:{{ $esIngreso ? 'bold' : 'normal' }};color:#25292c">
                <td data-type="s">{{ $cfdi->id }}{{ $metodo ? ' / '.$metodo : '' }}</td>
                <td data-type="s">{{ $cfdi->uuid ?: 'Sin UUID' }}</td>
                <td data-type="s">{{ $cfdi->nombre_receptor ?: (optional($cfdi->cliente)->nombre ?: 'No disponible') }}</td>
                <td data-type="s">{{ $cfdi->rfc_receptor }}</td>
                <td data-type="s">{{ $cfdi->fecha_timbrado ? fecha_abr($cfdi->fecha_timbrado) : '—' }}</td>
                <td>{{ $estados[$cfdi->status] ?? '—' }}</td>
                <td class="importe" data-type="n" data-format="{{ $formatoMoneda }}">{{ $excel ? (float) $cfdi->total : number_format($cfdi->total, 2) }}@if(!$excel && $moneda)<br><small>{{ $moneda }}</small>@endif</td>
                <td class="importe" data-type="n" data-format="{{ $formatoMoneda }}">{{ $excel ? round($saldo, 2) : number_format($saldo, 2) }}</td>
            </tr>
            @foreach($vinculadas as $op)
                @php
                    $tipo = (int) $op->empresa_operaciones_id;
                    $ref = [1=>$op->ventas_terrenos_id, 2=>$op->ventas_terrenos_id, 3=>$op->servicios_funerarios_id,
                        4=>$op->ventas_planes_id, 5=>$op->ventas_generales_id][$tipo] ?? null;
                @endphp
                <tr>
                    <td style="color:#80641a">Operación</td>
                    <td data-type="s">{{ $ref ?: 'Sin referencia' }}@if($tipo === 2 && $op->cuotas_cementerio_id) · Cuota {{ $op->cuotas_cementerio_id }}@endif</td>
                    <td data-type="s">{{ $op->cliente ?: 'No disponible' }}</td>
                    <td>{{ $tiposOperacion->get($tipo, 'Operación') }}</td>
                    <td data-type="s">{{ $op->fecha_operacion ? fecha_abr($op->fecha_operacion) : '—' }}</td>
                    <td>{{ [0=>'Cancelada', 1=>'Por pagar', 2=>'Pagada'][$op->status] ?? '—' }}</td>
                    <td class="importe" data-type="n" data-format='#,##0.00 "MXN"'>{{ $excel ? (float) $op->total : number_format($op->total, 2) }}@if(!$excel)<br><small>MXN</small>@endif</td>
                    <td class="importe" data-type="n" data-format='#,##0.00 "MXN"'>{{ $excel ? (float) $op->saldo : number_format($op->saldo, 2) }}</td>
                </tr>
            @endforeach
            @foreach(['Pago'=>$pagos, 'Egreso'=>$egresos] as $seccion => $movimientos)
                @foreach($movimientos->sortByDesc('cfdis_id') as $movimiento)
                    <tr>
                        <td style="color:#80641a">{{ $seccion }} {{ $movimiento->cfdis_id }}</td>
                        <td data-type="s" colspan="4">{{ $movimiento->uuid }}</td>
                        <td>{{ $estados[$movimiento->status] ?? '—' }}</td>
                        <td class="importe" data-type="n" data-format="{{ $formatoMoneda }}">{{ $excel ? (float) $movimiento->monto_relacion : number_format($movimiento->monto_relacion, 2) }}@if(!$excel && $moneda)<br><small>{{ $moneda }}</small>@endif</td>
                        <td></td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    @empty
        <tbody><tr><td colspan="8">No se encontraron CFDI con los filtros seleccionados.</td></tr></tbody>
    @endforelse
    @if($resumen)
        <tbody>
            @foreach($resumen as $codigo => $totales)
                <tr style="background-color:#f4f1e7;font-weight:bold">
                    <td colspan="6">Total de CFDI · {{ $codigo }}</td>
                    <td class="importe" data-type="n" data-format="#,##0.00">{{ $excel ? round($totales['total'], 2) : '$ '.number_format($totales['total'], 2) }}</td>
                    <td class="importe" data-type="n" data-format="#,##0.00">{{ $excel ? round($esIngreso ? $totales['saldo'] : $totales['aplicado'], 2) : '$ '.number_format($esIngreso ? $totales['saldo'] : $totales['aplicado'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    @endif

</table>
@if(!$excel)
    <div class="pie-listado">{{ $nombreEmpresa }} · {{ $direccion }} · {{ $contacto }}</div>
@endif
</body>
</html>
