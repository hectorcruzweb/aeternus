<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Formato compartido de pagos pendientes: hoy o próximos siete días; sin consultas en el exportador. */
class PagosVenceHoyExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos) { $this->datos = $datos; }
    public function sheets(): array
    {
        $nombreDetalle = $this->datos['etiquetaDetalle'] ?? 'Pagos que vencen hoy';
        $etiquetaImporte = $this->datos['etiquetaImporte'] ?? 'Por cobrar hoy';
        $proximos = (int) ($this->datos['dias'] ?? 1) === 7;
        $resumen = [];
        foreach ($this->datos['resumen']['tipos'] as $f) $resumen[] = [$f['nombre'], $f['cantidad'], $f['programado'], $f['aplicado'], $f['pendiente']];
        $detalle = [];
        foreach ($this->datos['pagos'] ?? [] as $f) {
            $fecha = \Carbon\Carbon::parse($f['fecha_programada']);
            $detalle[] = [$f['tipo_operacion'], (string) $f['numero_venta'], $f['ubicacion_texto'] ?? 'No disponible', (int) $f['empresa_operaciones_id'] === 3 ? (trim($f['fallecido'] ?? '') ?: 'No disponible') : '—', $f['referencia_pago'] ?? '', $f['concepto_pago'] ?? 'No disponible',
                $f['cliente'] ?: 'No disponible', implode(' / ', array_unique(array_filter([$f['telefono'] ?? '', $f['celular'] ?? '']))),
                mb_strtolower(trim($f['email'] ?? ''), 'UTF-8'),
                $fecha->format('d').'/'.ucfirst(rtrim($fecha->locale('es')->translatedFormat('M'), '.')).'/'.$fecha->format('Y'),
                (float) $f['monto_programado'], (float) $f['capital_cubierto'], (float) $f['pendiente'], $f['estado_pago'] ?? 'Vence hoy'];
        }
        return [new VentasCementerioHoja($this->datos, [
            'nombre' => 'Resumen general', 'nota' => $proximos ? 'Pagos pendientes de hoy y los siguientes seis días, agrupados por tipo de operación.' : 'Pagos pendientes que vencen en el día consultado, agrupados por tipo de operación. Cada cantidad corresponde a pagos programados.',
            'columnas' => ['Tipo de operación', 'Pagos pendientes', 'Programado', 'Aplicado a capital', $etiquetaImporte],
            'filas' => $resumen, 'moneda' => [2, 3, 4], 'sumar' => [1],
        ]), new VentasCementerioHoja($this->datos, [
            'nombre' => $nombreDetalle, 'nota' => 'Solo pagos con capital pendiente. El número de venta de Cuotas de Mantenimiento corresponde a la propiedad en cementerio.',
            'columnas' => ['Tipo de operación', 'Núm. venta / servicio', 'Ubicación', 'Fallecido', 'Referencia', 'Concepto del pago', 'Cliente', 'Teléfono / celular', 'Email', 'Fecha programada', 'Programado', 'Aplicado a capital', 'Pendiente', 'Estado'],
            'filas' => $detalle, 'moneda' => [10, 11, 12],
        ])];
    }
}
