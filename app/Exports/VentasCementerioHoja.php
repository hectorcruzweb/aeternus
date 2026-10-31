<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Motor visual de hojas Excel compartido: encabezado, logo, formatos numéricos, alineación y estilos por
 * sección.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

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
    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado); $seccion (definición de
     * títulos, filas y formatos de la hoja).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(array $datos, array $seccion) { $this->datos = $datos; $this->seccion = $seccion; }
    /**
     * Detalle del método: Obtiene el nombre de la pestaña de Excel desde la sección del reporte.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Texto que Maatwebsite Excel utiliza como título de hoja.
     */
    public function title(): string { return $this->seccion['nombre']; }
    /**
     * Detalle del método: Reserva las filas superiores para el encabezado corporativo e indica dónde
     * comienza la tabla.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Coordenada inicial de la tabla en el XLSX; cambiarla exige revisar rangos en
     * registerEvents.
     */
    public function startCell(): string { return 'A10'; }
    /**
     * Detalle del método: Define los títulos de columna en el mismo orden que los valores de cada fila.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de encabezados para la hoja; sincronizarlo con array y los formatos
     * numéricos.
     */
    public function headings(): array { return $this->seccion['columnas']; }
    /**
     * Detalle del método: Entrega o transforma las filas del reporte al orden de columnas que se
     * exportará.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Matriz de valores para Excel; los importes deben seguir siendo numéricos.
     */
    public function array(): array { return $this->seccion['filas']; }
    // Nombres, referencias y teléfonos son texto literal; no se ejecutan como fórmulas.
    /**
     * Detalle del método: Escribe identificadores/referencias como texto cuando corresponde para conservar
     * ceros iniciales y evitar interpretación de fórmulas.
     * Entradas: $cell (celda Excel a enlazar); $value (valor que se escribirá en esa celda).
     * Salida / efectos: Resultado del enlace de valor a la celda, delegando al binder base los demás
     * tipos.
     */
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value)) { $cell->setValueExplicit($value, DataType::TYPE_STRING); return true; }
        return parent::bindValue($cell, $value);
    }
    /**
     * Detalle del método: Configura el logo de la empresa como dibujo de la hoja Excel y su
     * posición/tamaño.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Dibujo o colección que agrega el exportador; revisar protección de objetos para
     * evitar moverlo.
     */
    public function drawings()
    {
        $ruta = public_path(env('LOGOJPG'));
        if (!is_file($ruta)) return [];
        $logo = new Drawing(); $logo->setPath($ruta); $logo->setHeight(52);
        $logo->setCoordinates('A1'); $logo->setOffsetX(5); $logo->setOffsetY(5);
        return $logo;
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
