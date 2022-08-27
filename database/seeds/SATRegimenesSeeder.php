<?php

use Illuminate\Database\Seeder;

class SATRegimenesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return voclave
     */
    public function run()
    {
        DB::table('sat_regimenes')->insert(['clave' => '601', 'regimen' => 'General de Ley Personas Morales']);
        DB::table('sat_regimenes')->insert(['clave' => '603', 'regimen' => 'Personas Morales con Fines no Lucrativos']);
        DB::table('sat_regimenes')->insert(['clave' => '605', 'regimen' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios']);
        DB::table('sat_regimenes')->insert(['clave' => '606', 'regimen' => 'Arrendamiento']);
        DB::table('sat_regimenes')->insert(['clave' => '608', 'regimen' => 'Demás ingresos']);
        DB::table('sat_regimenes')->insert(['clave' => '609', 'regimen' => 'Consolclaveación']);
        DB::table('sat_regimenes')->insert(['clave' => '610', 'regimen' => 'Resclaveentes en el Extranjero sin Establecimiento Permanente en México']);
        DB::table('sat_regimenes')->insert(['clave' => '611', 'regimen' => 'Ingresos por Divclaveendos (socios y accionistas)']);
        DB::table('sat_regimenes')->insert(['clave' => '612', 'regimen' => 'Personas Físicas con Activclaveades Empresariales y Profesionales']);
        DB::table('sat_regimenes')->insert(['clave' => '614', 'regimen' => 'Ingresos por intereses']);
        DB::table('sat_regimenes')->insert(['clave' => '616', 'regimen' => 'Sin obligaciones fiscales']);
        DB::table('sat_regimenes')->insert(['clave' => '620', 'regimen' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos']);
        DB::table('sat_regimenes')->insert(['clave' => '621', 'regimen' => 'Incorporación Fiscal']);
        DB::table('sat_regimenes')->insert(['clave' => '622', 'regimen' => 'Activclaveades Agrícolas, Ganaderas, Silvícolas y Pesqueras']);
        DB::table('sat_regimenes')->insert(['clave' => '623', 'regimen' => 'Opcional para Grupos de Sociedades']);
        DB::table('sat_regimenes')->insert(['clave' => '624', 'regimen' => 'Coordinados']);
        DB::table('sat_regimenes')->insert(['clave' => '628', 'regimen' => 'Hclaverocarburos']);
        DB::table('sat_regimenes')->insert(['clave' => '607', 'regimen' => 'Régimen de Enajenación o Adquisición de Bienes']);
        DB::table('sat_regimenes')->insert(['clave' => '629', 'regimen' => 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales']);
        DB::table('sat_regimenes')->insert(['clave' => '630', 'regimen' => 'Enajenación de acciones en bolsa de valores']);
        DB::table('sat_regimenes')->insert(['clave' => '615', 'regimen' => 'Régimen de los ingresos por obtención de premios']);
    }
}