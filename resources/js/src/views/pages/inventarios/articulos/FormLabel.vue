<template >
  <div class="centerx">
    <vs-popup
      :class="['forms-popup bg-content-theme', z_index]"
      fullscreen
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div
        class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2"
        v-if="form.lotes[0]"
      >
        <vx-card no-radius>
          <div class="flex flex-wrap">
            <div class="w-full mb-1 px-2">
              <span class="font-bold uppercase">
                Cantidad de etiquetas a imprimir
              </span>
            </div>
            <div
              class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 mb-1 px-2"
            >
              <div class="mt-3">
                <div class="text-left">
                  <label class="text-md font-bold opacity-75">Total </label>
                </div>
                <div class="text-left mt-3">
                  <label
                    :class="[
                      'text-md opacity-75 total_etiquetas',
                      total_etiquetas > 1500 ? 'text-danger' : '',
                    ]"
                    >{{ total_etiquetas }}
                  </label>
                </div>
              </div>
            </div>

            <div
              class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mb-1 px-2"
            >
              <div class="w-full text-right">
                <vs-button color="success" @click="openReporte()">
                  <span>Imprimir Seleccionados</span>
                </vs-button>
              </div>
            </div>
          </div>
        </vx-card>
      </div>
      <!--comienzo de tabla-->
      <vs-table
        :sst="true"
        noDataText="0 Resultados"
        :data="form.lotes"
        class="tabla-datos"
      >
        <template slot="header">
          <h3>Lotes Disponibles del artículo</h3>
        </template>
        <template slot="thead">
          <vs-th>
            <vs-checkbox
              ref="imprimirtodos"
              color="primary"
              class="mt-3 ml-auto mr-auto"
              v-model="todos"
            ></vs-checkbox>
          </vs-th>
          <vs-th>Clave Artículo</vs-th>
          <vs-th>Descripción</vs-th>
          <vs-th>Número de Lote</vs-th>
          <vs-th>Existencia</vs-th>
          <vs-th>Cantidad a Imprimir</vs-th>
          <vs-th hidden>Imprimir</vs-th>
        </template>
        <template slot-scope="{ data }">
          <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].id">
              <vs-checkbox
                ref="imprimir"
                color="primary"
                class="mt-3 ml-auto mr-auto"
                v-model="data[indextr].imprimir"
              ></vs-checkbox>
            </vs-td>
            <vs-td :data="data[indextr].id">
              <span class="font-semibold">{{
                data[indextr].articulos_id
              }}</span>
            </vs-td>
            <vs-td :data="data[indextr].id">
              <span class="">{{ data[indextr].descripcion }}</span>
            </vs-td>
            <vs-td :data="data[indextr].id">
              <span class="">{{ data[indextr].num_lote_inventario }}</span>
            </vs-td>
            <vs-td :data="data[indextr].id">
              <span class="">{{ data[indextr].existencia }}</span>
            </vs-td>
            <vs-td class="w-2/12">
              <vs-input
                :name="'cantidad' + indextr"
                data-vv-as=" "
                data-vv-validate-on="blur"
                v-validate="
                  'required|integer|min_value:' +
                  0 +
                  '|max_value:' +
                  form.lotes[indextr].existencia
                "
                class="
                  w-full
                  sm:w-10/12
                  md:w-8/12
                  lg:w-8/12
                  xl:w-8/12
                  mr-auto
                  ml-auto
                  mt-1
                  cantidad
                "
                maxlength="4"
                v-model="form.lotes[indextr].cantidad_imprimir"
              />
              <div>
                <span class="text-danger text-xs">{{
                  errors.first("cantidad" + indextr)
                }}</span>
              </div>

              <div>
                <span
                  class="text-danger text-xs"
                  v-if="errores['lotes.' + indextr + '.cantidad']"
                >
                  {{ errores["lotes." + indextr + ".cantidad"][0] }}
                </span>
              </div>
            </vs-td>

            <vs-td :data="data[indextr].id" hidden>
              <div class="flex flex-start">
                <img
                  class="cursor-pointer img-btn-32 mr-auto ml-auto"
                  src="@assets/images/printer.svg"
                  title="Habilitar"
                  @click="
                    altaArticulo(data[indextr].id, data[indextr].descripcion)
                  "
                />
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
      <!--fin de tabla-->
    </vs-popup>

    <ConfirmarDanger
      :z_index="'z-index58k'"
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>
    <Reporteador
      :header="'Impresión de inventario'"
      :show="openReportesLista"
      :listadereportes="ListaReportes"
      :request="request"
      @closeReportes="openReportesLista = false"
      :z_index="'z-index68k'"
    ></Reporteador>
    <ConfirmarAceptar
      :z_index="'z-index58k'"
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero registrar a este artículo'"
      :confirmarButton="'Guardar Artículo'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import inventario from "@services/inventario";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
/**VARIABLES GLOBALES */

export default {
  components: {
    "v-select": vSelect,
    ConfirmarDanger,
    ConfirmarAceptar,
    Reporteador,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
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
        this.$nextTick(() => {
          //this.$refs["nombre_comercial"].$el.querySelector("input").focus()
        });

        (async () => {
          this.title = "Imprimir etiquetado de inventario";
          await this.get_inventario();
          this.$nextTick(() => {
            this.$refs["imprimirtodos"].$el.querySelector("input").onchange = (
              $event
            ) => {
              this.CheckTodos($event, "btn");
            };
          });
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

    lotes_a_imprimir: function () {
      let etiquetas = [];
      this.form.lotes.forEach((element) => {
        if (element.imprimir == true) {
          etiquetas.push({
            cantidad: element.cantidad_imprimir,
            id_articulo: element.articulos_id,
            lotes_id: element.lote_id,
          });
        }
      });
      return etiquetas;
    },
    total_etiquetas: function () {
      let total = 0;
      this.lotes_a_imprimir.forEach((element) => {
        total += parseInt(element.cantidad);
      });
      return total;
    },
  },
  data() {
    return {
      openReportesLista: false,
      request: {
        etiquetas: "",
        email: "",
        destinatario: "",
      },
      ListaReportes: [],
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      openConfirmarSinPassword: false,
      callback: Function,
      datos_articulo: [],
      /**form */
      opciones_sino: [
        {
          value: 1,
          label: "SI",
        },
        {
          value: 0,
          label: "NO",
        },
      ],
      todos: false,
      form: {
        lotes: [],
      },
      errores: [],
    };
  },
  methods: {
    async get_inventario() {
      this.$vs.loading();
      try {
        let res = await inventario.get_inventariable_etiquetado();
        let datos = res.data;
        /**reviso que tenga lotes el articulo seleccionado */
        if (datos.length > 0) {
          /**cargando solo los lotes con existencia */
          datos.forEach((articulo) => {
            articulo.inventario.forEach((element, indextr) => {
              if (element.existencia > 0) {
                this.form.lotes.push({
                  descripcion: articulo.descripcion,
                  lote_id: element.lotes_id,
                  num_lote_inventario: element.num_lote_inventario,
                  articulos_id: element.articulos_id,
                  existencia: element.existencia,
                  cantidad_imprimir: element.existencia,
                  imprimir: element.articulos_id == false,
                });

                /**acomodando los eventos del checkbox */
                this.$nextTick(() => {
                  this.$refs["imprimir"][indextr].$el.querySelector(
                    "input"
                  ).onchange = ($event) => {
                    /**revisar si se activo el check */
                    //this.checarTodosModulo($event, modulo.id);
                    this.CheckTodos($event, indextr);
                  };
                });
              }
            });
          });
        } else {
          this.$vs.notify({
            title: "Impresión de etiquetas",
            text: "El artículo seleccionado no tiene lotes en el inventario.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "4000",
          });
          this.cerrarVentana();
        }

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

    CheckTodos(event, indextr) {
      this.$nextTick(() => {
        if (indextr != "btn") {
          /**recorriendo el array para ver si se debe activar el todos o quitar  */
          if (event.target.checked == false) {
            this.todos = false;
            this.$nextTick(() => {
              this.$refs["imprimirtodos"][0].$el.querySelector(
                "input"
              ).checked = false;
            });
          } else {
            this.form.lotes[indextr].imprimir = true;
            this.$refs["imprimir"][indextr].$el.querySelector(
              "input"
            ).checked = true;

            let allChecked = true;
            for (let index = 0; index < this.form.lotes.length; index++) {
              if (this.form.lotes[index].imprimir == false) {
                allChecked = false;
                break;
              }
            }
            if (allChecked == true) {
              this.todos = true;
              this.$refs["imprimirtodos"][0].$el.querySelector(
                "input"
              ).checked = true;
            }
          }
        } else {
          for (let index = 0; index < this.form.lotes.length; index++) {
            this.form.lotes[index].imprimir = event.target.checked;
            this.$refs["imprimir"][index].$el.querySelector("input").checked =
              event.target.checked;
          }
        }
      });
    },

    openReporte() {
      if (this.lotes_a_imprimir.length > 0) {
        if (this.total_etiquetas < 1500) {
          /**mandando las etiquetas a imprimir */
          this.ListaReportes = [];
          this.ListaReportes.push({
            nombre: "Impresión de etiquetas",
            url: "/inventario/get_pdf_etiquetas",
          });
          this.request.email = "";
          this.request.etiquetas = this.lotes_a_imprimir;
          this.request.destinatario = "";
          this.openReportesLista = true;
          this.$vs.loading.close();
        } else {
          this.$vs.notify({
            title: "Imprimir etiquetas",
            text: "La impresora puede imprimir 1500 etiquetas por rollo como máximo.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "8000",
          });
        }
      } else {
        this.$vs.notify({
          title: "Imprimir etiquetas",
          text: "Verifique que a seleccionado algun lote",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "4000",
        });
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
      this.form.lotes = [];
      this.todos.false;
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
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
  },
  created() {},
};
</script>
