<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatImpuestosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_impuestos')->insert(['clave' => '001', 'impuesto' => 'ISR']);
        DB::table('sat_impuestos')->insert(['clave' => '002', 'impuesto' => 'IVA']);
        DB::table('sat_impuestos')->insert(['clave' => '003', 'impuesto' => 'IEPS']);
    }
}