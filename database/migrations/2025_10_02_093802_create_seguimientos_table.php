<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeguimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->bigIncrements('id'); // primary key

            $table->boolean('programado_b')->default(false);
            $table->tinyInteger('tipo_cliente_id')->unsigned(); // 1 or 2
            $table->unsignedBigInteger('clientes_id'); // required
            $table->unsignedBigInteger('operaciones_id')->nullable(); // optional

            $table->dateTime('fechahora_programada')->nullable();
            $table->tinyInteger('motivo_id')->unsigned()->nullable(); // 1-10
            $table->tinyInteger('medio_preferido_programado_id')->unsigned()->nullable(); // 1-6
            $table->text('comentario_programado')->nullable();
            $table->dateTime('fechahora_registro_programado')->nullable();
            $table->unsignedBigInteger('registro_programado_id')->nullable(); // usuarios.id
            $table->unsignedBigInteger('modifico_programado_id')->nullable(); // usuarios.id
            $table->string('email_programado')->nullable();

            $table->dateTime('fechahora_seguimiento')->nullable();
            $table->tinyInteger('resultado_id')->unsigned()->nullable(); // 1-6
            $table->tinyInteger('medio_seguimiento_id')->unsigned()->nullable(); // 1-6
            $table->string('email_seguimiento')->nullable();
            $table->text('comentario_seguimiento')->nullable();
            $table->unsignedBigInteger('realizo_seguimiento_id')->nullable(); // usuarios.id
            $table->dateTime('fechahora_registro_seguimiento')->nullable();
            $table->unsignedBigInteger('modifico_seguimiento_id')->nullable(); // usuarios.id

            $table->dateTime('fechahora_cancelacion_programado')->nullable();
            $table->dateTime('fechahora_cancelacion_realizado')->nullable();
            $table->unsignedBigInteger('cancelo_programado_id')->nullable(); // usuarios.id
            $table->unsignedBigInteger('cancelo_realizado_id')->nullable(); // usuarios.id
            $table->tinyInteger('motivo_cancelacion_id')->unsigned()->nullable(); // 1-10
            $table->text('comentario_cancelacion')->nullable();

            $table->tinyInteger('status')->default(1);

            // Foreign keys
            $table->foreign('clientes_id')->references('id')->on('clientes')->onDelete('restrict');
            $table->foreign('operaciones_id')->references('id')->on('operaciones')->onDelete('set null');
            $table->foreign('registro_programado_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('realizo_seguimiento_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('modifico_seguimiento_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('modifico_programado_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('cancelo_programado_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('cancelo_realizado_id')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seguimientos');
    }
}
