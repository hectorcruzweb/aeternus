<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Operaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('clientes_id');
            $table->foreign('clientes_id')->references('id')->on('clientes');
            /**relacion para la antiguedad de la operacion que aplique */
            /*
            1- NUEVA VENTA
            2- A/S SIN LIQUIDAR
            3- A/S - LIQUIDADA
            */
            $table->tinyInteger('antiguedad_operacion_id')->unsigned()->nullable();
            /**datos genericos de la tabla */
            $table->tinyInteger('empresa_operaciones_id')->nullable();

            $table->unsignedDecimal('subtotal', 10, 2)->nullable();
            $table->unsignedDecimal('descuento', 10, 2)->nullable();
            $table->unsignedDecimal('impuestos', 10, 2)->nullable();
            $table->unsignedDecimal('total', 10, 2)->nullable();

            $table->tinyInteger('descuento_pronto_pago_b')->nullable()->default(0);
            $table->unsignedDecimal('costo_neto_pronto_pago', 10, 2)->nullable();

            /**solo para aquellos que aplica, venta de terrenos y venta de planes a futuro */
            $table->string('numero_solicitud', 35)->nullable();
            $table->string('numero_convenio', 35)->nullable();
            $table->string('numero_titulo', 35)->nullable();
            /**titular sustituto */
            $table->string('titular_sustituto')->nullable();
            $table->string('parentesco_titular_sustituto')->nullable();
            $table->string('telefono_titular_sustituto')->nullable();
            /** */

            /***aplica solo para servicios funerarios */
            //$table->unsignedBigInteger('servicios_funerarios_id')->unsigned()->nullable();
            //$table->foreign('servicios_funerarios_id')->references('id')->on('servicios_funerarios');
            /***aplica solo para venta de terrenos */
            $table->unsignedBigInteger('ventas_terrenos_id')->unsigned()->nullable();
            $table->foreign('ventas_terrenos_id')->references('id')->on('ventas_terrenos');
            /***aplica solo para venta de planes a futuro */
            //$table->unsignedBigInteger('ventas_planes_id')->unsigned()->nullable();
            //$table->foreign('ventas_planes_id')->references('id')->on('ventas_planes');
            /***aplica solo para ventas generles */
            //$table->unsignedBigInteger('ventas_generales_id')->unsigned()->nullable();
            //$table->foreign('ventas_generales_id')->references('id')->on('ventas_generales');

            /***aplica solo para anualidades del cmenterio */
            //$table->unsignedBigInteger('anualidades_cementerio_id')->unsigned()->nullable();
            //$table->foreign('anualidades_cementerio_id')->references('id')->on('anualidades_cementerio');

            /***aplica solo para operaciones de cobranza de cuotas del cementerio */
            //$table->unsignedBigInteger('terrenos_cuotas_id')->unsigned()->nullable();
            //$table->foreign('terrenos_cuotas_id')->references('id')->on('terrenos_cuotas');

            /**este puede ir desde el 1, hasta 64,  1 para operaciones de contado o programdos para un solo pago, y cuando es mayor a 1
             * se tomara como operacion a meses, con mas de un pago programado
             */
            $table->tinyInteger('financiamiento');
            $table->tinyInteger('aplica_devolucion_b');
            /**se guarda el precio neto de la operacion normal al momenot de la operacion solo como futuras referencias */
            $table->unsignedDecimal('costo_neto_financiamiento_normal', 10, 2)->nullable();
            /**status de la operacion */
            /**este se le asigna un valor numerico 1,2,3,0 segun la interpretacion en cada tipo de operacion */
            $table->unsignedDecimal('comision_venta_neto', 10, 2)->nullable();

            $table->dateTime('fecha_operacion');
            $table->dateTime('fecha_registro');
            $table->dateTime('fecha_modificacion')->nullable();
            $table->unsignedBigInteger('modifico_id')->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');

            /**fin de datos en caso de cancelacion */
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->mediumText('nota')->nullable();
            $table->tinyInteger('motivos_cancelacion_id')->nullable();
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->unsignedDecimal('cantidad_a_regresar_cancelacion', 10, 2)->nullable();
            $table->unsignedBigInteger('cancelo_id')->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->mediumText('nota_cancelacion')->nullable();
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
        Schema::dropIfExists('operaciones');
    }
}