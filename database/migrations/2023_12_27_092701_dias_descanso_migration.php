<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DiasDescansoMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dias_descanso', function (Blueprint $table) {
            $table->dateTime('fecha_aplicacion');
            $table->tinyInteger('dias_id');
            $table->unsignedBigInteger('usuarios_id')->unsigned()->nullable();
            $table->foreign('usuarios_id')->references('id')->on('usuarios');
        });

        Schema::create('dias_festivos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('num_dia');
            $table->string('num_mes');
            $table->string('festejo')->nullable();
            $table->tinyInteger('descanso_obligatorio_b');
        });

        $dias_festivos = [
            [ //01 de enero
                "num_dia" => "01",
                "num_mes" => "01",
                "festejo" => "Año nuevo",
                "descanso_obligatorio_b" => 1
            ],
            [ //6 de febrero: El Día de la Constitución Mexicana
                "num_dia" => "06",
                "num_mes" => "02",
                "festejo" => "Día de la Constitución Mexicana",
                "descanso_obligatorio_b" => 1
            ],
            [ // 20 de marzo: Natalicio de Benito Juárez
                "num_dia" => "20",
                "num_mes" => "03",
                "festejo" => "Natalicio de Benito Juárez",
                "descanso_obligatorio_b" => 1
            ],
            [ // 20 de marzo: Día internacional del trabajo
                "num_dia" => "01",
                "num_mes" => "05",
                "festejo" => "Día internacional del trabajo",
                "descanso_obligatorio_b" => 1
            ],
            [ // 16 de septiembre: Día de la independencia de México
                "num_dia" => "16",
                "num_mes" => "09",
                "festejo" => "Día de la independencia de México",
                "descanso_obligatorio_b" => 1
            ],
            [ // 20 de noviembre: Aniversario de la Revolución Mexicana 
                "num_dia" => "20",
                "num_mes" => "11",
                "festejo" => "Aniversario de la Revolución Mexicana",
                "descanso_obligatorio_b" => 1
            ],
            [ // 25 de diciembre: La celebración de la navidad 
                "num_dia" => "25",
                "num_mes" => "12",
                "festejo" => "Celebración de la navidad",
                "descanso_obligatorio_b" => 1
            ]
        ];
        foreach ($dias_festivos as $key => $dia) {
            DB::table('dias_festivos')->insert(
                [
                    [
                        'num_dia' => $dia["num_dia"],
                        'num_mes' => $dia["num_mes"],
                        'festejo' => $dia["festejo"],
                        'descanso_obligatorio_b' => $dia["descanso_obligatorio_b"],
                    ]
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dias_descanso');
        Schema::dropIfExists('dias_festivos');
    }
}
