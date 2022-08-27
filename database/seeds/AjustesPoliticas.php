<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AjustesPoliticas extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ajustes_politicas')->insert(
            [
                'tasa_fija_anual' => 12,
                'dias_antes_vencimiento' => 5,
                'maximo_dias_retraso' => 120,
                'porcentaje_pena_convencional_minima' => 60,
                'minima_partes_cubiertas' => 3,
                'maximo_pagos_vencidos' => 3,
                'maximo_dias_cancelar_contrato' => 5,
            ]
        );
    }
}