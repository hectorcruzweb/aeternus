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
            11 => "Otro"
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
            7 => "Otro"
        ];
    }

    /**
     * Get motivos de cancelación.
     *
     * @return array
     */
    public function getMotivosCancelacion()
    {
        return [
            1 => "Seguimiento generado por error",
            2 => "El cliente contrató con otro proveedor",
            3 => "No se logró contactar al cliente después de varios intentos",
            4 => "El cliente ya no está interesado en el servicio o producto",
            5 => "El cliente solicitó cancelar el seguimiento voluntariamente",
            6 => "Otro"
        ];
    }

    public function getResultadosContacto()
    {
        return [
            1 => "Cliente confirmado",
            2 => "Cliente solicita más información",
            3 => "Cliente pospuesto",
            4 => "Cliente no interesado",
            5 => "Cliente con otro proveedor",
            6 => "No fue posible contactar",
            7 => "El cliente se comprometió a pagar cuanto antes",
            8 => "Otro"
        ];
    }

    public function registrar_seguimientos(Request $request, $tipo_request = '')
    {
        // ✅ Validate tipo_request
        if (!in_array(trim($tipo_request), ['agregar', 'modificar', 'cancelar', 'atender_seguimiento_programado'])) {
            return $this->errorResponse('Error, debe especificar qué tipo de control está solicitando.', 409);
        }

        // Normalize enviar_x_email
        $request->merge([
            'enviar_x_email' => $request->enviar_x_email ? 1 : 0
        ]);

        // ✅ Base validation rules
        $validaciones = [
            'seguimiento_id' => array_merge(
                in_array(trim($tipo_request), ['modificar', 'cancelar', 'atender_seguimiento_programado']) ? ['required', 'integer', 'min:1'] : ['nullable', 'integer', 'min:1'],
                [
                    function ($attribute, $value, $fail) use ($tipo_request) {
                        if (in_array($tipo_request, ['modificar', 'cancelar', 'atender_seguimiento_programado'])) {
                            // 🚫 Skip if not numeric or less than 1
                            if (!is_numeric($value) || $value < 1) {
                                return;
                            }
                            $seguimiento = Seguimientos::where('id', $value)->first();
                            if (!$seguimiento) {
                                $fail("El seguimiento indicado no existe.");
                                return;
                            }
                            if ($tipo_request === 'atender_seguimiento_programado') {
                                if ($seguimiento->programado_b != 1 || $seguimiento->status != 1) {
                                    $fail("El seguimiento no está activo o no está programado para atender.");
                                }
                            }

                            if ($tipo_request === 'modificar') {
                                if (!($seguimiento->status == 1 || ($seguimiento->status == 2 && $seguimiento->programado_b == 1))) {
                                    $fail("El seguimiento no puede modificarse porque no cumple las condiciones requeridas.");
                                }
                            }

                            if ($tipo_request === 'cancelar') {
                                if ($seguimiento->status == 0) {
                                    $fail("El seguimiento no puede cancelarse porque ya está inactivo.");
                                }
                                if (($seguimiento->programado_b == 1 && $seguimiento->status == 1)) {
                                    $fail("El seguimiendo no puede cancelarse desde este apartado, ya que no ha sido atendido.");
                                }
                            }
                        }
                    }
                ]
            ),
            'tipo_cliente_id' => ($tipo_request === 'agregar') ? 'required|in:1,2' : '',
            'cliente_id' => array_merge(
                ($tipo_request === 'agregar')
                    ? ['required', 'integer', 'min:1']
                    : ['nullable', 'integer', 'min:1'],
                [
                    function ($attribute, $value, $fail) use ($request, $tipo_request) {
                        // 🚫 Skip if not numeric or less than 1
                        if (!is_numeric($value) || $value < 1) {
                            return;
                        }
                        if ($tipo_request === 'agregar') {
                            $tipo = $request->tipo_cliente_id;

                            if ($tipo == 1 && !Clientes::where('id', $value)->exists()) {
                                $fail("El cliente seleccionado no ha sido registrado.");
                            }

                            if ($tipo == 2 && !Cotizaciones::where('id', $value)->exists()) {
                                $fail("El cliente seleccionado no cuenta con Cotizaciones.");
                            }
                        }
                    }
                ]
            ),
            'operacion_id' => [
                'nullable',
                'integer',
                'min:1', // siempre opcional
                function ($attribute, $value, $fail) use ($request, $tipo_request) {
                    // 🚫 Skip if not numeric or less than 1
                    if (!is_numeric($value) || $value < 1) {
                        return;
                    }
                    // Validar solo si se envió un valor
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
            'fechahora_seguimiento' => ($tipo_request === 'cancelar') ? '' : 'required|date|before_or_equal:now',
            'enviar_x_email' => (in_array($tipo_request, ['cancelar'])) ? '' : 'required|in:1,0',
            'motivo.value' => array_merge(
                in_array(trim($tipo_request), ['agregar', 'modificar']) ? ['required', 'integer', 'between:1,11'] : [''],
                [
                    function ($attribute, $value, $fail) use ($request, $tipo_request) {
                        // Required for agregar
                        if ($tipo_request === 'agregar') {
                            if (empty($value) && $value !== '0') {
                                $fail("Debe seleccionar un motivo.");
                            }
                            return;
                        }
                        // Required for modificar only if programado_b == 0
                        if ($tipo_request === 'modificar' && $request->filled('seguimiento_id')) {
                            $seguimiento = Seguimientos::find($request->seguimiento_id);
                            if ($seguimiento && $seguimiento->programado_b == 0 && (empty($value) && $value !== '0')) {
                                $fail("Debe seleccionar un motivo.");
                            }
                        }
                    }
                ]
            ),
            'medio.value' => (in_array($tipo_request, ['cancelar'])) ? '' : 'required|integer|between:1,7',
            'resultado.value' => (in_array($tipo_request, ['cancelar'])) ? '' : 'required|integer|between:1,8',
            'motivo_cancelacion.value' => ($tipo_request === 'cancelar') ? 'required|integer|between:1,6' : '',
            'email_seguimiento' => (in_array($tipo_request, ['cancelar'])) ? '' : 'required_if:enviar_x_email,1|email|nullable',
            'comentario_seguimiento' => ''
        ];
        $mensajes = [
            'seguimiento_id.required' => 'El ID del seguimiento es requerido si se va a modificar, cancelar o atender.',
            'tipo_cliente_id.required' => 'Debe seleccionar el tipo de cliente.',
            'tipo_cliente_id.in' => 'El tipo de cliente debe ser 1 o 2.',
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'operacion_id.required' => 'Debe seleccionar una operación si se desea registrar.',
            'fechahora_seguimiento.required' => 'Debe ingresar la fecha y hora del seguimiento.',
            'fechahora_seguimiento.date' => 'Ingrese una fecha y hora válida para el seguimiento.',
            'fechahora_seguimiento.before_or_equal' => 'La fecha y hora del seguimiento no puede ser mayor a la fecha actual.',
            'enviar_x_email.required' => 'Debe indicar si desea enviar por correo electrónico.',
            'enviar_x_email.in' => 'El valor de enviar_x_email debe ser 1 o 0.',
            'motivo.value.required' => 'Seleccione un motivo de seguimiento.',
            'motivo.value.integer' => 'El motivo debe ser un número válido.',
            'motivo.value.between' => 'El motivo seleccionado no es válido.',
            'medio.value.required' => 'Debe seleccionar un medio de contacto.',
            'medio.value.integer' => 'El medio debe ser un número válido.',
            'medio.value.between' => 'El medio seleccionado no es válido.',
            'resultado.value.required' => 'Debe seleccionar un resultado.',
            'resultado.value.integer' => 'El resultado debe ser un número válido.',
            'resultado.value.between' => 'El resultado seleccionado no es válido.',
            'motivo_cancelacion.value.required' => 'Debe seleccionar un motivo de cancelación.',
            'motivo_cancelacion.value.integer' => 'El motivo de cancelación debe ser un número válido.',
            'motivo_cancelacion.value.between' => 'El motivo de cancelación seleccionado no es válido.',
            'email_seguimiento.required_if' => 'Debe ingresar un correo electrónico cuando se selecciona enviar por email.',
            'email_seguimiento.email' => 'Debe ingresar un correo electrónico válido.',
            // comentario_seguimiento es opcional, no necesita mensaje
        ];

        // ✅ Run validation
        request()->validate($validaciones, $mensajes);

        $queryClosure = function () use ($request, $tipo_request) {
            // Common seguimiento data
            $seguimientoData = [
                'email_seguimiento' => $request->email_seguimiento,
                'medio_seguimiento_id' => $request->medio['value'] ?? null,
                'fechahora_seguimiento' => $request->fechahora_seguimiento ?? null,
                'comentario_seguimiento' => $request->comentario_seguimiento ?? null,
                'resultado_id' => $request->resultado['value'] ?? null,
            ];

            if (in_array($tipo_request, ['agregar', 'modificar'])) {
                $seguimientoData['motivo_id'] = $request->motivo['value'] ?? null;
            }

            if (in_array($tipo_request, ['agregar', 'atender_seguimiento_programado'])) {
                $seguimientoData['realizo_seguimiento_id'] = (int) $request->user()->id;
                $seguimientoData['fechahora_registro_seguimiento'] =  now();
            }

            // === ATENDER SEGUIMIENTO PROGRAMDO ===
            if ($tipo_request === 'atender_seguimiento_programado') {
                $seguimientoData['status'] = 2; //status de seguimiento programado atendido
                Seguimientos::where('id', $request->seguimiento_id)->update($seguimientoData);
                if ($request->enviar_x_email == 1 && $request->email_seguimiento) {
                    $this->email_sender($request->seguimiento_id, $request->email_seguimiento, 'atender seguimiento');
                }
                return $this->successResponse([
                    'message' => 'Seguimiento atendido correctamente.',
                    'seguimiento_id' => $request->seguimiento_id
                ], 200);
            }
            // === AGREGAR ===
            if ($tipo_request === 'agregar') {
                $seguimientoData['tipo_cliente_id'] = $request->tipo_cliente_id;
                $seguimientoData['clientes_id'] = $request->cliente_id;
                $seguimientoData['operaciones_id'] = $request->operacion_id ?? null;
                $seguimientoId = Seguimientos::insertGetId($seguimientoData);
                if ($request->enviar_x_email == 1 && $request->email_seguimiento) {
                    $this->email_sender($seguimientoId, $request->email_seguimiento, 'registrar seguimiento');
                }
                return $this->successResponse([
                    'message' => 'Seguimiento registrado correctamente.',
                    'seguimiento_id' => $seguimientoId
                ], 200);
            }
            // === MODIFICAR ===
            if ($tipo_request === 'modificar') {
                $seguimientoData['modifico_seguimiento_id'] = (int) $request->user()->id;
                Seguimientos::where('id', $request->seguimiento_id)->update($seguimientoData);
                if ($request->enviar_x_email == 1 && $request->email_seguimiento) {
                    $this->email_sender($request->seguimiento_id, $request->email_seguimiento, 'modificar seguimiento');
                }
                return $this->successResponse([
                    'message' => 'Seguimiento actualizado correctamente.',
                    'seguimiento_id' => $request->seguimiento_id
                ], 200);
            }
            // === CANCELAR ===
            if ($tipo_request === 'cancelar') {
                try {
                    DB::beginTransaction(); // 🧩 Start transaction
                    $cancelarData = [
                        'fechahora_cancelacion_realizado' => now(),
                        'comentario_cancelacion' => $request->comentario_cancelacion,
                        'motivo_cancelacion_id' => $request->motivo_cancelacion['value'] ?? null,
                        'cancelo_realizado_id' => (int) $request->user()->id,
                        'status' => 0,
                    ];

                    $seguimiento = Seguimientos::find($request->seguimiento_id);
                    if (!$seguimiento) {
                        return $this->errorResponse("El seguimiento no existe.", 409);
                    }
                    // 🔹 Crear nuevo seguimiento programado (si aplica)
                    if ($seguimiento->programado_b == 1) {
                        $seguimientoData = [
                            'tipo_cliente_id' => $seguimiento->tipo_cliente_id,
                            'clientes_id' => $seguimiento->clientes_id,
                            'operaciones_id' => $seguimiento->operaciones_id ?? null,
                            'email_programado' => $seguimiento->email_programado ?? null,
                            'motivo_id' => $seguimiento->motivo_id,
                            'medio_preferido_programado_id' => $seguimiento->medio_preferido_programado_id,
                            'fechahora_programada' => $seguimiento->fechahora_programada,
                            'comentario_programado' => $seguimiento->comentario_programado ?? null,
                            'programado_b' => 1,
                            'fechahora_registro_programado' => $seguimiento->fechahora_registro_programado,
                            'registro_programado_id' => $seguimiento->registro_programado_id,
                        ];
                        Seguimientos::insert($seguimientoData);
                    }

                    Seguimientos::where('id', $request->seguimiento_id)->update($cancelarData);

                    DB::commit(); // ✅ All good — commit changes

                    // 🔹 Email should happen *after* commit to avoid rollback side-effects
                    if ($request->enviar_x_email == 1 && $request->email_seguimiento) {
                        $this->email_sender($request->seguimiento_id, $request->email_seguimiento, 'cancelar seguimiento realizado');
                    }

                    return $this->successResponse([
                        'message' => 'Seguimiento cancelado correctamente.',
                        'seguimiento_id' => $request->seguimiento_id
                    ], 200);
                } catch (\Throwable $e) {
                    DB::rollBack(); // ❌ Any error = undo all DB changes
                    //\Log::error("Error al cancelar seguimiento: " . $e->getMessage());
                    return $this->errorResponse("Error al procesar la cancelación.", 500);
                }
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



    public function programar_segumientos(Request $request, $tipo_request = '')
    {
        // ✅ Validate tipo_request
        if (!in_array(trim($tipo_request), ['agregar', 'modificar', 'cancelar'])) {
            return $this->errorResponse('Error, debe especificar qué tipo de control está solicitando.', 409);
        }
        // Normalize enviar_x_email
        $request->merge([
            'enviar_x_email' => $request->enviar_x_email ? 1 : 0
        ]);
        // ✅ Base validation rules
        $validaciones = [
            'seguimiento_id' => [
                (in_array(trim($tipo_request), ['modificar', 'cancelar']) ? 'required' : ''),
                function ($attribute, $value, $fail) use ($tipo_request) {
                    if (in_array($tipo_request, ['modificar', 'cancelar']) && $value) {
                        $exists = DB::table('seguimientos')
                            ->where('id', $value)
                            ->where('status', 1)
                            ->where('programado_b', 1)
                            ->exists();
                        if (!$exists) {
                            $fail("El seguimiento indicado no existe, o no está activo para poder {$tipo_request}.");
                        }
                    }
                }
            ],
            'tipo_cliente_id' => ($tipo_request === 'cancelar') ? '' : 'required|in:1,2',
            'cliente_id' => [
                ($tipo_request === 'cancelar') ? '' : 'required',
                function ($attribute, $value, $fail) use ($request, $tipo_request) {
                    if ($tipo_request !== 'cancelar') {
                        $tipo = $request->tipo_cliente_id;
                        if ($tipo == 1 && !Clientes::where('id', $value)->exists()) {
                            $fail("El cliente seleccionado no ha sido registrado.");
                        }
                        if ($tipo == 2 && !Cotizaciones::where('id', $value)->exists()) {
                            $fail("El cliente seleccionado no cuenta con Cotizaciones.");
                        }
                    }
                }
            ],
            'operacion_id' => [
                function ($attribute, $value, $fail) use ($request, $tipo_request) {
                    if ($tipo_request !== 'cancelar' && !is_null($value) && $value !== '') {
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
            'fecha_a_contactar' => ($tipo_request === 'cancelar') ? '' : 'required|date|after_or_equal:now',
            'enviar_x_email' => ($tipo_request === 'cancelar') ? '' : 'required|in:1,0',
            'motivo.value' => ($tipo_request === 'cancelar') ? '' : 'required|integer|between:1,11',
            'medio.value' => ($tipo_request === 'cancelar') ? '' : 'required|integer|between:1,7',
            'motivo_cancelacion.value' => !($tipo_request === 'cancelar') ? '' : 'required|integer|between:1,6',
            'email' => ($tipo_request === 'cancelar') ? '' : 'required_if:enviar_x_email,1|email|nullable',
            'comentario_programado' => ''
        ];

        $mensajes = [
            'seguimiento_id.required' => 'El ID del seguimiento es requerido si se va a modificar o cancelar.',
            'cliente_id.required' => 'Seleccione un cliente.',
            'tipo_cliente_id.required' => 'Seleccione el tipo de cliente.',
            'tipo_cliente_id.in' => 'El tipo de cliente debe ser 1 o 2.',
            'fecha_a_contactar.required' => 'Ingrese la fecha a contactar.',
            'fecha_a_contactar.date' => 'Ingrese una fecha válida.',
            'fecha_a_contactar.after_or_equal' => 'La fecha debe ser igual o posterior a la actual.',
            'motivo.value.required' => 'Seleccione un motivo.',
            'medio.value.required' => 'Seleccione un medio de contacto.',
            'motivo_cancelacion.value.required' => 'Seleccione un motivo de cancelación.',
            'email.required_if' => 'Debe ingresar un correo electrónico cuando se selecciona enviar por email.',
            'email.email' => 'Debe ingresar un correo electrónico válido.'
        ];

        // ✅ Run validation
        request()->validate($validaciones, $mensajes);

        $queryClosure = function () use ($request, $tipo_request) {
            // Common seguimiento data
            $seguimientoData = [
                'tipo_cliente_id' => $request->tipo_cliente_id,
                'clientes_id' => $request->cliente_id,
                'operaciones_id' => $request->operacion_id ?? null,
                'email_programado' => $request->email,
                'motivo_id' => $request->motivo['value'] ?? null,
                'medio_preferido_programado_id' => $request->medio['value'] ?? null,
                'fechahora_programada' => $request->fecha_a_contactar ?? null,
                'comentario_programado' => $request->comentario_programado ?? null,
                'programado_b' => 1,
                'fechahora_registro_programado' => now(),
            ];

            // === AGREGAR ===
            if ($tipo_request === 'agregar') {
                $seguimientoData['registro_programado_id'] = (int) $request->user()->id;
                $seguimientoId = Seguimientos::insertGetId($seguimientoData);
                if ($request->enviar_x_email == 1 && $request->email) {
                    $this->email_sender($seguimientoId, $request->email, 'programar seguimiento');
                }
                return $this->successResponse([
                    'message' => 'Seguimiento agregado correctamente.',
                    'seguimiento_id' => $seguimientoId
                ], 200);
            }

            // === MODIFICAR ===
            if ($tipo_request === 'modificar') {
                $updateData = [
                    'email_programado' => $seguimientoData['email_programado'],
                    'motivo_id' => $seguimientoData['motivo_id'],
                    'medio_preferido_programado_id' => $seguimientoData['medio_preferido_programado_id'],
                    'fechahora_programada' => $seguimientoData['fechahora_programada'],
                    'comentario_programado' => $seguimientoData['comentario_programado'],
                    'modifico_programado_id' => (int) $request->user()->id
                ];

                Seguimientos::where('id', $request->seguimiento_id)->update($updateData);

                if ($request->enviar_x_email == 1 && $request->email) {
                    $this->email_sender($request->seguimiento_id, $request->email, 'reprogramar seguimiento');
                }

                return $this->successResponse([
                    'message' => 'Seguimiento actualizado correctamente.',
                    'seguimiento_id' => $request->seguimiento_id
                ], 200);
            }

            // === CANCELAR ===
            if ($tipo_request === 'cancelar') {
                $cancelarData = [
                    'status' => 0, // Cancelled
                    'comentario_cancelacion' => $request->comentario_cancelacion,
                    'motivo_cancelacion_id' => $request->motivo_cancelacion['value'] ?? null,
                    'cancelo_programado_id' => (int) $request->user()->id,
                    'fechahora_cancelacion_programado' => now(),
                ];
                $seguimiento = Seguimientos::where('id', $request->seguimiento_id)
                    ->where('status', 1)
                    ->where('programado_b', 1)
                    ->first();

                if (!$seguimiento) {
                    return $this->errorResponse("El seguimiento no existe o ya fue cancelado.", 409);
                }

                Seguimientos::where('id', $request->seguimiento_id)
                    ->update($cancelarData);
                if ($seguimiento->email_programado) {
                    if ($request->enviar_x_email == 1 && $request->email) {
                        $this->email_sender($request->seguimiento_id, $request->email, 'cancelar seguimiento programado');
                    }
                }
                return $this->successResponse([
                    'message' => 'Seguimiento cancelado correctamente.',
                    'seguimiento_id' => $request->seguimiento_id
                ], 200);
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

    private function email_sender($id_seguimiento = '', $email = '', $tipo = '')
    {
        $send_email_function = function () use ($id_seguimiento, $email, $tipo) {
            // 🧩 Create a fake Request instance to reuse your existing method
            $request = new \Illuminate\Http\Request([
                'id' => $id_seguimiento,
                'programado_b' => 1, // or 0, depending on what you need
            ]);
            // 📨 Get seguimiento data by calling your own public method
            $seguimiento = $this->get_seguimientos($request);

            $seguimiento = json_decode($seguimiento->getContent(), true);
            if (!$seguimiento) {
                // No seguimiento found — do nothing
                return;
            } else $seguimiento = $seguimiento[0];
            // 🧩 Create a fake Request instance to reuse your existing method
            $request = new \Illuminate\Http\Request([
                'id' => $seguimiento['clientes_id'],
                'tipo_cliente_id' => var_export($seguimiento['tipo_cliente_id'], true), // or 0, depending on what you need
                'filtrar_x_operaciones' => '1'
            ]);
            $cliente = new ClientesController();
            // 📨 Get seguimiento data by calling your own public method
            $cliente = $cliente->get_clientes_seguimientos($request);
            if (!$cliente) { // No seguimiento found — do nothing
                return;
            }
            $cliente = json_decode($cliente->getContent(), true);
            //From here, I Collect the data required to send the email
            $data['cliente'] = $cliente['nombre'];
            $data['medio'] = $seguimiento['medio_texto'];
            $data['motivo'] = $seguimiento['motivo_texto'];
            $data['fechahora_programada'] = fechahora($seguimiento['fechahora_programada']);
            $data['fechahora_registro_programado'] = fechahora($seguimiento['fechahora_registro_programado']);

            if ($seguimiento['tipo_cliente_id'] == 1 && !empty($seguimiento['operaciones_id']) && $seguimiento['operaciones_id'] > 0) {
                //cliente con operaciones
                $data['operacion'] = collect($cliente['operaciones'])
                    ->firstWhere('operacion_id', $seguimiento['operaciones_id'])['descripcion'] ?? null;
            }
            if ($seguimiento['tipo_cliente_id'] == 2) {
                //cliente bajo presupuesto
                $data['operacion'] = $cliente['operaciones'][0]['descripcion'];
            }
            $data['tipo'] = $tipo;
            // 🧠 Build email content using seguimiento info
            //Send Mail
            Mail::to(!config('app.debug') ? $email : 'hectorcrzprz@gmail.com')->send(new SeguimientoMail($data));
        };

        // Check debug mode
        if (!config('app.debug')) {
            try {
                return  $send_email_function();
            } catch (\Exception $e) {
                // Catch any DB or unexpected error
                //Log::error("Error en programar seguimientos: " . $e->getMessage());
                return $this->errorResponse("Ocurrió un error al procesar el seguimiento.", 500);
            }
        } else {
            return $send_email_function(); // directly run without try-catch
        }

        try {
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
            'status' => 'sometimes|in:0,1,2'
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

        // Load lookups once
        $motivos = $this->getMotivos();
        $medios = $this->getMedios();

        $queryClosure = function () use ($request, $motivos, $medios) {
            $query = Seguimientos::query()
                ->with(['cliente:id,nombre']); // 👈 load cliente info automatically

            if ($request->has('id')) {
                $query->where('id', $request->id);
            }

            if ($request->has('programado_b')) {
                $query->where('programado_b', $request->programado_b);
                // Base select
                $select = [
                    'id',
                    'tipo_cliente_id',
                    'clientes_id',
                    'operaciones_id',
                    'fechahora_programada',
                    DB::raw(
                        'DATE(fechahora_programada) as fecha_programada'
                    ),
                    DB::raw(
                        'TIME(fechahora_programada) as hora_programada'
                    ),
                    'motivo_id',
                    'medio_preferido_programado_id',
                    'fechahora_registro_programado',
                    'registro_programado_id',
                    'email_programado',
                    'status',
                ];
                // ✅ Add comentario_programado *only if ID was requested*
                if ($request->has('id')) {
                    $select[] = 'comentario_programado';
                }

                $query->select($select);
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

            // Retrieve data
            $seguimientos = $query->orderByDesc('id')->get();
            // Append readable text fields
            return $seguimientos->map(function ($item) use ($motivos, $medios) {
                $item->motivo_texto = $motivos[$item->motivo_id] ?? 'Desconocido';
                $item->medio_texto = $medios[$item->medio_preferido_programado_id] ?? 'Desconocido';
                return $item;
            });
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
            $seguimientos = $queryClosure();
        }

        if (isset($request->paginated_b) && $request->paginated_b == 1) {
            return $this->showAllPaginated($seguimientos);
        }
        return $this->successResponse($seguimientos, 200);
    }
}
