<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Departamentos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**tipo de articulos del inventario */
        $departamentos = [
            [
                'departamento' => 'Artículos Funerarios',
                'categorias' => [
                    'Ataudes', 'Urnas', 'Relicarios'
                ]
            ],
            [
                'departamento' => 'Servicios Funerarios',
                'categorias' => [
                    'Servicios funerarios'
                ]
            ],
            [
                'departamento' => 'Equipo y Material de Velación',
                'categorias' => [
                    'Equipo de velación'
                ]
            ],
            [
                'departamento' => 'Productos de Cafetería',
                'categorias' => [
                    'Cafetería'
                ]
            ]
        ];
        try {
            DB::beginTransaction();
            foreach ($departamentos as $departamento) {
                $id = DB::table('departamentos')->insertGetId([
                    'departamento' => $departamento['departamento']
                ]);
                foreach ($departamento['categorias'] as $categoria) {
                    DB::table('categorias')->insert([
                        'categoria' => $categoria,
                        'departamentos_id' => $id
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}