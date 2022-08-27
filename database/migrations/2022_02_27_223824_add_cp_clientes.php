<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCpClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function ($table) {
            $table->string('cp')->nullable()->after('direccion_fiscal');
            $table->unsignedBigInteger('regimen_fiscal_id')->unsigned()->nullable()->after('direccion_fiscal');
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
            $table->dropColumn('cp');
            $table->dropColumn('regimen_fiscal_id');
        });
    }
}
