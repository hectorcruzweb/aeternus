<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicosLegistasEmbalsamadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::create('medicos_legistas_embalsamadores', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nombre')->nullable();
                $table->tinyInteger('tipo_persona')->default(1);//1 - medico legista. 2- tecnico embalsamador
                $table->string('cedula')->nullable();
            });
            Schema::table('servicios_funerarios', function ($table) {
                $table->unsignedBigInteger('legista_id')->after('certificado_informante_parentesco')->default(1)->nullable();
                $table->string('cedula_legista')->after('medico_legista')->default("N/A")->nullable();
                $table->unsignedBigInteger('embalsamador_id')->after('embalsamar_b')->default(2)->nullable();
                $table->string('cedula_embalsamador')->after('medico_responsable_embalsamado')->default("N/A")->nullable();
            });
            DB::table('medicos_legistas_embalsamadores')->insert(
                [
                    [
                        'nombre' => "Otro",
                        'tipo_persona' => 1,
                        'cedula' => 'N/A'
                    ],
                    [
                        'nombre' => "Otro",
                        'tipo_persona' => 2,
                        'cedula' => 'N/A'
                    ],
                    //medicos legistas
                    [
                        'nombre' => "Genaro Arnoldo Arredondo Sandoval",
                        'tipo_persona' => 1,
                        'cedula' => '901788'
                    ],
                    [
                        'nombre' => "Javier Octavio Dojaquez González",
                        'tipo_persona' => 1,
                        'cedula' => '3670628'
                    ],
                    [
                        'nombre' => "David Guerrero Hernández",
                        'tipo_persona' => 1,
                        'cedula' => '13043364'
                    ],
                    [
                        'nombre' => "Naysin Yurai Velez Martinez",
                        'tipo_persona' => 1,
                        'cedula' => '7000785'
                    ],
                    [
                        'nombre' => "Carlos Alberto Solano German",
                        'tipo_persona' => 1,
                        'cedula' => '12684744'
                    ],
                    [
                        'nombre' => "Fabián Sánchez Lizarraga",
                        'tipo_persona' => 1,
                        'cedula' => '693311'
                    ],
                    //embsalsamdores

                    [
                        'nombre' => "DAVID RAMIREZ PORTILLO",
                        'tipo_persona' => 2,
                        'cedula' => 'EC-240121'
                    ],
                    [
                        'nombre' => "JOAQUÍN NAZARETH MEJÍA BURGUEÑO",
                        'tipo_persona' => 2,
                        'cedula' => 'EC-220113'
                    ]
                ]
            );
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servicios_funerarios', function ($table) {
            $table->dropColumn('legista_id');
            $table->dropColumn('cedula_legista');
            $table->dropColumn('embalsamador_id');
            $table->dropColumn('cedula_embalsamador');
        });
        Schema::dropIfExists('medicos_legistas_embalsamadores');
    }
}
