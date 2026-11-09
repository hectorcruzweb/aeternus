<template>
    <div>
        <div class="w-full text-right buttons-container-header">
            <vs-button
                class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0 hidden"
                color="primary"
                @click="openPlanesVenta = true"
                type="border"
            >
                <span>Planes de Venta</span>
            </vs-button>
            <vs-button
                class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                color="success"
                @click="TipoFormularioSolicitud('agregar')"
            >
                <span>Nuevo Servicio Funerario</span>
            </vs-button>
        </div>

        <!-- Versión alternativa: el listado anterior permanece intacto. -->
        <div class="pt-2 vx-col w-full"><FiltrosServiciosFunerarios :traslados="catalogosTraslados" :reporte-deshabilitado="cargandoFiltros || !!errorFiltros" @change="aplicarFiltros" @reporte="abrirReporteFiltrado" /></div>
        <VisorReportes v-if="mostrarReporteFiltrado" :show="mostrarReporteFiltrado" :cargar="cargarReporteFiltrado" :descargar-archivo="descargarExcelFiltrado" :descargar-pdf="descargarPdfFiltrado" @close="mostrarReporteFiltrado = false" />
        <p v-if="errorFiltros" class="servicios-error" role="alert">{{ errorFiltros }}</p>
        <p v-if="cargandoFiltros" class="servicios-cargando" role="status">Actualizando servicios…</p>
        <div id="resultados" class="mt-5 flex flex-col flex-1">
            <div
                v-if="noDataFound"
                class="w-full skeleton flex-1 items-center justify-center"
            >
                <span class="text-gray-600 text-lg font-normal"
                    >No hay datos que mostrar</span
                >
            </div>
            <div v-else id="results" class="w-full flex flex-wrap">
                <div class="w-full py-2">
                    <vs-table
                        :sst="true"
                        :max-items="15"
                        :data="ventas"
                        noDataText="0 Resultados"
                        class="tabla-datos"
                    >
                        <template slot="header">
                            <h3>Listado de Servicios Funerarios Atendidos</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Núm. Servicio</vs-th>
                            <vs-th>Fallecido</vs-th>
                            <vs-th v-if="mostrarColumnaCliente">Cliente / contratante temporal</vs-th>
                            <vs-th>Tipo Servicio</vs-th>
                            <vs-th>Fecha</vs-th>
                            <vs-th>$ Saldo</vs-th>
                            <vs-th>Estatus</vs-th>
                            <vs-th>Acciones</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr
                                :data="tr"
                                :key="indextr"
                                v-for="(tr, indextr) in data"
                            >
                                <vs-td :data="data[indextr].servicio_id">
                                    <span class="font-semibold">{{
                                        data[indextr].servicio_id
                                    }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].nombre_afectado">
                                    {{ data[indextr].nombre_afectado }}
                                </vs-td>
                                <vs-td v-if="mostrarColumnaCliente" :data="data[indextr].cliente_nombre">
                                    {{ data[indextr].cliente_nombre || 'No disponible' }}
                                    <small v-if="!data[indextr].operacion" class="servicios-contacto-temporal">Contratante temporal</small>
                                </vs-td>
                                <vs-td
                                    :data="data[indextr].tipo_solicitud_texto"
                                >
                                    {{ data[indextr].tipo_solicitud_texto }}
                                </vs-td>
                                <vs-td
                                    :data="data[indextr].fecha_solicitud_texto"
                                >
                                    <span class="font-medium">
                                        {{
                                            data[indextr].fecha_solicitud_texto
                                        }}
                                    </span>
                                </vs-td>
                                <vs-td
                                    :data="data[indextr].saldo"
                                    v-if="data[indextr].operacion"
                                >
                                    $
                                    {{
                                        data[indextr].operacion.saldo
                                            | numFormat("0,000.00")
                                    }}
                                </vs-td>
                                <vs-td :data="data[indextr].saldo" v-else>
                                    $ {{ 0.0 | numFormat("0,000.00") }}
                                </vs-td>
                                <vs-td>
                                    <p
                                        v-if="
                                            data[indextr].status_texto ==
                                            'Cancelada'
                                        "
                                    >
                                        {{ data[indextr].status_texto }}
                                        <span class="dot-danger"></span>
                                    </p>
                                    <p
                                        v-else-if="
                                            data[indextr].status_texto ==
                                            'Por pagar'
                                        "
                                    >
                                        {{ data[indextr].status_texto }}
                                        <span class="dot-warning"></span>
                                    </p>

                                    <p
                                        v-else-if="
                                            data[indextr].status_texto ==
                                            'Activa'
                                        "
                                    >
                                        Sin Contrato
                                        <span class="dot-warning"></span>
                                    </p>

                                    <p
                                        v-else-if="
                                            data[indextr].status_texto ==
                                            'Pagada'
                                        "
                                    >
                                        {{ data[indextr].status_texto }}
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
                                            v-if="data[indextr].nota_servicio"
                                            class="cursor-pointer img-btn-20 mr-6"
                                            src="@assets/images/notepad_ver.svg"
                                            title="Notas"
                                            @click="
                                                verNota(
                                                    data[
                                                        indextr
                                                    ].nota_servicio.trim(),
                                                    data[indextr]
                                                        .tipo_solicitud_texto +
                                                        '/ ' +
                                                        data[indextr]
                                                            .nombre_afectado
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
                                            v-show="
                                                data[indextr].permite_exhumar_b
                                            "
                                            class="cursor-pointer img-btn-20 mx-3 hidden"
                                            src="@assets/images/shovel.svg"
                                            title="Exhumar Cuerpo"
                                            @click="
                                                Exhumar(
                                                    data[indextr].servicio_id
                                                )
                                            "
                                        />
                                        <img
                                            v-show="data[indextr].exhumado_b"
                                            class="cursor-pointer img-btn-20 mx-3 hidden"
                                            src="@assets/images/shovel_disabled.svg"
                                            title="Servicio Exhumado"
                                            @click="Exhumado()"
                                        />

                                        <img
                                            class="cursor-pointer img-btn-20 mx-3"
                                            src="@assets/images/folder.svg"
                                            title="Expediente"
                                            @click="
                                                ConsultarVenta(
                                                    data[indextr].servicio_id
                                                )
                                            "
                                        />
                                        <img
                                            v-show="
                                                verModificarSolicitud(
                                                    data[indextr]
                                                )
                                            "
                                            class="img-btn-18 mx-3"
                                            src="@assets/images/edit.svg"
                                            title="Modificar Solicitud de Servicio"
                                            @click="
                                                openModificarSolicitud(
                                                    data[indextr].servicio_id
                                                )
                                            "
                                        />

                                        <img
                                            v-if="
                                                data[indextr]
                                                    .tipo_solicitud_id == 1
                                            "
                                            class="img-btn-22 mx-3"
                                            src="@assets/images/contrato.svg"
                                            title="Editar Contrato"
                                            @click="
                                                openModificar(
                                                    data[indextr].servicio_id
                                                )
                                            "
                                        />

                                        <img
                                            v-else
                                            class="img-btn-22 mx-3 hidden"
                                            src="@assets/images/contrato.svg"
                                            title="Editar Contrato"
                                            @click="
                                                ModificarExhumacion(
                                                    data[indextr].servicio_id
                                                )
                                            "
                                        />

                                        <img
                                            v-if="data[indextr].status_b >= 1"
                                            class="img-btn-22 mx-3"
                                            src="@assets/images/trash.svg"
                                            title="Cancelar Contrato"
                                            @click="
                                                cancelarVenta(
                                                    data[indextr].servicio_id
                                                )
                                            "
                                        />
                                        <img
                                            v-else-if="
                                                data[indextr].operacion != null
                                            "
                                            class="img-btn-22 mx-3"
                                            src="@assets/images/trash-open.svg"
                                            title="Este contrato ya fue cancelado, puede hacer click aquí para consultar"
                                            @click="
                                                ConsultarVentaAcuse(
                                                    data[indextr].servicio_id
                                                )
                                            "
                                        />
                                        <img
                                            v-else
                                            class="img-btn-22 mx-3"
                                            src="@assets/images/trash-open.svg"
                                            title="Este servicio ya fue cancelado pero no tiene contrato asignado"
                                            @click="sinContrato()"
                                        />
                                    </div>
                                </vs-td>
                                <template
                                    class="expand-user"
                                    slot="expand"
                                ></template>
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
                </div>
            </div>
        </div>

        <Password
            v-if="openStatus"
            :show="openStatus"
            :callback-on-success="callback"
            @closeVerificar="closeStatus"
            :accion="accionNombre"
        >
        </Password>
        <VerNotas
            v-if="openVerNotas"
            :show="openVerNotas"
            :nota="nota_contenido"
            :title="titulo_nota"
            @closeVerNotas="openVerNotas = false"
        >
        </VerNotas>
        <FormularioSeguimientos
            v-if="openSeguimientos"
            :show="openSeguimientos"
            :filters="filtersSeguimientos"
            @closeVentana="openSeguimientos = false"
        >
        </FormularioSeguimientos>
        <PlanesVenta
            v-if="openPlanesVenta"
            :show="openPlanesVenta"
            @closePlanesFuneraria="openPlanesVenta = false"
        >
        </PlanesVenta>

        <FormularioSolicitud
            v-if="verFormularioSolicitud"
            :id_solicitud="id_solicitud_modificar"
            :tipo="tipoFormularioSolicitud"
            :show="verFormularioSolicitud"
            @closeVentana="verFormularioSolicitud = false"
            @ver_pdfs_nueva_solicitud="ConsultarVenta"
        >
        </FormularioSolicitud>
        <ReportesServicio
            v-if="openReportes"
            :verAcuse="verAcuse"
            :show="openReportes"
            @closeListaReportes="closeListaReportes"
            :id_solicitud="id_solicitud"
        ></ReportesServicio>
        <FormularioServicios
            v-if="verFormularioServicios"
            :id_solicitud="id_solicitud_modificar"
            :tipo="tipoFormulario"
            :show="verFormularioServicios"
            @closeVentana="verFormularioServicios = false"
            @ver_pdfs_nueva_venta="ConsultarVenta"
        ></FormularioServicios>
        <CancelarVenta
            v-if="openCancelar"
            :show="openCancelar"
            @closeCancelarVenta="openCancelar = false"
            @ConsultarVenta="ConsultarVenta"
            :id_solicitud="id_solicitud"
        ></CancelarVenta>
    </div>
</template>

<script>
//planes de venta
import funeraria from "@services/funeraria";
import FormularioSeguimientos from "../../seguimientos/FormularioSeguimientos.vue";
import FormularioServicios from "../servicios_funerarios/FormularioServicios";
import FormularioSolicitud from "../servicios_funerarios/FormularioSolicitud";

import ReportesServicio from "../servicios_funerarios/ReportesServicio";
import CancelarVenta from "../servicios_funerarios/CancelarVenta";

//componente de password
import Password from "@pages/confirmar_password";
import VerNotas from "@pages/VerNotas";
import usuarios from "@services/Usuarios";
/**VARIABLES GLOBALES */
import FiltrosServiciosFunerarios from "./FiltrosServiciosFunerarios.vue";
import VisorReportes from "@pages/VisorReportes.vue";
import filtrarServicios from "@services/filtrarServiciosFunerarios";
import axiosCancel from "axios";

import PlanesVenta from "@pages/funeraria/ventas/PlanesVentas";
export default {
    name: "ListadoServiciosFunerariosFiltros",
    components: {
        FiltrosServiciosFunerarios,
        VisorReportes,
        Password,
        FormularioServicios,
        ReportesServicio,
        CancelarVenta,
        PlanesVenta,
        FormularioSolicitud,
        VerNotas,
        FormularioSeguimientos,
    },
    computed: {
        mostrarColumnaCliente() { return !!String(this.filtrosServicios.cliente || '').trim(); },
        noDataFound() {
            return this.ventas.length === 0;
        },
    },
    watch: {
        actual(pagina) { if (pagina !== this.paginaCargada) this.get_data(pagina); },
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
            verFormularioServicios: false,
            tipoFormulario: "",
            //variable
            tipo_propiedades: [],
            propiedad: { label: "Todos", value: "" },
            openReportesLista: false,
            filtrosServicios: {},
            mostrarReporteFiltrado: false,
            filtrosReporteFiltrado: {},
            catalogosTraslados: [],
            catalogosCargados: false,
            cargandoFiltros: false,
            errorFiltros: '',
            paginaCargada: 0,
            peticionActual: 0,
            cancelarConsulta: null,
            debounceFiltros: null,
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
        OpenFormSeguimientos(venta) {
            this.filtersSeguimientos = {};
            if (venta.operacion) {
                this.filtersSeguimientos.cliente_id =
                    venta.operacion.clientes_id;
                this.filtersSeguimientos.tipo_cliente_id = 1;
                this.filtersSeguimientos.operacion_id = venta.operacion.id;
                this.filtersSeguimientos.origen = 2; //clientes
            } else {
                this.$vs.notify({
                    title: "Permiso denegado",
                    text: "Este servicio no tiene un contrato.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    time: 4000,
                });
                return;
            }
            this.$log(this.filtersSeguimientos);
            this.openSeguimientos = true;
        },
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
        /** Una consulta tras 300 ms de cambios; el resto de acciones/modales
         * conserva los ids originales y sus endpoints, sin alterar el listado anterior. */
        /** El reporte abarca todos los resultados, no solo la página de 15.
         * Congelar filtros evita que el visor cambie por ediciones del listado. */
        abrirReporteFiltrado() {
            this.filtrosReporteFiltrado = JSON.parse(JSON.stringify(this.filtrosServicios));
            this.mostrarReporteFiltrado = true;
        },
        async cargarReporteFiltrado() { return (await filtrarServicios.reporte(this.filtrosReporteFiltrado)).data; },
        async descargarExcelFiltrado(token) { return (await filtrarServicios.descargar(token, 'excel')).data; },
        async descargarPdfFiltrado(token) { return (await filtrarServicios.descargar(token, 'pdf')).data; },
        aplicarFiltros(filtros) {
            this.filtrosServicios = filtros;
            this.cargandoFiltros = true;
            clearTimeout(this.debounceFiltros);
            // Invalidar respuestas anteriores incluso durante la espera del debounce.
            this.peticionActual++;
            if (this.cancelarConsulta) this.cancelarConsulta();
            this.debounceFiltros = setTimeout(() => this.get_data(1), 300);
        },
        /** Pagina en servidor; descarta respuestas tardías para no mezclar filtros. */
        async get_data(page) {
            if (this.cancelarConsulta) this.cancelarConsulta();
            const id = ++this.peticionActual;
            this.errorFiltros = '';
            const f = this.filtrosServicios;
            if (f.desde && f.hasta && f.hasta < f.desde) {
                this.cargandoFiltros = false;
                this.errorFiltros = 'La fecha hasta debe ser igual o posterior a la fecha desde.';
                return;
            }
            if (f.numero && !/^[1-9][0-9]*$/.test(f.numero)) {
                this.cargandoFiltros = false;
                this.errorFiltros = 'Ingresa un número de servicio válido.';
                return;
            }
            this.cargandoFiltros = true;
            const source = axiosCancel.CancelToken.source();
            this.cancelarConsulta = source.cancel;
            try {
                const res = await filtrarServicios.listar(Object.assign({}, f, {page, catalogos: this.catalogosCargados ? 0 : 1}), source.token);
                if (id !== this.peticionActual) return;
                this.ventas = res.data.data;
                this.total = res.data.last_page;
                this.paginaCargada = res.data.current_page;
                this.actual = res.data.current_page;
                if (res.data.catalogos) {
                    this.catalogosTraslados = res.data.catalogos.traslados;
                    this.catalogosCargados = true;
                }
                this.verPaginado = true;
            } catch (err) {
                if (id !== this.peticionActual || axiosCancel.isCancel(err)) return;
                const datos = err.response && err.response.data;
                this.errorFiltros = (err.response && err.response.status === 403) ? 'No tienes permiso para consultar los servicios.' : (datos && datos.message) || 'No se pudieron consultar los servicios. Intenta nuevamente.';
            } finally {
                if (id === this.peticionActual) { this.cargandoFiltros = false; this.cancelarConsulta = null; }
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
        clearTimeout(this.debounceFiltros);
        this.peticionActual++;
        if (this.cancelarConsulta) this.cancelarConsulta();
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>

<style scoped>
.servicios-error{padding:10px 12px;margin:10px 0;border-left:2px solid #b44;background:#fcf7f7;color:#a94442;font-size:12px}.servicios-cargando{margin:8px 0;color:#8b7a45;font-size:12px}
.servicios-contacto-temporal{display:block;color:#8b9298;font-size:10px;margin-top:3px}
</style>
