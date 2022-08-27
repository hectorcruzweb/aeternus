<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlanesFunerarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planes_funerarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('plan');
            $table->string('plan_ingles');
            $table->dateTime('fecha_registro');
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->mediumText('nota')->nullable();
            $table->mediumText('nota_ingles')->nullable();
            $table->unsignedBigInteger('modifico_id')->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_modificacion')->nullable();
            $table->unsignedBigInteger('cancelo_id')->nullable();
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
        Schema::dropIfExists('planes_funerarios');
    }
}