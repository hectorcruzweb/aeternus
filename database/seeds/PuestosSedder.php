<?php

use Illuminate\Database\Seeder;

class PuestosSedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('puestos')->insert(['puesto' => 'Gerente']);
        DB::table('puestos')->insert(['puesto' => 'Vendedor (a)']);
        DB::table('puestos')->insert(['puesto' => 'Recepcionista']);
        DB::table('puestos')->insert(['puesto' => 'Servicios Operativos']);
        DB::table('puestos')->insert(['puesto' => 'Cobrador']);
        DB::table('puestos')->insert(['puesto' => 'tanatólogo (a)']);
    }
}