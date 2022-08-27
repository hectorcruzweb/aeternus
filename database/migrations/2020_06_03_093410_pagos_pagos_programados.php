<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PagosPagosProgramados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_pagos_programados', function (Blueprint $table) {
            $table->unsignedBigInteger('pagos_id')->unsigned();
            $table->foreign('pagos_id')->references('id')->on('pagos');
            $table->unsignedBigInteger('pagos_programados_id')->unsigned();
            $table->foreign('pagos_programados_id')->references('id')->on('pagos_programados');
            $table->decimal('monto', 10, 2);
            $table->unsignedTinyInteger('movimientos_pagos_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos_pagos_programados');
    }
}
