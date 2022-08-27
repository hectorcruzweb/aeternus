<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsuariosPuestos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_puestos', function (Blueprint $table) {
            $table->unsignedBigInteger('usuarios_id')->nullable();
            $table->foreign('usuarios_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('puestos_id')->nullable();
            $table->foreign('puestos_id')->references('id')->on('puestos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_puestos');
    }
}