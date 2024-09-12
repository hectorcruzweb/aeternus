<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosFunerariaCementerioFiltroField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //1 => funeraria, 2=>cementerio
        Schema::table('usuarios', function (Blueprint $table) {
            $table->tinyInteger('cementerio_funeraria_filtro')->after('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios', function ($table) {
            $table->dropColumn('cementerio_funeraria_filtro');
        });
    }
}
