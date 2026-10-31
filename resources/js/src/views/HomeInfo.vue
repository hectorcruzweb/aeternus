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
    esResumenCuotas() { return !!this.parametrosReporteVentas && Number(this.parametrosReporteVentas.empresa_operaciones_id) === 2 && this.parametrosReporteVentas.cuota_id == null; },
    esDetalleCuota() { return !!this.parametrosReporteVentas && Number(this.parametrosReporteVentas.empresa_operaciones_id) === 2 && this.parametrosReporteVentas.cuota_id != null; },
    esReporteGeneral() { return !!this.parametrosReporteVentas && this.parametrosReporteVentas.empresa_operaciones_id === "todas"; },
    mostrarVistaGerencial() {
      // Al inicio solo hay formas neutras; el servidor decide la vista con datos.
      return this.isLoading
        ? this.vistaCarga !== "operativa"
        : this.dashboardDisponible && this.puedeVerDashboardCompleto;
    },
    puedeVerDashboardCompleto() {
      return this.dashboardDisponible && this.acceso.vista === "gerencial";
    },
    // Fuente real; el filtro mensual nunca modifica las cards del resumen inicial.
    datosGrafica() { return this.operacionesGrafica; },
  },
  methods: {
    // Cinco minutos sin interacción: actualizar y volver a contar mientras siga inactivo.
    programarActualizacionInactiva() {
      clearTimeout(this._temporizadorInactividad);
      if(this._dashboardDestruido)return;
      this._temporizadorInactividad=setTimeout(()=>this.actualizarPorInactividad(),5*60*1000);
    },
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
    tieneSeccion(nombre) {
      return (
        this.dashboardDisponible && this.acceso.secciones.indexOf(nombre) !== -1
      );
    },
    // Limpiar al recargar o fallar: nunca dejar importes/permisos de una sesión anterior.
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
    recargarDashboard() {
      return this._fetchDashboard();
    },
    // Actualizar datos mostrando los skeletons propios del dashboard.
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
    solicitarResumenDia() { if(!this.isLoading && this.puedeVerDashboardCompleto)this.mostrarReporteDia=true; },
    async cargarReporteDia(){return (await reportesResumenDia.visualizar()).data;},
    async descargarDiaExcel(t){return (await reportesResumenDia.excel(t)).data;},
    async descargarDiaPdf(t){return (await reportesResumenDia.pdf(t)).data;},
    async enviarDiaCorreo(t,d){return (await reportesResumenDia.correo(t,d)).data;},
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
    async cargarReporteCfdisMes() { return (await reportesCfdisMes.visualizar()).data; },
    async descargarCfdisMesExcel(token) { return (await reportesCfdisMes.excel(token)).data; },
    async descargarCfdisMesPdf(token) { return (await reportesCfdisMes.pdf(token)).data; },
    async enviarCfdisMesCorreo(token, datos) { return (await reportesCfdisMes.correo(token, datos)).data; },
    async cargarReporteCfdisAdeudos() { return (await reportesCfdisAdeudos.visualizar()).data; },
    async descargarCfdisAdeudosExcel(token) { return (await reportesCfdisAdeudos.excel(token)).data; },
    async descargarCfdisAdeudosPdf(token) { return (await reportesCfdisAdeudos.pdf(token)).data; },
    async enviarCfdisAdeudosCorreo(token, datos) { return (await reportesCfdisAdeudos.correo(token, datos)).data; },
    cerrarReporteTimbres() { this.mostrarReporteTimbres = false; this.recargaReporteTimbres = null; },
    abrirHistorialRecarga(id) {
      const coincidencia = /^recarga-([1-9]\d*)$/.exec(String(id));
      if (!this.puedeVerDashboardCompleto || !coincidencia) return;
      this.recargaReporteTimbres = Number(coincidencia[1]);
      this.$nextTick(() => { if (this.$refs.visorTimbres) this.$refs.visorTimbres.consultar(); });
    },
    volverRecargasTimbres() {
      this.recargaReporteTimbres = null;
      this.$nextTick(() => { if (this.$refs.visorTimbres) this.$refs.visorTimbres.consultar(); });
    },
    async cargarReporteTimbres() { return (await reportesTimbres.visualizar(this.recargaReporteTimbres)).data; },
    async descargarTimbresExcel(token) { return (await reportesTimbres.excel(token)).data; },
    async descargarTimbresCompleto(token) { return (await reportesTimbres.excel(token, true)).data; },
    async descargarTimbresPdf(token) { return (await reportesTimbres.pdf(token)).data; },
    async enviarTimbresCorreo(token, datos) { return (await reportesTimbres.correo(token, datos)).data; },
    async cargarReporteClientesPagosVencidos() { return (await reportesClientesPagosVencidos.visualizar()).data; },
    async descargarClientesPagosVencidosExcel(token) { return (await reportesClientesPagosVencidos.excel(token)).data; },
    async descargarClientesPagosVencidosPdf(token) { return (await reportesClientesPagosVencidos.pdf(token)).data; },
    async enviarClientesPagosVencidosCorreo(token, datos) { return (await reportesClientesPagosVencidos.correo(token, datos)).data; },
    async cargarReporteSeguimientosVencidos() { return (await reportesSeguimientosVencidos.visualizar()).data; },
    async descargarSeguimientosVencidosExcel(token) { return (await reportesSeguimientosVencidos.excel(token)).data; },
    async descargarSeguimientosVencidosPdf(token) { return (await reportesSeguimientosVencidos.pdf(token)).data; },
    async enviarSeguimientosVencidosCorreo(token, datos) { return (await reportesSeguimientosVencidos.correo(token, datos)).data; },
    async cargarReporteSeguimientosProgramados() { return (await reportesSeguimientosProgramados.visualizar()).data; },
    async descargarSeguimientosProgramadosExcel(token) { return (await reportesSeguimientosProgramados.excel(token)).data; },
    async descargarSeguimientosProgramadosPdf(token) { return (await reportesSeguimientosProgramados.pdf(token)).data; },
    async enviarSeguimientosProgramadosCorreo(token, datos) { return (await reportesSeguimientosProgramados.correo(token, datos)).data; },
    async cargarReporteSeguimientosRealizados() { return (await reportesSeguimientosRealizados.visualizar()).data; },
    async descargarSeguimientosRealizadosExcel(token) { return (await reportesSeguimientosRealizados.excel(token)).data; },
    async descargarSeguimientosRealizadosPdf(token) { return (await reportesSeguimientosRealizados.pdf(token)).data; },
    async enviarSeguimientosRealizadosCorreo(token, datos) { return (await reportesSeguimientosRealizados.correo(token, datos)).data; },
    async cargarReporteSolicitudes() { return (await reportesSolicitudesSinContrato.visualizar()).data; },
    async descargarSolicitudesExcel(token) { return (await reportesSolicitudesSinContrato.excel(token)).data; },
    async descargarSolicitudesPdf(token) { return (await reportesSolicitudesSinContrato.pdf(token)).data; },
    async enviarSolicitudesCorreo(token, datos) { return (await reportesSolicitudesSinContrato.correo(token, datos)).data; },
    async cargarReporteProximosPagos() { return (await reportesProximosPagos.visualizar()).data; },
    async descargarProximosPagosExcel(token) { return (await reportesProximosPagos.excel(token)).data; },
    async descargarProximosPagosPdf(token) { return (await reportesProximosPagos.pdf(token)).data; },
    async enviarProximosPagosCorreo(token, datos) { return (await reportesProximosPagos.correo(token, datos)).data; },
    async cargarReporteVenceHoy() { return (await reportesVenceHoy.visualizar()).data; },
    async descargarVenceHoyExcel(token) { return (await reportesVenceHoy.excel(token)).data; },
    async descargarVenceHoyPdf(token) { return (await reportesVenceHoy.pdf(token)).data; },
    async enviarVenceHoyCorreo(token, datos) { return (await reportesVenceHoy.correo(token, datos)).data; },
    cerrarReporteVentas() { this.mostrarReporteVentas = false; this.reporteDesdeGeneral = false; },
    abrirReporteDesdeGeneral(tipo) {
      if (!this.puedeVerDashboardCompleto || ![1,2,3,4,5].includes(Number(tipo))) return;
      this.reporteDesdeGeneral = true;
      this.parametrosReporteVentas = Object.assign({}, this.parametrosReporteVentas, { empresa_operaciones_id: Number(tipo), cuota_id: null });
      this.$nextTick(() => { if (this.$refs.visorVentas) this.$refs.visorVentas.consultar(); });
    },
    abrirDetalleCuota(id) {
      if (!this.puedeVerDashboardCompleto || !this.esResumenCuotas || !/^cuota-\d+$/.test(String(id))) return;
      this.parametrosReporteVentas = Object.assign({}, this.parametrosReporteVentas, { cuota_id: Number(String(id).slice(6)) });
      this.$nextTick(() => { if (this.$refs.visorVentas) this.$refs.visorVentas.consultar(); });
    },
    volverReporteGeneral() {
      this.parametrosReporteVentas = Object.assign({}, this.parametrosReporteVentas, { empresa_operaciones_id: this.esDetalleCuota ? 2 : "todas", cuota_id: null });
      this.$nextTick(() => { if (this.$refs.visorVentas) this.$refs.visorVentas.consultar(); });
    },
    async descargarVentasCompleto(token) { return (await reportesVentas.completo(token)).data; },
    async cargarReporteVentas() {
      return (await reportesVentas.visualizar(this.parametrosReporteVentas)).data;
    },
    async descargarVentasExcel(token) { return (await reportesVentas.excel(token, this.esReporteGeneral)).data; },
    async descargarVentasPdf(token) { return (await reportesVentas.pdf(token, this.esReporteGeneral)).data; },
    async enviarVentasCorreo(token, datos) { return (await reportesVentas.correo(token, datos, this.esReporteGeneral)).data; },
    periodoGraficaValido(periodo) {
      const anio=Number(periodo && periodo.anio),mes=Number(periodo && periodo.mes);
      return Number.isInteger(anio) && anio>=1000 && anio<=this.fechaReferencia.anio &&
        Number.isInteger(mes) && mes>=1 && mes<=12 &&
        (anio<this.fechaReferencia.anio || mes<=this.fechaReferencia.mes);
    },
    cambiarPeriodoGrafica(periodo) {
      if (this.isLoading || !this.puedeVerDashboardCompleto || !this.periodoGraficaValido(periodo)) return;
      this.periodoGrafica={anio:Number(periodo.anio),mes:Number(periodo.mes)};
      return this.cargarGrafica();
    },
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
  created() {
    this._dashboardDestruido = false;
    this.isLoading = true;
  },
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
  beforeDestroy() {
    window.removeEventListener("scroll",this._posicionarAccionesDashboard,true);
    window.removeEventListener("resize",this._posicionarAccionesDashboard);
    this._dashboardDestruido = true;
    clearTimeout(this._temporizadorInactividad);
    (this._eventosActividadDashboard||[]).forEach(evento=>window.removeEventListener(evento,this._reiniciarInactividadDashboard,true));
  },
};
</script>

