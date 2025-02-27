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
            $table->tinyInteger('periodo_validez');
            $table->tinyInteger('predefinidas_b');
            $table->unsignedDecimal('descuento', 10, 2);
            $table->unsignedDecimal('total', 10, 2);
            $table->tinyInteger('modalidad');
            $table->string('descripcion_pagos');
            $table->longText('nota');
            $table->tinyInteger('tipo_persona')->default(1);//1 - medico legista. 2- tecnico embalsamador
            $table->unsignedBigInteger('modifico_id')->unsigned()->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_modificacion');
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_cancelacion');
            $table->longText('nota_cancelacion');
            $table->unsignedBigInteger('modifico_aceptado_id')->unsigned()->nullable();
            $table->foreign('modifico_aceptado_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_aceptado');
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
            $table->string('descripcion');
            $table->tinyInteger('financiamiento');
            $table->unsignedDecimal('costo_neto', 10, 2);
            $table->unsignedDecimal('pago_inicial', 10, 2);
            $table->unsignedDecimal('abono_mensual', 10, 2);
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
