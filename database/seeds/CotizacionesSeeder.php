<?php

use Illuminate\Database\Seeder;

class CotizacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //inserto el modulo de checador
        try {
            DB::beginTransaction();
            DB::table('modulos')->insert(
                [
                    'modulo' => "Cotizaciones",
                    'icon' => "FileTextIcon",
                    'parent_modulo_id' => 15,
                    'url' => '/funeraria/cotizaciones',
                    "secciones_id" => 2,
                    "status" => 1
                ]
            );
            $modulo_id = DB::getPdo()->lastInsertId();
            $permisos = [
                [
                    'modulos_id' => $modulo_id,
                    'permiso' => "Crear / Modificar Cotizaciones"
                ],
                [
                    'modulos_id' => $modulo_id,
                    'permiso' => "Habilitar / Deshabilitar Cotizaciones"
                ],
                [
                    'modulos_id' => $modulo_id,
                    'permiso' => "Ver Cotizaciones"
                ]
            ];
            foreach ($permisos as $key => $permiso) {
                DB::table('permisos')->insert($permiso);
                $permiso_id = DB::getPdo()->lastInsertId();
                DB::table('roles_permisos')->insert(
                    [
                        'roles_id' => 1,
                        'permisos_id' => $permiso_id
                    ]
                );
                DB::table('roles_permisos')->insert(
                    [
                        'roles_id' => 4,
                        'permisos_id' => $permiso_id
                    ]
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
