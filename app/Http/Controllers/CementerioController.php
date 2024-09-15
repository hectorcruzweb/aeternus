<?php

namespace App\Http\Controllers;

use App\Ajustes;
use App\AjustesPoliticas;
use App\AntiguedadesVenta;
use App\Clientes;
use App\Cuotas;
use App\Http\Controllers\FirmasController;
use App\Operaciones;
use App\PagosPagosProgramados;
use App\PagosProgramados as AppPagosProgramados;
use App\PreciosPropiedades;
use App\Propiedades;
use App\SatFormasPago;
use App\tipoPropiedades;
use App\User;
use App\VentasTerrenos;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use PDF;

class CementerioController extends ApiController
{
    /**CONTROL DE CUOTAS*/
    public function control_cuotas(Request $request, $tipo_servicio = '')
    {
        if (!(trim($tipo_servicio) == 'agregar' || trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }

        //validaciones directas sin condicionales
        $validaciones = [
            'descripcion' => 'required',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'cuota_total' => 'required|numeric|min:1',
            'tasa_iva' => 'required|numeric|min:0|max:16',
            'id_cuota' => $tipo_servicio == "agregar" ? '' : 'required|integer|min:1',
        ];

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'descripcion.required' => 'Ingrese la descripción de la cuota.',
            'fecha_inicio.required' => 'Ingrese la fecha de inicio.',
            'fecha_fin.required' => 'Ingrese la fecha final.',
            'fecha_fin.after' => 'La fecha final debe ser mayor que la fecha inicial.',
            'tasa_iva.required' => 'Ingrese la tasa de IVA.',
        ];

        request()->validate(
            $validaciones,
            $mensajes
        );

        $id_return = 0;
        try {
            DB::beginTransaction();

            $tasa_iva = $request->tasa_iva;
            $tasa_iva_calculos = ($tasa_iva / 100) + 1;
            $tasa_iva_decimal = ($tasa_iva / 100);
            $total = $request->cuota_total;
            $subtotal = round($total / $tasa_iva_calculos, 2);
            $descuento = 0;
            $impuestos = round(($subtotal - $descuento) * $tasa_iva_decimal, 2);

            /**datos a guardar en la operacion
             * tasa_iva
             * subtotal
             * descuento
             * impuestos
             * total
             * descuento_pronto_pago_b => 0
             * ventas_terrenos_id
             * financiamiento => 1 contado
             * clientes_id
             * fecha_registro
             * fecha_operacion
             * registro_id
             * modifico_id
             * fecha_modificacion
             * status
             * cuotas_cementerio_id
             * aplica_devolucion_b =>0
             */

            /**agrego los datos del request que necesita la funcion de programar pagos */
            $request->request->add(['costo_neto' => $total]);
            $request->request->add(['pago_inicial' => $total]);
            $request->request->add(['descuento' => $descuento]);
            $request->request->add(['fecha_venta' => $request->fecha_inicio]);
            $request->request->add(['tipo_financiamiento' => 1]); //de contado

            if ($tipo_servicio == 'modificar') {
                $id_cuota = $request->id_cuota;
                /**verificando si es tipo modificar para validar que venga el id a modificar */
                /**para poder modificar una cuota de mantenimiento se debe de tener en cuenta lo siguiente
                 * No debe tener pagos ya realizados de ningun cliente porque eso afectaria el total con el cliente
                 * Si se movio la fecha se debe eliminar las operaciones anteriores, asi como sus pagos progrmados y pagos recibidos y se deben volver a registrar las operaciones con los nuevos datos
                 * el registro de cuota se actualiza con la nueva informacion
                 */
                $datos_cuota = $this->get_cuotas($request, $id_cuota, false)[0];
                if (!empty($datos_cuota)) {
                    //validando primero que no tenga pagos activos
                    if ($datos_cuota['pagos_vigentes'] > 0) {
                        return $this->errorResponse('Esta cuota ya tiene pagos vigentes y no se puede modificar. Se debe cancelar esta cuota para anular los pagos pendientes.', 409);
                    }
                } else {
                    return $this->errorResponse('Esta cuota no está registrada.', 409);
                }
                $operaciones = Operaciones::select('id')->where('cuotas_cementerio_id', $id_cuota)->get();
                $pagos_programados = AppPagosProgramados::select('id')->whereIn('operaciones_id', $operaciones)->get();
                $pagos_pagos_programados = PagosPagosProgramados::select('pagos_id')->whereIn('pagos_programados_id', $pagos_programados)->get();

                /**aqui ya hago el barrido de pagos_pagos_programados de pagos_programados y operaciones */
                DB::table('pagos_pagos_programados')->whereIn('pagos_id', $pagos_pagos_programados)->delete();
                DB::table('pagos')->whereIn('id', $pagos_pagos_programados)->delete();
                DB::table('pagos_programados')->whereIn('id', $pagos_programados)->delete();
                DB::table('operaciones')->whereIn('id', $operaciones)->delete();

                DB::table('cuotas_cementerio')->where('id', $id_cuota)->update(
                    [
                        'descripcion' => $request->descripcion,
                        'cuota_total' => $request->cuota_total,
                        'fecha_inicio' => $request->fecha_inicio,
                        'fecha_fin' => $request->fecha_fin,
                        'tasa_iva' => $request->tasa_iva,
                        'modifico_id' => (int) $request->user()->id,
                    ]
                );
                /**se hace el borrado de datos operaciones, pagos_programados, pagos_pagos_programados, pagos para volver a insertar la informacion nueva */
            } else {
                /**es agregar */
                $id_cuota = DB::table('cuotas_cementerio')->insertGetId(
                    [
                        'descripcion' => $request->descripcion,
                        'cuota_total' => $request->cuota_total,
                        'fecha_inicio' => $request->fecha_inicio,
                        'fecha_fin' => $request->fecha_fin,
                        'tasa_iva' => $request->tasa_iva,
                        'registro_id' => (int) $request->user()->id,
                        'modifico_id' => (int) $request->user()->id,
                        'fechahora_registro' => now(),
                    ]
                );
            }

            /**se hace el insert masivo en la base de datos de las operaciones con los clientes que apliquen para la fecha de la cuota segun la fecha compra */
            $select = 'SELECT 2,' . $id_cuota . ',' . $tasa_iva . ',' . $subtotal . ',' . $descuento . ',' . $impuestos . ',' . $total . ',' . $total . ',' . '0,ventas_terrenos_id,1,clientes_id,' . '"' . now() . '"' . ',' . '"' . $request->fecha_inicio . '"' . ',' . (int) $request->user()->id . ',' . (int) $request->user()->id . ',1,0,' . $total . ' from operaciones where empresa_operaciones_id =1 and status <>0 and fecha_operacion <=' . '"' . $request->fecha_inicio . '"';
            /**una vez registrada la cuota debemos asignar las cuotas a pagar a los clientes con propieades en el cementerio
             * Para ello debemos obtener todas las operaciones que son de tipo venta de propiedad 'empresa_operaciones_id  => 1'
             */
            //return DB::select(DB::raw($select));
            DB::table('operaciones')->insertUsing(['empresa_operaciones_id', 'cuotas_cementerio_id', 'tasa_iva', 'subtotal', 'descuento', 'impuestos', 'total', 'saldo', 'descuento_pronto_pago_b', 'ventas_terrenos_id', 'financiamiento', 'clientes_id', 'fecha_registro', 'fecha_operacion', 'registro_id', 'modifico_id', 'status', 'aplica_devolucion_b', 'costo_neto_pronto_pago'], $select);
            $operaciones_a_programar_pagos = Operaciones::select('id', 'ventas_terrenos_id', 'clientes_id', 'cuotas_cementerio_id')->where('empresa_operaciones_id', 2)->where('cuotas_cementerio_id', $id_cuota)->get()->toArray();

            /**se hace la inserccion de pagos programados en la base de datos */
            ini_set('max_execution_time', '300'); //300 seconds = 5 minutes

            foreach ($operaciones_a_programar_pagos as $operacion) {
                /**operacion tipo 2- 002- SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO.
                 * El id venta lo modificamos para que se ajuste a las necesidades de esta operacion
                 * en el caso de pago de cuota de mantenimiento se agregan los digitos de la tabla de ventas_terrenos para mantener la singularidad del registro de pagos en
                 * la BD final asi 00220210814011-140
                 */
                $this->programarPagos($request, $operacion['id'], $id_cuota . '-' . $operacion['ventas_terrenos_id'], '002');
            }

            // return $this->errorResponse('fff', 409);
            $id_return = $id_cuota;
            /**todo salio bien y se debe de guardar */

            DB::commit();
            return $id_return;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function cancelar_cuota(Request $request)
    {

        //validar id de cuota a cencelar
        $validaciones = [
            'id_cuotas' => 'required|integer|min:1',
        ];

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'id_cuotas.required' => 'Ingrese la cuota a cancelar.',
        ];

        request()->validate(
            $validaciones,
            $mensajes
        );

        $id_cuota = $request->id_cuotas;
        $id_return = 0;
        try {
            DB::beginTransaction();
            /**comienzo la cancelacion de la cuota
             * cuando se cancela una cuota se considera lo siguiente
             * 1 - La cuota no puede volver a reactivarse
             * 2 - La cuota al cancelarse se cancelan las operaciones relacionadas a ella
             */

            $datos_cuota = $this->get_cuotas($request, $id_cuota, false)[0];
            if (!empty($datos_cuota)) {
                //validando primero que la cuota este activa
                if ($datos_cuota['status'] != 0) {

                    DB::table('cuotas_cementerio')->where('id', $id_cuota)->update(
                        [
                            'fechahora_cancelacion' => now(),
                            'status' => 0,
                            'cancelo_id' => (int) $request->user()->id,
                        ]
                    );
                    DB::table('operaciones')->where('cuotas_cementerio_id', $id_cuota)->update(
                        [
                            'fecha_cancelacion' => now(),
                            'status' => 0,
                            'cancelo_id' => (int) $request->user()->id,
                        ]
                    );
                }
            } else {
                return $this->errorResponse('Esta cuota no está registrada.', 409);
            }

            $id_return = $id_cuota;
            /**todo salio bien y se cancelo la cuota */

            DB::commit();
            return $id_return;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**get cuotas simple */
    public function get_cuotas_simple()
    {

        return Cuotas::orderBy('id', 'asc')->where('status', '<>', 0)
            ->get();
    }

    /**obtiene todas cuotas de mantenimiento en el cementerio */
    public function get_cuotas(Request $request, $id_cuota = 'all', $paginated = false)
    {

        /**este servicio debe listar las cuotas registradas, el numero de propieades que tienen asignadas a pagar, lo ya cobrado, lo que resta de pagar */
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $numero_control = $request->numero_control;
        $status = $request->status;

        $resultado_query = Cuotas::with('cancelador:id,nombre')
            ->with('registro:id,nombre')
            ->with('modifico:id,nombre')
            ->with('cancelador:id,nombre')
            ->with('propiedades.get_pagos_pagos_programados.pagados')
            ->with('propiedades.venta_terreno:id,ubicacion')
            ->with('propiedades.cliente:id,nombre,celular,telefono,status')
            ->with('propiedades:id,ventas_terrenos_id,cuotas_cementerio_id,clientes_id,subtotal,descuento,impuestos,tasa_iva,total,costo_neto_pronto_pago,financiamiento,fecha_cancelacion as fecha_cancelacion_operacion,status')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS status_texto'
                ),
                DB::raw(
                    '(NULL) AS periodo'
                ),
                DB::raw(
                    '(0) AS num_pagos_programados'
                ),
                DB::raw(
                    '(0) AS num_pagos_programados_vigentes'
                ),
                DB::raw(
                    '(0) AS total_x_cuota'
                ),
                DB::raw(
                    '(0) AS total_cubierto'
                ),
                DB::raw(
                    '(0) AS abonado_capital'
                ),
                DB::raw(
                    '(0) AS abonado_intereses'
                ),
                DB::raw(
                    '(0) AS descontado_capital'
                ),
                DB::raw(
                    '(0) AS complementado_cancelacion'
                ),
                DB::raw(
                    '(0) AS saldo_neto'
                ),
                DB::raw(
                    '(0) AS pagos_vencidos'
                ),
                DB::raw(
                    '(0) AS pagos_programados_cubiertos'
                ),
                DB::raw(
                    '(0) AS pagos_realizados'
                ),
                DB::raw(
                    '(0) AS pagos_vigentes'
                ),
                DB::raw(
                    '(0) AS pagos_cancelados'
                )
            )
            ->where(function ($q) use ($id_cuota) {
                if (trim($id_cuota) == 'all' || $id_cuota > 0) {
                    if (trim($id_cuota) == 'all') {
                        $q->where('cuotas_cementerio.id', '>', $id_cuota);
                    } else if ($id_cuota > 0) {
                        $q->where('cuotas_cementerio.id', '=', $id_cuota);
                    }
                }
            })
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**ajustar en caso de necesitarse */
                        //$q->where('operaciones.numero_solicitud', '=', $numero_control);
                    }
                }
            })
            ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('cuotas_cementerio.status', '=', $status);
                }
            })
            ->orderBy('cuotas_cementerio.id', 'desc')
            ->get();
        /**verificando si el usario necesita el resultado paginado, todo o por id */
        $resultado = array();
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado = &$resultado_query;
        }

        /**datos del cmeenterio para actualizar los valores de la ubicacion */
        $datos_cementerio = $this->get_cementerio();

        foreach ($resultado as $index_cuota => &$cuota) {
            if ($cuota['status'] == 0) {
                $cuota['status_texto'] = "Cancelada";
            } else {
                $cuota['status_texto'] = "Activa";
            }
            /**periodo fechas */
            $cuota['periodo'] = 'Del ' . fecha_abr($cuota['fecha_inicio']) . ' al ' . fecha_abr($cuota['fecha_fin']);

            foreach ($cuota['propiedades'] as $index_propiedad => &$propiedad) {
                /**asignamos la ubicación que tiene la propiedad */
                $propiedad['venta_terreno'] = $this->ubicacion_texto($propiedad['venta_terreno']['ubicacion'], $datos_cementerio);

                /**asignamos los totales recibidos por los pagos programados */

                $propiedad['intereses'] = 0;
                $propiedad['abonado_capital'] = 0;
                $propiedad['abonado_intereses'] = 0;
                $propiedad['descontado_pronto_pago'] = 0;
                $propiedad['descontado_capital'] = 0;
                $propiedad['complementado_cancelacion'] = 0;
                $propiedad['saldo_neto'] = 0;
                /**calculando el total cubierto de la venta, sin intereses pagados, solo lo que ya esta cubierto */
                $propiedad['total_cubierto'] = 0;

                /**calculamos los pagos recibidos por cada operacion */

                $porcentaje_descuento_pronto_pago = 0;
                if ($propiedad['total'] > 0) {
                    $porcentaje_descuento_pronto_pago = ($propiedad['costo_neto_pronto_pago'] * 100) / ($propiedad['total']);
                }

                $propiedad['num_pagos_programados'] = count($propiedad['get_pagos_pagos_programados']);
                $num_pagos_programados_vigentes = 0;

                if ($propiedad['num_pagos_programados'] > 0) {
                    /**si tiene pagos programados, eso quiere decir que la venta no tuvo 100 de descuento */
                    /**recorriendo arreglo de pagos programados */
                    $vencidos = 0;
                    $pagos_programados_cubiertos = 0;
                    $dias_vencido_primer_pago_vencido = '';
                    $pagos_vigentes = 0;
                    $pagos_cancelados = 0;
                    $pagos_realizados = 0;

                    $arreglo_de_pagos_realizados = [];
                    /**guardo los dias que lleva vencido el pago vencido mas antiguo */
                    foreach ($propiedad['get_pagos_pagos_programados'] as $index_programado => &$programado) {
                        /**actualizando el concepto del pago */
                        if ($programado['conceptos_pagos_id'] == 1) {
                            $programado['concepto_texto'] = 'Enganche';
                        } elseif ($programado['conceptos_pagos_id'] == 2) {
                            $programado['concepto_texto'] = 'Abono';
                        } else {
                            $programado['concepto_texto'] = 'Pago Único';
                        }

                        /**actualizando fecha de pago abre con helper de fechas */
                        $programado['fecha_programada_abr'] = fecha_abr($programado['fecha_programada']);

                        //if ($programado['status'] == 1) {
                        if ($programado['status'] == 1) {
                            $num_pagos_programados_vigentes++;
                        }
                        /**aumento el pago programado vigente */
                        /**haciendo sumatoria de los montos que se han destinado a un pago programado segun el tipo de movimiento */
                        /**montos segun su tipo de movimiento */
                        $abonado_intereses = 0;
                        $abonado_capital = 0;
                        $descontado_pronto_pago = 0;
                        $descontado_capital = 0;
                        $complemento_cancelacion = 0;
                        /**aqui voy */
                        $total_cubierto = 0;
                        $fecha_ultimo_pago = '';

                        foreach ($programado['pagados'] as $index_pagados => &$pagado) {
                            /**haciendo el arreglo de pagos realizados limpio(no repetidos) */
                            array_push(
                                $arreglo_de_pagos_realizados,
                                $pagado
                            );

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
                                    // $pago_total += $pagado['monto'];
                                    $abonado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 4) {
                                    /**fue descuento al capital */
                                    $descontado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 5) {
                                    /**fue complemento por cancelacion */
                                    $complemento_cancelacion += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 2) {
                                    /**es tipo interes */
                                    if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                        /**es abono de intereses */
                                        $abonado_intereses += $pagado['pagos_cubiertos']['monto'];
                                        //$pago_total += $pagado['monto'];
                                    }
                                } else if ($pagado['movimientos_pagos_id'] == 3) {
                                    if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                        /**es descuento por pronto pago */
                                        $descontado_pronto_pago += $pagado['pagos_cubiertos']['monto'];
                                        //$pago_total += $pagado['monto'];
                                    }
                                }

                                /**fecha en que se realizo el ultimo pago */
                                $fecha_ultimo_pago = $pagado['fecha_pago'];
                                // }
                                $pagos_vigentes++;
                            } //fin if pago status=1
                            else {
                                if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                    $pagos_cancelados++;
                                }
                            }
                            if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                $pagos_realizados++;
                            }
                        } //fin foreach pagado

                        /** al final del ciclo se actualizan los valores en el pago programado*/
                        $programado['abonado_capital'] = round($abonado_capital, 2, PHP_ROUND_HALF_UP);
                        $programado['abonado_intereses'] = $abonado_intereses;
                        $programado['descontado_pronto_pago'] = $descontado_pronto_pago;
                        $programado['descontado_capital'] = $descontado_capital;
                        $programado['complementado_cancelacion'] = round($complemento_cancelacion, 2, PHP_ROUND_HALF_UP);

                        $saldo_pago_programado = $programado['monto_programado'] - $abonado_capital - $descontado_pronto_pago - $descontado_capital - $complemento_cancelacion;

                        /**sumo al total x cuota */
                        $cuota['total_x_cuota'] += $programado['monto_programado'];

                        $programado['saldo_neto'] = round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP);
                        /**asignando la fecha del pago que liquidado el pago programado */
                        if ($programado['saldo_neto'] <= 0) {
                            $programado['fecha_ultimo_pago'] = $fecha_ultimo_pago;
                            $programado['fecha_ultimo_pago_abr'] = fecha_abr($fecha_ultimo_pago);
                        }
                        /**verificando el estado del pago programado*/
                        /**verificando si la fecha sigue vigente o esta vencida */
                        /**variables para controlar el incremento por intereses */
                        $dias_retrasados_del_pago = 0;
                        $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);

                        /**aqui verifico que si la operacion esta activa genere los intereses acorde al dia de hoy, si esta cancelada que tomen intereses a partir de la fecha de cancelacion */
                        $fecha_para_intereses = date('Y-m-d');
                        if ($propiedad['status'] != 0) {
                            if (trim($propiedad['fecha_cancelacion_operacion']) != '') {
                                $date = date_create($propiedad['fecha_cancelacion_operacion']);
                                $fecha_para_intereses = date_format($date, 'Y-m-d');
                            }
                        }

                        $fecha_hoy = Carbon::createFromFormat('Y-m-d', $fecha_para_intereses);
                        $interes_generado = 0;
                        $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_programada']);
                        /**fin varables por intereses */
                        /**verificando que el pago programado tiene un saldo de capital que cobrar para saber si aplica o no intereses */
                        if (round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP) > 0) {
                            /**tiene todavia saldo que pagar, se debe verificar si el pago esta vencido para generarle los intereses correspondientes */
                            if (date('Y-m-d', strtotime($programado['fecha_programada'])) < date('Y-m-d')) {
                                /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                                $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                                if ($dias_vencido_primer_pago_vencido == '') {
                                    $dias_vencido_primer_pago_vencido = $dias_retrasados_del_pago;
                                }
                                $programado['fecha_a_pagar'] = date('Y-m-d');
                                $programado['fecha_a_pagar_abr'] = fecha_abr(date('Y-m-d'));
                                /**aqui actualizamos el saldo neto del pago con todo e intereses, quitando los intereses que ya se han pagado previamente */
                                $programado['saldo_neto'] = round($saldo_pago_programado + $interes_generado, 2, PHP_ROUND_HALF_UP);
                                /**la fecha qui es mayor que la fecha programada del pago */
                                $programado['status_pago'] = 0;
                                $programado['status_pago_texto'] = 'Vencido';
                                $vencidos++;
                                $programado['dias_vencido'] = $dias_retrasados_del_pago;
                                $programado['intereses'] = $interes_generado;
                            } else {
                                /**la fecha aun no vence */
                                $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                                $programado['status_pago'] = 1;
                                $programado['status_pago_texto'] = 'Pendiente';
                            }
                        } else {
                            $pagos_programados_cubiertos++;
                            $programado['fecha_a_pagar'] = $pagado['fecha_pago'];
                            /**el pago programado ya fue cubierto */
                            $programado['status_pago'] = 2;
                            $programado['status_pago_texto'] = 'Pagado';
                        }

                        /**monto con pronto pago de cada abono */
                        $programado['monto_pronto_pago'] = round(($porcentaje_descuento_pronto_pago * $programado['monto_programado']) / 100, 0, PHP_ROUND_HALF_UP);
                        $programado['total_cubierto'] = $abonado_capital + $descontado_pronto_pago + $descontado_capital + $complemento_cancelacion;

                        /**actualizando los totales de montos en la operacion de cuota */
                        $propiedad['intereses'] += $interes_generado;
                        $propiedad['abonado_capital'] += $abonado_capital;
                        $propiedad['abonado_intereses'] += $abonado_intereses;
                        $propiedad['descontado_pronto_pago'] += $descontado_pronto_pago;
                        $propiedad['descontado_capital'] += $descontado_capital;
                        $propiedad['complementado_cancelacion'] += $complemento_cancelacion;
                        $propiedad['saldo_neto'] += $saldo_pago_programado + $interes_generado;
                        /**calculando el total cubierto de la venta, sin intereses pagados, solo lo que ya esta cubierto */
                        $propiedad['total_cubierto'] += $programado['total_cubierto'];
                    } //fin foreach programados

                    /**actualizando los datos en general por cuota */
                    $propiedad['pagos_realizados'] = $pagos_realizados;
                    $propiedad['pagos_vigentes'] = $pagos_vigentes;

                    $propiedad['pagos_cancelados'] = $pagos_cancelados;
                    $propiedad['pagos_programados_cubiertos'] = $pagos_programados_cubiertos;
                    $propiedad['pagos_vencidos'] = $vencidos;
                    $propiedad['dias_vencidos'] = $dias_vencido_primer_pago_vencido;

                    /**actualizando los datos en general por cuota */

                    $cuota['num_pagos_programados'] += 1;
                    $cuota['num_pagos_programados_vigentes'] += $num_pagos_programados_vigentes;
                    $cuota['total_cubierto'] += $propiedad['total_cubierto'];
                    $cuota['abonado_capital'] += $propiedad['abonado_capital'];
                    $cuota['abonado_intereses'] += $propiedad['abonado_intereses'];
                    $cuota['descontado_capital'] += $propiedad['descontado_capital'];
                    $cuota['complementado_cancelacion'] += $propiedad['complementado_cancelacion'];
                    $cuota['saldo_neto'] += $propiedad['saldo_neto'];
                    $cuota['pagos_vencidos'] += $vencidos;
                    $cuota['pagos_programados_cubiertos'] += $pagos_programados_cubiertos;
                    $cuota['pagos_realizados'] += $pagos_realizados;
                    $cuota['pagos_vigentes'] += $pagos_vigentes;
                    $cuota['pagos_cancelados'] += $pagos_cancelados;
                } else {
                    /**la venta no tiene pagos programados debido a que fue 100% "GRATIS" */
                }
            }

            //$cuota['propiedades']['venta_terreno']['ubicacion_texto'] = $this->ubicacion_texto($cuota['propiedades']['venta_terreno']['ubicacion']['ubicacion_texto'], $datos_cementerio);
        } //fin foreach cuotas

        return $resultado_query;
        /**aqui se puede hacer todo los calculos para llenar la informacion calculada del servicio get_cuotas */
    }

    /**CANCELAR LA VENTA */
    public function cancelar_venta(Request $request)
    {
        //return $request->minima_cuota_inicial;
        //validaciones directas sin condicionales
        try {
            //code...
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar este servicio.', 409);
        }
        $datos_venta = $this->get_ventas($request, $request->venta_id, '')[0];

        /**unicamente puede regresarse lo que  se ha cubierto de capital */
        $validaciones = [
            'venta_id' => 'required',
            'motivo.value' => 'required',
            'cantidad' => 'numeric|min:0|' . 'max:' . $datos_venta['abonado_capital'],
        ];

        $mensajes = [
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'max' => 'La cantidad a devolver no debe superar a la cantidad abonada hasta la fecha: $ ' . number_format($datos_venta['abonado_capital'], 2),
            'min' => 'La cantidad a devolver debe ser mínimo: $ 00.00 Pesos MXN',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        /**validar si la propiedad tiene gente sepultada */
        /**pendiente
         * pendiente
         * pendiente
         * pendiente
         */

        /**validar si la propiedad no fue dada de baja ya */

        if ($datos_venta['operacion_status'] == 0) {
            return $this->errorResponse('Esta venta ya habia sido dada de baja.', 409);
        }

        try {
            DB::beginTransaction();
            DB::table('operaciones')->where('ventas_terrenos_id', $request->venta_id)->update(
                [
                    'motivos_cancelacion_id' => $request['motivo.value'],
                    'fecha_cancelacion' => now(),
                    'cantidad_a_regresar_cancelacion' => (float) $request->cantidad,
                    'cancelo_id' => (int) $request->user()->id,
                    'nota_cancelacion' => $request->comentario,
                    'status' => 0,
                ]
            );

            /**se cancelan las operaciones relacionadas a las cuotas de esta venta */
            DB::table('operaciones')->where('ventas_terrenos_id', $request->venta_id)->where('empresa_operaciones_id', 2)->update(
                [
                    'motivos_cancelacion_id' => $request['motivo.value'],
                    'fecha_cancelacion' => now(),
                    'cantidad_a_regresar_cancelacion' => 0,
                    'cancelo_id' => (int) $request->user()->id,
                    'nota_cancelacion' => $request->comentario,
                    'status' => 0,
                ]
            );
            /**cnacelando los pagos programados de dicha cuota */
            $operaciones_cuotas = Operaciones::select('id')->where('empresa_operaciones_id', 2)->where('ventas_terrenos_id', $request->venta_id)->get()->toArray();
            DB::table('pagos_programados')->whereIn('operaciones_id', $operaciones_cuotas)->update(
                [
                    'status' => 0,
                ]
            );

            DB::commit();
            return $request->venta_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function actualizar_status_convenio(Request $request)
    {
        /**unicamente puede regresarse lo que  se ha cubierto de capital */
        $validaciones = [
            'id' => 'required',
            'tipo' => 'required',
            'action' => 'required',
        ];

        $mensajes = [
            'required' => 'Ingrese este dato',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        $id = $request->id;
        $nota = $request->nota;
        //marca como entregado
        $fecha = now();
        $usuario = (int) $request->user()->id;

        //verificamos que tipo es
        if ($request->tipo == 'terreno') {
            $solicitud = DB::table('ventas_terrenos')->where('id', $id)->first();
            if ($solicitud->status_convenio == 1) {
                if ($request->action == 0) {
                    $fecha = null;
                    $usuario = null;
                } else {
                    $fecha = $solicitud->fecha_registro_convenio;
                    $usuario = $solicitud->registro_id_convenio;
                }
            }
            //checo si ya fue actualizado
            DB::table('ventas_terrenos')->where('id', $id)->update(
                [
                    'status_convenio' => $request->action,
                    'nota_convenio' => $nota,
                    'fecha_registro_convenio' => $fecha,
                    'registro_id_convenio' => $usuario,
                ]
            );
        } else {
            $solicitud = DB::table('ventas_planes')->where('id', $id)->first();
            if ($solicitud->status_convenio == 1) {
                if ($request->action == 0) {
                    $fecha = null;
                    $usuario = null;
                } else {
                    $fecha = $solicitud->fecha_registro_convenio;
                    $usuario = $solicitud->registro_id_convenio;
                }
            }
            //checo si ya fue actualizado
            DB::table('ventas_planes')->where('id', $id)->update(
                [
                    'status_convenio' => $request->action,
                    'nota_convenio' => $nota,
                    'fecha_registro_convenio' => $fecha,
                    'registro_id_convenio' => $usuario,
                ]
            );
        }
        return $this->successResponse(1, 200);
    }

    public function get_cuota_pdf($idioma = 'es', Request $request)
    {

        if (!($idioma == 'en' || $idioma == 'es')) {
            $idioma = 'es';
        }
        App::setLocale($idioma);

        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        if ($email == true) {
            if (!$request->email_addres || !$request->destinatario) {
                $this->errorResponse('Es necesario un correo y un destinatario', 409);
            }
        }
        $email_to = $request->email_address;
        $datos_request = json_decode($request->request_parent[0], true);

        $id_cuota = $datos_request['reporte']['value'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*  $id_cuota = 1;
        $email = false;
        $email_to = 'hector@gmail.com';
         */
        $cuota = $this->get_cuotas($request, $id_cuota, false);
        if (count($cuota) > 0) {
            $cuota = $cuota[0];
        }

        //obtengo la informacion de esa cuota

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('cementerios/cuotas/cuota', ['empresa' => $empresa, 'cuota' => $cuota, 'idioma' => $idioma]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = __('cementerio/cuotas.cuota') . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.cuotas.footer', ['empresa' => $empresa]),
        ]);

        $pdf->setOptions([
            'header-html' => view('cementerios.cuotas.header'),
        ]);

        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 12.4);
        $pdf->setOption('margin-bottom', 12.4);
        $pdf->setOption('page-size', 'letter');

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
                $request->destinatario,
                __('cementerio/cuotas.cuota'),
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    public function get_abonos_vencidos_propiedades($idioma = 'es', Request $request)
    {
        if (!($idioma == 'en' || $idioma == 'es')) {
            $idioma = 'es';
        }
        App::setLocale($idioma);

        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        if ($email == true) {
            if (!$request->email_addres || !$request->destinatario) {
                $this->errorResponse('Es necesario un correo y un destinatario', 409);
            }
        }
        /*
        $email_to        = $request->email_address;
        $datos_request   = json_decode($request->request_parent[0], true);
         */
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */

        $email = false;
        $email_to = 'hector@gmail.com';

        $ventas = $this->get_ventas($request, 'all', false);

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('cementerios/abonos_vencidos/reporte', ['empresa' => $empresa, 'ventas' => $ventas, 'idioma' => $idioma]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = 'Pagos vencidos en propiedades.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.abonos_vencidos.footer', ['empresa' => $empresa]),
        ]);

        $pdf->setOptions([
            'header-html' => view('cementerios.abonos_vencidos.header'),
        ]);

        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 12.4);
        $pdf->setOption('margin-bottom', 12.4);
        $pdf->setOption('page-size', 'letter');

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
                $request->destinatario,
                'Abonos vencidos de propiedades',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    public function get_mapeado($idioma = 'es', Request $request)
    {
        if (!($idioma == 'en' || $idioma == 'es')) {
            $idioma = 'es';
        }
        App::setLocale($idioma);
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        if ($email == true) {
            if (!$request->email_addres || !$request->destinatario) {
                $this->errorResponse('Es necesario un correo y un destinatario', 409);
            }
        }

        //$email_to        = $request->email_address;
        //$datos_request   = json_decode($request->request_parent[0], true);

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        $tipo_propiedad_id = '';
        $id_area = '';
        $filtro_seleccion = '';
        $fecha_inicio = '';
        $fecha_fin = '';
        $datosRequest = null;
        if (isset($request->request_parent[0])) {
            $datosRequest = json_decode($request->request_parent[0], true);
        }
        if (isset($datosRequest['modulo']['value'])) {
            $tipo_propiedad_id = $datosRequest['tipo_propiedad']['value'];
            $id_area = $datosRequest['area_propiedades']['value'];
            $filtro_seleccion = $datosRequest['filtro_seleccion']['value'];
            $fecha_inicio = $datosRequest['fecha_inicio'];
            $fecha_fin = $datosRequest['fecha_fin'];
            $email_to = $request->email_address;
        } else {
            //return $this->errorResponse('Error al generar el reporte', 409);
            $tipo_propiedad_id = '4';
            $id_area = '';
            $filtro_seleccion = '3';
            $fecha_inicio = '2020-01-01';
            $fecha_fin = now();
            $email = false;
            $email_to = 'hector@gmail.com';
        }

        $cementerio = $this->get_cementerio();
        //$ventas = $this->get_ventas($request, 'all', false);
        //$funeraria = new FunerariaController();
        //$servicios = $funeraria->get_solicitudes_servicios($request, 'all', false);
        $query = Operaciones::select(
            'id',
            'clientes_id',
            'fecha_operacion',
            'ventas_terrenos_id',
            DB::raw(
                '(NULL) AS fecha_venta_texto'
            )
        )
            ->with('cliente:id,nombre')
            ->with('venta_terreno:id,ubicacion,tipo_propiedades_id,propiedades_id')
            ->with([
                'venta_terreno.servicios_por_terreno' => function ($q) use ($filtro_seleccion, $fecha_fin, $fecha_inicio) {
                    if ($filtro_seleccion == 3) {
                        /**solo las ventas que tienen servicios funerarios dentro de esas fechas */
                        $q->WhereBetween('fechahora_inhumacion', [$fecha_inicio, $fecha_fin]);
                    }
                }
            ])
            /*
            ->WhereHas(isset($request->filtrar_solo_adeudos) ? 'operacion' : 'registro', function ($q) use ($request) {
            if (isset($request->filtrar_solo_adeudos)) {
            $q->where('status', 1)->where('total', '>', 0);
            }
            })*/
            ->where('empresa_operaciones_id', 1)->where('status', '<>', 0);

        //aplico los filtros a las ventas que sean en la fecha señalada si aplican
        if ($filtro_seleccion == 2) {
            /**solo vnentas entre las fechas señaladas */
            $query->WhereBetween('fecha_operacion', [$fecha_inicio, $fecha_fin]);
        }

        $ventas_cementerio = $query->get();

        /**agrego la fila y el lote desde la ubicacion completa */

        foreach ($ventas_cementerio as $key => $venta) {
            //obtengo los datos para las propieades vendidad por tipo de area , id_area, fila y lote
            $venta['fila_raw'] = (intval(explode("-", $venta['venta_terreno']['ubicacion'])[2]));
            $venta['lote_raw'] = (intval(explode("-", $venta['venta_terreno']['ubicacion'])[3]));
        }

        //aqui ando

        /**limpiando array areas seleccionadas del cementerio */
        foreach ($cementerio as $key => &$area) {
            unset($area['tipo_propiedad']);
            unset($area['frente']);
            if (($tipo_propiedad_id != $area['tipo_propiedades_id']) && $tipo_propiedad_id != '') {
                unset($cementerio[$key]);
                continue;
            } else if (($id_area != $area['id']) && $id_area != '') {
                unset($cementerio[$key]);
                continue;
            }

            /**obtengo las propieades vendidas de dicha area del cementerio */
            $propiedades = array();
            foreach ($ventas_cementerio as $key => &$venta) {
                if ($venta['venta_terreno']['propiedades_id'] == $area['id']) {
                    /**al pertenecer a dicha area esta venta se agrega los datos del mapeado */
                    /**actualizo la feca del servicio con texto */
                    foreach ($venta['venta_terreno']['servicios_por_terreno'] as &$servicio) {
                        $servicio['fecha_inhumacion_texto'] = fecha_abr($servicio['fechahora_inhumacion']);
                    }
                    array_push(
                        $propiedades,
                        [
                            'operacion_id' => $venta['id'],
                            'clientes_id' => $venta['clientes_id'],
                            'fecha_operacion' => $venta['fecha_operacion'],
                            'ventas_terrenos_id' => $venta['ventas_terrenos_id'],
                            'ubicacion' => $venta['venta_terreno']['ubicacion'],
                            'ubicacón_texto' => $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $cementerio)['ubicacion_texto'],
                            'fila_raw' => $venta['fila_raw'],
                            'lote_raw' => $venta['lote_raw'],
                            'cliente' => $venta['cliente']['nombre'],
                            'fecha_venta_texto' => fecha_abr($venta['fecha_operacion']),
                            'num_servicios' => count($venta['venta_terreno']['servicios_por_terreno']),
                            'servicios_funerarios' => $venta['venta_terreno']['servicios_por_terreno'],
                        ]
                    );
                }
            }
            $area['propiedades'] = $propiedades;
        }

        /**agregando al array los parametros de filtracion */
        $nombre_reporte = '';
        switch ($filtro_seleccion) {
            case '1':
                $nombre_reporte = 'Disponibilidad de propiedades';
                break;
            case '2':
                $nombre_reporte = 'Propiedades por fecha de venta';
                break;
            case '1':
                $nombre_reporte = 'Propiedades por fecha de servicio';
                break;
            default:
                $nombre_reporte = 'Estatus general del cementerio';
                break;
        }

        /**anexo la filtracion del reporte */

        $cementerio = [
            'cementerio' => $cementerio,
            'filtracion' => [
                'tipo_propiedad_id' => $tipo_propiedad_id,
                'id_area' => $id_area,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'filtro_seleccion' => $filtro_seleccion,
                'nombre_reporte' => $nombre_reporte,
            ],
        ];

        //return $cementerio;

        //aqui estoy trabajando
        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('cementerios/cementerio_mapa/reporte', ['empresa' => $empresa, 'cementerio' => $cementerio, 'idioma' => $idioma]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = 'Pagos vencidos en propiedades.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.cementerio_mapa.footer', ['empresa' => $empresa]),
        ]);

        $pdf->setOptions([
            'header-html' => view('cementerios.cementerio_mapa.header'),
        ]);

        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 12.4);
        $pdf->setOption('margin-bottom', 12.4);
        $pdf->setOption('page-size', 'letter');

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
                $request->destinatario,
                'Abonos vencidos de propiedades',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    public function get_cuota_pdf_todas($idioma = 'es', Request $request)
    {

        if (!($idioma == 'en' || $idioma == 'es')) {
            $idioma = 'es';
        }
        App::setLocale($idioma);

        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        if ($email == true) {
            if (!$request->email_addres || !$request->destinatario) {
                $this->errorResponse('Es necesario un correo y un destinatario', 409);
            }
        }
        $email_to = $request->email_address;
        // $datos_request   = json_decode($request->request_parent[0], true);
        //$id_cuota = $datos_request['id_cuota'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*  $id_cuota = 1;
        $email = false;
        $email_to = 'hector@gmail.com';
         */
        $cuotas = $this->get_cuotas($request, 'all', false);

        //obtengo la informacion de esa cuota

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();

        $pdf = PDF::loadView('cementerios/cuotas_todas/cuota', ['empresa' => $empresa, 'cuotas' => $cuotas, 'idioma' => $idioma]);

        $name_pdf = 'Cuotas de cementerio.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.cuotas_todas.footer', ['empresa' => $empresa]),
        ]);

        $pdf->setOptions([
            'header-html' => view('cementerios.cuotas_todas.header'),
        ]);

        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 12.4);
        $pdf->setOption('margin-bottom', 12.4);
        $pdf->setOption('page-size', 'letter');

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
                $request->destinatario,
                __('cementerio/cuotas_todas.cuota'),
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    public function get_cementerio()
    {

        $datos = Propiedades::select(
            '*',
            DB::raw(
                '(NULL) AS nombre_area'
            ),
            DB::raw(
                '(NULL) AS mapa'
            )
        )
            ->with('filas_columnas')->with('tipoPropiedad')->with('tipoPropiedad.precios')->with('filas_columnas')->orderBy('id', 'asc')->get()->toArray();
        foreach ($datos as $key => &$dato) {
            if ($dato['tipo_propiedades_id'] == 1) {
                /**uniplex */
                $dato['nombre_area'] = 'Sección uniplex ' . $dato['propiedad_indicador'];
            } else if ($dato['tipo_propiedades_id'] == 2) {
                /**duplex */
                $dato['nombre_area'] = 'Sección duplex ' . $dato['propiedad_indicador'];
            } else if ($dato['tipo_propiedades_id'] == 3) {
                /**nichos */
                $dato['nombre_area'] = 'nichos columna ' . $dato['propiedad_indicador'];
            } else if ($dato['tipo_propiedades_id'] == 4) {
                /**terrazas */
                $dato['nombre_area'] = 'Terraza ' . $dato['propiedad_indicador'];
            } else if ($dato['tipo_propiedades_id'] == 5) {
                /**triplex */
                $dato['nombre_area'] = 'Sección Triplex ' . $dato['propiedad_indicador'];
            } else if ($dato['tipo_propiedades_id'] == 6) {
                /**cuadriplez dsin terraza */
                $dato['nombre_area'] = 'cuadriplex ' . $dato['propiedad_indicador'];
            } else {
                /**cuadriplez dsin terraza */
                $dato['nombre_area'] = 'mausoleo ' . $dato['propiedad_indicador'];
            }

            foreach ($dato['tipo_propiedad']['precios'] as $precio_key => &$precio) {

                if ($precio['financiamiento'] == 1) {
                    $precio['tipo_financiamiento'] = "Pago Único/Uso Inmediato";
                    $precio['tipo_financiamiento_ingles'] = "Spot Price";
                    $precio['pago_mensual']
                        = 0;
                } else {
                    $precio['tipo_financiamiento'] = "Pago a " . $precio['financiamiento'] . " Meses/A Futuro";
                    $precio['tipo_financiamiento_ingles'] = $precio['financiamiento'] . "-Month Payment";
                    $precio['pago_mensual']
                        = ($precio['costo_neto'] - $precio['pago_inicial']) / $precio['financiamiento'];
                }
                /**sacando los descuentos en caso de que tenga pronto pago */
                if ($precio['descuento_pronto_pago_b'] == 1) {

                    $precio['descuento_x_pago'] = ($precio['costo_neto'] - $precio['costo_neto_pronto_pago']) / $precio['financiamiento'];
                    $precio['porcentaje_pronto_pago'] = 100 - (($precio['costo_neto_financiamiento_normal'] * 100) / $precio['costo_neto']);
                } else {
                    $precio['descuento_x_pago'] = ' 0';
                    $precio['porcentaje_pronto_pago'] = ' 0';
                }
            }

            /**agregando fila, lote, y tipo, por separado en valor numrico */

            /*foreach ($dato['ventas'] as $key_venta => &$venta) {
        $venta['fila_raw'] = (intval(explode("-", $venta['ubicacion'])[2]));
        $venta['lote_raw'] = (intval(explode("-", $venta['ubicacion'])[3]));
        }*/
        }

        return $datos;
    }

    //obtiene los usuarios para vendedores
    public function get_vendedores()
    {
        //no super usuarios
        /**puesto de venderor id 2 */
        /**obtiene los usuarios con puesto de vendedor */
        return User::select('id', 'nombre')
            ->join('usuarios_puestos', 'usuarios_puestos.usuarios_id', '=', 'usuarios.id')
            ->where('roles_id', '>', 1)
            ->where('puestos_id', '=', 2)
            ->where('usuarios.status', '>', 0)
            ->get();
    }

    public function get_sat_formas_pago()
    {
        //id del conjunto de propieades

        return
            SatFormasPago::where('clave', '<>', '99')->where('clave', '<>', '25')->get();
    }

    /**GUARDAR LA VENTA */
    public function control_ventas(Request $request, $tipo_servicio = '')
    {

        if (!(trim($tipo_servicio) == 'agregar' || trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }
        /**procede la peticion */

        /**valdiando que cuadren las cantidades de la venta */
        //validaciones directas sin condicionales
        $validaciones = [
            'salarios_minimos' => 'integer|required|min:1|max:150',
            //datos de la propiedad
            'id_venta' => '',
            /**solo para modificaciones */
            'tipo_propiedades_id' => 'required|min:1',
            'propiedades_id' => 'required|min:1',
            'ubicacion' => 'required',
            //fin de datos de la propiedad
            //datos de la venta
            'fecha_venta' => 'required|date',
            'ventaAntiguedad.value' => 'required',
            'tipo_financiamiento' => 'required',
            'filas.value' => 'required',
            'lotes.value' => '', //modificada segun condiciones
            'vendedor.value' => 'required',
            'solicitud' => '',
            'convenio' => '',
            'titulo' => '',
            /**id del cliente */
            'id_cliente' => 'required',
            //info del plan de venta y pagos
            //'planVenta.value' => 'numeric|required',
            /**nuevos datos a requerir */
            'financiamiento' => '',
            'tasa_iva' => 'numeric|required|min:1|max:25',
            'descuento' => '',
            'costo_neto' => 'numeric|required|min:1',
            'pago_inicial' => '',
            /**titular_sustituto */
            'titular_sustituto' => 'required',
            'parentesco_titular_sustituto' => 'required',
            'telefono_titular_sustituto' => 'required',
            /**beneficiarios */
            'beneficiarios.*.nombre' => [
                'required',
            ],
            'beneficiarios.*.parentesco' => [
                'required',
            ],
        ];

        /**verificando si es tipo modificar para validar que venga el id a modificar */
        $datos_venta = array();
        if ($tipo_servicio == 'modificar') {
            $datos_venta = $this->get_ventas($request, $request->id_venta, '')[0];
            if (empty($datos_venta)) {
                /**no se encontro los datos */
                return $this->errorResponse('No se encontró la información de la venta solicitada', 409);
            } else if ($datos_venta['operacion_status'] == 0) {
                return $this->errorResponse('Esta venta ya fue cancelada, no puede modificarse', 409);
            } else {
                /**puede proceder con las valiaciones necesarias para */
                if ($request->fecha_venta != $datos_venta['fecha_operacion']) {
                    return $this->errorResponse('No se puede modificar la fecha de la venta por que tiene pagos programados. Puede cancelar la venta e ingresar nuevos datos.', 409);
                }
            }
        }

        /**creamos los calculos del total a pagar para desglosar impuestos y pago inicial necesario segun el financiamiento */
        /**aqui comienzan a gurdar los datos */
        $tasa_iva_calculos = 0;
        $tasa_iva_decimal = 0;
        if (isset($request->tasa_iva) && isset($request->costo_neto) && isset($request->descuento)) {
            if ($request->tasa_iva > 0) {
                $tasa_iva_calculos = ($request->tasa_iva / 100) + 1;
                $tasa_iva_decimal = $request->tasa_iva / 100;
            }
        } else {
            return $this->errorResponse('Ingrese IVA, costo neto y descuento.', 409);
        }

        $tasa_iva = $request->tasa_iva;
        $costo_neto = $request->costo_neto;
        $descuento = $request->descuento;

        /**total neto a pagar */
        $total_pagar = $costo_neto - $descuento;

        /**calculando los descuentos para calcular los impuestos por IVA */
        $subtotal = $costo_neto / $tasa_iva_calculos;
        $subtotal_con_descuento = $total_pagar / $tasa_iva_calculos;
        /**obtengo la cantidad que se le aplica al subototal como descuento para registrar impuestos */
        $descuento_real_para_impuestos = $subtotal - $subtotal_con_descuento;

        /**calulando los impuestos */
        $iva = ($subtotal - $descuento_real_para_impuestos) * $tasa_iva_decimal;

        /**si pasan estas condicones podemos continuar */
        /**solo en caso de modificaciones */
        /**validando el pago inicial */
        if ($request->financiamiento == 1) {
            /**cuando es a contado */
            /**es un solo pago de inicio */
            $validaciones['pago_inicial'] = 'numeric|required|min:' . $total_pagar . '|max:' . $total_pagar;
        } else {
            //cuando es a credito
            $validaciones['pago_inicial'] = 'numeric|required|min:' . ($total_pagar * .1) . '|max:' . ($total_pagar * .7);
        }

        /**validando que el descuento no sobrepase el costo neto de la venta */
        $validaciones['descuento'] = 'numeric|required|min:0|max:' . $costo_neto;

        /**validando de manera manual si la ubicacion enviada ya esta registrada y esta activa */
        $ubicacion_enviada = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
            ->where('ubicacion', '=', $request->ubicacion)->where('operaciones.status', '<>', 0)->first();
        if (!empty($ubicacion_enviada)) {
            if ($tipo_servicio == 'modificar') {
                if ($ubicacion_enviada->id != $request->id_venta) {
                    return $this->errorResponse('La ubicación seleccionada ya ha sido vendida.', 409);
                }
            } else {
                return $this->errorResponse('La ubicación seleccionada ya ha sido vendida.', 409);
            }
        }

        /**VALIDACIONES CONDICIONADAS*/
        //validando que mande el user el lote en caso de ser terraza
        if ($request->tipo_propiedades_id == 4) {
            //checando que tipo de propiedad es, si es terraza
            $validaciones['lotes.value'] = "required";
        }

        //validnado en caso de que sea de uso inmediato y de venta antes del sistema.
        if ($request->ventaAntiguedad['value'] == 3) {
            //venta de uso inmediato
            $validaciones['titulo'] = 'required';
            /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
            $titulo = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
                ->where('numero_titulo', $request->titulo)->where('operaciones.status', '<>', 0)->first();
            if (!empty($titulo)) {
                if ($tipo_servicio == 'modificar') {
                    if ($titulo->id != $request->id_venta) {
                        return $this->errorResponse('El número de título seleccionado ya ha sido registrado.', 409);
                    }
                } else {

                    return $this->errorResponse('El número de título seleccionado ya ha sido registrado.', 409);
                }
            }
        }

        if ($request->tipo_financiamiento == 1) {
            /**cuando es a contado */
            /**es un solo pago de inicio */
            $validaciones['financiamiento'] = 'numeric|required|min:' . 1 . '|max:' . 1;
        } else {
            //cuando es a credito
            $validaciones['financiamiento'] = 'numeric|required|min:' . 1 . '|max:' . 120;
        }

        //validnado en caso de que sea de uso futuro
        if ($request->tipo_financiamiento == 2) {
            //venta de uso inmediato
            $validaciones['solicitud'] = 'required';

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $solicitud = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
                ->where('numero_solicitud', trim($request->solicitud))->where('operaciones.status', '<>', 0)->first();
            if (!empty($solicitud)) {
                if ($tipo_servicio == 'modificar') {
                    if ($solicitud->id != $request->id_venta) {
                        return $this->errorResponse('El número de solicitud ingresado ya ha sido registrado.', 409);
                    }
                } else {
                    return $this->errorResponse('El número de solicitud ingresado ya ha sido registrado.', 409);
                }
            }
        }

        //valido si es de venta antes del sistema
        if ($request->ventaAntiguedad['value'] == 2) {
            /**venta ya realizada anterior al sistema pero no liquidadada */
            $validaciones['convenio'] = 'required';
            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $convenio = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
                ->where('numero_convenio', trim($request->convenio))->where('operaciones.status', '<>', 0)->first();
            if (!empty($convenio)) {
                if ($tipo_servicio == 'modificar') {
                    if ($convenio->id != $request->id_venta) {
                        return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                    }
                } else {
                    return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
            }
        } else if ($request->ventaAntiguedad['value'] == 3) {
            /**venta ya liquidada antes del sistema */
            $validaciones['convenio'] = 'required';
            $validaciones['titulo'] = 'required';

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $convenio = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
                ->where('numero_convenio', $request->convenio)->where('operaciones.status', '<>', 0)->first();
            if (!empty($convenio)) {
                if ($tipo_servicio == 'modificar') {
                    if ($convenio->id != $request->id_venta) {
                        return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                    }
                } else {
                    return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
            }
            /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
            $titulo = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
                ->where('numero_titulo', $request->titulo)->where('operaciones.status', '<>', 0)->first();
            if (!empty($titulo)) {
                if ($tipo_servicio == 'modificar') {
                    if ($titulo->id != $request->id_venta) {
                        return $this->errorResponse('El número de título ingresado ya ha sido registrado.', 409);
                    }
                } else {
                    return $this->errorResponse('El número de título ingresado ya ha sido registrado.', 409);
                }
            }
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'id_venta.required' => 'Ingrese un la clave única de la venta para continuar',
            'max' => 'verifique la cantidad',
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'ubicacion.unique' => 'Este terreno ya fue vendido',
            'solicitud.unique' => 'Esta solicitud ya fue registrada en otra venta',
            'convenio.unique' => 'Este convenio ya fue registrado en otra venta',
            'titulo.unique' => 'Este título ya fue registrado en otra venta',
            'num_operacion.unique' => 'Este número de operación ya fue capturado',
            //beneficiarios
            '*.nombre.required' => 'ingrese este dato',
            '*.parentesco.required' => 'ingrese este dato',
            'lte' => 'verifique la cantidad',
            'unique.num_operacion' => 'Este número de operación ya fue registrado.',
            'pago_inicial.min' => 'El pago inicial debe ser al menos :min ',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        if ($tipo_servicio == 'agregar') {
            /**revisar si el cliente esta vigente para proceder con la venta */
            $cliente = Clientes::where('id', (int) $request->id_cliente)->first();
            if ($cliente->status != 1) {
                /**no esta activo y la venta no puede proceder */
                return $this->errorResponse('Este cliente no se encuentra activo en la base de datos.', 409);
            }
        }

        /**haciendo las validaciones necesarias para saber si se puede proceder con la modificacion de los datos */
        $reprogramar_pagos = false;
        if ($tipo_servicio == 'modificar') {
            /**tiene pagos vigentes, se debe validar lo que no puede modifcar.
             * todo aquello que altere los precios y totales de la venta, pues al tener pagos realizados vigentes se alteria
             * el contrato y la programacion de pagos
             * solo puede modificar lo que no altere el contrato como cliente,vendedor, cliente
             * titular sustituto
             * beneficiarios
             */
            /**verificando que no hay modificado nada relativo a precios */

            if (
                $request->financiamiento != $datos_venta['financiamiento'] ||
                $request->fecha_venta != $datos_venta['fecha_operacion'] ||
                (round($iva, 2, PHP_ROUND_HALF_UP) != round($datos_venta['impuestos'], 2, PHP_ROUND_HALF_UP) ||
                    round($subtotal, 2, PHP_ROUND_HALF_UP) != round($datos_venta['subtotal'], 2, PHP_ROUND_HALF_UP) ||
                    round($total_pagar, 2, PHP_ROUND_HALF_UP) != round($datos_venta['total'], 2, PHP_ROUND_HALF_UP) ||
                    ((float) $request->pago_inicial) != (count($datos_venta['pagos_programados']) > 0 ? ((float) $datos_venta['pagos_programados'][0]['monto_programado']) : 0) ||
                    round($descuento_real_para_impuestos, 2, PHP_ROUND_HALF_UP) != round($datos_venta['descuento'], 2, PHP_ROUND_HALF_UP))
            ) {
                if ($datos_venta['total'] > 0) {
                    /**si la venta no fue gratis */
                    if ($datos_venta['pagos_realizados'] > 0) {

                        return $this->errorResponse('La venta no puede modificar datos relativos a cantidades, fecha, ubicacion, tipo de venta, tipo de
                financiamiento, etc. Esto se debe a que existen pagos relacionados a esta venta y modificar cantidades o precios causaría que se perdiera la integridad de esta información.', 409);
                    } else {
                        $reprogramar_pagos = true;
                    }
                } else {
                    /**la venta no fue gratis */
                    return $this->errorResponse('La venta no puede modificar datos relativos a cantidades, fecha, ubicacion, tipo de venta, tipo de
                financiamiento, etc. Esto se debe a que la venta tiene un saldo de $0.00 pesos(está liquidada).', 409);
                }
            }
        }

        try {
            /**bloqueando ubicaciones que estan a la venta pero que no están habilitadas en el cementerio
             * UBICACION TIPO-ID-FILA-COLUMNA
             */
            $bloqueadas = [
                /**Propiedad terraza 23 id 55 */
                '4-55-2-14',
                '4-55-2-15',
                '4-55-2-16',
                '4-55-2-17',
                '4-55-2-18',
                '4-55-2-19',
                /**Propiedad terraza 23 id 55 */

                /**Propiedad terraza 25 id 57 */
                '4-57-2-6',
                '4-57-2-14',
                '4-57-2-20',
                /**Propiedad terraza 25 id 57 */

                /**Propiedad terraza 28 id 60 */
                '4-60-1-9',
                /**Propiedad terraza 28 id 60 */

                /**Propiedad terraza 29 id 61 */
                '4-61-1-9',
                '4-61-2-24',
                '4-61-4-20',
                '4-61-5-20',
                /**Propiedad terraza 29 id 61 */

                /**Propiedad terraza 30 id 62 */
                '4-62-2-13',
                '4-62-3-13',
                '4-62-3-27',
                '4-62-4-13',
                '4-62-4-27',
                '4-62-5-13',
                '4-62-5-27',
                /**Propiedad terraza 30 id 62 */

                /**Propiedad terraza 31 id 63 */
                '4-63-2-11',
                '4-63-2-23',
                '4-63-3-11',
                '4-63-3-23',
                /**Propiedad terraza 31 id 63 */

                /**Propiedad terraza 32 id 64 */
                '4-64-1-13',
                /**Propiedad terraza 32 id 64 */

                /**Propiedad terraza 33 id 65
                 * pendiente de revisar cual ubicacion bloquear
                 */
                '4-65-5-21',
                /**Propiedad terraza 33 id 65 */

                /**Propiedad terraza 34 id 66
                 * pendiente de revisar cual ubicacion bloquear
                 */
                '4-66-2-17',
                '4-66-3-17',
                '4-66-3-17',
                '4-66-4-9',
                '4-66-4-17',
                '4-66-5-9',
                '4-66-5-17',
                /**Propiedad terraza 34 id 66 */

                /**Propiedad terraza 35 id 67 */
                '4-67-2-21',
                '4-67-3-13',
                '4-67-3-21',
                '4-67-4-13',
                '4-67-4-21',
                '4-67-5-13',
                '4-67-5-21',
                /**Propiedad terraza 35 id 67 */

                /**Propiedad terraza 36 id 68 */
                '4-68-1-4',
                '4-68-1-10',
                '4-68-1-16',
                '4-68-1-17',
                '4-68-2-4',
                '4-68-2-10',
                '4-68-2-16',
                '4-68-2-17',
                '4-68-3-4',
                '4-68-3-10',
                '4-68-3-17',
                '4-68-4-17',
                '4-68-5-17',
                /**Propiedad terraza 36 id 68 */

                /**Propiedad terraza 37 id 69 */
                '4-69-1-5',
                '4-69-1-13',
                '4-69-1-21',
                '4-69-1-22',
                '4-69-2-5',
                '4-69-2-13',
                '4-69-2-21',
                '4-69-2-22',
                '4-69-3-5',
                '4-69-3-13',
                '4-69-3-21',
                '4-69-3-22',
                '4-69-4-5',
                '4-69-4-22',
                '4-69-5-22',
                /**Propiedad terraza 37 id 69 */

                /**Propiedad terraza 38 id 70 */
                '4-70-1-9',
                '4-70-1-17',
                '4-70-1-25',
                '4-70-2-9',
                '4-70-2-17',
                '4-70-2-25',
                '4-70-3-9',
                '4-70-3-17',
                '4-70-3-25',
                /**Propiedad terraza 38 id 70 */

                /**Propiedad terraza 39 id 71
                 * con dudas sobre la fila 5
                 */
                '4-71-1-11',
                '4-71-1-21',
                '4-71-1-22',
                '4-71-1-32',
                '4-71-1-33',

                '4-71-2-11',
                '4-71-2-21',
                '4-71-2-22',
                '4-71-2-32',
                '4-71-2-33',

                '4-71-3-11',
                '4-71-3-21',
                '4-71-3-22',
                '4-71-3-33',
                '4-71-4-22',
                '4-71-4-33',
                '4-71-5-33',
                /**Propiedad terraza 39 id 71 */

                /**Propiedad terraza 40 id 72 */
                '4-72-1-10',
                '4-72-1-18',
                '4-72-1-19',
                '4-72-1-25',
                '4-72-2-10',
                '4-72-2-18',
                '4-72-2-19',
                '4-72-1-25',
                '4-72-3-10',
                '4-72-3-18',
                '4-72-3-25',
                '4-72-4-10',
                '4-72-4-18',
                /**Propiedad terraza 40 id 72 */

                /**Propiedad terraza 41 id 73 */
                '4-73-1-7',
                '4-73-1-8',
                '4-73-1-14',
                '4-73-1-15',
                '4-73-1-21',

                '4-73-2-7',
                '4-73-2-8',
                '4-73-2-14',
                '4-73-2-15',
                '4-73-2-21',

                '4-73-4-7',
                '4-73-4-15',
                '4-73-4-21',

                '4-73-5-7',
                '4-73-5-21',
                /**Propiedad terraza 41 id 73 */

                /**Propiedad terraza 42 id 74 */
                '4-74-1-4',
                '4-74-1-10',
                '4-74-1-11',
                '4-74-1-12',
                '4-74-1-13',
                '4-74-2-4',
                '4-74-2-10',
                '4-74-2-11',
                '4-74-2-12',
                '4-74-3-4',
                '4-74-3-10',
                '4-74-3-11',
                '4-74-4-4',
                '4-74-4-11',
                /**Propiedad terraza 42 id 74 */

                /**Propiedad terraza 43 id 75 */
                '4-75-1-4',
                '4-75-1-12',
                '4-75-1-13',
                '4-75-1-14',
                '4-75-1-15',
                '4-75-1-16',
                '4-75-2-4',
                '4-75-2-13',
                '4-75-2-14',
                '4-75-2-15',
                '4-75-2-16',
                '4-75-3-4',
                '4-75-3-14',
                '4-75-3-15',
                '4-75-3-16',
                '4-75-4-14',
                /**Propiedad terraza 43 id 75 */
            ];

            /**verificando si alguna propiedad de las bloqueadas fue elegida para venta */
            foreach ($bloqueadas as $key => $ubicacion) {
                if ($ubicacion == $request->ubicacion) {
                    /**es una ubicacion bloqueada */
                    return $this->errorResponse('Error, Esta ubicación no se toma en cuenta en el cementerio.', 409);
                }
            }
            $id_venta = 0;
            $id_operacion_cuota = 0;
            DB::beginTransaction();
            ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
            if ($tipo_servicio == 'agregar') {
                //venta de uso inmediato y de control sistematizado
                //captura de la venta
                $id_venta = DB::table('ventas_terrenos')->insertGetId(
                    [
                        'ubicacion' => $request->ubicacion,
                        'propiedades_id' => $request->propiedades_id,
                        'tipo_propiedades_id' => $request->tipo_propiedades_id,
                        'vendedor_id' => (int) $request->vendedor['value'],
                        'considerar_mantenimiento_b' => 1,
                        'tipo_financiamiento' => $request->tipo_financiamiento,
                        'salarios_minimos' => $request->salarios_minimos,
                    ]
                );
                /**a partir de la venta se crea la operaicon */
                $id_operacion = DB::table('operaciones')->insertGetId(
                    [
                        'clientes_id' => (int) $request->id_cliente,
                        'ventas_terrenos_id' => $id_venta,
                        /**venta a futuro solamente */
                        'numero_solicitud' => ($request->tipo_financiamiento == 2) ? $request->solicitud : null,
                        /**venta  liquidada solamente */
                        'numero_convenio' => $this->generarNumeroConvenio($request),
                        'numero_titulo' => ($request->ventaAntiguedad['value'] == 3) ? $request->titulo : null,
                        'empresa_operaciones_id' => 1, //venta de terrenos
                        'subtotal' => round($subtotal, 2, PHP_ROUND_HALF_UP),
                        'tasa_iva' => $tasa_iva,
                        'descuento' => round($descuento_real_para_impuestos, 2, PHP_ROUND_HALF_UP),
                        'impuestos' => round($iva, 2, PHP_ROUND_HALF_UP),
                        'total' => round($total_pagar, 2, PHP_ROUND_HALF_UP),
                        'saldo' => round($total_pagar, 2, PHP_ROUND_HALF_UP),
                        'descuento_pronto_pago_b' => 1,
                        'costo_neto_pronto_pago' => round($total_pagar, 2, PHP_ROUND_HALF_UP), //paso este dato por defecto pues no se utiliza en la practica
                        'antiguedad_operacion_id' => (int) $request->ventaAntiguedad['value'],
                        /** titular_sustituto */
                        'titular_sustituto' => $request->titular_sustituto,
                        'parentesco_titular_sustituto' => $request->parentesco_titular_sustituto,
                        'telefono_titular_sustituto' => $request->telefono_titular_sustituto,
                        'financiamiento' => $request->financiamiento,
                        'aplica_devolucion_b' => 0,
                        'costo_neto_financiamiento_normal' => $costo_neto,
                        'comision_venta_neto' => 0,
                        'fecha_registro' => now(),
                        'fecha_operacion' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                        'registro_id' => (int) $request->user()->id,
                        'nota' => $request->nota,
                        'status' => $costo_neto > 0 ? 1 : '2',
                    ]
                );
                /**guardando los datos de la tasa para intereses */
                $this->guardarAjustesPoliticas($request, $id_operacion);
                /**programacion de pagos */
                if ($costo_neto > 0) {
                    /**si la cantidad que resta a pagar es mayor a cero se manda llamar la programcion de pagos */
                    $this->programarPagos($request, $id_operacion, $id_venta, '001');
                } else {
                    /**no hay nada que cobrar, por lo cual debemos generar un numero de titulo inmeadiato */
                    $this->generarNumeroTitulo($id_operacion, true);
                }
                //captura de los beneficiarios
                $this->guardarBeneficiarios($request, $id_operacion);
                /**guardar venta parte final */
                /**captura de pagos */
                /**fin de captura de pagos */

                /**aqui verifico la fecha de la venta para ver que cuotas de mantenimiento se le deben adjuntar
                 * para ello se deben obterner las cuotas que le corresponden segun la fecha de la venta del terreno
                 *
                 */
                /**aqui estoy */

                $cuotas = Cuotas::select('*')->where('fecha_inicio', '>=', $request->fecha_venta)->get()->toArray();
                foreach ($cuotas as $cuota) {
                    $tasa_iva = $cuota['tasa_iva'];
                    $tasa_iva_calculos = ($tasa_iva / 100) + 1;
                    $tasa_iva_decimal = ($tasa_iva / 100);
                    $total = $cuota['cuota_total'];
                    $subtotal = round($total / $tasa_iva_calculos, 2);
                    $descuento = 0;
                    $impuestos = round(($subtotal - $descuento) * $tasa_iva_decimal, 2);
                    /**datos a guardar en la operacion
                     * tasa_iva
                     * subtotal
                     * descuento
                     * impuestos
                     * total
                     * descuento_pronto_pago_b => 0
                     * ventas_terrenos_id
                     * financiamiento => 1 contado
                     * clientes_id
                     * fecha_registro
                     * fecha_operacion
                     * registro_id
                     * modifico_id
                     * fecha_modificacion
                     * status
                     * cuotas_cementerio_id
                     * aplica_devolucion_b =>0
                     */
                    /**agrego los datos del request que necesita la funcion de programar pagos */
                    $request->request->add(['costo_neto' => $total]);
                    $request->request->add(['pago_inicial' => $total]);
                    $request->request->add(['descuento' => $descuento]);
                    $request->request->add(['fecha_venta' => $cuota['fecha_inicio']]);
                    $request->request->add(['tipo_financiamiento' => 1]); //de contado

                    /**guardo operacion */
                    $id_operacion_cuota = DB::table('operaciones')->insertGetId(
                        [
                            'empresa_operaciones_id' => 2,
                            'cuotas_cementerio_id' => $cuota['id'],
                            'tasa_iva' => $tasa_iva,
                            'subtotal' => $subtotal,
                            'descuento' => $descuento,
                            'impuestos' => $impuestos,
                            'total' => $total,
                            'descuento_pronto_pago_b' => 0,
                            'ventas_terrenos_id' => $id_venta,
                            'financiamiento' => 1,
                            'clientes_id' => (int) $request->id_cliente,
                            'fecha_registro' => now(),
                            'fecha_operacion' => $cuota['fecha_inicio'],
                            'registro_id' => (int) $request->user()->id,
                            'modifico_id' => (int) $request->user()->id,
                            'status' => $cuota['status'],
                            'aplica_devolucion_b' => 0,
                            'costo_neto_pronto_pago' => $total,
                        ]
                    );

                    /**operacion tipo 2- 002- SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO.
                     * El id venta lo modificamos para que se ajuste a las necesidades de esta operacion
                     * en el caso de pago de cuota de mantenimiento se agregan los digitos de la tabla de ventas_terrenos para mantener la singularidad del registro de pagos en
                     * la BD final asi 00220210814011-140
                     */
                    $this->programarPagos($request, $id_operacion_cuota, $cuota['id'] . '-' . $id_venta, '002');

                    //return $this->errorResponse($id_operacion_cuota, 409);
                }
            }
            /**fin if servicio tipo agregar */ else {
                $id_venta = $request->id_venta;
                /**es modificar */
                DB::table('ventas_terrenos')->where('id', '=', $request->id_venta)->update(
                    [
                        'ubicacion' => $request->ubicacion,
                        'propiedades_id' => $request->propiedades_id,
                        'tipo_propiedades_id' => $request->tipo_propiedades_id,
                        'vendedor_id' => (int) $request->vendedor['value'],
                        'tipo_financiamiento' => $request->tipo_financiamiento,
                        'salarios_minimos' => $request->salarios_minimos,
                    ]
                );

                DB::table('operaciones')->where('id', '=', $datos_venta['operacion_id'])->update(
                    [
                        'clientes_id' => (int) $request->id_cliente,
                        /**venta a futuro solamente */
                        'numero_solicitud' => ($request->tipo_financiamiento == 2) ? trim($request->solicitud) : null,
                        /**venta  liquidada solamente */
                        'numero_convenio' => trim($request->convenio),
                        'numero_titulo' => trim($request->titulo),
                        'subtotal' => round($subtotal, 2, PHP_ROUND_HALF_UP),
                        'tasa_iva' => $tasa_iva,
                        'descuento' => round($descuento_real_para_impuestos, 2, PHP_ROUND_HALF_UP),
                        'impuestos' => round($iva, 2, PHP_ROUND_HALF_UP),
                        'total' => round($total_pagar, 2, PHP_ROUND_HALF_UP),
                        'saldo' => round($total_pagar, 2, PHP_ROUND_HALF_UP),
                        'descuento_pronto_pago_b' => 1,
                        'costo_neto_pronto_pago' => round($total_pagar, 2, PHP_ROUND_HALF_UP), //paso este dato por defecto pues no se utiliza en la practica
                        'antiguedad_operacion_id' => (int) $request->ventaAntiguedad['value'],
                        /** titular_sustituto */
                        'titular_sustituto' => $request->titular_sustituto,
                        'parentesco_titular_sustituto' => $request->parentesco_titular_sustituto,
                        'telefono_titular_sustituto' => $request->telefono_titular_sustituto,
                        'financiamiento' => $request->financiamiento,
                        'costo_neto_financiamiento_normal' => $costo_neto,
                        'status' => ($costo_neto > 0 && $datos_venta['saldo_neto'] > 0) ? '1' : '2',
                        'fecha_modificacion' => now(),
                        'fecha_operacion' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                        'modifico_id' => (int) $request->user()->id,
                        'nota' => $request->nota,
                    ]
                );

                /**verificamos si tiene pagos realizados para saber si podemos eliminar los pagos existentes o si solo cancelarlos */
                if ($reprogramar_pagos == true) {
                    if ($datos_venta['pagos_realizados'] > 0) {
                        /**cancelamos los pagos programados vigentes */
                        DB::table('pagos_programados')->where('operaciones_id', '=', $datos_venta['operacion_id'])->update(
                            [
                                'status' => 0,
                            ]
                        );
                    } else {
                        /**podemos eliminar los pagos programados viegente para rehacerlo */
                        DB::table('pagos_programados')->where('operaciones_id', '=', $datos_venta['operacion_id'])->delete();
                    }
                    /**programacion de pagos */
                    if ($costo_neto > 0) {

                        /**si la cantidad que resta a pagar es mayor a cero se manda llamar la programcion de pagos */
                        $this->programarPagos($request, $datos_venta['operacion_id'], $request->id_venta, '001');
                    } else {
                        /**no hay nada que cobrar, por lo cual debemos generar un numero de titulo inmeadiato */
                        if (trim($datos_venta['numero_titulo']) == '') {
                            $this->generarNumeroTitulo($datos_venta['operacion_id'], true);
                        }
                    }
                }
                //captura de los beneficiarios
                $this->guardarBeneficiarios($request, $datos_venta['operacion_id']);
                /**pendiente hacer modificacion de progrmacion de pagos */
            } //fin else de modificar venta de propiedad

            DB::commit();
            return $id_venta;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
        /**FIN DE VENTA A USO INMEDIATO */
        /**********FIN DE CASOS DE VENTA 1 - NUEVA VENTA "SISTEMATIZADA" */
    }

    /**generar numero de convenio automatico */
    public function generarNumeroConvenio(Request $request)
    {
        $numero_convenio = 0;

        if ($request->ventaAntiguedad['value'] == 1) {
            //debo generar el numero de convenio
            //para aquellas ventas que son a futuro pero del sistema nuevo, con control sistematizado, el orden de convenios con sistemas comenzará con el numero
            //500 (quinientos)
            //determino si ya esta en funcion la asignacion de numeros de convenios automaticos
            $ajustes = Ajustes::first();
            if ($ajustes->numero_convenios_sistematizados == true) {
                //quiere decir que ya esta funcionando esto y debo elejir el numero de convenio mayor para crear el siguiente
                //$numero_convenio = ((int) VentasPropiedades::where('antiguedad_ventas_id', 1)->where('ventas_referencias_id', 2)->max('numero_convenio')) + 1;
                $result = DB::select(DB::raw("SELECT MAX(CAST(numero_convenio AS UNSIGNED) ) AS max_numero_convenio FROM operaciones"));
                $ultimo_convenio = json_decode(json_encode($result), true)[0]['max_numero_convenio'];
                $numero_convenio = ((float) $ultimo_convenio) + 1;
                if ($numero_convenio < 500) {
                    //lo forzo a iniciar desde el 500
                    $numero_convenio = 500;
                }
            } else {
                //comenzamos en numero 500 (quinientos) y marcamos numero_convenios_sistematizados como true en la base de datos
                $ajustes->numero_convenios_sistematizados = true;
                $ajustes->timestamps = false;
                $ajustes->save();
                $numero_convenio = 500;
            }
        } else {
            //se debe ingresar manualmente
            $numero_convenio = $request->convenio; //es el que capturo el usuario
        }

        //se retorna el nuevo numero de convenio generado
        return $numero_convenio;
    }

    /**generar numero de convenio titulo */
    public function generarNumeroTitulo($operacion_id = 0, $liquidado = false)
    {
        if ($operacion_id > 0) {
            /**pasa a generar el numero de titulo*/

            /**se debe revisar si esta operacion es apta para crearle un numero de titulo automatico */
            $operacion_info = Operaciones::where('operaciones.status', '<>', 0)
                ->where('operaciones.id', '=', $operacion_id)
                ->first()->toArray();
            if (isset($operacion_info['antiguedad_operacion_id'])) {
                if ($operacion_info['antiguedad_operacion_id'] != 3) {
                    /**verificando que la operacion no tenga un titulo ya asigando, si tiene titulo se le deja el original */
                    if (trim($operacion_info['numero_titulo']) == '') {
                        //determino si ya esta en funcion la asignacion de numeros de titulos automaticos
                        $ajustes = Ajustes::first();
                        $numero_titulo = 0;
                        if ($ajustes->numero_titulos_sistematizados == true) {
                            //quiere decir que ya esta funcionando esto y debo elejir el numero de convenio mayor para crear el siguiente
                            $result = DB::select(DB::raw("SELECT MAX(CAST(numero_titulo AS UNSIGNED) ) AS max_numero_titulo FROM operaciones"));
                            $ultimo_titulo = json_decode(json_encode($result), true)[0]['max_numero_titulo'];
                            if (intval($ultimo_titulo) > 0) {
                                $numero_titulo = $ultimo_titulo + 1;
                            } else {
                                $numero_titulo = 500;
                            }
                        } else {
                            //comenzamos en numero 500 (quinientos) y marcamos numero_titulos_sistematizados como true en la base de datos
                            $ajustes->numero_titulos_sistematizados = true;
                            $ajustes->timestamps = false;
                            $ajustes->save();
                            $numero_titulo = 500;
                        }

                        $operacion = Operaciones::find($operacion_id);
                        //actualizamos la venta con su nuevo numero de titulo
                        $operacion->numero_titulo = $numero_titulo;
                        $operacion->timestamps = false;
                        $operacion->save();
                    }
                }
            }
        }
    }

    public function guardarAjustesPoliticas(Request $request, $operacion_id = 0)
    {
        /**aqui obtengo el plan de intereses con que funcionara esta venta */
        $ajustes_politicas = AjustesPoliticas::find(1);
        /**hago un registro con la informacion que afectara el control de intereses para esta venta */
        DB::table('ajustes_politicas_operacion')->insert(
            [
                'tasa_fija_anual' => $ajustes_politicas->tasa_fija_anual,
                'dias_antes_vencimiento' => $ajustes_politicas->dias_antes_vencimiento,
                'maximo_dias_retraso' => $ajustes_politicas->maximo_dias_retraso,
                'porcentaje_pena_convencional_minima' => $ajustes_politicas->porcentaje_pena_convencional_minima,
                'minima_partes_cubiertas' => $ajustes_politicas->minima_partes_cubiertas,
                'maximo_pagos_vencidos' => $ajustes_politicas->maximo_pagos_vencidos,
                'maximo_dias_cancelar_contrato' => $ajustes_politicas->maximo_dias_cancelar_contrato,
                'operaciones_id' => $operacion_id,
            ]
        );
    }

    //guarda los beneficiarios de la venta de una propiedad
    public function guardarBeneficiarios(Request $request, $operacion_id = 0)
    {

        /**primero elimino beneficiarios si existen, de esta forma
         * la funcion me sirve perfecto tanto para insertar beneficiarios y actualizarlos
         */
        DB::table('beneficiarios')->where('operaciones_id', $operacion_id)->delete();

        //id del conjunto de propieades
        for ($i = 0; $i < count($request['beneficiarios']); $i++) {
            DB::table('beneficiarios')->insert(
                [
                    'nombre' => $request['beneficiarios'][$i]['nombre'],
                    'parentesco' => $request['beneficiarios'][$i]['parentesco'],
                    'telefono' => $request['beneficiarios'][$i]['telefono'],
                    'operaciones_id' => $operacion_id,
                ]
            );
        }
    }

    //guarda los beneficiarios de la venta de una propiedad
    public function programarPagos(Request $request, $operacion_id = 0, $id_venta = 0, $referencia_pago_clave = '')
    {
        /**aqui comienzan a gurdar los datos */
        /* $subtotal = round($request->subtotal, 2, PHP_ROUND_HALF_UP); //sin iva
        $iva = round($request->impuestos, 2, PHP_ROUND_HALF_UP); //solo el iva
        $descuento = round($request->descuento, 2, PHP_ROUND_HALF_UP);
        $costo_neto = round($request->costo_neto, 2, PHP_ROUND_HALF_UP);
        $pago_inicial = round($request->pago_inicial, 2, PHP_ROUND_HALF_UP);
        $total_pagar = round($request->total_pagar, 2, PHP_ROUND_HALF_UP);
         */

        $pago_inicial = $request->pago_inicial;

        /**creamos los calculos del total a pagar para desglosar impuestos y pago inicial necesario segun el financiamiento */
        /**aqui comienzan a gurdar los datos */
        $tasa_iva_calculos = 0;
        if (isset($request->tasa_iva) && isset($request->costo_neto) && isset($request->descuento)) {
            $tasa_iva_calculos = ($request->tasa_iva / 100) + 1;
        } else {
            return $this->errorResponse('Ingrese IVA, costo neto y descuento.', 409);
        }

        $tasa_iva = $request->tasa_iva;

        $costo_neto = $request->costo_neto;
        $descuento = $request->descuento;

        /**total neto a pagar */
        $total_pagar = $costo_neto - $descuento;

        /**calculando los descuentos para calcular los impuestos por IVA */
        $subtotal = $costo_neto / $tasa_iva_calculos;
        $subtotal_con_descuento = $total_pagar / $tasa_iva_calculos;
        /**obtengo la cantidad que se le aplica al subototal como descuento para registrar impuestos */
        $descuento_real_para_impuestos = $subtotal - $subtotal_con_descuento;

        /**valdiando que cuadren las cantidades de la venta */

        //verificando si la venta viene con algun descuento
        /**como se genera la referencia del pago para realizar pago en bancos */
        /*•    3 dígitos según la tabla de empresa_operaciones
        •    Fecha programada del pago, 8 dígitos(ej., 20200601
        •    Numero de pago 01, 02, 12, 18, 24, 32, máximo son 64 etc. (2 dígitos)
        •    Id de la tabla origen que se incluye en la tabla de operaciones, es decir, si la operación es tipo venta de terrenos,
        el id se tomara de la tabla ventas_terrenos, y así respectivamente el tipo de operación. De esta manera vamos incrementando
        las referencias según el tipo de operaciones y la tabla de operaciones solo la utilizamos para darle uso de centralización de tablas.
        Ejemplo de referencia de pago
        Referencia de pago para venta de terrenos de contado realizada el día 01/junio/2020.
        •    00120200601011(3 dígitos del tipo de operación, 8 dígitos 20200601, 2 dígitos del número de pago 01 y el id de la tabla de origen
        según la operación, en este caso de la tabla venta de terrenos, el id de la venta numero 1 o según el id de la venta).
        en el caso de pago de cuota de mantenimiento se agregan los digitos de la tabla de ventas_terrenos para mantener la singularidad del registro de pagos en
        la BD
        quedando al final asi
        00220210814011-140
         */
        //puede que venga con descuento pero no es del 100%
        //determinamos que tipo de ventas
        if ($request->tipo_financiamiento == 1 || (int) $request->financiamiento == 1) {
            //de uso inmediato sin importar si es seleccionado a futuro o inmediato ya que selecciono pagarlo de contado
            /**se crea un solo pago */
            //se agregan 0 dias a los enganches y a las liquidaciones para ser capturadas
            $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add(0, 'day');
            $id_pago_programado_unico = DB::table('pagos_programados')->insertGetId(
                [
                    'num_pago' => 1, //numero 1, pues es unico
                    'referencia_pago' => $referencia_pago_clave . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                    'fecha_programada' => $fecha_maxima, //fecha de la venta
                    'conceptos_pagos_id' => 3, //3-pago unico //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                    'monto_programado' => $total_pagar,
                    'operaciones_id' => $operacion_id,
                    'status' => 1,
                ]
            );
        } else {
            //registro el enganche
            /**los pagos deben llevar los valores en proporcion al descuento
             * por decir asi, cuando el precio lleva descuento se debe de repartir el descuento total entre los diferentes pagos
             * segun el porcentaje del pago
             */

            $resto_a_mensualidades = $total_pagar - $pago_inicial;

            //enganche inicial mandado mas lo descontado para sacar impuestos completos

            //se agregan tres dias a los enfanches y a las liquidaciones para ser capturadas
            $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add(0, 'day');

            $id_pago_programado_enganche = DB::table('pagos_programados')->insertGetId(
                [
                    'num_pago' => 1, //numero 1, pues es unico
                    'referencia_pago' => $referencia_pago_clave . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                    'fecha_programada' => $fecha_maxima, //fecha de la venta
                    'conceptos_pagos_id' => 1, //3-pago unico //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                    'monto_programado' => $pago_inicial,
                    'operaciones_id' => $operacion_id,
                    'status' => 1,
                ]
            );

            //$monto_abono = round(($costo_neto - $pago_inicial) / $request->financiamiento, 2, PHP_ROUND_HALF_UP);

            //a futuro y a meses
            for ($i = 1; $i <= ((int) $request->financiamiento); $i++) {
                /**calculando el abono en bruto */
                $monto_abono = ($total_pagar - $pago_inicial) / $request->financiamiento;

                $numero_pago_para_referencia = '';
                if ($i < 9) {
                    //se debe asignar un cero (0) para crear la referencia correcta
                    $numero_pago_para_referencia = '0' . ($i + 1);
                } else {
                    $numero_pago_para_referencia = ($i + 1);
                }
                $fecha = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add($i, 'month');

                /**definiendo el monto del abono a programar */
                if ($i == 1) {
                    /**aqui debemos hacer que el primer abono absorba todos los decimales para que los proximos abonos salgan limpios(enteros) */
                    $decimales = $monto_abono - intval($monto_abono);
                    if ($decimales > 0) {
                        /**tiene decimales */
                        $abono = ($total_pagar - $request->pago_inicial) - (intval($monto_abono) * $request->financiamiento);
                        $monto_abono = round($abono + intval($monto_abono), 2, PHP_ROUND_HALF_UP);
                    }
                } else {
                    $abono = intval($monto_abono);
                    $monto_abono = $abono;
                }

                $id_pago_programado = DB::table('pagos_programados')->insertGetId(
                    [
                        'num_pago' => ($i + 1), //numero 1, pues es unico
                        'referencia_pago' => $referencia_pago_clave . date('Ymd', strtotime($request->fecha_venta)) . $numero_pago_para_referencia . $id_venta, //se crea una referencia para saber a que pago pertenece
                        'fecha_programada' => $fecha, //fecha de la venta
                        'conceptos_pagos_id' => 2, //3-pago unico //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                        'monto_programado' => $monto_abono,
                        'operaciones_id' => $operacion_id,
                        'status' => 1,
                    ]
                );
            }
        }
    }

    public function propiedadesById(Request $request)
    {
        //id del conjunto de propieades
        $id_propiedad = $request->id_propiedad;

        $datos = Propiedades::select(
            '*',
            DB::raw(
                '(NULL) AS nombre_area'
            )
        )
            ->with('filas_columnas')->with('tipoPropiedad')->orderBy('tipo_propiedades_id', 'asc')->where('propiedades.id', '=', $id_propiedad)->get()->toArray();

        if ($datos[0]['tipo_propiedades_id'] == 1) {
            /**uniplex */
            $datos[0]['nombre_area'] = 'Sección uniplex ' . $datos[0]['propiedad_indicador'];
        } else if ($datos[0]['tipo_propiedades_id'] == 2) {
            /**duplex */
            $datos[0]['nombre_area'] = 'Sección duplex ' . $datos[0]['propiedad_indicador'];
        } else if ($datos[0]['tipo_propiedades_id'] == 3) {
            /**nichos */
            $datos[0]['nombre_area'] = 'nichos columna ' . $datos[0]['propiedad_indicador'];
        } else if ($datos[0]['tipo_propiedades_id'] == 4) {
            /**terrazas */
            $datos[0]['nombre_area'] = 'Terraza ' . $datos[0]['propiedad_indicador'];
        } else if ($datos[0]['tipo_propiedades_id'] == 5) {
            /**triplex */
            $datos[0]['nombre_area'] = 'Sección Triplex ' . $datos[0]['propiedad_indicador'];
        } else if ($datos[0]['tipo_propiedades_id'] == 6) {
            /**cuadriplez dsin terraza */
            $datos[0]['nombre_area'] = 'Sección de cuadriplex ' . $datos[0]['propiedad_indicador'];
        } else {
            /**cuadriplex de mausoleo */
            $datos[0]['nombre_area'] = 'Sección de mausoleo ' . $datos[0]['propiedad_indicador'];
        }

        return $datos;
    }

    //retorna los tipos de propiedad
    public function tipoPropiedades()
    {
        return DB::table('tipo_propiedades')->get();
    }

    public function get_financiamientos()
    {
        $resultado = tipoPropiedades::with('precios')->withCount('precios')->get()->toArray();

        foreach ($resultado as $tipo_key => &$tipo) {
            foreach ($tipo['precios'] as $precio_key => &$precio) {
                if ($precio['financiamiento'] == 1) {
                    $precio['tipo_financiamiento'] = "Pago Único/Uso Inmediato";
                    $precio['tipo_financiamiento_ingles'] = "Spot Price";
                    $precio['pago_mensual']
                        = 0;
                } else {
                    $precio['tipo_financiamiento'] = "Pago a " . $precio['financiamiento'] . " Meses/A Futuro";
                    $precio['tipo_financiamiento_ingles'] = $precio['financiamiento'] . "-Month Payment";
                    $precio['pago_mensual']
                        = ($precio['costo_neto'] - $precio['pago_inicial']) / $precio['financiamiento'];
                }
                /**sacando los descuentos en caso de que tenga pronto pago */
                if ($precio['descuento_pronto_pago_b'] == 1) {
                    $precio['descuento_x_pago'] = ($precio['costo_neto'] - $precio['costo_neto_pronto_pago']) / $precio['financiamiento'];
                    $precio['porcentaje_pronto_pago'] = 100 - (($precio['costo_neto_financiamiento_normal'] * 100) / $precio['costo_neto']);
                } else {
                    $precio['descuento_x_pago'] = ' 0';
                    $precio['porcentaje_pronto_pago'] = ' 0';
                }
            }
        }
        return $resultado;
    }

    /**obtiene todos los tipos de propiedades */
    public function get_tipo_propiedades()
    {
        $resultado = tipoPropiedades::orderBy('id', 'asc')->get();
        return $resultado;
    }

    /**obtiene un precio por id */
    public function get_precio_by_id(Request $request)
    {
        if (!$request->id_precio) {
            return $this->errorResponse('Es necesario un id, para continuar', 409);
        }
        $resultado = PreciosPropiedades::where('id', $request->id_precio)->get()->first();
        return $resultado;
    }

    /**GUARDAR PRECIO DE PROPIEDAD*/
    public function registrar_precio_propiedad(Request $request)
    {

        //validaciones directas sin condicionales
        $validaciones = [
            'descripcion' => 'required',
            'contado_b.value' => 'required|integer|min:0|max:1',
            'financiamiento' => '',
            'pago_inicial' => '',
            'costo_neto' => 'required|numeric|min:1',
            'tipo_propiedades_id.value' => 'required',
        ];

        $mensaje_financiamiento = '';
        $mensaje_pago_inicial = '';
        /**validando si es a contado o credito */
        if ($request->contado_b['value'] == 1) {
            //es a contado
            $validaciones['financiamiento'] = 'required|integer|max:1';
            $mensaje_financiamiento = ' Este dato debe ser "1" Máximo';

            /**forzando en pago inicial a ser igual al costo neto de la propiedad */
            $validaciones['pago_inicial'] = 'required|numeric|min:' . $request->costo_neto . '|max:' . $request->costo_neto;
            $mensaje_pago_inicial = 'Este valor debe ser "$ 1.00" Mínimo y $ ' . number_format(($request->costo_neto), 2) . " máximo.";
        } else {
            /**es a credito */
            $validaciones['financiamiento'] = 'required|integer|min:2|max:120';
            $mensaje_financiamiento = ' Este dato debe ser "2" Mínimo y "120" Máximo';

            /**se puede mantener el pago inicial por debajo del costo neto */
            $validaciones['pago_inicial'] = 'required|numeric|min:1|max:' . ($request->costo_neto * .7);
            $mensaje_pago_inicial = 'Este valor debe ser "$ 1.00" Mínimo y $ ' . number_format(($request->costo_neto * .7), 2) . " máximo.";
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/

        $mensajes = [
            'pago_inicial.min' => $mensaje_pago_inicial,
            'pago_inicial.max' => $mensaje_pago_inicial,
            'financiamiento.min' => $mensaje_financiamiento,
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'costo_neto.min' => 'Esta cantidad debe ser mayor a cero',
            'costo_neto.gte' => 'Esta cantidad debe ser mayor o igual al costo neto de contado',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        /**validar si el precio existe */
        $precio = PreciosPropiedades::where('tipo_propiedades_id', $request->tipo_propiedades_id['value'])
            ->where('financiamiento', $request->financiamiento)
            ->get()
            ->first();
        if (!empty($precio)) {
            /**ya existe el precio */
            if ($precio->status == 1) {
                return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento', 409);
            } else {
                return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento, solo debe habilitarlo nuevamente', 409);
            }
        }

        try {
            $subtotal = (float) (($request->costo_neto / (1 + config('globales.iva_decimal'))));
            $iva = $subtotal * (config('globales.iva_decimal'));
            $costo_neto = $request->costo_neto;
            DB::beginTransaction();
            $id_precio = 0;
            $id_precio = DB::table('precios_propiedades')->insertGetId(
                [
                    'pago_inicial' => (float) $request->pago_inicial,
                    'subtotal' => $subtotal,
                    'impuestos' => $iva,
                    'costo_neto' => $costo_neto,
                    'costo_neto_financiamiento_normal' => $costo_neto,
                    'descuento_pronto_pago_b' => 0,
                    'costo_neto_pronto_pago' => $costo_neto,
                    'tipo_propiedades_id' => (int) ($request->tipo_propiedades_id['value']),
                    'fecha_actualizacion' => now(),
                    'actualizo_id' => (int) $request->user()->id,
                    'financiamiento' => (int) ($request->financiamiento),
                    'contado_b' => (int) ($request->contado_b['value']),
                    'descripcion' => $request->descripcion,
                    'descripcion_ingles' => $request->descripcion,
                ]
            );

            /**todo salio bien y se debe de guardar */
            DB::commit();
            return $id_precio;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**MODIFICAR PRECIO DE PROPIEDAD*/
    public function update_precio_propiedad(Request $request)
    {

        //validaciones directas sin condicionales
        $validaciones = [
            'id_precio_modificar' => 'required',
            'descripcion' => 'required',
            'contado_b.value' => 'required|integer|min:0|max:1',
            'financiamiento' => '',
            'pago_inicial' => '',
            'costo_neto' => 'required|numeric|min:1',
            'tipo_propiedades_id.value' => 'required',
        ];

        $mensaje_financiamiento = '';
        $mensaje_pago_inicial = '';
        /**validando si es a contado o credito */
        if ($request->contado_b['value'] == 1) {
            //es a contado
            $validaciones['financiamiento'] = 'required|integer|max:1';
            $mensaje_financiamiento = ' Este dato debe ser "1" Máximo';

            /**forzando en pago inicial a ser igual al costo neto de la propiedad */
            $validaciones['pago_inicial'] = 'required|numeric|min:' . $request->costo_neto . '|max:' . $request->costo_neto;
            $mensaje_pago_inicial = 'Este valor debe ser "$ 1.00" Mínimo y $ ' . number_format(($request->costo_neto), 2) . " máximo.";
        } else {
            /**es a credito */
            $validaciones['financiamiento'] = 'required|integer|min:2|max:64';
            $mensaje_financiamiento = ' Este dato debe ser "2" Mínimo y "64" Máximo';

            /**se puede mantener el pago inicial por debajo del costo neto */
            $validaciones['pago_inicial'] = 'required|numeric|min:1|max:' . ($request->costo_neto * .7);
            $mensaje_pago_inicial = 'Este valor debe ser "$ 1.00" Mínimo y $ ' . number_format(($request->costo_neto * .7), 2) . " máximo.";
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/

        $mensajes = [
            'pago_inicial.min' => $mensaje_pago_inicial,
            'pago_inicial.max' => $mensaje_pago_inicial,
            'financiamiento.min' => $mensaje_financiamiento,
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'costo_neto.gte' => 'Esta cantidad debe ser mayor o igual al costo neto de contado',
            'costo_neto.min' => 'Esta cantidad debe ser mayor a cero',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        /**validar si el precio existe */
        $precio = PreciosPropiedades::where('tipo_propiedades_id', $request->tipo_propiedades_id['value'])
            ->where('financiamiento', $request->financiamiento)
            ->get()
            ->first();
        if (!empty($precio)) {
            /**verificando que el precio no es el mismo para que entre ala siguiente validacion */
            if ($request->id_precio_modificar != $precio->id) {
                /**ya existe el precio */
                if ($precio->status == 1) {
                    return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento', 409);
                } else {
                    return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento, solo debe habilitarlo nuevamente', 409);
                }
            } else {
                /**checando si el mismo esta desactivado */
                if ($precio->status != 1) {
                    return $this->errorResponse('Este precio se encuentra desactivado y no puede modificarse.', 409);
                }
            }
        }

        try {
            $subtotal = (float) (($request->costo_neto / (1 + config('globales.iva_decimal'))));
            $iva = $subtotal * (config('globales.iva_decimal'));
            $costo_neto = $request->costo_neto;
            DB::beginTransaction();
            $res = DB::table('precios_propiedades')->where('id', $request->id_precio_modificar)->update(
                [
                    'pago_inicial' => (float) $request->pago_inicial,
                    'subtotal' => $subtotal,
                    'impuestos' => $iva,
                    'costo_neto' => $costo_neto,
                    'costo_neto_financiamiento_normal' => $costo_neto,
                    'descuento_pronto_pago_b' => 0,
                    'costo_neto_pronto_pago' => $costo_neto,
                    'tipo_propiedades_id' => (int) ($request->tipo_propiedades_id['value']),
                    'fecha_actualizacion' => now(),
                    'actualizo_id' => (int) $request->user()->id,
                    'financiamiento' => (int) ($request->financiamiento),
                    'contado_b' => (int) ($request->contado_b['value']),
                    'descripcion' => $request->descripcion,
                    'descripcion_ingles' => $request->descripcion,
                ]
            );
            /**todo salio bien y se debe de guardar */
            DB::commit();
            return $res > 0 ? $request->id_precio_modificar : 0;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**ENABLE DISABLE PRECIO DE PROPIEDAD*/
    public function enable_disable(Request $request)
    {

        //validaciones directas sin condicionales
        $validaciones = [
            'id_precio' => 'required',
        ];

        $mensajes = [
            'required' => 'Dese ingresar la clave del precio',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        /**validar si el precio existe */
        $precio = PreciosPropiedades::where('id', $request->id_precio)
            ->get()
            ->first();
        //definiendo status
        $status = 0;
        if (!empty($precio)) {
            $status = !$precio->status;
        } else {
            return $this->errorResponse('No se encontró este precio en la base de datos', 409);
        }

        try {
            DB::beginTransaction();
            $res = DB::table('precios_propiedades')->where('id', $request->id_precio)->update(
                [

                    'status' => $status,
                ]
            );
            /**todo salio bien y se debe de modificar */
            DB::commit();
            return $request->id_precio;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function lista_precios_pdf($idioma = 'es', Request $request)
    {

        if (!($idioma == 'en' || $idioma == 'es')) {
            $idioma = 'es';
        }
        App::setLocale($idioma);

        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        if ($email == true) {
            if (!$request->email_addres || !$request->destinatario) {
                $this->errorResponse('Es necesario un correo y un destinatario', 409);
            }
        }
        $email_to = $request->email_address;
        $datos_request = json_decode($request->request_parent[0], true);
        $financiamientos = $this->get_financiamientos();

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$id_venta = 2;
        $email = false;
        $email_to = 'hector@gmail.com';
        ¨*/
        //obtengo la informacion de esa venta

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('cementerios/planes_venta/reportes', ['empresa' => $empresa, 'financiamientos' => $financiamientos, 'id_tipo_propiedad' => $datos_request['id_tipo_propiedad'], 'idioma' => $idioma]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = __('cementerio/lista_precios.titulo_reporte') . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.planes_venta.footer', ['empresa' => $empresa]),
        ]);

        $pdf->setOptions([
            'header-html' => view('cementerios.planes_venta.header'),
        ]);

        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 12.4);
        $pdf->setOption('margin-bottom', 12.4);
        $pdf->setOption('page-size', 'letter');

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
                $request->destinatario,
                __('cementerio/lista_precios.titulo_reporte'),
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    //retorna los tipos de propiedad
    public function get_propiedades_by_tipo(Request $request)
    {
        //id del conjunto de propieades
        $id_propiedad_tipo = $request->id_propiedad_tipo;
        return
            Propiedades::where('propiedades.tipo_propiedades_id', '=', $id_propiedad_tipo)->get();
    }

    //retorna los datos de columnas_filas para saber en que numero de lote inicia y acaba una fila de una terraza
    public function get_columna_fila_terraza(Request $request)
    {
        //id del conjunto de propieades
        $propiedades_id = $request->propiedades_id;
        $fila = $request->fila;
        return DB::table('columnas_filas')->where('fila', $fila)->where('propiedades_id', $propiedades_id)->get();
    }

    //retorna los tipos de precios y tarifas segun las propiedadad
    public function precios_tarifas()
    {
        return tipoPropiedades::with('precios.tipo')->get();
    }

    /**UPDATE precios de tarifas */
    public function actualizar_precios_tarifas(Request $request)
    {

        //return count($request->all());
        //return ($request->all());
        //creando los valores que necesito validar
        request()->validate(
            [
                '*.precio_neto' => [
                    'required',
                    'numeric',
                ],
                '*.enganche_inicial' => [
                    'required',
                    'numeric',
                    'lte:*.precio_neto',
                ],
                '*.meses' => [
                    'required',
                    'integer',
                    'digits_between:1,2',
                ],
            ],
            [
                '*.precio_neto.required' => 'ingrese este dato.',
                '*.precio_neto.numeric' => 'Ingrese una cantidad correcta.',
                '*.enganche_inicial.lte' => 'El pago inicial debe ser menor o igual al precio neto de la propiedad.',
                '*.enganche_inicial.required' => 'ingrese este dato.',
                '*.meses.numeric' => 'ingrese un número de meses correcto.',
                '*.meses.required' => 'ingrese este dato.',
                '*.meses.digits_between' => 'ingrese este dato (2 dígitos máximo).',
            ]
        );

        //actualizo los nuevos datos de tarifas

        try {
            DB::beginTransaction();
            //elimino todos los datos de esas tarifas
            DB::table('precios_propiedades')->where('tipo_propiedades_id', '=', $request[0]['tipo_propiedades_id'])->delete();

            for ($i = 0; $i < count($request->all()); $i++) {
                DB::table('precios_propiedades')->insert(
                    [
                        'precio_neto' => $request[$i]['precio_neto'],
                        'meses' => $request[$i]['meses'],
                        'enganche_inicial' => $request[$i]['enganche_inicial'],
                        'tipo_precios_id' => $request[$i]['tipo_precios_id'],
                        'tipo_propiedades_id' => $request[0]['tipo_propiedades_id'],
                        'fecha_hora' => now(),
                        'actualizo_id' => $request->user()->id,
                    ]
                );
            }

            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            DB::rollBack();
            return 0;
        }
    }

    public function get_usuarios_para_vendedores()
    {
        return (User::select(
            'usuarios.id as id_user',
            'nombre',
            'email',
            'genero',
            'imagen',
            'telefono',
            'fecha_alta',
            'roles_id',
            'usuarios.status as estado',
            'rol',
            DB::raw('(CASE
                        WHEN usuarios.genero = "1" THEN "Hombre"
                        ELSE "Mujer"
                        END) AS genero_des')
        )
            ->join('roles', 'roles.id', '=', 'usuarios.roles_id')
            //->where('roles_id', ">", 1)
            ->where('usuarios.roles_id', '>', '1') //no muestro super usuarios
            ->get());
    }

    //retorna que tipo de venta es de la propiedad, si es de uso inmediato o a futuro
    public function get_ventas_referencias_propiedades()
    {
        return DB::table('ventas_referencias')->where('id', '<', 3)->get();
    }

    //obtiene los tipos de antiguedad de una venta, son 3 registros predefinidos
    public function get_antiguedades_venta()
    {
        //id del conjunto de propieades
        return AntiguedadesVenta::get();
    }

    /**obtiene todas las ventas para el paginado de ventas de cementerio */
    public function get_ventas(Request $request, $id_venta = 'all', $paginated = false)
    {
        $light = $request->light;
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $titular = $request->titular;
        $fallecido = $request->fallecido;
        $numero_control = $request->numero_control;
        $status = $request->status;
        $fecha_operacion = $request->fecha_operacion;
        $resultado_query = Operaciones::with('sepultados')->with("venta_terreno");
        if (!isset($light)) {
            $resultado_query = $resultado_query->with('pagosProgramados.pagados')
                ->with('cuota_cementerio_terreno.pagosProgramados.pagados')
                ->with('cuota_cementerio_terreno.cuota_cementerio:id,descripcion,status')
                ->with('venta_terreno.vendedor')
                ->with('venta_terreno.entrego_convenio')
                ->with('venta_terreno.tipo_propiedad')
                ->with('beneficiarios')
                ->with('AjustesPoliticas')
                ->with('cancelador:id,nombre')
                ->with('registro:id,nombre');
        }
        $resultado_query = $resultado_query->where('empresa_operaciones_id', '=', 1)
            /**solo ventas de cementerio */
            ->select(
                /**venta operacion */
                'operaciones.id',
                'operaciones.id as operacion_id',
                'antiguedad_operacion_id',
                'empresa_operaciones_id',
                'subtotal',
                'tasa_iva',
                'descuento',
                'impuestos',
                'total',
                'saldo',
                'descuento_pronto_pago_b',
                'costo_neto_pronto_pago',
                DB::raw(
                    '(NULL) AS costo_neto_calculado'
                ),
                DB::raw(
                    '(NULL) AS descuento_neto_calculado'
                ),
                'numero_solicitud',
                'numero_convenio',
                'numero_titulo',
                'titular_sustituto',
                'parentesco_titular_sustituto',
                'telefono_titular_sustituto',
                'ventas_terrenos_id',
                'financiamiento',
                'aplica_devolucion_b',
                'costo_neto_financiamiento_normal',
                'comision_venta_neto',
                'operaciones.status as operacion_status',
                'clientes.id as cliente_id',
                'clientes.nombre',
                'clientes.direccion',
                'clientes.ciudad',
                'clientes.estado',
                'clientes.telefono',
                'clientes.celular',
                'clientes.telefono_extra',
                'clientes.rfc',
                'clientes.email',
                'clientes.fecha_nac',
                DB::raw(
                    'DATE(operaciones.fecha_operacion) as fecha_operacion'
                ),
                DB::raw(
                    'DATE(operaciones.fecha_cancelacion) as fecha_cancelacion_operacion'
                ),
                DB::raw(
                    '(NULL) as fecha_operacion_texto'
                ),
                'operaciones.nota',
                /**fin de datos de  operacion */
                DB::raw(
                    '(0) AS tipo_financimiento_texto'
                ),
                DB::raw(
                    '(0) AS num_pagos_programados'
                ),
                DB::raw(
                    '(0) AS num_pagos_programados_vigentes'
                ),
                DB::raw(
                    '(0) AS intereses'
                ),
                DB::raw(
                    '(0) AS total_cubierto'
                ),
                DB::raw(
                    '(0) AS abonado_capital'
                ),
                DB::raw(
                    '(0) AS abonado_intereses'
                ),
                DB::raw(
                    '(0) AS descontado_pronto_pago'
                ),
                DB::raw(
                    '(0) AS descontado_capital'
                ),
                DB::raw(
                    '(0) AS complementado_cancelacion'
                ),
                DB::raw(
                    '(0) AS saldo_neto'
                ),
                DB::raw(
                    '(0) AS pagos_vencidos'
                ),
                DB::raw(
                    '(0) AS dias_vencidos'
                ),
                DB::raw(
                    '(0) AS pagos_programados_cubiertos'
                ),
                DB::raw(
                    '(0) AS pagos_realizados'
                ),
                DB::raw(
                    '(0) AS pagos_vigentes'
                ),
                DB::raw(
                    '(0) AS pagos_cancelados'
                ),
                DB::raw(
                    '(CASE
                        WHEN operaciones.numero_solicitud <> "" THEN operaciones.numero_solicitud
                        ELSE "N/A"
                        END) AS numero_solicitud_texto'
                ),
                DB::raw(
                    '(CASE
                        WHEN operaciones.numero_titulo <> "" THEN operaciones.numero_titulo
                        ELSE "Pendiente"
                        END) AS numero_titulo_texto'
                ),
                DB::raw(
                    '(NULL) AS status_texto'
                ),
                /*DB::raw(
            '(NULL) AS pagos_realizados_arreglo'
            ),*/
                'operaciones.registro_id',
                'operaciones.cancelo_id',
                'operaciones.modifico_id',
                'operaciones.nota_cancelacion',
                'operaciones.motivos_cancelacion_id',
                'operaciones.cantidad_a_regresar_cancelacion',
                DB::raw(
                    '(NULL) AS motivos_cancelacion_texto'
                )
            )
            ->where(function ($q) use ($id_venta) {
                if (trim($id_venta) == 'all' || $id_venta > 0) {
                    if (trim($id_venta) == 'all') {
                        $q->where('operaciones.ventas_terrenos_id', '>', $id_venta);
                    } else if ($id_venta > 0) {
                        $q->where('operaciones.ventas_terrenos_id', '=', $id_venta);
                    }
                }
            })
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_solicitud', '=', $numero_control);
                    } else if ($filtro_especifico_opcion == 2) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_convenio', '=', $numero_control);
                    } else if ($filtro_especifico_opcion == 3) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_titulo', '=', $numero_control);
                    } else {
                        /**filtro por numero de solicitud */
                        // $q->where('ventas_terrenos.id', $numero_control);
                    }
                }
            })

            //aqui filtro solo cuando es busqueda por ubicacion en raw
            ->WhereHas(isset($request->ubicacion_raw) ? 'venta_terreno' : 'registro', function ($q) use ($request) {
                if (isset($request->ubicacion_raw)) {
                    $q->where('ubicacion', $request->ubicacion_raw);
                    $q->where('operaciones.status', '<>', 0);
                }
            });
        if (isset($request->fallecido)) {
            $resultado_query = $resultado_query->WhereHas('sepultados', function ($q) use ($request) {
                $q->where('nombre_afectado', 'like', '%' . trim($request->fallecido) . '%');
            });
        }

        /*->*/

        //fin de filtrado por ubicacion
        $resultado_query = $resultado_query->where(function ($q) use ($status) {
            if (trim($status) != "") {
                if ($status == 1) {
                    //solo listo los servicios con adeudo
                    $q->where('saldo', '>', 0)->where("operaciones.status", "=", 1);
                } elseif ($status == 2) {
                    //solo las pagadas
                    $q->where('operaciones.status', "=", 2)->where('saldo', '<=', 0);
                } elseif ($status == 3) {
                    //solo las activas
                    $q->where('operaciones.status', ">", 0);
                } elseif ($status == 0) {
                    $q->where('operaciones.status', 0);
                }
            }
        })
            ->join('clientes', 'clientes.id', '=', 'operaciones.clientes_id')
            ->where('nombre', 'like', '%' . $titular . '%')
            ->orderBy('operaciones.ventas_terrenos_id', 'desc')
            ->get();
        /**verificando si el usario necesita el resultado paginado, todo o por id */
        $resultado = array();
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado = &$resultado_query;
        }
        /**datos del cmeenterio para actualizar los valores de la ubicacion */
        $datos_cementerio = $this->get_cementerio();

        foreach ($resultado as $index_venta => &$venta) {

            foreach ($venta['sepultados'] as $index_sepultado => &$sepultado) {
                /**asigno los datos de los finados dentro de la propiedad */
                $sepultado['fecha_defuncion_texto'] = fecha_abr($sepultado['fechahora_defuncion']);
                $sepultado['fecha_inhumacion_texto'] = fecha_abr($sepultado['fechahora_inhumacion']);
            }

            /**calculando el costo neto y descuento calcuado */
            /**aqui voy*/
            $tasa_iva_decimal = $venta['tasa_iva'] / 100;

            $venta['costo_neto_calculado'] = round($venta['total'] + ($venta['descuento'] * (1 + $tasa_iva_decimal)), 2, PHP_ROUND_HALF_UP);
            $venta['descuento_neto_calculado'] = round(($venta['descuento'] * (1 + $tasa_iva_decimal)), 2, PHP_ROUND_HALF_UP);

            /**DEFINIENDO EL STATUS DE LA VENTA*/
            if ($venta['operacion_status'] == 0) {
                $venta['status_texto'] = 'Cancelada';
                if ($venta['motivos_cancelacion_id'] == 1) {
                    /**fue por fal de pago */
                    $venta['motivos_cancelacion_texto'] = 'falta de pago';
                } elseif ($venta['motivos_cancelacion_id'] == 2) {
                    /**fue por peticion de lciente */
                    $venta['motivos_cancelacion_texto'] = 'a petición del cliente';
                } elseif ($venta['motivos_cancelacion_id'] == 3) {
                    /**fue por error de captura */
                    $venta['motivos_cancelacion_texto'] = 'error de captura';
                }
                /**actualizando el motivo de cancelacion */
            } elseif ($venta['operacion_status'] == 1) {
                $venta['status_texto'] = 'Por Pagar';
            } elseif ($venta['operacion_status'] == 2) {
                $venta['status_texto'] = 'Pagada';
            }

            $venta['fecha_operacion_texto'] = fecha_abr($venta['fecha_operacion']);

            /**aqui se saca el porcentaje para ver cuanto seria el descuento por pago en cada pronto pago */
            $porcentaje_descuento_pronto_pago = 0;
            if ($venta['total'] > 0) {
                $porcentaje_descuento_pronto_pago = ($venta['costo_neto_pronto_pago'] * 100) / ($venta['total']);
            }

            /**tipo de financiamiento texto */
            if ($venta['financiamiento'] == 1) {
                $venta['tipo_financimiento_texto'] = 'Uso inmediato/Pago único';
            } else {
                $venta['tipo_financimiento_texto'] = 'A futuro/'
                    . $venta['financiamiento'] . ' Mes(s)';
            }

            if (!isset($light)) {
                $venta['num_pagos_programados'] = count($venta['pagos_programados']);
                $num_pagos_programados_vigentes = 0;
                if ($venta['num_pagos_programados'] > 0) {
                    /**si tiene pagos programados, eso quiere decir que la venta no tuvo 100 de descuento */
                    /**recorriendo arreglo de pagos programados */
                    $vencidos = 0;
                    $pagos_programados_cubiertos = 0;
                    $dias_vencido_primer_pago_vencido = '';
                    $pagos_vigentes = 0;
                    $pagos_cancelados = 0;
                    $pagos_realizados = 0;

                    $arreglo_de_pagos_realizados = [];
                    /**guardo los dias que lleva vencido el pago vencido mas antiguo */
                    foreach ($venta['pagos_programados'] as $index_programado => &$programado) {
                        /**actualizando el concepto del pago */
                        if ($programado['conceptos_pagos_id'] == 1) {
                            $programado['concepto_texto'] = 'Enganche';
                        } elseif ($programado['conceptos_pagos_id'] == 2) {
                            $programado['concepto_texto'] = 'Abono';
                        } else {
                            $programado['concepto_texto'] = 'Pago Único';
                        }

                        /**actualizando fecha de pago abre con helper de fechas */
                        $programado['fecha_programada_abr'] = fecha_abr($programado['fecha_programada']);

                        //if ($programado['status'] == 1) {
                        if ($programado['status'] == 1) {
                            $num_pagos_programados_vigentes++;
                        }
                        /**aumento el pago programado vigente */
                        /**haciendo sumatoria de los montos que se han destinado a un pago programado segun el tipo de movimiento */
                        /**montos segun su tipo de movimiento */
                        $abonado_intereses = 0;
                        $abonado_capital = 0;
                        $descontado_pronto_pago = 0;
                        $descontado_capital = 0;
                        $complemento_cancelacion = 0;
                        $total_cubierto = 0;
                        $fecha_ultimo_pago = '';

                        foreach ($programado['pagados'] as $index_pagados => &$pagado) {
                            /**haciendo el arreglo de pagos realizados limpio(no repetidos) */
                            array_push(
                                $arreglo_de_pagos_realizados,
                                $pagado
                            );

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
                                    // $pago_total += $pagado['monto'];
                                    $abonado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 4) {
                                    /**fue descuento al capital */
                                    $descontado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 5) {
                                    /**fue complemento por cancelacion */
                                    $complemento_cancelacion += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 2) {
                                    /**es tipo interes */
                                    if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                        /**es abono de intereses */
                                        $abonado_intereses += $pagado['pagos_cubiertos']['monto'];
                                        //$pago_total += $pagado['monto'];
                                    }
                                } else if ($pagado['movimientos_pagos_id'] == 3) {
                                    if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                        /**es descuento por pronto pago */
                                        $descontado_pronto_pago += $pagado['pagos_cubiertos']['monto'];
                                        //$pago_total += $pagado['monto'];
                                    }
                                }

                                /**fecha en que se realizo el ultimo pago */
                                $fecha_ultimo_pago = $pagado['fecha_pago'];
                                // }
                                $pagos_vigentes++;
                            } //fin if pago status=1
                            else {
                                if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                    $pagos_cancelados++;
                                }
                            }
                            if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                $pagos_realizados++;
                            }
                        } //fin foreach pagado

                        /** al final del ciclo se actualizan los valores en el pago programado*/
                        $programado['abonado_capital'] = round($abonado_capital, 2, PHP_ROUND_HALF_UP);
                        $programado['abonado_intereses'] = $abonado_intereses;
                        $programado['descontado_pronto_pago'] = $descontado_pronto_pago;
                        $programado['descontado_capital'] = $descontado_capital;
                        $programado['complementado_cancelacion'] = round($complemento_cancelacion, 2, PHP_ROUND_HALF_UP);

                        $saldo_pago_programado = $programado['monto_programado'] - $abonado_capital - $descontado_pronto_pago - $descontado_capital - $complemento_cancelacion;

                        $programado['saldo_neto'] = round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP);
                        /**asignando la fecha del pago que liquidado el pago programado */
                        if ($programado['saldo_neto'] <= 0) {
                            $programado['fecha_ultimo_pago'] = $fecha_ultimo_pago;
                            $programado['fecha_ultimo_pago_abr'] = fecha_abr($fecha_ultimo_pago);
                        }
                        /**verificando el estado del pago programado*/
                        /**verificando si la fecha sigue vigente o esta vencida */
                        /**variables para controlar el incremento por intereses */
                        $dias_retrasados_del_pago = 0;
                        $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);

                        /**aqui verifico que si la operacion esta activa genere los intereses acorde al dia de hoy, si esta cancelada que tomen intereses a partir de la fecha de cancelacion */
                        $fecha_para_intereses = date('Y-m-d');
                        if ($venta['operacion_status'] == 0) {
                            if (trim($venta['fecha_cancelacion_operacion']) != '') {
                                $fecha_para_intereses = $venta['fecha_cancelacion_operacion'];
                            }
                        }
                        $fecha_hoy = Carbon::createFromFormat('Y-m-d', $fecha_para_intereses);
                        $interes_generado = 0;
                        $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_programada']);
                        /**fin varables por intereses */
                        /**verificando que el pago programado tiene un saldo de capital que cobrar para saber si aplica o no intereses */
                        if (round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP) > 0) {
                            /**tiene todavia saldo que pagar, se debe verificar si el pago esta vencido para generarle los intereses correspondientes */
                            if (date('Y-m-d', strtotime($programado['fecha_programada'])) < date('Y-m-d')) {
                                /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */

                                $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                                if ($dias_vencido_primer_pago_vencido == '') {
                                    $dias_vencido_primer_pago_vencido = $dias_retrasados_del_pago;
                                }
                                $programado['fecha_a_pagar'] = date('Y-m-d');
                                $programado['fecha_a_pagar_abr'] = fecha_abr(date('Y-m-d'));
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
                                    $interes_generado = round(((($programado['monto_programado'] * ($venta['ajustes_politicas']['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago), 0, PHP_ROUND_HALF_UP);
                                    if ($interes_generado > 0) {
                                        /**esto siginifica que la fecha de pago seria mayor o igual a la fecha en que se hizo el ultimo abono a intereses */
                                        $interes_generado -= $programado['abonado_intereses'];
                                    }
                                }
                                /**aqui actualizamos el saldo neto del pago con todo e intereses, quitando los intereses que ya se han pagado previamente */
                                $programado['saldo_neto'] = round($saldo_pago_programado + $interes_generado, 2, PHP_ROUND_HALF_UP);
                                /**la fecha qui es mayor que la fecha programada del pago */
                                $programado['status_pago'] = 0;
                                $programado['status_pago_texto'] = 'Vencido';
                                $vencidos++;
                                $programado['dias_vencido'] = $dias_retrasados_del_pago;
                                $programado['intereses'] = $interes_generado;
                            } else {
                                /**la fecha aun no vence */
                                $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                                $programado['status_pago'] = 1;
                                $programado['status_pago_texto'] = 'Pendiente';
                            }
                        } else {
                            $pagos_programados_cubiertos++;
                            $programado['fecha_a_pagar'] = $pagado['fecha_pago'];
                            /**el pago programado ya fue cubierto */
                            $programado['status_pago'] = 2;
                            $programado['status_pago_texto'] = 'Pagado';
                        }
                        /**monto con pronto pago de cada abono */
                        $programado['monto_pronto_pago'] = round(($porcentaje_descuento_pronto_pago * $programado['monto_programado']) / 100, 0, PHP_ROUND_HALF_UP);
                        $programado['total_cubierto'] = $abonado_capital + $descontado_pronto_pago + $descontado_capital + $complemento_cancelacion;
                        /**actualizando los totales de montos en la venta */
                        $venta['intereses'] += $interes_generado;
                        $venta['abonado_capital'] += $abonado_capital;
                        $venta['abonado_intereses'] += $abonado_intereses;
                        $venta['descontado_pronto_pago'] += $descontado_pronto_pago;
                        $venta['descontado_capital'] += $descontado_capital;
                        $venta['complementado_cancelacion'] += $complemento_cancelacion;
                        $venta['saldo_neto'] += $saldo_pago_programado + $interes_generado;
                        /**calculando el total cubierto de la venta, sin intereses pagados, solo lo que ya esta cubierto */
                        $venta['total_cubierto'] += $programado['total_cubierto'];
                        /**verificado el monto que seria con pronnto pago  */
                        //} //fin foreach if status 1 programado
                    } //fin foreach programados
                    $venta['pagos_realizados'] = $pagos_realizados;
                    $venta['pagos_vigentes'] = $pagos_vigentes;
                    $venta['num_pagos_programados_vigentes'] = $num_pagos_programados_vigentes;
                    $venta['pagos_cancelados'] = $pagos_cancelados;
                    $venta['pagos_programados_cubiertos'] = $pagos_programados_cubiertos;
                    $venta['pagos_vencidos'] = $vencidos;
                    $venta['dias_vencidos'] = $dias_vencido_primer_pago_vencido;
                    /**areegloe de todos los pagos limpios(no repetidos) */
                    //$venta['pagos_realizados_arreglo'] = $arreglo_de_pagos_realizados;
                } else {
                    /**la venta no tiene pagos programados debido a que fue 100% "GRATIS" */
                }

                /**incio pagos de cuotas */
                if (count($venta['cuota_cementerio_terreno']) > 0) {
                    foreach ($venta['cuota_cementerio_terreno'] as $key => &$cuota) {

                        # code...

                        /**si tiene pagos programados, eso quiere decir que la venta no tuvo 100 de descuento */
                        /**recorriendo arreglo de pagos programados */
                        $vencidos = 0;
                        $pagos_programados_cubiertos = 0;
                        $dias_vencido_primer_pago_vencido = '';
                        $pagos_vigentes = 0;
                        $pagos_cancelados = 0;
                        $pagos_realizados = 0;
                        $arreglo_de_pagos_realizados = [];
                        /**guardo los dias que lleva vencido el pago vencido mas antiguo */
                        foreach ($cuota['pagos_programados'] as $index_programado => &$programado) {

                            $status_cuota = $cuota['cuota_cementerio']['status'];
                            $programado['status'] = $status_cuota == 0 ? $status_cuota : $programado['status'];
                            /**actualizando el concepto del pago */
                            if ($programado['conceptos_pagos_id'] == 1) {
                                $programado['concepto_texto'] = 'Enganche';
                            } elseif ($programado['conceptos_pagos_id'] == 2) {
                                $programado['concepto_texto'] = 'Abono';
                            } else {
                                $programado['concepto_texto'] = 'Cuota ' . $cuota['cuota_cementerio']['descripcion'];
                            }

                            /**actualizando fecha de pago abre con helper de fechas */
                            $programado['fecha_programada_abr'] = fecha_abr($programado['fecha_programada']);

                            //if ($programado['status'] == 1) {
                            if ($programado['status'] == 1) {
                                $num_pagos_programados_vigentes++;
                            }

                            /**aumento el pago programado vigente */
                            /**haciendo sumatoria de los montos que se han destinado a un pago programado segun el tipo de movimiento */
                            /**montos segun su tipo de movimiento */
                            $abonado_intereses = 0;
                            $abonado_capital = 0;
                            $descontado_pronto_pago = 0;
                            $descontado_capital = 0;
                            $complemento_cancelacion = 0;
                            $total_cubierto = 0;
                            $fecha_ultimo_pago = '';

                            foreach ($programado['pagados'] as $index_pagados => &$pagado) {

                                /**haciendo el arreglo de pagos realizados limpio(no repetidos) */
                                array_push(
                                    $arreglo_de_pagos_realizados,
                                    $pagado
                                );

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
                                        // $pago_total += $pagado['monto'];
                                        $abonado_capital += $pagado['pagos_cubiertos']['monto'];
                                    } else if ($pagado['movimientos_pagos_id'] == 4) {
                                        /**fue descuento al capital */
                                        $descontado_capital += $pagado['pagos_cubiertos']['monto'];
                                    } else if ($pagado['movimientos_pagos_id'] == 5) {
                                        /**fue complemento por cancelacion */
                                        $complemento_cancelacion += $pagado['pagos_cubiertos']['monto'];
                                    } else if ($pagado['movimientos_pagos_id'] == 2) {
                                        /**es tipo interes */
                                        if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                            /**es abono de intereses */
                                            $abonado_intereses += $pagado['pagos_cubiertos']['monto'];
                                            //$pago_total += $pagado['monto'];
                                        }
                                    } else if ($pagado['movimientos_pagos_id'] == 3) {
                                        if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                            /**es descuento por pronto pago */
                                            $descontado_pronto_pago += $pagado['pagos_cubiertos']['monto'];
                                            //$pago_total += $pagado['monto'];
                                        }
                                    }

                                    /**fecha en que se realizo el ultimo pago */
                                    $fecha_ultimo_pago = $pagado['fecha_pago'];
                                    // }
                                    $pagos_vigentes++;
                                } //fin if pago status=1
                                else {
                                    if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                        $pagos_cancelados++;
                                    }
                                }
                                if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                    $pagos_realizados++;
                                }
                            } //fin foreach pagado

                            /** al final del ciclo se actualizan los valores en el pago programado*/
                            $programado['abonado_capital'] = round($abonado_capital, 2, PHP_ROUND_HALF_UP);
                            $programado['abonado_intereses'] = $abonado_intereses;
                            $programado['descontado_pronto_pago'] = $descontado_pronto_pago;
                            $programado['descontado_capital'] = $descontado_capital;
                            $programado['complementado_cancelacion'] = round($complemento_cancelacion, 2, PHP_ROUND_HALF_UP);

                            $saldo_pago_programado = $programado['monto_programado'] - $abonado_capital - $descontado_pronto_pago - $descontado_capital - $complemento_cancelacion;

                            $programado['saldo_neto'] = round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP);
                            /**asignando la fecha del pago que liquidado el pago programado */
                            if ($programado['saldo_neto'] <= 0) {
                                $programado['fecha_ultimo_pago'] = $fecha_ultimo_pago;
                                $programado['fecha_ultimo_pago_abr'] = fecha_abr($fecha_ultimo_pago);
                            }
                            /**verificando el estado del pago programado*/
                            /**verificando si la fecha sigue vigente o esta vencida */
                            /**variables para controlar el incremento por intereses */
                            $dias_retrasados_del_pago = 0;
                            $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);

                            /**aqui verifico que si la operacion esta activa genere los intereses acorde al dia de hoy, si esta cancelada que tomen intereses a partir de la fecha de cancelacion */
                            $fecha_para_intereses = date('Y-m-d');
                            if ($venta['operacion_status'] == 0) {
                                if (trim($venta['fecha_cancelacion_operacion']) != '') {
                                    $fecha_para_intereses = $venta['fecha_cancelacion_operacion'];
                                }
                            }

                            $fecha_hoy = Carbon::createFromFormat('Y-m-d', $fecha_para_intereses);

                            $interes_generado = 0;
                            $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_programada']);
                            /**fin varables por intereses */
                            /**verificando que el pago programado tiene un saldo de capital que cobrar para saber si aplica o no intereses */
                            if (round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP) > 0) {
                                /**tiene todavia saldo que pagar, se debe verificar si el pago esta vencido para generarle los intereses correspondientes */
                                if (date('Y-m-d', strtotime($programado['fecha_programada'])) < date('Y-m-d')) {
                                    /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */

                                    $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                                    if ($dias_vencido_primer_pago_vencido == '') {
                                        $dias_vencido_primer_pago_vencido = $dias_retrasados_del_pago;
                                    }
                                    $programado['fecha_a_pagar'] = date('Y-m-d');
                                    $programado['fecha_a_pagar_abr'] = fecha_abr(date('Y-m-d'));
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
                                        $interes_generado = round(((($programado['monto_programado'] * ($venta['ajustes_politicas']['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago), 0, PHP_ROUND_HALF_UP);
                                        if ($interes_generado > 0) {
                                            /**esto siginifica que la fecha de pago seria mayor o igual a la fecha en que se hizo el ultimo abono a intereses */
                                            $interes_generado -= $programado['abonado_intereses'];
                                        }
                                    }

                                    /**aqui actualizamos el saldo neto del pago con todo e intereses, quitando los intereses que ya se han pagado previamente */
                                    $programado['saldo_neto'] = round($saldo_pago_programado + $interes_generado, 2, PHP_ROUND_HALF_UP);
                                    $cuota["saldo"] = $programado['saldo_neto'];
                                    /**la fecha qui es mayor que la fecha programada del pago */
                                    $programado['status_pago'] = 0;
                                    $programado['status_pago_texto'] = 'Vencido';
                                    $vencidos++;
                                    $programado['dias_vencido'] = $dias_retrasados_del_pago;
                                    $programado['intereses'] = $interes_generado;
                                } else {
                                    /**la fecha aun no vence */
                                    $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                                    $programado['status_pago'] = 1;
                                    $programado['status_pago_texto'] = 'Pendiente';
                                }
                            } else {
                                $pagos_programados_cubiertos++;
                                $programado['fecha_a_pagar'] = $pagado['fecha_pago'];
                                /**el pago programado ya fue cubierto */
                                $programado['status_pago'] = 2;
                                $programado['status_pago_texto'] = 'Pagado';
                            }

                            /**monto con pronto pago de cada abono */
                            $programado['monto_pronto_pago'] = round(($porcentaje_descuento_pronto_pago * $programado['monto_programado']) / 100, 0, PHP_ROUND_HALF_UP);
                            $programado['total_cubierto'] = $abonado_capital + $descontado_pronto_pago + $descontado_capital + $complemento_cancelacion;

                            /**actualizando los totales de montos en la venta */

                            /**calculando el total cubierto de la venta, sin intereses pagados, solo lo que ya esta cubierto */
                            $venta['total_cubierto'] += $programado['total_cubierto'];
                            /**verificado el monto que seria con pronnto pago  */
                            //} //fin foreach if status 1 programado
                        } //fin foreach programados
                        /** */
                    }
                    /**areegloe de todos los pagos limpios(no repetidos) */
                    //$venta['pagos_realizados_arreglo'] = $arreglo_de_pagos_realizados;
                }
                /**fin pagos de cuotas */
            }

            /**verificando el tipo de venta segun financiamiento*/
            if ($venta['venta_terreno']['tipo_financiamiento'] == 1) {
                $venta['venta_terreno']['tipo_financiamiento_texto'] = 'Uso Inmediato';
            } else {
                $venta['venta_terreno']['tipo_financiamiento_texto'] = 'A Futuro';
            }
            /**actualiznado ubicacion */
            $venta['venta_terreno']['ubicacion_texto'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['ubicacion_texto'];
            $venta['venta_terreno']['area_nombre'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['area_nombre'];
            $venta['venta_terreno']['tipo_texto'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['tipo_texto'];
            $venta['venta_terreno']['fila_texto'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['fila_texto'];
            $venta['venta_terreno']['lote_texto'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['lote_texto'];
            $venta['venta_terreno']['fecha_convenio_entrega_texto'] = $venta['venta_terreno']['fecha_registro_convenio'] != null ? fecha_abr($venta['venta_terreno']['fecha_registro_convenio']) : null;
            /**agregando fila, lote, y tipo, por separado en valor numrico */
            $venta['venta_terreno']['fila_raw'] = (intval(explode("-", $venta['venta_terreno']['ubicacion'])[2]));
            $venta['venta_terreno']['lote_raw'] = (intval(explode("-", $venta['venta_terreno']['ubicacion'])[3]));
        } //fin foreach venta

        return $resultado_query;
        /**aqui se puede hacer todo los calculos para llenar la informacion calculada del servicio get_ventas */
    }

    public function ubicacion_texto($dato = '', $datos_cementerio = [])
    {
        /**se hace un arreglo para regresar la ubicacion completa y por separado (fila, pripieda, lote tipo) */

        /**para decidir el nombre del area, en caso de ser teraza, seria 1,2,3,4,5 en tipo
         * de unplex seria a,b,c
         */
        $areas_nombres = [
            /**uniplex */
            1 => 'a',
            2 => 'b',
            3 => 'd',
            4 => 'e',
            5 => 'm',
            6 => 'n',
            7 => 'ñ',
            8 => 'o',
            /**duplex */
            9 => 'c',
            10 => 'f',
            11 => 'g',
            12 => 'h',
            13 => 'i',
            14 => 'j',
            15 => 'k',
            16 => 'l',
            /**nichos */
            17 => '1',
            18 => '2',
            19 => '3',
            20 => '4',
            21 => '5',
            22 => '6',
            23 => '7',
            24 => '8',
            25 => '9',
            26 => '10',
            27 => '11',
            28 => '12',
            /**terrazas */
            29 => '1',
            30 => '2',
            31 => '3',
            32 => '4',
            33 => '5',
            34 => '6',
            35 => '7',
            36 => '8',
            37 => '9',
            38 => '10',
            39 => '11',
            40 => '12',
            41 => '13',
            42 => '14',
            43 => '15',
            44 => '16',
            45 => '17',
            46 => '18',
            51 => '19',
            52 => '20',
            53 => '21',
            54 => '22',
            55 => '23',
            56 => '24',
            57 => '25',
            58 => '26',
            59 => '27',
            60 => '28',
            61 => '29',
            62 => '30',
            63 => '31',
            64 => '32',
            65 => '33',
            66 => '34',
            67 => '35',
            68 => '36',
            69 => '37',
            70 => '38',
            71 => '39',
            72 => '40',
            73 => '41',
            74 => '42',
            75 => '43',
            /**duplex */
            47 => 's',
            /**triplex */
            48 => 'p',
            49 => 'r',
            /**cuadriplex sin terraza */
            50 => 'q',
            /**cuadriplex de mausoleo */
            76 => 'a',
            77 => 'b',
            78 => 'c',
        ];

        //checo si los datos del cemeterio vienen vacios para llenar el arreglo
        if (count($datos_cementerio) == 0) {
            /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
            $datos_cementerio = $this->get_cementerio();
            /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
        }

        /**se obtienen los parametros de la ubicacion */
        $id_tipo = explode("-", $dato)[0];
        $id_propiedad = explode("-", $dato)[1];
        $fila = explode("-", $dato)[2];
        $lote = explode("-", $dato)[3];

        /**se necesita crear un arregle con el abecedario para cuadrar las propiedades segun su fila "en alfabeto" */
        $alfabeto = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'ñ', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        /**se procede a crear una ubicacion entendible para el usuario */

        $ubicacion_texto = '';
        foreach ($datos_cementerio as $propiedad) {
            //recorriendo propiedades
            if ($propiedad['id'] == $id_propiedad) {
                //una vez encontrada el id defino si es terraza que es
                if ($propiedad['tipo_propiedades_id'] == 1) {
                    //uniplex
                    $ubicacion_texto .= "Uniplex " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                    $datos['tipo_texto'] = "uniplex";
                    $datos['fila_texto'] = " Módulo " . $fila;
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['lote_texto'] = "n/a";
                } else if ($propiedad['tipo_propiedades_id'] == 2) {
                    //duplex
                    $ubicacion_texto .= "Duplex " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                    $datos['tipo_texto'] = "duplex";
                    $datos['fila_texto'] = " Módulo " . $fila;
                    $datos['lote_texto'] = "n/a";
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                } else if ($propiedad['tipo_propiedades_id'] == 3) {
                    //nicho
                    $ubicacion_texto .= "Nichos - Columna " . $propiedad['propiedad_indicador'] . ", Fila " . $fila;
                    $datos['tipo_texto'] = "nicho";
                    $datos['fila_texto'] = $fila;
                    $datos['area_nombre'] = "Columna " . $areas_nombres[($id_propiedad)];
                    $datos['lote_texto'] = "n/a";
                } else if ($propiedad['tipo_propiedades_id'] == 4) {
                    $datos['lote_texto'] = $lote;
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['tipo_texto'] = "terraza";
                    $datos['fila_texto'] = strtoupper($alfabeto[$fila - 1]);
                    //cuadriplex
                    $ubicacion_texto .= "Terraza " . $propiedad['propiedad_indicador'] . ", Fila " . strtoupper($alfabeto[$fila - 1]) . " Lote " . $lote;
                } else if ($propiedad['tipo_propiedades_id'] == 5) {
                    $datos['lote_texto'] = "n/a";
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['tipo_texto'] = "triplex";
                    $datos['fila_texto'] = " Módulo " . $fila;
                    //triplex
                    $ubicacion_texto .= "Triplex " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                } else if ($propiedad['tipo_propiedades_id'] == 6) {
                    $datos['lote_texto'] = $lote;
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['tipo_texto'] = "cuadriplex";
                    $datos['fila_texto'] = " Módulo " . $fila;
                    //cuadriplex sin terraza
                    $ubicacion_texto .= "cuadriplex " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                } else if ($propiedad['tipo_propiedades_id'] == 7) {
                    $datos['lote_texto'] = $lote;
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['tipo_texto'] = "Mausoleo";
                    $datos['fila_texto'] = " Módulo " . $fila;
                    //cuadriplex de mausoleo
                    $ubicacion_texto .= "Mausoleo " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                }
            }
        }
        $datos['ubicacion_texto'] = $ubicacion_texto;

        return $datos;
    }

    public function acuse_cancelacion(Request $request)
    {
        try {
            /*
            $id_venta = 8;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email = $request->email_send === 'true' ? true : false;
            $email_to = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta = $requestVentasList['venta_id'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */

            //obtengo la informacion de esa venta
            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];

            if (empty($datos_venta)) {
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa = $get_funeraria->get_empresa_data();

            $FirmasController = new FirmasController();
            $firma_cliente = $FirmasController->get_firma_documento($datos_venta['operacion_id'], 7, 'por_area_firma');
            $firma_gerente = $FirmasController->get_firma_documento($datos_venta['venta_terreno']['vendedor_id'], null, 'por_gerente');

            $firmas = [
                'cliente' => $firma_cliente['firma_path'],
                'gerente' => $firma_gerente['firma_path'],
            ];

            $pdf = PDF::loadView('cementerios/acuse_cancelacion/acuse', ['datos' => $datos_venta, 'empresa' => $empresa, 'firmas' => $firmas]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "ACUSE DE CANCELACIÓN " . strtoupper($datos_venta['nombre']) . '.pdf';

            $pdf->setOptions([
                'title' => $name_pdf,
                'footer-html' => view('cementerios.acuse_cancelacion.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_venta['operacion_status'] != 0) {
                $pdf->setOptions([
                    'header-html' => view('cementerios.acuse_cancelacion.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 13.4);
            $pdf->setOption('margin-right', 13.4);
            $pdf->setOption('margin-top', 6.4);
            $pdf->setOption('margin-bottom', 19.4);
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
                $enviar_email = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'ACUSE DE CANCELACIÓN',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al cargar los datos.', 409);
        }
    }

    public function documento_titulo(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /* $id_venta = 1;
        $email = false;
        $email_to = 'hector@gmail.com';
         */

        //obtengo la informacion de esa venta
        $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
        if (empty($datos_venta)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $FirmasController = new FirmasController();
        $firma_cliente = $FirmasController->get_firma_documento($datos_venta['operacion_id'], 6, 'por_area_firma');
        $firma_gerente = $FirmasController->get_firma_documento($datos_venta['venta_terreno']['vendedor_id'], null, 'por_gerente');

        $firmas = [
            'cliente' => $firma_cliente['firma_path'],
            'gerente' => $firma_gerente['firma_path'],
        ];

        $pdf = PDF::loadView('cementerios/titulo/titulo', ['datos' => $datos_venta, 'empresa' => $empresa, 'firmas' => $firmas]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "FORMATO DE TITULO " . strtoupper($datos_venta['nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.titulo.footer', ['empresa' => $empresa]),
        ]);
        if ($datos_venta['operacion_status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('cementerios.titulo.header'),
            ]);
        }

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 14.4);
        $pdf->setOption('margin-right', 14.4);
        $pdf->setOption('margin-top', 24.4);
        $pdf->setOption('margin-bottom', 30.4);
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
            $enviar_email = $email_controller->pdf_email(
                $email_to,
                strtoupper($datos_venta['nombre']),
                'FORMATO DE TITULO',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    public function referencias_de_pago(Request $request, $id_pago = '')
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*
        $id_venta = 35;
        $email = false;
        $email_to = 'hector@gmail.com';
         */

        $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
        if (empty($datos_venta)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('cementerios/pagos/referencias', ['id_pago' => $id_pago, 'datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "REFERENCIA DE PAGOS TITULAR " . strtoupper($datos_venta['nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.pagos.footer'),
        ]);
        if ($datos_venta['operacion_status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('cementerios.pagos.header'),
            ]);
        }

        //$pdf->setOption('grayscale', true);
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
            $enviar_email = $email_controller->pdf_email(
                $email_to,
                strtoupper($datos_venta['nombre']),
                'REFERENCIAS DE PAGO CEMENTERIO',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    /**pdf del convenio plan de cementerio */
    public function documento_convenio(Request $request)
    {

        /* $id_venta = 136;
        $email = false;
        $email_to = 'hector@gmail.com';
         */
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        $email = $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        //obtengo la informacion de esa venta
        $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
        if (empty($datos_venta)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_convenio_raw'] == null) {
        return 0;
        }*/

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();

        $FirmasController = new FirmasController();
        $firma_cliente = $FirmasController->get_firma_documento($datos_venta['operacion_id'], 2, 'por_area_firma');
        $firma_vendedor = $FirmasController->get_firma_documento($datos_venta['venta_terreno']['vendedor_id'], null, 'por_vendedor');
        $firma_gerente = $FirmasController->get_firma_documento($datos_venta['venta_terreno']['vendedor_id'], null, 'por_gerente');

        $firmas = [
            'cliente' => $firma_cliente['firma_path'],
            'vendedor' => $firma_vendedor['firma_path'],
            'gerente' => $firma_gerente['firma_path'],
        ];

        $pdf = PDF::loadView('cementerios/convenio/documento_convenio', ['datos' => $datos_venta, 'empresa' => $empresa, 'firmas' => $firmas]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "CONVENIO TITULAR " . strtoupper($datos_venta['nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.convenio.footer', ['empresa' => $empresa]),
        ]);
        if ($datos_venta['operacion_status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('cementerios.convenio.header'),
            ]);
        }
        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 20.4);
        $pdf->setOption('margin-right', 20.4);
        $pdf->setOption('margin-top', 15.4);
        $pdf->setOption('margin-bottom', 30.4);
        $pdf->setOption('page-size', 'letter');

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
                strtoupper($datos_venta['nombre']),
                'COPIA DEL CONVENIO / CEMENTERIO AETERNUS',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    /**pdf de la solicitud de plan de cementerio */
    public function documento_solicitud(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /* $id_venta = 40;
        $email = false;
        $email_to = 'hector@gmail.com';
         */

        //obtengo la informacion de esa venta
        $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
        if (empty($datos_venta)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_solicitud_raw'] == null) {
        return 0;
        }*/

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();

        $FirmasController = new FirmasController();
        $firma_cliente = $FirmasController->get_firma_documento($datos_venta['operacion_id'], 1, 'por_area_firma');
        $firma_vendedor = $FirmasController->get_firma_documento($datos_venta['venta_terreno']['vendedor_id'], null, 'por_vendedor');

        $firmas = [
            'cliente' => $firma_cliente['firma_path'],
            'vendedor' => $firma_vendedor['firma_path'],
        ];

        $pdf = PDF::loadView('cementerios/solicitud/documento_solicitud', ['datos' => $datos_venta, 'empresa' => $empresa, 'firmas' => $firmas]);

        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "SOLICITUD TITULAR " . strtoupper($datos_venta['nombre']) . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.solicitud.footer', ['empresa' => $empresa]),
        ]);
        if ($datos_venta['operacion_status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('cementerios.solicitud.header'),
            ]);
        }

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 5.4);
        $pdf->setOption('margin-right', 5.4);
        $pdf->setOption('margin-top', 5.4);
        $pdf->setOption('margin-bottom', 35.4);
        $pdf->setOption('page-size', 'letter');

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
                strtoupper($datos_venta['nombre']),
                'SOLICITUD DE PROPIEDAD / CEMENTERIO AETERNUS',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    /**pdf de los servicios que han usado esta propiedad */
    public function servicios_propiedad(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$id_venta = 210;
        $email = false;
        $email_to = 'hector@gmail.com';
         */

        //obtengo la informacion de esa venta
        $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
        if (empty($datos_venta)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_solicitud_raw'] == null) {
        return 0;
        }*/

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();

        $FirmasController = new FirmasController();
        $firma_cliente = $FirmasController->get_firma_documento($datos_venta['operacion_id'], 1, 'por_area_firma');
        $firma_vendedor = $FirmasController->get_firma_documento($datos_venta['venta_terreno']['vendedor_id'], null, 'por_vendedor');

        $firmas = [
            'cliente' => $firma_cliente['firma_path'],
            'vendedor' => $firma_vendedor['firma_path'],
        ];

        $pdf = PDF::loadView('cementerios/servicios_propiedad/documento', ['datos' => $datos_venta, 'empresa' => $empresa, 'firmas' => $firmas]);

        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "SERVICIOS POR PROPIEDAD | TITULAR " . strtoupper($datos_venta['nombre']) . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.servicios_propiedad.footer', ['empresa' => $empresa]),
        ]);
        if ($datos_venta['operacion_status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('cementerios.servicios_propiedad.header'),
            ]);
        }

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 12.4);
        $pdf->setOption('margin-bottom', 32.4);
        $pdf->setOption('page-size', 'letter');

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
                strtoupper($datos_venta['nombre']),
                'SERVICIOS POR PROPIEDAD',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    /**pdf del estado de cuenta del cementerio */
    public function documento_estado_de_cuenta_cementerio(Request $request)
    {
        try {
            $id_venta = 2;
            $email = false;
            $email_to = 'hector@gmail.com';
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email = $request->email_send === 'true' ? true : false;
            $email_to = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta = $requestVentasList['venta_id'];
            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            //obtengo la informacion de esa venta
            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
            if (empty($datos_venta)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }
            $pagos_operacion = [];
            $client = new \GuzzleHttp\Client();
            try {

                /**TRAYENDO EL TOKEN PARA CONSUMIR EL SERVICE */
                $response = $client->request('POST', config('services.passport.login_endpoint'), [
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => config('services.passport.client_backend_id'),
                        'client_secret' => config('services.passport.client_backend_secret'),
                    ],
                ]);
                $token = json_decode((string) $response->getBody(), true)['access_token'];
                if ($token == '') {
                    return $this->errorResponse('Ocurrió un error durante la petición. Por favor reintente.', 409);
                }
                $pagos_operacion =
                    json_decode($client->request(
                        'GET',
                        env('APP_URL') . 'pagos/get_pagos_backend/all/false/false?operacion_id=' . $datos_venta['operacion_id'],
                        [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $token,
                            ],
                        ]
                    )->getBody(), true);
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                return $this->errorResponse('Ocurrió un error durante la petición. Por favor reintente.', $e->getCode());
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa = $get_funeraria->get_empresa_data();
            $pdf = PDF::loadView('cementerios/estado_cuenta/estado_cuenta', ['pagos_operacion' => $pagos_operacion, 'datos' => $datos_venta, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "ESTADO CUENTA " . strtoupper($datos_venta['nombre']) . '.pdf';
            $pdf->setOptions([
                'title' => $name_pdf,
                'footer-html' => view('cementerios.estado_cuenta.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_venta['operacion_status'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('cementerios.estado_cuenta.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            //$pdf->setOption('orientation', 'landscape');
            $pdf->setOption('margin-left', 12.4);
            $pdf->setOption('margin-right', 12.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 30.4);
            $pdf->setOption('page-size', 'letter');

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
                    strtoupper($datos_venta['nombre']),
                    'ESTADO DE CUENTA / CEMENTERIO AETERNUS',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al cargar los datos.', 409);
        }
    }

    /**pdf del convenio plan de cementerio */
    public function reglamento_pago(Request $request)
    {

        /* $id_venta = 35;
        $email = false;
        $email_to = 'hector@gmail.com';
         */
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        $email = $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        //obtengo la informacion de esa venta
        $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
        if (empty($datos_venta)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_convenio_raw'] == null) {
        return 0;
        }*/

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();

        $FirmasController = new FirmasController();
        $firma_cliente = $FirmasController->get_firma_documento($datos_venta['operacion_id'], 6, 'por_area_firma');
        $firma_gerente = $FirmasController->get_firma_documento($datos_venta['venta_terreno']['vendedor_id'], null, 'por_gerente');

        $firmas = [
            'cliente' => $firma_cliente['firma_path'],
            'gerente' => $firma_gerente['firma_path'],
        ];

        $pdf = PDF::loadView('cementerios/reglamento_pago/reglamento', ['datos' => $datos_venta, 'empresa' => $empresa, 'firmas' => $firmas]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "REGLAMENTO DE PAGO " . strtoupper($datos_venta['nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('cementerios.reglamento_pago.footer', ['empresa' => $empresa]),
        ]);
        if ($datos_venta['operacion_status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('cementerios.reglamento_pago.header'),
            ]);
        }
        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 20.4);
        $pdf->setOption('margin-right', 20.4);
        $pdf->setOption('margin-top', 10.4);
        $pdf->setOption('margin-bottom', 35.4);
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
            $enviar_email = $email_controller->pdf_email(
                $email_to,
                strtoupper($datos_venta['nombre']),
                'REGLAMENTO DE PAGO / CEMENTERIO AETERNUS',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }

    public function documento_ubicacion_terreno(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        /* $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];
         */
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        $id_venta = 5;
        $email = false;
        $email_to = 'hector@gmail.com';

        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta);

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_solicitud_raw'] == null) {
        return 0;
        }*/

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/estado_cuenta/estado_cuenta', ['datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "ESTADO CUENTA " . strtoupper($datos_venta['cliente_nombre']) . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.estado_cuenta.footer', ['empresa' => $empresa]),
        ]);
        if ($datos_venta['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.estado_cuenta.header'),
            ]);
        }

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 12.4);
        $pdf->setOption('margin-bottom', 12.4);
        $pdf->setOption('page-size', 'a4');

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
                strtoupper($datos_venta['cliente_nombre']),
                'ESTADO DE CUENTA / CEMENTERIO AETERNUS',
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
