<?php

use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'rol' => 'SuperUsuario',
                'descripcion' => 'Tiene acceso 100 al sistema y solo los administradores del sistema tienen acceso',
            ],
            [
                'rol' => 'Admin',
                'descripcion' => 'Tiene acceso 100 al sistema, sin permisos administrador de sistemas(puesto).',
            ]
        ];
        foreach ($roles as $key) {
            DB::table('roles')->insert([
                'rol' => $key['rol'],
                'descripcion' => $key['descripcion']
            ]);
        }


        //aqui inserto los permisos en los roles
        $modulos = DB::table('modulos')->select('id')->where('url', '<>', '')->get();
        $roles_res = DB::table('roles')->where('id', '<=', 2)->get();

        $permisos = DB::table('permisos')->get();

        foreach ($permisos as $permiso) {
            foreach ($modulos as $modulo) {
                if ($permiso->modulos_id == $modulo->id) {
                    foreach ($roles_res as $rol) {
                        DB::table('roles_permisos')->insert([
                            'roles_id' => $rol->id,
                            'permisos_id' => $permiso->id
                        ]);
                    }
                }
            }
        }
    }
}