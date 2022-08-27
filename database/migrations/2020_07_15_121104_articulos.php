<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Articulos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo_barras')->nullable()->unique();
            $table->string('descripcion')->nullable();
            $table->string('descripcion_ingles')->nullable();
            $table->longText('imagen')->nullable();
            $table->unsignedDecimal('precio_compra', 10, 2);
            $table->unsignedDecimal('precio_venta', 10, 2);
            $table->integer('minimo');
            $table->integer('maximo');
            $table->string('localizacion')->nullable();
            $table->tinyInteger('caduca_b');
            $table->tinyInteger('grava_iva_b');
            $table->unsignedBigInteger('categorias_id')->unsigned()->nullable();
            $table->foreign('categorias_id')->references('id')->on('categorias');
            $table->unsignedBigInteger('sat_unidades_compra')->unsigned()->nullable();
            $table->foreign('sat_unidades_compra')->references('id')->on('sat_unidades');
            $table->unsignedBigInteger('sat_unidades_venta')->unsigned()->nullable();
            $table->foreign('sat_unidades_venta')->references('id')->on('sat_unidades');
            $table->integer('factor');
            $table->unsignedBigInteger('sat_productos_servicios_id')->unsigned()->nullable();
            $table->foreign('sat_productos_servicios_id')->references('id')->on('sat_productos_servicios');
            $table->unsignedBigInteger('tipo_articulos_id')->unsigned()->nullable();
            $table->foreign('tipo_articulos_id')->references('id')->on('tipo_articulos');
            $table->mediumText('nota')->nullable();
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
        Schema::dropIfExists('articulos');
    }
}