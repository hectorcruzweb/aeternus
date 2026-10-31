<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Define resumen y detalle de la agenda de seguimientos próximos.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Agenda pendiente: resumen y detalle ordenado por fecha programada ascendente. */
class SeguimientosProgramadosExport implements WithMultipleSheets
{
    private $datos;
    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(array $datos){$this->datos=$datos;}
    /**
     * Detalle del método: Organiza las pestañas del archivo a partir de las secciones de
     * SeguimientosProgramadosExport.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
     * contexto lo habilita.
     */
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
