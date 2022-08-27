<?php

use Illuminate\Database\Seeder;

class FamiliasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('familias')->insert(['familia' => 'MADERA', 'categorias_id' => 1]);
        DB::table('familias')->insert(['familia' => 'METALICOS', 'categorias_id' => 1]);

        DB::table('familias')->insert(['familia' => 'MADERA', 'categorias_id' => 2]);
        DB::table('familias')->insert(['familia' => 'METALICOS', 'categorias_id' => 2]);

        DB::table('familias')->insert(['familia' => 'CAFE', 'categorias_id' => 3]);
        DB::table('familias')->insert(['familia' => 'BEBIDAS', 'categorias_id' => 3]);
        DB::table('familias')->insert(['familia' => 'CONDIMENTOS', 'categorias_id' => 3]);

        DB::table('familias')->insert(['familia' => 'SILLAS', 'categorias_id' => 4]);
        DB::table('familias')->insert(['familia' => 'CARPAS', 'categorias_id' => 4]);
        DB::table('familias')->insert(['familia' => 'CRUCIFIJOS', 'categorias_id' => 4]);
    }
}
