<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SolicitudCancelacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitud_cancelacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->dateTime('fecha_registro');
            $table->unsignedBigInteger('operaciones_id')->unsigned()->nullable();
            $table->foreign('operaciones_id')->references('id')->on('operaciones');
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->mediumText('nota')->nullable();
            $table->unsignedDecimal('monto_devolver', 10, 2);
            $table->unsignedDecimal('total_pago_cuenta', 10, 2);
            $table->unsignedBigInteger('motivos_cancelacion_id')->unsigned()->nullable();
            $table->foreign('motivos_cancelacion_id')->references('id')->on('motivos_cancelacion');


            $table->tinyInteger('aprobacion_directa_b')->default(1);
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitud_cancelacion');
    }
}
