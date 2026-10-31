<template>
  <DashboardSkeleton v-if="cargando" variante="grafica" titulo="gráfica de ventas y saldos" />
<section v-else
    class="dashboard-grafica dashboard-card"
    aria-label="Ventas y saldos por operación"
  >
    <header class="dashboard-grafica__encabezado">
      <h2>
        <BarChart2Icon size="16" aria-hidden="true" /> Ventas y saldos
        por operación
      </h2>
      <div class="dashboard-grafica__controles">
        <label
          >Año
          <input type="number" min="1000" :max="anioActual" step="1"
            :value="periodo.anio" :disabled="cargando" aria-label="Año a consultar"
            @change="cambiarAnio($event)">
        </label>
        <label
          >Mes
          <select
            :value="periodo.mes === null ? '' : periodo.mes"
            :disabled="cargando"
            @change="cambiarMes($event.target.value)"
          >
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
        <label>Reporte
          <select v-model="operacionReporteId" :disabled="cargando || !hayDatos || !!error">
            <option value="todas">Todas las operaciones</option>
            <option v-for="operacion in datos" :key="operacion.id" :value="Number(operacion.id)">{{ operacion.nombre }}</option>
          </select>
        </label>
        <button
          type="button"
          class="dashboard-enlace"
          :disabled="cargando || !hayDatos || !!error"
          @click="solicitarReporte"
        >
          Ver reporte <ArrowUpRightIcon size="13" aria-hidden="true" />
        </button>
      </div>
    </header>

    <fieldset v-if="hayDatos && !error" class="dashboard-grafica__filtros">
      <legend>Operaciones visibles</legend>
      <div class="dashboard-grafica__opciones">
        <label v-for="operacion in datos.filter(f => Number(f.id)!==2)" :key="operacion.id">
          <input type="checkbox" :checked="operacionVisible(operacion.id)"
            @change="alternarOperacion(operacion.id)">
          <span>{{ operacion.nombre }}</span>
        </label>
        <button type="button" class="dashboard-enlace" :disabled="!operacionesOcultas.length" @click="mostrarTodas">Mostrar todas</button>
      <div class="dashboard-grafica__periodo-destacado" role="status" aria-live="polite">
        <CalendarIcon size="22" aria-hidden="true" />
        <div><span>Periodo consultado</span><strong>{{ meses[periodo.mes - 1] }} <small>{{ periodo.anio }}</small></strong></div>
      </div>
      </div>
    </fieldset>
    <p v-if="hayDatos && !error" class="dashboard-grafica__alcance" aria-live="polite">
      Totales de operaciones visibles · {{ datosVisibles.length }} de {{ datos.filter(f => Number(f.id)!==2).length }} · Sin Cuotas de Mantenimiento
    </p>

    <!-- Totales del mismo periodo y conjunto de operaciones que dibuja la gráfica. -->
    <div class="dashboard-grafica__totales">

      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta">Ventas del mes</span>
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--ventas"
          :aria-label="totalVentas !== null ? moneda(totalVentas) + ' MXN' : 'No disponible'"
        >
          <template v-if="totalVentas !== null"><CountTo
            :start-val="0"
            :end-val="totalVentas"
            :duration="2000"
            :decimals="decimalesImporte(totalVentas)"
            prefix="$"
            separator=","
            aria-hidden="true"
          /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template><span v-else aria-hidden="true">—</span>
        </strong>
      </div>
      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta"
          >Por cobrar del mes</span
        >
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--saldo"
          :aria-label="totalPorCobrar !== null ? moneda(totalPorCobrar) + ' MXN' : 'No disponible'"
        >
          <template v-if="totalPorCobrar !== null"><CountTo
            :start-val="0"
            :end-val="totalPorCobrar"
            :duration="2000"
            :decimals="decimalesImporte(totalPorCobrar)"
            prefix="$"
            separator=","
            aria-hidden="true"
          /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template><span v-else aria-hidden="true">—</span>
        </strong>
      </div>
      <div class="dashboard-indicador">
        <span class="dashboard-indicador__etiqueta">Vencido Acumulado Histórico</span>
        <strong
          class="dashboard-indicador__valor dashboard-indicador__valor--vencido"
          :aria-label="totalVencido !== null ? moneda(totalVencido) + ' MXN' : 'No disponible'"
        >
          <template v-if="totalVencido !== null"><CountTo
            :start-val="0"
            :end-val="totalVencido"
            :duration="2000"
            :decimals="decimalesImporte(totalVencido)"
            prefix="$"
            separator=","
            aria-hidden="true"
          /><span class="dashboard-moneda" aria-hidden="true">MXN</span></template><span v-else aria-hidden="true">—</span>
        </strong>
      </div>
    </div>

    <p class="dashboard-grafica__periodo" aria-live="polite">
      {{ periodoTexto }} · MXN · Vencido hasta el mes seleccionado, con saldo pendiente actual. Saldos sin intereses.
    </p>
    <div class="dashboard-grafica__comparacion">
    <div class="dashboard-grafica__barras">
    <p v-if="cargando" class="dashboard-grafica__estado" role="status">
      Cargando gráfica…
    </p>
    <p v-else-if="error" class="dashboard-grafica__estado" role="alert">
      {{ error }} <button type="button" class="dashboard-enlace" @click="$emit('reintentar')">Reintentar</button>
    </p>
    <div
      v-else-if="hayDatosVisibles"
      class="dashboard-grafica__lienzo"
      role="img"
      :aria-label="descripcionGrafica"
    >
      <VueApexCharts
        :key="esPantallaCompacta ? 'horizontal' : 'vertical'"
        type="bar"
        height="340"
        width="100%"
        :options="opciones"
        :series="series"
      />
    </div>
    <p v-else class="dashboard-grafica__estado">
      {{ hayDatos ? "Selecciona al menos una operación para comparar." : "Sin datos para el periodo seleccionado." }}
    </p>
    </div>
    <aside v-if="cuotas && !error" class="dashboard-grafica__cuotas" aria-label="Cuotas de Mantenimiento">
      <header><h3>Cuotas de Mantenimiento</h3><button type="button" class="dashboard-enlace" @click="solicitarReporteCuotas">Ver reporte ↗</button></header>
      <div v-for="indicador in indicadoresCuotas" :key="indicador.campo" class="dashboard-grafica__cuota-indicador">
        <span>{{ indicador.nombre }}</span>
        <strong :class="{'dashboard-grafica__cuota-vencido': indicador.campo === 'vencido'}">{{ importeTexto(cuotas[indicador.campo]) }} <small>MXN</small></strong>
      </div>
      <p>Vencido hasta el mes elegido · Pendiente actual, sin intereses.</p>
    </aside>
    </div>
  </section>
</template>

<script>
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Compara operaciones 1/3/4/5 en barras y muestra cuotas tipo 2 aparte; permite elegir mes/año y reporte.
 * Dónde ajustar: Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos
 * proceden de HomeInfo y del backend. Mantener responsive: [] de ApexCharts: la adaptación móvil usa
 * matchMedia para evitar el error de recursión observado. Los filtros visuales no cambian el alcance del
 * reporte elegido.
 * Relacionado: HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

import DashboardSkeleton from "./DashboardSkeleton.vue";
import VueApexCharts from "vue-apexcharts";
import CountTo from "vue-count-to";
import { BarChart2Icon, ArrowUpRightIcon, CalendarIcon } from "vue-feather-icons";
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
    DashboardSkeleton, VueApexCharts, CountTo, BarChart2Icon, ArrowUpRightIcon, CalendarIcon },
  // El padre administra filtros y datos. La gráfica no hace solicitudes HTTP.
  props: {
    datos: { type: Array, required: true },
    periodo: { type: Object, required: true },
    anioActual: { type: Number, default: null },
    mesActual: { type: Number, default: null },
    cargando: { type: Boolean, default: false },
    error: { type: String, default: "" },
  },
  /**
   * Detalle del método: Devuelve el estado reactivo inicial por instancia de VentasCobrosGrafica; no
   * consulta al servidor por sí mismo.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  data() {
    return { meses: MESES, operacionReporteId: "todas", esPantallaCompacta: false, operacionesOcultas: [] };
  },
  /**
   * Detalle del método: Conecta matchMedia para adaptar la gráfica a móvil sin responsive recursivo de
   * ApexCharts.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  mounted() {
    // Compatibilidad Apex3/Vue2: adaptar orientación sin usar el responsive interno,
    // que clona referencias circulares al desmontar y montar tras cambiar de periodo.
    this._mediaGrafica=window.matchMedia("(max-width: 639px)");
    this.esPantallaCompacta=this._mediaGrafica.matches;
    this._cambiarPantallaGrafica=e=>{this.esPantallaCompacta=e.matches;};
    if(this._mediaGrafica.addEventListener)this._mediaGrafica.addEventListener("change",this._cambiarPantallaGrafica);
    else this._mediaGrafica.addListener(this._cambiarPantallaGrafica);
  },
  /**
   * Detalle del método: Libera recursos o invalida respuestas pendientes de VentasCobrosGrafica al
   * destruirse; evita listeners, tareas o popups huérfanos según la limpieza implementada.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  beforeDestroy() {
    if(!this._mediaGrafica)return;
    if(this._mediaGrafica.removeEventListener)this._mediaGrafica.removeEventListener("change",this._cambiarPantallaGrafica);
    else this._mediaGrafica.removeListener(this._cambiarPantallaGrafica);
  },
  watch: {
    datos: { immediate: true, /**
   * Detalle del método: Reacciona a nuevos datos para mantener válidos filtros y selección locales.
   * Entradas: filas (nuevo conjunto de filas observado).
   */
  handler(filas) {
      if(this.operacionReporteId === "todas" || !filas.length) return; // Conservar la selección mientras cambia el periodo.
      if(!filas.some(f => Number(f.id)===this.operacionReporteId)) this.operacionReporteId="todas";
    } },
  },
  computed: {
    /**
     * Detalle del método: Devuelve el tipo 2 para su panel separado; no lo incorpora a las barras.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    cuotas() { return this.datos.find(f => Number(f.id)===2); },
    /**
     * Detalle del método: Devuelve campos y etiquetas de ventas, por cobrar y vencido del panel de cuotas.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    indicadoresCuotas() { return [{campo:"ventas",nombre:"Cuotas generadas del mes"},{campo:"porCobrar",nombre:"Por cobrar del mes"},{campo:"vencido",nombre:"Vencido Acumulado Histórico"}]; },
    /**
     * Detalle del método: Devuelve los tipos visibles de las barras excluyendo siempre cuotas tipo 2.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    datosVisibles() { return this.datos.filter(f => Number(f.id)!==2 && this.operacionVisible(f.id)); },
    /**
     * Detalle del método: Devuelve si las operaciones visibles contienen importes numéricos que pueden
     * representarse.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    hayDatosVisibles() { return this.datosVisibles.some(f => [f.ventas,f.porCobrar,f.vencido].some(this.importeValido)); },
    /**
     * Detalle del método: Devuelve si hay datos utilizables para dibujar la gráfica.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    hayDatos() {
      return this.datos.some(
        (d) =>
          this.importeValido(d.ventas) ||
          this.importeValido(d.porCobrar) ||
          this.importeValido(d.vencido)
      );
    },
    /**
     * Detalle del método: Devuelve la suma de ventas de los tipos visibles mediante sumar; cuotas queda en
     * su panel separado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    totalVentas() {
      return this.sumar("ventas");
    },
    /**
     * Detalle del método: Devuelve la suma de por cobrar de los tipos visibles mediante sumar; cuotas
     * queda en su panel separado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    totalPorCobrar() {
      return this.sumar("porCobrar");
    },
    /**
     * Detalle del método: Devuelve la suma de vencido de los tipos visibles mediante sumar; cuotas queda
     * en su panel separado.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    totalVencido() {
      return this.sumar("vencido");
    },
    /**
     * Detalle del método: Devuelve el nombre del mes y año seleccionado para hacerlo visible en la card.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    periodoTexto() { return MESES[this.periodo.mes - 1] + " " + this.periodo.anio; },
    /**
     * Detalle del método: Transforma los tipos visibles en series de ventas, por cobrar y vencido de
     * ApexCharts.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    series() {
      return [
        {
          name: "Ventas del mes",
          data: this.datosVisibles.map((d) =>
            this.importeValido(d.ventas) ? Number(d.ventas) : null
          ),
        },
        {
          name: "Por cobrar del mes",
          data: this.datosVisibles.map((d) =>
            this.importeValido(d.porCobrar) ? Number(d.porCobrar) : null
          ),
        },
        {
          name: "Vencido Acumulado Histórico",
          data: this.datosVisibles.map((d) =>
            this.importeValido(d.vencido) ? Number(d.vencido) : null
          ),
        },
      ];
    },
    /**
     * Detalle del método: Devuelve una descripción textual de la gráfica para accesibilidad.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    descripcionGrafica() {
      return (
        this.periodoTexto +
        ". Comparación de ventas, por cobrar del mes y vencido acumulado histórico por tipo de operación. Importes en pesos mexicanos."
      );
    },
    /**
     * Detalle del método: Devuelve ejes, leyendas, colores y formato de ApexCharts. Mantener responsive
     * vacío: la adaptación móvil usa matchMedia para evitar recursión.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
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
        colors: ["#ac8b26", "#73777a", "#ae5748"],
        plotOptions: { bar: { horizontal: this.esPantallaCompacta, columnWidth: "48%", barHeight: "60%" } },
        dataLabels: { enabled: false },
        xaxis: {
          categories: this.datosVisibles.map((d) => this.nombreCorto(d)),
          labels: { rotate: 0, trim: false, style: { fontSize: this.esPantallaCompacta ? "11px" : "12px" },
            formatter: this.esPantallaCompacta ? this.importeEje : undefined },
          title: { text: this.esPantallaCompacta ? "Importe · MXN" : undefined },
        },
        yaxis: {
          title: { text: this.esPantallaCompacta ? undefined : "Importe · MXN" },
          labels: { formatter: this.esPantallaCompacta ? (v => v) : this.importeEje, maxWidth: this.esPantallaCompacta ? 120 : undefined },
        },
        tooltip: {
          shared: true,
          intersect: false,
          y: { formatter: (v) => this.moneda(v) + " MXN" },
        },
        grid: { borderColor: "#e8e8e8" },
        legend: { position: "bottom", fontSize: "12px" },
        noData: { text: "Sin datos para el periodo seleccionado" },
        // La orientación móvil se resuelve arriba; no usar responsive de Apex3 aquí.
        responsive: [],
      };
    },
  },
  methods: {
    /**
     * Detalle del método: Emite al padre el reporte tipo 2 con el periodo actual de la gráfica.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    solicitarReporteCuotas() {
      if(this.cargando || this.error)return;
      this.$emit("ver-reporte",{tipo:"ventas-saldos-periodo",empresa_operaciones_id:2,anio:this.periodo.anio,mes:this.periodo.mes});
    },
    moneda,
    /**
     * Detalle del método: Devuelve si el ID no pertenece a la lista local de operaciones ocultas.
     * Entradas: id (ID del elemento seleccionado en este contexto).
     */
    operacionVisible(id) { return this.operacionesOcultas.indexOf(String(id)) === -1; },
    /**
     * Detalle del método: Agrega o retira un tipo de la lista oculta; solo cambia la comparación visual y
     * sus totales visibles.
     * Entradas: id (ID del elemento seleccionado en este contexto).
     */
    alternarOperacion(id) {
      if(this.cargando || this.error || !this.datos.some(f => String(f.id)===String(id)))return;
      const clave=String(id);
      this.operacionesOcultas=this.operacionVisible(id)?this.operacionesOcultas.concat(clave):this.operacionesOcultas.filter(f=>f!==clave);
    },
    /**
     * Detalle del método: Vacía filtros locales para mostrar todos los tipos de barras; cuotas sigue
     * aparte.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    mostrarTodas() { this.operacionesOcultas=[]; },
    /**
     * Detalle del método: Devuelve el importe formateado o el marcador de dato ausente, sin modificar la
     * cifra numérica.
     * Entradas: valor (valor recibido del control o propiedad observada).
     */
    importeTexto(valor) { return this.importeValido(valor)?moneda(Number(valor)):"—"; },
    /**
     * Detalle del método: Devuelve si el importe es numérico y finito; cero sigue siendo válido.
     * Entradas: valor (valor recibido del control o propiedad observada).
     */
    importeValido(valor) {
      return (
        valor !== null &&
        valor !== undefined &&
        valor !== "" &&
        Number.isFinite(Number(valor))
      );
    },
    // No presentar como total completo una suma con operaciones sin dato.
    /**
     * Detalle del método: Devuelve la suma del campo indicado de los tipos visibles, sin sumar el panel de
     * cuotas.
     * Entradas: campo (campo del dato o indicador que se evalúa).
     */
    sumar(campo) {
      return this.datosVisibles.length &&
        this.datosVisibles.every((d) => this.importeValido(d[campo]))
        ? Math.round(
            this.datosVisibles.reduce((s, d) => s + Number(d[campo]), 0) * 100
          ) / 100
        : null;
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
     * Detalle del método: Devuelve una etiqueta abreviada de importe para facilitar la lectura del eje.
     * Entradas: valor (valor recibido del control o propiedad observada).
     */
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
    /**
     * Detalle del método: Devuelve una etiqueta breve del tipo de operación para el eje, conservando el
     * dato original.
     * Entradas: dato (fila de operación usada como etiqueta).
     */
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
    /**
     * Detalle del método: Valida y emite el cambio de año al padre, que realizará la consulta de la
     * gráfica.
     * Entradas: valor (valor recibido del control o propiedad observada).
     */
    cambiarAnio(valor) {
      if (this.cargando) return;
      const entrada=valor && valor.target ? valor.target : null;
      const anio = Number(entrada ? entrada.value : valor);
      if(!Number.isInteger(anio) || anio<1000 || anio>this.anioActual) {
        if(entrada) entrada.value=this.periodo.anio;
        return;
      }
      const mes =
        anio === this.anioActual && this.periodo.mes > this.mesActual
          ? this.mesActual
          : this.periodo.mes;
      this.$emit("cambiar-periodo", { anio, mes });
    },
    /**
     * Detalle del método: Valida y emite el cambio de mes al padre, sin afectar el periodo de otras
     * tarjetas.
     * Entradas: valor (valor recibido del control o propiedad observada).
     */
    cambiarMes(valor) {
      if (this.cargando) return;
      this.$emit("cambiar-periodo", {
        anio: this.periodo.anio,
        mes: Number(valor),
      });
    },
    /**
     * Detalle del método: Emite al padre el indicador o contexto de reporte elegido cuando la card ya está
     * lista.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    solicitarReporte() {
      if (this.cargando || this.error || !this.hayDatos || (this.operacionReporteId !== "todas" && !this.datos.some(f => Number(f.id)===Number(this.operacionReporteId)))) return;
      this.$emit("ver-reporte", {
        tipo: "ventas-saldos-periodo",
        titulo: "Ventas y saldos por operación",
        periodo: this.periodoTexto,
        empresa_operaciones_id: this.operacionReporteId,
        anio: this.periodo.anio,
        mes: this.periodo.mes,
      });
    },
  },
};
</script>
