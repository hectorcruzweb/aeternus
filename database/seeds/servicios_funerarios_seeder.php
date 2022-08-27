<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class servicios_funerarios_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** insertando titulos */
        DB::table('titulos')->insert(['titulo' => 'Señor']);
        DB::table('titulos')->insert(['titulo' => 'Señora']);
        DB::table('titulos')->insert(['titulo' => 'Joven']);
        DB::table('titulos')->insert(['titulo' => 'Señorita']);
        DB::table('titulos')->insert(['titulo' => 'Niño']);
        DB::table('titulos')->insert(['titulo' => 'Niña']);
        DB::table('titulos')->insert(['titulo' => 'Recién Nacido']);

        /**lugares de servicio de velacion */
        DB::table('lugares_servicios')->insert(['lugar' => 'Domicilio']);
        DB::table('lugares_servicios')->insert(['lugar' => 'Funeraria Aeternus | Sala la piedad']);
        DB::table('lugares_servicios')->insert(['lugar' => 'Funeraria Aeternus | Sala la misericordia']);
        DB::table('lugares_servicios')->insert(['lugar' => 'Funeraria Aeternus | Sala la resurreción']);

        /**Tipo Solicitud*/
        DB::table('tipo_solicitud')->insert(['tipo' => 'Servicio Funerario']);
        DB::table('tipo_solicitud')->insert(['tipo' => 'Exhumación']);

        /**escolaridades */
        DB::table('escolaridades')->insert(['escolaridad' => 'ninguna']);
        DB::table('escolaridades')->insert(['escolaridad' => 'preescolar']);
        DB::table('escolaridades')->insert(['escolaridad' => 'primaria']);
        DB::table('escolaridades')->insert(['escolaridad' => 'secundaria']);
        DB::table('escolaridades')->insert(['escolaridad' => 'preparatoria']);
        DB::table('escolaridades')->insert(['escolaridad' => 'profesional']);
        DB::table('escolaridades')->insert(['escolaridad' => 'posgrado']);
        DB::table('escolaridades')->insert(['escolaridad' => 'se ignora']);

        /**afilaciones */
        DB::table('afiliaciones')->insert(['afiliacion' => 'ninguna']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'imss']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'pemex']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'unidad médica privada']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'imss prospera']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'issste']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'semar']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'sedena']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'otros']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'se ignora']);

        /**estados civiles */
        DB::table('estados_civiles')->insert(['estado' => 'soltero (a)']);
        DB::table('estados_civiles')->insert(['estado' => 'casado (a)']);
        DB::table('estados_civiles')->insert(['estado' => 'separado (a)']);
        DB::table('estados_civiles')->insert(['estado' => 'divorciado (a)']);
        DB::table('estados_civiles')->insert(['estado' => 'unión libre']);
        DB::table('estados_civiles')->insert(['estado' => 'viudo']);
        DB::table('estados_civiles')->insert(['estado' => 'se ignora']);

        /**sitio muerte */
        DB::table('sitios_muerte')->insert(['sitio' => 'domicilio']);
        DB::table('sitios_muerte')->insert(['sitio' => 'imss']);
        DB::table('sitios_muerte')->insert(['sitio' => 'pemex']);
        DB::table('sitios_muerte')->insert(['sitio' => 'unidad médica privada']);
        DB::table('sitios_muerte')->insert(['sitio' => 'imss prospera']);
        DB::table('sitios_muerte')->insert(['sitio' => 'issste']);
        DB::table('sitios_muerte')->insert(['sitio' => 'semar']);
        DB::table('sitios_muerte')->insert(['sitio' => 'sedena']);
        DB::table('sitios_muerte')->insert(['sitio' => 'otro']);

        /**estado afectado */
        DB::table('estado_afectado')->insert(['estado' => 'recién Fallecido']);
        DB::table('estado_afectado')->insert(['estado' => 'embalsamado']);
        DB::table('estado_afectado')->insert(['estado' => 'en descomposición']);
        DB::table('estado_afectado')->insert(['estado' => 'cenizas']);
        DB::table('estado_afectado')->insert(['estado' => 'restos Aridos']);
        DB::table('estado_afectado')->insert(['estado' => 'por confirmar']);

        /**cementerios servicio */
        DB::table('cementerios_servicio')->insert(['cementerio' => 'Cementerio Aeternus']);
        DB::table('cementerios_servicio')->insert(['cementerio' => 'Otro Cementerio']);
        DB::table('cementerios_servicio')->insert(['cementerio' => 'Fosa Común']);


        DB::table('tipos_contratante')->insert(['tipo' => 'titular']);
        DB::table('tipos_contratante')->insert(['tipo' => 'titular sustituto']);
        DB::table('tipos_contratante')->insert(['tipo' => 'beneficiario']);
        DB::table('tipos_contratante')->insert(['tipo' => 'otro']);
    }
}
