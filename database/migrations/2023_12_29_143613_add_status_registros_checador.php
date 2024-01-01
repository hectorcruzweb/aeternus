<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusRegistrosChecador extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registros_checador', function ($table) {
            $table->tinyInteger('status')->after('usuarios_id')->default(1);
            $table->unsignedBigInteger('registro_id')->after('status')->nullable();
            $table->unsignedBigInteger('modifico_id')->after('status')->nullable();
            $table->unsignedBigInteger('cancelo_id')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registros_checador', function ($table) {
            $table->dropColumn('status');
            $table->dropColumn('registro_id');
            $table->dropColumn('modifico_id');
            $table->dropColumn('cancelo_id');
        });
    }
}
