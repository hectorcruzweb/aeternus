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

            DB::table('permisos')->insert(
                [
                    'modulos_id'        => $modulo_id,
                    'permiso'          => "Todos"
                ]
            );
            $rol_id = DB::getPdo()->lastInsertId();

            DB::table('roles_permisos')->insert(
                [
                    'roles_id'        => 1,
                    'permisos_id'          => $rol_id
                ]
            );

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
