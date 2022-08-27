<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlanConceptos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_conceptos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('seccion_id')->unsigned();
            $table->string('concepto');
            $table->string('concepto_ingles');
            $table->unsignedBigInteger('planes_funerarios_id')->unsigned()->nullable();
            $table->foreign('planes_funerarios_id')->references('id')->on('planes_funerarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_conceptos');
    }
}