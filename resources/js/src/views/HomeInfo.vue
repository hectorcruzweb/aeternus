<template>
  <div class="dashboard" :aria-busy="isLoading ? 'true' : 'false'">
    <!-- Configuración de temporada siempre visible para el programador. -->
    <MensajeTemporada
      :habilitado="false && !isLoading"
      :modo-desarrollador="false"
      :una-vez-por-sesion="false"
      :duracion="4000"
    />

    <div class="dashboard__acciones" aria-label="Acciones del dashboard">
      <button
        type="button"
        class="dashboard-enlace"
        :disabled="isLoading || !dashboardDisponible"
        @click="solicitarResumenDia"
      >
        <DownloadIcon size="16" aria-hidden="true" /> Descargar reporte del día
      </button>
      <button
        type="button"
        class="dashboard-enlace"
        :disabled="isLoading"
        ref="botonRecargar"
        @click="recargarDashboard"
      >
        <RefreshCwIcon size="16" aria-hidden="true" />
        {{ isLoading ? "Actualizando…" : "Recargar dashboard" }}
      </button>
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

      <!-- Datos ficticios autorizados hasta integrar sus resúmenes del backend. -->
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
        :cargando="isLoading"
        :datos="datosGrafica"
        :periodo="periodoGrafica"
        :anios="aniosGrafica"
        :anio-actual="fechaReferencia.anio"
        :mes-actual="fechaReferencia.mes"
        @cambiar-periodo="cambiarPeriodoGrafica"
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
import { DownloadIcon, RefreshCwIcon } from "vue-feather-icons";
import VistaLimitada from "./dashboard/VistaLimitada.vue";
import MensajeTemporada from "./dashboard/MensajeTemporada.vue";
import VentasSaldos from "./dashboard/VentasSaldos.vue";
import Facturacion from "./dashboard/Facturacion.vue";
import SeguimientosResumen from "./dashboard/SeguimientosResumen.vue";
import PendientesResumen from "./dashboard/PendientesResumen.vue";
import VentasCobrosGrafica from "./dashboard/VentasCobrosGrafica.vue";
import ServiciosFunerarios from "./dashboard/ServiciosFunerarios.vue";
import Inventario from "./dashboard/Inventario.vue";
import dashboard from "../services/dashboard";
import VisorReportes from "./pages/VisorReportes.vue";
import reportesVentas from "../services/reportesVentas";
import reportesTimbres from "../services/reportesTimbres";
import reportesCfdisAdeudos from "../services/reportesCfdisAdeudos";
import reportesCfdisMes from "../services/reportesCfdisMes";

export default {
  name: "Dashboard",
  components: {
    VisorReportes,
    VistaLimitada,
    DownloadIcon,
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
      operacionesGraficaEjemplo: [],
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
    aniosGrafica() {
      return Number.isInteger(this.fechaReferencia.anio)
        ? Array.from({ length: 7 }, (_, i) => this.fechaReferencia.anio - i)
        : [];
    },
    // Gráfica ficticia: independiente de ventasSaldos y de los importes reales.
    datosGrafica() {
      const p = this.periodoGrafica;
      const ultimoMes =
        p.mes === null
          ? p.anio === this.fechaReferencia.anio
            ? this.fechaReferencia.mes
            : 12
          : p.mes;
      const primerMes = p.mes === null ? 1 : p.mes;
      const factorAnio = 1 - (this.fechaReferencia.anio - p.anio) * 0.06;
      return this.operacionesGraficaEjemplo.map((operacion) => {
        let ventas = 0;
        let cobrado = 0;
        let vencido = 0;
        for (let mes = primerMes; mes <= ultimoMes; mes++) {
          const factorMes =
            (0.8 + mes * 0.02) / (0.8 + this.fechaReferencia.mes * 0.02);
          const ventaMes = Math.round(
            operacion.ventas * factorAnio * factorMes
          );
          const cobradoMes = Math.round(ventaMes * operacion.proporcionCobro);
          ventas += ventaMes;
          cobrado += cobradoMes;
          vencido += Math.round((ventaMes - cobradoMes) * 0.4);
        }
        return {
          id: operacion.id,
          nombre: operacion.nombre,
          ventas,
          cobrado,
          vencido,
        };
      });
    },
  },
  methods: {
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
      this.operacionesGraficaEjemplo = [];
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
      if (completa) {
        this.cargarEjemplosPendientes();
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
        realizadosMes: 124,
        programadosHoy: 9,
        vencidos: 7,
        clientesConPagosVencidos: 23,
      };
      this.pendientes = {
        venceHoy: { cantidad: 6, importe: 18400 },
        proximosSieteDias: { cantidad: 18, importe: 62800 },
        solicitudesSinContrato: 3,
      };
      this.operacionesGraficaEjemplo = [
        {
          id: 1,
          nombre: "Ventas en cementerio",
          ventas: 210000,
          proporcionCobro: 0.68,
        },
        {
          id: 2,
          nombre: "Cuotas de Mantenimiento",
          ventas: 35000,
          proporcionCobro: 0.8,
        },
        {
          id: 3,
          nombre: "Servicios funerarios",
          ventas: 540000,
          proporcionCobro: 0.9,
        },
        {
          id: 4,
          nombre: "Venta de planes funerarios",
          ventas: 360000,
          proporcionCobro: 0.48,
        },
        {
          id: 5,
          nombre: "Ventas en general",
          ventas: 70000,
          proporcionCobro: 0.85,
        },
      ];
    },
    recargarDashboard() {
      return this._fetchDashboard();
    },
    // Conservar el indicador de carga de Vuesax ya utilizado por el proyecto.
    async _fetchDashboard() {
      if (this.isLoading) return;
      this.vistaCarga = this.acceso.vista;
      this.isLoading = true;
      this.errorDashboard = "";
      this.limpiarDashboard();
      // Carga Vuesax local en el botón: no cubrir los skeletons con una capa global.
      const contenedorCarga = this.$refs.botonRecargar;
      this.$vs.loading({
        container: contenedorCarga,
        scale: 0.35,
        background: "transparent",
        color: "#b69a3a",
      });
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
        this.$vs.loading.close(contenedorCarga);
      }
    },
    solicitarResumenDia() {
      if (this.isLoading || !this.dashboardDisponible) return;
      const contexto = {
        tipo: "dashboard-resumen-dia",
        titulo: this.puedeVerDashboardCompleto
          ? "Resumen general del día"
          : "Servicios funerarios del día",
        vista: this.acceso.vista,
        fecha: this.meta.fechaCorte,
        anio: this.fechaReferencia.anio,
        mes: this.fechaReferencia.mes,
      };
      this.reportePendiente = contexto;
      this.$emit("solicitar-reporte-dia", contexto);
      // Solo intención: el endpoint de descarga se implementará con los reportes.
    },
    prepararReporte(contexto) {
      if (this.isLoading || !this.puedeVerDashboardCompleto || !contexto)
        return;
      this.reportePendiente = Object.assign({}, contexto, {
        fechaCorte: this.meta.fechaCorte,
        periodoResumen: Object.assign({}, this.meta.periodoResumen),
      });
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
    cambiarPeriodoGrafica(periodo) {
      if (this.isLoading || !this.puedeVerDashboardCompleto) return;
      this.periodoGrafica = { anio: periodo.anio, mes: periodo.mes };
      // Por ahora solo cambia la gráfica de ejemplo; el endpoint real está pendiente.
    },
  },
  created() {
    this._dashboardDestruido = false;
    this.isLoading = true;
  },
  mounted() {
    // El botón ya existe para alojar la carga Vuesax sin bloquear el dashboard.
    this.isLoading = false;
    this._fetchDashboard();
  },
  beforeDestroy() {
    this._dashboardDestruido = true;
  },
};
</script>

