<?php

use Illuminate\Database\Seeder;

class CementerioSeeder extends Seeder
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
        DB::table('cementerios')->insert(
            [
                'cementerio' => 'Parque Funerario Aeternus',
                'calle' => 'Maxipista Mazatlán Culiacán Km. 1',
                'num_ext' => 'n/a',
                'num_int' => '0',
                'colonia' => 'n/a',
                'cp' => 'n/a',
                'ciudad' => 'Mazatlán',
                'estado' => 'Sinaloa',
                'telefono' => '(669) 983 15 77',
                'fax' => '(669) 983 15 88',
                'email' => 'administracion@aeternus.com.mx',
                'funeraria_id' => '1', //funeraria 1
                'hora_apertura' => '09:00',
                'hora_cierre' => '17:00',
                'salario_minimo' => '123.22',
                /**salario hasta la fecha 2020 */
                'numero_salarios' => '12',
                'mes_maximo_pago' => '1', //enero
                /**mes en el que se hace el pago de mantenimiento */
                'dia_maximo_pago' => '31'
                /**dia maximo del mes para el pago de mantenimiento */
            ]
        );
    }
}