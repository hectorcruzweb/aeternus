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
    /** Regla común de reportes: excluir solo vínculos de cliente a operaciones canceladas. */
    private function sinOperacionesCanceladas($consulta)
    {
        return $consulta->where(function ($q) {
            // En tipo2 operaciones_id puede referir a cotización: nunca cruzar sus IDs.
            $q->where('s.tipo_cliente_id','<>',1)->orWhereNull('s.tipo_cliente_id')
                ->orWhereNotExists(function ($op) {
                    $op->select(DB::raw(1))->from('operaciones as oc')
                        ->whereColumn('oc.id','s.operaciones_id')->where('oc.status',0);
                });
        });
    }

    /** Estado común de agenda e histórico: programados vigentes todavía sin atención. */
    private function consultaProgramadosPendientes()
    {
        return $this->sinOperacionesCanceladas(DB::table('seguimientos as s')
            ->where('s.programado_b',1)->where('s.status',1)
            ->whereNull('s.fechahora_registro_seguimiento'));
    }

    /** Atraso legible al día del corte, sin alterar las fechas ni las reglas de vencimiento. */
    private function atrasoSeguimientoTexto(\Carbon\Carbon $fecha, \Carbon\Carbon $corte)
    {
        $desde=$fecha->copy()->startOfDay();$hasta=$corte->copy()->startOfDay();
        $dias=(int)$desde->diffInDays($hasta);
        if($dias===0)return 'Hoy';
        if($dias<7)return $dias.($dias===1?' día':' días');
        $anios=(int)$desde->diffInYears($hasta);
        if($anios>0)return $anios.($anios===1?' año':' años');
        $meses=(int)$desde->diffInMonths($hasta);
        if($meses>0)return $meses.($meses===1?' mes':' meses');
        $semanas=intdiv($dias,7);
        return $semanas.($semanas===1?' semana':' semanas');
    }

    /** Histórico pendiente: vence al día siguiente, sin límite inferior de antigüedad. */
    private function consultaSeguimientosVencidosHistorico(\Carbon\Carbon $corte)
    {
        return $this->consultaProgramadosPendientes()
            // Excluir fechas cero heredadas; 1000-01-01 es el mínimo válido de DATETIME.
            ->where('s.fechahora_programada','>=','1000-01-01 00:00:00')
            ->where('s.fechahora_programada','<',$corte->toDateString());
    }

    public function getSeguimientosVencidosHistoricoDashboard(\Carbon\Carbon $fechaCorte = null)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : \Carbon\Carbon::now('America/Mazatlan');
        return (int) $this->consultaSeguimientosVencidosHistorico($corte)->count();
    }

    /** Resumen y detalle histórico: no contar atendidos, cancelados ni pendientes de hoy. */
    public function getReporteSeguimientosVencidosHistorico(\Carbon\Carbon $fechaCorte = null, $soloResumen = false)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : \Carbon\Carbon::now('America/Mazatlan');
        $fila = $this->consultaSeguimientosVencidosHistorico($corte)->selectRaw('COUNT(*) AS total,
            SUM(CASE WHEN s.tipo_cliente_id=1 THEN 1 ELSE 0 END) AS clientes,
            SUM(CASE WHEN s.tipo_cliente_id=2 THEN 1 ELSE 0 END) AS cotizaciones,
            SUM(CASE WHEN s.fechahora_programada < ? THEN 1 ELSE 0 END) AS antiguos',[$corte->copy()->subDays(7)->toDateString()])->first();
        $resumen=[]; foreach(['total','clientes','cotizaciones','antiguos'] as $k)$resumen[$k]=(int)$fila->$k;
        $fechaCorta=function($f){return $f->format('d').'/'.ucfirst(rtrim($f->locale('es')->translatedFormat('M'),'.')).'/'.$f->format('Y');};
        $periodo='Histórico acumulado al '.$fechaCorta($corte);
        $datos=['tipo_operacion'=>'seguimientos_vencidos_historico','titulo'=>'Reporte de Seguimientos Vencidos — Histórico',
            'modulo'=>'Seguimientos vencidos','periodo'=>$periodo,'fecha'=>$fechaCorta($corte).' '.$corte->format('H:i'),
            'corte'=>$corte->toDateTimeString(),'hasta'=>$corte->copy()->subDay()->toDateString(),
            'resumen'=>$resumen,'totalRegistros'=>$resumen['total'],'resumenTexto'=>$resumen['total'].' seguimientos vencidos pendientes de atención',
            'encabezadoPeriodo'=>$periodo.' | Consultado al '.$corte->format('H:i'),
            'avisoSaldos'=>'Pendientes con fecha programada anterior a hoy. Vencen desde el día siguiente; no incluye atendidos, cancelados ni vínculos a operaciones canceladas.'];
        if($soloResumen)return $datos;
        $motivos=$this->getMotivos();$medios=$this->getMedios();
        $datos['seguimientos']=$this->consultaSeguimientosVencidosHistorico($corte)
            ->leftJoin('clientes as c',function($j){$j->on('c.id','=','s.clientes_id')->where('s.tipo_cliente_id',1);})
            ->leftJoin('cotizaciones as cot',function($j){$j->on('cot.id','=','s.clientes_id')->where('s.tipo_cliente_id',2);})
            ->leftJoin('usuarios as u','u.id','=','s.registro_programado_id')
            ->select('s.id','s.tipo_cliente_id','s.fechahora_programada','s.motivo_id','s.medio_preferido_programado_id','s.email_programado','s.comentario_programado','u.nombre as programo')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.nombre WHEN 2 THEN cot.cliente_nombre END AS nombre_cliente')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.telefono WHEN 2 THEN cot.cliente_telefono END AS telefono')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.celular END AS celular')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.email WHEN 2 THEN cot.cliente_email END AS email_cliente')
            ->orderBy('s.fechahora_programada','asc')->orderBy('s.id','asc')
            ->get()->map(function($f)use($corte,$fechaCorta,$motivos,$medios){
                $fecha=\Carbon\Carbon::parse($f->fechahora_programada,'America/Mazatlan');
                $f->fecha_texto=$fechaCorta($fecha).' '.$fecha->format('H:i');
                $f->tipo_cliente=[1=>'Cliente registrado',2=>'Bajo cotización'][(int)$f->tipo_cliente_id]??'No disponible';
                $f->contacto=implode(' / ',array_unique(array_filter([$f->telefono,$f->celular])));
                $f->email=mb_strtolower(trim($f->email_programado??'')?:trim($f->email_cliente??''),'UTF-8');
                $f->motivo=$motivos[(int)$f->motivo_id]??'No disponible';
                $f->medio=$medios[(int)$f->medio_preferido_programado_id]??'No disponible';
                $f->dias_atraso=(int)$fecha->copy()->startOfDay()->diffInDays($corte->copy()->startOfDay());
                $f->atraso_texto=$this->atrasoSeguimientoTexto($fecha,$corte);
                $f->estado='Vencido';
                $f->comentario=trim(html_entity_decode(strip_tags((string)$f->comentario_programado),ENT_QUOTES|ENT_HTML5,'UTF-8'));
                return(array)$f;
            })->all();
        return $datos;
    }

    public function previewReporteSeguimientosVencidosHistorico(Request $request)
    {
        return app(OperacionesController::class)->previewSeguimientosVencidosHistorico($request);
    }

    /** Agenda pendiente: hoy y seis días más; rango indexable y sin cargar contactos. */
    private function consultaProgramadosProximosSieteDias(\Carbon\Carbon $corte)
    {
        return $this->consultaProgramadosPendientes()
            ->where('s.fechahora_programada','>=',$corte->toDateString())
            ->where('s.fechahora_programada','<',$corte->copy()->addDays(7)->toDateString());
    }

    public function getProgramadosProximosSieteDiasDashboard(\Carbon\Carbon $fechaCorte = null)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : \Carbon\Carbon::now('America/Mazatlan');
        return (int) $this->consultaProgramadosProximosSieteDias($corte)->count();
    }

    /** Detalle de agenda, separado de realizados; los pendientes no tienen resultado de contacto. */
    public function getReporteProgramadosProximosSieteDias(\Carbon\Carbon $fechaCorte = null, $soloResumen = false)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : \Carbon\Carbon::now('America/Mazatlan');
        $fila = $this->consultaProgramadosProximosSieteDias($corte)->selectRaw('COUNT(*) AS total,
            SUM(CASE WHEN s.tipo_cliente_id=1 THEN 1 ELSE 0 END) AS clientes,
            SUM(CASE WHEN s.tipo_cliente_id=2 THEN 1 ELSE 0 END) AS cotizaciones,
            SUM(CASE WHEN s.fechahora_programada < ? THEN 1 ELSE 0 END) AS hoy',[$corte->copy()->addDay()->toDateString()])->first();
        $resumen=[]; foreach(['total','clientes','cotizaciones','hoy'] as $k)$resumen[$k]=(int)$fila->$k;
        $fechaCorta=function($f){return $f->format('d').'/'.ucfirst(rtrim($f->locale('es')->translatedFormat('M'),'.')).'/'.$f->format('Y');};
        $fin=$corte->copy()->addDays(6);$periodo=$fechaCorta($corte).' al '.$fechaCorta($fin).' (incluye hoy)';
        $datos=['tipo_operacion'=>'seguimientos_programados_proximos','titulo'=>'Reporte de Seguimientos Programados — Próximos 7 Días',
            'modulo'=>'Seguimientos programados','periodo'=>$periodo,'fecha'=>$fechaCorta($corte).' '.$corte->format('H:i'),
            'corte'=>$corte->toDateTimeString(),'desde'=>$corte->toDateString(),'hasta'=>$fin->toDateString(),
            'resumen'=>$resumen,'totalRegistros'=>$resumen['total'],'resumenTexto'=>$resumen['total'].' seguimientos pendientes | '.$resumen['hoy'].' para hoy',
            'encabezadoPeriodo'=>$periodo.' | Consultado al '.$corte->format('H:i'),
            'avisoSaldos'=>'Agenda pendiente de atención. No incluye seguimientos cancelados o atendidos ni los ligados a operaciones canceladas.'];
        if($soloResumen)return $datos;
        $motivos=$this->getMotivos();$medios=$this->getMedios();
        $datos['seguimientos']=$this->consultaProgramadosProximosSieteDias($corte)
            ->leftJoin('clientes as c',function($j){$j->on('c.id','=','s.clientes_id')->where('s.tipo_cliente_id',1);})
            ->leftJoin('cotizaciones as cot',function($j){$j->on('cot.id','=','s.clientes_id')->where('s.tipo_cliente_id',2);})
            ->leftJoin('usuarios as u','u.id','=','s.registro_programado_id')
            ->select('s.id','s.tipo_cliente_id','s.fechahora_programada','s.motivo_id','s.medio_preferido_programado_id','s.email_programado','s.comentario_programado','u.nombre as programo')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.nombre WHEN 2 THEN cot.cliente_nombre END AS nombre_cliente')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.telefono WHEN 2 THEN cot.cliente_telefono END AS telefono')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.celular END AS celular')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.email WHEN 2 THEN cot.cliente_email END AS email_cliente')
            ->orderBy('s.fechahora_programada','asc')->orderBy('s.id','asc')
            ->get()->map(function($f)use($corte,$fechaCorta,$motivos,$medios){
                $fecha=\Carbon\Carbon::parse($f->fechahora_programada,'America/Mazatlan');
                $f->fecha_texto=$fechaCorta($fecha).' '.$fecha->format('H:i');
                $f->tipo_cliente=[1=>'Cliente registrado',2=>'Bajo cotización'][(int)$f->tipo_cliente_id]??'No disponible';
                $f->contacto=implode(' / ',array_unique(array_filter([$f->telefono,$f->celular])));
                $f->email=mb_strtolower(trim($f->email_programado??'')?:trim($f->email_cliente??''),'UTF-8');
                $f->motivo=$motivos[(int)$f->motivo_id]??'No disponible';
                $f->medio=$medios[(int)$f->medio_preferido_programado_id]??'No disponible';
                $f->estado=$fecha->toDateString()===$corte->toDateString()?'Para hoy':'Pendiente';
                $f->comentario=trim(html_entity_decode(strip_tags((string)$f->comentario_programado),ENT_QUOTES|ENT_HTML5,'UTF-8'));
                return(array)$f;
            })->all();
        return $datos;
    }

    public function previewReporteProgramadosProximosSieteDias(Request $request)
    {
        return app(OperacionesController::class)->previewProgramadosProximosSieteDias($request);
    }

    /** Realizados del mes: fecha efectiva, excluyendo operaciones canceladas según criterio vigente. */
    private function consultaRealizadosMes(\Carbon\Carbon $corte)
    {
        $inicio = $corte->copy()->startOfMonth()->startOfDay();
        $consulta = DB::table('seguimientos as s')
            ->where('s.fechahora_seguimiento', '>=', $inicio)
            ->where('s.fechahora_seguimiento', '<', $inicio->copy()->addMonth())
            ->where('s.fechahora_seguimiento', '<=', $corte)
            ->where(function ($q) {
                $q->where(function ($x) { $x->where('s.programado_b', 0)->where('s.status', 1); })
                    ->orWhere(function ($x) { $x->where('s.programado_b', 1)->where('s.status', 2); });
            });
        return $this->sinOperacionesCanceladas($consulta);
    }

    /** Una consulta agregada; no carga clientes, cotizaciones ni comentarios para el dashboard. */
    public function getSeguimientosRealizadosMesDashboard(\Carbon\Carbon $fechaCorte = null)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : \Carbon\Carbon::now('America/Mazatlan');
        return (int) $this->consultaRealizadosMes($corte)->count();
    }

    /** Reporte mensual de actividad: clientes registrados y prospectos bajo cotización. */
    public function getReporteSeguimientosRealizadosMes(\Carbon\Carbon $fechaCorte = null, $soloResumen = false)
    {
        $corte = $fechaCorte ? $fechaCorte->copy()->setTimezone('America/Mazatlan') : \Carbon\Carbon::now('America/Mazatlan');
        $fila = $this->consultaRealizadosMes($corte)->selectRaw('COUNT(*) AS total,
            SUM(CASE WHEN s.tipo_cliente_id = 1 THEN 1 ELSE 0 END) AS clientes,
            SUM(CASE WHEN s.tipo_cliente_id = 2 THEN 1 ELSE 0 END) AS cotizaciones,
            SUM(CASE WHEN s.resultado_id = 6 THEN 1 ELSE 0 END) AS sin_contacto,
            SUM(CASE WHEN s.programado_b = 0 THEN 1 ELSE 0 END) AS directos,
            SUM(CASE WHEN s.programado_b = 1 THEN 1 ELSE 0 END) AS programados')->first();
        $resumen = [];
        foreach (['total','clientes','cotizaciones','sin_contacto','directos','programados'] as $campo) $resumen[$campo] = (int) $fila->$campo;
        $fecha = $corte->format('d').'/'.ucfirst(rtrim($corte->locale('es')->translatedFormat('M'), '.')).'/'.$corte->format('Y H:i');
        $periodo = $corte->locale('es')->translatedFormat('F \\d\\e Y');
        $datos = ['tipo_operacion'=>'seguimientos_realizados_mes', 'titulo'=>'Reporte de Seguimientos Realizados del Mes',
            'modulo'=>'Seguimientos realizados', 'periodo'=>$periodo, 'fecha'=>$fecha, 'corte'=>$corte->toDateTimeString(),
            'resumen'=>$resumen, 'totalRegistros'=>$resumen['total'],
            'resumenTexto'=>$resumen['total'].' seguimientos realizados | '.$resumen['directos'].' directos | '.$resumen['programados'].' programados atendidos',
            'encabezadoPeriodo'=>ucfirst($periodo).' | Consultado al '.$fecha,
            'avisoSaldos'=>'No incluye operaciones canceladas. Los intentos en los que no fue posible contactar también cuentan como gestión realizada.'];
        if ($soloResumen) return $datos;
        $motivos = $this->getMotivos(); $medios = $this->getMedios(); $resultados = $this->getResultadosContacto();
        $datos['seguimientos'] = $this->consultaRealizadosMes($corte)
            // Condicionar cada unión evita confundir clientes y cotizaciones con el mismo ID.
            ->leftJoin('clientes as c', function ($j) { $j->on('c.id','=','s.clientes_id')->where('s.tipo_cliente_id',1); })
            ->leftJoin('cotizaciones as cot', function ($j) { $j->on('cot.id','=','s.clientes_id')->where('s.tipo_cliente_id',2); })
            ->leftJoin('usuarios as u','u.id','=','s.realizo_seguimiento_id')
            ->select('s.id','s.tipo_cliente_id','s.programado_b','s.fechahora_seguimiento','s.motivo_id',
                's.medio_seguimiento_id','s.resultado_id','s.comentario_seguimiento','u.nombre as realizo')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.nombre WHEN 2 THEN cot.cliente_nombre END AS nombre_cliente')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.telefono WHEN 2 THEN cot.cliente_telefono END AS telefono')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.celular ELSE NULL END AS celular')
            ->selectRaw('CASE s.tipo_cliente_id WHEN 1 THEN c.email WHEN 2 THEN cot.cliente_email END AS email')
            ->orderBy('s.fechahora_seguimiento','desc')->orderBy('s.id','desc')
            ->get()->map(function ($f) use ($motivos,$medios,$resultados) {
                $fecha = \Carbon\Carbon::parse($f->fechahora_seguimiento,'America/Mazatlan');
                $f->fecha_texto = $fecha->format('d').'/'.ucfirst(rtrim($fecha->locale('es')->translatedFormat('M'),'.')).'/'.$fecha->format('Y H:i');
                $f->tipo_cliente = [1=>'Cliente registrado',2=>'Bajo cotización'][(int)$f->tipo_cliente_id] ?? 'No disponible';
                $f->motivo = $motivos[(int)$f->motivo_id] ?? 'No disponible';
                $f->medio = $medios[(int)$f->medio_seguimiento_id] ?? 'No disponible';
                $f->resultado = $resultados[(int)$f->resultado_id] ?? 'No disponible';
                $f->origen = (int)$f->programado_b === 1 ? 'Programado atendido' : 'Directo';
                $f->email = mb_strtolower(trim($f->email ?? ''),'UTF-8');
                $f->contacto = implode(' / ',array_unique(array_filter([$f->telefono,$f->celular])));
                $f->comentario = trim(html_entity_decode(strip_tags((string)$f->comentario_seguimiento),ENT_QUOTES | ENT_HTML5,'UTF-8'));
                return (array)$f;
            })->all();
        return $datos;
    }

    /** El módulo aporta reglas y datos; el transporte usa el visor compartido ya protegido. */
    public function previewReporteSeguimientosRealizadosMes(Request $request)
    {
        return app(OperacionesController::class)->previewSeguimientosRealizadosMes($request);
    }


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

                //Seguimientos::where('id', $request->seguimiento_id)->update($seguimientoData);
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
            $programado_b = Seguimientos::select('programado_b')->where('id', $id_seguimiento)->first();
            if (!$programado_b) {
                return $this->errorResponse("Error al consultar los datos para el correo.", 500);
            } else {
                $programado_b = $programado_b->programado_b;
            }
            // 🧩 Create a fake Request instance to reuse your existing method
            $requestDatos = new \Illuminate\Http\Request([
                'id' => $id_seguimiento,
                'programado_b' => $programado_b,
                'atendido' => 1
            ]);
            // 📨 Get seguimiento data by calling your own public method
            $seguimiento = $this->get_seguimientos($requestDatos);
            $seguimiento = json_decode($seguimiento->getContent(), true);
            if (!$seguimiento) {
                // No seguimiento found — do nothing
                throw new \Exception("Error, Seguimiento not found");
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
                throw new \Exception("Error, Cliente not found");
            }
            $cliente = json_decode($cliente->getContent(), true);
            //From here, I Collect the data required to send the email
            $data['programado_b'] = $programado_b;
            $data['cliente'] = $cliente['nombre'] ?? 'N/A';
            $data['medio'] = $seguimiento['medio_texto'] ?? 'N/A';
            $data['motivo'] = $seguimiento['motivo_texto'] ?? 'N/A';
            $data['fechahora_programada'] = $seguimiento['fechahora_programada'] ? fechahora($seguimiento['fechahora_programada']) : 'N/A';
            $data['fechahora_registro_programado'] = $seguimiento['fechahora_registro_programado'] ? fechahora($seguimiento['fechahora_registro_programado']) : 'N/A';
            //datos de seguimientos atendidos / realizados
            $data['medio_seguimiento_texto'] = $seguimiento['medio_seguimiento_texto'] ?? 'N/A';
            $data['resultado_texto'] = $seguimiento['resultado_texto'] ?? 'N/A';
            $data['fechahora_seguimiento_texto'] = $seguimiento['fechahora_seguimiento'] ? fechahora($seguimiento['fechahora_seguimiento']) : 'N/A';
            $data['fechahora_registro_seguimiento'] = $seguimiento['fechahora_registro_seguimiento'] ? fechahora($seguimiento['fechahora_registro_seguimiento']) : 'N/A';
            if ($seguimiento['tipo_cliente_id'] == 1 && !empty($seguimiento['operaciones_id']) && $seguimiento['operaciones_id'] > 0) {
                //cliente con operaciones
                $data['operacion'] = collect($cliente['operaciones'])
                    ->firstWhere('operacion_id', $seguimiento['operaciones_id'])['descripcion'] ?? null;
            }
            if ($seguimiento['tipo_cliente_id'] == 2) {
                //cliente bajo presupuesto
                $data['operacion'] = $cliente['operaciones'][0]['descripcion'] ?? 'N/A';
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
            'programado_b' => 'required|in:0,1|integer',
            'consulta_historica' => 'sometimes|boolean',
            'id' => 'sometimes|integer',
            'cliente_id' => 'sometimes|integer',
            'tipo_cliente_id' => 'sometimes|in:1,2|integer',
            'operaciones_id' => 'sometimes|integer',
            'status' => 'sometimes|in:0,1,2|integer',
            'atendido' => 'sometimes|in:0,1|integer'
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
        if ($request->boolean('consulta_historica') && (!$request->filled('id') || filter_var($request->id,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]) === false)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['id'=>'La consulta histórica requiere un seguimiento específico.']);
        }
        // Load lookups once
        $motivos = $this->getMotivos();
        $medios = $this->getMedios();
        $resultados = $this->getResultadosContacto();

        $queryClosure = function () use ($request, $motivos, $medios, $resultados) {
            // Normalize enviar_x_email
            $request->merge([
                'atendido' => $request->atendido ? 1 : 0
            ]);
            $query = Seguimientos::query()
                ->with(['cliente:id,nombre', 'cotizacion'])
                ->when($request->filled('cliente_nombre'), function ($q) use ($request) {
                    $q->where(function ($sub) use ($request) {
                        $sub->where(function ($inner) use ($request) {
                            $inner->where('tipo_cliente_id', 1)
                                ->whereHas('cliente', function ($has) use ($request) {
                                    $has->where('nombre', 'like', '%' . $request->cliente_nombre . '%');
                                });
                        })
                            ->orWhere(function ($inner) use ($request) {
                                $inner->where('tipo_cliente_id', 2)
                                    ->whereHas('cotizacion', function ($has) use ($request) {
                                        $has->where('cliente_nombre', 'like', '%' . $request->cliente_nombre . '%');
                                    });
                            });
                    });
                });
            $query->with(['operacion:id,empresa_operaciones_id,status'])
                ->when($request->filled('empresa_operaciones_id'), function ($q) use ($request) {
                    $q->whereHas('operacion', function ($q2) use ($request) {
                        if (trim($request->empresa_operaciones_id) && $request->empresa_operaciones_id > 0) {
                            $q2->where('empresa_operaciones_id', $request->empresa_operaciones_id);
                        }
                    });
                })
                ->when(!$request->boolean('consulta_historica'), function ($q) {
                    $q->where(function ($x) {
                        $x->whereHas('operacion', function ($sub) { $sub->where('status', '>', 0); })
                            ->orWhereDoesntHave('operacion');
                    });
                });
            if ((isset($request->fecha_inicio) && trim($request->fecha_inicio) != "") && (isset($request->fecha_fin) && trim($request->fecha_fin) != "")) {
                if ((int)$request->programado_b === 1) {
                    $query->whereDate('fechahora_programada', '>=', $request->fecha_inicio)->whereDate('fechahora_programada', '<=', $request->fecha_fin);
                } else {
                    $query->whereDate('fechahora_seguimiento', '>=', $request->fecha_inicio)->whereDate('fechahora_seguimiento', '<=', $request->fecha_fin);
                }
            }

            if ($request->has('id')) {
                $query->where('id', $request->id);
            }
            $select = [
                'id',
                'tipo_cliente_id',
                'clientes_id',
                'operaciones_id',
                'motivo_id',
                'status',
                'programado_b',
                'fechahora_programada',
                'fechahora_seguimiento',
                'medio_preferido_programado_id',
                'fechahora_registro_programado',
                'registro_programado_id',
                'email_programado',
                'medio_seguimiento_id',
                'fechahora_registro_seguimiento',
                'realizo_seguimiento_id',
                'email_seguimiento',
                'resultado_id',
                DB::raw(
                    'DATE(fechahora_programada) as fecha_programada'
                ),
                DB::raw(
                    'TIME(fechahora_programada) as hora_programada'
                ),
                DB::raw(
                    'DATE(fechahora_seguimiento) as fecha_seguimiento'
                ),
                DB::raw(
                    'TIME(fechahora_seguimiento) as hora_seguimiento'
                ),
            ];
            if ($request->has('id')) {
                $select[] = 'comentario_seguimiento';
                $select[] = 'comentario_programado';
                $select[] = 'comentario_cancelacion';
            }
            $query->select($select);
            // Por ID histórico se consulta el registro guardado, sin filtros de captura activa.
            if (!$request->boolean('consulta_historica')) {
            if ((int)$request->programado_b === 1) {
                //son programados
                $query->where('programado_b', 1);
                if ($request->atendido !== 1) {
                    $query->where('fechahora_registro_seguimiento', null);
                }
            } else {
                //son seguimientos
                $query->where(function ($q) use ($request) {
                    $q->where('programado_b', 0)
                        ->orWhere(function ($sub) use ($request) {
                            if ($request->atendido !== 1) {
                                $sub->where('programado_b', 1)
                                    ->where('fechahora_registro_seguimiento', '<>', null);
                            }
                        });
                });
            }
            if ($request->has('status')) {
                if ((int)$request->programado_b === 1) {
                    //son programados
                    $query->where('status', $request->status);
                } else {
                    //son seguimientos realizados
                    //si es status 0
                    $query->where(function ($q) use ($request) {
                        $q->where('status', $request->status)
                            ->orWhere(function ($sub) use ($request) {
                                $sub->where('programado_b', 1)
                                    ->where('fechahora_registro_seguimiento', '<>', null);
                                if ((int)$request->status === 0) {
                                    $sub->where('status', $request->status);
                                } else {
                                    $sub->whereIn('status', [1, 2]);
                                }
                            });
                    });
                }
            }
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
            return $seguimientos->map(function ($item) use ($motivos, $medios, $resultados) {
                $item->motivo_texto = $motivos[$item->motivo_id] ?? 'Desconocido';
                $item->medio_texto = $medios[$item->medio_preferido_programado_id] ?? 'Desconocido';
                $item->resultado_texto = $resultados[$item->resultado_id] ?? 'Desconocido';
                $item->medio_seguimiento_texto = $medios[$item->medio_seguimiento_id] ?? 'Desconocido';
                $item->tipo_programado_texto = $item->programado_b === 1 ? 'Si' : 'No';

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
