<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIvaPorcentajeOperaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operaciones', function (Blueprint $table) {
            $table->tinyInteger('tasa_iva')->unsigned()->nullable()->after('impuestos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operaciones', function (Blueprint $table) {
            $table->dropColumn('tasa_iva');
        });
    }
}
