<template>
  <div class="dashboard">
    <!-- Conservar siempre estas props visibles para configuración del programador. -->
    <MensajeTemporada
      :habilitado="true && !isLoading"
      :modo-desarrollador="false"
      :una-vez-por-sesion="false"
      :duracion="4000"
    />
    <!-- Fila 1: componentes existentes; no cambiamos sus datos ni su diseño. -->
    <div class="dashboard__operacion">
      <ServiciosFunerarios :data="serviciosFunerarios" />
      <Inventario :data="inventario" />
    </div>

    <!-- Fila 2: resumen financiero. En esta etapa usa datos locales de ejemplo. -->
    <section
      class="dashboard__resumen"
      aria-label="Ventas, saldos y facturación"
    >
      <VentasSaldos
        :operaciones="ventasSaldos"
        :periodo-resumen="periodoResumen"
        @ver-reporte="prepararReporte"
      />
      <Facturacion
        :facturacion="facturacion"
        :periodo-resumen="periodoResumen"
        @ver-reporte="prepararReporte"
      />
    </section>

    <!-- Fila 3: pendientes actuales, independientes de los resúmenes históricos. -->
    <PendientesResumen
      :pendientes="pendientes"
      @ver-reporte="prepararReporte"
    />

    <!-- Seguimientos: actividad mensual y atención de la agenda. -->
    <SeguimientosResumen
      :seguimientos="seguimientos"
      :periodo-resumen="periodoResumen"
      @ver-reporte="prepararReporte"
    />

    <!-- Fila 4: filtros y datos independientes; no altera las cards actuales. -->
    <VentasCobrosGrafica
      :datos="datosGrafica"
      :periodo="periodoGrafica"
      :anios="aniosGrafica"
      :anio-actual="fechaReferencia.anio"
      :mes-actual="fechaReferencia.mes"
      @cambiar-periodo="cambiarPeriodoGrafica"
      @ver-reporte="prepararReporte"
    />
  </div>
</template>

<script>
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
    const hoy = new Date();
    return {
      // Datos actuales del backend: se conserva la consulta existente.
      serviciosFunerarios: [],
      inventario: [],
      isLoading: false,
      // Datos de diseño. No representan una respuesta real de la API.
      periodoResumen: hoy.toLocaleDateString("es-MX", {
        month: "long",
        year: "numeric",
      }),
      ventasSaldos: [
        {
          id: 1,
          nombre: "Ventas en cementerio",
          ventas: 210000,
          porCobrar: 980000,
          vencido: 140000,
        },
        {
          id: 2,
          nombre: "Cuotas de Mantenimiento",
          ventas: 35000,
          porCobrar: 180000,
          vencido: 62000,
        },
        {
          id: 3,
          nombre: "Servicios funerarios",
          ventas: 540000,
          porCobrar: 420000,
          vencido: 48000,
        },
        {
          id: 4,
          nombre: "Venta de planes funerarios",
          ventas: 360000,
          porCobrar: 810000,
          vencido: 65000,
        },
        {
          id: 5,
          nombre: "Ventas en general",
          ventas: 70000,
          porCobrar: 70000,
          vencido: 11000,
        },
      ],
      facturacion: {
        fechaCaducidadFiel: "2027-12-24",
        timbresDisponibles: 86,
        facturasPendientes: 12,
        saldoPendiente: 28400,
        ventasFacturadas: 425000,
      },
      // Conteos ficticios para diseñar la sección; no son estadísticas reales.
      seguimientos: {
        realizadosMes: 124,
        programadosHoy: 9,
        vencidos: 7,
        clientesConPagosVencidos: 23,
      },
      // Ejemplos para diseñar la nueva fila; todavía no se calculan con la API.
      pendientes: {
        venceHoy: { cantidad: 6, importe: 18400 },
        proximosSieteDias: { cantidad: 18, importe: 62800 },
        solicitudesSinContrato: 3,
      },
      // Referencia temporal del navegador para la maqueta; luego vendrá del servidor.
      fechaReferencia: { anio: hoy.getFullYear(), mes: hoy.getMonth() + 1 },
      periodoGrafica: { anio: hoy.getFullYear(), mes: hoy.getMonth() + 1 },
      reportePendiente: null,
    };
  },
  computed: {
    aniosGrafica() {
      return Array.from({ length: 7 }, (_, i) => this.fechaReferencia.anio - i);
    },
    // Generación EXCLUSIVA de ejemplos: reemplazar por resúmenes del backend después.
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
      const proporciones = { 1: 0.68, 2: 0.8, 3: 0.9, 4: 0.48, 5: 0.85 };
      return this.ventasSaldos.map((operacion) => {
        let ventas = 0;
        let cobrado = 0;
        let vencido = 0;
        for (let mes = primerMes; mes <= ultimoMes; mes++) {
          const factorMes =
            (0.8 + mes * 0.02) / (0.8 + this.fechaReferencia.mes * 0.02);
          const ventaMes = Math.round(
            operacion.ventas * factorAnio * factorMes
          );
          ventas += ventaMes;
          const cobradoMes = Math.round(ventaMes * proporciones[operacion.id]);
          cobrado += cobradoMes;
          // Solo maqueta: esta proporción NO representa la regla real de cartera vencida.
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
    // Cambiar el filtro de esta gráfica no modifica los otros resúmenes.
    cambiarPeriodoGrafica(periodo) {
      this.periodoGrafica = { anio: periodo.anio, mes: periodo.mes };
    },
    // Mantenemos el indicador de carga que ya utiliza tu proyecto.
    async _fetchDashboard() {
      if (this.isLoading) return;
      this.isLoading = true;
      this.$vs.loading();
      try {
        const response = await dashboard._fetchDashboard();
        this.serviciosFunerarios = response.data.servicios_funerarios;
        this.inventario = response.data.inventario;
      } catch (error) {
        this.$error("Error al obtener el dashboard:", error);
      } finally {
        this.isLoading = false;
        this.$vs.loading.close();
      }
    },
    // Punto de integración del futuro pop-up. Hoy no abre vistas ni consulta datos.
    prepararReporte(contexto) {
      this.reportePendiente = contexto;
      // TODO: abrir el componente del reporte con este contexto.
    },
  },
  created() {
    this._fetchDashboard();
  },
};
</script>
