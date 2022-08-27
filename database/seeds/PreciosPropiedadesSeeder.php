<?php

use Illuminate\Database\Seeder;

class PreciosPropiedadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //uniplex
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 20000,
                'financiamiento' => 1,
                'subtotal' => 17241.37931,
                'impuestos' => 2758.62069,
                'costo_neto' => 20000,
                'costo_neto_financiamiento_normal' => 20000,
                'descuento_pronto_pago_b' => 0,
                'costo_neto_pronto_pago' => 20000,
                'tipo_propiedades_id' => 1,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );


        //duplex
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' =>
                26000,
                'financiamiento' => 1,
                'subtotal' =>
                22413.7931,
                'impuestos' =>
                3586.206897,
                'costo_neto' => 26000,
                'costo_neto_financiamiento_normal' => 26000,
                'descuento_pronto_pago_b' => 0,
                'costo_neto_pronto_pago' => 26000,
                'tipo_propiedades_id' => 2,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );

        //nichos
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' =>
                26000,
                'financiamiento' => 1,
                'subtotal' =>
                22413.7931,
                'impuestos' =>
                3586.206897,
                'costo_neto' =>
                26000,
                'costo_neto_financiamiento_normal' => 20000,
                'descuento_pronto_pago_b' => 0,
                'costo_neto_pronto_pago' => 20000,
                'tipo_propiedades_id' => 3,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );


        //cuadruplex
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 50000,
                'financiamiento' => 1,
                'subtotal' =>
                43103.44828,
                'impuestos' =>
                6896.551724,
                'costo_neto' => 50000,
                'costo_neto_financiamiento_normal' => 50000,
                'descuento_pronto_pago_b' => 0,
                'costo_neto_pronto_pago' => 50000,
                'tipo_propiedades_id' => 4,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );



        //triplex
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 30000,
                'financiamiento' => 1,
                'subtotal' =>
                25862.06897,
                'impuestos' =>
                4137.931034,
                'costo_neto' => 30000,
                'costo_neto_financiamiento_normal' => 30000,
                'descuento_pronto_pago_b' => 0,
                'costo_neto_pronto_pago' => 30000,
                'tipo_propiedades_id' => 5,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );
        //cuadruplex sin terraza
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 50000,
                'financiamiento' => 1,
                'subtotal' =>
                43103.44828,
                'impuestos' =>
                6896.551724,
                'costo_neto' => 50000,
                'costo_neto_financiamiento_normal' => 50000,
                'descuento_pronto_pago_b' => 0,
                'costo_neto_pronto_pago' => 50000,
                'tipo_propiedades_id' => 6,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );
    }
}