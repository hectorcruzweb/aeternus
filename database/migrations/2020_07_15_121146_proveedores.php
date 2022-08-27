<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Proveedores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_comercial')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('rfc')->nullable()->unique();
            $table->string('nombre_contacto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
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
        Schema::dropIfExists('proveedores');
    }
}