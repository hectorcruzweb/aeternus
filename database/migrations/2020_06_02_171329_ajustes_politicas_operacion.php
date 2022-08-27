<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AjustesPoliticasOperacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ajustes_politicas_operacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedDecimal('tasa_fija_anual', 10, 2)->nullable()->unsigned();
            $table->integer('dias_antes_vencimiento');
            $table->integer('maximo_dias_retraso');
            $table->unsignedDecimal('porcentaje_pena_convencional_minima', 10, 2)->nullable()->unsigned();
            $table->integer('minima_partes_cubiertas');
            $table->integer('maximo_pagos_vencidos');
            $table->integer('maximo_dias_cancelar_contrato');
            $table->unsignedBigInteger('operaciones_id')->nullable();
            $table->foreign('operaciones_id')->references('id')->on('operaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ajustes_politicas_operacion');
    }
}
