<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AjustarComprasInventario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('compra_detalle');
         Schema::create('compra_detalle', function (Blueprint $table) {
            $table->integer('cantidad');
            $table->dateTime('fecha_caducidad')->nullable();
            $table->unsignedDecimal('costo_neto', 10, 2)->nullable();
            $table->unsignedDecimal('costo_neto_descuento', 10, 2)->nullable();
            $table->tinyInteger('descuento_b')->nullable();
            $table->tinyInteger('facturable_b')->nullable();
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->unsignedBigInteger('movimientos_inventario_id')->unsigned()->nullable();
            $table->foreign('movimientos_inventario_id')->references('id')->on('movimientos_inventario');
        });

        Schema::table('movimientos_inventario', function ($table) {
            $table->unsignedDecimal('pago_efectivo', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('pago_cheque', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('pago_transferencia', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('pago_tarjeta', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('pago_credito', 10, 2)->nullable()->after('registro_id');
            $table->date('fecha_vencimiento_credito', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('iva_porcentaje', 10, 2)->nullable()->after('registro_id');
            $table->unsignedBigInteger('num_compra')->nullable()->after('registro_id');
        });

        Schema::create('costos_incurridos', function (Blueprint $table) {
            $table->string('costo_detalle')->nullable();
            $table->unsignedDecimal('costo_neto', 10, 2)->nullable();
            $table->unsignedBigInteger('movimientos_inventario_id')->unsigned()->nullable();
            $table->foreign('movimientos_inventario_id')->references('id')->on('movimientos_inventario');
            $table->tinyInteger('facturable_b')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('movimientos_inventario', function ($table) {
            $table->dropColumn('pago_efectivo');
            $table->dropColumn('pago_cheque');
            $table->dropColumn('pago_transferencia');
            $table->dropColumn('pago_tarjeta');
            $table->dropColumn('pago_credito');
            $table->dropColumn('fecha_vencimiento_credito');
            $table->dropColumn('iva_porcentaje');
            $table->dropColumn('num_compra');
        });

        Schema::dropIfExists('compra_detalle');
        Schema::dropIfExists('costos_incurridos');
    }
}
