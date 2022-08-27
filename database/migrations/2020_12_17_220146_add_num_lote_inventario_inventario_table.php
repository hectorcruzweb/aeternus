<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddNumLoteInventarioInventarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventario', function ($table) {
            $table->integer('num_lote_inventario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventario', function ($table) {
            $table->dropColumn('num_lote_inventario');
        });

    }
}
