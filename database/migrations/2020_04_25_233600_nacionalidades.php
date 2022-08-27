<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Nacionalidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nacionalidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pais', 75)->nullable();
            $table->string('nacionalidad', 75)->nullable();
            $table->string('iso_nac', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nacionalidades');
    }
}