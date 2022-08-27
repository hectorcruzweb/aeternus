<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LugaresVelacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*1- Sin Velacion
        2- Domicilio Particular
        3- Sala la piedad
        4- Sala misericordia
        5- Sala Resureccion*/
        Schema::create('lugares_velacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lugar')->nullable();
            $table->unsignedBigInteger('funeraria_id')->nullable();
            $table->foreign('funeraria_id')->references('id')->on('funeraria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lugares_velacion');
    }
}