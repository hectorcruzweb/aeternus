<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups-55 precios_financiamientos normal-forms"
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="flex flex-wrap px-2">
        <div class="w-full pb-3">
          <img
            width="60"
            class="img-center"
            src="@assets/images/preciotag.svg"
          />
          <h3 class="text-xl text-center">
            Información del plan de Financiamiento
          </h3>
        </div>

        <vs-divider />
        <!--datos del titular-->
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            Descrición/Nombre del Plan
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            ref="descripcion"
            name="descripcion"
            data-vv-as=" "
            v-validate.disable="'required'"
            maxlength="85"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. Pago de Contado"
            v-model="form.descripcion"
          />
          <div>
            <span class="text-danger text-sm">{{
              errors.first("descripcion")
            }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.descripcion">{{
              errores.descripcion[0]
            }}</span>
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            Descrición/Nombre del Plan(Inglés)
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            ref="descripcion_ingles"
            name="descripcion_ingles"
            data-vv-as=" "
            v-validate.disable="'required'"
            maxlength="85"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. Spot Price"
            v-model="form.descripcion_ingles"
          />
          <div>
            <span class="text-danger text-sm">{{
              errors.first("descripcion_ingles")
            }}</span>
          </div>
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores.descripcion_ingles"
              >{{ errores.descripcion_ingles[0] }}</span
            >
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            <span>Tipo de Financiamiento</span>
            <span class="text-danger text-sm">(*)</span>
          </label>
          <v-select
            :options="financiamientos"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="form.contado_b"
            class="pb-1 pt-1"
            name="contado_b"
            data-vv-as=" "
          >
            <div slot="no-options">Seleccione una opción</div>
          </v-select>

          <div>
            <span class="text-danger text-sm">{{
              errors.first("contado_b")
            }}</span>
          </div>
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores['contado_b.value']"
              >{{ errores["contado_b.value"][0] }}</span
            >
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            <span>Plan Funerario</span>
            <span class="text-danger text-sm">(*)</span>
          </label>
          <v-select
            :options="tipo_planes"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="form.tipo_plan"
            v-validate:tipo_propiedad_computed.immediate="'required'"
            class="pb-1 pt-1"
            name="tipo_propiedades_id"
            data-vv-as=" "
          >
            <div slot="no-options">Seleccione una opción</div>
          </v-select>
          <div>
            <span class="text-danger text-sm">{{
              errors.first("tipo_propiedades_id")
            }}</span>
          </div>
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores['tipo_propiedades_id.value']"
              >{{ errores["tipo_propiedades_id.value"][0] }}</span
            >
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            Pagos/Meses a Pagar
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            name="financiamiento"
            data-vv-as=" "
            v-validate.disable="'required|integer'"
            maxlength="2"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. 1"
            :disabled="es_contado"
            v-model="form.financiamiento"
          />

          <div>
            <span class="text-danger text-sm">{{
              errors.first("financiamiento")
            }}</span>
          </div>
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores.financiamiento"
              >{{ errores.financiamiento[0] }}</span
            >
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            $ Costo Neto(Con IVA)
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            ref="costo_neto"
            name="costo_neto"
            data-vv-as=" "
            v-validate.disable="'required'"
            maxlength="85"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. $1000.00"
            v-model="form.costo_neto"
          />
          <div>
            <span class="text-danger text-sm">{{
              errors.first("costo_neto")
            }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.costo_neto">{{
              errores.costo_neto[0]
            }}</span>
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            $ Pago Inicial Mínimo
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            name="pago_inicial"
            data-vv-as=" "
            v-validate.disable="'required|numeric'"
            maxlength="6"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. $1000.00"
            v-model="form.pago_inicial"
          />
          <div>
            <span class="text-danger text-sm">{{
              errors.first("pago_inicial")
            }}</span>
          </div>
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores.pago_inicial"
              >{{ errores.pago_inicial[0] }}</span
            >
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            $ Costo Neto(Con IVA) de Contado
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            ref="costo_neto_financiamiento_normal"
            name="costo_neto_financiamiento_normal"
            data-vv-as=" "
            v-validate.disable="'required'"
            maxlength="6"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. 1000.00"
            v-model="form.costo_neto_financiamiento_normal"
          />
          <div>
            <span class="text-danger text-sm">{{
              errors.first("costo_neto_financiamiento_normal")
            }}</span>
          </div>
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores.costo_neto_financiamiento_normal"
              >{{ errores.costo_neto_financiamiento_normal[0] }}</span
            >
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            <span>Aplica Descuento x Pronto Pago</span>
            <span class="text-danger text-sm">(*)</span>
          </label>
          <v-select
            :options="descuento"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="form.descuento_pronto_pago_b"
            class="pb-1 pt-1"
            name="descuento_pronto_pago_b"
            data-vv-as=" "
          >
            <div slot="no-options">Seleccione una opción</div>
          </v-select>

          <div>
            <span class="text-danger text-sm">{{
              errors.first("descuento_pronto_pago_b")
            }}</span>
          </div>
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores['descuento_pronto_pago_b.value']"
              >{{ errores["descuento_pronto_pago_b.value"][0] }}</span
            >
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            $ Costo Neto(Con IVA) con Pronto Pago
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            name="costo_neto_pronto_pago"
            data-vv-as=" "
            v-validate.disable="'numeric'"
            maxlength="6"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. 1000.00"
            v-model="form.costo_neto_pronto_pago"
            :disabled="aplica_descuento"
          />
          <div>
            <span class="text-danger text-sm">{{
              errors.first("costo_neto_pronto_pago")
            }}</span>
          </div>
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores.costo_neto_pronto_pago"
              >{{ errores.costo_neto_pronto_pago[0] }}</span
            >
          </div>
        </div>

        <vs-divider />
      </div>

      <div class="flex flex-wrap px-2">
        <div class="w-full px-2">
          <div class="mt-2">
            <p class="text-center">
              <span class="text-danger font-medium">Ojo:</span>
              Por favor revise la información ingresada, si todo es correcto de
              click en "Botón de Abajo”.
            </p>
          </div>
        </div>
      </div>
      <div
        class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 pt-6 pb-10 px-2 mr-auto ml-auto"
      >
        <vs-button
          class="w-full"
          @click="acceptAlert()"
          color="primary"
          size="small"
        >
          <img
            width="25px"
            class="cursor-pointer"
            src="@assets/images/save.svg"
          />
          <span class="texto-btn" v-if="this.getTipoformulario == 'agregar'"
            >Guardar Datos</span
          >
          <span class="texto-btn" v-else>Modificar Datos</span>
        </vs-button>
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
      :accion="'He revisado la información y quiero registrar este precio'"
      :confirmarButton="'Registrar Precio'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import planes from "@services/planes";
import vSelect from "vue-select";

import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
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
    tipo: {
      type: String,
      required: true,
    },
    id_precio: {
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

        this.form.contado_b = {
          value: 1,
          label: "Pago de Contado/Uso Inmediato",
        };

        this.form.descuento_pronto_pago_b = {
          value: "0",
          label: "No",
        };

        (async () => {
          /**de manera asincrona para evitar errores en popular los selects */
          /**cargando los tipos de propeidades */
          await this.get_planes();
          if (this.getTipoformulario == "modificar") {
            this.title = "Modificar Financiamiento";
            this.get_precio_by_id();
            /**se cargan los datos al formulario */
          } else {
            this.title = "Registrar Nuevo Financiamiento";
          }
        })();
      }

      this.limpiarValidation();
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
    get_precio_id: {
      get() {
        return this.id_precio;
      },
      set(newValue) {
        return newValue;
      },
    },
    tipo_propiedad_computed: function () {
      return this.form.tipo_plan.value;
    },
    es_contado: function () {
      if (this.form.contado_b.value == 1) {
        return true;
      } else {
        return false;
      }
    },
    aplica_descuento: function () {
      if (this.form.descuento_pronto_pago_b.value == 1) {
        return false;
      } else {
        return true;
      }
    },
  },
  data() {
    return {
      planes: [],
      tipo_planes: [],
      /**variables del formulario */
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
        descripcion_ingles: "",
        contado_b: {},
        financiamiento: "",
        pago_inicial: "",
        costo_neto: "",
        costo_neto_financiamiento_normal: "",
        descuento_pronto_pago_b: {},
        costo_neto_pronto_pago: "",
        tipo_plan: { value: "", label: "Seleccione 1" },
      },
      errores: [],
    };
  },
  methods: {
    async get_planes() {
      try {
        this.$vs.loading();
        let res = await planes.get_planes(false, "");
        this.planes = res.data;
        /**llenando los tipos de propiedad para el select */
        this.tipo_planes = [];
        this.tipo_planes.push({
          label: "Todos los Planes",
          value: "",
        });
        res.data.forEach((element) => {
          this.tipo_planes.push({
            label: element.plan.charAt(0).toUpperCase() + element.plan.slice(1),
            value: element.id,
          });
          //la primero opcion
          this.plan_tipo = this.tipo_planes[0];
        });
        this.$vs.loading.close();
      } catch (err) {
        this.$vs.loading.close();
        this.ver = true;
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
          }
        }
      }
    },
    /**trae la info del precio */
    async get_precio_by_id() {
      this.$vs.loading();
      try {
        let res = await planes.get_precio_by_id(this.get_precio_id);
        this.datosPrecio = res.data;
        this.form.descripcion = this.datosPrecio.descripcion;
        this.form.descripcion_ingles = this.datosPrecio.descripcion_ingles;
        //actualizo los datos en el formulario
        this.financiamientos.forEach((element) => {
          if (element.value == this.datosPrecio.contado_b) {
            this.form.contado_b = element;
            this.form.financiamiento = this.datosPrecio.financiamiento;
            return;
          }
        });
        this.tipo_planes.forEach((element) => {
          if (element.value == this.datosPrecio.planes_funerarios_id) {
            this.form.tipo_plan = element;
            return;
          }
        });
        this.form.costo_neto = parseFloat(this.datosPrecio.costo_neto);
        this.form.pago_inicial = parseFloat(this.datosPrecio.pago_inicial);
        this.form.costo_neto_financiamiento_normal = parseFloat(
          this.datosPrecio.costo_neto_financiamiento_normal
        );

        this.descuento.forEach((element) => {
          if (element.value == this.datosPrecio.descuento_pronto_pago_b) {
            this.form.descuento_pronto_pago_b = element;
            return;
          }
        });
        this.form.costo_neto_pronto_pago =
          this.datosPrecio.descuento_pronto_pago_b == 1
            ? parseFloat(this.datosPrecio.costo_neto_pronto_pago)
            : "";

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
              this.callBackConfirmarAceptar = this.registrar_precio;
              this.openConfirmarAceptar = true;
            } else {
              /**modificar, se valida con password */
              this.form.id_precio_modificar = this.get_precio_id;
              this.callback = this.update_precio;
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => {});
    },

    registrar_precio() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      planes
        .registrar_precio(this.form)
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

    update_precio() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      planes
        .update_precio(this.form)
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
      this.form.id_precio_modificar = 0;
      /**datos */
      this.form.descripcion = "";
      this.form.descripcion_ingles = "";
      this.form.contado_b = {};
      this.form.financiamiento = "";
      this.form.pago_inicial = "";
      this.form.costo_neto = "";
      this.form.costo_neto_financiamiento_normal = "";
      this.form.descuento_pronto_pago_b = {};
      this.form.costo_neto_pronto_pago = "";
      this.form.tipo_plan = { value: "", label: "Seleccione 1" };
      this.errores = [];
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