<template>
    <div>
        <div class="text-right">
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
                @click="openCuotasCementerio = true" type="border">
                <span>Cuotas / Cementerio</span>
            </vs-button>
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
                @click="openPlanesVenta = true" type="border">
                <span>Planes de Venta</span>
            </vs-button>
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                @click="formulario('agregar')">
                <span>Vender Propiedad</span>
            </vs-button>
        </div>
        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 mb-1 px-2 input-text">
                        <label class="">Mostrar</label>
                        <v-select :options="mostrarOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="mostrar" class="sm:mb-0" />
                    </div>
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 mb-1 px-2 input-text">
                        <label class="">Estado</label>
                        <v-select :options="estadosOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="estado" class="" />
                    </div>
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 mb-1 px-2 input-text">
                        <label class="">Filtrar Específico</label>
                        <v-select :options="filtrosEspecificos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="filtroEspecifico" class="" />
                    </div>
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 px-2 input-text">
                        <label class="">Número de Control</label>
                        <vs-input class="w-full" icon="search" maxlength="14"
                            placeholder="Filtrar por Número de Control" v-model="serverOptions.numero_control"
                            v-on:keyup.enter="get_data(1)" v-on:blur="get_data(1, 'blur')" />
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <div class="w-full px-2 py-4">
                        <h3 class="text-base">
                            <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />Filtrar por Nombre del
                            Titular
                        </h3>
                    </div>
                    <div class="w-full sm:w-6/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 input-text">
                        <label class="">Nombre del Titular</label>
                        <vs-input class="w-full" icon="search" placeholder="Filtrar por Nombre del Titular"
                            v-model="serverOptions.titular" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" maxlength="75" />
                    </div>
                    <div class="w-full sm:w-6/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 input-text">
                        <label class="">Nombre del Fallecido</label>
                        <vs-input class="w-full" icon="search" placeholder="Filtrar por Nombre del Fallecido"
                            v-model="serverOptions.fallecido" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" maxlength="75" />
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
                    <vs-table :sst="true" :max-items="serverOptions.per_page.value" :data="ventas"
                        noDataText="0 Resultados" class="tabla-datos">
                        <template slot="header">
                            <h3>Listado de Ventas Realizadas del Cementerio</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Núm. Venta</vs-th>
                            <vs-th>Titular</vs-th>
                            <vs-th>Uso Venta</vs-th>
                            <vs-th>Solicitud</vs-th>
                            <vs-th>Convenio</vs-th>
                            <vs-th>Título</vs-th>
                            <vs-th>Ubicacion</vs-th>
                            <vs-th>Convenio</vs-th>
                            <vs-th>$ Saldo Venta</vs-th>
                            <vs-th>Estatus</vs-th>
                            <vs-th>Acciones</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                <vs-td :data="data[indextr].ventas_terrenos_id">
                                    <span class="font-medium">{{
                                        data[indextr].ventas_terrenos_id
                                    }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].nombre">{{
                                    data[indextr].nombre
                                }}</vs-td>
                                <vs-td :data="data[indextr].venta_terreno
                                    .tipo_financiamiento_texto
                                    ">{{
                                        data[indextr].venta_terreno
                                            .tipo_financiamiento_texto
                                    }}</vs-td>
                                <vs-td :data="data[indextr].numero_solicitud">
                                    <span class="">{{
                                        data[indextr].numero_solicitud_texto
                                    }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].numero_convenio">
                                    <span class="">{{
                                        data[indextr].numero_convenio
                                    }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].numero_titulo">
                                    <span class="">{{
                                        data[indextr].numero_titulo_texto
                                    }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].ubicacion_texto">{{
                                    data[indextr].venta_terreno.ubicacion_texto
                                }}</vs-td>
                                <!--Convenio-->
                                <vs-td :data="data[indextr].venta_terreno.status_convenio">
                                    <p v-if="
                                        data[indextr].venta_terreno.status_convenio == 0
                                    ">
                                        <img class="cursor-pointer img-btn-20" src="@assets/images/convenio-no.svg"
                                            title="Convenio no Entregado" @click="verEntregarConvenio(data[indextr])" />
                                    </p>
                                    <p v-else>
                                        <img class="cursor-pointer img-btn-20" src="@assets/images/convenio-si.svg"
                                            title="Convenio Entregado" @click="verEntregarConvenio(data[indextr])" />
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

                                <vs-td :data="data[indextr].id">
                                    <div class="flex justify-center">
                                        <img class="img-btn-24 mx-6" src="@assets/images/seguimientos.svg"
                                            title="Control de Seguimientos" @click="OpenFormSeguimientos(tr)" />
                                        <img v-if="data[indextr].sepultados.length > 0"
                                            class="cursor-pointer img-btn-24 mr-6"
                                            src="@assets/images/conserviciosfunerarios.svg"
                                            title="Propiedad con servicios Funerarios"
                                            @click="ConsultarListaFallecidos(data[indextr])" />
                                        <img v-else class="cursor-pointer img-btn-24 mr-6"
                                            src="@assets/images/sinserviciosfunerarios.svg"
                                            title="Propiedad sin servicios Funerarios" />
                                        <img v-if="data[indextr].nota" class="cursor-pointer img-btn-20 mr-6"
                                            src="@assets/images/notepad_ver.svg" title="Notas" @click="
                                                verNota(
                                                    data[indextr].nota.trim(),
                                                    data[indextr].venta_terreno
                                                        .ubicacion_texto
                                                )
                                                " />
                                        <img v-else class="cursor-pointer img-btn-20 mr-6"
                                            src="@assets/images/notepad_ver_no.svg" title="Notas" />
                                        <img class="cursor-pointer img-btn-20 mx-3" src="@assets/images/folder.svg"
                                            title="Expediente" @click="
                                                ConsultarVenta(
                                                    data[indextr].ventas_terrenos_id
                                                )
                                                " />
                                        <img class="img-btn-18 mx-3" src="@assets/images/edit.svg"
                                            title="Modificar Contrato" @click="
                                                openModificar(
                                                    data[indextr].ventas_terrenos_id
                                                )
                                                " />
                                        <img v-if="data[indextr].operacion_status >= 1" class="img-btn-22 mx-3"
                                            src="@assets/images/trash.svg" title="Cancelar Contrato" @click="
                                                cancelarVenta(
                                                    data[indextr].ventas_terrenos_id
                                                )
                                                " />
                                        <img v-else class="img-btn-22 mx-3" src="@assets/images/trash-open.svg"
                                            title="Esta venta ya fue cancelada, puede hacer click aquí para consultar"
                                            @click="
                                                ConsultarVentaAcuse(
                                                    data[indextr].ventas_terrenos_id
                                                )
                                                " />
                                    </div>
                                </vs-td>
                                <template class="expand-user" slot="expand"></template>
                            </vs-tr>
                        </template>
                    </vs-table>

                    <div>
                        <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual"
                            class="mt-8"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>
        <FormularioVentas v-if="verFormularioVentas" :id_venta="id_venta_modificar" :tipo="tipoFormulario"
            :show="verFormularioVentas" @closeVentana="verFormularioVentas = false"
            @ver_pdfs_nueva_venta="ConsultarVenta">
        </FormularioVentas>
        <Password v-if="openStatus" :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus"
            :accion="accionNombre">
        </Password>
        <ReportesVentas v-if="openReportes" :verAcuse="verAcuse" :show="openReportes"
            @closeListaReportes="closeListaReportes" :id_venta="id_venta">
        </ReportesVentas>
        <FallecidosList v-if="openListaFallecidos" :show="openListaFallecidos"
            @closeListaFallecidos="closeListaFallecidos" :fallecidos="fallecidos">
        </FallecidosList>
        <CancelarVenta v-if="openCancelar" :show="openCancelar" @closeCancelarVenta="openCancelar = false"
            @ConsultarVenta="ConsultarVenta" :id_venta="id_venta"></CancelarVenta>
        <CuotasCementerio v-if="openCuotasCementerio" :show="openCuotasCementerio"
            @closeCuotasCementerio="openCuotasCementerio = false">
        </CuotasCementerio>
        <PlanesVenta v-if="openPlanesVenta" :show="openPlanesVenta" @closePlanesCementerio="openPlanesVenta = false">
        </PlanesVenta>
        <VerNotas v-if="openVerNotas" :show="openVerNotas" :nota="nota_contenido" :title="titulo_nota"
            @closeVerNotas="openVerNotas = false">
        </VerNotas>
        <Entregarconvenio v-if="openEntregarconvenio" :show="openEntregarconvenio" :datos="datosConvenio"
            @closeEntregarConvenio="closeEntregarConvenio">
        </Entregarconvenio>
        <FormularioSeguimientos v-if="openSeguimientos" :show="openSeguimientos" :filters="filtersSeguimientos"
            @closeVentana="openSeguimientos = false">
        </FormularioSeguimientos>
    </div>
</template>

<script>
//planes de venta
import cementerio from "@services/cementerio";
import FormularioSeguimientos from "../../seguimientos/FormularioSeguimientos.vue";
import FormularioVentas from "../ventas/FormularioVentas";
import ReportesVentas from "../ventas/ReportesVentas";
import FallecidosList from "../ventas/FallecidosList";
import CancelarVenta from "../ventas/CancelarVenta";
//componente de password
import Password from "@pages/confirmar_password";
import VerNotas from "@pages/VerNotas";
import Entregarconvenio from "@pages/EntregarConvenio";
/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";
import PlanesVenta from "@pages/cementerio/ventas/PlanesVentas";
import CuotasCementerio from "@pages/cementerio/ventas/cuotas/CuotasCementerio";
export default {
    name: "VentasListCementerio",
    components: {
        "v-select": vSelect,
        Password,
        FormularioVentas,
        ReportesVentas,
        CancelarVenta,
        PlanesVenta,
        CuotasCementerio,
        VerNotas,
        Entregarconvenio,
        FallecidosList,
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
    computed: {
        noDataFound() {
            return (
                this.ventas.length === 0
            );
        },
    },
    data() {
        return {
            openSeguimientos: false,
            filtersSeguimientos: {
                cliente_id: null,
                tipo_cliente_id: null,
                operacion_id: null,
                origen: 1, //Formulario de Origen. 1-seguim
            },
            fallecidos: [],
            verAcuse: false,
            openPlanesVenta: false,
            openCuotasCementerio: false,
            openVerNotas: false,
            openEntregarconvenio: false,
            datosConvenio: {
                tipo: "terreno",
                id: "",
                titular: "",
                status: "",
                producto: "",
                fecha_entrega: "",
                entregado_por: "",
                nota: "",
            },
            nota_contenido: "",
            titulo_nota: "",
            openCancelar: false,
            openReportes: false,
            openListaFallecidos: false,
            verFormularioVentas: false,
            tipoFormulario: "",
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
                    label: "Activas",
                    value: "3",
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
                    label: "Núm. Título",
                    value: "3",
                },
                {
                    label: "Núm. Venta",
                    value: "4",
                },
            ],
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                filtro_especifico_opcion: "",
                numero_control: "",
                titular: "",
                fallecido: "",
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
            this.filtersSeguimientos.cliente_id = venta.cliente_id;
            this.filtersSeguimientos.tipo_cliente_id = 1;
            this.filtersSeguimientos.operacion_id = venta.operacion_id;
            this.filtersSeguimientos.origen = 2; //clientes
            this.openSeguimientos = true;
        },
        verNota(nota, title) {
            this.openVerNotas = true;
            this.nota_contenido = nota;
            this.titulo_nota = title;
        },

        verEntregarConvenio(datos) {
            this.datosConvenio.id = datos.ventas_terrenos_id;
            this.datosConvenio.titular = datos.nombre;
            this.datosConvenio.nota = datos.nombre;
            this.datosConvenio.status = datos.venta_terreno.status_convenio;
            this.datosConvenio.producto = datos.venta_terreno.ubicacion_texto;
            this.datosConvenio.fecha_entrega =
                datos.venta_terreno.fecha_convenio_entrega_texto;
            this.datosConvenio.entregado_por =
                datos.venta_terreno.entrego_convenio != null
                    ? datos.venta_terreno.entrego_convenio.nombre
                    : "";
            this.datosConvenio.nota = datos.venta_terreno.nota_convenio;

            this.openEntregarconvenio = true;
        },

        reset(card) {
            card.removeRefreshAnimation(500);
            this.filtroEspecifico = { label: "Núm. Solicitud", value: "1" };
            this.serverOptions.numero_control = "";
            this.mostrar = { label: "15", value: "15" };
            this.estado = { label: "Todas", value: "" };
            this.serverOptions.titular = "";
            this.serverOptions.fallecido = "";
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
            if (cementerio.cancel) {
                cementerio.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.verPaginado = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = this.mostrar.value;
            this.serverOptions.status = this.estado.value;
            this.serverOptions.filtro_especifico_opcion =
                this.filtroEspecifico.value;

            try {
                let res = await cementerio.get_ventas(this.serverOptions, true);
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

        ConsultarListaFallecidos(fallecidos) {
            this.fallecidos = fallecidos;
            this.openListaFallecidos = true;
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

        closeEntregarConvenio() {
            this.openEntregarconvenio = false;
            (async () => {
                await this.get_data(this.actual);
            })();
        },

        closeListaReportes() {
            this.openReportes = false;
            this.verAcuse = false;
            this.id_venta = 0;
            (async () => {
                await this.get_data(this.actual);
            })();
        },

        closeListaFallecidos() {
            this.openListaFallecidos = false;
            this.fallecidos = [];
        },

        closeCancelarVentaRefrescar() {
            this.openCancelar = false;
            (async () => {
                await this.get_data(this.actual);
            })();
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
        (async () => {
            await this.get_data(this.actual);
        })();
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
    },
    beforeDestroy() {

    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
