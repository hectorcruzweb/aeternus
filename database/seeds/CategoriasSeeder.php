<?php

use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categorias')->insert(['categoria' => 'ATAUDES']);
        DB::table('categorias')->insert(['categoria' => 'URNAS']);
        DB::table('categorias')->insert(['categoria' => 'CAFETERIA']);
        DB::table('categorias')->insert(['categoria' => 'MATERIAL RENTABLE']);
    }
}
