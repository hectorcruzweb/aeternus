<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVentasPlanesIdCampoToOperaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('ventas_planes_id')->unsigned()->nullable()->after('ventas_terrenos_id');
            $table->foreign('ventas_planes_id')->references('id')->on('ventas_planes');
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
            $table->dropForeign('operaciones_ventas_planes_id_foreign');
            $table->dropColumn('ventas_planes_id');
        });
    }
}