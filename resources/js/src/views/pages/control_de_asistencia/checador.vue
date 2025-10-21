<template>
  <div>
    <vs-tabs alignment="left" position="top" v-model="activeTab">
      <vs-tab label="REGISTROS DEL CHECADOR" class=""></vs-tab>
      <vs-tab label="TARJETAS DE ASISTENCIA"></vs-tab>
    </vs-tabs>
    <div :class="[activeTab === 0 ? 'flex flex-col flex-1' : '']" v-show="activeTab == 0">
      <Registros :datos="datosEmpresa" :erroresForm="erroresRegistros" @actualizar="actualizar"
        scope="this api replaced by slot-scope in 2.5.0+"></Registros>
    </div>
    <div :class="[activeTab === 1 ? 'flex flex-col flex-1' : '']" v-show="activeTab == 1">
      <Tarjetas></Tarjetas>
    </div>
    <Password v-if="operConfirmar" :show="operConfirmar" :callback-on-success="callback"
      @closeVerificar="operConfirmar = false" :accion="accionNombre"></Password>
    <pdf :show="verPdf" :pdf="pdfLink" @closePdf="verPdf = false"></pdf>
  </div>
</template>

<script>
import Tarjetas from "../control_de_asistencia/tarjetas";
import Registros from "../control_de_asistencia/registros";
import pdf from "../pdf_viewer.vue";

//componente de password
import Password from "../confirmar_password";
import vSelect from "vue-select";

export default {
  name: "ChecadorList",
  components: {
    "v-select": vSelect,
    Password,
    pdf,
    Tarjetas,
    Registros,
  },
  watch: {},
  data() {
    return {
      verPdf: false,
      pdfLink: "",
      operConfirmar: false,
      callback: Function,
      accionNombre: "",
      datosEmpresa: {},
      //errores por modulo
      erroresRegistros: {},
      erroresFuneraria: {},
      erroresRegistroPublico: {},
      //fin de errores por modulo
      activeTab: 0,
      ver: true,
      //los datos mandado del children por el emit
      modulo: "",
    };
  },
  methods: {
    //obtengo todos los datos de la empresa
  },
  // Lifecycle hooks
  created() {
    this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
  },
  mounted() {
    this.$log("Component mounted! " + this.$options.name);
    this.get_data(this.actual);
  },
  beforeDestroy() {
    this.$log("Before Component destroyed! " + this.$options.name);
  },
  destroyed() {
    this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
  },
};
</script>
