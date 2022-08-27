<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('modulo')->comment('nombre del modulo o del grupo de modulos');
            $table->string('icon')->nullable()->comment('nombre del codigo usado para ientificar el modulo');
            $table->integer('parent_modulo_id')->nullable()->comment('debera ser nulo en caso de ser un modulo independiente');
            $table->string('url')->nullable()->comment('url del modulo');
            $table->unsignedInteger('secciones_id');
            $table->tinyInteger('status')->nullable()->default(1); //para decidir si se va a ver en produccion 1- si 0 -no y solo se vera duranre desarrollo por el super usuario
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulos');
    }
}