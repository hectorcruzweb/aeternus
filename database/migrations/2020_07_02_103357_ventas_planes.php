<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VentasPlanes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas_planes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('tipo_financiamiento')->nullable(); //1 contado(uso inmeadiatoo) 2-credito uso a futuro
            $table->unsignedBigInteger('vendedor_id')->unsigned()->nullable();
            $table->foreign('vendedor_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('planes_funerarios_id')->unsigned()->nullable();
            $table->foreign('planes_funerarios_id')->references('id')->on('usuarios');
            $table->string('nombre_original');
            $table->string('nombre_original_ingles');
            $table->string('nota_original');
            $table->string('nota_original_ingles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas_planes');
    }
}
