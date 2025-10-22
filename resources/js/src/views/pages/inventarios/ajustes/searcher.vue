<template>
  <div class="centerx">
    <vs-popup :class="['forms-popup bg-content-theme', z_index]" fullscreen title="Catálogo de articulos registrados"
      :active="localShow" :ref="this.$options.name">
      <div class="w-full text-right">
        <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
          @click="verFormularioArticulos = true">
          <span>Registrar Artículo</span>
        </vs-button>
      </div>

      <!--inicio de buscador-->
      <div class="py-3">
        <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
          :collapse-action="false">
          <template slot="no-body">
            <div>
              <div class="flex flex-wrap px-4 py-4">
                <div class="
                    w-full
                    sm:w-12/12
                    md:w-2/12
                    lg:w-2/12
                    xl:w-2/12
                    px-2
                    input-text
                  ">
                  <label class="text-sm opacity-75 font-bold">Núm. articulo</label>
                  <vs-input name="num_articulo" data-vv-as=" " type="text" class="w-full pb-1 pt-1"
                    placeholder="Ej. 1258" maxlength="6" v-model.trim="serverOptions.id_articulo"
                    v-on:keyup.enter="get_data('id_articulo', 1)" v-on:blur="get_data('id_articulo', 1, 'blur')" />
                  <div>
                    <span class="text-danger text-sm">
                      {{ errors.first("num_articulo") }}
                    </span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div class="
                    w-full
                    sm:w-12/12
                    md:w-2/12
                    lg:w-2/12
                    xl:w-2/12
                    px-2
                    input-text
                  ">
                  <label class="text-sm opacity-75 font-bold">Código de Barras</label>
                  <vs-input name="codigo_barras" data-vv-as=" " type="text" class="w-full pb-1 pt-1"
                    placeholder="ej. 0000001" maxlength="13" v-model.trim="serverOptions.codigo_barras"
                    v-on:keyup.enter="get_data('codigo_barras', 1)" v-on:blur="get_data('codigo_barras', 1, 'blur')" />
                  <div>
                    <span class="text-danger text-sm">
                      {{ errors.first("codigo_barras") }}
                    </span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div class="
                    w-full
                    sm:w-12/12
                    md:w-8/12
                    lg:w-8/12
                    xl:w-8/12
                    px-2
                    input-text
                  ">
                  <label class="text-sm opacity-75 font-bold">Nombre del Artículo</label>
                  <vs-input name="nombre_articulo" data-vv-as=" " type="text" class="w-full pb-1 pt-1"
                    placeholder="Ej. Ataúd de Madera" maxlength="12" v-model.trim="serverOptions.articulo"
                    v-on:keyup.enter="get_data('articulo', 1)" v-on:blur="get_data('articulo', 1, 'blur')" />
                  <div>
                    <span class="text-danger text-sm">
                      {{ errors.first("nombre_articulo") }}
                    </span>
                  </div>
                  <div class="mt-2"></div>
                </div>
              </div>
            </div>
          </template>
        </vx-card>

        <div class="resultados_articulos mt-10">
          <div class="w-full text-right">
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
              @click="SeleccionarTodos">
              <span>Seleccionar Todos</span>
            </vs-button>
          </div>

          <vs-table :sst="true" :max-items="serverOptions.per_page" :data="articulos" stripe noDataText="0 Resultados"
            class="tabla-datos mt-4">
            <template slot="header">
              <h3>Lista actualizada de artículos registrados</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Artículo</vs-th>
              <vs-th>Código Barras</vs-th>
              <vs-th>Descripción</vs-th>
              <vs-th hidden>Caduca</vs-th>
              <vs-th>($) Precio Venta</vs-th>
              <vs-th>Existencias</vs-th>
              <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{ data[indextr].id }}</span>
                </vs-td>
                <vs-td :data="data[indextr].codigo_barras">
                  <span class="font-semibold">
                    {{ data[indextr].codigo_barras }}
                  </span>
                </vs-td>
                <vs-td :data="data[indextr].descripcion">
                  <span class="uppercase">{{ data[indextr].descripcion }}</span>
                </vs-td>
                <vs-td hidden :data="data[indextr].caduca_texto">
                  <span class="uppercase">{{
                    data[indextr].caduca_texto
                  }}</span>
                </vs-td>
                <vs-td :data="data[indextr].precio_venta">
                  <span class="uppercase">$
                    {{
                      data[indextr].precio_venta | numFormat("0,000.00")
                    }}</span>
                </vs-td>
                <vs-td :data="data[indextr].existencia">
                  <span class="uppercase">{{ data[indextr].existencia }}</span>
                </vs-td>
                <vs-td :data="data[indextr].id">
                  <div class="flex flex-start">
                    <img width="25" class="cursor-pointer ml-auto mr-auto" src="@assets/images/checked.svg"
                      @click="retornarSeleccion(data[indextr])" />
                  </div>
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
          <div>
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-3"></vs-pagination>
          </div>
        </div>
      </div>
      <FormularioArticulos v-if="verFormularioArticulos" :tipo="'agregar'" :show="verFormularioArticulos"
        @closeVentana="verFormularioArticulos = false" @retornar_id="retorno_id" :z_index="'z-index56k'">
      </FormularioArticulos>
      <!--fin de buscador-->
    </vs-popup>
  </div>
</template>
<script>
import FormularioArticulos from "@pages/inventarios/articulos/FormularioArticulos";
import inventario from "@services/inventario";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

export default {
  name: "SearcherArticulos",
  components: {
    "v-select": vSelect,
    Datepicker,
    FormularioArticulos,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    z_index: {
      type: String,
      required: false,
      default: "z-index55k",
    },
  },
  watch: {
    "serverOptions.nacionalidad": function (newVal, previousVal) {
      this.get_data("", 1);
    },
    actual: function (newValue, oldValue) {
      this.get_data("", this.actual);
    },
    show: {
      immediate: true, // runs when component is mounted too
      async handler(newValue) {
        if (newValue) {
          this.get_data("", 1);
          this.$popupManager.register(this, this.cancelar, "nombre_articulo");
        } else {
          /**cerrar y limpiar el formulario */
          this.serverOptions.id_articulo = "";
          this.serverOptions.articulo = "";
          this.serverOptions.codigo_barras = "";
          this.serverOptions.celular = "";
          this.serverOptions.nacionalidad = this.nacionalidades[0];
          this.$popupManager.unregister(this.$options.name);
        }
        this.localShow = newValue;
      },
    },
  },
  computed: {
  },
  data() {
    return {
      localShow: false,
      verFormularioArticulos: false,
      selected: [],
      nacionalidades: [],
      disabledDates: {
        from: new Date(),
      },
      articulos: [],
      serverOptions: {
        page: "",
        per_page: "150",
        id_articulo: "",
        codigo_barras: "",
        celular: "",
        articulo: "",
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
      this.serverOptions.id_articulo = "";
      this.serverOptions.articulo = "";
      this.serverOptions.codigo_barras = "";
      this.serverOptions.celular = "";
      this.serverOptions.nacionalidad = this.nacionalidades[0];
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
        if (origen == "articulo") {
          if (this.serverOptions.articulo.trim() == "") {
            return;
          }
        } else if (origen == "id_articulo") {
          if (this.serverOptions.id_articulo.trim() == "") {
            return;
          }
        } else if (origen == "codigo_barras") {
          if (this.serverOptions.codigo_barras.trim() == "") {
            return;
          }
        } else if (origen == "celular") {
          if (this.serverOptions.celular.trim() == "") {
            return;
          }
        }
      }

      if (inventario.cancel) {
        inventario.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = 150;
      this.serverOptions.status = 1;
      inventario
        .get_inventariable(this.serverOptions)
        .then((res) => {
          this.articulos = res.data.data;
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
    handleSearch(searching) { },
    handleChangePage(page) { },
    handleSort(key, active) { },
    retornarSeleccion(datos) {
      let dato = [];
      dato.push(datos);
      /**retorna los datos seleccionados a la venta que los solicita */
      this.$emit("retornoArticulo", dato);
      this.$nextTick(() => {
        setTimeout(() => {
          this.$emit("closeBuscador");
        }, 50);
      });
    },
    SeleccionarTodos() {
      /**retorna todos los articulos que se listan los datos seleccionados */
      this.$emit("retornoArticulo", this.articulos);

      this.$nextTick(() => {
        setTimeout(() => {
          this.$emit("closeBuscador");
        }, 50);
      });

    },

    retorno_id(dato) {
      this.get_data("", this.actual);
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