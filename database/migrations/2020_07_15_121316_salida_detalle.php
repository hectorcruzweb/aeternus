<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SalidaDetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salida_detalles', function (Blueprint $table) {
            $table->dateTime('fecha_caducidad')->nullable();
            $table->integer('cantidad');
            $table->unsignedBigInteger('movimientos_inventario_id')->unsigned()->nullable();
            $table->foreign('movimientos_inventario_id')->references('id')->on('movimientos_inventario');
            $table->unsignedBigInteger('lotes_id')->unsigned()->nullable();
            $table->foreign('lotes_id')->references('id')->on('movimientos_inventario');
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salida_detalles');
    }
}