<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddContratanteTemporalServiciosFunerarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servicios_funerarios', function ($table) {
            $table->string('nombre_contratante_temp')->nullable()->after('parentesco_contratante');
            $table->string('telefono_contratante_temp')->nullable()->after('parentesco_contratante');
            $table->string('parentesco_contratante_temp')->nullable()->after('parentesco_contratante');
            $table->string('direccion_contratante_temp')->nullable()->after('parentesco_contratante');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servicios_funerarios', function ($table) {
            $table->dropColumn('nombre_contratante_temp');
            $table->dropColumn('telefono_contratante_temp');
            $table->dropColumn('parentesco_contratante_temp');
            $table->dropColumn('direccion_contratante_temp');
        });

    }
}
