<template>
  <DashboardSkeleton v-if="cargando" variante="grafica" titulo="gráfica de ventas y cobros" />
<section v-else
    class="dashboard-grafica dashboard-card"
    aria-label="Ventas, cobros y vencido por operación"
  >
    <header class="dashboard-grafica__encabezado">
      <h2>
        <BarChart2Icon size="16" aria-hidden="true" /> Ventas, cobros y vencido
        por operación
      </h2>
      <div class="dashboard-grafica__controles">
        <label
          >Año
          <select
            :value="periodo.anio"
            :disabled="cargando"
            @change="cambiarAnio($event.target.value)"
          >
            <option v-for="anio in anios" :key="anio" :value="anio">
              {{ anio }}
            </option>
          </select>
        </label>
        <label
          >Mes
          <select
            :value="periodo.mes === null ? '' : periodo.mes"
            :disabled="cargando"
            @change="cambiarMes($event.target.value)"
          >
            <option value="">Todos los meses</option>
            <option
              v-for="(mes, indice) in meses"
              :key="indice"
              :value="indice + 1"
              :disabled="periodo.anio === anioActual && indice + 1 > mesActual"
            >
              {{ mes }}
            </option>
          </select>
        </label>
        <button
          type="button"
          class="dashboard-enlace"
          :disabled="cargando || !hayDatos"
          @click="solicitarReporte"
        >
          Ver reporte <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
      </div>
    </header>

    <!-- Totales del mismo periodo y conjunto de operaciones que dibuja la gráfica. -->
    <div class="dashboard-grafica__totales">
      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta">Ventas del periodo</span>
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--ventas"
          :aria-label="totalVentas !== null ? moneda(totalVentas) + ' MXN' : 'No disponible'"
        >
          <CountTo
            v-if="totalVentas !== null"
            :start-val="0"
            :end-val="totalVentas"
            :duration="2000"
            :decimals="decimalesImporte(totalVentas)"
            prefix="$"
            suffix=" MXN"
            separator=","
            aria-hidden="true"
          /><span v-else aria-hidden="true">—</span>
        </strong>
      </div>
      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta"
          >Cobrado de esas ventas</span
        >
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--cobrado"
          :aria-label="totalCobrado !== null ? moneda(totalCobrado) + ' MXN' : 'No disponible'"
        >
          <CountTo
            v-if="totalCobrado !== null"
            :start-val="0"
            :end-val="totalCobrado"
            :duration="2000"
            :decimals="decimalesImporte(totalCobrado)"
            prefix="$"
            suffix=" MXN"
            separator=","
            aria-hidden="true"
          /><span v-else aria-hidden="true">—</span>
        </strong>
      </div>
      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta">Vencido del periodo</span>
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--vencido"
          :aria-label="totalVencido !== null ? moneda(totalVencido) + ' MXN' : 'No disponible'"
        >
          <CountTo
            v-if="totalVencido !== null"
            :start-val="0"
            :end-val="totalVencido"
            :duration="2000"
            :decimals="decimalesImporte(totalVencido)"
            prefix="$"
            suffix=" MXN"
            separator=","
            aria-hidden="true"
          /><span v-else aria-hidden="true">—</span>
        </strong>
      </div>
    </div>

    <p class="dashboard-grafica__periodo" aria-live="polite">
      {{ periodoTexto }} · MXN
    </p>
    <p v-if="cargando" class="dashboard-grafica__estado" role="status">
      Cargando gráfica…
    </p>
    <p v-else-if="error" class="dashboard-grafica__estado" role="alert">
      {{ error }}
    </p>
    <div
      v-else-if="hayDatos"
      class="dashboard-grafica__lienzo"
      role="img"
      :aria-label="descripcionGrafica"
    >
      <VueApexCharts
        type="bar"
        height="340"
        width="100%"
        :options="opciones"
        :series="series"
      />
    </div>
    <p v-else class="dashboard-grafica__estado">
      Sin datos para el periodo seleccionado.
    </p>
  </section>
</template>

<script>
import DashboardSkeleton from "./DashboardSkeleton.vue";
import VueApexCharts from "vue-apexcharts";
import CountTo from "vue-count-to";
import { BarChart2Icon, ArrowUpRightIcon } from "vue-feather-icons";
import { moneda } from "../../utils/formato";

const MESES = [
  "Enero",
  "Febrero",
  "Marzo",
  "Abril",
  "Mayo",
  "Junio",
  "Julio",
  "Agosto",
  "Septiembre",
  "Octubre",
  "Noviembre",
  "Diciembre",
];

export default {
  name: "VentasCobrosGrafica",
  components: {
    DashboardSkeleton, VueApexCharts, CountTo, BarChart2Icon, ArrowUpRightIcon },
  // El padre administra filtros y datos. La gráfica no hace solicitudes HTTP.
  props: {
    datos: { type: Array, required: true },
    periodo: { type: Object, required: true },
    anios: { type: Array, required: true },
    anioActual: { type: Number, default: null },
    mesActual: { type: Number, default: null },
    cargando: { type: Boolean, default: false },
    error: { type: String, default: "" },
  },
  data() {
    return { meses: MESES };
  },
  computed: {
    hayDatos() {
      return this.datos.some(
        (d) =>
          this.importeValido(d.ventas) ||
          this.importeValido(d.cobrado) ||
          this.importeValido(d.vencido)
      );
    },
    totalVentas() {
      return this.sumar("ventas");
    },
    totalCobrado() {
      return this.sumar("cobrado");
    },
    totalVencido() {
      return this.sumar("vencido");
    },
    periodoTexto() {
      if (this.periodo.mes !== null)
        return MESES[this.periodo.mes - 1] + " " + this.periodo.anio;
      return this.periodo.anio === this.anioActual
        ? "Acumulado enero a " +
            MESES[this.mesActual - 1].toLowerCase() +
            " " +
            this.periodo.anio
        : "Año " + this.periodo.anio;
    },
    series() {
      return [
        {
          name: "Ventas del periodo",
          data: this.datos.map((d) =>
            this.importeValido(d.ventas) ? Number(d.ventas) : null
          ),
        },
        {
          name: "Cobrado de esas ventas",
          data: this.datos.map((d) =>
            this.importeValido(d.cobrado) ? Number(d.cobrado) : null
          ),
        },
        {
          name: "Vencido del periodo",
          data: this.datos.map((d) =>
            this.importeValido(d.vencido) ? Number(d.vencido) : null
          ),
        },
      ];
    },
    descripcionGrafica() {
      return (
        this.periodoTexto +
        ". Comparación de ventas, cobrado de esas ventas y vencido del periodo por tipo de operación. Importes en pesos mexicanos."
      );
    },
    opciones() {
      return {
        chart: {
          id: "dashboard-ventas-cobros",
          fontFamily: "inherit",
          parentHeightOffset: 0,
          toolbar: { show: false },
          animations: { enabled: false },
          redrawOnParentResize: true,
          redrawOnWindowResize: true,
        },
        // Las tres series comparten escala monetaria y se comparan sin apilar.
        colors: ["#ac8b26", "#34785d", "#ae5748"],
        plotOptions: { bar: { horizontal: false, columnWidth: "48%" } },
        dataLabels: { enabled: false },
        xaxis: {
          categories: this.datos.map((d) => this.nombreCorto(d)),
          labels: { rotate: 0, trim: false, style: { fontSize: "12px" } },
        },
        yaxis: {
          title: { text: "Importe · MXN" },
          labels: { formatter: this.importeEje },
        },
        tooltip: {
          shared: true,
          intersect: false,
          y: { formatter: (v) => this.moneda(v) + " MXN" },
        },
        grid: { borderColor: "#e8e8e8" },
        legend: { position: "bottom", fontSize: "12px" },
        noData: { text: "Sin datos para el periodo seleccionado" },
        // En móvil, barras horizontales para conservar nombres legibles.
        responsive: [
          {
            breakpoint: 640,
            options: {
              plotOptions: { bar: { horizontal: true, barHeight: "60%" } },
              xaxis: {
                labels: {
                  formatter: this.importeEje,
                  style: { fontSize: "11px" },
                },
                title: { text: "Importe · MXN" },
              },
              yaxis: {
                title: { text: undefined },
                labels: {
                  formatter: (v) => v,
                  maxWidth: 120,
                  style: { fontSize: "11px" },
                },
              },
              legend: { fontSize: "11px" },
            },
          },
        ],
      };
    },
  },
  methods: {
    moneda,
    importeValido(valor) {
      return (
        valor !== null &&
        valor !== undefined &&
        valor !== "" &&
        Number.isFinite(Number(valor))
      );
    },
    // No presentar como total completo una suma con operaciones sin dato.
    sumar(campo) {
      return this.datos.length &&
        this.datos.every((d) => this.importeValido(d[campo]))
        ? Math.round(
            this.datos.reduce((s, d) => s + Number(d[campo]), 0) * 100
          ) / 100
        : null;
    },
    decimalesImporte(valor) {
      return Math.round(Number(valor) * 100) % 100 === 0 ? 0 : 2;
    },
    importeEje(valor) {
      const numero = Number(valor);
      if (!Number.isFinite(numero)) return "";
      const abs = Math.abs(numero);
      if (abs >= 1000000)
        return (
          "$" +
          (numero / 1000000).toLocaleString("es-MX", {
            maximumFractionDigits: 1,
          }) +
          " M"
        );
      if (abs >= 1000)
        return (
          "$" +
          (numero / 1000).toLocaleString("es-MX", {
            maximumFractionDigits: 1,
          }) +
          " mil"
        );
      return moneda(numero);
    },
    nombreCorto(dato) {
      return (
        {
          1: "Cementerio",
          2: "Cuotas de mantenimiento",
          3: "Servicios funerarios",
          4: "Planes funerarios",
          5: "Ventas en general",
        }[dato.id] || dato.nombre
      );
    },
    cambiarAnio(valor) {
      if (this.cargando) return;
      const anio = Number(valor);
      const mes =
        anio === this.anioActual && this.periodo.mes > this.mesActual
          ? this.mesActual
          : this.periodo.mes;
      this.$emit("cambiar-periodo", { anio, mes });
    },
    cambiarMes(valor) {
      if (this.cargando) return;
      this.$emit("cambiar-periodo", {
        anio: this.periodo.anio,
        mes: valor === "" ? null : Number(valor),
      });
    },
    solicitarReporte() {
      if (this.cargando) return;
      // Después conectaremos este contexto al pop-up con impresión y descargas.
      this.$emit("ver-reporte", {
        tipo: "ventas-cobros",
        titulo: "Ventas, cobros y vencido por operación",
        periodo: this.periodoTexto,
        anio: this.periodo.anio,
        mes: this.periodo.mes,
      });
    },
  },
};
</script>
