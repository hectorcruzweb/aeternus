<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ListadoCfdiExport implements FromView, WithEvents
{
    private $datos;

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    /** Usa la misma tabla del visor y conserva los importes como números. */
    public function view(): View
    {
        return view('reportes.temporales_visor_excel.facturacion.listado_cfdi', $this->datos + ['excel' => true]);
    }

    /** Ajusta el ancho de impresión a una página y repite el encabezado. */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $evento) {
                $hoja = $evento->sheet->getDelegate();
                $hoja->setShowGridlines(false);
                // Incrusta el logo en el archivo para que Excel pueda mostrarlo sin acceder al servidor.
                $rutaLogo = $this->datos['logo'] ?? null;
                if (!$rutaLogo || !@getimagesize($rutaLogo)) {
                    $rutaLogo = public_path('images/logo/logo_png.png');
                }
                $hoja->getRowDimension(1)->setRowHeight(62);
                if (@getimagesize($rutaLogo)) {
                    $logo = new Drawing();
                    $logo->setName('Aeternus Funerales')->setPath($rutaLogo)->setHeight(52);
                    $logo->setCoordinates('A1')->setOffsetX(6)->setOffsetY(12)->setWorksheet($hoja);
                }

                // Bloquea la edición de las celdas y los cambios sobre el logotipo.
                $proteccion = $hoja->getProtection();
                $proteccion->setPassword(bin2hex(random_bytes(12)));
                $proteccion->setSheet(true);
                $proteccion->setObjects(true);
                $hoja->getParent()->getSecurity()->setLockStructure(true);


                $hoja->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
                foreach (['A'=>17, 'B'=>41, 'C'=>37, 'D'=>23, 'E'=>16, 'F'=>19, 'G'=>23, 'H'=>20] as $columna => $ancho) {
                    $hoja->getColumnDimension($columna)->setWidth($ancho);
                }
                $ultimaFila = $hoja->getHighestRow();
                $hoja->getStyle('A1:H'.$ultimaFila)->getAlignment()->setWrapText(true)->setVertical('center');
                $encabezado = 1;
                for ($fila = 1; $fila <= $ultimaFila; $fila++) {
                    if ($hoja->getCell('A'.$fila)->getValue() === 'Folio / método') {
                        $encabezado = $fila;
                        break;
                    }
                }
                $hoja->getStyle('A'.$encabezado.':D'.$encabezado)->getAlignment()->setHorizontal('left');
                $hoja->getStyle('E'.$encabezado.':F'.$ultimaFila)->getAlignment()->setHorizontal('center');
                $hoja->getStyle('G'.$encabezado.':H'.$ultimaFila)->getAlignment()->setHorizontal('right');
                $hoja->freezePane('A'.($encabezado + 1));
                $empresa = $this->datos['empresa'];
                $pie = NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null)
                    . ' · ' . ($empresa['telefono'] ?? '');
                $hoja->getHeaderFooter()->setOddFooter('&L'.str_replace('&', '&&', $pie).'&RPág. &P/&N');
                $hoja->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                    ->setPaperSize(PageSetup::PAPERSIZE_LETTER)->setFitToWidth(1)->setFitToHeight(0)
                    ->setRowsToRepeatAtTopByStartAndEnd($encabezado, $encabezado);
            },
        ];
    }
}
