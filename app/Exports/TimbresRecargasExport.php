<?php
namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Mapeo único para HTML, PDF y Excel; no consulta la base de datos. */
class TimbresRecargasExport implements WithMultipleSheets
{
    private $datos;
    public function __construct(array $datos) { $this->datos = $datos; }
    public static function fecha($valor)
    {
        if (!$valor) return 'No disponible';
        $f = Carbon::parse($valor, 'America/Mazatlan');
        return $f->format('d') . '/' . ucfirst(rtrim($f->locale('es')->translatedFormat('M'), '.')) . '/' . $f->format('Y H:i:s');
    }
    public static function secciones(array $d): array
    {
        $filas = [];
        foreach ($d['recargas'] as $r) {
            $filas[] = ['Recarga ' . $r['id'], self::fecha($r['fecha_compra']), self::fecha($r['fecha_activacion']),
                $r['recargados'], $r['consumidos'], $r['ajustes'], $r['disponibles'],
                self::fecha($r['primer_consumo']), self::fecha($r['ultimo_consumo']), $r['estado']];
        }
        $sec = [['nombre' => 'Recargas', 'columnas' => ['Paquete', 'Fecha de compra', 'Activación', 'Recargados', 'Consumidos', 'Ajustes', 'Disponibles', 'Primer consumo', 'Último consumo', 'Estado'],
            'filas' => $filas, 'sumar' => [3,4,5,6], 'lotes' => array_column($d['recargas'], 'id')]];
        if ($d['recarga_id'] !== null || !empty($d['completo'])) {
            $historial = [];
            foreach ($d['consumos'] as $c) {
                $historial[] = ['Recarga ' . $c['recarga_id'], (string)$c['cfdi_id'], $c['uuid'] ?: 'No disponible',
                    self::fecha($c['fecha_timbrado']), $c['cliente'] ?: ($c['nombre_receptor'] ?: 'No disponible'),
                    trim(($c['tipo_clave'] ?? '') . ' · ' . ($c['tipo_cfdi'] ?? ''), ' ·'),
                    $c['usuario'] ?: 'No disponible', self::fecha($c['fecha_cancelacion']),
                    $c['asignacion_estimada'] ? 'Estimada' : 'Registrada'];
            }
            $sec[] = ['nombre' => 'CFDI asociados', 'columnas' => ['Paquete', 'Núm. CFDI', 'UUID', 'Timbrado', 'Cliente', 'Tipo de CFDI', 'Usuario que timbró', 'Fecha de cancelación', 'Asignación'],
                'filas' => $historial, 'sumar' => []];
        }
        return $sec;
    }
    public function sheets(): array
    {
        // El reporte individual conserva su resumen y el historial del lote abierto.
        if (empty($this->datos['completo']) || $this->datos['recarga_id'] !== null) {
            return array_map(function ($sec) { return new TimbresRecargasHoja($this->datos, $sec); }, self::secciones($this->datos));
        }

        // Excel completo: resumen global y una hoja por recarga, incluso si no tiene consumos.
        $resumen = $this->datos;
        $resumen['completo'] = false;
        $resumen['consumos'] = [];
        $hojas = [new TimbresRecargasHoja($resumen, self::secciones($resumen)[0])];
        $porRecarga = [];
        foreach ($this->datos['consumos'] as $consumo) {
            $porRecarga[(string)$consumo['recarga_id']][] = $consumo;
        }
        foreach ($this->datos['recargas'] as $recarga) {
            $datos = $this->datos;
            $datos['recarga_id'] = $recarga['id'];
            $datos['recargas'] = [$recarga];
            $datos['consumos'] = $porRecarga[(string)$recarga['id']] ?? [];
            $datos['titulo'] = 'Historial de Timbres';
            $seccion = self::secciones($datos)[1];
            $seccion['nombre'] = 'Recarga ' . $recarga['id'];
            // Totales del encabezado corresponden solo al lote de esta hoja.
            $hojas[] = new TimbresRecargasHoja($datos, $seccion);
        }
        return $hojas;
    }
}
