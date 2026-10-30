<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosTimbresTable extends Migration
{
    /**
     * Historial por lotes, sin duplicar saldos almacenados.
     * Lote: tipo recarga/saldo_inicial, cantidad positiva, fechas de compra/activación.
     * Consumo: cantidad -1, cfdis_id y recarga_id; fecha_movimiento = fecha_timbrado.
     * Ajuste: cantidad con signo y recarga_id del lote afectado; motivo en nota.
     * Disponible del lote = cantidad inicial + SUM(cantidad de sus movimientos hijos).
     * Disponible global = suma de disponibles de lotes activados al momento del corte.
     * El usuario de timbrado se obtiene mediante cfdis.timbro_id -> usuarios.id.
     *
     * El código que registre movimientos deberá validar cantidades, fechas y tipos:
     * asignar la recarga más antigua activada con saldo, dentro de una transacción
     * con bloqueo para evitar sobreconsumos concurrentes. Una reconstrucción
     * histórica marcará asignacion_estimada y no duplicará consumos existentes.
     * Las FK no validan estas reglas de negocio.
     * HistorialTimbresSeeder registrará los tres lotes confirmados (4500 timbres).
     * Se incluyen timbrados cancelados: la cancelación no borra el consumo.
     * La fecha de compra desconocida se conserva NULL, sin inventar fechas.
     * Esta migración no inserta datos
     * ni modifica todavía el timbrado ni genera reportes.
     */
    public function up()
    {
        Schema::create('movimientos_timbres', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->enum('tipo', ['saldo_inicial', 'recarga', 'consumo', 'ajuste']);
            $table->integer('cantidad')->comment('Lote positivo; consumo -1; ajuste con signo');
            $table->dateTime('fecha_movimiento')->comment('Fecha efectiva; en consumos corresponde al timbrado');

            // Solo para lotes. El saldo inicial puede no tener una compra conocida.
            $table->dateTime('fecha_compra')->nullable();
            $table->dateTime('fecha_activacion')->nullable()->comment('Desde cuándo se puede consumir este lote');
            $table->string('referencia', 191)->nullable()->comment('Referencia del lote o UUID confirmado del consumo');
            $table->text('nota')->nullable();

            // NULL en lotes y ajustes. Un CFDI no puede consumir dos veces.
            $table->unsignedBigInteger('cfdis_id')->nullable();
            // NULL en lotes; en consumos/ajustes apunta al registro de recarga/saldo inicial.
            $table->unsignedBigInteger('recarga_id')->nullable();
            $table->boolean('asignacion_estimada')->default(false)
                ->comment('Consumo histórico asociado por reconstrucción de fechas');
            $table->timestamps();

            $table->index('fecha_movimiento', 'mov_timbres_fecha_idx');
            $table->index(['tipo', 'fecha_activacion'], 'mov_timbres_tipo_activacion_idx');
            $table->index(['recarga_id', 'fecha_movimiento'], 'mov_timbres_recarga_fecha_idx');
            $table->unique('cfdis_id', 'mov_timbres_cfdi_unique');
            $table->unique(['tipo', 'referencia'], 'mov_timbres_tipo_referencia_unique');
            // Conservar historial: no borrar el CFDI ni el lote con consumos asociados.
            $table->foreign('cfdis_id', 'mov_timbres_cfdi_fk')
                ->references('id')->on('cfdis')->onDelete('restrict');
            $table->foreign('recarga_id', 'mov_timbres_recarga_fk')
                ->references('id')->on('movimientos_timbres')->onDelete('restrict');
        });
    }

    /** Retirar la autorrelación antes de eliminar la tabla en MySQL/MariaDB. */
    public function down()
    {
        Schema::table('movimientos_timbres', function (Blueprint $table) {
            $table->dropForeign('mov_timbres_recarga_fk');
        });
        Schema::dropIfExists('movimientos_timbres');
    }
}
