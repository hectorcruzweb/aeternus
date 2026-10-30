<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/** Estilo corporativo Arial, libro protegido y cadenas tratadas como texto literal. */
class TimbresRecargasHoja extends DefaultValueBinder implements FromArray, WithHeadings, WithTitle, WithCustomStartCell, WithEvents, WithDrawings, WithStrictNullComparison, WithCustomValueBinder
{
    private $datos; private $seccion;
    public function __construct(array $datos, array $seccion) { $this->datos = $datos; $this->seccion = $seccion; }
    public function title(): string { return $this->seccion['nombre']; }
    public function startCell(): string { return 'A9'; }
    public function headings(): array { return $this->seccion['columnas']; }
    public function array(): array { return $this->seccion['filas']; }
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value)) { $cell->setValueExplicit($value, DataType::TYPE_STRING); return true; }
        return parent::bindValue($cell, $value);
    }
    public function drawings()
    {
        $ruta = public_path(env('LOGOJPG'));
        if (!is_file($ruta)) return [];
        $logo = new Drawing(); $logo->setPath($ruta); $logo->setHeight(50);
        $logo->setCoordinates('A1'); $logo->setOffsetX(5); $logo->setOffsetY(5);
        return $logo;
    }
    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $s = $event->sheet->getDelegate(); $d = $this->datos; $sec = $this->seccion;
            $last = Coordinate::stringFromColumnIndex(count($sec['columnas']));
            $fin = 9 + count($sec['filas']); $total = max(11, $fin + 1);
            $s->getStyle('A1:'.$last.$total)->getFont()->setName('Arial')->setSize(10)->getColor()->setRGB('343A40');
            $empresa = $d['empresa'];
            $s->mergeCells('C1:'.$last.'1')->setCellValueExplicit('C1', \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null), DataType::TYPE_STRING);
            $s->mergeCells('C2:'.$last.'2')->setCellValueExplicit('C2', implode(' · ', array_filter([$empresa['razon_social'] ?? '', $empresa['rfc'] ?? ''])), DataType::TYPE_STRING);
            $s->mergeCells('C3:'.$last.'3')->setCellValueExplicit('C3', implode(' · ', array_filter([
                DireccionReportes::formatear($empresa),
                $empresa['telefono'] ?? '', mb_strtolower(trim($empresa['email'] ?? ''), 'UTF-8')
            ])), DataType::TYPE_STRING);
            $vigencia=$d['fechaCaducidadFiel'] ?? null;
            $vigenciaTexto=$vigencia ? explode(' ',TimbresRecargasExport::fecha($vigencia))[0] : 'No disponible';
            $s->mergeCells('C4:'.$last.'4')->setCellValueExplicit('C4', 'Vigencia de la Firma Electrónica: '.$vigenciaTexto, DataType::TYPE_STRING);
            $s->getStyle('C4:'.$last.'4')->getFont()->setSize(9)->getColor()->setRGB('70757B');
            $s->getStyle('C4:'.$last.'4')->getAlignment()->setHorizontal('right');
            $s->getRowDimension(4)->setRowHeight(18);
            $comprados = array_sum(array_column($d['recargas'], 'recargados'));
            $consumidos = array_sum(array_column($d['recargas'], 'consumidos'));
            $disponibles = array_sum(array_column($d['recargas'], 'disponibles'));
            foreach ([5 => $d['titulo'].' · '.$sec['nombre'], 6 => 'Datos consultados: '.$d['fecha'],
                7 => 'Recargados: '.number_format($comprados).' | Consumidos: '.number_format($consumidos).' | Disponibles: '.number_format($disponibles),
                8 => 'Los timbres consumidos incluyen CFDI cancelados. Las asignaciones históricas son estimadas.'] as $r=>$texto) {
                $s->mergeCells('A'.$r.':'.$last.$r)->setCellValueExplicit('A'.$r, $texto, DataType::TYPE_STRING);
            }
            $s->getStyle('A5')->getFont()->setSize(15)->setBold(true)->getColor()->setRGB('967316');
            $s->getStyle('A7:'.$last.'7')->getFill()->setFillType('solid')->getStartColor()->setRGB('F6F4EE');
            $s->getStyle('A7')->getFont()->setBold(true);
            $s->getStyle('A8')->getFont()->setSize(9)->getColor()->setRGB('70757B');
            $s->getStyle('A5:'.$last.'8')->getAlignment()->setWrapText(true)->setVertical('center');
            foreach ([5=>26,6=>19,7=>25,8=>25,9=>32] as $r=>$h) $s->getRowDimension($r)->setRowHeight($h);
            $s->getStyle('A9:'.$last.'9')->getFill()->setFillType('solid')->getStartColor()->setRGB('343A40');
            $s->getStyle('A9:'.$last.'9')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $s->getStyle('A9:'.$last.$total)->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
            foreach ($sec['columnas'] as $i=>$nombre) {
                $col = Coordinate::stringFromColumnIndex($i+1);
                $s->getColumnDimension($col)->setWidth($nombre === 'UUID' ? 43 : (in_array($nombre, ['Cliente','Usuario que timbró']) ? 30 : 22));
            }
            for ($r=10;$r<=$fin;$r++) {
                $s->getRowDimension($r)->setRowHeight(30);
                if ($r%2===0) $s->getStyle('A'.$r.':'.$last.$r)->getFill()->setFillType('solid')->getStartColor()->setRGB('F3F4F5');
            }
            if ($sec['sumar']) {
                $s->setCellValue('A'.$total, 'TOTAL');
                foreach ($sec['sumar'] as $i) {
                    $s->setCellValue(Coordinate::stringFromColumnIndex($i+1).$total, array_sum(array_column($sec['filas'], $i)));
                    $s->getStyle(Coordinate::stringFromColumnIndex($i+1).'10:'.Coordinate::stringFromColumnIndex($i+1).$total)->getNumberFormat()->setFormatCode('#,##0;[Red]-#,##0;0');
                }
                $s->getStyle('A'.$total.':'.$last.$total)->getFont()->setBold(true);
            }
            $s->freezePane('C10'); $s->setAutoFilter('A9:'.$last.max(9,$fin)); $s->getSheetView()->setZoomScale(115);
            $s->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setPaperSize(PageSetup::PAPERSIZE_A4)
                ->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(1,9)->setPrintArea('A1:'.$last.$total);
            $s->getHeaderFooter()->setOddFooter('&L'.(parse_url(config('app.url'),PHP_URL_HOST) ?: '').' · Generado: '.TimbresRecargasExport::fecha(now('America/Mazatlan')).'&RPág. &P/&N');
            $s->getProtection()->setPassword(bin2hex(random_bytes(16)))->setSheet(true)->setObjects(true)->setAutoFilter(false);
            $s->getParent()->getSecurity()->setLockStructure(true);
        }];
    }
}
