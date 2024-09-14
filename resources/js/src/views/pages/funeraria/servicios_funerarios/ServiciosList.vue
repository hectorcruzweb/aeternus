<template>
    <div>
        <div class="w-full text-right">
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0 hidden" color="primary"
                @click="openPlanesVenta = true" type="border">
                <span>Planes de Venta</span>
            </vs-button>
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                @click="TipoFormularioSolicitud('agregar')">
                <span>Nuevo Servicio Funerario</span>
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
                            Fallecido
                        </h3>
                    </div>
                    <div class="w-full input-text px-2">
                        <label class="">Nombre del Fallecido</label>
                        <vs-input class="w-full" icon="search" placeholder="Filtrar por Nombre del Fallecido"
                            v-model="serverOptions.fallecido" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" maxlength="75" />
                    </div>
                </div>
            </vx-card>
        </div>

        <br />
        <vs-table :sst="true" :max-items="serverOptions.per_page.value" :data="ventas" noDataText="0 Resultados"
            class="tabla-datos">
            <template slot="header">
                <h3>Listado de Servicios Funerarios Atendidos</h3>
            </template>
            <template slot="thead">
                <vs-th>Núm. Servicio</vs-th>
                <vs-th>Fallecido</vs-th>
                <vs-th>Tipo Servicio</vs-th>
                <vs-th>Fecha</vs-th>
                <vs-th>$ Saldo</vs-th>
                <vs-th>Estatus</vs-th>
                <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="data[indextr].servicio_id">
                        <span class="font-semibold">{{ data[indextr].servicio_id }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].nombre_afectado">
                        {{ data[indextr].nombre_afectado }}
                    </vs-td>
                    <vs-td :data="data[indextr].tipo_solicitud_texto">
                        {{ data[indextr].tipo_solicitud_texto }}
                    </vs-td>
                    <vs-td :data="data[indextr].fecha_solicitud_texto">
                        <span class="font-medium">
                            {{ data[indextr].fecha_solicitud_texto }}
                        </span>
                    </vs-td>
                    <vs-td :data="data[indextr].saldo" v-if="data[indextr].operacion">
                        $ {{ data[indextr].operacion.saldo | numFormat("0,000.00") }}
                    </vs-td>
                    <vs-td :data="data[indextr].saldo" v-else>
                        $ {{ 0.00 | numFormat("0,000.00") }}
                    </vs-td>
                    <vs-td>
                        <p v-if="data[indextr].status_texto == 'Cancelada'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-danger"></span>
                        </p>
                        <p v-else-if="data[indextr].status_texto == 'Por pagar'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-warning"></span>
                        </p>

                        <p v-else-if="data[indextr].status_texto == 'Activa'">
                            Sin Contrato
                            <span class="dot-warning"></span>
                        </p>

                        <p v-else-if="data[indextr].status_texto == 'Pagada'">
                            {{ data[indextr].status_texto }}
                            <span class="dot-success"></span>
                        </p>
                    </vs-td>
                    <vs-td :data="data[indextr].id">
                        <div class="flex justify-center">
                            <img v-if="data[indextr].nota_servicio" class="cursor-pointer img-btn-20 mr-6"
                                src="@assets/images/notepad_ver.svg" title="Notas"
                                @click="verNota(data[indextr].nota_servicio.trim(), data[indextr].tipo_solicitud_texto + '/ ' + data[indextr].nombre_afectado)" />
                            <img v-else class="cursor-pointer img-btn-20 mr-6" src="@assets/images/notepad_ver_no.svg"
                                title="Notas" />
                            <img v-show="data[indextr].permite_exhumar_b" class="cursor-pointer img-btn-20 mx-3 hidden"
                                src="@assets/images/shovel.svg" title="Exhumar Cuerpo"
                                @click="Exhumar(data[indextr].servicio_id)" />
                            <img v-show="data[indextr].exhumado_b" class="cursor-pointer img-btn-20 mx-3 hidden"
                                src="@assets/images/shovel_disabled.svg" title="Servicio Exhumado"
                                @click="Exhumado()" />

                            <img class="cursor-pointer img-btn-20 mx-3" src="@assets/images/folder.svg"
                                title="Expediente" @click="ConsultarVenta(data[indextr].servicio_id)" />
                            <img v-show="verModificarSolicitud(data[indextr])" class="img-btn-18 mx-3"
                                src="@assets/images/edit.svg" title="Modificar Solicitud de Servicio"
                                @click="openModificarSolicitud(data[indextr].servicio_id)" />

                            <img v-if="data[indextr].tipo_solicitud_id == 1" class="img-btn-22 mx-3"
                                src="@assets/images/contrato.svg" title="Editar Contrato"
                                @click="openModificar(data[indextr].servicio_id)" />

                            <img v-else class="img-btn-22 mx-3 hidden" src="@assets/images/contrato.svg"
                                title="Editar Contrato" @click="ModificarExhumacion(data[indextr].servicio_id)" />

                            <img v-if="data[indextr].status_b >= 1" class="img-btn-22 mx-3"
                                src="@assets/images/trash.svg" title="Cancelar Contrato"
                                @click="cancelarVenta(data[indextr].servicio_id)" />
                            <img v-else-if="data[indextr].operacion != null" class="img-btn-22 mx-3"
                                src="@assets/images/trash-open.svg"
                                title="Este contrato ya fue cancelado, puede hacer click aquí para consultar"
                                @click="ConsultarVentaAcuse(data[indextr].servicio_id)" />
                            <img v-else class="img-btn-22 mx-3" src="@assets/images/trash-open.svg"
                                title="Este servicio ya fue cancelado pero no tiene contrato asignado"
                                @click="sinContrato()" />
                        </div>
                    </vs-td>
                    <template class="expand-user" slot="expand"></template>
                </vs-tr>
            </template>
        </vs-table>

        <div>
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
        </div>

        <FormularioServicios :id_solicitud="id_solicitud_modificar" :tipo="tipoFormulario"
            :show="verFormularioServicios" @closeVentana="verFormularioServicios = false"
            @ver_pdfs_nueva_venta="ConsultarVenta"></FormularioServicios>

        <FormularioSolicitud :id_solicitud="id_solicitud_modificar" :tipo="tipoFormularioSolicitud"
            :show="verFormularioSolicitud" @closeVentana="verFormularioSolicitud = false"
            @ver_pdfs_nueva_solicitud="ConsultarVenta"></FormularioSolicitud>

        <Password :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus"
            :accion="accionNombre">
        </Password>

        <ReportesServicio :verAcuse="verAcuse" :show="openReportes" @closeListaReportes="closeListaReportes"
            :id_solicitud="id_solicitud"></ReportesServicio>

        <CancelarVenta :show="openCancelar" @closeCancelarVenta="openCancelar = false" @ConsultarVenta="ConsultarVenta"
            :id_solicitud="id_solicitud"></CancelarVenta>

        <PlanesVenta :show="openPlanesVenta" @closePlanesFuneraria="openPlanesVenta = false"></PlanesVenta>

        <VerNotas :show="openVerNotas" :nota="nota_contenido" :title="titulo_nota"
            @closeVerNotas="openVerNotas = false">
        </VerNotas>
    </div>
</template>

<script>
//planes de venta
import funeraria from "@services/funeraria";

import FormularioServicios from "../servicios_funerarios/FormularioServicios";
import FormularioSolicitud from "../servicios_funerarios/FormularioSolicitud";

import ReportesServicio from "../servicios_funerarios/ReportesServicio";
import CancelarVenta from "../servicios_funerarios/CancelarVenta";

//componente de password
import Password from "@pages/confirmar_password";
import VerNotas from "@pages/VerNotas";
import usuarios from "@services/Usuarios";
/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";
import PlanesVenta from "@pages/funeraria/ventas/PlanesVentas";
export default {
    components: {
        "v-select": vSelect,
        Password,
        FormularioServicios,
        ReportesServicio,
        CancelarVenta,
        PlanesVenta,
        FormularioSolicitud,
        VerNotas
    },
    computed: {},
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
            openVerNotas: false,
            nota_contenido: '',
            titulo_nota: '',
            verAcuse: false,
            openPlanesVenta: false,
            openCancelar: false,
            openReportes: false,
            verFormularioServicios: false,
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
                    label: "Sin Contrato",
                    value: "4",
                },
                {
                    label: "Canceladas",
                    value: "0",
                },
            ],
            filtroEspecifico: { label: "Núm. Servicio", value: "1" },
            filtrosEspecificos: [
                {
                    label: "Núm. Servicio",
                    value: "1",
                },
            ],
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                filtro_especifico_opcion: "",
                numero_control: "",
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
            id_solicitud_modificar: 0,
            /**opciones para filtrar la peticion del server */
            id_solicitud: 0 /**para consultar los reportesw */,

            /**datos del form */
            id_solicitud_modificar: 0,
            tipoFormularioSolicitud: "",
            verFormularioSolicitud: false,
        };
    },
    methods: {
        verNota(nota, title) {
            this.openVerNotas = true;
            this.nota_contenido = nota;
            this.titulo_nota = title;
        },
        verModificarSolicitud(datos) {
            if (datos.tipo_solicitud_id == 2) {
                return false;
            } else {
                return true;
            }
        },
        reset(card) {
            card.removeRefreshAnimation(500);
            this.filtroEspecifico = { label: "Núm. Servicio", value: "1" };
            this.serverOptions.numero_control = "";
            this.mostrar = { label: "15", value: "15" };
            this.estado = { label: "Todas", value: "" };
            this.serverOptions.fallecido = "";
            //this.get_data(this.actual);
        },
        async get_data(page, evento = "") {
            if (evento == "blur") {
                if (
                    this.serverOptions.fallecido != "" ||
                    this.serverOptions.fallecido == ""
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
                let res = await funeraria.get_solicitudes_servicios(this.serverOptions);
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

        ConsultarVenta(id_solicitud) {
            this.id_solicitud = id_solicitud;
            this.openReportes = true;
        },
        ConsultarVentaAcuse(id_solicitud) {
            this.verAcuse = true;
            this.id_solicitud = id_solicitud;
            this.openReportes = true;
        },

        openModificar(id_solicitud) {
            this.tipoFormulario = "servicio_funerario";
            this.id_solicitud_modificar = id_solicitud;
            this.verFormularioServicios = true;
        },

        openModificarSolicitud(id_solicitud) {
            this.tipoFormularioSolicitud = "modificar";
            this.id_solicitud_modificar = id_solicitud;
            this.verFormularioSolicitud = true;
        },

        Exhumar(id_solicitud) {
            this.tipoFormulario = "exhumar";
            this.id_solicitud_modificar = id_solicitud;
            this.verFormularioServicios = true;
        },

        Exhumado() {
            this.$vs.notify({
                title: "Exhumar Servicio",
                text: "Este servicio ya fue exhumado.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 4000,
            });
        },

        ModificarExhumacion(id_solicitud) {
            this.tipoFormulario = "modificar_exhumar";
            this.id_solicitud_modificar = id_solicitud;
            this.verFormularioServicios = true;
        },

        cancelarVenta(id_solicitud) {
            this.id_solicitud = id_solicitud;
            this.openCancelar = true;
        },
        formulario(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioServicios = true;
        },

        TipoFormularioSolicitud(tipo) {
            this.tipoFormularioSolicitud = tipo;
            this.verFormularioSolicitud = true;
        },

        closeListaReportes() {
            this.openReportes = false;
            this.verAcuse = false;
            this.id_solicitud = 0;
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

        sinContrato() {
            this.$vs.notify({
                title: "Consultar acuse de cancelación",
                text: "Servicio cancelado sin contrato.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 4000,
            });
        },
    },

    created() {
        (async () => {
            await this.get_data(this.actual);
        })();
    },
};
</script>
