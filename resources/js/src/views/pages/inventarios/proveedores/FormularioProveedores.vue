<template >
  <div class="centerx">
    <vs-popup
      :class="['forms-popup', 'popup-70', z_index]"
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="form-group">
        <div class="title-form-group">
          <span>Datos del Proveedor</span>
        </div>
        <div class="form-group-content">
          <div class="flex flex-wrap px-2">
            <div
              class="
                w-full
                sm:w-12/12
                md:w-6/12
                lg:w-6/12
                xl:w-6/12
                px-2
                input-text
              "
            >
              <label>
                Nombre Comercial / Empresa
                <span>(*)</span>
              </label>

              <vs-input
                ref="nombre_comercial"
                name="nombre_comercial"
                data-vv-as=" "
                v-validate.disabled="'required'"
                maxlength="100"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Funeraria Aeternus"
                v-model.trim="form.nombre_comercial"
              />
              <span>{{ errors.first("nombre_comercial") }}</span>
              <span v-if="this.errores.nombre_comercial">{{
                errores.nombre_comercial[0]
              }}</span>
            </div>
            <div
              class="
                w-full
                sm:w-12/12
                md:w-6/12
                lg:w-6/12
                xl:w-6/12
                px-2
                input-text
              "
            >
              <label> Razón Social </label>
              <vs-input
                name="razon_social"
                maxlength="125"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Servicios integrales de Sinaloa SA DE CV"
                v-model.trim="form.razon_social"
              />
              <span>{{ errors.first("razon_social") }}</span>
              <span v-if="this.errores.razon_social">{{
                errores.razon_social[0]
              }}</span>
            </div>

            <div
              class="
                w-full
                sm:w-12/12
                md:w-12/12
                lg:w-12/12
                xl:w-12/12
                px-2
                input-text
              "
            >
              <label> Domicilio Completo </label>
              <vs-input
                name="direccion"
                maxlength="250"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Domicilio Completo"
                v-model.trim="form.direccion"
              />
              <span>{{ errors.first("direccion") }}</span>
              <span v-if="this.errores.direccion">{{
                errores.direccion[0]
              }}</span>
            </div>

            <div
              class="
                w-full
                sm:w-12/12
                md:w-6/12
                lg:w-6/12
                xl:w-6/12
                px-2
                input-text
              "
            >
              <label>
                Nombre del contacto
                <span>(*)</span>
              </label>

              <vs-input
                name="nombre_contacto"
                data-vv-as=" "
                v-validate.disabled="'required'"
                maxlength="125"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Juán Pérez"
                v-model.trim="form.nombre_contacto"
              />
              <span>{{ errors.first("nombre_contacto") }}</span>
              <span v-if="this.errores.nombre_contacto">{{
                errores.nombre_contacto[0]
              }}</span>
            </div>

            <div
              class="
                w-full
                sm:w-12/12
                md:w-6/12
                lg:w-6/12
                xl:w-6/12
                px-2
                input-text
              "
            >
              <label>Teléfono</label>
              <vs-input
                maxlength="25"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ingrese el teléfono de contacto"
                v-model.trim="form.telefono"
              />
            </div>

            <div
              class="
                w-full
                sm:w-12/12
                md:w-12/12
                lg:w-12/12
                xl:w-12/12
                px-2
                input-text
              "
            >
              <label>Correo Electrónico</label>
              <vs-input
                name="email"
                data-vv-as=" "
                v-validate.disabled="'email'"
                maxlength="85"
                type="email"
                class="w-full pb-1 pt-1"
                placeholder="Ingrese el email"
                v-model.trim="form.email"
              />

              <span>{{ errors.first("email") }}</span>

              <span v-if="this.errores.email">{{ errores.email[0] }}</span>
            </div>

            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
            >
              <vs-textarea
                height="200px"
                maxlength="350"
                size="large"
                ref="nota"
                type="text"
                class="w-full pt-3 pb-3"
                placeholder="Ingrese una nota..."
                v-model.trim="form.nota"
              />
            </div>

            <!--fin de datos del titular-->

            <vs-divider />
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
              >Guardar Datos</span
            >
            <span class="" v-else>Modificar Datos</span>
          </vs-button>
        </div>
      </div>

      <!--fin proveedor-->
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
    <ConfirmarDanger
      :z_index="'z-index62k'"
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>
    <ConfirmarAceptar
      :z_index="'z-index62k'"
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero registrar a este proveedor'"
      :confirmarButton="'Guardar Proveedor'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import proveedores from "@services/proveedores";

import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
/**VARIABLES GLOBALES */

export default {
  components: {
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
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
    id_proveedor: {
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
      this.limpiarValidation();
      if (newValue == true) {
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["nombre_comercial"].$el.querySelector("input").focus()
        );
        if (this.getTipoformulario == "modificar") {
          this.title = "Modificar Proveedor";
          /**se cargan los datos al formulario */
          (async () => {
            await this.get_proveedor_by_id(this.get_proveedor_id);
          })();
        } else {
          this.title = "Registrar Proveedor";
        }
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
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_proveedor_id: {
      get() {
        return this.id_proveedor;
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
      accionNombre: "Modificar Proveedor",
      form: {
        /**en caso de modificar */
        id_proveedor_modificar: 0,
        nombre_comercial: "",
        razon_social: "",
        direccion: "",
        nombre_contacto: "",
        telefono: "",
        email: "",
        nota: "",
      },
      errores: [],
    };
  },
  methods: {
    async get_proveedor_by_id() {
      /**trae la informacion de el proveedor por id */
      this.$vs.loading();
      try {
        let res = await proveedores.get_proveedor_by_id(this.get_proveedor_id);
        let datos = res.data[0];
        //actualizo los datos en el formulario
        this.form.nombre_comercial = datos.nombre_comercial;
        this.form.razon_social = datos.razon_social;
        this.form.direccion = datos.direccion;
        this.form.nombre_contacto = datos.nombre_contacto;
        this.form.telefono = datos.telefono;
        this.form.email = datos.email;
        this.form.nota = datos.nota;
        this.form.id_proveedor_modificar = datos.id;
        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
        this.$vs.notify({
          title: "Modificar Proveedor",
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
              title: "Guardar Proveedor",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
          } else {
            this.errores = [];
            (async () => {
              if (this.getTipoformulario == "agregar") {
                this.callBackConfirmarAceptar = await this.guardar_proveedor;
                this.openConfirmarAceptar = true;
              } else {
                /**modificar, se valida con password */
                this.form.id_proveedor_modificar = this.get_proveedor_id;
                this.callback = await this.modificar_proveedor;
                this.operConfirmar = true;
              }
            })();
          }
        })
        .catch(() => {});
    },
    async guardar_proveedor() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await proveedores.guardar_proveedor(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Registro de Proveedores",
            text: "Se ha guardado el proveedor correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.$emit("retornar_id", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Registro de Proveedores",
            text: "Error al guardar el proveedor, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000,
          });
        }
        this.$vs.loading.close();
      } catch (err) {
        if (err.response) {
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 12000,
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;
            this.$vs.notify({
              title: "Registro de Proveedores",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 12000,
            });
          }
        }
        this.$vs.loading.close();
      }
    },

    async modificar_proveedor() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await proveedores.modificar_proveedor(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Modificación de Proveedores",
            text: "Se modificó el proveedor correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.$emit("retornar_id", res.data);
        } else {
          this.$vs.notify({
            title: "Modificación de Proveedores",
            text: "No se han realizado cambios, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "warning",
            time: 4000,
          });
        }
        this.cerrarVentana();
        this.$vs.loading.close();
      } catch (err) {
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
              title: "Modificación de Proveedores",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          }
        }
        this.$vs.loading.close();
      }
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
      this.form.nombre_comercial = "";
      this.form.razon_social = "";
      this.form.direccion = "";
      this.form.nombre_contacto = "";
      this.form.telefono = "";
      this.form.email = "";
      this.form.nota = "";
    },
    limpiarValidation() {
      this.$validator.pause();
      this.$nextTick(() => {
        this.$validator.errors.clear();
        this.$validator.fields.items.forEach((field) => field.reset());
        this.$validator.fields.items.forEach((field) =>
          this.errors.remove(field)
        );
        this.$validator.resume();
      });
    },
    closeChecker() {
      this.operConfirmar = false;
    },
  },
  created() {},
};
</script>