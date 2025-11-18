<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmpresaController;
use App\Operaciones;
use Illuminate\Http\Request;
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

    public function reporte_especial()
    {
        $operaciones = Operaciones::selectRaw("
        id,
        empresa_operaciones_id,
        saldo,
        status,
        CASE empresa_operaciones_id
            WHEN 1 THEN 'VENTA DE TERRENOS'
            WHEN 2 THEN 'SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO'
            WHEN 3 THEN 'SERVICIOS FUNERARIOS'
            WHEN 4 THEN 'VENTA DE PLANES FUNERARIOS A FUTURO'
            WHEN 5 THEN 'VENTAS EN GENERAL'
            ELSE NULL
        END as tipo_operacion
    ")
            ->where('status', '>', 0)
            ->with('cfdis') // ya viene sin pivot
            ->limit(1500)
            ->orderby('id', 'desc')
            ->get();

        return $this->showAll($operaciones);
    }
}
