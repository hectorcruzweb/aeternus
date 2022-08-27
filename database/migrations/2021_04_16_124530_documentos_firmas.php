<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DocumentosFirmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('documentos', function (Blueprint $table) {
         $table->bigIncrements('id');
         $table->unsignedBigInteger('tipo_documentos_id')->unsigned()->nullable();
         $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
         $table->string('documento')->nullable();
         $table->string('descripcion')->nullable();
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
         Schema::dropIfExists('documentos');
    }
}
