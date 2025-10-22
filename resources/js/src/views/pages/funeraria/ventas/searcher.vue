<template>
  <div class="centerx">
    <vs-popup :class="['forms-popup popup-85', z_index]" title="Catálogo de Planes Funerarios a Futuro Vendidos"
      :active="localShow" :ref="this.$options.name">
      <!--inicio de buscador-->
      <div class="py-6">
        <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset">
          <template slot="no-body">
            <div>
              <div class="flex flex-wrap px-4 py-6">
                <div class="w-full input-text xl:w-3/12 px-2">
                  <label class="">Núm. Convenio</label>
                  <vs-input name="numero_control" data-vv-as=" " type="text" class="w-full" placeholder="Ej. 1258"
                    maxlength="6" v-model.trim="serverOptions.numero_control"
                    v-on:keyup.enter="get_data('numero_control', 1)"
                    v-on:blur="get_data('numero_control', 1, 'blur')" />

                  <span class="">{{ errors.first("numero_control") }}</span>
                </div>

                <div class="w-full input-text xl:w-9/12 px-2">
                  <label class="">Titular del Plan Funerario</label>
                  <vs-input ref="nombre_titular" name="nombre_titular" data-vv-as=" " type="text" class="w-full"
                    placeholder="Ej. Juan Pérez" maxlength="12" v-model.trim="serverOptions.titular"
                    v-on:keyup.enter="get_data('titular', 1)" v-on:blur="get_data('titular', 1, 'blur')" />

                  <span class="">{{ errors.first("nombre_titular") }}</span>
                </div>
              </div>
            </div>
          </template>
        </vx-card>
        <div class="py-6">
          <vs-table :sst="true" :max-items="serverOptions.per_page" :data="terrenos" stripe noDataText="0 Resultados"
            class="tabla-datos">
            <template slot="header">
              <h3>Lista de planes a futuro vendidos</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Venta</vs-th>
              <vs-th>Núm. Convenio</vs-th>
              <vs-th>Titular</vs-th>
              <vs-th>Plan Funerario</vs-th>
              <vs-th>Estado</vs-th>
              <vs-th>Seleccionar</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].ventas_planes_id" :class="data[indextr].operacion_status == 0
                  ? 'color-danger-900'
                  : ''
                  ">
                  <span class="font-semibold">{{
                    data[indextr].ventas_planes_id
                    }}</span>
                </vs-td>
                <vs-td :data="data[indextr].id" :class="data[indextr].operacion_status == 0
                  ? 'color-danger-900'
                  : ''
                  ">{{ data[indextr].numero_convenio }}</vs-td>

                <vs-td :data="data[indextr].id" :class="data[indextr].operacion_status == 0
                  ? 'color-danger-900'
                  : ''
                  ">{{ data[indextr].nombre }}</vs-td>
                <vs-td :data="data[indextr].venta_plan" :class="data[indextr].operacion_status == 0
                  ? 'color-danger-900'
                  : ''
                  ">{{ data[indextr].venta_plan.nombre_original }}</vs-td>
                <vs-td :data="data[indextr].status_texto" :class="data[indextr].operacion_status == 0
                  ? 'color-danger-900'
                  : ''
                  ">
                  <p v-if="data[indextr].operacion_status == 0">
                    {{ data[indextr].status_texto }}
                    <span class="dot-danger"></span>
                  </p>
                  <p v-else-if="data[indextr].operacion_status == 1">
                    {{ data[indextr].status_texto }}
                    <span class="dot-warning"></span>
                  </p>
                  <p v-else-if="data[indextr].operacion_status == 2">
                    {{ data[indextr].status_texto }}
                    <span class="dot-success"></span>
                  </p>
                </vs-td>
                <vs-td :data="data[indextr].id">
                  <img class="cursor-pointer img-btn-20 mx-3" src="@assets/images/checked.svg"
                    @click="retornarSeleccion(data[indextr])" />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
          <div class="pt-6">
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class=""></vs-pagination>
          </div>
        </div>
      </div>
      <FormularioVentas :id_venta="''" :tipo="'agregar'" :show="verFormularioVentas"
        @closeVentana="verFormularioVentas = false" @ver_pdfs_nueva_venta="get_data('', 1)"></FormularioVentas>
      <!--fin de buscador-->
    </vs-popup>
  </div>
</template>
<script>
import FormularioVentas from "@pages/cementerio/ventas/FormularioVentas";
import funeraria from "@services/funeraria";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

export default {
  name: "PlanesFunerariosBuscador",
  components: {
    "v-select": vSelect,
    Datepicker,
    FormularioVentas,
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
    show: {
      immediate: true, // runs when component is mounted too
      async handler(newValue) {
        if (newValue) {
          this.get_data("", 1);
          this.$popupManager.register(this, this.cancelar, "nombre_titular");
        } else {
          /**cerrar y limpiar el formulario */
          this.serverOptions.numero_control = "";
          this.serverOptions.titular = "";
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
      verFormularioVentas: false,
      selected: [],
      disabledDates: {
        from: new Date(),
      },
      terrenos: [],
      serverOptions: {
        filtro_especifico_opcion: 2,
        page: "",
        per_page: "",
        numero_control: "",
        titular: "",
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
      this.serverOptions.titular = "";
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
        if (origen == "titular") {
          if (this.serverOptions.titular.trim() == "") {
            //return;
          }
        } else if (origen == "numero_control") {
          if (this.serverOptions.numero_control.trim() == "") {
            //return;
          }
        }
      }

      let self = this;
      if (funeraria.cancel) {
        funeraria.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = 12;
      funeraria
        .get_planes_a_futuro(this.serverOptions)
        .then((res) => {
          this.terrenos = res.data.data;
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
    retornarSeleccion(datos) {
      if (datos.operacion_status != 0) {
        /**retorna los datos seleccionados a la venta que los solicita */
        this.$emit("retornoPlan", datos);
        this.$nextTick(() => {
          setTimeout(() => {
            this.$emit("closeBuscador");
          }, 50);
        });
      } else {
        /**error, venta cancelada */
        this.$vs.notify({
          title: "Servicios Funerarios",
          text: "Seleccione otra opción, esta operación fue cancelada.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          time: 6000,
        });
      }
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