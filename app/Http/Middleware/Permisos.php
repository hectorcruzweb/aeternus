<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Permisos
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    /**LAS RUTAS QUE ESTAN CONTROLADAS CON EL MIDDLEWARE "permiso" RECIBEN 2 PARAMETROS
     * EL PRIMERO ES EL NUMERO DEL MODULO
     * EL SEGUNDO EL NUMERO DE PERMISO
     */
    public function handle($request, Closure $next, $modulo, $permiso)
    {
        if ($request->user()) {
            $resultado = DB::table('usuarios')
                ->select('id')
                ->where('id', '=', $request->user()->id)
                ->where('status', '=', 1)
                ->get();
            if (count($resultado) > 0) {
                if (!Auth::check()) {
                    //VERIFICO SI EL USUARIO QUE HACE LA PETICION ESTA LOGUEADO
                    return $this->errorResponse('Usuario no autenticado', 401);
                } else {
                    //SE HACE LA CONSULTA PARA VER SI EL USUARIO TIENE EL PERMISO
                    $resultado = DB::table('usuarios')
                        ->select('permisos_id')
                        ->join('roles', 'usuarios.roles_id', '=', 'roles.id')
                        ->join('roles_permisos', 'roles_permisos.roles_id', '=', 'roles.id')
                        ->join('permisos', 'roles_permisos.permisos_id', '=', 'permisos.id')
                        ->where('usuarios.id', '=', Auth::user()->id)
                        ->where('modulos_id', '=', $modulo)
                        ->where('permisos_id', '=', $permiso)
                        ->get();
                    if (count($resultado)) {
                        //EL USUARIO TIENE PERMISO DE HACER LA ACCION Y PUEDE CONTINUAR
                        return $next($request);
                    } else {
                        //EL USUARIO NO CUENTA CON EL PERMISO
                        return $this->errorResponse('Acceso denegado', 403);
                    }
                }
            } else {
                //EL USUARIO NO CUENTA CON EL PERMISO
                return $this->errorResponse('Usuario deshabilitado', 440);
            }
        } else {
            //no existe el token y regresamos un codigo de error
            return $this->errorResponse('Usuario no autenticado', 401);
        }
    }
}