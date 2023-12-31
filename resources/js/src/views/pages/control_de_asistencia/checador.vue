<template>
  <div>
    <vs-tabs alignment="left" position="top" v-model="activeTab">
      <vs-tab label="REGISTROS DEL CHECADOR" class=""></vs-tab>
      <vs-tab label="PERMISOS"></vs-tab>
      <vs-tab label="TARJETAS DE ASISTENCIA"></vs-tab>
    </vs-tabs>
    <div class="" v-show="activeTab == 0">
      <Registros
        :datos="datosEmpresa"
        :erroresForm="erroresRegistros"
        @actualizar="actualizar"
      ></Registros>
    </div>
    <div class="" v-show="activeTab == 1">
      <!--
      <RegistroPublico
        :datos="datosEmpresa"
        :erroresForm="erroresRegistroPublico"
        @actualizar="actualizar"
      ></RegistroPublico>
    --></div>
    <div class=" " v-show="activeTab == 2">
      <!--
      <Funeraria
        :datos="datosEmpresa"
        :erroresForm="erroresFuneraria"
        @actualizar="actualizar"
      ></Funeraria>
      -->
    </div>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="operConfirmar = false"
      :accion="accionNombre"
    ></Password>
    <pdf :show="verPdf" :pdf="pdfLink" @closePdf="verPdf = false"></pdf>
  </div>
</template>

<script>
import Funeraria from "../control_de_asistencia/funeraria";
import RegistroPublico from "../control_de_asistencia/registro_publico";
import Registros from "../control_de_asistencia/registros";
import pdf from "../pdf_viewer.vue";

//componente de password
import Password from "../confirmar_password";

import empresa from "@services/empresa";
/**VARIABLES GLOBALES */
import {
  mostrarOptions,
  estadosOptions,
  rolesOptions,
} from "@/VariablesGlobales";
import vSelect from "vue-select";

export default {
  components: {
    "v-select": vSelect,
    Password,
    pdf,
    Funeraria,
    RegistroPublico,
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
  created() {},
};
</script>
