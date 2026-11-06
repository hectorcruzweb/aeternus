<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Cancela solicitudes antiguas SIN contrato en servicios_funerarios.
 *
 * USO (desde la raíz del proyecto):
 *   php artisan servicios:cancelar-solicitudes-antiguas           // Simulación.
 *   php artisan servicios:cancelar-solicitudes-antiguas --aplicar // Guarda status=0.
 *
 * AUTOMÁTICO: Kernel programa --aplicar diariamente a las 02:00, Mazatlán.
 * El servidor debe ejecutar `php artisan schedule:run` cada minuto (cron o
 * Programador de tareas de Windows). Registrar el horario no inicia ese proceso.
 *
 * CRITERIOS: status>0, fechahora_solicitud válida y estrictamente anterior
 * a ahora menos 10 días completos. A exactamente 10 días todavía no se cancela.
 * Se omiten fechas nulas/cero, cualquier operación tipo 3 asociada (incluso
 * cancelada, para conservar el historial contractual) y servicios que tienen
 * una exhumación activa. No se borran registros ni se alteran operaciones,
 * inventario, pagos, facturas o seguimientos. No se inventa un usuario cancelador.
 *
 * AUDITORÍA: escribe corte, IDs y estado anterior en el log configurado de Laravel.
 * No añade campos a la tabla ni sustituye la cancelación manual de contratos.
 * Reejecutar es seguro: las solicitudes status=0 dejan de ser candidatas.
 */
class CancelarSolicitudesAntiguas extends Command
{
    private const DIAS_ANTIGUEDAD = 10;
    private const ZONA_HORARIA = 'America/Mazatlan';

    protected $signature = 'servicios:cancelar-solicitudes-antiguas
        {--aplicar : Cancelar solicitudes; sin esta opcion solo muestra las candidatas}';

    protected $description = 'Cancela solicitudes funerarias sin contrato con mas de 10 dias completos; por defecto simula.';

    /**
     * Consulta compartida por simulación y escritura. El NOT EXISTS evita
     * duplicados y protege contratos históricos; se usa de nuevo al actualizar.
     * $limite queda fijo durante toda la ejecución para no mover el corte por lote.
     */
    private function candidatas(Carbon $limite, $verificarExhumacion = true)
    {
        return DB::table('servicios_funerarios')
            ->where('status', '>', 0)
            ->where('fechahora_solicitud', '>', '1000-01-01 00:00:00')
            ->where('fechahora_solicitud', '<', $limite->toDateTimeString())
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('operaciones as o')
                    ->whereColumn('o.servicios_funerarios_id', 'servicios_funerarios.id')
                    ->where('o.empresa_operaciones_id', 3);
            })
            ->when($verificarExhumacion, function ($consulta) {
                $consulta->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('servicios_funerarios as exhumacion')
                    ->whereColumn('exhumacion.servicios_funerarios_exhumado_id', 'servicios_funerarios.id')
                    ->where('exhumacion.status', 1);
                });
            });
    }

    /**
     * Recorre lotes de 200 sin cargar todo el historial. En modo real bloquea y
     * revalida cada solicitud dentro de una transacción antes de cambiar status.
     * Una falla detiene la ejecución con código 1; los lotes previos ya confirmados
     * quedan auditados y una nueva ejecución continúa con las restantes.
     */
    public function handle()
    {
        $ahora = Carbon::now(self::ZONA_HORARIA);
        $limite = $ahora->copy()->subDays(self::DIAS_ANTIGUEDAD);
        $aplicar = (bool) $this->option('aplicar');
        $total = 0;
        $this->info(($aplicar ? 'Aplicando' : 'SIMULACION').' · Solicitudes anteriores a '.$limite->toDateTimeString().' ('.self::ZONA_HORARIA.').');

        try {
            $this->candidatas($limite)->select('id')->orderBy('id')->chunkById(200, function ($filas) use ($limite, $ahora, $aplicar, &$total) {
                foreach ($filas as $fila) {
                    if (!$aplicar) {
                        $this->line('Solicitud '.$fila->id);
                        $total++;
                        continue;
                    }
                    $anterior = DB::transaction(function () use ($fila, $limite) {
                        $solicitud = $this->candidatas($limite)->where('id', $fila->id)->lockForUpdate()->first(['id', 'status']);
                        if (!$solicitud) return null;
                        // La exhumación ya se comprobó al bloquear la fila. Evitar una
                        // subconsulta a la misma tabla en UPDATE (MySQL error 1093).
                        $actualizadas = $this->candidatas($limite, false)->where('id', $fila->id)->update(['status' => 0]);
                        return $actualizadas ? $solicitud : null;
                    });
                    if ($anterior) {
                        $total++;
                        Log::info('Solicitud funeraria cancelada automaticamente por antiguedad.', [
                            'servicios_funerarios_id' => $anterior->id,
                            'status_anterior' => $anterior->status,
                            'status_nuevo' => 0,
                            'fecha_ejecucion' => $ahora->toDateTimeString(),
                            'solicitudes_anteriores_a' => $limite->toDateTimeString(),
                            'dias_antiguedad' => self::DIAS_ANTIGUEDAD,
                        ]);
                    }
                }
            });
            $this->info(($aplicar ? 'Canceladas: ' : 'Candidatas: ').$total);
            return 0;
        } catch (\Throwable $e) {
            Log::error('Fallo al cancelar solicitudes funerarias antiguas.', ['mensaje' => $e->getMessage(), 'canceladas' => $total]);
            $this->error('No se completo la ejecucion. Revise el log de Laravel.');
            return 1;
        }
    }
}
