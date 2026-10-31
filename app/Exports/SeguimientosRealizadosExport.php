<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Resumen y detalle de gestión mensual; sin consultas ni importes monetarios. */
class SeguimientosRealizadosExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos) { $this->datos = $datos; }
    public function sheets(): array
    {
        $d=$this->datos; $r=$d['resumen']; $filas=[];
        foreach($d['seguimientos'] as $f) $filas[] = [$f['fecha_texto'],$f['tipo_cliente'],trim($f['nombre_cliente'] ?? '') ?: 'No disponible',
            $f['contacto'] ?: 'No disponible',$f['email'] ?: 'No disponible',$f['motivo'],$f['medio'],$f['resultado'],
            trim($f['realizo'] ?? '') ?: 'No disponible',$f['origen'],$f['comentario'] ?: 'Sin comentarios'];
        return [new VentasCementerioHoja($d,[
            'nombre'=>'Resumen','nota'=>'Cantidad de gestiones realizadas; una misma persona puede tener varios seguimientos.',
            'columnas'=>['Indicador','Cantidad','Alcance'],
            'filas'=>[['Realizados del mes',$r['total'],'Seguimientos directos y programados atendidos'],
                ['A clientes registrados',$r['clientes'],'Gestiones a clientes registrados'],['Bajo cotización',$r['cotizaciones'],'Gestiones a prospectos bajo cotización'],
                ['Sin contacto logrado',$r['sin_contacto'],'Resultado: No fue posible contactar; incluido en el total'],
                ['Directos',$r['directos'],'Registrados como seguimiento directo'],['Programados atendidos',$r['programados'],'Programaciones que ya se atendieron']],
            'moneda'=>[],'sinTotal'=>true]),
            new VentasCementerioHoja($d,[
                'nombre'=>'Seguimientos realizados','nota'=>'Orden del más reciente al más antiguo. No incluye gestiones vinculadas a operaciones canceladas.',
                'columnas'=>['Fecha y hora','Tipo de cliente','Cliente / prospecto','Teléfono / celular','Email','Motivo','Medio','Resultado','Realizó','Origen','Comentarios'],
                'filas'=>$filas,'moneda'=>[],'sinTotal'=>true,'altoFila'=>68,
                'anchos'=>['A'=>25,'B'=>23,'C'=>34,'D'=>25,'E'=>32,'F'=>40,'G'=>24,'H'=>39,'I'=>30,'J'=>25,'K'=>70]])];
    }
}
