<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompraDetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compra_detalle', function (Blueprint $table) {
            $table->integer('cantidad');
            $table->dateTime('fecha_caducidad')->nullable();
            $table->unsignedDecimal('costo', 10, 2);
            $table->unsignedDecimal('descuento', 10, 2);
            $table->unsignedDecimal('impuestos', 10, 2);
            $table->unsignedDecimal('total', 10, 2);
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->unsignedBigInteger('movimientos_inventario_id')->unsigned()->nullable();
            $table->foreign('movimientos_inventario_id')->references('id')->on('movimientos_inventario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compra_detalle');
    }
}