<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\RegistrosChecador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChecadorController extends ApiController
{


    public function login_usuario_checador_registro_huellas(Request $request)
    {
        request()->validate(
            [
                'username'   => 'required|email',
                'password'   => 'required'
            ],
            [
                'username.required'  => 'Ingrese el email del usuario.',
                'username.email'     => 'El email debe ser un correo válido.',
                'password.required' => 'debe ingresar una contraseña.'
            ]
        );
        try {
            //verifico que el usuario este activo
            $resultado = DB::table('usuarios')
                ->select('*')
                ->where('email', '=', $request->username)
                ->get();
            if (!$resultado->isEmpty()) {
                if ($resultado[0]->status != 0) {
                    if ($resultado[0]->roles_id != 4 && $resultado[0]->roles_id != 1) {
                        return $this->errorResponse('Este usuario no tiene permiso para utilizar este módulo.', 403);
                    }
                    if (Hash::check($request->password, $resultado[0]->password)) {
                        //return User Found And Procceed
                        return $this->successResponse(true, 200);
                    } else {
                        return $this->errorResponse('Error de contraseña. Verifique que sus credenciales de acceso son correctas y vuelva a intentarlo.', 409);
                    }
                } else {
                    return $this->errorResponse('Este usuario no cuenta con acceso al sistema.', 409);
                }
            } else {
                return $this->errorResponse('Este usuario no está registrado en este sistema.', 409);
            }
        } catch (Exception $e) {
            return $this->errorResponse(true, 409);
        }
    }


    public function guardar_huella(Request $request)
    {
        try {
            //delete las huellas que ya se habia capturado con el mismo id
            DB::table('usuarios_huellas')->where('usuarios_id', $request->usuarios_id)->where('huellas_id', $request->huellas_id)->delete();
            if (DB::table('usuarios_huellas')->insert(
                [
                    'usuarios_id'        => $request->usuarios_id,
                    'huellas_id'          => $request->huellas_id,
                    'huella'          => $request->huella
                ]
            ))
                return $this->successResponse("success", 200);
            else
                return $this->errorResponse("Error on save.", 409);
        } catch (Exception $e) {
            return $this->errorResponse($e, 409);
        }
    }

    public function get_huellas_users()
    {
        return $this->successResponse($resultado = DB::table('usuarios_huellas')->get(), 200);
    }


    public function get_configuracion()
    {
        return $this->successResponse($resultado = DB::table('configuracion_checador')->get(), 200);
    }


    //se guarda el registro
    public function guardarRegistro(Request $request)
    {
        //validamos datos de acceso
        request()->validate(
            [
                'tipo_registro' => 'required',
                'id_usuario' => 'required',
                'password' => $request->tipo_registro != "por_huella" ? "required" : ""
            ],
            [
                'tipo_registro.required' => 'El tipo de registro es necesario.',
                'id_usuario.required' => 'El ID del usuario es necesario.',
                'password.required' => "ingrese la contraseña del usuario."
            ]
        );
        try {
            date_default_timezone_set("America/Mazatlan");
            $registro_last = RegistrosChecador::orderBy('id', 'DESC')->where("usuarios_id", $request->id_usuario)->first();
            //tipo_registro_id  => 1 es entrada, 0=> es salida
            $tipo_registro_id = 1;
            if ($registro_last != null) {
                //si no es null
                if ($registro_last->tipo_registro_id == 1) {
                    //el registro recuperado fue de entrada, por lo tanto el nuevo registro será salida
                    $tipo_registro_id = 0;
                }
            }
            $tipo_registro = $request->tipo_registro == "por_huella" ? 1 : 0;

            if ($tipo_registro == 0) {
                //se ocupa numero de usuario y password
                $resultado = DB::table('usuarios')
                    ->select('*')
                    ->where('id', '=', $request->id_usuario)
                    ->get();
                if (!$resultado->isEmpty()) {
                    if ($resultado[0]->status != 0) {
                        if ($resultado[0]->roles_id != 4 && $resultado[0]->roles_id != 1) {
                            return $this->errorResponse('Este usuario no tiene permiso para utilizar este módulo.', 403);
                        }
                        if (!Hash::check($request->password, $resultado[0]->password)) {
                            //return User Found And Procceed
                            return $this->errorResponse("Error, la contraseña no es válida.", 409);
                        }
                    } else {
                        return $this->errorResponse('Este usuario no cuenta con acceso al sistema.', 409);
                    }
                } else {
                    return $this->errorResponse('Este usuario no está registrado en este sistema.', 409);
                }
            }

            $registro = RegistrosChecador::insertGetId(
                [
                    'tipo_registro_id'        => $tipo_registro_id,
                    'fecha_hora'          => now(),
                    'registro_huella_b'          => $tipo_registro,
                    "usuarios_id" => $request->id_usuario
                ]
            );
            if ($registro) //registro correcto
                return $this->get_registros_checador($registro)[0];
            else
                return $this->errorResponse("Error on save.", 409);
        } catch (Exception $e) {
            return $this->errorResponse($e, 409);
        }
    }

    //functiones para el checador
    public function get_user_list_huellas(Request $request, $usuarios_id = "")
    {
        $fingers = ["izquierda_menique", "izquierda_anular", "izquierda_medio", "izquierda_indice", "izquierda_pulgar", "derecha_menique", "derecha_anular", "derecha_medio", "derecha_indice", "derecha_pulgar"];
        $fingers_texto = ["Menique izquierdo", "Anualar izquierdo", "Dedo medio izquierdo", "Índice izquierdo", "Pulgar izquierdo", "Menique derecho", "Anular derecho", "Dedo medio derecho", "Índice derecho", "Pulgar derecho"];
        $resultado_query = User::select(
            'usuarios.id as id_user',
            'nombre',
            'rol'
        )
            ->join('roles', 'roles.id', '=', 'usuarios.roles_id')
            ->where(function ($q) use ($request) {
                if ($request->empleado != '') {
                    $q->where('usuarios.nombre', 'like', '%' . $request->empleado . '%');
                }
            })
            ->with("huellas")
            //->where('usuarios.roles_id', '>', '1') //no muestro super usuarios
            ->orderBy("id_user", "asc");
        $resultado = array();
        if (trim($usuarios_id) == "") {
            //$resultado_query->where("usuarios.status", ">", 0);
            $resultado_query = $resultado_query->get();
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado       = &$resultado_query['data'];
        } else {
            $resultado_query->where("usuarios.id", "=", $usuarios_id);
            $resultado_query = $resultado_query->get()->toArray();
            $resultado       = &$resultado_query;
        }

        foreach ($resultado as $key => &$usuario) {
            if (!empty($usuario["huellas"])) {
                foreach ($usuario["huellas"] as $key_huella => &$huella) {
                    $huella["huella_nombre"] = $fingers[$huella["huellas_id"] - 1];
                    $huella["huella_nombre_texto"] = $fingers_texto[$huella["huellas_id"] - 1];
                }
            }
        }
        return $resultado_query;
    }

    public function get_registros_checador($registro_id = "all", $usuario_id = "all", $paginated = "no_paginated")
    {
        $resultado_query = RegistrosChecador::select(
            "*",
            DB::raw(
                '(NULL) as tipo_registro_texto'
            ),
            DB::raw(
                '(NULL) as fecha_registro_texto'
            ),
            DB::raw(
                '(NULL) as hora_registro'
            ),
            DB::raw(
                '(NULL) as mensaje'
            )
        )->with("usuario:id,nombre")->orderBy('id', 'DESC');
        if ($usuario_id != "all") {
            $resultado_query = $resultado_query->where("usuarios_id", $usuario_id);
        }
        if ($registro_id != "all") {
            $resultado_query->where("id", "=", $registro_id);
        }
        $resultado_query = $resultado_query->get();
        $resultado = array();
        if ($paginated == "paginated") {
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado = &$resultado_query["data"];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado = &$resultado_query;
        }
        foreach ($resultado as $key => &$registro) {
            $registro["tipo_registro_texto"] = strtoupper($registro["tipo_registro_id"] == 1 ? "registro de entrada" : "registro de salida");
            $registro["fecha_registro_texto"] = strtoupper(fecha_abr($registro["fecha_hora"]));
            $registro["hora_registro"] = strtoupper(hora_seg($registro["fecha_hora"]));
            $registro["mensaje"] = $registro["tipo_registro_id"] == 1 ? strtoupper("Ingreso registrado correctamente.") : strtoupper("salida registrada correctamente.");
        }

        return $resultado_query;
    }
}
