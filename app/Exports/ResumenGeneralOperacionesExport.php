<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Excel general de las operaciones; agrega las hojas de detalle cuando se solicita completo.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Resumen de cinco filas; detalle de cuatro hojas únicamente bajo demanda. */
class ResumenGeneralOperacionesExport implements WithMultipleSheets
{
    private $datos;
    private $completo;
    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado); $completo (solicita
     * desgloses completos donde el reporte los permite).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(array $datos, $completo = false) { $this->datos = $datos; $this->completo = $completo; }
    /**
     * Detalle del método: Organiza las pestañas del archivo a partir de las secciones de
     * ResumenGeneralOperacionesExport.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
     * contexto lo habilita.
     */
    public function sheets(): array
    {
        $d = $this->datos;
        $filas = [];
        foreach ($d['tipos'] as $f) $filas[] = [$f['nombre'], (int) $f['cantidad'], (float) $f['ventas'], (float) $f['porCobrar'], (float) $f['vencido']];
        $resumen = ['nombre' => 'Resumen general', 'nota' => $d['alcance_vencido'] ?? 'Ventas y por cobrar del mes consultado. Vencido Acumulado Histórico a la fecha de consulta.',
            'columnas' => ['Tipo de operación', 'Cantidad del mes', 'Ventas del mes', 'Por cobrar del mes', 'Vencido Acumulado Histórico'],
            'filas' => $filas, 'moneda' => [2, 3, 4], 'sumar' => [1]];
        $hojas = [new VentasCementerioHoja($d, $resumen)];
        if (!$this->completo) return $hojas;
        $grupos = ['ventas' => [], 'por_cobrar' => [], 'vencidos' => []];
        $fecha = function ($v) { if (!$v) return 'No disponible'; $f = \Carbon\Carbon::parse($v); return $f->format('d').'/'.ucfirst(rtrim($f->locale('es')->translatedFormat('M'), '.')).'/'.$f->format('Y'); };
        foreach ($d['detalles'] as $detalle) {
            $tipo = (int) $detalle['tipo_operacion'];
            foreach ($grupos as $clave => $_) {
                foreach ($detalle[$clave] as $f) {
                    $descripcion = [1 => $f['ubicacion_texto'] ?? '', 2 => implode(' · ', array_filter([$f['descripcion_cuota'] ?? '', $f['ubicacion_texto'] ?? ''])),
                        3 => $f['fallecido'] ?? '', 4 => $f['plan'] ?? '', 5 => 'No aplica'][$tipo];
                    $fila = [$detalle['modulo'], (string) $f['numero_venta'], $descripcion ?: 'No disponible', $tipo === 4 ? $f['modalidad'] : 'No aplica',
                        $fecha($f['fecha_operacion']), $f['cliente'] ?: 'No disponible',
                        implode(' / ', array_unique(array_filter([$f['telefono'] ?? '', $f['celular'] ?? '']))), mb_strtolower(trim($f['email'] ?? ''), 'UTF-8')];
                    if ($clave === 'ventas') {
                        $fila = array_merge($fila, [(float) $f['total'], (float) $f['por_cobrar'], (float) $f['vencido']]);
                    } else {
                        $fila = array_merge($fila, [$f['referencia_pago'] ?? '', $fecha($f['fecha_programada']),
                            (float) $f['monto_programado'], (float) $f['capital_cubierto'], (float) $f['pendiente'],
                            $clave === 'vencidos' || ($f['fecha_programada'] && substr($f['fecha_programada'], 0, 10) < substr($d['corte'], 0, 10)) ? 'Vencido' : 'Por vencer']);
                    }
                    $grupos[$clave][] = ['fecha' => $clave === 'ventas' ? $f['fecha_operacion'] : $f['fecha_programada'],
                        'id' => $clave === 'ventas' ? $f['operacion_id'] : $f['pago_programado_id'], 'fila' => $fila];
                }
            }
        }
        $base = ['Tipo de operación', 'Núm. venta / servicio', 'Detalle', 'Modalidad', 'Fecha venta / contrato', 'Cliente', 'Teléfono / celular', 'Email'];
        foreach ($grupos as $clave => $filas) {
            usort($filas, function ($a, $b) { $c = strcmp($b['fecha'] ?? '', $a['fecha'] ?? ''); return $c ?: ($b['id'] <=> $a['id']); });
            $venta = $clave === 'ventas';
            $sec = ['nombre' => ['ventas' => 'Ventas del mes', 'por_cobrar' => 'Por cobrar del mes', 'vencidos' => 'Vencidos acumulados'][$clave],
                'nota' => 'Datos actualizados al generar este Excel. Registros del más reciente al más antiguo.',
                'columnas' => array_merge($base, $venta ? ['Total venta', 'Saldo de la venta', 'Vencido de la venta']
                    : ['Referencia', 'Fecha programada', 'Programado', 'Aplicado a capital', 'Pendiente', 'Estado']),
                'filas' => array_column($filas, 'fila'), 'moneda' => $venta ? [8, 9, 10] : [10, 11, 12]];
            if (!$venta) $sec['estado'] = 13;
            $hojas[] = new VentasCementerioHoja($d, $sec);
        }
        return $hojas;
    }
}
