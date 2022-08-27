<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RolesPermisos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_permisos', function (Blueprint $table) {
            $table->unsignedBigInteger('roles_id')->nullable();
            $table->foreign('roles_id')->references('id')->on('roles');
            $table->unsignedBigInteger('permisos_id')->nullable();
            $table->foreign('permisos_id')->references('id')->on('permisos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_permisos');
    }
}