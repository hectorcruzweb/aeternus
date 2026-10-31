<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Hoja Excel particular para presentar el detalle de una cuota de mantenimiento.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/** Tres apartados en una hoja por cuota; conserva el encabezado y protección corporativos. */
class CuotaMantenimientoHoja extends VentasCementerioHoja
{
    private $detalle;
    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado); $nombre (título de la hoja
     * o etiqueta del contexto).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(array $datos, $nombre)
    {
        $this->detalle = $datos;
        parent::__construct($datos, ['nombre' => $nombre, 'nota' => 'Detalle de la cuota. Importes expresados en MXN.',
            'columnas' => array_fill(0, 13, ''), 'filas' => [], 'moneda' => [], 'sinTotal' => true]);
    }
    /**
     * Detalle del método: Registra el formato posterior a llenar la hoja: encabezado, rangos, importes,
     * estilos e impresión según esta clase.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Mapa de eventos de Maatwebsite Excel; el formato se aplica al ejecutarse
     * AfterSheet, no al declarar el método.
     */
    public function registerEvents(): array
    {
        $base = parent::registerEvents()[AfterSheet::class];
        return [AfterSheet::class => function (AfterSheet $evento) use ($base) {
            $base($evento);
            $s = $evento->sheet->getDelegate();
            $s->removeRow(10, 3);
            $fila = 10;
            foreach (VentasCementerioExport::secciones($this->detalle) as $seccion) {
                $n = count($seccion['columnas']); $last = Coordinate::stringFromColumnIndex($n);
                $s->mergeCells('A'.$fila.':'.$last.$fila)->setCellValueExplicit('A'.$fila, $seccion['nombre'], DataType::TYPE_STRING);
                $s->getStyle('A'.$fila.':'.$last.$fila)->getFill()->setFillType('solid')->getStartColor()->setRGB($seccion['nombre'] === 'Vencidos acumulados' ? 'A53C3C' : 'F6F4EE');
                $s->getStyle('A'.$fila.':'.$last.$fila)->getFont()->setBold(true)->getColor()->setRGB($seccion['nombre'] === 'Vencidos acumulados' ? 'FFFFFF' : '343A40');
                $s->getRowDimension($fila++)->setRowHeight(25);
                $s->mergeCells('A'.$fila.':'.$last.$fila)->setCellValueExplicit('A'.$fila, $seccion['nota'], DataType::TYPE_STRING);
                $s->getStyle('A'.$fila.':'.$last.$fila)->getAlignment()->setWrapText(true); $s->getRowDimension($fila++)->setRowHeight(28);
                $inicio = $fila;
                foreach ($seccion['columnas'] as $i => $texto) $s->setCellValueExplicit(Coordinate::stringFromColumnIndex($i + 1).$fila, $texto, DataType::TYPE_STRING);
                $s->getStyle('A'.$fila.':'.$last.$fila)->getFill()->setFillType('solid')->getStartColor()->setRGB('343A40');
                $s->getStyle('A'.$fila.':'.$last.$fila)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $s->getRowDimension($fila++)->setRowHeight(32);
                foreach ($seccion['filas'] as $valores) {
                    foreach ($valores as $i => $valor) $s->setCellValueExplicit(Coordinate::stringFromColumnIndex($i + 1).$fila, $valor, is_string($valor) ? DataType::TYPE_STRING : DataType::TYPE_NUMERIC);
                    if ($fila % 2 === 0) $s->getStyle('A'.$fila.':'.$last.$fila)->getFill()->setFillType('solid')->getStartColor()->setRGB('F3F4F5');
                    if (isset($seccion['estado']) && $valores[$seccion['estado']] === 'Vencido') $s->getStyle(Coordinate::stringFromColumnIndex($seccion['estado'] + 1).$fila)->getFont()->getColor()->setRGB('A53C3C');
                    $s->getRowDimension($fila++)->setRowHeight(34);
                }
                if (!$seccion['filas']) { $s->setCellValue('A'.$fila++, 'Sin registros.'); }
                $s->setCellValue('A'.$fila, 'TOTAL');
                foreach ($seccion['moneda'] as $i) {
                    $col = Coordinate::stringFromColumnIndex($i + 1);
                    $s->setCellValue($col.$fila, array_sum(array_column($seccion['filas'], $i)));
                    $s->getStyle($col.($inicio + 1).':'.$col.$fila)->getNumberFormat()->setFormatCode('"$" #,##0.00;[Red]-"$" #,##0.00;"$" 0.00');
                }
                $s->getStyle('A'.$fila.':'.$last.$fila)->getFont()->setBold(true);
                $s->getStyle('A'.$inicio.':'.$last.$fila)->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
                $fila += 2;
            }
            $s->getStyle('A1:M'.$fila)->getFont()->setName('Arial')->setSize(10);
            $s->getStyle('A5')->getFont()->setSize(15);
            foreach (['A'=>30,'B'=>16,'C'=>28,'D'=>18,'E'=>32,'F'=>23,'G'=>32,'H'=>30,'I'=>19,'J'=>20,'K'=>22,'L'=>22,'M'=>16] as $col=>$ancho) $s->getColumnDimension($col)->setWidth($ancho);
            $s->setAutoFilter('A10:A10'); $s->removeAutoFilter();
            $s->freezePane('D10');
            $s->getPageSetup()->setPrintArea('A1:M'.($fila - 1))->setRowsToRepeatAtTopByStartAndEnd(1, 9);
        }];
    }
}
