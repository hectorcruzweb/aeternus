<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Resumen por cuota; el archivo completo añade una hoja por cuota, sin consultar la BD. */
class CuotasMantenimientoExport implements WithMultipleSheets
{
    private $datos;
    private $completo;
    public function __construct(array $datos, $completo = false) { $this->datos = $datos; $this->completo = $completo; }
    public function sheets(): array
    {
        $filas = [];
        foreach ($this->datos['cuotas'] as $cuota) $filas[] = [$cuota['descripcion'], $cuota['cantidad'], $cuota['ventas'], $cuota['porCobrar'], $cuota['vencido']];
        $hojas = [new VentasCementerioHoja($this->datos, [
            'nombre' => 'Resumen general', 'nota' => 'Cuotas y pagos por cobrar del mes. Vencido Acumulado Histórico a la fecha de consulta.',
            'columnas' => ['Cuota', 'Cantidad del mes', 'Cuotas del mes', 'Por cobrar del mes', 'Vencido Acumulado Histórico'],
            'filas' => $filas, 'moneda' => [2, 3, 4], 'sumar' => [1],
        ])];
        if ($this->completo) foreach ($this->datos['detalles_cuotas'] as $i => $detalle) {
            // Nombre corto y único incluso si las descripciones coinciden o superan 31 caracteres.
            $hojas[] = new CuotaMantenimientoHoja($detalle, 'Cuota '.($i + 1));
        }
        return $hojas;
    }
}
