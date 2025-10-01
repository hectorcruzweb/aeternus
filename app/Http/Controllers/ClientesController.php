<?php

namespace App\Http\Controllers;

use App\Clientes;
use App\Cotizaciones;
use App\Http\Controllers\CementerioController;
use App\Nacionalidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientesController extends ApiController
{
    public function get_nacionalidades()
    {
        /**todas las nacionalidades */
        return Nacionalidades::get();
    }

    /**obtiene todas los clientes segun los parametros recibidos */
    public function get_clientes(Request $request)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $cliente                  = $request->cliente;
        $numero_control           = $request->numero_control;
        $status                   = $request->status;

        $id_cliente   = $request->id_cliente;
        $rfc          = $request->rfc;
        $celular      = $request->celular;
        $nacionalidad = $request->nacionalidad_id;

        $resultado = $this->showAllPaginated(
            Clientes::select(
                'id',
                'nombre',
                'direccion',
                'cp',
                'cp as direccion_fiscal_cp',
                'email',
                'rfc as rfc_raw',
                'status',
                'razon_social',
                'direccion_fiscal',
                'vivo_b as vivo_b_raw',
                'regimen_fiscal_id',
                DB::Raw('IFNULL( clientes.rfc , "N/A" ) as rfc'),
                DB::Raw('IFNULL( clientes.celular , "N/A" ) as celular'),
                DB::Raw('IF(clientes.vivo_b=1 , "Vivo","Fallecido" ) as vivo_b'),
                'nacionalidades_id',
                'generos_id',
                'servicios_gratis',
                'nota_servicios_gratis'
            )->with('regimen')->with('nacionalidad')->with('genero')->where(function ($q) use ($status) {
                if ($status != '') {
                    $q->where('clientes.status', $status);
                }
            })
                ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                    if (trim($numero_control) != '') {
                        if ($filtro_especifico_opcion == 1) {

                            $q->where('clientes.id', '=', $numero_control);
                        } else if ($filtro_especifico_opcion == 2) {

                            $q->where('clientes.rfc', '=', $numero_control);
                        } else if ($filtro_especifico_opcion == 3) {

                            $q->where('clientes.celular', '=', $numero_control);
                        } else {

                            $q->where('clientes.email', $numero_control);
                        }
                    }
                })
                ->where(function ($q) use ($cliente) {
                    if (trim($cliente) != '') {
                        $q->where('clientes.nombre', 'like', '%' . $cliente . '%');
                    }
                })
                ->where(function ($q) use ($id_cliente) {
                    if (trim($id_cliente) != '') {
                        $q->where('clientes.id', '=', $id_cliente);
                    }
                })
                ->where(function ($q) use ($celular) {
                    if (trim($celular) != '') {
                        $q->where('clientes.celular', 'like', '%' . $celular . '%');
                    }
                })
                ->where(function ($q) use ($rfc) {
                    if (trim($rfc) != '') {
                        $q->where('clientes.rfc', 'like', '%' . $rfc . '%');
                    }
                })
                ->where(function ($q) use ($nacionalidad) {
                    if (trim($nacionalidad) != '') {
                        $q->where('clientes.nacionalidades_id', '=', $nacionalidad);
                    }
                })
                /**descartando el cliente publico en general */
                //->whereNotIn('clientes.id', [1, 193])
                ->orderBy('clientes.id', 'desc')
                ->get()
        );

        //se retorna el resultado
        return $resultado;
    }

    /**obtiene el cliente por id */
    public function get_cliente_id(Request $request)
    {
        $cliente_id = $request->cliente_id;
        $resultado  =
            Clientes::select(
                '*',
                'vivo_b as vivo_b_raw',
                DB::Raw('IF(clientes.vivo_b=1 , "VIVO","FALLECIDO" ) as vivo_b'),
                'nacionalidades_id',
                'generos_id'
            )->where('clientes.id', '=', $cliente_id)->with('nacionalidad')->with('genero')->with('regimen')
            ->first();
        //se retorna el resultado
        return $resultado;
    }

    public function guardar_cliente(Request $request)
    {
        $validaciones = [
            /**personal */
            'nombre'             => 'required',
            'fecha_nac'          => '',
            'direccion'          => 'required',
            'ciudad'             => 'required',
            'estado'             => 'required',
            'nacionalidad.value' => 'required',
            'email'              => '',
            /**fiscal */
            'rfc'                => '',
            'razon_social'       => '',
            'direccion_fiscal'   => '',
            'cp'                 => '',
            'regimen.value'      => '',
        ];

        /**VALIDACIONES CONDICIONADAS*/

        if (trim($request->email)) {
            $validaciones['email'] = 'email';
        }

        if (trim($request->rfc) != '' || trim($request->razon_social) != '' || trim($request->direccion_fiscal) != '' || trim($request->cp) != '') {
            if (trim($request->rfc) != 'XAXX010101000' && trim($request->rfc) != 'XEX010101000') {
                // $validaciones['rfc']              = 'required|unique:clientes,rfc';
            } else {
                $validaciones['rfc'] = 'required';
            }

            $validaciones['razon_social']     = 'required';
            $validaciones['direccion_fiscal'] = 'required';
            $validaciones['cp']               = 'required';
            $validaciones['regimen.value']    = 'required';
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'date_format'  => 'Formato de Fecha yyyy-mm-dd',
            'required'     => 'Este dato es obligatorio',
            'email.email'  => 'Ingrese un email válido',
            'email.unique' => 'Este email ya fue registrado',
            'rfc.unique'   => 'Este RFC ya fue registrado',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        return DB::table('clientes')->insertGetId(
            [
                /**informacion fiscal */
                'generos_id'          => (int) $request->genero['value'],
                'nombre'              => $request->nombre,
                'direccion'           => $request->direccion,
                'ciudad'              => $request->ciudad,
                'estado'              => $request->estado,
                'fecha_nac'           => trim($request->fecha_nac) != '' ? date('Y-m-d H:i:s', strtotime($request->fecha_nac)) : null,
                'telefono'            => trim($request->telefono) != '' ? trim($request->telefono) : null,
                'celular'             => trim($request->celular) != '' ? trim($request->celular) : null,
                'telefono_extra'      => trim($request->telefono_extra) != '' ? trim($request->telefono_extra) : null,
                'email'               => trim($request->email) != '' ? trim($request->email) : null,
                'nacionalidades_id'   => (int) $request->nacionalidad['value'],
                /**informacion fiscal */
                'rfc'                 => trim($request->rfc) != '' ? trim($request->rfc) : null,
                'razon_social'        => trim($request->razon_social) != '' ? trim($request->razon_social) : null,
                'direccion_fiscal'    => trim($request->direccion_fiscal) != '' ? trim($request->direccion_fiscal) : null,
                'cp'                  => trim($request->cp) != '' ? trim($request->cp) : null,
                'regimen_fiscal_id'   => (int) $request->regimen['value'],
                /**datos del contacto */
                'nombre_contacto'     => $request->nombre_contacto,
                'parentesco_contacto' => $request->parentesco_contacto,
                'telefono_contacto'   => trim($request->telefono_contacto),

                'fecha_registro'      => now(),
                'registro_id'         => (int) $request->user()->id,
                'vivo_b'              => $request->status_cliente,
            ]
        );
    }

    /**modificar clientes */
    public function modificar_cliente(Request $request)
    {
        $validaciones = [
            /**personal */
            'nombre'             => 'required',
            'direccion'          => 'required',
            'ciudad'             => 'required',
            'estado'             => 'required',
            'nacionalidad.value' => 'required',
            'email'              => '',
            /**fiscal */
            'rfc'                => '',
            'razon_social'       => '',
            'direccion_fiscal'   => '',
            'cp'                 => '',
            'regimen.value'      => '',
        ];

        /**VALIDACIONES CONDICIONADAS*/
        /*
        if (trim($request->email)) {
            $validaciones['email'] = [
                'email',
                Rule::unique('clientes', 'email')->ignore($request->id_cliente_modificar),
            ];
        }
*/
        if (trim($request->rfc) != '' || trim($request->razon_social) != '' || trim($request->direccion_fiscal) != '' || trim($request->cp) != '') {
            if (trim($request->rfc) != 'XAXX010101000' && trim($request->rfc) != 'XEX010101000') {
                /*$validaciones['rfc'] = [
                    'required',
                    Rule::unique('clientes', 'rfc')->ignore($request->id_cliente_modificar),
                ];*/
            } else {
                $validaciones['rfc'] = 'required';
            }
            $validaciones['razon_social']     = 'required';
            $validaciones['direccion_fiscal'] = 'required';
            $validaciones['cp']               = 'required';
            $validaciones['regimen.value']    = 'required';
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'required'     => 'Este dato es obligatorio',
            'email.email'  => 'Ingrese un email válido',
            'email.unique' => 'Este email ya fue registrado',
            'rfc.unique'   => 'Este RFC ya fue registrado',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        $res = DB::table('clientes')->where('id', $request->id_cliente_modificar)->update(
            [
                /**informacion fiscal */
                'generos_id'          => (int) $request->genero['value'],
                'nombre'              => $request->nombre,
                'direccion'           => $request->direccion,
                'ciudad'              => $request->ciudad,
                'estado'              => $request->estado,
                'fecha_nac'           => trim($request->fecha_nac) != '' ? date('Y-m-d H:i:s', strtotime($request->fecha_nac)) : null,
                'telefono'            => trim($request->telefono) != '' ? trim($request->telefono) : null,
                'celular'             => trim($request->celular) != '' ? trim($request->celular) : null,
                'telefono_extra'      => trim($request->telefono_extra) != '' ? trim($request->telefono_extra) : null,
                'email'               => trim($request->email) != '' ? trim($request->email) : null,
                'nacionalidades_id'   => (int) $request->nacionalidad['value'],
                /**informacion fiscal */
                'rfc'                 => trim($request->rfc) != '' ? trim($request->rfc) : null,
                'razon_social'        => trim($request->razon_social) != '' ? trim($request->razon_social) : null,
                'direccion_fiscal'    => trim($request->direccion_fiscal) != '' ? trim($request->direccion_fiscal) : null,
                'cp'                  => trim($request->cp) != '' ? trim($request->cp) : null,
                'regimen_fiscal_id'   => (int) $request->regimen['value'],
                /**datos del contacto */
                'nombre_contacto'     => $request->nombre_contacto,
                'parentesco_contacto' => $request->parentesco_contacto,
                'telefono_contacto'   => trim($request->telefono_contacto),

                'fecha_modificacion'  => now(),
                'modifico_id'         => (int) $request->user()->id,
                'vivo_b'              => $request->status_cliente,
            ]
        );

        if ($res > 0) {
            return $request->id_cliente_modificar;
        } else {
            return 0;
        }
    }

    /**DELETE CLIENTES */
    public function baja_cliente(Request $request)
    {
        $cliente_id = $request->cliente_id;
        request()->validate(
            [
                'cliente_id' => 'required',
            ],
            [
                'cliente_id.required' => 'El ID del cliente es necesario.',
            ]
        );
        return DB::table('clientes')->where('id', $cliente_id)->update(
            [
                'status' => 0,
            ]
        );
    }

    //servicios gratis para clientes
    public function servicios_gratis(Request $request)
    {
        $cliente_id = $request->id;
        request()->validate(
            [
                'id'                     => 'required',
                'servicios_gratis.value' => 'required',
            ],
            [
                'required' => 'Dato necesario.',
            ]
        );
        return DB::table('clientes')->where('id', $cliente_id)->update(
            [
                'servicios_gratis'      => $request->servicios_gratis['value'],
                'nota_servicios_gratis' => $request->nota_servicios_gratis,
            ]
        );
    }

    public function alta_cliente(Request $request)
    {
        $cliente_id = $request->cliente_id;
        request()->validate(
            [
                'cliente_id' => 'required',
            ],
            [
                'cliente_id.required' => 'El ID del cliente es necesario.',
            ]
        );
        return DB::table('clientes')->where('id', $cliente_id)->update(
            [
                'status' => 1,
            ]
        );
    }

    public function get_clientes_seguimientos(Request $request)
    {
        $nameFilter            = trim($request->nombre ?? '');
        $idFilter              = trim($request->id ?? '');
        $tipoClienteFilter     = $request->filtro_especifico; // null, 1, or 2
        $queries               = [];
        $filtrar_x_operaciones = trim($request->filtrar_x_operaciones ?? '');
        $summary               = trim($request->summary ?? ''); //null or 1;
        //validations to show operaciones for seguimientos
        if (
            $filtrar_x_operaciones !== ''
            && (
                $filtrar_x_operaciones !== '1'
                || ! in_array($tipoClienteFilter, ['1', '2'], true)
                || $idFilter <= 0
            )
        ) {
            return $this->errorResponse("Validation error, code:seguimientos.", 409);
        }
        if (! is_null($tipoClienteFilter) && ! (in_array($tipoClienteFilter, ['1', '2'], true))) {
            return $this->errorResponse("Validation error, code:type_of_clients.", 409);
        }
        if ($summary !== '') {
            if (! ($summary === '1'
                && $filtrar_x_operaciones === '1'
                && $idFilter > 0
                && $tipoClienteFilter === '1')) {
                return $this->errorResponse("Validation error, code:summary.", 409);
            }
        }

        // === CLIENTES ===
        if (is_null($tipoClienteFilter) || $tipoClienteFilter == 1) {
            $clientesQuery = Clientes::select(
                'id',
                'status',
                'nombre',
                DB::raw('UPPER("cliente registrado") as tipo_cliente'),
                DB::raw('1 as tipo_cliente_id'),
                DB::raw("
                    CONCAT(
                        COALESCE(direccion, ''),
                        CASE WHEN direccion IS NOT NULL AND TRIM(direccion) != '' THEN ', ' ELSE '' END,
                        COALESCE(ciudad, ''),
                        CASE WHEN ciudad IS NOT NULL AND TRIM(ciudad) != '' THEN ', ' ELSE '' END,
                        COALESCE(estado, '')
                    ) as direccion_completa
                "),
                DB::raw("
                CASE
                    WHEN TRIM(COALESCE(celular, '')) != ''
                         AND TRIM(COALESCE(telefono, '')) != ''
                        THEN CONCAT(celular, ' / ', telefono)
                    WHEN TRIM(COALESCE(celular, '')) != ''
                        THEN celular
                    WHEN TRIM(COALESCE(telefono, '')) != ''
                        THEN telefono
                    ELSE 'N/A'
                END as telefono
            "),
                DB::raw("
                CASE
                    WHEN TRIM(COALESCE(email, '')) = ''
                        THEN NULL
                    ELSE LOWER(email)
                END as email
            ")
            )
                //->where('status', 1) // ✅ filter clientes
                ->when($nameFilter != '', function ($q) use ($nameFilter) {
                    $q->where('nombre', 'like', "%{$nameFilter}%");
                })
                ->when($idFilter != '', function ($q) use ($idFilter) {
                    $q->where('id', $idFilter);
                });

            if ($filtrar_x_operaciones === '1') {
                // 👇 only eager load operaciones if filtering specifically by clientes
                $CementerioController = new CementerioController();
                $clientes             = $clientesQuery->with(['operaciones' => function ($q) {
                    $q->where('empresa_operaciones_id', '!=', 2) // exclude type 2
                        ->with([
                            'ventasTerrenos.terrenosCuotas.cuotasCementerios', // nested cuotas
                            'serviciosFunerarios',
                            'ventasPlanes',
                            'ventasGenerales',
                        ])->orderBy('id', 'desc'); // 👈 order by operaciones.id desc
                }])->get();
                if ($clientes->isEmpty()) {
                    return $this->errorResponse('Error code: client not found.', 404); // or return null directly
                } else {
                    $clientes = $clientes->toArray();
                }
                //Filtrar x Operaciones y ID, para mostrar directo en form de seguimientos
                $operaciones_list = [];
                foreach ($clientes as &$cliente) {
                    if (isset($cliente['operaciones'])) {
                        $empresaOperaciones = [
                            1 => 'VENTA DE ESPACIO EN CEMENTERIO',
                            2 => 'CUOTA',
                            3 => 'SERVICIO FUNERARIO',
                            4 => 'PLANES FUNERARIO A FUTURO',
                            5 => 'VENTA EN GRAL.',
                        ];
                        foreach ($cliente['operaciones'] as &$operacion) {
                            //making a description
                            $descripcion       = '';
                            $ubicacion         = '';
                            $tipo_operacion    = $empresaOperaciones[$operacion['empresa_operaciones_id']];
                            $id_numero_control = '';
                            switch ($operacion['empresa_operaciones_id']) {
                                case 1:
                                    $ubicacion         = ' Ubic. ' . $CementerioController->ubicacion_texto($operacion['ventas_terrenos']['ubicacion'])['ubicacion_texto'];
                                    $descripcion       = $tipo_operacion . $ubicacion;
                                    $id_numero_control = $operacion['ventas_terrenos_id'];
                                    break;
                                case 3:
                                    $descripcion       = $tipo_operacion . ' Fallecido(a) ' . $operacion['servicios_funerarios']['nombre_afectado'];
                                    $id_numero_control = $operacion['servicios_funerarios_id'];
                                    break;
                                case 4:
                                    $descripcion       = $tipo_operacion . ' ' . $operacion['ventas_planes']['nombre_original'];
                                    $id_numero_control = $operacion['ventas_planes_id'];
                                    break;
                                case 5:
                                    $descripcion       = $tipo_operacion;
                                    $id_numero_control = $operacion['ventas_generales_id'];
                                    break;
                                default:
                                    $descripcion = '';
                                    break;
                            }
                            $descripcion .= ', ' . fecha_abr($operacion['fecha_operacion']) . '.';

                            $operaciones_list[] = [
                                'operacion_id'      => $operacion['id'],
                                'id_numero_control' => $id_numero_control,
                                'saldo'             => '$' . number_format($operacion['saldo'], 2, '.', ',') . ' MXN', // example static value
                                'descripcion'       => $descripcion,                                                   // example static value
                                'status'            => $operacion['status'],
                                'cuotas'            => null,
                            ];

                            if ($operacion['empresa_operaciones_id'] == 1) {
                                $operaciones_cuota = [];
                                //could have cuotas de cementerio
                                if (isset($operacion['ventas_terrenos']['terrenos_cuotas'])) {
                                    foreach ($operacion['ventas_terrenos']['terrenos_cuotas'] as $cuota) {
                                        $ubicacion           = ! $summary ? $ubicacion : '';
                                        $operaciones_cuota[] = [
                                            'operacion_id'      => $cuota['id'],
                                            'id_numero_control' => $cuota['cuotas_cementerios']['id'],
                                            'saldo'             => '$' . number_format($cuota['saldo'], 2, '.', ',') . ' MXN',                                                                    // example static value
                                            'descripcion'       => $empresaOperaciones[$cuota['empresa_operaciones_id']] . ', ' . $cuota['cuotas_cementerios']['descripcion'] . $ubicacion . '.', // example static value
                                            'status'            => $cuota['status'],
                                        ];
                                    }
                                }
                                if (! $summary) {
                                    $operaciones_list = array_merge($operaciones_list, $operaciones_cuota);
                                } else {
                                    $operaciones_list[count($operaciones_list) - 1]['cuotas'] = $operaciones_cuota;
                                }
                            }
                        }
                    }
                    $cliente['operaciones'] = $operaciones_list;
                    return $this->successResponse($cliente, 200);
                }
            }
            $queries[] = $clientesQuery;
        }
        // === COTIZACIONES ===
        if (is_null($tipoClienteFilter) || $tipoClienteFilter == 2) {
            $cotizacionesQuery = Cotizaciones::select(
                'id',
                'status',
                'cliente_nombre as nombre',
                DB::raw('UPPER("bajo cotización") as tipo_cliente'),
                DB::raw('2 as tipo_cliente_id'),
                DB::raw('"N/A, CLIENTE EN PROCESO DE COTIZACIÓN" as direccion_completa'),
                DB::raw("
                CASE
                    WHEN TRIM(COALESCE(cliente_telefono, '')) != ''
                        THEN cliente_telefono
                    ELSE 'N/A'
                END as telefono
            "),
                DB::raw("
                CASE
                    WHEN TRIM(COALESCE(cliente_email, '')) = ''
                        THEN NULL
                    ELSE LOWER(cliente_email)
                END as email
            ")
            )
                //->where('status', 1) // ✅ filter cotizaciones
                ->when($nameFilter != '', function ($q) use ($nameFilter) {
                    $q->where('cliente_nombre', 'like', "%{$nameFilter}%");
                })
                ->when($idFilter != '', function ($q) use ($idFilter) {
                    $q->where('id', $idFilter);
                });
            if ($filtrar_x_operaciones === '1') {
                $cotizacionesQuery = $cotizacionesQuery->first();

                if ($cotizacionesQuery) {
                    $cotizacionesQuery = $cotizacionesQuery->toArray();
                } else {
                    return $this->errorResponse('Error code: client not found.', 404); // or return null directly
                }

                $cotizacionesQuery['operaciones'][] = [
                    'operacion_id'      => $cotizacionesQuery['id'],
                    'id_numero_control' => $cotizacionesQuery['id'],
                    'saldo'             => ' N/A',                     // example static value
                    'descripcion'       => "Cotización de servicio.", // example static value
                    'status'            => $cotizacionesQuery['status'],
                ];
                return $this->successResponse($cotizacionesQuery, 200);
            }
            $queries[] = $cotizacionesQuery;
        }
        // === UNION MODE ===
        if (count($queries) == 2) {
            $combinedQuery = $queries[0]->unionAll($queries[1]);
        } else {
            $combinedQuery = $queries[0];
        }
        // === FINAL EXECUTION ===
        $sql = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($combinedQuery->getQuery())
            ->orderBy('id', 'asc')
            ->orderByRaw("CASE tipo_cliente_id WHEN 1 THEN 0 ELSE 1 END ASC");

        return $this->showAllPaginated($sql->get());
    }
}
