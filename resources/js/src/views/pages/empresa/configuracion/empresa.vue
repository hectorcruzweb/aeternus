<template>
  <div>
    <vs-tabs alignment="left" position="top" v-model="activeTab">
      <vs-tab label="FUNERARIA" class=""></vs-tab>
      <vs-tab label="REGISTRO PÚBLICO"></vs-tab>
      <vs-tab label="CEMENTERIO"></vs-tab>
      <vs-tab label="FIRMA ELECTRÓNICA"></vs-tab>
      <!--<vs-tab label="FACTURACIÓN" icon="fingerprint"></vs-tab>-->
    </vs-tabs>
    <div class="" v-show="activeTab == 0">
      <Funeraria
        :datos="datosEmpresa"
        :erroresForm="erroresFuneraria"
        @actualizar="actualizar"
      ></Funeraria>
    </div>
    <div class="" v-show="activeTab == 1">
      <RegistroPublico
        :datos="datosEmpresa"
        :erroresForm="erroresRegistroPublico"
        @actualizar="actualizar"
      ></RegistroPublico>
    </div>

    <div class=" " v-show="activeTab == 2">
      <Cementerio
        :datos="datosEmpresa"
        :erroresForm="erroresCementerio"
        @actualizar="actualizar"
      ></Cementerio>
    </div>

    <div class=" " v-show="activeTab == 3">
      <Facturacion
        :datos="datosEmpresa"
        :erroresForm="erroresFacturacion"
        @actualizar="actualizar"
      ></Facturacion>
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
import Funeraria from "../configuracion/funeraria";
import RegistroPublico from "../configuracion/registro_publico";
import Cementerio from "../configuracion/cementerio";
import Facturacion from "../configuracion/facturacion";
import pdf from "../../pdf_viewer";

//componente de password
import Password from "../../confirmar_password";

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
    Cementerio,
    Facturacion,
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
      erroresFuneraria: {},
      erroresRegistroPublico: {},
      erroresCementerio: {},
      erroresFacturacion: {},
      //fin de errores por modulo
      activeTab: 0,
      ver: true,
      //los datos mandado del children por el emit
      datos_para_actualizar: [],
      modulo: "",
    };
  },
  methods: {
    //obtengo todos los datos de la empresa
    get_datos_empresa() {
      this.$vs.loading();
      empresa
        .get_datos_empresa()
        .then((res) => {
          this.$vs.loading.close();
          //mando los datos
          this.datosEmpresa = res.data[0];
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    actualizar(datos) {
      this.datos_para_actualizar = datos;
      this.callback = this.modificarDatos;
      this.operConfirmar = true;
    },

    //modificamos los datos
    modificarDatos() {
      //aqui mando guardar los datos
      if (this.datos_para_actualizar.modulo == "funeraria") {
        this.erroresFuneraria = {};
        this.modulo = "funeraria";
      } else if (this.datos_para_actualizar.modulo == "registro_publico") {
        this.erroresRegistroPublico = {};
        this.modulo = "registro_publico";
      } else if (this.datos_para_actualizar.modulo == "cementerio") {
        this.erroresCementerio = {};
        this.modulo = "cementerio";
      } else {
        //facturacion
        this.erroresFacturacion = {};
        this.modulo = "facturacion";
      }

      this.modificarInformacion();
    },

    modificarInformacion() {
      this.$vs.loading();
      empresa
        .modificarInformacion(this.datos_para_actualizar, this.modulo)
        .then((res) => {
          if (res.data >= 0) {
            //success
            this.$vs.notify({
              title: "Actualizar Datos",
              text: "Se han guardado los cambios correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000,
            });
          } else {
            this.$vs.notify({
              title: "Actualizar Datos",
              text: "Error al guardar cambios, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 4000,
            });
          }

          this.$vs.loading.close();
        })
        .catch((err) => {
          if (err.response) {
            if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text:
                  "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 4000,
              });
            } else if (err.response.status == 422) {
              //checo si existe cada error
              if (this.datos_para_actualizar.modulo == "funeraria") {
                this.erroresFuneraria = err.response.data.error;
              } else if (
                this.datos_para_actualizar.modulo == "registro_publico"
              ) {
                this.erroresRegistroPublico = err.response.data.error;
              } else if (this.datos_para_actualizar.modulo == "cementerio") {
                this.erroresCementerio = err.response.data.error;
              } else {
                //facturacion
                this.erroresFacturacion = err.response.data.error;
              }
            }
          }
          this.$vs.loading.close();
        });
    },
  },
  created() {
    this.get_datos_empresa();
  },
};
</script>
