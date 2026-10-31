<template>
  <DashboardSkeleton v-if="cargando" variante="inventario" titulo="inventario" />
<div
    v-else-if="data.total_inventariables !== undefined"
    class="inventario inventario--resumen"
  >
    <header class="seccion-title">
      <h2>Estado del Inventario</h2>
      <button
        type="button"
        class="dashboard-enlace"
        @click="abrirReporte('all')"
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
                cantidadValida(data.total_inventariables)
                  ? data.total_inventariables + ' artículos inventariables'
                  : 'No disponible'
              "
            >
              <CountTo
                v-if="cantidadValida(data.total_inventariables)"
                :start-val="0"
                :end-val="numero(data.total_inventariables)"
                :duration="2000"
                separator=","
                aria-hidden="true"
              /><span v-else aria-hidden="true">—</span>
            </p>
            <button type="button" class="descripcion inventario-indicador__enlace"
              aria-label="Ver reporte de artículos inventariables" @click="abrirReporte('articles')">
              Artículos inventariables <DownloadIcon size="13" aria-hidden="true" />
            </button>
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
              aria-label="Ver reporte de desabastecidos"
              @click="abrirReporte('low')"
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
              aria-label="Ver reporte de sobreabastecidos"
              @click="abrirReporte('over')"
            >
              Sobreabastecidos <DownloadIcon size="13" aria-hidden="true" />
            </button>
          </div>
        </div>
        <!-- Conteo de conceptos de tipo servicio enviado por el backend. -->
        <div class="reporte">
          <span class="inventario-icono inventario-icono--servicios" aria-hidden="true"><BriefcaseIcon size="20" /></span>
          <div class="inventario-indicador__contenido">
            <p class="cantidad" :aria-label="cantidadValida(data.total_servicios) ? data.total_servicios + ' servicios' : 'No disponible'">
              <CountTo v-if="cantidadValida(data.total_servicios)" :start-val="0" :end-val="numero(data.total_servicios)" :duration="2000" separator="," aria-hidden="true" />
              <span v-else aria-hidden="true">—</span>
            </p>
            <button type="button" class="descripcion inventario-indicador__enlace"
              aria-label="Ver reporte de servicios" @click="abrirReporte('services')">
              Servicios <DownloadIcon size="13" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>
      <!-- Recurso original: se conserva su lugar al costado del inventario. -->
      <div class="imagen inventario__ilustracion" aria-hidden="true">
        <img src="@assets/images/conserviciosfunerarios.svg" alt="" />
      </div>
    </div>
    <!-- Metadatos distribuidos; se apilan cuando el ancho disponible no es suficiente. -->
    <div class="inventario-pie">
      <p class="inventario-ultimo-ajuste">
        <ClockIcon size="14" aria-hidden="true" />
        <span>Último ajuste de inventario: <strong>{{ ultimoAjusteTexto }}</strong></span>
      </p>
      <p class="inventario-valor" :aria-label="'Valor de artículos inventariables a precio de venta, sin servicios: ' + (cantidadValida(data.costo_total_inventario) ? moneda(data.costo_total_inventario) + ' MXN' : 'No disponible')">
        <span title="Existencias de artículos inventariables × precio de venta. No incluye servicios.">Valor de artículos inventariables:</span>
        <strong>
          <CountTo v-if="cantidadValida(data.costo_total_inventario)" :start-val="0" :end-val="numero(data.costo_total_inventario)" :duration="2000" :decimals="decimalesImporte(data.costo_total_inventario)" prefix="$" separator="," decimal="." aria-hidden="true" />
          <span v-else>—</span>
          <span v-if="cantidadValida(data.costo_total_inventario)" class="dashboard-moneda" aria-hidden="true">MXN</span>
        </strong>
      </p>
    </div>
    <VisorReportes :show="mostrarVisor" :cargar="cargarReporteExcel" :descargar-archivo="descargarReporteExcel" :descargar-pdf="descargarReportePdf" :enviar-correo="enviarReporteCorreo" @close="mostrarVisor = false" />
  </div>
  <div v-else class="skeleton">
    <p>Obteniendo información del inventario.</p>
  </div>
</template>
<script>
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Muestra artículos inventariables, servicios, faltantes, excedentes, último ajuste y valor inventariable;
 * emite el filtro de reporte.
 * Dónde ajustar: Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos
 * proceden de HomeInfo y del backend.
 * Relacionado: HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

import DashboardSkeleton from "./DashboardSkeleton.vue";
import VisorReportes from "../pages/VisorReportes.vue";
import CountTo from "vue-count-to";
import {
  PackageIcon,
  AlertTriangleIcon,
  LayersIcon,
  BriefcaseIcon,
  ArrowUpRightIcon,
  DownloadIcon,
  ClockIcon,
} from "vue-feather-icons";
import { moneda } from "../../utils/formato";
import inventario from "../../services/inventario";

export default {
  name: "Inventario(dashboard)",
  components: {
    DashboardSkeleton,
    VisorReportes,
    CountTo,
    PackageIcon,
    AlertTriangleIcon,
    LayersIcon,
    BriefcaseIcon,
    ArrowUpRightIcon,
    DownloadIcon,
    ClockIcon,
  },
  // El padre inicia con [] y la API entrega indicadores como propiedades.
  props: {
    // Ocultar importes/acciones mientras el padre espera datos.
    cargando: { type: Boolean, default: false },
    data: { type: [Array, Object], required: true },
    // Fecha o leyenda formateada por el padre; el campo real de la API está pendiente.
    ultimoAjuste: { type: String, default: null },
  },
  /**
   * Detalle del método: Devuelve el estado reactivo inicial por instancia de Inventario; no consulta al
   * servidor por sí mismo.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  data() {
    return { mostrarVisor: false, filtroReporte: "all" };
  },
  computed: {
    /**
     * Detalle del método: Devuelve el último ajuste ya formateado por el backend o el texto de ausencia de
     * fecha.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    ultimoAjusteTexto() {
      return this.ultimoAjuste && this.ultimoAjuste.trim()
        ? this.ultimoAjuste.trim()
        : "No disponible";
    },
  },
  methods: {
    moneda,
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
    // El modal solicita y conserva una instantánea hasta cerrarse o actualizarse.
    /**
     * Detalle del método: Selecciona el filtro de inventario y abre el visor cuando hay datos disponibles.
     * Entradas: filtro = "all" (filtro de inventario; all usa el listado general).
     */
    abrirReporte(filtro = "all") {
      if (this.cargando) return;
      this.filtroReporte = filtro;
      this.mostrarVisor = true;
    },
    /**
     * Detalle del método: Devuelve la promesa del envío de inventario por token con destinatarios y
     * formato confirmados.
     * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
     * datos confirmados del envío, según el método).
     */
    async enviarReporteCorreo(token, datos) {
      const respuesta = await inventario.enviarExcelPreview(token, datos);
      return respuesta.data;
    },
    /**
     * Detalle del método: Devuelve la promesa con Blob PDF de inventario para el visor.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarReportePdf(token) {
      const respuesta = await inventario.descargarPdfPreview(token);
      return respuesta.data;
    },
    /**
     * Detalle del método: Devuelve la promesa con Blob XLSX de inventario para el visor.
     * Entradas: token (token privado del reporte, distinto al de sesión).
     */
    async descargarReporteExcel(token) {
      const respuesta = await inventario.descargarExcelPreview(token);
      return respuesta.data;
    },
    /**
     * Detalle del método: Devuelve la promesa con HTML/metadatos del filtro de inventario actual; todavía
     * no genera XLSX.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    async cargarReporteExcel() {
      const respuesta = await inventario.visualizarExcel(this.filtroReporte);
      return respuesta.data;
    },
  },
  /**
   * Detalle del método: Inicializa el estado previo al montaje de Inventario; Vue lo ejecuta al crear la
   * instancia, sin valor de retorno.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  created() {
    this.$log("Component created! " + this.$options.name);
  },
  /**
   * Detalle del método: Completa la preparación de Inventario cuando sus referencias de DOM ya están
   * disponibles.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  mounted() {
    this.$log("Component mounted! " + this.$options.name);
  },
  /**
   * Detalle del método: Libera recursos o invalida respuestas pendientes de Inventario al destruirse;
   * evita listeners, tareas o popups huérfanos según la limpieza implementada.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  beforeDestroy() {
    this.$popupManager.unregister(this.$options.name);
  },
  /**
   * Detalle del método: Libera recursos o invalida respuestas pendientes de Inventario al destruirse;
   * evita listeners, tareas o popups huérfanos según la limpieza implementada.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  destroyed() {
    this.$log("Component destroyed! " + this.$options.name);
  },
};
</script>
