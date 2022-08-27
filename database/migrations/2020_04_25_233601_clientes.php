<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Clientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('generos_id')->desciption('define hombre o mujer');
            $table->foreign('generos_id')->references('id')->on('generos');
            $table->string('nombre', 100)->nullable()->description('nombre completo del cliente');
            $table->string('direccion', 175)->nullable()->description('direccion completa del cliente');
            $table->string('ciudad', 75)->nullable();
            $table->string('estado', 75)->nullable();
            $table->date('fecha_nac')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('celular', 50)->nullable();
            $table->string('telefono_extra', 50)->nullable();
            $table->string('email', 75)->nullable();
            $table->unsignedBigInteger('nacionalidades_id')->desciption('define la nacionalidad del cliente');
            $table->foreign('nacionalidades_id')->references('id')->on('nacionalidades');
            $table->string('nombre_contacto', 100)->nullable()->description('nombre de algun contacto');
            $table->string('parentesco_contacto', 100)->nullable()->description('parentescon con el contacto');
            $table->string('telefono_contacto', 50)->nullable()->description('algun telefono de dicho contacto');
            $table->string('rfc', 13)->nullable()->description('rfc en caso de requerir facturas');
            $table->string('razon_social', 150)->nullable()->description('razon social del rfc');
            $table->string('direccion_fiscal', 175)->nullable()->description('direccion fiscal del registro del rfc');
            $table->dateTime('fecha_registro')->nullable()->description('fecha de ingreso en la bd');
            $table->dateTime('fecha_modificacion')->nullable()->description('fecha de la ultima modifcacion en la bd');
            $table->dateTime('fecha_cancelacion')->nullable()->description('fecha en que se cancelo en la bd');
            $table->unsignedBigInteger('registro_id');
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('cancelo_id')->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('modifico_id')->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->longText('nota')->nullable()->description('algun comentario sobre el cliente');
            $table->tinyInteger('vivo_b')->default(1)->description('1, vivo - 0, fallecido');
            $table->tinyInteger('status')->default(1)->description('1, activo - 0, baja');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}