<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Organiza resumen por tipo, clientes y desglose de sus pagos vencidos.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Resumen y detalle agrupado: identidad del cliente una sola vez por bloque. */
class ClientesPagosVencidosExport implements WithMultipleSheets
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
     * ClientesPagosVencidosExport.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
     * contexto lo habilita.
     */
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
