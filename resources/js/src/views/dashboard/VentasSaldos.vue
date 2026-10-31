<template>
  <DashboardSkeleton v-if="cargando" variante="resumen" titulo="ventas y saldos" />
<article v-else class="dashboard-resumen-card dashboard-ventas">
    <header class="dashboard-resumen-card__header">
      <h2 aria-live="polite">
        {{
          operacionSeleccionada
            ? operacionSeleccionada.nombre
            : "Ventas y saldos"
        }}
      </h2>
      <div class="dashboard-resumen-card__acciones">
        <div
          class="dashboard-carrusel__controles"
          aria-label="Cambiar tipo de operación"
        >
          <button
            type="button"
            class="dashboard-boton-icono"
            :disabled="operacionesResumen.length < 2"
            aria-label="Operación anterior"
            @click="moverOperacion(-1)"
          >
            <ChevronLeftIcon size="16" aria-hidden="true" />
          </button>
          <button
            type="button"
            class="dashboard-boton-icono"
            :disabled="operacionesResumen.length < 2"
            aria-label="Operación siguiente"
            @click="moverOperacion(1)"
          >
            <ChevronRightIcon size="16" aria-hidden="true" />
          </button>
        </div>
        <button
          type="button"
          class="dashboard-enlace"
          :disabled="!operacionSeleccionada"
          @click="verReporteVentas"
        >
          Ver reporte <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
      </div>
    </header>

    <!-- El mismo carrusel usado en servicios; aquí no rota automáticamente. -->
    <VueSlickCarousel
      v-if="operacionesResumen.length"
      :key="claveCarrusel"
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
              :aria-label="cantidadValida(operacion.ventas) ? moneda(operacion.ventas) + ' MXN' : 'No disponible'"
            >
              <!-- Se recrea al activar el slide para animar únicamente la operación visible. -->
              <template v-if="cantidadValida(operacion.ventas)"><CountTo
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
              /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template>
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
              Por cobrar del mes</span
            >
            <strong
              class="dashboard-indicador__valor"
              :aria-label="cantidadValida(operacion.porCobrar) ? moneda(operacion.porCobrar) + ' MXN' : 'No disponible'"
            >
              <!-- Se recrea al activar el slide para animar únicamente la operación visible. -->
              <template v-if="cantidadValida(operacion.porCobrar)"><CountTo
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
              /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template>
              <span v-else aria-hidden="true">—</span>
            </strong>
            <small>{{ periodoResumen }} · saldo actual</small>
          </div>
          <div class="dashboard-indicador">
            <span class="dashboard-indicador__etiqueta"
              ><AlertCircleIcon
                size="14"
                class="dashboard-indicador__icono"
                aria-hidden="true"
              />
              Vencido Acumulado Histórico
            </span>
            <strong
              class="dashboard-indicador__valor dashboard-indicador__valor--vencido"
              :aria-label="cantidadValida(operacion.vencido) ? moneda(operacion.vencido) + ' MXN' : 'No disponible'"
            >
              <!-- Se recrea al activar el slide para animar únicamente la operación visible. -->
              <template v-if="cantidadValida(operacion.vencido)"><CountTo
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
              /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template>
              <span v-else aria-hidden="true">—</span>
            </strong>
            <!-- Vencido histórico al consultar; no se divide entre el saldo del mes. -->
            <small>A la fecha de consulta</small>
          </div>
        </div>
      </div>
    </VueSlickCarousel>
    <p v-else class="dashboard-indicador__etiqueta">
      No hay información disponible.
    </p>
  </article>
</template>
<script>
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Carrusel de resúmenes recibido del backend, incluido todas; el enlace usa el tipo del slide activo.
 * Dónde ajustar: Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos
 * proceden de HomeInfo y del backend.
 * Relacionado: HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

import DashboardSkeleton from "./DashboardSkeleton.vue";
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
    DashboardSkeleton,
    CountTo,
    VueSlickCarousel,
    ChevronLeftIcon,
    ChevronRightIcon,
    ArrowUpRightIcon,
    TrendingUpIcon,
    DollarSignIcon,
    AlertCircleIcon,
  },
  // El padre entrega ventas y porCobrar del mes; vencido acumulado hasta la consulta.
  // El componente presenta los importes recibidos, sin calcular vencidos ni porcentajes.
  props: {
    // Ocultar importes/acciones mientras el padre espera datos.
    cargando: { type: Boolean, default: false },
    operaciones: { type: Array, required: true },
    periodoResumen: { type: String, required: true },
  },
  /**
   * Detalle del método: Devuelve el estado reactivo inicial por instancia de VentasSaldos; no consulta
   * al servidor por sí mismo.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
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
    // Una fila recibida = un slide; conservar nombres, importes y orden del backend.
    // No fabricar el general, sumar importes, filtrar tipos ni limitar su cantidad.
    /**
     * Detalle del método: Devuelve las filas válidas del carrusel en el orden del backend, sin crear otro
     * total general.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    operacionesResumen() {
      return this.operaciones;
    },
    /**
     * Detalle del método: Identifica el conjunto de operaciones o reacciona a su cambio para mantener
     * válido el slide activo.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    claveCarrusel() {
      return this.operaciones.map((operacion) => operacion.id).join("|");
    },
    /**
     * Detalle del método: Devuelve el resumen del slide activo o el fallback definido cuando faltan filas.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    operacionSeleccionada() {
      return (
        this.operacionesResumen[this.operacionActiva] ||
        this.operacionesResumen[0] ||
        null
      );
    },
  },
  watch: {
    // Si cambia la lista de tipos, recrear el carrusel y activar su primera fila.
    /**
     * Detalle del método: Identifica el conjunto de operaciones o reacciona a su cambio para mantener
     * válido el slide activo.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    claveCarrusel() {
      this.operacionActiva = 0;
    },
  },
  methods: {
    moneda,
    // CountTo exige números; un dato ausente conserva el guion en lugar de mostrar cero.
    /**
     * Detalle del método: Devuelve si la cantidad es numérica y finita, distinguiendo cero real de
     * ausencia de datos.
     * Entradas: valor (valor recibido del control o propiedad observada).
     */
    cantidadValida(valor) {
      return (
        valor !== null &&
        valor !== undefined &&
        valor !== "" &&
        Number.isFinite(Number(valor))
      );
    },
    /**
     * Detalle del método: Convierte a número la cantidad admitida por la validación local y usa el
     * fallback definido para valores no válidos.
     * Entradas: valor (valor recibido del control o propiedad observada).
     */
    numero(valor) {
      return Number(valor);
    },
    /**
     * Detalle del método: Devuelve la precisión visual del importe, conservando centavos cuando existen;
     * no redondea el saldo del backend.
     * Entradas: valor (valor recibido del control o propiedad observada).
     */
    decimalesImporte(valor) {
      return Math.round(Number(valor) * 100) % 100 === 0 ? 0 : 2;
    },
    /**
     * Detalle del método: Avanza o retrocede la selección del carrusel usando el paso recibido y sus
     * límites.
     * Entradas: paso (desplazamiento del carrusel para avanzar/retroceder).
     */
    moverOperacion(paso) {
      if (this.cargando) return;
      if (this.operacionesResumen.length < 2) return;
      const carrusel = this.$refs.carruselOperaciones;
      if (!carrusel) return;
      if (paso < 0) carrusel.prev();
      else carrusel.next();
    },
    /**
     * Detalle del método: Sincroniza el índice activo notificado por el carrusel para mantener correcto el
     * enlace del reporte.
     * Entradas: indice (posición seleccionada del arreglo local).
     */
    cambiarOperacionActiva(indice) {
      this.operacionActiva = indice;
    },
    /**
     * Detalle del método: Emite al padre el contexto de tipo y periodo del slide visible; no suma de nuevo
     * el concentrado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    verReporteVentas() {
      if (this.cargando) return;
      const seleccion = this.operacionSeleccionada;
      if (!seleccion) return;
      // Solo enviamos el contexto; los detalles se pedirán desde el futuro pop-up.
      this.$emit("ver-reporte", {
        tipo: "ventas-saldos",
        idResumen: seleccion.id, // Identificador recibido; "todas" para el general.
        empresa_operaciones_id: seleccion.id === "todas" ? null : seleccion.id,
        titulo: "Ventas y saldos · " + seleccion.nombre,
        periodo: this.periodoResumen,
      });
    },
  },
};
</script>
