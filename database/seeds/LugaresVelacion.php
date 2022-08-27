<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LugaresVelacion extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    /*1- Sin Velacion
        2- Domicilio Particular
        3- Sala la piedad
        4- Sala misericordia
        5- Sala Resureccion*/
    public function run()
    {
        DB::table('lugares_velacion')->insert(['lugar' => 'Sin Velación', 'funeraria_id' => 1]);
        DB::table('lugares_velacion')->insert(['lugar' => 'Domicilio Particular', 'funeraria_id' => 1]);
        DB::table('lugares_velacion')->insert(['lugar' => 'Sala la piedad', 'funeraria_id' => 1]);
        DB::table('lugares_velacion')->insert(['lugar' => 'Sala misericordia', 'funeraria_id' => 1]);
        DB::table('lugares_velacion')->insert(['lugar' => 'Sala Resurección', 'funeraria_id' => 1]);
    }
}