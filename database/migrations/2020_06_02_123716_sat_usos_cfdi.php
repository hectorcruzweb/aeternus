<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SatUsosCfdi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_usos_cfdi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave')->nullable();
            $table->string('uso')->nullable();
            $table->tinyInteger('aplica_b');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sat_usos_cfdi');
    }
}