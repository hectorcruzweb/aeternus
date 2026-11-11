<table class="resumen">
    <tr>
        <td>Propiedades existentes<b>{{ number_format($totales['total']) }}</b></td>
        <td>Vendidas<b>{{ number_format($totales['vendidos']) }}</b></td>
        <td>Disponibles para venta<b>{{ number_format($totales['disponibles']) }}</b></td>
    </tr>
</table>

<section class="area">
    <h2>Resumen por tipo de propiedad</h2>
    <table class="detalle">
        <thead>
            <tr><th>Tipo de propiedad</th><th>Distribución</th><th>Existentes</th><th>Vendidas</th><th>Disponibles</th></tr>
        </thead>
        <tbody>
            @forelse($grupos as $grupo)
                <tr>
                    <td>{{ $grupo['tipo'] }}</td>
                    <td>{{ $grupo['distribucion'] }}</td>
                    <td class="centro">{{ number_format($grupo['total']) }}</td>
                    <td class="centro">{{ number_format($grupo['vendidos']) }}</td>
                    <td class="centro">{{ number_format($grupo['disponibles']) }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No se encontraron áreas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>

@if($contenido === 'resumida_desglose' && count($grupos))
<section class="area">
    <h2>Desglose por ubicación</h2>
    @foreach($grupos as $grupo)
        <table class="detalle">
            <thead>
                <tr class="detalle-grupo">
                    <th colspan="4">{{ \Illuminate\Support\Str::ucfirst($grupo['tipo']) }}</th>
                </tr>
                <tr><th>Ubicación</th><th>Existentes</th><th>Vendidas</th><th>Disponibles</th></tr>
            </thead>
            <tbody>
                @foreach($grupo['ubicaciones'] as $ubicacion)
                    <tr>
                        <td>{{ $ubicacion['nombre'] }}</td>
                        <td class="centro">{{ number_format($ubicacion['total']) }}</td>
                        <td class="centro">{{ number_format($ubicacion['vendidos']) }}</td>
                        <td class="centro">{{ number_format($ubicacion['disponibles']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</section>
@endif
