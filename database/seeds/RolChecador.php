<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolChecador extends Seeder
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
                    'modulo'        => "Reloj Checador",
                    'icon'          => "ClockIcon",
                    'parent_modulo_id'          => 0,
                    'url' => '/control-de-asistencia',
                    "secciones_id" => 1,
                    "status" => 1
                ]
            );
            $modulo_id = DB::getPdo()->lastInsertId();
            $permisos = [
                [
                    'modulos_id'        => $modulo_id,
                    'permiso'          => "Crear Registros"
                ],
                [
                    'modulos_id'        => $modulo_id,
                    'permiso'          => "Modificar Registros"
                ],
                [
                    'modulos_id'        => $modulo_id,
                    'permiso'          => "Habilitar / Deshabilitar Registros"
                ],
                [
                    'modulos_id'        => $modulo_id,
                    'permiso'          => "Descargar Lista de Registros"
                ],
                [
                    'modulos_id'        => $modulo_id,
                    'permiso'          => "Modificar Días de Descanso"
                ],
                [
                    'modulos_id'        => $modulo_id,
                    'permiso'          => "Consultar Tarjetas de Asistencia"
                ]
            ];
            foreach ($permisos as $key => $permiso) {
                DB::table('permisos')->insert($permiso);
                $permiso_id = DB::getPdo()->lastInsertId();
                DB::table('roles_permisos')->insert(
                    [
                        'roles_id'        => 1,
                        'permisos_id'          => $permiso_id
                    ]
                );
                DB::table('roles_permisos')->insert(
                    [
                        'roles_id'        => 4,
                        'permisos_id'          => $permiso_id
                    ]
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
