<?php

namespace App\Http\Controllers;

use App\Exports\ReporteEspecialExport;
use App\Exports\VentasPorCfdisExport;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmpresaController;
use App\Operaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportesController extends ApiController
{
    public function get_reportes(Request $request)
    {
        $email        = $request->email_send === 'true' ? true : false;
        $email_to     = $request->email_address;
        $datosRequest = [];
        $modulo       = '';
        $reporte      = '';
        $fecha        = '';
        $fecha_inicio = '';
        $fecha_fin    = '';

        if (isset($request->request_parent[0])) {
            $datosRequest = json_decode($request->request_parent[0], true);
        }
        if (isset($datosRequest['modulo']['value'])) {
            $modulo       = $datosRequest['modulo']['value'];
            $reporte      = $datosRequest['reporte']['value'];
            $fecha        = $datosRequest['fecha'];
            $fecha_inicio = $datosRequest['fecha_inicio'];
            $fecha_fin    = $datosRequest['fecha_fin'];
        } else {
            //return $this->errorResponse('Error al generar el reporte',409);
            $modulo       = 1;
            $reporte      = 2;
            $fecha        = now();
            $fecha_inicio = '1990-01-01';
            $fecha_fin    = now();
        }

        $inventario = new InventarioController();
        /**obteniendo datos para los reportes segun la peticion del usuario */

        /**guardo los datos a mostrar en el reporte */
        $datos_reporte = [];
        $name_pdf      = '';
        $header        = '';
        $footer        = '';
        $pdf_template  = '';

        if ($modulo == 1) {
            $header = 'reportes.inventario.header';
            $footer = 'reportes.inventario.footer';
            /**Inventarios*/
            if ($reporte == 1) {
                /**valido que ingresó la fecha */
                if (trim($fecha) == null) {
                    return $this->errorResponse('Ingrese la fecha para generar el reporte.', 409);
                }
                /**Existencias y Costos*/
                $datos_reporte = $inventario->get_reporte_existencias_costos($fecha);
                $name_pdf      = 'Existencias y Costos';
                $pdf_template  = 'reportes/inventario/existencias_costos/reporte';
            } elseif ($reporte == 2) {
                /**valido que ingresó las fechas */
                if (trim($fecha_inicio) == null || trim($fecha_fin) == null) {
                    return $this->errorResponse('Ingrese el rango de fechas para generar el reporte', 409);
                }
                /**Movimientos del inventario*/
                $datos_reporte = $inventario->get_reporte_movimientos_inventario($fecha_inicio, $fecha_fin);
                $name_pdf      = 'Movimientos del Inventario';
                $pdf_template  = 'reportes/inventario/movimientos_inventario/reporte';
            } elseif ($reporte == 3) {
                /**valido que ingresó las fechas */
                if (trim($fecha_inicio) == null || trim($fecha_fin) == null) {
                    return $this->errorResponse('Ingrese el rango de fechas para generar el reporte', 409);
                }
                /**Movimientos del inventario*/
                $datos_reporte = $inventario->get_reporte_inventario_con_rotacion($fecha_inicio, $fecha_fin);
                $name_pdf      = 'Inventario Actual Global en Importes (Costo Definido por el Prodcuto)';
                $pdf_template  = 'reportes/inventario/inventario_global/reporte';
            }
        } else if ($modulo == 2) {
            /**cementerio */
            $cementerio = new CementerioController();
            $funeraria  = new FunerariaController();
            if (trim($datosRequest['tipo_reporte']) != '') {
                if ($datosRequest['tipo_reporte'] == 'cuota_cementerio') {
                    return $cementerio->get_cuota_pdf('es', $request);
                } elseif ($datosRequest['tipo_reporte'] == 'reporte_mapa') {
                    return $cementerio->get_mapeado('es', $request);
                }
            } else {
                if ($reporte == 'reporte_propiedades') {
                    return $cementerio->get_abonos_vencidos_propiedades('es', $request);
                } else if ($reporte == 'reporte_planes') {
                    $funeraria->get_abonos_vencidos_planes_funerarios('es', $request);
                }
            }
        } else if ($modulo == 3) {
            /**funeraria */
            $funeraria = new FunerariaController();
            if ($reporte == 'reporte_planes') {
                return $funeraria->get_abonos_vencidos_planes_funerarios('es', $request);
            } else if ($reporte == 'reporte_servicios') {
                return $funeraria->get_servicios_adeudos('es', $request);
            }
        }

        $datos_reporte['name_pdf'] = $name_pdf;
        /**creando el pdf */
        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();
        $pdf           = PDF::loadView($pdf_template, ['datos' => $datos_reporte, 'empresa' => $empresa]);

        $name_pdf = $name_pdf . '.pdf';

        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view($footer),
        ]);
        $pdf->setOptions([
            'header-html' => view($header),
        ]);

        $pdf->setOption('margin-left', 12.5);
        $pdf->setOption('margin-right', 12.5);
        $pdf->setOption('margin-top', 12.5);
        $pdf->setOption('margin-bottom', 12.5);
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
            $enviar_email     = $email_controller->pdf_email(
                $email_to,
                $request->destinatario,
                '',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }

        return $this->errorResponse($datos_reporte, 409);
    }

    public function adeudos_cfdis()
    {
        $operaciones = Operaciones::join('clientes', 'clientes.id', '=', 'operaciones.clientes_id')
            ->selectRaw("
        operaciones.id,
         clientes.nombre as cliente_nombre,
    operaciones.empresa_operaciones_id,
    ventas_terrenos_id,
    servicios_funerarios_id,
    ventas_planes_id,
    ventas_generales_id,
    operaciones.saldo,
    operaciones.total,
    operaciones.status,
    CASE empresa_operaciones_id
        WHEN 1 THEN 'VENTA DE TERRENOS'
        WHEN 2 THEN 'SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO'
        WHEN 3 THEN 'SERVICIOS FUNERARIOS'
        WHEN 4 THEN 'VENTA DE PLANES FUNERARIOS A FUTURO'
        WHEN 5 THEN 'VENTAS EN GENERAL'
        ELSE NULL
    END AS tipo_operacion
    ")
            ->where('operaciones.status', '>', 0) //Solo activas
            ->with('cfdis')
            ->whereHas('cfdis')
            ->withCount('cfdis')
            ->whereHas('cfdis', function ($q) {
                $q->whereRaw("
        (
            SELECT COALESCE(SUM(r.monto_relacion), 0)
            FROM cfdis_tipo_relacion r
            JOIN cfdis c2 ON c2.id = r.cfdis_id
            WHERE r.cfdis_id_relacionado = cfdis.id
            AND r.tipo_relacion_id IN (2,3)
            AND c2.status > 0
        ) < cfdis.total
    ");
            })
            // ->where('operaciones.empresa_operaciones_id', 4) //Filtro por tipo operacion
            //->limit(500)
            ->orderby('empresa_operaciones_id', 'asc')
            ->orderby('fecha_timbrado', 'desc')
            ->get();
        /* return [
            'total'       => count($operaciones),
            'operaciones' => $operaciones,
        ];*/

        return Excel::download(new ReporteEspecialExport($operaciones), 'reporte_especial.xlsx');
    }

    public function ventas_x_mes_x_cfdis($year = null, $mes = null)
    {

        // ---------------------------------------------------------
        // VALIDATION
        // ---------------------------------------------------------
        if (! is_numeric($year) || ! is_numeric($mes)) {
            return $this->errorResponse('Año o mes inválido. Debe ser un número correcto.', 409);
        }

        $year = (int) $year;
        $mes  = (int) $mes;

        $currentYear  = (int) date('Y');
        $currentMonth = (int) date('m');

        // Year cannot be in the future or less than 2000 (arbitrary lower limit)
        if ($year < 2020 || $year > $currentYear) {
            return $this->errorResponse('Año no válido.', 409);
        }

        // Month must be 1–12
        if ($mes < 1 || $mes > 12) {
            return $this->errorResponse('Mes no válido.', 409);
        }

        // If same year, month cannot be in the future
        if ($year === $currentYear && $mes > $currentMonth) {
            return $this->errorResponse('Este mes está fuera de rango.', 409);
        }

        $rows = DB::table('cfdis')
            ->selectRaw("
    cfdis.id,
    cfdis.uuid,
    cfdis.clientes_id,
    cfdis.sat_formas_pago_id,
    cfdis.subtotal,
    cfdis.descuento,
    cfdis.total,
    cfdis.sat_metodos_pago_id,
    cfdis.rfc_receptor,
    cfdis.nombre_receptor,
    cfdis.fecha_timbrado,
    cfdis.status,

    operaciones.id as operacion_id,
    operaciones.clientes_id as operacion_cliente_id,
    operaciones.empresa_operaciones_id,
    operaciones.subtotal as operacion_subtotal,
    operaciones.descuento as operacion_descuento,
    operaciones.total as operacion_total,
    operaciones.saldo as operacion_saldo,
    CASE operaciones.empresa_operaciones_id
        WHEN 1 THEN 'VENTA EN CEMENTERIO'
        WHEN 2 THEN 'CUOTA DE MANTENIMIENTO'
        WHEN 3 THEN 'SERVICIO FUNERARIO'
        WHEN 4 THEN 'VENTA DE PLAN A FUTURO'
        WHEN 5 THEN 'VENTA EN GENERAL'
        ELSE NULL
    END AS tipo_operacion,

    CASE operaciones.empresa_operaciones_id
        WHEN 1 THEN operaciones.ventas_terrenos_id
        WHEN 2 THEN operaciones.cuotas_cementerio_id
        WHEN 3 THEN operaciones.servicios_funerarios_id
        WHEN 4 THEN operaciones.ventas_planes_id
        WHEN 5 THEN operaciones.ventas_generales_id
        ELSE NULL
    END AS operacion_venta_id,

    clientes.nombre
")

            ->join('cfdis_operaciones', 'cfdis_operaciones.cfdis_id', '=', 'cfdis.id')
            ->join('operaciones', 'operaciones.id', '=', 'cfdis_operaciones.operaciones_id')
            ->join('clientes', 'operaciones.clientes_id', '=', 'clientes.id')

            ->whereYear('cfdis.fecha_timbrado', $year)
            ->whereMonth('cfdis.fecha_timbrado', $mes)

            ->where('cfdis.status', '>', 0)
            ->where('operaciones.status', '>', 0)

            ->where('cfdis.sat_tipo_comprobante_id', 1) // solo ingresos
            ->orderBy('cfdis.fecha_timbrado', 'desc')
            ->get();

        // -----------------------------
        // GROUPED CFDIS (DATA)
        // -----------------------------
        $data = $rows->groupBy('id')->map(function ($items) {
            $cfdi = $items->first();

            return [
                'id'             => $cfdi->id,
                'uuid'           => $cfdi->uuid,
                'rfc_receptor'           => $cfdi->rfc_receptor,
                'nombre_receptor'           => $cfdi->nombre_receptor,
                'clientes_id'    => $cfdi->clientes_id,
                'total'          => $cfdi->total,
                'fecha_timbrado' => fecha_abr($cfdi->fecha_timbrado),
                'operaciones'    => $items->map(function ($op) {
                    return [
                        'operacion_id'   => $op->operacion_id,
                        'total'          => round($op->total, 2),
                        'tipo_operacion' => $op->tipo_operacion,
                        'id_referencia'  => $op->operacion_venta_id,
                        'cliente' => $op->nombre,
                    ];
                }),
            ];
        })->values(); // important to remove keys

        // -----------------------------
        // FORZAR LOS 5 TIPOS EN SUMMARY
        // -----------------------------
        $tipos = [
            'VENTA EN CEMENTERIO',
            'CUOTA DE MANTENIMIENTO',
            'SERVICIO FUNERARIO',
            'VENTA DE PLAN A FUTURO',
            'VENTA EN GENERAL',
        ];
        $tipos_operacion = collect($tipos)->mapWithKeys(function ($tipo) use ($rows) {
            $group = $rows->where('tipo_operacion', $tipo);

            return [
                $tipo => [
                    'count' => $group->count(),
                    'total' => round($group->sum('operacion_total'), 2),
                ],
            ];
        });
        // -----------------------------
        // GLOBAL SUMMARY
        // -----------------------------
        $summary = [
            'total_cfdis'       => $data->count(),
            'total_operaciones' => $rows->count(),
            'monto_facturado'   => round($data->sum('total'), 2),
            'operaciones'       => $tipos_operacion,
        ];

        $fecha = now()
            ->locale('es')
            ->translatedFormat('D d M H-i-s') . ' hrs';

        $filename = "reporte_especial_{$fecha}.xlsx";
        return Excel::download(new VentasPorCfdisExport($data, $summary), $filename);
        return [
            'summary' => $summary,
            'data'    => $data,
        ];
    }
}
