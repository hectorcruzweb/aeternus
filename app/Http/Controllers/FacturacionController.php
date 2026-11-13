<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Calcula timbres disponibles, CFDI con adeudo, facturado/cubierto del mes y vigencia de firma; construye
 * sus reportes.
 * Dónde ajustar: getTimbresDashboard y resumenRecargasTimbres para lotes; consultaCfdisAdeudosBase para
 * saldo fiscal; getFacturacionMesDashboard para importes del card; getVigenciaFirmaDashboard para
 * facturacion.fecha_vencimiento.
 * Relacionado: movimientos_timbres; cfdis_tipo_relacion; Facturacion.vue; exportadores Cfdis* y Timbres*
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */


namespace App\Http\Controllers;

use App\Cfdis;
use App\cfdi\ClienteFormasDigitales;
use App\Facturacion;
use App\Funeraria;
use App\MetodosPago;
use App\Operaciones;
use App\SatFormasPago;
use App\SatPais;
use App\SATProductosServicios;
use App\SATRegimenes;
use App\SatUnidades;
use App\SatUsosCfdi;
use App\TipoComprobantes;
use App\TiposRelacion;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS2D;
use PDF;
use SoapClient;
use Spatie\ArrayToXml\ArrayToXml;
use ZipArchive;

class FacturacionController extends ApiController
{
    /**get tipo de comprobantes */
    public function get_tipos_comprobante()
    {
        $datos = TipoComprobantes::whereNotIn('id', [3, 4])->orderBy('id', 'asc')->get();
        foreach ($datos as $key => &$dato) {
            $dato['tipo'] = $dato['tipo'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_metodos_pago()
    {
        $metodos = MetodosPago::orderBy('id', 'asc')->get();

        foreach ($metodos as $key => &$metodo) {
            $metodo['metodo'] = $metodo['metodo'] . ' (' . $metodo['clave'] . ')';
        }

        return $metodos;
    }

    public function get_sat_formas_pago()
    {
        return SatFormasPago::orderBy('id', 'asc')->get();
    }

    public function get_tipos_relacion()
    {
        return TiposRelacion::orderBy('id', 'asc')->get();
    }

    public function get_claves_productos_sat()
    {
        /**todos menos el tipo de servicios de facturacion */
        // $datos = SATProductosServicios::whereNotIn('clave', ['84111506', '42262102'])->get();
        $datos = SATProductosServicios::get();

        foreach ($datos as $key => &$dato) {
            $dato['clave_original'] = $dato['clave'];
            $dato['clave']          = $dato['descripcion'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_sat_unidades()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatUnidades::get();

        foreach ($datos as $key => &$dato) {
            $dato['clave_original'] = $dato['clave'];

            $dato['clave'] = $dato['unidad'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_usos_cfdi()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatUsosCfdi::where('aplica_b', 1)->get();

        foreach ($datos as $key => &$dato) {
            $dato['uso'] = $dato['uso'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_sat_paises()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatPais::get();

        foreach ($datos as $key => &$dato) {
            $dato['pais'] = $dato['pais'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_empresa_tipo_operaciones()
    {
        /**los diferentes tipos de operaciones que maneja la empresa */
        $datos = [
            [
                'id'    => 1,
                'tipo'  => 'Venta de terrenos',
                'ver_b' => 1,
            ],
            [
                'id'    => 2,
                'tipo'  => 'Mantenimiento en cementerio',
                'ver_b' => 1,
            ],
            [
                'id'    => 3,
                'tipo'  => 'Servicios funerarios',
                'ver_b' => 1,
            ],
            [
                'id'    => 4,
                'tipo'  => 'Venta de plan funerario a futuro',
                'ver_b' => 1,
            ],
            [
                'id'    => 5,
                'tipo'  => 'Ventas en general',
                'ver_b' => 1,
            ],
        ];

        $operaciones = [];
        foreach ($datos as $key => $value) {
            if ($value['ver_b'] == 1) {
                array_push($operaciones, $value);
            }
        }
        return $operaciones;
    }

    /**control de obtencion de operaciones para la facturacion */

    /**
     * id_operacion_local se refiere al id de la venta de propiedad, plan, servicios funerarios, etc, no a la tabla de operaciones
     */
    public function get_operaciones(Request $request, $id_operacion_local = 'all', $paginated = false)
    {
        $cliente           = $request->cliente;
        $numero_control    = $request->numero_control;
        $fecha_inicio      = $request->fecha_inicio;
        $fecha_fin         = $request->fecha_fin;
        $tipo_operacion_id = $request->tipo_operacion_id;

        $resultado_query = Operaciones::select(
            'empresa_operaciones_id',
            'subtotal',
            'tasa_iva',
            'descuento',
            'total',
            'operaciones.id',
            'operaciones.id as operacion_id',
            'clientes.id as cliente_id',
            'clientes.nombre',
            'fecha_operacion',
            'ventas_terrenos_id',
            'servicios_funerarios_id',
            'cuotas_cementerio_id',
            'ventas_planes_id',
            'ventas_generales_id',
            DB::raw(
                '(NULL) AS fecha_operacion_texto'
            ),
            DB::raw(
                '(NULL) AS tipo_operacion_texto'
            ),
            DB::raw(
                '(NULL) AS conceptos'
            )
        )
            ->with('movimiento_operacion_inventario.articulos_operacion:movimientos_inventario_id,cantidad,plan_b,descuento_b,facturable_b,costo_neto_normal,costo_neto_descuento,articulos_id,sat_productos_servicios.id as sat_producto_servicio_id,sat_productos_servicios.clave as sat_producto_servicio_clave,sat_productos_servicios.descripcion as sat_producto_servicio_descripcion,articulos.descripcion as articulo_descripcion,sat_unidades.id as sat_unidad_id,sat_unidades.unidad as sat_unidad,sat_unidades.clave as sat_unidad_clave')
            ->with('conceptos_temporales')
            ->where('operaciones.status', '<>', 0)
            ->where(function ($q) use ($tipo_operacion_id) {
                if ($tipo_operacion_id > 0) {
                    $q->where('operaciones.empresa_operaciones_id', '=', $tipo_operacion_id);
                }
            })
            ->with('venta_terreno:id,ubicacion')
            ->with('venta_plan')
            ->join('clientes', 'clientes.id', '=', 'operaciones.clientes_id')
            ->where('nombre', 'like', '%' . $cliente . '%')
            ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                if (trim($fecha_inicio) != '' && trim($fecha_fin) != '') {
                    if ($fecha_fin != $fecha_inicio) {
                        $q->whereBetween('fecha_operacion', [$fecha_inicio, $fecha_fin]);
                    } else {
                        $q->whereDate('fecha_operacion', '=', $fecha_inicio);
                    }
                }
            })
            ->where(function ($q) use ($numero_control) {
                if ($numero_control > 0) {
                    //cuota de mantenimiento
                    $q->where('ventas_terrenos_id', '=', $numero_control);
                    $q->orWhere('ventas_planes_id', '=', $numero_control);
                    $q->orWhere('servicios_funerarios_id', '=', $numero_control);
                    $q->orWhere('ventas_generales_id', '=', $numero_control);
                }

                if ($numero_control == 2) {
                    //si es de pago de cuota de mantenimiento traigo solo los pagados
                    $q->where('ventas_terrenos_id', '=', $numero_control);
                }
            })
            ->orderBy('operaciones.fecha_operacion', 'desc')
            ->get();

        $resultado = [];
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado       = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado       = &$resultado_query;
        }

        $tipos_de_operaciones = $this->get_empresa_tipo_operaciones();
        /**formanteando datos */

        /**obtengo el cementerio */
        $cementerio_controller = new CementerioController();

        $datos_cementerio = $cementerio_controller->get_cementerio();

        foreach ($resultado as $index_operacion => &$operacion) {
            $operacion['fecha_operacion_texto'] = fecha_abr($operacion['fecha_operacion']);

            /**asignando el tipo de operacion */
            foreach ($tipos_de_operaciones as $key => $tipo) {
                # code...
                if ($operacion['empresa_operaciones_id'] == $tipo['id']) {
                    $operacion['tipo_operacion_texto'] = $tipo['tipo'];
                    break;
                }
            }

            $conceptos = [];

            /**creando el arreglo con los conceptos que puede facturar el usuario segun la operacion*/

            if ($operacion['empresa_operaciones_id'] == 5) {
                /**es venta en gral */
                if (isset($operacion['conceptos_temporales'])) {
                    foreach ($operacion['conceptos_temporales'] as $id => $concepto) {
                        if ($concepto['facturable_b'] == 1) {
                            /**solo los que  */
                            array_push($conceptos, [
                                'clave_sat'             => ['value' => $concepto['sat_producto_servicio_id'], 'label' => $concepto['sat_producto_servicio_descripcion'] . ' (' . $concepto['sat_producto_servicio_clave'] . ')'],
                                'unidad_sat'            => ['value' => $concepto['sat_unidad_id'], 'label' => $concepto['sat_unidad'] . ' (' . $concepto['sat_unidad_clave'] . ')'],
                                "cantidad"              => $concepto['cantidad'],
                                "descripcion"           => $concepto['descripcion'],
                                'descuento_b'           => $concepto['descuento_b'] == 1 ? ['value' => 1, 'label' => 'SI'] : ['value' => 0, 'label' => 'NO'],
                                'modifica_b'            => 0,
                                'concepto_operacion_id' => $operacion['operacion_id'],
                                'precio_neto'           => $concepto['costo_neto_normal'],
                                'precio_descuento'      => $concepto['costo_neto_descuento'],
                            ]);
                        }
                    }
                }
            }

            if ($operacion['empresa_operaciones_id'] == 3) {
                /**es servicio funerario */
                if (isset($operacion['movimiento_operacion_inventario']['articulos_operacion'])) {
                    foreach ($operacion['movimiento_operacion_inventario']['articulos_operacion'] as $id => $concepto) {
                        if ($concepto['facturable_b'] == 1) {
                            /**solo los que  */
                            array_push($conceptos, [
                                'clave_sat'             => ['value' => $concepto['sat_producto_servicio_id'], 'label' => $concepto['sat_producto_servicio_descripcion'] . ' (' . $concepto['sat_producto_servicio_clave'] . ')'],
                                'unidad_sat'            => ['value' => $concepto['sat_unidad_id'], 'label' => $concepto['sat_unidad'] . ' (' . $concepto['sat_unidad_clave'] . ')'],
                                "cantidad"              => $concepto['cantidad'],
                                "descripcion"           => $concepto['articulo_descripcion'],
                                'descuento_b'           => $concepto['descuento_b'] == 1 ? ['value' => 1, 'label' => 'SI'] : ['value' => 0, 'label' => 'NO'],
                                'modifica_b'            => 0,
                                'concepto_operacion_id' => $operacion['operacion_id'],
                                'precio_neto'           => $concepto['costo_neto_normal'],
                                'precio_descuento'      => $concepto['costo_neto_descuento'],
                            ]);
                        }
                    }
                }
            } elseif ($operacion['empresa_operaciones_id'] == 1) {
                /**es venta de terrenos */
                array_push($conceptos, [
                    'clave_sat'             => ['value' => 5, 'label' => 'Productos de entierro o tumbas (48131500)'],
                    'unidad_sat'            => ['value' => 2, 'label' => 'Pieza (H87)'],
                    "cantidad"              => 1,
                    "descripcion"           => 'Espacio en cementerio (Ubicación ' . $cementerio_controller->ubicacion_texto($operacion['venta_terreno']['ubicacion'], $datos_cementerio)['ubicacion_texto'] . ')',
                    'descuento_b'           => $operacion['descuento'] > 0 ? ['value' => 1, 'label' => 'SI'] : ['value' => 0, 'label' => 'NO'],
                    'modifica_b'            => 0,
                    'concepto_operacion_id' => $operacion['operacion_id'],
                    'precio_neto'           => $operacion['descuento'] > 0 ? round((($operacion['subtotal']) * (1 + ($operacion['tasa_iva'] / 100))), 2, PHP_ROUND_HALF_UP) : $operacion['total'],
                    'precio_descuento'      => $operacion['descuento'] > 0 ? $operacion['total'] : 0,
                ]);
            } elseif ($operacion['empresa_operaciones_id'] == 2) {
                /**Mantenimiento de terrenos */
                array_push($conceptos, [
                    'clave_sat'             => ['value' => 2, 'label' => 'Servicios funerarios y asociados (85171500)'],
                    'unidad_sat'            => ['value' => 1, 'label' => 'Unidad de servicio (E48)'],
                    "cantidad"              => 1,
                    "descripcion"           => 'Cuota de mantenimiento (Ubicación ' . $cementerio_controller->ubicacion_texto($operacion['venta_terreno']['ubicacion'], $datos_cementerio)['ubicacion_texto'] . ') Periodo ' . fecha_abr($operacion['fecha_operacion']),
                    'descuento_b'           => $operacion['descuento'] > 0 ? ['value' => 1, 'label' => 'SI'] : ['value' => 0, 'label' => 'NO'],
                    'modifica_b'            => 1,
                    'concepto_operacion_id' => $operacion['operacion_id'],
                    'precio_neto'           => $operacion['descuento'] > 0 ? round((($operacion['subtotal']) * (1 + ($operacion['tasa_iva'] / 100))), 2, PHP_ROUND_HALF_UP) : $operacion['total'],
                    'precio_descuento'      => $operacion['descuento'] > 0 ? $operacion['total'] : 0,
                ]);
            } elseif ($operacion['empresa_operaciones_id'] == 4) {
                /**es venta de planes funerarios */
                array_push($conceptos, [
                    'clave_sat'             => ['value' => 2, 'label' => 'Servicios funerarios y asociados (85171500)'],
                    'unidad_sat'            => ['value' => 1, 'label' => 'Unidad de servicio (E48)'],
                    "cantidad"              => 1,
                    "descripcion"           => 'Plan Funerario a Futuro / ' . $operacion['venta_plan']['nombre_original'],
                    'descuento_b'           => $operacion['descuento'] > 0 ? ['value' => 1, 'label' => 'SI'] : ['value' => 0, 'label' => 'NO'],
                    'modifica_b'            => 1,
                    'concepto_operacion_id' => $operacion['operacion_id'],
                    'precio_neto'           => $operacion['descuento'] > 0 ? round((($operacion['subtotal']) * (1 + ($operacion['tasa_iva'] / 100))), 2, PHP_ROUND_HALF_UP) : $operacion['total'],
                    'precio_descuento'      => $operacion['descuento'] > 0 ? $operacion['total'] : 0,
                ]);
            }

            /**actualizo los conceptos */
            $operacion['conceptos'] = $conceptos;

            /**remuevo el arreglo original de datos de conceptos ya que en la parte de arriba organizo los conceptos a conveniencia */
            unset($operacion['movimiento_operacion_inventario']);
            unset($operacion['venta_plan']);
            unset($operacion['venta_terreno']);
        }

        return $resultado_query;
    }

    public function GenerarXmlCfdi(Request $request, $folio_para_asignar = '')
    {
        if ($folio_para_asignar == '') {
            /**no hay folio y se debe de cancelar el servicio */
            return 'El folio para el cfdi no es válido';
        }
        /**obteniendo claves de los catalogos */
        $forma_pago = SatFormasPago::where('id', '=', $request->forma_pago['value'])->first();
        if (is_null($forma_pago)) {
            return 'No se encontró la forma de pago que se está utilizando.';
        }

        $tipo_comprobante = TipoComprobantes::where('id', '=', $request->tipo_comprobante['value'])->first();
        if (is_null($tipo_comprobante)) {
            return 'No se encontró el tipo de comprobante que se está utilizando.';
        }
        $metodo_pago = MetodosPago::where('id', '=', $request->metodo_pago['value'])->first();
        if (is_null($metodo_pago)) {
            return 'No se encontró el método de pago que se está utilizando.';
        }
        $uso_cfdi = SatUsosCfdi::where('id', '=', $request->uso_cfdi['value'])->first();
        if (is_null($uso_cfdi)) {
            return 'No se encontró el uso de cfdi que se está utilizando.';
        }

        /**obtengo los datos de la funeraria */
        $datos_funeraria = Funeraria::first();

        /**checabndo que tipo de comprobante es */
        $subtotal                       = 0.0;
        $descuento                      = 0.0;
        $iva_trasladado                 = 0.0;
        $total                          = 0.0;
        $serie                          = '';
        $claves_productos_servicios_sat = $this->get_claves_productos_sat();
        $get_sat_unidades               = $this->get_sat_unidades();
        $tasa_iva                       = number_format((float) round($request->tasa_iva / 100, 2), 6, '.', '');

        /**NODO INICIAL DE CONCEPTOS */
        $conceptos['cfdi:Concepto'] = [];
        $total_base_impuestos       = 0;
        /**VERIFICANDO QUE TIPO DE COMPROBANTE SE VA A REALIZAR */
        if ($request->tipo_comprobante['value'] == '1' || $request->tipo_comprobante['value'] == '2') {
            /**validnado que tenga conceptos si es de tipo ingreso o egreso */

            if (count($request->conceptos) == 0) {
                /**no hay conceptos y no se puede seguir con el timbrado */
                return 'Se deben agregar conceptos para el CFDI.';
            }

            if ($request->tipo_comprobante['value'] == '1') {
                /**ingreso */
                $serie = 'I';
            } else {
                /**egreso */
                $serie = 'E';
            }

            /**CREANDO LOS NODOS DE CONCEPTO */
            $iva_trasladado       = 0;
            $total_base_impuestos = 0;
            foreach ($request->conceptos as $key => $concepto) {
                //cargo la clave del sat
                $esta_clave_sat = false;
                $clave_sat      = '';
                foreach ($claves_productos_servicios_sat as $key => $clave) {
                    if ($clave->id == $concepto['clave_sat']['value']) {
                        $esta_clave_sat = true;
                        $clave_sat      = $clave['clave_original'];
                        break;
                    }
                }
                if (! $esta_clave_sat) {
                    return 'No se encontró la clave de producto del Sat.';
                }
                //cargo la unidad
                $esta_clave_unidad = false;
                $clave_unidad      = '';

                foreach ($get_sat_unidades as $key => $clave) {
                    if ($clave->id == $concepto['unidad_sat']['value']) {
                        $esta_clave_unidad = true;
                        $clave_unidad      = $clave['clave_original'];
                        break;
                    }
                }

                if (! $esta_clave_unidad) {
                    return 'No se encontró la clave de unidad del Sat.';
                }

                $ValorUnitarioPrecioNeto      = round((float) ($concepto['precio_neto'] / (1 + ($request->tasa_iva / 100))), 2, PHP_ROUND_HALF_UP);
                $ValorUnitarioPrecioDescuento = round((float) $concepto['precio_descuento'] / (1 + ($request->tasa_iva / 100)), 2, PHP_ROUND_HALF_UP);
                $descuento_concepto           = $concepto['descuento_b']['value'] == 1 ? round(($ValorUnitarioPrecioNeto - $ValorUnitarioPrecioDescuento), 2) : 0;

                $subtotal += $ValorUnitarioPrecioNeto * $concepto['cantidad'];
                $descuento += $descuento_concepto * $concepto['cantidad'];
                $iva_trasladado += round((($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad']) * $tasa_iva, 2);
                $total_base_impuestos += number_format((float) round(($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad'], 2), 2, '.', '');
                $total += round((($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad']) * (1 + $tasa_iva), 2);
                array_push(
                    $conceptos['cfdi:Concepto'],
                    [
                        '_attributes'    =>
                        [
                            'Cantidad'      => $concepto['cantidad'],
                            'ClaveProdServ' => trim($clave_sat),
                            'ClaveUnidad'   => $clave_unidad,
                            'Descripcion'   => normalizeDescription(($concepto['descripcion'])),
                            'Importe'       => number_format((float) round($concepto['cantidad'] * $ValorUnitarioPrecioNeto, 2), 2, '.', ''),
                            'ValorUnitario' => number_format((float) round($ValorUnitarioPrecioNeto, 2), 2, '.', ''),
                            'Descuento'     => number_format((float) round($descuento_concepto * $concepto['cantidad'], 2), 2, '.', ''),
                            'ObjetoImp'     => '02',
                            /*
                                    01	No objeto de impuesto.	19/10/2021
                                    02	Sí objeto de impuesto.	19/10/2021
                                    03	Sí objeto del impuesto y no obligado al desglose.
                                */
                        ],
                        'cfdi:Impuestos' => [
                            'cfdi:Traslados' => [
                                'cfdi:Traslado' => [
                                    '_attributes' => [
                                        'Base'       => number_format((float) round(($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad'], 2), 2, '.', ''),
                                        'Importe'    => number_format((float) round((($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad']) * $tasa_iva, 2), 2, '.', ''),
                                        'Impuesto'   => "002", //IVA
                                        'TasaOCuota' => $tasa_iva,
                                        'TipoFactor' => "Tasa",
                                    ],
                                ],
                            ],
                        ],
                    ]
                );
                /**verificando si se debe de quitar el nodo de Descuento */
                if ($descuento_concepto <= 0) {
                    /**no hay descuento y debe quitarse*/
                    unset($conceptos['cfdi:Concepto'][count($conceptos['cfdi:Concepto']) - 1]['_attributes']['Descuento']);
                }
            }
        } else if ($request->tipo_comprobante['value'] == '5') {
            /**pago */
            $serie = 'P';

            foreach ($request->cfdis_a_pagar as $key => $cfdi) {
                $total += $cfdi['monto_pago'];
            }
            array_push(
                $conceptos['cfdi:Concepto'],
                [
                    '_attributes' =>
                    [
                        'Cantidad'      => 1,
                        'ClaveProdServ' => '84111506',
                        'ClaveUnidad'   => 'ACT',
                        'Descripcion'   => 'Pago',
                        'Importe'       => 0.0,
                        'ObjetoImp'     => '01',
                        'ValorUnitario' => 0.0,
                    ],
                ]
            );
            /**agregago los pagos relacionados */
        } else {
            /**error */
            return 'No se encontró tipo de comprobante que se está utilizando.';
        }

        //Determino el atributo DomicilioFiscalReceptor
        //aqui trabajo
        //pub en general local o extranjero
        $DomicilioFiscalReceptor = '82140'; //pongo el rfc de la empresa
        $RegimenFiscalReceptor   = '616';
        if ($request->tipo_rfc['value'] == 1) {
            if (ENV('APP_ENV') != 'local') {
                $RegimenFiscalReceptor = DB::table('sat_regimenes')->where('id', $request->regimen['value'])->first()->clave;
            }
        }
        if ($request->tipo_rfc['value'] == 1) {
            //es DomicilioFiscalReceptor del cliente, tomado del catalogo de clientes
            if (ENV('APP_ENV') != 'local') {
                //checo si es tipo pub en gral
                if ($request->rfc != trim('XAXX010101000') && $request->rfc != trim('XEXX010101000')) {
                    //pub en gral
                    $DomicilioFiscalReceptor = $request->direccion_fiscal_cp;
                }
            }
        }
        /**creando nodos de EMISOR Y RECEPTOR Y AGREGANDO LOC CONCEPTOS QUE VAN A APLICAR A ESTE CFDI*/
        $array = [
            /* 'cfdi:InformacionGlobal'         => [
                '_attributes' => [
                    'Periodicidad'     => ENV('APP_ENV') == 'local' ? '01' : strtoupper($request->rfc),
                    'Meses'  => ENV('APP_ENV') == 'local' ? '02' : strtoupper($request->mes_global),
                    'Año' => ENV('APP_ENV') == 'local' ? '2022' : strtoupper($request->year_gloabal),
                ],
            ],
            */
            'cfdi:CfdiRelacionados' => $request->array_cfdis_a_relacionar_xml,
            'cfdi:Emisor'           => [
                '_attributes' => [
                    'RegimenFiscal' => '601',
                    'Rfc'           => ENV('APP_ENV') == 'local' ? 'EWE1709045U0' : strtoupper($datos_funeraria['rfc']),
                    'Nombre'        => ENV('APP_ENV') == 'local' ? 'ESCUELA WILSON ESQUIVEL' : strtoupper($datos_funeraria['razon_social']),
                ],
            ],
            'cfdi:Receptor'         => [
                '_attributes' => [
                    'Rfc'                     => ENV('APP_ENV') == 'local' ? 'XEXX010101000' : strtoupper($request->rfc),
                    'Nombre'                  => ENV('APP_ENV') == 'local' ? 'público en general' : strtoupper($request->razon_social),
                    'UsoCFDI'                 => $request->tipo_comprobante['value'] == '5' ? 'CP01' : $uso_cfdi['clave'],
                    'RegimenFiscalReceptor'   => $RegimenFiscalReceptor,
                    'DomicilioFiscalReceptor' => $DomicilioFiscalReceptor, //codigo postal del emisor
                ],
            ],
            'cfdi:Conceptos'        => $conceptos,
            'cfdi:Impuestos'        => [
                '_attributes'    => [
                    'TotalImpuestosTrasladados' => number_format((float) round($iva_trasladado, 2), 2, '.', ''),
                ],
                'cfdi:Traslados' => [
                    'cfdi:Traslado' => [
                        '_attributes' => [
                            'Base'       => $total_base_impuestos,
                            'Importe'    => number_format((float) round($iva_trasladado, 2), 2, '.', ''),
                            'Impuesto'   => "002",
                            'TasaOCuota' => $tasa_iva,
                            'TipoFactor' => "Tasa",
                        ],
                    ],
                ],
            ],
            //aqui traba
            'cfdi:Complemento'      => [
                'pago20:Pagos' => [
                    '_attributes'    => [
                        'Version' => '2.0',
                    ],
                    'pago20:Totales' => [
                        '_attributes' => [
                            'MontoTotalPagos' => number_format((float) round($total, 2), 2, '.', ''),
                        ],
                    ],
                    'pago20:Pago'    => [
                        '_attributes'             => [
                            'FechaPago'    => $request->fecha_pago . 'T12:00:00',
                            'FormaDePagoP' => $forma_pago['clave'],
                            'MonedaP'      => 'MXN',
                            'Monto'        => number_format((float) round($total, 2), 2, '.', ''),
                            'TipoCambioP'  => '1',
                        ],
                        'pago20:DoctoRelacionado' => $request->array_cfdis_a_pagar_xml,

                    ],
                ],
            ],
        ];

        /**AQUI AGREGO LOS PAGOS QUE SE GENERARON */
        $numero_serie = 0;
        if ($request->tipo_comprobante['value'] == 1) {
            $schema_location = 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd';
            $numero_serie    = Cfdis::where('sat_tipo_comprobante_id', '=', 1)->count();
        } else if ($request->tipo_comprobante['value'] == 2) {
            $schema_location = 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd';
            $numero_serie    = Cfdis::where('sat_tipo_comprobante_id', '=', 2)->count();
        } else {
            if ($request->tipo_comprobante['value'] == 5) {
                $numero_serie    = Cfdis::where('sat_tipo_comprobante_id', '=', 5)->count();
                $schema_location = 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/Pagos20 http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos20.xsd';
            }
        }
        $comprobante = [
            'xmlns:cfdi'         => 'http://www.sat.gob.mx/cfd/4',
            'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
            'xmlns:pago20'       => 'http://www.sat.gob.mx/Pagos20',
            'Certificado'        => '',
            'Fecha'              => str_replace(" ", "T", date("Y-m-d H:i:s")),
            'Folio'              => $folio_para_asignar,
            'FormaPago'          => $forma_pago['clave'],
            'LugarExpedicion'    => $datos_funeraria['cp'],
            'MetodoPago'         => $metodo_pago['clave'],
            'Moneda'             => $request->tipo_comprobante['value'] != '5' ? "MXN" : 'XXX',
            'NoCertificado'      => '',
            'Sello'              => '',
            'Serie'              => $serie . ($numero_serie),
            'SubTotal'           => $request->tipo_comprobante['value'] != '5' ? number_format((float) round($subtotal, 2), 2, '.', '') : '0',
            'Descuento'          => number_format((float) round($descuento, 2), 2, '.', ''),
            'TipoCambio'         => '1',
            'TipoDeComprobante'  => $tipo_comprobante['clave'],
            'Total'              => $request->tipo_comprobante['value'] != '5' ? number_format((float) round($total, 2), 2, '.', '') : '0',
            'Version'            => '4.0',
            'xsi:schemaLocation' => $schema_location,
            'Exportacion'        => '01', //No aplica
            /*
            “Exportacion” y se pretende incorporar un catálogo de operaciones para considerar si
            se trata de una exportación definitiva (02),
            temporal (03)
            o no se trata de este tipo de operaciones (01).
        */
        ];

        /**removiendo cfdis relacionados en caso de no aplica */
        if ($request->tipo_relacion['value'] <= 0) {
            /**no hay relacioandos y se quita el nodo */
            unset($array['cfdi:CfdiRelacionados']);
        }

        if ($request->tipo_comprobante['value'] != '5') {
            unset($comprobante['xmlns:pago20']);
            unset($array['cfdi:Complemento']);
        } else {
            unset($comprobante['FormaPago']);
            unset($comprobante['TipoCambio']);
            unset($comprobante['MetodoPago']);
            unset($array['cfdi:Impuestos']);
        }

        if ($descuento == 0) {
            /**no hay descuento y debe quitarse */
            unset($comprobante['Descuento']);
        }

        $result = ArrayToXml::convert($array, [
            'rootElementName' => 'cfdi:Comprobante',
            '_attributes'     => $comprobante,
        ], true, 'UTF-8');

        //$nombre_temporal = rand(0, 100000) . '.xml';
        //$nombre_temporal = 'ok.xml';
        $nombre_temporal = $folio_para_asignar . '.xml';
        Storage::disk('cfdis')->put($nombre_temporal, $result);

        $arreglo_de_datos_a_retornar['nombre_xml'] = $nombre_temporal;
        $arreglo_de_datos_a_retornar['subtotal']   = $subtotal;
        $arreglo_de_datos_a_retornar['descuento']  = $descuento;
        $arreglo_de_datos_a_retornar['total']      = $total;

        return $arreglo_de_datos_a_retornar;
    }

    public function timbrar_cfdi(Request $request)
    {
        $validaciones = [
            /**validacion de datos para el cfdi */
            'id_cliente'               => 'required|integer|min:1',
            'cliente'                  => 'required',
            'tipo_rfc.value'           => 'required',
            'rfc'                      => '',
            'razon_social'             => '',
            'direccion_fiscal'         => '',
            'sat_pais.value'           => 'required',
            'tipo_comprobante.value'   => 'required',
            'metodo_pago.value'        => 'required',
            'forma_pago.value'         => 'required',
            'fecha_pago'               => '',
            'uso_cfdi.value'           => 'required',
            'tipo_relacion.value'      => '',
            'cfdis_relacionados'       => '',
            'operaciones_relacionadas' => '',
            'conceptos'                => '',
            'tasa_iva'                 => 'required|numeric|min:16|max:16',
        ];

        /**VALIDACIONES CONDICIONADAS RESPECTO AL TIPO DE RFC*/
        if ($request->tipo_rfc['value'] == 1) {
            /**ocupa rfc que lo escriban, minimo 12 caracteres */
            $validaciones['rfc']          = 'required|min:12';
            $validaciones['razon_social'] = 'required';
            /**asigando pais por defecto mexico id 151 */
            $request->merge([
                'sat_pais.value' => 151,
            ]);
        } elseif ($request->tipo_rfc['value'] == 2) {
            /**es publico en general */
            $request->merge([
                'rfc' => 'XAXX010101000',
            ]);
            $request->merge([
                'razon_social' => 'público en general',
            ]);
            //$request->direccion_fiscal = 'N/A';
            /**asigando pais por defecto mexico id 151 */
            $request->merge([
                'sat_pais.value' => 151,
            ]);
        } else {
            /**es publico gral extranjero */
            $request->merge([
                'rfc' => 'XEX010101000',
            ]);
            $request->merge([
                'razon_social' => 'público en general extranjero',
            ]);
            //$request->direccion_fiscal = 'N/A';
            $validaciones['sat_pais'] = 'required';
        }

        /**VALIDANDO EL TIPO DE COMPROBANTE */
        if ($request->tipo_comprobante['value'] == 1) {
            /**es de tipo ingreso y ocupa conceptos */
        } elseif ($request->tipo_comprobante['value'] == 2) {
            /**es egreso */
        } else {
            /**es pago debe ser id 5 */
            $validaciones['fecha_pago'] = 'required';
            /**metodo pago pue */
            $request->merge([
                'metodo_pago.value' => 1,
            ]);
            /**P01, por definir, por regla del sat */
            $request->merge([
                'uso_cfdi.value' => 22,
            ]);
        }
        /**MENSAJES DE LAS VALIDACIONES*/
        $mensajes = [
            'integer'  => 'Ingrese un número entero',
            'numeric'  => 'Ingrese un valor numérico',
            'required' => 'Dato obligatorio',
            'min'      => "Este valor debe ser mínimo :min",
            'max'      => "Este valor debe ser máximo :max",
        ];

        /**obtenermos el folio para asignar al cfdi */

        /**VALIDANDO LOS DATOS */
        request()->validate(
            $validaciones,
            $mensajes
        );
        /**LAS VALIDACIONES HAN PASADO Y SE DEBEN VALIDAR LOS CAMPOS QUE APLICAN SEGUN EL CASO*/
        if ($request->tipo_comprobante['value'] == '1') {
            /**ES DE TIPO INGRESO */
            if ($request->metodo_pago['value'] == '2') {
                /**es PPD */
                if ($request->forma_pago['value'] != 9) {
                    /**es diferente de POR DEFINIR */
                    return $this->errorResponse('La forma de pago debe ser por definir para este tipo de comprobante.', 409);
                }
            }
        } else if ($request->tipo_comprobante['value'] == '5') {
            /**es pago */
        } else if ($request->tipo_comprobante['value'] == '2') {
            /**es egreso */
        } else {
            return $this->errorResponse('el tipo de comprobante no es válido', 409);
        }

        /**procede con las validaciones y se hace la insercion de la transanccion en la base de datos */
        $datos_funeraria = Funeraria::first();
        /**COMENZANDO A GUARDAR EL CFDI EN LA BASE DE DATOS */
        try {
            DB::beginTransaction();
            /**GUARDAMOS LA INFO INICIAL DEL  */
            $folio_para_asignar = DB::table('cfdis')->insertGetId(
                [
                    'uuid'                        => null,
                    'tasa_iva'                    => $request->tasa_iva,
                    'clientes_id'                 => $request->id_cliente,
                    'serie'                       => null,
                    'fecha'                       => null,
                    'sat_formas_pago_id'          => $request->forma_pago['value'],
                    'subtotal'                    => 0,
                    'descuento'                   => 0,
                    'fecha_pago'                  => $request->tipo_comprobante['value'] == 5 ? $request->fecha_pago : null,
                    'sat_monedas_id'              => 1, //peso mxn
                    'tipo_cambio'                 => 1,
                    'total'                       => 0,
                    'sat_tipo_comprobante_id'     => $request->tipo_comprobante['value'],
                    'sat_metodos_pago_id'         => $request->metodo_pago['value'],
                    'rfc_emisor'                  => ENV('APP_ENV') == 'local' ? 'EWE1709045U0' : strtoupper($datos_funeraria['rfc']),
                    'nombre_emisor'               => $datos_funeraria->nombre_comercial,
                    'sat_regimenes_id'            => 1, //personas morales
                    'sat_pais_id'                 => $request->sat_pais['value'],
                    'rfc_receptor'                => $request->rfc,
                    'nombre_receptor'             => $request->razon_social,
                    'residencia_fiscal_receptor'  => $request->direccion_fiscal,
                    'sat_usos_cfdi_id'            => $request->uso_cfdi['value'],
                    'fecha_timbrado'              => null,
                    'rfc_proveedor_certificado'   => null,
                    'fecha_registro'              => now(),
                    'nota'                        => $request->nota,
                    'timbro_id'                   => (int) $request->user()->id,
                    'num_operacion'               => null,
                    'rfc_emisor_cta_ordenante'    => null,
                    'nombre_banco_ordenante'      => null,
                    'rfc_emisor_cta_beneficiario' => null,
                    'cta_beneficiario'            => null,
                    'tipos_cadena_pago_clave'     => null,
                    'sat_tipo_relacion_id'        => $request->tipo_relacion['value'],
                ]
            );

            /**total a egresar en caso de aplicar */
            $total_a_egresar = 0;

            if ($request->tipo_comprobante['value'] == 1) {
                /**ingreso */
                /**GUARDANDO OPERACIONES RELACIONADAS */
                if (isset($request->operaciones_relacionadas)) {
                    if (count($request->operaciones_relacionadas) > 0) {
                        /**tiene operaciones relacionadas */
                        foreach ($request->operaciones_relacionadas as $key => $operacion) {
                            //return $this->errorResponse($operacion['operacion_id'], 409);
                            DB::table('cfdis_operaciones')->insert(
                                [
                                    'cfdis_id'       => $folio_para_asignar,
                                    'operaciones_id' => $operacion['operacion_id'],
                                ]
                            );
                        }
                    }
                }
                /**GUARDANDO CONCEPTOS DEL CFDI */
                if (isset($request->conceptos)) {
                    //return $request->conceptos;
                    if (count($request->conceptos) > 0) {
                        /**tiene conceptos */
                        foreach ($request->conceptos as $key => $concepto) {
                            //return $this->errorResponse($concepto['clave_sat']['value'], 409);
                            $ValorUnitarioPrecioNeto      = (float) ($concepto['precio_neto'] / (1 + ($request->tasa_iva / 100)));
                            $ValorUnitarioPrecioDescuento = $concepto['precio_descuento'] / (1 + ($request->tasa_iva / 100));
                            $descuento_concepto           = $concepto['descuento_b']['value'] == 1 ? ($ValorUnitarioPrecioNeto - $ValorUnitarioPrecioDescuento) : 0;
                            $valor_unitario_real          = $concepto['descuento_b']['value'] == 1 ? $ValorUnitarioPrecioDescuento : $ValorUnitarioPrecioNeto;
                            DB::table('conceptos_cfdi')->insert(
                                [
                                    'sat_productos_servicios_id' => $concepto['clave_sat']['value'],
                                    'cantidad'                   => $concepto['cantidad'],
                                    'descripcion'                => $concepto['descripcion'],
                                    'valor_unitario'             => $valor_unitario_real,
                                    'importe'                    => $valor_unitario_real * $concepto['cantidad'],
                                    'descuento'                  => $descuento_concepto,
                                    'sat_unidades_id'            => $concepto['unidad_sat']['value'],
                                    'concepto_operacion_id'      => $concepto['concepto_operacion_id'],
                                    'cfdis_id'                   => $folio_para_asignar,
                                    'concepto_operacion_ver_b'   => $concepto['concepto_operacion_ver_b'],
                                    'modifica_b'                 => $concepto['modifica_b'],
                                ]
                            );
                        }
                    } else {
                        /**regreso el id de la base de datos que se iba consumir */
                        $this->regresar_bd_folio();
                        return $this->errorResponse('Ingrese los conceptos a facturar.', 409);
                    }
                } else {
                    /**regreso el id de la base de datos que se iba consumir */
                    $this->regresar_bd_folio();
                    return $this->errorResponse('Ingrese los conceptos a facturar.', 409);
                }
            } else if ($request->tipo_comprobante['value'] == '5') {
                /**ES DE TIPO PAGO, REVISANDO QUE LOS CFDIS A PAGAR SEAN DE TIPO PAGO, QUE ESTEN VIGENTES Y EL MONTO SEA VALIDO */
                $cfdis_a_pagar          = [];
                $total_comprobante_pago = 0;
                if (isset($request->cfdis_a_pagar)) {
                    if (count($request->cfdis_a_pagar) > 0) {
                        foreach ($request->cfdis_a_pagar as $key => $cfdi) {
                            if (isset($cfdi['id'])) {
                                array_push($cfdis_a_pagar, $cfdi['id']);
                                $total_comprobante_pago += $cfdi['monto_pago'];
                                if ($cfdi['status'] != 1) {
                                    /**no se puden pagar cfdis que no estan activos */
                                    $this->regresar_bd_folio();
                                    return $this->errorResponse('Revise que los cfdis que va a pagar no estén cancelados', 409);
                                }
                            } else {
                                /**regreso el id de la base de datos que se iba consumir */
                                $this->regresar_bd_folio();
                                return $this->errorResponse('El folio del cfdi que ingresó no es válido.', 409);
                            }
                        }
                        /**una vez verificados que los cfdis existen, se mandan traer de la bd para verificar que el total pagado permite el monto
                         * del abonos
                         */
                        $cfdis_de_bd = Cfdis::select(
                            'status',
                            'rfc_receptor',
                            'uuid',
                            'id',
                            'total',
                            'sat_tipo_comprobante_id',
                            'sat_metodos_pago_id'
                        )->with('pagos_asociados')
                            ->with('egresos_asociados')
                            ->whereIn('id', $cfdis_a_pagar)->get();

                        if (count($cfdis_de_bd) == 0) {
                            /**regreso el id de la base de datos que se iba consumir */
                            $this->regresar_bd_folio();
                            return $this->errorResponse('No se han encontrado cfdis a pagar', 409);
                        }

                        /**creo el array con los pagos relacionados del xml */
                        $array_cfdis_a_pagar_xml = [];

                        /**validamos que todo esté segun las validaciones del SAT */
                        foreach ($cfdis_de_bd as $key => $cfdi_bd) {
                            if ($cfdi_bd['sat_tipo_comprobante_id'] == 1 && $cfdi_bd['sat_metodos_pago_id'] == 2) {
                                /**se procede con el pago, al ser un cfdi de tipo ingreso y ppd */
                                $total_pagado       = 0;
                                $numero_parcialidad = 1;
                                foreach ($cfdi_bd['pagos_asociados'] as $key_pago => $pago) {
                                    if ($pago['status'] == 1) {
                                        /**pago activo */
                                        $total_pagado += $pago['monto_relacion'];
                                        if ($pago['tipo_relacion_id'] == 2) {
                                            /**solo para relacion de tipo pago */
                                            $numero_parcialidad++;
                                        }
                                    }
                                }
                                $total_egresado = 0;
                                foreach ($cfdi_bd['egresos_asociados'] as $key_egreso => $egreso) {
                                    if ($egreso['status'] == 1) {
                                        /**pago activo */
                                        $total_egresado += round($egreso['monto_relacion'], 2);
                                    }
                                }

                                foreach ($request->cfdis_a_pagar as $key_pagar => $cfdi_pagar) {
                                    if ($cfdi_pagar['id'] == $cfdi_bd['id']) {
                                        /**valido qie el rfc del receptor es igual al rfc del cfdi a pagar */
                                        if ($cfdi_bd['rfc_receptor'] != $request->rfc) {
                                            $this->regresar_bd_folio();
                                            return $this->errorResponse('El RFC del receptor debe ser igual al CFDI a pagar.', 409);
                                        }
                                        if ($cfdi_pagar['monto_pago'] > 0) {
                                            $importe_saldo_anterior = round($cfdi_bd['total'] - $total_pagado - $total_egresado, 2);
                                            $importe_saldo_insoluto = round($cfdi_bd['total'] - $total_pagado - $total_egresado - $cfdi_pagar['monto_pago'], 2);

                                            /**validando que el monto a abonar sea menor o igual al total menos el total pagado y egresado */
                                            if (round($total_pagado + $cfdi_pagar['monto_pago'] + $total_egresado, 2) <= round($cfdi_bd['total'], 2)) {
                                                /**procede la relacion del cfdi para pago */
                                                DB::table('cfdis_tipo_relacion')->insert(
                                                    [
                                                        'importe_saldo_anterior' => $importe_saldo_anterior,
                                                        'importe_saldo_insoluto' => $importe_saldo_insoluto,
                                                        'numero_parcialidad'     => $numero_parcialidad,
                                                        'sat_metodos_pago_id'    => $cfdi_bd['sat_metodos_pago_id'],
                                                        'monto_relacion'         => $cfdi_pagar['monto_pago'],
                                                        'tipo_relacion_id'       => 2, //pago
                                                        'cfdis_id_relacionado'   => $cfdi_bd['id'],
                                                        'cfdis_id'               => $folio_para_asignar,
                                                    ]
                                                );
                                                $metodo_pago = MetodosPago::where('id', '=', $cfdi_bd['sat_metodos_pago_id'])->first();
                                                if (is_null($metodo_pago)) {
                                                    $this->regresar_bd_folio();
                                                    return $this->errorResponse('No se encontró el método de pago que se está utilizando.', 409);
                                                }
                                                $saldo_insoluto = round($cfdi_bd['total'] - $total_pagado - $cfdi_pagar['monto_pago'] - $total_egresado, 2);

                                                //return $this->errorResponse($cfdi_bd['total']."-".$total_pagado."-".$cfdi_pagar['monto_pago']."-". $total_egresado."=".$saldo_insoluto, 409);
                                                array_push($array_cfdis_a_pagar_xml, [
                                                    '_attributes' => [
                                                        'EquivalenciaDR'   => "1",
                                                        'Folio'            => $cfdi_bd['id'],
                                                        'IdDocumento'      => $cfdi_bd['uuid'],
                                                        'ImpPagado'        => round($cfdi_pagar['monto_pago'], 2),
                                                        'ImpSaldoAnt'      => round($cfdi_bd['total'] - $total_pagado - $total_egresado, 2),
                                                        'ImpSaldoInsoluto' => $saldo_insoluto == -0 ? 0 : $saldo_insoluto,
                                                        'MonedaDR'         => "MXN",
                                                        'NumParcialidad'   => $numero_parcialidad,
                                                        'ObjetoImpDR'      => '01',
                                                    ],
                                                ]);
                                            } else {
                                                /**No aplica por que le pago es mayor a lo que resta de pagar */
                                                $this->regresar_bd_folio();
                                                return $this->errorResponse('El monto a pagar debe ser menor o igual a $ ' . number_format($cfdi_bd['total'] - $total_pagado, 2), 409);
                                            }
                                        } else {
                                            /**regreso el id de la base de datos que se iba consumir */
                                            $this->regresar_bd_folio();
                                            return $this->errorResponse('El monto a pagar de los comprobantes debe ser mayor a cero.', 409);
                                        }
                                        break;
                                    }
                                }
                                //return $this->errorResponse('aqui =>'.$cfdi_bd['total'], 409);
                                $request->merge([
                                    'array_cfdis_a_pagar_xml' => $array_cfdis_a_pagar_xml,
                                ]);
                            } else {
                                /**regreso el id de la base de datos que se iba consumir */
                                $this->regresar_bd_folio();
                                return $this->errorResponse('Verifique que los CFDIs son de tipo ingreso y PPD', 409);
                            }
                        }
                    } else {
                        /**regreso el id de la base de datos que se iba consumir */
                        $this->regresar_bd_folio();
                        return $this->errorResponse('Ingrese los cfdis a pagar.', 409);
                    }
                } else {
                    /**regreso el id de la base de datos que se iba consumir */
                    $this->regresar_bd_folio();
                    return $this->errorResponse('Ingrese los cfdis a pagar.', 409);
                }
            } else if ($request->tipo_comprobante['value'] == '2') {
                /**dejando todos los datos por defecto segun la regulacion para comprobantes de egresos */
                /**egreso */
                $request->merge([
                    'metodo_pago.value' => 1, //pue
                ]);
                $myRequest = new Request();
                $myRequest->request->add(['test' => 'test']);

                if (isset($request->cfdis_relacionados)) {
                    if (count($request->cfdis_relacionados) == 1) {
                        /**se puede proceder */
                        /**obteniendo los datos del cfdi a crear el egreso */
                        if (isset($request->cfdis_relacionados[0]['id'])) {
                            $cfdi_a_egresar = $this->get_cfdis_timbrados($myRequest, $request->cfdis_relacionados[0]['id']);
                            if (! is_null($cfdi_a_egresar)) {
                                $cfdi_a_egresar = $cfdi_a_egresar[0];

                                if ($cfdi_a_egresar['status'] != 1) {
                                    /**no se puden hacer egresos sobre cfdis que no estan activos */
                                    $this->regresar_bd_folio();
                                    return $this->errorResponse('Revise que los cfdis que va a relacionar no estén cancelados', 409);
                                }

                                /**validando que el rfc del cfdi relacionado sea el mismo que el del comprobante que se esta haciendo */
                                if ($cfdi_a_egresar['rfc_receptor'] != $request->rfc) {
                                    $this->regresar_bd_folio();
                                    return $this->errorResponse('El rfc receptor del cfdi relacionado debe ser igual al rfc que está ingresando.', 409);
                                }
                                /**el cfdi esta en la base de datos, revisnado que sea de tipo ingreo */
                                if ($cfdi_a_egresar['sat_tipo_comprobante_id'] == 1) {
                                    /**revisando que haya conceptos que ingresar para sacar un total del egreso */
                                    /**GUARDANDO CONCEPTOS DEL CFDI */
                                    if (isset($request->conceptos)) {
                                        //return $request->conceptos;
                                        if (count($request->conceptos) > 0) {
                                            /**tiene conceptos */
                                            foreach ($request->conceptos as $key => $concepto) {
                                                //return $this->errorResponse($concepto['clave_sat']['value'], 409);
                                                $ValorUnitarioPrecioNeto      = (float) ($concepto['precio_neto'] / (1 + ($request->tasa_iva / 100)));
                                                $ValorUnitarioPrecioDescuento = $concepto['precio_descuento'] / (1 + ($request->tasa_iva / 100));
                                                $descuento_concepto           = $concepto['descuento_b']['value'] == 1 ? ($ValorUnitarioPrecioNeto - $ValorUnitarioPrecioDescuento) : 0;
                                                $valor_unitario_real          = $concepto['descuento_b']['value'] == 1 ? $ValorUnitarioPrecioDescuento : $ValorUnitarioPrecioNeto;
                                                DB::table('conceptos_cfdi')->insert(
                                                    [
                                                        'sat_productos_servicios_id' => $concepto['clave_sat']['value'],
                                                        'cantidad'                   => $concepto['cantidad'],
                                                        'descripcion'                => $concepto['descripcion'],
                                                        'valor_unitario'             => $valor_unitario_real,
                                                        'importe'                    => $valor_unitario_real * $concepto['cantidad'],
                                                        'descuento'                  => $descuento_concepto,
                                                        'sat_unidades_id'            => $concepto['unidad_sat']['value'],
                                                        'concepto_operacion_id'      => $concepto['concepto_operacion_id'],
                                                        'cfdis_id'                   => $folio_para_asignar,
                                                        'concepto_operacion_ver_b'   => $concepto['concepto_operacion_ver_b'],
                                                        'modifica_b'                 => $concepto['modifica_b'],
                                                    ]
                                                );
                                                $total_a_egresar += (float) $concepto['precio_neto'];
                                            }
                                        } else {
                                            /**regreso el id de la base de datos que se iba consumir */
                                            $this->regresar_bd_folio();
                                            return $this->errorResponse('Ingrese los conceptos a facturar.', 409);
                                        }
                                    } else {
                                        /**regreso el id de la base de datos que se iba consumir */
                                        $this->regresar_bd_folio();
                                        return $this->errorResponse('Ingrese los conceptos a facturar.', 409);
                                    }
                                } else {
                                    $this->regresar_bd_folio();
                                    return $this->errorResponse('El cfdi a relacionar para egresos debe ser de tipo Ingreso.', 409);
                                }
                            } else {
                                $this->regresar_bd_folio();
                                return $this->errorResponse('El cfdi a relacionar no está en la base de datos', 409);
                            }

                            /**continuando despues de guardar los conceptos en la base de datos */
                            /**verifianco que la cantidad a egresar no sobrepasa el limite a egresar dle cfdi */
                            if (round($cfdi_a_egresar['maximo_a_egresar']) < round($total_a_egresar)) {
                                $this->regresar_bd_folio();
                                return $this->errorResponse('Este cfdi solo puede tener egresos de máximo $' . number_format($cfdi_a_egresar['maximo_a_egresar'], 2) . '.', 409);
                            }
                        } else {
                            $this->regresar_bd_folio();
                            return $this->errorResponse('Ingrese el folio del cfdi a relacionar', 409);
                        }
                    } else {
                        $this->regresar_bd_folio();
                        return $this->errorResponse('Solo se puede relacionar un cfdi para egresos.', 409);
                    }
                } else {
                    $this->regresar_bd_folio();
                    return $this->errorResponse('Debe relacionar un cfdi para egresos.', 409);
                }
            }
            /**GUARDANDO CFDIS RELACIONADOS A ESTE NUEVO DOCUMENTO*/
            /**aqui voy */
            $array_cfdis_a_relacionar_xml = [];

            if ($request->tipo_relacion['value'] > 0) {
                if (count($request->cfdis_relacionados) > 0) {
                    $tipo_relacion = TiposRelacion::where('id', '=', $request->tipo_relacion['value'])->first();
                    if (is_null($tipo_relacion)) {
                        $this->regresar_bd_folio();
                        return $this->errorResponse('No se encontró el tipo de relación que se está utilizando.', 409);
                    }

                    $array_cfdis_a_relacionar_xml = [
                        '_attributes'          => [
                            'TipoRelacion' => $tipo_relacion['clave'],
                        ],
                        'cfdi:CfdiRelacionado' => [],
                    ];
                    /**tiene cfdis relacionados */
                    foreach ($request->cfdis_relacionados as $key => $cfdi_relacionado) {
                        /**validando los documentos relacionados */
                        if (isset($cfdi_relacionado['id']) && isset($cfdi_relacionado['uuid']) && isset($cfdi_relacionado['sat_tipo_comprobante_id'])) {
                            if ($request->tipo_comprobante['value'] == 1 || $request->tipo_comprobante['value'] == 5) {
                                if ($cfdi_relacionado['sat_tipo_comprobante_id'] != $request->tipo_comprobante['value']) {
                                    /**regreso el id de la base de datos que se iba consumir */
                                    $this->regresar_bd_folio();
                                    return $this->errorResponse('El tipo de cfdi a relacionar debe ser del mismo tipo que el nuevo documento.', 409);
                                }
                            } else {
                                if ($request->tipo_comprobante['value'] == 2) {
                                    /**egreso */
                                    /**el cfdi a relacionar debe ser de ingreso */
                                    if (! $cfdi_relacionado['sat_tipo_comprobante_id'] == 1) {
                                        $this->regresar_bd_folio();
                                        return $this->errorResponse('Ingrese un cfdi a relacionar de tipo ingreso.', 409);
                                    }
                                }
                            }

                            /**guardamos en la base de datos */
                            DB::table('cfdis_tipo_relacion')->insert(
                                [
                                    'cfdis_id'             => $folio_para_asignar,
                                    'cfdis_id_relacionado' => $cfdi_relacionado['id'],
                                    'tipo_relacion_id'     => $request->tipo_comprobante['value'] == 1 || $request->tipo_comprobante['value'] == 5 ? 1 : 3, //de tipo relacion sat
                                    'sat_metodos_pago_id'  => $cfdi_relacionado['sat_metodos_pago_id'],
                                    'monto_relacion'       => $request->tipo_comprobante['value'] == 1 || $request->tipo_comprobante['value'] == null ? 1 : $total_a_egresar, //de tipo relacion sat
                                ]
                            );
                            /**agregamos al array que mandaremos al generar el xml */
                            array_push($array_cfdis_a_relacionar_xml['cfdi:CfdiRelacionado'], [
                                '_attributes' => [
                                    'UUID' => $cfdi_relacionado['uuid'],
                                ],
                            ]);
                        } else {
                            /**regreso el id de la base de datos que se iba consumir */
                            $this->regresar_bd_folio();
                            return $this->errorResponse('Ingrese el folio, uuid y tipo del cfdi a relacionar.', 409);
                        }
                    }
                    /**mando el arreglo listo para push al array de generar xml */
                    $request->merge([
                        'array_cfdis_a_relacionar_xml' => $array_cfdis_a_relacionar_xml,
                    ]);
                }
            }

            header('Content-type: text/html; charset=utf-8');
            try {
                set_time_limit(0);
                ini_set('display_errors', true);
                ini_set("soap.wsdl_cache_enabled", "0");
                /**mandamos crear el XML, con el nombre temporal del folio registrado en la parte superior */
                $xml_a_timbrar = $this->GenerarXmlCfdi($request, $folio_para_asignar);
                //return $this->errorResponse( $xml_a_timbrar, 409);

                /**verificando que el xml se haya genrado sin errores */
                if ($xml_a_timbrar['nombre_xml'] != $folio_para_asignar . '.xml') {
                    Storage::disk($storage_disk_xmls)->delete($xml_a_timbrar['nombre_xml']);
                    return $this->errorResponse($responseTimbre->acuseCFDI->error, 409);

                    /**el xml no se creo */
                    return $this->errorResponse('Ocurrió un error al generar el XML, reintente por favor.', 409);
                }

                /* carga archivo xml */
                $storage_disk_credentials = ENV('STORAGE_DISK_CREDENTIALS');
                $storage_disk_xmls        = ENV('STORAGE_DISK_XML');

                /**datos */
                $certificado_path     = '';
                $key_path             = '';
                $usuario              = '';
                $password             = '';
                $root_path_cer        = '';
                $root_path_key        = '';
                $credentials_password = '';
                if (ENV('APP_ENV') == 'local') {
                    $certificado_path     = ENV('CER_PAC');
                    $key_path             = ENV('KEY_PAC');
                    $usuario              = ENV('USER_PAC_DEV');
                    $password             = ENV('PASSWORD_PAC_DEV');
                    $root_path_cer        = ENV('ROOT_CER_DEV');
                    $root_path_key        = ENV('ROOT_KEY_DEV');
                    $credentials_password = ENV('PASSWORD_LLAVES');
                } else {
                    $facturacion_datos_sistema = Facturacion::First();
                    /**data from DB */
                    if (! $facturacion_datos_sistema->cerFile || ! $facturacion_datos_sistema->keyFile || ! $facturacion_datos_sistema->password) {
                        /**no procede */
                        return $this->errorResponse("no se han capturado los certificados digitales de facturación", 409);
                    }
                    $certificado_path     = $facturacion_datos_sistema->cerFile; //sistema
                    $key_path             = $facturacion_datos_sistema->keyFile; //sistema
                    $usuario              = ENV('USER_PAC_PROD');
                    $password             = ENV('PASSWORD_PAC_PROD');
                    $root_path_cer        = ENV('ROOT_CER_PROD');
                    $root_path_key        = ENV('ROOT_KEY_PROD');
                    $credentials_password = $facturacion_datos_sistema->password;
                }
                $contenido_xml_a_timbrar = Storage::disk($storage_disk_xmls)->path($xml_a_timbrar['nombre_xml']);
                /**mandamos llamar la clase del PAC*/
                $clienteFD = new ClienteFormasDigitales($contenido_xml_a_timbrar);
                /* se le pasan los datos de acceso */
                $autentica           = new Autenticar();
                $autentica->usuario  = $usuario;
                $autentica->password = $password;
                $parametros          = new Parametros();
                $parametros->accesos = $autentica;
                //$this->errorResponse($clienteFD->sellarXML($certFile, $keyFile), 409);
                /**SE MANDA SELLAR EL XML */

                $datos_credenciales = [
                    'disk'     => $storage_disk_credentials,
                    'cer_name' => $certificado_path,
                    'key_name' => $key_path,
                    'cer_root' => $root_path_cer,
                    'key_root' => $root_path_key,
                    'password' => $credentials_password,
                ];
                if (ENV('GENERAR_PEMS') == true) {
                    $clienteFD->crear_pem_files($datos_credenciales);
                }
                $certFile = Storage::disk($storage_disk_credentials)->path($root_path_cer . $certificado_path);
                $keyFile  = Storage::disk($storage_disk_credentials)->path($root_path_key . $key_path . '.pem');
                $clienteFD->sellarXML($certFile, $keyFile);
                $retorno_del_sellado     = $clienteFD->sellarXML($certFile, $keyFile);
                $parametros->comprobante = $retorno_del_sellado['xml'];
                $cadena_original_cfdi    = $retorno_del_sellado['cadena_original'];
                /* se manda el xml a TIMBRAR */
                $responseTimbre = $clienteFD->timbrar($parametros);
                if (isset($responseTimbre->acuseCFDI->error)) {
                    /**regreso el id de la base de datos que se iba consumir */
                    $this->regresar_bd_folio();
                    //return $this->errorResponse(($responseTimbre->acuseCFDI->codigoError), 409);
                    /**eliminar el xml ya que no se timbro*/
                    Storage::disk($storage_disk_xmls)->delete($xml_a_timbrar['nombre_xml']);
                    return $this->errorResponse($responseTimbre->acuseCFDI->error, 409);
                }
                /**verificando si esta timbrado el cfdi */
                if (isset($responseTimbre->acuseCFDI->xmlTimbrado)) {
                    /**EL XML SE TIMBRO CORRECTAMENTE */
                    /**se comiezna a guardar el resultado en la base de datos */
                    /**se actualiza el xml */
                    Storage::disk($storage_disk_xmls)->put($xml_a_timbrar['nombre_xml'], $responseTimbre->acuseCFDI->xmlTimbrado);
                    $xml_timbrado_file = Storage::disk($storage_disk_xmls)->get($xml_a_timbrar['nombre_xml']);
                    $xml_timbrado      = $this->leer_xml($folio_para_asignar);

                    $total = 0;
                    if ($request->tipo_comprobante['value'] == '1') {
                        /**pago */
                        $total = $xml_timbrado['Comprobante']['Total'];
                    } else if ($request->tipo_comprobante['value'] == '2') {
                        /**egregso */
                        $total = $total_a_egresar;
                    } else {
                        if ($request->tipo_comprobante['value'] == '5') {
                            /**pago */
                            $total = $total_comprobante_pago;
                        }
                    }
                    DB::table('cfdis')->where('id', '=', $folio_para_asignar)->update(
                        [
                            'uuid'                        => $xml_timbrado['Complemento']['TimbreFiscalDigital']['UUID'],
                            'version'                     => $xml_timbrado['Comprobante']['Version'],
                            'serie'                       => $xml_timbrado['Comprobante']['Serie'],
                            'fecha'                       => $xml_timbrado['Comprobante']['Fecha'],
                            'subtotal'                    => $xml_timbrado['Comprobante']['SubTotal'],
                            'descuento'                   => $xml_timbrado['Comprobante']['Descuento'],
                            'total'                       => $total,
                            'fecha_timbrado'              => $xml_timbrado['Complemento']['TimbreFiscalDigital']['FechaTimbrado'],
                            'rfc_proveedor_certificado'   => $xml_timbrado['Complemento']['TimbreFiscalDigital']['RfcProvCertif'],
                            'num_operacion'               => null,
                            'rfc_emisor_cta_ordenante'    => null,
                            'nombre_banco_ordenante'      => null,
                            'rfc_emisor_cta_beneficiario' => null,
                            'cta_beneficiario'            => null,
                            'tipos_cadena_pago_clave'     => null,
                            'cadena_original'             => $cadena_original_cfdi,
                            'xml_timbrado'                => $xml_timbrado_file,
                        ]
                    );
                    DB::commit();
                    return $folio_para_asignar;
                    //$clienteFD = new ClienteFormasDigitales($contents = Storage::disk($storage_disk_xmls)->path($file_guardar));
                    //return $clienteFD->generarCadenaOriginal();
                }
            } catch (SoapFault $e) {
                $this->regresar_bd_folio();
                print("Auth Error:::: $e");
            }
            /**SE MANDA REGISTRAR LA TRANSACCIOEN LA BASE DE DATOS */
        } catch (\Throwable $th) {
            /**regreso el id de la base de datos que se iba consumir */
            $this->regresar_bd_folio();
            //return $this->errorResponse('Error al guardar en la base de datos.', 409);
            return $th;
        }
    }

    public function regresar_bd_folio()
    {
        DB::rollBack();
        $maxId = DB::table('cfdis')->max('id');
        if (trim($maxId) > 0) {
            DB::statement("ALTER TABLE cfdis AUTO_INCREMENT=$maxId");
        }
    }

    /**leo los datos del xml para guardar en la base de datos */
    public function leer_xml($folio_xml = '')
    {
        $id_folio_cfdi = 0;
        $cfdi          = null;
        //EMPIEZO A LEER LA INFORMACION DEL CFDI
        if (trim($folio_xml) == '') {
            return $this->errorResponse('Debe especificar una ruta del archivo .xml', 409);
        } else {
            $id_folio_cfdi     = $folio_xml;
            $storage_disk_xmls = ENV('STORAGE_DISK_XML');
            $cfdi              = Cfdis::with('timbro')->with('cliente')->where('id', '=', $id_folio_cfdi)->first();
            if (isset($cfdi->xml_timbrado)) {
                //lo creo
                Storage::disk($storage_disk_xmls)->put($folio_xml . '.xml', ($cfdi->xml_timbrado));
            }
            /* carga archivo xml */
            $folio_xml = Storage::disk($storage_disk_xmls)->path($folio_xml . '.xml');
        }

        /**se trae la informacion del cfdi de la bd */

        if (File::exists($folio_xml)) {
            $xml = simplexml_load_string(file_get_contents($folio_xml));
        } else {
            return $this->errorResponse('El xml que indicó no existe en la base de datos.', 409);
        }

        /**se trae la informacion del cfdi de la bd */
        //$cfdi = Cfdis::with('timbro')->with('cliente')->where('id', '=', $id_folio_cfdi)->first();

        if (is_null($cfdi)) {
            return $this->errorResponse('El xml que indicó no existe.', 409);
        }

        $tasa_iva = number_format((float) round($cfdi->tasa_iva / 100, 2), 6, '.', '');
        //aqui trabajo 2
        $ns = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('cfdi', 'http://www.sat.gob.mx/cfd/4');
        $xml->registerXPathNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
        $xml->registerXPathNamespace('pago20', 'http://www.sat.gob.mx/Pagos');

        $comprobante = $xml->xpath('//cfdi:Comprobante')[0];
        $emisor      = $xml->xpath('//cfdi:Emisor')[0];
        $receptor    = $xml->xpath('//cfdi:Receptor')[0];

        $relacionados      = $xml->xpath('//cfdi:CfdiRelacionados');
        $CfdisRelacionados = [];
        if (isset($relacionados[0])) {
            $tipo_relacion = TiposRelacion::where('clave', '=', (string) $relacionados[0]['TipoRelacion'])->first();
            if (is_null($tipo_relacion)) {
                return $this->errorResponse('No se encontró el tipo de relación que se está utilizando en el xml.', 409);
            }
            $CfdisRelacionados['TipoRelacion']      = (string) $relacionados[0]['TipoRelacion'];
            $CfdisRelacionados['TipoRelacionTexto'] = $tipo_relacion['tipo'];
            $CfdisRelacionados['CfdiRelacionado']   = [];

            foreach ($xml->xpath('//cfdi:CfdiRelacionados//cfdi:CfdiRelacionado') as $relacionado) {
                array_push($CfdisRelacionados['CfdiRelacionado'], [
                    'UUID' => (string) $relacionado['UUID'],
                ]);
            }
        }

        /**determinando el regimen */
        $regimen               = SATRegimenes::select('regimen')->where('clave', (string) $emisor['RegimenFiscal'])->first();
        $uso_cfdi              = SatUsosCfdi::select('uso')->where('clave', (string) $receptor['UsoCFDI'])->first();
        $RegimenFiscalReceptor = SatRegimenes::select('regimen')->where('clave', (string) $receptor['RegimenFiscalReceptor'])->first();

        /**agregando conceptos */
        $conceptos = [];
        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $concepto) {
            $traslado = [];
            /**agregando traslado */

            $descuento = ! empty((string) $concepto['Descuento']) ? (string) $concepto['Descuento'] : 0;

            array_push($traslado, [
                'Base'       => number_format((float) round(((string) $concepto['ValorUnitario'] - ($descuento / $concepto['Cantidad'])) * ((string) $concepto['Cantidad']), 2), 2, '.', ''),
                'Importe'    => number_format((float) round((((string) $concepto['ValorUnitario'] - ($descuento / $concepto['Cantidad'])) * (string) $concepto['Cantidad']) * .16, 2), 2, '.', ''),
                'Impuesto'   => '002',
                'TasaOCuota' => $tasa_iva,
                'TipoFactor' => 'Tasa',
            ]);

            array_push($conceptos, [
                'ClaveProdServ' => trim((string) $concepto['ClaveProdServ']),
                'ClaveUnidad'   => (string) $concepto['ClaveUnidad'],
                'Importe'       => (string) $concepto['Importe'],
                'Cantidad'      => (string) $concepto['Cantidad'],
                'Descripcion'   => (string) $concepto['Descripcion'],
                'ValorUnitario' => (string) $concepto['ValorUnitario'],
                'Descuento'     => $descuento,
                'Impuestos'     => [
                    'Traslados' => $traslado,
                ],
            ]);
        }

        $ultimo_nodo_de_impuestos = count($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado'));

        /**impuestos trasladados */
        $impuestos_trasladados       = [];
        $total_impuestos_trasladados = 0;

        /**verificando si tiene nodos de Impuestos Traslados */
        if ($ultimo_nodo_de_impuestos > 0) {
            $impuestos                   = $xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado')[$ultimo_nodo_de_impuestos - 1];
            $total_impuestos_trasladados = (string) $impuestos['Importe'];
            array_push($impuestos_trasladados, [
                'Importe'    => (string) $impuestos['Importe'],
                'Impuesto'   => "002",
                'TasaOCuota' => $tasa_iva,
                'TipoFactor' => "Tasa",
            ]);
        }

        $complemento                     = $xml->xpath('//tfd:TimbreFiscalDigital')[0];
        $ultimos_ocho_digitos_sello_cfdi = $newstring = substr((string) $comprobante['Sello'], -8);

        $cadena_codigo_qr = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=" . (string) $complemento['UUID'] . "&tt=" . (string) $comprobante['Total'] . "&re=" . (string) $emisor['Rfc'] . "&rr=" . (string) $receptor['Rfc'] . "&fe=" . $ultimos_ocho_digitos_sello_cfdi;
        $codigo_qr        = DNS2D::getBarcodeHTML($cadena_codigo_qr, 'QRCODE', 3, 3);

        /**DATOS DEL NODO DE PAGOS */
        $pago_arreglo               = [];
        $documentos_pagados_arreglo = [];

        $forma_pago  = null;
        $metodo_pago = MetodosPago::where('clave', '=', (string) $comprobante['TipoDeComprobante'] == 'I' ? (string) $comprobante['MetodoPago'] : 'PUE')->first();

        $tipo_comprobante = '';
        if ((string) $comprobante['TipoDeComprobante'] == 'I') {
            $tipo_comprobante = 'Ingreso';
            $schema_location  = 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd';
            /**obteniendo claves de los catalogos */
            $forma_pago = SatFormasPago::where('clave', '=', (string) $comprobante['FormaPago'])->first();
        } else if ((string) $comprobante['TipoDeComprobante'] == 'E') {
            $tipo_comprobante = 'Egreso';
            $schema_location  = 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd';
            $forma_pago       = SatFormasPago::where('clave', '=', (string) $comprobante['FormaPago'])->first();
        } else {
            if ((string) $comprobante['TipoDeComprobante'] == 'P') {
                $tipo_comprobante = 'Pago';
                if (isset($xml->xpath('//pago20:Pago')[0])) {
                    $pago         = $xml->xpath('//pago20:Pago')[0]->attributes();
                    $forma_pago   = SatFormasPago::where('clave', '=', (string) $pago['FormaDePagoP'])->first();
                    $pago_arreglo = [
                        'FechaPago'    => fecha_abr((string) $pago['FechaPago']),
                        'FormaDePagoP' => $forma_pago['forma'] . ' (' . (string) $pago['FormaDePagoP'] . ')',
                        'MonedaP'      => (string) $pago['MonedaP'],
                        'Monto'        => (string) $pago['Monto'],
                    ];

                    if (! isset($xml->xpath('//pago20:DoctoRelacionado')[0])) {
                        return $this->errorResponse('Error al leer el xml, los documentos relacionados no existen', 409);
                    }

                    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//pago20:Pagos//pago20:Pago//pago20:DoctoRelacionado') as $docto_relacionado) {
                        /**documentos relacionados */
                        array_push(
                            $documentos_pagados_arreglo,
                            [
                                'Folio'            => (string) $docto_relacionado['Folio'],
                                'IdDocumento'      => (string) $docto_relacionado['IdDocumento'],
                                'ImpPagado'        => (string) $docto_relacionado['ImpPagado'],
                                'ImpSaldoAnt'      => (string) $docto_relacionado['ImpSaldoAnt'],
                                'ImpSaldoInsoluto' => (string) $docto_relacionado['ImpSaldoInsoluto'],
                                'MonedaDR'         => (string) $docto_relacionado['MonedaDR'],
                                'NumParcialidad'   => (string) $docto_relacionado['NumParcialidad'],
                            ]
                        );
                    }
                } else {
                    return $this->errorResponse('Error al leer el xml', 409);
                }
                $schema_location = 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd';
            } else {
                return $this->errorResponse('Error al leer el xml', 409);
            }
        }

        if (is_null($forma_pago)) {
            return 'No se encontró la forma de pago que se está utilizando.';
        }

        /**se crea el arreglo con la informacion del xml */
        $comprobante_cfdi = [
            'Sistema'           => [
                'total_letra'         => numeros_a_letras($cfdi['sat_tipo_comprobante_id'] != 5 ? (string) $comprobante['Total'] : (string) $pago['Monto']),
                'cliente_id'          => $cfdi['cliente']['id'],
                'cliente_nombre'      => $cfdi['cliente']['nombre'],
                'cliente_direccion'   => trim($cfdi['cliente']['direccion_fiscal']) != '' ? $cfdi['cliente']['direccion_fiscal'] : 'N/A',
                'cliente_email'       => $cfdi['cliente']['email'],
                'cliente_telefono'    => $cfdi['cliente']['celular'],
                'timbro_nombre'       => $cfdi['timbro']['nombre'],
                'tipo_comprobante_id' => $cfdi['sat_tipo_comprobante_id'],
            ],
            'Comprobante'       => [
                'xmlns:cfdi'         => 'http://www.sat.gob.mx/cfd/4',
                'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
                'xmlns:pago20'       => 'http://www.sat.gob.mx/Pagos',
                'Certificado'        => (string) $comprobante['Certificado'],
                'Fecha'              => (string) $comprobante['Fecha'],
                'FechaTexto'         => fecha_abr((string) $comprobante['Fecha']),
                'Folio'              => (string) $comprobante['Folio'],
                'FormaPago'          => $comprobante['FormaPago'] . ' (' . $forma_pago['forma'] . ')',
                'LugarExpedicion'    => (string) $comprobante['LugarExpedicion'],
                'MetodoPago'         => $metodo_pago['clave'] . ' (' . $metodo_pago['metodo'] . ')',
                'Moneda'             => (string) $comprobante['Moneda'],
                'NoCertificado'      => (string) $comprobante['NoCertificado'],
                'Sello'              => (string) $comprobante['Sello'],
                'Serie'              => (string) $comprobante['Serie'],
                'SubTotal'           => (string) $comprobante['SubTotal'],
                'Descuento'          => ! empty((string) $comprobante['Descuento']) ? (string) $comprobante['Descuento'] : 0,
                'TipoCambio'         => (string) $comprobante['TipoCambio'],
                'TipoDeComprobante'  => (string) $comprobante['TipoDeComprobante'] . ' (' . $tipo_comprobante . ')',
                'Total'              => (string) $comprobante['Total'],
                'Version'            => (string) $comprobante['Version'],
                'xsi:schemaLocation' => $schema_location,
            ],
            'CfdisRelacionados' => $CfdisRelacionados,
            'Emisor'            => [
                'Rfc'           => (string) $emisor['Rfc'],
                'Nombre'        => (string) $emisor['Nombre'],
                'RegimenFiscal' => $regimen != null ? (string) $emisor['RegimenFiscal'] . ' (' . $regimen['regimen'] . ')' : '',
            ],
            'Receptor'          => [
                'Rfc'                     => (string) $receptor['Rfc'],
                'Nombre'                  => (string) $receptor['Nombre'],
                'UsoCFDI'                 => $uso_cfdi != null ? (string) $receptor['UsoCFDI'] . ' (' . $uso_cfdi['uso'] . ')' : '',
                'RegimenFiscalReceptor'   => $RegimenFiscalReceptor != null ? (string) $receptor['RegimenFiscalReceptor'] . ' (' . $RegimenFiscalReceptor['regimen'] . ')' : '',
                'DomicilioFiscalReceptor' => (string) $receptor['DomicilioFiscalReceptor'],
            ],
            'Conceptos'         => $conceptos,
            'Impuestos'         => [
                'TotalImpuestosTrasladados' => $total_impuestos_trasladados,
                'Traslados'                 => [
                    'Traslado' => $impuestos_trasladados,
                ],
            ],
            'Complemento'       => [
                'TimbreFiscalDigital' => [
                    'xmlns:tfd'          => 'http://www.sat.gob.mx/TimbreFiscalDigital',
                    'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
                    'FechaTimbrado'      => (string) $complemento['FechaTimbrado'],
                    'NoCertificadoSAT'   => (string) $complemento['NoCertificadoSAT'],
                    'RfcProvCertif'      => (string) $complemento['RfcProvCertif'],
                    'SelloCFD'           => (string) $complemento['SelloCFD'],
                    'SelloSAT'           => (string) $complemento['SelloSAT'],
                    'UUID'               => (string) $complemento['UUID'],
                    'Version'            => (string) $complemento['Version'],
                    'xsi:schemaLocation' => 'http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd',
                ],
            ],
            'CadenaOriginal'    => $cfdi->cadena_original,
            'CodigoQr'          => $codigo_qr,
        ];

        /**removiendo el nodo de $CfdisRelacionados en cado de no llevar documentos relaciondos*/
        if ($CfdisRelacionados == null) {
            unset($comprobante_cfdi['CfdisRelacionados']);
        }

        if ((string) $comprobante['TipoDeComprobante'] == 'P') {
            //unset($comprobante_cfdi['Comprobante']['MetodoPago']);
            unset($comprobante_cfdi['Comprobante']['FormaPago']);
            //unset($comprobante_cfdi['Comprobante']['Descuento']);
            //unset($comprobante_cfdi['Comprobante']['TipoCambio']);
            unset($comprobante_cfdi['Impuestos']);
            unset($comprobante_cfdi['Conceptos'][0]['Impuestos']);
            $comprobante_cfdi['Complemento']['Pago']                     = $pago_arreglo;
            $comprobante_cfdi['Complemento']['Pago']['DoctoRelacionado'] = $documentos_pagados_arreglo;
            /**agregando el nodo del pago */
        } else {
            unset($comprobante_cfdi['Comprobante']['xmlns:pago20']);
            unset($comprobante_cfdi['Complemento']['Pago']);
        }
        return $comprobante_cfdi;
    }

    public function get_cfdis_timbrados(Request $request, $folio_id = 'all', $paginated = false, $metodo_pago_id = 'all', $tipo_comprobante_id = 'all')
    {
        $cliente             = $request->cliente;
        $rfc                 = $request->rfc;
        $numero_control      = isset($request->numero_control) ? $request->numero_control : $folio_id;
        $metodo_pago_id      = isset($request->metodo_pago['value']) ? $request->metodo_pago['value'] : $metodo_pago_id;
        $tipo_comprobante_id = isset($request->tipo_comprobante_id) ? $request->tipo_comprobante_id : $tipo_comprobante_id;
        $fecha_inicio        = $request->fecha_inicio;
        $fecha_fin           = $request->fecha_fin;
        //$tipo_operacion_id   = $request->tipo_operacion_id;
        $status = $request->status;

        $resultado_query = Cfdis::select(
            'id',
            'uuid',
            'clientes_id',
            DB::raw(
                '(NULL) as cliente_nombre'
            ),
            DB::raw(
                '(NULL) as cliente_email'
            ),
            'serie',
            'fecha',
            'sat_formas_pago_id',
            'subtotal',
            'descuento',
            'total',
            DB::raw(
                '(NULL) as total_pagado'
            ),
            DB::raw(
                '(NULL) as total_egresos'
            ),
            DB::raw(
                '(NULL) as saldo_cfdi'
            ),
            DB::raw(
                '(NULL) as maximo_a_egresar'
            ),
            'sat_tipo_comprobante_id',
            'sat_metodos_pago_id',
            'rfc_emisor',
            'nombre_emisor',
            'sat_pais_id',
            'rfc_receptor',
            'nombre_receptor',
            'residencia_fiscal_receptor',
            'sat_usos_cfdi_id',
            'fecha_timbrado',
            'status',
            'nota',
            'tasa_iva',
            DB::raw(
                '(NULL) as tipo_comprobante_texto'
            ),
            DB::raw(
                '(NULL) as sat_uso_cfdi_texto'
            ),
            DB::raw(
                '(NULL) as sat_formas_pago_texto'
            ),
            DB::raw(
                '(NULL) as sat_metodos_pago_texto'
            ),
            DB::raw(
                '(NULL) as sat_pais_texto'
            ),
            DB::raw(
                '(NULL) as fecha_timbrado_texto'
            ),
            DB::raw(
                '(NULL) as situacion_pago_texto'
            ),
            DB::raw(
                '(NULL) as status_texto'
            )
        )
            ->with('servicios_funerarios.operacionFactura.servicio_funerario')
            ->with('pagos_asociados')
            ->with('egresos_asociados')
            ->with('cfdis_pagados')
            ->with('cfdis_egresados')
            ->with('cfdis_relacionados')
            ->with('cliente:id,nombre,email')
            ->whereHas('cliente', function ($query) use ($cliente) {
                $query->where('nombre', 'like', '%' . $cliente . '%');
            })
            ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                if (trim($fecha_inicio) != '' && trim($fecha_fin) != '') {
                    if ($fecha_fin != $fecha_inicio) {
                        $q->whereDate('fecha_timbrado', '>=', $fecha_inicio);
                        $q->whereDate('fecha_timbrado', '<=', $fecha_fin);
                    } else {
                        $q->whereDate('fecha_timbrado', '=', $fecha_inicio);
                    }
                }
            })
            /**en caso de que sea filtrado por operacion */
            //->with('cfdis_operaciones.operaciones.cliente')
            //->with('cfdis_operaciones.operaciones.venta_terreno')
            ->where(function ($q) use ($numero_control) {
                if (((float) (trim($numero_control))) > 0) {
                    /**filtro por numero de folio */
                    $q->where('cfdis.id', '=', $numero_control);
                }
            })
            ->where(function ($q) use ($numero_control) {
                if (((float) (trim($numero_control))) > 0) {
                    /**filtro por numero de folio */
                    $q->where('cfdis.id', '=', $numero_control);
                }
            })
            ->where(function ($q) use ($metodo_pago_id) {
                if (((float) (trim($metodo_pago_id))) > 0) {
                    /**filtro por numero de folio */
                    $q->where('cfdis.sat_metodos_pago_id', '=', $metodo_pago_id);
                }
            })
            ->where(function ($q) use ($tipo_comprobante_id) {
                if (((float) (trim($tipo_comprobante_id))) > 0) {
                    /**filtro por numero de folio */
                    $q->where('cfdis.sat_tipo_comprobante_id', '=', $tipo_comprobante_id);
                }
            })
            ->where(function ($q) use ($rfc) {
                if (((trim($rfc))) != '') {
                    $q->where('cfdis.rfc_receptor', '=', $rfc);
                }
            })
            ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('cfdis.status', '=', $status);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        $resultado = [];
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado       = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado       = &$resultado_query;
        }

        $formas_pago  = SatFormasPago::select('*')->get();
        $metodos_pago = MetodosPago::select('*')->get();
        $sat_paises   = SatPais::select('*')->get();
        $sat_usos     = SatUsosCfdi::select('*')->get();
        foreach ($resultado as $index_cfdi => &$cfdi) {
            //datos del servicio funerario
            //aqui voy
            if (isset($cfdi['servicios_funerarios'])) {
                //tiene operaciones asociadas
                foreach ($cfdi['servicios_funerarios'] as &$servicio) {
                    if (count($servicio['operacion_factura']) > 0) {
                        if ($servicio['operacion_factura'][0]['empresa_operaciones_id'] == 3) {
                            //es tipo servicio funerario
                            $servicio['operacion_factura'][0]['servicio_funerario']['fecha_defuncion_texto'] = fecha_abr($servicio['operacion_factura'][0]['servicio_funerario']['fechahora_defuncion']);
                        }
                    }
                }
            }
            /**status */
            if ($cfdi['status'] == 1) {
                $cfdi['status_texto'] = 'Vigente';
            } else if ($cfdi['status'] == 3) {
                $cfdi['status_texto'] = 'En proceso de cancelación';
            } else {
                $cfdi['status_texto'] = 'Cancelado';
            }

            /**tipo de comprobante */
            if ($cfdi['sat_tipo_comprobante_id'] == 1) {
                $cfdi['tipo_comprobante_texto'] = 'Ingreso (I)';

                /**sacando el total de egresos asociados */
                $total_egresado = 0;
                foreach ($cfdi['egresos_asociados'] as $key_egreso => $egreso) {
                    if ($egreso['status'] == 1) {
                        $total_egresado += $egreso['monto_relacion'];
                    }
                }

                $cfdi['total_egresos'] = $total_egresado;

                if ($cfdi['sat_metodos_pago_id'] == 1) {
                    /**pue */
                    $cfdi['total_pagado']     = $cfdi['total'];
                    $cfdi['saldo_cfdi']       = 0;
                    $cfdi['maximo_a_egresar'] = round($cfdi['total'] - $total_egresado);
                    /**aunque tenga egresos asociados el documento quedo liquidado */
                } else {
                    /**ppd */
                    $pagado = 0;
                    foreach ($cfdi['pagos_asociados'] as $key_pago => $pago) {
                        if ($pago['status'] == 1) {
                            $pagado += $pago['monto_relacion'];
                        }
                    }
                    $cfdi['total_pagado']     = round($pagado, 2);
                    $cfdi['saldo_cfdi']       = $cfdi['total'] - $cfdi['total_pagado'] - $cfdi['total_egresos'];
                    $cfdi['maximo_a_egresar'] = $cfdi['total'] - $cfdi['total_pagado'] - $total_egresado;

                    if ($cfdi['status'] == 1) {
                        if ($pagado >= $cfdi['total']) {
                            $cfdi['situacion_pago_texto'] = 'Pagado';
                        } else {
                            $cfdi['situacion_pago_texto'] = 'Por liquidar';
                        }
                    } else {
                        $cfdi['situacion_pago_texto'] = 'CFDI no válido';
                    }
                }
            } elseif ($cfdi['sat_tipo_comprobante_id'] == 2) {
                $cfdi['tipo_comprobante_texto'] = 'Egreso (E)';
            } elseif ($cfdi['sat_tipo_comprobante_id'] == 5) {
                $cfdi['tipo_comprobante_texto'] = 'Pago (P)';
            } else {
                return $this->errorResponse('Error al cargar los datos del cfdi.', 409);
            }
            /**sat_formas_pago_id */
            foreach ($formas_pago as $key => $forma) {
                if ($cfdi['sat_formas_pago_id'] == $forma->id) {
                    $cfdi['sat_formas_pago_texto'] = $forma->forma . ' (' . $forma->id . ')';
                    break;
                }
            }
            /**sat_formas_pago_id */
            foreach ($metodos_pago as $key => $metodo) {
                if ($cfdi['sat_metodos_pago_id'] == $metodo->id) {
                    $cfdi['sat_metodos_pago_texto'] = $metodo->clave;
                    break;
                }
            }
            /**sat_formas_pago_id */
            foreach ($sat_paises as $key => $pais) {
                if ($cfdi['sat_pais_id'] == $pais->id) {
                    $cfdi['sat_pais_texto'] = $pais->pais . ' (' . $pais->clave . ')';
                    break;
                }
            }
            /**sat_usos_cfdi_id */
            foreach ($sat_usos as $key => $uso) {
                if ($cfdi['sat_usos_cfdi_id'] == $uso->id) {
                    $cfdi['sat_uso_cfdi_texto'] = $uso->uso . ' (' . $uso->clave . ')';
                    break;
                }
            }

            /**cliente */
            if (isset($cfdi['cliente'])) {
                $cfdi['cliente_nombre'] = $cfdi['cliente']['nombre'];
                $cfdi['cliente_email']  = $cfdi['cliente']['email'];
            }

            $cfdi['fecha_timbrado_texto'] = fecha_abr($cfdi['fecha_timbrado']);
            /**limpiando datos innecesarios */
            //unset($cfdi['sat_tipo_comprobante_id']);
            unset($cfdi['sat_formas_pago_id']);
            //unset($cfdi['sat_metodos_pago_id']);
            unset($cfdi['sat_pais_id']);
            unset($cfdi['sat_usos_cfdi_id']);
            unset($cfdi['fecha_timbrado']);
            unset($cfdi['cliente']);
            unset($cfdi['sat_formas_pago_id']);
        }
        return $resultado_query;
    }

    /**
     * Lista CFDI en páginas de 15 con filtros combinables. Los límites de timbrado
     * incluyen los días seleccionados y se pueden usar por separado.
     */
    public function FiltrarCfdis(Request $request)
    {
        $consulta = $this->consultaFiltrarCfdis($request);
        $tiposCfdi = $this->get_tipos_comprobante()->pluck('tipo', 'id');
        $metodos = MetodosPago::pluck('clave', 'id');
        $pagina = $consulta->orderByDesc('id')->paginate(15);
        $pagina->getCollection()->transform(function ($cfdi) use ($tiposCfdi, $metodos) {
            $saldo = null;
            if ((int) $cfdi->sat_tipo_comprobante_id === 1) {
                $saldo = (int) $cfdi->sat_metodos_pago_id === 1 ? 0 :
                    round($cfdi->total - $cfdi->pagos_asociados->where('status', 1)->sum('monto_relacion')
                        - $cfdi->egresos_asociados->where('status', 1)->sum('monto_relacion'), 2);
            }

            return [
                'id' => $cfdi->id,
                'uuid' => $cfdi->uuid,
                'cliente_nombre' => optional($cfdi->cliente)->nombre ?: $cfdi->nombre_receptor,
                'rfc_receptor' => $cfdi->rfc_receptor,
                'fecha_timbrado_texto' => $cfdi->fecha_timbrado ? fecha_abr($cfdi->fecha_timbrado) : null,
                'tipo_comprobante_texto' => $tiposCfdi->get($cfdi->sat_tipo_comprobante_id),
                'sat_metodos_pago_texto' => $metodos->get($cfdi->sat_metodos_pago_id),
                'status' => $cfdi->status,
                'status_texto' => [0 => 'Cancelado', 1 => 'Vigente', 3 => 'En proceso de cancelación'][$cfdi->status] ?? 'Cancelado',
                'total' => $cfdi->total,
                'saldo_cfdi' => $saldo,
                'nota' => $cfdi->nota,
                'operaciones_count' => (int) $cfdi->operaciones_count,
            ];
        });

        return $pagina;
    }

    /** Comparte los criterios de búsqueda entre la pantalla y el reporte de ingresos. */
    private function consultaFiltrarCfdis(Request $request, $ingresosPorDefecto = false)
    {
        $referencias = [
            1 => 'ventas_terrenos_id',
            2 => 'ventas_terrenos_id',
            3 => 'servicios_funerarios_id',
            4 => 'ventas_planes_id',
            5 => 'ventas_generales_id',
        ];
        $filtros = $request->validate([
            'folio' => 'nullable|integer|min:1',
            'uuid' => 'nullable|string|max:36',
            'cliente' => 'nullable|string|max:150',
            'rfc' => 'nullable|string|max:13',
            'tipo_cfdi' => 'nullable|integer|exists:sat_tipo_comprobante,id',
            'status' => 'nullable|in:0,1,3',
            'metodo_pago' => 'nullable|in:PUE,PPD',
            'saldo_pendiente' => 'nullable|in:1',
            'desde' => 'nullable|date_format:Y-m-d',
            'hasta' => 'nullable|date_format:Y-m-d',
            'operaciones_relacionadas' => 'nullable|in:con,sin',
            'tipos_servicio' => 'nullable|array|max:5',
            'tipos_servicio.*' => 'integer|distinct|in:1,2,3,4,5',
            'numero_servicio' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
        ]);

        if (isset($filtros['desde'], $filtros['hasta']) && $filtros['hasta'] < $filtros['desde']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'hasta' => 'La fecha final debe ser igual o posterior a la inicial.',
            ]);
        }

        $consulta = Cfdis::query()->select([
            'id', 'uuid', 'clientes_id', 'nombre_receptor', 'rfc_receptor', 'fecha_timbrado',
            'sat_tipo_comprobante_id', 'sat_metodos_pago_id', 'status', 'total', 'nota',
        ])->selectRaw('(SELECT COUNT(DISTINCT co.operaciones_id) FROM cfdis_operaciones co
            INNER JOIN operaciones o ON o.id = co.operaciones_id WHERE co.cfdis_id = cfdis.id) AS operaciones_count')
            ->with(['cliente:id,nombre', 'pagos_asociados', 'egresos_asociados']);

        if ($ingresosPorDefecto && !$request->filled('tipo_cfdi')) {
            $consulta->where('sat_tipo_comprobante_id', 1);
        }

        foreach (['folio' => 'id', 'tipo_cfdi' => 'sat_tipo_comprobante_id', 'status' => 'status', 'rfc' => 'rfc_receptor'] as $filtro => $campo) {
            if ($request->filled($filtro)) {
                $consulta->where($campo, $filtros[$filtro]);
            }
        }

        if (isset($filtros['metodo_pago'])) {
            $consulta->whereIn('sat_metodos_pago_id', function ($metodos) use ($filtros) {
                $metodos->select('id')->from('sat_metodos_pago')->where('clave', $filtros['metodo_pago']);
            });
        }

        // Replica el saldo del listado: solo ingresos PPD vigentes, con pagos y egresos vigentes aplicados.
        if ($request->filled('saldo_pendiente')) {
            $consulta->where('cfdis.status', 1)->where('cfdis.sat_tipo_comprobante_id', 1)
                ->where('cfdis.sat_metodos_pago_id', 2)
                ->whereRaw('ROUND(cfdis.total - COALESCE((
                    SELECT SUM(r.monto_relacion)
                    FROM cfdis_tipo_relacion r
                    INNER JOIN cfdis relacionado ON relacionado.id = r.cfdis_id
                    WHERE r.cfdis_id_relacionado = cfdis.id
                        AND r.tipo_relacion_id IN (2, 3) AND relacionado.status = 1
                ), 0), 2) > 0');
        }

        if ($request->filled('uuid')) {
            $consulta->where('uuid', 'like', trim($filtros['uuid']).'%');
        }
        if ($request->filled('cliente')) {
            $nombre = '%'.trim($filtros['cliente']).'%';
            $consulta->where(function ($q) use ($nombre) {
                $q->where('nombre_receptor', 'like', $nombre)
                    ->orWhereHas('cliente', function ($cliente) use ($nombre) {
                        $cliente->where('nombre', 'like', $nombre);
                    });
            });
        }
        if ($request->filled('desde')) {
            $consulta->where('fecha_timbrado', '>=', $filtros['desde'].' 00:00:00');
        }
        if ($request->filled('hasta')) {
            $fin = \Carbon\Carbon::parse($filtros['hasta'])->addDay()->toDateString();
            $consulta->where('fecha_timbrado', '<', $fin.' 00:00:00');
        }

        if (isset($filtros['operaciones_relacionadas'])) {
            $this->filtrarCfdisPorOperacion($consulta, [], null, [], $filtros['operaciones_relacionadas'] === 'sin');
        }

        $tipos = $filtros['tipos_servicio'] ?? [];
        $numero = $filtros['numero_servicio'] ?? null;
        if ($tipos || $numero !== null) {
            $this->filtrarCfdisPorOperacion($consulta, $tipos, $numero, $referencias);
        }

        return $consulta;
    }

    /**
     * Busca una operación vinculada directamente al CFDI que cumpla el tipo
     * y la referencia comercial. EXISTS evita duplicar comprobantes.
     */
    private function filtrarCfdisPorOperacion($consulta, array $tipos, $numero, array $referencias, $sinOperaciones = false)
    {
        $consulta->whereExists(function ($operaciones) use ($tipos, $numero, $referencias) {
            $operaciones->selectRaw('1')->from('cfdis_operaciones as co')
                ->join('operaciones as o', 'o.id', '=', 'co.operaciones_id')
                ->whereColumn('co.cfdis_id', 'cfdis.id');

            if ($tipos) {
                $operaciones->whereIn('o.empresa_operaciones_id', $tipos);
            }
            if ($numero !== null) {
                $operaciones->where(function ($referencia) use ($numero, $referencias) {
                    foreach ($referencias as $tipo => $campo) {
                        $referencia->orWhere(function ($operacion) use ($tipo, $campo, $numero) {
                            $operacion->where('o.empresa_operaciones_id', $tipo)->where('o.'.$campo, $numero);
                        });
                    }
                });
            }
        }, 'and', $sinOperaciones);
    }

    /** Consulta las operaciones directas y elimina vínculos duplicados por CFDI. */
    private function consultaOperacionesCfdi()
    {
        return DB::table('cfdis_operaciones as co')
            ->join('operaciones as o', 'o.id', '=', 'co.operaciones_id')
            ->leftJoin('clientes as c', 'c.id', '=', 'o.clientes_id')
            ->select('co.cfdis_id as cfdi_id', 'o.id', 'o.empresa_operaciones_id', 'o.ventas_terrenos_id', 'o.cuotas_cementerio_id',
                'o.servicios_funerarios_id', 'o.ventas_planes_id', 'o.ventas_generales_id',
                'o.fecha_operacion', 'o.status', 'o.total', 'o.saldo', 'c.nombre as cliente')
            ->distinct()->orderByDesc('o.fecha_operacion')->orderByDesc('o.id');
    }

    /** Agrupa ingresos filtrados con sus operaciones y aplicaciones de pagos y egresos. */
    public function previewListadoCfdi(Request $request)
    {
        $consulta = $this->consultaFiltrarCfdis($request, true);
        $tipo = (int) $request->input('tipo_cfdi', 1) ?: 1;
        $esIngreso = $tipo === 1;
        $relacionAplicada = [2 => 'cfdis_egresados', 5 => 'cfdis_pagados'][$tipo] ?? null;
        if (!$esIngreso) {
            $consulta->setEagerLoads([]);
            $consulta->with($relacionAplicada ? ['cliente:id,nombre', $relacionAplicada] : ['cliente:id,nombre']);
        }
        $cfdis = $consulta->addSelect('sat_monedas_id')->orderByDesc('id')->get();
        $operaciones = $cfdis->isEmpty() ? collect() : $this->consultaOperacionesCfdi()
            ->whereIn('co.cfdis_id', $cfdis->modelKeys())
            ->get()->groupBy('cfdi_id');
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $monedas = DB::table('sat_monedas')->pluck('codigo_moneda', 'id');
        $resumen = [];
        foreach ($cfdis as $cfdi) {
            $moneda = $monedas->get($cfdi->sat_monedas_id) ?: 'Sin moneda';
            $resumen[$moneda] = $resumen[$moneda] ?? ['cantidad' => 0, 'total' => 0, 'saldo' => 0, 'aplicado' => 0];
            $cfdi->importe_aplicado_reporte = $relacionAplicada ? $cfdi->{$relacionAplicada}->sum('monto_relacion') : null;
            $cfdi->saldo_reporte = !$esIngreso || (int) $cfdi->sat_metodos_pago_id === 1 ? 0 :
                round($cfdi->total - $cfdi->pagos_asociados->where('status', 1)->sum('monto_relacion')
                    - $cfdi->egresos_asociados->where('status', 1)->sum('monto_relacion'), 2);
            $resumen[$moneda]['cantidad']++;
            $resumen[$moneda]['total'] += $cfdi->total;
            $resumen[$moneda]['saldo'] += $cfdi->saldo_reporte;
            $resumen[$moneda]['aplicado'] += $cfdi->importe_aplicado_reporte ?? 0;
        }

        $tipoTexto = $this->get_tipos_comprobante()->pluck('tipo', 'id')->get($tipo, 'CFDI');
        $titulo = 'Reporte del listado de CFDI · '.$tipoTexto;
        $visor = new \App\Services\VisorDocumentos('facturacion_listado_cfdi_');
        $fecha = now('America/Mazatlan')->format('d/m/Y H:i');
        $tipos = collect($this->get_empresa_tipo_operaciones())->pluck('tipo', 'id');
        $datos = [
            'titulo' => $titulo,
            'cfdis' => $cfdis,
            'operaciones' => $operaciones,
            'tiposOperacion' => $tipos,
            'metodos' => MetodosPago::pluck('clave', 'id'),
            'monedas' => $monedas,
            'empresa' => $empresa,
            'resumen' => $resumen,
            'esIngreso' => $esIngreso,
            'tipoTexto' => $tipoTexto,
            'filtros' => $request->only(['folio', 'uuid', 'cliente', 'rfc', 'status', 'metodo_pago',
                'desde', 'hasta', 'operaciones_relacionadas', 'tipos_servicio', 'numero_servicio', 'saldo_pendiente']),
            'fecha' => $fecha,
            'logo' => env('LOGOJPG') ? public_path(env('LOGOJPG')) : null,
        ];
        $pdf = PDF::loadView('reportes.temporales_visor_excel.facturacion.listado_cfdi', $datos)->setOptions([
            'title' => $titulo, 'page-size' => 'Letter', 'orientation' => 'Portrait',
            'margin-top' => 10, 'margin-right' => 10, 'margin-bottom' => 14, 'margin-left' => 10,
            'footer-html' => view('reportes.temporales_visor_excel.compartidos.footer_pdf', [
                'dominio' => \App\Exports\NombreEmpresaReportes::formatear($empresa['nombre_comercial'] ?? null)
                    . ' · ' . ($empresa['telefono'] ?? '') . ' · ' . ($empresa['email'] ?? ''),
                'fechaImpresion' => $fecha,
            ]),
        ]);

        $captura = $visor->capturar($pdf);
        $captura['total_registros'] = $cfdis->count();
        $respuesta = $visor->preparar($captura, 'listado-ingresos', $titulo, null, $request->user('api'),
            $cfdis->count() . ' CFDI · '.$tipoTexto.' · PDF y Excel');
        $vista = $respuesta->getData(true);
        $vista['soloPdf'] = false;
        $vista['nombreArchivo'] = preg_replace('/\.pdf$/i', '.xlsx', $vista['nombreArchivo']);
        $vista['nota'] = 'El PDF y el Excel se generan al descargarlos.';
        \Illuminate\Support\Facades\Cache::put('facturacion_listado_excel_'.$vista['tokenDescarga'], [
            'usuario_id' => (int) $request->user('api')->id,
            'datos' => $datos,
            'nombre' => $vista['nombreArchivo'],
        ], \Carbon\Carbon::parse($vista['venceEn']));
        return $respuesta->setData($vista);
    }

    /** Exporta los mismos resultados y filtros guardados al abrir el visor. */
    public function descargarListadoCfdiExcel(Request $request, $token)
    {
        abort_unless(preg_match('/^[a-f0-9]{64}$/D', $token), 404);
        $reporte = \Illuminate\Support\Facades\Cache::get('facturacion_listado_excel_'.$token);
        abort_unless($reporte, 410, 'La consulta del reporte venció. Actualice el reporte.');
        abort_unless($reporte['usuario_id'] === (int) $request->user('api')->id, 403);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ListadoCfdiExport($reporte['datos']), $reporte['nombre']
        );
    }

    /** Descarga la instantánea del listado que se abrió en el visor. */
    public function descargarListadoCfdi(Request $request, $token)
    {
        return (new \App\Services\VisorDocumentos('facturacion_listado_cfdi_'))
            ->descargar($request, $token, 'listado-ingresos');
    }



    /** Prepara el resumen de un CFDI y las operaciones vinculadas directamente. */
    public function previewOperacionesCfdi(Request $request)
    {
        $request->validate(['folio_id' => 'required|integer|min:1']);
        $cfdi = Cfdis::with(['cliente:id,nombre'])->findOrFail($request->input('folio_id'));
        $operaciones = $this->consultaOperacionesCfdi()->where('co.cfdis_id', $cfdi->id)->get();

        $titulo = 'CFDI y operaciones relacionadas';
        $visor = new \App\Services\VisorDocumentos('facturacion_operaciones_cfdi_');
        $pdf = PDF::loadView('reportes.temporales_visor_excel.facturacion.operaciones_cfdi', [
            'titulo' => $titulo,
            'cfdi' => $cfdi,
            'operaciones' => $operaciones,
            'tiposOperacion' => collect($this->get_empresa_tipo_operaciones())->pluck('tipo', 'id'),
            'tipoCfdi' => $this->get_tipos_comprobante()->pluck('tipo', 'id')->get($cfdi->sat_tipo_comprobante_id),
            'metodoPago' => MetodosPago::where('id', $cfdi->sat_metodos_pago_id)->value('clave'),
            'moneda' => DB::table('sat_monedas')->where('id', $cfdi->sat_monedas_id)->value('codigo_moneda'),
            'fechaConsulta' => now('America/Mazatlan')->format('d/m/Y H:i'),
            'logo' => env('LOGOJPG') ? public_path(env('LOGOJPG')) : null,
        ])->setOptions([
            'title' => $titulo, 'page-size' => 'Letter',
            'margin-top' => 12, 'margin-right' => 12, 'margin-bottom' => 12, 'margin-left' => 12,
        ]);

        return $visor->preparar($visor->capturar($pdf), 'operaciones', $titulo, $cfdi->id,
            $request->user('api'), 'Folio ' . $cfdi->id . ' · Operaciones vinculadas: ' . $operaciones->count());
    }

    /** Descarga el PDF correspondiente a la vista previa del usuario autenticado. */
    public function descargarOperacionesCfdi(Request $request, $token)
    {
        return (new \App\Services\VisorDocumentos('facturacion_operaciones_cfdi_'))
            ->descargar($request, $token, 'operaciones');
    }



    /** Visor de CFDI: captura HTML con los mismos datos/XML y validaciones del PDF
     * anterior. Catálogo cerrado, ampliable sin crear rutas por cada documento. */
    private const DOCUMENTOS_CFDI_VISOR = ['cfdi' => 'Comprobante Fiscal Digital'];

    public function previewDocumentoCfdi(Request $request, $documento)
    {
        abort_unless(isset(self::DOCUMENTOS_CFDI_VISOR[$documento]), 404);
        $usuario = $request->user('api');
        abort_unless($usuario, 401);
        $request->validate(['folio_id' => 'required|integer|min:1']);
        $id = (int) $request->input('folio_id');
        $interno = clone $request;
        $interno->replace(['email_send' => 'false', 'request_parent' => [json_encode(['folio_id' => $id])]]);
        $interno->attributes->set('_cfdi_visor', true);
        $captura = $this->get_cfdi_pdf($interno);
        if ($captura instanceof \Symfony\Component\HttpFoundation\Response) return $captura;
        return (new \App\Services\VisorDocumentos('facturacion_cfdi_'))->preparar(
            $captura, $documento, self::DOCUMENTOS_CFDI_VISOR[$documento], $id, $usuario, 'CFDI núm. '.$id.' · Documento PDF');
    }

    /** El PDF se produce desde la instantánea privada al descargar. */
    public function descargarDocumentoCfdi(Request $request, $documento, $token)
    {
        abort_unless(isset(self::DOCUMENTOS_CFDI_VISOR[$documento]), 404);
        return (new \App\Services\VisorDocumentos('facturacion_cfdi_'))->descargar($request, $token, $documento);
    }

    /** Adjuntar también el XML original, como el envío anterior. El id y la ruta
     * se obtienen de la instantánea del servidor, nunca de campos del navegador. */
    public function enviarDocumentoCfdiCorreo(Request $request, $documento, $token)
    {
        abort_unless(isset(self::DOCUMENTOS_CFDI_VISOR[$documento]), 404);
        $usuario = $request->user('api');
        abort_unless($usuario, 401);
        abort_unless(is_string($token) && preg_match('/^[a-f0-9]{64}$/D', $token), 404);
        $datos = \Illuminate\Support\Facades\Cache::get('facturacion_cfdi_'.$token);
        abort_unless($datos, 410, 'La consulta del reporte venció. Actualice el reporte.');
        abort_unless((int) $datos['usuario_id'] === (int) $usuario->id, 403);
        abort_unless($datos['documento'] === $documento, 404);
        $id = (int) $datos['venta_id'];
        $disco = Storage::disk(env('STORAGE_DISK_XML', 'cfdis'));
        abort_unless($disco->exists($id.'.xml'), 409, 'No se encontró el XML del CFDI para adjuntar.');
        return (new \App\Services\VisorDocumentos('facturacion_cfdi_'))->enviarCorreo($request, $token, $documento, [
            ['contenido' => $disco->get($id.'.xml'), 'nombre' => 'XML FOLIO '.$id.'.xml', 'mime' => 'application/xml'],
        ]);
    }

    public function get_cfdi_pdf(Request $request, $folio = '')
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        if (isset($request->request_parent)) {
            $status_actualizado = null;
            $email              = $request->email_send === 'true' ? true : false;
            $email_to           = $request->email_address;
            $requestVentasList  = json_decode($request->request_parent[0], true);
            $folio_id           = $requestVentasList['folio_id'];
            try {
                //code...
                //actualizamos cfdis en caso de que este en produccion
                //get cfdi status rapido para evitar hacer la peticion al sat
                $cfdi = Cfdis::select("status")->where("id", $requestVentasList['folio_id'])->first();

                if (ENV('APP_ENV') == 'production' && $cfdi["status"] != 0) {
                    $checando_cfdi = $this->get_cfdi_status_sat($requestVentasList['folio_id'], $request);
                    if (isset($checando_cfdi['estado'])) {
                        if ($checando_cfdi['estado'] == 'No Encontrado') {
                            //return $this->errorResponse('El CFDI ' . $checando_cfdi['uuid'] . ' no se encuentra en la base de datos del SAT.', 409);
                            $status_actualizado = false;
                        } else {
                            $status_actualizado = true;
                        }
                    }
                }
            } catch (\Throwable $th) {
                //throw $th;
                $status_actualizado = false;
            }
        } else {
            $folio_id = $folio;
            $email    = false;
            $email_to = 'hector@gmail.com';
        }

        $myRequest = new Request();
        $myRequest->request->add(['test' => 'test']);
        $cfdi = ($this->get_cfdis_timbrados($myRequest, $folio_id)[0] ?? null);

        if (empty($cfdi)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos del cfdi.', 409);
        }

        //obtengo la informacion de esa venta
        $datos = $this->leer_xml($folio_id, '');
        if (empty($datos)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos del xml.', 409);
        }

        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();

        /**determinando que tipo de formato se va utilizar */

        $documento = '';

        if ($datos['Sistema']['tipo_comprobante_id'] == 1 || $datos['Sistema']['tipo_comprobante_id'] == 2) {
            /**ingreso o egreso */
            $documento = 'facturacion/cfdi/cfdi_ingreso_egreso';
        } else {
            /**pago */
            $documento = 'facturacion/cfdi/cfdi_pago';
        }

        $pdf      = PDF::loadView($documento, ['datos' => $datos, 'cfdi' => $cfdi, 'empresa' => $empresa]);
        $name_pdf = "FACTURA FOLIO " . strtoupper($datos['Comprobante']['Folio']) . '.pdf';

        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view('facturacion.cfdi.footer', ['empresa' => $empresa]),
        ]);
        if (!in_array((int) $datos['Sistema']['tipo_comprobante_id'], [1, 2], true)) {
        if ($cfdi['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('facturacion.cfdi.header'),
            ]);
        } else {
            /**verificando si lleva leyenda pagado */
            if ($cfdi['sat_tipo_comprobante_id'] == 1) {
                if ($cfdi['saldo_cfdi'] <= 0) {
                    $pdf->setOptions([
                        'header-html' => view('facturacion.cfdi.header_pagado'),
                    ]);
                }
            }
        }

        } // Ingreso/egreso muestran cancelación y pago en el encabezado comercial.

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 8.4);
        $pdf->setOption('margin-bottom', 12.4);
        $pdf->setOption('page-size', 'Letter');
        // Los formatos comerciales I/E/P ajustan el cuerpo antes de generar el PDF.
        if (in_array((int) $datos['Sistema']['tipo_comprobante_id'], [1, 2, 5], true)) {
            $pdf->setOption('javascript-delay', 500);
        }
        // La vista inicial solo captura HTML/opciones; no ejecutar inline ni output.
        if ($request->attributes->get('_cfdi_visor')) {
            return (new \App\Services\VisorDocumentos('facturacion_cfdi_'))->capturar($pdf);
        }
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
                'Facturas Aeternus',
                "FACTURA FOLIO " . strtoupper($datos['Comprobante']['Folio']),
                $name_pdf,
                $pdf,
                'cfdi',
                $datos['Comprobante']['Folio']
            );
            return $enviar_email;
            /**email fin */
        } else {
            if (isset($request->agregar_zip)) {
                return $pdf->save(public_path('/') . $folio_id . '.pdf');
            } else {
                return $pdf->inline($name_pdf);
            }
        }
    }

    public function get_cfdi_download(Request $request, $folio = '')
    {
        $zip_file = 'cdfi.zip'; // Name of our archive to download

        if (File::exists(public_path($zip_file))) {
            File::delete(public_path($zip_file));
        }

        $pdf = $folio . '.pdf';
        if (File::exists(public_path($pdf))) {
            File::delete(public_path($pdf));
        }

        // Initializing PHP class
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zipfile1 = 'zip.txt';

        $xml      = $folio . '.xml';
        $path_xml = Storage::disk('cfdis')->path($xml);

        $request->merge([
            'agregar_zip' => true,
        ]);
        $this->get_cfdi_pdf($request, $folio);
        // Adding file: second parameter is what will the path inside of the archive
        // So it will create another folder called "storage/" inside ZIP, and put the file there.
        $zip->addFile($path_xml, $xml);
        $zip->addFile(public_path($pdf), $pdf);
        $zip->close();
        if (File::exists(public_path($pdf))) {
            File::delete(public_path($pdf));
        }
        // We return the file immediately after download
        return response()->download($zip_file);
    }

    public function get_cfdi_status_sat($folio = '', Request $request)
    {
        $cfdi = Cfdis::where('id', $folio)->first();
        if (empty($cfdi)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos del cfdi.', 409);
        }
        $xml = $this->leer_xml($folio);
        if (empty($xml)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos del xml.', 409);
        }
        // dd($cfdi);
        /**datos para la consulta */
        $comprobante = new Comprobante();
        $cfdi        = Cfdis::with('timbro')->with('cliente')->where('id', '=', $folio)->first();
        //dd($cfdi);
        $comprobante->rfcEmisor   = $cfdi->rfc_emisor;
        $comprobante->rfcReceptor = $cfdi->rfc_receptor;
        $comprobante->uuid        = trim($cfdi['uuid']);
        $comprobante->totalCFDI   = $cfdi['sat_tipo_comprobante_id'] != 5 ? $xml['Comprobante']['Total'] : "0";
        $comprobante->selloCFDI   = trim($xml['Complemento']['TimbreFiscalDigital']['SelloCFD']);
        $url_cancelar             = '';
        if (ENV('APP_ENV') === 'local') {
            $usuario      = ENV('USER_PAC_DEV');
            $password     = ENV('PASSWORD_PAC_DEV');
            $url_cancelar = ENV('WEB_SERVICE_CONSULTA_DEVELOP');
        } else {
            $usuario      = ENV('USER_PAC_PROD');
            $password     = ENV('PASSWORD_PAC_PROD');
            $url_cancelar = ENV('WEB_SERVICE_CONSULTA_PRODUCTION');
        }
        $autentica            = new Autenticar();
        $autentica->usuario   = $usuario;
        $autentica->password  = $password;
        $comprobante->accesos = $autentica;
        $client               = new SoapClient($url_cancelar);
        $result               = $client->ConsultarEstatusCFDI_2($comprobante);
        /**determinando status segun el resultado de la respuesta del sat */
        /**
         * codigoEstatus
         * esCancelable
         * estado
         * estatusCancelacion
         * Código Estatus    Descripción    Observaciones
         * N 601    La expresión impresa proporcionada no es válida    Este código de respuesta se presentará cuando la petición de validación no se haya respetado en el formato definido.
         * N 602    Comprobante no encontrado    Este código de respuesta se presentará cuando el UUID del comprobante no se encuentre en la Base de Datos del SAT.
         * S    Comprobante obtenido satisfactoriamente    Este código se presentará cuando el UUID del comprobante se encuentre en la Base de Datos del SAT
         */

        /** esCancelable
         * Cancelable sin aceptación
         * Cancelable con aceptación
         * No cancelable.
         */
        /*
            Respuesta a la cancelación
            Una vez terminado el proceso de cancelación, el emisor recibirá la respuesta a su solicitud que incluirá el nuevo estado del CFDI junto con el estado de cancelación.
            Cuando el CFDI se canceló sin necesitar la autorización del receptor, este tendrá el estado de "Cancelado" y un estatus de cancelación de "Cancelado sin aceptación".
            Cuando el CFDI se canceló con autorización del receptor, este tendrá el estado de "Cancelado" y un estatus de cancelación de "Cancelado con aceptación".
            Cuando el CFDI se canceló porque se le solicitó autorización al receptor, este no dio respuesta y después de las 72 horas se dio por cancelado el comprobante; este tendrá el estado de "Cancelado" y un estatus de cancelación de "Cancelado plazo vencido".
            Cuando el CFDI NO se canceló debido a que el receptor rechazó la solicitud, este tendrá el estado de "Vigente" y un estatus de cancelación de "Solicitud rechazada".
          */
        if ($result->return->codigoEstatus === "S - Comprobante obtenido satisfactoriamente.") {
            /**si existe en el sat */
            if ($result->return->estado === "Cancelado") {
                /**se actualiza la base de datos */
                $fecha_cancelacion = date("Y-m-d H:i:s");
                DB::table('cfdis')->where('id', '=', $folio)->update(
                    [
                        'status'            => 0,
                        'cancelo_id'        => (int) ($request->user()->id ?? 1),
                        'fecha_cancelacion' => $fecha_cancelacion,
                    ]
                );
            } elseif ($result->return->estado === 'En proceso') {
                /**se actualiza la base de datos */
                DB::table('cfdis')->where('id', '=', $folio)->update(
                    [
                        'status'            => 3,
                        'fecha_cancelacion' => null,
                    ]
                );
            } else {
                //esta vigente el cfdi
                DB::table('cfdis')->where('id', '=', $folio)->update(
                    [
                        'status'            => 1,
                        'cancelo_id'        => null,
                        'fecha_cancelacion' => null,
                        'acuse_cancelacion' => null,
                    ]
                );
            }
        } else {
            return $this->errorResponse('CFDI no encontrado en la base de datos del SAT.', 409);
        }
        $retorno['codigoEstatus']      = $result->return->codigoEstatus;
        $retorno['esCancelable']       = $result->return->esCancelable;
        $retorno['estado']             = $result->return->estado;
        $retorno['estatusCancelacion'] = $result->return->estatusCancelacion;
        $retorno['uuid']               = $comprobante->uuid;
        return $retorno;
    }

    public function cancelar_cfdi_folio(Request $request)
    {
        $validaciones = [
            /**validacion de datos para el cfdi */
            'id' => 'required|integer|min:1',
        ];

        /**MENSAJES DE LAS VALIDACIONES*/
        $mensajes = [
            'integer'  => 'Ingrese un número entero',
            'required' => 'Dato obligatorio',
            'min'      => "Este valor debe ser mínimo :min",
        ];

        /**VALIDANDO LOS DATOS */
        request()->validate(
            $validaciones,
            $mensajes
        );

        if (ENV('APP_ENV') != 'production') {
            return $this->errorResponse('Esta operación es solo para usuarios en servidor.', 409);
        }
        //actualizamos cfdis en caso de que este en produccion
        $checando_cfdi = $this->get_cfdi_status_sat($request->id, $request);
        if (isset($checando_cfdi['estado'])) {
            if ($checando_cfdi['estado'] == 'No Encontrado') {
                return $this->errorResponse('El CFDI ' . $checando_cfdi['uuid'] . ' no se encuentra en la base de datos del SAT.', 409);
            }
        }
        if (isset($checando_cfdi['estado'])) {
            if ($checando_cfdi['estado'] == 'No Encontrado') {
                return $this->errorResponse('El CFDI ' . $checando_cfdi['uuid'] . ' no se encuentra en la base de datos del SAT.', 409);
            }
        }
        //verifico si es cancelable
        if ($checando_cfdi['esCancelable'] == "No cancelable") {
            return $this->errorResponse('El CFDI ' . $request->id . ' es no cancelable, según respuesta del SAT.', 409);
        }
        //verifico status actual del cfdi
        if ($checando_cfdi['estado'] == 'Cancelado') {
            return $this->errorResponse('El CFDI ' . $request->id . ' ya ha sido ' . $checando_cfdi['estatusCancelacion'] . '.', 409);
        }
        //verifico si hay algun estado en respuesta de una cancelación
        if ($checando_cfdi['estatusCancelacion'] != "") {
            return $this->errorResponse('CFDI ' . $request->id . ' en ' . $checando_cfdi['estatusCancelacion'] . '.', 409);
        }

        $cfdi = Cfdis::where('id', $request->id)->first();
        if (empty($cfdi)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos del cfdi.', 409);
        }
        if ($cfdi['status'] != 1) {
            /**datos no encontrados */
            return $this->errorResponse('Este cfdi ya ha sido cancelado previamente.', 409);
        }

        $xml = $this->leer_xml($request->id);
        if (empty($xml)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos del xml.', 409);
        }

        $fecha_cancelacion = date("Y-m-d H:i:s");
        /**datos para la consulta */
        $parametros            = new Parametros();
        $parametros->rfcEmisor = trim($cfdi->rfc_emisor);
        $parametros->fecha     = str_replace(" ", "T", $fecha_cancelacion);
        //folios de cancelacion
        $folio1                   = new wsFolio();
        $folio1->uuid             = $cfdi['uuid'];
        $folio1->motivo           = $request->motivo;
        $folio1->folioSustitucion = "";
        if ($folio1->motivo != "03") {
            $folio1->folioSustitucion = $request->uuid_a_sustituir_cancelar;
        }
        $folios               = new wsFolios();
        $folios->folio        = $folio1;
        $parametros->folios   = $folios;
        $parametros->SelloCFD = $xml['Complemento']['TimbreFiscalDigital']['SelloCFD'];
        if (ENV('APP_ENV') == 'local') {
            $certificado_path     = ENV('CER_PAC');
            $key_path             = ENV('KEY_PAC');
            $usuario              = ENV('USER_PAC_DEV');
            $password             = ENV('PASSWORD_PAC_DEV');
            $root_path_cer        = ENV('ROOT_CER_DEV');
            $root_path_key        = ENV('ROOT_KEY_DEV');
            $credentials_password = ENV('PASSWORD_LLAVES');
            $url_cancelar         = ENV('WEB_SERVICE_CANCELACION_DEVELOP');
        } else {
            $facturacion_datos_sistema = Facturacion::First();
            /**data from DB */
            if (! $facturacion_datos_sistema->cerFile || ! $facturacion_datos_sistema->keyFile || ! $facturacion_datos_sistema->password) {
                /**no procede */
                return $this->errorResponse("no se han capturado los certificados digitales de facturación", 409);
            }
            $certificado_path     = $facturacion_datos_sistema->cerFile; //sistema
            $key_path             = $facturacion_datos_sistema->keyFile; //sistema
            $usuario              = ENV('USER_PAC_PROD');
            $password             = ENV('PASSWORD_PAC_PROD');
            $root_path_cer        = ENV('ROOT_CER_PROD');
            $root_path_key        = ENV('ROOT_KEY_PROD');
            $credentials_password = $facturacion_datos_sistema->password;
            $url_cancelar         = ENV('WEB_SERVICE_CANCELACION_PRODUCTION');
        }

        $storage_disk_credentials = ENV('STORAGE_DISK_CREDENTIALS');
        //aqui trabajo
        $certFile               = Storage::disk($storage_disk_credentials)->path($root_path_cer . $certificado_path);
        $keyFile                = Storage::disk($storage_disk_credentials)->path($root_path_key . $key_path);
        $parametros->publicKey  = file_get_contents($certFile);
        $parametros->privateKey = file_get_contents($keyFile);
        $parametros->password   = $credentials_password;
        $autentica              = new Autenticar();
        $autentica->usuario     = $usuario;
        $autentica->password    = $password;
        $parametros->accesos    = $autentica;
        $client                 = new SoapClient($url_cancelar, ['trace' => 1]);
        $result                 = $client->Cancelacion40_1($parametros);
        //return $this->errorResponse($result, 409);
        //return $result;
        if (isset($result->return->acuse)) {
            /*CÓDIGO    MENSAJE
            201    UUID Cancelado.
            202    UUID previamente cancelado.
            203    UUID no encontrado.
            204    UUUID no aplicable o cancelación.
            205    UUID No existe.
             */
            $codigo_respuesta = $result->return->folios->folio->estatusUUID;
            if ($codigo_respuesta == 201) {
                //se guarda acuse en la bd
                DB::table('cfdis')->where('id', '=', $request->id)->update(
                    [
                        'status'            => 0,
                        'cancelo_id'        => (int) $request->user()->id,
                        'fecha_cancelacion' => $fecha_cancelacion,
                        'acuse_cancelacion' => $result->return->acuse,
                    ]
                );
                return $codigo_respuesta;
            } else {
                return $this->errorResponse($result->return->folios->folio->mensaje, 409);
            }
            //echo "<br><br>Estatus UUID: " . $result->return->folios->folio->estatusUUID . "<br>Mensaje: " . $result->return->folios->folio->mensaje;
            //echo '<br>XML response:<br><textarea>' . $result->return->acuse . '</textarea>';
        } else {
            /**ocurrio un error al cancelar el cfdi */
            return $this->errorResponse('Ocurrió un error al cancelar el CFDI.', 409);
        }
    }

    public function get_acuse_cancelacion_pdf(Request $request, $folio = '')
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        if (isset($request->request_parent)) {
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $folio_id          = $requestVentasList['folio_id'];
        } else {
            $folio_id = $folio;
            $email    = false;
            $email_to = 'hector@gmail.com';
        }

        /**aqui voy */
        if (ENV('APP_ENV') != 'local') {
            //actualizamos cfdis en caso de que este en produccion
            $checando_cfdi = $this->get_cfdi_status_sat($folio_id, $request);
            if (isset($checando_cfdi['estado'])) {
                if ($checando_cfdi['estado'] == 'No Encontrado') {
                    return $this->errorResponse('El CFDI ' . $checando_cfdi['uuid'] . ' no se encuentra en la base de datos del SAT.', 409);
                }
            }
            //dd($checando_cfdi);
        } else {
            //los datos se pasan vacios pues no hay datos rales que mostrar
            $checando_cfdi['codigoEstatus']      = 'S: Comprobante obtenido satisfactoriamente';
            $checando_cfdi['esCancelable']       = 'Cancelable sin Aceptación';
            $checando_cfdi['estado']             = 'Cancelado';
            $checando_cfdi['estatusCancelacion'] = 'Cancelado sin aceptación';
            $checando_cfdi['uuid']               = '';
        }

        $cfdi = Cfdis::where('id', $folio_id)->first();

        if (empty($cfdi)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos del cfdi.', 409);
        }

        //obtengo la informacion de esa venta
        $datos = $this->leer_xml($folio_id, '');
        if (empty($datos)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos del xml.', 409);
        }
        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();

        /**determinando que tipo de formato se va utilizar */
        $documento = 'facturacion/cfdi/cfdi_acuse_cancelacion';

        $pdf      = PDF::loadView($documento, ['cfdi' => $cfdi, 'datos' => $datos, 'status_sat' => $checando_cfdi, 'empresa' => $empresa]);
        $name_pdf = "ACUSE DE CANCELACION FACTURA FOLIO " . strtoupper($datos['Comprobante']['Folio']) . '.pdf';

        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view('facturacion.cfdi.footer', ['empresa' => $empresa]),
        ]);

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 8.4);
        $pdf->setOption('margin-bottom', 12.4);
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
                'Facturas Aeternus',
                "ACUSE DE CANCELACION FACTURA FOLIO " . strtoupper($datos['Comprobante']['Folio']),
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }
    /** Misma configuración global que usa timbrar_cfdi (primer registro).
     * Consultar únicamente la fecha, sin cargar archivos CER/KEY ni contraseña.
     * DATE se entrega como día calendario YYYY-MM-DD; no convertir a UTC.
     
     * Mantenimiento: La vigencia real procede de facturacion.fecha_vencimiento; se muestra en el card y los
     * reportes de facturación. No proviene del catálogo SAT.
     
     * Detalle del método: Lee la fecha de vencimiento de la firma electrónica registrada en facturacion.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Dato de vigencia para el card y los encabezados de reportes fiscales.
     */
    public function getVigenciaFirmaDashboard()
    {
        $valor=Facturacion::query()->value('fecha_vencimiento');
        $fecha=$valor ? substr((string)$valor,0,10) : null;
        $partes=[];
        if (!$fecha || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D',$fecha,$partes)
            || !checkdate((int)$partes[2],(int)$partes[3],(int)$partes[1])) {
            $fecha=null;
        }
        return ['fechaCaducidadFiel'=>$fecha];
    }

    /** Resumen ligero de timbres: solo agrupa movimientos, nunca carga CFDI individuales. 
     * Detalle del método: Suma los timbres disponibles de las recargas activadas hasta el corte, si existe
     * la tabla de movimientos.
     * Entradas: $corte (fecha de corte de la consulta).
     * Salida / efectos: Resumen de timbres disponibles; no consulta al proveedor externo.
     */
    public function getTimbresDashboard($corte = null)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('movimientos_timbres')) {
            return ['timbresDisponibles' => null];
        }
        $lotes = $this->resumenRecargasTimbres($corte ?: \Carbon\Carbon::now('America/Mazatlan'));
        $activos = array_filter($lotes, function ($lote) { return $lote['activada']; });
        return ['timbresDisponibles' => count($activos) ? array_sum(array_column($activos, 'disponibles')) : null];
    }

    /** Una fila por paquete; consumos y ajustes se agrupan antes de unir. 
     * Mantenimiento: Saldo de cada lote = cantidad inicial + movimientos hijos al corte. Este cálculo no
     * consulta el saldo del proveedor externo. Los consumos deben existir en movimientos_timbres.
     
     * Detalle del método: Suma consumos/ajustes por lote y los combina con la cantidad inicial y fecha de
     * activación.
     * Entradas: $corte (fecha de corte de la consulta).
     * Salida / efectos: Consulta/filas de recargas con sus totales y disponibilidad al corte.
     */
    private function resumenRecargasTimbres($corte)
    {
        $fecha = $corte->format('Y-m-d H:i:s');
        $movimientos = DB::table('movimientos_timbres')->whereNotNull('recarga_id')
            ->where('fecha_movimiento', '<=', $fecha)->groupBy('recarga_id')
            ->select('recarga_id')->selectRaw("SUM(cantidad) AS movimiento_neto,
                SUM(CASE WHEN tipo = 'consumo' THEN 1 ELSE 0 END) AS consumidos,
                SUM(CASE WHEN tipo = 'ajuste' THEN cantidad ELSE 0 END) AS ajustes,
                MIN(CASE WHEN tipo = 'consumo' THEN fecha_movimiento END) AS primer_consumo,
                MAX(CASE WHEN tipo = 'consumo' THEN fecha_movimiento END) AS ultimo_consumo");
        return DB::table('movimientos_timbres as r')->leftJoinSub($movimientos, 'm', function ($join) {
            $join->on('m.recarga_id', '=', 'r.id');
        })->whereIn('r.tipo', ['recarga', 'saldo_inicial'])->whereNull('r.recarga_id')
            ->orderBy('r.fecha_activacion', 'desc')->orderBy('r.id', 'desc')
            ->get(['r.id', 'r.tipo', 'r.referencia', 'r.cantidad', 'r.fecha_compra', 'r.fecha_activacion',
                'm.movimiento_neto', 'm.consumidos', 'm.ajustes', 'm.primer_consumo', 'm.ultimo_consumo'])
            ->map(function ($r) use ($fecha) {
                $activada = $r->fecha_activacion && $r->fecha_activacion <= $fecha;
                $restante = (int)$r->cantidad + (int)$r->movimiento_neto;
                return ['id' => (int)$r->id, 'referencia' => $r->referencia, 'recargados' => (int)$r->cantidad,
                    'fecha_compra' => $r->fecha_compra, 'fecha_activacion' => $r->fecha_activacion,
                    'consumidos' => (int)$r->consumidos, 'ajustes' => (int)$r->ajustes,
                    'disponibles' => $activada ? $restante : 0, 'activada' => (bool)$activada,
                    'primer_consumo' => $r->primer_consumo, 'ultimo_consumo' => $r->ultimo_consumo,
                    'estado' => !$activada ? 'Pendiente de activación' : ($restante < 0 ? 'Revisar saldo' : ($restante === 0 ? 'Agotada' : 'Disponible'))];
            })->all();
    }

    /** Relación persistida: no reasignar por fecha ni excluir consumos de CFDI cancelados. 
     * Detalle del método: Selecciona movimientos de consumo y datos de los CFDI del lote indicado hasta el
     * corte.
     * Entradas: $recargaId (ID del lote de timbres a consultar); $corte (fecha de corte de la consulta).
     * Salida / efectos: Consulta del historial para la vista y el Excel detallado.
     */
    private function consultaHistorialTimbres($recargaId, $corte)
    {
        $q = DB::table('movimientos_timbres as m')
            ->join('movimientos_timbres as r', 'r.id', '=', 'm.recarga_id')
            ->leftJoin('cfdis as c', 'c.id', '=', 'm.cfdis_id')
            ->leftJoin('clientes as cl', 'cl.id', '=', 'c.clientes_id')
            ->leftJoin('usuarios as u', 'u.id', '=', 'c.timbro_id')
            ->leftJoin('sat_tipo_comprobante as tc', 'tc.id', '=', 'c.sat_tipo_comprobante_id')
            ->where('m.tipo', 'consumo')->whereIn('r.tipo', ['recarga', 'saldo_inicial'])
            ->whereNull('r.recarga_id')->where('m.fecha_movimiento', '<=', $corte)
            ->select(['m.recarga_id', 'm.fecha_movimiento', 'm.asignacion_estimada', 'c.id as cfdi_id',
                'c.uuid', 'c.fecha_timbrado', 'c.nombre_receptor', 'c.fecha_cancelacion', 'cl.nombre as cliente',
                'u.nombre as usuario', 'tc.clave as tipo_clave', 'tc.tipo as tipo_cfdi']);
        if ($recargaId !== null) $q->where('m.recarga_id', $recargaId);
        return $q->orderBy('m.fecha_movimiento', 'desc')->orderBy('m.id', 'desc');
    }

    /**
     * Detalle del método: Valida sesión y rol de Admin/Gerencia antes de permitir un reporte de
     * facturación.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío).
     * Salida / efectos: Usuario autorizado o error de acceso.
     */
    private function autorizarReporteFacturacion(Request $request)
    {
        $usuario = $request->user('api');
        abort_unless($usuario && in_array((int)$usuario->roles_id, [1, 4], true), 403);
        return $usuario;
    }

    /** Resumen inicial; cada lote abre su propio historial dentro del mismo visor. 
     * Detalle del método: Prepara la vista previa de recargas de timbres: valida acceso y parámetros y
     * delega consulta/renderizado al flujo del reporte.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío).
     * Salida / efectos: Respuesta JSON con HTML/metadatos y token privado, o error de validación/acceso;
     * no descarga un Excel al abrir.
     */
    public function previewReporteTimbres(Request $request)
    {
        $usuario = $this->autorizarReporteFacturacion($request);
        $request->validate(['recarga_id' => 'nullable|integer|min:1']);
        abort_unless(\Illuminate\Support\Facades\Schema::hasTable('movimientos_timbres'), 409,
            'El historial de timbres todavía no está preparado. Aplique la migración y el seeder.');
        $id = $request->filled('recarga_id') ? (int)$request->input('recarga_id') : null;
        $corte = \Carbon\Carbon::now('America/Mazatlan');
        $datos = DB::transaction(function () use ($id, $corte) {
            $lotes = $this->resumenRecargasTimbres($corte);
            if ($id !== null) {
                $lotes = array_values(array_filter($lotes, function ($r) use ($id) { return $r['id'] === $id; }));
                abort_unless(count($lotes), 404, 'La recarga no existe.');
            }
            $total = $id === null ? count($lotes) : $this->consultaHistorialTimbres($id, $corte->format('Y-m-d H:i:s'))->count();
            $filas = $id !== null && $total <= 3500
                ? $this->consultaHistorialTimbres($id, $corte->format('Y-m-d H:i:s'))->get()->map(function ($r) { return (array)$r; })->all() : [];
            return ['recargas' => $lotes, 'consumos' => $filas, 'totalRegistros' => $total];
        });
        $datos['recarga_id'] = $id;
        $datos['titulo'] = $id === null ? 'Reporte de Recargas de Timbres' : 'Historial de Timbres · Recarga ' . $id;
        $datos['corte'] = $corte->format('Y-m-d H:i:s');
        $datos['fecha'] = \App\Exports\TimbresRecargasExport::fecha($datos['corte']);
        // Conservar la vigencia junto con la instantánea del reporte y sus adjuntos.
        $datos['fechaCaducidadFiel'] = $this->getVigenciaFirmaDashboard()['fechaCaducidadFiel'];
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $visualizable = $datos['totalRegistros'] <= 3500;
        $token = bin2hex(random_bytes(32));
        $vence = $corte->copy()->addMinutes(15);
        $nombre = $datos['titulo'] . ' ' . $corte->format('Y-m-d H-i-s') . '.xlsx';
        \Illuminate\Support\Facades\Cache::put('timbres_reporte_' . $token,
            ['usuario_id' => (int)$usuario->id, 'datos' => $datos, 'nombre' => $nombre, 'masivo' => !$visualizable], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => 3500, 'totalRegistros' => $datos['totalRegistros'],
            'mensaje' => 'El historial supera el tamaño de vista previa. Puede descargarlo completo; se consultarán los datos disponibles al descargar con el mismo corte.',
            'excelCompletoDisponible' => $id === null,
            'reportesRelacionados' => $id === null ? array_map(function ($r) {
                return ['id' => 'recarga-' . $r['id']];
            }, $datos['recargas']) : [],
            'hojas' => $visualizable ? [['nombre' => $id === null ? 'Recargas' : 'Historial', 'html' => $this->htmlReporteTimbres($datos)]] : [],
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Token privado por consulta; su vencimiento devuelve 410, no cierra la sesión. 
     * Detalle del método: Recupera el contexto privado de timbres y carga el historial cuando se pide un
     * lote o el archivo completo.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión); $completo (solicita desgloses completos donde
     * el reporte los permite).
     * Salida / efectos: Datos del reporte validados por propietario y caducidad.
     */
    private function datosReporteTimbres(Request $request, $token, $completo = false)
    {
        $usuario = $this->autorizarReporteFacturacion($request);
        abort_unless(preg_match('/^[a-f0-9]{64}$/D', $token), 404);
        $reporte = \Illuminate\Support\Facades\Cache::get('timbres_reporte_' . $token);
        abort_unless($reporte, 410, 'La consulta venció. Actualice el reporte para continuar.');
        abort_unless((int)$reporte['usuario_id'] === (int)$usuario->id, 403);
        if ($completo || $reporte['masivo']) {
            // Descargar detalle grande vuelve a consultar, manteniendo resumen y filas coherentes.
            $reporte['datos'] = DB::transaction(function () use ($reporte) {
                $datos = $reporte['datos'];
                $lotes = $this->resumenRecargasTimbres(\Carbon\Carbon::parse($datos['corte'], 'America/Mazatlan'));
                if ($datos['recarga_id'] !== null) {
                    $lotes = array_values(array_filter($lotes, function ($r) use ($datos) { return $r['id'] === $datos['recarga_id']; }));
                }
                $datos['recargas'] = $lotes;
                $datos['consumos'] = $this->consultaHistorialTimbres($datos['recarga_id'], $datos['corte'])
                    ->get()->map(function ($r) { return (array)$r; })->all();
                return $datos;
            });
        }
        $reporte['datos']['completo'] = $completo;
        return $reporte;
    }

    /**
     * Detalle del método: Renderiza el Blade de recargas e historial con el contexto del reporte y el modo
     * PDF/pantalla.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado); $pdf (selecciona
     * presentación PDF frente a pantalla).
     * Salida / efectos: HTML, sin crear un libro de Excel.
     */
    private function htmlReporteTimbres(array $datos, $pdf = false)
    {
        $ruta = public_path(env('LOGOJPG'));
        $logo = null;
        if (is_file($ruta)) {
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($ruta);
            if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)) {
                $logo = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($ruta));
            }
        }
        return view('reportes.temporales_visor_excel.facturacion.timbres', [
            'datos' => $datos, 'secciones' => \App\Exports\TimbresRecargasExport::secciones($datos),
            'logo' => $logo, 'modoPdf' => $pdf,
        ])->render();
    }

    /**
     * Detalle del método: Recupera el reporte privado del token y genera la descarga Excel correspondiente
     * al contexto seleccionado.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta de descarga Excel; verifica acceso/vigencia y no envía correo. El
     * resumen diario rechaza Excel.
     */
    public function descargarReporteTimbresExcel(Request $request, $token)
    {
        $request->validate(['completo' => 'sometimes|boolean']);
        $completo = $request->boolean('completo');
        $reporte = $this->datosReporteTimbres($request, $token, $completo);
        $respuesta = \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TimbresRecargasExport($reporte['datos']),
            ($completo ? 'Detalle ' : '') . $reporte['nombre']);
        $respuesta->headers->set('Cache-Control', 'no-store, private');
        return $respuesta;
    }

    /**
     * Detalle del método: Genera PDF desde la vista de timbres y configura página y pie de impresión.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado).
     * Salida / efectos: Documento PDF para descarga o envío.
     */
    private function construirPdfTimbres(array $datos)
    {
        $footer = view('reportes.temporales_visor_excel.compartidos.footer_pdf', [
            'fechaImpresion' => \App\Exports\TimbresRecargasExport::fecha(now('America/Mazatlan')),
            'dominio' => parse_url(config('app.url'), PHP_URL_HOST) ?: config('app.url'),
        ])->render();
        return PDF::loadHTML($this->htmlReporteTimbres($datos, true))->setOptions([
            'orientation' => 'landscape', 'page-size' => 'A4', 'encoding' => 'UTF-8',
            'margin-top' => 10, 'margin-bottom' => 12, 'margin-left' => 10, 'margin-right' => 10,
            'print-media-type' => true, 'disable-local-file-access' => true,
            'footer-html' => $footer, 'footer-spacing' => 3,
        ]);
    }

    /**
     * Detalle del método: Recupera el reporte privado del token y genera la descarga PDF correspondiente
     * al contexto seleccionado.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta de descarga PDF; verifica acceso/vigencia y no envía correo. El resumen
     * diario rechaza Excel.
     */
    public function descargarReporteTimbresPdf(Request $request, $token)
    {
        $reporte = $this->datosReporteTimbres($request, $token);
        $respuesta = $this->construirPdfTimbres($reporte['datos'])->download(preg_replace('/\.xlsx$/i', '.pdf', $reporte['nombre']));
        $respuesta->headers->set('Cache-Control', 'no-store, private');
        return $respuesta;
    }

    /** Envío reutiliza plantilla, destinatarios y desvío local del mail sender existente. 
     * Detalle del método: Valida destinatarios, asunto, mensaje y formato, genera el adjunto del reporte y
     * delega el envío a EmailController.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta JSON del envío; el token debe pertenecer al usuario y el formato debe
     * estar permitido.
     */
    public function enviarReporteTimbresCorreo(Request $request, $token)
    {
        // Autorizar antes de generar archivos o validar datos de destinatarios.
        $reporte = $this->datosReporteTimbres($request, $token);
        $request->validate([
            'destinatario' => 'required_without:destinatarios|nullable|string|max:75',
            'email_address' => 'required_without:destinatarios|nullable|email|max:75',
            'destinatarios' => 'sometimes|required|array|min:1|max:20',
            'destinatarios.*.nombre' => 'nullable|string|max:75',
            'destinatarios.*.email' => 'required|email|max:75',
            'formato' => 'sometimes|required|in:excel,pdf',
            'completo' => 'sometimes|boolean',
            'mensaje' => 'nullable|string|max:30000',
            'asunto' => ['nullable', 'string', 'max:150', 'not_regex:/[\r\n]/'],
        ]);
        $personas = $request->input('destinatarios', [['nombre' => $request->input('destinatario'), 'email' => $request->input('email_address')]]);
        $vistos = [];
        foreach ($personas as &$persona) {
            $persona['email'] = mb_strtolower(trim($persona['email']), 'UTF-8');
            $persona['nombre'] = trim($persona['nombre'] ?? '');
            if (isset($vistos[$persona['email']])) throw \Illuminate\Validation\ValidationException::withMessages(['destinatarios' => 'Hay correos duplicados.']);
            $vistos[$persona['email']] = true;
        }
        unset($persona);
        if (mb_strlen(trim(html_entity_decode(strip_tags($request->input('mensaje', '') ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 'UTF-8') > 5000) {
            throw \Illuminate\Validation\ValidationException::withMessages(['mensaje' => 'El mensaje no debe superar 5000 caracteres.']);
        }
        $completo = $request->boolean('completo');
        if ($completo) {
            abort_unless($reporte['datos']['recarga_id'] === null, 422, 'El detalle completo se adjunta desde el resumen de recargas.');
            abort_unless($request->input('formato', 'excel') === 'excel', 422, 'El detalle completo se envía en formato Excel.');
            $reporte = $this->datosReporteTimbres($request, $token, true);
            $reporte['nombre'] = 'Detalle ' . $reporte['nombre'];
        }
        $pdf = $request->input('formato', 'excel') === 'pdf';
        $contenido = $pdf ? $this->construirPdfTimbres($reporte['datos'])->output()
            : \Maatwebsite\Excel\Facades\Excel::raw(new \App\Exports\TimbresRecargasExport($reporte['datos']), \Maatwebsite\Excel\Excel::XLSX);
        return app(EmailController::class)->reporte_email([
            'email_address' => $personas[0]['email'], 'destinatario' => $personas[0]['nombre'], 'destinatarios' => $personas,
            'asunto' => $request->input('asunto') ?: $reporte['datos']['titulo'], 'mensaje' => $request->input('mensaje', ''),
        ], $contenido, $pdf ? preg_replace('/\.xlsx$/i', '.pdf', $reporte['nombre']) : $reporte['nombre'],
            $pdf ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /** Base compartida de ingresos vigentes para indicador y reportes.
     * Predeterminado: PPD con adeudo de todo el historial. modoMes: PUE/PPD
     * emitidos en el mes del corte, incluidos saldados y parcialmente cubiertos.
     * Aplicaciones según get_cfdis_timbrados: relaciones 2/3 con CFDI status=1.
     * Las aplicaciones no se restringen al mes de emisión; no es un cierre histórico.
     
     * Mantenimiento: Saldo fiscal separado del capital de operaciones: usa cfdis_tipo_relacion con CFDI
     * aplicador vigente y relaciones internas 2 (pagos) y 3 (egresos). PUE se considera cubierto; PPD conserva
     * el pendiente calculado. modoMes incluye ingresos PUE/PPD emitidos en el mes; sin modoMes devuelve PPD
     * histórico con saldo positivo.
     
     * Detalle del método: Calcula saldo fiscal desde relaciones de pagos/egresos vigentes; distingue PPD
     * histórico y PUE/PPD emitido en el mes.
     * Entradas: $corte (fecha de corte de la consulta); $modoMes (dato del contexto descrito en la firma).
     * Salida / efectos: Query Builder de facturas con pagos_aplicados, egresos_aplicados y
     * saldo_pendiente; no usa programados de operaciones.
     */
    private function consultaCfdisAdeudosBase($corte, $modoMes = false)
    {
        $aplicado = DB::table('cfdis_tipo_relacion as r')->join('cfdis as p', 'p.id', '=', 'r.cfdis_id')
            ->where('p.status', 1)->whereIn('r.tipo_relacion_id', [2, 3])->groupBy('r.cfdis_id_relacionado')
            ->select('r.cfdis_id_relacionado')
            ->selectRaw('SUM(CASE WHEN r.tipo_relacion_id = 2 THEN COALESCE(r.monto_relacion,0) ELSE 0 END) AS pagos,
                SUM(CASE WHEN r.tipo_relacion_id = 3 THEN COALESCE(r.monto_relacion,0) ELSE 0 END) AS egresos');
        $facturas = DB::table('cfdis as c')->leftJoinSub($aplicado, 'a', function ($j) {
                $j->on('a.cfdis_id_relacionado', '=', 'c.id');
            })->leftJoin('sat_monedas as mo', 'mo.id', '=', 'c.sat_monedas_id')
            ->where('c.status', 1)->where('c.sat_tipo_comprobante_id', 1)->whereIn('c.sat_metodos_pago_id', $modoMes ? [1,2] : [2])
            ->where('c.fecha', '<=', $corte)
            ->select(['c.id', 'c.uuid', 'c.clientes_id', 'c.fecha', 'c.total', 'c.nombre_receptor', 'c.rfc_receptor', 'c.timbro_id', 'c.sat_metodos_pago_id'])
            ->selectRaw("COALESCE(mo.codigo_moneda, 'Sin moneda') AS moneda,
                CASE WHEN c.sat_metodos_pago_id = 1 THEN COALESCE(c.total,0) ELSE ROUND(COALESCE(a.pagos,0),2) END AS pagos_aplicados, COALESCE(a.egresos,0) AS egresos_aplicados,
                CASE WHEN c.sat_metodos_pago_id = 1 THEN 0 ELSE ROUND(COALESCE(c.total,0) - ROUND(COALESCE(a.pagos,0),2) - COALESCE(a.egresos,0),2) END AS saldo_pendiente");
        if ($modoMes) {
            $inicio=\Carbon\Carbon::parse($corte,'America/Mazatlan')->startOfMonth()->format('Y-m-d H:i:s');
            $facturas->where('c.fecha','>=',$inicio);
        }
        $consulta=DB::query()->fromSub($facturas,'f');
        return $modoMes ? $consulta : $consulta->where('f.saldo_pendiente','>',0);
    }

    /**
     * Detalle del método: Agrupa por moneda los importes fiscales, cantidades PUE/PPD y receptores público
     * en general.
     * Entradas: $corte (fecha de corte de la consulta); $modoMes (dato del contexto descrito en la firma).
     * Salida / efectos: Arreglo de totales por moneda; evita sumar monedas diferentes.
     */
    private function resumenCfdisAdeudos($corte, $modoMes = false)
    {
        return $this->consultaCfdisAdeudosBase($corte, $modoMes)->select('f.moneda')->groupBy('f.moneda')->orderBy('f.moneda')
            ->selectRaw("COUNT(*) AS cantidad,
                SUM(CASE WHEN f.sat_metodos_pago_id=1 THEN 1 ELSE 0 END) AS cantidad_pue,
                SUM(CASE WHEN f.sat_metodos_pago_id=2 THEN 1 ELSE 0 END) AS cantidad_ppd, SUM(f.total) AS facturado, SUM(f.pagos_aplicados) AS pagos,
                SUM(f.egresos_aplicados) AS egresos, SUM(CASE WHEN f.saldo_pendiente > 0 THEN f.saldo_pendiente ELSE 0 END) AS pendiente,
                SUM(CASE WHEN f.pagos_aplicados > f.total THEN f.total WHEN f.pagos_aplicados < 0 THEN 0 ELSE f.pagos_aplicados END) AS saldado,
                SUM(CASE WHEN UPPER(TRIM(COALESCE(f.rfc_receptor,''))) = 'XAXX010101000' THEN 1 ELSE 0 END) AS publico_general")
            ->get()->map(function ($r) { return ['moneda' => $r->moneda, 'cantidad' => (int)$r->cantidad,
                'cantidad_pue' => (int)$r->cantidad_pue, 'cantidad_ppd' => (int)$r->cantidad_ppd,
                'publico_general' => (int)$r->publico_general, 'otros_receptores' => (int)$r->cantidad - (int)$r->publico_general,
                'facturado' => round((float)$r->facturado,2), 'pagos' => round((float)$r->pagos,2),
                'saldado' => round((float)$r->saldado,2), 'egresos' => round((float)$r->egresos,2), 'pendiente' => round((float)$r->pendiente,2)]; })->all();
    }

    /** El card está expresado en MXN; el reporte conserva monedas por separado. 
     * Detalle del método: Extrae el total pendiente y cantidad de facturas históricas con adeudo en MXN.
     * Entradas: $corte (fecha de corte de la consulta).
     * Salida / efectos: Indicadores del card con metadatos de corte y moneda.
     */
    public function getCfdisAdeudosDashboard($corte = null)
    {
        $corte = $corte ?: \Carbon\Carbon::now('America/Mazatlan');
        $resumen = $this->resumenCfdisAdeudos($corte->format('Y-m-d H:i:s'));
        $mxn = array_values(array_filter($resumen, function ($r) { return $r['moneda'] === 'MXN'; }));
        return ['saldoPendiente' => $mxn[0]['pendiente'] ?? 0, 'facturasPendientes' => $mxn[0]['cantidad'] ?? 0,
            'adeudosAcumulados' => true, 'adeudosMoneda' => 'MXN', 'adeudosCorte' => $corte->toIso8601String()];
    }

    /**
     * Detalle del método: Añade cliente, usuario y existencia de vínculos de operación a la consulta
     * fiscal de facturas.
     * Entradas: $corte (fecha de corte de la consulta); $modoMes (dato del contexto descrito en la firma).
     * Salida / efectos: Consulta ordenada por fecha descendente para las filas principales del reporte.
     */
    private function detalleCfdisAdeudos($corte, $modoMes = false)
    {
        return $this->consultaCfdisAdeudosBase($corte, $modoMes)
            ->leftJoin('clientes as cl', 'cl.id', '=', 'f.clientes_id')->leftJoin('usuarios as u', 'u.id', '=', 'f.timbro_id')
            ->select('f.*', 'cl.nombre as cliente', 'u.nombre as usuario')
            ->selectRaw('CASE WHEN EXISTS (SELECT 1 FROM cfdis_operaciones co WHERE co.cfdis_id = f.id) THEN 1 ELSE 0 END AS tiene_operaciones')
            ->orderBy('f.fecha', 'desc')->orderBy('f.id', 'desc');
    }

    /** Una fila por aplicación vigente a una factura que aún conserva adeudo.
     * Mostrar monto_relacion evita repetir el total del pago si cubre varios CFDI.
     * Misma selección que el saldo: relación interna2, comprobante aplicador status1.
     
     * Mantenimiento: Detalle debajo de la factura: mostrar monto_relacion aplicado a esa factura, no repetir
     * el total de un comprobante que cubrió varias. No recalcular aquí el total de la factura.
     
     * Detalle del método: Consulta cada aplicación fiscal recibida de un comprobante vigente a las
     * facturas del reporte.
     * Entradas: $corte (fecha de corte de la consulta); $modoMes (dato del contexto descrito en la firma).
     * Salida / efectos: Consulta con importe_aplicado por factura; no repetir el total del comprobante que
     * cubrió varias.
     */
    private function pagosRecibidosCfdisAdeudos($corte, $modoMes = false)
    {
        return $this->consultaCfdisAdeudosBase($corte, $modoMes)
            ->join('cfdis_tipo_relacion as r', 'r.cfdis_id_relacionado', '=', 'f.id')
            ->join('cfdis as p', 'p.id', '=', 'r.cfdis_id')
            ->leftJoin('clientes as cl', 'cl.id', '=', 'f.clientes_id')
            ->leftJoin('usuarios as u', 'u.id', '=', 'p.timbro_id')
            ->where('r.tipo_relacion_id', 2)->where('p.status', 1)
            ->select(['f.id as factura_id', 'f.uuid as factura_uuid', 'f.moneda', 'f.nombre_receptor',
                'cl.nombre as cliente', 'p.id as pago_cfdi_id', 'p.uuid as pago_uuid', 'p.fecha_pago',
                'p.fecha as fecha_comprobante', 'p.fecha_timbrado', 'r.monto_relacion as importe_aplicado',
                'u.nombre as usuario'])
            ->orderByRaw('COALESCE(p.fecha_pago,p.fecha) DESC')->orderBy('p.id', 'desc')->orderBy('f.id', 'desc');
    }

    /** Referencias de negocio de las operaciones ligadas, sin filtrar su estado histórico. 
     * Mantenimiento: Aporta referencias y cliente de las operaciones vinculadas para contexto del CFDI. Las
     * referencias visibles son las del negocio, no operaciones.id; el vínculo histórico puede conservar
     * operaciones canceladas.
     
     * Detalle del método: Recupera las operaciones vinculadas y su cliente para mostrarlas como contexto
     * debajo de la factura.
     * Entradas: $corte (fecha de corte de la consulta); $modoMes (dato del contexto descrito en la firma).
     * Salida / efectos: Consulta con referencias de negocio; conserva el vínculo histórico aunque la
     * operación se haya cancelado.
     */
    private function operacionesCfdisAdeudos($corte, $modoMes = false)
    {
        return $this->consultaCfdisAdeudosBase($corte, $modoMes)
            ->join('cfdis_operaciones as co', 'co.cfdis_id', '=', 'f.id')
            ->join('operaciones as o', 'o.id', '=', 'co.operaciones_id')
            ->leftJoin('clientes as oc', 'oc.id', '=', 'o.clientes_id')
            ->select(['f.id as factura_id', 'o.id as operacion_id', 'o.empresa_operaciones_id',
                'o.ventas_terrenos_id', 'o.cuotas_cementerio_id', 'o.servicios_funerarios_id',
                'o.ventas_planes_id', 'o.ventas_generales_id', 'o.fecha_operacion', 'o.status', 'oc.nombre as cliente_operacion'])
            ->distinct()->orderBy('f.id', 'desc')->orderBy('o.fecha_operacion', 'desc')->orderBy('o.id', 'desc');
    }

    /** Consulta propia del módulo de facturación; visor comparte el diseño corporativo. 
     * Detalle del método: Prepara la vista previa de CFDI con adeudo: valida acceso y parámetros y delega
     * consulta/renderizado al flujo del reporte.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío).
     * Salida / efectos: Respuesta JSON con HTML/metadatos y token privado, o error de validación/acceso;
     * no descarga un Excel al abrir.
     */
    public function previewReporteCfdisAdeudos(Request $request)
    {
        $usuario = $this->autorizarReporteFacturacion($request);
        $corte = \Carbon\Carbon::now('America/Mazatlan');
        $datos = DB::transaction(function () use ($corte) {
            $fecha = $corte->format('Y-m-d H:i:s');
            $resumen = $this->resumenCfdisAdeudos($fecha);
            $cantidad = array_sum(array_column($resumen, 'cantidad'));
            $cantidadPagos = $this->pagosRecibidosCfdisAdeudos($fecha)->count();
            $cantidadOperaciones = DB::query()->fromSub($this->operacionesCfdisAdeudos($fecha), 'relaciones')->count();
            $visible = $cantidad + $cantidadPagos + $cantidadOperaciones <= 3500;
            return ['resumen' => $resumen, 'cantidad' => $cantidad, 'cantidadPagos' => $cantidadPagos,
                'totalRegistros' => $cantidad + $cantidadPagos + $cantidadOperaciones,
                'operaciones' => $visible ? $this->operacionesCfdisAdeudos($fecha)->get()->map(function ($r) { return (array)$r; })->all() : [],
                'cfdis' => $visible ? $this->detalleCfdisAdeudos($fecha)->get()->map(function ($r) { return (array)$r; })->all() : [],
                'pagos' => $visible ? $this->pagosRecibidosCfdisAdeudos($fecha)->get()->map(function ($r) { return (array)$r; })->all() : []];
        });
        $datos['titulo'] = 'CFDI con Adeudo · Acumulado Histórico';
        $datos['corte'] = $corte->format('Y-m-d H:i:s');
        $datos['fecha'] = \App\Exports\TimbresRecargasExport::fecha($datos['corte']);
        // Conservar la vigencia junto con la instantánea del reporte y sus adjuntos.
        $datos['fechaCaducidadFiel'] = $this->getVigenciaFirmaDashboard()['fechaCaducidadFiel'];
        $datos['periodo'] = ucfirst($corte->locale('es')->translatedFormat('F Y'));
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial','razon_social','rfc','telefono','email','calle','num_ext','colonia','ciudad','estado'
        ])) : [];
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = 'CFDI con Adeudo Acumulado '.$corte->format('Y-m-d H-i-s').'.xlsx';
        $visible = $datos['totalRegistros'] <= 3500;
        \Illuminate\Support\Facades\Cache::put('cfdis_adeudos_reporte_'.$token,
            ['usuario_id'=>(int)$usuario->id,'datos'=>$datos,'nombre'=>$nombre,'masivo'=>!$visible], $vence);
        return response()->json(['titulo'=>$datos['titulo'],'nombreArchivo'=>$nombre,'tokenDescarga'=>$token,
            'venceEn'=>$vence->toIso8601String(),'vistaPreviaDisponible'=>$visible,'limiteVisualizacion'=>3500,
            'totalRegistros'=>$datos['totalRegistros'],
            'mensaje'=>'El reporte supera el tamaño de vista previa. Puede descargarlo completo; se consultarán los datos disponibles al descargar con el mismo corte.',
            'hojas'=>$visible ? [['nombre'=>'CFDI con adeudo','html'=>$this->htmlCfdisAdeudos($datos)]] : []
        ])->header('Cache-Control','no-store, private');
    }

    /**
     * Detalle del método: Recupera el token fiscal de adeudos y su detalle guardado o diferido,
     * comprobando usuario y vigencia.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Contexto validado del reporte de adeudos, listo para generar archivo.
     */
    private function datosCfdisAdeudos(Request $request, $token)
    {
        $usuario = $this->autorizarReporteFacturacion($request);
        abort_unless(preg_match('/^[a-f0-9]{64}$/D',$token),404);
        $r = \Illuminate\Support\Facades\Cache::get('cfdis_adeudos_reporte_'.$token);
        abort_unless($r,410,'La consulta venció. Actualice el reporte para continuar.');
        abort_unless((int)$r['usuario_id']===(int)$usuario->id,403);
        if ($r['masivo']) {
            $r['datos'] = DB::transaction(function () use ($r) {
                $d=$r['datos']; $d['resumen']=$this->resumenCfdisAdeudos($d['corte']);
                $d['cantidad']=array_sum(array_column($d['resumen'],'cantidad'));
                $d['cfdis']=$this->detalleCfdisAdeudos($d['corte'])->get()->map(function($r){return(array)$r;})->all();
                $d['pagos']=$this->pagosRecibidosCfdisAdeudos($d['corte'])->get()->map(function($r){return(array)$r;})->all();
                $d['operaciones']=$this->operacionesCfdisAdeudos($d['corte'])->get()->map(function($r){return(array)$r;})->all();
                $d['cantidadPagos']=count($d['pagos']); $d['totalRegistros']=$d['cantidad']+$d['cantidadPagos']+count($d['operaciones']);
                return $d;
            });
        }
        return $r;
    }

    /**
     * Detalle del método: Renderiza facturas con adeudo y, debajo, sus operaciones y pagos relacionados.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado); $pdf (selecciona
     * presentación PDF frente a pantalla).
     * Salida / efectos: HTML para vista previa o PDF; importes ya calculados por las consultas.
     */
    private function htmlCfdisAdeudos(array $datos, $pdf = false)
    {
        $ruta=public_path(env('LOGOJPG'));$logo=null;
        if(is_file($ruta)){$mime=(new \finfo(FILEINFO_MIME_TYPE))->file($ruta);
            if(in_array($mime,['image/jpeg','image/png','image/gif'],true))$logo='data:'.$mime.';base64,'.base64_encode(file_get_contents($ruta));}
        return view('reportes.temporales_visor_excel.facturacion.adeudos',[
            'datos'=>$datos,'secciones'=>\App\Exports\CfdisAdeudosExport::secciones($datos),'logo'=>$logo,'modoPdf'=>$pdf
        ])->render();
    }

    /**
     * Detalle del método: Genera el PDF del reporte de adeudos con el diseño y pie fiscal compartidos.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado).
     * Salida / efectos: Documento PDF descargable o adjuntable.
     */
    private function pdfCfdisAdeudos(array $datos)
    {
        $footer=view('reportes.temporales_visor_excel.compartidos.footer_pdf',[
            'fechaImpresion'=>\App\Exports\TimbresRecargasExport::fecha(now('America/Mazatlan')),
            'dominio'=>parse_url(config('app.url'),PHP_URL_HOST) ?: config('app.url')])->render();
        return PDF::loadHTML($this->htmlCfdisAdeudos($datos,true))->setOptions([
            'orientation'=>'landscape','page-size'=>'A4','encoding'=>'UTF-8','print-media-type'=>true,
            'margin-top'=>10,'margin-bottom'=>12,'margin-left'=>10,'margin-right'=>10,
            'disable-local-file-access'=>true,'footer-html'=>$footer,'footer-spacing'=>3]);
    }

    /**
     * Detalle del método: Recupera el reporte privado del token y genera la descarga Excel correspondiente
     * al contexto seleccionado.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta de descarga Excel; verifica acceso/vigencia y no envía correo. El
     * resumen diario rechaza Excel.
     */
    public function descargarCfdisAdeudosExcel(Request $request,$token)
    {
        $r=$this->datosCfdisAdeudos($request,$token);
        $respuesta=\Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CfdisAdeudosExport($r['datos']),$r['nombre']);
        $respuesta->headers->set('Cache-Control','no-store, private');return $respuesta;
    }
    /**
     * Detalle del método: Recupera el reporte privado del token y genera la descarga PDF correspondiente
     * al contexto seleccionado.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta de descarga PDF; verifica acceso/vigencia y no envía correo. El resumen
     * diario rechaza Excel.
     */
    public function descargarCfdisAdeudosPdf(Request $request,$token)
    {
        $r=$this->datosCfdisAdeudos($request,$token);
        $respuesta=$this->pdfCfdisAdeudos($r['datos'])->download(preg_replace('/\.xlsx$/i','.pdf',$r['nombre']));
        $respuesta->headers->set('Cache-Control','no-store, private');return $respuesta;
    }

    /**
     * Detalle del método: Valida destinatarios, asunto, mensaje y formato, genera el adjunto del reporte y
     * delega el envío a EmailController.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta JSON del envío; el token debe pertenecer al usuario y el formato debe
     * estar permitido.
     */
    public function enviarCfdisAdeudosCorreo(Request $request, $token)
    {
        // Autorizar antes de generar archivos o validar datos de destinatarios.
        $reporte = $this->datosCfdisAdeudos($request, $token);
        $request->validate([
            'destinatario' => 'required_without:destinatarios|nullable|string|max:75',
            'email_address' => 'required_without:destinatarios|nullable|email|max:75',
            'destinatarios' => 'sometimes|required|array|min:1|max:20',
            'destinatarios.*.nombre' => 'nullable|string|max:75',
            'destinatarios.*.email' => 'required|email|max:75',
            'formato' => 'sometimes|required|in:excel,pdf',
            'completo' => 'sometimes|boolean',
            'mensaje' => 'nullable|string|max:30000',
            'asunto' => ['nullable', 'string', 'max:150', 'not_regex:/[\r\n]/'],
        ]);
        $personas = $request->input('destinatarios', [['nombre' => $request->input('destinatario'), 'email' => $request->input('email_address')]]);
        $vistos = [];
        foreach ($personas as &$persona) {
            $persona['email'] = mb_strtolower(trim($persona['email']), 'UTF-8');
            $persona['nombre'] = trim($persona['nombre'] ?? '');
            if (isset($vistos[$persona['email']])) throw \Illuminate\Validation\ValidationException::withMessages(['destinatarios' => 'Hay correos duplicados.']);
            $vistos[$persona['email']] = true;
        }
        unset($persona);
        if (mb_strlen(trim(html_entity_decode(strip_tags($request->input('mensaje', '') ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 'UTF-8') > 5000) {
            throw \Illuminate\Validation\ValidationException::withMessages(['mensaje' => 'El mensaje no debe superar 5000 caracteres.']);
        }
        abort_if($request->boolean('completo'), 422, 'Este reporte ya incluye el detalle completo de los CFDI con adeudo.');
        $pdf = $request->input('formato', 'excel') === 'pdf';
        $contenido = $pdf ? $this->pdfCfdisAdeudos($reporte['datos'])->output()
            : \Maatwebsite\Excel\Facades\Excel::raw(new \App\Exports\CfdisAdeudosExport($reporte['datos']), \Maatwebsite\Excel\Excel::XLSX);
        return app(EmailController::class)->reporte_email([
            'email_address' => $personas[0]['email'], 'destinatario' => $personas[0]['nombre'], 'destinatarios' => $personas,
            'asunto' => $request->input('asunto') ?: $reporte['datos']['titulo'], 'mensaje' => $request->input('mensaje', ''),
        ], $contenido, $pdf ? preg_replace('/\.xlsx$/i', '.pdf', $reporte['nombre']) : $reporte['nombre'],
            $pdf ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }


    /** Facturado y cubierto de facturas de este mes; no es cobranza de ventas antiguas. 
     * Mantenimiento: Facturado y cubierto del mes de emisión. Cubierto incluye pagos parciales; no equivale a
     * sumar únicamente facturas liquidadas. El card muestra MXN y el reporte conserva agrupación por moneda.
     
     * Detalle del método: Resume en MXN lo emitido en el mes y lo cubierto de esas facturas, incluyendo
     * pagos parciales.
     * Entradas: $corte (fecha de corte de la consulta).
     * Salida / efectos: Importes facturado/cubierto y cantidad de facturas; no significa solo facturas
     * completamente liquidadas.
     */
    public function getFacturacionMesDashboard($corte = null)
    {
        $corte=$corte ?: \Carbon\Carbon::now('America/Mazatlan');
        $resumen=$this->resumenCfdisAdeudos($corte->format('Y-m-d H:i:s'),true);
        $mxn=array_values(array_filter($resumen,function($r){return $r['moneda']==='MXN';}));
        return ['ventasFacturadas'=>$mxn[0]['facturado'] ?? 0,'saldadoFacturado'=>$mxn[0]['saldado'] ?? 0,
            'cantidadFacturasMes'=>$mxn[0]['cantidad'] ?? 0];
    }

    /**
     * Detalle del método: Prepara la vista previa de facturación mensual: valida acceso y parámetros y
     * delega consulta/renderizado al flujo del reporte.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío).
     * Salida / efectos: Respuesta JSON con HTML/metadatos y token privado, o error de validación/acceso;
     * no descarga un Excel al abrir.
     */
    public function previewReporteCfdisMes(Request $request)
    {
        $usuario = $this->autorizarReporteFacturacion($request);
        $corte = \Carbon\Carbon::now('America/Mazatlan');
        $datos = DB::transaction(function () use ($corte) {
            $fecha = $corte->format('Y-m-d H:i:s');
            $resumen = $this->resumenCfdisAdeudos($fecha, true);
            $cantidad = array_sum(array_column($resumen, 'cantidad'));
            $cantidadPagos = $this->pagosRecibidosCfdisAdeudos($fecha, true)->count();
            $cantidadOperaciones = DB::query()->fromSub($this->operacionesCfdisAdeudos($fecha, true), 'relaciones')->count();
            $visible = $cantidad + $cantidadPagos + $cantidadOperaciones <= 3500;
            return ['resumen' => $resumen, 'cantidad' => $cantidad, 'cantidadPagos' => $cantidadPagos,
                'totalRegistros' => $cantidad + $cantidadPagos + $cantidadOperaciones,
                'operaciones' => $visible ? $this->operacionesCfdisAdeudos($fecha, true)->get()->map(function ($r) { return (array)$r; })->all() : [],
                'cfdis' => $visible ? $this->detalleCfdisAdeudos($fecha, true)->get()->map(function ($r) { return (array)$r; })->all() : [],
                'pagos' => $visible ? $this->pagosRecibidosCfdisAdeudos($fecha, true)->get()->map(function ($r) { return (array)$r; })->all() : []];
        });
        $datos['modo_mes'] = true;
        $datos['titulo'] = 'Reporte de Facturación del Mes';
        $datos['corte'] = $corte->format('Y-m-d H:i:s');
        $datos['fecha'] = \App\Exports\TimbresRecargasExport::fecha($datos['corte']);
        // Conservar la vigencia junto con la instantánea del reporte y sus adjuntos.
        $datos['fechaCaducidadFiel'] = $this->getVigenciaFirmaDashboard()['fechaCaducidadFiel'];
        $datos['periodo'] = ucfirst($corte->locale('es')->translatedFormat('F Y'));
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial','razon_social','rfc','telefono','email','calle','num_ext','colonia','ciudad','estado'
        ])) : [];
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = 'Facturacion del Mes '.$corte->format('Y-m-d H-i-s').'.xlsx';
        $visible = $datos['totalRegistros'] <= 3500;
        \Illuminate\Support\Facades\Cache::put('cfdis_mes_reporte_'.$token,
            ['usuario_id'=>(int)$usuario->id,'datos'=>$datos,'nombre'=>$nombre,'masivo'=>!$visible], $vence);
        return response()->json(['titulo'=>$datos['titulo'],'nombreArchivo'=>$nombre,'tokenDescarga'=>$token,
            'venceEn'=>$vence->toIso8601String(),'vistaPreviaDisponible'=>$visible,'limiteVisualizacion'=>3500,
            'totalRegistros'=>$datos['totalRegistros'],
            'mensaje'=>'El reporte supera el tamaño de vista previa. Puede descargarlo completo; se consultarán los datos disponibles al descargar con el mismo corte.',
            'hojas'=>$visible ? [['nombre'=>'Facturación del mes','html'=>$this->htmlCfdisMes($datos)]] : []
        ])->header('Cache-Control','no-store, private');
    }

    /**
     * Detalle del método: Recupera el contexto privado del reporte mensual de CFDI y reconstruye el
     * detalle si fue diferido.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Datos validados de facturas del mes para descarga o correo.
     */
    private function datosCfdisMes(Request $request, $token)
    {
        $usuario = $this->autorizarReporteFacturacion($request);
        abort_unless(preg_match('/^[a-f0-9]{64}$/D',$token),404);
        $r = \Illuminate\Support\Facades\Cache::get('cfdis_mes_reporte_'.$token);
        abort_unless($r,410,'La consulta venció. Actualice el reporte para continuar.');
        abort_unless((int)$r['usuario_id']===(int)$usuario->id,403);
        if ($r['masivo']) {
            $r['datos'] = DB::transaction(function () use ($r) {
                $d=$r['datos']; $d['resumen']=$this->resumenCfdisAdeudos($d['corte'], true);
                $d['cantidad']=array_sum(array_column($d['resumen'],'cantidad'));
                $d['cfdis']=$this->detalleCfdisAdeudos($d['corte'], true)->get()->map(function($r){return(array)$r;})->all();
                $d['pagos']=$this->pagosRecibidosCfdisAdeudos($d['corte'], true)->get()->map(function($r){return(array)$r;})->all();
                $d['operaciones']=$this->operacionesCfdisAdeudos($d['corte'], true)->get()->map(function($r){return(array)$r;})->all();
                $d['cantidadPagos']=count($d['pagos']); $d['totalRegistros']=$d['cantidad']+$d['cantidadPagos']+count($d['operaciones']);
                return $d;
            });
        }
        return $r;
    }

    /**
     * Detalle del método: Renderiza el reporte mensual usando el diseño de facturación con importes,
     * métodos y relaciones de cada factura.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado); $pdf (selecciona
     * presentación PDF frente a pantalla).
     * Salida / efectos: HTML para pantalla/PDF, sin generar todavía el XLSX.
     */
    private function htmlCfdisMes(array $datos, $pdf = false)
    {
        $ruta=public_path(env('LOGOJPG'));$logo=null;
        if(is_file($ruta)){$mime=(new \finfo(FILEINFO_MIME_TYPE))->file($ruta);
            if(in_array($mime,['image/jpeg','image/png','image/gif'],true))$logo='data:'.$mime.';base64,'.base64_encode(file_get_contents($ruta));}
        return view('reportes.temporales_visor_excel.facturacion.adeudos',[
            'datos'=>$datos,'secciones'=>\App\Exports\CfdisMesExport::secciones($datos),'logo'=>$logo,'modoPdf'=>$pdf
        ])->render();
    }

    /**
     * Detalle del método: Convierte el reporte mensual fiscal a PDF con la configuración y pie de
     * impresión.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado).
     * Salida / efectos: Documento PDF listo para descarga/envío.
     */
    private function pdfCfdisMes(array $datos)
    {
        $footer=view('reportes.temporales_visor_excel.compartidos.footer_pdf',[
            'fechaImpresion'=>\App\Exports\TimbresRecargasExport::fecha(now('America/Mazatlan')),
            'dominio'=>parse_url(config('app.url'),PHP_URL_HOST) ?: config('app.url')])->render();
        return PDF::loadHTML($this->htmlCfdisMes($datos,true))->setOptions([
            'orientation'=>'landscape','page-size'=>'A4','encoding'=>'UTF-8','print-media-type'=>true,
            'margin-top'=>10,'margin-bottom'=>12,'margin-left'=>10,'margin-right'=>10,
            'disable-local-file-access'=>true,'footer-html'=>$footer,'footer-spacing'=>3]);
    }

    /**
     * Detalle del método: Recupera el reporte privado del token y genera la descarga Excel correspondiente
     * al contexto seleccionado.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta de descarga Excel; verifica acceso/vigencia y no envía correo. El
     * resumen diario rechaza Excel.
     */
    public function descargarCfdisMesExcel(Request $request,$token)
    {
        $r=$this->datosCfdisMes($request,$token);
        $respuesta=\Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CfdisMesExport($r['datos']),$r['nombre']);
        $respuesta->headers->set('Cache-Control','no-store, private');return $respuesta;
    }
    /**
     * Detalle del método: Recupera el reporte privado del token y genera la descarga PDF correspondiente
     * al contexto seleccionado.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta de descarga PDF; verifica acceso/vigencia y no envía correo. El resumen
     * diario rechaza Excel.
     */
    public function descargarCfdisMesPdf(Request $request,$token)
    {
        $r=$this->datosCfdisMes($request,$token);
        $respuesta=$this->pdfCfdisMes($r['datos'])->download(preg_replace('/\.xlsx$/i','.pdf',$r['nombre']));
        $respuesta->headers->set('Cache-Control','no-store, private');return $respuesta;
    }

    /**
     * Detalle del método: Valida destinatarios, asunto, mensaje y formato, genera el adjunto del reporte y
     * delega el envío a EmailController.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío); $token (identificador privado
     * del contexto del reporte; no es el token de sesión).
     * Salida / efectos: Respuesta JSON del envío; el token debe pertenecer al usuario y el formato debe
     * estar permitido.
     */
    public function enviarCfdisMesCorreo(Request $request, $token)
    {
        // Autorizar antes de generar archivos o validar datos de destinatarios.
        $reporte = $this->datosCfdisMes($request, $token);
        $request->validate([
            'destinatario' => 'required_without:destinatarios|nullable|string|max:75',
            'email_address' => 'required_without:destinatarios|nullable|email|max:75',
            'destinatarios' => 'sometimes|required|array|min:1|max:20',
            'destinatarios.*.nombre' => 'nullable|string|max:75',
            'destinatarios.*.email' => 'required|email|max:75',
            'formato' => 'sometimes|required|in:excel,pdf',
            'completo' => 'sometimes|boolean',
            'mensaje' => 'nullable|string|max:30000',
            'asunto' => ['nullable', 'string', 'max:150', 'not_regex:/[\r\n]/'],
        ]);
        $personas = $request->input('destinatarios', [['nombre' => $request->input('destinatario'), 'email' => $request->input('email_address')]]);
        $vistos = [];
        foreach ($personas as &$persona) {
            $persona['email'] = mb_strtolower(trim($persona['email']), 'UTF-8');
            $persona['nombre'] = trim($persona['nombre'] ?? '');
            if (isset($vistos[$persona['email']])) throw \Illuminate\Validation\ValidationException::withMessages(['destinatarios' => 'Hay correos duplicados.']);
            $vistos[$persona['email']] = true;
        }
        unset($persona);
        if (mb_strlen(trim(html_entity_decode(strip_tags($request->input('mensaje', '') ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 'UTF-8') > 5000) {
            throw \Illuminate\Validation\ValidationException::withMessages(['mensaje' => 'El mensaje no debe superar 5000 caracteres.']);
        }
        abort_if($request->boolean('completo'), 422, 'Este reporte ya incluye el detalle completo de la facturación del mes.');
        $pdf = $request->input('formato', 'excel') === 'pdf';
        $contenido = $pdf ? $this->pdfCfdisMes($reporte['datos'])->output()
            : \Maatwebsite\Excel\Facades\Excel::raw(new \App\Exports\CfdisMesExport($reporte['datos']), \Maatwebsite\Excel\Excel::XLSX);
        return app(EmailController::class)->reporte_email([
            'email_address' => $personas[0]['email'], 'destinatario' => $personas[0]['nombre'], 'destinatarios' => $personas,
            'asunto' => $request->input('asunto') ?: $reporte['datos']['titulo'], 'mensaje' => $request->input('mensaje', ''),
        ], $contenido, $pdf ? preg_replace('/\.xlsx$/i', '.pdf', $reporte['nombre']) : $reporte['nombre'],
            $pdf ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

}

class Autenticar
{
    public $usuario;
    public $password;
}

class Parametros
{
    public $accesos;
    public $comprobante;
}

class Comprobante
{
    public $accesos;
    public $rfcEmisor;
    public $rfcReceptor;
    public $totalCFDI;
    public $uuid;
    public $selloCFDI;
}

class wsFolio
{
    public $motivo;
    public $folioSustitucion;
    public $uuid;
}

class wsFolios
{
    public $folio;


}

/*

Dim folio1 As wsFolio = New wsFolio                                              ‘Es un listado de objetos tipo folio donde se indica el motivo, folio de sustitucion y el uuid a cancelar.

            folio1.uuid = "BB450CC9-E7D9-4D06-B86B-72DEA13EDBA4"

            folio1.motivo = "02"

            folio1.folioSustitucion = ""



            Dim Folios As New wsFolios40()

            Folios.folio = folio1



            Dim Folios_40(0) As wsFolios40

            Folios_40(0) = Folios
            */
