<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Formato compartido de reportes tipos 1/2/3/4/5; nombre histórico por compatibilidad. Sin consultas aquí. */
class VentasCementerioExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos) { $this->datos = $datos; }

    public static function secciones(array $datos): array
    {
        $fecha = function ($valor) { return $valor ? (function ($f) { return $f->format('d') . '/' . ucfirst(rtrim($f->locale('es')->translatedFormat('M'), '.')) . '/' . $f->format('Y'); })(\Carbon\Carbon::parse($valor)) : 'No disponible'; };
        $contacto = function ($f) { return implode(' / ', array_unique(array_filter([$f['telefono'] ?? '', $f['celular'] ?? '']))); };
        $ventas = [];
        foreach ($datos['ventas'] ?? [] as $f) {
            $ventas[] = [(string) $f['numero_venta'], $f['ubicacion_texto'] ?? 'No disponible', $fecha($f['fecha_operacion']),
                $f['cliente'] ?: 'No disponible', $contacto($f), mb_strtolower(trim($f['email'] ?? ''), 'UTF-8'),
                (float) $f['total'], (float) $f['por_cobrar'], (float) $f['vencido']];
        }
        $secciones = [[
            'nombre' => 'Ventas del mes',
            'nota' => 'Operaciones vigentes vendidas en el mes. Saldo de la venta incluye todos sus pagos pendientes, incluso futuros; no es el indicador Por cobrar del mes. Vencido de estas ventas forma parte del acumulado.',
            'columnas' => ['Núm. venta', 'Ubicación', 'Fecha de venta', 'Cliente', 'Teléfono / celular', 'Email', 'Total venta', 'Saldo de la venta', 'Vencido de estas ventas'],
            'filas' => $ventas, 'moneda' => [6, 7, 8],
        ]];
        foreach (['por_cobrar' => 'Por cobrar del mes', 'vencidos' => 'Vencidos acumulados'] as $clave => $nombre) {
            $filas = [];
            foreach ($datos[$clave] ?? [] as $f) {
                $filas[] = [(string) $f['numero_venta'], $f['ubicacion_texto'] ?? 'No disponible', $fecha($f['fecha_operacion']),
                    $f['cliente'] ?: 'No disponible', $contacto($f), mb_strtolower(trim($f['email'] ?? ''), 'UTF-8'),
                    $f['referencia_pago'] ?? '', $fecha($f['fecha_programada']),
                    (float) $f['monto_programado'], (float) $f['capital_cubierto'], (float) $f['pendiente'],
                    !$f['fecha_programada'] ? 'Sin fecha' : (substr($f['fecha_programada'], 0, 10) < substr($datos['corte'], 0, 10) ? 'Vencido' : 'Por vencer')];
            }
            $secciones[] = [
                'nombre' => $nombre,
                'nota' => $clave === 'por_cobrar'
                    ? 'Pagos programados pendientes con fecha dentro del mes consultado, aunque la venta sea de meses anteriores. Cada fila es un pago programado, no una venta distinta.'
                    : 'Pagos programados pendientes con fecha anterior al día del corte, de todas las ventas vigentes, incluidas las antiguas. Los pagos que vencen hoy aún no están vencidos.',
                'columnas' => ['Núm. venta', 'Ubicación', 'Fecha venta', 'Cliente', 'Teléfono / celular', 'Email', 'Referencia', 'Fecha programada', 'Programado', 'Aplicado a capital', 'Pendiente', 'Estado'],
                'filas' => $filas, 'estado' => 11, 'moneda' => [8, 9, 10],
            ];
        }
        if ((int) ($datos['tipo_operacion'] ?? 1) === 2) {
            $secciones[0]['nombre'] = 'Cuotas del mes';
            $secciones[0]['nota'] = 'Cuotas del mes consultado. El saldo de cada cuota incluye todo lo pendiente de esa cuota.';
            $secciones[0]['columnas'][2] = 'Fecha de cuota';
            $secciones[0]['columnas'][8] = 'Vencido de estas cuotas';
            $secciones[0]['columnas'][7] = 'Saldo de cuota';
            $secciones[0]['columnas'][6] = 'Total cuota';
            $secciones[1]['nota'] = 'Pagos pendientes programados para el mes consultado, incluyendo cuotas de años anteriores.';
            $secciones[2]['nota'] = array_key_exists('cuota_id', $datos)
                ? 'Pagos vencidos de esta cuota a la fecha de consulta. No incluye los que vencen hoy.'
                : 'Pagos vencidos de todas las cuotas, incluidas las de años anteriores. No incluye los que vencen hoy.';
            // Mostrar la descripción de la cuota, sin añadir fechas ni alterar el tipo 1.
            foreach (['ventas', 'por_cobrar', 'vencidos'] as $i => $clave) {
                $secciones[$i]['columnas'][2] = 'Fecha de cuota';
                array_unshift($secciones[$i]['columnas'], 'Cuota');
                foreach ($secciones[$i]['filas'] as $n => &$fila) {
                    $origen = $datos[$clave][$n];
                    $descripcion = trim($origen['descripcion_cuota'] ?? '') ?: 'No disponible';
                    array_unshift($fila, $descripcion);
                }
                unset($fila);
                $secciones[$i]['moneda'] = array_map(function ($col) { return $col + 1; }, $secciones[$i]['moneda']);
                if (isset($secciones[$i]['estado'])) $secciones[$i]['estado']++;
            }
        }
        if ((int) ($datos['tipo_operacion'] ?? 1) === 3) {
            $secciones[0]['nombre'] = 'Servicios contratados del mes';
            $secciones[0]['nota'] = 'Servicios contratados en el mes consultado, con su importe y saldo pendiente.';
            $secciones[0]['columnas'][6] = 'Total del servicio';
            $secciones[0]['columnas'][7] = 'Saldo del servicio';
            $secciones[0]['columnas'][8] = 'Vencido de estos servicios';
            $secciones[1]['nota'] = 'Pagos pendientes programados para el mes consultado, incluyendo servicios contratados anteriormente.';
            $secciones[2]['nota'] = 'Pagos vencidos de todos los servicios contratados, incluidos los anteriores. No incluye los que vencen hoy.';
            foreach (['ventas', 'por_cobrar', 'vencidos'] as $i => $clave) {
                $secciones[$i]['columnas'][0] = 'Núm. servicio';
                $secciones[$i]['columnas'][1] = 'Fallecido';
                $secciones[$i]['columnas'][2] = 'Fecha de contrato';
                $secciones[$i]['columnas'][3] = 'Cliente / contratante';
                foreach ($secciones[$i]['filas'] as $n => &$fila) {
                    $fila[1] = trim($datos[$clave][$n]['fallecido'] ?? '') ?: 'No disponible';
                }
                unset($fila);
            }
        }
        if ((int) ($datos['tipo_operacion'] ?? 1) === 4) {
            $secciones[0]['nombre'] = 'Planes vendidos del mes';
            $secciones[0]['nota'] = 'Ventas de planes funerarios del mes consultado, con su importe y saldo pendiente.';
            $secciones[0]['columnas'][8] = 'Vencido de estas ventas';
            $secciones[1]['nota'] = 'Pagos pendientes programados para el mes consultado, incluyendo ventas de planes de meses anteriores.';
            $secciones[2]['nota'] = 'Pagos vencidos de todas las ventas de planes, incluidas las anteriores. No incluye los que vencen hoy.';
            foreach (['ventas', 'por_cobrar', 'vencidos'] as $i => $clave) {
                $secciones[$i]['columnas'][0] = 'Núm. venta de plan';
                $secciones[$i]['columnas'][1] = 'Plan funerario';
                array_splice($secciones[$i]['columnas'], 2, 0, ['Modalidad']);
                foreach ($secciones[$i]['filas'] as $n => &$fila) {
                    $origen = $datos[$clave][$n];
                    $fila[1] = $origen['plan'] ?? 'No disponible';
                    array_splice($fila, 2, 0, [$origen['modalidad'] ?? 'No disponible']);
                }
                unset($fila);
                $secciones[$i]['moneda'] = array_map(function ($col) { return $col + 1; }, $secciones[$i]['moneda']);
                if (isset($secciones[$i]['estado'])) $secciones[$i]['estado']++;
            }
        }
        if ((int) ($datos['tipo_operacion'] ?? 1) === 5) {
            $secciones[0]['nota'] = 'Ventas en general del mes consultado, con su importe y saldo pendiente.';
            $secciones[1]['nota'] = 'Pagos pendientes programados para el mes consultado, incluyendo ventas anteriores.';
            $secciones[2]['nota'] = 'Pagos vencidos de todas las ventas en general, incluidas las anteriores. No incluye los que vencen hoy.';
            foreach ($secciones as &$seccion) {
                // Ventas generales no tienen ubicación, plan ni fallecido.
                array_splice($seccion['columnas'], 1, 1);
                foreach ($seccion['filas'] as &$fila) array_splice($fila, 1, 1);
                unset($fila);
                $seccion['moneda'] = array_map(function ($col) { return $col - 1; }, $seccion['moneda']);
                if (isset($seccion['estado'])) $seccion['estado']--;
            }
            unset($seccion);
        }
        return $secciones;
    }

    public function sheets(): array
    {
        $d = $this->datos;
        $resumen = [
            'nombre' => 'Resumen', 'nota' => 'Por cobrar del mes y vencidos acumulados tienen alcances diferentes; no deben sumarse entre sí.',
            'columnas' => ['Indicador', 'Importe MXN', 'Alcance'],
            'filas' => [
                [$d['etiqueta_periodo'] ?? 'Ventas del mes', (float) $d['resumen']['ventas'], (int) ($d['tipo_operacion'] ?? 1) === 2 ? 'Total de cuotas del mes consultado.' : 'Total vendido de las operaciones del mes.'],
                ['Por cobrar del mes', (float) $d['resumen']['porCobrar'], 'Pendiente de pagos programados con fecha en el mes, incluidas ventas anteriores.'],
                ['Vencido Acumulado Histórico', (float) $d['resumen']['vencido'], 'Pendiente vencido de ventas históricas hasta el corte.'],
            ], 'moneda' => [1], 'sinTotal' => true,
        ];
        $hojas = [];
        foreach (array_merge([$resumen], self::secciones($d)) as $seccion) {
            $hojas[] = new VentasCementerioHoja($d, $seccion);
        }
        return $hojas;
    }
}
