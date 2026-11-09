<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Presentación del listado completo. Reutiliza Arial, logo, protección y binder literal
 * del motor existente; no ejecuta consultas ni modifica clientes. */
class ListadoClientesExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos) { $this->datos=$datos; }
    /** Hoja única con resumen, selección aplicada y datos de contacto separados para filtrar. */
    public function sheets(): array
    {
        $d=$this->datos; $r=$d['resumen'];
        $d['titulo']='Listado de clientes';
        $d['encabezadoPeriodo']='Consulta: '.$d['fecha'];
        $d['resumenTexto']='Clientes: '.$r['total'].' | Con celular: '.$r['celular'].' | Con correo: '.$r['correo'];
        $d['avisoSaldos']=!empty($d['extras']) && collect($d['extras'])->contains('moneda',true) ? 'Importes de capital en MXN. No incluyen intereses.' : '';
        $extras=$d['extras'] ?? []; $moneda=[];
        $columnas=['Núm. cliente','Cliente','Celular','Teléfono','Email','Domicilio','Alta','Estado'];
        foreach($extras as $i=>$extra){$columnas[]=$extra['titulo'];if($extra['moneda'])$moneda[]=8+$i;}
        $filas=[];
        foreach($d['filas'] as $c) { $fila=[$c['id'],$c['nombre'],$c['celular'] ?: 'No disponible',
            $c['telefono'] ?: 'No disponible',$c['email'] ?: 'No disponible',$c['direccion'] ?: 'No disponible',
            empty($c['alta']) ? 'No disponible' : \Carbon\Carbon::parse($c['alta'])->locale('es')->translatedFormat('d/M/Y'),
            $c['status']===1 ? 'Activo' : 'Cancelado'];
            foreach($extras as $extra) $fila[]=$c[$extra['clave']] ?? 0;
            $filas[]=$fila;
        }
        return [new VentasCementerioHoja($d,['nombre'=>'Clientes','nota'=>'Filtros: '.implode(' · ',$d['filtros']),
            'columnas'=>$columnas,
            'filas'=>$filas,'moneda'=>$moneda,'sinTotal'=>true,'altoFila'=>42,
            'anchos'=>['A'=>15,'B'=>38,'C'=>22,'D'=>22,'E'=>35,'F'=>65,'G'=>19,'H'=>16]])];
    }
}
