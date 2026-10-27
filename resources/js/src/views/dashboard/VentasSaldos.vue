<template>
  <article class="dashboard-resumen-card dashboard-ventas">
    <header class="dashboard-resumen-card__header">
      <h2 aria-live="polite">{{ operacionSeleccionada.nombre }}</h2>
      <div class="dashboard-resumen-card__acciones">
        <div
          class="dashboard-carrusel__controles"
          aria-label="Cambiar tipo de operación"
        >
          <button
            type="button"
            class="dashboard-boton-icono"
            aria-label="Operación anterior"
            @click="moverOperacion(-1)"
          >
            <ChevronLeftIcon size="16" aria-hidden="true" />
          </button>
          <button
            type="button"
            class="dashboard-boton-icono"
            aria-label="Operación siguiente"
            @click="moverOperacion(1)"
          >
            <ChevronRightIcon size="16" aria-hidden="true" />
          </button>
        </div>
        <button
          type="button"
          class="dashboard-enlace"
          @click="verReporteVentas"
        >
          Ver reporte <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
      </div>
    </header>

    <!-- El mismo carrusel usado en servicios; aquí no rota automáticamente. -->
    <VueSlickCarousel
      ref="carruselOperaciones"
      v-bind="configuracionCarrusel"
      @afterChange="cambiarOperacionActiva"
    >
      <div v-for="operacion in operacionesResumen" :key="operacion.id">
        <div class="dashboard-resumen-card__indicadores">
          <div class="dashboard-indicador">
            <span class="dashboard-indicador__etiqueta"
              ><TrendingUpIcon
                size="14"
                class="dashboard-indicador__icono"
                aria-hidden="true"
              />
              Ventas del mes</span
            >
            <strong
              class="dashboard-indicador__valor dashboard-indicador__valor--ventas"
              :aria-label="moneda(operacion.ventas)"
            >
              <!-- Se recrea al activar el slide para animar únicamente la operación visible. -->
              <CountTo
                v-if="cantidadValida(operacion.ventas)"
                :key="
                  operacion.id +
                  '-ventas-' +
                  (operacion.id === operacionSeleccionada.id
                    ? 'activo'
                    : 'inactivo')
                "
                :start-val="0"
                :end-val="numero(operacion.ventas)"
                :duration="2000"
                :decimals="decimalesImporte(operacion.ventas)"
                prefix="$"
                separator=","
                decimal="."
                :autoplay="operacion.id === operacionSeleccionada.id"
                aria-hidden="true"
              />
              <span v-else aria-hidden="true">—</span>
            </strong>
            <small>{{ periodoResumen }}</small>
          </div>
          <div class="dashboard-indicador">
            <span class="dashboard-indicador__etiqueta"
              ><DollarSignIcon
                size="14"
                class="dashboard-indicador__icono"
                aria-hidden="true"
              />
              Por cobrar</span
            >
            <strong
              class="dashboard-indicador__valor"
              :aria-label="moneda(operacion.porCobrar)"
            >
              <!-- Se recrea al activar el slide para animar únicamente la operación visible. -->
              <CountTo
                v-if="cantidadValida(operacion.porCobrar)"
                :key="
                  operacion.id +
                  '-porCobrar-' +
                  (operacion.id === operacionSeleccionada.id
                    ? 'activo'
                    : 'inactivo')
                "
                :start-val="0"
                :end-val="numero(operacion.porCobrar)"
                :duration="2000"
                :decimals="decimalesImporte(operacion.porCobrar)"
                prefix="$"
                separator=","
                decimal="."
                :autoplay="operacion.id === operacionSeleccionada.id"
                aria-hidden="true"
              />
              <span v-else aria-hidden="true">—</span>
            </strong>
            <small>MXN · saldo actual</small>
          </div>
          <div class="dashboard-indicador">
            <span class="dashboard-indicador__etiqueta"
              ><AlertCircleIcon
                size="14"
                class="dashboard-indicador__icono"
                aria-hidden="true"
              />
              Vencido
            </span>
            <strong
              class="dashboard-indicador__valor dashboard-indicador__valor--vencido"
              :aria-label="moneda(operacion.vencido)"
            >
              <!-- Se recrea al activar el slide para animar únicamente la operación visible. -->
              <CountTo
                v-if="cantidadValida(operacion.vencido)"
                :key="
                  operacion.id +
                  '-vencido-' +
                  (operacion.id === operacionSeleccionada.id
                    ? 'activo'
                    : 'inactivo')
                "
                :start-val="0"
                :end-val="numero(operacion.vencido)"
                :duration="2000"
                :decimals="decimalesImporte(operacion.vencido)"
                prefix="$"
                separator=","
                decimal="."
                :autoplay="operacion.id === operacionSeleccionada.id"
                aria-hidden="true"
              />
              <span v-else aria-hidden="true">—</span>
            </strong>
            <small :aria-label="porcentajeVencido(operacion) + ' del saldo'">
              <CountTo
                :key="
                  operacion.id +
                  '-porcentaje-' +
                  (operacion.id === operacionSeleccionada.id
                    ? 'activo'
                    : 'inactivo')
                "
                :start-val="0"
                :end-val="porcentajeNumerico(operacion)"
                :duration="2000"
                :decimals="1"
                :autoplay="operacion.id === operacionSeleccionada.id"
                suffix=" %"
                aria-hidden="true"
              />
              <span aria-hidden="true"> del saldo</span>
            </small>
          </div>
        </div>
      </div>
    </VueSlickCarousel>
  </article>
</template>
<script>
import VueSlickCarousel from "vue-slick-carousel";
import CountTo from "vue-count-to";
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowUpRightIcon,
  TrendingUpIcon,
  DollarSignIcon,
  AlertCircleIcon,
} from "vue-feather-icons";
import { moneda } from "../../utils/formato";

export default {
  name: "VentasSaldos",
  components: {
    CountTo,
    VueSlickCarousel,
    ChevronLeftIcon,
    ChevronRightIcon,
    ArrowUpRightIcon,
    TrendingUpIcon,
    DollarSignIcon,
    AlertCircleIcon,
  },
  // El padre entrega datos; este componente solo presenta el resumen.
  props: {
    operaciones: { type: Array, required: true },
    periodoResumen: { type: String, required: true },
  },
  data() {
    return {
      operacionActiva: 0,
      configuracionCarrusel: {
        arrows: false,
        dots: false,
        autoplay: false,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        adaptiveHeight: true,
        draggable: true,
        swipe: true,
        accessibility: true,
        speed: 250,
      },
    };
  },
  computed: {
    // Primera slide: total calculado; las siguientes son las cinco operaciones.
    operacionesResumen() {
      const total = this.operaciones.reduce(
        (resumen, operacion) => {
          resumen.ventas += operacion.ventas;
          resumen.porCobrar += operacion.porCobrar;
          resumen.vencido += operacion.vencido;
          return resumen;
        },
        {
          id: "todas",
          nombre: "Todas las operaciones",
          ventas: 0,
          porCobrar: 0,
          vencido: 0,
        }
      );
      return [total].concat(this.operaciones);
    },
    operacionSeleccionada() {
      return (
        this.operacionesResumen[this.operacionActiva] ||
        this.operacionesResumen[0]
      );
    },
  },
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
    porcentajeNumerico(operacion) {
      return operacion.porCobrar > 0
        ? (operacion.vencido / operacion.porCobrar) * 100
        : 0;
    },
    porcentajeVencido(operacion) {
      return operacion.porCobrar > 0
        ? ((operacion.vencido / operacion.porCobrar) * 100).toLocaleString(
            "es-MX",
            { maximumFractionDigits: 1 }
          ) + " %"
        : "0 %";
    },
    moverOperacion(paso) {
      const carrusel = this.$refs.carruselOperaciones;
      if (!carrusel) return;
      if (paso < 0) carrusel.prev();
      else carrusel.next();
    },
    cambiarOperacionActiva(indice) {
      this.operacionActiva = indice;
    },
    verReporteVentas() {
      const seleccion = this.operacionSeleccionada;
      // Solo enviamos el contexto; los detalles se pedirán desde el futuro pop-up.
      this.$emit("ver-reporte", {
        tipo: "ventas-saldos",
        empresa_operaciones_id: seleccion.id === "todas" ? null : seleccion.id,
        titulo: "Ventas y saldos · " + seleccion.nombre,
        periodo: this.periodoResumen,
      });
    },
  },
};
</script>
