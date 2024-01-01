<template>
  <div>
    <div class="w-full text-right">
      <vs-button
        class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
        color="primary"
        @click="formulario('agregar')"
      >
        <span>Imprimir Lista de Registros</span>
      </vs-button>
      <vs-button
        class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
        color="success"
        @click="formulario('agregar')"
      >
        <span>Crear Registro</span>
      </vs-button>
    </div>
    <div class="pt-2 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
      <vx-card
        title="Filtros de selección"
        refresh-content-action
        @refresh="reset"
        :collapse-action="false"
      >
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 px-2">
            <div class="w-full input-text pb-2">
              <label class="">Filtrar Por Fecha</label>
              <v-select
                :options="filtrosEspecificos"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="filtroEspecifico"
                class="md:mb-0"
              />
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-4/12 xl:w-4/12 px-2">
            <div class="w-full input-text">
              <label class="">
                Rango de Fechas año/mes/dia
                <span>(*)</span>
              </label>
              <flat-pickr
                :disabled="habilitar_rango == 1"
                name="rango_fechas"
                data-vv-as=" "
                :config="configdateTimePickerRange"
                v-model="rango_fechas"
                placeholder="Rango de fechas del reporte"
                class="w-full"
                @on-close="onCloseDate"
              />
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-5/12 xl:w-5/12 px-2">
            <div class="w-full input-text pb-2">
              <label class="">Empleado</label>
              <v-select
                :options="empleados"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="empleado"
                class="md:mb-0"
              />
            </div>
          </div>
        </div>
      </vx-card>
    </div>
    <br />
    <vs-table
      :sst="true"
      @search="handleSearch"
      @change-page="handleChangePage"
      @sort="handleSort"
      :max-items="serverOptions.per_page.value"
      :data="registros"
      noDataText="0 Resultados"
      class="tabla-datos"
    >
      <template slot="header">
        <h3>Listado de Clientes Registrados</h3>
      </template>
      <template slot="thead">
        <vs-th>Fecha / Hora</vs-th>
        <vs-th>Empleado</vs-th>
        <vs-th>Tipo de Registro</vs-th>
        <vs-th>Forma de Registro</vs-th>
        <vs-th>Status</vs-th>
        <vs-th>Acciones</vs-th>
      </template>
      <template slot-scope="{ data }">
        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="data[indextr].id">
            <span
              :class="[
                'font-bold',
                data[indextr].status == 1 ? '' : 'text-danger',
              ]"
              >{{ data[indextr].fecha_registro_texto }}
              {{ data[indextr].hora_registro }}</span
            >
          </vs-td>
          <vs-td :data="data[indextr].usuario">{{
            data[indextr].usuario.nombre
          }}</vs-td>
          <vs-td :data="data[indextr].tipo_registro_texto">
            <p v-if="data[indextr].tipo_registro_id == 1">
              Entrada <span class="dot-success"></span>
            </p>
            <p v-else>Salida <span class="dot-warning"></span></p>
          </vs-td>
          <vs-td :data="data[indextr].forma_registro">
            {{ data[indextr].forma_registro }}
          </vs-td>

          <vs-td :data="data[indextr].status">
            <p v-if="data[indextr].status == 1">
              Activo <span class="dot-success"></span>
            </p>
            <p v-else>Cancelado <span class="dot-danger"></span></p>
          </vs-td>
          <vs-td :data="data[indextr].id_user">
            <div class="flex justify-center">
              <img
                class="cursor-pointer img-btn-18 mx-2"
                src="@assets/images/edit.svg"
                title="Modificar"
                @click="openModificar(data[indextr].id)"
              />
              <img
                v-if="data[indextr].status == 1"
                class="img-btn-24 mx-2"
                src="@assets/images/switchon.svg"
                title="Deshabilitar"
                @click="CancelarRegistro(data[indextr])"
              />
              <img
                v-else
                class="img-btn-24 mx-2"
                src="@assets/images/switchoff.svg"
                title="Habilitar"
                @click="AltaRegistro(data[indextr])"
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
        class="mt-8"
      ></vs-pagination>
    </div>
    <pre ref="pre"></pre>
    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>
    <Reporteador
      :header="'consultar reporte de venta'"
      :show="openReportesLista"
      :listadereportes="ListaReportes"
      :request="request"
      @closeReportes="openReportesLista = false"
    ></Reporteador>
    <FormularioRegistros
      :id_registro="registro_id"
      :tipo="tipoFormulario"
      :show="verFormularioRegistros"
      @closeVentana="verFormularioRegistros = false"
      @retornar_id="retorno_id"
    ></FormularioRegistros>
  </div>
</template>

<script>
//planes de venta
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import Reporteador from "@pages/Reporteador";
import checador from "@services/checador";

import FormularioRegistros from "@pages/control_de_asistencia/registros/FormularioRegistros";

//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */
import {
  configdateTimePickerRange,
  configdateTimePicker,
  PermisosModulo,
} from "@/VariablesGlobales";
import ServiciosGratis from "@pages/clientes/ServiciosGratis";
const moment = require("moment");
import vSelect from "vue-select";

export default {
  components: {
    "v-select": vSelect,
    Password,
    FormularioRegistros,
    Reporteador,
    ServiciosGratis,
    flatPickr,
  },
  watch: {
    actual: function (newValue, oldValue) {
      this.get_data(this.actual);
    },
    "empleado.value": function (newValue, oldValue) {
      (async () => {
        await this.get_data(1);
      })();
    },
    "filtroEspecifico.value": function (newValue, oldValue) {
      if (newValue == "1") {
        this.rango_fechas = [];
        (async () => {
          await this.get_data(1);
        })();
      }
    },
  },

  computed: {
    habilitar_rango: function () {
      if (this.filtroEspecifico.value != "") {
        return this.filtroEspecifico.value;
      } else return 1;
    },
  },
  data() {
    return {
      configdateTimePicker: configdateTimePicker,
      configdateTimePickerRange: configdateTimePickerRange,
      registros: [],
      filtroEspecifico: { label: "Todos los Registros", value: "1" },
      filtrosEspecificos: [
        {
          label: "Todos los Registros",
          value: "1",
        },
        {
          label: "Rango de Fechas",
          value: "2",
        },
      ],
      empleado: { label: "Todos", value: "" },
      empleados: [],
      serverOptions: {
        page: "",
        per_page: "25",
        status: "",
        filtro_especifico_opcion: "",
        fecha_inicio: "",
        fecha_fin: "",
        id_empleado: "",
      },
      verPaginado: true,
      total: 0,
      actual: 1,
      rango_fechas: [],
      registro_id: "",
      accionNombre: "",
      errores: [],
      //hasta aqui

      //control de servicios gratis
      datosCliente: [],
      //variable
      ListaReportes: [],
      PermisosModulo: PermisosModulo,
      openReportesLista: false,

      //fin variables
      openStatus: false,
      callback: Function,

      datosModifcar: {},
      tipoFormulario: "",
      verFormularioRegistros: false,
      verModificar: false,
      selected: [],
      users: [],
      /**opciones para filtrar la peticion del server */
      /**user id para bajas y altas */
      cliente_id: "",
      request: {
        venta_id: "",
        email: "",
      },
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.rango_fechas = [];
      this.filtroEspecifico = this.filtrosEspecificos[0];
      this.empleado = this.empleados[0];
      (async () => {
        await this.get_data(1);
      })();
    },

    async get_empleados() {
      try {
        //this.$vs.loading();
        let res = await checador.get_empleados();
        this.empleados.push({
          value: "",
          label: "Todos",
        });
        res.data.forEach((element) => {
          this.empleados.push({
            value: element.id,
            label: element.nombre,
          });
        });
        this.empleado = this.empleados[0];
        //this.$vs.loading.close();
      } catch (error) {
        //this.$vs.loading.close();
      }
    },

    async get_data(page, evento = "") {
      if (evento == "blur") {
        if (
          this.serverOptions.id_cliente != "" ||
          this.serverOptions.id_cliente == ""
        ) {
          //la funcion no avanza
          return false;
        }
        if (
          this.serverOptions.fecha_fin == "" ||
          this.serverOptions.fecha_fin != ""
        ) {
          //la funcion no avanza
          return false;
        }
        if (
          this.serverOptions.fecha_inicio == "" ||
          this.serverOptions.fecha_inicio != ""
        ) {
          //la funcion no avanza
          return false;
        }
      }
      if (checador.cancel) {
        checador.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;
      this.serverOptions.id_empleado = this.empleado.value;
      checador
        .get_registros(this.serverOptions)
        .then((res) => {
          this.registros = res.data.data;
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
                time: 8000,
              });
            }
          }
        });
    },

    onCloseDate(selectedDates, dateStr, instance) {
      /**se valdiad que se busque la informacion solo en los casos donde la fechas cambien */
      if (selectedDates.length == 0) {
        /**no hay fechas que buscar */
        this.serverOptions.fecha_inicio = "";
        this.serverOptionsfecha_fin = "";
        this.filtroEspecifico = this.filtrosEspecificos[0];
      } else if (selectedDates.length == 1) {
        this.serverOptions.fecha_inicio = moment(selectedDates[0]).format(
          "YYYY-MM-DD"
        );
        this.serverOptions.fecha_fin = moment(selectedDates[0]).format(
          "YYYY-MM-DD"
        );
        //aqui mando llamar los nuevos datos
        (async () => {
          await this.get_data(this.actual);
        })();
      } else {
        /**hay fechas que buscar */
        if (
          this.serverOptions.fecha_inicio !=
            moment(selectedDates[0]).format("YYYY-MM-DD") ||
          this.serverOptions.fecha_fin !=
            moment(selectedDates[1]).format("YYYY-MM-DD")
        ) {
          /**agreggo la fecha 1 */
          this.serverOptions.fecha_inicio = moment(selectedDates[0]).format(
            "YYYY-MM-DD"
          );
          this.serverOptions.fecha_fin = moment(selectedDates[1]).format(
            "YYYY-MM-DD"
          );
          //aqui mando llamar los nuevos datos
          (async () => {
            await this.get_data(this.actual);
          })();
        }
      }
    },
    validarRangoFecha() {
      if (
        this.serverOptions.fecha_inicio == "" ||
        this.serverOptions.fecha_fin == ""
      ) {
        this.$vs.notify({
          title: "Error",
          text: "Seleccione el rango de fechas para el reporte.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "6000",
        });
        return false;
      } else {
        return true;
      }
    },

    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},

    //eliminar usuario logicamente

    closeModificar() {
      this.verModificar = false;
    },

    closeStatus() {
      this.openStatus = false;
    },

    formulario(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioRegistros = true;
    },
    openModificar(id_registro) {
      this.tipoFormulario = "modificar";
      this.registro_id = id_registro;
      this.verFormularioRegistros = true;
    },
    retorno_id(dato) {
      (async () => {
        await this.get_data(this.actual);
      })();
    },

    AltaRegistro(registro) {
      this.registro_id = registro.id;
      this.openStatus = true;
      (async () => {
        this.callback = this.habilitar_registro;
      })();
    },

    CancelarRegistro(registro) {
      this.registro_id = registro.id;
      this.openStatus = true;
      (async () => {
        this.callback = await this.cancelar_registro;
      })();
    },
    async cancelar_registro() {
      this.$vs.loading();
      let datos = {
        registro_id: this.registro_id,
      };
      await checador
        .cancelar_registro(datos)
        .then((res) => {
          this.$vs.loading.close();
          (async () => {
            await this.get_data(this.actual);
          })();
          if (res.data >= 1) {
            this.$vs.notify({
              title: "Cancelar Registro",
              text: "Se ha deshabilitado el registro exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 8000,
            });
          } else {
            this.$vs.notify({
              title: "Cancelar Registro",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 8000,
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
                text: "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 8000,
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    },
    async habilitar_registro() {
      this.$vs.loading();
      let datos = {
        registro_id: this.registro_id,
      };
      await checador
        .habilitar_registro(datos)
        .then((res) => {
          this.$vs.loading.close();
          (async () => {
            await this.get_data(this.actual);
          })();
          if (res.data >= 1) {
            this.$vs.notify({
              title: "Habilitar Registro",
              text: "Se ha habilitado el registro exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 8000,
            });
          } else {
            this.$vs.notify({
              title: "Habilitar Registro",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 8000,
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
                text: "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 8000,
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    },
  },
  created() {
    (async () => {
      await this.get_empleados();
      await this.get_data(this.actual);
    })();
  },
};
</script>
