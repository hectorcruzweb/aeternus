<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConceptosCfdi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conceptos_cfdi', function (Blueprint $table) {
            $table->unsignedBigInteger('cfdis_id')->unsigned();
            $table->foreign('cfdis_id')->references('id')->on('cfdis');
            $table->unsignedBigInteger('sat_productos_servicios_id')->unsigned();
            $table->foreign('sat_productos_servicios_id')->references('id')->on('sat_productos_servicios');
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->unsignedDecimal('cantidad', 10, 2);
            $table->string('descripcion', 150);
            $table->unsignedDecimal('valor_unitario', 10, 2);
            $table->unsignedDecimal('importe', 10, 2);
            $table->unsignedDecimal('descuento', 10, 2);
            $table->unsignedBigInteger('sat_unidades_id')->unsigned();
            $table->foreign('sat_unidades_id')->references('id')->on('sat_unidades');
            $table->unsignedBigInteger('concepto_operacion_id')->nullable();
            $table->foreign('concepto_operacion_id')->references('id')->on('operaciones');
            $table->tinyInteger('concepto_operacion_ver_b')->unsigned();
            $table->tinyInteger('modifica_b')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conceptos_cfdi');
    }
}
