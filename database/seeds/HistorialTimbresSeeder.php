<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HistorialTimbresSeeder extends Seeder
{
    /**
     * Carga inicial del historial; ejecutar explícitamente, no desde DatabaseSeeder.
     * php artisan db:seed --class=HistorialTimbresSeeder
     *
     * Criterio confirmado: UUID no vacío y fecha_timbrado válida, incluso cancelados.
     * FIFO interno: lote más antiguo activado con saldo a la fecha del timbrado.
     * No representa una asignación conocida del PAC: asignacion_estimada = true.
     * cfdis anteriores al primer lote quedan fuera y se informa su cantidad.
     * No modifica CFDI ni repone timbres por cancelación. No realiza llamadas al PAC.
     *
     * Repetible: valida registros existentes y omite consumos ya registrados.
     * Cualquier inconsistencia/insuficiencia revierte toda la transacción.
     * Es un inicializador histórico, no un mantenimiento para reasignar consumos:
     * si existen ajustes, otros lotes o consumos no estimados, se detiene.
     * Ejecutar con el timbrado detenido durante la carga inicial; el registro futuro
     * deberá compartir bloqueo de lotes para mantener consistencia concurrente.
     */
    private const RECARGAS = [
        ['referencia' => 'HIST-20220303-125528', 'cantidad' => 2000, 'activacion' => '2022-03-03 12:55:28'],
        ['referencia' => 'HIST-20240604-112947', 'cantidad' => 2000, 'activacion' => '2024-06-04 11:29:47'],
        ['referencia' => 'HIST-20260519-233248', 'cantidad' => 500, 'activacion' => '2026-05-19 23:32:48'],
    ];

    public function run()
    {
        if (!Schema::hasTable('movimientos_timbres')) {
            throw new RuntimeException('Ejecute primero la migración de movimientos_timbres.');
        }
        foreach (['cfdis_id', 'recarga_id', 'fecha_compra', 'fecha_activacion', 'asignacion_estimada'] as $campo) {
            if (!Schema::hasColumn('movimientos_timbres', $campo)) {
                throw new RuntimeException('La tabla no tiene la estructura vigente: falta ' . $campo . '. No borre datos; prepare una migración de modificación.');
            }
        }

        $corte = Carbon::now('America/Mazatlan')->format('Y-m-d H:i:s');
        $resumen = DB::transaction(function () use ($corte) {
            $ahora = Carbon::now('America/Mazatlan')->format('Y-m-d H:i:s');
            $lotes = [];
            foreach (self::RECARGAS as $datos) {
                $lote = DB::table('movimientos_timbres')
                    ->where('tipo', 'recarga')->where('referencia', $datos['referencia'])
                    ->lockForUpdate()->first();
                if (!$lote) {
                    $id = DB::table('movimientos_timbres')->insertGetId([
                        'tipo' => 'recarga', 'cantidad' => $datos['cantidad'],
                        'fecha_movimiento' => $datos['activacion'],
                        'fecha_compra' => null, 'fecha_activacion' => $datos['activacion'],
                        'referencia' => $datos['referencia'],
                        'nota' => 'Recarga histórica confirmada; fecha de compra no proporcionada.',
                        'cfdis_id' => null, 'recarga_id' => null, 'asignacion_estimada' => false,
                        'created_at' => $ahora, 'updated_at' => $ahora,
                    ]);
                    $lote = DB::table('movimientos_timbres')->where('id', $id)->lockForUpdate()->first();
                }
                if ((int)$lote->cantidad !== $datos['cantidad'] || $lote->fecha_activacion !== $datos['activacion']
                    || $lote->recarga_id !== null || $lote->cfdis_id !== null) {
                    throw new RuntimeException('La recarga ' . $datos['referencia'] . ' difiere de la configuración. No se sobrescribió.');
                }
                $lotes[] = ['id' => (int)$lote->id, 'referencia' => $datos['referencia'],
                    'activacion' => $datos['activacion'], 'cantidad' => $datos['cantidad'], 'disponibles' => $datos['cantidad']];
            }

            $ids = array_column($lotes, 'id');
            if (DB::table('movimientos_timbres')->whereNotIn('id', $ids)->where('tipo', '<>', 'consumo')->exists()) {
                throw new RuntimeException('Ya hay otros lotes o ajustes. Este seeder inicial no los reasigna.');
            }
            $existentes = DB::table('movimientos_timbres')->where('tipo', 'consumo')->lockForUpdate()->get()->keyBy('cfdis_id');
            foreach ($existentes as $consumo) {
                if (!$consumo->asignacion_estimada || (int)$consumo->cantidad !== -1 || !in_array((int)$consumo->recarga_id, $ids, true)) {
                    throw new RuntimeException('Hay consumos de otra procedencia; revise antes de reconstruir el historial.');
                }
            }

            $primera = self::RECARGAS[0]['activacion'];
            $base = DB::table('cfdis')->whereNotNull('uuid')->whereRaw("TRIM(uuid) <> ''")
                ->whereNotNull('fecha_timbrado');
            // Sin filtro de status: los CFDI cancelados también consumieron timbres.
            $anteriores = (clone $base)->where('fecha_timbrado', '>', '0000-00-00 00:00:00')
                ->where('fecha_timbrado', '<', $primera)->count();
            $cfdis = (clone $base)->where('fecha_timbrado', '>=', $primera)
                ->where('fecha_timbrado', '<=', $corte)
                ->select('id', 'uuid', 'fecha_timbrado')
                ->orderBy('fecha_timbrado')->orderBy('id')->get();

            $vistos = []; $revisados = []; $nuevos = [];
            foreach ($cfdis as $cfdi) {
                $uuid = strtolower(trim($cfdi->uuid));
                if (isset($vistos[$uuid])) {
                    throw new RuntimeException('UUID duplicado en CFDI ' . $vistos[$uuid] . ' y ' . $cfdi->id . '. No se cargó el historial.');
                }
                $vistos[$uuid] = $cfdi->id;
                $indice = null;
                foreach ($lotes as $i => $lote) {
                    if ($lote['activacion'] <= $cfdi->fecha_timbrado && $lote['disponibles'] > 0) {
                        $indice = $i; break;
                    }
                }
                if ($indice === null) {
                    throw new RuntimeException('No hay timbres de una recarga activada para CFDI ' . $cfdi->id
                        . ' (' . $cfdi->fecha_timbrado . '). Revise recargas/fechas; toda la carga se revierte.');
                }
                $lotes[$indice]['disponibles']--;
                $previo = $existentes->get($cfdi->id);
                if ($previo) {
                    if ((int)$previo->recarga_id !== $lotes[$indice]['id'] || $previo->fecha_movimiento !== $cfdi->fecha_timbrado) {
                        throw new RuntimeException('La asignación existente del CFDI ' . $cfdi->id . ' no coincide. No se reasignó.');
                    }
                    $revisados[(int)$cfdi->id] = true;
                    continue;
                }
                $nuevos[] = [
                    'tipo' => 'consumo', 'cantidad' => -1, 'fecha_movimiento' => $cfdi->fecha_timbrado,
                    'fecha_compra' => null, 'fecha_activacion' => null,
                    'referencia' => $uuid, 'nota' => 'Asignación histórica estimada por fecha y disponibilidad FIFO.',
                    'cfdis_id' => $cfdi->id, 'recarga_id' => $lotes[$indice]['id'],
                    'asignacion_estimada' => true, 'created_at' => $ahora, 'updated_at' => $ahora,
                ];
            }
            if (count($revisados) !== $existentes->count()) {
                throw new RuntimeException('Hay consumos previos sin CFDI elegible en el corte. Revise el historial antes de continuar.');
            }
            foreach (array_chunk($nuevos, 50) as $grupo) {
                DB::table('movimientos_timbres')->insert($grupo);
            }
            return ['lotes' => $lotes, 'anteriores' => $anteriores, 'nuevos' => count($nuevos),
                'existentes' => count($revisados), 'corte' => $corte];
        });

        if ($this->command) {
            $this->command->info('Historial cargado hasta ' . $resumen['corte'] . ' (America/Mazatlan).');
            $this->command->line('Consumos nuevos: ' . $resumen['nuevos'] . '; ya registrados: ' . $resumen['existentes']);
            $this->command->table(['Recarga', 'Activación', 'Recargados', 'Consumidos', 'Disponibles'],
                array_map(function ($lote) {
                    return [$lote['referencia'], $lote['activacion'], $lote['cantidad'],
                        $lote['cantidad'] - $lote['disponibles'], $lote['disponibles']];
                }, $resumen['lotes']));
            if ($resumen['anteriores'] > 0) {
                $this->command->warn('CFDI anteriores a la primera recarga, sin asignar: ' . $resumen['anteriores']);
            }
            $this->command->line('Disponible total: ' . array_sum(array_column($resumen['lotes'], 'disponibles')));
        }
    }
}
