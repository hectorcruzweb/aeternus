<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropiedadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**dando de alta propiedades uniplex*/
        $secciones = [
            [
                //1
                'propiedad_indicador' => 'A',
                'filas'               => '15', //15 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '1',
            ],
            [
                //2
                'propiedad_indicador' => 'B',
                'filas'               => '15', //15 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '1',
            ],
            [
                //3
                'propiedad_indicador' => 'D',
                'filas'               => '15', //15 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '1',
            ],
            [
                //4
                'propiedad_indicador' => 'E',
                'filas'               => '24', //24 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '1',
            ],
            [
                //5
                'propiedad_indicador' => 'M',
                'filas'               => '15', //15 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '1',
            ],
            [
                //6
                'propiedad_indicador' => 'N',
                'filas'               => '14', //14 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '1',
            ],
            [
                //7
                'propiedad_indicador' => 'Ñ',
                'filas'               => '14', //14 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '1',
            ],
            [
                //8
                'propiedad_indicador' => 'O',
                'filas'               => '15', //15 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '1',
            ],

            /**AGREGANDO PROPIEADES DUPLEX */
            [
                //9
                'propiedad_indicador' => 'C',
                'filas'               => '6', //6 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],
            [
                //10
                'propiedad_indicador' => 'F',
                'filas'               => '21', //21 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],
            [
                //11
                'propiedad_indicador' => 'G',
                'filas'               => '10', //10 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],
            [
                //12
                'propiedad_indicador' => 'H',
                'filas'               => '2', //2 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],
            [
                //13
                'propiedad_indicador' => 'I',
                'filas'               => '10', //10 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],
            [
                //14
                'propiedad_indicador' => 'J',
                'filas'               => '2', //2 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],
            [
                //15
                'propiedad_indicador' => 'K',
                'filas'               => '10', //10 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],
            [
                //16
                'propiedad_indicador' => 'L',
                'filas'               => '1', //1 PROPIEDA
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],

            //DANDO DE ALTA NICHOS
            [
                //17
                'propiedad_indicador' => '1',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //18
                'propiedad_indicador' => '2',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //19
                'propiedad_indicador' => '3',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //20
                'propiedad_indicador' => '4',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //21
                'propiedad_indicador' => '5',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //22
                'propiedad_indicador' => '6',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //23
                'propiedad_indicador' => '7',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //24
                'propiedad_indicador' => '8',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //25
                'propiedad_indicador' => '9',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //26
                'propiedad_indicador' => '10',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //27
                'propiedad_indicador' => '11',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            [
                //28
                'propiedad_indicador' => '12',
                'filas'               => '7', //1 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '3',
            ],
            /**registro de propiedades cuadruplex */
            [
                //29
                'propiedad_indicador' => '1',
                'frente'              => 'estacionamiento',
                'filas'               => '10',
                'columnas'            => '32',
                'tipo_propiedades_id' => '4',
            ],
            [
                //30
                'propiedad_indicador' => '2',
                'frente'              => 'estacionamiento',
                'filas'               => '10',
                'columnas'            => '33',
                'tipo_propiedades_id' => '4',
            ],
            [
                //31
                'propiedad_indicador' => '3',
                'frente'              => 'estacionamiento',
                'filas'               => '10',
                'columnas'            => '32',
                'tipo_propiedades_id' => '4',
            ],
            [
                //32
                'propiedad_indicador' => '4',
                'frente'              => 'TERRAZA 3',
                'filas'               => '10',
                'columnas'            => '31',
                'tipo_propiedades_id' => '4',
            ],
            [
                //33
                'propiedad_indicador' => '5',
                'frente'              => 'TERRAZA 2',
                'filas'               => '10',
                'columnas'            => '33',
                'tipo_propiedades_id' => '4',
            ],
            [
                //34
                'propiedad_indicador' => '6',
                'frente'              => 'TERRAZA 1',
                'filas'               => '10',
                'columnas'            => '32',
                'tipo_propiedades_id' => '4',
            ],
            [
                //35
                'propiedad_indicador' => '7',
                'frente'              => 'estacionamiento',
                'filas'               => '5',
                'columnas'            => '14',
                'tipo_propiedades_id' => '4',
            ],
            [
                //36
                'propiedad_indicador' => '8',
                'frente'              => 'terraza 4',
                'filas'               => '5',
                'columnas'            => '19',
                'tipo_propiedades_id' => '4',
            ],
            [
                //37
                'propiedad_indicador' => '9',
                'frente'              => 'terraza 5',
                'filas'               => '5',
                'columnas'            => '32',
                'tipo_propiedades_id' => '4',
            ],
            [
                //38
                'propiedad_indicador' => '10',
                'frente'              => 'terraza 6',
                'filas'               => '5',
                'columnas'            => '32',
                'tipo_propiedades_id' => '4',
            ],
            [
                //39
                'propiedad_indicador' => '11',
                'frente'              => 'terraza 7',
                'filas'               => '10',
                'columnas'            => '19',
                'tipo_propiedades_id' => '4',
            ],
            [
                //40
                'propiedad_indicador' => '12',
                'frente'              => 'terraza 11',
                'filas'               => '5',
                'columnas'            => '21',
                'tipo_propiedades_id' => '4',
            ],
            [
                //41
                'propiedad_indicador' => '13',
                'frente'              => 'terraza 9',
                'filas'               => '5',
                'columnas'            => '33',
                'tipo_propiedades_id' => '4',
            ],
            [
                //42
                'propiedad_indicador' => '14',
                'frente'              => 'terraza 10',
                'filas'               => '5',
                'columnas'            => '32',
                'tipo_propiedades_id' => '4',
            ],
            [
                //43
                'propiedad_indicador' => '15',
                'frente'              => 'terraza 12',
                'filas'               => '5',
                'columnas'            => '23',
                'tipo_propiedades_id' => '4',
            ],
            [
                //44
                'propiedad_indicador' => '16',
                'frente'              => 'terraza 13',
                'filas'               => '5',
                'columnas'            => '33',
                'tipo_propiedades_id' => '4',
            ],
            [
                //45
                'propiedad_indicador' => '17',
                'frente'              => 'terraza 14',
                'filas'               => '5',
                'columnas'            => '32',
                'tipo_propiedades_id' => '4',
            ],
            [
                //46
                'propiedad_indicador' => '18',
                'frente'              => 'terraza 15',
                'filas'               => '5',
                'columnas'            => '27',
                'tipo_propiedades_id' => '4',
            ],
            /**fin de registro de propiedades cuadruplex */

            /**duplex */
            [
                //47
                'propiedad_indicador' => 'S',
                'filas'               => '6', //6 PROPIEADES
                'columnas'            => '1',
                'tipo_propiedades_id' => '2',
            ],
            /**fin de duplex */

            /**triplex */
            [
                //48
                'propiedad_indicador' => 'P',
                'filas'               => '14',
                'columnas'            => '1',
                'tipo_propiedades_id' => '5',
            ],
            [
                //49
                'propiedad_indicador' => 'R',
                'filas'               => '12',
                'columnas'            => '1',
                'tipo_propiedades_id' => '5',
            ],
            /**fin de triplex */

            /**quadriplex sin terraza */
            [
                //50
                'propiedad_indicador' => 'Q',
                'filas'               => '8',
                'columnas'            => '1',
                'tipo_propiedades_id' => '6',
            ],
            /**fin de quadriplex sin terraza */
        ];

        foreach ($secciones as $key) {
            DB::table('propiedades')->insert([
                'propiedad_indicador' => $key['propiedad_indicador'],
                'filas'               => $key['filas'],
                'columnas'            => $key['columnas'],
                'tipo_propiedades_id' => $key['tipo_propiedades_id'],
            ]);
        }
    }
}
