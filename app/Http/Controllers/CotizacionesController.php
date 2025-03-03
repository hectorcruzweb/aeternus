<?php

namespace App\Http\Controllers;
use PDF;
use App\User;
use Carbon\Carbon;
use App\Cotizaciones;
use App\ConceptosCotizacion;
use Illuminate\Http\Request;
use App\ConceptosPredefinidas;
use App\CotizacionesPredefinidas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\FinanciamientosPredefinidas;
use Illuminate\Support\Facades\Storage;

class CotizacionesController extends ApiController
{
    public function control_cotizaciones(Request $request, $tipo_request = '')
    {
        if (!(trim($tipo_request) == 'agregar' || trim($tipo_request) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }
        $validaciones = [
            'id_cotizacion' => trim($tipo_request) == 'modificar' ? 'required' : '',
            'cliente' => 'required',
            'telefono' => '',
            'email' => '',
            'vendedor.value' => 'required',
            'fecha_cotizacion' => 'required|date|before:tomorrow',
            'validez.value' => 'required',
            'cotizaciones_predefinidas_b' => '',
            'modalidad_pago.value' => 'required',
            'pago_inicial' => 'numeric',
            'pago_inicial_porcentaje.' => 'numeric',
            'total' => 'required|numeric',
            'descuento' => 'required|numeric',
            'descripcion_pagos' => 'required',
            'predefinidos' => ''
        ];
        /**VALIDACIONES CONDICIONADAS*/
        if (isset($request->email)) {
            $validaciones['email'] = 'email';
        }
        $cotizacion_personalizada_b = isset($request->conceptos) && count($request->conceptos) > 0 ? true : false;
        if ($cotizacion_personalizada_b) {
            $validaciones['conceptos.*.descripcion'] = 'required';
            $validaciones['conceptos.*.costo_neto_normal'] = 'required|numeric|min:0';
            $validaciones['conceptos.*.costo_neto_descuento'] = 'required|numeric|min:0';
            $validaciones['conceptos.*.descuento_b'] = 'required|min:0|max:1';
            $validaciones['conceptos.*.cantidad'] = 'required|integer|min:1';
        }

        //VALIDANDO COTIZACIONES PREDEFINIDAS
        $cotizaciones_predefinidas_b = isset($request->cotizaciones_predefinidas_b) && $request->cotizaciones_predefinidas_b == 1 ? true : false;
        if ($cotizaciones_predefinidas_b) {
            $validaciones['predefinidos'] = 'required';
            $validaciones['predefinidos.*.tipo'] = 'required';
            $validaciones['predefinidos.*.label'] = 'required';
            $validaciones['predefinidos.*.financiamientos'] = 'required|min:1';
            $validaciones['predefinidos.*.secciones'] = 'required';
            $validaciones['predefinidos.*.financiamientos.*.costo_neto'] = 'required|numeric';
            $validaciones['predefinidos.*.financiamientos.*.financiamiento'] = 'required';
            $validaciones['predefinidos.*.financiamientos.*.pago_inicial'] = 'required|numeric';
            $validaciones['predefinidos.*.financiamientos.*.pago_mensual'] = 'required|numeric';
            $validaciones['predefinidos.*.secciones.*.seccion'] = 'required';
            $validaciones['predefinidos.*.secciones.*.conceptos'] = '';
        }

        //definiendo tipo de cotizacion
        $tipo_id = 0;
        if ($cotizacion_personalizada_b && $cotizaciones_predefinidas_b)
            $tipo_id = 3;
        else if (!$cotizacion_personalizada_b && $cotizaciones_predefinidas_b)
            $tipo_id = 2;
        else if ($cotizacion_personalizada_b && !$cotizaciones_predefinidas_b)
            $tipo_id = 1;

        if ($tipo_id == 0) {
            return $this->errorResponse("Ingrese los conceptos de esta cotización.", 409);
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        //Mensajes de validaciones
        $mensajes = [
            'cliente.required' => 'Ingrese el nombre del cliente.',
            'email' => 'Ingrese un E-mail válido.',
            'vendedor.value.required' => 'Seleccione 1 vendedor.',
            'fecha_cotizacion.required' => 'Ingrese la fecha de cotización.',
            'fecha_cotizacion.before' => 'Ingrese una fecha válida.',
            'date' => 'Ingrese una fecha válida.',
            'validez.value.required' => 'Seleccione el periodo de validéz.',
            'modalidad_pago.value.required' => 'Seleccione una modalidad de pago.',
            'numeric' => 'Este dato debe ser un número.',
            'descripcion_pagos.required' => 'Ingrese la descripción de pagos.',
            'conceptos.*.descripcion.required' => 'Ingrese la descripción del concepto.',
            'conceptos.*.costo_neto_normal.*' => 'Ingrese un costo neto válido.',
            'conceptos.*.descuento_b.*' => 'Indique si tiene descuento.',
            'conceptos.*.costo_neto_descuento.*' => 'Ingrese un costo neto con descuento válido.',
            'conceptos.*.cantidad.*' => 'Ingrese una cantidad válida.',
            'predefinidos.*' => "Verifique que ha capturado las cotizaciones correctamente."
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );
        try {
            $fecha_cotizacion = Carbon::create($request->fecha_cotizacion);
            $fecha_vencimiento = null;
            $periodo_validez_value = (int) $request->validez['value'];
            if ($periodo_validez_value == 1) {
                $fecha_vencimiento = $fecha_cotizacion->addDay();
            } else if ($periodo_validez_value == 2) {
                $fecha_vencimiento = $fecha_cotizacion->addWeek();
            } else if ($periodo_validez_value == 3) {
                $fecha_vencimiento = $fecha_cotizacion->addWeeks(2);
            } else if ($periodo_validez_value == 4) {
                $fecha_vencimiento = $fecha_cotizacion->addWeeks(3);
            } else if ($periodo_validez_value == 5) {
                $fecha_vencimiento = $fecha_cotizacion->addMonth();
            }
            DB::beginTransaction();
            $cotizacion = [
                'cliente_nombre' => strtoupper(trim($request->cliente)),
                'cliente_telefono' => trim($request->telefono),
                'cliente_email' => trim($request->email),
                'vendedor_id' => (int) $request->vendedor['value'],
                'fecha' => trim($request->fecha_cotizacion),
                'periodo_validez_id' => (int) $request->validez['value'],
                'fecha_vencimiento' => $fecha_vencimiento,
                'predefinidas_b' => isset($request->cotizaciones_predefinidas_b) ? $request->cotizaciones_predefinidas_b : 0,
                'descuento' => isset($request->descuento) ? $request->descuento : 0,
                'total' => isset($request->total) ? $request->total : 0,
                'modalidad' => (int) $request->modalidad_pago['value'],
                'descripcion_pagos' => strtoupper(trim($request->descripcion_pagos)),
                'nota' => trim($request->nota),
                'tipo_id' => $tipo_id
            ];
            $query = DB::table('cotizaciones');
            $id_cotizacion = 0;
            if ($tipo_request == 'agregar') {
                //se agregan los datos necesarios para la accion de agregar
                $cotizacion = array_merge(
                    $cotizacion,
                    [
                        'registro_id' => (int) $request->user()->id,
                        "fechahora_registro" => now()
                    ]
                );
                $id_cotizacion = $query->insertGetId($cotizacion);
            } else {
                //verificando que se pueda modificar
                $request_empty = new \Illuminate\Http\Request();
                $cotizacion_datos = $this->get_cotizaciones($request_empty, $request->id_cotizacion, false)[0];
                if ($cotizacion_datos['status'] == 0) {
                    return $this->errorResponse("Esta cotización ya fue cancelada y no se puede modificar.", 409);
                } else if ($cotizacion_datos['status'] == 3) {
                    return $this->errorResponse("Esta cotización ya venció y no se puede modificar.", 409);
                }
                $id_cotizacion = $request->id_cotizacion;
                $cotizacion = array_merge(
                    $cotizacion,
                    [
                        'modifico_id' => (int) $request->user()->id,
                        "fechahora_modificacion" => now()
                    ]
                );
                $query->where("id", "=", $id_cotizacion)->update($cotizacion);
                //aqui hacemos la actualizacion de conceptos
                ConceptosCotizacion::where('cotizaciones_id', '=', $id_cotizacion)->delete();
                //busco el id de las cotizaciones predefinidas para hacer la reinserccion
                $ids_cotizaciones_predefinidas = CotizacionesPredefinidas::select('id')->where('cotizaciones_id', '=', $id_cotizacion)->get();
                ConceptosPredefinidas::whereIn('cotizacion_predefinida_id', $ids_cotizaciones_predefinidas)->delete();
                FinanciamientosPredefinidas::whereIn('cotizacion_predefinida_id', $ids_cotizaciones_predefinidas)->delete();
                CotizacionesPredefinidas::whereIn('id', $ids_cotizaciones_predefinidas)->delete();
            }
            //aqui se hacen los registros de conceptos y planes predefinidos(asi como sus registros de conceptos y financiamentos correspondientes).
            //se agregan los conceptos
            if ($cotizacion_personalizada_b) {
                foreach ($request->conceptos as $key => $concepto) {
                    ConceptosCotizacion::insert(
                        [
                            'descripcion' => $concepto['descripcion'],
                            'cantidad' => $concepto['cantidad'],
                            'costo_neto_normal' => $concepto['costo_neto_normal'],
                            'costo_neto_descuento' => $concepto['costo_neto_descuento'],
                            'descuento_b' => $concepto['descuento_b'],
                            'cotizaciones_id' => $id_cotizacion
                        ]
                    );
                }
            }

            if ($cotizaciones_predefinidas_b) {
                foreach ($request->predefinidos as $key => $predefinido) {
                    //guardamos la cotizacion y obtenemos el ID
                    $cotizacion_predefinida_id = CotizacionesPredefinidas::insertGetId(
                        [
                            'descripcion' => $predefinido['label'],
                            'tipo' => $predefinido['tipo'],
                            'cotizaciones_id' => $id_cotizacion
                        ]
                    );
                    foreach ($predefinido["secciones"] as $key_seccion => $seccion) {
                        $seccion_id = 1;//1 por defecto seccion "incluye"
                        if ($seccion['seccion'] == 'inhumacion') {
                            $seccion_id = 2;
                        } else if ($seccion['seccion'] == 'cremacion') {
                            $seccion_id = 3;
                        } else if ($seccion['seccion'] == 'velacion') {
                            $seccion_id = 4;
                        }
                        foreach ($seccion["conceptos"] as $key_concepto => $concepto) {
                            ConceptosPredefinidas::insert(
                                [
                                    'seccion_id' => $seccion_id,
                                    'concepto' => $concepto,
                                    'cotizacion_predefinida_id' => $cotizacion_predefinida_id
                                ]
                            );
                        }
                    }
                    //guardo los financiamientos
                    foreach ($predefinido["financiamientos"] as $key_financiamiento => $financiamiento) {
                        FinanciamientosPredefinidas::insert(
                            [
                                'cotizacion_predefinida_id' => $cotizacion_predefinida_id,
                                'financiamiento' => $financiamiento['financiamiento'],
                                'costo_neto' => $financiamiento['costo_neto'],
                                'pago_inicial' => $financiamiento['pago_inicial'],
                                'pago_mensual' => $financiamiento['pago_mensual']
                            ]
                        );
                    }
                }
            }
            DB::commit();
            return $id_cotizacion;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function get_cotizaciones(Request $request, $id_cotizacion = 'all', $paginated = false)
    {
        $light = filter_var($request->light, FILTER_VALIDATE_BOOLEAN);
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $cliente = $request->cliente;
        $numero_control = $request->numero_control;
        $status = $request->status;
        $fecha_cotizacion = $request->fecha_cotizacion;
        $resultado_query = Cotizaciones::select(
            '*',
            \DB::raw('(CASE
                        WHEN status = "0" THEN "Cancelado"
                        WHEN status = "2" THEN "Aceptado"
                        WHEN status = "1" AND now() > fecha_vencimiento  THEN "Vencido"
                        ELSE "Vigente"
                        END) AS status_texto'),
            \DB::raw('(CASE
                        WHEN status = "0" THEN 0
                        WHEN status = "2" THEN 2
                        WHEN status = "1" AND now() > fecha_vencimiento  THEN 3
                        ELSE 1
                        END) AS status')
        );
        if (!$light) {
            //version con todo y datos
            $resultado_query = $resultado_query->with('vendedor')->with('registro')->with('conceptos')->with('predefinidos.conceptos')->with('predefinidos.financiamientos');
        }
        /**solo ventas de planes funerarios */
        $resultado_query = $resultado_query->where(function ($q) use ($id_cotizacion) {
            if (trim($id_cotizacion) == 'all' || $id_cotizacion > 0) {
                if (trim($id_cotizacion) == 'all') {
                    $q->where('id', '>', $id_cotizacion);
                } else if ($id_cotizacion > 0) {
                    $q->where('id', '=', $id_cotizacion);
                }
            }
        })
            ->where(function ($q) use ($numero_control) {
                if (trim($numero_control) != '') {
                    /**filtro por numero de venta en gral */
                    $q->where('id', '=', $numero_control);
                }
            })
            ->where(function ($q) use ($status) {
                if (trim($status) != "") {

                }
            })
            ->where('cliente_nombre', 'like', '%' . $cliente . '%')
            ->orderBy('id', 'desc')
            ->get();
        $resultado = array();
        if ($paginated == 'paginated') {
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado = &$resultado_query;
        }
        foreach ($resultado as $index_cotizacion => &$cotizacion) {
            $cotizacion['fecha_texto'] = fecha_abr($cotizacion["fecha"]);
            $cotizacion['fechahora_registro_texto'] = fecha_abr($cotizacion["fechahora_registro"]);
            $cotizacion['fechahora_modificacion_texto'] = $cotizacion["fechahora_modificacion"] != '' ? fecha_abr($cotizacion["fechahora_modificacion"]) : null;
            $cotizacion['fechahora_cancelacion_texto'] = $cotizacion["fechahora_cancelacion"] != '' ? fecha_abr($cotizacion["fechahora_cancelacion"]) : null;
            $cotizacion['fechahora_aceptado_texto'] = $cotizacion["fechahora_aceptado"] != '' ? fecha_abr($cotizacion["fechahora_aceptado"]) : null;
            /*
             { label: "Uso Inmediato (1 Día)", value: "1" },
                { label: "1 Semana", value: "2" },
                { label: "2 Semanas", value: "3" },
                { label: "3 Semanas", value: "4" },
                { label: "1 Mes", value: "5" },
            */
            $cotizacion['fecha_vencimiento_texto'] = fecha_abr($cotizacion["fecha_vencimiento"]);
            //modalidad
            if ($cotizacion["modalidad"] == 1) {
                $cotizacion["modalidad_texto"] = 'Pago Único.';
            } else {
                $cotizacion["modalidad_texto"] = $cotizacion["modalidad"] . ' Mensualidades.';
            }
            //tipo de cotizacion
            if ($cotizacion['tipo_id'] == 1) {
                $cotizacion['tipo_texto'] = 'Personalizada';
            } else if ($cotizacion['tipo_id'] == 2) {
                $cotizacion['tipo_texto'] = 'Predefinida';
            } else {
                $cotizacion['tipo_texto'] = 'Mixta';
            }

            if (!$light) {
                //hago el array listo para mostrar al frontend
                foreach ($cotizacion['predefinidos'] as $key_predefinidos => &$predefinido) {
                    /*if ($predefinido['tipo'] == "cementerio")
                        continue;*/
                    $secciones = array();
                    $secciones = [
                        [
                            'seccion' => 'incluye',
                            'conceptos' => [],
                        ],
                        [
                            'seccion' => 'inhumacion',
                            'conceptos' => [],
                        ],
                        [
                            'seccion' => 'cremacion',
                            'conceptos' => [],
                        ],
                        [
                            'seccion' => 'velacion',
                            'conceptos' => [],
                        ],
                    ];
                    foreach ($predefinido['conceptos'] as $key_seccion => $seccion) {
                        /**agregando los conceptos segun su seccion */
                        if ($seccion['seccion_id'] == 1) {
                            /**incluye */
                            array_push(
                                $secciones[0]['conceptos'],
                                $seccion['concepto']
                            );
                        } elseif ($seccion['seccion_id'] == 2) {
                            /**inhumacion */
                            array_push(
                                $secciones[1]['conceptos'],
                                $seccion['concepto']
                            );
                        } elseif ($seccion['seccion_id'] == 3) {
                            /**cremacion */
                            array_push(
                                $secciones[2]['conceptos'],
                                $seccion['concepto']
                            );
                        } elseif ($seccion['seccion_id'] == 4) {
                            /**velacion */
                            array_push(
                                $secciones[3]['conceptos'],
                                $seccion['concepto']
                            );
                        }
                    }
                    $predefinido = [
                        'label' => $predefinido['descripcion'],
                        'financiamientos' => $predefinido['financiamientos'],
                        'tipo' => $predefinido['tipo'],
                        'secciones' => $secciones
                    ];
                }
            }
        }
        return $resultado_query;
    }

}
