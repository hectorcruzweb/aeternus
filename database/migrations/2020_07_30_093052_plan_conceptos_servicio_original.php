<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlanConceptosServicioOriginal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_conceptos_servicio_original', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('seccion_id')->unsigned();
            $table->unsignedBigInteger('servicios_funerarios_id')->unsigned()->nullable();
            $table->foreign('servicios_funerarios_id')->references('id')->on('servicios_funerarios');
            $table->string('concepto');
            $table->string('concepto_ingles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_conceptos_servicio_original');
    }
}
