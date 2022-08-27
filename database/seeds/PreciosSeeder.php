<?php

use Illuminate\Database\Seeder;

class PreciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('precios')->insert(['precio' => 'Precio normal']);
        DB::table('precios')->insert(['precio' => 'Precio con descuento']);
        DB::table('precios')->insert(['precio' => 'Precio especial']);
    }
}