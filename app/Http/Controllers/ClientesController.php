<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Incluye la búsqueda de destinatarios de reportes entre clientes existentes.
 * Dónde ajustar: buscarCorreosReportes concentra la búsqueda por nombre/correo. No confundir sugerir un
 * cliente con agregarlo automáticamente a la lista de destinatarios.
 * Relacionado: EnviarReporteCorreo.vue; services/clientes.js; routes/api.php
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */


namespace App\Http\Controllers;

use Log;
use App\Clientes;
use App\Operaciones;
use App\Cotizaciones;
use App\Seguimientos;
use App\Nacionalidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CementerioController;

class ClientesController extends ApiController
{
    /** Sugerencias acotadas de clientes para el correo del visor; no carga expedientes. 
     * Detalle del método: Busca clientes por texto de nombre/correo para ofrecer sugerencias al preparar
     * un envío.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío).
     * Salida / efectos: JSON de coincidencias; no agrega destinatarios ni envía mensajes.
     */
    public function buscarCorreosReportes(Request $request)
    {
        $usuario = $request->user('api');
        abort_unless($usuario && in_array((int) $usuario->roles_id, [1, 4], true), 403);
        $request->validate(['q' => 'required|string|min:3|max:75', 'campo' => 'sometimes|required|in:email,nombre']);
        $campo = $request->input('campo', 'email') === 'nombre' ? 'nombre' : 'email';
        $texto = trim($request->input('q'));
        if (mb_strlen($texto, 'UTF-8') < 3) return response()->json([]);
        // Escapar comodines para que % o _ se busquen como caracteres, no como filtros abiertos.
        $prefijo = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $texto);
        $clientes = Clientes::query()->select(['id', 'nombre', 'email'])
            ->where('status', 1)->whereNotNull('email')->where('email', '<>', '')
            ->whereRaw('LENGTH(email) <= 75')
            ->whereRaw($campo . " LIKE ? ESCAPE '!'", [$campo === 'nombre' ? '%' . $prefijo . '%' : $prefijo . '%'])
            ->orderBy($campo)->orderBy('id')->limit(10)->get()
            ->filter(function ($cliente) { return filter_var(trim($cliente->email), FILTER_VALIDATE_EMAIL); })
            ->map(function ($cliente) {
                return ['id' => $cliente->id, 'nombre' => $cliente->nombre, 'email' => trim($cliente->email)];
            })->values();
        return response()->json($clientes)->header('Cache-Control', 'no-store, private');
    }

    public function get_nacionalidades()
    {
        /**todas las nacionalidades */
        return Nacionalidades::get();
    }


    /**
     * Listado alternativo de clientes. No modifica ni delega en get_clientes.
     * Parámetros: numero, busqueda (nombre/teléfonos/email), desde/hasta (alta),
     * estado, adeudos (sin/pendiente/vencido), operaciones[] (OR entre tipos),
     * seguimientos (sin/pendiente/vencido) y contacto[] (celular/correo).
     * Los grupos se combinan mediante AND; operaciones y adeudos se evalúan en
     * las MISMAS operaciones vigentes. Sin tipos seleccionados se consideran todos.
     * Pendiente: capital de programados activos menos aplicaciones vigentes de
     * movimientos 1/3/4/5, redondeado por programado y limitado a cero, sin intereses.
     * Vencido: pendiente con fecha anterior a hoy en America/Mazatlan, no incluye hoy.
     * Pagina en SQL, siempre 15 clientes únicos; no carga el catálogo completo en memoria.
     */
    public function FiltrarClientes(Request $request)
    {
        $filtros = $this->validarFiltrosClientes($request);
        $corte = \Carbon\Carbon::now('America/Mazatlan');
        $consulta = $this->consultaFiltrarClientes($filtros, $corte);
        return $consulta->orderBy('clientes.id', 'desc')
            ->paginate(15, ['*'], 'page', (int) ($filtros['page'] ?? 1))
            ->appends($request->except('page'));
    }

    /** Validación compartida: listado y exportación aceptan exactamente los mismos filtros. */
    private function validarFiltrosClientes(Request $request)
    {
        $filtros = $request->validate([
            'page' => 'sometimes|integer|min:1',
            'numero' => 'nullable|integer|min:1',
            'busqueda' => 'nullable|string|max:100',
            'desde' => 'nullable|date_format:Y-m-d',
            'hasta' => 'nullable|date_format:Y-m-d' . ($request->filled('desde') ? '|after_or_equal:desde' : ''),
            'estado' => 'nullable|in:0,1',
            'adeudos' => 'nullable|in:sin,pendiente,vencido',
            'operaciones' => 'sometimes|array|max:5',
            'operaciones.*' => 'required|integer|in:1,2,3,4,5|distinct',
            'seguimientos' => 'nullable|in:sin,pendiente,vencido',
            'contacto' => 'sometimes|array|max:2',
            'contacto.*' => 'required|in:celular_si,celular_no,correo_si,correo_no|distinct',
        ]);
        $contacto = $filtros['contacto'] ?? [];
        foreach (['celular', 'correo'] as $campo) {
            if (in_array($campo . '_si', $contacto, true) && in_array($campo . '_no', $contacto, true)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['contacto' => 'Seleccione solo una condición por dato de contacto.']);
            }
        }
        return $filtros;
    }

    /** Abre HTML sin generar archivos. Conserva instantánea hasta 5000 filas; las consultas
     * mayores guardan filtros y se ejecutan completas únicamente al descargar. */
    public function reporteListadoClientes(Request $request)
    {
        $usuario = $request->user('api');
        abort_unless($usuario, 403);
        $filtros = $this->validarFiltrosClientes($request);
        $corte = \Carbon\Carbon::now('America/Mazatlan');
        $consulta = $this->consultaFiltrarClientes($filtros, $corte);
        $total = (clone $consulta)->count();
        $filas = $total <= 5000 ? $this->filasReporteClientes($consulta, $filtros, $corte) : null;
        $contexto = ['usuario_id'=>(int)$usuario->id, 'filtros'=>$filtros,
            'corte'=>$corte->toIso8601String(), 'filas'=>$filas,
            'empresa'=>app(EmpresaController::class)->get_empresa_data()->toArray()];
        $token = bin2hex(random_bytes(32));
        \Illuminate\Support\Facades\Cache::put('clientes_listado_'.$token, $contexto, $corte->copy()->addMinutes(15));
        $datos = $this->presentarReporteClientes($contexto);
        return response()->json(['titulo'=>'Listado de clientes',
            'nombreArchivo'=>'Listado de clientes '.$corte->format('Y-m-d H-i-s').'.xlsx',
            'tokenDescarga'=>$token, 'totalRegistros'=>$total, 'limiteVisualizacion'=>5000,
            'vistaPreviaDisponible'=>$filas !== null,
            'mensaje'=>$filas === null ? 'El reporte no se puede visualizar por su tamaño. Puede descargarlo completo en Excel o PDF.' : null,
            'hojas'=>$filas !== null ? [['nombre'=>'Clientes', 'html'=>view('reportes.temporales_visor_excel.clientes.listado',$datos)->render()]] : []
        ])->header('Cache-Control','no-store, private');
    }

    /** Descarga completa en el formato permitido; nunca utiliza la página visible del listado.
     * El token privado pertenece al usuario. Su vencimiento responde 410, no cierra la sesión. */
    public function descargarListadoClientes(Request $request, $token, $formato)
    {
        abort_unless(in_array($formato,['excel','pdf'],true),404);
        $usuario = $request->user('api');
        abort_unless($usuario,403);
        abort_unless(preg_match('/^[a-f0-9]{64}$/D',$token),404);
        $contexto = \Illuminate\Support\Facades\Cache::get('clientes_listado_'.$token);
        abort_unless($contexto,410,'La consulta venció. Actualice el reporte antes de descargar.');
        abort_unless((int)$contexto['usuario_id'] === (int)$usuario->id,403);
        if ($contexto['filas'] === null) {
            $contexto['corte'] = \Carbon\Carbon::now('America/Mazatlan')->toIso8601String();
            $contexto['filas'] = $this->filasReporteClientes($this->consultaFiltrarClientes($contexto['filtros'],\Carbon\Carbon::parse($contexto['corte'])), $contexto['filtros'], \Carbon\Carbon::parse($contexto['corte']));
        }
        $datos = $this->presentarReporteClientes($contexto);
        $nombre = 'Listado de clientes '.\Carbon\Carbon::parse($contexto['corte'])->format('Y-m-d H-i-s');
        if ($formato === 'excel') {
            $respuesta = \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ListadoClientesExport($datos),$nombre.'.xlsx');
        } else {
            $datos['modoPdf'] = true;
            $pdf = \PDF::loadView('reportes.temporales_visor_excel.clientes.listado',$datos);
            $pdf->setOptions(['page-size'=>'Letter','orientation'=>'portrait','encoding'=>'UTF-8',
                'margin-top'=>10,'margin-bottom'=>14,'margin-left'=>10,'margin-right'=>10,
                'print-media-type'=>true,'enable-local-file-access'=>false,'disable-local-file-access'=>true,
                'footer-html'=>view('reportes.temporales_visor_excel.compartidos.footer_pdf',[
                    'fechaImpresion'=>$datos['fecha'],'dominio'=>$datos['dominio']])->render(),'footer-spacing'=>3]);
            $respuesta = $pdf->download($nombre.'.pdf');
        }
        $respuesta->headers->set('Cache-Control','no-store, private');
        return $respuesta;
    }

    /** Mapea datos de lectura; teléfonos y correos se conservan como texto, sin fórmulas Excel. */
    private function filasReporteClientes($consulta, array $filtros = [], $corte = null)
    {
        $corte = $corte ?: \Carbon\Carbon::now('America/Mazatlan');
        $this->agregarIndicadoresReporteClientes($consulta, $filtros, $corte);
        return $consulta->setEagerLoads([])->addSelect(['clientes.telefono','clientes.fecha_registro'])
            ->orderBy('clientes.id','desc')->get()->map(function($c){
                return ['id'=>$c->id,'nombre'=>$c->nombre,'direccion'=>$c->direccion,
                    'celular'=>$c->celular,'telefono'=>$c->telefono,'email'=>mb_strtolower(trim($c->email ?? ''),'UTF-8'),
                    'status'=>(int)$c->status,'alta'=>$c->fecha_registro,
                    'adeudo'=>(float)($c->reporte_adeudo ?? 0),'pagos_vencidos'=>(int)($c->reporte_pagos_vencidos ?? 0),
                    'seguimientos'=>(int)($c->reporte_seguimientos ?? 0),'operaciones'=>(int)($c->reporte_operaciones ?? 0)];
            })->all();
    }

    /** Agregados SQL por cliente, solo al exportar. Mismas reglas del filtro: capital de
     * programados activos, aplicaciones vigentes 1/3/4/5, operaciones 1/2 y cuotas activas.
     * El vencido excluye hoy. Cada subconsulta devuelve un escalar, sin duplicar clientes
     * ni cargar pagos/expedientes completos; los tipos seleccionados acotan los importes. */
    private function agregarIndicadoresReporteClientes($consulta, array $f, \Carbon\Carbon $corte)
    {
        $operaciones = DB::table('operaciones as ro')->whereColumn('ro.clientes_id','clientes.id')
            ->whereIn('ro.status',[1,2])->where('ro.fecha_operacion','<=',$corte->toDateTimeString())
            ->where(function($q){$q->where('ro.empresa_operaciones_id','<>',2)->orWhereExists(function($c){
                $c->selectRaw('1')->from('cuotas_cementerio as rc')->whereColumn('rc.id','ro.cuotas_cementerio_id')->where('rc.status',1);
            });});
        if(!empty($f['operaciones'])) {
            $operaciones->whereIn('ro.empresa_operaciones_id',array_map('intval',$f['operaciones']));
            $consulta->selectSub((clone $operaciones)->selectRaw('COUNT(*)'),'reporte_operaciones');
        }
        if(!empty($f['adeudos'])) {
            $aplicaciones=DB::table('pagos_pagos_programados as rp')->join('pagos as rg','rg.id','=','rp.pagos_id')
                ->where('rg.status',1)->whereIn('rg.movimientos_pagos_id',[1,3,4,5])
                ->select('rp.pagos_programados_id')->selectRaw('SUM(rp.monto) as cubierto')->groupBy('rp.pagos_programados_id');
            $pendientes=(clone $operaciones)->join('pagos_programados as rpp','rpp.operaciones_id','=','ro.id')
                ->leftJoinSub($aplicaciones,'ra',function($j){$j->on('ra.pagos_programados_id','=','rpp.id');})
                ->where('rpp.status',1)->whereRaw('ROUND(rpp.monto_programado - COALESCE(ra.cubierto,0),2)>0');
            if($f['adeudos']==='vencido') $pendientes->where('rpp.fecha_programada','>=','1000-01-01')->where('rpp.fecha_programada','<',$corte->toDateString());
            $consulta->selectSub((clone $pendientes)->selectRaw('COALESCE(SUM(ROUND(rpp.monto_programado - COALESCE(ra.cubierto,0),2)),0)'),'reporte_adeudo');
            if($f['adeudos']==='vencido') $consulta->selectSub((clone $pendientes)->selectRaw('COUNT(*)'),'reporte_pagos_vencidos');
        }
        if(!empty($f['seguimientos'])) {
            $seguimientos=DB::table('seguimientos as rs')->whereColumn('rs.clientes_id','clientes.id')
                ->where('rs.tipo_cliente_id',1)->where('rs.programado_b',1)->where('rs.status',1)
                ->whereNull('rs.fechahora_registro_seguimiento')->whereNotExists(function($q){
                    $q->selectRaw('1')->from('operaciones as rx')->whereColumn('rx.id','rs.operaciones_id')->where('rx.status',0);
                });
            if($f['seguimientos']==='vencido') $seguimientos->where('rs.fechahora_programada','>=','1000-01-01')->where('rs.fechahora_programada','<',$corte->toDateString());
            $consulta->selectSub($seguimientos->selectRaw('COUNT(*)'),'reporte_seguimientos');
        }
    }

    /** Títulos de filtros y resumen comunes a HTML, PDF y Excel, sin nuevas consultas de saldos. */
    private function presentarReporteClientes(array $contexto)
    {
        $f = $contexto['filtros']; $etiquetas=[];
        foreach (['numero'=>'Núm. cliente','busqueda'=>'Nombre, teléfono o correo','desde'=>'Alta desde','hasta'=>'Alta hasta'] as $key=>$label) {
            if (!empty($f[$key])) $etiquetas[]=$label.': '.(in_array($key,['desde','hasta']) ? \Carbon\Carbon::parse($f[$key])->locale('es')->translatedFormat('j \d\e F \d\e Y') : $f[$key]);
        }
        $mapas=['estado'=>['0'=>'Cancelados','1'=>'Activos'],
            'adeudos'=>['sin'=>'Sin adeudo','pendiente'=>'Con saldo pendiente','vencido'=>'Con pagos vencidos'],
            'operaciones'=>[1=>'Ventas en cementerio',2=>'Cuotas de mantenimiento',3=>'Servicios funerarios',4=>'Planes funerarios',5=>'Ventas en general'],
            'seguimientos'=>['sin'=>'Sin seguimiento pendiente','pendiente'=>'Con seguimiento pendiente','vencido'=>'Con seguimiento vencido'],
            'contacto'=>['celular_si'=>'Con celular','celular_no'=>'Sin celular','correo_si'=>'Con correo','correo_no'=>'Sin correo']];
        foreach($mapas as $key=>$mapa) foreach((array)($f[$key] ?? []) as $valor) if(isset($mapa[$valor])) $etiquetas[]=$mapa[$valor];
        $filas=$contexto['filas'] ?? []; $celulares=0; $correos=0;
        foreach($filas as $fila){
            $numero=preg_replace('/\D/','',$fila['celular'] ?? '');
            if(strlen($numero)>=10 && preg_match('/[1-9]/',$numero)) $celulares++;
            if(filter_var($fila['email'],FILTER_VALIDATE_EMAIL)) $correos++;
        }
        // Columnas contextuales comunes: clave, título, formato. No mostrar indicadores ajenos al filtro.
        $extras=[];
        if(!empty($f['operaciones'])) $extras[]=['clave'=>'operaciones','titulo'=>'Operaciones vigentes','moneda'=>false];
        if(!empty($f['adeudos'])) $extras[]=['clave'=>'adeudo','titulo'=>$f['adeudos']==='vencido' ? 'Saldo vencido' : 'Adeudo total','moneda'=>true];
        if(($f['adeudos'] ?? '')==='vencido') $extras[]=['clave'=>'pagos_vencidos','titulo'=>'Pagos vencidos','moneda'=>false];
        if(!empty($f['seguimientos'])) $extras[]=['clave'=>'seguimientos','titulo'=>$f['seguimientos']==='vencido' ? 'Seguimientos vencidos' : 'Seguimientos pendientes','moneda'=>false];
        $logo=public_path(env('LOGOJPG'));
        $mime=is_file($logo) ? (new \finfo(FILEINFO_MIME_TYPE))->file($logo) : '';
        return ['extras'=>$extras,'filas'=>$filas,'empresa'=>$contexto['empresa'],'filtros'=>$etiquetas ?: ['Todos los clientes'],
            'fecha'=>\Carbon\Carbon::parse($contexto['corte'])->locale('es')->translatedFormat('j \d\e F \d\e Y, H:i'),
            'dominio'=>parse_url(config('app.url'),PHP_URL_HOST) ?: config('app.url'),
            'logo'=>in_array($mime,['image/png','image/jpeg']) ? 'data:'.$mime.';base64,'.base64_encode(file_get_contents($logo)) : '',
            'resumen'=>['total'=>count($filas),'celular'=>$celulares,'correo'=>$correos]];
    }

    /** Construye la consulta sin ejecutarla para mantener separadas reglas y paginación.
     * Conservar esta base al implementar el reporte: no reconstruir otra lógica de saldos.
     */
    private function consultaFiltrarClientes(array $filtros, \Carbon\Carbon $corte)
    {
        // Mismos atributos y relaciones que necesita la tabla y sus formularios actuales.
        $consulta = Clientes::select([
            'clientes.id', 'clientes.nombre', 'clientes.direccion', 'clientes.cp',
            'clientes.cp as direccion_fiscal_cp', 'clientes.email', 'clientes.rfc as rfc_raw',
            'clientes.status', 'clientes.razon_social', 'clientes.direccion_fiscal',
            'clientes.vivo_b as vivo_b_raw', 'clientes.regimen_fiscal_id',
            'clientes.nacionalidades_id', 'clientes.generos_id', 'clientes.servicios_gratis',
            'clientes.nota_servicios_gratis',
        ])->selectRaw("COALESCE(clientes.rfc, 'N/A') AS rfc")
            ->selectRaw("COALESCE(clientes.celular, 'N/A') AS celular")
            ->selectRaw("CASE WHEN clientes.vivo_b = 1 THEN 'Vivo' ELSE 'Fallecido' END AS vivo_b")
            ->with(['regimen', 'nacionalidad', 'genero']);
        if (!empty($filtros['numero'])) $consulta->where('clientes.id', $filtros['numero']);
        if (isset($filtros['estado']) && $filtros['estado'] !== '') $consulta->where('clientes.status', $filtros['estado']);
        if (!empty($filtros['desde'])) $consulta->where('clientes.fecha_registro', '>=', $filtros['desde'] . ' 00:00:00');
        if (!empty($filtros['hasta'])) $consulta->where('clientes.fecha_registro', '<', \Carbon\Carbon::createFromFormat('Y-m-d', $filtros['hasta'])->addDay()->toDateString() . ' 00:00:00');

        $texto = trim($filtros['busqueda'] ?? '');
        if ($texto !== '') {
            // Escapar comodines: % y _ se interpretan literalmente, siempre con parámetros enlazados.
            $escapar = function ($valor) { return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $valor); };
            $digitos = preg_replace('/\D/', '', $texto);
            $buscarTelefono = $digitos !== '' && preg_match('/^[+0-9\s().-]+$/', $texto);
            $consulta->where(function ($q) use ($texto, $escapar, $digitos, $buscarTelefono) {
                $q->where(function ($nombre) use ($texto, $escapar) {
                    foreach (preg_split('/\s+/u', $texto, -1, PREG_SPLIT_NO_EMPTY) as $palabra) {
                        $nombre->whereRaw("clientes.nombre LIKE ? ESCAPE '!'", ['%' . $escapar($palabra) . '%']);
                    }
                })->orWhereRaw("clientes.email LIKE ? ESCAPE '!'", ['%' . $escapar($texto) . '%']);
                if ($buscarTelefono) {
                    foreach (['telefono', 'celular', 'telefono_extra'] as $campo) {
                        $q->orWhereRaw($this->telefonoNormalizadoClientes($campo) . ' LIKE ?', ['%' . $digitos . '%']);
                    }
                }
            });
        }
        foreach ($filtros['contacto'] ?? [] as $condicion) {
            $celular = $this->telefonoNormalizadoClientes('celular');
            // Número utilizable: al menos 10 dígitos; descartar vacíos, ceros y textos N/A.
            $tieneCelular = "(LENGTH($celular) >= 10 AND $celular REGEXP '^[0-9]+$' AND $celular REGEXP '[1-9]')";
            $tieneCorreo = "(TRIM(COALESCE(clientes.email, '')) REGEXP '^[^[:space:]@]+@[^[:space:]@]+[.][^[:space:]@]+$')";
            $condiciones = ['celular_si' => $tieneCelular, 'celular_no' => 'NOT ' . $tieneCelular, 'correo_si' => $tieneCorreo, 'correo_no' => 'NOT ' . $tieneCorreo];
            $consulta->whereRaw($condiciones[$condicion]);
        }

        $tipos = array_map('intval', $filtros['operaciones'] ?? []);
        $adeudo = $filtros['adeudos'] ?? '';
        if ($tipos || $adeudo !== '') {
            $operaciones = DB::table('operaciones as o')->selectRaw('1')
                ->whereColumn('o.clientes_id', 'clientes.id')->whereIn('o.status', [1, 2])
                ->where('o.fecha_operacion', '<=', $corte->toDateTimeString())
                ->where(function ($q) {
                    $q->where('o.empresa_operaciones_id', '<>', 2)->orWhereExists(function ($cuota) {
                        $cuota->selectRaw('1')->from('cuotas_cementerio as cc')
                            ->whereColumn('cc.id', 'o.cuotas_cementerio_id')->where('cc.status', 1);
                    });
                });
            if ($tipos) $operaciones->whereIn('o.empresa_operaciones_id', $tipos);
            if ($tipos) $consulta->getQuery()->addWhereExistsQuery(clone $operaciones);
            if ($adeudo !== '') {
                $aplicaciones = DB::table('pagos_pagos_programados as ppp')
                    ->join('pagos as p', 'p.id', '=', 'ppp.pagos_id')
                    ->where('p.status', 1)->whereIn('p.movimientos_pagos_id', [1, 3, 4, 5])
                    ->select('ppp.pagos_programados_id')->selectRaw('SUM(ppp.monto) AS capital_cubierto')
                    ->groupBy('ppp.pagos_programados_id');
                $pendientes = DB::table('pagos_programados as pp')
                    ->leftJoinSub($aplicaciones, 'a', function ($join) { $join->on('a.pagos_programados_id', '=', 'pp.id'); })
                    ->whereColumn('pp.operaciones_id', 'o.id')->where('pp.status', 1)
                    ->whereRaw('ROUND(pp.monto_programado - COALESCE(a.capital_cubierto, 0), 2) > 0')
                    ->selectRaw('1');
                if ($adeudo === 'vencido') $pendientes->where('pp.fecha_programada', '>=', '1000-01-01')->where('pp.fecha_programada', '<', $corte->toDateString());
                $conDeuda = (clone $operaciones)->addWhereExistsQuery($pendientes);
                $consulta->getQuery()->addWhereExistsQuery($conDeuda, 'and', $adeudo === 'sin');
            }
        }
        $seguimiento = $filtros['seguimientos'] ?? '';
        if ($seguimiento !== '') {
            $pendientes = DB::table('seguimientos as s')->selectRaw('1')
                ->whereColumn('s.clientes_id', 'clientes.id')->where('s.tipo_cliente_id', 1)
                ->where('s.programado_b', 1)->where('s.status', 1)->whereNull('s.fechahora_registro_seguimiento')
                ->whereNotExists(function ($op) {
                    $op->selectRaw('1')->from('operaciones as oc')->whereColumn('oc.id', 's.operaciones_id')->where('oc.status', 0);
                });
            if ($seguimiento === 'vencido') $pendientes->where('s.fechahora_programada', '>=', '1000-01-01')->where('s.fechahora_programada', '<', $corte->toDateString());
            $consulta->getQuery()->addWhereExistsQuery($pendientes, 'and', $seguimiento === 'sin');
        }
        return $consulta;
    }

    /** Solo recibe nombres internos de columnas, nunca datos del request. Compatible con MySQL 5.7. */
    private function telefonoNormalizadoClientes($campo)
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(clientes.$campo, ''), ' ', ''), '-', ''), '(', ''), ')', ''), '+', ''), '.', '')";
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

    /** Consulta histórica acotada al seguimiento y su cliente; no alimenta nuevas capturas. */
    private function datosClienteSeguimientoHistorico($seguimiento)
    {
        $tipo = (int) $seguimiento->tipo_cliente_id;
        if ($tipo === 1) {
            $cliente = DB::table('clientes')->where('id', $seguimiento->clientes_id)
                ->select('id','nombre','direccion','ciudad','estado','email')->first();
            $nombre = $cliente ? $cliente->nombre : 'Cliente no disponible';
            $direccion = $cliente ? implode(', ', array_filter([$cliente->direccion, $cliente->ciudad, $cliente->estado])) : 'No disponible';
        } else {
            $cliente = DB::table('cotizaciones')->where('id', $seguimiento->clientes_id)
                ->select('id','cliente_nombre as nombre','cliente_email as email','status')->first();
            $nombre = $cliente ? $cliente->nombre : 'Prospecto no disponible';
            $direccion = 'Cliente bajo cotización';
        }
        $datos = ['id' => $seguimiento->clientes_id, 'nombre' => $nombre, 'direccion_completa' => $direccion,
            'tipo_cliente_id' => $tipo, 'email' => $cliente ? mb_strtolower(trim($cliente->email ?? ''), 'UTF-8') : null,
            'operaciones' => [], 'consulta_historica' => true, 'cliente_disponible' => (bool) $cliente];
        if (!$seguimiento->operaciones_id) return $this->successResponse($datos, 200);
        if ($tipo === 2) {
            // No interpretar el ID de una cotización como operaciones.id.
            if ($cliente && (string) $cliente->id === (string) $seguimiento->operaciones_id) {
                $datos['operaciones'][] = ['operacion_id' => $cliente->id, 'descripcion' => 'COTIZACIÓN '.$cliente->id,
                    'saldo' => null, 'status' => $cliente->status];
            }
            return $this->successResponse($datos, 200);
        }
        // El vínculo y la pertenencia se verifican en servidor, incluso para operaciones canceladas.
        $op = DB::table('operaciones')->where('id', $seguimiento->operaciones_id)
            ->where('clientes_id', $seguimiento->clientes_id)
            ->select('id','empresa_operaciones_id','status','saldo','fecha_operacion','ventas_terrenos_id',
                'cuotas_cementerio_id','servicios_funerarios_id','ventas_planes_id','ventas_generales_id')->first();
        if (!$op) return $this->successResponse($datos, 200);
        $nombres = [1=>'VENTA DE ESPACIO EN CEMENTERIO',2=>'CUOTA DE MANTENIMIENTO',3=>'SERVICIO FUNERARIO',4=>'PLAN FUNERARIO',5=>'VENTA EN GENERAL'];
        $descripcion = $nombres[(int) $op->empresa_operaciones_id] ?? 'OPERACIÓN';
        if (in_array((int) $op->empresa_operaciones_id, [1,2], true)) {
            $codigo = DB::table('ventas_terrenos')->where('id', $op->ventas_terrenos_id)->value('ubicacion');
            $mapa = app(CementerioController::class)->getUbicacionesTextoReporte([$codigo]);
            $descripcion .= ' · '.($mapa[trim((string) $codigo)] ?? 'No disponible');
            if ((int) $op->empresa_operaciones_id === 2) $descripcion .= ' · '.(DB::table('cuotas_cementerio')->where('id',$op->cuotas_cementerio_id)->value('descripcion') ?: 'Cuota no disponible');
        } elseif ((int) $op->empresa_operaciones_id === 3) {
            $descripcion .= ' · '.(DB::table('servicios_funerarios')->where('id',$op->servicios_funerarios_id)->value('nombre_afectado') ?: 'Fallecido no disponible');
        } elseif ((int) $op->empresa_operaciones_id === 4) {
            $descripcion .= ' · '.(DB::table('ventas_planes')->where('id',$op->ventas_planes_id)->value('nombre_original') ?: 'Plan no disponible');
        }
        $datos['operaciones'][] = ['operacion_id'=>$op->id, 'descripcion'=>$descripcion,
            'saldo'=>'$'.number_format((float) $op->saldo,2,'.',',').' MXN', 'status'=>$op->status];
        return $this->successResponse($datos, 200);
    }

    public function get_clientes_seguimientos(Request $request)
    {
        $nameFilter            = trim($request->nombre ?? '');
        $idFilter              = trim($request->id ?? '');
        $tipoClienteFilter     = $request->tipo_cliente_id; // null, 1, or 2
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

        if ($request->has('seguimiento_historico_id')) {
            $request->validate(['seguimiento_historico_id' => 'required|integer|min:1']);
            if ($filtrar_x_operaciones !== '1' || !in_array($tipoClienteFilter, ['1','2'], true) || $idFilter <= 0 || $summary !== '') {
                return $this->errorResponse('Consulta histórica no válida.', 422);
            }
            // No confiar en operacion_id del navegador: resolver el vínculo almacenado.
            $seguimiento = Seguimientos::select('id','tipo_cliente_id','clientes_id','operaciones_id')
                ->where('id',$request->seguimiento_historico_id)->where('clientes_id',$idFilter)
                ->where('tipo_cliente_id',$tipoClienteFilter)->first();
            if (!$seguimiento) return $this->errorResponse('El seguimiento no corresponde al cliente indicado.', 404);
            return $this->datosClienteSeguimientoHistorico($seguimiento);
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
                        ])->where('status', '<>', 0)->orderBy('id', 'desc'); // 👈 order by operaciones.id desc
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
