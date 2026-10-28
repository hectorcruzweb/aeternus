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
        ref="botonRecargar" @click="recargarDashboard"
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
        <ServiciosFunerarios :cargando="isLoading"
          v-if="isLoading || tieneSeccion('servicios_funerarios')"
          :data="serviciosFunerarios"
        />
        <Inventario :cargando="isLoading"
          v-if="isLoading || tieneSeccion('inventario')"
          :data="inventario"
          :ultimo-ajuste="inventario.ultimoAjuste"
        />
      </div>

      <!-- El carrusel muestra las filas recibidas, incluido el general del backend. -->
      <section
        class="dashboard__resumen"
        :class="{ 'dashboard__resumen--solo': !isLoading && !tieneSeccion('ventasSaldos') }"
        aria-label="Ventas, saldos y facturación"
      >
        <VentasSaldos :cargando="isLoading"
          v-if="isLoading || tieneSeccion('ventasSaldos')"
          :operaciones="ventasSaldos"
          :periodo-resumen="periodoResumen"
          @ver-reporte="prepararReporte"
        />
        <Facturacion :cargando="isLoading"
          :facturacion="facturacion"
          :periodo-resumen="periodoResumen"
          @ver-reporte="prepararReporte"
        />
      </section>

      <!-- Datos ficticios autorizados hasta integrar sus resúmenes del backend. -->
      <PendientesResumen :cargando="isLoading"
        :pendientes="pendientes"
        @ver-reporte="prepararReporte"
      />
      <SeguimientosResumen :cargando="isLoading"
        :seguimientos="seguimientos"
        :periodo-resumen="periodoResumen"
        @ver-reporte="prepararReporte"
      />
      <VentasCobrosGrafica :cargando="isLoading"
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
      <ServiciosFunerarios :cargando="isLoading"
        v-if="isLoading || tieneSeccion('servicios_funerarios')"
        :data="serviciosFunerarios"
      />
    </VistaLimitada>
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

export default {
  name: "Dashboard",
  components: {
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
      reportePendiente: null,
    };
  },
  computed: {
    mostrarVistaGerencial() {
      // Al inicio solo hay formas neutras; el servidor decide la vista con datos.
      return this.isLoading ? this.vistaCarga !== "operativa"
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
      if (completa) this.cargarEjemplosPendientes();
      this.dashboardDisponible = true;
    },
    // Maqueta autorizada: retirar cada bloque al integrar su fuente real.
    cargarEjemplosPendientes() {
      this.facturacion = {
        fechaCaducidadFiel: "2027-12-24",
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
      this.$vs.loading({ container: contenedorCarga, scale: 0.35, background: "transparent", color: "#b69a3a" });
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
      // El futuro pop-up usará idResumen/empresa_operaciones_id del slide activo.
    },
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

