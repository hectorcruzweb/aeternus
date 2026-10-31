<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Resumen y detalle agrupado: identidad del cliente una sola vez por bloque. */
class ClientesPagosVencidosExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos){$this->datos=$datos;}
    public function sheets():array
    {
        $d=$this->datos;$filas=[];
        foreach($d['clientes'] as $c)$filas[]=[$c['cliente'],implode(' / ',array_unique(array_filter([$c['telefono'],$c['celular']]))),
            $c['email']?:'No disponible',$c['operaciones'],$c['cantidad'],$c['pendiente']];
        $hojas=(new PagosVenceHoyExport($d))->sheets();
        $hojas[1]=new ClientesPagosVencidosDetalleHoja($d);
        array_splice($hojas,1,0,[new VentasCementerioHoja($d,['nombre'=>'Clientes con adeudos',
            'nota'=>'Cada cliente aparece una vez. Pendiente de capital vencido; no incluye intereses.',
            'columnas'=>['Cliente','Teléfono / celular','Email','Operaciones','Pagos vencidos','Pendiente vencido'],
            'filas'=>$filas,'moneda'=>[5],'sumar'=>[4],'anchos'=>['A'=>42,'B'=>28,'C'=>38,'D'=>18,'E'=>20,'F'=>24]])]);
        return $hojas;
    }
}
