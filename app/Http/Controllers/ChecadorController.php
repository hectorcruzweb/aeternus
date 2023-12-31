<?php

namespace App\Http\Controllers;

use App\DiasFestivos;
use App\User;
use DateTime;
use Exception;
use App\RegistrosChecador;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

class ChecadorController extends ApiController
{

    function __construct()
    {
        date_default_timezone_set("America/Mazatlan");
    }


    public function get_empleados()
    {
        return $this->showAll(
            User::select(
                'id',
                'nombre',

            )
                ->whereHas('registros')
                ->orderBy("id", "asc")
                ->get()
        );
    }

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
                'tipo_registro_entrada_salida' => 'required',
                'tipo_registro' => 'required',
                'id_usuario' => 'required',
                'password' => $request->tipo_registro != "por_huella" ? "required" : ""
            ],
            [
                'tipo_registro.required' => 'El tipo de registro es necesario.',
                'id_usuario.required' => 'El ID del usuario es necesario.',
                'password.required' => "ingrese la contraseña del usuario.",
                'tipo_registro_entrada_salida.required' => 'El tipo de registro es necesario.',
            ]
        );
        try {

            $tipo_registro_id = $request->tipo_registro_entrada_salida;
            /*
            $registro_last = RegistrosChecador::orderBy('fecha_hora', 'DESC')->where("usuarios_id", $request->id_usuario)->first();
            //tipo_registro_id  => 1 es entrada, 0=> es salida
            $tipo_registro_id = 1;
            if ($registro_last != null) {
                //si no es null
                if ($registro_last->tipo_registro_id == 1) {
                    //el registro recuperado fue de entrada, por lo tanto el nuevo registro será salida
                    $tipo_registro_id = 0;
                }
            }
            */
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
            ->where('usuarios.status', '>', '0') //no muestro usuarios cancelados
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

    public function get_registros_checador(Request $request, $registro_id = "all", $usuario_id = "all", $paginated = "no_paginated")
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
        )->with("usuario:id,nombre")->orderBy('fecha_hora', 'DESC');
        if ($usuario_id != "all") {
            $resultado_query = $resultado_query->where("usuarios_id", $usuario_id);
        }
        if ($registro_id != "all") {
            $resultado_query->where("id", "=", $registro_id);
        }

        if ((isset($request->fecha_inicio) && trim($request->fecha_inicio) != "") && (isset($request->fecha_fin) && trim($request->fecha_fin) != "")) {
            $resultado_query->whereDate('fecha_hora', '>=', $request->fecha_inicio)->whereDate('fecha_hora', '<=', $request->fecha_fin);
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
            $registro["tipo_registro_texto"] = strtoupper($registro["tipo_registro_id"] == 1 ? "entrada" : "salida");
            $registro["fecha_registro_texto"] = strtoupper(fecha_abr($registro["fecha_hora"]));
            $registro["hora_registro"] = strtoupper(hora_seg($registro["fecha_hora"]));
            $registro["mensaje"] = $registro["tipo_registro_id"] == 1 ? strtoupper("Ingreso registrado correctamente.") : strtoupper("salida registrada correctamente.");
            if ($registro["registro_huella_b"] == 1) {
                $registro["forma_registro"] = strtoupper("Huella Digital");
            } elseif ($registro["registro_huella_b"] == 2) {
                $registro["forma_registro"] = strtoupper("Núm. Usuario y Contraseña");
            } else {
                $registro["forma_registro"] = strtoupper("Administrativa");
            }
        }
        return $resultado_query;
    }


    public function get_asistencia_reporte(Request $request, $paginated = "no_paginated")
    {
        request()->validate(
            [
                'fecha_inicio' => 'required|date|date_format:Y-m-d|before_or_equal:today',
                'fecha_fin'        => 'required|date|date_format:Y-m-d|after_or_equal:fecha_inicio|before_or_equal:today',
            ],
            [
                'fecha_inicio.before_or_equal' => 'La fecha inicial del reporte no debe ser mayor a la fecha actual.',
                'fecha_inicio.*' => 'La fecha de inicio es obligatoria.',
                'fecha_fin.before_or_equal' => 'La fecha final del reporte no debe ser mayor a la fecha actual.',
                'fecha_fin.*' => 'La fecha final del reporte es obligatoria (mayor o igual a la fecha de inicio).'
            ]
        );
        $resultado_query = User::select(
            "id",
            "nombre"
        );
        $resultado_query = $resultado_query->where("status", ">", 0);
        if (isset($request->usuario_id)) {
            $resultado_query = $resultado_query->where("id", $request->usuario_id);
        }
        $resultado_query = $resultado_query->with(["registros" => function ($q) use ($request) {
            $q->whereDate('fecha_hora', '>=', $request->fecha_inicio)
                ->whereDate('fecha_hora', '<=', $request->fecha_fin)->where("status", 1)->orderBy('fecha_hora', 'asc');
        }]);
        $resultado_query = $resultado_query->with(["horarios" => function ($q) use ($request) {
            $q->where('fecha_aplicacion', "<=",  $request->fecha_fin)->orderBy('fecha_aplicacion', 'asc');
        }, "horarios.diashorario"]);
        $resultado_query = $resultado_query->where("id", ">", 1);
        $resultado_query = $resultado_query->get();
        $resultado = array();
        if ($paginated == "paginated") {
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado = &$resultado_query["data"];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado = &$resultado_query;
        }
        //arreglo para las tarjetas a generar
        $tarjetas_checador = [];


        //dias_festuivos
        $dias_festivos = DiasFestivos::get();

        foreach ($resultado as $keyEmpleado => $empleado) {
            array_push($tarjetas_checador, [
                "id" => $empleado["id"],
                "empleado" => strtoupper($empleado["nombre"]),
                "indicadores" => [
                    "faltas" => 0,
                    "tiempo_trabajado" => 0,
                    "cumplio_jornada" => "No",
                    "horas_extra" => 0,
                    "tiempo_faltante_jornada" => 0 //las que faltan para las 8 horas diarias
                ],
                "tarjeta_checador" => [],
                //"horarios" => [] //$empleado["horarios"]
            ]);
            //para ese rango de fechas hago una evaluacion de sus registros en comparacion con sus horarios
            $fecha_inicial = new DateTime($request->fecha_inicio);
            $fecha_fin   = new DateTime($request->fecha_fin);
            //dias para los que se generará la tarjeta
            //$dias_tarjeta =  $fecha_inicial->diff($fecha_fin)->d + 1;
            //creando los dias que componen la tarjeta
            for ($dia = $fecha_inicial; $dia <= $fecha_fin; $dia->modify('+1 day')) {
                $tarjeta_empleado = [
                    "dia" => $dia->format('Y-m-d'),
                    "indicadores" => [
                        "asistido" => "",
                        "tiempo_trabajado" => "0 Horas",
                        "cumplio_jornada" => "No",
                        "horas_extra" => "0 Horas",
                        "tiempo_faltante_jornada" => "0 h 0 Min.", //las que faltan para las 8 horas diarias
                        "dia_descanso_obligatorio" => "No",
                    ],
                    "registros" => []
                ];
                //agrego sus registros por dia
                foreach ($empleado["registros"] as $keyRegistro => $registro) {
                    $registro_dia = new DateTime($registro["fecha_hora"]);
                    if ($registro_dia->format('Y-m-d') == $dia->format('Y-m-d')) {
                        array_push(
                            $tarjeta_empleado["registros"],
                            [
                                "tipo_registro_id" => $registro["tipo_registro_id"],
                                "tipo_registro_texto" => $registro["tipo_registro_id"] == 1 ? "Entrada" : "Salida",
                                "fecha_hora" => $registro["fecha_hora"],
                                "forma_registro" => $registro["registro_huella_b"] == 1 ? "Huella Digital" : "# de Usuario y Contraseña",
                                "jornada_texto" => "",
                                "registro_valido" => 0
                            ]
                        );
                    }
                }
                array_push($tarjetas_checador[$keyEmpleado]["tarjeta_checador"], $tarjeta_empleado);
            }
        }
        //Hasta aqui ya tenemos los registros de cada empleado por orden de dia
        foreach ($tarjetas_checador as $keyEmpleado => &$empleado) {
            foreach ($empleado["tarjeta_checador"] as $keyTarjeta => &$tarjeta) {
                /*
                Deshabilito los horarios definidos hasta nueva orden, estaremos trabajando solo con los registros capturados en cuestión de tiempo
                $horario_dia = null;
                foreach ($empleado["horarios"] as $keyHorario => $horario) {
                    if (new DateTime($horario["fecha_aplicacion"]) > new DateTime($tarjeta["dia"])) {
                        break;
                    } else {
                        if (new DateTime($horario["fecha_aplicacion"]) <= new DateTime($tarjeta["dia"])) {
                            $horario_dia = $horario;
                        }
                    }
                }
                */
                //Verifico si hay asistencia o falta ese día
                $asistido = false;
                foreach ($tarjeta["registros"] as $key => &$registro) {
                    if ($registro["tipo_registro_id"] == 1) {
                        $asistido = true;
                        break;
                    }
                }
                //determina si es día festivo el dia en cuestion
                $dia_festivo_aplicado = null;
                foreach ($dias_festivos as $key => $dia) {
                    $dia_registro = new DateTime($tarjeta["dia"]);
                    $dia_festivo = new DateTime($dia_registro->format('Y') . "-" . $dia["num_mes"] . "-" . $dia["num_dia"]);
                    if ($dia_registro->format('Y-m-d') == $dia_festivo->format('Y-m-d')) {
                        $dia_festivo_aplicado = $dia["festejo"];
                        $tarjeta["indicadores"]["dia_descanso_obligatorio"] = "Si (" . $dia_festivo_aplicado . ")";
                        break;
                    }
                }

                if ($asistido) {
                    $tarjeta["indicadores"]["asistido"] = "Si";
                    $minutos_trabajados_jornada = 0;
                    $minutos_extra_jornada = 0;
                    $minutos_faltantes_jornada = 0;
                    //obtenemos el tiempo trabajado
                    foreach ($tarjeta["registros"] as $key => &$registro) {
                        if ($registro["tipo_registro_id"] == 0) {
                            // si son de salida solo continuamos
                            continue;
                        } else {
                            if (isset($tarjeta["registros"][$key + 1])) {
                                if ($tarjeta["registros"][$key + 1]["tipo_registro_id"] == 1) {
                                    continue;
                                }
                                $registro["registro_valido"] = 1;
                                $hora_llegada = strtotime($registro["fecha_hora"]);
                                $hora_salida = strtotime($tarjeta["registros"][$key + 1]["fecha_hora"]);
                                $minutos_trabajados_jornada += round(abs($hora_llegada - $hora_salida) / 60, 0);
                                $registro["jornada_texto"] = "Llegada " . hora($registro["fecha_hora"]) . " - Salida " . hora($tarjeta["registros"][$key + 1]["fecha_hora"]);
                            } else {
                                //reviso si existe un registro de salida en el día siguiente para hacer el calculo de tiempo
                                if (isset($empleado["tarjeta_checador"][$keyTarjeta + 1])) {
                                    $registro_salida = $empleado["tarjeta_checador"][$keyTarjeta + 1];
                                    //pregunta si en registros esta un registro de salida
                                    if (count($registro_salida["registros"]) > 0) {
                                        if ($registro_salida["registros"][0]["tipo_registro_id"] == 0) {
                                            $registro["registro_valido"] = 1;
                                            $hora_llegada = strtotime($registro["fecha_hora"]);
                                            $hora_salida = strtotime($registro_salida["registros"][0]["fecha_hora"]);
                                            $minutos_trabajados_jornada += round(abs($hora_llegada - $hora_salida) / 60, 0);
                                            $registro["jornada_texto"] =  "Llegada " . hora($registro["fecha_hora"]) . " - Salida " . dia_completo($registro_salida["dia"]) . " " . fecha_abr($registro_salida["dia"]) . " " . hora($registro_salida["registros"][0]["fecha_hora"]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $tarjeta["indicadores"]["tiempo_trabajado"] = (($minutos_trabajados_jornada - ($minutos_trabajados_jornada % 60)) / 60) . " h " . ($minutos_trabajados_jornada % 60) . " Min.";
                    $empleado["indicadores"]["tiempo_trabajado"] += $minutos_trabajados_jornada;
                    if ($minutos_trabajados_jornada >= 480) {
                        //mayor a 8 horas x dia
                        $tarjeta["indicadores"]["cumplio_jornada"] = "Si";
                        $minutos_extra_jornada  = ($minutos_trabajados_jornada - 480);
                        if ($minutos_extra_jornada > 60) {
                            //si es mayor a 60 minutos se agrega un tiempo extra
                            $empleado["indicadores"]["horas_extra"] += $minutos_extra_jornada;
                            $tarjeta["indicadores"]["horas_extra"] = (($minutos_extra_jornada - ($minutos_extra_jornada % 60)) / 60) . " h " . ($minutos_extra_jornada % 60) . " Min.";
                        }
                    } else {
                        $minutos_faltantes_jornada  = (480 - $minutos_trabajados_jornada);
                        $tarjeta["indicadores"]["tiempo_faltante_jornada"] = (($minutos_faltantes_jornada - ($minutos_faltantes_jornada % 60)) / 60) . " h " . ($minutos_faltantes_jornada % 60) . " Min.";
                        if ($minutos_faltantes_jornada <= 20) {
                            //tolerancia de 20 minutos para cuando se cumple o no con la jornada
                            $tarjeta["indicadores"]["cumplio_jornada"] = "Si";
                        } else {
                            //se acumula en el global de tiempo faltante para hacer la indicacion
                            $empleado["indicadores"]["tiempo_faltante_jornada"] += $minutos_faltantes_jornada;
                        }
                    }
                    //return round($minutos_trabajados % 60, 0);
                } else {
                    if ($dia_festivo_aplicado == null) {
                        //no asistido
                        $empleado["indicadores"]["faltas"] += 1;
                        $tarjeta["indicadores"]["asistido"] = "No";
                        //al no tener registros se hacen el cargo de las 8 horas que tiene faltante "tiempo_faltante_jornada"
                        $minutos_faltantes_jornada  = 480; //8 horas
                        $tarjeta["indicadores"]["tiempo_faltante_jornada"] = (($minutos_faltantes_jornada - ($minutos_faltantes_jornada % 60)) / 60) . " h " . ($minutos_faltantes_jornada % 60) . " Min.";
                        $empleado["indicadores"]["tiempo_faltante_jornada"] += $minutos_faltantes_jornada;
                    } else {
                        $tarjeta["indicadores"]["asistido"] = "No (Día de descanso obligatorio)";
                    }
                }
                /*
                Deshabilito los horarios definidos hasta nueva orden, estaremos trabajando solo con los registros capturados en cuestión de tiempo
                //obtengo el horario sobre el cual serán evualuados los indicadores
                if ($horario_dia == null || $horario_dia["tipo_horario_id"] == 2) {
                    //sin horario o variable / calculando los indicadores
                    //calcula si falto el dia

                } else {
                    //tiene horario
                }
                */
                $tarjeta["dia"] = dia_completo($tarjeta["dia"]) . " " . fecha_abr($tarjeta["dia"]);
            }
            $empleado["indicadores"]["tiempo_trabajado"] = (($empleado["indicadores"]["tiempo_trabajado"] - ($empleado["indicadores"]["tiempo_trabajado"] % 60)) / 60) . " h " . ($empleado["indicadores"]["tiempo_trabajado"] % 60) . " Min.";
            if ($empleado["indicadores"]["tiempo_faltante_jornada"] < 480) {
                //si el tiempo faltante de jornada es mayor a 480 min = 1 dia de 8 horas se marca como que no cumple con su jornada
                $empleado["indicadores"]["cumplio_jornada"] = "Si";
            }
            $empleado["indicadores"]["tiempo_faltante_jornada"] = (($empleado["indicadores"]["tiempo_faltante_jornada"] - ($empleado["indicadores"]["tiempo_faltante_jornada"] % 60)) / 60) . " h " . ($empleado["indicadores"]["tiempo_faltante_jornada"] % 60) . " Min.";
            $empleado["indicadores"]["horas_extra"] = (($empleado["indicadores"]["horas_extra"] - ($empleado["indicadores"]["horas_extra"] % 60)) / 60) . " h " . ($empleado["indicadores"]["horas_extra"] % 60) . " Min.";
        }
        //retorno las tarjetas
        return  $tarjetas_checador;
    }
}
