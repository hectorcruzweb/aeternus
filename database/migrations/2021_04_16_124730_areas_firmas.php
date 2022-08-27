<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AreasFirmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('areas_firmas', function (Blueprint $table) {
         $table->bigIncrements('id');
         $table->unsignedBigInteger('documentos_id')->unsigned()->nullable();
         $table->foreign('documentos_id')->references('id')->on('documentos');
         $table->string('area')->nullable();
         $table->tinyInteger('status')->default(1);
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists('areas_firmas');
    }
}
