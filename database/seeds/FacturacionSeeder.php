<?php

use Illuminate\Database\Seeder;

class FacturacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('facturacion')->insert(
            [
                'cerFile' => null,
                'keyFile' => null,
                'fecha_solicitud' => now(),
                'fecha_vencimiento' => now(),
                'password' => '',
                'funeraria_id' => 1
            ]
        );
    }
}