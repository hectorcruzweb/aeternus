<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaqueteArticulos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paquete_articulos', function (Blueprint $table) {
            $table->integer('cantidad');
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paquete_articulos');
    }
}