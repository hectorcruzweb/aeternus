<?php

use Illuminate\Database\Seeder;

class MetodosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('metodos_pago')->insert(['id' => 1, 'metodo' => 'EFECTIVO']);
        DB::table('metodos_pago')->insert(['id' => 2, 'metodo' => 'CHEQUE']);
        DB::table('metodos_pago')->insert(['id' => 3, 'metodo' => 'TARJETA DE DEBITO']);
        DB::table('metodos_pago')->insert(['id' => 4, 'metodo' => 'TARJETA DE CREDITO']);
        DB::table('metodos_pago')->insert(['id' => 5, 'metodo' => 'TRANSFERENCIA BANCARIA']);
    }
}
