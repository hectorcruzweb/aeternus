<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PagosProgramados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_programados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('referencia_pago');
            $table->tinyInteger('num_pago');
            $table->unsignedTinyInteger('conceptos_pagos_id')->nullable();
            $table->date('fecha_programada');
            $table->unsignedDecimal('monto_programado', 10, 2);
            $table->unsignedBigInteger('operaciones_id')->nullable();
            $table->foreign('operaciones_id')->references('id')->on('operaciones');
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
        Schema::dropIfExists('pagos_programados');
    }
}