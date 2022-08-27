<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tiposPropiedadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $secciones = [
            [ //1
                'tipo'        => 'uniplex',
                'descripcion' => 'Propiedades organizadas por módulos',
                'capacidad'   => '1',
            ],
            [ //2
                'tipo'        => 'duplex',
                'descripcion' => 'Propiedades organizadas por módulos',
                'capacidad'   => '2',
            ],
            [ //3
                'tipo'        => 'nichos',
                'descripcion' => 'Propiedades organizadas por columnas',
                'capacidad'   => '1',
            ],
            [ //4
                'tipo'        => 'cuadriplex',
                'descripcion' => 'Propiedades organizadas por terrazas',
                'capacidad'   => '4',
            ],
            [ //5
                'tipo'        => 'triplex',
                'descripcion' => 'Propiedades por columnas',
                'capacidad'   => '3',
            ],
            [ //6
                'tipo'        => 'cuadriplex S/Terraza',
                'descripcion' => 'Propiedades columnas',
                'capacidad'   => '4',
            ],
        ];

        foreach ($secciones as $key) {
            DB::table('tipo_propiedades')->insert([
                'tipo'        => $key['tipo'],
                'descripcion' => $key['descripcion'],
                'capacidad'   => $key['capacidad'],
            ]);
        }
    }
}
