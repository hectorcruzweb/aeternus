<template>
  <DashboardSkeleton v-if="cargando" variante="resumen" titulo="facturación" />
<article v-else class="dashboard-resumen-card dashboard-facturacion">
    <header class="dashboard-resumen-card__header">
      <h2>Facturación</h2>
      <!-- Vigencia en días calendario; la fecha proviene del padre. -->
      <span
        class="dashboard-facturacion__fiel"
        :class="{
          'dashboard-facturacion__fiel--proxima': fielPorVencer,
          'dashboard-facturacion__fiel--vencida':
            diasVigenciaFiel !== null && diasVigenciaFiel < 0,
        }"
        :title="detalleVigenciaFiel"
      >
        <ShieldIcon size="14" aria-hidden="true" />
        <span
          >{{ etiquetaVigenciaFiel
          }}<strong v-if="fechaFiel"> {{ fechaFiel.texto }}</strong></span
        >
      </span>
    </header>
    <div class="dashboard-resumen-card__indicadores">
      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta"
          ><FileTextIcon
            size="14"
            class="dashboard-indicador__icono"
            aria-hidden="true"
          />
          Timbres restantes</span
        >
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--ventas"
          :aria-label="
            cantidadValida(facturacion.timbresDisponibles)
              ? facturacion.timbresDisponibles + ' timbres'
              : 'No disponible'
          "
        >
          <CountTo
            v-if="cantidadValida(facturacion.timbresDisponibles)"
            :start-val="0"
            :end-val="numero(facturacion.timbresDisponibles)"
            :duration="2000"
            :decimals="0"
            separator=","
            decimal="."
            aria-hidden="true"
          />
          <span v-else aria-hidden="true">—</span>
        </strong>
        <small>Disponibles actualmente</small>
        <button
          type="button"
          class="dashboard-enlace"
          @click="verReporteFacturacion('timbres')"
        >
          Ver reporte <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
      </div>
      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta"
          ><FileMinusIcon
            size="14"
            class="dashboard-indicador__icono"
            aria-hidden="true"
          />
          CFDI con adeudo acumulado</span
        >
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--vencido"
          :aria-label="cantidadValida(facturacion.saldoPendiente) ? moneda(facturacion.saldoPendiente) + ' MXN' : 'No disponible'"
        >
          <template v-if="cantidadValida(facturacion.saldoPendiente)"><CountTo
            :start-val="0"
            :end-val="numero(facturacion.saldoPendiente)"
            :duration="2000"
            :decimals="decimalesImporte(facturacion.saldoPendiente)"
            prefix="$"
            separator=","
            decimal="."
            aria-hidden="true"
          /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template>
          <span v-else aria-hidden="true">—</span>
        </strong>
        <small>Acumulado hasta {{ periodoResumen }}</small>
        <small
          :aria-label="
            cantidadValida(facturacion.facturasPendientes)
              ? facturacion.facturasPendientes + ' facturas pendientes'
              : 'Cantidad de facturas no disponible'
          "
        >
          <CountTo
            v-if="cantidadValida(facturacion.facturasPendientes)"
            :start-val="0"
            :end-val="numero(facturacion.facturasPendientes)"
            :duration="2000"
            :decimals="0"
            separator=","
            aria-hidden="true"
          />
          <span v-else aria-hidden="true">—</span
          ><span aria-hidden="true"> facturas pendientes</span>
        </small>
        <button
          type="button"
          class="dashboard-enlace"
          @click="verReporteFacturacion('facturas')"
        >
          Ver reporte <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
      </div>
      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta"
          ><CheckSquareIcon
            size="14"
            class="dashboard-indicador__icono"
            aria-hidden="true"
          />
          Facturado del mes</span
        >
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--ventas"
          :aria-label="cantidadValida(facturacion.ventasFacturadas) ? moneda(facturacion.ventasFacturadas) + ' MXN' : 'No disponible'"
        >
          <template v-if="cantidadValida(facturacion.ventasFacturadas)"><CountTo
            :start-val="0"
            :end-val="numero(facturacion.ventasFacturadas)"
            :duration="2000"
            :decimals="decimalesImporte(facturacion.ventasFacturadas)"
            prefix="$"
            separator=","
            decimal="."
            aria-hidden="true"
          /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template>
          <span v-else aria-hidden="true">—</span>
        </strong>
        <div class="dashboard-facturacion__saldado">
          <span>Saldado</span>
          <strong :aria-label="cantidadValida(facturacion.saldadoFacturado) ? moneda(facturacion.saldadoFacturado) + ' MXN saldados' : 'No disponible'">
            <template v-if="cantidadValida(facturacion.saldadoFacturado)"><CountTo
              :start-val="0" :end-val="numero(facturacion.saldadoFacturado)" :duration="2000"
              :decimals="decimalesImporte(facturacion.saldadoFacturado)" prefix="$" separator="," decimal="." aria-hidden="true"
            /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template>
            <span v-else aria-hidden="true">—</span>
          </strong>
        </div>
        <div class="dashboard-facturacion__reporte">
        <button
          type="button"
          class="dashboard-enlace"
          @click="verReporteFacturacion('ventas')"
        >
          Ver reporte <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
          <small>{{ periodoResumen }}</small>
        </div>
      </div>
    </div>
  </article>
</template>
<script>
import DashboardSkeleton from "./DashboardSkeleton.vue";
import CountTo from "vue-count-to";
import {
  FileTextIcon,
  ArrowUpRightIcon,
  FileMinusIcon,
  CheckSquareIcon,
  ShieldIcon,
} from "vue-feather-icons";
import { moneda } from "../../utils/formato";

export default {
  name: "Facturacion",
  components: {
    DashboardSkeleton,
    CountTo,
    FileTextIcon,
    ArrowUpRightIcon,
    FileMinusIcon,
    CheckSquareIcon,
    ShieldIcon,
  },
  // Los tres indicadores se muestran juntos y no generan consultas adicionales.
  props: {
    // Ocultar importes/acciones mientras el padre espera datos.
    cargando: { type: Boolean, default: false },
    facturacion: { type: Object, required: true },
    periodoResumen: { type: String, required: true },
    fechaReferencia: { type: Date, default: () => new Date() },
  },
  computed: {
    fechaFiel() {
      // Contrato provisional: fecha local YYYY-MM-DD; no interpretar texto ambiguo.
      const valor = this.facturacion.fechaCaducidadFiel;
      const partes =
        typeof valor === "string" && /^(\d{4})-(\d{2})-(\d{2})$/.exec(valor);
      if (!partes) return null;
      const anio = Number(partes[1]),
        mes = Number(partes[2]),
        dia = Number(partes[3]);
      const fecha = new Date(anio, mes - 1, dia);
      if (
        fecha.getFullYear() !== anio ||
        fecha.getMonth() !== mes - 1 ||
        fecha.getDate() !== dia
      )
        return null;
      const meses = [
        "Ene",
        "Feb",
        "Mar",
        "Abr",
        "May",
        "Jun",
        "Jul",
        "Ago",
        "Sep",
        "Oct",
        "Nov",
        "Dic",
      ];
      return {
        anio,
        mes,
        dia,
        texto: partes[3] + "/" + meses[mes - 1] + "/" + anio,
      };
    },
    diasVigenciaFiel() {
      if (!this.fechaFiel || Number.isNaN(this.fechaReferencia.getTime()))
        return null;
      const f = this.fechaFiel,
        hoy = this.fechaReferencia;
      return (
        (Date.UTC(f.anio, f.mes - 1, f.dia) -
          Date.UTC(hoy.getFullYear(), hoy.getMonth(), hoy.getDate())) /
        86400000
      );
    },
    fielPorVencer() {
      return (
        this.diasVigenciaFiel !== null &&
        this.diasVigenciaFiel >= 0 &&
        this.diasVigenciaFiel < 15
      );
    },
    etiquetaVigenciaFiel() {
      if (this.diasVigenciaFiel === null)
        return "Firma Electrónica: vigencia no disponible";
      if (this.diasVigenciaFiel < 0) return "Firma Electrónica vencida el:";
      if (this.diasVigenciaFiel === 0) return "Firma Electrónica vence hoy:";
      return "Firma Electrónica válida hasta:";
    },
    detalleVigenciaFiel() {
      const dias = this.diasVigenciaFiel;
      if (dias === null) return "Fecha de caducidad no disponible";
      if (dias < 0) return "Venció hace " + Math.abs(dias) + " día(s)";
      return dias === 0
        ? "Renovación pendiente: vence hoy"
        : "Faltan " + dias + " día(s) para su vencimiento";
    },
  },
  // CountTo actualiza el valor animado cuando cambia la prop del backend.
  methods: {
    moneda,
    // CountTo exige números; un dato ausente conserva el guion en lugar de mostrar cero.
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

    verReporteFacturacion(tipo) {
      if (this.cargando) return;
      const titulos = {
        timbres: "Reporte de Recargas de Timbres",
        facturas: "CFDI con Adeudo · Acumulado Histórico",
        ventas: "Reporte de Facturación del Mes",
      };
      // El padre abre el visor según el indicador; no se insertan tablas en el dashboard.
      this.$emit("ver-reporte", {
        tipo: "facturacion-" + tipo,
        titulo: titulos[tipo],
        periodo: tipo === "facturas" ? "Acumulado hasta " + this.periodoResumen : (tipo === "ventas" ? this.periodoResumen : "Estado actual"),
      });
    },
  },
};
</script>
