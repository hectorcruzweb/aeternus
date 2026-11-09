<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
/** Misma instantánea que PDF/visor. Arial, protección de celdas/logo y referencias
 * literales se heredan del motor compartido; no recalcula pagos ni suma recibido. */
class ListadoPagosExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos){$this->datos=$datos;}
    public function sheets(): array
    {
        $d=$this->datos;$d['encabezadoPeriodo']='Consulta: '.$d['fecha'];
        $d['resumenTexto']='Movimientos: '.$d['resumen']['total'].' | Activos: '.$d['resumen']['activos'].' | Cancelados: '.$d['resumen']['cancelados'];
        $d['avisoSaldos']='Importe de cada movimiento. Los descuentos no representan dinero recibido. Cancelados fuera del resumen de importes.';
        $filas=[];foreach($d['filas'] as $f)$filas[]=[$f['id'],$f['parent_pago_id']?:'', $f['fecha_pago_texto'],$f['cliente_texto'],$f['operaciones_texto'],$f['referencias_texto']?:'Sin referencia',$f['movimientos_pagos_texto'],(int)$f['status']===1?'Activo':'Cancelado',(float)$f['monto_pago']];
        $resumen=[];foreach($d['resumen']['tipos'] as $tipo)$resumen[]=[$tipo['nombre'],$tipo['cantidad'],(float)$tipo['importe']];
        return [new VentasCementerioHoja($d,['nombre'=>'Pagos','nota'=>'Filtros: '.implode(' · ',$d['filtros']),
            'columnas'=>['Núm. pago','Pago principal','Fecha','Cliente','Venta / servicio','Referencia','Movimiento','Estado','Importe MXN'],
            'filas'=>$filas,'moneda'=>[8],'sinTotal'=>true,'altoFila'=>42,
            'anchos'=>['A'=>14,'B'=>17,'C'=>19,'D'=>34,'E'=>40,'F'=>34,'G'=>29,'H'=>15,'I'=>20]]),
            new VentasCementerioHoja($d,['nombre'=>'Resumen','nota'=>'Solo movimientos activos. Filtros: '.implode(' · ',$d['filtros']),
            'columnas'=>['Tipo de movimiento','Cantidad','Importe MXN'],'filas'=>$resumen,'moneda'=>[2],'sinTotal'=>true,
            'anchos'=>['A'=>36,'B'=>18,'C'=>24]])];
    }
}
