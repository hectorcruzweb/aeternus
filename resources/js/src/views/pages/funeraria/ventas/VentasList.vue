<template>
    <div>
        <div class="w-full text-right">
            <vs-button
                class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                color="primary"
                @click="openPlanesVenta = true"
                type="border"
            >
                <span>Planes de Venta</span>
            </vs-button>
            <vs-button
                class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                color="success"
                @click="formulario('agregar')"
            >
                <span>Vender Planes Funerarios a Futuro</span>
            </vs-button>
        </div>

        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card
                no-radius
                title="Filtros de selección"
                refresh-content-action
                @refresh="reset"
                :collapse-action="false"
            >
                <div class="flex flex-wrap">
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label class="text-sm opacity-75">Mostrar</label>
                        <v-select
                            :options="mostrarOptions"
                            :clearable="false"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="mostrar"
                            class="w-full"
                        />
                    </div>
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label class="text-sm opacity-75">Estado</label>
                        <v-select
                            :options="estadosOptions"
                            :clearable="false"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="estado"
                            class="w-full"
                        />
                    </div>
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label class="">Filtrar Específico</label>
                        <v-select
                            :options="filtrosEspecificos"
                            :clearable="false"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="filtroEspecifico"
                            class="w-full"
                        />
                    </div>
                    <div class="w-full xl:w-3/12 px-2 input-text">
                        <label class="">Número de Control</label>
                        <vs-input
                            class="w-full"
                            icon="search"
                            maxlength="14"
                            placeholder="Filtrar por Número de Control"
                            v-model="serverOptions.numero_control"
                            v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <div class="w-full px-2 py-4">
                        <h3 class="text-base">
                            <feather-icon
                                icon="UserIcon"
                                class="mr-2"
                                svgClasses="w-5 h-5"
                            />Filtrar por Nombre del Titular
                        </h3>
                    </div>
                    <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text"
                    >
                        <label class="">Nombre del Titular</label>
                        <vs-input
                            class="w-full"
                            icon="search"
                            placeholder="Filtrar por Nombre del Titular"
                            v-model="serverOptions.titular"
                            v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')"
                            maxlength="75"
                        />
                    </div>
                </div>
            </vx-card>
        </div>

        <br />
        <vs-table
            :sst="true"
            :max-items="serverOptions.per_page.value"
            :data="ventas"
            noDataText="0 Resultados"
            class="tabla-datos"
        >
            <template slot="header">
                <h3>Listado de Ventas de Planes Funerarios a Futuro</h3>
            </template>
            <template slot="thead">
                <vs-th>Núm. Venta</vs-th>
                <vs-th>Titular</vs-th>
                <vs-th>Uso Venta</vs-th>
                <vs-th>Solicitud</vs-th>
                <vs-th>Convenio</vs-th>
                <vs-th>Plan Funerario</vs-th>
                <vs-th>Convenio</vs-th>
                <vs-th>$ Saldo</vs-th>
                <vs-th>Estatus</vs-th>
                <vs-th>Usado</vs-th>
                <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="data[indextr].ventas_planes_id">
                        <span class="font-semibold">{{
                            data[indextr].ventas_planes_id
                        }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].nombre">{{
                        data[indextr].nombre
                    }}</vs-td>
                    <vs-td
                        :data="
                            data[indextr].venta_plan.tipo_financiamiento_texto
                        "
                        >{{
                            data[indextr].venta_plan.tipo_financiamiento_texto
                        }}</vs-td
                    >
                    <vs-td :data="data[indextr].numero_solicitud">
                        <span class="font-medium">{{
                            data[indextr].numero_solicitud_texto
                        }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].numero_convenio">
                        <span class="font-medium">{{
                            data[indextr].numero_convenio
                        }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].venta_plan.nombre_original">
                        <span class="capitalize">
                            {{ data[indextr].venta_plan.nombre_original }}
                        </span>
                    </vs-td>

                    <!--Convenio-->
                    <vs-td :data="data[indextr].venta_plan.status_convenio">
                        <p v-if="data[indextr].venta_plan.status_convenio == 0">
                            <img
                                class="cursor-pointer img-btn-20"
                                src="@assets/images/convenio-no.svg"
                                title="Convenio no Entregado"
                                @click="verEntregarConvenio(data[indextr])"
                            />
                        </p>
                        <p v-else>
                            <img
                                class="cursor-pointer img-btn-20"
                                src="@assets/images/convenio-si.svg"
                                title="Convenio Entregado"
                                @click="verEntregarConvenio(data[indextr])"
                            />
                        </p>
                    </vs-td>
                    <vs-td :data="data[indextr].saldo">
                        $ {{ data[indextr].saldo | numFormat("0,000.00") }}
                    </vs-td>
                    <vs-td :data="data[indextr].operacion_status">
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
                    <vs-td :data="data[indextr].operacion_status">
                        <p v-if="data[indextr].status_usado_b == 1">
                            {{ data[indextr].status_usado_texto }}
                            <span class="dot-warning"></span>
                        </p>
                        <p v-else-if="data[indextr].status_usado_b == 0">
                            {{ data[indextr].status_usado_texto }}
                            <span class="dot-success"></span>
                        </p>
                    </vs-td>
                    <vs-td :data="data[indextr].id">
                        <div class="flex justify-center">
                            <img
                                class="img-btn-24 mx-6"
                                src="@assets/images/seguimientos.svg"
                                title="Control de Seguimientos"
                                @click="OpenFormSeguimientos(tr)"
                            />
                            <img
                                v-if="data[indextr].nota"
                                class="cursor-pointer img-btn-20 mr-6"
                                src="@assets/images/notepad_ver.svg"
                                title="Notas"
                                @click="
                                    verNota(
                                        data[indextr].nota.trim(),
                                        data[indextr].venta_plan
                                            .nombre_original +
                                            '/ ' +
                                            data[indextr].nombre
                                    )
                                "
                            />
                            <img
                                v-else
                                class="cursor-pointer img-btn-20 mr-6"
                                src="@assets/images/notepad_ver_no.svg"
                                title="Notas"
                            />
                            <img
                                class="cursor-pointer img-btn-20 mx-3"
                                src="@assets/images/folder.svg"
                                title="Expediente"
                                @click="
                                    ConsultarVenta(
                                        data[indextr].ventas_planes_id
                                    )
                                "
                            />
                            <img
                                class="img-btn-18 mx-3"
                                src="@assets/images/edit.svg"
                                title="Modificar Contrato"
                                @click="
                                    openModificar(
                                        data[indextr].ventas_planes_id
                                    )
                                "
                            />
                            <img
                                v-if="data[indextr].operacion_status >= 1"
                                class="img-btn-22 mx-3"
                                src="@assets/images/trash.svg"
                                title="Cancelar Contrato"
                                @click="
                                    cancelarVenta(
                                        data[indextr].ventas_planes_id
                                    )
                                "
                            />
                            <img
                                v-else
                                class="img-btn-22 mx-3"
                                src="@assets/images/trash-open.svg"
                                title="Esta venta ya fue cancelada, puede hacer click aquí para consultar"
                                @click="
                                    ConsultarVentaAcuse(
                                        data[indextr].ventas_planes_id
                                    )
                                "
                            />
                        </div>
                    </vs-td>

                    <template class="expand-user" slot="expand"></template>
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

        <FormularioVentas
            :id_venta="id_venta_modificar"
            :tipo="tipoFormulario"
            :show="verFormularioVentas"
            @closeVentana="verFormularioVentas = false"
            @ver_pdfs_nueva_venta="ConsultarVenta"
        ></FormularioVentas>

        <Password
            :show="openStatus"
            :callback-on-success="callback"
            @closeVerificar="closeStatus"
            :accion="accionNombre"
        >
        </Password>

        <ReportesVentas
            :verAcuse="verAcuse"
            :show="openReportes"
            @closeListaReportes="closeListaReportes"
            :id_venta="id_venta"
        >
        </ReportesVentas>

        <CancelarVenta
            :show="openCancelar"
            @closeCancelarVenta="openCancelar = false"
            @ConsultarVenta="ConsultarVenta"
            :id_venta="id_venta"
        ></CancelarVenta>

        <PlanesVenta
            :show="openPlanesVenta"
            @closePlanesFuneraria="openPlanesVenta = false"
        ></PlanesVenta>

        <VerNotas
            :show="openVerNotas"
            :nota="nota_contenido"
            :title="titulo_nota"
            @closeVerNotas="openVerNotas = false"
        >
        </VerNotas>
        <Entregarconvenio
            :show="openEntregarconvenio"
            :datos="datosConvenio"
            @closeEntregarConvenio="closeEntregarConvenio"
        >
        </Entregarconvenio>
        <FormularioSeguimientos
            v-if="openSeguimientos"
            :show="openSeguimientos"
            :filters="filtersSeguimientos"
            @closeVentana="openSeguimientos = false"
        >
        </FormularioSeguimientos>
    </div>
</template>

<script>
//planes de venta
import FormularioSeguimientos from "../../seguimientos/FormularioSeguimientos.vue";
import planes from "@services/planes";
import Entregarconvenio from "@pages/EntregarConvenio";
import FormularioVentas from "../ventas/FormularioVentas";
import ReportesVentas from "../ventas/ReportesVentas";
import CancelarVenta from "../ventas/CancelarVenta";
import VerNotas from "@pages/VerNotas";
//componente de password
import Password from "@pages/confirmar_password";
/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";
import PlanesVenta from "@pages/funeraria/ventas/PlanesVentas";
export default {
    components: {
        "v-select": vSelect,
        Password,
        FormularioVentas,
        ReportesVentas,
        CancelarVenta,
        PlanesVenta,
        VerNotas,
        Entregarconvenio,
        FormularioSeguimientos,
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
            openSeguimientos: false,
            filtersSeguimientos: {
                cliente_id: null,
                tipo_cliente_id: null,
                operacion_id: null,
                origen: 2, //Cliente
            },
            openVerNotas: false,
            nota_contenido: "",
            titulo_nota: "",
            verAcuse: false,
            openPlanesVenta: false,
            openCancelar: false,
            openReportes: false,
            verFormularioVentas: false,
            tipoFormulario: "",
            openEntregarconvenio: false,
            datosConvenio: {
                tipo: "plan_funerario",
                id: "",
                titular: "",
                status: "",
                producto: "",
                fecha_entrega: "",
                entregado_por: "",
                nota: "",
            },
            //variable
            tipo_propiedades: [],
            propiedad: { label: "Todos", value: "" },
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
                    label: "Canceladas",
                    value: "0",
                },
            ],
            filtroEspecifico: { label: "Núm. Solicitud", value: "1" },
            filtrosEspecificos: [
                {
                    label: "Núm. Solicitud",
                    value: "1",
                },
                {
                    label: "Núm. Convenio",
                    value: "2",
                },
                {
                    label: "Núm. Venta",
                    value: "3",
                },
            ],
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                filtro_especifico_opcion: "",
                numero_control: "",
                titular: "",
            },
            activeTab: 0,
            verPaginado: true,
            total: 0,
            actual: 1,
            ventas: [],
            //fin variables
            openStatus: false,
            callback: Function,
            accionNombre: "",
            verAgregar: false,
            verModificar: false,
            id_venta_modificar: 0,
            /**opciones para filtrar la peticion del server */
            id_venta: 0 /**para consultar los reportesw */,
        };
    },
    methods: {
        OpenFormSeguimientos(venta) {
            this.$log("🚀 ~ venta:", venta);
            this.filtersSeguimientos.cliente_id = venta.cliente_id;
            this.filtersSeguimientos.tipo_cliente_id = 1;
            this.filtersSeguimientos.operacion_id = venta.operacion_id;
            this.filtersSeguimientos.origen = 2; //clientes
            this.openSeguimientos = true;
        },
        verEntregarConvenio(datos) {
            this.datosConvenio.id = datos.ventas_planes_id;
            this.datosConvenio.titular = datos.nombre;
            this.datosConvenio.nota = datos.nombre;
            this.datosConvenio.status = datos.venta_plan.status_convenio;
            this.datosConvenio.producto = datos.venta_plan.nombre_original;
            this.datosConvenio.fecha_entrega =
                datos.venta_plan.fecha_convenio_entrega_texto;
            this.datosConvenio.entregado_por =
                datos.venta_plan.entrego_convenio != null
                    ? datos.venta_plan.entrego_convenio.nombre
                    : "";
            this.datosConvenio.nota = datos.venta_plan.nota_convenio;
            this.openEntregarconvenio = true;
        },
        closeEntregarConvenio() {
            this.openEntregarconvenio = false;
            (async () => {
                await this.get_data(this.actual);
            })();
        },
        verNota(nota, title) {
            this.openVerNotas = true;
            this.nota_contenido = nota;
            this.titulo_nota = title;
        },
        reset(card) {
            card.removeRefreshAnimation(500);
            this.filtroEspecifico = { label: "Núm. Solicitud", value: "1" };
            this.serverOptions.numero_control = "";
            this.mostrar = { label: "15", value: "15" };
            this.estado = { label: "Todas", value: "" };
            this.serverOptions.titular = "";
            //this.get_data(this.actual);
        },
        async get_data(page, evento = "") {
            if (evento == "blur") {
                if (
                    this.serverOptions.titular != "" ||
                    this.serverOptions.titular == ""
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
            if (planes.cancel) {
                planes.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.verPaginado = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = this.mostrar.value;
            this.serverOptions.status = this.estado.value;
            this.serverOptions.filtro_especifico_opcion =
                this.filtroEspecifico.value;

            try {
                let res = await planes.get_ventas(this.serverOptions, true);
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

        //eliminar usuario logicamente

        closeModificar() {
            this.verModificar = false;
        },

        closeStatus() {
            this.openStatus = false;
        },

        ConsultarVenta(id_venta) {
            this.id_venta = id_venta;
            this.openReportes = true;
        },
        ConsultarVentaAcuse(id_venta) {
            this.verAcuse = true;
            this.id_venta = id_venta;
            this.openReportes = true;
        },

        openModificar(id_venta) {
            this.tipoFormulario = "modificar";
            this.id_venta_modificar = id_venta;
            this.verFormularioVentas = true;
        },

        cancelarVenta(id_venta) {
            this.id_venta = id_venta;
            this.openCancelar = true;
        },
        formulario(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioVentas = true;
        },

        closeListaReportes() {
            this.openReportes = false;
            this.verAcuse = false;
            this.id_venta = 0;
            (async () => {
                await this.get_data(this.actual);
            })();
        },
        closeCancelarVentaRefrescar() {
            this.openCancelar = false;
            (async () => {
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
