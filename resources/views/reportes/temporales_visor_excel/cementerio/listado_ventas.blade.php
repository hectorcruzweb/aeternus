{{-- Misma fuente de datos que Excel. Solo presentación: no calcula adeudos ni consulta BD. --}}
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Ventas en cementerio</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#edf0f2;color:#343a40;font:12px Arial,sans-serif}.reporte{max-width:816px;margin:20px auto;background:white;padding:32px;min-height:1000px}.cabecera{width:100%;border-bottom:2px solid #ad8b28;padding-bottom:16px}.cabecera td{border:0;padding:0}.logo{width:180px;max-height:80px;object-fit:contain}.empresa{text-align:right;font-size:10px;line-height:1.6}h1{font-size:23px;margin:22px 0 7px}.muted{color:#737b82;font-size:10px}.indicadores{width:100%;border-spacing:6px 0;margin:19px 0}.indicadores td{background:#f6f4ee;border-top:2px solid #c8bb8f;padding:12px;width:33.33%}.indicadores strong{display:block;font-size:21px;margin-top:5px}.filtros{border:1px solid #ddd;padding:10px;line-height:1.7;margin:16px 0}.tabla{width:100%;border-collapse:collapse;table-layout:fixed;font-size:11px}.tabla th{background:#42494c;color:white;padding:9px 5px;text-align:center}.tabla td{padding:9px 6px;border-bottom:1px solid #ddd;vertical-align:top;overflow-wrap:break-word;word-wrap:break-word}.tabla tr:nth-child(even){background:#f6f6f6}.contacto{font-size:10px;line-height:1.6;color:#596168}.estado{font-size:9px;color:#52846b}.cancelado{color:#b04b50}.pie{border-top:1px solid #ddd;margin-top:20px;padding-top:8px;font-size:9px;color:#777}.modo-pdf{background:white}.modo-pdf .reporte{margin:0;padding:0;max-width:none;min-height:0}thead{display:table-header-group}tr{page-break-inside:avoid}h1,.indicadores,.filtros{page-break-inside:avoid}
@media print{@page{size:letter portrait;margin:10mm 10mm 14mm}body{background:white;-webkit-print-color-adjust:exact;print-color-adjust:exact}.reporte{margin:0;padding:0;max-width:none;min-height:0}.tabla{font-size:10px}.tabla td{padding:7px 5px}}

/* Densidad compartida por visor, PDF e impresión. Arial legible sin alturas fijas ni recortes.
   El encabezado de columnas se repite y cada cliente se mantiene completo entre páginas. */
body{font-size:11px}.reporte{padding:24px}.cabecera{padding-bottom:8px}.logo{width:145px;max-height:57px}.empresa{font-size:9px;line-height:1.35}h1{font-size:19px;margin:12px 0 4px}.indicadores{border-collapse:collapse;margin:10px 0}.indicadores td{padding:7px 10px;border-top-width:1px;font-size:10px}.indicadores strong{display:inline;font-size:14px;margin-left:8px}.filtros{padding:6px 8px;margin:9px 0;line-height:1.35;font-size:10px}.tabla{font-size:10px;line-height:1.3}.tabla th{padding:6px 4px}.tabla td{padding:5px 4px}.contacto{font-size:9px;line-height:1.35;color:#505860}.estado{font-size:8px}.alta{text-align:center;font-size:9px}.pie{margin-top:10px;padding-top:5px}
@media print{.tabla{font-size:10px}.tabla td{padding:5px 4px}.cabecera{padding-bottom:6px}.reporte{padding:0}}
/* Las columnas adicionales reciben espacio real, sin sumar anchos sobre el 100%. */
.tabla-contexto{table-layout:auto;font-size:9px}.tabla-contexto col{width:auto!important}.tabla-contexto .contacto{font-size:8px}.tabla-contexto td{padding:4px 3px}.indicador-cliente{text-align:right;white-space:nowrap;font-weight:bold}.nota-extra{font-size:10px;line-height:1.5;margin:7px 0}.nota-extra span{display:inline-block;margin-right:14px}
.ventas{font-size:9px}.ventas th,.ventas td{padding:5px 4px}.ventas .contacto{font-size:8px;line-height:1.35}.importe{text-align:right}.centro{text-align:center}
.personas{margin:3px 0;padding-top:3px;border-top:1px solid #e4e4e4;font-size:8px;line-height:1.35;color:#42494c}
</style>
@include('reportes.temporales_visor_excel.compartidos.zoom_listados')
</head><body class="{{ !empty($modoPdf) ? 'modo-pdf' : '' }}"><main class="reporte">
<table class="cabecera"><tr><td style="width:32%">
@if($logo)<img class="logo" src="{{ $logo }}" alt="Aeternus Funerales">
@endif
</td><td class="empresa"><b>{{ \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null) }}</b><br>{{ $empresa['razon_social'] ?? '' }}<br>{{ \App\Exports\DireccionReportes::formatear($empresa) }}<br>Tel. {{ $empresa['telefono'] ?? '' }} · {{ mb_strtolower($empresa['email'] ?? '', 'UTF-8') }}</td></tr></table>
<h1>Ventas en cementerio</h1><div class="muted">Consulta: {{ $fecha }}</div>
<table class="indicadores"><tr><td>Ventas encontradas<strong>{{ number_format($resumen['total']) }}</strong></td><td>Importe de ventas vigentes<strong>$ {{ number_format($resumen['ventas'],2) }}</strong></td><td>Por cobrar<strong>$ {{ number_format($resumen['pendiente'],2) }}</strong></td></tr></table>
<div class="filtros"><b>Filtros aplicados:</b> {{ implode(' · ',$filtros) }}</div>
<p class="muted">Importes en MXN. Capital sin intereses. 
@if($resumen['canceladas']){{ $resumen['canceladas'] }} ventas canceladas fuera de los totales monetarios.
@endif
</p>

@if($mostrarVencidos)<p class="muted">Saldo vencido de las ventas listadas: <b>$ {{ number_format($resumen['vencido'],2) }} MXN</b></p>
@endif


@if(!empty($mostrarSepultados))<p class="muted"><b>Personas sepultadas registradas en las propiedades listadas: {{ number_format($resumen['sepultados'] ?? 0) }}</b></p>
@endif


@if(!empty($mostrarCuotas))<p class="muted"><b>Cuotas de mantenimiento vencidas: $ {{ number_format($resumen['saldoCuotas'] ?? 0,2) }} MXN</b> · Importe independiente del saldo de la venta, sin intereses.</p>
@endif

<table class="tabla ventas"><colgroup><col style="width:9%"><col style="width:31%"><col style="width:23%"><col style="width:13%"><col style="width:12%"><col style="width:12%"></colgroup><thead><tr><th>Venta / fecha</th><th>Titular y contacto</th><th>Ubicación / referencias</th><th>Total</th><th>Por cobrar</th><th>Estado</th></tr></thead><tbody>

@forelse($filas as $fila)<tr><td class="centro"><b>{{ $fila['venta'] }}</b><br><span class="contacto">{{ $fila['fecha'] }}</span></td><td><b>{{ $fila['cliente'] }}</b><div class="contacto">{{ $fila['celular'] }}<br>{{ $fila['email'] ?: 'Sin correo' }}</div></td><td>{{ $fila['ubicacion'] }}
@if(!empty($mostrarCuotas))<div class="personas"><b>{{ $fila['cuotas_vencidas'] ?? 0 }} cuotas vencidas</b><br>$ {{ number_format($fila['saldo_cuotas_vencidas'] ?? 0,2) }} MXN</div>
@endif

@if(!empty($mostrarSepultados))<div class="personas"><b>{{ $fila['cantidad_sepultados'] ?? 0 }} personas sepultadas</b><br>{{ implode(' · ',$fila['sepultados'] ?? []) ?: 'Sin personas sepultadas' }}</div>
@endif
<div class="contacto">Solicitud: {{ $fila['solicitud'] }} · Convenio: {{ $fila['convenio'] }} · Título: {{ $fila['titulo'] }}</div></td><td class="importe">$ {{ number_format($fila['total'],2) }}</td><td class="importe">$ {{ number_format($fila['pendiente'],2) }}
@if($mostrarVencidos)<div class="contacto">Vencido: $ {{ number_format($fila['vencido'],2) }}<br>{{ $fila['pagos_vencidos'] }} pagos vencidos</div>
@endif
</td><td class="centro {{ $fila['cancelada'] ? 'cancelado' : '' }}">{{ $fila['estado'] }}</td></tr>

@empty
<tr><td colspan="6" class="centro">No se encontraron ventas con los filtros seleccionados.</td></tr>
@endforelse

</tbody></table><div class="pie">{{ $dominio }} · Generado: {{ $fecha }} · {{ count($filas) }} ventas</div></main></body></html>
