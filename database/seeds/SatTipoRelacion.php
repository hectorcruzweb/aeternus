<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatTipoRelacion extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_tipo_relacion')->insert(['clave' => '01', 'tipo' => 'Nota de crédito de los documentos relacionados', 'mostrar_b' => 1]);
        DB::table('sat_tipo_relacion')->insert(['clave' => '02', 'tipo' => 'Nota de débito de los documentos relacionados', 'mostrar_b' => 1]);
        DB::table('sat_tipo_relacion')->insert(['clave' => '03', 'tipo' => 'Devolución de mercancía sobre facturas o traslados previos', 'mostrar_b' => 1]);
        DB::table('sat_tipo_relacion')->insert(['clave' => '04', 'tipo' => 'Sustitución de los CFDI previos', 'mostrar_b' => 1]);
        DB::table('sat_tipo_relacion')->insert(['clave' => '05', 'tipo' => 'Traslados de mercancias facturados previamente', 'mostrar_b' => 1]);
        DB::table('sat_tipo_relacion')->insert(['clave' => '06', 'tipo' => 'Factura generada por los traslados previos', 'mostrar_b' => 1]);
        DB::table('sat_tipo_relacion')->insert(['clave' => '07', 'tipo' => 'CFDI por aplicación de anticipo', 'mostrar_b' => 1]);
        DB::table('sat_tipo_relacion')->insert(['clave' => '08', 'tipo' => 'Factura generada por pagos en parcialidades', 'mostrar_b' => 1]);
        DB::table('sat_tipo_relacion')->insert(['clave' => '09', 'tipo' => 'Factura generada por pagos diferidos', 'mostrar_b' => 1]);
    }
}