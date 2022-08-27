<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups big-forms planes_venta_form"
      fullscreen
      title="listado de planes funerarios"
      :active.sync="showVentana"
      ref="planes_planes"
    >
      <div class="flex flex-wrap pb-12 pt-2">
        <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12">
          <label class="text-sm opacity-75 font-bold">
            <span class="uppercase">Filtrar por plan Funerario:</span>
          </label>
          <v-select
            :options="tipo_planes"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="plan_tipo"
            class="pb-1 pt-1"
          >
            <div slot="no-options">Seleccione un Plan Funerario</div>
          </v-select>
        </div>
        <div class="w-full sm:w-12/12 md:w-7/12 lg:w-7/12 xl:w-7/12">
          <vs-button
            class="float-right mt-6"
            size="small"
            color="success"
            @click="agregarPrecios()"
          >
            <img
              class="cursor-pointer img-btn"
              src="@assets/images/precios.svg"
            />
            <span class="texto-btn">Agregar Precios </span>
          </vs-button>
          <vs-button
            class="float-right mt-6 mr-8"
            size="small"
            color="primary"
            @click="agregarPlan()"
          >
            <img class="cursor-pointer img-btn" src="@assets/images/plus.svg" />
            <span class="texto-btn">Crear Planes Funerarios</span>
          </vs-button>
          <vs-button
            class="float-right mr-8 mt-6"
            size="small"
            color="primary"
            @click="
              openReporte(
                'Todos los Planes (Español)',
                '/funeraria/planes_funerarios',
                ''
              )
            "
          >
            <img
              class="cursor-pointer img-btn"
              src="@assets/images/printer.svg"
            />
            <span class="texto-btn">Todos los Planes</span>
          </vs-button>
        </div>
      </div>
      <div v-if="planes.length > 0">
        <div
          v-for="(plan, index) in planes"
          :key="index"
          :class="[indexPrecios(index, plan.id), 'w-full']"
        >
          <vs-table :data="plan.precios" noDataText="0 Resultados">
            <template slot="header">
              <table class="w-full">
                <thead>
                  <tr>
                    <td
                      class="text-left w-full sm:w-12/12 md:w-10/12 lg:w-10/12 xl:w-10/12"
                    >
                      <span class="text-sm uppercase font-bold">
                        Plan Funerario
                      </span>
                    </td>
                    <td class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12">
                      <span class="text-sm uppercase font-bold">
                        Acciones
                      </span>
                    </td>
                  </tr>
                </thead>
                <tbody class="bg-white">
                  <tr class="border-solid border-2 border-gray-600">
                    <td class="text-left">
                      <span class="text-base font-medium uppercase">
                        {{ plan.plan }}
                      </span>
                    </td>
                    <td>
                      <img
                        class="cursor-pointer img-btn ml-auto mb-2"
                        src="@assets/images/edit.svg"
                        title="Modificar"
                        @click="openModificarPlan(plan.id)"
                      />
                      <img
                        v-if="plan.status == 1"
                        class="cursor-pointer img-btn-32 mr-auto ml-3"
                        src="@assets/images/switchon.svg"
                        title="Deshabilitar"
                        @click="
                          enable_disable_plan(
                            plan.id,
                            plan.plan,
                            'deshabilitar'
                          )
                        "
                      />
                      <img
                        v-else
                        class="cursor-pointer img-btn-32 mr-auto ml-3"
                        src="@assets/images/switchoff.svg"
                        title="Habilitar"
                        @click="
                          enable_disable_plan(plan.id, plan.plan, 'habilitar')
                        "
                      />

                      <img
                        width="25"
                        class="cursor-pointer mr-auto ml-4 mb-1"
                        src="@assets/images/printer.svg"
                        title="Imprimir Plan Funerario"
                        @click="
                          openReportePlan(
                            'Precios x Plan (Español)',
                            '/funeraria/pdf_plan_funerario',
                            plan.id
                          )
                        "
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
            </template>
            <template slot="thead">
              <vs-th>#</vs-th>
              <vs-th>Pago Inicial ($)</vs-th>
              <vs-th>Costo Neto ($)</vs-th>
              <vs-th>Costo a Pronto Pago ($)</vs-th>
              <vs-th>Abono Mensual ($)</vs-th>
              <vs-th>Desc. x Pago ($)</vs-th>
              <vs-th>Financiamiento</vs-th>
              <vs-th>Descripción</vs-th>
              <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr
                :data="precio"
                :key="index_precio"
                v-for="(precio, index_precio) in data"
              >
                <vs-td>
                  <span class="font-semibold">{{ index_precio + 1 }}</span>
                </vs-td>
                <vs-td
                  >$ {{ precio.pago_inicial | numFormat("0,000.00") }}</vs-td
                >
                <vs-td>$ {{ precio.costo_neto | numFormat("0,000.00") }}</vs-td>
                <vs-td
                  >$
                  {{
                    precio.costo_neto_pronto_pago | numFormat("0,000.00")
                  }}</vs-td
                >
                <vs-td :data="data[index_precio].pago_mensual"
                  >$ {{ precio.pago_mensual | numFormat("0,000.00") }}</vs-td
                >
                <vs-td
                  >$
                  {{ precio.descuento_x_pago | numFormat("0,000.00") }}</vs-td
                >
                <vs-td>{{ precio.tipo_financiamiento }}</vs-td>
                <vs-td>{{ precio.descripcion }}</vs-td>

                <vs-td>
                  <img
                    class="cursor-pointer img-btn ml-auto mb-1"
                    src="@assets/images/edit.svg"
                    title="Modificar"
                    @click="openModificar(data[index_precio].id)"
                  />
                  <img
                    v-if="data[index_precio].status == 1"
                    class="cursor-pointer img-btn-32 ml-3"
                    src="@assets/images/switchon.svg"
                    title="Deshabilitar"
                    @click="
                      enable_disable_precio(
                        data[index_precio].id,
                        data[index_precio].descripcion,
                        'deshabilitar'
                      )
                    "
                  />

                  <img
                    v-else
                    class="cursor-pointer img-btn-32 mr-auto ml-3"
                    src="@assets/images/switchoff.svg"
                    title="Habilitar"
                    @click="
                      enable_disable_precio(
                        data[index_precio].id,
                        data[index_precio].descripcion,
                        'habilitar'
                      )
                    "
                  />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </div>
      </div>

      <!--componente de confirmar sin contraseña-->
      <ConfirmarDanger
        :z_index="'z-index58k'"
        :show="operConfirmar"
        :callback-on-success="callback"
        @closeVerificar="operConfirmar = false"
        :accion="'¿Desea eliminar este plan de mensualidades? Los datos quedarán eliminados del sistema.'"
        :confirmarButton="'Eliminar'"
      ></ConfirmarDanger>
      <Password
        :show="openPassword"
        :callback-on-success="callbackPassword"
        @closeVerificar="openPassword = false"
        :accion="accionPassword"
      ></Password>

      <FormularioPrecios
        :id_precio="id_precio_modificar"
        :tipo="tipoFormulario"
        :show="verFormularioPrecios"
        @closeVentana="verFormularioPrecios = false"
        @retornar_id="retorno_plan"
      ></FormularioPrecios>

      <FormularioPlanes
        :id_plan="id_plan_modificar"
        :tipo="tipoFormularioPlan"
        :show="verFormularioPlan"
        @closeVentana="verFormularioPlan = false"
        @retornar="retorno_plan"
      ></FormularioPlanes>

      <Reporteador
        :header="'Consultar precios x propiedad'"
        :show="openReportesLista"
        :listadereportes="ListaReportes"
        :request="request"
        @closeReportes="openReportesLista = false"
      ></Reporteador>
      <!--fin de compornentes-->
    </vs-popup>
  </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import ConfirmarDanger from "@pages/ConfirmarDanger";
import Password from "@pages/confirmar_password";
import planes from "@services/planes";
import FormularioPrecios from "@pages/funeraria/ventas/FormularioPrecios";
import FormularioPlanes from "@pages/funeraria/ventas/FormularioPlanes";
import vSelect from "vue-select";
export default {
  props: {
    show: {
      type: Boolean,
      required: true,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["planes_planes"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };

        (async () => {
          await this.get_planes();
        })();
      } else {
        /**cerrar ventana */
        this.datosVenta = [];
        this.total = 0;
      }
    },
  },
  components: {
    ConfirmarDanger,
    Password,
    "v-select": vSelect,
    FormularioPrecios,
    Reporteador,
    FormularioPlanes,
  },
  data() {
    return {
      plan_tipo: {},
      tipo_planes: [],
      tipoFormularioPlan: "",
      verFormularioPlan: false,
      id_plan_modificar: 0,
      /**variables del modulo */
      /**reporteador */
      openReportesLista: false,
      ListaReportes: [],
      request: {
        destinatario: "",
        id_tipo_propiedad: "",
        email: "",
      },

      tipoFormularioPrecio: "",
      /**reportrador */
      selected: [],
      planes: [],
      planes_tipos: [],
      propiedad_tipo: {},
      verFormularioPrecios: false,
      tipoFormulario: "",
      id_precio_modificar: 0,
      /** */
      operConfirmar: false,
      openPassword: false,
      accionPassword: "",
      callback: Function,
      callbackPassword: Function,
      //datos que mando para actualizar
    };
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
    agregarPlan() {
      this.tipoFormularioPlan = "agregar";
      this.verFormularioPlan = true;
    },
    openModificarPlan(id_plan) {
      this.tipoFormularioPlan = "modificar";
      this.id_plan_modificar = id_plan;
      this.verFormularioPlan = true;
    },
    enable_disable_plan(id_plan, plan, accion) {
      this.accionPassword = accion + " plan " + plan;
      this.id_plan_modificar = id_plan;
      this.openPassword = true;
      this.callbackPassword = this.enable_disable_planes;
    },
    enable_disable_planes() {
      this.$vs.loading();
      let datos = {
        id_plan: this.id_plan_modificar,
      };
      planes
        .enable_disable_planes(datos)
        .then((res) => {
          this.$vs.loading.close();
          (async () => {
            await this.get_planes();
          })();
          if (res.data >= 1) {
            this.$vs.notify({
              title: "Cambiar estatus del plan funerario",
              text: "Se ha actualizado el estatus exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 4000,
            });
          } else {
            this.$vs.notify({
              title: "Cambiar estatus del plan funerario",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "primary",
              time: 4000,
            });
          }
        })
        .catch((err) => {
          this.$vs.loading.close();
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
              /**error de validacion */
              this.errores = err.response.data.error;
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Cambiar estatus del precio",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
              });
            }
          }
        });
    },

    agregarPrecios() {
      this.tipoFormulario = "agregar";
      this.verFormularioPrecios = true;
    },
    /**funciones del modulo */
    openReportePlan(nombre_reporte = "", link = "", parametro = "") {
      this.ListaReportes = [];
      /**agrego los reportes de manera manual */
      this.ListaReportes.push({
        nombre: "Precios x Plan (Español)",
        url: "/funeraria/pdf_plan_funerario/es",
      });
      this.ListaReportes.push({
        nombre: "Precios x Plan (Inglés)",
        url: "/funeraria/pdf_plan_funerario/en",
      });
      //estado de cuenta
      this.request.email = "";
      this.request.id_plan = parametro;
      this.openReportesLista = true;
      this.$vs.loading.close();
    },
    openReporte(nombre_reporte = "", link = "", parametro = "") {
      this.ListaReportes = [];
      /**agrego los reportes de manera manual */
      this.ListaReportes.push({
        nombre: "Todos los Planes (Español)",
        url: "/funeraria/planes_funerarios/es",
      });
      this.ListaReportes.push({
        nombre: "Todos los Planes (Inglés)",
        url: "/funeraria/planes_funerarios/en",
      });
      //estado de cuenta
      this.request.email = "";
      this.request.id_tipo_propiedad = this.propiedad_tipo.value;
      this.openReportesLista = true;
      this.$vs.loading.close();
    },
    openModificar(id_precio) {
      this.tipoFormulario = "modificar";
      this.id_precio_modificar = id_precio;
      this.verFormularioPrecios = true;
    },
    agregar() {
      this.tipoFormulario = "agregar";
      this.verFormularioPrecios = true;
    },
    indexPrecios(index, plan_id) {
      if (this.plan_tipo.value != "") {
        if (plan_id != this.plan_tipo.value) {
          return "hidden";
        }
      } else {
        if (index == 0) {
          return "";
        } else {
          return "pt-16";
        }
      }
    },
    async get_financiamientos() {
      try {
        this.$vs.loading();
        let res = await planes.get_financiamientos();
        this.planes = res.data;
        /**llenando los tipos de propiedad para el select */
        this.planes_tipos = [];
        this.planes_tipos.push({
          label: "Todas las Propiedades",
          value: "",
        });
        this.planes.forEach((element) => {
          this.planes_tipos.push({
            label: "Tipo " + element.tipo,
            value: element.id,
          });
          //la primero opcion
          this.propiedad_tipo = this.planes_tipos[0];
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
    cancelar() {
      this.$emit("closePlanesFuneraria");
      return;
    },
    retorno_plan(dato) {
      (async () => {
        await this.get_planes();
      })();
    },
    enable_disable_precio(id_precio, precio, accion) {
      this.accionPassword = accion + " precio " + precio;
      this.id_precio_modificar = id_precio;
      this.openPassword = true;
      this.callbackPassword = this.enable_disable;
    },
    enable_disable() {
      this.$vs.loading();
      let datos = {
        id_precio: this.id_precio_modificar,
      };
      planes
        .enable_disable(datos)
        .then((res) => {
          this.$vs.loading.close();
          (async () => {
            await this.get_planes();
          })();
          if (res.data >= 1) {
            this.$vs.notify({
              title: "Cambiar estatus del precio",
              text: "Se ha actualizado el estatus exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 4000,
            });
          } else {
            this.$vs.notify({
              title: "Cambiar estatus del precio",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "primary",
              time: 4000,
            });
          }
        })
        .catch((err) => {
          this.$vs.loading.close();
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
              /**error de validacion */
              this.errores = err.response.data.error;
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Cambiar estatus del precio",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
              });
            }
          }
        });
    },
  },
  created() {},
};
</script>