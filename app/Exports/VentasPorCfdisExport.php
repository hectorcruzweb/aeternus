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
    protected $reporte;
    protected $row_inicio_headings = 11;

    public function __construct($items, $summary, $reporte)
    {
        $this->items = $items;
        $this->summary = $summary;
        $this->reporte = $reporte;
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


                // =========================
                // RESUMEN Y TITULOS DEL HEADER(centrados)
                // =========================
                $sheet->mergeCells('A6:C6')->getStyle('A6')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('b18b1e');
                $sheet->setCellValue('A6', $this->reporte);

                $sheet->mergeCells('A8:C8');
                $sheet->setCellValue('A8', 'Fecha de impresión: ' . fechahora_completa());


                // =========================
                // RESUMEN (LEFT EN COLUMNA H)
                // =========================
                $sheet->mergeCells('E1:F1');
                $sheet->setCellValue('E1', 'RESUMEN DEL REPORTE');

                $sheet->setCellValue('E2', 'Total de CFDIs');
                $sheet->setCellValue('F2', '$ Total Facturado');
                $sheet->setCellValue('E4', 'Operaciones Facturadas');

                $ventas_cementerio = $this->summary['operaciones']['VENTA EN CEMENTERIO'];
                $cuotas_cementerio = $this->summary['operaciones']['CUOTA DE MANTENIMIENTO'];
                $servicios_funerarios = $this->summary['operaciones']['SERVICIO FUNERARIO'];
                $ventas_planes_a_futuro = $this->summary['operaciones']['VENTA DE PLAN A FUTURO'];
                $ventas_en_gral = $this->summary['operaciones']['VENTA EN GENERAL'];
                $sheet->setCellValue(
                    'F4',
                    "Ventas en Cementerio: {$ventas_cementerio['count']}"
                );
                $sheet->setCellValue('E6', "Cuotas de Mantenimiento: {$cuotas_cementerio['count']}");
                $sheet->setCellValue('F6', "Servicios Funerarios: {$servicios_funerarios['count']}");
                $sheet->setCellValue('E8', "Venta de Planes a Futuro: {$ventas_planes_a_futuro['count']}");
                $sheet->setCellValue('F8', "Ventas en General: {$ventas_en_gral['count']}");
                $centerAlign = [
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center',
                    ],
                ];
                $textBold = [
                    'font' => [
                        'bold' => true
                    ],
                ];
                $color_header_black = [
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '2C3E50']
                    ],
                    'font' => [
                        'color' => ['rgb' =>  'ffffff']
                    ]
                ];
                $bg_primary = [
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'b18b1e']
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' =>  'ffffff']
                    ]
                ];
                /**DATOS DEL RESUMEN */
                $sheet->getStyle('E1')->applyFromArray($textBold)->applyFromArray($bg_primary);
                $sheet->getStyle('E2:F2')->applyFromArray($color_header_black)->applyFromArray($textBold);
                $sheet->getStyle('E4:F4')->applyFromArray($color_header_black)->applyFromArray($textBold);
                $sheet->getStyle('E6:F6')->applyFromArray($color_header_black)->applyFromArray($textBold);
                $sheet->getStyle('E8:F8')->applyFromArray($color_header_black)->applyFromArray($textBold);
                $sheet->getStyle('E1:F9')->applyFromArray($centerAlign);
                $sheet->getStyle("F1:F9")->applyFromArray($centerAlign);
                $sheet->getStyle("E1:E9")->applyFromArray($centerAlign);
                $sheet->getStyle("F2:F8")->applyFromArray($centerAlign);

                $sheet->getCell("E3")->setValue($this->summary['total_cfdis']);
                $sheet->getCell("F3")->setValue($this->summary['monto_facturado']);
                $sheet->getStyle("F3")->getNumberFormat()
                    ->setFormatCode('"$ "#,##0.00');
                $sheet->getCell("E5")->setValue($this->summary['total_operaciones']);

                $sheet->getCell("F5")->setValue($ventas_cementerio['total']);
                $sheet->getCell("E7")->setValue($cuotas_cementerio['total']);
                $sheet->getCell("F7")->setValue($servicios_funerarios['total']);
                $sheet->getCell("E9")->setValue($ventas_planes_a_futuro['total']);
                $sheet->getCell("F9")->setValue($ventas_en_gral['total']);
                $sheet->getStyle("F5")->getNumberFormat()->setFormatCode('"$ "#,##0.00');
                $sheet->getStyle("E7")->getNumberFormat()->setFormatCode('"$ "#,##0.00');
                $sheet->getStyle("F7")->getNumberFormat()->setFormatCode('"$ "#,##0.00');
                $sheet->getStyle("E9")->getNumberFormat()->setFormatCode('"$ "#,##0.00');
                $sheet->getStyle("F9")->getNumberFormat()->setFormatCode('"$ "#,##0.00');
                /**DATOS DEL RESUMEN */

                $heading_cfdi = $this->row_inicio_headings;
                // ===== HEADINGS =====
                $sheet->getCell("A{$heading_cfdi}")->setValue("ID Sistema / Tipo"); // Columna donde está el texto
                $sheet->getCell("B{$heading_cfdi}")->setValue("UUID"); // Columna donde está el texto
                $sheet->getCell("C{$heading_cfdi}")->setValue("Rfc Receptor"); // Columna donde está el texto
                $sheet->getCell("D{$heading_cfdi}")->setValue("Receptor"); // Columna donde está el texto
                $sheet->getCell("E{$heading_cfdi}")->setValue("Fecha Timbrado"); // Columna donde está el texto
                $sheet->getCell("F{$heading_cfdi}")->setValue("$ Total"); // Columna donde está el texto
                $sheet->getStyle("A{$heading_cfdi}:f{$heading_cfdi}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '2C3E50']
                    ],
                    'alignment' => [
                        'horizontal' => 'center'
                    ]
                ]);

                //Data
                $row_cfdi = $this->row_inicio_headings + 1;

                $event->sheet->getDelegate()->freezePane('A' .   $row_cfdi);
                for ($row = 0; $row < $this->summary['total_cfdis']; $row++) {
                    $sheet->getCell("A{$row_cfdi}")->setValue($this->items[$row]['id'] . " / " . $this->items[$row]['metodo_clave']);
                    $sheet->getCell("B{$row_cfdi}")->setValue($this->items[$row]['uuid']);
                    $sheet->getCell("C{$row_cfdi}")->setValue($this->items[$row]['rfc_receptor']);
                    $sheet->getCell("D{$row_cfdi}")->setValue($this->items[$row]['nombre_receptor']);
                    $sheet->getCell("E{$row_cfdi}")->setValue($this->items[$row]['fecha_timbrado']);
                    $sheet->getCell("F{$row_cfdi}")->setValue($this->items[$row]['total']);
                    //center
                    $sheet->getStyle("A{$row_cfdi}:f{$row_cfdi}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center'
                        ],
                        'fill' => [
                            'fillType' => 'solid',
                            'startColor' => ['rgb' => 'EFF3F8']
                        ],
                    ])->applyFromArray($textBold);
                    $sheet->getStyle("F{$row_cfdi}")->getNumberFormat()
                        ->setFormatCode('"$ "#,##0.00');
                    $sheet->getRowDimension($row_cfdi)->setRowHeight(20);
                    //Operaciones
                    $row_cfdi++;
                    $tiene_operaciones = count($this->items[$row]['operaciones']) > 0 ? true : false;
                    $heading_operacion = $row_cfdi;
                    $sheet->mergeCells('C' . $heading_operacion . ':F' . $heading_operacion);
                    $sheet->setCellValue('C' . $heading_operacion,  $tiene_operaciones ? "Operaciones Facturadas" : "Sin operaciones facturadas");
                    $sheet->getStyle("C{$heading_operacion}")->applyFromArray(
                        [
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => $tiene_operaciones ? '111827' : '111827']
                            ],
                            'fill' => [
                                'fillType' => 'solid',
                                'startColor' => ['rgb' => $tiene_operaciones ? 'C9B36A' : 'E2CFCF']
                            ],
                            'alignment' => [
                                'horizontal' => 'center'
                            ]
                        ]
                    );

                    if ($tiene_operaciones) {
                        //Hay operaciones
                        $heading_operacion++;
                        $sheet->getCell("C{$heading_operacion}")->setValue("Tipo de Operación"); // Columna donde está el texto
                        $sheet->getCell("D{$heading_operacion}")->setValue("Clave Sistema"); // Columna donde está el texto
                        $sheet->getCell("E{$heading_operacion}")->setValue("Cliente"); // Columna donde está el texto
                        $sheet->getCell("F{$heading_operacion}")->setValue("$ Total"); // Columna donde está el texto
                        $sheet->getStyle("C{$heading_operacion}:F{$heading_operacion}")->applyFromArray(
                            [
                                'font' => [
                                    'bold' => true,
                                    'color' => ['rgb' => '111827']
                                ],
                                'fill' => [
                                    'fillType' => 'solid',
                                    'startColor' => ['rgb' => 'EFE5C6']
                                ],
                                'alignment' => [
                                    'horizontal' => 'center'
                                ]
                            ]
                        );
                        $row_cfdi++;
                        for ($operacion = 0; $operacion < count($this->items[$row]['operaciones']); $operacion++) {
                            $row_cfdi++;
                            $sheet->getCell("C{$row_cfdi}")->setValue($this->items[$row]['operaciones'][$operacion]['tipo_operacion']);
                            $sheet->getCell("D{$row_cfdi}")->setValue($this->items[$row]['operaciones'][$operacion]['id_referencia']);
                            $sheet->getCell("E{$row_cfdi}")->setValue($this->items[$row]['operaciones'][$operacion]['cliente']);
                            $sheet->getCell("F{$row_cfdi}")->setValue($this->items[$row]['operaciones'][$operacion]['total']);
                            $sheet->getStyle("F{$row_cfdi}")->getNumberFormat()
                                ->setFormatCode('"$ "#,##0.00');
                            $sheet->getStyle("C{$row_cfdi}:F{$row_cfdi}")->applyFromArray(
                                [
                                    'alignment' => [
                                        'horizontal' => 'center'
                                    ]
                                ]
                            );
                            if ($operacion % 2 !== 0) {
                                $sheet->getStyle("C{$row_cfdi}:F{$row_cfdi}")->applyFromArray([
                                    'fill' => [
                                        'fillType'   => 'solid',
                                        'startColor' => ['rgb' => 'F2F2F2'],
                                    ],
                                ]);
                            }
                        }
                        $row_cfdi++;
                    }
                    $row_cfdi++;

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
                // ==== AUTO SIZE DE TODAS LAS COLUMNAS ====
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
