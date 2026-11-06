<!doctype html><html lang="es"><head><meta charset="UTF-8"><title>Talonario de pagos</title></head><body class="talonario-planes">
@include('layouts.estilos')
<style>
html body.talonario-planes{font-family:Arial,sans-serif!important;color:#30383b;font-size:12px;margin:0}
.talonario-planes header{display:table;width:100%;border-bottom:1px solid #ab8a29;padding-bottom:12px}
.talonario-planes header>div{display:table-cell;vertical-align:middle}.talonario-planes .logo{width:195px;height:auto;max-width:100%}
.talonario-planes .folios{text-align:right;font-size:14px}.talonario-planes .folios span{display:inline-block;margin-left:24px}
.talonario-planes small{display:block;color:#777;font-size:11px;line-height:1.45}
.talonario-planes h1{font-size:23px;font-weight:normal;margin:15px 0 9px}
.talonario-planes .titular{width:100%;border-collapse:collapse;font-size:13px;margin-bottom:10px}.talonario-planes .titular td:last-child{text-align:right}
.talonario-planes .instruccion{font-size:12px;line-height:1.5;margin:8px 0 12px}
.talonario-planes .banco{width:100%;border-collapse:collapse;border-top:1px solid #ddd;border-bottom:1px solid #ddd;margin:10px 0 4px}
.talonario-planes .banco td{padding:10px 6px;vertical-align:middle;font-size:13px}.talonario-planes .santander{width:125px;height:auto}
.talonario-planes .beneficiario{font-size:11px;margin-bottom:15px}
.talonario-planes .pagos-tabla{width:100%;table-layout:fixed;border-collapse:collapse;font-size:11px}
.talonario-planes .pagos-tabla th{background:#454c4f;color:white;font-size:10px;font-weight:normal;padding:7px 3px;text-align:center}
.talonario-planes .pagos-tabla td{font-size:11px;padding:7px 3px!important;border-bottom:1px solid #ddd;text-align:center;vertical-align:middle;word-wrap:break-word}
.talonario-planes .pagos-tabla td span{font-weight:normal!important}
/* Clases explícitas: colspan de Totales cambia el índice de las celdas, pero no su columna monetaria. */
.talonario-planes .pagos-tabla th.importe{text-align:right!important;vertical-align:middle}
.talonario-planes .pagos-tabla td.importe{text-align:right!important;white-space:nowrap;font-variant-numeric:tabular-nums;font-family:Arial,sans-serif}
.talonario-planes .pagos-tabla td.total-etiqueta{text-align:right!important}
.talonario-planes .pagos-tabla td.referencia{white-space:nowrap;font-size:12px;letter-spacing:0.65px;font-variant-numeric:tabular-nums}
.talonario-planes .pagos-tabla tbody tr:nth-child(even){background:#fafafa}
.talonario-planes .pagos-tabla tbody tr:last-child{background:#f6f4ef;font-weight:bold;border-top:1px solid #ab8a29}
.talonario-planes .mxn{font-size:10px;text-align:right;margin:5px 0}
.talonario-planes .instrucciones{margin-top:18px}.talonario-planes .instrucciones h2{font-size:12px;margin:0 0 7px}
.talonario-planes .instrucciones p{font-size:11px!important;line-height:1.5!important;margin:7px 0!important;text-align:justify}
.talonario-planes .instrucciones span{font-size:inherit!important}
.talonario-planes thead{display:table-header-group}.talonario-planes tr,.talonario-planes p{page-break-inside:avoid;break-inside:avoid;orphans:3;widows:3}
@supports(display:grid){.talonario-planes .logo{width:156px}.talonario-planes .pagos-tabla td{font-size:8.8px}.talonario-planes .pagos-tabla th{font-size:8px}.talonario-planes .pagos-tabla td.referencia{font-size:9.6px;letter-spacing:0.52px}.talonario-planes .instrucciones p{font-size:8.8px!important}}
</style>
<div class="marca-agua-contenido">
{{-- Talonario compacto vertical: conserva logos y todos los campos originales.
     Las fechas, descuentos y totales siguen usando la lógica del formato anterior. --}}
<header><div><img class="logo" src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="folios"><span><small>Número de convenio</small><strong>{{ $datos['numero_convenio'] }}</strong></span><span><small>Venta de plan funerario a futuro</small><strong>{{ $datos['ventas_planes_id'] }}</strong></span></div></header>
<h1>Talonario de pagos</h1>
<table class="titular"><tr><td><small>Nombre del titular</small><strong>{{ $datos['nombre'] }}</strong></td><td><small>Fecha de venta</small>{{ fecha_abr($datos['fecha_operacion']) }}</td></tr></table>
<p class="instruccion">Puede realizar el pago en la sucursal de {{ $empresa->nombre_comercial }} llevando este documento o en cualquier sucursal Santander (depósito o transferencia), debiendo tomar en cuenta los plazos y cantidades establecidas según su contrato.</p>
<table class="banco"><tr><td style="width:24%"><img class="santander" src="{{ public_path('images/santander.png') }}" alt="Santander"><small>Banco para depósitos</small></td><td style="width:33%"><small>Número de cuenta</small>{{ $empresa->cuenta }}</td><td style="width:43%"><small>CLABE para transferencias electrónicas</small>{{ $empresa->clabe }}</td></tr></table><div class="beneficiario">Beneficiario: {{ $empresa->razon_social }}</div>
<table class="pagos-tabla"><colgroup><col style="width:3%"><col style="width:18%"><col style="width:10%"><col style="width:12%"><col style="width:8%"><col style="width:9.8%"><col style="width:9.8%"><col style="width:9.8%"><col style="width:9.8%"><col style="width:9.8%"></colgroup><thead><tr><th>#</th><th>Referencia<br>de pago</th><th>Concepto</th><th>Fecha máxima<br>de pago</th><th>Estatus</th><th class="importe">Pronto<br>pago</th><th class="importe">Monto<br>normal</th><th class="importe">Pagado</th><th class="importe">Intereses</th><th class="importe">Saldo</th></tr></thead><tbody>
                    @foreach ($datos['pagos_programados'] as $pago)
                    @if ($pago['status']!=0)
                    <tr>
                        <td class="py-2"><span class="bold">{{ $pago['num_pago'] }}</span></td>
                        <td class="referencia">{{ $pago['referencia_pago'] }}</td>
                        <td>{{ $pago['concepto_texto'] }}</td>
                        <td>
                            @if ($pago['status_pago']==0)
                            {{ fecha_abr($pago['fecha_a_pagar']) }}
                            @elseif($pago['status_pago']==1)
                            {{ fecha_abr($pago['fecha_programada']) }}
                            @else
                            {{ fecha_abr($pago['fecha_ultimo_pago']) }}
                            @endif

                        </td>
                        <td>{{ $pago['status_pago_texto'] }}</td>
                        <td class="importe">
                            @if ($pago['conceptos_pagos_id']==1 || $pago['conceptos_pagos_id']==3)
                            {{ number_format($pago['monto_programado'],2)}}
                            @else
                            {{ number_format($pago['monto_pronto_pago'],2)}}
                            @endif

                        </td>
                        <td class="importe">{{ number_format($pago['monto_programado'],2)}}</td>
                        <td class="importe">{{ number_format($pago['total_cubierto'],2)}}</td>
                        <td class="importe">{{ number_format($pago['intereses'],2)}}</td>
                        <td class="importe">{{ number_format($pago['saldo_neto'],2)}}</td>
                    </tr>
                    @endif
                    @endforeach
                    <tr>
                        <td class="py-2 total-etiqueta" colspan="5"><span class="bold pr-4">Totales</span></td>
                        <td class="importe">{{ number_format($datos['costo_neto_pronto_pago'],2)}}</td>
                        <td class="importe">{{ number_format($datos['total'],2)}}</td>
                        <td class="importe">{{ number_format($datos['total_cubierto'],2)}}</td>
                        <td class="importe">{{ number_format($datos['intereses'],2)}}</td>
                        <td class="importe">{{ number_format($datos['saldo_neto'],2)}}</td>
                    </tr>
</tbody></table><p class="mxn">Importes en MXN</p>
<div class="instrucciones"><h2>Nota importante</h2><p class="texto-sm justificar line-base">
                    Este documento no es un comprobante de pago si no cuenta con el sello del banco o la firma del
                    personal
                    de
                    <span class="capitalize bold">{{$empresa->nombre_comercial}}</span> que haya registrado el pago.
                </p>
<p class="texto-sm justificar line-base">
                    El titular deberá hacer llegar una copia del comprobante de pago al personal responsable de <span
                        class="capitalize bold">{{$empresa->nombre_comercial}}</span>.
                </p>
<p class="texto-sm justificar line-base">
                    El titular de este convenio deberá guardar las copias de los recibos para futuras aclaraciones.
                </p>
<p class="texto-sm justificar line-base">
                    Para solicitar su factura, debe acudir a las instalaciones de <span
                        class="capitalize bold">{{$empresa->nombre_comercial}}</span> a más tardar a fin de mes que haya
                    realizado la
                    operación de pago.
                </p>
<p class="texto-sm justificar line-base">
                    <span class="uppercase bold texto-sm underline pr-2">cláusula Vigésima tercera.- </span>
                    En caso de retrasarse en el pago mensual de las aportaciones, “El Cliente” deberá cubrir una pena
                    convencional sobre el total del monto de la mensualidad vencida,
                    importe que se considerará como aportación
                    adicional complementaria al cliente. El contratante se obliga a pagar a la agencia funeraria interés
                    moratorio del <span class="bold">{{$datos['ajustes_politicas']['tasa_fija_anual']}}</span>%
                    ({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['tasa_fija_anual'],'',false) }} por
                    ciento)
                    fija
                    anual, la que se calculará y liquidará sobre
                    cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
                    calcularán multiplicando el monto de lo que adeude el contratante por la tasa de interés
                    anual, dividida entre 365, este resultado se multiplica por el número de días transcurridos
                    entre la fecha de pago que debió ser hecho y la fecha que el contratante liquide el adeudo.
                </p></div></div>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'pagos', 'altoMarcaAgua' => 1160])
</body></html>