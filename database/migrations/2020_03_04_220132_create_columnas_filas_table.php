<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnasFilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('columnas_filas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('fila');
            $table->integer('empieza_columna');
            $table->integer('fin_columna');
            $table->integer('propiedades_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('columnas_filas');
    }
}