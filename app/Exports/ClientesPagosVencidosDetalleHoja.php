<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Hoja agrupada que muestra una sola cabecera por cliente y todos sus pagos/operaciones debajo.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;

/** Hoja agrupada: encabezado de cliente, sus pagos y subtotal; sin repetir contactos. */
class ClientesPagosVencidosDetalleHoja extends VentasCementerioHoja
{
    private $bloques=[];
    private $filaTotal;

    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(array $datos)
    {
        $columnas=['Tipo de operación','Núm. venta / servicio','Ubicación','Fallecido','Referencia','Concepto del pago','Fecha programada','Programado','Aplicado a capital','Pendiente','Estado'];
        $filas=[];
        foreach($datos['clientes'] as $c){
            $inicio=11+count($filas);
            $filas[]=array_pad(['Cliente: '.$c['cliente']],11,null);
            $contacto=implode(' / ',array_unique(array_filter([$c['telefono'],$c['celular']])))?:'No disponible';
            $filas[]=array_pad(['Teléfono / celular: '.$contacto.' | Email: '.($c['email']?:'No disponible').' | '.$c['operaciones'].' operaciones | '.$c['cantidad'].' pagos vencidos'],11,null);
            $filas[]=$columnas;
            foreach($c['pagos_indices'] as $indice){
                $p=$datos['pagos'][$indice];
                $filas[]=[$p['tipo_operacion'],(string)$p['numero_venta'],$p['ubicacion_texto'],
                    (int)$p['empresa_operaciones_id']===3?(trim($p['fallecido']??'')?:'No disponible'):'—',
                    $p['referencia_pago'],$p['concepto_pago'],$p['fecha_programada_texto'],
                    (float)$p['monto_programado'],(float)$p['capital_cubierto'],(float)$p['pendiente'],$p['estado_pago']];
            }
            $subtotal=11+count($filas);
            $filas[]=['TOTAL DEL CLIENTE',null,null,null,null,null,null,null,null,$c['pendiente'],null];
            $this->bloques[]=['inicio'=>$inicio,'subtotal'=>$subtotal];
        }
        if(!$filas)$filas[]=array_pad(['No hay clientes con pagos vencidos.'],11,null);
        $filas[]=['TOTAL GENERAL',null,null,null,null,null,null,$datos['resumen']['programado'],$datos['resumen']['aplicado'],$datos['resumen']['pendiente'],null];
        $this->filaTotal=10+count($filas);
        parent::__construct($datos,['nombre'=>'Pagos vencidos históricos','nota'=>'Un bloque por cliente con todos sus pagos vencidos. No incluye intereses.',
            'columnas'=>array_pad(['Desglose por cliente'],11,''),'filas'=>$filas,'moneda'=>[7,8,9],'sinTotal'=>true,'estado'=>10,
            'anchos'=>['A'=>30,'B'=>19,'C'=>30,'D'=>32,'E'=>29,'F'=>20,'G'=>22,'H'=>22,'I'=>22,'J'=>22,'K'=>18]]);
    }

    /**
     * Detalle del método: Registra el formato posterior a llenar la hoja: encabezado, rangos, importes,
     * estilos e impresión según esta clase.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Mapa de eventos de Maatwebsite Excel; el formato se aplica al ejecutarse
     * AfterSheet, no al declarar el método.
     */
    public function registerEvents():array
    {
        $eventos=parent::registerEvents();$base=$eventos[AfterSheet::class];
        $eventos[AfterSheet::class]=function(AfterSheet $evento)use($base){
            $base($evento);$s=$evento->sheet->getDelegate();
            $s->removeAutoFilter(); // Los bloques no son una tabla plana filtrable.
            foreach($this->bloques as $b){
                $i=$b['inicio'];$fin=$b['subtotal'];
                foreach([$i,$i+1] as $fila)$s->mergeCells('A'.$fila.':K'.$fila);
                $s->getStyle('A'.$i.':K'.($i+1))->getFill()->setFillType('solid')->getStartColor()->setRGB('F6F4EE');
                $s->getStyle('A'.$i.':K'.($i+1))->getAlignment()->setHorizontal('left');
                $s->getStyle('A'.$i)->getFont()->setBold(true)->setSize(11);
                $s->getRowDimension($i)->setRowHeight(27);$s->getRowDimension($i+1)->setRowHeight(29);
                $s->getStyle('A'.($i+2).':K'.($i+2))->getFill()->setFillType('solid')->getStartColor()->setRGB('343A40');
                $s->getStyle('A'.($i+2).':K'.($i+2))->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $s->mergeCells('A'.$fin.':I'.$fin);
                $s->getStyle('A'.$fin.':K'.$fin)->getFill()->setFillType('solid')->getStartColor()->setRGB('F6F4EE');
                $s->getStyle('A'.$fin.':K'.$fin)->getFont()->setBold(true);
            }
            $fin=$this->filaTotal;
            if(!$this->bloques)$s->mergeCells('A11:K11');
            $s->mergeCells('A'.$fin.':G'.$fin);
            $s->getStyle('A'.$fin.':K'.$fin)->getFont()->setBold(true);
            $s->getStyle('A'.$fin.':K'.$fin)->getFill()->setFillType('solid')->getStartColor()->setRGB('F6F4EE');
            $s->freezePane('A11');
        };
        return $eventos;
    }
}
