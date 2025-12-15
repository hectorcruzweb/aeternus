<template>
  <div class="centerx">
    <vs-popup :class="['forms-popup', 'popup-50', z_index]" :fullscreen="false" close="cancelar" :title="title"
      :active="localShow" :ref="this.$options.name">
      <!--Datos de contacto-->
      <div class="form-group">
        <div class="title-form-group">
          <span>Datos del Registro</span>
        </div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 input-text">
              <label class="">Fecha del Registro.</label>
              <flat-pickr name="fechahora" :config="configdateTimePickerWithTime" v-model="form.fechahora"
                placeholder="Seleccione una fecha y hora" v-validate:fechahora_computed.immediate="'required'"
                class="w-full" />
              <span class="">
                {{ errors.first("fechahora") }}
              </span>
              <span class="" v-if="this.errores.fechahora">
                {{ errores.fechahora[0] }}
              </span>
            </div>
            <div class="w-full md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 input-text">
              <label class="">
                Tipo de Registro
                <span class="">(*)</span>
              </label>
              <v-select :options="tipos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="form.tipo"
                class="w-full" v-validate:tipo_computed.immediate="'required'" name="tipo_registro_id" data-vv-as=" ">
                <div slot="no-options">Seleccione una opción</div>
              </v-select>
              <span class="">
                {{ errors.first("tipo_registro_id") }}
              </span>
              <span class="" v-if="this.errores['tipo_registro_id.value']">{{
                errores["tipo_registro_id.value"][0]
              }}</span>
            </div>

            <div class="w-full md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text">
              <label class="">
                Empleado
                <span class="">(*)</span>
              </label>
              <v-select :options="empleados" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="form.empleado"
                class="w-full" v-validate="'required'" v-validate:empleado_computed.immediate="'required'"
                name="empleado" data-vv-as=" ">
                <div slot="no-options">Seleccione una opción</div>
              </v-select>
              <span class="">
                {{ errors.first("empleado") }}
              </span>
              <span class="" v-if="this.errores['empleado.value']">{{
                errores["empleado.value"][0]
              }}</span>
            </div>
          </div>
        </div>
      </div>
      <!--Datos de contacto-->

      <div class="bottom-buttons-section">
        <div class="text-advice">
          <span class="ojo-advice">Ojo:</span>
          Por favor revise la información ingresada, si todo es correcto de
          click en el "Botón de Abajo”.
        </div>

        <div class="w-full">
          <vs-button class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="primary" @click="acceptAlert()">
            <span class="" v-if="this.getTipoformulario == 'agregar'">Guardar Registro</span>
            <span class="" v-else>Modificar Registro</span>
          </vs-button>
        </div>
      </div>
    </vs-popup>
    <Password v-if="operConfirmar" :show="operConfirmar" :callback-on-success="callback" @closeVerificar="closeChecker"
      :accion="accionNombre"></Password>
    <ConfirmarDanger v-if="openConfirmarSinPassword" :z_index="'z-index59k'" :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword" :confirmarButton="botonConfirmarSinPassword"></ConfirmarDanger>

    <ConfirmarAceptar v-if="openConfirmarAceptar" :z_index="'z-index59k'" :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar" @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero registrar a este cliente'" :confirmarButton="'Guardar Registro'">
    </ConfirmarAceptar>
  </div>
</template>
<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import checador from "@services/checador";
import sat from "@services/sat";
import vSelect from "vue-select";

import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import { configdateTimePickerWithTime } from "@/VariablesGlobales";
/**VARIABLES GLOBALES */

export default {
  name: "FormularioReporteVentasFacturadas",
  components: {
    "v-select": vSelect,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
    flatPickr,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    tipo: {
      type: String,
      required: true,
    },
    id_registro: {
      type: Number,
      required: false,
      default: 0,
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
          /**obtengo los datos para llenar el form con los permisos segun su modulo */
          await this.get_empleados();
          if (this.getTipoformulario == "modificar") {
            this.title = "Modificar Datos del Registro";
            /**se cargan los datos al formulario */
            await this.get_registro_by_id(this.get_registro_id);
          } else {
            this.title = "Crear Nuevo Registro";
          }
          this.$popupManager.register(
            this,
            this.cancelar,
            "fechahora"
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
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_registro_id: {
      get() {
        return this.id_registro;
      },
      set(newValue) {
        return newValue;
      },
    },
    tipo_computed: function () {
      return this.form.tipo.value;
    },
    empleado_computed: function () {
      return this.form.empleado.value;
    },
    fechahora_computed: function () {
      return this.form.fechahora;
    },
  },
  data() {
    return {
      localShow: false,
      configdateTimePickerWithTime: configdateTimePickerWithTime,
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Cliente",
      tipos: [
        {
          value: "1",
          label: "Entrada",
        },
        {
          value: "0",
          label: "Salida",
        },
      ],
      empleados: [],
      form: {
        empleado: { label: "Seleccione 1", value: "" },
        tipo: { label: "Entrada", value: "1" },
        id_registro_modificar: 0,
        fechahora: "",
      },
      errores: [],
    };
  },
  methods: {
    async get_empleados() {
      try {
        //this.$vs.loading();
        let res = await checador.get_empleados_todos();
        this.empleados.push({
          value: "",
          label: "Seleccione 1",
        });
        res.data.forEach((element) => {
          this.empleados.push({
            value: element.id,
            label: element.nombre,
          });
        });
        this.empleado = this.empleados[0];
        //this.$vs.loading.close();
      } catch (error) {
        //this.$vs.loading.close();
      }
    },

    async get_registro_by_id() {
      /**trae la informacion de el registro por id */
      this.$vs.loading();
      await checador
        .get_registro_by_id(this.get_registro_id)
        .then((res) => {
          if (res) {
            let registro = res.data[0];
            //actualizo los datos en el formulario
            var fecha_cremacion = registro.fecha_hora.substr(0, 10).split("-");
            var hora_cremacion = registro.fecha_hora.substr(11, 19).split(":");
            //yyyy-mm-dd hh:mm
            this.form.fechahora = new Date(
              fecha_cremacion[0],
              fecha_cremacion[1] - 1,
              fecha_cremacion[2],
              hora_cremacion[0],
              hora_cremacion[1]
            );

            if (registro.tipo_registro_id == 1) {
              this.form.tipo = this.tipos[0];
            } else {
              this.form.tipo = this.tipos[1];
            }
            //selecciono empleado
            var empleado_disponible = false;
            this.empleados.forEach((element, index) => {
              if (element.value == registro.usuarios_id) {
                empleado_disponible = true;
                this.form.empleado = element;
                return;
              }
            });
          }
          if (empleado_disponible == false) {
            this.$vs.notify({
              title: "Modificar Registro",
              text: "Verifique que el empleado esté habilitado para modificar este registro.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "8000",
            });
            this.$vs.loading.close();
            this.cerrarVentana();
          }
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: "Modificar Registro",
            text: "Ocurrió un error al traer la informacion, reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "8000",
          });
          this.cerrarVentana();
        });
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title:
                this.getTipoformulario == "agregar"
                  ? "Guardar Registro"
                  : "Modificar Registro",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "8000",
            });
          } else {
            this.errores = [];
            if (this.getTipoformulario == "agregar") {
              this.callBackConfirmarAceptar =
                this.guardar_registro_administrativo;
              this.openConfirmarAceptar = true;
            } else {
              /**modificar, se valida con password */
              this.form.id_registro_modificar = this.get_registro_id;
              this.callback = this.modificar_registro_administrativo;
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => { });
    },

    guardar_registro_administrativo() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      let request = {
        fechahora: this.form.fechahora,
        tipo_registro_id: this.form.tipo.value,
        id_usuario: this.form.empleado.value,
      };
      checador
        .guardar_registro_administrativo(request)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Guardar Registros",
              text: "Se ha guardado el registro correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 8000,
              position: "bottom-right",
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Guardar Registros",
              text: "Error al guardar el registro, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 8000,
              position: "bottom-right",
            });
          }
          this.$vs.loading.close();
        })
        .catch((err) => {
          if (err.response) {
            if (err.response.status == 409) {
              /**CONFLICT ERROR */
              this.$vs.notify({
                title: "Guardar Registros",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
                position: "bottom-right",
              });
            } else if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text: "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 8000,
                position: "bottom-right",
              });
            } else if (err.response.status == 422) {
              //checo si existe cada error
              this.errores = err.response.data.error;
              this.$vs.notify({
                title: "Guardar Registros",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
                position: "bottom-right",
              });
            }
          }
          this.$vs.loading.close();
        });
    },

    modificar_registro_administrativo() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();

      let request = {
        fechahora: this.form.fechahora,
        tipo_registro_id: this.form.tipo.value,
        id_usuario: this.form.empleado.value,
        id_registro_modificar: this.get_registro_id,
      };
      checador
        .modificar_registro_administrativo(request)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Modificación de Registros",
              text: "Se modificó el registro correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 8000,
              position: "bottom-right",
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Modificación de Registros",
              text: "Error al guardar modificar registro, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 8000,
              position: "bottom-right",
            });
          }
          this.$vs.loading.close();
        })
        .catch((err) => {
          if (err.response) {
            if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Modificar Registros",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
                position: "bottom-right",
              });
            } else if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text: "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 8000,
                position: "bottom-right",
              });
            } else if (err.response.status == 422) {
              //checo si existe cada error
              this.errores = err.response.data.error;
              this.$vs.notify({
                title: "Modificación de Registros",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
                position: "bottom-right",
              });
            }
          }
          this.$vs.loading.close();
        });
    },
    cancel() {
      this.$emit("closeVentana");
    },

    cancelar() {
      if (this.getTipoformulario === "modificar") {
        this.botonConfirmarSinPassword = "Salir y limpiar";
        this.accionConfirmarSinPassword =
          "Esta acción limpiará los datos que capturó en el formulario.";
        this.openConfirmarSinPassword = true;
        this.callBackConfirmar = this.cerrarVentana;
      } else {
        this.cerrarVentana();
      }

    },
    cerrarVentana() {
      //this.openConfirmarSinPassword = false;
      //this.limpiarVentana();
      this.$emit("closeVentana");
    },
    //regresa los datos a su estado inicial
    limpiarVentana() {
      this.empleados = [];
    },

    closeChecker() {
      this.operConfirmar = false;
    },
  },
  // Lifecycle hooks
  created() {
    this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
  },
  mounted() {
    this.$log("Component mounted! " + this.$options.name);
  },
  beforeDestroy() {
    this.$popupManager.unregister(this.$options.name);
  },
  destroyed() {
    this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
  },
};
</script>
