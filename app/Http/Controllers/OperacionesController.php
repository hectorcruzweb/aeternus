<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;

class OperacionesController extends ApiController
{
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
     * El año/mes filtra únicamente ventas y porCobrar; NO limita el vencido acumulado.
     * Para la gráfica, vencido no representa el vencido exclusivo del periodo elegido.
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
    public function getVentasSaldosOperacionesDashboard($tipoOperacion = null, $anio = null, $mes = null, Carbon $fechaCorte = null)
    {
        // Fuente de tipos/nombres/orden del carrusel: ampliar aquí al confirmar nuevos tipos.
        // El frontend renderiza cada fila recibida; no mantiene un catálogo paralelo.
        $tipos = [
            1 => 'Ventas en cementerio',
            2 => 'Cuotas de Mantenimiento',
            3 => 'Servicios funerarios',
            4 => 'Venta de planes funerarios',
            5 => 'Ventas en general',
        ];
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
                ELSE 0 END) AS vencido', [$corte->toDateString()])
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
    public function getReporteVentasCementerio($anio, $mes, Carbon $corte = null, $soloConteo = false)
    {
        return app(CementerioController::class)->getReporteVentasCementerio($anio, $mes, $corte, $soloConteo);
    }

    /** Tipo 2: cuotas por propiedad, sin duplicar consultas ni reglas de saldos. */
    public function getReporteCuotasMantenimiento($anio, $mes, Carbon $corte = null, $soloConteo = false)
    {
        return app(CementerioController::class)->getReporteCuotasMantenimiento($anio, $mes, $corte, $soloConteo);
    }

    /**
     * Motor financiero común. Cada controlador del módulo aporta su detalle específico.
     * El callback recibe las tres consultas y sus metadatos; no corre en modo soloConteo.
     * Cementerio aporta tipos 1/2; Funeraria aporta tipos 3/4/5. Aquí no se cargan sus catálogos.
     */
    public function getReporteVentasSaldosOperacionesDetallado($tipoOperacion, $anio, $mes, Carbon $corte = null, $soloConteo = false, callable $completarDetalle = null)
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
        $resumen = $this->getVentasSaldosOperacionesDashboard($tipoOperacion, $anio, $mes, $corte)[0];
        $inicio = Carbon::create((int) $anio, (int) $mes, 1, 0, 0, 0, 'America/Mazatlan');
        $fin = $inicio->copy()->addMonth();
        $base = DB::table('operaciones as o')->where('o.empresa_operaciones_id', $tipoOperacion)
            ->whereIn('o.status', [1, 2])->where('o.fecha_operacion', '<=', $corte);
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
            ->selectRaw('SUM(CASE WHEN sp.fecha_programada < ? THEN sp.pendiente ELSE 0 END) AS vencido', [$corte->toDateString()])
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
        $vencidos = $conPagos(clone $base)->where('pp.fecha_programada', '<', $corte->toDateString());
        $consultas = ['ventas' => $ventas, 'por_cobrar' => $cobrar, 'vencidos' => $vencidos];
        $conteos = [];
        foreach ($consultas as $clave => $consulta) {
            $conteos[$clave] = DB::query()->fromSub(clone $consulta, 'filas')->count();
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
        if ($soloConteo) return $resultado;
        if (!$completarDetalle) throw new \LogicException('Solicite el detalle mediante el controlador del módulo.');
        return $completarDetalle($consultas, $resultado);
    }

    /** Resolver el controlador del módulo sin duplicar transporte, caché o exportación. */
    private function obtenerReporteIndividual($tipoOperacion, $anio, $mes, Carbon $corte = null, $soloConteo = false)
    {
        if ($tipoOperacion === 3) {
            return app(FunerariaController::class)->getReporteServiciosFunerarios($anio, $mes, $corte, $soloConteo);
        }
        if ($tipoOperacion === 4) {
            return app(FunerariaController::class)->getReporteVentasPlanesFunerarios($anio, $mes, $corte, $soloConteo);
        }
        if ($tipoOperacion === 5) {
            return app(FunerariaController::class)->getReporteVentasGenerales($anio, $mes, $corte, $soloConteo);
        }
        if ($tipoOperacion === 1) return $this->getReporteVentasCementerio($anio, $mes, $corte, $soloConteo);
        if ($tipoOperacion === 2) return $this->getReporteCuotasMantenimiento($anio, $mes, $corte, $soloConteo);
        throw new \InvalidArgumentException('Reporte individual no disponible.');
    }


    /** Resumen ejecutivo: dos consultas agregadas, sin clientes, ventas o pagos individuales. */
    public function getResumenGeneralOperaciones($anio, $mes, Carbon $corte = null)
    {
        if ($mes === null) throw new \InvalidArgumentException('Indique un mes para el reporte general.');
        $corte = $corte ? $corte->copy()->setTimezone('America/Mazatlan') : Carbon::now('America/Mazatlan');
        $filas = $this->getVentasSaldosOperacionesDashboard(null, $anio, $mes, $corte);
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
        return [
            'tipo_operacion' => 'todas', 'titulo' => 'Resumen General de Ventas y Saldos',
            'modulo' => 'Todas las operaciones', 'etiqueta_periodo' => 'Ventas del mes',
            'anio' => (int) $anio, 'mes' => (int) $mes,
            'periodo' => $inicio->locale('es')->translatedFormat('F \\d\\e Y'),
            'fecha' => $corte->format('d').'/'.ucfirst(rtrim($corte->locale('es')->translatedFormat('M'), '.')).'/'.$corte->format('Y H:i'),
            'corte' => $corte->toDateTimeString(), 'resumen' => $total, 'tipos' => $filas,
        ];
    }

    /** El reporte general abre solo cinco filas. No invoca reportes detallados. */
    public function previewReporteGeneralOperaciones(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $request->validate(['anio' => 'required|integer|min:1000', 'mes' => 'required|integer|between:1,12']);
        $corte = Carbon::now('America/Mazatlan');
        try {
            $datos = DB::transaction(function () use ($request, $corte) {
                return $this->getResumenGeneralOperaciones($request->anio, $request->mes, $corte);
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
            $actual = $this->getResumenGeneralOperaciones($anterior['anio'], $anterior['mes'], $corte);
            $actual['empresa'] = $anterior['empresa'];
            $actual['detalles'] = [];
            foreach ($actual['tipos'] as $tipo) {
                // Cada módulo sigue siendo dueño de su información específica.
                $actual['detalles'][] = $this->obtenerReporteIndividual((int) $tipo['id'], $anterior['anio'], $anterior['mes'], $corte);
            }
            return $actual;
        });
    }

    /** El mismo selector se usa para descarga y correo: el general exporta solo su resumen. */
    private function exportadorReporteVentas(array $datos)
    {
        return ($datos['tipo_operacion'] ?? null) === 'todas'
            ? new \App\Exports\ResumenGeneralOperacionesExport($datos)
            : new \App\Exports\VentasCementerioExport($datos);
    }

    /** Entrada del visor: instantánea privada de 15 minutos; no genera archivos al consultar. */
    public function previewVentasCementerio(Request $request)
    {
        $usuario = $this->usuarioReporteCementerio($request);
        $request->validate(['anio' => 'required|integer|min:1000', 'mes' => 'required|integer|between:1,12', 'empresa_operaciones_id' => 'sometimes|required|integer|in:1,2,3,4,5']);
        $tipoOperacion = (int) $request->input('empresa_operaciones_id', 1);
        $corte = Carbon::now('America/Mazatlan');
        $limite = 3500; // Suma de filas de los tres apartados; no limita la descarga.
        try {
            $datos = DB::transaction(function () use ($request, $corte, $tipoOperacion, $limite) {
                $datos = $this->obtenerReporteIndividual($tipoOperacion, $request->anio, $request->mes, $corte, true);
                if ($datos['totalRegistros'] <= $limite) {
                    $datos = $this->obtenerReporteIndividual($tipoOperacion, $request->anio, $request->mes, $corte);
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
        if ($reporte['modo'] === 'filtro') {
            $anterior = $reporte['datos'];
            $reporte['datos'] = DB::transaction(function () use ($anterior) {
                return $this->obtenerReporteIndividual((int) ($anterior['tipo_operacion'] ?? 1), $anterior['anio'], $anterior['mes']);
            });
            $reporte['datos']['empresa'] = $anterior['empresa'];
        }
        return $reporte;
    }

    private function htmlReporteCementerio(array $datos, $modoPdf = false)
    {
        $esGeneral = ($datos['tipo_operacion'] ?? null) === 'todas';
        $ruta = public_path(env('LOGOJPG'));
        $mime = is_file($ruta) ? (new \finfo(FILEINFO_MIME_TYPE))->file($ruta) : null;
        $logo = in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)
            ? 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($ruta)) : null;
        return view($esGeneral ? 'reportes.temporales_visor_excel.general_operaciones.resumen' : 'reportes.temporales_visor_excel.ventas_cementerio.reporte', [
            'datos' => $datos, 'secciones' => $esGeneral ? [] : \App\Exports\VentasCementerioExport::secciones($datos),
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
            'orientation' => 'landscape', 'page-size' => 'A4', 'encoding' => 'UTF-8',
            'margin-top' => 10, 'margin-bottom' => 12, 'margin-left' => 10, 'margin-right' => 10,
            'print-media-type' => true, 'disable-javascript' => false,
            'enable-local-file-access' => false, 'disable-local-file-access' => true,
            'footer-html' => $footer, 'footer-spacing' => 3,
        ]);
    }

    public function descargarVentasCementerioExcel(Request $request, $token)
    {
        $reporte = $this->datosReporteCementerio($request, $token);
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
        $nombre = $reporte['nombre'];
        $completo = $request->boolean('completo');
        if ($completo) {
            abort_unless(($reporte['datos']['tipo_operacion'] ?? null) === 'todas', 422, 'Este reporte no ofrece un adjunto detallado completo adicional.');
            abort_unless($request->input('formato', 'excel') === 'excel', 422, 'El detalle completo se envía en formato Excel.');
            $reporte['datos'] = $this->datosDetalleGeneralVentas($reporte['datos']);
            $nombre = 'Detalle General de Ventas y Saldos '.now('America/Mazatlan')->format('Y-m-d H-i-s').'.xlsx';
        }

        if ($request->input('formato', 'excel') === 'pdf') {
            $contenido = $this->pdfReporteCementerio($reporte['datos'])->output();
            $nombre = preg_replace('/\.xlsx$/i', '.pdf', $nombre);
        } else {
            $contenido = \Maatwebsite\Excel\Facades\Excel::raw(($completo ? new \App\Exports\ResumenGeneralOperacionesExport($reporte['datos'], true) : $this->exportadorReporteVentas($reporte['datos'])), \Maatwebsite\Excel\Excel::XLSX);
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
