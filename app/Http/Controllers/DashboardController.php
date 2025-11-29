<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\ServiciosFunerarios;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    public function serviciosFunerarios()
    {
        $now = Carbon::now('America/Mazatlan');
        $rango_dias = $now->copy()->subDays(3);

        $servicios = ServiciosFunerarios::query()
            ->select([
                'id',
                'nombre_afectado',
                'cremacion_b',
                'inhumacion_b',
                'traslado_b',
                'velacion_b',
                'lugares_servicios_id',
                'direccion_velacion',
                'fechahora_cremacion',
                'fechahora_inhumacion',
                'fechahora_misa',
                'fechahora_solicitud',
            ])
            ->where('status', '>', 0)

            ->where(function ($q) use ($now, $rango_dias) {

                /*
            ────────────────────────────────────────────
            A) CASE 1: At least one FINAL date is defined
            ────────────────────────────────────────────
            Include the service ONLY if one of those 
            defined dates is still in the future.
            */
                $q->where(function ($x) use ($now) {
                    $x->where(function ($y) use ($now) {
                        // cremación exists and is future
                        $y->whereNotNull('fechahora_cremacion')
                            ->where('fechahora_cremacion', '>', $now);
                    })
                        ->orWhere(function ($y) use ($now) {
                            // inhumación exists and is future
                            $y->whereNotNull('fechahora_inhumacion')
                                ->where('fechahora_inhumacion', '>', $now);
                        })
                        ->orWhere(function ($y) use ($now) {
                            // misa exists and is future
                            $y->whereNotNull('fechahora_misa')
                                ->where('fechahora_misa', '>', $now);
                        });
                })

                    /*
            ────────────────────────────────────────────
            B) CASE 2: NONE of the final dates is defined
            ────────────────────────────────────────────
            Then use only fechahora_solicitud
            with max 3-day range.
            */
                    ->orWhere(function ($x) use ($rango_dias) {
                        $x->whereNull('fechahora_cremacion')
                            ->whereNull('fechahora_inhumacion')
                            ->whereNull('fechahora_misa')
                            ->where('fechahora_solicitud', '>', $rango_dias);
                    });
            })

            ->with(['datosoperacion' => function ($q) {
                $q->select([
                    'id',
                    'empresa_operaciones_id',
                    'servicios_funerarios_id',
                    'saldo',
                ])
                    ->where('empresa_operaciones_id', 3)
                    ->where('status', '>', 0);
            }])

            ->orderBy('fechahora_solicitud', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'servicios_funerarios' => $servicios,
        ]);
    }
}
