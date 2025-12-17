<template>
  <div class="centerx">
    <vs-popup :class="['forms-popup', 'popup-70', z_index]" :fullscreen="false" close="cancelar"
      title="Historial de ventas facturadas" :active="localShow" :ref="this.$options.name">
      <!--Listado de meses-->
      <div class="contenido">
        <div v-for="year in years" :key="year" class="flex flex-wrap justify-between gap-2">
          <div class="w-full bg-dark-500 text-white font-medium p-2">
            {{ year }}
          </div>
          <div class="w-full flex flex-wrap justify-between gap-2 py-4 pb-8">
            <span v-for="mes in mesesReversos" :key="year" @click="reporteVentasFacturadas(year, mes.value)"
              :class="['btn-span rounded cursor-pointer', (mesActual == mes.value && yearActual == year) ? 'primary-btn' : '']">
              {{ mes.label }}
            </span>
          </div>
        </div>
      </div>
    </vs-popup>
  </div>
</template>
<script>
//componente de password
import vSelect from "vue-select";
/**VARIABLES GLOBALES */

export default {
  name: "FormularioReporteVentasFacturadas",
  components: {
    "v-select": vSelect,

  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    z_index: {
      type: String,
      required: false,
      default: "z-index54k",
    },
  },
  watch: {
    show: {
      immediate: true, // runs when component is mounted too
      async handler(newValue) {
        if (newValue) {
          // Only listen when visible = true
          this.$popupManager.register(
            this,
            this.cancelar,
            ""
          );
        }
        this.localShow = newValue;
      },
    },
  },
  computed: {
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    mesesReversos() {
      return [...this.$meses_array].reverse();
    },
    years() {
      const startYear = 2021;
      const currentYear = new Date().getFullYear();
      const list = [];
      for (let y = startYear; y <= currentYear; y++) {
        list.push(y);
      }
      return list.reverse(); // optional: most recent first
    },
    mesActual() {
      return new Date().getMonth() + 1;
    },
    yearActual() {
      return new Date().getFullYear();
    }
  },
  data() {
    return {
      localShow: false,
    };
  },
  methods: {
    reporteVentasFacturadas(year = "", mes = "") {
      console.log("🚀 ~ mes:", mes)
      console.log("🚀 ~ year:", year)

    },
    cancelar() {
      this.$emit("closeVentana");
    },
  },
  // Lifecycle hooks
  created() {
    this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
  },
  mounted() {
    this.$log("Component mounted! " + this.$options.name);
    const now = new Date();
    this.selectedMonth = now.getMonth() + 1;
    this.selectedYear = now.getFullYear();
  },
  beforeDestroy() {
    this.$popupManager.unregister(this.$options.name);
  },
  destroyed() {
    this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
  },
};
</script>