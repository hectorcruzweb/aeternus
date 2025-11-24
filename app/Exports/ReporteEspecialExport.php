<?php
namespace App\Exports;

use App\Operaciones;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteEspecialExport implements
FromArray,
WithHeadings,
WithColumnFormatting,
ShouldAutoSize,
WithStyles
{
    protected $operaciones;

    public function __construct($operaciones)
    {
        $this->operaciones = $operaciones;
    }

    public function headings(): array
    {
        return [
            'Operacion ID',
            'Receptor',
            'RFC',
            'Tipo Operación',
            'ID CFDI(PPD) Sistema',
            'UUID',
            'Total CFDI',
            'Total Cubierto',
            'Saldo Pendiente',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '$#,#0.00',
            'H' => '$#,#0.00',
            'I' => '$#,#0.00',
        ];
    }

    /**  🎨 Styles: header + odd rows */
    public function styles(Worksheet $sheet)
    {
        // Header row = Row 1
        $sheet->getStyle('A1:I1')->applyFromArray([
            'fill'      => [
                'fillType'   => 'solid',
                'startColor' => ['rgb' => '1F4E78'], // Azul elegante
            ],
            'font'      => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'], // Blanco
            ],
            'alignment' => [
                'horizontal' => 'center', // <-- Centrar texto
                'vertical'   => 'center',
            ],
        ]);

        // Odd row shading + center text
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {

            // Center all cells for this row
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical'   => 'center',
                ],
            ]);

            // Odd row shading (zebra)
            if ($row % 2 !== 0) {
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'fill' => [
                        'fillType'   => 'solid',
                        'startColor' => ['rgb' => 'F2F2F2'],
                    ],
                ]);
            }
        }

        return [];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->operaciones as $op) {
            foreach ($op->cfdis as $cfdi) {

                switch ($op->empresa_operaciones_id) {
                    case 1:$id = $op->ventas_terrenos_id;
                        break;
                    case 2:$id = $op->id;
                        break;
                    case 3:$id = $op->servicios_funerarios_id;
                        break;
                    case 4:$id = $op->ventas_planes_id;
                        break;
                    case 5:$id = $op->ventas_generales_id;
                        break;
                    default: $id = 'N/A';
                        break;
                }

                $total = (float) $cfdi->total ?? '0.00';

                $monto_relacionado_total = (float) $cfdi->monto_relacionado_total > 0 ? (float) $cfdi->monto_relacionado_total : '0.00';

                $saldo_pendiente_facturacion = (float) $cfdi->saldo_pendiente_facturacion ?? '0.00';
                $rows[]                      = [
                    $id,
                    $cfdi->cliente_nombre,
                    $cfdi->rfc_receptor,
                    $op->tipo_operacion,
                    $cfdi->id,
                    $cfdi->uuid,
                    $total,
                    $monto_relacionado_total,
                    $saldo_pendiente_facturacion,
                ];
            }
        }

        return $rows;
    }
}
