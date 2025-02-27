<?php

namespace App\Http\Controllers;
use App\ConceptosCotizacion;
use App\ConceptosPredefinidas;
use App\Cotizaciones;
use App\CotizacionesPredefinidas;
use App\FinanciamientosPredefinidas;
use PDF;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
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
            'fecha_cotizacion' => 'required|date',
            'validez.value' => 'required',
            'cotizaciones_predefinidas_b' => '',
            'modalidad_pago.value' => 'required',
            'pago_inicial' => 'numeric',
            'pago_inicial_porcentaje.' => 'numeric',
            'total' => 'required|numeric',
            'descuento' => 'required|numeric',
            'descripcion_pagos' => 'required'
        ];
        /**VALIDACIONES CONDICIONADAS*/

        if (isset($request->email)) {
            $validaciones['email'] = 'email';
        }


        if (isset($request->conceptos) && count($request->conceptos) > 0) {
            $validaciones['conceptos.*.descripcion'] = 'required';
            $validaciones['conceptos.*.costo_neto_normal'] = 'required|numeric|min:0';
            $validaciones['conceptos.*.costo_neto_descuento'] = 'required|numeric|min:0';
            $validaciones['conceptos.*.descuento_b'] = 'required|min:0|max:1';
            $validaciones['conceptos.*.cantidad'] = 'required|integer|min:1';
        }
        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        //Mensajes de validaciones
        $mensajes = [
            'cliente.required' => 'Ingrese el nombre del cliente.',
            'email' => 'Ingrese un E-mail válido.',
            'vendedor.value.required' => 'Seleccione 1 vendedor.',
            'fecha_cotizacion.required' => 'Ingrese la fecha de cotización.',
            'date' => 'Ingrese una fecha válida.',
            'validez.value.required' => 'Seleccione el periodo de validéz.',
            'modalidad_pago.value.required' => 'Seleccione una modalidad de pago.',
            'numeric' => 'Este dato debe ser un número.',
            'descripcion_pagos.required' => 'Ingrese la descripción de pagos.',
            'conceptos.*.descripcion.required' => 'Ingrese la descripción del concepto.',
            'conceptos.*.costo_neto_normal.*' => 'Ingrese un costo neto válido.',
            'conceptos.*.descuento_b.*' => 'Indique si tiene descuento.',
            'conceptos.*.costo_neto_descuento.*' => 'Ingrese un costo neto con descuento válido.',
            'conceptos.*.cantidad.*' => 'Ingrese una cantidad válida.'
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );
        try {
            DB::beginTransaction();
            $cotizacion = [
                'cliente_nombre' => strtoupper(trim($request->cliente)),
                'cliente_telefono' => trim($request->telefono),
                'cliente_email' => trim($request->email),
                'vendedor_id' => (int) $request->vendedor['value'],
                'fecha' => trim($request->fecha_cotizacion),
                'periodo_validez_id' => (int) $request->validez['value'],
                'predefinidas_b' => isset($request->cotizaciones_predefinidas_b) ? $request->cotizaciones_predefinidas_b : 0,
                'descuento' => isset($request->descuento) ? $request->descuento : 0,
                'total' => isset($request->total) ? $request->total : 0,
                'modalidad' => (int) $request->modalidad_pago['value'],
                'descripcion_pagos' => strtoupper(trim($request->descripcion_pagos)),
                'nota' => trim($request->nota)
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
            }
            //aqui se hacen los registros de conceptos y planes predefinidos(asi como sus registros de conceptos y financiamentos correspondientes).

            //se agregan los conceptos
            if (isset($request->conceptos) && count($request->conceptos) > 0) {
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

            DB::commit();
            return $id_cotizacion;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}
