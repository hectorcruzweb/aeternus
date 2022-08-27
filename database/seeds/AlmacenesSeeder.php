<?php

use Illuminate\Database\Seeder;

class AlmacenesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('almacenes')->insert(['almacen' => 'Funeraria', 'status' => 1]);
    }
}
