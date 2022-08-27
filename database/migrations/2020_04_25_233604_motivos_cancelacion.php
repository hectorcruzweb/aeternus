<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MotivosCancelacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 1- falta de pago
         * 2- a peticion del cliente
         * 3- error de captura
         * 4- Otro
         */
        Schema::create('motivos_cancelacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('motivo', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motivos_cancelacion');
    }
}