<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlanConceptosOriginal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_conceptos_original', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('seccion_id')->unsigned();
            $table->unsignedBigInteger('ventas_planes_id')->unsigned()->nullable();
            $table->foreign('ventas_planes_id')->references('id')->on('ventas_planes');
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
        Schema::dropIfExists('plan_conceptos_original');
    }
}