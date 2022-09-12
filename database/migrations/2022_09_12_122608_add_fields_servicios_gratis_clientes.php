<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsServiciosGratisClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function ($table) {
            $table->integer('servicios_gratis')->default(0)->after('vivo_b');
            $table->longText('nota_servicios_gratis')->nullable()->after('vivo_b');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function ($table) {
            $table->dropColumn('servicios_gratis');
            $table->dropColumn('nota_servicios_gratis');
        });
    }
}
