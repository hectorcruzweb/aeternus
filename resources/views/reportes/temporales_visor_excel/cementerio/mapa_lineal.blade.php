{{-- Una fila por posición: la ubicación y su venta/servicios nunca se desalinean.
     Tabla paginable; solo cada fila individual se conserva completa. --}}
@php
    $ventasLineales = collect($area['detalle'])->keyBy('fila');
    $conVentas = in_array($contenido, ['ventas','general']);
    $conServicios = in_array($contenido, ['servicios','general']);
    $columnasDatos = ($conVentas ? 5 : 0) + ($conServicios ? 1 : 0);
@endphp
<table class="detalle lineal-integrada"><colgroup><col style="width:2%"><col style="width:5%"><col style="width:2%">
@if($conVentas)
<col style="width:7%"><col style="width:24%"><col style="width:13%"><col style="width:10%"><col style="width:10%">
@endif
@if($conServicios)
<col>
@endif
</colgroup><thead>
{{-- Se repite junto a las columnas cuando la sección continúa en otra hoja. --}}
<tr class="lineal-seccion"><th colspan="{{ 3 + $columnasDatos }}">Sección: {{ $area['nombre'] }} · Detalle de propiedades</th></tr>
<tr><th colspan="3">Posición</th>
@if($conVentas)
<th>Venta</th><th>Titular y contacto</th><th>Fecha / referencias</th><th>Venta MXN</th><th>Por cobrar MXN</th>
@endif
@if($conServicios)
<th>Servicio funerario / fallecido / inhumación</th>
@endif
</tr></thead><tbody>
@foreach($area['filas'] as $fila)
@php
    $celda = $fila['celdas'][0];
    $venta = $ventasLineales->get((int)$celda['numero']);
@endphp
<tr><td class="lineal-lateral">{{ $fila['etiqueta'] }}</td><td class="lineal-posicion {{ $celda['vendida'] ? 'vendida' : '' }} {{ $celda['servicios'] ? 'con-servicio' : '' }}">{{ $celda['numero'] }}</td><td class="lineal-lateral">{{ $fila['etiqueta'] }}</td>
@if($venta)
@if($conVentas)
<td class="centro"><b>{{ $venta['venta'] }}</b></td><td><b>{{ $venta['cliente'] }}</b><div class="contacto">{{ $venta['telefono'] }}<br>{{ $venta['email'] ?: 'Sin correo' }}</div></td><td>{{ $venta['fecha'] }}<div class="contacto">Solicitud: {{ $venta['solicitud'] }}<br>Convenio: {{ $venta['convenio'] }}</div></td><td class="derecha">$ {{ number_format($venta['total'],2) }}</td><td class="derecha">$ {{ number_format($venta['saldo'],2) }}</td>
@endif
@if($conServicios)
<td>
@forelse($venta['servicios'] as $persona)
<div class="lineal-persona"><b>Servicio {{ $persona['id'] }}</b> · {{ $persona['nombre'] }} <span class="contacto">· Inhumación: {{ $persona['fecha'] }}</span></div>
@empty
<span class="contacto">Sin servicios registrados</span>
@endforelse
</td>
@endif
@else
<td colspan="{{ $columnasDatos }}" class="contacto">{{ $celda['vendida'] ? 'Propiedad vendida · Sin servicios registrados' : 'Disponible para venta' }}</td>
@endif
</tr>
@endforeach
</tbody></table>
@if($conVentas)
<p class="nota">Por cobrar: capital pendiente de pagos programados activos. No incluye intereses.</p>
@endif
