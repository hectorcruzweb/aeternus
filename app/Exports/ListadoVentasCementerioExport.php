<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Excel del listado filtrado: misma instantánea y capital que visor/PDF.
 * Reutiliza el motor corporativo, Arial, logo, protección y valores de texto literales. */
class ListadoVentasCementerioExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos){$this->datos=$datos;}
    /** Detalle completo, sin paginación del listado; columnas de vencido según selección. */
    public function sheets(): array
    {
        $d=$this->datos;$r=$d['resumen'];$d['titulo']='Ventas en cementerio';
        $d['encabezadoPeriodo']='Consulta: '.$d['fecha'];
        $d['resumenTexto']='Ventas: '.$r['total'].' | Ventas vigentes: $ '.number_format($r['ventas'],2).' MXN | Por cobrar: $ '.number_format($r['pendiente'],2).' MXN';
        $d['avisoSaldos']='Capital sin intereses. Ventas canceladas fuera de los totales monetarios.';
        $columnas=['Núm. venta','Fecha','Titular','Contacto','Email','Ubicación','Solicitud','Convenio','Título','Estado','Total venta','Por cobrar'];$filas=[];$moneda=[10,11];
        if($d['mostrarVencidos']){$columnas[]='Saldo vencido';$columnas[]='Pagos vencidos';$moneda[]=12;}
        if(!empty($d['mostrarSepultados'])){$columnas[]='Personas sepultadas';$columnas[]='Nombres de personas sepultadas';}
        if(!empty($d['mostrarCuotas'])){$moneda[]=count($columnas)+1;$columnas[]='Cuotas vencidas';$columnas[]='Saldo cuotas vencidas';}
        foreach($d['filas'] as $v){
            $fila=[$v['venta'],$v['fecha'],$v['cliente'],$v['celular'],$v['email'] ?: 'No disponible',$v['ubicacion'],$v['solicitud'],$v['convenio'],$v['titulo'],$v['estado'],$v['total'],$v['pendiente']];
            if($d['mostrarVencidos']){$fila[]=$v['vencido'];$fila[]=$v['pagos_vencidos'];}
            if(!empty($d['mostrarSepultados'])){$fila[]=$v['cantidad_sepultados'] ?? 0;$fila[]=implode(' · ',$v['sepultados'] ?? []) ?: 'Sin personas sepultadas';}
            if(!empty($d['mostrarCuotas'])){$fila[]=$v['cuotas_vencidas'] ?? 0;$fila[]=$v['saldo_cuotas_vencidas'] ?? 0;}
            $filas[]=$fila;
        }
        return [new VentasCementerioHoja($d,['nombre'=>'Ventas filtradas','columnas'=>$columnas,'filas'=>$filas,'moneda'=>$moneda,'sinTotal'=>true,
            'nota'=>'Filtros: '.implode(' · ',$d['filtros']),'altoFila'=>38,
            'anchos'=>['A'=>14,'B'=>18,'C'=>36,'D'=>23,'E'=>32,'F'=>32,'G'=>18,'H'=>18,'I'=>18,'J'=>18,'K'=>22,'L'=>22]])];
    }
}
