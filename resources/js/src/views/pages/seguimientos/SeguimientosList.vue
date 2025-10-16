<template>
    <div class="">
        <div class="text-right buttons-container-header">
            <vs-button class="w-full md:w-auto md:ml-2 md:mt-0" color="success" @click="OpenFormSeguimientos()">
                <span>Registrar Seguimiento</span>
            </vs-button>
        </div>
        <div class="mt-5 vx-col w-full">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-full md:w-4/12 lg:w-6/12 xl:w-2/12 px-2 input-text">
                        <label class="">Tipo de Seguimiento</label>
                        <v-select :options="tipoSeguimientos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="tipo_seguimiento" class="w-full" />
                    </div>
                    <div class="w-full sm:w-full md:w-4/12 lg:w-6/12 xl:w-3/12 px-2 input-text">
                        <label class="">Tipo de Operación</label>
                        <v-select :options="empresaOperaciones" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="empresa_operacion" class="w-full" />
                    </div>
                    <div class="w-full sm:w-full md:w-4/12 lg:w-6/12 xl:w-3/12 input-text px-2">
                        <label class="">
                            Filtrar por Fechas
                            <span>(*)</span>
                        </label>
                        <flat-pickr name="rango_fechas" data-vv-as=" " :config="configdateTimePickerRange"
                            v-model="rango_fechas" placeholder="Rango de fechas" class="w-full"
                            @on-close="onCloseDate" />
                    </div>
                    <div class="w-full sm:w-full md:w-full lg:w-6/12 xl:w-4/12 input-text px-2">
                        <label class="">Nombre del Cliente</label>
                        <vs-input class="w-full" icon="search" placeholder="Filtrar por Nombre del Cliente"
                            v-model="serverOptions.cliente" maxlength="75" />
                    </div>
                </div>
            </vx-card>
        </div>
        <div id="results-area" class="mt-5">
            <div class="flex items-center justify-center hidden">
                <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
            </div>
            <div id="realizados">
                <vs-table :sst="true" :max-items="serverOptions.per_page" :data="SeguimientosList"
                    noDataText="0 Resultados" class="tabla-datos">
                    <template slot="header">
                        <h3>Seguimientos Atendidos</h3>
                    </template>
                    <template slot="thead">
                        <vs-th>Núm. Cotización</vs-th>
                        <vs-th>Cliente</vs-th>
                        <vs-th>Tel. / Celular</vs-th>
                        <vs-th>Fecha Elaboración</vs-th>
                        <vs-th>Fecha de Vencimiento</vs-th>
                        <vs-th>Tipo</vs-th>
                        <vs-th>Estatus</vs-th>
                        <vs-th>Acciones</vs-th>
                    </template>
                    <template slot-scope="{ data }">
                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                            <vs-td :data="data[indextr].id">
                                <span class="font-semibold">{{
                                    data[indextr].id
                                    }}</span>
                            </vs-td>
                            <vs-td :data="data[indextr].cliente_nombre">
                                {{ data[indextr].cliente_nombre }}
                            </vs-td>
                            <vs-td :data="data[indextr].cliente_telefono">
                                <span class="font-medium" v-if="data[indextr].cliente_telefono">
                                    {{ data[indextr].cliente_telefono }}
                                </span>
                                <span class="font-medium" v-else>
                                    N/A
                                </span>
                            </vs-td>
                            <vs-td :data="data[indextr].fecha_texto">
                                <span class="font-medium">
                                    {{ data[indextr].fecha_texto }}
                                </span>
                            </vs-td>
                            <vs-td :data="data[indextr].fecha_vencimiento_texto">
                                <span class="font-medium">
                                    {{ data[indextr].fecha_vencimiento_texto }}
                                </span>
                            </vs-td>
                            <vs-td :data="data[indextr].tipo_texto">
                                <span class="font-medium">

                                    {{ data[indextr].tipo_texto }}
                                </span>
                            </vs-td>
                            <vs-td>
                                <p v-if="data[indextr].status == 0">
                                    {{ data[indextr].status_texto }}
                                    <span class="dot-danger"></span>
                                </p>
                                <p v-else-if="data[indextr].status == 1">
                                    {{ data[indextr].status_texto }}
                                    <span class="dot-warning"></span>
                                </p>
                                <p v-else-if="data[indextr].status == 2">
                                    {{ data[indextr].status_texto }}
                                    <span class="dot-success"></span>
                                </p>
                                <p v-else-if="data[indextr].status == 3">
                                    {{ data[indextr].status_texto }}
                                    <span class="dot-danger"></span>
                                </p>
                            </vs-td>
                            <vs-td :data="data[indextr].id">
                                <div class="flex justify-center">
                                    <img class="cursor-pointer img-btn-20 mx-3" src="@assets/images/pdf.svg"
                                        title="Expediente" @click="openReporte(data[indextr])" />
                                    <img class=" img-btn-22 mx-3" src="@assets/images/edit.svg"
                                        title="Modificar Cotización" @click="openModificar(data[indextr])" />
                                    <img v-if="data[indextr].status > 0" class="img-btn-22 mx-3"
                                        src="@assets/images/trash.svg" title="Cancelar Cotización"
                                        @click="cancelarCotizacion(data[indextr])" />
                                    <img v-else-if="data[indextr].status == 0" class="img-btn-22 mx-3"
                                        src="@assets/images/trash-open.svg" title="Esta cotización ya fue cancelada."
                                        @click="openReporte(data[indextr])" />
                                </div>
                            </vs-td>
                            <template class="expand-user" slot="expand"></template>
                        </vs-tr>
                    </template>
                </vs-table>
                <div>
                    <vs-pagination v-if="serverOptions.realizados.verPaginado" :total="serverOptions.realizados.total"
                        v-model="serverOptions.realizados.actual" class="mt-8"></vs-pagination>
                </div>
            </div>
        </div>
        <FormularioSeguimientos v-if="verFormSeguimientos" :show="verFormSeguimientos" @closeVentana="reloadList">
        </FormularioSeguimientos>
    </div>
</template>
<script>
import FormularioSeguimientos from "@pages/seguimientos/FormularioSeguimientos";
import vSelect from "vue-select";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import { configdateTimePickerRange } from "@/VariablesGlobales";
const moment = require("moment");
export default {
    components: {
        FormularioSeguimientos,
        "v-select": vSelect,
        flatPickr,
    },
    computed: {
    },
    watch: {},
    data() {
        return {
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
                }
            ],
            empresaOperaciones: [
                {
                    label: "Todas",
                    value: "",
                },
                {
                    label: "Vigentes",
                    value: "1",
                },
                {
                    label: "Canceladas",
                    value: "2",
                },
                {
                    label: "Vencidas",
                    value: "3",
                }
            ],
            empresa_operacion: {},
            tipo_seguimiento: {},
            //results
            ProgramadosList: [],
            SeguimientosList: [],
            serverOptions: {
                per_page: 15,
                cliente: "",
                fecha_inicio: "",
                fecha_fin: "",
                empresa_operaciones_id: null,
                realizados: {
                    page: "",
                    verPaginado: true,
                    total: 0,
                    actual: 1,
                },
                programados: {
                    page: "",
                    verPaginado: true,
                    total: 0,
                    actual: 1,
                }
            },
        };
    },
    methods: {
        onCloseDate(selectedDates, dateStr, instance) {
            /**se valdiad que se busque la informacion solo en los casos donde la fechas cambien */
            if (selectedDates.length == 0) {
                /**no hay fechas que buscar */
                this.serverOptions.fecha_inicio = "";
                this.serverOptions.fecha_fin = "";
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
            //aqui mando llamar los nuevos datos
            /* (async () => {
                 //await this.get_data(this.actual);
             })();
             */
        },
        OpenFormSeguimientos() {
            this.verFormSeguimientos = true;
        },
        reloadList() {
            this.verFormSeguimientos = false;
        },
        reset(card) {
            card.removeRefreshAnimation(500);
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
        this.empresa_operacion = this.empresaOperaciones[0];
        this.tipo_seguimiento = this.tipoSeguimientos[0];
        //this.OpenFormSeguimientos();
    },
    beforeDestroy() {
        this.$log("Before Component destroyed! " + this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>