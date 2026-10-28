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
     * Por cobrar suma el pendiente de TODOS los pagos programados vigentes
     * de las operaciones vendidas en el periodo, incluso con vencimiento futuro.
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

        // Ventas y por cobrar de pagos programados se restringen a operaciones del periodo.
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
        // Por cobrar incluye pendientes de todas las fechas, incluso NULL.
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
                WHEN ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2) > 0
                THEN ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2)
                ELSE 0 END) AS por_cobrar')
            ->selectRaw('SUM(CASE
                WHEN pp.fecha_programada < ?
                 AND ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2) > 0
                THEN ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2)
                ELSE 0 END) AS vencido', [$corte->toDateString()])
            ->groupBy('pp.operaciones_id', 'op.empresa_operaciones_id');

        // Dos agregados independientes: no unir el vencido histórico a ventas del mes.
        // UNION ALL conserva tipos con vencido aunque no tengan ventas en el periodo.
        $ventasPeriodo = (clone $operacionesPeriodo)
            ->leftJoinSub(clone $saldosPorOperacion, 's', function ($join) {
                $join->on('s.operaciones_id', '=', 'o.id');
            })
            ->select('o.empresa_operaciones_id')
            ->selectRaw('COALESCE(SUM(o.total), 0) AS ventas')
            ->selectRaw('COALESCE(SUM(s.por_cobrar), 0) AS por_cobrar')
            ->selectRaw('0 AS vencido')
            ->groupBy('o.empresa_operaciones_id');

        $vencidoAcumulado = DB::query()
            ->fromSub($saldosPorOperacion, 'v')
            ->select('v.empresa_operaciones_id')
            ->selectRaw('0 AS ventas, 0 AS por_cobrar')
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
