<?php

use Illuminate\Database\Seeder;

class SatMetodosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_metodos_pago')->insert(['clave' => 'PUE', 'metodo' => 'Pago en una sola exhibición']);
        DB::table('sat_metodos_pago')->insert(['clave' => 'PPD', 'metodo' => 'Pago en parcialidades o diferido']);
    }
}