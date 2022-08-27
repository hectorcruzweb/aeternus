<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SatProductosServicios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_productos_servicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave')->nullable()->unique();
            $table->string('descripcion')->nullable();
            $table->tinyInteger('mostrar_b')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sat_productos_servicios');
    }
}