<?php

/**
 * Reconstrucción del historial de timbres.
 *
 * Ejecutar desde la raíz del proyecto con el timbrado detenido:
 * php artisan db:seed --class=HistorialTimbresSeeder
 *
 * En producción, para ejecutar sin la confirmación interactiva:
 * php artisan db:seed --class=HistorialTimbresSeeder --force
 *
 * Toma el corte en America/Mazatlan. Busca CFDI de Ingreso (1), Egreso (2)
 * y Pago (5) con UUID y fecha de timbrado, incluidos los cancelados.
 * Reemplaza los consumos hasta el corte en una sola transacción.
 * Conserva las recargas, los saldos iniciales, los ajustes y los
 * consumos posteriores al corte. Si faltan todos los lotes, crea los
 * tres lotes históricos definidos en RECARGAS.
 *
 * Al terminar, compara el saldo informado con el dashboard actualizado
 * y con el saldo del proveedor. El seeder solo consulta la base local.
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistorialTimbresSeeder extends Seeder
{
    private const RECARGAS = [
        ['HIST-20220303-125528', 2000, '2022-03-03 12:55:28'],
        ['HIST-20240604-112947', 2000, '2024-06-04 11:29:47'],
        ['HIST-20260519-233248', 500, '2026-05-19 23:32:48'],
    ];

    /**
     * Reconstruye los consumos de CFDI timbrados en una transacción.
     * Conserva las recargas y ajustes existentes. En una instalación sin lotes,
     * carga las tres recargas históricas. Ejecutar con el timbrado detenido.
     */
    public function run()
    {
        $corte = Carbon::now('America/Mazatlan')->format('Y-m-d H:i:s');
        $resumen = DB::transaction(function () use ($corte) {
            $consultaLotes = DB::table('movimientos_timbres')
                ->whereIn('tipo', ['recarga', 'saldo_inicial'])->whereNull('recarga_id')
                ->orderBy('fecha_activacion')->orderBy('id')->lockForUpdate();
            $lotes = $consultaLotes->get();
            if ($lotes->isEmpty()) {
                foreach (self::RECARGAS as [$referencia, $cantidad, $activacion]) {
                    DB::table('movimientos_timbres')->insert([
                        'tipo' => 'recarga', 'referencia' => $referencia, 'cantidad' => $cantidad,
                        'fecha_movimiento' => $activacion, 'fecha_activacion' => $activacion,
                        'created_at' => $corte, 'updated_at' => $corte,
                    ]);
                }
                $lotes = $consultaLotes->get();
            }

            // Aplica los ajustes por fecha antes de asignar cada consumo.
            $saldos = $lotes->pluck('cantidad', 'id')->all();
            $ajustes = DB::table('movimientos_timbres')->where('tipo', 'ajuste')
                ->whereIn('recarga_id', $lotes->pluck('id')->all())
                ->where('fecha_movimiento', '<=', $corte)
                ->orderBy('fecha_movimiento')->orderBy('id')->lockForUpdate()->get();
            $cfdis = DB::table('cfdis')->whereIn('sat_tipo_comprobante_id', [1, 2, 5])
                ->whereNotNull('uuid')->whereRaw("TRIM(uuid) <> ''")
                ->where('fecha_timbrado', '>=', '1000-01-01 00:00:00')
                ->where('fecha_timbrado', '<=', $corte)
                ->orderBy('fecha_timbrado')->orderBy('id')->get(['id', 'uuid', 'fecha_timbrado']);

            $previos = DB::table('movimientos_timbres')->where('tipo', 'consumo')->lockForUpdate()
                ->get(['cfdis_id', 'fecha_movimiento', 'recarga_id'])->keyBy('cfdis_id');
            $vistos = [];
            $consumos = [];
            $sinLote = 0;
            $indiceAjuste = 0;
            foreach ($cfdis as $cfdi) {
                $uuid = strtolower(trim($cfdi->uuid));
                if (isset($vistos[$uuid])) {
                    throw new RuntimeException('UUID duplicado en CFDI '.$vistos[$uuid].' y '.$cfdi->id.'.');
                }
                $vistos[$uuid] = $cfdi->id;

                while (isset($ajustes[$indiceAjuste]) && $ajustes[$indiceAjuste]->fecha_movimiento <= $cfdi->fecha_timbrado) {
                    $ajuste = $ajustes[$indiceAjuste++];
                    $saldos[$ajuste->recarga_id] += $ajuste->cantidad;
                }

                // FIFO: el primer lote activo con saldo recibe el consumo.
                // Si todos están agotados, el último activo refleja el desfase.
                $recarga = null;
                foreach ($lotes as $lote) {
                    if (!$lote->fecha_activacion || $lote->fecha_activacion > $cfdi->fecha_timbrado) {
                        continue;
                    }
                    $recarga = $lote->id;
                    if ($saldos[$recarga] > 0) {
                        break;
                    }
                }

                if ($recarga === null) {
                    $sinLote++;
                    // Sin lote activo, solo se reconstruye el consumo si ya existía. Queda fuera del saldo.
                    if (!$previos->has($cfdi->id)) {
                        continue;
                    }
                } else {
                    $saldos[$recarga]--;
                }

                $consumos[] = [
                    'tipo' => 'consumo', 'cantidad' => -1, 'fecha_movimiento' => $cfdi->fecha_timbrado,
                    'referencia' => $uuid, 'cfdis_id' => $cfdi->id, 'recarga_id' => $recarga,
                    'asignacion_estimada' => true, 'created_at' => $corte, 'updated_at' => $corte,
                ];
            }

            // Reemplaza solo consumos hasta el corte. Un error revierte el borrado y las inserciones.
            DB::table('movimientos_timbres')->where('tipo', 'consumo')
                ->where('fecha_movimiento', '<=', $corte)->delete();
            foreach (array_chunk($consumos, 50) as $grupo) {
                DB::table('movimientos_timbres')->insert($grupo);
            }

            while (isset($ajustes[$indiceAjuste])) {
                $ajuste = $ajustes[$indiceAjuste++];
                $saldos[$ajuste->recarga_id] += $ajuste->cantidad;
            }

            $disponibles = $lotes->filter(function ($lote) use ($corte) {
                return $lote->fecha_activacion && $lote->fecha_activacion <= $corte;
            })->sum(function ($lote) use ($saldos) {
                return $saldos[$lote->id];
            });

            return ['consumos' => count($consumos), 'sin_lote' => $sinLote, 'disponibles' => $disponibles];
        });

        if ($this->command) {
            $this->command->info('Consumos reconstruidos al '.$corte.': '.$resumen['consumos']);
            $this->command->line('CFDI sin lote activo al timbrar: '.$resumen['sin_lote']);
            $this->command->line('Saldo total de recargas activas: '.$resumen['disponibles']);
        }
    }
}

