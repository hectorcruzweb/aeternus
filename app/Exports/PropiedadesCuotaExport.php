<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
/** Exporta la instantánea filtrada del visor. No ejecuta consultas ni recalcula
 * saldos; reutiliza Arial, logo, protección y formatos del motor de reportes. */
class PropiedadesCuotaExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos) { $this->datos = $datos; }
    public function sheets(): array
    {
        $d=$this->datos; $filas=collect($d['filas']);
        $d['encabezadoPeriodo']='Consulta: '.$d['fecha'];
        $d['resumenTexto']='Propiedades: '.$filas->count().' | Programado: $ '.number_format($filas->sum('programado'),2).' | Cubierto: $ '.number_format($filas->sum('cubierto'),2).' | Pendiente: $ '.number_format($filas->sum('pendiente'),2).' MXN';
        $d['avisoSaldos']='Capital sin intereses. Totales correspondientes al filtro seleccionado.';
        $rows=$filas->map(function($f){return [$f['venta'],$f['ubicacion'],$f['nombre'] ?: 'No disponible',$f['celular'] ?: 'No disponible',$f['telefono'] ?: 'No disponible',$f['email'] ?: 'No disponible',(float)$f['programado'],(float)$f['cubierto'],(float)$f['pendiente'],$f['pendiente']>0?'Con adeudo':'Pagada'];})->all();
        return [new VentasCementerioHoja($d,['nombre'=>'Propiedades','nota'=>'Filtro: '.$d['filtro'],'columnas'=>['Venta','Ubicación','Cliente','Celular','Teléfono','Email','Programado MXN','Cubierto MXN','Pendiente MXN','Estado'],'filas'=>$rows,'moneda'=>[6,7,8],'sinTotal'=>true,'altoFila'=>35,'anchos'=>['A'=>14,'B'=>35,'C'=>40,'D'=>22,'E'=>22,'F'=>35,'G'=>22,'H'=>22,'I'=>22,'J'=>18]])];
    }
}
