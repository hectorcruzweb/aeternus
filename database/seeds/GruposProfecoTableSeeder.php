<?php

use Illuminate\Database\Seeder;

class GruposProfecoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grupos_profeco')->insert(['grupo' => 'Venta', 'ver_nombre' => 'Venta de:']);
        DB::table('grupos_profeco')->insert(['grupo' => 'Uso', 'ver_nombre' => 'Uso de:']);
        DB::table('grupos_profeco')->insert(['grupo' => 'Colocacion', 'ver_nombre' => 'Colocacion de  de:']);
        DB::table('grupos_profeco')->insert(['grupo' => 'Transporte', 'ver_nombre' => 'Transporte para:']);

        //VENTA
        DB::table('grupos_profeco')->insert(['grupo' => 'Ataud', 'ver_nombre' => 'Ataud', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Féretro', 'ver_nombre' => 'Féretro', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Urna', 'ver_nombre' => 'Urna', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Recepción de cadaver ', 'ver_nombre' => 'Recepción de cadaver', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Traslado del cadaver ', 'ver_nombre' => 'Traslado del cadaver', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Preparación estética/arreglo del cadaver', 'ver_nombre' => 'Preparación estética/arreglo del cadaver', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Embalsamiento del cadaver', 'ver_nombre' => 'Embalsamiento del cadaver', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Conservación transitoria del cadaver', 'ver_nombre' => 'Conservación transitoria del cadaver', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Uso de capilla', 'ver_nombre' => 'Uso de capilla', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Inhumación', 'ver_nombre' => 'Inhumación', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Exhumación', 'ver_nombre' => 'Exhumación', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Reinhumación', 'ver_nombre' => 'Reinhumación', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Cremación', 'ver_nombre' => 'Cremación', 'grupo_parent_id' => 1]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Gestoría del traslado del cadaver', 'ver_nombre' => 'Gestoría del traslado del cadaver', 'grupo_parent_id' => 1]);

        //USO
        DB::table('grupos_profeco')->insert(['grupo' => 'Fosa', 'ver_nombre' => 'Fosa', 'grupo_parent_id' => 2]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Cripta', 'ver_nombre' => 'Cripta', 'grupo_parent_id' => 2]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Nicho', 'ver_nombre' => 'Nicho', 'grupo_parent_id' => 2]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Osario', 'ver_nombre' => 'Osario', 'grupo_parent_id' => 2]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Lote', 'ver_nombre' => 'Lote', 'grupo_parent_id' => 2]);

        //COLOCACION
        DB::table('grupos_profeco')->insert(['grupo' => 'Lápida', 'ver_nombre' => 'Lápida', 'grupo_parent_id' => 3]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Monumento', 'ver_nombre' => 'Monumento', 'grupo_parent_id' => 3]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Placa', 'ver_nombre' => 'Placa', 'grupo_parent_id' => 3]);

        //TRANSPORTE
        DB::table('grupos_profeco')->insert(['grupo' => 'Acompañantes', 'ver_nombre' => 'Acompañantes', 'grupo_parent_id' => 4]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Flores', 'ver_nombre' => 'Flores', 'grupo_parent_id' => 4]);
        DB::table('grupos_profeco')->insert(['grupo' => 'Servicio de café', 'ver_nombre' => 'Servicio de café', 'grupo_parent_id' => 4]);
    }
}
