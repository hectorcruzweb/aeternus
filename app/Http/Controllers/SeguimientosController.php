<?php

namespace App\Http\Controllers;

use Log;
use App\Clientes;
use App\Operaciones;
use App\Cotizaciones;
use App\Seguimientos;
use Illuminate\Http\Request;
use App\Mail\SeguimientoMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SeguimientosController extends ApiController
{
    /**
     * Get motivos de seguimiento.
     *
     * @return array
     */
    public function getMotivos()
    {
        return [
            1  => "Recordatorio de pagos",
            2  => "Recordatorio de mantenimiento anual de nicho o sepultura",
            3  => "Renovación o actualización de plan funerario",
            4  => "Entrega de documentación o certificados",
            5  => "Seguimiento post-servicio funerario",
            6  => "Promoción de planes futuros",
            7  => "Entrega de cenizas, accesorios, etc.",
            8  => "Cambio de ubicación o información de contacto",
            9  => "Llamada de cortesía o atención personalizada",
            10 => "Programación de servicios adicionales",
        ];
    }

    /**
     * Get medios de contacto.
     *
     * @return array
     */
    public function getMedios()
    {
        return [
            1 => "Teléfono",
            2 => "Correo electrónico",
            3 => "WhatsApp",
            4 => "Mensaje de texto (SMS)",
            5 => "Visita presencial",
            6 => "Redes sociales",
        ];
    }



    public function programar_segumientos(Request $request, $tipo_request = '')
    {
        if (!(trim($tipo_request) == 'agregar' || trim($tipo_request) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }
        $request->merge([
            'enviar_x_email' => $request->enviar_x_email ? 1 : 0
        ]);
        $validaciones = [
            'seguimiento_id' => [
                trim($tipo_request) === 'modificar' ? 'required' : '', // required only if modifying
                function ($attribute, $value, $fail) use ($tipo_request) {
                    if (trim($tipo_request) === 'modificar' && $value) {
                        $exists = DB::table('seguimientos')
                            ->where('id', $value)
                            ->where('status', 1)
                            ->exists();
                        if (!$exists) {
                            $fail("El seguimiento indicado no existe o no se puede modificar.");
                        }
                    }
                }
            ],
            'tipo_cliente_id' => 'required|in:1,2', // only 1 or 2 allowed
            'cliente_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $tipo = $request->tipo_cliente_id;
                    if ($tipo == 1 && !Clientes::where('id', $value)->exists()) {
                        $fail("El cliente seleccionado no ha sido registrado.");
                    }
                    if ($tipo == 2 && !Cotizaciones::where('id', $value)->exists()) {
                        $fail("El cliente seleccionado no cuenta con Cotizaciones.");
                    }
                }
            ],
            'operacion_id' => [
                function ($attribute, $value, $fail) use ($request) {
                    // Only validate if operacion_id is provided
                    if (!is_null($value) && $value !== '') {
                        $tipo = $request->tipo_cliente_id;
                        $clienteId = $request->cliente_id;

                        if ($tipo == 1) {
                            $exists = Operaciones::where('id', $value)
                                ->where('clientes_id', $clienteId)
                                ->exists();
                            if (!$exists) {
                                $fail("La operación indicada no existe o no pertenece a este cliente.");
                            }
                        }
                        if ($tipo == 2) {
                            $exists = Cotizaciones::where('id', $value)->exists();
                            if (!$exists) {
                                $fail("La cotización indicada no existe.");
                            }
                        }
                    }
                }
            ],
            //datos del seguimiento
            'fecha_a_contactar' => 'required|date|after_or_equal:now', // >= current time
            'enviar_x_email' => 'required|in:1,0',
            'motivo.value'      => 'required|integer|between:1,10', // required and between 1-10
            'medio.value'       => 'required|integer|between:1,6',  // required and between 1-6
            'email' => 'required_if:enviar_x_email,1|email|nullable',
            'comentario_programado' => ''
        ];
        $mensajes = [
            'seguimiento_id.required'      => 'El ID del seguimiento es requerido si se va a modificar.',
            'cliente_id.required'          => 'Seleccione un cliente.',
            'tipo_cliente_id.required'     => 'Seleccione el tipo de cliente.',
            'tipo_cliente_id.in'           => 'El tipo de cliente debe ser 1 o 2.',
            'operacion_id.*'               => 'La operación indicada no es válida para este cliente.',
            'fecha_a_contactar.required'   => 'Ingrese la fecha a contactar.',
            'fecha_a_contactar.date'       => 'Ingrese una fecha válida.',
            'fecha_a_contactar.after_or_equal' => 'La fecha a contactar debe ser igual o posterior a la fecha y hora actual.',
            'enviar_x_email.required'      => 'Indique si desea enviar por correo electrónico.',
            'motivo.value.required'        => 'Seleccione un motivo de seguimiento.',
            'motivo.value.integer'         => 'El valor del motivo debe ser un número.',
            'motivo.value.between'         => 'El valor del motivo debe estar entre 1 y 10.',
            'medio.value.required'         => 'Seleccione un medio de contacto.',
            'medio.value.integer'          => 'El valor del medio debe ser un número.',
            'medio.value.between'          => 'El valor del medio debe estar entre 1 y 6.',
            'email.required_if' => 'Debe ingresar un correo electrónico cuando se selecciona enviar por email.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'comentario_programado.required' => 'Ingrese un comentario para el seguimiento.'
        ];
        // return $this->errorResponse(gettype($request->enviar_x_email), 500);
        request()->validate(
            $validaciones,
            $mensajes
        );

        $queryClosure = function () use ($request, $tipo_request) {
            // Prepare the common seguimiento data
            $seguimientoData = [
                'tipo_cliente_id' => $request->tipo_cliente_id,
                'clientes_id' => $request->cliente_id,
                'operaciones_id' => $request->operacion_id ?? null,
                'email_programado' => $request->email,
                'motivo_id' => $request->motivo['value'],
                'medio_preferido_programado_id' => $request->medio['value'],
                'fechahora_programada' => $request->fecha_a_contactar,
                'comentario_programado' => $request->comentario_programado,
                'programado_b' => 1,
            ];

            if (trim($tipo_request) === 'agregar') {
                // Insert a new seguimiento
                $seguimientoId = Seguimientos::insertGetId($seguimientoData);
                // Send email if requested
                if ($request->enviar_x_email == 1 && $request->email) {
                    $this->email_sender($request->email);
                }
                return $this->successResponse([
                    'message' => 'Seguimiento agregado correctamente.',
                    'seguimiento_id' => $seguimientoId
                ], 200);
            } elseif (trim($tipo_request) === 'modificar') {
                // Limit update to only allowed fields
                $updateData = [
                    'email_programado' => $seguimientoData['email_programado'],
                    'motivo_id' => $seguimientoData['motivo_id'],
                    'medio_preferido_programado_id' => $seguimientoData['medio_preferido_programado_id'],
                    'fechahora_programada' => $seguimientoData['fechahora_programada'],
                ];
                Seguimientos::where('id', $request->seguimiento_id)
                    ->update($updateData);
                return $this->successResponse("Seguimiento actualizado correctamente.", 200);
            }
        };
        // Check debug mode
        if (!config('app.debug')) {
            try {
                return $queryClosure();
            } catch (\Exception $e) {
                // Catch any DB or unexpected error
                //Log::error("Error en programar seguimientos: " . $e->getMessage());
                return $this->errorResponse("Ocurrió un error al procesar el seguimiento.", 500);
            }
        } else {
            return $queryClosure(); // directly run without try-catch
        }
    }

    private function email_sender($email = '', $id_seguimiento = '', $tipo = '')
    {
        try {
            Mail::to($email)->send(new SeguimientoMail([]));
        } catch (\Exception $e) {
            // Log the error, but do NOT stop the process
            //\Log::error("Error sending seguimiento email: " . $e->getMessage());
        }
    }

    public function get_seguimientos(Request $request)
    {
        $validaciones = [
            'programado_b' => 'required|in:0,1',
            'id' => 'sometimes|integer',
            'cliente_id' => 'sometimes|integer',
            'tipo_cliente_id' => 'sometimes|in:1,2',
            'operaciones_id' => 'sometimes|integer',
            'status' => 'sometimes|in:0,1,2',
        ];

        $mensajes = [
            'programado_b.required' => 'Debe indicar si el seguimiento está programado o no.',
            'programado_b.in' => 'El valor de programado_b debe ser 0 (no) o 1 (sí).',
            'id.integer' => 'El ID debe ser un número entero válido.',
            'cliente_id.integer' => 'El ID del cliente debe ser un número entero válido.',
            'tipo_cliente_id.in' => 'El tipo de cliente debe ser 1 o 2.',
            'operaciones_id.integer' => 'El ID de la operación debe ser un número entero válido.',
            'status.in' => 'El status solo puede ser 0, 1 o 2.'
        ];

        $request->validate($validaciones, $mensajes);

        $queryClosure = function () use ($request) {
            $query = Seguimientos::query();

            if ($request->has('id')) {
                $query->where('id', $request->id);
            }

            if ($request->has('programado_b')) {
                $query->where('programado_b', $request->programado_b);
                $query->select([
                    'tipo_cliente_id',
                    'clientes_id',
                    'operaciones_id',
                    'fechahora_programada',
                    'motivo_id',
                    'medio_preferido_programado_id',
                    'comentario_programado',
                    'fechahora_registro_programado',
                    'registro_programado_id',
                    'email_programado',
                    'status'
                ]);
            }

            if ($request->has('status')) {
                $statuses = is_array($request->status) ? $request->status : [$request->status];
                $query->whereIn('status', $statuses);
            }

            if ($request->has('cliente_id')) {
                $query->where('clientes_id', $request->cliente_id);
            }

            if ($request->has('tipo_cliente_id')) {
                $tipos = is_array($request->tipo_cliente_id) ? $request->tipo_cliente_id : [$request->tipo_cliente_id];
                $query->whereIn('tipo_cliente_id', $tipos);
            }

            if ($request->has('operaciones_id')) {
                $query->where('operaciones_id', $request->operaciones_id);
            }

            return $query->get();
        };

        // Check debug mode
        if (!config('app.debug')) {
            try {
                $seguimientos = $queryClosure();
            } catch (\Exception $e) {
                \Log::error("Error al obtener seguimientos: " . $e->getMessage());
                return $this->errorResponse("Ocurrió un error al obtener los seguimientos.", 500);
            }
        } else {
            $seguimientos = $queryClosure(); // directly run without try-catch
        }

        return $this->successResponse($seguimientos, 200);
    }
}
