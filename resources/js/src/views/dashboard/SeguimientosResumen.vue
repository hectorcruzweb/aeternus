<template>
  <!-- Cards independientes: cada una conserva su hover y acceso al reporte. -->
  <section class="dashboard-seguimientos" aria-label="Seguimientos a clientes">
    <article
      v-for="indicador in indicadores"
      :key="indicador.id"
      class="dashboard-seguimientos__indicador dashboard-card"
      :class="'dashboard-seguimientos__indicador--' + indicador.id"
    >
      <header class="dashboard-seguimientos__cabecera">
        <component
          :is="indicador.icono"
          size="20"
          class="dashboard-seguimientos__icono"
          aria-hidden="true"
        />
        <button
          type="button"
          class="dashboard-enlace"
          :aria-label="'Ver reporte de ' + indicador.titulo"
          @click="solicitarReporte(indicador)"
        >
          Ver reporte <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
      </header>
      <div class="dashboard-seguimientos__contenido">
        <h2>{{ indicador.titulo }}</h2>
        <strong
          class="dashboard-indicador__valor"
          :aria-label="
            cantidadValida(indicador.valor)
              ? indicador.valor + ' ' + (indicador.unidad || 'seguimientos')
              : 'No disponible'
          "
        >
          <CountTo
            v-if="cantidadValida(indicador.valor)"
            :start-val="0"
            :end-val="Number(indicador.valor)"
            :duration="2000"
            :decimals="0"
            separator=","
            aria-hidden="true"
          />
          <span v-else aria-hidden="true">—</span>
        </strong>
        <small>{{ indicador.descripcion }}</small>
      </div>
    </article>
  </section>
</template>

<script>
import CountTo from "vue-count-to";
import {
  CheckCircleIcon,
  CalendarIcon,
  AlertCircleIcon,
  UsersIcon,
  ArrowUpRightIcon,
} from "vue-feather-icons";

export default {
  name: "SeguimientosResumen",
  components: {
    CountTo,
    CheckCircleIcon,
    CalendarIcon,
    AlertCircleIcon,
    UsersIcon,
    ArrowUpRightIcon,
  },
  // Datos provisionales de diseño: la integración se decidirá con el backend.
  props: {
    seguimientos: { type: Object, default: () => ({}) },
    periodoResumen: { type: String, default: "Mes en curso" },
  },
  computed: {
    indicadores() {
      return [
        {
          id: "realizados",
          titulo: "Seguimientos realizados del mes",
          valor: this.seguimientos.realizadosMes,
          icono: "CheckCircleIcon",
          descripcion: this.periodoResumen,
          periodo: this.periodoResumen,
        },
        {
          id: "hoy",
          titulo: "Seguimientos programados para hoy",
          valor: this.seguimientos.programadosHoy,
          icono: "CalendarIcon",
          descripcion: "Agenda del día",
          periodo: "Hoy",
        },
        {
          id: "vencidos",
          titulo: "Seguimientos vencidos",
          valor: this.seguimientos.vencidos,
          icono: "AlertCircleIcon",
          descripcion: "Pendientes de atención",
          periodo: "Estado actual",
        },
        {
          id: "sugeridos",
          titulo: "Clientes con pagos vencidos",
          valor: this.seguimientos.clientesConPagosVencidos,
          icono: "UsersIcon",
          descripcion: "Se recomienda dar seguimiento",
          periodo: "Estado actual",
          unidad: "clientes",
        },
      ];
    },
  },
  methods: {
    Number,
    // Conteos enteros no negativos; ausentes no se convierten en cero.
    cantidadValida(valor) {
      return (
        valor !== null &&
        valor !== undefined &&
        valor !== "" &&
        typeof valor !== "boolean" &&
        Number.isInteger(Number(valor)) &&
        Number(valor) >= 0
      );
    },
    solicitarReporte(indicador) {
      this.$emit("ver-reporte", {
        tipo: "seguimientos-" + indicador.id,
        titulo: indicador.titulo,
        periodo: indicador.periodo,
      });
    },
  },
};
</script>
