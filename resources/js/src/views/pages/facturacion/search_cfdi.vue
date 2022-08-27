<template >
  <div class="centerx">
    <vs-popup
       :class="['forms-popup popup-90', z_index]"
     
      fullscreen
      title="Catálogo de CFDIs Timbrados"
      :active.sync="showVentana"
      ref="buscar_cfdi"
    >
      <!--inicio de buscador-->
      <div class="py-3">
        <vx-card
          no-radius
          title="Filtros de selección"
          refresh-content-action
          @refresh="reset"
        >
          <template slot="no-body">
            <div>
              <div class="flex flex-wrap px-4 py-4">
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Tipo de Comprobante</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="tipos_comprobante"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="serverOptions.tipo_comprobante"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="categoria"
                    data-vv-as=" "
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("categoria")
                    }}</span>
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold"
                    >Número de operación</label
                  >
                  <vs-input
                    name="numero_control"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. 001"
                    maxlength="15"
                    v-model.trim="serverOptions.numero_control"
                    v-on:keyup.enter="get_data('numero_control', 1)"
                    v-on:blur="get_data('numero_control', 1, 'blur')"
                  />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("numero_control")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Rango de Fechas año/mes/dia
                    <span class="texto-importante">(*)</span>
                  </label>

                  <flat-pickr
                    name="fecha_timbrado"
                    data-vv-as=" "
                    v-validate:fechatimbrado_validacion_computed.immediate="
                      'required'
                    "
                    :config="configdateTimePickerRange"
                    v-model="serverOptions.fecha_timbrado"
                    placeholder="Fecha(s) de timbrado"
                    class="w-full my-1"
                    @on-close="onCloseDate"
                  />
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold"
                    >Nombre del cliente</label
                  >
                  <vs-input
                    ref="cliente"
                    name="cliente"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. Juán Pérez"
                    maxlength="150"
                    v-model.trim="serverOptions.cliente"
                    v-on:keyup.enter="get_data('cliente', 1)"
                    v-on:blur="get_data('cliente', 1, 'blur')"
                  />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("cliente")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">Rfc</label>
                  <vs-input
                    ref="rfc"
                    name="rfc"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. XAXX010101000"
                    maxlength="13"
                    v-model.trim="serverOptions.rfc"
                    v-on:keyup.enter="get_data('rfc', 1)"
                    v-on:blur="get_data('rfc', 1, 'blur')"
                  />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("rfc")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
              </div>
            </div>
          </template>
        </vx-card>
        <div class="mt-10">
          <vs-table
            :sst="true"
            :max-items="serverOptions.per_page"
            :data="cfdis"
            stripe
            noDataText="0 Resultados"
          >
            <template slot="header">
              <h3>Lista de Artículos y Servicios por Lotes</h3>
            </template>
            <template slot="thead">
              <vs-th>#</vs-th>
              <vs-th>Folio</vs-th>
              <vs-th>UUID</vs-th>
              <vs-th>Fecha</vs-th>
              <vs-th>Cliente</vs-th>
              <vs-th>RFC</vs-th>
              <vs-th>Tipo</vs-th>
              <vs-th>Método de Pago</vs-th>
              <vs-th>Estatus</vs-th>
              <vs-th>Seleccionar</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{ indextr + 1 }}</span>
                </vs-td>
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{ data[indextr].id }}</span>
                </vs-td>
                <vs-td :data="data[indextr].uuid">{{
                  data[indextr].uuid
                }}</vs-td>
                <vs-td :data="data[indextr].fecha_timbrado_texto">{{
                  data[indextr].fecha_timbrado_texto
                }}</vs-td>
                <vs-td :data="data[indextr].cliente_nombre">{{
                  data[indextr].cliente_nombre
                }}</vs-td>
                <vs-td :data="data[indextr].rfc_receptor">{{
                  data[indextr].rfc_receptor
                }}</vs-td>
                <vs-td :data="data[indextr].tipo_comprobante_texto">{{
                  data[indextr].tipo_comprobante_texto
                }}</vs-td>
                <vs-td :data="data[indextr].sat_metodos_pago_texto">{{
                  data[indextr].sat_metodos_pago_texto
                }}</vs-td>
                <vs-td :data="data[indextr].status_texto">{{
                  data[indextr].status_texto
                }}</vs-td>
                <vs-td :data="data[indextr].id">
                  <img
                    width="25"
                    class="cursor-pointer"
                    src="@assets/images/checked.svg"
                    @click="retornarSeleccion(data[indextr])"
                  />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
          <div>
            <vs-pagination
              v-if="verPaginado"
              :total="this.total"
              v-model="actual"
              class="mt-3"
            ></vs-pagination>
          </div>
        </div>
      </div>

      <!--fin de buscador-->
    </vs-popup>
  </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import facturacion from "@services/facturacion";
import funeraria from "@services/funeraria";
import vSelect from "vue-select";

import { configdateTimePickerRange } from "@/VariablesGlobales";
const moment = require("moment");

export default {
  components: {
    "v-select": vSelect,
    flatPickr,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    tipo_search: {
      type: String,
      required: true,
    },
    z_index: {
      type: String,
      required: false,
      default: "z-index64k",
    },
  },
  watch: {
    "serverOptions.tipo_comprobante": function (newVal, previousVal) {
      this.get_data("", 1);
    },
    actual: function (newValue, oldValue) {
      this.get_data("", this.actual);
    },
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["descripcion"].$el.querySelector("input").focus()
        );
        this.$refs["buscar_cfdi"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
        (async () => {
          await this.get_tipos_comprobante();
        })();
        this.get_data("", 1);
      } else {
        /**cerrar y limpiar el formulario */
        this.serverOptions.numero_control = "";
        this.serverOptions.titular = "";
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
    getTipo: {
      get() {
        return this.tipo_search;
      },
      set(newValue) {
        return newValue;
      },
    },
    fechatimbrado_validacion_computed: function () {
      return this.serverOptions.fecha_timbrado;
    },
  },
  data() {
    return {
      configdateTimePickerRange: configdateTimePickerRange,
      /**VARIAVLES DEL MODULO */
      tipos_comprobante: [
        {
          label: "Ver todos",
          value: "",
        },
      ],
      serverOptions: {
        tipo_comprobante: {
          label: "Ver todos",
          value: "",
        },

        tipo_comprobante_id: "",
        fecha_timbrado: [],
        fecha_inicio: "",
        fecha_fin: "",
        page: "",
        per_page: "",
        numero_control: "",
        cliente: "",
        rfc: "",
      },
      /**FIN DE VARIABLES DEL MODULO */

      categorias: [
        {
          label: "Seleccione 1",
          value: "",
        },
      ],
      selected: [],
      cfdis: [],
      lotes: [],

      verPaginado: true,
      total: 0,
      actual: 1,
    };
  },
  methods: {
    onCloseDate(selectedDates, dateStr, instance) {
      /**se valdiad que se busque la informacion solo en los casos donde la fechas cambien */
      let buscar = false;
      if (selectedDates.length == 0) {
        /**no hay fechas que buscar */
        if (
          this.serverOptions.fecha_inicio != "" ||
          this.serverOptions.fecha_fin != ""
        ) {
          buscar = true;
        }
        this.serverOptions.fecha_inicio = "";
        this.serverOptions.fecha_fin = "";
      } else {
        /**hay fechas que buscar */
        if (
          this.serverOptions.fecha_inicio !=
            moment(selectedDates[0]).format("YYYY-MM-DD") ||
          this.serverOptions.fecha_fin !=
            moment(selectedDates[1]).format("YYYY-MM-DD")
        ) {
          buscar = true;
          /**agreggo la fecha 1 */
          this.serverOptions.fecha_inicio = moment(selectedDates[0]).format(
            "YYYY-MM-DD"
          );
          this.serverOptions.fecha_fin = moment(selectedDates[1]).format(
            "YYYY-MM-DD"
          );
        }
      }
      if (buscar) {
        this.get_data("fecha_timbrado", 1, "select");
      }
    },
    async get_tipos_comprobante() {
      this.$vs.loading();
      await facturacion
        .get_tipos_comprobante()
        .then((res) => {
          this.tipos_comprobante = [];
          this.tipos_comprobante.push({ label: "Ver todos", value: "" });
          res.data.forEach((element) => {
            this.tipos_comprobante.push({
              label: element.tipo,
              value: element.id,
            });
          });
          this.serverOptions.tipo_comprobante = this.tipos_comprobante[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    reset(card) {
      card.removeRefreshAnimation(500);
      this.serverOptions.numero_control = "";
      this.serverOptions.cliente = "";
      this.serverOptions.fecha_timbrado = "";
      this.serverOptions.fecha_fin = "";
      this.serverOptions.fecha_fin = "";
      this.serverOptions.tipo_comprobante = this.tipos_comprobante[0];
      this.serverOptions.rfc = "";
      this.get_data("", this.actual);
    },
    cancelar() {
      this.$emit("closeBuscador");
      return;
    },
    get_data(origen = "", page, evento = "") {
      if (evento == "blur") {
        return;
      } else {
        /**checando el origen */
        if (origen == "cliente") {
          if (this.serverOptions.cliente.trim() == "") {
            //return;
          }
        } else if (origen == "numero_control") {
          if (this.serverOptions.numero_control.trim() == "") {
            //return;
          }
        } else if (origen == "fecha_timbrado") {
          if (this.serverOptions.fecha_timbrado.trim() == "") {
            //return;
          }
        } else if (origen == "rfc") {
          if (this.serverOptions.rfc.trim() == "") {
            //return;
          }
        }
      }

      let self = this;
      if (facturacion.cancel) {
        facturacion.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = 24;
      this.serverOptions.tipo_comprobante_id = this.serverOptions.tipo_comprobante.value;
      facturacion
        .get_cfdis_timbrados(this.serverOptions)
        .then((res) => {
          this.cfdis = res.data.data;
          this.total = res.data.last_page;
          this.actual = res.data.current_page;
          this.verPaginado = true;
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
          this.ver = true;
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
            }
          }
        });
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    retornarSeleccion(datos) {
      if (this.getTipo == "pagar") {
        this.$emit("CfdiPagarSeleccionado", datos);
      } else if (this.getTipo == "relacionar") {
        this.$emit("CfdiRelacionarSeleccionado", datos);
      }

      this.$emit("closeBuscador");
    },
  },
};
</script>