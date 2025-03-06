<template>
    <div>
        <div class="text-right buttons-container-header">
            <vs-button class="w-full md:w-auto   md:ml-2 md:mt-0" color="success"
                @click="OpenFormularioCotizaciones('agregar')">
                <span>Hacer Cotización</span>
            </vs-button>
        </div>
        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-6/12 lg:w-3/12 px-2 input-text">
                        <label class="">Mostrar</label>
                        <v-select :options="mostrarOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="mostrar" class="w-full" />
                    </div>
                    <div class="w-full sm:w-6/12 lg:w-3/12 px-2 input-text">
                        <label class="">Estado</label>
                        <v-select :options="estadosOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="estado" class="w-full" />
                    </div>
                    <div class="w-full sm:w-6/12 lg:w-3/12 input-text px-2">
                        <label class="">
                            Rango de Fechas año/mes/dia
                            <span>(*)</span>
                        </label>
                        <flat-pickr name="rango_fechas" data-vv-as=" " :config="configdateTimePickerRange"
                            v-model="rango_fechas" placeholder="Rango de fechas" class="w-full"
                            @on-close="onCloseDate" />
                    </div>
                    <div class="w-full sm:w-6/12 lg:w-3/12 input-text px-2">
                        <label class="">Número de Control</label>
                        <vs-input class="w-full" icon="search" maxlength="14"
                            placeholder="Filtrar por Número de Control" v-model="serverOptions.numero_control"
                            v-on:keyup.enter="get_data(1)" v-on:blur="get_data(1, 'blur')" />
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <div class="w-full px-2">
                        <h3 class="text-base font-semibold my-3">
                            <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />Filtrar por Nombre del
                            Cliente
                        </h3>
                    </div>
                    <div class="w-full input-text px-2">
                        <label class="">Nombre del Cliente</label>
                        <vs-input class="w-full" icon="search" placeholder="Filtrar por Nombre del Cliente"
                            v-model="serverOptions.cliente" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" maxlength="75" />
                    </div>
                </div>
            </vx-card>
        </div>
        <br>
        <vs-table :sst="true" :max-items="serverOptions.per_page.value" :data="cotizaciones" noDataText="0 Resultados"
            class="tabla-datos">
            <template slot="header">
                <h3>Listado de Cotizaciones</h3>
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
                            <img class="cursor-pointer img-btn-20 mx-3" src="@assets/images/pdf.svg" title="Expediente"
                                @click="openReporte(data[indextr])" />
                            <img class=" img-btn-22 mx-3" src="@assets/images/edit.svg" title="Modificar Cotización"
                                @click="openModificar(data[indextr])" />
                            <img v-if="data[indextr].status > 0" class="img-btn-22 mx-3" src="@assets/images/trash.svg"
                                title="Cancelar Cotización" @click="cancelarCotizacion(data[indextr])" />
                            <img v-else-if="data[indextr].status == 0" class="img-btn-22 mx-3"
                                src="@assets/images/trash-open.svg" title="Esta cotización ya fue cancelada." />
                        </div>
                    </vs-td>
                    <template class="expand-user" slot="expand"></template>
                </vs-tr>
            </template>
        </vs-table>
        <div>
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
        </div>
        <!--Componentes-->
        <FormularioCotizaciones :id_cotizacion="id_cotizacion" :tipo="tipoFormulario" :show="verFormularioCotizaciones"
            @closeVentana="reloadList">
        </FormularioCotizaciones>
        <Cancelar :datos="datos_cancelar" :show="verCancelar"
            @closeCancelarCotizacion="() => { this.verCancelar = false; this.reloadList(); }">
        </Cancelar>
        <Reporteador :header="'consultar cotizaciones'" :show="openReportesLista" :listadereportes="ListaReportes"
            :request="request" @closeReportes="openReportesLista = false">
        </Reporteador>
    </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import FormularioCotizaciones from "@pages/cotizaciones/FormularioCotizaciones";
import Cancelar from "@pages/cotizaciones/Cancelar";
import { mostrarOptions, configdateTimePickerRange } from "@/VariablesGlobales";
const moment = require("moment");
import cotizacionesService from "@services/cotizaciones";
import vSelect from "vue-select";
export default {
    components: {
        "v-select": vSelect,
        FormularioCotizaciones,
        flatPickr,
        Cancelar,
        Reporteador
    },
    watch: {
        actual: function (newValue, oldValue) {
            if (this.watchers)
                (async () => {
                    await this.get_data(this.actual);
                })();
        },
        mostrar: function (newValue, oldValue) {
            if (this.watchers)
                (async () => {
                    await this.get_data(1);
                })();
        },
        estado: function (newVal, previousVal) {
            if (this.watchers)
                (async () => {
                    await this.get_data(1);
                })();
        },
    },
    data() {
        return {
            openReportesLista: false,
            ListaReportes: [],
            request: {
                id_cotizacion: "",
                email: "",
                destinatario: "",
            },
            verCancelar: false,
            datos_cancelar: {},
            watchers: true,
            configdateTimePickerRange: configdateTimePickerRange,
            rango_fechas: [],
            estado: { label: "Todas", value: "" },
            estadosOptions: [
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
                    value: "0",
                },
                {
                    label: "Vencidas",
                    value: "3",
                }
            ],
            mostrarOptions: mostrarOptions,
            mostrar: { label: "15", value: "15" },
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                filtro_especifico_opcion: "",
                numero_control: "",
                cliente: "",
                fecha_inicio: "",
                fecha_fin: "",
            },
            verPaginado: true,
            total: 0,
            actual: 1,
            cotizaciones: [],
            tipoFormulario: "",
            verFormularioCotizaciones: false,
            id_cotizacion: ''
        }
    },
    methods: {
        openReporte(cotizacion = null) {
            this.ListaReportes = [];
            this.ListaReportes.push({
                nombre: "Cotización.",
                url: "/cotizaciones/get_pdf",
            });
            //estado de cuenta
            this.request.email = cotizacion.cliente_email;
            this.request.id_cotizacion = cotizacion.id;
            this.request.destinatario = cotizacion.cliente_nombre;
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        cancelarCotizacion(cotizacion) {
            this.datos_cancelar = cotizacion;
            this.verCancelar = true;
        },
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
            (async () => {
                await this.get_data(this.actual);
            })();
        },
        reset(card) {
            card.removeRefreshAnimation(500);
            this.watchers = false;
            //this.filtroEspecifico = { label: "Núm. Venta", value: "1" };
            this.rango_fechas = [];
            this.serverOptions.fecha_fin = "";
            this.serverOptions.fecha_inicio = "";
            this.serverOptions.numero_control = "";
            this.mostrar = this.mostrarOptions[0];
            this.estado = { label: "Todas", value: "" };
            this.serverOptions.cliente = "";
            (async () => {
                await this.get_data(1);
            })();
        },
        OpenFormularioCotizaciones(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioCotizaciones = true;
        },
        async get_data(page, evento = "") {
            if (evento == "blur") {
                if (
                    this.serverOptions.cliente != "" ||
                    this.serverOptions.cliente == ""
                ) {
                    //la funcion no avanza

                    return false;
                }
                if (
                    this.serverOptions.numero_control == "" ||
                    this.serverOptions.numero_control != ""
                ) {
                    //la funcion no avanza

                    return false;
                }
            }
            if (cotizacionesService.cancel) {
                cotizacionesService.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.verPaginado = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = this.mostrar.value;
            this.serverOptions.status = this.estado.value;
            //this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;
            try {
                let res = await cotizacionesService.get_cotizaciones(this.serverOptions, true);
                if (res.data.data) {
                    this.cotizaciones = res.data.data;
                    this.total = res.data.last_page;
                    this.actual = res.data.current_page;
                }
                this.verPaginado = true;
                this.$vs.loading.close();
            } catch (err) {
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
                            time: 4000,
                        });
                    }
                }
            } finally {
                this.watchers = true;
            }
        },
        reloadList() {
            (async () => {
                this.verFormularioCotizaciones = false;
                await this.get_data(this.actual);
            })();
        },
        openModificar(cotizacion) {
            this.tipoFormulario = "modificar";
            this.id_cotizacion = cotizacion.id;
            this.verFormularioCotizaciones = true;
        },
    },
    mounted(
    ) {
        //this.OpenFormularioCotizaciones("agregar");
    },
    created() {
        (async () => {
            await this.get_data(this.actual);
        })();
    },
};
</script>
