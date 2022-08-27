<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AjustesPoliticas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ajustes_politicas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('tasa_fija_anual');
            $table->tinyInteger('dias_antes_vencimiento');
            $table->tinyInteger('maximo_dias_retraso');
            $table->decimal('porcentaje_pena_convencional_minima', 8, 2);
            $table->tinyInteger('minima_partes_cubiertas');
            $table->tinyInteger('maximo_pagos_vencidos');
            $table->tinyInteger('maximo_dias_cancelar_contrato');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ajustes_politicas');
    }
}