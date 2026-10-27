<template>
  <div
    v-if="data.total_articulos !== undefined"
    class="inventario inventario--resumen"
  >
    <header class="seccion-title">
      <h2>Estado del Inventario</h2>
      <button
        type="button"
        class="dashboard-enlace"
        @click="downloadExcel('all')"
      >
        Reporte General <ArrowUpRightIcon size="13" aria-hidden="true" />
      </button>
    </header>

    <!-- Indicadores a la izquierda e ilustración original a la derecha. -->
    <div class="reportes">
      <div class="lista">
        <div class="reporte">
          <span
            class="inventario-icono inventario-icono--conceptos"
            aria-hidden="true"
            ><PackageIcon size="20"
          /></span>
          <div class="inventario-indicador__contenido">
            <p
              class="cantidad"
              :aria-label="
                cantidadValida(data.total_articulos)
                  ? data.total_articulos + ' conceptos'
                  : 'No disponible'
              "
            >
              <CountTo
                v-if="cantidadValida(data.total_articulos)"
                :start-val="0"
                :end-val="numero(data.total_articulos)"
                :duration="2000"
                separator=","
                aria-hidden="true"
              /><span v-else aria-hidden="true">—</span>
            </p>
            <p class="descripcion">Conceptos</p>
          </div>
        </div>
        <div class="reporte">
          <span
            class="inventario-icono inventario-icono--bajo"
            aria-hidden="true"
            ><AlertTriangleIcon size="20"
          /></span>
          <div class="inventario-indicador__contenido">
            <p
              class="cantidad"
              :aria-label="
                cantidadValida(data.articulos_bajo_minimo)
                  ? data.articulos_bajo_minimo + ' desabastecidos'
                  : 'No disponible'
              "
            >
              <CountTo
                v-if="cantidadValida(data.articulos_bajo_minimo)"
                :start-val="0"
                :end-val="numero(data.articulos_bajo_minimo)"
                :duration="2000"
                separator=","
                aria-hidden="true"
              /><span v-else aria-hidden="true">—</span>
            </p>
            <button
              type="button"
              class="descripcion inventario-indicador__enlace"
              aria-label="Descargar reporte de desabastecidos"
              @click="downloadExcel('low')"
            >
              Desabastecidos <DownloadIcon size="13" aria-hidden="true" />
            </button>
          </div>
        </div>
        <div class="reporte">
          <span
            class="inventario-icono inventario-icono--sobre"
            aria-hidden="true"
            ><LayersIcon size="20"
          /></span>
          <div class="inventario-indicador__contenido">
            <p
              class="cantidad"
              :aria-label="
                cantidadValida(data.articulos_sobre_maximo)
                  ? data.articulos_sobre_maximo + ' sobreabastecidos'
                  : 'No disponible'
              "
            >
              <CountTo
                v-if="cantidadValida(data.articulos_sobre_maximo)"
                :start-val="0"
                :end-val="numero(data.articulos_sobre_maximo)"
                :duration="2000"
                separator=","
                aria-hidden="true"
              /><span v-else aria-hidden="true">—</span>
            </p>
            <button
              type="button"
              class="descripcion inventario-indicador__enlace"
              aria-label="Descargar reporte de sobreabastecidos"
              @click="downloadExcel('over')"
            >
              Sobreabastecidos <DownloadIcon size="13" aria-hidden="true" />
            </button>
          </div>
        </div>
        <div class="reporte">
          <span
            class="inventario-icono inventario-icono--costo"
            aria-hidden="true"
            ><DollarSignIcon size="20"
          /></span>
          <div class="inventario-indicador__contenido">
            <p
              class="cantidad"
              :aria-label="moneda(data.costo_total_inventario)"
            >
              <CountTo
                v-if="cantidadValida(data.costo_total_inventario)"
                :start-val="0"
                :end-val="numero(data.costo_total_inventario)"
                :duration="2000"
                :decimals="decimalesImporte(data.costo_total_inventario)"
                prefix="$"
                separator=","
                decimal="."
                aria-hidden="true"
              /><span v-else aria-hidden="true">—</span>
              <small class="inventario-indicador__moneda" aria-hidden="true"
                >MXN</small
              >
            </p>
            <p class="descripcion">Costo Total</p>
          </div>
        </div>
      </div>
      <!-- Recurso original: se conserva su lugar al costado del inventario. -->
      <div class="imagen inventario__ilustracion" aria-hidden="true">
        <img src="@assets/images/conserviciosfunerarios.svg" alt="" />
      </div>
    </div>
    <!-- Dato opcional; no implica que cargar el dashboard sea un ajuste de inventario. -->
    <p class="inventario-ultimo-ajuste">
      <ClockIcon size="14" aria-hidden="true" />
      <span
        >Último ajuste de inventario:
        <strong>{{ ultimoAjusteTexto }}</strong></span
      >
    </p>
  </div>
  <div v-else class="skeleton">
    <p>Obteniendo información del inventario.</p>
  </div>
</template>
<script>
import CountTo from "vue-count-to";
import {
  PackageIcon,
  AlertTriangleIcon,
  LayersIcon,
  DollarSignIcon,
  ArrowUpRightIcon,
  DownloadIcon,
  ClockIcon,
} from "vue-feather-icons";
import { moneda } from "../../utils/formato";
import inventario from "../../services/inventario";

export default {
  name: "Inventario(dashboard)",
  components: {
    CountTo,
    PackageIcon,
    AlertTriangleIcon,
    LayersIcon,
    DollarSignIcon,
    ArrowUpRightIcon,
    DownloadIcon,
    ClockIcon,
  },
  // El padre inicia con [] y la API entrega indicadores como propiedades.
  props: {
    data: { type: [Array, Object], required: true },
    // Fecha o leyenda formateada por el padre; el campo real de la API está pendiente.
    ultimoAjuste: { type: String, default: null },
  },
  computed: {
    ultimoAjusteTexto() {
      return this.ultimoAjuste && this.ultimoAjuste.trim()
        ? this.ultimoAjuste.trim()
        : "No disponible";
    },
  },
  methods: {
    moneda,
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
    // Se conservan el servicio, los filtros, nombres de archivo y el indicador de carga.
    async downloadExcel(filtro = "all") {
      this.$vs.loading();
      try {
        const res = await inventario.descargarExcel(filtro);
        const nombres = {
          low: "inventario_desabastecidos",
          over: "inventario_sobreabastecido",
          all: "inventario_general",
        };
        const filename = nombres[filtro] || nombres.all;
        this.$downloadFileExcel(res.data, `${filename} ${this.$fechaHora()}`);
      } catch (error) {
        this.$error("Error al descargar inventario:", error);
      } finally {
        this.$vs.loading.close();
      }
    },
  },
  created() {
    this.$log("Component created! " + this.$options.name);
  },
  mounted() {
    this.$log("Component mounted! " + this.$options.name);
  },
  beforeDestroy() {
    this.$popupManager.unregister(this.$options.name);
  },
  destroyed() {
    this.$log("Component destroyed! " + this.$options.name);
  },
};
</script>
