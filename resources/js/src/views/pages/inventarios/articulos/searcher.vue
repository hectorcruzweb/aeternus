<template>
  <div class="centerx">
    <vs-popup :class="['forms-popup', z_index]" fullscreen title="Catálogo de clientes registrados"
      :active.sync="showVentana" ref="buscador_cliente">
      <div class="flex flex-wrap my-2">
        <div class="w-full sm:w-12/12 ml-auto md:w-1/5 lg:w-1/5 xl:w-1/5 mb-1 px-2">
          <vs-button color="success" size="small" class="w-full ml-auto" @click="verFormularioClientes = true">
            <img class="cursor-pointer img-btn" src="@assets/images/plus.svg" />
            <span class="texto-btn">Registrar Cliente</span>
          </vs-button>
        </div>
      </div>
      <!--inicio de buscador-->
      <div class="py-3">
        <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset">
          <template slot="no-body">
            <div>
              <div class="flex flex-wrap px-4 py-4">
                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                  <label class="text-sm opacity-75 font-bold">Nombre</label>
                  <vs-input ref="nombre_cliente" name="nombre_cliente" data-vv-as=" " type="text"
                    class="w-full pb-1 pt-1" placeholder="Ej. Juan Pérez" maxlength="12"
                    v-model.trim="serverOptions.cliente" v-on:keyup.enter="get_data('cliente', 1)"
                    v-on:blur="get_data('cliente', 1, 'blur')" />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("nombre_cliente")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                  <label class="text-sm opacity-75 font-bold">Núm. Cliente</label>
                  <vs-input name="num_cliente" data-vv-as=" " type="text" class="w-full pb-1 pt-1"
                    placeholder="Ej. 1258" maxlength="6" v-model.trim="serverOptions.id_cliente"
                    v-on:keyup.enter="get_data('id_cliente', 1)" v-on:blur="get_data('id_cliente', 1, 'blur')" />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("num_cliente")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2 hidden">
                  <label class="text-sm opacity-75 font-bold">RFC</label>
                  <vs-input name="rfc" data-vv-as=" " type="text" class="w-full pb-1 pt-1"
                    placeholder="ej. DIS961210RG9" maxlength="13" v-model.trim="serverOptions.rfc"
                    v-on:keyup.enter="get_data('rfc', 1)" v-on:blur="get_data('rfc', 1, 'blur')" />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("rfc")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>

                <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2" style="z-index: 52002 !important">
                  <label class="text-sm opacity-75 font-bold">
                    <span>Nacionalidad</span>
                  </label>
                  <v-select :options="nacionalidades" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="serverOptions.nacionalidad" class="pb-1 pt-1" name="nacionalidad" data-vv-as=" ">
                    <div slot="no-options">Seleccione una opción</div>
                  </v-select>
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("nacionalidad")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
              </div>
            </div>
          </template>
        </vx-card>
        <div class="resultados_clientes mt-10">
          <vs-table :sst="true" :max-items="serverOptions.per_page.value" :data="clientes" stripe
            noDataText="0 Resultados">
            <template slot="header">
              <h3>Lista actualizada de clientes registrados</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Cliente</vs-th>
              <vs-th>Nombre</vs-th>
              <vs-th>Domicilio</vs-th>
              <vs-th>Celular</vs-th>
              <vs-th>Nacionalidad</vs-th>
              <vs-th>Seleccionar</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{ data[indextr].id }}</span>
                </vs-td>
                <vs-td :data="data[indextr].nombre">{{
                  data[indextr].nombre
                }}</vs-td>
                <vs-td :data="data[indextr].direccion">{{
                  data[indextr].direccion
                }}</vs-td>
                <vs-td :data="data[indextr].celular">
                  <span class="font-medium">{{ data[indextr].celular }}</span>
                </vs-td>

                <vs-td :data="data[indextr].nacionalidad['id']">{{
                  data[indextr].nacionalidad["nacionalidad"]
                }}</vs-td>
                <vs-td :data="data[indextr].id_user">
                  <img width="25" class="cursor-pointer" src="@assets/images/checked.svg" @click="
                    retornarSeleccion(data[indextr].nombre, data[indextr].id)
                    " />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
          <div>
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-3"></vs-pagination>
          </div>
        </div>
      </div>
      <FormularioClientes :tipo="'agregar'" :show="verFormularioClientes" @closeVentana="verFormularioClientes = false"
        @retornar_id="retorno_id"></FormularioClientes>
      <!--fin de buscador-->
    </vs-popup>
  </div>
</template>
<script>
import FormularioClientes from "@pages/clientes/FormularioClientes";
import clientes from "@services/clientes";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

export default {
  components: {
    "v-select": vSelect,
    Datepicker,
    FormularioClientes,
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
          this.$refs["nombre_cliente"].$el.querySelector("input").focus()
        );
        this.$refs["buscador_cliente"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
        this.get_data("", 1);
      } else {
        /**cerrar y limpiar el formulario */
        this.serverOptions.id_cliente = "";
        this.serverOptions.cliente = "";
        this.serverOptions.rfc = "";
        this.serverOptions.celular = "";
        this.serverOptions.nacionalidad = this.nacionalidades[0];
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
      verFormularioClientes: false,
      selected: [],
      nacionalidades: [],
      disabledDates: {
        from: new Date(),
      },
      clientes: [],
      serverOptions: {
        page: "",
        per_page: "",
        id_cliente: "",
        rfc: "",
        celular: "",
        cliente: "",
        nacionalidad: {
          value: "122",
          label: "Mexicana",
        },
        nacionalidad_id: "",
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
      this.serverOptions.id_cliente = "";
      this.serverOptions.cliente = "";
      this.serverOptions.rfc = "";
      this.serverOptions.celular = "";
      this.serverOptions.nacionalidad = this.nacionalidades[0];
      this.get_data("", this.actual);
    },
    cancelar() {
      this.$emit("closeBuscador");
      return;
    },
    get_nacionalidades() {
      clientes
        .get_nacionalidades()
        .then((res) => {
          //le agrego las nacionalidades
          this.nacionalidades = [];
          this.nacionalidades.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.nacionalidades.push({
              label: element.nacionalidad,
              value: element.id,
            });
          });
          this.serverOptions.nacionalidad = this.nacionalidades[0];
        })
        .catch((err) => { });
    },
    get_data(origen = "", page, evento = "") {
      if (evento == "blur") {
        return;
      } else {
        /**checando el origen */
        if (origen == "cliente") {
          if (this.serverOptions.cliente.trim() == "") {
            return;
          }
        } else if (origen == "id_cliente") {
          if (this.serverOptions.id_cliente.trim() == "") {
            return;
          }
        } else if (origen == "rfc") {
          if (this.serverOptions.rfc.trim() == "") {
            return;
          }
        } else if (origen == "celular") {
          if (this.serverOptions.celular.trim() == "") {
            return;
          }
        }
      }

      let self = this;
      if (clientes.cancel) {
        clientes.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = 5;
      this.serverOptions.status = 1;
      this.serverOptions.nacionalidad_id = this.serverOptions.nacionalidad.value;
      clientes
        .get_clientes(this.serverOptions)
        .then((res) => {
          this.clientes = res.data.data;
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
    handleSearch(searching) { },
    handleChangePage(page) { },
    handleSort(key, active) { },
    retornarSeleccion(nombre = "", id = "") {
      /**retorna los datos seleccionados a la venta que los solicita */
      this.$emit("retornoCliente", { id_cliente: id, nombre: nombre });
      this.$emit("closeBuscador");
    },
    retorno_id(dato) {
      this.get_data("", this.actual);
    },
  },
  created() {
    this.get_nacionalidades();
  },
};
</script>