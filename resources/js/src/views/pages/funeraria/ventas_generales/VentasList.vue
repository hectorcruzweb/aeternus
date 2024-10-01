<template>
    <div>
        <div class="w-full text-right">
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                @click="OpenFormularioVentas('agregar')">
                <span>Registrar Venta</span>
            </vs-button>
        </div>

        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label class="">Mostrar</label>
                        <v-select :options="mostrarOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="mostrar" class="w-full" />
                    </div>
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label class="">Estado</label>
                        <v-select :options="estadosOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="estado" class="w-full" />
                    </div>
                    <div class="w-full sm:w-6/12 xl:w-3/12 input-text px-2">
                        <label class="">Filtrar Específico</label>
                        <v-select :options="filtrosEspecificos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="filtroEspecifico" class="w-full" />
                    </div>
                    <div class="w-full sm:w-6/12 xl:w-3/12 input-text px-2">
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
        <br />
        <vs-table :sst="true" :max-items="serverOptions.per_page.value" :data="ventas" noDataText="0 Resultados"
            class="tabla-datos">
            <template slot="header">
                <h3>Listado de Ventas en Gral.</h3>
            </template>
            <template slot="thead">
                <vs-th>Núm. Venta</vs-th>
                <vs-th>Cliente</vs-th>
                <vs-th>Fecha Venta</vs-th>
                <vs-th>$ Total Venta</vs-th>
                <vs-th>$ Saldo Venta</vs-th>
                <vs-th>Estatus Entrega</vs-th>
                <vs-th>Estatus Pago</vs-th>
                <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="data[indextr].ventas_generales_id">
                        <span class="font-semibold">{{
                            data[indextr].ventas_generales_id
                            }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].nombre">
                        {{ data[indextr].nombre }}
                    </vs-td>
                    <vs-td :data="data[indextr].fecha_operacion_texto">
                        <span class="font-medium">
                            {{ data[indextr].fecha_operacion_texto }}
                        </span>
                    </vs-td>
                    <vs-td :data="data[indextr].total">
                        <span class="font-medium">
                            $
                            {{ data[indextr].total | numFormat("0,000.00") }}
                        </span>
                    </vs-td>
                    <vs-td :data="data[indextr].saldo">
                        <span class="font-medium">
                            $
                            {{ data[indextr].saldo | numFormat("0,000.00") }}
                        </span>
                    </vs-td>
                    <vs-td :data="data[indextr].venta_general">
                        <p>
                            {{ data[indextr].venta_general.status_entregado_texto }}
                            <span :class="[
                                data[indextr].venta_general.entregado_b == 0
                                    ? 'dot-warning'
                                    : 'dot-success',
                            ]"></span>
                        </p>
                    </vs-td>
                    <vs-td>
                        <p v-if="data[indextr].status_texto == 'Cancelada'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-danger"></span>
                        </p>
                        <p v-else-if="data[indextr].status_texto == 'Por Pagar'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-warning"></span>
                        </p>
                        <p v-else-if="data[indextr].status_texto == 'Pagada'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-success"></span>
                        </p>
                    </vs-td>
                    <vs-td :data="data[indextr].id">
                        <div class="flex justify-center">
                            <img v-if="
                                data[indextr].operacion_status == 0
                            " class="img-btn-22 mx-3" src="@assets/images/deliver-disabled.svg"
                                title="Hacer Entrega de Venta" @click="openEntregarVenta(data[indextr])" />
                            <img v-if="data[indextr].operacion_status >= 1 && data[indextr].venta_general.entregado_b == 0"
                                class="img-btn-22 mx-3" src="@assets/images/deliver-yes.svg"
                                title="Hacer Entrega de Venta" @click="openEntregarVenta(data[indextr])" />
                            <img v-else class="img-btn-22 mx-3" src="@assets/images/deliver-no.svg"
                                title="Esta venta ya fue entregada." @click="openEntregarVenta(data[indextr])" />
                            <img class="cursor-pointer img-btn-20 mx-3" src="@assets/images/folder.svg"
                                title="Expediente" @click="ConsultarVenta(data[indextr].ventas_generales_id)" />
                            <img v-if="
                                data[indextr].operacion_status != 0 &&
                                data[indextr].venta_general.entregado_b == 0
                            " class="img-btn-22 mx-3" src="@assets/images/edit.svg" title="Modificar Venta"
                                @click="openModificar(data[indextr].ventas_generales_id)" /><img v-else
                                class="img-btn-22 mx-3" src="@assets/images/edit.svg" title="Modificar Venta" />
                            <img v-if="data[indextr].operacion_status >= 1" class="img-btn-22 mx-3"
                                src="@assets/images/trash.svg" title="Cancelar Venta"
                                @click="cancelarVenta(data[indextr].ventas_generales_id)" />
                            <img v-else class="img-btn-22 mx-3" src="@assets/images/trash-open.svg"
                                title="Esta venta ya fue cancelada, puede hacer click aquí para consultar"
                                @click="ConsultarVentaAcuse(data[indextr].ventas_generales_id)" />
                        </div>
                    </vs-td>
                    <template class="expand-user" slot="expand"></template>
                </vs-tr>
            </template>
        </vs-table>

        <div>
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
        </div>

        <Password :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus"
            :accion="accionNombre">
        </Password>

        <ReportesServicio :verAcuse="verAcuse" :show="openReportes" @closeListaReportes="closeListaReportes"
            :id_venta="id_venta"></ReportesServicio>

        <FormularioVentas :id_venta="id_venta" :tipo="tipoFormulario" :show="verFormularioVentas"
            @closeVentana="reloadList">
        </FormularioVentas>

        <CancelarVenta :show="openCancelar" @closeCancelarVenta="openCancelar = false" @ConsultarVenta="ConsultarVenta"
            :id_venta="id_venta"></CancelarVenta>
        <EntregarVenta :show="openEntregar" @closeEntregarVenta="closeEntregarVenta" :id_venta="id_venta">
        </EntregarVenta>
    </div>
</template>

<script>
import funeraria from "@services/funeraria";
import FormularioVentas from "@pages/funeraria/ventas_generales/FormularioVentas";
import ReportesServicio from "@pages/funeraria/ventas_generales/ReportesServicio";
//componente de password
import Password from "@pages/confirmar_password";
/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import CancelarVenta from "../ventas_generales/CancelarVenta";
import EntregarVenta from "../ventas_generales/EntregarVenta";
import vSelect from "vue-select";
export default {
    components: {
        "v-select": vSelect,
        Password,
        FormularioVentas,
        ReportesServicio,
        CancelarVenta,
        EntregarVenta
    },
    watch: {
        actual: function (newValue, oldValue) {
            (async () => {
                await this.get_data(this.actual);
            })();
        },
        mostrar: function (newValue, oldValue) {
            (async () => {
                await this.get_data(1);
            })();
        },
        estado: function (newVal, previousVal) {
            (async () => {
                await this.get_data(1);
            })();
        },
    },
    data() {
        return {
            openEntregar: false,
            openCancelar: false,
            openReportes: false,
            verFormularioVentas: false,
            tipoFormulario: "",
            //variable
            openReportesLista: false,
            mostrarOptions: mostrarOptions,
            mostrar: { label: "15", value: "15" },
            estado: { label: "Todas", value: "" },
            estadosOptions: [
                {
                    label: "Todas",
                    value: "",
                },
                {
                    label: "Por Pagar",
                    value: "1",
                },
                {
                    label: "Pagadas",
                    value: "2",
                },
                {
                    label: "Entregadas",
                    value: "3",
                },
                {
                    label: "Por Entregar",
                    value: "4",
                },
                {
                    label: "Activas",
                    value: "5",
                },
                {
                    label: "Canceladas",
                    value: "0",
                },
            ],
            filtroEspecifico: { label: "Núm. Venta", value: "1" },
            filtrosEspecificos: [
                {
                    label: "Núm. Venta",
                    value: "1",
                },
            ],
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                filtro_especifico_opcion: "",
                numero_control: "",
                cliente: "",
            },
            verPaginado: true,
            total: 0,
            actual: 1,
            ventas: [],
            //fin variables
            openStatus: false,
            callback: Function,
            accionNombre: "",
            /**opciones para filtrar la peticion del server */
            id_venta: 0 /**para consultar los reportesw */,
        };
    },
    methods: {
        reset(card) {
            card.removeRefreshAnimation(500);
            this.filtroEspecifico = { label: "Núm. Venta", value: "1" };
            this.serverOptions.numero_control = "";
            this.mostrar = { label: "15", value: "15" };
            this.estado = { label: "Todas", value: "" };
            this.serverOptions.cliente = "";
            //this.get_data(this.actual);
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
            let self = this;
            if (funeraria.cancel) {
                funeraria.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.verPaginado = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = this.mostrar.value;
            this.serverOptions.status = this.estado.value;
            this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;

            try {
                let res = await funeraria.get_ventas_gral(this.serverOptions, true);
                if (res.data.data) {
                    this.ventas = res.data.data;
                    this.total = res.data.last_page;
                    this.actual = res.data.current_page;
                }
                this.verPaginado = true;
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
        openEntregarVenta(venta) {
            this.id_venta = venta.ventas_generales_id;
            this.openEntregar = true;
        },
        openModificar(id_venta) {
            this.tipoFormulario = "modificar";
            this.id_venta = id_venta;
            this.verFormularioVentas = true;
        },

        closeStatus() {
            this.openStatus = false;
        },

        closeEntregarVenta() {
            this.openEntregar = false;
            this.id_venta = 0;
        },

        ConsultarVenta(id_venta) {
            this.id_venta = id_venta;
            this.openReportes = true;
        },

        OpenFormularioVentas(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioVentas = true;
        },

        cancelarVenta(id_venta) {
            this.id_venta = id_venta;
            this.openCancelar = true;
        },

        async cancelar_venta() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await inventario.cancelar_venta({
                    id_venta: this.id_venta,
                });
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Cancelar Venta",
                        text: "Se ha cancelado la venta correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    (async () => {
                        await this.get_data(this.actual);
                    })();
                    this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Cancelar Venta",
                        text: "Error al cancelar la venta, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 6000,
                    });
                }
                this.$vs.loading.close();
            } catch (err) {
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
                    } else if (err.response.status == 422) {
                        //checo si existe cada error
                        this.errores = err.response.data.error;
                        this.$vs.notify({
                            title: "Cancelar Venta",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        //este error es por alguna condicion que el contrano no cumple para modificar
                        this.$vs.notify({
                            title: "Cancelar Venta",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 8000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },

        closeListaReportes() {
            this.openReportes = false;
            this.verAcuse = false;
            this.id_venta = 0;
            (async () => {
                await this.get_data(this.actual);
            })();
        },

        reloadList() {
            (async () => {
                this.verFormularioVentas = false;
                await this.get_data(this.actual);
            })();
        },
    },

    created() {
        (async () => {
            await this.get_data(this.actual);
        })();
    },
};
</script>
