<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class formasPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_formas_pago')->insert(['clave' => '01', 'forma' => 'Efectivo']);
        DB::table('sat_formas_pago')->insert(['clave' => '02', 'forma' => 'Cheque nominativo']);
        DB::table('sat_formas_pago')->insert(['clave' => '03', 'forma' => 'Transferencia electrónica de fondos']);
        DB::table('sat_formas_pago')->insert(['clave' => '04', 'forma' => 'Tarjeta de crédito']);
        DB::table('sat_formas_pago')->insert(['clave' => '28', 'forma' => 'Tarjeta de débito']);
        DB::table('sat_formas_pago')->insert(['clave' => '15', 'forma' => 'Condonación']);
        DB::table('sat_formas_pago')->insert(['clave' => '25', 'forma' => 'Remisión de deuda']);
        DB::table('sat_formas_pago')->insert(['clave' => '31', 'forma' => 'Intermediario pagos']);
        DB::table('sat_formas_pago')->insert(['clave' => '99', 'forma' => 'Por definir']);
    }
}