<?php

use Illuminate\Database\Seeder;

class RegistroPublicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('registro_publico')->insert(
            [
                'rep_legal' => 'Representante Legal',
                't_nep' => '4761',
                'fecha_tnep' => now(),
                'fe_lic' => 'Enrique Ibarra Delgado',
                'num_np' => '9', //numero de notario publico
                'ciudad_np' => 'Culiacán',
                'estado_np' => 'Sinaloa',
                'estado_rpc' => 'Sinaloa',
                'ciudad_rpc' => 'Mazatlán',
                'num_rpc' => '145',
                'fecha_rpc' => now(),
                'funeraria_id' => '1', //funeraria 1
            ]
        );
    }
}