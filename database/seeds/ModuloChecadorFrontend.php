<?php

use App\Modulos;
use App\Permisos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuloChecadorFrontend extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $modulo = Modulos::where("modulo", "=", "Reloj Checador")->first();
            if ($modulo) {
                //borra todo
                $permisos = DB::table('permisos')->select("id")->where('modulos_id', $modulo->id);
                DB::table('roles_permisos')->whereIn('permisos_id', $permisos)->delete();
                DB::table('permisos')->where('modulos_id', $modulo->id)->delete();
                Modulos::where("id", $modulo->id)->delete();
            };
            $id_modulo = DB::table('modulos')->insertGetId([
                'modulo' => 'Reloj Checador',
                'icon' =>  'ClockIcon',
                'parent_modulo_id' => 0,
                'url' =>  '/checador',
                'secciones_id' => 1,
                'status' => 1
            ]);

            //inserto los permisos
            DB::table('permisos')->insert(
                [
                    [
                        'modulos_id' => $id_modulo,
                        'permiso' =>  'Ver Registros'
                    ],
                    [
                        'modulos_id' => $id_modulo,
                        'permiso' =>  'Consultar Tarjeta'
                    ]
                ]
            );

            $permisos = Permisos::where("modulos_id",  $id_modulo)->get();

            foreach ($permisos as $key => $permiso) {
                DB::table('roles_permisos')->insert(
                    [
                        'roles_id' => 1,
                        'permisos_id' =>  $permiso->id
                    ]
                );
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}
