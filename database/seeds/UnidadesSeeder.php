<?php

use Illuminate\Database\Seeder;

class UnidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('unidades')->insert(['unidad' => 'Pieza']);
        DB::table('unidades')->insert(['unidad' => 'Paquete']);
        DB::table('unidades')->insert(['unidad' => 'Caja']);
        DB::table('unidades')->insert(['unidad' => 'Kilogramo']);
        DB::table('unidades')->insert(['unidad' => 'Gramo']);
        DB::table('unidades')->insert(['unidad' => 'Miligramo']);
        DB::table('unidades')->insert(['unidad' => 'Metro']);
        DB::table('unidades')->insert(['unidad' => 'Centimetro']);
        DB::table('unidades')->insert(['unidad' => 'Milimetro']);
        DB::table('unidades')->insert(['unidad' => 'Litro']);
        DB::table('unidades')->insert(['unidad' => 'Mililitro']);
        DB::table('unidades')->insert(['unidad' => 'Servicio']);
    }
}
