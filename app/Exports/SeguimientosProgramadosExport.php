<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Agenda pendiente: resumen y detalle ordenado por fecha programada ascendente. */
class SeguimientosProgramadosExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos){$this->datos=$datos;}
    public function sheets():array
    {
        $d=$this->datos;$r=$d['resumen'];$filas=[];
        foreach($d['seguimientos'] as $f)$filas[]=[$f['fecha_texto'],$f['tipo_cliente'],trim($f['nombre_cliente']??'')?:'No disponible',
            $f['contacto']?:'No disponible',$f['email']?:'No disponible',$f['motivo'],$f['medio'],
            trim($f['programo']??'')?:'No disponible',$f['comentario']?:'Sin comentarios',$f['estado']];
        return [new VentasCementerioHoja($d,['nombre'=>'Resumen','nota'=>'Hoy y los seis días siguientes. Cada registro corresponde a una gestión programada pendiente.',
            'columnas'=>['Indicador','Cantidad','Alcance'],'filas'=>[['Programados pendientes',$r['total'],'Próximos siete días incluyendo hoy'],['Para hoy',$r['hoy'],'Incluidos en el total'],['A clientes registrados',$r['clientes'],'Programados de clientes'],['Bajo cotización',$r['cotizaciones'],'Programados de prospectos']],
            'moneda'=>[],'sinTotal'=>true]),new VentasCementerioHoja($d,['nombre'=>'Agenda próximos 7 días','nota'=>'Orden de fecha menor a mayor; primero lo más próximo. Email programado o, si falta, email actual de contacto.',
            'columnas'=>['Fecha programada','Tipo de cliente','Cliente / prospecto','Teléfono / celular','Email de contacto','Motivo','Medio previsto','Programó','Comentarios','Estado'],
            'filas'=>$filas,'moneda'=>[],'sinTotal'=>true,'altoFila'=>68,
            'anchos'=>['A'=>25,'B'=>23,'C'=>34,'D'=>25,'E'=>32,'F'=>40,'G'=>25,'H'=>30,'I'=>70,'J'=>20]])];
    }
}
