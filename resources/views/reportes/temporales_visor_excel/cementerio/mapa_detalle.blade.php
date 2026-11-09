{{-- Detalle compartido, filas completas y encabezados repetidos al paginar. --}}
@if($contenido !== 'disponibilidad')
<h3>{{ $contenido === 'servicios' ? 'Servicios funerarios registrados por propiedad' : 'Información de las propiedades vendidas' }}</h3>
<table class="detalle"><thead><tr><th style="width:16%">Ubicación / venta</th>
@if($contenido === 'ventas' || $contenido === 'general')
<th style="width:30%">Titular y contacto</th><th style="width:17%">Fecha / referencias</th><th style="width:12%">Venta MXN</th><th style="width:12%">Por cobrar MXN</th>
@endif
@if($contenido === 'servicios' || $contenido === 'general')
<th>Servicio funerario / fallecido / inhumación</th>
@endif
</tr></thead><tbody>
@forelse($area['detalle'] as $venta)
<tr><td><b>{{ $venta['ubicacion'] }}</b><br><span class="contacto">Venta {{ $venta['venta'] }}</span></td>
@if($contenido === 'ventas' || $contenido === 'general')
<td><b>{{ $venta['cliente'] }}</b><div class="contacto">{{ $venta['telefono'] }}<br>{{ $venta['email'] ?: 'Sin correo' }}</div></td><td>{{ $venta['fecha'] }}<div class="contacto">Solicitud: {{ $venta['solicitud'] }}<br>Convenio: {{ $venta['convenio'] }}</div></td><td class="derecha">$ {{ number_format($venta['total'],2) }}</td><td class="derecha">$ {{ number_format($venta['saldo'],2) }}</td>
@endif
@if($contenido === 'servicios' || $contenido === 'general')
<td>
@if(count($venta['servicios']))
<ul class="personas">
@foreach($venta['servicios'] as $persona)
<li><b>Servicio {{ $persona['id'] }}</b> · {{ $persona['nombre'] }} <span class="contacto">· Inhumación: {{ $persona['fecha'] }}</span></li>
@endforeach
</ul>
@else
<span class="contacto">Sin servicios registrados</span>
@endif
</td>
@endif
</tr>
@empty
<tr><td colspan="6" class="centro">No hay propiedades vendidas para mostrar en esta área.</td></tr>
@endforelse
</tbody></table>
@if($contenido === 'ventas' || $contenido === 'general')
<p class="nota">Por cobrar: capital pendiente de pagos programados activos. No incluye intereses.</p>
@endif
@endif
