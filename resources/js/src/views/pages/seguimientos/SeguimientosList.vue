<template>
    <div>
        <div class="text-right buttons-container-header">
            <vs-button class="w-full md:w-auto md:ml-2 md:mt-0" color="success" @click="OpenFormSeguimientos()">
                <span>Registrar Seguimiento</span>
            </vs-button>
        </div>
        <div class="mt-5 vx-col w-full">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-full md:w-6/12 lg:w-4/12 xl:w-2/12 px-2 input-text">
                        <label class="">Tipo de Seguimiento</label>
                        <v-select :options="tipoSeguimientos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="tipo_seguimiento" class="w-full" @input="onFilterChange" />
                    </div>
                    <div class="w-full sm:w-full md:w-6/12 lg:w-4/12 xl:w-3/12 px-2 input-text">
                        <label class="">Tipo de Operación</label>
                        <v-select :options="empresaOperaciones" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="empresa_operacion" class="w-full" @input="onFilterChange" />
                    </div>
                    <div class="w-full sm:w-full md:w-6/12 lg:w-4/12 xl:w-3/12 input-text px-2">
                        <label class="">
                            Filtrar por Fechas
                            <span>(*)</span>
                        </label>
                        <flat-pickr name="rango_fechas" data-vv-as=" " :config="configdateTimePickerRange"
                            v-model="rango_fechas" placeholder="Rango de fechas" class="w-full"
                            @on-close="onCloseDate" />
                    </div>
                    <div class="w-full sm:w-full md:w-6/12 lg:w-full xl:w-4/12 input-text px-2">
                        <label class="">Nombre del Cliente</label>
                        <vs-input name="cliente_nombre" class="w-full" icon="search"
                            placeholder="Filtrar por Nombre del Cliente" v-model="serverOptions.cliente_nombre"
                            maxlength="75" @keyup.enter="onEnterPress('cliente_nombre')"
                            @blur="onBlurFetch('cliente_nombre')" />
                    </div>
                </div>
            </vx-card>
        </div>

        <div id="resultados" class="mt-5 flex flex-col flex-1">
            <div v-if="noDataFound" class="w-full skeleton flex-1 items-center justify-center">
                <span class="text-gray-600 text-lg font-normal">No hay datos que mostrar</span>
            </div>
            <div v-else id="results" class="w-full flex flex-wrap">
                <!--Programados-->
                <div v-if="verProgramados" class="w-full py-2">
                    <vs-table :sst="true" :max-items="serverOptions.per_page" :data="ProgramadosList"
                        noDataText="0 Resultados" class="tabla-datos">
                        <template slot="header">
                            <h3>Seguimientos Programados Pendientes</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Acciones</vs-th>
                            <vs-th>Cliente</vs-th>
                            <vs-th>Motivo </vs-th>
                            <vs-th>Fecha Programada</vs-th>
                            <vs-th>Cancelar</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                <!-- Main columns -->
                                <vs-td>
                                    <div class="flex justify-center">
                                        <img class="cursor-pointer img-btn-20 mx-4" src="@assets/images/folder.svg"
                                            title="Consultar Seguimiento" @click="
                                                programarSeguimiento(
                                                    'consultar',
                                                    tr
                                                )
                                                " />
                                        <img class="cursor-pointer img-btn-20 mx-4" src="@assets/images/edit.svg"
                                            title="Modificar Seguimiento" @click="
                                                programarSeguimiento(
                                                    'modificar',
                                                    tr
                                                )
                                                " />
                                        <img class="img-btn-20 mx-4" src="@assets/images/seguimientos.svg"
                                            title="Atender Seguimiento" @click="
                                                registrarSeguimiento(
                                                    'atender_seguimiento_programado',
                                                    tr
                                                )
                                                " />
                                    </div>
                                </vs-td>
                                <vs-td><span v-if="tr.tipo_cliente_id === 1">{{ tr.cliente.nombre }}</span> <span
                                        v-else>{{ tr.cotizacion.nombre }}</span></vs-td>
                                <vs-td>{{ tr.motivo_texto }}</vs-td>
                                <vs-td>{{
                                    !hasSeguimientos
                                        ? tr.fechahora_programada_texto
                                        : tr.fechahora_programada_texto_abr
                                }}</vs-td>
                                <vs-td>
                                    <div class="flex justify-center">
                                        <img class="img-btn-20 mx-3" src="@assets/images/trash.svg"
                                            title="Cancelar Seguimiento" @click="
                                                programarSeguimiento(
                                                    'cancelar',
                                                    tr
                                                )
                                                " />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                    <div>
                        <vs-pagination v-if="programados_params.verPaginado" :total="programados_params.total"
                            v-model="programados_params.page" class="mt-8"></vs-pagination>
                    </div>
                </div>
                <!--Realizados-->
                <div v-if="verSeguimientos" class="w-full py-2">
                    <vs-table :sst="true" :max-items="serverOptions.per_page" :data="SeguimientosList"
                        noDataText="0 Resultados" class="tabla-datos">
                        <template slot="header">
                            <h3>Seguimientos Atendidos</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Acciones</vs-th>
                            <vs-th>Cliente</vs-th>
                            <vs-th>Motivo</vs-th>
                            <vs-th>Resultado Obtenido</vs-th>
                            <vs-th>Fue Programado</vs-th>
                            <vs-th>Fecha Realizado</vs-th>
                            <vs-th>Cancelar</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                <!-- Main columns -->
                                <vs-td>
                                    <div class="flex justify-center">
                                        <img class="cursor-pointer img-btn-20 mx-4" src="@assets/images/folder.svg"
                                            title="Consultar Seguimiento" @click="
                                                registrarSeguimiento(
                                                    'consultar',
                                                    tr
                                                )
                                                " />
                                        <img class="cursor-pointer img-btn-20 mx-4" src="@assets/images/edit.svg"
                                            title="Modificar Seguimiento" @click="
                                                registrarSeguimiento(
                                                    'modificar',
                                                    tr
                                                )
                                                " />
                                    </div>
                                </vs-td>
                                <vs-td><span v-if="tr.tipo_cliente_id === 1">{{ tr.cliente.nombre }}</span> <span
                                        v-else>{{ tr.cotizacion.nombre }}</span></vs-td>
                                <vs-td>{{ tr.motivo_texto }}</vs-td>
                                <vs-td>{{ tr.resultado_texto }}</vs-td>
                                <vs-td>{{ tr.tipo_programado_texto }}</vs-td>
                                <vs-td>{{
                                    tr.fechahora_seguimiento_texto_abr
                                }}</vs-td>
                                <vs-td>
                                    <div class="flex justify-center">
                                        <img class="img-btn-20 mx-3" src="@assets/images/trash.svg"
                                            title="Cancelar Seguimiento" @click="
                                                registrarSeguimiento('cancelar', tr)
                                                " />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                    <div>
                        <vs-pagination v-if="realizados_params.verPaginado" :total="realizados_params.total"
                            v-model="realizados_params.page" class="mt-8"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>

        <FormularioSeguimientos v-if="verFormSeguimientos" :show="verFormSeguimientos" @closeVentana="reloadList">
        </FormularioSeguimientos>
        <FormularioRegistrarSeguimiento v-if="ShowFormRegistrarSeguimientos" :show="ShowFormRegistrarSeguimientos"
            :filters="filtersSeguimiento" :tipo="tipoFormSeguimiento"
            @closeVentana="ShowFormRegistrarSeguimientos = false" @agregar_modificar_success_seguimiento="
                agregar_modificar_success_seguimiento
            " />
        <FormularioProgramarSeguimiento v-if="ShowFormProgramarSeguimientos" :show="ShowFormProgramarSeguimientos"
            :filters="filtersSeguimiento" :tipo="tipoFormSeguimiento"
            @closeVentana="ShowFormProgramarSeguimientos = false" @agregar_modificar_success_seguimiento="
                agregar_modificar_success_seguimiento
            " />
    </div>
</template>
<script>
import debounce from "lodash/debounce";
import seguimientos from "../../../services/seguimientos";
import FormularioSeguimientos from "@pages/seguimientos/FormularioSeguimientos";
import vSelect from "vue-select";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import { configdateTimePickerRange } from "@/VariablesGlobales";
import FormularioProgramarSeguimiento from "./FormularioProgramarSeguimiento";
import FormularioRegistrarSeguimiento from "./FormularioRegistrarSeguimiento.vue";
const moment = require("moment");
export default {
    name: "SeguimientosList",
    components: {
        FormularioSeguimientos,
        "v-select": vSelect,
        flatPickr,
        FormularioRegistrarSeguimiento,
        FormularioProgramarSeguimiento
    },
    computed: {
        verSeguimientos() {
            return (
                (Array.isArray(this.SeguimientosList) &&
                    this.SeguimientosList.length > 0) &&
                (this.tipo_seguimiento.value === "" ||
                    this.tipo_seguimiento.value === "0")
            );
        },
        verProgramados() {
            return (
                (Array.isArray(this.ProgramadosList) &&
                    this.ProgramadosList.length > 0) && (
                    this.tipo_seguimiento.value === "" ||
                    this.tipo_seguimiento.value === "1")
            );
        },
        noDataFound() {
            let res = false;
            return (
                (this.ProgramadosList.length === 0 &&
                    this.SeguimientosList.length === 0) || (this.tipo_seguimiento.value === '1' && this.ProgramadosList.length === 0) || (this.tipo_seguimiento.value === '0' && this.SeguimientosList.length === 0)
            );
        },
    },
    watch: {
        'realizados_params.page': function (newValue) {
            this.fetchDataSeguimientos("realizados");
        },
        'programados_params.page': function (newValue) {
            this.fetchDataSeguimientos("programados");
        },
    },
    data() {
        return {
            ShowFormRegistrarSeguimientos: false,
            ShowFormProgramarSeguimientos: false,
            filtersSeguimiento: [],
            tipoFormSeguimiento: '',
            isLoading: false, //API CALLS
            rango_fechas: [],
            configdateTimePickerRange: configdateTimePickerRange,
            verFormSeguimientos: false,
            tipoSeguimientos: [
                {
                    label: "Todos",
                    value: "",
                },
                {
                    label: "Programados",
                    value: "1",
                },
                {
                    label: "Atendidos",
                    value: "0",
                },
            ],
            empresaOperaciones: [
                {
                    label: "Todas",
                    value: "",
                },
                {
                    label: "Venta de espacios en cementerio.",
                    value: "1",
                },
                {
                    label: "Cuotas de mantenimiento",
                    value: "2",
                },
                {
                    label: "Servicios funerarios",
                    value: "3",
                },
                {
                    label: "Planes funerarios",
                    value: "4",
                },
                {
                    label: "Ventas en GRAL.",
                    value: "5",
                },
            ],
            empresa_operacion: {},
            tipo_seguimiento: {},
            //results
            ProgramadosList: [],
            SeguimientosList: [],
            serverOptions: {
                per_page: 10,
                cliente_nombre: "",
                fecha_inicio: "",
                fecha_fin: "",
                empresa_operaciones_id: null,
                paginated_b: 1,
                status: 1
            },
            previousServerOptions: {
                empresa_operaciones_id: null,
                cliente_nombre: "",
                fecha_inicio: "",
                fecha_fin: "",
            },
            realizados_params: {
                programado_b: 0,
                page: 1,
                verPaginado: true,
                total: 0,
                actual: 1,
            },
            programados_params: {
                programado_b: 1,
                page: 1,
                verPaginado: true,
                total: 0,
                actual: 1,
            },
        };
    },
    methods: {
        //Registrar Seguimientos Methods
        registrarSeguimiento(tipo = "", datos_seguimiento = null) {
            this.tipoFormSeguimiento = tipo;
            this.filtersSeguimiento = {
                cliente_id: datos_seguimiento.clientes_id,
                tipo_cliente_id: datos_seguimiento.tipo_cliente_id,
                operacion_id: datos_seguimiento.operaciones_id,
                seguimiento_id: datos_seguimiento.id,
            };
            this.ShowFormRegistrarSeguimientos = true;
        },

        programarSeguimiento(tipo = "", datos_seguimiento = null) {
            this.tipoFormSeguimiento = tipo;
            this.filtersSeguimiento = {
                cliente_id: datos_seguimiento.clientes_id,
                tipo_cliente_id: datos_seguimiento.tipo_cliente_id,
                operacion_id: datos_seguimiento.operaciones_id,
                seguimiento_id: datos_seguimiento.id,
            };
            this.ShowFormProgramarSeguimientos = true;
        },
        agregar_modificar_success_seguimiento() {
            //success after insert or update ()
            this.ShowFormProgramarSeguimientos = false;
            this.ShowFormRegistrarSeguimientos = false;
            this.fetchDataSeguimientos();
        },
        onFilterChange() {
            this.serverOptions.page = 1; // reset page
            this._fetchDataSeguimientos(); // debounced
        },
        // Enter key triggers immediate fetch
        async onEnterPress(field) {
            const value = this.serverOptions[field].trim();
            this.realizados_params.page = 1;
            this.programados_params.page = 1;
            await this._fetchDataSeguimientos();
            // Update previous value
            this.previousServerOptions[field] = value;
        },
        // Blur triggers fetch only if value has changed
        onBlurFetch(field) {
            const value = this.serverOptions[field].trim();
            if (value !== this.previousServerOptions[field]) {
                this.realizados_params.page = 1;
                this.programados_params.page = 1;
                this.fetchDataSeguimientos();
                this.previousServerOptions[field] = value; // update tracker
            }
        },
        async _fetchDataSeguimientos(tipo_seguimiento = "") {
            if (this.isLoading) {
                this.$error(
                    "Validation failed. API call skipped due to loading."
                );
                return; // ✅ Prevents multiple calls while loading
            }
            this.isLoading = true;
            this.$vs.loading();
            try {
                this.serverOptions.empresa_operaciones_id =
                    this.empresa_operacion.value;
                //
                //
                // Call the API from clientes service
                if (
                    ((this.tipo_seguimiento.value === "" ||
                        this.tipo_seguimiento.value === "0") && tipo_seguimiento === "") || (tipo_seguimiento === "realizados")
                ) {
                    if (this.tipo_seguimiento.value === "0" || tipo_seguimiento === "realizados") {
                        //this.ProgramadosList = [];
                        this.realizados_params.verPaginado = false;
                    }
                    //solo los realizados
                    //Seguimientos Realizados
                    const realizados = await seguimientos.getSeguimientos({
                        ...this.serverOptions,
                        ...this.realizados_params,
                    });
                    this.SeguimientosList = realizados.data; // assuming API returns
                    this.realizados_params.total = realizados.last_page;
                    this.realizados_params.page = realizados.current_page;
                }

                if (
                    ((this.tipo_seguimiento.value === "" ||
                        this.tipo_seguimiento.value === "1") && tipo_seguimiento === "") || (tipo_seguimiento === "programados")
                ) {
                    if (this.tipo_seguimiento.value === "1" || tipo_seguimiento === "programados") {
                        //this.SeguimientosList = [];
                        this.programados_params.verPaginado = false;
                    }
                    //solo los programados
                    //Seguimientos Programados
                    const programados = await seguimientos.getSeguimientos({
                        ...this.serverOptions,
                        ...this.programados_params,
                    });
                    this.ProgramadosList = programados.data; // assuming API returns
                    this.programados_params.total = programados.last_page;
                    this.programados_params.page = programados.current_page;
                }
            } catch (error) {
                this.$error("🚀 ~ error:", error);
            } finally {
                this.isLoading = false;
                this.programados_params.verPaginado = true;
                this.realizados_params.verPaginado = true;
                this.$vs.loading.close();
            }
        },
        onCloseDate(selectedDates, dateStr, instance) {
            /**se valdiad que se busque la informacion solo en los casos donde la fechas cambien */
            if (selectedDates.length == 0) {
                /**no hay fechas que buscar */
                this.serverOptions.fecha_inicio = "";
                this.serverOptions.fecha_fin = "";
            } else if (selectedDates.length == 1) {
                this.serverOptions.fecha_inicio = moment(
                    selectedDates[0]
                ).format("YYYY-MM-DD");
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
                    this.serverOptions.fecha_inicio = moment(
                        selectedDates[0]
                    ).format("YYYY-MM-DD");
                    this.serverOptions.fecha_fin = moment(
                        selectedDates[1]
                    ).format("YYYY-MM-DD");
                }
            }
            //aqui mando llamar los nuevos datos
            if ((this.serverOptions.fecha_inicio !== this.previousServerOptions.fecha_inicio) || (this.serverOptions.fecha_fin !== this.previousServerOptions.fecha_fin)) {
                this.realizados_params.page = 1;
                this.programados_params.page = 1;
                this.previousServerOptions.fecha_inicio = this.serverOptions.fecha_inicio;
                this.previousServerOptions.fecha_fin = this.serverOptions.fecha_fin;
                this.fetchDataSeguimientos();
            }
        },
        OpenFormSeguimientos() {
            this.verFormSeguimientos = true;
        },
        reloadList() {
            this.fetchDataSeguimientos();
            this.verFormSeguimientos = false;
        },
        resetData() {
            // Reset all fields to their default values
            this.empresa_operacion = this.empresaOperaciones[0];
            this.tipo_seguimiento = this.tipoSeguimientos[0];
            this.rango_fechas = [];
            this.serverOptions.cliente_nombre = "";
            this.serverOptions.fecha_inicio = "";
            this.serverOptions.fecha_fin = "";
            this.serverOptions.empresa_operaciones_id = "";
            this.realizados_params.page = 1;
            this.programados_params.page = 1;
        },
        reset(card) {
            card.removeRefreshAnimation(500);
            // Optionally, fetch all clients again
            this.resetData();
            this.fetchDataSeguimientos();
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
        this.fetchDataSeguimientos = debounce(this._fetchDataSeguimientos, 400);
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
        this.empresa_operacion = this.empresaOperaciones[0];
        this.tipo_seguimiento = this.tipoSeguimientos[0];

        this.fetchDataSeguimientos();

        if (this.$isDev)
            this.OpenFormSeguimientos();
    },
    beforeDestroy() {
        this.$log("Before Component destroyed! " + this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
