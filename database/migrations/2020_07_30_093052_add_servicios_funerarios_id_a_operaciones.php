<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiciosFunerariosIdAOperaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('servicios_funerarios_id')->unsigned()->nullable()->after('ventas_planes_id');
            $table->foreign('servicios_funerarios_id')->references('id')->on('servicios_funerarios');
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
            $table->dropForeign('operaciones_servicios_funerarios_id_foreign');
            $table->dropColumn('servicios_funerarios_id');
        });
    }
}