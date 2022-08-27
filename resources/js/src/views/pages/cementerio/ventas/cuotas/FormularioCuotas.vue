<template>
  <div class="centerx">
    <vs-popup
      class="forms-popup popup-50"
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="form-group">
        <div class="title-form-group">Cuota de Mantenimiento</div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full px-2 input-text">
              <label class="">
                Periodo de cuota (Descripción)
                <span class="">(*)</span>
              </label>
              <vs-input
                name="descripcion"
                data-vv-as=" "
                data-vv-validate-on="blur"
                v-validate="'required'"
                maxlength="100"
                type="text"
                class="w-full"
                placeholder="Ene 2021 - Enero 2022"
                v-model="form.descripcion"
              />
              <span class="">{{ errors.first("descripcion") }}</span>
              <span class="" v-if="this.errores.descripcion">{{
                errores.descripcion[0]
              }}</span>
            </div>
            <div class="w-full lg:w-6/12 px-2 input-text">
              <label class="">
                $ Cuota Total
                <span class="">(*)</span>
              </label>
              <vs-input
                name="cuota_total"
                data-vv-as=" "
                data-vv-validate-on="blur"
                v-validate="'required|decimal'"
                maxlength="10"
                type="text"
                class="w-full"
                placeholder="Ej. $1000.00"
                v-model="form.cuota_total"
              />
              <span class="">{{ errors.first("cuota_total") }}</span>
              <span class="" v-if="this.errores.cuota_total">{{
                errores.cuota_total[0]
              }}</span>
            </div>

            <div class="w-full lg:w-6/12 px-2 input-text">
              <label class="">
                % IVA
                <span class="">(*)</span>
              </label>
              <vs-input
                name="tasa_iva"
                data-vv-as=" "
                data-vv-validate-on="blur"
                v-validate="'required|integer|min_value:0|max_value:16'"
                maxlength="2"
                type="text"
                class="w-full"
                placeholder=""
                v-model="form.tasa_iva"
              />
              <span class="">{{ errors.first("tasa_iva") }}</span>
              <span class="" v-if="this.errores.tasa_iva">{{
                errores.tasa_iva[0]
              }}</span>
            </div>

            <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
              <label class="">
                Fecha de inicio (Año-Mes-Dia)
                <span>(*)</span>
              </label>
              <flat-pickr
                name="fecha_inicio"
                data-vv-as=" "
                v-validate:fecha_inicio_validacion_computed="'required'"
                :config="configdateTimePicker"
                v-model="form.fecha_inicio"
                placeholder="Fecha de inicio"
                class="w-full"
              />
              <span>{{ errors.first("fecha_inicio") }}</span>
              <span v-if="this.errores.fecha_inicio">{{
                errores.fecha_inicio[0]
              }}</span>
            </div>
            <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
              <label class="">
                Fecha final (Año-Mes-Dia)
                <span>(*)</span>
              </label>
              <flat-pickr
                name="fecha_fin"
                data-vv-as=" "
                v-validate:fecha_fin_validacion_computed="'required'"
                :config="configdateTimePickerFechasCaducidad"
                v-model="form.fecha_fin"
                placeholder="Fecha final"
                class="w-full"
              />
              <span>{{ errors.first("fecha_fin") }}</span>
              <span v-if="this.errores.fecha_fin">{{
                errores.fecha_fin[0]
              }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bottom-buttons-section">
        <div class="text-advice">
          <span class="ojo-advice">Ojo:</span>
          Por favor revise la información ingresada, si todo es correcto de
          click en el "Botón de Abajo”.
        </div>

        <div class="w-full">
          <vs-button
            class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
            color="primary"
            @click="acceptAlert()"
          >
            <span class="" v-if="this.getTipoformulario == 'agregar'"
              >Guardar Cuota</span
            >
            <span class="" v-else>Modificar Cuota</span>
          </vs-button>
        </div>
      </div>
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
    <ConfirmarDanger
      :z_index="'z-index58k'"
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>

    <ConfirmarAceptar
      :z_index="'z-index58k'"
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero registrar este Cuota'"
      :confirmarButton="'Registrar Cuota'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";

import {
  configdateTimePicker,
  configdateTimePickerFechasCaducidad,
} from "@/VariablesGlobales";
/**VARIABLES GLOBALES */

export default {
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
    id_cuota: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["descripcion"].$el.querySelector("input").focus()
        );

        (async () => {
          if (this.getTipoformulario == "modificar") {
            this.title = "Modificar Cuota de Mantenimiento";
            this.get_cuota_by_id();
            /**se cargan los datos al formulario */
          } else {
            this.title = "Registrar Cuota de Mantenimiento";
          }
        })();
      }
    },
  },
  computed: {
    fecha_inicio_validacion_computed: function () {
      return this.form.fecha_inicio;
    },

    fecha_fin_validacion_computed: function () {
      return this.form.fecha_fin;
    },

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
    get_cuota_id: {
      get() {
        return this.id_cuota;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      configdateTimePicker: configdateTimePicker,
      configdateTimePickerFechasCaducidad: configdateTimePickerFechasCaducidad,
      datosCuota: [],
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Cuota",

      form: {
        /**en caso de modificar */
        id_cuota: 0,
        /**nuevos datos del form */
        fecha_inicio: "",
        fecha_fin: "",
        cuota_total: "",
        tasa_iva: 16,
        descripcion: "",
      },
      errores: [],
    };
  },
  methods: {
    /**trae la info dla cuota */
    async get_cuota_by_id() {
      this.$vs.loading();
      try {
        let res = await cementerio.get_cuota_by_id(this.get_cuota_id);
        this.datosCuota = res.data[0];
        this.form.descripcion = this.datosCuota.descripcion;
        this.form.tasa_iva = this.datosCuota.tasa_iva;
        this.form.cuota_total = parseFloat(this.datosCuota.cuota_total);
        this.form.fecha_inicio = this.datosCuota.fecha_inicio;
        this.form.fecha_fin = this.datosCuota.fecha_fin;
        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
        this.$vs.notify({
          title: "Modificar Cuotas",
          text: "Ocurrió un error al traer la informacion, reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "4000",
        });
        this.cerrarVentana();
      }
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Registro de Cuotas",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
          } else {
            this.errores = [];
            if (this.getTipoformulario == "agregar") {
              this.callBackConfirmarAceptar = this.registrar_cuota;
              this.openConfirmarAceptar = true;
            } else {
              /**modificar, se valida con password */
              this.form.id_cuota = this.get_cuota_id;
              this.callback = this.update_cuota;
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => {});
    },

    registrar_cuota() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      cementerio
        .registrar_cuota(this.form)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Registro de Cuotas",
              text: "Se ha guardado la cuota correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000,
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Registro de Cuotas",
              text: "Error al guardar la cuota, por favor reintente.",
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
                text: "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 4000,
              });
            } else if (err.response.status == 422) {
              //checo si existe cada error
              this.errores = err.response.data.error;
              this.$vs.notify({
                title: "Registro de Cuotas",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Registro de Cuotas",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
              });
            }
          }
          this.$vs.loading.close();
        });
    },

    update_cuota() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      cementerio
        .update_cuota(this.form)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Modificación de Cuotas",
              text: "Se modificó la cuota correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000,
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Modificación de Cuotas",
              text: "Error al guardar la cuota, por favor reintente.",
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
                text: "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 4000,
              });
            } else if (err.response.status == 422) {
              //checo si existe cada error
              this.errores = err.response.data.error;
              this.$vs.notify({
                title: "Modificación de Cuotas",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Modificación de Cuotas",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
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
      this.botonConfirmarSinPassword = "Salir y limpiar";
      this.accionConfirmarSinPassword =
        "Esta acción limpiará los datos que capturó en el formulario.";
      this.openConfirmarSinPassword = true;
      this.callBackConfirmar = this.cerrarVentana;
    },
    cerrarVentana() {
      this.openConfirmarSinPassword = false;
      this.limpiarVentana();
      this.$emit("closeVentana");
    },
    //regresa los datos a su estado inicial
    limpiarVentana() {
      /**en caso de modificar */
      this.form.id_cuota = 0;
      /**datos */
      this.form.descripcion = "";
      this.form.cuota_total = "";
      this.form.fecha_fin = "";
      this.form.fecha_inicio = "";
      this.errores = [];
    },

    closeChecker() {
      this.operConfirmar = false;
    },
  },
  created() {},
};
</script>
