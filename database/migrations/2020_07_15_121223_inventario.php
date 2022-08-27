<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Inventario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->unsignedDecimal('precio_compra_neto', 10, 2);
            $table->date('fecha_caducidad')->nullable();
            $table->unsignedBigInteger('lotes_id')->unsigned()->nullable();
            $table->foreign('lotes_id')->references('id')->on('movimientos_inventario');
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->integer('existencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventario');
    }
}