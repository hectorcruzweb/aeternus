<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;

class OperacionesController extends ApiController
{
    /** Histórico de seguimientos vencidos: consultas en SeguimientosController, transporte compartido. */
    public function previewResumenDashboard(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $corte = Carbon::now('America/Mazatlan');
        $datos = DB::transaction(function () use ($corte,$request) {
            $dashboard=app(DashboardController::class)->buildDashboard($request)->getData(true);
            // Contactos solo para este reporte, en una consulta para todos los servicios.
            $ids=array_column($dashboard['servicios_funerarios']??[],'id');
            $contactos=$ids ? DB::table('operaciones as o')->leftJoin('clientes as c','c.id','=','o.clientes_id')
                ->where('o.empresa_operaciones_id',3)->whereIn('o.status',[1,2])->whereIn('o.servicios_funerarios_id',$ids)
                ->select('o.id','o.servicios_funerarios_id','c.nombre','c.telefono','c.celular')->get()->keyBy('id') : collect();
            $temporales=$ids ? DB::table('servicios_funerarios')->whereIn('id',$ids)
                ->select('id','parentesco_contratante_temp','telefono_contratante_temp','nombre_contratante_temp')->get()->keyBy('id') : collect();
            foreach($dashboard['servicios_funerarios'] as &$servicio){
                $operacion=$servicio['datosoperacion']??[];
                $contacto=$contactos->get($operacion['id']??null);
                $servicio['cliente_reporte']=$contacto ? ['nombre'=>$contacto->nombre,'telefono'=>$contacto->telefono,'celular'=>$contacto->celular] : null;
                $servicio['contratante_temporal_reporte']=empty($operacion);
                if(empty($operacion)){
                    $temporal=$temporales->get($servicio['id']);
                    $servicio['cliente_reporte']=$temporal ? ['nombre'=>$temporal->nombre_contratante_temp,
                        'telefono'=>$temporal->telefono_contratante_temp,'celular'=>null,
                        'parentesco'=>$temporal->parentesco_contratante_temp] : null;
                }

            }
            unset($servicio);

            return ['tipo_operacion'=>'resumen_dashboard','titulo'=>'Resumen del Día — Aeternus Funerales',
                'periodo'=>$corte->locale('es')->translatedFormat('F \d\e Y'),'fecha'=>$corte->format('d/m/Y H:i'),
                'dashboard'=>$dashboard,'totalRegistros'=>1,'resumenTexto'=>'Estado general a la fecha de consulta',
                'avisoSaldos'=>'Importes de capital sin intereses. Los alcances mensual, diario e histórico no deben sumarse entre sí.'];
        });
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $visualizable = true;
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = 'Resumen Dashboard '.$corte->format('Y-m-d H-i-s').'.xlsx';
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_'.$token, [
            'usuario_id' => (int) $usuario->id, 'datos' => $datos, 'nombre' => $nombre,
            'modo' => $visualizable ? 'instantanea' : 'seguimientos_vencidos_filtro',
        ], $vence);
        return response()->json([
            'soloPdf'=>true, 'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => 3500, 'totalRegistros' => $datos['totalRegistros'],
            'mensaje' => $visualizable ? null : 'No se puede visualizar este reporte por su tamaño. Puede descargarlo completo. Los datos se actualizarán al generar el archivo.',
            'hojas' => $visualizable ? [['nombre' => 'Resumen del día', 'html' => $this->htmlReporteCementerio($datos)]] : [],
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Histórico de seguimientos vencidos: consultas en SeguimientosController, transporte compartido. */
    public function previewSeguimientosVencidosHistorico(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $corte = Carbon::now('America/Mazatlan');
        $datos = DB::transaction(function () use ($corte) {
            $modulo = app(SeguimientosController::class);
            $datos = $modulo->getReporteSeguimientosVencidosHistorico($corte, true);
            return $datos['totalRegistros'] <= 3500 ? $modulo->getReporteSeguimientosVencidosHistorico($corte) : $datos;
        });
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $visualizable = isset($datos['seguimientos']);
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = 'Seguimientos Vencidos Historicos '.$corte->format('Y-m-d H-i-s').'.xlsx';
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_'.$token, [
            'usuario_id' => (int) $usuario->id, 'datos' => $datos, 'nombre' => $nombre,
            'modo' => $visualizable ? 'instantanea' : 'seguimientos_vencidos_filtro',
        ], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => 3500, 'totalRegistros' => $datos['totalRegistros'],
            'mensaje' => $visualizable ? null : 'No se puede visualizar este reporte por su tamaño. Puede descargarlo completo. Los datos se actualizarán al generar el archivo.',
            'hojas' => $visualizable ? [['nombre' => 'Seguimientos vencidos', 'html' => $this->htmlReporteCementerio($datos)]] : [],
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Agenda de próximos siete días: consultas en SeguimientosController, transporte compartido. */
    public function previewProgramadosProximosSieteDias(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $corte = Carbon::now('America/Mazatlan');
        $datos = DB::transaction(function () use ($corte) {
            $modulo = app(SeguimientosController::class);
            $datos = $modulo->getReporteProgramadosProximosSieteDias($corte, true);
            return $datos['totalRegistros'] <= 3500 ? $modulo->getReporteProgramadosProximosSieteDias($corte) : $datos;
        });
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $visualizable = isset($datos['seguimientos']);
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = 'Seguimientos Programados Proximos 7 Dias '.$corte->format('Y-m-d H-i-s').'.xlsx';
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_'.$token, [
            'usuario_id' => (int) $usuario->id, 'datos' => $datos, 'nombre' => $nombre,
            'modo' => $visualizable ? 'instantanea' : 'seguimientos_programados_filtro',
        ], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => 3500, 'totalRegistros' => $datos['totalRegistros'],
            'mensaje' => $visualizable ? null : 'No se puede visualizar este reporte por su tamaño. Puede descargarlo completo. Los datos se actualizarán al generar el archivo.',
            'hojas' => $visualizable ? [['nombre' => 'Seguimientos programados', 'html' => $this->htmlReporteCementerio($datos)]] : [],
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Transporte mensual de seguimientos: reglas y datos en SeguimientosController. */
    public function previewSeguimientosRealizadosMes(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $corte = Carbon::now('America/Mazatlan');
        $datos = DB::transaction(function () use ($corte) {
            $modulo = app(SeguimientosController::class);
            $datos = $modulo->getReporteSeguimientosRealizadosMes($corte, true);
            return $datos['totalRegistros'] <= 3500 ? $modulo->getReporteSeguimientosRealizadosMes($corte) : $datos;
        });
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $visualizable = isset($datos['seguimientos']);
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = 'Seguimientos Realizados del Mes '.$corte->format('Y-m-d H-i-s').'.xlsx';
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_'.$token, [
            'usuario_id' => (int) $usuario->id, 'datos' => $datos, 'nombre' => $nombre,
            'modo' => $visualizable ? 'instantanea' : 'seguimientos_realizados_filtro',
        ], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => 3500, 'totalRegistros' => $datos['totalRegistros'],
            'mensaje' => $visualizable ? null : 'No se puede visualizar este reporte por su tamaño. Puede descargarlo completo. Los datos se actualizarán al generar el archivo.',
            'hojas' => $visualizable ? [['nombre' => 'Seguimientos realizados', 'html' => $this->htmlReporteCementerio($datos)]] : [],
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Transporte de solicitudes: las consultas y sus reglas permanecen en FunerariaController. */
    public function previewSolicitudesSinContrato(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $corte = Carbon::now('America/Mazatlan');
        $datos = DB::transaction(function () use ($corte) {
            $modulo = app(FunerariaController::class);
            $datos = $modulo->getReporteSolicitudesSinContrato($corte, true);
            return $datos['totalRegistros'] <= 3500 ? $modulo->getReporteSolicitudesSinContrato($corte) : $datos;
        });
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $visualizable = isset($datos['solicitudes']);
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = 'Solicitudes de Servicio Funerario sin Contrato '.$corte->format('Y-m-d H-i-s').'.xlsx';
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_'.$token, [
            'usuario_id' => (int) $usuario->id, 'datos' => $datos, 'nombre' => $nombre,
            'modo' => $visualizable ? 'instantanea' : 'solicitudes_sin_contrato_filtro',
        ], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => 3500, 'totalRegistros' => $datos['totalRegistros'],
            'mensaje' => $visualizable ? null : 'No se puede visualizar este reporte por su tamaño. Puede descargarlo completo. Los datos se actualizarán al generar el archivo.',
            'hojas' => $visualizable ? [['nombre' => 'Solicitudes sin contrato', 'html' => $this->htmlReporteCementerio($datos)]] : [],
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Catálogo compartido por los resúmenes y reportes de operaciones. */
    public function getTiposOperacionesDashboard()
    {
        return [1 => 'Ventas en cementerio', 2 => 'Cuotas de Mantenimiento', 3 => 'Servicios funerarios',
            4 => 'Venta de planes funerarios', 5 => 'Ventas en general'];
    }

    /**
     * Una fila por pago programado pendiente en el rango de 1 o 7 días, incluyendo hoy.
     * Primero suma aplicaciones vigentes a capital: no repite importes al unir pagos.
     * Excluye canceladas, intereses, saldados y operaciones posteriores al corte.
     * El rango conserva el uso del índice de fecha y acepta columnas DATE o DATETIME.
     */
    private function consultaPagosPorVencer(Carbon $corte, $dias = 1)
    {
        $capital = DB::table('pagos_pagos_programados as ppp')
            ->join('pagos as p', 'p.id', '=', 'ppp.pagos_id')->where('p.status', 1)
            ->whereIn('p.movimientos_pagos_id', [1, 3, 4, 5])
            ->select('ppp.pagos_programados_id')->selectRaw('SUM(ppp.monto) AS capital_cubierto')
            ->groupBy('ppp.pagos_programados_id');
        $pendiente = 'ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2)';
        $consulta = DB::table('pagos_programados as pp')
            ->join('operaciones as o', 'o.id', '=', 'pp.operaciones_id')
            ->leftJoinSub($capital, 'a', function ($j) { $j->on('a.pagos_programados_id', '=', 'pp.id'); })
            ->whereIn('o.empresa_operaciones_id', array_keys($this->getTiposOperacionesDashboard()))
            ->whereIn('o.status', [1, 2])->where('o.fecha_operacion', '<=', $corte)
            ->where('pp.status', 1)
            ->whereRaw($pendiente.' > 0')
            ->select('pp.id as pago_programado_id', 'pp.referencia_pago', 'pp.fecha_programada', 'pp.monto_programado',
                'o.id as operacion_id', 'o.empresa_operaciones_id', 'o.fecha_operacion', 'o.clientes_id')
            ->selectRaw('COALESCE(a.capital_cubierto, 0) AS capital_cubierto, '.$pendiente.' AS pendiente')
            ->selectRaw('CASE o.empresa_operaciones_id WHEN 1 THEN o.ventas_terrenos_id WHEN 2 THEN o.ventas_terrenos_id WHEN 3 THEN o.servicios_funerarios_id WHEN 4 THEN o.ventas_planes_id WHEN 5 THEN o.ventas_generales_id END AS numero_venta');
        // dias=0: histórico vencido. Los demás valores conservan la agenda vigente.
        if ($dias === 0) {
            $consulta->where('pp.fecha_programada', '>=', '1000-01-01')
                ->where('pp.fecha_programada', '<', $corte->toDateString());
        } else {
            $consulta->where('pp.fecha_programada', '>=', $corte->toDateString())
                ->where('pp.fecha_programada', '<', $corte->copy()->addDays($dias)->toDateString());
        }
        return $consulta;
    }

    /** Clientes únicos del histórico vencido; no cargar contactos ni filas en el dashboard. */
    public function getClientesPagosVencidosDashboard(Carbon $fechaCorte = null)
    {
        $corte=$fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : Carbon::now('America/Mazatlan');
        return (int) DB::query()->fromSub($this->consultaPagosPorVencer($corte,0),'q')
            ->selectRaw('COUNT(DISTINCT COALESCE(q.clientes_id,0)) AS cantidad')->value('cantidad');
    }

    public function getReporteClientesPagosVencidos(Carbon $fechaCorte = null, $soloResumen = false)
    {
        return $this->getReportePagosPorVencer(0,$fechaCorte,$soloResumen);
    }

    public function previewClientesPagosVencidos(Request $request)
    {
        return $this->previewPagosPorVencer($request,0);
    }

    /** Solo agregados: el dashboard no consulta contactos ni carga pagos individuales. */
    public function getPagosVenceHoyDashboard(Carbon $fechaCorte = null)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : Carbon::now('America/Mazatlan');
        $datos = $this->resumenPagosPorVencer($corte, 1);
        return ['cantidad' => $datos['cantidad'], 'importe' => $datos['pendiente']];
    }

    /** Siete días naturales: hoy y los siguientes seis, con los mismos criterios de capital. */
    public function getPagosProximosSieteDiasDashboard(Carbon $fechaCorte = null)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : Carbon::now('America/Mazatlan');
        $datos = $this->resumenPagosPorVencer($corte, 7);
        return ['cantidad' => $datos['cantidad'], 'importe' => $datos['pendiente']];
    }

    /** Una consulta agregada, reutilizada por el indicador y por el reporte detallado. */
    private function resumenPagosPorVencer(Carbon $corte, $dias = 1)
    {
        $filas = DB::query()->fromSub($this->consultaPagosPorVencer($corte, $dias), 'q')
            ->select('q.empresa_operaciones_id')->selectRaw('COUNT(*) AS cantidad, SUM(q.monto_programado) AS programado, SUM(q.capital_cubierto) AS aplicado, SUM(q.pendiente) AS pendiente')
            ->groupBy('q.empresa_operaciones_id')->get()->keyBy('empresa_operaciones_id');
        $resumen = ['cantidad' => 0, 'programado' => 0, 'aplicado' => 0, 'pendiente' => 0, 'tipos' => []];
        foreach ($this->getTiposOperacionesDashboard() as $id => $nombre) {
            $fila = $filas->get($id);
            $tipo = ['id' => $id, 'nombre' => $nombre, 'cantidad' => $fila ? (int) $fila->cantidad : 0];
            foreach (['programado', 'aplicado', 'pendiente'] as $campo) $tipo[$campo] = $fila ? round((float) $fila->$campo, 2) : 0;
            foreach (['cantidad', 'programado', 'aplicado', 'pendiente'] as $campo) $resumen[$campo] += $tipo[$campo];
            $resumen['tipos'][] = $tipo;
        }
        foreach (['programado', 'aplicado', 'pendiente'] as $campo) $resumen[$campo] = round($resumen[$campo], 2);
        return $resumen;
    }

    /** Detalle bajo demanda. No expone IDs internos de operación/pago al usuario. */
    public function getReportePagosVenceHoy(Carbon $fechaCorte = null, $soloResumen = false)
    {
        return $this->getReportePagosPorVencer(1, $fechaCorte, $soloResumen);
    }

    public function getReportePagosProximosSieteDias(Carbon $fechaCorte = null, $soloResumen = false)
    {
        return $this->getReportePagosPorVencer(7, $fechaCorte, $soloResumen);
    }

    /** Motor compartido: intervalo explícito y datos específicos únicamente al pedir detalle. */
    private function getReportePagosPorVencer($dias, Carbon $fechaCorte = null, $soloResumen = false)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : Carbon::now('America/Mazatlan');
        $resumen = $this->resumenPagosPorVencer($corte, $dias);
        $fecha = $corte->format('d').'/'.ucfirst(rtrim($corte->locale('es')->translatedFormat('M'), '.')).'/'.$corte->format('Y');
        $fin = $corte->copy()->addDays($dias - 1);
        $fechaFin = $fin->format('d').'/'.ucfirst(rtrim($fin->locale('es')->translatedFormat('M'), '.')).'/'.$fin->format('Y');
        $detalle = $dias === 1 ? 'Pagos que vencen hoy' : 'Pagos próximos 7 días';
        $etiquetaImporte = $dias === 1 ? 'Por cobrar hoy' : 'Por cobrar del periodo';
        $datos = ['tipo_operacion' => $dias === 1 ? 'vence_hoy' : 'proximos_siete_dias',
            'titulo' => $dias === 1 ? 'Reporte de Pagos que Vencen Hoy' : 'Reporte de Pagos de los Próximos 7 Días',
            'modulo' => $detalle, 'etiquetaDetalle' => $detalle, 'etiquetaImporte' => $etiquetaImporte,
            'periodo' => $dias === 1 ? $fecha : $fecha.' al '.$fechaFin.' (incluye hoy)', 'fecha' => $fecha.' '.$corte->format('H:i'),
            'dias' => $dias, 'hasta' => $fin->toDateString(),
            'corte' => $corte->toDateTimeString(), 'fechaConsulta' => $corte->toDateString(),
            'resumen' => $resumen, 'totalRegistros' => $resumen['cantidad'],
            'resumenTexto' => $resumen['cantidad'].' pagos programados pendientes | '.$etiquetaImporte.': $ '.number_format($resumen['pendiente'], 2).' MXN',
            'avisoSaldos' => 'Importes pendientes de capital. No incluyen intereses. Los pagos que vencen hoy todavía no se consideran vencidos.'];
        if ($dias === 0) {
            $datos['tipo_operacion']='clientes_pagos_vencidos';
            $datos['titulo']='Reporte de Clientes con Pagos Vencidos';
            $datos['modulo']=$datos['etiquetaDetalle']='Pagos vencidos históricos';
            $datos['etiquetaImporte']='Pendiente vencido';
            $datos['notaResumen']='Histórico de pagos vencidos pendientes, agrupados por tipo de operación. No incluye intereses.';
            $datos['periodo']='Histórico acumulado al '.$fecha;
            $datos['encabezadoPeriodo']=$datos['periodo'].' | Consultado a las '.$corte->format('H:i');
            $datos['hasta']=$corte->copy()->subDay()->toDateString();
            $datos['resumen']['clientes']=$this->getClientesPagosVencidosDashboard($corte);
            $datos['resumenTexto']=$datos['resumen']['clientes'].' clientes | '.$resumen['cantidad'].' pagos vencidos | Pendiente: $ '.number_format($resumen['pendiente'],2).' MXN';
            $datos['avisoSaldos']='Importes pendientes de capital. NO INCLUYEN INTERESES. Solo pagos con fecha anterior a hoy; los que vencen hoy todavía no están vencidos.';
        }
        if ($soloResumen) return $datos;
        $tipos = $this->getTiposOperacionesDashboard();
        // Próximos pagos: primero lo más cercano. Vence hoy conserva su orden anterior.
        $direccion = $dias === 7 ? 'asc' : 'desc';
        $consultaDetalle = $this->consultaPagosPorVencer($corte, $dias)
            ->leftJoin('clientes as c', 'c.id', '=', 'o.clientes_id')
            // Fallecido solo para tipo 3; una unión en bloque, sin consultas por servicio.
            ->leftJoin('servicios_funerarios as sf', function ($j) {
                $j->on('sf.id', '=', 'o.servicios_funerarios_id')->where('o.empresa_operaciones_id', '=', 3);
            })
            ->leftJoin('ventas_terrenos as vt', function ($j) {
                $j->on('vt.id', '=', 'o.ventas_terrenos_id')->whereIn('o.empresa_operaciones_id', [1, 2]);
            })
            ->addSelect('c.nombre as cliente', 'c.telefono', 'c.celular', 'c.email', 'sf.nombre_afectado as fallecido', 'vt.ubicacion', 'pp.conceptos_pagos_id');
        // Agrupar visualmente cada cliente, manteniendo los pagos antiguos primero.
        if ($dias === 0) {
            $consultaDetalle->orderBy('c.nombre')->orderBy('o.clientes_id')->orderBy('pp.fecha_programada')->orderBy('pp.id');
        } else {
            $consultaDetalle->orderBy('pp.fecha_programada',$direccion)->orderBy('o.fecha_operacion',$direccion)->orderBy('pp.id',$direccion);
        }
        $datos['pagos']=$consultaDetalle->get()->map(function ($f) use ($tipos, $corte, $dias) {
                // Catálogo simbólico confirmado: no requiere una tabla adicional.
                $f->concepto_pago = [1 => 'Enganche', 2 => 'Abono', 3 => 'Pago único'][(int) $f->conceptos_pagos_id] ?? 'No disponible';
                $f->tipo_operacion = $tipos[(int) $f->empresa_operaciones_id];
                $programada = Carbon::parse($f->fecha_programada, 'America/Mazatlan');
                $f->fecha_programada_texto = $programada->format('d').'/'.ucfirst(rtrim($programada->locale('es')->translatedFormat('M'), '.')).'/'.$programada->format('Y');
                $f->estado_pago = $dias === 0 ? 'Vencido' : ($programada->toDateString() === $corte->toDateString() ? 'Vence hoy' : 'Por vencer');
                $f->email = mb_strtolower(trim($f->email ?? ''), 'UTF-8');
                return (array) $f;
            })->all();
        $codigos = array_column(array_filter($datos['pagos'], function ($f) { return in_array((int) $f['empresa_operaciones_id'], [1, 2], true); }), 'ubicacion');
        $ubicaciones = app(CementerioController::class)->getUbicacionesTextoReporte($codigos);
        foreach ($datos['pagos'] as &$pago) {
            $pago['ubicacion_texto'] = in_array((int) $pago['empresa_operaciones_id'], [1, 2], true)
                ? ($ubicaciones[trim((string) $pago['ubicacion'])] ?? 'No disponible') : '—';
        }
        unset($pago);
        if ($dias === 0) {
            // Una fila por cliente: no repetir su saldo por cada operación o pago.
            $clientes=[];
            foreach ($datos['pagos'] as $indice => $p) {
                $id=(string)($p['clientes_id']??0);
                if(!isset($clientes[$id])) $clientes[$id]=['cliente'=>$p['cliente']?:'No disponible',
                    'telefono'=>$p['telefono'],'celular'=>$p['celular'],'email'=>$p['email'],
                    'cantidad'=>0,'operaciones'=>[],'pendiente'=>0,'pagos_indices'=>[]];
                // Índices: agrupar sin duplicar cada pago en memoria y en caché.
                $clientes[$id]['pagos_indices'][]=$indice;
                $clientes[$id]['cantidad']++;
                $clientes[$id]['operaciones'][(string)$p['operacion_id']]=true;
                $clientes[$id]['pendiente']+=(float)$p['pendiente'];
            }
            foreach($clientes as &$c){$c['operaciones']=count($c['operaciones']);$c['pendiente']=round($c['pendiente'],2);}unset($c);
            $datos['clientes']=array_values($clientes);
        }
        return $datos;
    }

    /** Mismo visor/transporte autenticado de los reportes de operaciones. */
    public function previewPagosVenceHoy(Request $request)
    {
        return $this->previewPagosPorVencer($request, 1);
    }

    public function previewPagosProximosSieteDias(Request $request)
    {
        return $this->previewPagosPorVencer($request, 7);
    }

    private function previewPagosPorVencer(Request $request, $dias)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $corte = Carbon::now('America/Mazatlan');
        $limite = $dias === 0 ? 5000 : 3500; // Pagos, no cabeceras o subtotales de clientes.
        $datos = DB::transaction(function () use ($corte, $dias, $limite) {
            $resumen = $this->getReportePagosPorVencer($dias, $corte, true);
            return $resumen['totalRegistros'] <= $limite ? $this->getReportePagosPorVencer($dias, $corte) : $resumen;
        });
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $visualizable = isset($datos['pagos']);
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = ($dias === 0 ? 'Clientes con Pagos Vencidos ' : ($dias === 1 ? 'Pagos que Vencen Hoy ' : 'Pagos Proximos 7 Dias ')).$corte->format('Y-m-d H-i-s').'.xlsx';
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_'.$token, [
            'usuario_id' => (int) $usuario->id, 'datos' => $datos, 'nombre' => $nombre,
            'modo' => $visualizable ? 'instantanea' : ($dias === 0 ? 'clientes_vencidos_filtro' : ($dias === 1 ? 'vence_hoy_filtro' : 'proximos_pagos_filtro')),
        ], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => $limite, 'totalRegistros' => $datos['totalRegistros'],
            'mensaje' => $visualizable ? null : 'No se puede visualizar este reporte por su tamaño. Puede descargarlo. Los datos se actualizarán al generar el archivo, conservando el periodo consultado.',
            'hojas' => $visualizable ? [['nombre' => $datos['modulo'], 'html' => $this->htmlReporteCementerio($datos)]] : [],
        ])->header('Cache-Control', 'no-store, private');
    }


    /** Alcance visible y persistente en visor/Excel/PDF/correo de reportes de gráfica. */
    private function anotarAlcanceVencido(array $datos,$hastaPeriodo,$limiteExclusivo)
    {
        $datos['vencido_hasta_periodo']=(bool)$hastaPeriodo;
        if($hastaPeriodo){
            $fin=Carbon::parse($limiteExclusivo,'America/Mazatlan')->subDay()->locale('es');
            $fecha=$fin->format('d').'/'.ucfirst(rtrim($fin->translatedFormat('M'),'.')).'/'.$fin->format('Y');
            $datos['alcance_vencido']='Vencido acumulado hasta '.$fecha.'. Se muestra lo que sigue pendiente actualmente. No incluye intereses.';
            $datos['encabezadoPeriodo']=ucfirst($datos['periodo']).' | '.$datos['alcance_vencido'];
        }
        return $datos;
    }

    /** Gráfica mensual: solo agregados de los tipos, sin fila general ni detalles. */
    public function getGraficaVentasSaldosDashboard(Request $request)
    {
        $this->usuarioReporteCementerio($request);
        $corte=Carbon::now('America/Mazatlan');
        $request->validate(['anio'=>'required|integer|min:1000|max:'.$corte->format('Y'),
            'mes'=>'required|integer|between:1,12']);
        try {
            $filas=$this->getVentasSaldosOperacionesDashboard(null,$request->anio,$request->mes,$corte,true);
        } catch (\InvalidArgumentException $e) {
            abort(422,$e->getMessage());
        }
        // El general ya se calculó en memoria; la gráfica ofrece solo tipos individuales.
        $filas=array_values(array_filter($filas,function($f){return $f['id']!=='todas';}));
        return response()->json(['periodo'=>['anio'=>(int)$request->anio,'mes'=>(int)$request->mes],
            'fechaCorte'=>$corte->toDateString(),'operaciones'=>$filas])->header('Cache-Control','no-store, private');
    }

    /**
     * Resumen por tipo: ventas/saldo del periodo y vencido acumulado al corte.
     * tipoOperacion null: total general seguido de los tipos del catálogo; con ID: solo ese tipo.
     * Sin año/mes: mes actual; año y mes: mes; solo año: acumulado anual.
     * Devuelve únicamente resúmenes; los reportes desglosados quedan separados.
     * No carga clientes, ventas individuales ni pagos individuales en PHP.
     * Por cobrar suma el pendiente de pagos programados con fecha en el periodo,
     * aunque la operación se haya vendido en meses anteriores.
     * No utiliza operaciones.saldo; una operación sin programados activos aporta cero.
     * Vencido: capital pendiente vencido de TODAS las operaciones vigentes hasta el corte.
     * Por defecto el año/mes filtra ventas/porCobrar; el vencido llega hasta hoy.
     * vencidoHastaPeriodo=true limita vencimientos al fin del mes elegido (gráfica),
     * conservando el pendiente actual y sin reconstruir pagos al cierre histórico.
     * Es acumulado desde el origen, no solo vencimientos ocurridos dentro de ese mes.
     * Aplica movimientos activos 1/3/4/5; excluye intereses y pagos cancelados.
     * No calcula cobrado ni reconstruye saldos históricos.
     * La autorización del usuario se valida en el controlador de entrada HTTP.
     * @param int|string|null $tipoOperacion empresa_operaciones_id; null = todas.
     * @param int|string|null $anio Año a consultar.
     * @param int|string|null $mes Mes 1-12; null con año explícito = anual.
     * @param Carbon|null $fechaCorte Referencia común, copiada sin modificarla.
     * @return array Total general + tipos del catálogo, o uno al filtrar por tipo.
     * Cada fila: id, nombre, ventas, porCobrar y vencido; sin movimientos incluye ceros.
     * El general usa id="todas", que no es un ID de tipo en la base de datos.
     * @throws \InvalidArgumentException Por tipo/periodo inválido o futuro.
     */
    public function getVentasSaldosOperacionesDashboard($tipoOperacion = null, $anio = null, $mes = null, Carbon $fechaCorte = null, $vencidoHastaPeriodo = false)
    {
        // Fuente de tipos/nombres/orden del carrusel: ampliar aquí al confirmar nuevos tipos.
        // El frontend renderiza cada fila recibida; no mantiene un catálogo paralelo.
        $tipos = $this->getTiposOperacionesDashboard();
        if ($tipoOperacion !== null) {
            $tipoValido = !is_bool($tipoOperacion) && is_scalar($tipoOperacion)
                ? filter_var($tipoOperacion, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                : false;
            if ($tipoValido === false || !array_key_exists($tipoValido, $tipos)) {
                throw new \InvalidArgumentException('El tipo de operación debe existir en el catálogo del dashboard, o ser null para todas.');
            }
            $tipos = [$tipoValido => $tipos[$tipoValido]];
        }

        $corte = $fechaCorte
            ? $fechaCorte->copy()->setTimezone('America/Mazatlan')
            : Carbon::now('America/Mazatlan');

        // Un dashboard sin filtros siempre inicia en el mes actual.
        if ($anio === null && $mes === null) {
            $anio = (int) $corte->format('Y');
            $mes = (int) $corte->format('n');
        } elseif ($anio === null) {
            throw new \InvalidArgumentException('Indica el año para consultar un mes específico.');
        }

        // Validar antes de construir fechas; la futura ruta también validará su request.
        $anioValido = !is_bool($anio) && is_scalar($anio)
            ? filter_var($anio, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1000, 'max_range' => (int) $corte->format('Y')]])
            : false;
        $mesValido = $mes === null ? null : (!is_bool($mes) && is_scalar($mes)
            ? filter_var($mes, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]])
            : false);
        if ($anioValido === false || $mesValido === false) {
            throw new \InvalidArgumentException('El año o mes solicitado no es válido.');
        }

        $inicio = Carbon::create($anioValido, $mesValido === null ? 1 : $mesValido, 1, 0, 0, 0, 'America/Mazatlan');
        if ($inicio->greaterThan($corte)) {
            throw new \InvalidArgumentException('No se permite consultar un periodo futuro.');
        }
        $finExclusivo = $mesValido === null
            ? $inicio->copy()->addYear()
            : $inicio->copy()->addMonth();

        // Para gráfica: solo vencimientos hasta el fin del mes elegido, con saldo actual.
        $limiteVencido=$vencidoHastaPeriodo && $finExclusivo->lessThan($corte->copy()->startOfDay())
            ? $finExclusivo->toDateString() : $corte->toDateString();

        // Base histórica: excluye canceladas y operaciones registradas después del corte.
        // No aplicar aquí el inicio del mes: el vencido incluye ventas antiguas.
        $operacionesVigentes = DB::table('operaciones as o')
            ->whereIn('o.empresa_operaciones_id', array_keys($tipos))
            ->whereIn('o.status', [1, 2])
            ->where('o.fecha_operacion', '<=', $corte);

        // Solo ventas se filtra por fecha_operacion; por cobrar usa fecha_programada.
        $operacionesPeriodo = (clone $operacionesVigentes)
            ->where('o.fecha_operacion', '>=', $inicio)
            ->where('o.fecha_operacion', '<', $finExclusivo);

        // Primero sumar aplicaciones por pago programado, no el total del recibo.
        // Criterio vigente de tus módulos: clasificar por pagos.movimientos_pagos_id.
        // 1 capital, 3 pronto pago, 4 descuento capital, 5 complemento cancelación.
        // 2 intereses queda fuera: no reduce el capital pendiente de este indicador.
        $aplicacionesCapital = DB::table('pagos_pagos_programados as ppp')
            ->join('pagos as p', 'p.id', '=', 'ppp.pagos_id')
            ->where('p.status', 1)
            ->whereIn('p.movimientos_pagos_id', [1, 3, 4, 5])
            ->select('ppp.pagos_programados_id')
            ->selectRaw('SUM(ppp.monto) AS capital_cubierto')
            ->groupBy('ppp.pagos_programados_id');

        // Una fila por operación evita multiplicar ventas al unir sus pagos.
        // Por cobrar usa fecha_programada del periodo; NULL y otros meses no califican.
        // Para vencido, hoy y fechas NULL no califican.
        $saldosPorOperacion = DB::table('pagos_programados as pp')
            ->joinSub((clone $operacionesVigentes)->select('o.id', 'o.empresa_operaciones_id'), 'op', function ($join) {
                $join->on('op.id', '=', 'pp.operaciones_id');
            })
            ->leftJoinSub($aplicacionesCapital, 'a', function ($join) {
                $join->on('a.pagos_programados_id', '=', 'pp.id');
            })
            ->where('pp.status', 1)
            ->select('pp.operaciones_id', 'op.empresa_operaciones_id')
            // Redondear por pago: un sobrepago no compensa otro programado.
            ->selectRaw('SUM(CASE
                WHEN pp.fecha_programada >= ? AND pp.fecha_programada < ?
                 AND ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2) > 0
                THEN ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2)
                ELSE 0 END) AS por_cobrar', [$inicio->toDateString(), $finExclusivo->toDateString()])
            ->selectRaw('SUM(CASE
                WHEN pp.fecha_programada < ?
                 AND ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2) > 0
                THEN ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2)
                ELSE 0 END) AS vencido', [$limiteVencido])
            ->groupBy('pp.operaciones_id', 'op.empresa_operaciones_id');

        // Dos agregados independientes: ventas por fecha de venta; saldos por fecha programada.
        // UNION ALL conserva tipos con vencido aunque no tengan ventas en el periodo.
        $ventasPeriodo = (clone $operacionesPeriodo)
            ->select('o.empresa_operaciones_id')
            ->selectRaw('COALESCE(SUM(o.total), 0) AS ventas')
            ->selectRaw('0 AS por_cobrar')
            ->selectRaw('0 AS vencido')
            ->groupBy('o.empresa_operaciones_id');

        $vencidoAcumulado = DB::query()
            ->fromSub($saldosPorOperacion, 'v')
            ->select('v.empresa_operaciones_id')
            ->selectRaw('0 AS ventas')
            ->selectRaw('COALESCE(SUM(v.por_cobrar), 0) AS por_cobrar')
            ->selectRaw('COALESCE(SUM(v.vencido), 0) AS vencido')
            ->groupBy('v.empresa_operaciones_id');

        // Una sentencia SQL y hasta cinco filas; sin cargar pagos ni operaciones en PHP.
        $totales = DB::query()
            ->fromSub($ventasPeriodo->unionAll($vencidoAcumulado), 'resumen')
            ->select('resumen.empresa_operaciones_id')
            ->selectRaw('SUM(resumen.ventas) AS ventas')
            ->selectRaw('SUM(resumen.por_cobrar) AS por_cobrar')
            ->selectRaw('SUM(resumen.vencido) AS vencido')
            ->groupBy('resumen.empresa_operaciones_id')
            ->get()
            ->keyBy('empresa_operaciones_id');

        $resumenes = [];
        foreach ($tipos as $id => $nombre) {
            $fila = $totales->get($id);
            $resumenes[] = [
                'id' => $id,
                'nombre' => $nombre,
                'ventas' => $fila ? round((float) $fila->ventas, 2) : 0,
                'porCobrar' => $fila ? round((float) $fila->por_cobrar, 2) : 0,
                'vencido' => $fila ? round((float) $fila->vencido, 2) : 0,
            ];
        }

        // Primera fila del carrusel: concentrado de los tipos del catálogo sin otra consulta.
        // Cuando se pide un tipo específico, conservar la respuesta individual.
        if ($tipoOperacion === null) {
            $general = [
                'id' => 'todas',
                'nombre' => 'Todas las operaciones',
                'ventas' => round(array_sum(array_column($resumenes, 'ventas')), 2),
                'porCobrar' => round(array_sum(array_column($resumenes, 'porCobrar')), 2),
                'vencido' => round(array_sum(array_column($resumenes, 'vencido')), 2),
            ];
            array_unshift($resumenes, $general);
        }

        return $resumenes;
    }


    /**
     * Detalle exclusivo de tipo 1, separado del resumen ligero del dashboard.
     * Ventas: fecha_operacion del mes. Por cobrar: fecha_programada del mes, de cualquier venta vigente.
     * Vencidos: todas las ventas hasta hoy.
     * $soloConteo evita cargar miles de filas al abrir el visor.
     * No usa operaciones.saldo ni total_pago: aplica monto del enlace al programado.
     */
    public function getReporteVentasCementerio($anio, $mes, Carbon $corte = null, $soloConteo = false, $vencidoHastaPeriodo = false)
    {
        return app(CementerioController::class)->getReporteVentasCementerio($anio, $mes, $corte, $soloConteo, $vencidoHastaPeriodo);
    }

    /** Tipo 2: cuotas por propiedad, sin duplicar consultas ni reglas de saldos. */
    public function getReporteCuotasMantenimiento($anio, $mes, Carbon $corte = null, $soloConteo = false, $vencidoHastaPeriodo = false)
    {
        return app(CementerioController::class)->getReporteCuotasMantenimiento($anio, $mes, $corte, $soloConteo, $vencidoHastaPeriodo);
    }

    /**
     * Motor financiero común. Cada controlador del módulo aporta su detalle específico.
     * El callback recibe las tres consultas y sus metadatos; no corre en modo soloConteo.
     * Cementerio aporta tipos 1/2; Funeraria aporta tipos 3/4/5. Aquí no se cargan sus catálogos.
     */
    public function getReporteVentasSaldosOperacionesDetallado($tipoOperacion, $anio, $mes, Carbon $corte = null, $soloConteo = false, callable $completarDetalle = null, $cuotaId = null, $vencidoHastaPeriodo = false)
    {
        if (!in_array($tipoOperacion, [1, 2, 3, 4, 5], true)) throw new \InvalidArgumentException('Reporte individual no disponible para ese tipo.');
        $camposNumero = [1 => 'o.ventas_terrenos_id', 2 => 'o.ventas_terrenos_id', 3 => 'o.servicios_funerarios_id', 4 => 'o.ventas_planes_id', 5 => 'o.ventas_generales_id'];
        $campoNumero = $camposNumero[$tipoOperacion];
        // Etiquetas de presentación por módulo; no cambian los criterios financieros.
        $titulos = [
            1 => 'Reporte de Ventas y Saldos en Cementerio',
            2 => 'Reporte de Cuotas de Mantenimiento',
            3 => 'Reporte de Ventas y Saldos de Servicios Funerarios',
            4 => 'Reporte de Ventas y Saldos de Planes Funerarios',
            5 => 'Reporte de Ventas en General y Saldos',
        ];
        $modulos = [1 => 'Ventas en cementerio', 2 => 'Cuotas de Mantenimiento', 3 => 'Servicios funerarios', 4 => 'Venta de planes funerarios', 5 => 'Ventas en general'];
        $etiquetasPeriodo = [1 => 'Ventas del mes', 2 => 'Cuotas del mes', 3 => 'Servicios contratados del mes', 4 => 'Planes vendidos del mes', 5 => 'Ventas del mes'];
        $corte = $corte ? $corte->copy()->setTimezone('America/Mazatlan') : Carbon::now('America/Mazatlan');
        if ($mes === null) throw new \InvalidArgumentException('Este reporte requiere un mes específico.');
        // Reutiliza también la validación de periodo y las reglas del resumen existente.
        $resumen = $this->getVentasSaldosOperacionesDashboard($tipoOperacion, $anio, $mes, $corte, $vencidoHastaPeriodo)[0];
        $inicio = Carbon::create((int) $anio, (int) $mes, 1, 0, 0, 0, 'America/Mazatlan');
        $fin = $inicio->copy()->addMonth();
        $limiteVencido=$vencidoHastaPeriodo && $fin->lessThan($corte->copy()->startOfDay())
            ? $fin->toDateString() : $corte->toDateString();
        $base = DB::table('operaciones as o')->where('o.empresa_operaciones_id', $tipoOperacion)
            ->whereIn('o.status', [1, 2])->where('o.fecha_operacion', '<=', $corte);
        // El filtro de cuota conserva los mismos criterios financieros del reporte tipo 2.
        if ($cuotaId !== null) {
            if ($tipoOperacion !== 2) throw new \InvalidArgumentException('El filtro de cuota solo aplica al tipo 2.');
            $base->whereRaw('COALESCE(o.cuotas_cementerio_id, 0) = ?', [(int) $cuotaId]);
        }
        $periodo = (clone $base)->where('o.fecha_operacion', '>=', $inicio)->where('o.fecha_operacion', '<', $fin);
        $aplicaciones = DB::table('pagos_pagos_programados as ppp')
            ->join('pagos as p', 'p.id', '=', 'ppp.pagos_id')->where('p.status', 1)
            ->whereIn('p.movimientos_pagos_id', [1, 3, 4, 5])
            ->select('ppp.pagos_programados_id')->selectRaw('SUM(ppp.monto) AS capital_cubierto')
            ->groupBy('ppp.pagos_programados_id');
        $pendiente = '(CASE WHEN ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2) > 0 THEN ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2) ELSE 0 END)';
        $programados = DB::table('pagos_programados as pp')
            ->leftJoinSub($aplicaciones, 'a', function ($j) { $j->on('a.pagos_programados_id', '=', 'pp.id'); })
            ->where('pp.status', 1)->select('pp.id', 'pp.operaciones_id', 'pp.fecha_programada', 'pp.referencia_pago', 'pp.num_pago', 'pp.monto_programado')
            ->selectRaw('COALESCE(a.capital_cubierto, 0) AS capital_cubierto')
            ->selectRaw($pendiente . ' AS pendiente');
        $saldos = DB::query()->fromSub(clone $programados, 'sp')->select('sp.operaciones_id')
            ->selectRaw('SUM(sp.pendiente) AS por_cobrar')
            ->selectRaw('SUM(CASE WHEN sp.fecha_programada < ? THEN sp.pendiente ELSE 0 END) AS vencido', [$limiteVencido])
            ->groupBy('sp.operaciones_id');
        $ventas = (clone $periodo)->leftJoinSub($saldos, 's', function ($j) { $j->on('s.operaciones_id', '=', 'o.id'); })
            ->select('o.id as operacion_id', $campoNumero . ' as numero_venta', 'o.fecha_operacion', 'o.clientes_id', 'o.total')
            ->selectRaw('COALESCE(s.por_cobrar, 0) AS por_cobrar, COALESCE(s.vencido, 0) AS vencido');
        $conPagos = function ($operaciones) use ($programados, $campoNumero) {
            return $operaciones->joinSub(clone $programados, 'pp', function ($j) { $j->on('pp.operaciones_id', '=', 'o.id'); })
                ->where('pp.pendiente', '>', 0)
                ->select('o.id as operacion_id', $campoNumero . ' as numero_venta', 'o.fecha_operacion', 'o.clientes_id',
                    'pp.id as pago_programado_id', 'pp.num_pago', 'pp.referencia_pago', 'pp.fecha_programada', 'pp.monto_programado', 'pp.capital_cubierto', 'pp.pendiente');
        };
        $cobrar = $conPagos(clone $base)
            ->where('pp.fecha_programada', '>=', $inicio->toDateString())
            ->where('pp.fecha_programada', '<', $fin->toDateString());
        $vencidos = $conPagos(clone $base)->where('pp.fecha_programada', '<', $limiteVencido);
        $consultas = ['ventas' => $ventas, 'por_cobrar' => $cobrar, 'vencidos' => $vencidos];
        $conteos = [];
        foreach ($consultas as $clave => $consulta) {
            $conteos[$clave] = DB::query()->fromSub(clone $consulta, 'filas')->count();
        }
        if ($cuotaId !== null) {
            // Agregar antes de enriquecer: no multiplicar ventas por sus pagos.
            $resumen['ventas'] = round((float) DB::query()->fromSub(clone $ventas, 'v')->sum('v.total'), 2);
            $resumen['porCobrar'] = round((float) DB::query()->fromSub(clone $cobrar, 'v')->sum('v.pendiente'), 2);
            $resumen['vencido'] = round((float) DB::query()->fromSub(clone $vencidos, 'v')->sum('v.pendiente'), 2);
        }
        $resultado = [
            'tipo_operacion' => $tipoOperacion,
            'titulo' => $titulos[$tipoOperacion],
            'modulo' => $modulos[$tipoOperacion],
            'etiqueta_periodo' => $etiquetasPeriodo[$tipoOperacion],
            'etiqueta_conteo' => mb_strtolower($etiquetasPeriodo[$tipoOperacion], 'UTF-8'),
            'periodo' => $inicio->locale('es')->translatedFormat('F \\d\\e Y'),
            'anio' => (int) $anio, 'mes' => (int) $mes,
            'fecha' => $corte->format('d') . '/' . ucfirst(rtrim($corte->locale('es')->translatedFormat('M'), '.')) . '/' . $corte->format('Y H:i'), 'corte' => $corte->toDateTimeString(),
            'resumen' => $resumen, 'conteos' => $conteos, 'totalRegistros' => array_sum($conteos),
        ];
        $resultado=$this->anotarAlcanceVencido($resultado,$vencidoHastaPeriodo,$limiteVencido);
        if ($soloConteo) return $resultado;
        if (!$completarDetalle) throw new \LogicException('Solicite el detalle mediante el controlador del módulo.');
        return $completarDetalle($consultas, $resultado);
    }

    /** Resolver el controlador del módulo sin duplicar transporte, caché o exportación. */
    private function obtenerReporteIndividual($tipoOperacion, $anio, $mes, Carbon $corte = null, $soloConteo = false, $vencidoHastaPeriodo = false)
    {
        if ($tipoOperacion === 3) {
            return app(FunerariaController::class)->getReporteServiciosFunerarios($anio, $mes, $corte, $soloConteo, $vencidoHastaPeriodo);
        }
        if ($tipoOperacion === 4) {
            return app(FunerariaController::class)->getReporteVentasPlanesFunerarios($anio, $mes, $corte, $soloConteo, $vencidoHastaPeriodo);
        }
        if ($tipoOperacion === 5) {
            return app(FunerariaController::class)->getReporteVentasGenerales($anio, $mes, $corte, $soloConteo, $vencidoHastaPeriodo);
        }
        if ($tipoOperacion === 1) return $this->getReporteVentasCementerio($anio, $mes, $corte, $soloConteo, $vencidoHastaPeriodo);
        if ($tipoOperacion === 2) return $this->getReporteCuotasMantenimiento($anio, $mes, $corte, $soloConteo, $vencidoHastaPeriodo);
        throw new \InvalidArgumentException('Reporte individual no disponible.');
    }


    /** Resumen ejecutivo: dos consultas agregadas, sin clientes, ventas o pagos individuales. */
    public function getResumenGeneralOperaciones($anio, $mes, Carbon $corte = null, $vencidoHastaPeriodo = false)
    {
        if ($mes === null) throw new \InvalidArgumentException('Indique un mes para el reporte general.');
        $corte = $corte ? $corte->copy()->setTimezone('America/Mazatlan') : Carbon::now('America/Mazatlan');
        $filas = $this->getVentasSaldosOperacionesDashboard(null, $anio, $mes, $corte, $vencidoHastaPeriodo);
        $total = array_shift($filas);
        $inicio = Carbon::create((int) $anio, (int) $mes, 1, 0, 0, 0, 'America/Mazatlan');
        $conteos = DB::table('operaciones')->whereIn('status', [1, 2])
            ->whereIn('empresa_operaciones_id', array_column($filas, 'id'))
            ->where('fecha_operacion', '>=', $inicio)->where('fecha_operacion', '<', $inicio->copy()->addMonth())
            ->where('fecha_operacion', '<=', $corte)
            ->select('empresa_operaciones_id')->selectRaw('COUNT(*) AS cantidad')
            ->groupBy('empresa_operaciones_id')->get()->keyBy('empresa_operaciones_id');
        foreach ($filas as &$fila) $fila['cantidad'] = isset($conteos[$fila['id']]) ? (int) $conteos[$fila['id']]->cantidad : 0;
        unset($fila);
        $total['cantidad'] = array_sum(array_column($filas, 'cantidad'));
        $datos = [
            'tipo_operacion' => 'todas', 'titulo' => 'Resumen General de Ventas y Saldos',
            'modulo' => 'Todas las operaciones', 'etiqueta_periodo' => 'Ventas del mes',
            'anio' => (int) $anio, 'mes' => (int) $mes,
            'periodo' => $inicio->locale('es')->translatedFormat('F \\d\\e Y'),
            'fecha' => $corte->format('d').'/'.ucfirst(rtrim($corte->locale('es')->translatedFormat('M'), '.')).'/'.$corte->format('Y H:i'),
            'corte' => $corte->toDateTimeString(), 'resumen' => $total, 'tipos' => $filas,
        ];
        $fin=$inicio->copy()->addMonth();
        $limite=$vencidoHastaPeriodo && $fin->lessThan($corte->copy()->startOfDay())?$fin->toDateString():$corte->toDateString();
        return $this->anotarAlcanceVencido($datos,$vencidoHastaPeriodo,$limite);
    }

    /** El reporte general abre solo cinco filas. No invoca reportes detallados. */
    public function previewReporteGeneralOperaciones(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $request->validate(['anio' => 'required|integer|min:1000', 'mes' => 'required|integer|between:1,12', 'vencido_hasta_periodo'=>'sometimes|boolean']);
        $corte = Carbon::now('America/Mazatlan');
        try {
            $datos = DB::transaction(function () use ($request, $corte) {
                return $this->getResumenGeneralOperaciones($request->anio, $request->mes, $corte, (bool)$request->input('vencido_hasta_periodo',false));
            });
        } catch (\InvalidArgumentException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages(['periodo' => $e->getMessage()]);
        }
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = $datos['titulo'].' '.$corte->format('Y-m-d H-i-s').'.xlsx';
        // Reutiliza propiedad/vigencia del token y el transporte de correo del visor.
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_'.$token, [
            'usuario_id' => (int) $usuario->id, 'modo' => 'general', 'datos' => $datos, 'nombre' => $nombre,
        ], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => true,
            'totalRegistros' => count($datos['tipos']), 'limiteVisualizacion' => 3500,
            'hojas' => [['nombre' => 'Resumen general', 'html' => $this->htmlReporteCementerio($datos)]],
            'reportesIndividuales' => array_map(function ($f) { return ['id' => $f['id'], 'nombre' => $f['nombre']]; }, $datos['tipos']),
            'excelCompletoDisponible' => true,
        ])->header('Cache-Control', 'no-store, private');
    }

    /**
     * Excel completo solo bajo solicitud. No llena el caché ni la vista con el historial.
     * Consulta datos actuales del mismo periodo en una transacción de lectura.
     * El usuario ve explícitamente que este archivo se actualiza al generarse.
     */
    public function descargarReporteGeneralCompleto(Request $request, $token)
    {
        $reporte = $this->datosReporteCementerio($request, $token);
        if (!empty($reporte['datos']['resumen_cuotas'])) {
            $datos = $this->datosDetalleCuotas($reporte['datos']);
            $respuesta = \Maatwebsite\Excel\Facades\Excel::download($this->exportadorReporteVentas($datos), 'Cuotas de Mantenimiento - Detalle completo.xlsx');
            $respuesta->headers->set('Cache-Control', 'no-store, private');
            return $respuesta;
        }
        abort_unless(($reporte['datos']['tipo_operacion'] ?? null) === 'todas', 422, 'Este archivo corresponde al reporte general.');
        $datos = $this->datosDetalleGeneralVentas($reporte['datos']);
        $nombre = 'Detalle General de Ventas y Saldos '.now('America/Mazatlan')->format('Y-m-d H-i-s').'.xlsx';
        $respuesta = \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ResumenGeneralOperacionesExport($datos, true), $nombre);
        $respuesta->headers->set('Cache-Control', 'no-store, private');
        return $respuesta;
    }

    /** Mismo detalle y corte para descargar o adjuntar el Excel completo. */
    private function datosDetalleGeneralVentas(array $anterior)
    {
        return DB::transaction(function () use ($anterior) {
            $corte = Carbon::now('America/Mazatlan');
            $actual = $this->getResumenGeneralOperaciones($anterior['anio'], $anterior['mes'], $corte, !empty($anterior['vencido_hasta_periodo']));
            $actual['empresa'] = $anterior['empresa'];
            $actual['detalles'] = [];
            foreach ($actual['tipos'] as $tipo) {
                // Cada módulo sigue siendo dueño de su información específica.
                $actual['detalles'][] = $this->obtenerReporteIndividual((int) $tipo['id'], $anterior['anio'], $anterior['mes'], $corte, false, !empty($anterior['vencido_hasta_periodo']));
            }
            return $actual;
        });
    }

    /** El mismo selector se usa para descarga y correo: el general exporta solo su resumen. */
    private function exportadorReporteVentas(array $datos)
    {
        if (($datos['tipo_operacion'] ?? null) === 'resumen_dashboard') return new \App\Exports\ResumenDashboardExport($datos);
        if (($datos['tipo_operacion'] ?? null) === 'clientes_pagos_vencidos') return new \App\Exports\ClientesPagosVencidosExport($datos);
        if (($datos['tipo_operacion'] ?? null) === 'seguimientos_vencidos_historico') return new \App\Exports\SeguimientosVencidosExport($datos);
        if (($datos['tipo_operacion'] ?? null) === 'seguimientos_programados_proximos') return new \App\Exports\SeguimientosProgramadosExport($datos);
        if (($datos['tipo_operacion'] ?? null) === 'seguimientos_realizados_mes') return new \App\Exports\SeguimientosRealizadosExport($datos);
        if (($datos['tipo_operacion'] ?? null) === 'solicitudes_sin_contrato') return new \App\Exports\SolicitudesSinContratoExport($datos);
        if (in_array($datos['tipo_operacion'] ?? null, ['vence_hoy', 'proximos_siete_dias'], true)) return new \App\Exports\PagosVenceHoyExport($datos);
        if (!empty($datos['resumen_cuotas'])) return new \App\Exports\CuotasMantenimientoExport($datos, !empty($datos['detalles_cuotas']));
        return ($datos['tipo_operacion'] ?? null) === 'todas'
            ? new \App\Exports\ResumenGeneralOperacionesExport($datos)
            : new \App\Exports\VentasCementerioExport($datos);
    }

    /** Entrada del visor: instantánea privada de 15 minutos; no genera archivos al consultar. */
    public function previewVentasCementerio(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $request->validate(['anio' => 'required|integer|min:1000', 'mes' => 'required|integer|between:1,12', 'vencido_hasta_periodo'=>'sometimes|boolean', 'empresa_operaciones_id' => 'sometimes|required|integer|in:1,2,3,4,5']);
        $tipoOperacion = (int) $request->input('empresa_operaciones_id', 1);
        if ($tipoOperacion === 2) return $this->previewCuotasMantenimiento($request, $usuario);
        $corte = Carbon::now('America/Mazatlan');
        $limite = 3500; // Suma de filas de los tres apartados; no limita la descarga.
        try {
            $datos = DB::transaction(function () use ($request, $corte, $tipoOperacion, $limite) {
                $datos = $this->obtenerReporteIndividual($tipoOperacion, $request->anio, $request->mes, $corte, true, (bool)$request->input('vencido_hasta_periodo',false));
                if ($datos['totalRegistros'] <= $limite) {
                    $datos = $this->obtenerReporteIndividual($tipoOperacion, $request->anio, $request->mes, $corte, false, (bool)$request->input('vencido_hasta_periodo',false));
                }
                return $datos;
            });
        } catch (\InvalidArgumentException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages(['periodo' => $e->getMessage()]);
        }
        $visualizable = isset($datos['ventas']) && $datos['totalRegistros'] <= $limite;
        // Empresa también se conserva en la instantánea: PDF, Excel y HTML muestran lo mismo.
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $html = $visualizable ? $this->htmlReporteCementerio($datos) : null;
        if (!$visualizable) unset($datos['ventas'], $datos['por_cobrar'], $datos['vencidos']);
        $token = bin2hex(random_bytes(32));
        $vence = $corte->copy()->addMinutes(15);
        $nombre = $datos['titulo'] . ' ' . $corte->format('Y-m-d H-i-s') . '.xlsx';
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_' . $token, [
            'usuario_id' => (int) $usuario->id, 'datos' => $datos, 'nombre' => $nombre,
            'modo' => $visualizable ? 'instantanea' : 'filtro',
        ], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => $limite, 'totalRegistros' => $datos['totalRegistros'],
            'mensaje' => $visualizable ? null : 'No se puede visualizar este reporte por su tamaño. Descargue el Excel completo. Los datos se actualizarán al generar el archivo.',
            'hojas' => $visualizable ? [['nombre' => $datos['modulo'], 'html' => $html]] : [],
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Entrada tipo 2: primero resumen por cuota; el detalle se solicita al seleccionarla. */
    private function previewCuotasMantenimiento(Request $request, $usuario)
    {
        $request->validate(['cuota_id' => 'nullable|integer|min:0']);
        $corte = Carbon::now('America/Mazatlan');
        $cuotaId = $request->filled('cuota_id') ? (int) $request->input('cuota_id') : null;
        try {
            $datos = DB::transaction(function () use ($request, $corte, $cuotaId) {
                $modulo = app(CementerioController::class);
                if ($cuotaId === null) return $modulo->getResumenCuotasMantenimiento($request->anio, $request->mes, $corte, (bool)$request->input('vencido_hasta_periodo',false));
                $datos = $modulo->getDetalleCuotaMantenimiento($request->anio, $request->mes, $cuotaId, $corte, true, (bool)$request->input('vencido_hasta_periodo',false));
                abort_unless($datos['totalRegistros'] > 0, 404, 'La cuota no tiene información para este reporte.');
                return $datos['totalRegistros'] <= 3500 ? $modulo->getDetalleCuotaMantenimiento($request->anio, $request->mes, $cuotaId, $corte, false, (bool)$request->input('vencido_hasta_periodo',false)) : $datos;
            });
        } catch (\InvalidArgumentException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages(['periodo' => $e->getMessage()]);
        }
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $datos['empresa'] = $empresa ? array_intersect_key($empresa->toArray(), array_flip([
            'nombre_comercial', 'razon_social', 'rfc', 'telefono', 'email', 'calle', 'num_ext', 'colonia', 'ciudad', 'estado'
        ])) : [];
        $resumen = !empty($datos['resumen_cuotas']);
        $visualizable = $resumen ? count($datos['cuotas']) <= 3500 : isset($datos['ventas']);
        $token = bin2hex(random_bytes(32)); $vence = $corte->copy()->addMinutes(15);
        $nombre = 'Cuotas de Mantenimiento '.($resumen ? 'Resumen' : 'Detalle').' '.$corte->format('Y-m-d H-i-s').'.xlsx';
        \Illuminate\Support\Facades\Cache::put('cementerio_reporte_'.$token, [
            'usuario_id' => (int) $usuario->id, 'datos' => $datos, 'nombre' => $nombre,
            'modo' => ($resumen || $visualizable) ? 'instantanea' : 'filtro',
        ], $vence);
        return response()->json([
            'titulo' => $datos['titulo'], 'nombreArchivo' => $nombre, 'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(), 'vistaPreviaDisponible' => $visualizable,
            'limiteVisualizacion' => 3500, 'totalRegistros' => $resumen ? count($datos['cuotas']) : $datos['totalRegistros'],
            'mensaje' => $visualizable ? null : 'No se puede visualizar este reporte por su tamaño. Puede descargarlo en Excel.',
            'hojas' => $visualizable ? [['nombre' => $resumen ? 'Resumen por cuota' : 'Detalle de cuota', 'html' => $this->htmlReporteCementerio($datos)]] : [],
            'reportesRelacionados' => $resumen ? array_map(function ($f) { return ['id' => 'cuota-'.$f['id'], 'nombre' => $f['descripcion']]; }, $datos['cuotas']) : [],
            'excelCompletoDisponible' => $resumen,
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Descarga/correo completo: un único detalle enriquecido y separado por cuota en memoria. */
    private function datosDetalleCuotas(array $anterior)
    {
        return DB::transaction(function () use ($anterior) {
            $corte = Carbon::now('America/Mazatlan');
            $modulo = app(CementerioController::class);
            $datos = $modulo->getResumenCuotasMantenimiento($anterior['anio'], $anterior['mes'], $corte, !empty($anterior['vencido_hasta_periodo']));
            $datos['empresa'] = $anterior['empresa'];
            $detalle = $modulo->getReporteCuotasMantenimiento($anterior['anio'], $anterior['mes'], $corte, false, !empty($anterior['vencido_hasta_periodo']));
            // Agrupar en una pasada: evita recorrer todo el historial por cada cuota.
            $agrupados = [];
            foreach (['ventas', 'por_cobrar', 'vencidos'] as $clave) {
                foreach ($detalle[$clave] as $fila) $agrupados[(int) ($fila['cuota_id'] ?? 0)][$clave][] = $fila;
            }
            $datos['detalles_cuotas'] = [];
            foreach ($datos['cuotas'] as $cuota) {
                $d = $detalle; $d['resumen'] = $cuota; $d['cuota_id'] = $cuota['id'];
                $d['titulo'] = 'Cuotas de Mantenimiento · '.$cuota['descripcion'];
                foreach (['ventas', 'por_cobrar', 'vencidos'] as $clave) {
                    $d[$clave] = $agrupados[$cuota['id']][$clave] ?? [];
                }
                $datos['detalles_cuotas'][] = $d;
            }
            return $datos;
        });
    }

    private function usuarioReporteCementerio(Request $request)
    {
        $usuario = $request->user('api');
        abort_unless($usuario && in_array((int) $usuario->roles_id, [1, 4], true), 403);
        return $usuario;
    }

    /** Verifica el propietario incluso si conoce un token válido de otro usuario. */
    private function datosReporteCementerio(Request $request, $token)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        abort_unless(is_string($token) && preg_match('/^[a-f0-9]{64}$/D', $token), 404);
        $reporte = \Illuminate\Support\Facades\Cache::get('cementerio_reporte_' . $token);
        abort_unless($reporte, 410, 'La consulta venció. Actualice el reporte.');
        abort_unless((int) $reporte['usuario_id'] === (int) $usuario->id, 403);
        if ($reporte['modo'] === 'seguimientos_vencidos_filtro') {
            $anterior = $reporte['datos'];
            $reporte['datos'] = DB::transaction(function () use ($anterior) {
                return app(SeguimientosController::class)->getReporteSeguimientosVencidosHistorico(Carbon::parse($anterior['corte'], 'America/Mazatlan'));
            });
            $reporte['datos']['empresa'] = $anterior['empresa'];
        }
        if ($reporte['modo'] === 'seguimientos_programados_filtro') {
            $anterior = $reporte['datos'];
            $reporte['datos'] = DB::transaction(function () use ($anterior) {
                return app(SeguimientosController::class)->getReporteProgramadosProximosSieteDias(Carbon::parse($anterior['corte'], 'America/Mazatlan'));
            });
            $reporte['datos']['empresa'] = $anterior['empresa'];
        }
        if ($reporte['modo'] === 'seguimientos_realizados_filtro') {
            $anterior = $reporte['datos'];
            $reporte['datos'] = DB::transaction(function () use ($anterior) {
                return app(SeguimientosController::class)->getReporteSeguimientosRealizadosMes(Carbon::parse($anterior['corte'], 'America/Mazatlan'));
            });
            $reporte['datos']['empresa'] = $anterior['empresa'];
        }
        if ($reporte['modo'] === 'solicitudes_sin_contrato_filtro') {
            $anterior = $reporte['datos'];
            $reporte['datos'] = DB::transaction(function () use ($anterior) {
                return app(FunerariaController::class)->getReporteSolicitudesSinContrato(Carbon::parse($anterior['corte'], 'America/Mazatlan'));
            });
            $reporte['datos']['empresa'] = $anterior['empresa'];
        }
        if (in_array($reporte['modo'], ['vence_hoy_filtro', 'proximos_pagos_filtro', 'clientes_vencidos_filtro'], true)) {
            $anterior = $reporte['datos'];
            $reporte['datos'] = DB::transaction(function () use ($anterior) {
                return $this->getReportePagosPorVencer((int) ($anterior['dias'] ?? 1), Carbon::parse($anterior['corte'], 'America/Mazatlan'));
            });
            $reporte['datos']['empresa'] = $anterior['empresa'];
        }
        if ($reporte['modo'] === 'filtro') {
            $anterior = $reporte['datos'];
            $reporte['datos'] = DB::transaction(function () use ($anterior) {
                if (array_key_exists('cuota_id', $anterior)) {
                    return app(CementerioController::class)->getDetalleCuotaMantenimiento($anterior['anio'], $anterior['mes'], (int) $anterior['cuota_id'], null, false, !empty($anterior['vencido_hasta_periodo']));
                }
                return $this->obtenerReporteIndividual((int) ($anterior['tipo_operacion'] ?? 1), $anterior['anio'], $anterior['mes'], null, false, !empty($anterior['vencido_hasta_periodo']));
            });
            $reporte['datos']['empresa'] = $anterior['empresa'];
        }
        return $reporte;
    }

    private function htmlReporteCementerio(array $datos, $modoPdf = false)
    {
        if (($datos['tipo_operacion'] ?? null) === 'resumen_dashboard') return view('reportes.temporales_visor_excel.dashboard.resumen',['datos'=>$datos])->render();
        if (($datos['tipo_operacion'] ?? null) === 'clientes_pagos_vencidos') {
            return view('reportes.temporales_visor_excel.pagos.clientes_vencidos', ['datos'=>$datos,'modoPdf'=>$modoPdf])->render();
        }
        if (($datos['tipo_operacion'] ?? null) === 'seguimientos_vencidos_historico') {
            return view('reportes.temporales_visor_excel.seguimientos.vencidos_historico', ['datos'=>$datos,'modoPdf'=>$modoPdf])->render();
        }
        if (($datos['tipo_operacion'] ?? null) === 'seguimientos_programados_proximos') {
            return view('reportes.temporales_visor_excel.seguimientos.programados_proximos', ['datos'=>$datos,'modoPdf'=>$modoPdf])->render();
        }
        if (($datos['tipo_operacion'] ?? null) === 'seguimientos_realizados_mes') {
            return view('reportes.temporales_visor_excel.seguimientos.realizados_mes', ['datos' => $datos, 'modoPdf' => $modoPdf])->render();
        }
        if (($datos['tipo_operacion'] ?? null) === 'solicitudes_sin_contrato') {
            return view('reportes.temporales_visor_excel.funeraria.solicitudes_sin_contrato', ['datos' => $datos, 'modoPdf' => $modoPdf])->render();
        }
        if (in_array($datos['tipo_operacion'] ?? null, ['vence_hoy', 'proximos_siete_dias'], true)) {
            return view('reportes.temporales_visor_excel.pagos.vence_hoy', ['datos' => $datos, 'modoPdf' => $modoPdf])->render();
        }
        $esGeneral = ($datos['tipo_operacion'] ?? null) === 'todas';
        $ruta = public_path(env('LOGOJPG'));
        $mime = is_file($ruta) ? (new \finfo(FILEINFO_MIME_TYPE))->file($ruta) : null;
        $logo = in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)
            ? 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($ruta)) : null;
        return view(!empty($datos['resumen_cuotas']) ? 'reportes.temporales_visor_excel.cuotas_mantenimiento.resumen' : ($esGeneral ? 'reportes.temporales_visor_excel.general_operaciones.resumen' : 'reportes.temporales_visor_excel.ventas_cementerio.reporte'), [
            'datos' => $datos, 'secciones' => ($esGeneral || !empty($datos['resumen_cuotas'])) ? [] : \App\Exports\VentasCementerioExport::secciones($datos),
            'logo' => $logo, 'modoPdf' => $modoPdf,
        ])->render();
    }

    private function pdfReporteCementerio(array $datos)
    {
        $footer = view('reportes.temporales_visor_excel.compartidos.footer_pdf', [
            'fechaImpresion' => Carbon::now('America/Mazatlan')->format('d') . '/' . ucfirst(rtrim(Carbon::now('America/Mazatlan')->locale('es')->translatedFormat('M'), '.')) . '/' . Carbon::now('America/Mazatlan')->format('Y H:i'),
            'dominio' => parse_url(config('app.url'), PHP_URL_HOST) ?: config('app.url'),
        ])->render();
        return \PDF::loadHTML($this->htmlReporteCementerio($datos, true))->setOptions([
            'orientation' => ($datos['tipo_operacion']??null)==='resumen_dashboard'?'portrait':'landscape', 'page-size' => ($datos['tipo_operacion']??null)==='resumen_dashboard'?'Letter':'A4', 'encoding' => 'UTF-8',
            'margin-top' => 10, 'margin-bottom' => 12, 'margin-left' => 10, 'margin-right' => 10,
            'print-media-type' => true, 'disable-javascript' => false,
            'enable-local-file-access' => false, 'disable-local-file-access' => true,
            'footer-html' => $footer, 'footer-spacing' => 3,
        ]);
    }

    public function descargarVentasCementerioExcel(Request $request, $token)
    {
        $reporte = $this->datosReporteCementerio($request, $token);
        abort_if(($reporte['datos']['tipo_operacion']??null)==='resumen_dashboard',422,'El resumen se ofrece únicamente en PDF.');
        $respuesta = \Maatwebsite\Excel\Facades\Excel::download($this->exportadorReporteVentas($reporte['datos']), $reporte['nombre']);
        $respuesta->headers->set('Cache-Control', 'no-store, private');
        return $respuesta;
    }

    public function descargarVentasCementerioPdf(Request $request, $token)
    {
        $reporte = $this->datosReporteCementerio($request, $token);
        $respuesta = $this->pdfReporteCementerio($reporte['datos'])->download(preg_replace('/\.xlsx$/i', '.pdf', $reporte['nombre']));
        $respuesta->headers->set('Cache-Control', 'no-store, private');
        return $respuesta;
    }

    /** Correo con las mismas validaciones, confirmación y plantilla del visor. */
    public function enviarVentasCementerioCorreo(Request $request, $token)
    {
        $request->validate([
            'destinatario' => 'required_without:destinatarios|nullable|string|max:75',
            'email_address' => 'required_without:destinatarios|nullable|email|max:75',
            'destinatarios' => 'sometimes|required|array|min:1|max:20',
            'destinatarios.*.nombre' => 'nullable|string|max:75',
            'destinatarios.*.email' => 'required|email|max:75',
            'formato' => 'sometimes|required|in:excel,pdf',
            'completo' => 'sometimes|boolean',
            'mensaje' => 'nullable|string|max:30000', // HTML acotado; texto visible hasta 5000 caracteres.
            'asunto' => ['nullable', 'string', 'max:150', 'not_regex:/[\r\n]/'],
        ]);
        $destinatarios = $request->input('destinatarios', [[
            'nombre' => $request->input('destinatario'), 'email' => $request->input('email_address'),
        ]]);
        $vistos = [];
        foreach ($destinatarios as &$persona) {
            $persona['email'] = trim($persona['email']);
            $persona['nombre'] = trim($persona['nombre'] ?? '');
            $clave = strtolower($persona['email']);
            if (isset($vistos[$clave])) throw \Illuminate\Validation\ValidationException::withMessages(['destinatarios' => 'Hay correos duplicados en la lista.']);
            $vistos[$clave] = true;
        }
        unset($persona);
        $textoMensaje = html_entity_decode(strip_tags($request->input('mensaje', '') ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (mb_strlen(trim($textoMensaje), 'UTF-8') > 5000) {
            throw \Illuminate\Validation\ValidationException::withMessages(['mensaje' => 'El mensaje no debe superar 5000 caracteres.']);
        }
        $reporte = $this->datosReporteCementerio($request, $token);
        abort_if(($reporte['datos']['tipo_operacion']??null)==='resumen_dashboard' && $request->input('formato','excel')!=='pdf',422,'Seleccione formato PDF.');
        $nombre = $reporte['nombre'];
        $completo = $request->boolean('completo');
        if ($completo) {
            abort_unless(($reporte['datos']['tipo_operacion'] ?? null) === 'todas' || !empty($reporte['datos']['resumen_cuotas']), 422, 'Este reporte no ofrece un adjunto detallado completo adicional.');
            abort_unless($request->input('formato', 'excel') === 'excel', 422, 'El detalle completo se envía en formato Excel.');
            $reporte['datos'] = !empty($reporte['datos']['resumen_cuotas']) ? $this->datosDetalleCuotas($reporte['datos']) : $this->datosDetalleGeneralVentas($reporte['datos']);
            $nombre = (!empty($reporte['datos']['resumen_cuotas']) ? 'Cuotas de Mantenimiento - Detalle completo ' : 'Detalle General de Ventas y Saldos ').now('America/Mazatlan')->format('Y-m-d H-i-s').'.xlsx';
        }

        if ($request->input('formato', 'excel') === 'pdf') {
            $contenido = $this->pdfReporteCementerio($reporte['datos'])->output();
            $nombre = preg_replace('/\.xlsx$/i', '.pdf', $nombre);
        } else {
            $contenido = \Maatwebsite\Excel\Facades\Excel::raw(($completo && ($reporte['datos']['tipo_operacion'] ?? null) === 'todas' ? new \App\Exports\ResumenGeneralOperacionesExport($reporte['datos'], true) : $this->exportadorReporteVentas($reporte['datos'])), \Maatwebsite\Excel\Excel::XLSX);
        }
        // Texto del usuario y adjunto se entregan al envío compartido de reportes.
        return app(EmailController::class)->reporte_email([
            'email_address' => $destinatarios[0]['email'],
            'destinatario' => $destinatarios[0]['nombre'],
            'destinatarios' => $destinatarios,
            'asunto' => $request->input('asunto') ?: $reporte['datos']['titulo'],
            'mensaje' => $request->input('mensaje', ''),
        ], $contenido, $nombre, $request->input('formato', 'excel') === 'pdf'
            ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }


    /**
     * Mantenimiento interno de saldo/status; NO llamar desde buildDashboard.
     * Por defecto solo simula. $aplicar=true confirma todos los cambios en transacción.
     * $operacionesStatus0 contiene operaciones.id, nunca IDs de ventas/servicios.
     * Conserva canceladas; las excepciones manuales cambian solo status, no saldo.
     * Excluye falta de programación activa, montos inválidos y programación
     * diferente al total vendido (> $0.01); informa estos casos para revisión.
     * No inventa pagos ni corrige la programación. El saldo nunca será negativo.
     * Ejecutar sin cobros/ediciones concurrentes; no representa un cierre histórico.
     * La autorización corresponde al comando/controlador interno que lo invoque.
     * @param array $operacionesStatus0 Lista opcional de IDs para cancelar.
     * @param bool $aplicar false=simulación; true=guardar con commit automático.
     * @param array $tiposOperacion 1..5; vacío = todos.
     * @return array Conteos, incidencias (máximo 50), IDs manuales no encontrados.
     */
    public function mantenerSaldosStatusOperaciones(array $operacionesStatus0 = [], $aplicar = false, array $tiposOperacion = [])
    {
        if (!is_bool($aplicar)) {
            throw new \InvalidArgumentException('Aplicar debe ser true o false.');
        }
        $ids = [];
        foreach ($operacionesStatus0 as $id) {
            $valido = !is_bool($id) && is_scalar($id)
                ? filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                : false;
            if ($valido === false) {
                throw new \InvalidArgumentException('Cada operación debe tener un ID entero positivo.');
            }
            $ids[$valido] = $valido;
        }
        $ids = array_values($ids);
        $tipos = [];
        foreach ($tiposOperacion as $tipo) {
            $valido = !is_bool($tipo) && is_scalar($tipo)
                ? filter_var($tipo, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]])
                : false;
            if ($valido === false) {
                throw new \InvalidArgumentException('Cada tipo de operación debe ser de 1 a 5.');
            }
            $tipos[$valido] = $valido;
        }
        $tipos = $tipos ? array_values($tipos) : [1, 2, 3, 4, 5];

        // La tabla modificada debe permitir rollback; no asumirlo por usar una transacción.
        if ($aplicar && DB::connection()->getDriverName() === 'mysql') {
            $tabla = DB::selectOne('SELECT ENGINE AS motor FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', ['operaciones']);
            if (!$tabla || strcasecmp($tabla->motor, 'InnoDB') !== 0) {
                throw new \RuntimeException('El mantenimiento requiere operaciones con motor InnoDB.');
            }
        }

        // DB::transaction confirma al terminar y revierte todo ante una excepción.
        return DB::transaction(function () use ($ids, $aplicar, $tipos) {
            $resultado = [
                'modo' => $aplicar ? 'aplicado' : 'simulacion',
                'tiposOperacion' => $tipos,
                'generadoEn' => Carbon::now('America/Mazatlan')->toIso8601String(),
                'cancelacionesManualesPrevistas' => 0,
                'cancelacionesManualesAplicadas' => 0,
                'idsNoEncontrados' => [],
                'operacionesRevisadas' => 0,
                'operacionesConCambios' => 0,
                'operacionesActualizadas' => 0,
                'omitidas' => [
                    'sinProgramadosActivos' => 0,
                    'montosInvalidos' => 0,
                    'programacionDifiereDeVenta' => 0,
                ],
                'incidencias' => [],
            ];

            if ($ids) {
                $consultaManuales = DB::table('operaciones')->whereIn('id', $ids);
                if ($aplicar) {
                    $consultaManuales->lockForUpdate();
                }
                $manuales = $consultaManuales->get(['id', 'status', 'empresa_operaciones_id']);
                foreach ($manuales as $manual) {
                    if (!in_array((int) $manual->empresa_operaciones_id, $tipos, true)) {
                        throw new \InvalidArgumentException('Una operación a cancelar no pertenece a los tipos seleccionados: ' . $manual->id);
                    }
                }
                $resultado['idsNoEncontrados'] = array_values(array_diff($ids, $manuales->pluck('id')->all()));
                $resultado['cancelacionesManualesPrevistas'] = $manuales->filter(function ($fila) {
                    return $fila->status === null || (int) $fila->status !== 0;
                })->count();
                if ($aplicar) {
                    $resultado['cancelacionesManualesAplicadas'] = DB::table('operaciones')
                        ->whereIn('id', $ids)
                        ->where(function ($q) {
                            $q->whereNull('status')->orWhere('status', '<>', 0);
                        })
                        ->update(['status' => 0]);
                }
            }

            $aplicaciones = DB::table('pagos_pagos_programados as ppp')
                ->join('pagos as p', 'p.id', '=', 'ppp.pagos_id')
                ->where('p.status', 1)
                ->whereIn('p.movimientos_pagos_id', [1, 3, 4, 5])
                ->select('ppp.pagos_programados_id')
                ->selectRaw('SUM(ppp.monto) AS cubierto')
                ->groupBy('ppp.pagos_programados_id');

            // Futuro y fecha NULL también forman parte del saldo total pendiente.
            $programados = DB::table('pagos_programados as pp')
                ->leftJoinSub($aplicaciones, 'a', function ($join) {
                    $join->on('a.pagos_programados_id', '=', 'pp.id');
                })
                ->where('pp.status', 1)
                ->select('pp.operaciones_id')
                ->selectRaw('COUNT(*) AS cantidad, SUM(pp.monto_programado) AS total_programado')
                ->selectRaw('SUM(CASE WHEN pp.monto_programado IS NULL OR pp.monto_programado < 0
                    THEN 1 ELSE 0 END) AS invalidos')
                ->selectRaw('SUM(CASE
                    WHEN ROUND(pp.monto_programado - COALESCE(a.cubierto, 0), 2) > 0
                    THEN ROUND(pp.monto_programado - COALESCE(a.cubierto, 0), 2)
                    ELSE 0 END) AS saldo_calculado')
                ->groupBy('pp.operaciones_id');

            $operaciones = DB::table('operaciones as o')
                ->leftJoinSub($programados, 'r', function ($join) {
                    $join->on('r.operaciones_id', '=', 'o.id');
                })
                ->where('o.status', '<>', 0)
                ->whereIn('o.empresa_operaciones_id', $tipos)
                ->select([
                    'o.id as operacion_id',
                    'o.total',
                    'o.saldo',
                    'o.status',
                    'r.cantidad',
                    'r.total_programado',
                    'r.invalidos',
                    'r.saldo_calculado'
                ]);
            if ($ids) {
                // También excluir las excepciones en simulación, sin modificarlas.
                $operaciones->whereNotIn('o.id', $ids);
            }
            if ($aplicar) {
                $operaciones->lockForUpdate();
            }

            // Leer/actualizar por bloques; no cargar todo el historial en memoria.
            $operaciones->chunkById(150, function ($filas) use (&$resultado, $aplicar) {
                $cambios = [];
                foreach ($filas as $fila) {
                    $resultado['operacionesRevisadas']++;
                    $motivo = null;
                    if (!$fila->cantidad) {
                        $motivo = 'sinProgramadosActivos';
                    } elseif ((int) $fila->invalidos > 0) {
                        $motivo = 'montosInvalidos';
                    } elseif (
                        $fila->total === null ||
                        abs((float) $fila->total_programado - (float) $fila->total) > 0.01000001
                    ) {
                        $motivo = 'programacionDifiereDeVenta';
                    }
                    if ($motivo !== null) {
                        $resultado['omitidas'][$motivo]++;
                        if (count($resultado['incidencias']) < 50) {
                            $resultado['incidencias'][] = ['operacion_id' => $fila->operacion_id, 'motivo' => $motivo];
                        }
                        continue;
                    }
                    $saldo = max(round((float) $fila->saldo_calculado, 2), 0);
                    $status = $saldo > 0 ? 1 : 2;
                    if ($fila->saldo === null || round((float) $fila->saldo, 2) !== $saldo || (int) $fila->status !== $status) {
                        $resultado['operacionesConCambios']++;
                        $cambios[] = ['id' => $fila->operacion_id, 'saldo' => $saldo, 'status' => $status];
                    }
                }
                if (!$aplicar || !$cambios) {
                    return;
                }

                // Una actualización por bloque, con parámetros enlazados; sin SQL interpolado de IDs.
                $casosSaldo = [];
                $casosStatus = [];
                $bindingsSaldo = [];
                $bindingsStatus = [];
                $idsBloque = [];
                foreach ($cambios as $cambio) {
                    $casosSaldo[] = 'WHEN ? THEN ?';
                    $bindingsSaldo[] = $cambio['id'];
                    $bindingsSaldo[] = $cambio['saldo'];
                    $casosStatus[] = 'WHEN ? THEN ?';
                    $bindingsStatus[] = $cambio['id'];
                    $bindingsStatus[] = $cambio['status'];
                    $idsBloque[] = $cambio['id'];
                }
                $sql = 'UPDATE operaciones SET saldo = CASE id ' . implode(' ', $casosSaldo) .
                    ' ELSE saldo END, status = CASE id ' . implode(' ', $casosStatus) .
                    ' ELSE status END WHERE status <> 0 AND id IN (' .
                    implode(',', array_fill(0, count($idsBloque), '?')) . ')';
                $resultado['operacionesActualizadas'] += DB::update(
                    $sql,
                    array_merge($bindingsSaldo, $bindingsStatus, $idsBloque)
                );
            }, 'o.id', 'operacion_id');

            return $resultado;
        });
    }
}
