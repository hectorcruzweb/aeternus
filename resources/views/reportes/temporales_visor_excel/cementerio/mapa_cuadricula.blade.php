{{-- Cuadrícula común; orientación y estados recibidos del controlador. --}}
<table class="mapa" aria-label="{{ $area['nombre'] }}">
@foreach($area['filas'] as $fila)
<tr><th>{{ $fila['etiqueta'] }}</th>
@foreach($fila['celdas'] as $celda)
<td class="{{ !$celda['existe'] ? 'inexistente' : ($celda['vendida'] ? 'vendida' : '') }} {{ $celda['servicios'] ? 'con-servicio' : '' }}">
@if($celda['existe'])
{{ $celda['numero'] }}
@endif
</td>
@endforeach
<th>{{ $fila['etiqueta'] }}</th></tr>
@endforeach
</table>
