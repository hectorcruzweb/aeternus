<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Conserva el formato compartido de facturas/operaciones/pagos, con resumen mensual. */
class CfdisMesExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos){$this->datos=$datos;}
    public static function secciones(array $d): array
    {
        $sec=CfdisAdeudosExport::secciones($d);
        $sec[0]['columnas']=['CFDI facturados','Total facturado','Importe saldado','Egresos aplicados','Saldo pendiente'];
        $filas=[];$tipos=[];$varias=count($d['resumen'])>1;
        foreach($d['resumen'] as $r){
            if($varias){$filas[]=['Importes en '.$r['moneda'],null,null,null,null];$tipos[]='grupo_moneda';}
            $filas[]=[$r['cantidad'],$r['facturado'],$r['saldado'],$r['egresos'],$r['pendiente']];$tipos[]='resumen';
        }
        $sec[0]['filas']=$filas;$sec[0]['tipos_fila']=$tipos;
        $sec[0]['nota']='Saldado incluye pagos parciales y facturas PUE, hasta el importe de cada factura. Los egresos se muestran por separado.';
        return $sec;
    }
    public function sheets(): array
    {
        return array_map(function($s){return new CfdisAdeudosHoja($this->datos,$s);},self::secciones($this->datos));
    }
}
