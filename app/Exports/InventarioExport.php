<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class InventarioExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithDrawings,
    ShouldAutoSize,
    WithEvents,
    WithCustomStartCell
{
    protected $items;
    protected $summary;
    protected $row_inicio_headings = 7;

    public function __construct($items, $summary)
    {
        $this->items = $items;
        $this->summary = $summary;
    }

    // ======================================================
    //  LOGO
    // ======================================================
    public function drawings()
    {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath(public_path(env('LOGOJPG')));
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1'); // ubicamos en esquina superior izquierda
        return $drawing;
    }


    public function startCell(): string
    {
        return 'A' . $this->row_inicio_headings; // Headings will appear in row 7
    }


    // ======================================================
    //  HEADINGS ORIGINALES
    // ======================================================
    public function headings(): array
    {
        return [
            'Clave Sistema',
            'Descripción',
            'Existencia',
            'Mínimo',
            'Máximo',
            'Bajo Mínimo',
            'Sobre Máximo',
            'Precio Venta',
            'Costo Total',
        ];
    }

    // ======================================================
    //  ESTILOS
    // ======================================================
    public function styles(Worksheet $sheet)
    {

        // ===== PORTADA ESTILO =====
        //$sheet->getStyle('B1:B8')->getFont()->setSize(12)->setBold(true);
        //$sheet->getStyle('B1:B3')->getFont()->setSize(15);
        //$sheet->getStyle('B1:B8')->getAlignment()->setHorizontal('left');
        // ===== HEADINGS =====
        $sheet->getStyle("A{$this->row_inicio_headings}:I{$this->row_inicio_headings}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '22292f']
            ],
            'alignment' => [
                'horizontal' => 'center'
            ]
        ]);

        // ===== DATOS =====
        $start = $this->row_inicio_headings + 1;
        $end   = $sheet->getHighestRow();
        $sheet->getStyle("A{$start}:I{$end}")->getAlignment()->setHorizontal('center');

        $currentRow = $start;
        foreach ($this->items as $item) {


            //eefcto tipo odd row
            if ($currentRow % 2 !== 0) {
                $sheet->getStyle("A{$currentRow}:I{$currentRow}")->applyFromArray([
                    'fill' => [
                        'fillType'   => 'solid',
                        'startColor' => ['rgb' => 'F2F2F2'],
                    ],
                ]);
            }
            if ($item->en_orden != 1) {
                // ❌ NO está en orden
                $sheet->getStyle("C{$currentRow}:C{$currentRow}")
                    ->getFont()
                    ->getColor()
                    ->setRGB('FF0000'); // Amarillo suave
            }

            $currentRow++;
        }
        return [];
    }
    // ======================================================
    //  CONTENIDO
    // ======================================================
    public function array(): array
    {
        // Espacio antes de headings
        $rows = [];
        // ======== DETALLE ARTÍCULOS =========
        foreach ($this->items as $item) {
            $costo = (float)$item->existencia * (float)$item->precio_venta;
            $rows[] = [
                $item->id,
                $item->descripcion,
                $item->existencia,
                $item->minimo ?? 0,
                $item->maximo ?? 0,
                $item->desabastecido ? 'Sí' : 'No',
                $item->sobreabastecido ? 'Sí' : 'No',
                (float)$item->precio_venta > 0 ? (float)$item->precio_venta : '0.00',
                $costo > 0 ? (float)$costo : '0.00',
            ];
        }
        return $rows;
    }

    // ======================================================
    //  EVENTOS
    // ======================================================
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $start = $this->row_inicio_headings + 1;
                $end   = $sheet->getHighestRow();
                $event->sheet->getStyle("H{$start}:H{$end}")
                    ->getNumberFormat()
                    ->setFormatCode('"$ "#,##0.00');
                $event->sheet->getStyle("I{$start}:I{$end}")
                    ->getNumberFormat()
                    ->setFormatCode('"$ "#,##0.00');
                $event->sheet->getStyle("B5:B5")
                    ->getNumberFormat()
                    ->setFormatCode('"$ "#,##0.00');

                // Freeze everything above row 8 (keeps row 7 visible)
                $event->sheet->getDelegate()->freezePane('A8');

                // ==========================================
                //  LOGO (A1)
                // ==========================================
                $sheet->getRowDimension(1)->setRowHeight(60);

                // ==========================================
                //  TITLES MANUALLY POSITIONED
                // ==========================================

                // =========================
                // TITULOS (centrados)
                // =========================
                $sheet->mergeCells('A2:F2')->getStyle('A2')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('b18b1e');
                $sheet->setCellValue('A2', $this->summary['reporte']);
                $sheet->getStyle('H1')->getAlignment()->setHorizontal('left');
                $sheet->getStyle('H1')->getAlignment()->setVertical('center');
                $sheet->getStyle('I1')->getAlignment()->setVertical('center');

                $sheet->mergeCells('A3:F3');
                $sheet->setCellValue('A3', 'Fecha de impresión: ' . fechahora_completa());

                // CENTRAR texto en A2 y A3
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('left');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal('left');

                // =========================
                // RESUMEN (LEFT EN COLUMNA H)
                // =========================
                $sheet->setCellValue('H1', 'Total de Conceptos');
                $sheet->setCellValue('H2', 'Total de Artículos');
                $sheet->setCellValue('H3', 'Total de Servicios');
                $sheet->setCellValue('H4', 'Artículos Desabastecidos');
                $sheet->setCellValue('H5', 'Artículos Sobreabastecidos');

                // Alinear izquierda todos los textos de resumen
                $sheet->getStyle('H1:H5')->getAlignment()->setHorizontal('left');

                // =========================
                // VALORES DEL RESUMEN (derecha en columna I)
                // =========================
                $sheet->setCellValue('I1', $this->summary['total_articulos']);
                $sheet->setCellValue('I2', $this->summary['tipo_articulo']);
                $sheet->setCellValue('I3', $this->summary['tipo_servicio']);
                $sheet->setCellValue('I4', $this->summary['bajo_minimo']);
                $sheet->setCellValue('I5', $this->summary['sobre_maximo']);


                $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A5')->getFont()->setBold(true);
                $sheet->setCellValue('A5', 'Costo Total de Inventario Listado');
                $sheet->setCellValue('B5', $this->summary['costo_total_inventario']);
                $sheet->getStyle('B5')->getAlignment()->setHorizontal('left');


                // Alinear derecha valores numéricos
                $sheet->getStyle('I1:I5')->getAlignment()->setHorizontal('right');

                // ========== Ajuste de fuente ===============
                $sheet->getStyle('H1:H5')->getFont()->setBold(true);

                // Fit ALL columns on one page
                $sheet->getPageSetup()
                    ->setFitToWidth(1)
                    ->setFitToHeight(0); // 0 = unlimited pages vertically

                // Optional: set page orientation to landscape
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                // Optional: adjust paper size
                $sheet->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                // ---- MARGINS (1 cm = 0.39 inch) ----
                $sheet->getPageMargins()->setTop(0.15);
                $sheet->getPageMargins()->setBottom(0.39);
                $sheet->getPageMargins()->setLeft(0.39);
                $sheet->getPageMargins()->setRight(0.39);
            }
        ];
    }
}
