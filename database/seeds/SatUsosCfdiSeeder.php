<?php

use Illuminate\Database\Seeder;

class SatUsosCfdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_usos_cfdi')->insert(['clave' => 'G01', 'uso' => 'Adquisición de mercancias', 'aplica_b' => 1]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'G02', 'uso' => 'Devoluciones, descuentos o bonificaciones', 'aplica_b' => 1]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'G03', 'uso' => 'Gastos en general', 'aplica_b' => 1]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'I01', 'uso' => 'Construcciones', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'I02', 'uso' => 'Mobilario y equipo de oficina por inversiones', 'aplica_b' => 1]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'I03', 'uso' => 'Equipo de transporte', 'aplica_b' => 1]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'I04', 'uso' => 'Equipo de computo y accesorios', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'I05', 'uso' => 'Dados, troqueles, moldes, matrices y herramental', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'I06', 'uso' => 'Comunicaciones telefónicas', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'I07', 'uso' => 'Comunicaciones satelitales', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'I08', 'uso' => 'Otra maquinaria y equipo', 'aplica_b' => 1]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D01', 'uso' => 'Honorarios médicos, dentales y gastos hospitalarios', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D02', 'uso' => 'Gastos médicos por incapacidad o discapacidad', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D03', 'uso' => 'Gastos funerales', 'aplica_b' => 1]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D04', 'uso' => 'Donativos', 'aplica_b' => 1]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D05', 'uso' => 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D06', 'uso' => 'Aportaciones voluntarias al SAR', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D07', 'uso' => 'Primas por seguros de gastos médicos', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D08', 'uso' => 'Gastos de transportación escolar obligatoria', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D09', 'uso' => 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'D10', 'uso' => 'Pagos por servicios educativos (colegiaturas)', 'aplica_b' => 0]);
        DB::table('sat_usos_cfdi')->insert(['clave' => 'P01', 'uso' => 'Por definir', 'aplica_b' => 1]);
    }
}