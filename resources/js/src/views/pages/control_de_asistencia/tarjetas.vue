<template>
    <div class="flex flex-col flex-1">
        <div class="w-full text-right">
            <vs-button class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="primary" @click="openReporte()">
                <span>Imprimir Lista de Registros</span>
            </vs-button>
        </div>
        <div class="pt-2 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card title="Filtros de selección" refresh-content-action @refresh="reset" :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 px-2">
                        <div class="w-full input-text pb-2">
                            <label class="">Listar por Estado</label>
                            <v-select :options="filtrosEspecificos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-model="filtroEspecifico" class="md:mb-0" />
                        </div>
                    </div>
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 px-2">
                        <div class="w-full input-text">
                            <label class="">
                                Rango de Fechas año/mes/dia
                                <span>(*)</span>
                            </label>
                            <flat-pickr name="rango_fechas" data-vv-as=" " :config="configdateTimePickerRange"
                                v-model="rango_fechas" placeholder="Rango de fechas del reporte" class="w-full"
                                @on-close="onCloseDate" />
                        </div>
                    </div>
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 px-2">
                        <div class="w-full input-text">
                            <label class="">Nombre del empleado</label>
                            <vs-input class="w-full" icon="search" placeholder="Filtrar por nombre"
                                v-model="serverOptions.nombre" v-on:keyup.enter="get_data(1)"
                                v-on:blur="get_data(1, 'blur')" />
                        </div>
                    </div>
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 px-2">
                        <div class="w-full input-text pb-2">
                            <label class="">Área de Servicio</label>
                            <v-select :options="areasOptionsTodas" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-model="area" class="md:mb-0" />
                        </div>
                    </div>
                </div>
            </vx-card>
        </div>


        <div id="resultados" class="mt-5 flex flex-col flex-1">
            <div v-if="noDataFound" class="w-full skeleton flex-1 items-center justify-center">
                <span class="text-gray-600 text-lg font-normal">No hay datos que mostrar</span>
            </div>
            <div v-else id="results" class="w-full flex flex-wrap">
                <div class="w-full py-2">
                    <vs-table :sst="true" @search="handleSearch" @change-page="handleChangePage" @sort="handleSort"
                        :max-items="serverOptions.per_page.value" :data="registros" noDataText="0 Resultados"
                        class="tabla-datos">
                        <template slot="header">
                            <h3>Listado de Empleados Registrados</h3>
                        </template>
                        <template slot="thead">
                            <vs-th># de Empleado</vs-th>
                            <vs-th>Empleado</vs-th>
                            <vs-th>Usuario</vs-th>
                            <vs-th>Rol</vs-th>
                            <vs-th>Género</vs-th>
                            <vs-th>Área</vs-th>
                            <vs-th>Estado</vs-th>
                            <vs-th>Acciones</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                <vs-td :data="data[indextr].id">
                                    <span :class="['font-bold']">{{ data[indextr].id }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].nombre">{{ data[indextr].nombre }}</vs-td>
                                <vs-td :data="data[indextr].email"> {{ data[indextr].email }}</vs-td>
                                <vs-td :data="data[indextr].rol"> {{ data[indextr].rol.rol }}</vs-td>

                                <vs-td :data="data[indextr].genero">
                                    {{ data[indextr].genero }}</vs-td>
                                <vs-td :data="data[indextr].area_des">{{ data[indextr].area_des }}</vs-td>
                                <vs-td :data="data[indextr].status">
                                    <p v-if="data[indextr].status == 1">
                                        Activo <span class="dot-success"></span>
                                    </p>
                                    <p v-else>Cancelado <span class="dot-danger"></span></p>
                                </vs-td>
                                <vs-td :data="data[indextr].id">
                                    <div class="flex justify-center">
                                        <img class="cursor-pointer img-btn-18 mx-2" src="@assets/images/pdf.svg"
                                            title="Ver Reporte de Asistencia" @click="openReporte(data[indextr].id)" />
                                        <img class="cursor-pointer img-btn-18 mx-2" src="@assets/images/calendar.svg"
                                            title="Asignar Días de Descanso" @click="openDiasDescanso(data[indextr])" />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                    <div>
                        <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual"
                            class="mt-8"></vs-pagination>
                    </div>
                    <pre ref="pre"></pre>
                </div>
            </div>
        </div>


        <Password v-if="openStatus" :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus"
            :accion="accionNombre">
        </Password>
        <Reporteador v-if="openReportesLista" :header="'consultar reporte de registros'" :show="openReportesLista"
            :listadereportes="ListaReportes" :request="request" @closeReportes="openReportesLista = false">
        </Reporteador>
        <FormularioDiasDescanso v-if="verFormularioDiasDescanso" :usuario_id="usuario_id"
            :show="verFormularioDiasDescanso" @CloseFormularioDiasDescanso="verFormularioDiasDescanso = false">
        </FormularioDiasDescanso>
    </div>
</template>

<script>
//planes de venta
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import Reporteador from "@pages/Reporteador";
import checador from "@services/checador";
//componente de password
import Password from "@pages/confirmar_password";
import FormularioDiasDescanso from "@pages/control_de_asistencia/tarjetas/FormularioDiasDescanso";
/**VARIABLES GLOBALES */
import { configdateTimePickerRange, PermisosModulo, areasOptionsTodas } from "@/VariablesGlobales";
const moment = require("moment");
import vSelect from "vue-select";

export default {
    components: {
        "v-select": vSelect,
        Password,
        Reporteador,
        flatPickr,
        FormularioDiasDescanso,
        areasOptionsTodas
    },
    watch: {
        actual: function (newValue, oldValue) {
            this.get_data(this.actual);
        },
        "area.value": function (newValue, oldValue) {
            (async () => {
                await this.get_data(1);
            })();
        },
        "filtroEspecifico.value": function (newValue, oldValue) {
            (async () => {
                await this.get_data(1);
            })();
        },
    },
    computed: {
        noDataFound() {
            return (
                this.registros.length === 0
            );
        },
    },
    data() {
        return {
            areasOptionsTodas: areasOptionsTodas,
            area: { label: "Ambas", value: "0" },
            configdateTimePickerRange: configdateTimePickerRange,
            registros: [],
            filtroEspecifico: { label: "Empleados Vigentes", value: "1" },
            filtrosEspecificos: [
                {
                    label: "Todos los Empleados",
                    value: "",
                },
                {
                    label: "Empleados Vigentes",
                    value: "1",
                },
                {
                    label: "Empleados Cancelados",
                    value: "0",
                }
            ],
            usuario_id: "",
            empleados: [],
            serverOptions: {
                page: "",
                per_page: "25",
                status: "",
                filtro_especifico_opcion: "",
                fecha_inicio: "",
                fecha_fin: "",
                nombre: "",
                area: ''
            },
            verPaginado: true,
            total: 0,
            actual: 1,
            rango_fechas: [],
            registro_id: "",
            accionNombre: "",
            errores: [],
            ListaReportes: [],
            PermisosModulo: PermisosModulo,
            openReportesLista: false,
            openStatus: false,
            callback: Function,
            tipoFormulario: "",
            verFormularioDiasDescanso: false,
            verModificar: false,
            /**opciones para filtrar la peticion del server */
            /**user id para bajas y altas */
            request: {
                email: "",
                destinatario: "",
            },
        };
    },
    methods: {
        reset(card) {
            card.removeRefreshAnimation(500);
            this.rango_fechas = [];
            this.fecha_fin = "";
            this.fecha_inicio = "";
            this.serverOptions.nombre = "";
            this.area = this.areasOptionsTodas[0];
            this.filtroEspecifico = this.filtrosEspecificos[1];
            (async () => {
                await this.get_data(1);
            })();
        },

        async get_data(page, evento = "") {
            if (evento == "blur") {
                if (this.nombre != "") {
                    //la funcion no avanza
                    return false;
                }
                if (
                    this.serverOptions.area == "" ||
                    this.serverOptions.area != ""
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
            this.serverOptions.area = this.area.value;
            this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;
            checador
                .get_empleados_paginados(this.serverOptions)
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
            } else if (selectedDates.length == 1) {
                this.serverOptions.fecha_inicio = moment(selectedDates[0]).format(
                    "YYYY-MM-DD"
                );
                this.serverOptions.fecha_fin = moment(selectedDates[0]).format(
                    "YYYY-MM-DD"
                );
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

        handleSearch(searching) { },
        handleChangePage(page) { },
        handleSort(key, active) { },

        //eliminar usuario logicamente
        closeModificar() {
            this.verModificar = false;
        },
        closeStatus() {
            this.openStatus = false;
        },
        formulario(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioDiasDescanso = true;
        },
        openModificar(id_registro) {
            this.tipoFormulario = "modificar";
            this.registro_id = id_registro;
            this.verFormularioDiasDescanso = true;
        },
        openReporte(usuario = "") {
            if (!this.validarRangoFecha()) return;
            let url =
                "/checador/reporte_tarjeta?fecha_inicio=" +
                this.serverOptions.fecha_inicio +
                "&fecha_fin=" +
                this.serverOptions.fecha_fin;
            if (usuario != "") {
                url += "&usuario_id=" + usuario;
            } else {
                url += "&cementerio_funeraria_filtro=" + this.area.value;
                url += "&nombre=" + this.serverOptions.nombre;
            }

            this.ListaReportes = [];
            this.ListaReportes.push({
                nombre: "Reporte de Asistencia",
                url: url,
            });
            this.openReportesLista = true;
        },
        openDiasDescanso(datos) {
            this.usuario_id = datos.id;
            this.verFormularioDiasDescanso = true;
        },
    },
    created() {
        (async () => {
            await this.get_data(this.actual);
        })();
    },
};
</script>
