<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsConveniosStatusMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //campos para loas tablas de terrenos y venta de planeas a futuro
        Schema::table('ventas_terrenos', function ($table) {
            $table->tinyInteger('status_convenio')->default(0)->after('tipo_financiamiento');
            $table->longText('nota_convenio')->nullable()->after('tipo_financiamiento');
            $table->dateTime('fecha_registro_convenio')->nullable()->after('tipo_financiamiento');
            $table->unsignedBigInteger('registro_id_convenio')->unsigned()->nullable()->after('tipo_financiamiento');
            $table->foreign('registro_id_convenio')->references('id')->on('usuarios');
        });
        Schema::table('ventas_planes', function ($table) {
            $table->tinyInteger('status_convenio')->default(0)->after('nota_original_ingles');
            $table->longText('nota_convenio')->nullable()->after('nota_original_ingles');
            $table->dateTime('fecha_registro_convenio')->nullable()->after('nota_original_ingles');
            $table->unsignedBigInteger('registro_id_convenio')->unsigned()->nullable()->after('nota_original_ingles');
            $table->foreign('registro_id_convenio')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas_terrenos', function ($table) {
            $table->dropColumn('status_convenio');
            $table->dropColumn('nota_convenio');
            $table->dropColumn('fecha_registro_convenio');
            $table->dropForeign('ventas_terrenos_registro_id_convenio_foreign');
            $table->dropColumn('registro_id_convenio');
        });

        Schema::table('ventas_planes', function ($table) {
            $table->dropColumn('status_convenio');
            $table->dropColumn('nota_convenio');
            $table->dropColumn('fecha_registro_convenio');
            $table->dropForeign('ventas_planes_registro_id_convenio_foreign');
            $table->dropColumn('registro_id_convenio');
        });
    }
}
