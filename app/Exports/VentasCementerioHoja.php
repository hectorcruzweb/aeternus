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

/** Arial, encabezado corporativo, ceros visibles y protección de celdas/logotipo. */
class VentasCementerioHoja extends DefaultValueBinder implements FromArray, WithHeadings, WithTitle, WithCustomStartCell, WithEvents, WithDrawings, WithStrictNullComparison, WithCustomValueBinder
{
    private $datos;
    private $seccion;
    public function __construct(array $datos, array $seccion) { $this->datos = $datos; $this->seccion = $seccion; }
    public function title(): string { return $this->seccion['nombre']; }
    public function startCell(): string { return 'A10'; }
    public function headings(): array { return $this->seccion['columnas']; }
    public function array(): array { return $this->seccion['filas']; }
    // Nombres, referencias y teléfonos son texto literal; no se ejecutan como fórmulas.
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value)) { $cell->setValueExplicit($value, DataType::TYPE_STRING); return true; }
        return parent::bindValue($cell, $value);
    }
    public function drawings()
    {
        $ruta = public_path(env('LOGOJPG'));
        if (!is_file($ruta)) return [];
        $logo = new Drawing(); $logo->setPath($ruta); $logo->setHeight(52);
        $logo->setCoordinates('A1'); $logo->setOffsetX(5); $logo->setOffsetY(5);
        return $logo;
    }
    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $s = $event->sheet->getDelegate(); $d = $this->datos; $sec = $this->seccion;
            $n = count($sec['columnas']); $last = Coordinate::stringFromColumnIndex($n);
            $fin = 10 + count($sec['filas']); $total = max(12, $fin + 1);
            $s->getStyle('A1:'.$last.$total)->getFont()->setName('Arial')->setSize(10)->getColor()->setRGB('343A40');
            $empresa = $d['empresa'] ?? [];
            $s->mergeCells('C1:'.$last.'1')->setCellValueExplicit('C1', \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null), DataType::TYPE_STRING);
            $s->mergeCells('C2:'.$last.'2')->setCellValueExplicit('C2', implode(' · ', array_filter([$empresa['razon_social'] ?? '', $empresa['rfc'] ?? ''])), DataType::TYPE_STRING);
            $s->mergeCells('C3:'.$last.'3')->setCellValueExplicit('C3', implode(' · ', array_filter([
                DireccionReportes::formatear($empresa), $empresa['telefono'] ?? '', mb_strtolower(trim($empresa['email'] ?? ''), 'UTF-8')
            ])), DataType::TYPE_STRING);
            foreach ([5 => $d['titulo'].' · '.$sec['nombre'], 6 => $d['encabezadoPeriodo'] ?? (ucfirst($d['periodo']).' | Corte: '.$d['fecha'].' | Moneda: MXN'),
                7 => $d['resumenTexto'] ?? (($d['etiqueta_periodo'] ?? 'Ventas del mes').': $ '.number_format($d['resumen']['ventas'],2).' MXN   |   Por cobrar del mes: $ '.number_format($d['resumen']['porCobrar'],2).' MXN   |   Vencido Acumulado Histórico: $ '.number_format($d['resumen']['vencido'],2).' MXN'),
                8 => $sec['nota'],
                9 => $d['avisoSaldos'] ?? 'Por cobrar del mes y Vencido Acumulado Histórico no incluyen intereses.'] as $fila => $texto) {
                $s->mergeCells('A'.$fila.':'.$last.$fila)->setCellValueExplicit('A'.$fila,$texto,DataType::TYPE_STRING);
            }
            $s->getStyle('A5')->getFont()->setSize(15)->setBold(true)->getColor()->setRGB('967316');
            $s->getStyle('A7:'.$last.'7')->getFill()->setFillType('solid')->getStartColor()->setRGB('F6F4EE');
            $s->getStyle('A8:'.$last.'8')->getFont()->setSize(9)->getColor()->setRGB('70757B');
            $s->getStyle('A8:'.$last.'8')->getAlignment()->setWrapText(true); $s->getRowDimension(8)->setRowHeight(30);
            // Aviso de negocio destacado: los saldos mostrados no incluyen intereses.
            $s->getStyle('A9:'.$last.'9')->getFont()->setSize(10)->setBold(true)->getColor()->setRGB('70530F');
            $s->getStyle('A9:'.$last.'9')->getFill()->setFillType('solid')->getStartColor()->setRGB('FFF4D6');
            $s->getStyle('A9:'.$last.'9')->getBorders()->getLeft()->setBorderStyle('medium')->getColor()->setRGB('B18B1E');
            $s->getStyle('A9:'.$last.'9')->getAlignment()->setWrapText(true)->setVertical('center'); $s->getRowDimension(9)->setRowHeight(28);
            $s->getStyle('A10:'.$last.'10')->getFill()->setFillType('solid')->getStartColor()->setRGB('343A40');
            $s->getStyle('A10:'.$last.'10')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            if ($sec['nombre'] === 'Vencidos acumulados') {
                // Bloque identificador de vencidos; las columnas y filas conservan su estilo.
                foreach ([5, 8] as $filaEncabezado) {
                    $rango = 'A'.$filaEncabezado.':'.$last.$filaEncabezado;
                    $s->getStyle($rango)->getFill()->setFillType('solid')->getStartColor()->setRGB('A53C3C');
                    $s->getStyle($rango)->getFont()->getColor()->setRGB('FFFFFF');
                }
            }
            $s->getStyle('A10:'.$last.$total)->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
            $s->getRowDimension(10)->setRowHeight(32);
            foreach ($sec['columnas'] as $i => $titulo) {
                $col = Coordinate::stringFromColumnIndex($i+1);
                $ancho = in_array($titulo,['Cliente','Cliente / contratante','Fallecido','Plan funerario','Email','Referencia','Alcance','Ubicación','Cuota']) ? 30 : (strpos($titulo,'Teléfono')!==false ? 23 : 16);
                $s->getColumnDimension($col)->setWidth($ancho);
            }
            foreach ($sec['anchos'] ?? [] as $col => $ancho) $s->getColumnDimension($col)->setWidth($ancho);
            if ($sec['nombre'] === 'Resumen') { $s->getColumnDimension('A')->setWidth(33); $s->getColumnDimension('C')->setWidth(100); }
            if ($sec['nombre'] === 'Resumen general') {
                foreach (['A'=>40,'B'=>18,'C'=>24,'D'=>24,'E'=>30] as $col=>$ancho) $s->getColumnDimension($col)->setWidth($ancho);
                $s->getStyle('A7:'.$last.'7')->getAlignment()->setWrapText(true);
                $s->getRowDimension(7)->setRowHeight(30);
            }
            for ($fila=11; $fila<=$fin; $fila++) {
                $s->getRowDimension($fila)->setRowHeight($sec['altoFila'] ?? 34);
                if ($fila%2===0) $s->getStyle('A'.$fila.':'.$last.$fila)->getFill()->setFillType('solid')->getStartColor()->setRGB('F3F4F5');
                if (isset($sec['estado']) && ($sec['filas'][$fila-11][$sec['estado']] ?? '') === 'Vencido') {
                    $s->getStyle(Coordinate::stringFromColumnIndex($sec['estado'] + 1).$fila)->getFont()->getColor()->setRGB('A53C3C');
                }
            }
            if (empty($sec['sinTotal'])) {
                $s->setCellValue('A'.$total,'TOTAL');
                foreach (array_unique(array_merge($sec['moneda'], $sec['sumar'] ?? [])) as $i) {
                    $s->setCellValue(Coordinate::stringFromColumnIndex($i+1).$total, array_sum(array_column($sec['filas'],$i)));
                }
                $s->getStyle('A'.$total.':'.$last.$total)->getFont()->setBold(true);
            }
            foreach ($sec['moneda'] as $i) {
                $col = Coordinate::stringFromColumnIndex($i+1);
                $s->getStyle($col.'11:'.$col.$total)->getNumberFormat()->setFormatCode('"$" #,##0.00;[Red]-"$" #,##0.00;"$" 0.00');
            }
            $s->freezePane('D11'); $s->setAutoFilter('A10:'.$last.max(10,$fin)); $s->getSheetView()->setZoomScale(115);
            $s->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setPaperSize(PageSetup::PAPERSIZE_A4)
                ->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(1,10)->setPrintArea('A1:'.$last.$total);
            $s->getHeaderFooter()->setOddFooter('&L'.(parse_url(config('app.url'),PHP_URL_HOST) ?: '').' · Generado: '.now('America/Mazatlan')->format('d') . '/' . ucfirst(rtrim(now('America/Mazatlan')->locale('es')->translatedFormat('M'), '.')) . '/' . now('America/Mazatlan')->format('Y H:i').'&RPág. &P/&N');
            $s->getProtection()->setPassword(bin2hex(random_bytes(16)))->setSheet(true)->setObjects(true)->setAutoFilter(false);
            $s->getParent()->getSecurity()->setLockStructure(true);
        }];
    }
}
