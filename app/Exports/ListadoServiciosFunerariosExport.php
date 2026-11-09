<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
/** Exporta la misma captura del visor; no consulta ni recalcula importes.
 * Reutiliza Arial, encabezado, logo, protección y valores literales del motor. */
class ListadoServiciosFunerariosExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos){$this->datos=$datos;}
    public function sheets(): array
    {
        $d=$this->datos;$d['encabezadoPeriodo']='Consulta: '.$d['fecha'];
        $d['resumenTexto']='Servicios: '.$d['resumen']['total'].' | Con contrato: '.$d['resumen']['contratos'].' | Saldo vigente: $ '.number_format($d['resumen']['saldo'],2).' MXN';
        $d['avisoSaldos']='Las solicitudes sin contrato y los servicios cancelados no aportan saldo al resumen.';
        $filas=[];foreach($d['filas']as$f)$filas[]=[$f['servicio'],$f['fallecido'],$f['cliente'],$f['conContrato']?'Cliente del contrato':'Contratante temporal',$f['contacto']?:'Sin teléfono',$f['email']?:'Sin correo',$f['solicitud'],$f['contratacion'],$f['servicios'],$f['estado'],(float)$f['saldo']];
        return [new VentasCementerioHoja($d,['nombre'=>'Servicios','nota'=>'Filtros: '.implode(' · ',$d['filtros']),'columnas'=>['Núm. servicio','Fallecido','Cliente / contratante','Tipo de contacto','Teléfono / celular','Email','Solicitud','Contratación','Servicios y destino','Estado','Saldo MXN'],'filas'=>$filas,'moneda'=>[10],'sinTotal'=>true,'altoFila'=>48,'anchos'=>['A'=>15,'B'=>36,'C'=>36,'D'=>24,'E'=>26,'F'=>35,'G'=>20,'H'=>20,'I'=>65,'J'=>20,'K'=>22]])];
    }
}
