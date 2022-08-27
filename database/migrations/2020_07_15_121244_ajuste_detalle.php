<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AjusteDetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ajuste_detalle', function (Blueprint $table) {
            $table->date('fecha_caducidad')->nullable();
            $table->integer('existencia_sistema');
            $table->integer('existencia_fisica');
            $table->unsignedBigInteger('movimientos_inventario_id')->unsigned()->nullable();
            $table->foreign('movimientos_inventario_id')->references('id')->on('movimientos_inventario');
            $table->unsignedBigInteger('lotes_id')->unsigned()->nullable();
            $table->foreign('lotes_id')->references('id')->on('movimientos_inventario');
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->string('nota')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ajuste_detalle');
    }
}