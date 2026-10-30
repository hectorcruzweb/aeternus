<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/** Presentación del inventario. Recibe los datos y filtros del controlador sin consultarlos. */
class InventarioExport implements FromArray, WithStrictNullComparison, WithHeadings, WithDrawings, WithEvents, WithCustomStartCell, WithTitle
{
    protected $items;
    protected $summary;
    protected $row_inicio_headings = 10;
    protected $generadoEn;


    public function __construct($items, $summary)
    {
        $this->items = $items;
        $this->summary = $summary;
        $this->generadoEn = Carbon::now('America/Mazatlan')->format('d') . '/' . ucfirst(rtrim(Carbon::now('America/Mazatlan')->locale('es')->translatedFormat('M'), '.')) . '/' . Carbon::now('America/Mazatlan')->format('Y H:i');
    }

    public function title(): string
    {
        return 'Inventario';
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setPath(public_path(env('LOGOJPG')));
        $drawing->setHeight(55);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(6);
        $drawing->setOffsetY(5);
        return $drawing;
    }

    public function startCell(): string
    {
        return 'A' . $this->row_inicio_headings;
    }

    public function headings(): array
    {
        return ['Clave', 'Descripción', 'Existencia', 'Mín.', 'Máx.', 'Estado', 'Precio de venta', 'Valor total'];
    }

    /** Una sola leyenda sustituye las dos columnas Sí/No; los servicios no se clasifican como existencias. */
    protected function estado($item): string
    {
        if ((int) $item->tipo_articulos_id === 2) {
            return 'Servicio';
        }
        if ($item->desabastecido) {
            return 'Bajo mínimo';
        }
        if ($item->sobreabastecido) {
            return 'Sobre máximo';
        }
        return $item->en_orden ? 'En rango' : 'Sin clasificar';
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->items as $item) {
            $valor = (float) $item->existencia * (float) $item->precio_venta;
            // Conserva la regla anterior de importes negativos: su revisión queda separada del diseño.
            $rows[] = [
                $item->id,
                $item->descripcion,
                $item->existencia,
                $item->minimo ?? 0,
                $item->maximo ?? 0,
                $this->estado($item),
                max((float) $item->precio_venta, 0),
                max($valor, 0),
            ];
        }
        return $rows;
    }

    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $inicio = $this->row_inicio_headings + 1;
            $fin = $inicio + count($this->items) - 1;
            $ultimaFila = max($inicio, $fin);
            $moneda = '"$ "#,##0.00;[Red]-"$ "#,##0.00;"$ "0.00';

            // Arial se aplica al libro y a todas las celdas del reporte, incluidos encabezados y resumen.
            $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
            $sheet->getStyle("A1:H{$ultimaFila}")->getFont()->setName('Arial')->setSize(10)->getColor()->setRGB('343A40');
            $sheet->getStyle("A1:H{$ultimaFila}")->getAlignment()->setVertical('center');
            $sheet->setShowGridlines(false);
            $sheet->setPrintGridlines(false);
            // Zoom de pantalla independiente del ajuste de impresión.
            $sheet->getSheetView()->setZoomScale(115);
            foreach (['A' => 12, 'B' => 52, 'C' => 14, 'D' => 10, 'E' => 10, 'F' => 22, 'G' => 22, 'H' => 23] as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }
            $sheet->getDefaultRowDimension()->setRowHeight(23);
            $sheet->getRowDimension(1)->setRowHeight(22);
            $sheet->getRowDimension(2)->setRowHeight(22);
            $sheet->getRowDimension(3)->setRowHeight(19);
            $sheet->mergeCells('C1:H2')->setCellValue('C1', $this->summary['reporte']);
            $sheet->getStyle('C1')->getFont()->setSize(15)->setBold(true);
            $sheet->getStyle('C1')->getAlignment()->setHorizontal('right')->setWrapText(true);
            $sheet->mergeCells('C3:H3')->setCellValue('C3', 'Generado: ' . $this->generadoEn . ' · Moneda: MXN');
            $sheet->getStyle('C3')->getAlignment()->setHorizontal('right');
            $sheet->getStyle('C3')->getFont()->setSize(9)->getColor()->setRGB('69717A');
            $sheet->getStyle('A3:H3')->getBorders()->getBottom()->setBorderStyle('thin')->getColor()->setRGB('B18B1E');
            $sheet->getRowDimension(4)->setRowHeight(4);

            // Acentos semánticos discretos: fondos casi blancos y texto de alto contraste.
            $indicadores = [
                ['A', 'A', 'Conceptos', 'total_articulos', 'F6F4EE', '80672C'],
                ['B', 'B', 'Artículos', 'tipo_articulo', 'F1F4F7', '50667B'],
                ['C', 'D', 'Servicios', 'tipo_servicio', 'F4F2F6', '6D617B'],
                ['E', 'F', 'Bajo mínimo', 'bajo_minimo', 'FAF1F1', '89565B'],
                ['G', 'H', 'Sobre máximo', 'sobre_maximo', 'FAF6EC', '826B42'],
            ];
            foreach ($indicadores as $indicador) {
                list($a, $b, $titulo, $key, $fondo, $color) = $indicador;
                if ($a !== $b) {
                    $sheet->mergeCells("{$a}5:{$b}5");
                    $sheet->mergeCells("{$a}6:{$b}6");
                }
                $sheet->setCellValue("{$a}5", $titulo);
                $sheet->setCellValue("{$a}6", $this->summary[$key] ?? 0);
                $sheet->getStyle("{$a}5:{$b}6")->getFill()->setFillType('solid')->getStartColor()->setRGB($fondo);
                $sheet->getStyle("{$a}5:{$b}6")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("{$a}5:{$b}6")->getFont()->setBold(true)->getColor()->setRGB($color);
                $sheet->getStyle("{$a}5")->getFont()->setSize(9);
                $sheet->getStyle("{$a}6")->getFont()->setSize(14);
            }
            // Espaciado compacto antes del detalle, sin alterar los datos ni sus filas.
            $sheet->getRowDimension(5)->setRowHeight(18);
            $sheet->getRowDimension(6)->setRowHeight(23);
            $sheet->getRowDimension(7)->setRowHeight(4);
            $sheet->mergeCells('A8:F8')->setCellValue('A8', 'Valor a precio de venta del inventario listado (MXN)');
            $sheet->mergeCells('G8:H8')->setCellValue('G8', $this->summary['costo_total_inventario'] ?? 0);
            $sheet->getStyle('A8:H8')->getFont()->setBold(true)->getColor()->setRGB('967316');
            $sheet->getStyle('G8')->getFont()->setSize(13);
            $sheet->getStyle('G8')->getNumberFormat()->setFormatCode($moneda);
            $sheet->getStyle('G8')->getAlignment()->setHorizontal('right');
            $sheet->getRowDimension(8)->setRowHeight(23);
            $sheet->mergeCells('A9:H9')->setCellValue('A9', 'Valoración: existencia × precio de venta. Servicios excluidos del valor total.');
            $sheet->getRowDimension(9)->setRowHeight(18);
            $sheet->getStyle('A9')->getFont()->setSize(9)->getColor()->setRGB('69717A');

            $sheet->getStyle('A10:H10')->applyFromArray([
                'font' => ['name' => 'Arial', 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '343A40']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]);
            $sheet->getRowDimension(10)->setRowHeight(28);
            $sheet->getStyle('A10:H10')->getAlignment()->setHorizontal('center')->setVertical('center');

            if ($fin >= $inicio) {
                // Centra todos los datos listados; mantiene el formato numérico y monetario.
                $sheet->getStyle("A{$inicio}:H{$fin}")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("G{$inicio}:H{$fin}")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("G{$inicio}:H{$fin}")->getNumberFormat()->setFormatCode($moneda);
                $sheet->getStyle("C{$inicio}:E{$fin}")->getNumberFormat()->setFormatCode('#,##0.##');
                $sheet->getStyle("B{$inicio}:B{$fin}")->getAlignment()->setWrapText(true)->setHorizontal('center');
                $sheet->getStyle("F{$inicio}:F{$fin}")->getAlignment()->setHorizontal('center');
                $row = $inicio;
                foreach ($this->items as $item) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                    if (($row - $inicio) % 2 === 1) {
                        $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType('solid')->getStartColor()->setRGB('F3F4F5');
                    }
                    $estado = $this->estado($item);
                    // Sin fondos de alerta: el texto identifica el estado con acentos discretos.
                    $colores = ['Bajo mínimo' => '89565B', 'Sobre máximo' => '826B42', 'En rango' => '4D705C', 'Servicio' => '6D617B'];
                    if (isset($colores[$estado])) {
                        $sheet->getStyle("F{$row}")->getFont()->setBold(true)->getColor()->setRGB($colores[$estado]);
                    }
                    $row++;
                }
                $sheet->setAutoFilter("A10:H{$fin}");
            } else {
                $sheet->mergeCells("A{$inicio}:H{$inicio}")->setCellValue("A{$inicio}", 'No hay conceptos para el filtro seleccionado.');
            }

            // Protección de lectura: celdas bloqueadas; se permite seleccionar/copiar y usar filtros existentes.
            // La clave se genera por archivo y no se conserva. No equivale a cifrado del documento.
            $proteccion = $sheet->getProtection();
            $proteccion->setPassword(bin2hex(random_bytes(12)));
            $proteccion->setSheet(true);
            $proteccion->setObjects(true); // Impide mover, redimensionar o eliminar el logotipo.
            $proteccion->setAutoFilter(false);
            $proteccion->setSelectLockedCells(false);
            $proteccion->setSelectUnlockedCells(false);
            $sheet->getParent()->getSecurity()->setLockStructure(true);

            // Impresión: una página de ancho, tantas de alto como requiera el detalle.

            $sheet->freezePane('C11');
            $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setPaperSize(PageSetup::PAPERSIZE_A4)
                ->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(10, 10)->setPrintArea("A1:H{$ultimaFila}");
            $sheet->getPageMargins()->setTop(0.3)->setBottom(0.4)->setLeft(0.25)->setRight(0.25);
            $sheet->getHeaderFooter()->setOddFooter('&L&"Arial,Regular"&9Aeternus Funerales · Control de inventario&R&"Arial,Regular"&9Página &P de &N');
        }];
    }
}
