<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Define resumen y detalle de gestiones realizadas del mes.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Resumen y detalle de gestión mensual; sin consultas ni importes monetarios. */
class SeguimientosRealizadosExport implements WithMultipleSheets
{
    private $datos;
    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(array $datos) { $this->datos = $datos; }
    /**
     * Detalle del método: Organiza las pestañas del archivo a partir de las secciones de
     * SeguimientosRealizadosExport.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
     * contexto lo habilita.
     */
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
