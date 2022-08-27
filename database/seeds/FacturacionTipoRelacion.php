<?php

use Illuminate\Database\Seeder;

class FacturacionTipoRelacion extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_relacion')->insert(['tipo' => 'Relacion por SAT']);
        DB::table('tipo_relacion')->insert(['tipo' => 'Relacion por Pago']);
        DB::table('tipo_relacion')->insert(['tipo' => 'Relacion por Egreso']);
    }
}
