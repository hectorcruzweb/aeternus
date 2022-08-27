<?php

use Illuminate\Database\Seeder;

class TipoPreciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //con estos valores hago los tipos de precios que se van a manejar para el control de
        //tarifas en la venta de propiedades
        DB::table('tipo_precios')->insert(['tipo' => 'Uso inmediato']);
        DB::table('tipo_precios')->insert(['tipo' => 'A futuro']);
    }

    public function down()
    {
        Schema::dropIfExists('tipo_precios');
    }
}
