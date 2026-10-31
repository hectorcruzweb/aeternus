<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Construye el JSON del dashboard y decide la vista gerencial u operativa desde el usuario autenticado.
 * Dónde ajustar: Agregar bloques agregados en buildDashboard; no cargar aquí el desglose de los reportes.
 * Relacionado: HomeInfo.vue; controladores de cada módulo; routes/api.php
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */


namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    protected $serviciosFunerarios;
    protected $inventario;
    protected $operaciones;

    // Reutilizamos los controladores de cada módulo; sus métodos devuelven datos.
    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $serviciosFunerarios (dato del contexto descrito en la firma); $inventario (dato del
     * contexto descrito en la firma); $operaciones (controlador inyectado que ejecuta la conciliación).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(
        FunerariaController $serviciosFunerarios,
        InventarioController $inventario,
        OperacionesController $operaciones
    ) {
        $this->serviciosFunerarios = $serviciosFunerarios;
        $this->inventario = $inventario;
        $this->operaciones = $operaciones;
    }

    /**
     * Flujo: autenticar -> resolver acceso -> obtener resúmenes permitidos -> JSON.
     * La ruta debe permanecer dentro del grupo auth:api.
     * Los detalles y exportaciones se consultarán mediante sus propias rutas.
     
     * Detalle del método: Resuelve permisos del usuario autenticado y reúne los indicadores permitidos con
     * la fecha de corte del servidor.
     * Entradas: $request (petición autenticada, filtros y/o datos de envío).
     * Salida / efectos: Respuesta JSON raíz con meta, acceso y bloques autorizados; 401 si no hay sesión.
     */
    public function buildDashboard(Request $request)
    {
        // El guard api obtiene al usuario autenticado; no leer roles del cliente.
        $usuario = $request->user('api');
        if (!$usuario) {
            return $this->errorResponse('Es necesario iniciar sesión.', 401);
        }

        // Admin 1 y Gerencia 4 pueden consultar los indicadores gerenciales.
        $vistaCompleta = in_array($usuario->roles_id, [1, '1', 4, '4'], true);
        $fechaCorte = Carbon::now('America/Mazatlan');

        $dashboard = [
            // Referencia del servidor: los filtros de gráfica iniciarán en este mes.
            'meta' => [
                'version' => 1,
                'generadoEn' => $fechaCorte->toIso8601String(),
                'zonaHoraria' => 'America/Mazatlan',
                'moneda' => 'MXN',
                'fechaCorte' => $fechaCorte->toDateString(),
                'periodoResumen' => [
                    'anio' => (int) $fechaCorte->format('Y'),
                    'mes' => (int) $fechaCorte->format('n'),
                    'desde' => $fechaCorte->copy()->startOfMonth()->toDateString(),
                    'hasta' => $fechaCorte->toDateString(),
                ],
            ],
            // La futura vista con skeleton usará este bloque, no localStorage.
            'acceso' => [
                'vista' => $vistaCompleta ? 'gerencial' : 'operativa',
                'secciones' => ['servicios_funerarios'],
            ],
            // Conserva la consulta y estructura del carrusel actualmente funcional.
            'servicios_funerarios' => $this->serviciosFunerarios->getServiciosFunerariosDashboard(),
        ];

        // No ejecutar la consulta ni devolver el bloque si el perfil es restringido.
        if ($vistaCompleta) {
            $dashboard['inventario'] = $this->inventario->getInventarioDashboard();
            // Campo adicional del card: fecha del ajuste más reciente hasta este corte.
            $dashboard['inventario']['ultimoAjuste'] = $this->inventario
                ->getUltimoAjusteInventarioDashboard($fechaCorte);
            $dashboard['acceso']['secciones'][] = 'inventario';

            // null: los cinco tipos. El mismo corte fija el mes y el vencido histórico.
            // Solo resúmenes: no cargar ventas individuales ni pagos del reporte.
            $dashboard['ventasSaldos'] = $this->operaciones
                ->getVentasSaldosOperacionesDashboard(null, null, null, $fechaCorte);
            $dashboard['acceso']['secciones'][] = 'ventasSaldos';

            // Solo el agregado real de timbres; el historial se consulta al abrir su reporte.
            $dashboard['facturacion'] = app(FacturacionController::class)->getTimbresDashboard($fechaCorte);
            $dashboard['facturacion'] = array_merge($dashboard['facturacion'],
                app(FacturacionController::class)->getCfdisAdeudosDashboard($fechaCorte),
                app(FacturacionController::class)->getFacturacionMesDashboard($fechaCorte),
                app(FacturacionController::class)->getVigenciaFirmaDashboard());
            $dashboard['acceso']['secciones'][] = 'facturacion';

            // Tres indicadores agregados, sin cargar contactos o registros individuales.
            $solicitudes = $this->serviciosFunerarios->getSolicitudesSinContratoDashboard($fechaCorte);
            $dashboard['pendientes'] = [
                'venceHoy' => $this->operaciones->getPagosVenceHoyDashboard($fechaCorte),
                'proximosSieteDias' => $this->operaciones->getPagosProximosSieteDiasDashboard($fechaCorte),
                'solicitudesSinContrato' => $solicitudes['recientes'],
                'solicitudesAntiguasSinContrato' => $solicitudes['antiguas'],
                'solicitudesSinContratoAcumuladas' => $solicitudes['total'],
                'diasSolicitudesRecientes' => $solicitudes['dias'],
            ];
            $dashboard['acceso']['secciones'][] = 'pendientes';
            // Conteos de realizados, agenda e histórico; no cargar el detalle en el dashboard.
            $dashboard['seguimientos'] = [
                'clientesConPagosVencidos' => $this->operaciones->getClientesPagosVencidosDashboard($fechaCorte),
                'realizadosMes' => app(SeguimientosController::class)->getSeguimientosRealizadosMesDashboard($fechaCorte),
                'vencidos' => app(SeguimientosController::class)->getSeguimientosVencidosHistoricoDashboard($fechaCorte),
                'programadosProximosSieteDias' => app(SeguimientosController::class)->getProgramadosProximosSieteDiasDashboard($fechaCorte),
            ];
            $dashboard['acceso']['secciones'][] = 'seguimientos';
        }

        // Agregaremos otros bloques aquí cuando sus reglas y consultas estén listas.
        // successResponse ya entrega el objeto raíz: no envolverlo en otra clave data.
        return $this->successResponse($dashboard);
    }
}
