<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoPropiedadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_propiedades', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('tipo');
            $table->string('descripcion');
            $table->tinyInteger('capacidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('tipo_propiedades');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}