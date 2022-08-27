<?php

use Illuminate\Database\Seeder;

class GenerosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('generos')->insert(
            [
                'genero' => 'Hombre',
            ]
        );
        DB::table('generos')->insert(
            [
                'genero' => 'Mujer',
            ]
        );
    }
}