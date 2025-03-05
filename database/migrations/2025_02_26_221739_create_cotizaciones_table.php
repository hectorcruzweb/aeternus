<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cliente_nombre');
            $table->string('cliente_telefono')->nullable();
            $table->string('cliente_email')->nullable();
            $table->unsignedBigInteger('vendedor_id')->unsigned();
            $table->foreign('vendedor_id')->references('id')->on('usuarios');
            $table->date('fecha');
            $table->tinyInteger('periodo_validez_id');
            $table->date('fecha_vencimiento');
            $table->tinyInteger('predefinidas_b');
            $table->unsignedDecimal('descuento', 10, 2);
            $table->unsignedDecimal('total', 10, 2);
            $table->tinyInteger('modalidad');
            $table->unsignedDecimal('pago_inicial', 10, 2);
            $table->string('descripcion_pagos')->nullable();
            $table->longText('nota')->nullable();
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_registro');
            $table->unsignedBigInteger('modifico_id')->unsigned()->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_modificacion')->nullable();
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_cancelacion')->nullable();
            $table->longText('nota_cancelacion')->nullable();
            $table->unsignedBigInteger('modifico_aceptado_id')->unsigned()->nullable();
            $table->foreign('modifico_aceptado_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_aceptado')->nullable();
            $table->tinyInteger('tipo_id')->default(1);//1- Cotizacion Personalizada 2- Cotización Predefinidad 3- Cotizacion Mixta
            $table->tinyInteger('status')->default(1);
        });

        Schema::create('conceptos_cotizacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->smallInteger('cantidad');
            $table->unsignedDecimal('costo_neto_normal', 10, 2);
            $table->unsignedDecimal('costo_neto_descuento', 10, 2);
            $table->tinyInteger('descuento_b')->default(0);//1 => si, 0 => no
            $table->unsignedBigInteger('cotizaciones_id')->unsigned();
            $table->foreign('cotizaciones_id')->references('id')->on('cotizaciones');
        });
        Schema::create('cotizaciones_predefinidas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->string('tipo');
            $table->unsignedBigInteger('cotizaciones_id')->unsigned();
            $table->foreign('cotizaciones_id')->references('id')->on('cotizaciones');
        });
        Schema::create('financiamientos_predefinidas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('financiamiento');
            $table->unsignedDecimal('costo_neto', 10, 2);
            $table->unsignedDecimal('pago_inicial', 10, 2);
            $table->unsignedDecimal('pago_mensual', 10, 2);
            $table->unsignedBigInteger('cotizacion_predefinida_id')->unsigned();
            $table->foreign('cotizacion_predefinida_id')->references('id')->on('cotizaciones_predefinidas');
        });
        Schema::create('conceptos_predefinidas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('seccion_id');
            $table->string('concepto');
            $table->unsignedBigInteger('cotizacion_predefinida_id')->unsigned();
            $table->foreign('cotizacion_predefinida_id')->references('id')->on('cotizaciones_predefinidas');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financiamientos_predefinidas');
        Schema::dropIfExists('conceptos_predefinidas');
        Schema::dropIfExists('cotizaciones_predefinidas');
        Schema::dropIfExists('conceptos_cotizacion');
        Schema::dropIfExists('cotizaciones');
    }
}
