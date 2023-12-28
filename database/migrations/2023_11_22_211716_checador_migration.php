<?php

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChecadorMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::beginTransaction();

            Schema::create('usuarios_huellas', function (Blueprint $table) {
                $table->tinyInteger('huellas_id');
                $table->unsignedBigInteger('usuarios_id')->unsigned()->nullable();
                $table->foreign('usuarios_id')->references('id')->on('usuarios');
            });
            DB::statement("ALTER TABLE usuarios_huellas ADD huella LONGBLOB");
            Schema::create('configuracion_checador', function (Blueprint $table) {
                $table->increments('id');
                $table->string('configuracion')->nullable();
            });
            Schema::create('usuarios_permisos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->tinyInteger('tipo_permiso_id');
                $table->tinyInteger('accion_permiso_id');
                $table->dateTime('fecha_inicio');
                $table->dateTime('fecha_fin');
                $table->longText('nota')->nullable();
                $table->unsignedBigInteger('usuarios_id')->unsigned()->nullable();
                $table->foreign('usuarios_id')->references('id')->on('usuarios');
            });
            Schema::create('horarios', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->tinyInteger('tipo_horario_id');
                $table->string('descripcion');
                $table->date('fecha_aplicacion');
                $table->unsignedBigInteger('usuarios_id')->unsigned()->nullable();
                $table->foreign('usuarios_id')->references('id')->on('usuarios');
            });
            Schema::create('dias_horario', function (Blueprint $table) {
                $table->tinyInteger('semana_numero');
                $table->tinyInteger('dia_laboral_b');
                $table->tinyInteger('dia_entrada');
                $table->time('hora_entrada');
                $table->tinyInteger('dia_salida');
                $table->time('hora_salida');
                $table->tinyInteger('comida_b');
                $table->time('dia_salida_comida')->nullable();
                $table->tinyInteger('hora_salida_comida')->nullable();
                $table->time('dia_entrada_comida')->nullable();
                $table->tinyInteger('hora_entrada_comida')->nullable();
                $table->unsignedBigInteger('horarios_id')->unsigned()->nullable();
                $table->foreign('horarios_id')->references('id')->on('horarios');
            });
            Schema::create('registros_checador', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->tinyInteger('tipo_registro_id');
                $table->dateTime('fecha_hora');
                $table->tinyInteger('registro_huella_b');
                $table->unsignedBigInteger('usuarios_id')->unsigned();
                $table->foreign('usuarios_id')->references('id')->on('usuarios');
            });
            //creando horarios
            $horarios = [
                [
                    "tipo_horario_id" => 1,
                    "descripcion" => "Horario Matutino Lunes a Sábado de 6 AM a 2 PM sin horario de comida (Descanso el Domingo).",
                    "fecha_aplicacion" => now(),
                    "dias_horario" => [
                        [ //lunes
                            "semana_numero" => 1,
                            "dia_laboral_b" => 1,
                            "dia_entrada" => 1,
                            "hora_entrada" => "06:00:00",
                            "dia_salida" => 1,
                            "hora_salida" => "14:00:00",
                            "comida_b" => 0,
                            "dia_salida_comida" => null,
                            "hora_salida_comida" => null,
                            "dia_entrada_comida" => null,
                            "hora_entrada_comida" => null
                        ],
                        [ //martes
                            "semana_numero" => 1,
                            "dia_laboral_b" => 1,
                            "dia_entrada" => 2,
                            "hora_entrada" => "06:00:00",
                            "dia_salida" => 2,
                            "hora_salida" => "14:00:00",
                            "comida_b" => 0,
                            "dia_salida_comida" => null,
                            "hora_salida_comida" => null,
                            "dia_entrada_comida" => null,
                            "hora_entrada_comida" => null
                        ],
                        [ //miercoles
                            "semana_numero" => 1,
                            "dia_laboral_b" => 1,
                            "dia_entrada" => 3,
                            "hora_entrada" => "06:00:00",
                            "dia_salida" => 3,
                            "hora_salida" => "14:00:00",
                            "comida_b" => 0,
                            "dia_salida_comida" => null,
                            "hora_salida_comida" => null,
                            "dia_entrada_comida" => null,
                            "hora_entrada_comida" => null
                        ],
                        [ //jueves
                            "semana_numero" => 1,
                            "dia_laboral_b" => 1,
                            "dia_entrada" => 4,
                            "hora_entrada" => "06:00:00",
                            "dia_salida" => 4,
                            "hora_salida" => "14:00:00",
                            "comida_b" => 0,
                            "dia_salida_comida" => null,
                            "hora_salida_comida" => null,
                            "dia_entrada_comida" => null,
                            "hora_entrada_comida" => null
                        ],
                        [ //viernes
                            "semana_numero" => 1,
                            "dia_laboral_b" => 1,
                            "dia_entrada" => 5,
                            "hora_entrada" => "06:00:00",
                            "dia_salida" => 5,
                            "hora_salida" => "14:00:00",
                            "comida_b" => 0,
                            "dia_salida_comida" => null,
                            "hora_salida_comida" => null,
                            "dia_entrada_comida" => null,
                            "hora_entrada_comida" => null
                        ],
                        [ //sabado
                            "semana_numero" => 1,
                            "dia_laboral_b" => 1,
                            "dia_entrada" => 6,
                            "hora_entrada" => "06:00:00",
                            "dia_salida" => 6,
                            "hora_salida" => "14:00:00",
                            "comida_b" => 0,
                            "dia_salida_comida" => null,
                            "hora_salida_comida" => null,
                            "dia_entrada_comida" => null,
                            "hora_entrada_comida" => null
                        ], [ //domingo
                            "semana_numero" => 1,
                            "dia_laboral_b" => 0,
                            "dia_entrada" => 7,
                            "hora_entrada" => "06:00:00",
                            "dia_salida" => 7,
                            "hora_salida" => "14:00:00",
                            "comida_b" => 0,
                            "dia_salida_comida" => null,
                            "hora_salida_comida" => null,
                            "dia_entrada_comida" => null,
                            "hora_entrada_comida" => null
                        ]
                    ]
                ]
            ];
            $users = User::get();
            foreach ($users as $key => $user) {
                if ($user->id == 1 || $user->status == 0) {
                    //al admin gral del sistema no se le ingresa horario
                    continue;
                }
                $horario = DB::table('horarios')->insert(
                    [
                        [
                            'tipo_horario_id' => 1,
                            'descripcion' => $horarios[0]["descripcion"],
                            'fecha_aplicacion' => $horarios[0]["fecha_aplicacion"],
                            'usuarios_id' => $user->id
                        ]
                    ]
                );

                $horarioId = DB::getPdo()->lastInsertId();


                foreach ($horarios[0]["dias_horario"] as $key => $dia) {
                    DB::table('dias_horario')->insert(
                        [
                            [
                                "semana_numero" => $dia["semana_numero"],
                                "dia_laboral_b" => $dia["dia_laboral_b"],
                                "dia_entrada" => $dia["dia_entrada"],
                                "hora_entrada" => $dia["hora_entrada"],
                                "dia_salida" => $dia["dia_salida"],
                                "hora_salida" => $dia["hora_salida"],
                                "comida_b" => $dia["comida_b"],
                                "dia_salida_comida" => $dia["dia_salida_comida"],
                                "hora_salida_comida" => $dia["hora_salida_comida"],
                                "dia_entrada_comida" => $dia["dia_entrada_comida"],
                                "hora_entrada_comida" => $dia["hora_entrada_comida"],
                                'horarios_id' => $horarioId
                            ]
                        ]
                    );
                }
            }
            //seeder express
            DB::table('usuarios_huellas')->insert(
                [
                    [
                        'huellas_id' => 1,
                        'huella' => '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',
                        'usuarios_id' => 1,
                    ],
                    [
                        'huellas_id' => 2,
                        'huella' => 'APiBAcgq43NcwEE3Catx8JkUVZJI0A4U8VJDDjSb/yYrnt45Euf0Na1RiPTVJU6sYfC4lI5ZNeiTqaYWksTE0tZjEcd2mzPedQKwTiqtJp5Iz7zWYw2UFWFORxeGQU3bh3iTfUAf7S+PBGcLYJCp0rLmAYeaj72f8p2Qns1yJVHu6CiTdbjFTR8mXUObwVuTG+Vf4lkeLoCMBQu2CsVQCWLl+Iw53xlVun5yy5fdiEmUTXYNmf0LUupsPm2FWiI0If8Hk801oXKdT9Uq1sEpWMS5MN6Hy2u61+jLGSIy6P8iB2uUElrGcz+BCvkRpDERoFY9WjN4NDER+iEVNFX+NMoV3Y5382J30pULtaTcRXfA7WjA8pxToM4SRz9YTFK8ZxMYYYyl1yI6GIhb1FgWuBxRUleFHyRngzMWU+jJae6qxN1g9pskx+mb1LqS1TP/neDSuwrxHHLO4/pn28FvbTyCkZMvDXh/MrSP/i9bReozXSyHjHv0JqrD6WRTHAtCf8oCFDlvAPh+Acgq43NcwEE3CatxcJQUVZLdBNpiv9FuJYrv4nLdgyHAquNgSFZhgBrDfUUUnIjjeSpGIrQHyXl5VI5FgCxYdOBTnxiJGc5BMZWwhu2cZugAM8gk6Wj2Z/V1CSVrq6QDmPIUahVrrmI90QcfnbPtyZ/kVqmF0L+lxAzsp1UVfTcqbQGE87M119XbrL8mfKiQnKA8d0afZvnQFUVzOlheq5jfkrgpJHauZFcQKiA8E6Q1AikfFM88B8j7FEeiJRMjHG3upA1qvBWB2zw0VNjNz3wBctljjCu4n/zCG8MrzkUiYObSF4zFOa/m3CGVwajCrA4/SpwLAEhjBdfuQLz0XT/O8apSQgK5DjMKMtA05FzxfCAhV0LUhUqSD/FSN+hurFUulZ76vXLm9DGOeD6Pgo/cn5TCbUMtV3bEkkiwVHbdv9DaNL/ZkD37QAoOqjK6Ci2YViFyycVM5pAy3jiyg14P414Kl2DCW/xecsUmmSB341wyn99Lj8znaMkT3stvAPh+Acgq43NcwEE3Catx8JgUVZIz+XPNF71PCayEvUIMA4aYyjiQNgZozcaxpNcxiAQ4hLbAiS5sdpJBFxpj0REB61Go4TF8RNyWHSsPw5M3z8DSPlLTXo30WpqWkual91CuWpNwzZ59U7J4m2oHXJOhnnIJHvvS7KC0d9yCZUxsp3acZN0Fwkt86tMvhtJpFzNlQry4ap/aUFW81LGAdgHY+b5YfPR+w7s/k5UskCAw0UbQ0ijwCPRqFH3/Jbu9E8o8wB/afHQ1jblzuq0eusKO1I7dNeBlluUaGxQ4/WslNbtVE7VFJENK1rt+18rOLizk8L/nAzoVayQEss+xMUP8MgBa9W382Ew5e58oZegLAFh8Pr4wurfbB5gTHgVU/Ala1lfde5n2yipvbF9LagWSAWymnSZYPDE5wJfCzuEU2nHYzdX6rPfyzVkarz2QIICpZTY7ekB25kCO706ZMEgA+lHn2zzpXTJSIzytv69zIEWZrvoobLIIj/mf8jVgBwtvAOiAAcgq43NcwEE3CatxMJ4UVZJD589tmwaUUWnXGRXk+w/lu1mzgK7dKM9O/TgWdbqqaEYz32ZYJBilZhSomxnljnUQTt8UQdMYbZNamI4Zf2Cis9+cw1lUI01tOEH0Fj8lhlqhfvyfx4C2LRf0SUbvx/Tbbj2+TKg+Ci/KfmNbWEpF9MImv2A8qG9V13STcl1WzeOrhh4KL+hbWt+9UBbUYg/lKY4kqoxhujowp/UlXZQu+YRA4RlW09C/HXBcETAc0U+ki/Ea4dCTumuW/ObVWOjn0CnZtQ+xwwOVUbu0Sgb4OT/kIGEg8fKEUF/F7ItMo/QDknfXxcV/93AbAkayUvndyHOSKoZS6YbjVp1qi1r7GUDwKWoSVrTtUN9mSgAL7peCsvhgprG5cjRjUyKuub2Nb/xzH7RtY3ZSNShzJpUxQWzwpvBOrbnvrza6/3xjiBGZ5ZXOI2/mwCc8wGLht5V0j0DDriScaNa/4Agw1R/C3Knc20qXXEqo8Cb2nOIygW8nGa6lkOMrkOI7kuJrEr4nWs6lUN4CS96iMN4n4Q6i5W4K5T+w5D8K8wgSjyHxKGKPJxmfpZDzKpDyOpLyahKvJ1q/pVDPAkvPojDPJ/EN',
                        'usuarios_id' => 1,
                    ],
                    [
                        'huellas_id' => 3,
                        'huella' => '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',
                        'usuarios_id' => 1,
                    ],
                    [
                        'huellas_id' => 4,
                        'huella' => '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',
                        'usuarios_id' => 1,
                    ],
                    [
                        'huellas_id' => 5,
                        'huella' => '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',
                        'usuarios_id' => 1,
                    ],
                    [ //usuario 2
                        'huellas_id' => 10,
                        'huella' => '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',
                        'usuarios_id' => 2,
                    ]
                    /*
                    [
                        'huellas_id' => 6,
                        'huella' => 'APh+Acgq43NcwEE3CatxsIoUVZLSoUOS9lT887avvoSiubboUf9YUfuSrvNnV1Mmck7Lc7o0zoo1BdwVltL+HhBQK0q0ynx/mhiPkUgzqeNw3lQPpFUGPOTMsmr51hf4C8R6wOKnnyRVpJORFGlkg/OSb5AfmYDru79RD3MUQSdHxVFhSSIXzds6cu7hspsLS+WcDPZmW0Ds5bdYZZTtrMEVoTMf6H85rBVfyg4J7uisrxjmqVM/x/yv0OmQfBHU4gKGQd0edM1wwHQHCEk69wtk5wx6yGtiaPTgDZ+hRj4OBaKjeGp4BONa3tsB3aM956oidpkEceDo/6NR/11Sl4ZpMauDwy7Xs3976ciMDTdrj9uoVtow3Xj9eWONj3KNpIyHhdfG3uXwSoVzMQQdbOWJVhCVmq0MtSFrAPSss7Rk1e5zb45sLHv1tmVM3mhZO7NX89AK97Wg6t3n0rJ7gt89CNjfhsDNPqM4ltaeXMGz5ywA1w8mIQ2zaCNwMcb8zy9vAPiAAcgq43NcwEE3CatxsOEUVZJ4/pEBPnkGvurmDN9LL2vwg/wuiaVuU1uc90G5TOFANTfkWTkjY+opGy5NPlwtH6fyHagiP7+/Rt2En5vZ3P8uqhHcQ9HQlD5i0o2RtCGvg0LGhdoNz67war202FEvg35nCw4B1PcTvaSShHNmZ0D+Bd9OWP9HFJUNXz/YSFBnDvzzP9y+5Tvm5gvWLcA1yZdJPYbhgjtw/thJJN/PRh0FFymIc640JSmYZ3RMcpdq+OyFf5MwGxrL5mnvxV5GIvschcSDnstHXw3cIsTyfteFt4V93h9tuuG40Xe7kkiY8j7MXmyxLZkPzEP0AYFoVkYNqZEq4HDh5Xh/IkYeAi8Wi7jvmRKJSmU2BS3ivuqDm39trxiKnqNaP3jEopmpa3Vh9cq8DpmF7CKwPg/sT08AC0KN+sPMnAgfP+h+sRGlhldBtiNsEUHqphTyiX9lD+9Dlff5vxbfb6cb7vpqoP7BCTGAEJ64tRaj6YCf0paLdG8A+H4ByCrjc1zAQTcJq3Gw7xRVklf5oDdMJeBppj8eEd5cPD1U8TdfGxhyYdZm1MpKR99tWwWtTBB/+ciTMnvBcVBtTvfVJbEydiDddR46ohe4MkSV9PlQOrqaSOm3jzQnmnDts+xntdW6DTwtqn2pGNw92YQc9Oyj720+xZdvlV1lOhHBPqHX/+9HMMdC6EHSgIoKw9gv2jHYxj8REi6WhfE/ijinM2sMInstUekj7B2/0r5QvoYMyay2yekXEXryVQQqXI+xKnhG8ryV96U4UxmHqgjEADuPZ6Weoj49bGPl0xVO7NtxXD5W9h7rZ0MrFCRos64oqra0tkffG+xtBgJDkjI95l4R7zFF8pBulK2QjORYL9nO3HrRdsH6LqVD6mHTAG9FfZyJms1TfXKmHF2SxbrP2YFM7WipfzoNwRVy4DVf15VqnxRBWvJdycqdr31Duqxc1xrIpkG4D7C0pycx6gWnFeAVZ7kWimcfXLvSGWDIOi2ulVelAK9B2etdeG8A6H4ByCrjc1zAQTcJq3GwgxRVklyCZk8RFVN8mIj5sxjYEEpc+I3lRFqT99CkyQVc6Wz/nURAX5eM21R4RXjef4TEYoTLqBm4eT3+wgvK5JgeQjveLDIaxYvtcBno9Br0y7SJmm1QeoPD//Ggzi3aBlfeSO4cQRJ8iec7L8TAFjlGDeEWm4lMXUeTFYN5GLk+bMHUtjJvLiIQMMGNes2qIzx5pKwDuTi8r2S43xAjfTX+Oe5yaC5SvsRe05wIjz0NfQk/iXz8/RrQJmpkgXiQkBvpLxIvS1phL5lzXi2K+o8owdkWMtGfDZiPSuyku5aSxGJ9TBIpa+1EyVnNiy0wgT1pqDOSrSlI86ISuZBu7Nxpvt/zI7FjJmwGkoIhPTM4PQFg1sJRJLXSCuDiEgco7YdxqTPz4XPwU2tXJe8xTC/pjb1PFhh+ijY7547ZVuC/xj3B0AtQYooDLOElo3pmnznLHK1AkGGMfRdO2xkmyFOsO5QVxUAc/z4wnRUjxNooI28AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
                        'usuarios_id' => 1,
                    ],
                    [
                        'huellas_id' => 7,
                        'huella' => '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',
                        'usuarios_id' => 1,
                    ],
                    [
                        'huellas_id' => 8,
                        'huella' => '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',
                        'usuarios_id' => 1,
                    ],
                    [
                        'huellas_id' => 9,
                        'huella' => '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',
                        'usuarios_id' => 1,
                    ]
                    */


                ]
            );
            DB::table('configuracion_checador')->insert(
                [
                    [
                        'configuracion' => 4
                    ]
                ]
            );
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
        //

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('usuarios_huellas');
        Schema::dropIfExists('usuarios_permisos');
        Schema::dropIfExists('dias_horario');
        Schema::dropIfExists('horarios');
        Schema::dropIfExists('configuracion_checador');
        Schema::dropIfExists('registros_checador');
    }
}
