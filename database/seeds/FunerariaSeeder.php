<?php

use Illuminate\Database\Seeder;

class FunerariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //se inserta un unico registro
        //el modulo de empresa seria solo de modificacion de datos
        DB::table('funeraria')->insert(
            [
                'nombre_comercial' => 'aeternus funerales',
                'razon_social' => 'servicios integrales de sinaloa sa de cv',
                'rfc' => 'SIS961210RG9',
                'calle' => 'carretera internacional',
                'num_ext' => '58',
                'num_int' => '',
                'colonia' => 'Adolfo lópez mateos',
                'cp' => '82140',
                'ciudad' => 'Mazatlán',
                'estado' => 'Sinaloa',
                'zona_horaria' => 'America/Mazatlan',
                'telefono' => '(669) 983 15 77',
                'ext' => '0',
                'fax' => '(669) 983 15 88',
                'email' => 'administracion@aeternus.com.mx',
                'facebook' => '',
                'web' => '',
                'cuenta' => '65-50040187-9',
                'clabe' => '0147 3065 5004 0187 96',
                'sat_regimenes_id' => '1', //General de Ley Personas Morales
            ]
        );
    }
}