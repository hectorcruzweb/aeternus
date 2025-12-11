<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class VentasPorCfdisExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithDrawings,
    ShouldAutoSize,
    WithEvents,
    WithCustomStartCell,
    WithTitle
{
    protected $items;
    protected $summary;
    protected $row_inicio_headings = 7;

    public function __construct($items, $summary)
    {
        $this->items = $items;
        $this->summary = $summary;
    }

    public function title(): string
    {
        return 'Ventas X Mes';
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
        return ''; // Headings will appear in row 7
    }

    // ======================================================
    //  HEADINGS ORIGINALES
    // ======================================================
    public function headings(): array
    {
        return [];
    }

    // ======================================================
    //  ESTILOS
    // ======================================================
    public function styles(Worksheet $sheet)
    {


        return [];
    }
    // ======================================================
    //  CONTENIDO
    // ======================================================
    public function array(): array
    {
        // Espacio antes de headings
        return [];
    }

    // ======================================================
    //  EVENTOS
    // ======================================================
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row_heading = $this->row_inicio_headings - 1;
                // ===== HEADINGS =====
                $sheet->getCell("A{$row_heading}")->setValue("ID Sistema"); // Columna donde está el texto
                $sheet->getCell("B{$row_heading}")->setValue("UUID"); // Columna donde está el texto
                $sheet->getCell("C{$row_heading}")->setValue("Rfc Receptor"); // Columna donde está el texto
                $sheet->getCell("D{$row_heading}")->setValue("Receptor"); // Columna donde está el texto
                $sheet->getCell("E{$row_heading}")->setValue("Fecha Timbrado"); // Columna donde está el texto
                $sheet->getCell("F{$row_heading}")->setValue("$ Total"); // Columna donde está el texto
                $sheet->getStyle("A{$row_heading}:f{$row_heading}")->applyFromArray([
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
                $sheet->getStyle("A{$row_heading}:F{$row_heading}")->getAlignment()->setHorizontal('center');
                //Data
                $desface = 0;
                for ($row = 0; $row < $this->summary['total_cfdis']; $row++) {
                    $row_cfdi = $this->row_inicio_headings + $row + $desface;
                    $sheet->getCell("A{$row_cfdi}")->setValue($this->items[$row]['id']);
                    $sheet->getCell("B{$row_cfdi}")->setValue($this->items[$row]['uuid']);
                    $sheet->getCell("C{$row_cfdi}")->setValue($this->items[$row]['rfc_receptor']);
                    $sheet->getCell("D{$row_cfdi}")->setValue($this->items[$row]['nombre_receptor']);
                    $sheet->getCell("E{$row_cfdi}")->setValue($this->items[$row]['fecha_timbrado']);
                    $sheet->getCell("F{$row_cfdi}")->setValue($this->items[$row]['total']);
                    //center
                    $sheet->getStyle("A{$row_cfdi}:f{$row_cfdi}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => 'center'
                        ]
                    ]);
                    $sheet->getStyle("F{$row_cfdi}")->getNumberFormat()
                        ->setFormatCode('"$ "#,##0.00');
                    //listado de Operaciones ligadas el CFDI

                    if (count($this->items[$row]['operaciones']) > 0) {
                        //tiene operaciones
                        $row_heading_operacion = $row_cfdi + 1;

                        $sheet->mergeCells('C' . $row_heading_operacion . ':F' . $row_heading_operacion);
                        $sheet->setCellValue('C' . $row_heading_operacion, "Operaciones Facturadas");

                        $sheet->getStyle("C{$row_heading_operacion}")->applyFromArray([
                            'alignment' => [
                                'horizontal' => 'center'
                            ]
                        ]);


                        $row_heading_operacion++;
                        $sheet->getCell("C{$row_heading_operacion}")->setValue("Tipo de Operación"); // Columna donde está el texto
                        $sheet->getCell("D{$row_heading_operacion}")->setValue("Clave Sistema"); // Columna donde está el texto
                        $sheet->getCell("E{$row_heading_operacion}")->setValue("Cliente"); // Columna donde está el texto
                        $sheet->getCell("F{$row_heading_operacion}")->setValue("$ Total"); // Columna donde está el texto
                        $sheet->getStyle("C{$row_heading_operacion}:F{$row_heading_operacion}")->applyFromArray([
                            'alignment' => [
                                'horizontal' => 'center'
                            ]
                        ]);
                        $desface++;
                        for ($operacion = 0; $operacion < count($this->items[$row]['operaciones']); $operacion++) {
                            $desface++;
                        }
                    }
                }
                // ==== AUTO SIZE DE TODAS LAS COLUMNAS ====
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
