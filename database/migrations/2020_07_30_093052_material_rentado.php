<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MaterialRentado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_rentado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('servicios_funerarios_id')->unsigned()->nullable();
            $table->foreign('servicios_funerarios_id')->references('id')->on('servicios_funerarios');
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->integer('cantidad')->nullable();
            $table->string('nota')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_rentado');
    }
}
