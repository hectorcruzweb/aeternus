<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FirmasController;
use App\Operaciones;
use App\Pagos;
use App\PagosProgramados;
use App\SatFormasPago;
use App\SATMonedas;
use App\ServiciosFunerarios;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PagosController extends ApiController
{

    public function get_cobradores()
    {
        //no super usuarios
        /**puesto de venderor id 5 */
        /**obtiene los usuarios con puesto de cobrador */
        return $this->showAll(User::select('id', 'nombre')
                ->join('usuarios_puestos', 'usuarios_puestos.usuarios_id', '=', 'usuarios.id')
                ->where('roles_id', '>', 1)
                ->where('puestos_id', '=', 5)
                ->where('usuarios.status', '>', 0)
                ->get());
    }

    public function get_formas_pago_sat()
    {
        /**las formas de pago que aplican para el caso de esta empresa */
        return $this->showAll(SatFormasPago::where('clave', '<>', '99')->where('clave', '<>', '15')->get());
    }

    public function get_monedas_sat()
    {
        return $this->showAll(SATMonedas::get());
    }

    public function calcular_adeudo($referencia, $fecha = '', $multipago = 'false')
    {
        $request = new \Illuminate\Http\Request();

        $request->replace([
            'referencia' => $referencia,
            'multipago' => $multipago,
            'fecha_pago' => $fecha,
        ]);

        $validaciones = [
            'referencia' => 'required',
            'multipago' => 'required',
            'fecha_pago' => 'required|date_format:Y-m-d H:i',
        ];

        $mensajes = [
            'referencia.required' => 'Es necesario ingresar un número de referencia.',
            'multipago.required' => 'Indique la modalidad de pago.',
            'fecha_pago.required' => 'Debe ingresar una fecha de pago.',
            'fecha_pago.date_format' => 'El formato de la fecha no es correcto(Y-m-d H:i).',
        ];
        $request->validate(
            $validaciones,
            $mensajes
        );
        /**esta funcion recibe como parametros un numero de referencia
         * una fecha de pago
         * y un true o false para considerar o no un pago multiple(todas las referencias de dicha operacion que aun tienen adeudo)
         */

        /**con la referencia enviada, tenemos la forma de obtener todos los pagos asocaidos a esa operacion */
        $pago_programado = PagosProgramados::where('referencia_pago', '=', $request->referencia)->orderBy('id', 'asc')->where('status', '=', 1)->get();
        if (count($pago_programado)) {
            /**se ha encontrado la referencia y se procede a hacer los respectivos calculos */
            $resultado = Operaciones::select(
                'operaciones.id',
                'ventas_planes_id',
                'ventas_terrenos_id',
                'cuotas_cementerio_id',
                'servicios_funerarios_id',
                'operaciones.id as operacion_id',
                'operaciones.status as operacion_status',
                'total',
                'costo_neto_pronto_pago',
                'empresa_operaciones_id',
                'clientes.nombre',
                DB::raw(
                    'DATE(fecha_operacion) as fecha_operacion'
                ),
                DB::raw(
                    '(NULL) as status_texto'
                ),
                DB::raw(
                    '(NULL) as fecha_operacion_texto'
                ),
                DB::raw(
                    '(NULL) AS operacion_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar_texto'
                )
            )
                ->with('cuota_cementerio')
                ->with([
                    'pagosProgramados' => function ($query) use ($request) {
                        if ($request->multipago == 'false') {
                            /**como no es multipago, solo se filtra la referencia solicitada */
                            return $query->where('pagos_programados.referencia_pago', '=', $request->referencia)->where('status', '=', 1)->with('pagados');
                        } else {
                            return $query->with('pagados');
                        }
                    },
                ])
                ->with('AjustesPoliticas')
                ->join('clientes', 'clientes.id', '=', 'operaciones.clientes_id')
                ->where('operaciones.id', '=', $pago_programado[0]->operaciones_id)
                ->get()->toArray();

            foreach ($resultado as $index_dato => &$dato) {
                $dato['fecha_a_pagar'] = date('Y-m-d', strtotime($request->fecha_pago));
                $dato['fecha_a_pagar_texto'] = fecha_abr(date('Y-m-d', strtotime($request->fecha_pago)));
                $dato['fecha_operacion_texto'] = fecha_abr($dato['fecha_operacion']);
                /**DEFINIENDO EL STATUS DE LA dato*/
                if ($dato['operacion_status'] == 0) {
                    $dato['status_texto'] = 'Cancelada';
                } elseif ($dato['operacion_status'] == 1) {
                    $dato['status_texto'] = 'Por Pagar';
                } elseif ($dato['operacion_status'] == 2) {
                    $dato['status_texto'] = 'Pagada';
                }

                if ($dato['empresa_operaciones_id'] == 1) {
                    /**es venta de propiedad */
                    $dato['operacion_texto'] = 'Abono por Venta de Propiedad';
                } elseif ($dato['empresa_operaciones_id'] == 2) {
                    /**SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO. */
                    $dato['operacion_texto'] = 'Cuota Mtto. Cementerio | ' . $dato['cuota_cementerio']['descripcion'];
                } elseif ($dato['empresa_operaciones_id'] == 3) {
                    /**SERVICIOS FUNERARIOS. */
                    $dato['operacion_texto'] = 'Pago por Servicios Funerarios';
                } elseif ($dato['empresa_operaciones_id'] == 4) {
                    /**VENTA DE PLANES FUNERARIOS A FUTURO */
                    $dato['operacion_texto'] = 'Abono a Plan Funerario de uso a Futuro';
                } elseif ($dato['empresa_operaciones_id'] == 5) {
                    /**ERVICIOS ESPECIALES CON EXTREMIDADES */
                    $dato['operacion_texto'] = 'Pago por Servicios Especiales con Extremidades';
                } elseif ($dato['empresa_operaciones_id'] == 6) {
                    /**VENTAS EN GRAL. */
                    $dato['operacion_texto'] = 'Venta en Gral.';
                }

                /**aqui se saca el porcentaje para ver cuanto seria el descuento por pago en cada pronto pago */
                $porcentaje_descuento_pronto_pago = 0;
                if ($dato['total'] > 0) {
                    $porcentaje_descuento_pronto_pago = ($dato['costo_neto_pronto_pago']) * 100 / $dato['total'];
                }

                /**recorriendo arreglo de pagos programados */
                foreach ($dato['pagos_programados'] as $index_programado => &$programado) {
                    /**actualizando fecha de pago abre con helper de fechas */
                    $programado['fecha_programada_abr'] = fecha_abr($programado['fecha_programada']);
                    //if ($programado['status_pago'] != 2) {
                    /**actualizando el concepto del pago */
                    if ($programado['conceptos_pagos_id'] == 1) {
                        $programado['concepto_texto'] = 'Enganche';
                        /**verificando que el pago de enganche no se trate de hacer con pronto pago*/
                        if (date('Y-m-d', strtotime(substr($request->fecha_pago, 0, 10))) < date('Y-m-d', strtotime($programado['fecha_programada']))) {
                            return $this->errorResponse('El pago de tipo (Enganche) no puede ser pagado antes de su fecha programada (' . $programado['fecha_programada_abr'] . ').', 409);
                        }
                    } elseif ($programado['conceptos_pagos_id'] == 2) {
                        $programado['concepto_texto'] = 'Abono';
                    } else {
                        $programado['concepto_texto'] = 'Pago Único';
                        /**verificando que el pago de enganche no se trate de hacer con pronto pago*/
                        if (date('Y-m-d', strtotime(substr($request->fecha_pago, 0, 10))) < date('Y-m-d', strtotime($programado['fecha_programada']))) {
                            return $this->errorResponse('El pago de tipo (Pago Único) no puede ser pagado antes de su fecha programada (' . $programado['fecha_programada_abr'] . ').', 409);
                        }
                    }

                    /**aumento el pago programado vigente */
                    /**haciendo sumatoria de los montos que se han destinado a un pago programado segun el tipo de movimiento */
                    /**montos segun su tipo de movimiento */
                    $abonado_intereses = 0;
                    $abonado_capital = 0;
                    $descontado_pronto_pago = 0;
                    $descontado_capital = 0;
                    $complemento_cancelacion = 0;
                    $fecha_ultimo_pago = '';

                    if (!empty($programado['pagados'])) {
                        $pago_total = 0;
                        foreach ($programado['pagados'] as $index_pagados => &$pagado) {
                            if ($pagado['status'] == 1) {
                                /**si esta activo el pago se toma en cuenta el monto de cada operacion */
                                /**tomando en cuenta solo pagos que son parent(todos los tipos menos abono a intereses y descuento por pronto pago, estos 2 tipos
                                 * son los que van incluidos dentro de un parent) */
                                // if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                /**aqui entrarian en los abonos a capital, descuento al capital y complementos por cancelacion*/
                                if ($pagado['movimientos_pagos_id'] == 1) {
                                    /**si es de tipo 1, abono a copital, por lo regular podria llevar asociados pagos children
                                     * y se debe de recorrer el foreach para obtener los distintos montos asignados a cada pago programado
                                     */
                                    $pago_total += $pagado['monto'];
                                    $abonado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 4) {
                                    /**fue descuento al capital */
                                    $descontado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 5) {
                                    /**fue complemento por cancelacion */
                                    $complemento_cancelacion += $pagado['pagos_cubiertos']['monto'];
                                } elseif ($pagado['movimientos_pagos_id'] == 2) {
                                    /**es tipo interes */
                                    if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                        /**es abono de intereses */
                                        $abonado_intereses += $pagado['pagos_cubiertos']['monto'];
                                        $pago_total += $pagado['monto'];
                                    }
                                } elseif ($pagado['movimientos_pagos_id'] == 3) {
                                    if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                        /**es descuento por pronto pago */
                                        $descontado_pronto_pago += $pagado['pagos_cubiertos']['monto'];
                                        $pago_total += $pagado['monto'];
                                    }
                                }
                                /**fecha en que se realizo el ultimo pago */
                                $fecha_ultimo_pago = $pagado['fecha_pago'];
                                // }
                            } //fin if pago status=1
                        } //fin foreach pagado
                        /**actualizando el total del pago, cuando tiene pagos hijos */
                        $pagado['pago_total'] = $pago_total;
                    }

                    /** al final del ciclo se actualizan los valores en el pago programado*/
                    $programado['abonado_capital'] = round($abonado_capital, 2, PHP_ROUND_HALF_UP);
                    $programado['abonado_intereses'] = round($abonado_intereses, 2, PHP_ROUND_HALF_UP);
                    $programado['descontado_pronto_pago'] = round($descontado_pronto_pago, 2, PHP_ROUND_HALF_UP);
                    $programado['descontado_capital'] = round($descontado_capital, 2, PHP_ROUND_HALF_UP);
                    $programado['complementado_cancelacion'] = round($complemento_cancelacion, 2, PHP_ROUND_HALF_UP);

                    $saldo_pago_programado = $programado['monto_programado'] - $abonado_capital - $descontado_pronto_pago - $descontado_capital - $complemento_cancelacion;
                    $programado['saldo_neto'] = round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP);

                    /**asignando la fecha del pago que liquidado el pago programado */
                    if ($programado['saldo_neto'] <= 0) {
                        $programado['fecha_ultimo_pago'] = $fecha_ultimo_pago;
                    }

                    /**verificando el estado del pago programado*/
                    /**verificando si la fecha sigue vigente o esta vencida */
                    /**variables para controlar el incremento por intereses */
                    $dias_retrasados_del_pago = 0;
                    $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);

                    $fecha_a_pagar = Carbon::createFromFormat('Y-m-d', substr($request->fecha_pago, 0, 10));
                    $interes_generado = 0;
                    $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_programada']);
                    /**fin varables por intereses */
                    /**verificando que el pago programado tiene un saldo de capital que cobrar para saber si aplica o no intereses */
                    if ($programado['saldo_neto'] > 0) {
                        $programado['fecha_a_pagar'] = date('Y-m-d', strtotime($request->fecha_pago));
                        $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_a_pagar']);
                        /**tiene todavia saldo que pagar, se debe verificar si el pago esta vencido para generarle los intereses correspondientes */
                        if (date('Y-m-d', strtotime($programado['fecha_programada'])) < $programado['fecha_a_pagar']) {
                            /**no aplica pronto pago, porque la fecha ya vencio */
                            $programado['aplica_pronto_pago_b'] = 0;
                            /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                            $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_a_pagar);
                            /**
                             * Los intereses moratorios se calcularán
                             * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                             * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                             * ser hecho y la fecha que el contratante
                             * liquide el adeudo.
                             **/
                            /**aplicando intereses solo a abonos */
                            $interes_generado = 0;
                            if ($programado['conceptos_pagos_id'] == 2) {
                                $interes_generado = round(((($programado['monto_programado'] * ($dato['ajustes_politicas']['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago), 0, PHP_ROUND_HALF_UP);
                                if ($interes_generado > 0) {
                                    if ($interes_generado >= $programado['abonado_intereses']) {
                                        /**esto siginifica que la fecha de pago seria mayor o igual a la fecha en que se hizo el ultimo abono a intereses */
                                        $interes_generado -= $programado['abonado_intereses'];
                                    } else {
                                        /**fechas anteriores a la de los intereses, por lo cual salta el error de que cambio dfechas */
                                        return $this->errorResponse('Hemos detectado que hay pagos con fecha superior a la fecha de pago que desea registrar. No se puede registrar el pago con esta fecha debido a que ya fueron aplicadas las políticas de descuento e intereses en pagos con fechas anteriores.', 409);
                                    }
                                }
                            }

                            /**aqui actualizamos el saldo neto del pago con todo e intereses, quitando los intereses que ya se han pagado previamente */
                            $programado['saldo_neto'] = $saldo_pago_programado + ($interes_generado);
                            /**la fecha qui es mayor que la fecha programada del pago */
                            $programado['status_pago'] = 0;
                            $programado['status_pago_texto'] = 'Vencido';

                            $programado['dias_vencido'] = $dias_retrasados_del_pago;
                            $programado['intereses'] = $interes_generado;
                        } else {
                            /**la fecha aun no vence */
                            $programado['status_pago'] = 1;
                            $programado['status_pago_texto'] = 'Pendiente';
                            /**verifianco si la fecha es antes del pagro progrmaado para veri si tiene derecho a descuento */
                            if (date('Y-m-d', strtotime(substr($request->fecha_pago, 0, 10))) < date('Y-m-d', strtotime($programado['fecha_programada']))) {
                                /**aplicando pronto pago solo a abonos */
                                if ($programado['conceptos_pagos_id'] == 2) {
                                    $programado['aplica_pronto_pago_b'] = 1;
                                    /**calculando monto a a descontar */
                                    //return $programado['descontado_pronto_pago'];
                                    $programado['descuento_pronto_pago'] = round(((($programado['monto_programado']) - (($porcentaje_descuento_pronto_pago * ($programado['monto_programado'])) / 100))), 0, PHP_ROUND_HALF_DOWN);
                                    if ($programado['descuento_pronto_pago'] > 0) {
                                        if ($programado['descuento_pronto_pago'] >= $programado['descontado_pronto_pago']) {
                                            /**esto siginifica que la fecha de pago seria mayor o igual a la fecha en que se hizo el ultimo descuento a capital */
                                            $programado['descuento_pronto_pago'] -= $programado['descontado_pronto_pago'];
                                        } else {
                                            /**fechas anteriores a la del descuento de capital, por lo cual salta el error de que cambio dfechas */
                                            return $this->errorResponse('Hemos detectado que hay pagos con fecha superior a la fecha de pago que desea registrar. No se puede registrar el pago con esta fecha debido a que ya fueron aplicadas las políticas de descuento e intereses en pagos con fechas anteriores.', 409);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $programado['fecha_a_pagar'] = $pagado['fecha_pago'];
                        /**el pago programado ya fue cubierto */
                        $programado['status_pago'] = 2;
                        $programado['status_pago_texto'] = 'Pagado';
                    }
                    /**monto con pronto pago de cada abono */

                    $programado['monto_pronto_pago'] = round((($porcentaje_descuento_pronto_pago * $programado['monto_programado']) / 100), 2, PHP_ROUND_HALF_UP);
                    $programado['total_cubierto'] = $programado['abonado_capital'] + $programado['descontado_pronto_pago'] + $programado['descontado_capital'] + $programado['complementado_cancelacion'];

                    /**verificando el estado de la referencia que se desa consultar */
                    if ($programado['referencia_pago'] == $referencia) {
                        if ($programado['saldo_neto'] <= 0) {
                            /**el pago que se mando como refencia ya fue pagado */
                            return $this->errorResponse('La referencia que desea abonar (' . $referencia . ') ya fue liqudiada.', 409);
                        }
                    }
                    // } //fin if status_pago!=2 // pagado
                } //fin foreach programados

            } //fin foreach dato
            return $resultado;
        } else {
            /**vacio, no encontrado */
            return $this->errorResponse('No se ha encontrado el número de referencia solicitado.', 409);
        }
    }

    public function cancelar_pago(Request $request)
    {
        //return $request;
        $validaciones = [
            'pago_id' => 'required',
            'motivo.value' => 'required',
        ];

        $mensajes = [
            'required' => 'Ingrese este dato',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );
        $datos_pago = $this->get_pagos($request, $request->pago_id, false, false);
        if (!empty($datos_pago)) {
            $datos_pago = $datos_pago[0];
        } else {
            /**no hay datos */
            return $this->errorResponse('Error al buscar el movimiento a cancelar.', 409);
        }
        /**verificando que el pago a cancelar sea un tipo de pago sin subpagos y que este vigente */
        if ($datos_pago['parent_pago_id'] > 0) {
            /**es sub pago */
            return $this->errorResponse('Este tipo de pagos afectan a otros pagos, debe ingresar la clave del pago que contiene a este pago.', 409);
        }

        if ($datos_pago['status'] == 0) {
            /**este pago ya ha sid cancelado antes */
            return $this->errorResponse('Este movimimiento ya ha sido cancelado previamente.', 409);
        }

        /**checando que la operacion del movimienrto a cancelar este vigente */
        if ($datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['status'] == 0) {
            return $this->errorResponse('Hemos detectado que la operación de este movimiento ya fue cancelada. No se puede cancelar este movimiento.', 409);
        }

        try {
            DB::beginTransaction();
            $datos_venta=[];
            $datos_operacion = [];
            $cementerio_controller = new CementerioController();
            $funeraria_controller = new FunerariaController();
            $empresa_operaciones_id= $datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'];
            $saldo_tabla_operaciones=0;
            if ( $empresa_operaciones_id == 1) {
                /**es tipo de ventas de propiedades */
                $datos_venta = $cementerio_controller->get_ventas($request, $datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['ventas_terrenos_id'], '')[0];
                //verificando que no este cancelada
                if ($datos_venta['operacion_status'] == 0) {
                    return $this->errorResponse('No se puede proceder con el proceso, debido a que la operación afectada ha sido cancelada.', 409);
                }
                $saldo_tabla_operaciones=$datos_venta["saldo_neto"];
            } else if ( $empresa_operaciones_id == 2) {
                $operacion_id= $datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['id'];
                //checando si la operacion a la que pertenece este pago de cuuota no ha sido cancelada
                 $venta_terreno = $cementerio_controller->get_ventas($request, $datos_pago['referencias_cubiertas'][0]["operacion_del_pago"]["ventas_terrenos_id"], '')[0];
                 if ($venta_terreno["operacion_status"] == 0) {
                    return $this->errorResponse('No se puede proceder con el proceso, debido a que la operación afectada ha sido cancelada.', 409);
                 }
                 foreach ($venta_terreno["cuota_cementerio_terreno"] as $key => $operacion_cuota) {
                   if($operacion_cuota["id"]==$operacion_id){
                        $saldo_tabla_operaciones=$operacion_cuota["pagos_programados"][0]["saldo_neto"];
                        break;
                   }
                 }
                //return $this->errorResponse($saldo_tabla_operaciones+$datos_pago['referencias_cubiertas'][0]['monto'], 409);
            } else if ( $empresa_operaciones_id == 3) {
                /**servicios funerarios */
                $datos_venta = $funeraria_controller->get_solicitudes_servicios($request, $datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['servicios_funerarios_id'], '')[0];
                /**verificnado si la operacion no esta cancelada o pagada */
                if ($datos_venta['operacion']['operacion_status'] == 0) {
                    return $this->errorResponse('No se puede proceder con el proceso, debido a que la operación afectada ha sido cancelada.', 409);
                }
                $saldo_tabla_operaciones=$datos_venta['operacion']["saldo_neto"];
            } else if ( $empresa_operaciones_id == 4) {
                /**venta de planes a futuro */
                $datos_venta = $funeraria_controller->get_ventas($request, $datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['ventas_planes_id'], '')[0];
                //verificando que no este cancelada
                if ($datos_venta['operacion_status'] == 0) {
                    return $this->errorResponse('No se puede proceder con el proceso, debido a que la operación afectada ha sido cancelada.', 409);
                }
                $saldo_tabla_operaciones=$datos_venta["saldo_neto"];
            }
            /** cambiando el estatus de la operacion*/
            DB::table('operaciones')->where('id', $datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['id'])->update(
                [
                    'status' => 1,
                    'saldo' => round($saldo_tabla_operaciones + $datos_pago['monto_pago'], 2)
                ]
            );
            /** cambiando el estatus del pago/movimiento*/
            DB::table('pagos')->where('id', $request->pago_id)->Orwhere('parent_pago_id', $request->pago_id)->update(
                [
                    'status' => 0,
                    'motivos_cancelacion_id' => $request['motivo.value'],
                    'fecha_cancelacion' => now(),
                    'cancelo_id' => (int) $request->user()->id,
                    'nota_cancelacion' => $request->comentario,
                ]
            );
            DB::commit();
            return $request->pago_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function guardar_pago(Request $request, $tipo_operacion = '')
    {
        //return $request;
        $validaciones = [
            'empresa_operaciones_id' => 'required',
            'multipago' => 'required',
            'fecha_pago' => 'required|date_format:Y-m-d H:i',
            'pagos_a_cubrir' => 'required',
            'pagos_a_cubrir.*.referencia_pago' => 'required',
            'pagos_a_cubrir.*.fecha_a_pagar' => 'required|date_format:Y-m-d',
            'abono' => 'numeric|required|gt:0|gte:descuento_pronto_pago|gte:intereses',
            'intereses' => 'numeric|required|min:0',
            'descuento_pronto_pago' => 'numeric|required|min:0|lte:abono',
            'total' => 'numeric|required|min:0',
            'formaPago.value' => 'required',
            'cobrador.value' => 'required',
            'moneda.value' => 'required',
            'pago_con_cantidad' => '',
            'cambio_pago' => '',
        ];

        if (isset($request->formaPago['value'])) {
            if ($request->formaPago['value'] == 1) {
                $validaciones['pago_con_cantidad'] = 'numeric|required|min:' . $request->total;
                $validaciones['cambio_pago'] = 'numeric|required|min:0';
            } else {

                /**verificnado que no tenga contidad con que pago incluido cuando es por remision de deuda*/
                if ($request->formaPago['value'] != 7) {
                    $request->pago_con_cantidad = $request->total;
                } else {
                    /**no debe de haber valores mayores a cero */
                    $request->pago_con_cantidad = 0;
                }
                $request->cambio_pago = 0;
            }
        } else {
            return $this->errorResponse('No se ha ingresado una forma de pago.', 409);
        }

        $mensajes = [

            'pago_con_cantidad.required' => 'Debe ingresar la $ Cantidad con que se realizó el pago.',
            'pago_con_cantidad.min' => 'La $ Cantidad con que se realizó el pago debe ser al menos $ ' . number_format((float) $request->total, 2),
            'cambio_pago.required' => 'Debe ingresar la $ Cantidad que se devolvió como cambio.',
            'cambio_pago.min' => 'La $ Cantidad con que se realizó el pago debe ser al menos $ ' . number_format(0, 2),
            'formaPago.value.required' => 'Debe ingresar la clave de la forma de pago.',
            'moneda.value.required' => 'Debe ingresar la clave del tipo de moneda de cambio.',
            'cobrador.value.required' => 'Debe ingresar la clave del cobrador que realizó el cobro.',
            '*.fecha_a_pagar.required' => 'Debe ingresar la fecha de pago en cada una de las referencias.',
            '*.fecha_a_pagar.date_format' => 'El formato de la fecha no es correcto(Y-m-d).',
            '*.referencia_pago.required' => 'Debe ingresar las referencias de pago.',
            'pagos_a_cubrir.required' => 'Debe ingresar el conjunto de pagos a cubrir.',
            'multipago.required' => 'Indique la modalidad de pago.',
            'fecha_pago.required' => 'Debe ingresar una fecha de pago.',
            'fecha_pago.date_format' => 'El formato de la fecha no es correcto(Y-m-d H:i).',
            /**cantidades del pago */
            'abono.required' => 'Ingrese la $ cantidad del Abono.',
            'abono.numeric' => 'La $ cantidad del Abono debe ser un número valido.',
            'abono.min' => 'La $ cantidad del Abono debe ser mayor o igual a cero.',
            'abono.gt' => 'La $ cantidad del Abono debe ser mayor a cero pesos.',
            'abono.gte' => 'La $ cantidad del Abono debe ser mayor o igual a los intereses o descuento.',
            'intereses.required' => 'Ingrese la $ cantidad de Intereses.',
            'intereses.numeric' => 'La $ cantidad de Intereses debe ser un número valido.',
            'intereses.min' => 'La $ cantidad de Intereses debe ser mayor o igual a cero.',
            'descuento_pronto_pago.required' => 'Ingrese la $ cantidad de Descuento por Pronto Pago.',
            'descuento_pronto_pago.lte' => 'El descuento por pronto pago dede ser menor o igual que el abono a capital.',
            'descuento_pronto_pago.numeric' => 'La $ cantidad de Descuento por Pronto Pago debe ser un número valido.',
            'descuento_pronto_pago.min' => 'La $ cantidad de Descuento por Pronto Pago debe ser mayor o igual a cero.',
            'total.required' => 'Ingrese la $ cantidad Total del Pago.',
            'total.numeric' => 'La $ cantidad Total del Pago debe ser un número valido.',
            'total.min' => 'La $ cantidad Total del Pago debe ser mayor o igual a cero.',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        foreach ($request->pagos_a_cubrir as $index_referencia => $referencia) {
            /**verificando que las fechas enviadas en los pagoa a cubrir y la seleccionada al calcular el adeudo coincidan */
            if (date('Y-m-d', strtotime($referencia['fecha_a_pagar'])) != date('Y-m-d', strtotime($request->fecha_pago))) {
                /**las fechas coinciden */
                return $this->errorResponse('Hemos detectado que la fecha de pago ha cambiado, por favor vuelva a calcular el adeudo.', 409);
            }
        }
        /**las validaciones han pasado y se procede a guardar los datos */
        //calcular_adeudo($referencia, $fecha = '', $multipago = 'false')
        /**verificando si el pago es multipago */
        $referencias_adeudos = array();
        if (count($request->pagos_a_cubrir) > 1) {
            /**es multipago */
            $referencias_adeudos = $this->calcular_adeudo($request->referencia, $request->fecha_pago, 'true');
        } else if (count($request->pagos_a_cubrir) == 1) {
            /**es unipago */
            $referencias_adeudos = $this->calcular_adeudo($request->referencia, $request->fecha_pago, 'false');
        } else {
            /**no hay pagoa a cubrir */
            return $this->errorResponse('No se ha encontrado ninguna referencia de pago a cubrir.', 409);
        }
       
        /**checar si el pago no esta siendo hecho antes de la fecha de la venta */
        if (date('Y-m-d', strtotime(substr($request->fecha_pago, 0, 10))) < date('Y-m-d', strtotime($referencias_adeudos[0]['fecha_operacion']))) {
            return $this->errorResponse('No se pueden registrar pagos con fecha anterior a la fecha de la venta/operación (' . $referencias_adeudos[0]['fecha_operacion_texto'] . ').', 409);
        }

        //operacion_id

        /**se obtienen los valores actualizados con la fecha y cantidades a cubrir */
        /**cantidades enviadas por el usuario para procesar el pago */
        $abono = round(($request->abono), 2, PHP_ROUND_HALF_UP);
        $intereses = round($request->intereses, 2, PHP_ROUND_HALF_UP);
        $descuento_pronto_pago = round($request->descuento_pronto_pago, 2, PHP_ROUND_HALF_UP);
        $total = round($request->total, 2, PHP_ROUND_HALF_UP);
        $pago_con_cantidad = round($request->pago_con_cantidad, 2, PHP_ROUND_HALF_UP);
        $cambio_pago = round($request->cambio_pago, 2, PHP_ROUND_HALF_UP);
        $monto_pago_parent = $abono - $descuento_pronto_pago; //el pago parent ha registrar, el abono menos el descuento
        /**verificando que no haya descuento por pronyo pago ni intereses si la forma de pago es remision de deuda */
        if ($request->formaPago['value'] == 7) {
            /**remision de deuda, descuento directo al capital */
            if ($intereses > 0 || $descuento_pronto_pago > 0) {
                /**no debe de haber valores mayores a cero */
                return $this->errorResponse('El pago que desea realizar no puede llevar valores de intereses ni descuentos por pronto pago.', 409);
            }
        }

        /**validando los totales */

        if (round(($abono - $descuento_pronto_pago + $intereses), 2, PHP_ROUND_HALF_UP) > $total) {
            return $this->errorResponse('Hemos encontrado errores en la captura de las cantidades a pagar, por favor verifique y vuelva a intentar.', 409);
        }
        /**verificando que los pagos a distribuir en las referencias sean correctos para poder hacer las insercciones de pagos y distribucion en referencias */
        $abono_a_cubrir = $monto_pago_parent;
        $intereses_a_cubrir = $intereses;
        $descuento_pronto_pago_a_cubrir = $descuento_pronto_pago;
        $array_abonos_cubrir = array();
        $array_intereses_cubrir = array();
        $array_descuento_a_cubrir = array();
        $array_pagos_programados_id = array();

        $id_tipo_movimiento = 1;
        /**por default es abono a capital  */
        /**verificando que si es renision de deuda se aplique la clave de movimiento 4 //descuento a capital*/
        if ($request->formaPago['value'] == 7) {
            $id_tipo_movimiento = 4;
        }
        try {
            DB::beginTransaction();
            try {
                /**verificando que la operaicon no este cancelada */
                $datos_operacion = $referencias_adeudos[0];
                $datos_venta = [];
                $cementerio_controller = new CementerioController();
                $funeraria_controller = new FunerariaController();
                //con este indicador decido si hago el commit antes de llegar a registrar el pago, porque ya este pagada esa opeacion
                $commit=false;
                /**verificando que tipo de operacion_empresa es */
                if ($datos_operacion['empresa_operaciones_id'] == 1) {
                    /**es tipo de ventas de propiedades */
                    $datos_venta = $cementerio_controller->get_ventas($request, $datos_operacion['ventas_terrenos_id'], '')[0];
                    //verificando que no este cancelada
                    if ($datos_venta['operacion_status'] == 0) {
                        return $this->errorResponse('No se puede proceder con el pago, debido a que la operación afectada ha sido cancelada.', 409);
                    }
                    if (round($datos_venta['saldo_neto'], 2, PHP_ROUND_HALF_UP) <= 0) {
                        /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                        DB::table('operaciones')->where('id', $datos_venta['operacion_id'])->update(
                            [
                                /**status de ya liquidada */
                                'status' => 2,
                                'saldo' => 0,
                            ]
                        );
                        /**generando el numero de titulo de la venta de propiedad */
                        $cementerio_controller->generarNumeroTitulo($datos_operacion['operacion_id'], true);
                        $commit=true;
                    }
                } else if ($datos_operacion['empresa_operaciones_id'] == 2) {
                    //checando si la operacion a la que pertenece este pago de cuuota no ha sido cancelada
                    $venta_terreno = $cementerio_controller->get_ventas($request, $datos_operacion['ventas_terrenos_id'], '')[0];
                    if ($venta_terreno["operacion_status"] == 0) {
                        return $this->errorResponse("Error, la venta de propiedad al que pertenece este pago de cuota ha sido cancelada.", 409);
                    }
                    /**cuotas de mantenimiento en cementerio */
                    //buscamos la cuota en cuestion para ver su saldo
                    if ($datos_operacion['pagos_programados'][0]["saldo_neto"] <= 0) {
                        /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                        DB::table('operaciones')->where('id', $datos_operacion['operacion_id'])->update(
                            [
                                /**status de ya liquidada */
                                'status' => 2,
                                'saldo' => 0,
                            ]
                        );
                        $commit=true;
                    }
                } else if ($datos_operacion['empresa_operaciones_id'] == 3) {
                    /**servicios funerarios */
                    $datos_venta = $funeraria_controller->get_solicitudes_servicios($request, $datos_operacion['servicios_funerarios_id'], '')[0];
                    /**verificnado si la operacion no esta cancelada o pagada */
                    if ($datos_venta['operacion']['operacion_status'] == 0) {
                        return $this->errorResponse('No se puede proceder con el pago, debido a que la operación afectada ha sido cancelada.', 409);
                    }
                    if (round($datos_venta['operacion']['saldo_neto'], 2, PHP_ROUND_HALF_UP) <= 0) {
                        /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                        DB::table('operaciones')->where('id', $datos_venta['operacion']['operacion_id'])->update(
                            [
                                /**status de ya liquidada */
                                'status' => 2,
                                "saldo" => 0,
                            ]
                        );
                        $commit=true;
                    }

                } else if ($datos_operacion['empresa_operaciones_id'] == 4) {
                    /**venta de planes a futuro */
                    $datos_venta = $funeraria_controller->get_ventas($request, $datos_operacion['ventas_planes_id'], '')[0];
                    if ($datos_venta['operacion_status'] == 0) {
                        return $this->errorResponse('No se puede proceder con el pago, debido a que la operación afectada ha sido cancelada.', 409);
                    }
                    if (round($datos_venta['saldo_neto'], 2, PHP_ROUND_HALF_UP) <= 0) {
                        /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                        DB::table('operaciones')->where('id', $datos_venta['operacion_id'])->update(
                            [
                                /**status de ya liquidada */
                                'status' => 2,
                                'saldo' => 0,
                            ]
                        );
                        $commit=true;
                    }
                }
                if($commit){
                    DB::commit();
                    return $this->errorResponse('Esta operación ya está pagada, no se puede seguir agregando más pagos.', 409);
                }

                foreach ($request->pagos_a_cubrir as $index_referencia => $referencia) {
                    $encontrada = false;
                    foreach ($referencias_adeudos[0]['pagos_programados'] as $index_programado => $programado) {
                        /**el paog programada se recorre aqui y se compara con las referencias enviadas para pago */
                        if ($programado['referencia_pago'] == $referencia['referencia_pago']) {
                            /**agregando los ids para ver si ya fueron afectadoes en un pago anterior con fechas anteriores y mostrar el error */
                            array_push($array_pagos_programados_id, $programado['id']);
                            /**referencia encontrada */
                            $encontrada = true;
                            /**checando el estatus del pago programada */
                            if ($programado['status_pago'] == 2) {
                                /**el pago ya habia sido saldado, no es habil que venta en la solicitud de pago*/
                                return $this->errorResponse('Ocurrió un error al guardar el pago, uno de los pagos que desea pagar (' . $referencia['referencia_pago'] . ') ya ha sido cubierto previamente. Por favor cierre su aplicación y vuelva a intentar.', 409);
                            } else {
                                if ($abono_a_cubrir > 0) {
                                    /**verificando la cantidad de intereses moratorios */
                                    if ($intereses_a_cubrir > 0) {
                                        if ($programado['intereses'] > 0) {
                                            /**solo si tiene intereses */
                                            if ($programado['intereses'] >= $intereses_a_cubrir) {
                                                array_push($array_intereses_cubrir, [
                                                    'referencia_pago' => $programado['referencia_pago'],
                                                    'pagos_programados_id' => $programado['id'],
                                                    'monto' => $intereses_a_cubrir,
                                                    'movimientos_pagos_d' => 2, //abono a intereses
                                                ]);
                                                /**se acaba el intereses_a_cubrir */
                                                $intereses_a_cubrir = 0;
                                            } else {
                                                array_push($array_intereses_cubrir, [
                                                    'referencia_pago' => $programado['referencia_pago'],
                                                    'pagos_programados_id' => $programado['id'],
                                                    'monto' => $programado['intereses'],
                                                    'movimientos_pagos_d' => 2, //abono a intereses
                                                ]);
                                                /**se puede asignar el 100 de intereses programados */
                                                $intereses_a_cubrir -= $programado['intereses'];
                                            }
                                        }
                                    } //fin if intereses > 0

                                    /**verificando la cantidad de descuento por pronto pago */
                                    if ($descuento_pronto_pago_a_cubrir > 0) {
                                        if ($programado['descuento_pronto_pago'] > 0) {
                                            /**solo si tiene descuento_pronto_pago */
                                            if ($programado['descuento_pronto_pago'] >= $descuento_pronto_pago_a_cubrir) {
                                                array_push($array_descuento_a_cubrir, [
                                                    'referencia_pago' => $programado['referencia_pago'],
                                                    'pagos_programados_id' => $programado['id'],
                                                    'monto' => $descuento_pronto_pago_a_cubrir,
                                                    'movimientos_pagos_d' => 3, //descuento por pronto pago
                                                ]);
                                                /**se acaba el des$descuento_pronto_pago_a_cubrir */
                                                $descuento_pronto_pago_a_cubrir = 0;
                                            } else {
                                                array_push($array_descuento_a_cubrir, [
                                                    'referencia_pago' => $programado['referencia_pago'],
                                                    'pagos_programados_id' => $programado['id'],
                                                    'monto' => ($programado['descuento_pronto_pago']),
                                                    'movimientos_pagos_d' => 3, //descuento por pronto pago
                                                ]);
                                                /**se puede asignar el 100 de descuento_pronto_pago */
                                                $descuento_pronto_pago_a_cubrir -= $programado['descuento_pronto_pago'];
                                            }
                                        }
                                    } //fin if descuento_pronto_pago_a_cubrir > 0

                                    /**haciendo los arrays de pagos a distriburir */
                                    /**se puede verificar cantidad a pagar del abono */
                                    $monto_programado_restante = $programado['monto_programado'] - $programado['total_cubierto'];
                                    if ($abono_a_cubrir > 0) {
                                        $monto_con_descuento = $monto_programado_restante;
                                        if ($descuento_pronto_pago > 0) {
                                            /**verificando si el descuento que se aplicara a este pago es todavia mayor al al descuento de pronto pago programado */
                                            if ($programado['descuento_pronto_pago'] <= $descuento_pronto_pago) {
                                                /**el descuento no aplica 100% el programado debido a que el descuento programado sobrepasa lo que el usuario desea descontar */
                                                $monto_con_descuento = $monto_programado_restante - $programado['descuento_pronto_pago'];
                                            }
                                        }

                                        if ($monto_con_descuento >= $abono_a_cubrir) {
                                            array_push($array_abonos_cubrir, [
                                                'referencia_pago' => $programado['referencia_pago'],
                                                'pagos_programados_id' => $programado['id'],
                                                'monto' => round($abono_a_cubrir, 2, PHP_ROUND_HALF_UP),
                                                'movimientos_pagos_d' => $id_tipo_movimiento,
                                            ]);
                                            /**se acaba el abono_a_cubrir */
                                            $abono_a_cubrir = 0;
                                        } else {
                                            /**se puede asignar el 100 del pago programado */
                                            array_push($array_abonos_cubrir, [
                                                'referencia_pago' => $programado['referencia_pago'],
                                                'pagos_programados_id' => $programado['id'],
                                                'monto' => round($monto_con_descuento, 2, PHP_ROUND_HALF_UP),
                                                'movimientos_pagos_d' => $id_tipo_movimiento,
                                            ]);
                                            $abono_a_cubrir -= round($monto_con_descuento, 2, PHP_ROUND_HALF_UP);
                                        }
                                    }
                                } //fin if abono a cubrir >0
                                break;
                            }
                        } //fin if referencia de pago
                    } //fin foreach de pagros programados

                    if ($encontrada == false) {
                        return $this->errorResponse('Ocurrió un error al guardar el pago, uno de los pagos que desea pagar (' . $referencia['referencia_pago'] . ') no fue econtontrado en la lista de posibles pagos a cubrir según la venta.', 409);
                    }
                } //fin de foreach de pagos a cubrir
            } catch (\Throwable $th) {
                //throw $th;
                return $this->errorResponse('Ocurrió un error al guardar el pago, por favor reintente.', 409);
            }
            /**al final de distribuir las cantidades es sus respectivos pagos, sedebe verificar que no quedaron montos remanenetes que no se hayan
             * distribuido en sus respectivas referencias
             */
            if ((round($abono_a_cubrir, 2, PHP_ROUND_HALF_UP) != 0 || round($intereses_a_cubrir, 2, PHP_ROUND_HALF_UP) != 0 || round($descuento_pronto_pago_a_cubrir, 2, PHP_ROUND_HALF_UP) != 0)) {
                return $this->errorResponse('Hemos encontrado errores en el registro de este pago debido a que las cantidades ingresadas no cuadran según la operación que está realizando, por favor verifique que el descuento o interés que desea cobrar están en relación a los pagos que desea pagar. Por favor vuelva a intentar la operación.', 409);
            }

            //return $array_intereses_cubrir;

            /**antes de insertar el pago se debe de verificar que no existan pagos con fecha superior al pago que se va hacer,
             * puesp eso haria que se perdiera el control sobre el manejo de intereses y descuentos
             */

            $pago_despues_fecha_pago = DB::table('pagos')->select('pagos.id AS pagos_antes_de_fecha')
                ->join('pagos_pagos_programados', 'pagos_pagos_programados.pagos_id', '=', 'pagos.id')
                ->where('pagos.status', '=', 1)
                ->where('pagos.fecha_pago', '>', $request->fecha_pago)
                ->whereIn('pagos_pagos_programados.pagos_programados_id', $array_pagos_programados_id)->get();

            if (count($pago_despues_fecha_pago) > 0) {
                return $this->errorResponse('Hemos detectado que hay pagos con fecha superior a la fecha de pago que desea registrar. No se puede registrar el pago con esta fecha debido a que ya fueron aplicadas las políticas de descuento e intereses en pagos con fechas anteriores.', 409);
            }
            $array_pagos_programados_id;

            $id_abono_capital = DB::table('pagos')->insertGetId(
                [
                    'monto_pago' => $monto_pago_parent,
                    'total_pago' => round($total, 2, PHP_ROUND_HALF_UP),
                    /**solo la cantidad primero que va destinada al pago parent*/
                    'pago_con_cantidad' => $pago_con_cantidad,
                    'cambio_pago' => $cambio_pago,
                    'fecha_registro' => now(),
                    'banco' => $request->banco,
                    'referencia' => $request->referencia_sobre_pago,
                    'fecha_pago' => $request->fecha_pago, //fecha de la venta
                    'registro_id' => (int) $request->user()->id,
                    'cobrador_id' => $request->cobrador['value'],
                    'sat_formas_pago_id' => $request->formaPago['value'],
                    'movimientos_pagos_id' => $id_tipo_movimiento,
                    'nota' => $request->nota,
                    'sat_monedas_id' => $request->moneda['value'],
                    'tipo_cambio' => 1, //1 pesos,
                ]
            );

            /**guardando los de tipo abono a capital */
            foreach ($array_abonos_cubrir as $key => $pago) {
                DB::table('pagos_pagos_programados')->insert(
                    [
                        'pagos_id' => $id_abono_capital,
                        'monto' => $pago['monto'],
                        'pagos_programados_id' => $pago['pagos_programados_id'],
                        'movimientos_pagos_id' => $id_tipo_movimiento,
                    ]
                );
            }
            /**fin de captura de pagos */

            /**guardando children de descuento e intereses */
            $id_descuento_pronto_pago = '';
            if ($descuento_pronto_pago > 0) {
                $id_descuento_pronto_pago = DB::table('pagos')->insertGetId(
                    [
                        'monto_pago' => $descuento_pronto_pago,
                        'total_pago' => round($total, 2, PHP_ROUND_HALF_UP),
                        /**solo la cantidad primero que va destinada al pago parent*/
                        'pago_con_cantidad' => 0,
                        'cambio_pago' => 0,
                        'fecha_registro' => now(),
                        'fecha_pago' => $request->fecha_pago, //fecha de la venta
                        'registro_id' => (int) $request->user()->id,
                        'cobrador_id' => $request->cobrador['value'],
                        'sat_formas_pago_id' => $request->formaPago['value'],
                        'movimientos_pagos_id' => 3, //descuento pronto pago
                        'sat_monedas_id' => $request->moneda['value'],
                        'tipo_cambio' => 1, //1 pesos,
                        'parent_pago_id' => $id_abono_capital,
                    ]
                );

                /**guardando los de tipo descuento */
                foreach ($array_descuento_a_cubrir as $key => $descuento) {
                    DB::table('pagos_pagos_programados')->insert(
                        [
                            'pagos_id' => $id_descuento_pronto_pago,
                            'monto' => $descuento['monto'],
                            'pagos_programados_id' => round($descuento['pagos_programados_id'], 2, PHP_ROUND_HALF_UP),
                            'movimientos_pagos_id' => 3, //descuento pronto pago
                        ]
                    );
                }
                //return $array_descuento_a_cubrir;
                /**fin de los de tipo descuento */
            }

            $id_intereses = '';
            if ($intereses > 0) {
                $id_intereses = DB::table('pagos')->insertGetId(
                    [
                        'monto_pago' => $intereses,
                        'total_pago' => round($total, 2, PHP_ROUND_HALF_UP),
                        /**solo la cantidad primero que va destinada al pago parent*/
                        'pago_con_cantidad' => 0,
                        'cambio_pago' => 0,
                        'fecha_registro' => now(),
                        'fecha_pago' => $request->fecha_pago, //fecha de la venta
                        'registro_id' => (int) $request->user()->id,
                        'cobrador_id' => $request->cobrador['value'],
                        'sat_formas_pago_id' => $request->formaPago['value'],
                        'movimientos_pagos_id' => 2, //abono a intereses
                        'sat_monedas_id' => $request->moneda['value'],
                        'tipo_cambio' => 1, //1 pesos,
                        'parent_pago_id' => $id_abono_capital,
                    ]
                );
                /**guardando los de tipo intereses */
                foreach ($array_intereses_cubrir as $key => $interes) {
                    DB::table('pagos_pagos_programados')->insert(
                        [
                            'pagos_id' => $id_intereses,
                            'monto' => $interes['monto'],
                            'pagos_programados_id' => $interes['pagos_programados_id'],
                            'movimientos_pagos_id' => 2, //abono a intereses
                        ]
                    );
                }
                /**fin de los de tipo intereses */
            }
            DB::commit();

            /**se deve revisar si la operacion fue liquidada para marcar la venta como liquidada con status 2 */
            $datos_operacion = $referencias_adeudos[0];
            $cementerio_controller = new CementerioController();
            /**verificando que tipo de operacion_empresa es */
            if ($datos_operacion['empresa_operaciones_id'] == 1) {
                /**es tipo de ventas de propiedades */
                $datos_venta = $cementerio_controller->get_ventas($request, $datos_operacion['ventas_terrenos_id'], '')[0];
                if (round($datos_venta['saldo_neto'], 2, PHP_ROUND_HALF_UP) <= 0) {
                    /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                    DB::table('operaciones')->where('id', $datos_venta['operacion_id'])->update(
                        [
                            /**status de ya liquidada */
                            'status' => 2,
                            'saldo'=>0
                        ]
                    );
                    /**generando el numero de titulo de la venta de propiedad */
                    $cementerio_controller->generarNumeroTitulo($datos_operacion['operacion_id'], true);
                }else{
                    DB::table('operaciones')->where('id', $datos_venta['operacion_id'])->update(
                        [
                            /**status de ya liquidada */
                            'saldo'=>round($datos_venta['saldo_neto'], 2, PHP_ROUND_HALF_UP)
                        ]
                    );
                }
            } else if ($datos_operacion['empresa_operaciones_id'] == 2) {
                //una vez aplicado el pago recalculo el total para guardar el saldo nuevo de dicha operacion
                $datos_operacion = $this->calcular_adeudo($request->referencia, $request->fecha_pago, 'false')[0];
                /**es tipo de ventas de propiedades */
                $datos_venta = $cementerio_controller->get_ventas($request, $datos_operacion['cuotas_cementerio_id'], '')[0];
                if ($datos_operacion['pagos_programados'][0]["saldo_neto"] <= 0) {
                    /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                    DB::table('operaciones')->where('id', $datos_venta['operacion_id'])->update(
                        [
                            /**status de ya liquidada */
                            'status' => 2,
                            'saldo'=>0
                        ]
                    );
                }else{
                    DB::table('operaciones')->where('id', $datos_operacion['operacion_id'])->update(
                        [
                            /**status de ya liquidada */
                            'saldo'=>$datos_operacion['pagos_programados'][0]["saldo_neto"]
                        ]
                    );
                }
            } else if ($datos_operacion['empresa_operaciones_id'] == 4) {
                /**venta de planes a futuro */
                $funeraria_controller = new FunerariaController();
                $datos_venta = $funeraria_controller->get_ventas($request, $datos_operacion['ventas_planes_id'], '')[0];
                if (round($datos_venta['saldo_neto'], 2, PHP_ROUND_HALF_UP) <= 0) {
                    /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                    DB::table('operaciones')->where('id', $datos_venta['operacion_id'])->update(
                        [
                            /**status de ya liquidada */
                            'status' => 2,
                            'saldo'=>0
                        ]
                    );
                }else{
                    DB::table('operaciones')->where('id', $datos_venta['operacion_id'])->update(
                        [
                            /**status de ya liquidada */
                            'saldo'=>round($datos_venta['saldo_neto'], 2, PHP_ROUND_HALF_UP)
                        ]
                    );
                }
            } else if ($datos_operacion['empresa_operaciones_id'] == 3) {
                /**servicios funerarios */
                $funeraria_controller = new FunerariaController();
                $datos_venta = $funeraria_controller->get_solicitudes_servicios($request, $datos_operacion['servicios_funerarios_id'], '')[0];
                if (round($datos_venta['operacion']['saldo_neto'], 2, PHP_ROUND_HALF_UP) <= 0) {
                    /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                    DB::table('operaciones')->where('id', $datos_venta['operacion']['operacion_id'])->update(
                        [
                            /**status de ya liquidada */
                            'status' => 2,
                            'saldo'=>0
                        ]
                    );
                }
                else {
                    /**tiene cero saldo y se debe de modificar el status a pagado de la venta (2) */
                    DB::table('operaciones')->where('id', $datos_venta['operacion']['operacion_id'])->update(
                        [
                            /**status de ya liquidada */
                            'saldo'=>round($datos_venta['operacion']['saldo_neto'], 2, PHP_ROUND_HALF_UP)
                        ]
                    );
                }
            }

            return $id_abono_capital;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function get_pagos(Request $request, $id_pago = 'all', $paginated = false, $ver_sub_pagos = false)
    {
       /* if (isset($request->operacion_id)) {
            return $this->errorResponse($request->operacion_id, 409);
        }else
        if (isset($request->operaciones_id)) {
            return $this->errorResponse($request->operaciones_id, 409);
        }*/
        try {
            $status = $request->status;
            $fecha_pago = $request->fecha_pago;
            /**filtros de este servicio */
            /**
             * se podra filtrar por numero de pago
             * fecha de pago
             * numero de operacion
             * filtro de clave unica de servicio(venta_id, servicio funerario id, etc)
             * nombre del cliente
             * nombre del cobrador
             * nombre del registrador
             * tipo de servicio
             * status
             * referencia de pago
             * tipo movimiento de pago id
             */

            $resultado_query = Pagos::select(
                '*',
                DB::raw(
                    '(0) AS deuda_cubierta'
                    /**es la suma de lo abonado a capital mas lo abonado a descuento por pronto pago segun aplique el tipo de pago movimiento */
                ),
                DB::raw(
                    '(NULL) AS movimientos_pagos_texto'
                ),
                DB::raw(
                    '(NULL) AS status_texto'
                ),
                DB::raw(
                    '(NULL) AS motivos_cancelacion_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_pago_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_registro_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_cancelacion_texto'
                ),
                DB::raw(
                    '(NULL) AS pago_con_cantidad_texto'
                ),
                DB::raw(
                    '(NULL) AS monto_pago_texto'
                ),
                DB::raw(
                    '(NULL) AS cambio_pago_texto'
                ),
                DB::raw(
                    '(NULL) AS total_pagado_operacion'
                    /**solo cuando lleva descuentos */
                ),
                DB::raw(
                    '(NULL) AS intereses_aplicados'
                ),
                DB::raw(
                    '(NULL) AS descuento_pronto_pago_aplicado'
                ),
                DB::raw(
                    '(NULL) AS tipo_operacion_texto'
                ),
                DB::raw(
                    '(NULL) AS status_texto'
                )
            )
            // ->with(['referencias_cubiertas:id,referencia_pago,operaciones_id,monto_programado,fecha_programada,conceptos_pagos_id'])
                ->whereHas('referencias_cubiertas', function ($q) {
                    //$q->where('referencia_pago', '=', '00120200101025');
                })
                ->with('referencias_cubiertas.operacion_del_pago.cuota_cementerio:id,descripcion')
                ->with('referencias_cubiertas.operacion_del_pago:id,clientes_id,total,empresa_operaciones_id,status,ventas_terrenos_id,ventas_planes_id,servicios_funerarios_id,cuotas_cementerio_id,saldo', 'referencias_cubiertas.operacion_del_pago.cliente:id,nombre,email')
                ->whereHas('referencias_cubiertas.operacion_del_pago', function ($q) use ($request) {
                    if (isset($request->operacion_id)) {
                        if($request->operacion_id!=null)
                        $q->where('id', '=', $request->operacion_id);
                    }else
                    if (isset($request->operaciones_id)) {
                        if($request->operaciones_id!=null)
                        $q->whereIn('id', $request->operaciones_id);
                    }
                })
                ->with(['registro:id,nombre', 'sat_moneda', 'cobrador:id,nombre', 'cancelador:id,nombre', 'subpagos.referencias_cubiertas', 'forma_pago'])
                ->whereHas('forma_pago', function ($q) {
                    //$q->where('id', '=', 1);
                })
                ->where(function ($q) use ($id_pago) {
                    if (is_numeric(trim($id_pago))) {
                        $q->where('pagos.id', '=', $id_pago);
                    }
                })
                ->where(function ($q) use ($fecha_pago) {
                    if (trim($fecha_pago)) {
                        $q->where('pagos.fecha_pago', '=', $fecha_pago);
                    }
                })
                ->where(function ($q) use ($ver_sub_pagos) {
                    if (filter_var($ver_sub_pagos, FILTER_VALIDATE_BOOLEAN) == false) {
                        $q->where('pagos.movimientos_pagos_id', '<>', 2)->where('pagos.movimientos_pagos_id', '<>', 3)->with('parent_pago');
                    }
                })
                ->where(function ($q) use ($status) {
                    if (trim($status) != '') {
                        $q->where('pagos.status', '=', $status);
                    }
                })
                ->orderBy('pagos.id', 'desc')
                ->orderBy('movimientos_pagos_id', 'asc')

            //->where('parent_pago_id', '<>', NULL)
                ->get();
            $resultado = array();
            if ($paginated == 'paginated') {
                /**queire el resultado paginado */
                $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
                $resultado = &$resultado_query['data'];
            } else {
                $resultado_query = $resultado_query->toArray();
                $resultado = &$resultado_query;
            }

            foreach ($resultado as $key_pago => &$pago) {
                /**estatus del pago */

                if ($pago['status'] == 1) {
                    $pago['status_texto'] = 'Activo';
                } else {
                    $pago['fecha_cancelacion_texto'] = fecha_abr($pago['fecha_cancelacion']);
                    $pago['status_texto'] = 'Cancelado';
                    if ($pago['motivos_cancelacion_id'] == 1) {
                        /**fue por fal de pago */
                        $pago['motivos_cancelacion_texto'] = 'falta de pago';
                    } elseif ($pago['motivos_cancelacion_id'] == 2) {
                        /**fue por peticion de lciente */
                        $pago['motivos_cancelacion_texto'] = 'a petición del cliente';
                    } elseif ($pago['motivos_cancelacion_id'] == 3) {
                        /**fue por error de captura */
                        $pago['motivos_cancelacion_texto'] = 'error de captura';
                    }
                    /**actualizando el motivo de cancelacion */
                }

                /**modificando el concepto del movimiento dle pago */
                if ($pago['movimientos_pagos_id'] == 1) {
                    /*** */
                    $pago['movimientos_pagos_texto'] = 'Abono al capital de la deuda';
                } elseif ($pago['movimientos_pagos_id'] == 2) {
                    /*** */
                    $pago['movimientos_pagos_texto'] = 'Abono intereses';
                } elseif ($pago['movimientos_pagos_id'] == 3) {
                    /*** */
                    $pago['movimientos_pagos_texto'] = 'Descuento x pronto pago';
                } elseif ($pago['movimientos_pagos_id'] == 4) {
                    /*** */
                    $pago['movimientos_pagos_texto'] = 'Descuento a capital';
                } elseif ($pago['movimientos_pagos_id'] == 5) {
                    /*** */
                    $pago['movimientos_pagos_texto'] = 'Complemento x cancelacion';
                }

                /**coceptios de subpago */
                foreach ($pago['subpagos'] as $key_subpago => &$subpago) {
                    /**modificando el concepto del movimiento dle pago */
                    if ($subpago['movimientos_pagos_id'] == 1) {
                        /*** */
                        $subpago['movimientos_pagos_texto'] = 'Abono al capital de la deuda';
                    } elseif ($subpago['movimientos_pagos_id'] == 2) {
                        /*** */
                        $subpago['movimientos_pagos_texto'] = 'Abono intereses';
                    } elseif ($subpago['movimientos_pagos_id'] == 3) {
                        /*** */
                        $subpago['movimientos_pagos_texto'] = 'Descuento x pronto pago';
                    } elseif ($subpago['movimientos_pagos_id'] == 4) {
                        /*** */
                        $subpago['movimientos_pagos_texto'] = 'Descuento a capital';
                    } elseif ($subpago['movimientos_pagos_id'] == 5) {
                        /*** */
                        $subpago['movimientos_pagos_texto'] = 'Complemento x cancelacion';
                    }

                    /**buscando segun su tipo de movimiento id, interes o descuento por pronto pago */
                    if ($subpago['movimientos_pagos_id'] == 2) {
                        /**intereses */
                        $pago['intereses_aplicados'] = $subpago['monto_pago'];
                    } else {
                        /**es de tipo descuento pronto pago */
                        $pago['descuento_pronto_pago_aplicado'] = $subpago['monto_pago'];
                    }

                    /**fecha de pago subpago */
                    $subpago['fecha_pago_texto'] = fecha_abr($subpago['fecha_pago']);

                    if ($subpago['status'] == 1) {
                        $subpago['status_texto'] = 'Activo';
                    } else {
                        $subpago['status_texto'] = 'Cancelado';
                    }
                }

                /**actualizando el total dle pago abonado de la deuda */
                if ($pago['movimientos_pagos_id'] == 1) {
                    $pago['deuda_cubierta'] = $pago['monto_pago'] + $pago['descuento_pronto_pago_aplicado'];
                }
                /**fecha del pago */
                $pago['fecha_pago_texto'] = fecha_abr($pago['fecha_pago']);

                /**verificando que tipo de operacion es */
                if ($pago['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 1) {
                    /**es tipo de venta de terreno del cementerio */
                    $pago['tipo_operacion_texto'] = 'VENTA DE TERRENOS';
                } else if ($pago['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 2) {
                    /**es tipo de SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO. */
                        $pago['tipo_operacion_texto'] = strtoupper('CUOTA DE MTTO. EN CEMENTERIO: '.$pago['referencias_cubiertas'][0]['operacion_del_pago']['cuota_cementerio']["descripcion"]);
                } elseif ($pago['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 3) {
                    /**es tipo de  SERVICIOS FUNERARIOS */
                    $pago['tipo_operacion_texto'] = 'SERVICIOS FUNERARIOS';
                } elseif ($pago['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 4) {
                    /**es tipo de VENTA DE PLANES FUNERARIOS A FUTURO */
                    $pago['tipo_operacion_texto'] = 'VENTA DE PLAN FUNERARIO A FUTURO';
                } elseif ($pago['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 5) {
                    /**es tipo de SERVICIOS ESPECIALES CON EXTREMIDADES */
                    $pago['tipo_operacion_texto'] = 'SERVICIOS ESPECIALES CON EXTREMIDADES';
                } elseif ($pago['referencias_cubiertas'][0]['operacion_del_pago']['empresa_operaciones_id'] == 6) {
                    /**es tipo de VENTAS EN GRAL. */
                    $pago['tipo_operacion_texto'] = 'VENTAS EN GRAL.';
                }
            }

            return $resultado_query;
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al cargar los datos solicitados.', 409);
        }
    }

    public function recibo_de_pago(Request $request, $id_pago = '')
    {

        try {

            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email = $request->email_send === 'true' ? true : false;
            $email_to = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_pago = $requestVentasList['id_pago'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */

            /*
            $id_pago = 3353;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //code...
            $datos_pago = $this->get_pagos($request, $id_pago, '', false)[0];
            if (empty($datos_pago)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();

        $FirmasController = new FirmasController();
        $firma_cobrador = $FirmasController->get_firma_documento($datos_pago['cobrador_id'], null, 'por_cobrador');
        $cancelo = $FirmasController->get_firma_documento($datos_pago['cancelo_id'], null, 'por_cobrador');
        $firmas = [
            'cobrador' => $firma_cobrador['firma_path'],
            'cancelo' => $cancelo['firma_path'],
        ];

        //nombre del fallecido
        $fallecido = "N/A";
        $id_servicio_funerario = $datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['servicios_funerarios_id'];
        if ($id_servicio_funerario != null) {
            $res = ServiciosFunerarios::select('nombre_afectado')->where('id', $id_servicio_funerario)->get()->first();
            $fallecido = $res->nombre_afectado;
        }

        $pdf = PDF::loadView('pagos/recibo_pago', ['id_pago' => $id_pago, 'datos' => $datos_pago, 'empresa' => $empresa, 'firmas' => $firmas, 'fallecido' => $fallecido]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "RECIBO DE PAGO " . strtoupper($datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['cliente']['nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('pagos.footer'),
        ]);
        if ($datos_pago['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('pagos.header'),
            ]);
        }

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 13.4);
        $pdf->setOption('margin-right', 13.4);
        $pdf->setOption('margin-top', 13.4);
        $pdf->setOption('margin-bottom', 13.4);
        $pdf->setOption('page-size', 'A4');

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
            $enviar_email = $email_controller->pdf_email(
                $email_to,
                strtoupper($datos_pago['referencias_cubiertas'][0]['operacion_del_pago']['cliente']['nombre']),
                'RECIBO DE PAGO ',
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
