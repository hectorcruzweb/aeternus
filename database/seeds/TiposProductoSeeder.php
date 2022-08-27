<?php

use Illuminate\Database\Seeder;

class TiposProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipos_producto')->insert(['tipo' => 'Producto']);
        DB::table('tipos_producto')->insert(['tipo' => 'Servicio']);
        DB::table('tipos_producto')->insert(['tipo' => 'Paquete']);
    }
}
