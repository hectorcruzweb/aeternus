<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Inventario extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**tipo de articulos del inventario */
        $tipo_articulos = [
            [
                'tipo' => 'Artículo',
                'articulos' => []
            ],
            [
                'tipo' => 'Servicio',
                'articulos' => []
            ],
            [
                'tipo' => 'Equipo Rentable',
                'articulos' => []
            ]
        ];
        try {
            DB::beginTransaction();
            foreach ($tipo_articulos as $tipo) {
                DB::table('tipo_articulos')->insert([
                    'tipo' => $tipo['tipo']
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}