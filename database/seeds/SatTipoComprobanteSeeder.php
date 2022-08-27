<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatTipoComprobanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_tipo_comprobante')->insert(['clave' => 'I', 'tipo' => 'Ingreso', 'mostrar_b' => 1]);
        DB::table('sat_tipo_comprobante')->insert(['clave' => 'E', 'tipo' => 'Egreso', 'mostrar_b' => 1]);
        DB::table('sat_tipo_comprobante')->insert(['clave' => 'T', 'tipo' => 'Traslado', 'mostrar_b' => 0]);
        DB::table('sat_tipo_comprobante')->insert(['clave' => 'N', 'tipo' => 'Nómina', 'mostrar_b' => 0]);
        DB::table('sat_tipo_comprobante')->insert(['clave' => 'P', 'tipo' => 'Pago', 'mostrar_b' => 1]);
    }
}