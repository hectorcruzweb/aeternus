<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MovimientosInventario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('folio_referencia')->nullable();
            $table->dateTime('fecha_registro')->nullable();
            $table->dateTime('fecha_movimiento')->nullable();
            //$table->unsignedDecimal('subtotal', 10, 2);
            //$table->unsignedDecimal('descuento', 10, 2);
            //$table->unsignedDecimal('impuestos', 10, 2);
            //$table->unsignedDecimal('total', 10, 2);
            $table->mediumText('nota')->nullable();
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->mediumText('nota_cancelacion')->nullable();
            $table->unsignedBigInteger('operaciones_id')->unsigned()->nullable();
            $table->foreign('operaciones_id')->references('id')->on('operaciones');
            $table->unsignedBigInteger('tipo_movimientos_id')->unsigned()->nullable();
            $table->foreign('tipo_movimientos_id')->references('id')->on('tipo_movimientos');
            $table->unsignedBigInteger('proveedores_id')->unsigned()->nullable();
            $table->foreign('proveedores_id')->references('id')->on('proveedores');
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->tinyInteger('status')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientos_inventario');
    }
}