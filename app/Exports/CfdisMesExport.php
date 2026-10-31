<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Organiza el Excel de facturado/cubierto del mes, usando el diseño compartido de facturación.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Conserva el formato compartido de facturas/operaciones/pagos, con resumen mensual. */
class CfdisMesExport implements WithMultipleSheets
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
     * Detalle del método: Define títulos, columnas y filas de cada sección del reporte desde sus datos ya
     * calculados.
     * Entradas: $d (datos agregados y filas preparados para el reporte).
     * Salida / efectos: Arreglo de secciones reutilizable por la exportación; no consulta la base de
     * datos.
     */
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
    /**
     * Detalle del método: Organiza las pestañas del archivo a partir de las secciones de CfdisMesExport.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
     * contexto lo habilita.
     */
    public function sheets(): array
    {
        return array_map(function($s){return new CfdisAdeudosHoja($this->datos,$s);},self::secciones($this->datos));
    }
}
