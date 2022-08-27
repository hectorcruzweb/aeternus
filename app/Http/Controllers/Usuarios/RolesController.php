<?php

namespace App\Http\Controllers\Usuarios;

use App\User;
use App\Roles;
use App\Modulos;
use App\Secciones;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;

class RolesController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_roles()
    {
        return $this->showAll(
            Roles::select(
                'id as value',
                'rol as label'
            )
                ->where('roles.id', '>', 1)
                ->get()
        );
    }


    public function index(Request $request)
    {
        $status = $request->status;
        $rol_id = $request->rol_id;
        $nombre = $request->nombre;
        return $this->showAllPaginated(
            Roles::select(
                'id as id_rol',
                'rol',
                'descripcion',
                'roles.status as status_rol'
            )
                ->where('roles.id', ">", 1)
                ->where(function ($q) use ($status) {
                    if ($status != '') {
                        $q->where('roles.status', $status);
                    }
                })
                ->where(function ($q) use ($nombre) {
                    if ($nombre != '') {
                        $q->where('roles.rol', 'like', '%' . $nombre . '%');
                    }
                })
                ->get()
        );
    }



    public function get_rol_id(Request $request)
    {
        return
            $this->showOne(Roles::select(
                'roles.id',
                'roles.id as id_rol',
                'rol',
                'roles.descripcion',
                'roles.status as status_rol'
            )
                ->with('permisos')
                ->where('roles.id', '=', $request->rol_id)
                ->get()->first());
    }



    public function get_modulos_permisos()
    {
        return $modulos = Secciones::with('modulos.permisos')->get();
    }

    /**get_modulos */
    public function get_modulos()
    {
        //obtengo solo los modulos que no dependen de otros
        //estos son los que me interesan para los usuarios

        $modulos = DB::table('secciones')->select(
            'seccion',
            'modu.id as mod_id',
            'modulo'
        )
            ->join('modulos as modu', 'modu.secciones_id', '=', 'secciones.id')
            ->where(
                DB::raw("(select @sub_modulos:=COUNT(DISTINCT(modulos.id)) FROM modulos WHERE parent_modulo_id=modu.id)"),
                '=',
                0
            )
            ->orderBy('modu.id', 'asc')
            ->get();
        return $this->showAll($modulos);
    }

    public function get_modulos_urls_permisos(Request $request)
    {
        if ($request->user()) {
            $usuario = User::find($request->user()->id);
            /**RECIBIMOS COMO PARAMETRO EL TOKEN PARA OBTENER LOS PERMISOS DEL USUARIO*/
            $resultado = Modulos::select(
                'url',
                'modulo',
                'modulos_id',
                'permisos_id',
                'permiso'
            )
                ->join('permisos', 'permisos.modulos_id', '=', 'modulos.id')
                ->join('roles_permisos', 'permisos.id', '=', 'roles_permisos.permisos_id')
                ->where('modulos.url', '<>', '')
                ->where('roles_permisos.roles_id', '=', $usuario->roles_id)
                ->distinct()
                ->get();
            return $resultado;
        } else
            //no existe el token y regresamos un codigo de error
            return $this->errorResponse('Usuario no autenticado', 401);
    }



    /**AGREGAR ROLRES */
    public function add_roles(Request $request)
    {
        //verifico que si el rol esta dado de baja solo se habilite nuevamente
        /*$maxima_cantidad = Roles::where('id', $request->venta_id)->first();
        if (!$maxima_cantidad) {
            return $this->errorResponse('Dato no válido', 404);
        }*/
        request()->validate(
            [
                'rol' => 'required|unique:roles',
                'permisos' => 'required',
            ],
            [
                'required' => 'Este dato es obligatorio.',
                'unique' => 'Este rol ya existe'
            ]
        );

        try {
            DB::beginTransaction();
            $rol_id = DB::table('roles')->insertGetId(
                ['rol' => $request->rol]
            );

            foreach ($request->permisos as $permiso) {
                DB::table('roles_permisos')->insert(
                    [
                        'permisos_id' => ($permiso),
                        'roles_id' => $rol_id
                    ]
                );
            }
            DB::commit();
            return $rol_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return 0;
        }
    }

    /**OBTENGO LOS PERMISOS QUE TIENE CADA ROL SOBRE LOS MODULOS DEL SISTEMA */
    public function get_rol_permisos(Request $request)
    {
        $rol_id = $request['rol_id'];
        $modulos = DB::table('secciones')->select(
            'modu.id as mod_id',
            'permisos_id'
        )
            ->join('modulos as modu', 'modu.secciones_id', '=', 'secciones.id')
            ->join('modulos_roles_permisos', 'modulos_roles_permisos.modulos_id', '=', 'modu.id')
            ->where(
                DB::raw("(select @sub_modulos:=COUNT(DISTINCT(modulos.id)) FROM modulos WHERE parent_modulo_id=modu.id)"),
                '=',
                0
            )
            ->where('roles_id', '=', $rol_id)
            ->orderBy('modu.id', 'asc')
            ->orderBy('permisos_id', 'asc')
            ->get();

        $modulos_permisos = [];
        foreach ($modulos as $mod) {
            array_push(
                $modulos_permisos,
                $mod->mod_id . "_" . $mod->permisos_id
            );
        }
        return $modulos_permisos;
    }


    /**OBTENGO LOS PERMISOS QUE TIENE CADA ROL SOBRE LOS MODULOS DEL SISTEMA */
    public function update_rol(Request $request)
    {
        $rol_id = $request['rol_id'];
        $rol = $request['rol'];

        request()->validate(
            [
                'rol' => [
                    Rule::unique('roles', 'rol')->ignore($rol_id),
                ],
                'permisos' => 'required'
            ],
            [
                'rol.required' => 'Debe seleccionar un rol',
                'permisos.required' => 'Debe seleccionar 1 permiso al menos.',
                'unique' => 'Este rol ya existe'
            ]
        );

        try {
            DB::beginTransaction();
            //al pasar la validacion borro todos los permisos del rol
            DB::table('roles_permisos')->where('roles_id', '=', $rol_id)->delete();
            if (trim($rol)) {
                //si el rol ha cambiado
                $update = DB::table('roles')->where('id',  $rol_id)->update(['rol' => $rol]);
            }


            foreach ($request->permisos as $permiso) {
                DB::table('roles_permisos')->insert(
                    [
                        'permisos_id' => ($permiso),
                        'roles_id' => $rol_id
                    ]
                );
            }

            DB::commit();
            return $rol_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return 0;
        }
    }



    /**BAJA LOGICA DEL ROL */
    public function delete_rol(Request $request)
    {
        $rol_id = $request['rol_id'];

        request()->validate(
            [
                'rol_id' => 'required'
            ],
            [
                'required' => 'Debe seleccionar un rol',
            ]
        );

        //checar si existe el rol
        $rol = DB::table('roles')->select('id')
            ->where('id', '=', $rol_id)
            ->count();
        if ($rol == 0) {
            //no existe
            return $this->errorResponse('Esto rol no existe en la BD.', 409);
        }

        //verifico si el rol puede ser dado de baja por no tener usuarios asignados
        $usuarios = DB::table('usuarios')->select('id')
            ->where('roles_id', '=', $rol_id)
            ->count();
        if ($usuarios > 0) {
            return $this->errorResponse('Error al eliminar, este rol cuenta con usuarios relacionados.', 409);
        } else {

            /**el rol no tiene usuarios y se puede eliminar */
            try {
                DB::beginTransaction();
                //al pasar la validacion borro todos los permisos del rol
                DB::table('roles_permisos')->where('roles_id', '=', $rol_id)->delete();

                DB::table('roles')->where('id',  $rol_id)->delete();

                DB::commit();
                return $rol_id;
            } catch (\Throwable $th) {
                DB::rollBack();
                return 0;
            }
        }
    }
}
/**fin clase */