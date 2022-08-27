<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCementeriosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cementerios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('funeraria_id')->nullable();
            $table->foreign('funeraria_id')->references('id')->on('funeraria');
            $table->string('cementerio');
            $table->string('calle');
            $table->string('num_ext');
            $table->string('num_int')->nullable();
            $table->string('email')->nullable();
            $table->string('colonia');
            $table->string('cp')->default(0)->nullable();
            $table->string('ciudad');
            $table->string('estado');
            $table->string('telefono');
            /**horarios */
            $table->string('hora_apertura');
            $table->string('hora_cierre');
            /**horarios */
            /**pagos de mantenimiento */
            $table->double('salario_minimo');
            $table->tinyInteger('numero_salarios');
            $table->tinyInteger('mes_maximo_pago');
            $table->tinyInteger('dia_maximo_pago');
            /**pagos de mantenimiento */
            $table->string('fax')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cementerios');
    }
}