<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NuevasPropiedadesCementerio extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**insetando el nuevo tipo de propiedad de mausoleo */
        DB::table('tipo_propiedades')->insert([
            [ //7
                'tipo'        => 'mausoleos',
                'descripcion' => 'Propiedades columnas',
                'capacidad'   => '4',
            ],
        ]);

        $secciones = [
            /**agregando las terrazas de la numero 19 a la numero 22 */
            [
                //51
                'propiedad_indicador' => '19',
                'frente'              => '-',
                'filas'               => '10',
                'columnas'            => '36',
                'tipo_propiedades_id' => '4',
            ],
            [
                //52
                'propiedad_indicador' => '20',
                'frente'              => '-',
                'filas'               => '10',
                'columnas'            => '51',
                'tipo_propiedades_id' => '4',
            ],
            [
                //53
                'propiedad_indicador' => '21',
                'frente'              => '-',
                'filas'               => '9',
                'columnas'            => '17',
                'tipo_propiedades_id' => '4',
            ],
            [
                //54
                'propiedad_indicador' => '22',
                'frente'              => '-',
                'filas'               => '9',
                'columnas'            => '25',
                'tipo_propiedades_id' => '4',
            ],

            /**agregando las terrazas de la numero 23 a la numero 43 */
            /**registro de propiedades cuadruplex */
            [
                //55
                'propiedad_indicador' => '23',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '33',
                'tipo_propiedades_id' => '4',
            ],

            [
                //56
                'propiedad_indicador' => '24',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '40',
                'tipo_propiedades_id' => '4',
            ],
            [
                //57
                'propiedad_indicador' => '25',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '35',
                'tipo_propiedades_id' => '4',
            ],
            [
                //58
                'propiedad_indicador' => '26',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '30',
                'tipo_propiedades_id' => '4',
            ],
            [
                //59
                'propiedad_indicador' => '27',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '25',
                'tipo_propiedades_id' => '4',
            ],
            [
                //60
                'propiedad_indicador' => '28',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '30',
                'tipo_propiedades_id' => '4',
            ],
            [
                //61
                'propiedad_indicador' => '29',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '35',
                'tipo_propiedades_id' => '4',
            ],
            [
                //62
                'propiedad_indicador' => '30',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '35',
                'tipo_propiedades_id' => '4',
            ],
            [
                //63
                'propiedad_indicador' => '31',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '30',
                'tipo_propiedades_id' => '4',
            ],
            [
                //64
                'propiedad_indicador' => '32',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '24',
                'tipo_propiedades_id' => '4',
            ],
            [
                //65
                'propiedad_indicador' => '33',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '26',
                'tipo_propiedades_id' => '4',
            ],
            [
                //66
                'propiedad_indicador' => '34',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '30',
                'tipo_propiedades_id' => '4',
            ],
            [
                //67
                'propiedad_indicador' => '35',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '33',
                'tipo_propiedades_id' => '4',
            ],
            [
                //68
                'propiedad_indicador' => '36',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '20',
                'tipo_propiedades_id' => '4',
            ],
            [
                //69
                'propiedad_indicador' => '37',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '28',
                'tipo_propiedades_id' => '4',
            ],
            [
                //70
                'propiedad_indicador' => '38',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '35',
                'tipo_propiedades_id' => '4',
            ],
            [
                //71
                'propiedad_indicador' => '39',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '42',
                'tipo_propiedades_id' => '4',
            ],
            [
                //72
                'propiedad_indicador' => '40',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '32',
                'tipo_propiedades_id' => '4',
            ],
            [
                //73
                'propiedad_indicador' => '41',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '26',
                'tipo_propiedades_id' => '4',
            ],
            [
                //74
                'propiedad_indicador' => '42',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '16',
                'tipo_propiedades_id' => '4',
            ],
            [
                //75
                'propiedad_indicador' => '43',
                'frente'              => '-',
                'filas'               => '5',
                'columnas'            => '24',
                'tipo_propiedades_id' => '4',
            ],
            /**propiedades de tipo mausoleos */
            [
                //76 //Mausoleo A
                'propiedad_indicador' => 'A',
                'frente'              => '-',
                'filas'               => '148',
                'columnas'            => '1',
                'tipo_propiedades_id' => '7',
            ],
            [
                //77 //Mausoleo B
                'propiedad_indicador' => 'B',
                'frente'              => '-',
                'filas'               => '126',
                'columnas'            => '1',
                'tipo_propiedades_id' => '7',
            ],
            [
                //78 //Mausoleo C
                'propiedad_indicador' => 'C',
                'frente'              => '-',
                'filas'               => '100',
                'columnas'            => '1',
                'tipo_propiedades_id' => '7',
            ],
        ];

        /**insetando las propiedades */
        foreach ($secciones as $key) {
            DB::table('propiedades')->insert([
                'propiedad_indicador' => $key['propiedad_indicador'],
                'filas'               => $key['filas'],
                'columnas'            => $key['columnas'],
                'tipo_propiedades_id' => $key['tipo_propiedades_id'],
            ]);
        }

        /**insertando las columnas filas de las nuevas propiedades */

        $secciones = [
            /**propiedades de la 19 a la 22 */
            /**propiedad 51 */
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '18',
                'fin_columna'     => '36',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '16',
                'fin_columna'     => '36',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '14',
                'fin_columna'     => '36',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '12',
                'fin_columna'     => '36',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '10',
                'fin_columna'     => '36',
                'fila'            => '5', //E
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '8',
                'fin_columna'     => '36',
                'fila'            => '6', //F
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '6',
                'fin_columna'     => '36',
                'fila'            => '7', //G
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '4',
                'fin_columna'     => '36',
                'fila'            => '8', //H
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '3',
                'fin_columna'     => '36',
                'fila'            => '9', //I
            ],
            [
                //id propiedad 51
                'propiedades_id'  => '51',
                'empieza_columna' => '1',
                'fin_columna'     => '36',
                'fila'            => '10', //J
            ],

            /**fin propiedad 52 */
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '27',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '30',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '33',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '36',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '38',
                'fila'            => '5', //E
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '41',
                'fila'            => '6', //F
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '44',
                'fila'            => '7', //G
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '47',
                'fila'            => '8', //H
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '48',
                'fila'            => '9', //I
            ],
            [
                //id propiedad 52
                'propiedades_id'  => '52',
                'empieza_columna' => '1',
                'fin_columna'     => '51',
                'fila'            => '10', //J
            ],
            /**fin propiedad 52 */

            /**fin propiedad 53 */
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '2',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '3',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '5',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '7',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '9',
                'fila'            => '5', //E
            ],
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '11',
                'fila'            => '6', //F
            ],
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '13',
                'fila'            => '7', //G
            ],
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '15',
                'fila'            => '8', //H
            ],
            [
                //id propiedad 53
                'propiedades_id'  => '53',
                'empieza_columna' => '1',
                'fin_columna'     => '17',
                'fila'            => '9', //I
            ],
            /**fin propiedad 53 */

            /**fin propiedad 54 */
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '3',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '6',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '8',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '11',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '13',
                'fila'            => '5', //E
            ],
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '16',
                'fila'            => '6', //F
            ],
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '19',
                'fila'            => '7', //G
            ],
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '22',
                'fila'            => '8', //H
            ],
            [
                //id propiedad 54
                'propiedades_id'  => '54',
                'empieza_columna' => '1',
                'fin_columna'     => '25',
                'fila'            => '9', //I
            ],
            /**fin propiedad 54 */
            /**FIN DE PROPIEDADES DE LA 19 A LA 22 */

            /**AGREGANDO LAS TERRAZAS DESDE LA 23 A LA 43 */
            /**propiedad 55 */
            [
                //id propiedad 55
                'propiedades_id'  => '55',
                'empieza_columna' => '1',
                'fin_columna'     => '14',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 55
                'propiedades_id'  => '55',
                'empieza_columna' => '1',
                'fin_columna'     => '19',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 55
                'propiedades_id'  => '55',
                'empieza_columna' => '1',
                'fin_columna'     => '23',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 55
                'propiedades_id'  => '55',
                'empieza_columna' => '1',
                'fin_columna'     => '28',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 55
                'propiedades_id'  => '55',
                'empieza_columna' => '1',
                'fin_columna'     => '33',
                'fila'            => '5', //E
            ],
            /**fin propiedad 55 */

            /**propiedad 56 */
            [
                //id propiedad 56
                'propiedades_id'  => '56',
                'empieza_columna' => '1',
                'fin_columna'     => '40',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 56
                'propiedades_id'  => '56',
                'empieza_columna' => '1',
                'fin_columna'     => '40',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 56
                'propiedades_id'  => '56',
                'empieza_columna' => '1',
                'fin_columna'     => '39',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 56
                'propiedades_id'  => '56',
                'empieza_columna' => '1',
                'fin_columna'     => '37',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 56
                'propiedades_id'  => '56',
                'empieza_columna' => '1',
                'fin_columna'     => '37',
                'fila'            => '5', //E
            ],
            /**fin propiedad 56 */

            /**propiedad 57 */
            [
                //id propiedad 57
                'propiedades_id'  => '57',
                'empieza_columna' => '1',
                'fin_columna'     => '35',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 57
                'propiedades_id'  => '57',
                'empieza_columna' => '1',
                'fin_columna'     => '35',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 57
                'propiedades_id'  => '57',
                'empieza_columna' => '1',
                'fin_columna'     => '31',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 57
                'propiedades_id'  => '57',
                'empieza_columna' => '1',
                'fin_columna'     => '31',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 57
                'propiedades_id'  => '57',
                'empieza_columna' => '1',
                'fin_columna'     => '31',
                'fila'            => '5', //E
            ],
            /**fin propiedad 57 */

            /**propiedad 58 */
            [
                //id propiedad 58
                'propiedades_id'  => '58',
                'empieza_columna' => '1',
                'fin_columna'     => '30',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 58
                'propiedades_id'  => '58',
                'empieza_columna' => '1',
                'fin_columna'     => '26',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 58
                'propiedades_id'  => '58',
                'empieza_columna' => '1',
                'fin_columna'     => '26',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 58
                'propiedades_id'  => '58',
                'empieza_columna' => '1',
                'fin_columna'     => '25',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 58
                'propiedades_id'  => '58',
                'empieza_columna' => '1',
                'fin_columna'     => '25',
                'fila'            => '5', //E
            ],
            /**fin propiedad 58 */

            /**propiedad 59 */
            [
                //id propiedad 59
                'propiedades_id'  => '59',
                'empieza_columna' => '1',
                'fin_columna'     => '25',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 59
                'propiedades_id'  => '59',
                'empieza_columna' => '1',
                'fin_columna'     => '25',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 59
                'propiedades_id'  => '59',
                'empieza_columna' => '1',
                'fin_columna'     => '20',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 59
                'propiedades_id'  => '59',
                'empieza_columna' => '1',
                'fin_columna'     => '20',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 59
                'propiedades_id'  => '59',
                'empieza_columna' => '1',
                'fin_columna'     => '20',
                'fila'            => '5', //E
            ],
            /**fin propiedad 59 */

            /**propiedad 60 */
            [
                //id propiedad 60
                'propiedades_id'  => '60',
                'empieza_columna' => '1',
                'fin_columna'     => '30',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 60
                'propiedades_id'  => '60',
                'empieza_columna' => '1',
                'fin_columna'     => '30',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 60
                'propiedades_id'  => '60',
                'empieza_columna' => '1',
                'fin_columna'     => '28',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 60
                'propiedades_id'  => '60',
                'empieza_columna' => '1',
                'fin_columna'     => '27',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 60
                'propiedades_id'  => '60',
                'empieza_columna' => '1',
                'fin_columna'     => '26',
                'fila'            => '5', //E
            ],
            /**fin propiedad 60 */

            /**propiedad 61 */
            [
                //id propiedad 61
                'propiedades_id'  => '61',
                'empieza_columna' => '1',
                'fin_columna'     => '1',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 61
                'propiedades_id'  => '61',
                'empieza_columna' => '1',
                'fin_columna'     => '29',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 61
                'propiedades_id'  => '61',
                'empieza_columna' => '2',
                'fin_columna'     => '35',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 61
                'propiedades_id'  => '61',
                'empieza_columna' => '2',
                'fin_columna'     => '35',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 61
                'propiedades_id'  => '61',
                'empieza_columna' => '2',
                'fin_columna'     => '34',
                'fila'            => '5', //E
            ],
            /**fin propiedad 61 */

            /**propiedad 62 */
            [
                //id propiedad 62
                'propiedades_id'  => '62',
                'empieza_columna' => '2',
                'fin_columna'     => '21',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 62
                'propiedades_id'  => '62',
                'empieza_columna' => '1',
                'fin_columna'     => '21',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 62
                'propiedades_id'  => '62',
                'empieza_columna' => '1',
                'fin_columna'     => '35',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 62
                'propiedades_id'  => '62',
                'empieza_columna' => '2',
                'fin_columna'     => '35',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 62
                'propiedades_id'  => '62',
                'empieza_columna' => '2',
                'fin_columna'     => '34',
                'fila'            => '5', //E
            ],
            /**fin propiedad 62 */

            /**propiedad 63 */
            [
                //id propiedad 63
                'propiedades_id'  => '63',
                'empieza_columna' => '1',
                'fin_columna'     => '30',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 63
                'propiedades_id'  => '63',
                'empieza_columna' => '1',
                'fin_columna'     => '30',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 63
                'propiedades_id'  => '63',
                'empieza_columna' => '1',
                'fin_columna'     => '29',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 63
                'propiedades_id'  => '63',
                'empieza_columna' => '1',
                'fin_columna'     => '26',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 63
                'propiedades_id'  => '63',
                'empieza_columna' => '1',
                'fin_columna'     => '26',
                'fila'            => '5', //E
            ],
            /**fin propiedad 63 */

            /**propiedad 64 */
            [
                //id propiedad 64
                'propiedades_id'  => '64',
                'empieza_columna' => '1',
                'fin_columna'     => '24',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 64
                'propiedades_id'  => '64',
                'empieza_columna' => '1',
                'fin_columna'     => '23',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 64
                'propiedades_id'  => '64',
                'empieza_columna' => '1',
                'fin_columna'     => '22',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 64
                'propiedades_id'  => '64',
                'empieza_columna' => '1',
                'fin_columna'     => '20',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 64
                'propiedades_id'  => '64',
                'empieza_columna' => '1',
                'fin_columna'     => '20',
                'fila'            => '5', //E
            ],
            /**fin propiedad 64 */

            /**propiedad 65 */
            [
                //id propiedad 65
                'propiedades_id'  => '65',
                'empieza_columna' => '1',
                'fin_columna'     => '26',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 65
                'propiedades_id'  => '65',
                'empieza_columna' => '1',
                'fin_columna'     => '26',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 65
                'propiedades_id'  => '65',
                'empieza_columna' => '1',
                'fin_columna'     => '23',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 65
                'propiedades_id'  => '65',
                'empieza_columna' => '1',
                'fin_columna'     => '23',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 65
                'propiedades_id'  => '65',
                'empieza_columna' => '1',
                'fin_columna'     => '24',
                'fila'            => '5', //E
            ],
            /**fin propiedad 65 */

            /**propiedad 66 */
            [
                //id propiedad 66
                'propiedades_id'  => '66',
                'empieza_columna' => '1',
                'fin_columna'     => '29',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 66
                'propiedades_id'  => '66',
                'empieza_columna' => '1',
                'fin_columna'     => '29',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 66
                'propiedades_id'  => '66',
                'empieza_columna' => '1',
                'fin_columna'     => '29',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 66
                'propiedades_id'  => '66',
                'empieza_columna' => '2',
                'fin_columna'     => '30',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 66
                'propiedades_id'  => '66',
                'empieza_columna' => '2',
                'fin_columna'     => '30',
                'fila'            => '5', //E
            ],
            /**fin propiedad 66 */

            /**propiedad 67 */
            [
                //id propiedad 67
                'propiedades_id'  => '67',
                'empieza_columna' => '1',
                'fin_columna'     => '33',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 67
                'propiedades_id'  => '67',
                'empieza_columna' => '1',
                'fin_columna'     => '33',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 67
                'propiedades_id'  => '67',
                'empieza_columna' => '1',
                'fin_columna'     => '33',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 67
                'propiedades_id'  => '67',
                'empieza_columna' => '2',
                'fin_columna'     => '33',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 67
                'propiedades_id'  => '67',
                'empieza_columna' => '2',
                'fin_columna'     => '33',
                'fila'            => '5', //E
            ],
            /**fin propiedad 67 */

            /**propiedad 68 */
            [
                //id propiedad 68
                'propiedades_id'  => '68',
                'empieza_columna' => '2',
                'fin_columna'     => '18',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 68
                'propiedades_id'  => '68',
                'empieza_columna' => '2',
                'fin_columna'     => '18',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 68
                'propiedades_id'  => '68',
                'empieza_columna' => '1',
                'fin_columna'     => '19',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 68
                'propiedades_id'  => '68',
                'empieza_columna' => '1',
                'fin_columna'     => '19',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 68
                'propiedades_id'  => '68',
                'empieza_columna' => '1',
                'fin_columna'     => '20',
                'fila'            => '5', //E
            ],
            /**fin propiedad 68 */

            /**propiedad 69 */
            [
                //id propiedad 69
                'propiedades_id'  => '69',
                'empieza_columna' => '2',
                'fin_columna'     => '27',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 69
                'propiedades_id'  => '69',
                'empieza_columna' => '2',
                'fin_columna'     => '27',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 69
                'propiedades_id'  => '69',
                'empieza_columna' => '2',
                'fin_columna'     => '28',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 69
                'propiedades_id'  => '69',
                'empieza_columna' => '2',
                'fin_columna'     => '28',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 69
                'propiedades_id'  => '69',
                'empieza_columna' => '1',
                'fin_columna'     => '28',
                'fila'            => '5', //E
            ],
            /**fin propiedad 69 */

            /**propiedad 70 */
            [
                //id propiedad 70
                'propiedades_id'  => '70',
                'empieza_columna' => '2',
                'fin_columna'     => '33',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 70
                'propiedades_id'  => '70',
                'empieza_columna' => '2',
                'fin_columna'     => '34',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 70
                'propiedades_id'  => '70',
                'empieza_columna' => '2',
                'fin_columna'     => '34',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 70
                'propiedades_id'  => '70',
                'empieza_columna' => '1',
                'fin_columna'     => '35',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 70
                'propiedades_id'  => '70',
                'empieza_columna' => '1',
                'fin_columna'     => '35',
                'fila'            => '5', //E
            ],
            /**fin propiedad 70 */

            /**propiedad 71 */
            [
                //id propiedad 71
                'propiedades_id'  => '71',
                'empieza_columna' => '2',
                'fin_columna'     => '41',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 71
                'propiedades_id'  => '71',
                'empieza_columna' => '2',
                'fin_columna'     => '41',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 71
                'propiedades_id'  => '71',
                'empieza_columna' => '1',
                'fin_columna'     => '41',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 71
                'propiedades_id'  => '71',
                'empieza_columna' => '1',
                'fin_columna'     => '42',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 71
                'propiedades_id'  => '71',
                'empieza_columna' => '1',
                'fin_columna'     => '42',
                'fila'            => '5', //E
            ],
            /**fin propiedad 71 */

            /**propiedad 72 */
            [
                //id propiedad 72
                'propiedades_id'  => '72',
                'empieza_columna' => '3',
                'fin_columna'     => '32',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 72
                'propiedades_id'  => '72',
                'empieza_columna' => '3',
                'fin_columna'     => '32',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 72
                'propiedades_id'  => '72',
                'empieza_columna' => '2',
                'fin_columna'     => '32',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 72
                'propiedades_id'  => '72',
                'empieza_columna' => '2',
                'fin_columna'     => '32',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 72
                'propiedades_id'  => '72',
                'empieza_columna' => '1',
                'fin_columna'     => '32',
                'fila'            => '5', //E
            ],
            /**fin propiedad 72 */

            /**propiedad 73 */
            [
                //id propiedad 73
                'propiedades_id'  => '73',
                'empieza_columna' => '4',
                'fin_columna'     => '26',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 73
                'propiedades_id'  => '73',
                'empieza_columna' => '4',
                'fin_columna'     => '26',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 73
                'propiedades_id'  => '73',
                'empieza_columna' => '3',
                'fin_columna'     => '26',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 73
                'propiedades_id'  => '73',
                'empieza_columna' => '3',
                'fin_columna'     => '26',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 73
                'propiedades_id'  => '73',
                'empieza_columna' => '2',
                'fin_columna'     => '26',
                'fila'            => '5', //E
            ],
            /**fin propiedad 73 */

            /**propiedad 74 */
            [
                //id propiedad 74
                'propiedades_id'  => '74',
                'empieza_columna' => '1',
                'fin_columna'     => '16',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 74
                'propiedades_id'  => '74',
                'empieza_columna' => '1',
                'fin_columna'     => '16',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 74
                'propiedades_id'  => '74',
                'empieza_columna' => '1',
                'fin_columna'     => '16',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 74
                'propiedades_id'  => '74',
                'empieza_columna' => '1',
                'fin_columna'     => '16',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 74
                'propiedades_id'  => '74',
                'empieza_columna' => '1',
                'fin_columna'     => '16',
                'fila'            => '5', //E
            ],
            /**fin propiedad 74 */

            /**propiedad 75 */
            [
                //id propiedad 75
                'propiedades_id'  => '75',
                'empieza_columna' => '1',
                'fin_columna'     => '24',
                'fila'            => '1', //A
            ],
            [
                //id propiedad 75
                'propiedades_id'  => '75',
                'empieza_columna' => '1',
                'fin_columna'     => '24',
                'fila'            => '2', //B
            ],
            [
                //id propiedad 75
                'propiedades_id'  => '75',
                'empieza_columna' => '1',
                'fin_columna'     => '24',
                'fila'            => '3', //C
            ],
            [
                //id propiedad 75
                'propiedades_id'  => '75',
                'empieza_columna' => '1',
                'fin_columna'     => '24',
                'fila'            => '4', //D
            ],
            [
                //id propiedad 75
                'propiedades_id'  => '75',
                'empieza_columna' => '1',
                'fin_columna'     => '24',
                'fila'            => '5', //E
            ],
            /**fin propiedad 75 */
        ];
        foreach ($secciones as $key) {
            DB::table('columnas_filas')->insert([
                'propiedades_id'  => $key['propiedades_id'],
                'empieza_columna' => $key['empieza_columna'],
                'fin_columna'     => $key['fin_columna'],
                'fila'            => $key['fila'],
            ]);
        }

    }
}
