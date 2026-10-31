<template>
  <div class="dashboard" :aria-busy="isLoading ? 'true' : 'false'">
    <!-- Configuración de temporada siempre visible para el programador. -->
    <MensajeTemporada
      :habilitado="false && !isLoading"
      :modo-desarrollador="false"
      :una-vez-por-sesion="false"
      :duracion="4000"
    />

    <div ref="accionesDashboard" class="dashboard__acciones dashboard__acciones--superiores"
      :class="{ 'dashboard__acciones--fijadas': accionesDashboardFijadas }" aria-label="Acciones del dashboard">
      <div class="dashboard-acciones-superiores__grupo">
        <button type="button" class="dashboard-accion dashboard-accion--reporte"
          :disabled="isLoading || !dashboardDisponible" @click="solicitarResumenDia()">
          <FileTextIcon size="16" aria-hidden="true" /> Resumen del día · PDF
        </button>
        <button type="button" class="dashboard-accion dashboard-accion--actualizar"
          :disabled="isLoading" @click="recargarDashboard()">
          <RefreshCwIcon size="16" aria-hidden="true" :class="{ 'dashboard-accion__recargando': isLoading }" />
          {{ isLoading ? "Actualizando…" : "Actualizar datos" }}
        </button>
      </div>
    </div>

    <p v-if="errorDashboard" class="dashboard__error" role="alert">
      {{ errorDashboard }}
    </p>

    <!-- Las cards dibujan sus skeletons sin montar importes ni acciones reales. -->
    <template v-if="mostrarVistaGerencial">
      <div class="dashboard__operacion">
        <ServiciosFunerarios
          :cargando="isLoading"
          v-if="isLoading || tieneSeccion('servicios_funerarios')"
          :data="serviciosFunerarios"
        />
        <Inventario
          :cargando="isLoading"
          v-if="isLoading || tieneSeccion('inventario')"
          :data="inventario"
          :ultimo-ajuste="inventario.ultimoAjuste"
        />
      </div>

      <!-- El carrusel muestra las filas recibidas, incluido el general del backend. -->
      <section
        class="dashboard__resumen"
        :class="{
          'dashboard__resumen--solo':
            !isLoading && !tieneSeccion('ventasSaldos'),
        }"
        aria-label="Ventas, saldos y facturación"
      >
        <VentasSaldos
          :cargando="isLoading"
          v-if="isLoading || tieneSeccion('ventasSaldos')"
          :operaciones="ventasSaldos"
          :periodo-resumen="periodoResumen"
          @ver-reporte="prepararReporte"
        />
        <Facturacion
          :cargando="isLoading"
          :facturacion="facturacion"
          :periodo-resumen="periodoResumen"
          @ver-reporte="prepararReporte"
        />
      </section>

      <!-- Indicadores de pagos y solicitudes con resúmenes reales del servidor. -->
      <PendientesResumen
        :cargando="isLoading"
        :pendientes="pendientes"
        @ver-reporte="prepararReporte"
      />
      <SeguimientosResumen
        :cargando="isLoading"
        :seguimientos="seguimientos"
        :periodo-resumen="periodoResumen"
        @ver-reporte="prepararReporte"
      />
      <VentasCobrosGrafica
        :cargando="isLoading || cargandoGrafica"
        :error="errorGrafica"
        :datos="datosGrafica"
        :periodo="periodoGrafica"
        :anio-actual="fechaReferencia.anio"
        :mes-actual="fechaReferencia.mes"
        @cambiar-periodo="cambiarPeriodoGrafica"
        @reintentar="cargarGrafica"
        @ver-reporte="prepararReporte"
      />
    </template>

    <VistaLimitada v-else-if="isLoading || dashboardDisponible">
      <ServiciosFunerarios
        :cargando="isLoading"
        v-if="isLoading || tieneSeccion('servicios_funerarios')"
        :data="serviciosFunerarios"
      />
    </VistaLimitada>
    <VisorReportes v-if="mostrarReporteDia" :show="mostrarReporteDia" :cargar="cargarReporteDia" :descargar-archivo="descargarDiaExcel" :descargar-pdf="descargarDiaPdf" :enviar-correo="enviarDiaCorreo" @close="mostrarReporteDia=false" />
    <VisorReportes v-if="mostrarReporteClientesPagosVencidos" :show="mostrarReporteClientesPagosVencidos"
      :cargar="cargarReporteClientesPagosVencidos" :descargar-archivo="descargarClientesPagosVencidosExcel"
      :descargar-pdf="descargarClientesPagosVencidosPdf" :enviar-correo="enviarClientesPagosVencidosCorreo"
      @close="mostrarReporteClientesPagosVencidos = false" />
    <VisorReportes v-if="mostrarReporteSeguimientosVencidos" :show="mostrarReporteSeguimientosVencidos"
      :cargar="cargarReporteSeguimientosVencidos" :descargar-archivo="descargarSeguimientosVencidosExcel"
      :descargar-pdf="descargarSeguimientosVencidosPdf" :enviar-correo="enviarSeguimientosVencidosCorreo"
      @close="mostrarReporteSeguimientosVencidos = false" />
    <VisorReportes v-if="mostrarReporteSeguimientosProgramados" :show="mostrarReporteSeguimientosProgramados"
      :cargar="cargarReporteSeguimientosProgramados" :descargar-archivo="descargarSeguimientosProgramadosExcel"
      :descargar-pdf="descargarSeguimientosProgramadosPdf" :enviar-correo="enviarSeguimientosProgramadosCorreo"
      @close="mostrarReporteSeguimientosProgramados = false" />
    <VisorReportes v-if="mostrarReporteSeguimientosRealizados" :show="mostrarReporteSeguimientosRealizados"
      :cargar="cargarReporteSeguimientosRealizados" :descargar-archivo="descargarSeguimientosRealizadosExcel"
      :descargar-pdf="descargarSeguimientosRealizadosPdf" :enviar-correo="enviarSeguimientosRealizadosCorreo"
      @close="mostrarReporteSeguimientosRealizados = false" />
    <VisorReportes v-if="mostrarReporteSolicitudes" :show="mostrarReporteSolicitudes"
      :cargar="cargarReporteSolicitudes" :descargar-archivo="descargarSolicitudesExcel"
      :descargar-pdf="descargarSolicitudesPdf" :enviar-correo="enviarSolicitudesCorreo"
      @close="mostrarReporteSolicitudes = false" />
    <VisorReportes v-if="mostrarReporteProximosPagos" :show="mostrarReporteProximosPagos"
      :cargar="cargarReporteProximosPagos" :descargar-archivo="descargarProximosPagosExcel"
      :descargar-pdf="descargarProximosPagosPdf" :enviar-correo="enviarProximosPagosCorreo"
      @close="mostrarReporteProximosPagos = false" />
    <!-- Detalle de Vence hoy: se consulta únicamente al abrir el visor. -->
    <VisorReportes v-if="mostrarReporteVenceHoy" :show="mostrarReporteVenceHoy"
      :cargar="cargarReporteVenceHoy" :descargar-archivo="descargarVenceHoyExcel"
      :descargar-pdf="descargarVenceHoyPdf" :enviar-correo="enviarVenceHoyCorreo"
      @close="mostrarReporteVenceHoy = false" />
    <VisorReportes v-if="mostrarReporteCfdisMes" :show="mostrarReporteCfdisMes"
      :cargar="cargarReporteCfdisMes" :descargar-archivo="descargarCfdisMesExcel"
      :descargar-pdf="descargarCfdisMesPdf" :enviar-correo="enviarCfdisMesCorreo"
      @close="mostrarReporteCfdisMes = false" />
    <!-- Adeudo acumulado: consultar el detalle únicamente al abrir su reporte. -->
    <VisorReportes v-if="mostrarReporteCfdisAdeudos" :show="mostrarReporteCfdisAdeudos"
      :cargar="cargarReporteCfdisAdeudos" :descargar-archivo="descargarCfdisAdeudosExcel"
      :descargar-pdf="descargarCfdisAdeudosPdf" :enviar-correo="enviarCfdisAdeudosCorreo"
      @close="mostrarReporteCfdisAdeudos = false" />
    <!-- Recargas e historial bajo demanda, sin cargar CFDI al abrir el dashboard. -->
    <VisorReportes
      v-if="mostrarReporteTimbres"
      ref="visorTimbres"
      :show="mostrarReporteTimbres"
      :cargar="cargarReporteTimbres"
      :descargar-archivo="descargarTimbresExcel"
      :descargar-pdf="descargarTimbresPdf"
      :enviar-correo="enviarTimbresCorreo"
      :descargar-completo="recargaReporteTimbres === null ? descargarTimbresCompleto : null"
      :mostrar-volver-resumen="recargaReporteTimbres !== null"
      @abrir-relacionado="abrirHistorialRecarga"
      @volver-resumen="volverRecargasTimbres"
      @close="cerrarReporteTimbres"
    />
    <!-- Reporte del slide seleccionado; el visor no carga detalles hasta abrirse. -->
    <VisorReportes
      v-if="mostrarReporteVentas"
      ref="visorVentas"
      :show="mostrarReporteVentas"
      :cargar="cargarReporteVentas"
      :descargar-archivo="descargarVentasExcel"
      :descargar-pdf="descargarVentasPdf"
      :enviar-correo="enviarVentasCorreo"
      :descargar-completo="(esReporteGeneral || esResumenCuotas) ? descargarVentasCompleto : null"
      :mostrar-volver-resumen="(reporteDesdeGeneral && !esReporteGeneral) || esDetalleCuota"
      @abrir-reporte="abrirReporteDesdeGeneral"
      @abrir-relacionado="abrirDetalleCuota"
      @volver-resumen="volverReporteGeneral"
      @close="cerrarReporteVentas"
    />
  </div>
</template>

<script>
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Coordina carga del dashboard, permisos recibidos, filtros de gráfica y apertura de todos los visores.
 * Dónde ajustar: _fetchDashboard carga el JSON; cambiarPeriodoGrafica/cargarGrafica actualizan solo la
 * gráfica; programarActualizacionInactiva actualiza tras 5 minutos sin actividad. Las acciones superiores
 * siguen el scroll y cambian de fondo al fijarse.
 * Relacionado: services/dashboard.js; dashboard/*.vue; VisorReportes.vue; _dashboard.scss
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

import { DownloadIcon, RefreshCwIcon, FileTextIcon } from "vue-feather-icons";
import VistaLimitada from "./dashboard/VistaLimitada.vue";
import MensajeTemporada from "./dashboard/MensajeTemporada.vue";
import VentasSaldos from "./dashboard/VentasSaldos.vue";
import Facturacion from "./dashboard/Facturacion.vue";
import SeguimientosResumen from "./dashboard/SeguimientosResumen.vue";
import PendientesResumen from "./dashboard/PendientesResumen.vue";
import VentasCobrosGrafica from "./dashboard/VentasCobrosGrafica.vue";
import ServiciosFunerarios from "./dashboard/ServiciosFunerarios.vue";
import Inventario from "./dashboard/Inventario.vue";
import reportesResumenDia from "../services/reportesResumenDia";
import dashboard from "../services/dashboard";
import VisorReportes from "./pages/VisorReportes.vue";
import reportesVentas from "../services/reportesVentas";
import reportesVenceHoy from "../services/reportesVenceHoy";
import reportesProximosPagos from "../services/reportesProximosPagos";
import reportesSolicitudesSinContrato from "../services/reportesSolicitudesSinContrato";
import reportesSeguimientosRealizados from "../services/reportesSeguimientosRealizados";
import reportesClientesPagosVencidos from "../services/reportesClientesPagosVencidos";
import reportesSeguimientosVencidos from "../services/reportesSeguimientosVencidos";
import reportesSeguimientosProgramados from "../services/reportesSeguimientosProgramados";
import reportesTimbres from "../services/reportesTimbres";
import reportesCfdisAdeudos from "../services/reportesCfdisAdeudos";
import reportesCfdisMes from "../services/reportesCfdisMes";

export default {
  name: "Dashboard",
  components: {
    VisorReportes,
    VistaLimitada,
    FileTextIcon, DownloadIcon,
    RefreshCwIcon,
    SeguimientosResumen,
    MensajeTemporada,
    ServiciosFunerarios,
    Inventario,
    VentasSaldos,
    Facturacion,
    PendientesResumen,
    VentasCobrosGrafica,
  },
  /**
   * Detalle del método: Devuelve el estado reactivo inicial por instancia de HomeInfo; no consulta al
   * servidor por sí mismo.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  data() {
    return {
      isLoading: false,
      errorDashboard: "",
      dashboardDisponible: false,
      vistaCarga: null, // Solo conserva el diseño previo durante la recarga, no sus datos.
      acceso: { vista: null, secciones: [] },
      meta: null,
      serviciosFunerarios: [],
      inventario: {},
      ventasSaldos: [],
      periodoResumen: "",
      fechaReferencia: { anio: null, mes: null },
      periodoGrafica: { anio: null, mes: null },
      // Ejemplos independientes; se cargan al confirmar la vista gerencial.
      facturacion: {},
      pendientes: {},
      seguimientos: {},
      operacionesGrafica: [],
      cargandoGrafica: false,
      errorGrafica: "",
      accionesDashboardFijadas: false,
      mostrarReporteDia: false,
      mostrarReporteClientesPagosVencidos: false,
      mostrarReporteSeguimientosVencidos: false,
      mostrarReporteSeguimientosProgramados: false,
      mostrarReporteSeguimientosRealizados: false,
      mostrarReporteSolicitudes: false,
      mostrarReporteProximosPagos: false,
      mostrarReporteVenceHoy: false,
      mostrarReporteCfdisMes: false,
      mostrarReporteCfdisAdeudos: false,
      mostrarReporteTimbres: false,
      recargaReporteTimbres: null,
      mostrarReporteVentas: false,
      reporteDesdeGeneral: false,
      parametrosReporteVentas: null,
      reportePendiente: null,
    };
  },
  computed: {
    /**
     * Detalle del método: Indica si se muestra el resumen del tipo 2 sin una cuota individual elegida.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    esResumenCuotas() { return !!this.parametrosReporteVentas && Number(this.parametrosReporteVentas.empresa_operaciones_id) === 2 && this.parametrosReporteVentas.cuota_id == null; },
    /**
     * Detalle del método: Indica si el contexto del visor corresponde a una cuota concreta.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    esDetalleCuota() { return !!this.parametrosReporteVentas && Number(this.parametrosReporteVentas.empresa_operaciones_id) === 2 && this.parametrosReporteVentas.cuota_id != null; },
    /**
     * Detalle del método: Indica si el reporte seleccionado es todas las operaciones.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    esReporteGeneral() { return !!this.parametrosReporteVentas && this.parametrosReporteVentas.empresa_operaciones_id === "todas"; },
    /**
     * Detalle del método: Decide la estructura visual durante carga y según la vista autorizada por el
     * servidor.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    mostrarVistaGerencial() {
      // Al inicio solo hay formas neutras; el servidor decide la vista con datos.
      return this.isLoading
        ? this.vistaCarga !== "operativa"
        : this.dashboardDisponible && this.puedeVerDashboardCompleto;
    },
    /**
     * Detalle del método: Comprueba el acceso gerencial recibido del backend para habilitar acciones e
     * indicadores.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    puedeVerDashboardCompleto() {
      return this.dashboardDisponible && this.acceso.vista === "gerencial";
    },
    // Fuente real; el filtro mensual nunca modifica las cards del resumen inicial.
    /**
     * Detalle del método: Devuelve las filas de operacionesGrafica sin recalcular los importes.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    datosGrafica() { return this.operacionesGrafica; },
  },
  methods: {
    // Cinco minutos sin interacción: actualizar y volver a contar mientras siga inactivo.
    // Mantenimiento: Reinicia el temporizador de 5 minutos desde la última actividad; no es una recarga periódica que
    // interrumpa al usuario activo.
    /**
     * Detalle del método: Reinicia el temporizador de 5 minutos desde la última actividad; todavía no
     * consulta datos.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    programarActualizacionInactiva() {
      clearTimeout(this._temporizadorInactividad);
      if(this._dashboardDestruido)return;
      this._temporizadorInactividad=setTimeout(()=>this.actualizarPorInactividad(),5*60*1000);
    },
    // Mantenimiento: Evita recargar con reportes abiertos u otra carga en curso, conserva el periodo elegido de la
    // gráfica y reprograma el temporizador.
    /**
     * Detalle del método: Pospone la recarga si hay reportes abiertos u otra carga; de lo contrario
     * actualiza y conserva el periodo de la gráfica. Devuelve una promesa.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async actualizarPorInactividad() {
      const reporteAbierto=Object.keys(this.$data).some(k=>k.indexOf("mostrarReporte")===0 && this[k]===true);
      if(this.isLoading || this.cargandoGrafica || reporteAbierto || !this.dashboardDisponible){
        this.programarActualizacionInactiva();return;
      }
      const periodo=Object.assign({},this.periodoGrafica);
      try {
        await this._fetchDashboard();
        if(!this._dashboardDestruido && this.puedeVerDashboardCompleto && this.periodoGraficaValido(periodo) &&
          (periodo.anio!==this.periodoGrafica.anio || periodo.mes!==this.periodoGrafica.mes)) await this.cambiarPeriodoGrafica(periodo);
      } finally { this.programarActualizacionInactiva(); }
    },
    /**
     * Detalle del método: Devuelve si el JSON válido del backend autoriza la sección indicada.
     * Entradas: nombre (clave de sección que se desea comprobar).
     */
    tieneSeccion(nombre) {
      return (
        this.dashboardDisponible && this.acceso.secciones.indexOf(nombre) !== -1
      );
    },
    // Limpiar al recargar o fallar: nunca dejar importes/permisos de una sesión anterior.
    /**
     * Detalle del método: Vacía datos y permisos anteriores e invalida peticiones tardías de la gráfica
     * para no mostrar importes obsoletos.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    limpiarDashboard() {
      this.dashboardDisponible = false;
      this.acceso = { vista: null, secciones: [] };
      this.meta = null;
      this.serviciosFunerarios = [];
      this.inventario = {};
      this.ventasSaldos = [];
      this.periodoResumen = "";
      this.fechaReferencia = { anio: null, mes: null };
      this.periodoGrafica = { anio: null, mes: null };
      this.facturacion = {};
      this.pendientes = {};
      this.seguimientos = {};
      this.operacionesGrafica = [];
      this.cargandoGrafica = false;
      this.errorGrafica = "";
      this._graficaSolicitud = (this._graficaSolicitud || 0) + 1;
      this.reportePendiente = null;
    },
    // Validar la respuesta completa antes de publicar el estado del dashboard.
    /**
     * Detalle del método: Valida el contrato del JSON antes de actualizar cards y periodo. Lanza error si
     * falta acceso, metadatos o bloques requeridos.
     * Entradas: datos (respuesta del módulo o datos confirmados del envío, según el método).
     */
    aplicarDatosDashboard(datos) {
      const acceso = datos && datos.acceso;
      const meta = datos && datos.meta;
      const periodo = meta && meta.periodoResumen;
      const anio = periodo && Number(periodo.anio);
      const mes = periodo && Number(periodo.mes);
      if (
        !acceso ||
        ["gerencial", "operativa"].indexOf(acceso.vista) === -1 ||
        !Array.isArray(acceso.secciones) ||
        !meta ||
        !Number.isInteger(anio) ||
        anio < 1000 ||
        !Number.isInteger(mes) ||
        mes < 1 ||
        mes > 12 ||
        typeof meta.fechaCorte !== "string"
      ) {
        throw new Error(
          "La respuesta del dashboard no contiene acceso y periodo válidos."
        );
      }
      const completa = acceso.vista === "gerencial";
      const secciones = completa
        ? acceso.secciones.slice()
        : acceso.secciones.filter(
            (nombre) => nombre === "servicios_funerarios"
          );
      const incluye = (nombre) => secciones.indexOf(nombre) !== -1;
      if (
        incluye("servicios_funerarios") &&
        !Array.isArray(datos.servicios_funerarios)
      ) {
        throw new Error("No se recibió el resumen de servicios funerarios.");
      }
      if (
        incluye("inventario") &&
        (!datos.inventario ||
          typeof datos.inventario !== "object" ||
          Array.isArray(datos.inventario))
      ) {
        throw new Error("No se recibió el resumen de inventario.");
      }
      if (incluye("ventasSaldos") && !Array.isArray(datos.ventasSaldos)) {
        throw new Error("No se recibió el resumen de ventas y saldos.");
      }

      this.meta = meta;
      this.acceso = { vista: acceso.vista, secciones };
      this.fechaReferencia = { anio, mes };
      this.periodoGrafica = { anio, mes };
      // La fecha solo se construye para dar formato al mes enviado por el servidor.
      this.periodoResumen = new Date(
        Date.UTC(anio, mes - 1, 1)
      ).toLocaleDateString("es-MX", {
        month: "long",
        year: "numeric",
        timeZone: "UTC",
      });
      this.serviciosFunerarios = incluye("servicios_funerarios")
        ? datos.servicios_funerarios
        : [];
      this.inventario = incluye("inventario") ? datos.inventario : {};
      this.ventasSaldos = incluye("ventasSaldos") ? datos.ventasSaldos : [];
      // Mes actual: reutilizar el resumen ya recibido sin otra petición ni consulta SQL.
      this.operacionesGrafica = this.ventasSaldos.filter(f => f.id !== "todas").map(f => Object.assign({}, f));
      if (completa) {
        this.cargarEjemplosPendientes();
        this.seguimientos.realizadosMes = incluye("seguimientos") && datos.seguimientos && Object.prototype.hasOwnProperty.call(datos.seguimientos, "realizadosMes")
          ? datos.seguimientos.realizadosMes : null;
        this.seguimientos.clientesConPagosVencidos = incluye("seguimientos") && datos.seguimientos && Object.prototype.hasOwnProperty.call(datos.seguimientos, "clientesConPagosVencidos")
          ? datos.seguimientos.clientesConPagosVencidos : null;
        this.seguimientos.vencidos = incluye("seguimientos") && datos.seguimientos && Object.prototype.hasOwnProperty.call(datos.seguimientos, "vencidos")
          ? datos.seguimientos.vencidos : null;
        this.seguimientos.programadosProximosSieteDias = incluye("seguimientos") && datos.seguimientos && Object.prototype.hasOwnProperty.call(datos.seguimientos, "programadosProximosSieteDias")
          ? datos.seguimientos.programadosProximosSieteDias : null;
        // Vence hoy ya es real: no conservar valores de ejemplo si falta el bloque.
        this.pendientes.venceHoy = incluye("pendientes") && datos.pendientes && datos.pendientes.venceHoy
          ? datos.pendientes.venceHoy : { cantidad: null, importe: null };
        this.pendientes.proximosSieteDias = incluye("pendientes") && datos.pendientes && datos.pendientes.proximosSieteDias
          ? datos.pendientes.proximosSieteDias : { cantidad: null, importe: null };
        ["solicitudesSinContrato", "solicitudesAntiguasSinContrato", "solicitudesSinContratoAcumuladas", "diasSolicitudesRecientes"].forEach(campo => {
          this.pendientes[campo] = incluye("pendientes") && datos.pendientes && Object.prototype.hasOwnProperty.call(datos.pendientes, campo)
            ? datos.pendientes[campo] : null;
        });
        // Todos los indicadores de facturación y la vigencia provienen del servidor.
        this.facturacion.timbresDisponibles = datos.facturacion &&
          Object.prototype.hasOwnProperty.call(datos.facturacion, "timbresDisponibles")
          ? datos.facturacion.timbresDisponibles : null;
        ["saldoPendiente", "facturasPendientes", "adeudosCorte", "adeudosMoneda", "ventasFacturadas", "saldadoFacturado", "cantidadFacturasMes", "fechaCaducidadFiel"].forEach(campo => {
          this.facturacion[campo] = datos.facturacion && Object.prototype.hasOwnProperty.call(datos.facturacion, campo)
            ? datos.facturacion[campo] : null;
        });
      }
      this.dashboardDisponible = true;
    },
    // Maqueta autorizada: retirar cada bloque al integrar su fuente real.
    /**
     * Detalle del método: Inicializa valores de maqueta heredados; aplicarDatosDashboard los sustituye
     * después por los indicadores reales integrados. No hace consultas.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    cargarEjemplosPendientes() {
      this.facturacion = {
        fechaCaducidadFiel: null,
        timbresDisponibles: 86,
        facturasPendientes: 12,
        saldoPendiente: 28400,
        ventasFacturadas: 425000,
      };
      this.seguimientos = {
        realizadosMes: null,
        programadosProximosSieteDias: null,
        vencidos: null,
        clientesConPagosVencidos: null,
      };
      this.pendientes = {
        venceHoy: { cantidad: null, importe: null },
        proximosSieteDias: { cantidad: null, importe: null },
        solicitudesSinContrato: null,
        solicitudesAntiguasSinContrato: null,
        solicitudesSinContratoAcumuladas: null,
        diasSolicitudesRecientes: null,
      };

    },
    /**
     * Detalle del método: Devuelve la promesa de la carga principal invocada por Actualizar datos.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    recargarDashboard() {
      return this._fetchDashboard();
    },
    // Actualizar datos mostrando los skeletons propios del dashboard.
    // Mantenimiento: Carga raíz y aplica acceso.vista/secciones del backend; muestra skeletons y evita peticiones
    // simultáneas. Las descargas del visor tienen su propio estado y no deben controlar la sesión del
    // sistema.
    /**
     * Detalle del método: Consulta el JSON raíz, gestiona carga/errores y aplica datos autorizados.
     * Devuelve la promesa de la consulta.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async _fetchDashboard() {
      if (this.isLoading) return;
      this.vistaCarga = this.acceso.vista;
      this.isLoading = true;
      this.errorDashboard = "";
      this.limpiarDashboard();
      // Los skeletons indican la carga sin insertar capas sobre el menú flotante.
      try {
        const response = await dashboard._fetchDashboard();
        if (this._dashboardDestruido) return;
        // successResponse devuelve el objeto raíz directamente en response.data.
        this.aplicarDatosDashboard(response.data);
      } catch (error) {
        if (this._dashboardDestruido) return;
        this.limpiarDashboard();
        this.errorDashboard =
          "No fue posible actualizar el dashboard. Intenta recargarlo.";
        if (typeof this.$error === "function")
          this.$error("Error al obtener el dashboard:", error);
      } finally {
        this.isLoading = false;
      }
    },
    /**
     * Detalle del método: Abre el visor diario solo con datos disponibles y acceso gerencial; todavía no
     * descarga el PDF.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    solicitarResumenDia() { if(!this.isLoading && this.puedeVerDashboardCompleto)this.mostrarReporteDia=true; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte Dia usando
     * su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteDia(){return (await reportesResumenDia.visualizar()).data;},
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte. Compatibilidad heredada: el resumen diario solo admite PDF y no debe habilitarse esta
     * descarga Excel.
     * Entradas: t (token privado del reporte).
     */
    async descargarDiaExcel(t){return (await reportesResumenDia.excel(t)).data;},
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: t (token privado del reporte).
     */
    async descargarDiaPdf(t){return (await reportesResumenDia.pdf(t)).data;},
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: t (token privado del reporte); d (datos confirmados del envío).
     */
    async enviarDiaCorreo(t,d){return (await reportesResumenDia.correo(t,d)).data;},
    /**
     * Detalle del método: Interpreta el contexto emitido por cada card y abre el visor correcto con tipo y
     * periodo seleccionados.
     * Entradas: contexto (tipo, periodo y origen emitidos por la card).
     */
    prepararReporte(contexto) {
      if (this.isLoading || !this.puedeVerDashboardCompleto || !contexto)
        return;
      this.reportePendiente = Object.assign({}, contexto, {
        fechaCorte: this.meta.fechaCorte,
        periodoResumen: Object.assign({}, this.meta.periodoResumen),
      });
      if (contexto.tipo === "seguimientos-sugeridos") {
        this.mostrarReporteClientesPagosVencidos = true;
        return;
      }
      if (contexto.tipo === "seguimientos-vencidos") {
        this.mostrarReporteSeguimientosVencidos = true;
        return;
      }
      if (contexto.tipo === "seguimientos-programados") {
        this.mostrarReporteSeguimientosProgramados = true;
        return;
      }
      if (contexto.tipo === "seguimientos-realizados") {
        this.mostrarReporteSeguimientosRealizados = true;
        return;
      }
      if (contexto.tipo === "solicitudes-sin-contrato") {
        this.mostrarReporteSolicitudes = true;
        return;
      }
      if (contexto.tipo === "pagos-proximos-siete-dias") {
        this.mostrarReporteProximosPagos = true;
        return;
      }
      if (contexto.tipo === "pagos-vence-hoy") {
        this.mostrarReporteVenceHoy = true;
        return;
      }
      if (contexto.tipo === "facturacion-ventas") {
        this.mostrarReporteCfdisMes = true;
        return;
      }
      if (contexto.tipo === "facturacion-facturas") {
        this.mostrarReporteCfdisAdeudos = true;
        return;
      }
      if (contexto.tipo === "facturacion-timbres") {
        this.recargaReporteTimbres = null;
        this.mostrarReporteTimbres = true;
        return;
      }
      if (contexto.tipo === "ventas-saldos-periodo") {
        const esGeneral=contexto.empresa_operaciones_id === "todas";
        const tipo=esGeneral ? "todas" : Number(contexto.empresa_operaciones_id);
        if (this.cargandoGrafica || this.errorGrafica || !this.periodoGraficaValido(contexto) ||
            (!esGeneral && !this.datosGrafica.some(f => Number(f.id) === tipo)) ||
            Number(contexto.anio) !== this.periodoGrafica.anio || Number(contexto.mes) !== this.periodoGrafica.mes) return;
        this.parametrosReporteVentas={empresa_operaciones_id:tipo,anio:this.periodoGrafica.anio,mes:this.periodoGrafica.mes,vencido_hasta_periodo:1};
        this.reporteDesdeGeneral=esGeneral;
        this.mostrarReporteVentas=true;
        return;
      }
      if (contexto.tipo === "ventas-saldos") {
        if (contexto.empresa_operaciones_id == null || [1, 2, 3, 4, 5].includes(Number(contexto.empresa_operaciones_id))) {
          // El mes es el del resumen recibido del servidor, no el de la gráfica.
          this.parametrosReporteVentas = {
            empresa_operaciones_id: contexto.empresa_operaciones_id == null ? "todas" : Number(contexto.empresa_operaciones_id),
            anio: this.meta.periodoResumen.anio,
            mes: this.meta.periodoResumen.mes,
          };
          this.reporteDesdeGeneral = contexto.empresa_operaciones_id == null;
          this.mostrarReporteVentas = true;
        } else {
          this.$vs.notify({ title: "Reporte pendiente", text: "El reporte de Todas las operaciones todavía no está disponible. Selecciona un tipo de operación.", color: "warning" });
        }
      }
    },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte CfdisMes
     * usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteCfdisMes() { return (await reportesCfdisMes.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarCfdisMesExcel(token) { return (await reportesCfdisMes.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarCfdisMesPdf(token) { return (await reportesCfdisMes.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarCfdisMesCorreo(token, datos) { return (await reportesCfdisMes.correo(token, datos)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte
     * CfdisAdeudos usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteCfdisAdeudos() { return (await reportesCfdisAdeudos.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarCfdisAdeudosExcel(token) { return (await reportesCfdisAdeudos.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarCfdisAdeudosPdf(token) { return (await reportesCfdisAdeudos.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarCfdisAdeudosCorreo(token, datos) { return (await reportesCfdisAdeudos.correo(token, datos)).data; },
    /**
     * Detalle del método: Cierra el visor de timbres y limpia el lote seleccionado para volver al resumen
     * al reabrir.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    cerrarReporteTimbres() { this.mostrarReporteTimbres = false; this.recargaReporteTimbres = null; },
    /**
     * Detalle del método: Selecciona el lote autorizado solicitado desde el resumen y pide su vista de
     * consumos al visor.
     * Entradas: id (ID del elemento seleccionado en este contexto).
     */
    abrirHistorialRecarga(id) {
      const coincidencia = /^recarga-([1-9]\d*)$/.exec(String(id));
      if (!this.puedeVerDashboardCompleto || !coincidencia) return;
      this.recargaReporteTimbres = Number(coincidencia[1]);
      this.$nextTick(() => { if (this.$refs.visorTimbres) this.$refs.visorTimbres.consultar(); });
    },
    /**
     * Detalle del método: Quita la selección de lote y recarga el resumen de recargas dentro del mismo
     * visor.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    volverRecargasTimbres() {
      this.recargaReporteTimbres = null;
      this.$nextTick(() => { if (this.$refs.visorTimbres) this.$refs.visorTimbres.consultar(); });
    },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte Timbres
     * usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteTimbres() { return (await reportesTimbres.visualizar(this.recargaReporteTimbres)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarTimbresExcel(token) { return (await reportesTimbres.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarTimbresCompleto(token) { return (await reportesTimbres.excel(token, true)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarTimbresPdf(token) { return (await reportesTimbres.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarTimbresCorreo(token, datos) { return (await reportesTimbres.correo(token, datos)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte
     * ClientesPagosVencidos usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteClientesPagosVencidos() { return (await reportesClientesPagosVencidos.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarClientesPagosVencidosExcel(token) { return (await reportesClientesPagosVencidos.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarClientesPagosVencidosPdf(token) { return (await reportesClientesPagosVencidos.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarClientesPagosVencidosCorreo(token, datos) { return (await reportesClientesPagosVencidos.correo(token, datos)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte
     * SeguimientosVencidos usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteSeguimientosVencidos() { return (await reportesSeguimientosVencidos.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarSeguimientosVencidosExcel(token) { return (await reportesSeguimientosVencidos.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarSeguimientosVencidosPdf(token) { return (await reportesSeguimientosVencidos.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarSeguimientosVencidosCorreo(token, datos) { return (await reportesSeguimientosVencidos.correo(token, datos)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte
     * SeguimientosProgramados usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteSeguimientosProgramados() { return (await reportesSeguimientosProgramados.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarSeguimientosProgramadosExcel(token) { return (await reportesSeguimientosProgramados.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarSeguimientosProgramadosPdf(token) { return (await reportesSeguimientosProgramados.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarSeguimientosProgramadosCorreo(token, datos) { return (await reportesSeguimientosProgramados.correo(token, datos)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte
     * SeguimientosRealizados usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteSeguimientosRealizados() { return (await reportesSeguimientosRealizados.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarSeguimientosRealizadosExcel(token) { return (await reportesSeguimientosRealizados.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarSeguimientosRealizadosPdf(token) { return (await reportesSeguimientosRealizados.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarSeguimientosRealizadosCorreo(token, datos) { return (await reportesSeguimientosRealizados.correo(token, datos)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte
     * Solicitudes usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteSolicitudes() { return (await reportesSolicitudesSinContrato.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarSolicitudesExcel(token) { return (await reportesSolicitudesSinContrato.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarSolicitudesPdf(token) { return (await reportesSolicitudesSinContrato.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarSolicitudesCorreo(token, datos) { return (await reportesSolicitudesSinContrato.correo(token, datos)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte
     * ProximosPagos usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteProximosPagos() { return (await reportesProximosPagos.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarProximosPagosExcel(token) { return (await reportesProximosPagos.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarProximosPagosPdf(token) { return (await reportesProximosPagos.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarProximosPagosCorreo(token, datos) { return (await reportesProximosPagos.correo(token, datos)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte VenceHoy
     * usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteVenceHoy() { return (await reportesVenceHoy.visualizar()).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarVenceHoyExcel(token) { return (await reportesVenceHoy.excel(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarVenceHoyPdf(token) { return (await reportesVenceHoy.pdf(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarVenceHoyCorreo(token, datos) { return (await reportesVenceHoy.correo(token, datos)).data; },
    /**
     * Detalle del método: Cierra el visor de ventas y limpia la bandera de navegación desde el reporte
     * general.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    cerrarReporteVentas() { this.mostrarReporteVentas = false; this.reporteDesdeGeneral = false; },
    /**
     * Detalle del método: Valida el tipo seleccionado y carga su detalle manteniendo el periodo del
     * concentrado.
     * Entradas: tipo (tipo de reporte/operación seleccionado).
     */
    abrirReporteDesdeGeneral(tipo) {
      if (!this.puedeVerDashboardCompleto || ![1,2,3,4,5].includes(Number(tipo))) return;
      this.reporteDesdeGeneral = true;
      this.parametrosReporteVentas = Object.assign({}, this.parametrosReporteVentas, { empresa_operaciones_id: Number(tipo), cuota_id: null });
      this.$nextTick(() => { if (this.$refs.visorVentas) this.$refs.visorVentas.consultar(); });
    },
    /**
     * Detalle del método: Valida el identificador cuota-N de la navegación y pide el desglose de esa
     * cuota.
     * Entradas: id (ID del elemento seleccionado en este contexto).
     */
    abrirDetalleCuota(id) {
      if (!this.puedeVerDashboardCompleto || !this.esResumenCuotas || !/^cuota-\d+$/.test(String(id))) return;
      this.parametrosReporteVentas = Object.assign({}, this.parametrosReporteVentas, { cuota_id: Number(String(id).slice(6)) });
      this.$nextTick(() => { if (this.$refs.visorVentas) this.$refs.visorVentas.consultar(); });
    },
    /**
     * Detalle del método: Regresa de cuota individual a resumen de cuotas, o de operación individual al
     * concentrado, conservando el periodo.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    volverReporteGeneral() {
      this.parametrosReporteVentas = Object.assign({}, this.parametrosReporteVentas, { empresa_operaciones_id: this.esDetalleCuota ? 2 : "todas", cuota_id: null });
      this.$nextTick(() => { if (this.$refs.visorVentas) this.$refs.visorVentas.consultar(); });
    },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarVentasCompleto(token) { return (await reportesVentas.completo(token)).data; },
    /**
     * Detalle del método: Devuelve una promesa con response.data de la vista previa del reporte Ventas
     * usando su servicio y contexto seleccionado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteVentas() {
      return (await reportesVentas.visualizar(this.parametrosReporteVentas)).data;
    },
    /**
     * Detalle del método: Devuelve una promesa con Blob Excel para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarVentasExcel(token) { return (await reportesVentas.excel(token, this.esReporteGeneral)).data; },
    /**
     * Detalle del método: Devuelve una promesa con Blob PDF para el visor mediante el servicio del
     * reporte.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarVentasPdf(token) { return (await reportesVentas.pdf(token, this.esReporteGeneral)).data; },
    /**
     * Detalle del método: Devuelve una promesa con la respuesta del envío del reporte seleccionado,
     * delegando token y datos confirmados a su servicio.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarVentasCorreo(token, datos) { return (await reportesVentas.correo(token, datos, this.esReporteGeneral)).data; },
    /**
     * Detalle del método: Devuelve si el año/mes es válido y no posterior a la fecha de referencia del
     * servidor.
     * Entradas: periodo (objeto con año y mes solicitados).
     */
    periodoGraficaValido(periodo) {
      const anio=Number(periodo && periodo.anio),mes=Number(periodo && periodo.mes);
      return Number.isInteger(anio) && anio>=1000 && anio<=this.fechaReferencia.anio &&
        Number.isInteger(mes) && mes>=1 && mes<=12 &&
        (anio<this.fechaReferencia.anio || mes<=this.fechaReferencia.mes);
    },
    /**
     * Detalle del método: Acepta un periodo válido y devuelve la consulta exclusiva de la gráfica; no
     * cambia las demás cards.
     * Entradas: periodo (objeto con año y mes solicitados).
     */
    cambiarPeriodoGrafica(periodo) {
      if (this.isLoading || !this.puedeVerDashboardCompleto || !this.periodoGraficaValido(periodo)) return;
      this.periodoGrafica={anio:Number(periodo.anio),mes:Number(periodo.mes)};
      return this.cargarGrafica();
    },
    // Mantenimiento: Consulta solo el periodo local de la gráfica. _graficaSolicitud descarta respuestas tardías para que
    // un mes anterior no reemplace al último elegido.
    /**
     * Detalle del método: Devuelve una promesa que consulta el periodo elegido y descarta respuestas
     * anteriores mediante _graficaSolicitud.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarGrafica() {
      if (this.isLoading || !this.puedeVerDashboardCompleto || !this.periodoGraficaValido(this.periodoGrafica)) return;
      const periodo=Object.assign({},this.periodoGrafica);
      const solicitud=this._graficaSolicitud=(this._graficaSolicitud||0)+1;
      this.cargandoGrafica=true;this.errorGrafica="";this.operacionesGrafica=[];
      try {
        const respuesta=(await dashboard.graficaVentasSaldos(periodo)).data;
        if(this._dashboardDestruido || solicitud!==this._graficaSolicitud) return;
        const filas=respuesta && respuesta.operaciones;
        if(!respuesta.periodo || Number(respuesta.periodo.anio)!==periodo.anio || Number(respuesta.periodo.mes)!==periodo.mes ||
          !Array.isArray(filas) || !filas.length || filas.some(f => !Number.isInteger(Number(f.id)) || Number(f.id)<1 ||
            ["ventas","porCobrar","vencido"].some(k => f[k]===null || f[k]===undefined || f[k]==="" || !Number.isFinite(Number(f[k]))))) {
          throw new Error("No se recibió un resumen válido de la gráfica.");
        }
        this.operacionesGrafica=filas.map(f => Object.assign({},f));
      } catch(error) {
        if(this._dashboardDestruido || solicitud!==this._graficaSolicitud) return;
        this.errorGrafica="No fue posible cargar el periodo. Intenta nuevamente.";
      } finally {
        if(solicitud===this._graficaSolicitud) this.cargandoGrafica=false;
      }
    },
  },
  /**
   * Detalle del método: Inicializa el estado previo al montaje de HomeInfo; Vue lo ejecuta al crear la
   * instancia, sin valor de retorno.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  created() {
    this._dashboardDestruido = false;
    this.isLoading = true;
  },
  /**
   * Detalle del método: Conecta scroll/actividad, programa la actualización por inactividad y solicita
   * los datos iniciales.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  mounted() {
    // Capturar también el scroll de contenedores interiores del layout.
    this._posicionarAccionesDashboard=()=>{
      const barra=this.$refs.accionesDashboard;
      if (!barra) return;
      const limite=parseFloat(window.getComputedStyle(barra).top) || 0;
      this.accionesDashboardFijadas=barra.getBoundingClientRect().top<=limite+1;
    };
    window.addEventListener("scroll",this._posicionarAccionesDashboard,{passive:true,capture:true});
    window.addEventListener("resize",this._posicionarAccionesDashboard,{passive:true});
    this.$nextTick(this._posicionarAccionesDashboard);
    this._eventosActividadDashboard=["pointerdown","pointermove","keydown","scroll","touchstart"];
    this._reiniciarInactividadDashboard=()=>this.programarActualizacionInactiva();
    this._eventosActividadDashboard.forEach(evento=>window.addEventListener(evento,this._reiniciarInactividadDashboard,{passive:true,capture:true}));
    this.programarActualizacionInactiva();
    // Los skeletons acompañan la primera consulta del dashboard.
    this.isLoading = false;
    this._fetchDashboard();
  },
  /**
   * Detalle del método: Libera recursos o invalida respuestas pendientes de HomeInfo al destruirse;
   * evita listeners, tareas o popups huérfanos según la limpieza implementada.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  beforeDestroy() {
    window.removeEventListener("scroll",this._posicionarAccionesDashboard,true);
    window.removeEventListener("resize",this._posicionarAccionesDashboard);
    this._dashboardDestruido = true;
    clearTimeout(this._temporizadorInactividad);
    (this._eventosActividadDashboard||[]).forEach(evento=>window.removeEventListener(evento,this._reiniciarInactividadDashboard,true));
  },
};
</script>

