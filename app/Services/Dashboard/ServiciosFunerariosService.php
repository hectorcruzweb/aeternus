<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use App\ServiciosFunerarios;
use Illuminate\Support\Facades\DB;

class ServiciosFunerariosService
{
    public function getServicios()
    {
        $now = Carbon::now('America/Mazatlan');
        $rango_dias = $now->copy()->subDays(3); // LAST 3 DAYS
        $servicios = ServiciosFunerarios::query()
            ->select([
                'id',
                'nombre_afectado',
                'cremacion_b',
                'inhumacion_b',
                'traslado_b',
                'misa_b',
                'velacion_b',
                'lugares_servicios_id',
                'direccion_velacion',
                'fechahora_cremacion',
                'fechahora_inhumacion',
                'fechahora_traslado',
                'fechahora_misa',
                'fechahora_solicitud',
                DB::raw("CASE WHEN velacion_b = 1 THEN 'si' ELSE 'no' END AS velacion"),
                DB::raw("CASE WHEN misa_b = 1 THEN 'si' ELSE 'no' END AS misa"),
                DB::raw("CASE WHEN cremacion_b = 1 THEN 'si' ELSE 'no' END AS cremacion"),
                DB::raw("CASE WHEN inhumacion_b = 1 THEN 'si' ELSE 'no' END AS inhumacion"),
                DB::raw("CASE WHEN traslado_b = 1 THEN 'si' ELSE 'no' END AS traslado"),
            ])
            ->where('status', '>', 0)
            ->where(function ($q) use ($now, $rango_dias) {
                // A) CASE 1 — At least one FINAL event exists and is FUTURE
                $q->where(function ($x) use ($now) {
                    $x->whereNotNull('fechahora_cremacion')
                        ->where('fechahora_cremacion', '>', $now)
                        ->orWhereNotNull('fechahora_inhumacion')
                        ->where('fechahora_inhumacion', '>', $now)
                        ->orWhereNotNull('fechahora_misa')
                        ->where('fechahora_misa', '>', $now)
                        ->orWhereNotNull('fechahora_traslado')
                        ->where('fechahora_traslado', '>', $now);
                })
                    // B) CASE 2 — NONE of the final events exist → fallback to solicitud
                    ->orWhere(function ($x) use ($rango_dias) {
                        $x->whereNull('fechahora_cremacion')
                            ->whereNull('fechahora_inhumacion')
                            ->whereNull('fechahora_misa')
                            ->whereNull('fechahora_traslado')
                            ->where('fechahora_solicitud', '>', $rango_dias);
                    });
            })
            ->with(['datosoperacion' => function ($q) {
                $q->select(['id', 'empresa_operaciones_id', 'servicios_funerarios_id', 'saldo', DB::raw("CONCAT('$ ', FORMAT(saldo, 2, 'es_MX')) AS saldo_pesos")])
                    ->where('empresa_operaciones_id', 3)
                    ->where('status', '>', 0);
            }])
            // Lugar de velación (only if velación_b = 1 will return non-null)
            ->with(['lugarvelacion' => function ($q) {
                $q->select(['*']);
            }])
            ->orderBy('fechahora_solicitud', 'desc')
            ->limit(10)
            ->get();
        return $servicios;
    }
}
