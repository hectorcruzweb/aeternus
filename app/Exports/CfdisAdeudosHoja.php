<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Da formato a las hojas de facturación y presenta agrupados los datos complementarios de las facturas.
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

/** Estilo corporativo Arial, libro protegido y cadenas tratadas como texto literal. */
class CfdisAdeudosHoja extends DefaultValueBinder implements FromArray, WithHeadings, WithTitle, WithCustomStartCell, WithEvents, WithDrawings, WithStrictNullComparison, WithCustomValueBinder
{
    private $datos; private $seccion;
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
    public function startCell(): string { return 'A9'; }
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
        $logo = new Drawing(); $logo->setPath($ruta); $logo->setHeight(50);
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
            $metodosTexto=!empty($d['modo_mes']) ? ' | PUE: '.number_format(array_sum(array_column($d['resumen'],'cantidad_pue'))).' | PPD: '.number_format(array_sum(array_column($d['resumen'],'cantidad_ppd'))) : '';
            foreach ([5 => $d['titulo'].' · '.$sec['nombre'], 6 => (!empty($d['modo_mes']) ? 'Periodo facturado: ' : 'Acumulado hasta ').$d['periodo'].' | Datos consultados: '.$d['fecha'],
                7 => number_format(array_sum(array_column($d['resumen'],'publico_general'))).' de '.number_format($d['cantidad']).(!empty($d['modo_mes']) ? ' CFDI facturados son de público en general. Otros receptores: ' : ' CFDI por pagar son de público en general. Otros receptores: ').number_format($d['cantidad']-array_sum(array_column($d['resumen'],'publico_general'))).'.'.$metodosTexto,
                8 => ($sec['nota'] ?? 'Incluye facturas de meses y años anteriores. Los importes corresponden al saldo actual.').' PUE: Pago en una sola exhibición. PPD: Pago en parcialidades o diferido.'] as $r=>$texto) {
                $s->mergeCells('A'.$r.':'.$last.$r)->setCellValueExplicit('A'.$r, $texto, DataType::TYPE_STRING);
            }
            $s->getStyle('A5')->getFont()->setSize(15)->setBold(true)->getColor()->setRGB('967316');
            $s->getStyle('A7:'.$last.'7')->getFill()->setFillType('solid')->getStartColor()->setRGB('F6F4EE');
            $s->getStyle('A7')->getFont()->setBold(true);
            $s->getStyle('A8')->getFont()->setSize(9)->getColor()->setRGB('70757B');
            $s->getStyle('A5:'.$last.'8')->getAlignment()->setWrapText(true)->setVertical('center');
            foreach ([5=>26,6=>19,7=>25,8=>40,9=>32] as $r=>$h) $s->getRowDimension($r)->setRowHeight($h);
            if(!empty($d['modo_mes']))$s->getRowDimension(7)->setRowHeight(36);
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
            foreach ($sec['moneda'] as $i) {
                $col = Coordinate::stringFromColumnIndex($i+1);
                $s->getStyle($col.'10:'.$col.$total)->getNumberFormat()->setFormatCode('"$" #,##0.00;[Red]-"$" #,##0.00;"$" 0.00');
            }
            if (isset($sec['tipos_fila'])) {
                // Colores y sangría distinguen factura/pagos sin permitir ordenar y separar bloques.
                foreach ($sec['tipos_fila'] as $i=>$tipo) {
                    $fila=10+$i;$rango='A'.$fila.':'.$last.$fila;
                    if ($tipo==='factura') {
                        $s->getStyle($rango)->getFill()->setFillType('solid')->getStartColor()->setRGB('F3F4F5');
                        $s->getStyle($rango)->getFont()->setBold(false);
                        $s->getStyle($rango)->getBorders()->getTop()->setBorderStyle('thin')->getColor()->setRGB('DDDDDD');
                        $s->getRowDimension($fila)->setRowHeight(30);
                        if ((float)($sec['filas'][$i][9] ?? 0)>0) {
                            $s->getStyle($rango)->getFill()->setFillType('solid')->getStartColor()->setRGB('FFF8ED');
                            $s->getStyle('A'.$fila)->getBorders()->getLeft()->setBorderStyle('medium')->getColor()->setRGB('B98236');
                            $s->getStyle('A'.$fila)->getFont()->setBold(true);
                            $s->getStyle('J'.$fila)->getFont()->setBold(true)->getColor()->setRGB('A53C3C');
                        }

                    } elseif ($tipo==='pago') {
                        // A:H contiene el comprobante; I es el importe bajo Total pagado.
                        $s->mergeCells('A'.$fila.':H'.$fila);
                        $s->getStyle($rango)->getFont()->setSize(9)->getColor()->setRGB('66736B');
                        $s->getStyle($rango)->getFill()->setFillType('solid')->getStartColor()->setRGB('F7FAF8');
                        $s->getStyle('A'.$fila)->getAlignment()->setIndent(1)->setHorizontal('left');
                        $s->getRowDimension($fila)->setRowHeight(30);
                    } elseif (in_array($tipo,['sin_pagos','operacion','grupo_moneda'],true)) {
                        $s->mergeCells($rango);
                        $s->getStyle($rango)->getFont()->setSize(9)->getColor()->setRGB('70757B');
                        $s->getStyle('A'.$fila)->getAlignment()->setHorizontal('left')->setIndent(1);
                        $s->getRowDimension($fila)->setRowHeight(24);
                        if($tipo==='grupo_moneda')$s->getStyle($rango)->getFont()->setBold(true);
                    } elseif ($tipo==='separador') {
                        $s->getStyle($rango)->getFill()->setFillType('solid')->getStartColor()->setRGB('FFFFFF');
                        $s->getRowDimension($fila)->setRowHeight(4);
                    }
                }
            } else {
                $s->setAutoFilter('A9:'.$last.max(9,$fin));
            }
            $s->freezePane('C10'); $s->getSheetView()->setZoomScale(115);
            $s->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setPaperSize(PageSetup::PAPERSIZE_A4)
                ->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(1,9)->setPrintArea('A1:'.$last.$total);
            $s->getHeaderFooter()->setOddFooter('&L'.(parse_url(config('app.url'),PHP_URL_HOST) ?: '').' · Generado: '.TimbresRecargasExport::fecha(now('America/Mazatlan')).'&RPág. &P/&N');
            $s->getProtection()->setPassword(bin2hex(random_bytes(16)))->setSheet(true)->setObjects(true)->setAutoFilter(false);
            $s->getParent()->getSecurity()->setLockStructure(true);
        }];
    }
}
