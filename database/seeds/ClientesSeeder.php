<?php

use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clientes')->insert(
            [
                'nombre' => 'público en general',
                'direccion' => 'N/A',
                'ciudad' => 'N/A',
                'estado' => 'N/A',
                'telefono' => 'N/A',
                'celular' => 'N/A',
                'telefono_extra' => 'N/A',
                'email' => 'N/A',
                'fecha_nac' => now(),
                'nacionalidades_id' => 122,
                /**mexico */
                'nombre_contacto' => 'N/A',
                'telefono_contacto' => 'N/A',
                'parentesco_contacto' => 'N/A',
                'rfc' => 'XAXX010101000',
                'razon_social' => 'N/A',
                'direccion_fiscal' => 'N/A',
                'fecha_registro' => now(),
                'fecha_modificacion' => null,
                'fecha_cancelacion' =>  null,
                'registro_id' => 1,
                'cancelo_id' => null,
                'nota' => null,
                'generos_id' => 1,
                'modifico_id' => null,
                'status' => 1
            ]
        );
    }
}