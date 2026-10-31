<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Excel de cuotas: resumen y organización del detalle por cuota según el modo solicitado.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Resumen por cuota; el archivo completo añade una hoja por cuota, sin consultar la BD. */
class CuotasMantenimientoExport implements WithMultipleSheets
{
    private $datos;
    private $completo;
    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado); $completo (solicita
     * desgloses completos donde el reporte los permite).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(array $datos, $completo = false) { $this->datos = $datos; $this->completo = $completo; }
    /**
     * Detalle del método: Organiza las pestañas del archivo a partir de las secciones de
     * CuotasMantenimientoExport.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
     * contexto lo habilita.
     */
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
