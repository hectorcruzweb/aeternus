<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Define resumen y detalle de solicitudes funerarias sin contrato.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Resumen y dos grupos de solicitudes, sin consultas ni cambios de estado. */
class SolicitudesSinContratoExport implements WithMultipleSheets
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
     * SolicitudesSinContratoExport.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
     * contexto lo habilita.
     */
    public function sheets(): array
    {
        $d = $this->datos; $r = $d['resumen'];
        $hojas = [new VentasCementerioHoja($d, ['nombre' => 'Resumen',
            'nota' => 'Recientes: hoy y los seis días anteriores. Antiguas: anteriores a ese rango o sin fecha disponible.',
            'columnas' => ['Grupo', 'Solicitudes', 'Acción sugerida'],
            'filas' => [['Recientes', $r['recientes'], 'Dar seguimiento'], ['Antiguas', $r['antiguas'], 'Revisar si deben continuar activas'], ['Total acumulado', $r['total'], 'Recientes + antiguas']],
            'moneda' => [], 'sinTotal' => true])];
        foreach (['recientes' => 'Recientes', 'antiguas' => 'Antiguas por revisar'] as $clave => $nombre) {
            $filas = [];
            foreach ($d[$clave] as $f) $filas[] = [(string) $f['numero_servicio'], $f['fecha_solicitud_texto'],
                $f['antiguedad_texto'] ?? 'No disponible',
                trim($f['fallecido'] ?? '') ?: 'No disponible', trim($f['nombre_informante'] ?? '') ?: 'No disponible',
                trim($f['telefono_informante'] ?? '') ?: 'No disponible', trim($f['nombre_contratante_temp'] ?? '') ?: 'No disponible',
                trim($f['telefono_contratante_temp'] ?? '') ?: 'No disponible', $f['estado']];
            $hojas[] = new VentasCementerioHoja($d, ['nombre' => $nombre,
                'nota' => $clave === 'recientes' ? 'Solicitudes recientes para seguimiento. Ventana de '.$r['dias'].' días, incluyendo hoy.' : 'Solicitudes antiguas para revisión. No se cancelan automáticamente. Sin fecha válida se incluyen aquí.',
                'columnas' => ['Núm. servicio', 'Fecha de solicitud', 'Antigüedad', 'Fallecido', 'Informante', 'Tel. informante', 'Contratante provisional', 'Tel. contratante', 'Estado'],
                'filas' => $filas, 'moneda' => [], 'sinTotal' => true, 'altoFila' => 46,
                'anchos' => ['A'=>17,'B'=>25,'C'=>20,'D'=>32,'E'=>32,'F'=>24,'G'=>32,'H'=>24,'I'=>26]]);
        }
        return $hojas;
    }
}
