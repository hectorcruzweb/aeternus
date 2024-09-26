<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasGeneralesMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas_generales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('entregado_b')->default(1);//1 => si, 0 => no
            $table->dateTime('fechahora_entrega')->nullable();
            $table->unsignedBigInteger('entrego_id')->unsigned()->nullable();
            $table->foreign('entrego_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('vendedores_id')->unsigned();
            $table->foreign('vendedores_id')->references('id')->on('usuarios');
        });

        //ventas_generales en operaciones
        Schema::table('operaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('ventas_generales_id')->unsigned()->after('cuotas_cementerio_id')->nullable();
            $table->foreign('ventas_generales_id')->references('id')->on('ventas_generales');
        });

        Schema::create('articulos_venta_general_temporal', function (Blueprint $table) {
            $table->integer('cantidad');
            $table->unsignedBigInteger('articulos_id')->unsigned();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->unsignedDecimal('costo_neto_normal', 10, 2);
            $table->unsignedDecimal('costo_neto_descuento', 10, 2);
            $table->tinyInteger('descuento_b')->default(0);//1 => si, 0 => no
            $table->tinyInteger('facturable_b')->default(0);//1 => si, 0 => no
            $table->unsignedBigInteger('operaciones_id');
            $table->foreign('operaciones_id')->references('id')->on('operaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //quitando el campo de ventas_genrales_id en operaciones
        Schema::table('operaciones', function ($table) {
            $table->dropForeign(['ventas_generales_id']);
            $table->dropColumn('ventas_generales_id');
        });
        //quitando tabla de ventas_generales
        Schema::dropIfExists('ventas_generales');
        //quitando tabla de articulos_venta_general_temporal
        Schema::dropIfExists('articulos_venta_general_temporal');
    }
}
