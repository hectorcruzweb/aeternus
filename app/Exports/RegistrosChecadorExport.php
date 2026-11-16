<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/** Exporta la misma tabla del visor con el encabezado corporativo y papel carta vertical. */
class RegistrosChecadorExport extends VentasCementerioHoja
{
    public function __construct(array $datos)
    {
        $datos['encabezadoPeriodo'] = $datos['periodo'] . ' | Consulta: ' . $datos['fecha'];
        $datos['resumenTexto'] = 'Registros: ' . count($datos['filas']) . ' | Entradas: ' . $datos['entradas']
            . ' | Salidas: ' . $datos['salidas'] . ' | Cancelados: ' . $datos['cancelados'] . ' | Incidencias: ' . $datos['incidencias'];
        $datos['avisoSaldos'] = '';
        parent::__construct($datos, [
            'nombre' => 'Registros', 'columnas' => $datos['columnas'], 'filas' => $datos['filas'],
            'nota' => 'Empleado: ' . $datos['empleado'] . ' | Área: ' . $datos['area'],
            'sinTotal' => true, 'moneda' => [], 'estado' => 5, 'altoFila' => 24,
            'anchos' => ['A' => 23, 'B' => 43, 'C' => 14, 'D' => 12, 'E' => 20, 'F' => 12, 'G' => 20],
        ]);
    }

    public function registerEvents(): array
    {
        $eventos = parent::registerEvents();
        $estiloCompartido = $eventos[AfterSheet::class];
        $eventos[AfterSheet::class] = function (AfterSheet $evento) use ($estiloCompartido) {
            $estiloCompartido($evento);
            $hoja = $evento->sheet->getDelegate();
            $hoja->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT)->setPaperSize(PageSetup::PAPERSIZE_LETTER);
            $hoja->getRowDimension(9)->setVisible(false);
            if ($hoja->getHighestDataRow() >= 11) {
                $hoja->getStyle('B11:B' . $hoja->getHighestDataRow())->getAlignment()
                    ->setWrapText(false)->setShrinkToFit(true);
            }
        };
        return $eventos;
    }
}
