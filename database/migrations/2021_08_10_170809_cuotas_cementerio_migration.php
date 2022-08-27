<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CuotasCementerioMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuotas_cementerio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion')->nullable();
            $table->unsignedDecimal('cuota_total', 10, 2);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->dateTime('fechahora_registro')->nullable();
            $table->tinyInteger('tasa_iva');
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('modifico_id')->unsigned()->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_cancelacion')->nullable();
            $table->tinyInteger('status')->default(1);
        });
        /**Agregando campo de cuotas_cementerio_id a operaciones*/
        Schema::table('operaciones', function ($table) {
            $table->unsignedBigInteger('cuotas_cementerio_id')->unsigned()->nullable()->after('servicios_funerarios_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuotas_cementerio');
        Schema::table('operaciones', function ($table) {
            $table->dropColumn('cuotas_cementerio_id');
        });
    }
}
