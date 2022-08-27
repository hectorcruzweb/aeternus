<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Firmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('firmas', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('areas_firmas_id')->unsigned()->nullable();
          $table->foreign('areas_firmas_id')->references('id')->on('areas_firmas');
          $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
          $table->foreign('registro_id')->references('id')->on('usuarios');
          $table->string('firma_path')->nullable();
          $table->bigInteger('operacion_id')->nullable();
          $table->bigInteger('solicitudes_id')->nullable();
          $table->bigInteger('pagos_id')->nullable();
          $table->bigInteger('facturas_id')->nullable();
          $table->dateTime('fecha_hora_firma')->nullable();
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
        Schema::dropIfExists('firmas');
    }
}
