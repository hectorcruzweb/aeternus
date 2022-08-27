<template >
  <div class="centerx">
    <vs-popup
      :class="['forms-popup popup-85', z_index]"
      title="Catálogo de proveedores registrados"
      :active.sync="showVentana"
      ref="buscador_proveedor"
    >
      <div class="w-full text-right">
        <vs-button
          class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
          color="primary"
          @click="verFormularioProveedores = true"
        >
          <span>Registrar Proveedor</span>
        </vs-button>
      </div>

      <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
        <vx-card
          no-radius
          title="Filtros de selección"
          refresh-content-action
          @refresh="reset"
          :collapse-action="false"
        >
          <div class="flex flex-wrap pb-6">
            <div class="w-full input-text xl:w-3/12 px-2">
              <label>Núm. Proveedor</label>
              <vs-input
                name="num_proveedor"
                data-vv-as=" "
                type="text"
                class="w-full"
                placeholder="Ej. 1258"
                maxlength="6"
                v-model.trim="serverOptions.numero_control"
                v-on:keyup.enter="get_data('numero_control', 1)"
                v-on:blur="get_data('numero_control', 1, 'blur')"
              />
              <span class="">{{ errors.first("num_proveedor") }}</span>
            </div>

            <div class="w-full input-text xl:w-9/12 px-2">
              <label>Nombre</label>
              <vs-input
                ref="nombre_proveedor"
                name="nombre_proveedor"
                data-vv-as=" "
                type="text"
                class="w-full"
                placeholder="Ej. Juan Pérez"
                maxlength="12"
                v-model.trim="serverOptions.nombre_comercial"
                v-on:keyup.enter="get_data('nombre_comercial', 1)"
                v-on:blur="get_data('nombre_comercial', 1, 'blur')"
              />
              <span class="">{{ errors.first("nombre_proveedor") }}</span>
            </div>
          </div>
        </vx-card>
      </div>

      <!--inicio de buscador-->
      <div class="py-6">
        <div class="resultados_proveedores py-6">
          <vs-table
            :sst="true"
            :max-items="serverOptions.per_page.value"
            :data="proveedores"
            stripe
            noDataText="0 Resultados"
            class="tabla-datos"
          >
            <template slot="header">
              <h3>Lista actualizada de proveedores registrados</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Cliente</vs-th>
              <vs-th>Nombre</vs-th>
              <vs-th>Domicilio</vs-th>
              <vs-th>Celular</vs-th>

              <vs-th>Seleccionar</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{ data[indextr].id }}</span>
                </vs-td>
                <vs-td :data="data[indextr].nombre_comercial">{{
                  data[indextr].nombre_comercial
                }}</vs-td>
                <vs-td :data="data[indextr].direccion">{{
                  data[indextr].direccion
                }}</vs-td>
                <vs-td :data="data[indextr].telefono">
                  <span class="font-medium">{{ data[indextr].telefono }}</span>
                </vs-td>

                <vs-td :data="data[indextr].id_user">
                  <div class="flex justify-center">
                    <img
                      class="cursor-pointer img-btn-20 mx-3"
                      src="@assets/images/checked.svg"
                      @click="
                        retornarSeleccion(
                          data[indextr].nombre_comercial,
                          data[indextr].id,
                          data[indextr]
                        )
                      "
                    />
                  </div>
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
          <div>
            <vs-pagination
              v-if="verPaginado"
              :total="this.total"
              v-model="actual"
              class="mt-6"
            ></vs-pagination>
          </div>
        </div>
      </div>
      <FormularioProveedores
        :tipo="'agregar'"
        :z_index="'z-index58k'"
        :show="verFormularioProveedores"
        @closeVentana="verFormularioProveedores = false"
        @retornar_id="retorno_id"
      ></FormularioProveedores>
      <!--fin de buscador-->
    </vs-popup>
  </div>
</template>
<script>
import FormularioProveedores from "@pages/inventarios/proveedores/FormularioProveedores";
import proveedores from "@services/proveedores";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

export default {
  components: {
    "v-select": vSelect,
    Datepicker,
    FormularioProveedores,
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
    "serverOptions.nacionalidad": function (newVal, previousVal) {
      this.get_data("", 1);
    },
    actual: function (newValue, oldValue) {
      this.get_data("", this.actual);
    },
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["nombre_proveedor"].$el.querySelector("input").focus()
        );
        this.$refs["buscador_proveedor"].$el.querySelector(".vs-icon").onclick =
          () => {
            this.cancelar();
          };
        this.get_data("", 1);
      } else {
        /**cerrar y limpiar el formulario */
        this.serverOptions.numero_control = "";
        this.serverOptions.nombre_comercial = "";
        this.serverOptions.rfc = "";
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
  },
  data() {
    return {
      verFormularioProveedores: false,
      selected: [],
      nacionalidades: [],
      disabledDates: {
        from: new Date(),
      },
      proveedores: [],
      serverOptions: {
        page: "",
        per_page: "",
        numero_control: "",
        rfc: "",
        nombre_comercial: "",
        filtro_especifico_opcion: 1,
      },
      verPaginado: true,
      total: 0,
      actual: 1,
      spanishDatepicker: es,
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.serverOptions.numero_control = "";
      this.serverOptions.nombre_comercial = "";
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
        if (origen == "nombre_comercial") {
          if (this.serverOptions.nombre_comercial.trim() == "") {
            //return;
          }
        } else if (origen == "numero_control") {
          if (this.serverOptions.numero_control.trim() == "") {
            return;
          }
        } else if (origen == "rfc") {
          if (this.serverOptions.rfc.trim() == "") {
            return;
          }
        }
      }

      let self = this;
      if (proveedores.cancel) {
        proveedores.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = 12;
      this.serverOptions.status = 1;
      proveedores
        .get_proveedores(this.serverOptions)
        .then((res) => {
          this.proveedores = res.data.data;
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
                text: "Verifique sus permisos con el administrador del sistema.",
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
    retornarSeleccion(nombre = "", id = "", todos_los_datos = []) {
      /**retorna los datos seleccionados a la venta que los solicita */
      this.$emit("retornoCliente", {
        numero_control: id,
        nombre: nombre,
        datos: todos_los_datos,
      });
      this.$emit("closeBuscador");
    },
    retorno_id(dato) {
      this.get_data("", this.actual);
    },
  },
};
</script>