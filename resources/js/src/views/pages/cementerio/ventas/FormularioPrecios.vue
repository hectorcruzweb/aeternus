<template>
  <div class="centerx">
    <vs-popup :class="['forms-popup popup-50', z_index]" close="cancelar" :title="title" :active="localShow"
      :ref="this.$options.name">
      <div class="form-group">
        <div class="title-form-group">Plan de Financiamiento</div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full px-2 input-text">
              <label class="">
                Descripción/Nombre del Plan
                <span class="">(*)</span>
              </label>
              <vs-input ref="descripcion" name="descripcion" data-vv-as=" " data-vv-validate-on="blur"
                v-validate="'required'" maxlength="85" type="text" class="w-full" placeholder="Ej. Pago de Contado"
                v-model="form.descripcion" />
              <span class="">{{ errors.first("descripcion") }}</span>
              <span class="" v-if="this.errores.descripcion">{{
                errores.descripcion[0]
              }}</span>
            </div>
            <div class="w-full lg:w-6/12 px-2 input-text">
              <label class=" ">
                Tipo de Financiamiento
                <span class="">(*)</span>
              </label>
              <v-select :options="financiamientos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="form.contado_b" class="w-full" name="contado_b" data-vv-as=" ">
                <div slot="no-options">Seleccione una opción</div>
              </v-select>
              <span class="">{{ errors.first("contado_b") }}</span>
              <span class="" v-if="this.errores['contado_b.value']">{{
                errores["contado_b.value"][0]
              }}</span>
            </div>
            <div class="w-full lg:w-6/12 px-2 input-text">
              <label class=" ">
                Tipo de Propiedad
                <span class="">(*)</span>
              </label>
              <v-select :options="tipos_propiedad" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="form.tipo_propiedades_id" v-validate:tipo_propiedad_computed.immediate="'required'"
                class="w-full" name="tipo_propiedades_id" data-vv-as=" ">
                <div slot="no-options">Seleccione una opción</div>
              </v-select>
              <span class="">{{ errors.first("tipo_propiedades_id") }}</span>
              <span class="" v-if="this.errores['tipo_propiedades_id.value']">{{
                errores["tipo_propiedades_id.value"][0]
              }}</span>
            </div>
            <div class="w-full lg:w-4/12 px-2 input-text">
              <label class=" ">
                Pagos/Meses a Pagar
                <span class="">(*)</span>
              </label>
              <vs-input name="financiamiento" data-vv-as=" " data-vv-validate-on="blur" v-validate="'required|integer'"
                maxlength="2" type="text" class="w-full" placeholder="Ej. 1" :disabled="es_contado"
                v-model="form.financiamiento" />
              <span class="">{{ errors.first("financiamiento") }}</span>
              <span class="" v-if="this.errores.financiamiento">{{
                errores.financiamiento[0]
              }}</span>
            </div>
            <div class="w-full lg:w-4/12 px-2 input-text">
              <label class=" ">
                $ Costo Neto(Con IVA)
                <span class="">(*)</span>
              </label>
              <vs-input ref="costo_neto" name="costo_neto" data-vv-as=" " data-vv-validate-on="blur"
                v-validate="'required'" maxlength="10" type="text" class="w-full" placeholder="Ej. $1000.00"
                v-model="form.costo_neto" />
              <span class="">{{ errors.first("costo_neto") }}</span>
              <span class="" v-if="this.errores.costo_neto">{{
                errores.costo_neto[0]
              }}</span>
            </div>
            <div class="w-full lg:w-4/12 px-2 input-text">
              <label class=" ">
                $ Pago Inicial Mínimo
                <span class="">(*)</span>
              </label>
              <vs-input name="pago_inicial" data-vv-as=" " data-vv-validate-on="blur" v-validate="'required|numeric'"
                maxlength="10" type="text" class="w-full" placeholder="Ej. $1000.00" v-model="form.pago_inicial" />
              <span class="">{{ errors.first("pago_inicial") }}</span>
              <span class="" v-if="this.errores.pago_inicial">{{
                errores.pago_inicial[0]
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
          <vs-button class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="primary" @click="acceptAlert()">
            <span class="" v-if="this.getTipoformulario == 'agregar'">Guardar Precio</span>
            <span class="" v-else>Modificar Precio</span>
          </vs-button>
        </div>
      </div>
    </vs-popup>
    <Password v-if="operConfirmar" :show="operConfirmar" :callback-on-success="callback" @closeVerificar="closeChecker"
      :accion="accionNombre" :z_index="'z-index59k'"></Password>
    <ConfirmarDanger v-if="openConfirmarSinPassword" :z_index="'z-index59k'" :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword" :confirmarButton="botonConfirmarSinPassword"></ConfirmarDanger>

    <ConfirmarAceptar v-if="openConfirmarAceptar" :z_index="'z-index59k'" :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar" @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero registrar este precio'" :confirmarButton="'Registrar Precio'">
    </ConfirmarAceptar>
  </div>
</template>
<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
import vSelect from "vue-select";

import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
/**VARIABLES GLOBALES */

export default {
  name: "FormularioPreciosCementerio",
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
    tipo: {
      type: String,
      required: true,
    },
    id_precio: {
      type: Number,
      required: false,
      default: 0,
    },
    z_index: {
      type: String,
      required: false,
      default: "z-index58k",
    },
  },
  watch: {
    show: {
      immediate: true, // runs when component is mounted too
      async handler(newValue) {
        if (newValue) {
          this.form.contado_b = {
            value: 1,
            label: "Pago de Contado/Uso Inmediato",
          };
          /**de manera asincrona para evitar errores en popular los selects */
          /**cargando los tipos de propeidades */
          await this.get_tipo_propiedades();
          if (this.getTipoformulario == "modificar") {
            this.title = "Modificar Financiamiento";
            this.get_precio_by_id();
            /**se cargan los datos al formulario */
          } else {
            this.title = "Registrar Nuevo Financiamiento";
          }
          this.$popupManager.register(this, this.cancelar, "descripcion");
        } else {
          this.$popupManager.unregister(this.$options.name);
        }
        this.localShow = newValue;
      },
    },
    "form.contado_b": function (newValue, oldValue) {
      if (newValue.value == 1) {
        this.form.financiamiento = 1;
      } else {
        if (this.getTipoformulario == "modificar") {
          /**se restablece el precio que estaba capturado */
          this.form.financiamiento = this.datosPrecio.financiamiento;
        }
      }
    },
  },
  computed: {
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_precio_id: {
      get() {
        return this.id_precio;
      },
      set(newValue) {
        return newValue;
      },
    },
    tipo_propiedad_computed: function () {
      return this.form.tipo_propiedades_id.value;
    },
    es_contado: function () {
      if (this.form.contado_b.value == 1) {
        return true;
      } else {
        return false;
      }
    },
  },
  data() {
    return {
      localShow: false,
      datosPrecio: [],
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Precio",
      financiamientos: [
        {
          value: "1",
          label: "Pago de Contado/Uso Inmediato",
        },
        {
          value: "0",
          label: "Pago a Meses/Uso a Futuro",
        },
      ],
      descuento: [
        {
          value: "1",
          label: "Si",
        },
        {
          value: "0",
          label: "No",
        },
      ],
      tipos_propiedad: [],
      form: {
        /**en caso de modificar */
        id_precio_modificar: 0,
        /**datos */
        descripcion: "",
        contado_b: {},
        financiamiento: "",
        pago_inicial: "",
        costo_neto: "",
        tipo_propiedades_id: { value: "", label: "Seleccione 1" },
      },
      errores: [],
    };
  },
  methods: {
    async get_tipo_propiedades() {
      this.$vs.loading();
      try {
        let res = await cementerio.get_tipo_propiedades();
        /**llenando los tipos de propiedad para el select */
        this.tipos_propiedad = [];
        this.tipos_propiedad.push({
          value: "",
          label: "Seleccione 1",
        });
        res.data.forEach((element) => {
          this.tipos_propiedad.push({
            label: "Tipo " + element.tipo,
            value: element.id,
          });
          //la primero opcion
          this.form.tipo_propiedades_id = this.tipos_propiedad[0];
        });
        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
      }
    },
    /**trae la info del precio */
    async get_precio_by_id() {
      this.$vs.loading();
      try {
        let res = await cementerio.get_precio_by_id(this.get_precio_id);
        this.datosPrecio = res.data;
        this.form.descripcion = this.datosPrecio.descripcion;
        //actualizo los datos en el formulario
        this.financiamientos.forEach((element) => {
          if (element.value == this.datosPrecio.contado_b) {
            this.form.contado_b = element;
            this.form.financiamiento = this.datosPrecio.financiamiento;
            return;
          }
        });
        this.tipos_propiedad.forEach((element) => {
          if (element.value == this.datosPrecio.tipo_propiedades_id) {
            this.form.tipo_propiedades_id = element;
            return;
          }
        });
        this.form.costo_neto = parseFloat(this.datosPrecio.costo_neto);
        this.form.pago_inicial = parseFloat(this.datosPrecio.pago_inicial);
        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
        this.$vs.notify({
          title: "Modificar Precios",
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
              title: "Registro de Precios",
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
              this.callBackConfirmarAceptar = this.registrar_precio_propiedad;
              this.openConfirmarAceptar = true;
            } else {
              /**modificar, se valida con password */
              this.form.id_precio_modificar = this.get_precio_id;
              this.callback = this.update_precio_propiedad;
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => { });
    },

    registrar_precio_propiedad() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      cementerio
        .registrar_precio_propiedad(this.form)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Registro de Precios",
              text: "Se ha guardado el precio correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000,
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Registro de Precios",
              text: "Error al guardar el precio, por favor reintente.",
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
              this.errores = err.response.data.error;
              this.$vs.notify({
                title: "Registro de Precios",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Registro de Precios",
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

    update_precio_propiedad() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      cementerio
        .update_precio_propiedad(this.form)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Modificación de Precios",
              text: "Se modificó el precio correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000,
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Modificación de Precios",
              text: "Error al guardar el precio, por favor reintente.",
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
              this.errores = err.response.data.error;
              this.$vs.notify({
                title: "Modificación de Precios",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Modificación de Precios",
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
      if (this.getTipoformulario == "modificar") {
        this.botonConfirmarSinPassword = "Salir y limpiar";
        this.accionConfirmarSinPassword =
          "Esta acción limpiará los datos que capturó en el formulario.";
        this.openConfirmarSinPassword = true;
        this.callBackConfirmar = this.cerrarVentana;
      } else {
        this.$emit("closeVentana");
      }
    },
    cerrarVentana() {
      this.openConfirmarSinPassword = false;
      this.limpiarVentana();
      this.$emit("closeVentana");
    },
    //regresa los datos a su estado inicial
    limpiarVentana() {
      /**en caso de modificar */
      this.form.id_precio_modificar = 0;
      /**datos */
      this.form.descripcion = "";
      this.form.contado_b = {};
      this.form.financiamiento = "";
      this.form.pago_inicial = "";
      this.form.costo_neto = "";
      this.form.tipo_propiedades_id = { value: "", label: "Seleccione 1" };
      this.errores = [];
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