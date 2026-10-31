<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Entrada Artisan de mantenimiento de saldo/status, separada de la lectura del dashboard.
 * Dónde ajustar: OPERACIONES_STATUS_0 contiene operaciones.id verificadas (3412 y 897), no IDs de
 * servicios. Sin --aplicar simula; --tipo limita los tipos. La actualización real se delega a
 * mantenerSaldosStatusOperaciones.
 * Relacionado: OperacionesController; php artisan help operaciones:actualizar-saldos
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */


namespace App\Console\Commands;

use App\Http\Controllers\OperacionesController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * MANTENIMIENTO MANUAL DE OPERACIONES (Laravel 6 / PHP 7.4).
 *
 * USO desde la raíz del proyecto:
 *   php artisan operaciones:actualizar-saldos                       // Simular todos.
 *   php artisan operaciones:actualizar-saldos --tipo=3              // Simular tipo 3.
 *   php artisan operaciones:actualizar-saldos --aplicar             // Guardar todos.
 *   php artisan operaciones:actualizar-saldos --tipo=3 --aplicar    // Guardar tipo 3.
 *   php artisan operaciones:actualizar-saldos --tipo=1 --tipo=2 --aplicar
 *   php artisan help operaciones:actualizar-saldos
 *
 * FLUJO: resolver excepciones por operaciones.id -> consultar el mantenimiento
 * del controlador -> mostrar conteos, IDs inexistentes y hasta 50 incidencias.
 * No se invoca desde el dashboard ni se programa automáticamente.
 *
 * CALCULO: pagos programados status=1 menos repartos de pagos status=1 con
 * movimientos 1/3/4/5 (capital y ajustes); no descuenta intereses. Incluye pagos
 * con vencimiento futuro. Redondea por programado, sin compensar sobrepagos;
 * saldo final mínimo cero: saldo positivo => status 1; cero => status 2.
 *
 * EXCLUSIONES: no reactiva status 0; omite ausencia de programación activa,
 * montos programados inválidos y suma programada distinta del total vendido
 * por más de $0.01. Esos casos requieren revisión, no se dan por liquidados.
 *
 * --aplicar confirma la transacción al terminar; un error revierte sus cambios.
 * Simular primero, conservar respaldo y aplicar sin cobros/ediciones concurrentes.
 * El mantenimiento no corrige los registros fuente ni prueba que una deuda sea real.
 * No cancela recibos, facturas o programados y no elimina registros.
 *
 * SALIDA: 0 = ejecución terminada (puede tener casos omitidos); 1 = error.
 * La lógica de cálculo reside en mantenerSaldosStatusOperaciones para reutilizarla.
 */
class ActualizarSaldosOperaciones extends Command
{
    protected $signature = 'operaciones:actualizar-saldos
        {--tipo=* : Tipo 1..5; repetir la opcion para varios. Omitir para todos}
        {--aplicar : Guardar cambios; sin esta opcion solo simula}';

    /**
     * CONFIGURACION EXCLUSIVA DEL PROGRAMADOR: lista plana de operaciones.id.
     * Ejemplo real: operaciones.id=3412 corresponde a servicios_funerarios_id=1383.
     * Aqui se escribe 3412, NO 1383. No se agrupa por tipo ni se usan IDs de venta.
     * Agregar mas operaciones.id como elementos de esta misma lista, con coma.
     * No existe opcion CLI para agregar IDs. --tipo resuelve su tipo en la base.
     * Al aplicar se fija solo status=0 y se conserva el saldo de estas excepciones.
     * Si se quita un ID de la lista, NO se reactiva una operación ya cancelada.
     */
    private const OPERACIONES_STATUS_0 = [
        3412, //servicio funerario 1383
        897, // servicio funerario 515
        // Agregar otros operaciones.id verificados.
    ];

    protected $description = 'Concilia saldo y status de operaciones desde pagos programados y pagos vigentes';

    // Disponible también desde: php artisan help operaciones:actualizar-saldos
    protected $help = <<<'AYUDA'
Sin --aplicar solo simula; no guarda cambios.

Ejemplos:
  php artisan operaciones:actualizar-saldos
  php artisan operaciones:actualizar-saldos --tipo=3
  php artisan operaciones:actualizar-saldos --aplicar
  php artisan operaciones:actualizar-saldos --tipo=3 --aplicar
  php artisan operaciones:actualizar-saldos --tipo=1 --tipo=2 --aplicar

Tipos: 1 cementerio, 2 cuotas, 3 servicios funerarios, 4 planes, 5 ventas generales.
Repetir --tipo para varios; omitirlo procesa los cinco tipos.

Excepciones: editar OPERACIONES_STATUS_0 dentro de este archivo.
La lista contiene exclusivamente operaciones.id; no IDs de servicios ni ventas.
Solo se aplican las excepciones pertenecientes a los tipos seleccionados.
Quedan en status 0 conservando saldo; no se eliminan datos ni se cancelan pagos.

Revisar los casos omitidos y los IDs no encontrados que muestra el resultado.
Un codigo de salida 0 no significa que se hayan corregido todas las incidencias.
Con --aplicar, commit automatico; ante error, rollback. No ejecutar COMMIT manual.
Aplicar con respaldo y sin cobros/ediciones concurrentes.
AYUDA;


    /**
     * El comando es el punto de entrada de mantenimiento, separado del dashboard.
     * Laravel resuelve el controlador por inyección; no usa Request ni rutas web.
     * Solo --aplicar habilita escrituras. La función hace commit/rollback completo.
     
     * Detalle del método: Lee --tipo y --aplicar, incorpora excepciones de operaciones.id y llama al
     * mantenimiento de saldo/status.
     * Entradas: $operaciones (controlador inyectado que ejecuta la conciliación).
     * Salida / efectos: Código de salida del comando y resumen por consola; simula por defecto.
     */
    public function handle(OperacionesController $operaciones)
    {
        $aplicar = (bool) $this->option('aplicar');
        $tipos = $this->option('tipo');
        $idsStatus0 = self::OPERACIONES_STATUS_0;
        $this->info($aplicar
            ? 'APLICACION: se actualizaran saldos y estados. Ejecutar sin cobros/ediciones concurrentes.'
            : 'SIMULACION: no se guardara ningun cambio.');

        try {
            if ($tipos && $idsStatus0) {
                // Resolver el tipo real desde operaciones, no desde el arreglo.
                // Conservar IDs inexistentes para que el mantenimiento los informe.
                $idsOtrosTipos = DB::table('operaciones')
                    ->whereIn('id', $idsStatus0)
                    ->whereNotIn('empresa_operaciones_id', $tipos)
                    ->pluck('id')->all();
                $idsStatus0 = array_values(array_diff($idsStatus0, $idsOtrosTipos));
            }
            if ($idsStatus0) {
                $this->line('operaciones.id internos para status 0: ' . implode(', ', $idsStatus0));
            }

            $resultado = $operaciones->mantenerSaldosStatusOperaciones(
                $idsStatus0,
                $aplicar,
                $tipos
            );
        } catch (\Throwable $error) {
            $this->error('Mantenimiento no completado: ' . $error->getMessage());
            $this->line('Si la transaccion ya habia comenzado, sus cambios se revirtieron.');
            return 1;
        }

        $this->line('Tipos: ' . implode(', ', $resultado['tiposOperacion']));
        $this->table(['Concepto', 'Cantidad'], [
            ['Operaciones revisadas', $resultado['operacionesRevisadas']],
            ['Operaciones con cambios calculados', $resultado['operacionesConCambios']],
            ['Operaciones actualizadas', $resultado['operacionesActualizadas']],
            ['Cancelaciones manuales previstas', $resultado['cancelacionesManualesPrevistas']],
            ['Cancelaciones manuales aplicadas', $resultado['cancelacionesManualesAplicadas']],
            ['Omitidas: sin programados activos', $resultado['omitidas']['sinProgramadosActivos']],
            ['Omitidas: montos invalidos', $resultado['omitidas']['montosInvalidos']],
            ['Omitidas: programacion diferente de venta', $resultado['omitidas']['programacionDifiereDeVenta']],
        ]);

        if ($resultado['idsNoEncontrados']) {
            $this->warn('IDs internos para status 0 no encontrados: ' . implode(', ', $resultado['idsNoEncontrados']));
        }
        if ($resultado['incidencias']) {
            $this->warn('Casos omitidos para revision (se muestran hasta 50):');
            $filas = array_map(function ($incidencia) {
                return [$incidencia['operacion_id'], $incidencia['motivo']];
            }, $resultado['incidencias']);
            $this->table(['Operacion', 'Motivo'], $filas);
        }

        $this->info($aplicar
            ? 'Mantenimiento confirmado. Los casos omitidos conservan sus datos.'
            : 'Simulacion terminada. Agrega --aplicar para guardar los cambios calculados.');
        return 0;
    }
}
