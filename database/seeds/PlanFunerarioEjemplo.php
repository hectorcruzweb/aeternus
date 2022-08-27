<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanFunerarioEjemplo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**lista de conceptos */
        $conceptos = [
            [
                'seccion_id' => 1,
                'concepto' => 'Recoger a la persona fallecida dentro del área urbana de la ciudad para trasladarla a nuestras instalaciones.',
                'concepto_ingles' => 'Transfer of the person who died from place of death (Mazatlán Area) to funeral home.',
                'planes_funerarios_id' => 1
            ],
            [
                'seccion_id' => 1,
                'concepto' => 'Traslado de la casa funeraria al horno crematorio (ubicado en el parque funerario Aeternus).',
                'concepto_ingles' => 'Transfer from the funeral home to the crematory.',
                'planes_funerarios_id' => 1
            ],
            [
                'seccion_id' => 1,
                'concepto' => 'Asesoría en trámites y permisos.',
                'concepto_ingles' => 'Supporting with all the paperwork and permissions.',
                'planes_funerarios_id' => 1
            ],
            [
                'seccion_id' => 1,
                'concepto' => 'Servicio de cremación.',
                'concepto_ingles' => 'Cremation service.',
                'planes_funerarios_id' => 1
            ],
            [
                'seccion_id' => 1,
                'concepto' => 'Urna modelo básica.',
                'concepto_ingles' => 'Basic urn made of wood.',
                'planes_funerarios_id' => 1
            ],
            [
                'seccion_id' => 1,
                'concepto' => 'Entrega de urna conteniendo las cenizas en las instalaciones de la funeraria.',
                'concepto_ingles' => 'The ashes are delivered at the funeral home.',
                'planes_funerarios_id' => 1
            ]
        ];
        /**seeder de plan de funerario de ejemplo */
        try {
            DB::beginTransaction();
            $id_plan = DB::table('planes_funerarios')->insertGetId([
                'plan' => 'servicio de cremación',
                'plan_ingles' =>  'cremation service',
                'nota' => 'no incluye otros tramites y/o permisos necesarios para la realizacion de dicho servicio.',
                'nota_ingles' =>  'it does not include other procedures and / or permits necessary for the performance of said service.',
                'fecha_registro' => now(),
                'registro_id' => 1,
                'fecha_modificacion' => now()
            ]);

            /**CAPTURANDO LOS PERMISOS EN LA BASE DE DATOS */
            foreach ($conceptos as $concepto) {
                DB::table('plan_conceptos')->insert([
                    'seccion_id' => $concepto['seccion_id'],
                    'concepto' => $concepto['concepto'],
                    'concepto_ingles' => $concepto['concepto_ingles'],
                    'planes_funerarios_id' => $id_plan
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}