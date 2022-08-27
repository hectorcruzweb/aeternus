<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatPais extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_pais')->insert(['clave' => 'MEX', 'pais' => 'México']);
    }
}