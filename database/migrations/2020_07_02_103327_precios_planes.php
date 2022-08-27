<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PreciosPlanes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precios_planes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('planes_funerarios_id')->unsigned()->nullable();
            $table->foreign('planes_funerarios_id')->references('id')->on('planes_funerarios');
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_registro');
            $table->unsignedBigInteger('actualizo_id')->unsigned()->nullable();
            $table->foreign('actualizo_id')->references('id')->on('usuarios');
            $table->tinyInteger('financiamiento'); //indica con un valor entero el numeero de pagos qe creara esta operacion 1 para de contado y mayor a 1 para meses
            $table->dateTime('fecha_actualizacion')->nullable();
            $table->string('descripcion');
            $table->string('descripcion_ingles');
            $table->unsignedDecimal('pago_inicial', 10, 2);
            $table->unsignedDecimal('subtotal', 10, 2);
            $table->unsignedDecimal('impuestos', 10, 2);
            $table->unsignedDecimal('costo_neto', 10, 2);
            $table->tinyInteger('contado_b');
            $table->unsignedDecimal('costo_neto_financiamiento_normal', 10, 2);
            $table->tinyInteger('descuento_pronto_pago_b');
            $table->unsignedDecimal('costo_neto_pronto_pago', 10, 2);
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->mediumText('nota_cancelacion')->nullable();
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
        Schema::dropIfExists('precios_planes');
    }
}