<template>
  <div class="centerx">
    <vs-popup
      :class="['forms-popup', 'popup-60', z_index]"
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <!--Datos de contacto-->
      <div class="form-group">
        <div class="title-form-group">
          <span>Seleccione los días que tiene de descanso</span>
        </div>
        <div class="form-group-content">
          <div class="w-full">
            <div class="text-center uppercase font-bold">
              {{ form.nombre }}
            </div>
            <table class="w-full mx-auto my-6">
              <tr>
                <td
                  v-for="(dias, index) in form.dias"
                  :key="dias.id"
                  class="text-center"
                >
                  <div class="font-medium">{{ dias.dia }}</div>
                  <div>
                    <img
                      v-if="dias.seleccionado == 1"
                      class="img-btn-32 mx-2 cursor-pointer"
                      src="@assets/images/switchon.svg"
                      title="Deshabilitar"
                      @click="
                        dias.seleccionado = dias.seleccionado == 1 ? 0 : 1
                      "
                    />
                    <img
                      v-else
                      class="img-btn-32 mx-2 cursor-pointer"
                      src="@assets/images/switchoff.svg"
                      title="Habilitar"
                      @click="
                        dias.seleccionado = dias.seleccionado == 1 ? 0 : 1
                      "
                    />
                  </div>
                </td>
              </tr>
            </table>
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
          <vs-button
            class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
            color="primary"
            @click="acceptAlert()"
          >
            <span class="">Guardar Cambios</span>
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
      :z_index="'z-index59k'"
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>

    <ConfirmarAceptar
      :z_index="'z-index59k'"
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero registrar a este cliente'"
      :confirmarButton="'Guardar Cambios'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import checador from "@services/checador";
import vSelect from "vue-select";

import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import { configdateTimePickerWithTime } from "@/VariablesGlobales";
/**VARIABLES GLOBALES */

export default {
  components: {
    "v-select": vSelect,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    usuario_id: {
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
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["registro"].$el.querySelector("input").focus()
        );
        (async () => {
          this.title = "Actualizar Días de Descanso";
          /**se cargan los datos al formulario */
          await this.get_dias_descanso_empleado();
        })();
      }
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
    get_usuario_id: {
      get() {
        return this.usuario_id;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Guardar Cambios",
      form: {
        empleado_id: 0,
        nombre: "",
        dias: [
          { id: 1, dia: "Lunes", seleccionado: 0 },
          { id: 2, dia: "Martes", seleccionado: 0 },
          { id: 3, dia: "Miércoles", seleccionado: 0 },
          { id: 4, dia: "Jueves", seleccionado: 0 },
          { id: 5, dia: "Viernes", seleccionado: 0 },
          { id: 6, dia: "Sábado", seleccionado: 0 },
          { id: 7, dia: "Domingo", seleccionado: 0 },
        ],
      },
      errores: [],
    };
  },
  methods: {
    async get_dias_descanso_empleado() {
      try {
        this.$vs.loading();
        let res = await checador.get_dias_descanso_empleado(
          this.get_usuario_id
        );
        res = res.data;
        this.form.nombre = res.nombre;
        this.form.empleado_id = res.id;
        res.dias_descanso.forEach((element) => {
          if (element.dias_id == 1) {
            this.form.dias[0].seleccionado = 1;
          } else if (element.dias_id == 2) {
            this.form.dias[1].seleccionado = 1;
          } else if (element.dias_id == 3) {
            this.form.dias[2].seleccionado = 1;
          } else if (element.dias_id == 4) {
            this.form.dias[3].seleccionado = 1;
          } else if (element.dias_id == 5) {
            this.form.dias[4].seleccionado = 1;
          } else if (element.dias_id == 6) {
            this.form.dias[5].seleccionado = 1;
          } else if (element.dias_id == 7) {
            this.form.dias[6].seleccionado = 1;
          }
        });
        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
      }
    },
    acceptAlert() {
      /**modificar, se valida con password */
      this.callback = this.guardar_dias_descanso;
      this.operConfirmar = true;
    },
    guardar_dias_descanso() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      checador
        .guardar_dias_descanso(this.form)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Guardar Cambioss",
              text: "Se ha guardado el registro correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 8000,
              position: "bottom-right",
            });
            this.$emit("CloseFormularioDiasDescanso", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Guardar Cambioss",
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
                title: "Guardar Cambioss",
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
                title: "Guardar Cambioss",
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
      this.$emit("CloseFormularioDiasDescanso");
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
      this.$emit("CloseFormularioDiasDescanso");
    },
    //regresa los datos a su estado inicial
    limpiarVentana() {
      this.form.dias = [
        { id: 1, dia: "Lunes", seleccionado: 0 },
        { id: 2, dia: "Martes", seleccionado: 0 },
        { id: 3, dia: "Miércoles", seleccionado: 0 },
        { id: 4, dia: "Jueves", seleccionado: 0 },
        { id: 5, dia: "Viernes", seleccionado: 0 },
        { id: 6, dia: "Sábado", seleccionado: 0 },
        { id: 7, dia: "Domingo", seleccionado: 0 },
      ];
    },
    closeChecker() {
      this.operConfirmar = false;
    },
  },
  created() {},
};
</script>
