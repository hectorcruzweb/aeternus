<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //el 28 de feb de 2020 agregue los campos para el control de los datos de empleado
        //(celular, calle. colonia, cp y datos de contacto)
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('email')->unique();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('genero');
            $table->longText('imagen')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('nombre_contacto')->nullable();
            $table->string('tel_contacto')->nullable();
            $table->string('parentesco')->nullable();
            $table->dateTime('fecha_alta')->nullable();
            $table->dateTime('fecha_baja')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->unsignedInteger('roles_id');
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('usuarios');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}