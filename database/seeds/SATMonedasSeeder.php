<?php

use Illuminate\Database\Seeder;

class SATMonedasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_monedas')->insert(['codigo_moneda' => 'MXN', 'descripcion' => 'Peso Mexicano', 'decimales' => '2']);
    }
}