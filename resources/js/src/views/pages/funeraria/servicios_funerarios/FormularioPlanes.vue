<template >
  <div class="centerx">
    <vs-popup
      class="precios_financiamientos forms-popups normal-forms"
      fullscreen
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
            Descripción y conceptos del plan Funerario
          </h3>
        </div>
        <vs-divider />

        <!--datos del titular-->
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            Descripción/Nombre del Plan
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            ref="descripcion"
            name="descripcion"
            data-vv-as=" "
            v-validate.disable="'required'"
            maxlength="200"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. Servicio de cremación"
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
            Descripción/Nombre del Plan(Inglés)
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            ref="descripcion_ingles"
            name="descripcion_ingles"
            data-vv-as=" "
            v-validate.disable="'required'"
            maxlength="200"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. Cremation Service"
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
        <vs-divider />
      </div>
      <div class="flex flex-wrap px-2">
        <div class="w-full sm:w-12/12 md:w-7/12 lg:w-7/12 xl:w-7/12 px-2">
          <div class="flex flex-wrap">
            <div class="w-full pt-3 pb-3">
              <div class="float-left">
                <img width="36px" src="@assets/images/list_planes.svg" />
                <h3
                  class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                >
                  Contenido del Plan Funerario
                </h3>
              </div>
            </div>
            <div class="w-full">
              <div class="mt-2 py-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores['conceptos.0.conceptos']"
                  >{{ errores["conceptos.0.conceptos"][0] }}</span
                >
              </div>
            </div>
            <div class="w-full" v-if="verLista">
              <vs-table
                :data="datos"
                noDataText="No se han agregado Artículos ni Servicios"
              >
                <template slot="header">
                  <h3>Servicios y Artículos que Incluye el Paquete</h3>
                </template>
                <template slot="thead">
                  <vs-th>#</vs-th>
                  <vs-th>Artículo/Servicio</vs-th>
                  <vs-th>Aplicar en</vs-th>
                  <vs-th>Acciones</vs-th>
                </template>
                <template slot-scope="{ data }">
                  <vs-tr
                    :data="tr"
                    :key="indextr"
                    v-for="(tr, indextr) in data"
                  >
                    <vs-td class="w-1/12">
                      <div class="capitalize">
                        <span class="lowercase">{{ alfabeto[indextr] }})</span>
                      </div>
                    </vs-td>
                    <vs-td class="w-7/12">
                      <div class="capitalize">
                        {{ tr.concepto }}
                        <span class="text-sm">({{ tr.concepto_ingles }})</span>
                      </div>
                    </vs-td>
                    <vs-td class="w-2/12">
                      <div class="capitalize">{{ tr.aplicar }}</div>
                    </vs-td>
                    <vs-td class="w-2/12">
                      <div class="flex flex-start py-1">
                        <img
                          class="cursor-pointer img-btn ml-auto mr-3"
                          src="@assets/images/edit.svg"
                          title="Modificar"
                          @click="update(tr)"
                        />
                        <img
                          width="24"
                          class="cursor-pointer mr-auto ml-3"
                          src="@assets/images/trash.svg"
                          title="Quitar"
                          @click="remove(tr)"
                        />
                      </div>
                    </vs-td>
                    <template class="expand-user" slot="expand"></template>
                  </vs-tr>
                </template>
              </vs-table>
            </div>
            <div class="w-full" v-else>
              <vs-table
                :data="[]"
                noDataText="No se han agregado Artículos ni Servicios"
              >
                <template slot="header">
                  <h3>Servicios y Artículos que Incluye el Paquete</h3>
                </template>
                <template slot="thead">
                  <vs-th>#</vs-th>
                  <vs-th>Artículo/Servicio</vs-th>
                  <vs-th>Aplicar en</vs-th>
                  <vs-th>Acciones</vs-th>
                </template>
              </vs-table>
            </div>
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
          <div class="flex flex-wrap">
            <div class="w-full pt-3 pb-3">
              <div class="float-left">
                <img width="30px" src="@assets/images/plus.svg" class="mt-2" />
                <h3
                  class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                >
                  Agregar Conceptos
                </h3>
              </div>
            </div>
            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
            >
              <label class="text-sm opacity-75 font-bold">
                Concepto
                <span class="text-danger text-sm">(*)</span>
              </label>
              <vs-input
                data-vv-scope="conceptos"
                ref="concepto"
                name="concepto"
                data-vv-as=" "
                v-validate.disable="'required'"
                maxlength="200"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Una urna básica"
                v-model="form.concepto"
              />
              <div>
                <span class="text-danger text-sm">{{
                  errors.first("conceptos.concepto")
                }}</span>
              </div>
              <div class="mt-2"></div>
            </div>
            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
            >
              <label class="text-sm opacity-75 font-bold">
                Concepto en inglés
                <span class="text-danger text-sm">(*)</span>
              </label>
              <vs-input
                data-vv-scope="conceptos"
                ref="concepto_ingles"
                name="concepto_ingles"
                data-vv-as=" "
                v-validate.disable="'required'"
                maxlength="200"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Basic urn"
                v-model="form.concepto_ingles"
              />
              <div>
                <span class="text-danger text-sm">{{
                  errors.first("conceptos.concepto_ingles")
                }}</span>
              </div>
              <div class="mt-2"></div>
            </div>
            <div
              :class="[
                'w-full sm:w-12/12 px-2',
                verModificar
                  ? 'md:w-12/12 lg:w-12/12 xl:w-12/12'
                  : 'md:w-7/12 lg:w-7/12 xl:w-7/12',
              ]"
            >
              <label class="text-sm opacity-75 font-bold">
                <span>Aplicar en</span>
                <span class="texto-importante">(*)</span>
              </label>
              <v-select
                :options="secciones"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="form.seccion"
                class="pb-1 pt-1 plan_venta"
                name="plan_venta"
                data-vv-as=" "
              >
                <div slot="no-options">
                  No Se Ha Seleccionado Ninguna Opción
                </div>
              </v-select>
              <div class="mt-2"></div>
            </div>
            <div
              :class="[
                'w-full sm:w-12/12  pt-2 pb-3 px-2',
                verModificar
                  ? ' md:w-6/12 lg:w-6/12 xl:w-6/12'
                  : ' md:w-6/12 lg:w-6/12 xl:w-6/12',
              ]"
              v-if="verModificar"
            >
              <vs-button
                class="w-full"
                @click="cancelarModificacion"
                color="danger"
                size="small"
              >
                <span class="font-medium text-base">Cancelar Modificación</span>
              </vs-button>
            </div>
            <div
              :class="[
                'w-full sm:w-12/12  pt-2 pb-3 px-2',
                verModificar
                  ? ' md:w-6/12 lg:w-6/12 xl:w-6/12'
                  : ' md:w-5/12 lg:w-5/12 xl:w-5/12 mt-5',
              ]"
            >
              <vs-button
                class="w-full"
                @click="agregarConcepto"
                color="success"
                size="small"
              >
                <span class="font-medium text-base" v-if="!verModificar"
                  >Agregar a la Lista</span
                >
                <span class="font-medium text-base" v-else
                  >Modificar Servicio</span
                >
              </vs-button>
            </div>

            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
            >
              <label class="text-sm opacity-75 font-bold">
                NOTA U OBSERVACIÓN:
              </label>
              <vs-textarea
                height="120px"
                :rows="5"
                size="large"
                ref="nota"
                type="text"
                class="w-full pt-3 pb-3 mt-1 large_textarea"
                placeholder="Ingrese una nota..."
                v-model.trim="form.nota"
              />
            </div>
            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
            >
              <label class="text-sm opacity-75 font-bold">
                NOTA U OBSERVACIÓN(EN INGLÉS):
              </label>
              <vs-textarea
                height="120px"
                :rows="5"
                size="large"
                ref="nota"
                type="text"
                class="w-full pt-3 pb-3 mt-1 large_textarea"
                placeholder="Ingrese una nota en inglés..."
                v-model.trim="form.nota_ingles"
              />
            </div>

            <div class="flex flex-wrap px-2">
              <div class="w-full px-2">
                <div class="mt-2">
                  <p class="text-center">
                    <span class="text-danger font-medium">Ojo:</span>
                    Por favor revise la información ingresada, si todo es
                    correcto de click en "Botón de Abajo”.
                  </p>
                </div>
              </div>
            </div>
            <div class="w-full mt-4">
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
                <span
                  class="texto-btn"
                  v-if="this.getTipoformulario == 'agregar'"
                  >Guardar Datos</span
                >
                <span class="texto-btn" v-else>Modificar Datos</span>
              </vs-button>
            </div>
          </div>
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
      :accion="'He revisado la información y quiero registrar este plan funerario'"
      :confirmarButton="'Registrar Plan Funerario'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import planes from "@services/planes";
import vSelect from "vue-select";
import { alfabeto } from "@/VariablesGlobales";
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
    id_plan: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.limpiarValidation();
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["descripcion"].$el.querySelector("input").focus()
        );
        /**agregando por default una seccion y un concepto */
        this.form.seccion = {
          value: "incluye",
          label: "plan funerario",
        };
        (async () => {
          if (this.getTipoformulario == "modificar") {
            this.title = "Modificar Plan Funerario";
            /**cargar los datos para modificar */
            await this.get_planes_id();
          } else {
            this.title = "Registrar Plan Funerario";
          }
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
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_plan_id: {
      get() {
        return this.id_plan;
      },
      set(newValue) {
        return newValue;
      },
    },
    verLista: function () {
      if (this.form.conceptos) {
        let mostrar = false;
        this.datos = [];
        this.form.conceptos.forEach((element, index_seccion) => {
          if (element.conceptos) {
            if (element.conceptos.length > 0) {
              element.conceptos.forEach((concepto, index_concepto) => {
                this.datos.push({
                  concepto: concepto.concepto,
                  concepto_ingles: concepto.concepto_ingles,
                  aplicar: concepto.aplicar_en,
                  seccion: element.seccion,
                  index_seccion: index_seccion,
                  index_concepto: index_concepto,
                });
              });
              mostrar = true;
            }
          }
        });
        if (mostrar == true) {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    },
    verModificar: function () {
      if (this.index_seccion !== "" && this.index_concepto !== "") {
        return true;
      } else {
        return false;
      }
    },
  },
  data() {
    return {
      alfabeto: alfabeto,
      /**index de modificacion de concepto */
      index_seccion: "",
      index_concepto: "",
      /** */
      datos: [],
      secciones: [
        {
          value: "incluye",
          label: "plan funerario",
        },
        {
          value: "inhumacion",
          label: "caso de inhumación",
        },
        {
          value: "cremacion",
          label: "caso de cremación",
        },
        {
          value: "velacion",
          label: "caso de velación",
        },
      ],
      form: {
        descripcion: "",
        descripcion_ingles: "",
        seccion: {},
        conceptos: [
          {
            seccion: "incluye",
            conceptos: [],
          },
          {
            seccion: "inhumacion",
            conceptos: [],
          },
          {
            seccion: "cremacion",
            conceptos: [],
          },
          {
            seccion: "velacion",
            conceptos: [],
          },
        ],
        nota: "",
        nota_ingles: "",
        concepto: "",
        concepto_ingles: "",
        id_plan_modificar: 0,
        /**variables del modulo */
      },
      errores: [],
      /**variables del modulo */
      datosPlan: [],
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Plan Funerario",
    };
  },
  methods: {
    remove(datos) {
      this.index_seccion = datos.index_seccion;
      this.index_concepto = datos.index_concepto;
      this.botonConfirmarSinPassword = "eliminar";
      this.accionConfirmarSinPassword =
        "¿Desea remover este Artículo/Servicio? Los datos quedarán eliminados del sistema";
      this.callBackConfirmar = this.remover_concepto_callback;
      this.openConfirmarSinPassword = true;
    },
    //remover beneficiario callback quita del array al beneficiario seleccionado
    remover_concepto_callback() {
      this.form.conceptos[this.index_seccion].conceptos.splice(
        this.index_concepto,
        1
      );
      this.cancelarModificacion();
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
    update(datos) {
      this.form.concepto = datos.concepto;
      this.form.concepto_ingles = datos.concepto_ingles;
      /**buscando el item de la lista de secciones */
      this.secciones.forEach((element) => {
        if (element.value === datos.seccion) {
          this.form.seccion = element;
        }
      });
      /**agregando los indexes de concepto */
      this.index_seccion = datos.index_seccion;
      this.index_concepto = datos.index_concepto;
      this.$nextTick(() =>
        this.$refs["concepto"].$el.querySelector("input").focus()
      );
      this.limpiarValidation();
    },
    cancelarModificacion() {
      /**reseteando el concepto */
      this.form.seccion = {
        value: "incluye",
        label: "plan funerario",
      };
      this.index_concepto = "";
      this.index_seccion = "";
      this.form.concepto = "";
      this.form.concepto_ingles = "";
      this.limpiarValidation();
      this.$nextTick(() =>
        this.$refs["concepto"].$el.querySelector("input").focus()
      );
    },
    agregarConcepto() {
      this.$validator
        .validateAll("conceptos")
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Agregar Artículos/Servicios al Plan funerario",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
          } else {
            if (this.verModificar == true) {
              /**verificando si la acion es modificar o agregar */
              if (this.index_seccion !== "" && this.index_concepto !== "") {
                /**verificnando si cambio de secion el concepto
                 * si no cambio, solo se actualiza, si cambio se debe de remover para agregar nuevamente
                 */
                if (
                  this.form.seccion.value !=
                  this.form.conceptos[this.index_seccion].conceptos[
                    this.index_concepto
                  ].seccion
                ) {
                  /**cambio la seccion y se debe de quitar el item para reinsertarse */
                  this.form.conceptos[this.index_seccion].conceptos.splice(
                    this.index_concepto,
                    1
                  );
                  /**se agrega el concepto al arreglo */
                  this.form.conceptos.forEach((element) => {
                    if (element.seccion == this.form.seccion.value) {
                      element.conceptos.push({
                        concepto: this.form.concepto,
                        concepto_ingles: this.form.concepto_ingles,
                        aplicar_en: this.form.seccion.label,
                        seccion: this.form.seccion.value,
                      });
                    }
                  });
                } else {
                  /**solo se actualiza */
                  this.form.conceptos[this.index_seccion].conceptos[
                    this.index_concepto
                  ].concepto = this.form.concepto;
                  this.form.conceptos[this.index_seccion].conceptos[
                    this.index_concepto
                  ].concepto_ingles = this.form.concepto_ingles;
                }
              }
            } else {
              /**es agrregar el concepto */
              /**se agrega el concepto al arreglo */
              this.form.conceptos.forEach((element) => {
                if (element.seccion == this.form.seccion.value) {
                  element.conceptos.push({
                    concepto: this.form.concepto,
                    concepto_ingles: this.form.concepto_ingles,
                    aplicar_en: this.form.seccion.label,
                    seccion: this.form.seccion.value,
                  });
                }
              });
            }

            /**reseteando el concepto */
            this.form.seccion = {
              value: "incluye",
              label: "plan funerario",
            };
            this.index_concepto = "";
            this.index_seccion = "";
            this.form.concepto = "";
            this.form.concepto_ingles = "";
            this.limpiarValidation();
            this.$nextTick(() =>
              this.$refs["concepto"].$el.querySelector("input").focus()
            );
          }
        })
        .catch(() => {});
    },
    /**trae la info del precio */
    async get_planes_id() {
      this.$vs.loading();
      try {
        let res = await planes.get_planes(false, this.get_plan_id);
        this.datosPlan = res.data[0];
        //actualizo los datos en el formulario
        this.id_plan_modificar = this.datosPlan.id;
        this.form.descripcion = this.datosPlan.plan;
        this.form.descripcion_ingles = this.datosPlan.plan_ingles;
        this.form.nota = this.datosPlan.nota;
        this.form.nota_ingles = this.datosPlan.nota_ingles;
        this.form.conceptos = this.datosPlan.secciones;
        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
        this.$vs.notify({
          title: "Modificar Plan",
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
              title: "Registro de Planes Funerarios",
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
              this.callBackConfirmarAceptar = this.registrar_plan;
              this.openConfirmarAceptar = true;
            } else {
              /**modificar, se valida con password */
              this.form.id_plan_modificar = this.get_plan_id;
              this.callback = this.update_plan;
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => {});
    },
    /**funciones del modulo */

    registrar_plan() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      planes
        .registrar_plan(this.form)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Registro de Planes Funerarios",
              text: "Se ha guardado el plan funerario correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000,
            });
            this.$emit("retornar", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Registro de Planes Funerarios",
              text: "Error al guardar el plan funerario, por favor reintente.",
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
                title: "Registro de Planes Funerarios",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Registro de Planes Funerarios",
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

    update_plan() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      planes
        .update_plan(this.form)
        .then((res) => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Modificación de Planes",
              text: "Se modificó el plan funerario correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000,
            });
            this.$emit("retornar", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Modificación de Planes",
              text:
                "Error al modificar el plan funerario, por favor reintente.",
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
                title: "Modificación de Planes",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Modificación de Planes",
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
      this.form.id_plan_modificar = 0;
      /**datos */
      this.form.descripcion = "";
      this.form.descripcion_ingles = "";
      this.cancelarModificacion();
      this.form.conceptos = [
        {
          seccion: "incluye",
          conceptos: [],
        },
        {
          seccion: "inhumacion",
          conceptos: [],
        },
        {
          seccion: "cremacion",
          conceptos: [],
        },
        {
          seccion: "velacion",
          conceptos: [],
        },
      ];
      this.form.nota = "";
      this.form.nota_ingles = "";
      this.errores = [];
    },
    closeChecker() {
      this.operConfirmar = false;
    },
  },
  created() {},
};
</script>