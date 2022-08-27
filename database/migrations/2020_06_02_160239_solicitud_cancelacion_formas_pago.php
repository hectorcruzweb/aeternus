<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SolicitudCancelacionFormasPago extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancelacion_formas_pago', function (Blueprint $table) {
            $table->unsignedBigInteger('solicitud_cancelacion_id')->unsigned()->nullable();
            $table->foreign('solicitud_cancelacion_id')->references('id')->on('solicitud_cancelacion');
            $table->unsignedBigInteger('sat_formas_pago_id')->unsigned()->nullable();
            $table->foreign('sat_formas_pago_id')->references('id')->on('sat_formas_pago');
            $table->unsignedDecimal('monto', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cancelacion_formas_pago');
    }
}
