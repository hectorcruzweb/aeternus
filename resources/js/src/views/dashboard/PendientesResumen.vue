<template>
  <!-- Cards independientes; cada indicador conserva su enlace al futuro reporte. -->
  <DashboardSkeleton v-if="cargando" variante="pendientes" titulo="pendientes" />
<section v-else class="dashboard-pendientes" aria-label="Pendientes de atención">
    <article
      v-for="indicador in indicadores"
      :key="indicador.id"
      class="dashboard-pendientes__indicador dashboard-card"
      :class="'dashboard-pendientes__indicador--' + indicador.id"
    >
      <header class="dashboard-pendientes__cabecera">
        <component
          :is="indicador.icono"
          size="20"
          class="dashboard-pendientes__icono"
          aria-hidden="true"
        />
        <button
          type="button"
          class="dashboard-enlace"
          :aria-label="indicador.enlace + ': ' + indicador.titulo"
          @click="solicitarReporte(indicador)"
        >
          {{ indicador.enlace }}
          <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
      </header>
      <div class="dashboard-pendientes__contenido">
        <h2>{{ indicador.titulo }}</h2>
        <strong
          class="dashboard-indicador__valor"
          :class="{
            'dashboard-indicador__valor--vencido': indicador.id === 'hoy',
          }"
          :aria-label="etiquetaValor(indicador)"
        >
          <template v-if="cantidadValida(indicador.valor)"><CountTo
            :start-val="0"
            :end-val="numero(indicador.valor)"
            :duration="2000"
            :decimals="
              indicador.esImporte ? decimalesImporte(indicador.valor) : 0
            "
            :prefix="indicador.esImporte ? '$' : ''"
            separator=","
            decimal="."
            aria-hidden="true"
          /><span v-if="indicador.esImporte" class="dashboard-moneda" aria-hidden="true">MXN</span></template>
          <span v-else aria-hidden="true">—</span>
        </strong>
        <small
          v-if="indicador.esImporte"
          :aria-label="
            cantidadValida(indicador.cantidad)
              ? indicador.cantidad + ' pagos programados'
              : 'Cantidad no disponible'
          "
        >
          <CountTo
            v-if="cantidadValida(indicador.cantidad)"
            :start-val="0"
            :end-val="numero(indicador.cantidad)"
            :duration="2000"
            :decimals="0"
            separator=","
            aria-hidden="true"
          />
          <span v-else aria-hidden="true">—</span
          ><span aria-hidden="true"> pagos programados</span>
        </small>
        <small v-else>Servicios funerarios</small>
      </div>
    </article>
  </section>
</template>

<script>
import DashboardSkeleton from "./DashboardSkeleton.vue";
import CountTo from "vue-count-to";
import {
  CalendarIcon,
  ClockIcon,
  FileTextIcon,
  ArrowUpRightIcon,
} from "vue-feather-icons";
import { moneda } from "../../utils/formato";

export default {
  name: "PendientesResumen",
  components: {
    DashboardSkeleton,
    CountTo,
    CalendarIcon,
    ClockIcon,
    FileTextIcon,
    ArrowUpRightIcon,
  },
  // Los resúmenes llegan desde HomeInfo; este componente no consulta al servidor.
  props: {
    // Ocultar importes/acciones mientras el padre espera datos.
    cargando: { type: Boolean, default: false }, pendientes: { type: Object, required: true } },
  computed: {
    indicadores() {
      const hoy = this.pendientes.venceHoy || {};
      const proximos = this.pendientes.proximosSieteDias || {};
      return [
        {
          id: "hoy",
          titulo: "Vence hoy",
          icono: "CalendarIcon",
          valor: hoy.importe,
          cantidad: hoy.cantidad,
          esImporte: true,
          enlace: "Ver pagos",
        },
        {
          id: "proximos",
          titulo: "Próximos 7 días",
          icono: "ClockIcon",
          valor: proximos.importe,
          cantidad: proximos.cantidad,
          esImporte: true,
          enlace: "Ver próximos pagos",
        },
        {
          id: "solicitudes",
          titulo: "Solicitudes sin contrato",
          icono: "FileTextIcon",
          valor: this.pendientes.solicitudesSinContrato,
          esImporte: false,
          enlace: "Ver reporte",
        },
      ];
    },
  },
  methods: {
    // Preservar la diferencia entre cero y un dato que aún no está disponible.
    cantidadValida(valor) {
      return (
        valor !== null &&
        valor !== undefined &&
        valor !== "" &&
        Number.isFinite(Number(valor))
      );
    },
    numero(valor) {
      return Number(valor);
    },
    decimalesImporte(valor) {
      return Math.round(Number(valor) * 100) % 100 === 0 ? 0 : 2;
    },
    etiquetaValor(indicador) {
      return this.cantidadValida(indicador.valor)
        ? indicador.esImporte
          ? moneda(indicador.valor) + " MXN"
          : indicador.valor + " solicitudes"
        : "No disponible";
    },
    solicitarReporte(indicador) {
      if (this.cargando) return;
      const tipos = {
        hoy: "pagos-vence-hoy",
        proximos: "pagos-proximos-siete-dias",
        solicitudes: "solicitudes-sin-contrato",
      };
      // El padre abrirá el pop-up cuando se implemente; no se muestran tablas aquí.
      this.$emit("ver-reporte", {
        tipo: tipos[indicador.id],
        titulo: indicador.titulo,
        periodo:
          indicador.id === "proximos"
            ? "Desde mañana hasta los siguientes 7 días; excluye hoy"
            : "Estado actual",
      });
    },
  },
};
</script>
