<?php

namespace App\Http\Controllers;

use App\DiasDescanso;
use PDF;
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
        date_default_timezone_set("Etc/GMT+7");
    }



    public function get_empleados_dias_descanso($id = "")
    {
        return User::select(
            'id',
            'nombre'
        )->where("id", $id)->with("dias_descanso")->first();
    }

    public function get_empleados($empleados_todos = "no")
    {
        $empleados = User::select(
            'id',
            'nombre'
        );

        if ($empleados_todos == "no") {
            $empleados->whereHas('registros');
        } else {
            $empleados->where("status", ">", 0)->where("id", ">", 1);
        }
        return $empleados->orderBy("id", "asc")->get();
    }

    public function get_empleados_paginados(Request $request)
    {
        $resultado_query = User::select(
            'id',
            'nombre',
            'roles_id',
            'genero',
            'status',
            'email'
        )->whereHas('registros')->with("rol:id,rol")->where('nombre', 'like', '%' . $request->nombre . '%')->orderBy("id", "asc")->get();
        $resultado = array();
        $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
        $resultado       = &$resultado_query['data'];
        foreach ($resultado as $key => &$usuario) {
            $usuario["genero"] = $usuario["genero"] == 2 ? "Mujer" : "Hombre";
        }
        return $resultado_query;
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
                'tipo_registro_entrada_salida.required' => 'El tipo de registro es necesario.'
            ]
        );

        try {
            DB::beginTransaction();
            //guardamos los 7 dias de descanso
            /* $dias_descanso = DiasDescanso::get();
            if (!$dias_descanso) {
                //guardamos los dias descanso del usuario
            }*/


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
                    'fecha_hora'          => date("Y-m-d H:i:s"),
                    'registro_huella_b'          => $tipo_registro,
                    "usuarios_id" => $request->id_usuario
                ]
            );
            if ($registro) //registro correcto
            {
                DB::commit();
                $request = new \Illuminate\Http\Request();
                return $this->get_registros_checador($request, $registro)[0];
            } else {
                DB::rollBack();
                return $this->errorResponse("Error on save.", 409);
            }
        } catch (Exception $e) {
            DB::rollBack();
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
            $registro["status_texto"] = $registro["status"] == 1 ? "Activo" : "Cancelado";
        }
        return $resultado_query;
    }


    public function get_asistencia_reporte(Request $request, $paginated = "no_paginated")
    {
        request()->validate(
            [
                'fecha_inicio' => 'required|date|date_format:Y-m-d|before_or_equal:today',
                'fecha_fin'        => 'required|date|date_format:Y-m-d|after_or_equal:fecha_inicio|before_or_equal:today'
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
            "nombre",
            "roles_id",
            "genero"
        )->with("rol:id,rol")->with("dias_descanso")->where('nombre', 'like', '%' . $request->nombre . '%');
        $resultado_query = $resultado_query;
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

        //parametros del reporte
        $dias_tarjeta =  0;

        //dias_festuivos
        $dias_festivos = DiasFestivos::get();

        foreach ($resultado as $keyEmpleado => $empleado) {
            array_push($tarjetas_checador, [
                "id" => $empleado["id"],
                "empleado" => strtoupper($empleado["nombre"]),
                "rol" => $empleado["rol"],
                "genero" => $empleado["genero"],
                "indicadores_empleado" => [
                    "dias_a_descansar" => 0,
                    "dias_descansados" => 0,
                    "horas_requeridas" => 0,
                    "tiempo_trabajado" => 0,
                    "cumplio_jornada" => "No",
                    "tiempo_faltante_jornada" => 0, //las que faltan para las 8 horas diarias
                    "faltas" => 0,
                    "horas_extra" => 0,
                    "porcentaje_asistencia" => ""
                ],
                "tarjeta_checador" => [],
                //"horarios" => [] //$empleado["horarios"]
                "dias_descanso" => $empleado["dias_descanso"]
            ]);
            //para ese rango de fechas hago una evaluacion de sus registros en comparacion con sus horarios
            $fecha_inicial = new DateTime($request->fecha_inicio);
            $fecha_fin   = new DateTime($request->fecha_fin);
            //dias para los que se generará la tarjeta
            $dias_tarjeta =  $fecha_inicial->diff($fecha_fin)->d + 1;
            //creando los dias que componen la tarjeta
            for ($dia = $fecha_inicial; $dia <= $fecha_fin; $dia->modify('+1 day')) {
                $tarjeta_empleado = [
                    "dia" => $dia->format('Y-m-d'),
                    "descanso" => 0,
                    "indicadores_tarjeta" => [
                        "asistido" => "",
                        "tiempo_trabajado" => "0 Horas",
                        "cumplio_jornada" => "No",
                        "horas_extra" => "0 Horas",
                        "tiempo_faltante_jornada" => "0 h 0 Min.", //las que faltan para las 8 horas diarias
                        "dia_descanso_obligatorio" => "No"
                    ],
                    "registros" => []
                ];
                //buscamos si son sus dias laborales
                //calculo si es dia laboral para ir sumando las horas (horas_requeridas) en array tarjetas_checador 480 hrs son 8 horas por dia
                $dia_semana = $dia->format('w');
                $descanso = false;
                foreach ($tarjetas_checador[$keyEmpleado]["dias_descanso"] as $key_dia => &$dias_descanso) {
                    $dias_descanso["dias_id"] == 7 ? $dias_descanso["dias_id"] = 0 : $dias_descanso["dias_id"] = $dias_descanso["dias_id"];
                    if ($dia_semana == $dias_descanso["dias_id"]) {
                        $descanso = true;
                        $tarjeta_empleado["descanso"] = 1;
                        break;
                    }
                }
                if (!$descanso) {
                    //si no es dia de descanso aumentamos las 8 horas de trabajo
                    $tarjetas_checador[$keyEmpleado]["indicadores_empleado"]["horas_requeridas"] += 480;
                }
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
                $descanso = false;
                if ($tarjeta["descanso"] == 1) {
                    $descanso = true;
                    $empleado["indicadores_empleado"]["dias_a_descansar"] += 1;
                }
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
                        $tarjeta["indicadores_tarjeta"]["dia_descanso_obligatorio"] = "Si (" . $dia_festivo_aplicado . ")";
                        break;
                    }
                }
                $minutos_faltantes_jornada = 0;
                $minutos_trabajados_jornada = 0;
                $minutos_extra_jornada = 0;
                if ($asistido) {
                    $tarjeta["indicadores_tarjeta"]["asistido"] = "Si";
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
                                $minutos_trabajados = round(abs($hora_llegada - $hora_salida) / 60, 0);
                                $minutos_trabajados_jornada +=  $minutos_trabajados;
                                $registro["jornada_texto"] = "Llegada " . hora($registro["fecha_hora"]) . " - Salida " . hora($tarjeta["registros"][$key + 1]["fecha_hora"]);
                                $registro["horas"] = (($minutos_trabajados - ($minutos_trabajados % 60)) / 60) . " h " . ($minutos_trabajados % 60) . " Min.";
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
                                            $minutos_trabajados = round(abs($hora_llegada - $hora_salida) / 60, 0);
                                            $minutos_trabajados_jornada +=  $minutos_trabajados;
                                            $registro["jornada_texto"] =  "Llegada " . hora($registro["fecha_hora"]) . " - Salida " . dia_completo($registro_salida["dia"]) . " " . fecha_abr($registro_salida["dia"]) . " " . hora($registro_salida["registros"][0]["fecha_hora"]);
                                            $registro["horas"] = (($minutos_trabajados - ($minutos_trabajados % 60)) / 60) . " h " . ($minutos_trabajados % 60) . " Min.";
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $tarjeta["indicadores_tarjeta"]["tiempo_trabajado"] = (($minutos_trabajados_jornada - ($minutos_trabajados_jornada % 60)) / 60) . " h " . ($minutos_trabajados_jornada % 60) . " Min.";
                    $empleado["indicadores_empleado"]["tiempo_trabajado"] += $minutos_trabajados_jornada;
                    if ($minutos_trabajados_jornada >= 480) {
                        //mayor a 8 horas x dia
                        $tarjeta["indicadores_tarjeta"]["cumplio_jornada"] = "Si";
                        $minutos_extra_jornada  = ($minutos_trabajados_jornada - 480);
                        if ($minutos_extra_jornada > 60) {
                            //si es mayor a 60 minutos se agrega un tiempo extra
                            $empleado["indicadores_empleado"]["horas_extra"] += $minutos_extra_jornada;
                            $tarjeta["indicadores_tarjeta"]["horas_extra"] = (($minutos_extra_jornada - ($minutos_extra_jornada % 60)) / 60) . " h " . ($minutos_extra_jornada % 60) . " Min.";
                        }
                    } else {
                        $minutos_faltantes_jornada  = (480 - $minutos_trabajados_jornada);
                        $tarjeta["indicadores_tarjeta"]["tiempo_faltante_jornada"] = (($minutos_faltantes_jornada - ($minutos_faltantes_jornada % 60)) / 60) . " h " . ($minutos_faltantes_jornada % 60) . " Min.";
                        if ($minutos_faltantes_jornada <= 20) {
                            //tolerancia de 20 minutos para cuando se cumple o no con la jornada
                            $tarjeta["indicadores_tarjeta"]["cumplio_jornada"] = "Si";
                        }
                        //se acumula en el global de tiempo faltante para hacer la indicacion
                        $empleado["indicadores_empleado"]["tiempo_faltante_jornada"] += $minutos_faltantes_jornada;
                    }
                } else {
                    if ($descanso) {
                        $tarjeta["indicadores_tarjeta"]["asistido"] = "Descanso semanal.";
                        $empleado["indicadores_empleado"]["dias_descansados"] += 1;
                    } else {
                        if ($dia_festivo_aplicado == null) {
                            //no asistido
                            $empleado["indicadores_empleado"]["faltas"] += 1;
                            $tarjeta["indicadores_tarjeta"]["asistido"] = "No";
                            //al no tener registros se hacen el cargo de las 8 horas que tiene faltante "tiempo_faltante_jornada"
                            $minutos_faltantes_jornada  = 480; //8 horas
                            $tarjeta["indicadores_tarjeta"]["tiempo_faltante_jornada"] = (($minutos_faltantes_jornada - ($minutos_faltantes_jornada % 60)) / 60) . " h " . ($minutos_faltantes_jornada % 60) . " Min.";
                            $empleado["indicadores_empleado"]["tiempo_faltante_jornada"] += $minutos_faltantes_jornada;
                        } else {
                            $tarjeta["indicadores_tarjeta"]["asistido"] = "No (Día de descanso obligatorio)";
                        }
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
                $tarjeta["descanso"] = $tarjeta["descanso"] == 0 ? "No" : "Si";
            }

            //escribo las horas requeridas para el periodo seleccionado
            $horas_requeridas = $empleado["indicadores_empleado"]["horas_requeridas"];
            $empleado["indicadores_empleado"]["horas_requeridas"]  = (($horas_requeridas - ($horas_requeridas % 60)) / 60) . " h " . ($horas_requeridas % 60) . " Min.";;
            $horas_trabajadas = $empleado["indicadores_empleado"]["tiempo_trabajado"];
            $empleado["indicadores_empleado"]["tiempo_trabajado"] = (($horas_trabajadas - ($horas_trabajadas % 60)) / 60) . " h " . ($horas_trabajadas % 60) . " Min.";

            //porcentaje_asistencia
            $empleado["indicadores_empleado"]["porcentaje_asistencia"] = round(($horas_trabajadas * 100) / $horas_requeridas, 0);
            if ($empleado["indicadores_empleado"]["tiempo_faltante_jornada"] < 480) {
                //si el tiempo faltante de jornada es mayor a 480 min = 1 dia de 8 horas se marca como que no cumple con su jornada
                $empleado["indicadores_empleado"]["cumplio_jornada"] = "Si";
            }
            $empleado["indicadores_empleado"]["tiempo_faltante_jornada"] = (($empleado["indicadores_empleado"]["tiempo_faltante_jornada"] - ($empleado["indicadores_empleado"]["tiempo_faltante_jornada"] % 60)) / 60) . " h " . ($empleado["indicadores_empleado"]["tiempo_faltante_jornada"] % 60) . " Min.";
            $empleado["indicadores_empleado"]["horas_extra"] = (($empleado["indicadores_empleado"]["horas_extra"] - ($empleado["indicadores_empleado"]["horas_extra"] % 60)) / 60) . " h " . ($empleado["indicadores_empleado"]["horas_extra"] % 60) . " Min.";

            $empleado["genero"] = $empleado["genero"] == 1 ? "Hombre" : "Mujer";
        }





        //retorno de datos solicitados
        $tarjetas_checador = [
            "parametros" => [
                "dias_tarjeta" => $dias_tarjeta
            ],
            "tarjetas" => $tarjetas_checador
        ];

        //retorno las tarjetas
        return  $tarjetas_checador;
    }

    public function cancelar_registro(Request $request)
    {
        $registro_id = $request->registro_id;
        request()->validate(
            [
                'registro_id' => 'required'
            ],
            [
                'registro_id.required' => 'El ID del registro es necesario.'
            ]
        );
        return DB::table('registros_checador')->where('id', $registro_id)->update(
            [
                'status' => 0,
                "cancelo_id" =>  auth()->user()->id
            ]
        );
    }

    public function habilitar_registro(Request $request)
    {
        $registro_id = $request->registro_id;
        request()->validate(
            [
                'registro_id' => 'required',
            ],
            [
                'registro_id.required' => 'El ID del registro es necesario.'
            ]
        );

        $registro = RegistrosChecador::where("id", $registro_id)->first();
        if ($registro != null) {
            if ($registro->status != 0) {
                return $this->errorResponse("El registro se encuentra activo.", 409);
            }
            return DB::table('registros_checador')->where('id', $registro_id)->update(
                [
                    'status' => 1,
                    "cancelo_id" => null,
                    "modifico_id" =>  auth()->user()->id
                ]
            );
        } else {
            return $this->errorResponse("Este registro no existe en la Base de Datos.", 409);
        }
    }


    public function guardar_registro_administrativo(Request $request)
    {
        //validamos datos de acceso
        request()->validate(
            [
                'fechahora' => 'required',
                'tipo_registro_id' => 'required',
                'id_usuario' => 'required'
            ],
            [
                'fechahora.required' => 'Ingrese la fecha y hora del registro.',
                'id_usuario.required' => 'El ID del usuario es necesario.',
                'tipo_registro_id.required' => 'El tipo de registro es necesario.'
            ]
        );
        try {
            $registro = RegistrosChecador::insertGetId(
                [
                    'tipo_registro_id'        => $request->tipo_registro_id,
                    'fecha_hora'          => $request->fechahora,
                    'registro_huella_b'          => 3, //administrativa
                    "usuarios_id" => $request->id_usuario,
                    "registro_id" =>  auth()->user()->id
                ]
            );
            if ($registro) //registro correcto
                return $registro;
            else
                return $this->errorResponse("Error on save.", 409);
        } catch (Exception $e) {
            return $this->errorResponse($e, 409);
        }
    }

    public function guardar_dias_descanso(Request $request)
    {
        //validamos datos de acceso
        request()->validate(
            [
                'dias' => 'required',
                'empleado_id' => 'required'
            ],
            [
                'dias.required' => 'Ingrese los datos de los días de descanso a registrar.',
                'empleado_id.required' => 'El ID del usuario es necesario.'
            ]
        );


        try {
            DB::beginTransaction();
            DB::table('dias_descanso')->where('usuarios_id', $request->empleado_id)->delete();
            foreach ($request->dias as $key => $dia) {
                if ($dia["seleccionado"] == 1)
                    DB::table('dias_descanso')->insert(
                        [
                            'fecha_aplicacion'        => now(),
                            'dias_id'          => $dia["id"],
                            'usuarios_id' => $request["empleado_id"]
                        ]
                    );
            }
            DB::commit();
            return 1;
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse("od", 409);
        }
    }



    public function modificar_registro_administrativo(Request $request)
    {
        //validamos datos de acceso
        request()->validate(
            [
                'fechahora' => 'required',
                'tipo_registro_id' => 'required',
                'id_usuario' => 'required',
                'id_registro_modificar' => "required"
            ],
            [
                'fechahora.required' => 'Ingrese la fecha y hora del registro.',
                'id_usuario.required' => 'El ID del usuario es necesario.',
                'tipo_registro_id.required' => 'El tipo de registro es necesario.',
                'id_registro_modificar.required' => 'El tipo de registro es necesario.'
            ]
        );
        try {
            return DB::table('registros_checador')->where('id', $request->id_registro_modificar)->update(
                [
                    'tipo_registro_id'        => $request->tipo_registro_id,
                    'fecha_hora'          => $request->fechahora,
                    'registro_huella_b'          => 3, //administrativa
                    "usuarios_id" => $request->id_usuario,
                    "modifico_id" =>  auth()->user()->id
                ]
            );
        } catch (Exception $e) {
            return $this->errorResponse($e, 409);
        }
    }


    public function lista_registros(Request $request, $fecha_inicio = "all", $fecha_fin = "all", $usuario_id = "all")
    {
        try {
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
        } catch (\Throwable $th) {
            $email = false;
            $email_to = 'hector@gmail.com';
        }
        $request = new \Illuminate\Http\Request();
        $request->replace(
            [
                'fecha_inicio' => $fecha_inicio == "all" ? "" : $fecha_inicio,
                'fecha_fin' => $fecha_fin == "all" ? "" : $fecha_fin,
            ]
        );
        $datos = $this->get_registros_checador($request, "all", $usuario_id, "no_paginated");
        /* if (empty($datos)) {
            return $this->errorResponse('Error, no se han encontrado datos que mostrar.', 409);
        }
        */
        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();
        $empleado = User::select(
            'nombre'
        )->where("id", $usuario_id)->first();
        $empleado = $usuario_id != "all" ? $empleado->nombre : "Todos";
        $fecha =  $fecha_inicio != "all" ? ("Del " . fecha_abr($fecha_inicio) . " al " . fecha_abr($fecha_fin)) : "Todo el Historial";
        $datos = [
            "empleado" => $empleado,
            "datos" => $datos,
            "rango_fechas" => $fecha
        ];
        $pdf = PDF::loadView('checador/registros/reporte', ['datos' => $datos, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "REGLAMENTO DE PAGO " . strtoupper("nombre del reporte") . '.pdf';
        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view('checador.registros.footer', ['empresa' => $empresa]),
        ]);
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 13.4);
        $pdf->setOption('margin-right', 13.4);
        $pdf->setOption('margin-top', 9.4);
        $pdf->setOption('margin-bottom', 13.4);
        $pdf->setOption('page-size', 'Letter');
        if ($email == true) {
            /**email */
            /**
             * parameters lista de la funcion
             * to destinatario
             * to_name nombre del destinatario
             * subject motivo del correo
             * name_pdf nombre del pdf
             * pdf archivo pdf a enviar
             */
            /**quiere decir que el usuario desa mandar el archivo por correo y no consultarlo */
            $email_controller = new EmailController();
            $enviar_email     = $email_controller->pdf_email(
                $email_to,
                strtoupper($empleado),
                'reporte de registros de checador ' . $fecha,
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    public function reporte_tarjeta(Request $request)
    {

        if (!isset($request->fecha_inicio) || !isset($request->fecha_fin))
            return $this->errorResponse('Error, debe ingresar la fecha de inicio y fin del reporte.', 409);
        try {
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
        } catch (\Throwable $th) {
            $email = false;
            $email_to = 'hector@gmail.com';
        }
        $requestService = new \Illuminate\Http\Request();
        $requestService->replace(
            [
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'usuario_id' => $request->usuario_id,
                'nombre' => $request->nombre
            ]
        );

        $fecha_del_reporte = strtoupper("DEL " . fecha_abr($request->fecha_inicio) . " al " . fecha_abr($request->fecha_fin));

        $datos = $this->get_asistencia_reporte($requestService);
        if (empty($datos["tarjetas"])) {
            return $this->errorResponse('Error, no se han encontrado datos que mostrar.', 409);
        } else {
            if (!isset($request->usuario_id)) {
                $name_pdf = strtoupper("TARJETA DE ASISTENCIA DE TODOS LOS EMPLEADOS " . $fecha_del_reporte) . '.pdf';
            } else {
                $name_pdf = strtoupper("TARJETA DE ASISTENCIA DE " . $datos["tarjetas"][0]["empleado"] . " " . $fecha_del_reporte) . '.pdf';
            }
        }
        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('checador/tarjeta/reporte', ['datos' => $datos, "fecha_del_reporte" => $fecha_del_reporte, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view('checador.tarjeta.footer', ['empresa' => $empresa]),
        ]);
        //$pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 13.4);
        $pdf->setOption('margin-right', 9.4);
        $pdf->setOption('margin-top', 9.4);
        $pdf->setOption('margin-bottom', 9.4);
        $pdf->setOption('page-size', 'Letter');
        if ($email == true) {
            /**email */
            /**
             * parameters lista de la funcion
             * to destinatario
             * to_name nombre del destinatario
             * subject motivo del correo
             * name_pdf nombre del pdf
             * pdf archivo pdf a enviar
             */
            /**quiere decir que el usuario desa mandar el archivo por correo y no consultarlo */
            $email_controller = new EmailController();
            $enviar_email     = $email_controller->pdf_email(
                $email_to,
                strtoupper("reporte de asistencia"),
                'reporte de registros de checador ',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }
}
