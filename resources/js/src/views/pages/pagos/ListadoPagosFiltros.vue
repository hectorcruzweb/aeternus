<template>
    <div>
        <div class="w-full text-right buttons-container-header">
            <vs-button
                class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
                color="success"
                @click="formulario('agregar')"
            >
                <span class="">Capturar Pago</span>
            </vs-button>
        </div>

        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <FiltrosPagos :operaciones="catalogos.operaciones" :movimientos="catalogos.movimientos" :reporte-deshabilitado="cargandoListado || !!errorListado" @reporte="abrirReporteListado" @change="aplicarFiltros" />
            <p v-if="errorListado" class="pagos-error" role="alert">{{ errorListado }}</p>
            <p v-else-if="cargandoListado" class="pagos-cargando" role="status">Cargando pagos…</p>
        </div>
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
                        :data="pagos"
                        noDataText="0 Resultados"
                        class="tabla-datos"
                    >
                        <template slot="header">
                            <h3>Listado de Movimientos Realizados</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Núm. Movimiento</vs-th>
                            <vs-th v-if="mostrarColumnaReferencia">Referencia</vs-th>
                            <vs-th>Tipo Movimiento</vs-th>
                            <vs-th>Fecha</vs-th>
                            <vs-th>Tipo de Operación</vs-th>
                            <vs-th>Cliente</vs-th>
                            <vs-th>$ Monto</vs-th>
                            <vs-th>Estatus</vs-th>
                            <vs-th>Acciones</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr
                                :data="tr"
                                :key="indextr"
                                v-for="(tr, indextr) in data"
                            >
                                <vs-td :data="data[indextr].id">
                                    <span class="font-semibold">
                                        {{ data[indextr].id }}
                                    </span>
                                </vs-td>
                                <vs-td v-if="mostrarColumnaReferencia" :data="data[indextr].referencias_texto">
                                    <span class="pago-referencias">{{ data[indextr].referencias_texto || 'No disponible' }}</span>
                                </vs-td>
                                <vs-td
                                    :data="
                                        data[indextr].movimientos_pagos_texto
                                    "
                                >
                                    <span
                                        v-if="data[indextr].parent_pago_id > 0"
                                    >
                                        {{
                                            data[indextr]
                                                .movimientos_pagos_texto
                                        }}(Sobre el pago
                                        {{ data[indextr].parent_pago_id }}
                                        <img
                                            width="10"
                                            src="@assets/images/included.svg"
                                        />)
                                    </span>
                                    <span v-else>
                                        {{
                                            data[indextr]
                                                .movimientos_pagos_texto
                                        }}
                                    </span>
                                </vs-td>
                                <vs-td :data="data[indextr].fecha_pago_texto">
                                    <span class="font-medium">{{
                                        data[indextr].fecha_pago_texto
                                    }}</span>
                                </vs-td>
                                <vs-td
                                    :data="data[indextr].tipo_operacion_texto"
                                    >{{
                                        data[indextr].tipo_operacion_texto
                                    }}</vs-td
                                >
                                <vs-td
                                    :data="data[indextr].cliente_texto"
                                    >{{
                                        data[indextr].cliente_texto
                                    }}</vs-td
                                >
                                <vs-td :data="data[indextr].monto_pago">
                                    $
                                    {{
                                        data[indextr].monto_pago
                                            | numFormat("0,000.00")
                                    }}
                                </vs-td>
                                <vs-td :data="data[indextr].status">
                                    <p v-if="data[indextr].status == 1">
                                        Cobrado
                                        <span class="dot-success"></span>
                                    </p>
                                    <p v-else>
                                        Cancelado
                                        <span class="dot-danger"></span>
                                    </p>
                                </vs-td>
                                <vs-td :data="data[indextr].id">
                                    <div class="flex justify-center py-1">
                                        <img
                                            class="img-btn-20 mx-2"
                                            src="@assets/images/pdf.svg"
                                            title="Ver Recibo"
                                            @click="openReporte(data[indextr])"
                                        />
                                        <!--verificar que tipo de movimeinto es, me itneresa que el id del pago a cancelar sea el del pago parent siempre-->
                                        <img
                                            v-if="data[indextr].status == 1"
                                            class="img-btn-20 mx-2"
                                            src="@assets/images/cancel.svg"
                                            title="Cancelar Movimiento"
                                            @click="
                                                cancelarPago(
                                                    data[indextr]
                                                        .parent_pago_id > 0
                                                        ? data[indextr]
                                                              .parent_pago_id
                                                        : data[indextr].id
                                                )
                                            "
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
        <VisorReportes v-if="mostrarReporteListado" :show="mostrarReporteListado" :cargar="cargarReporteListado"
            :descargar-archivo="descargarListadoExcel" :descargar-pdf="descargarListadoPdf" @close="mostrarReporteListado = false" />
        <FormularioPagos
            v-if="verFormularioPagos"
            :show="verFormularioPagos"
            @closeVentana="closeFormularioPagos"
            @retorno_pagos="retorno_pagos"
        >
        </FormularioPagos>
        <Password
            v-if="openStatus"
            :show="openStatus"
            :callback-on-success="callback"
            @closeVerificar="closeStatus"
            :accion="accionNombre"
        >
        </Password>
        <VisorReportes v-if="mostrarDocumentoVisor" :show="mostrarDocumentoVisor"
            :cargar="cargarDocumentoVisor" :descargar-pdf="descargarDocumentoPdf"
            :enviar-correo="enviarDocumentoCorreo" @close="closeListaReportes" />
        <CancelarPago
            v-if="openCancelar"
            :show="openCancelar"
            @closeCancelarPago="closeCancelarPago"
            @retorno_pago="retorno_pago"
            :id_pago="id_pago"
        ></CancelarPago>
    </div>
</template>

<script>
import pagos from "@services/pagos";
import FormularioPagos from "@pages/pagos/FormularioPagos";
import CancelarPago from "@pages/pagos/CancelarPago";
import VisorReportes from "@pages/VisorReportes.vue";
import reportesDocumentosPagos from "@services/reportesDocumentosPagos";
import FiltrosPagos from "./FiltrosPagos.vue";
import filtrarPagos from "@services/filtrarPagos";
// CancelToken/isCancel pertenecen al módulo Axios, no a la instancia HTTP configurada.
import axios from "axios";


//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */


export default {
    name: "ListadoPagosFiltros",
    components: {
        FiltrosPagos,
        Password,
        FormularioPagos,
        VisorReportes,
        CancelarPago,

    },
    watch: {
        actual(valor) { if (valor !== this.paginaCargada) this.get_data(valor); },
    },
    computed: {
        /** Encabezado y celdas comparten condición para conservar alineación. */
        mostrarColumnaReferencia() {
            return this.filtrosListado.buscar_por === 'referencia' && String(this.filtrosListado.numero || '').trim() !== '';
        },
        noDataFound() {
            return this.pagos.length === 0;
        },
    },
    data() {
        return {
            mostrarReporteListado: false, filtrosReporteListado: {},
            cargandoListado: false, errorListado: '', paginaCargada: 1,
            filtrosListado: {}, catalogos: { operaciones: [], movimientos: [] }, catalogosCargados: false,
            versionConsulta: 0, cancelacionConsulta: null, temporizadorConsulta: null, listadoDestruido: false,
            verFormularioPagos: false,
            mostrarDocumentoVisor: false,
            pagoDocumentoId: null,
            ListaReportes: [],
            request: {
                id_pago: "",
                email: "",
                destinatario: "",
            },
            id_pago: 0,
            openCancelar: false,
            activeTab: 0,
            verPaginado: true,
            total: 0,
            actual: 1,
            pagos: [],
            tipoFormulario: "",
            //fin variables
            openStatus: false,
            callback: Function,
            accionNombre: "",
        };
    },
    methods: {
        /** Congela los filtros al abrir: actualizar/descargar mantiene ese alcance. */
        abrirReporteListado() { this.filtrosReporteListado = { ...this.filtrosListado }; this.mostrarReporteListado = true; },
        async cargarReporteListado() { return (await filtrarPagos.reporte(this.filtrosReporteListado)).data; },
        async descargarListadoExcel(token) { return (await filtrarPagos.descargar(token, 'excel')).data; },
        async descargarListadoPdf(token) { return (await filtrarPagos.descargar(token, 'pdf')).data; },

        /** Mantiene el recibo principal al consultar movimientos asociados.
         * Capturar el id evita que la selección de la tabla altere el documento abierto. */
        openReporte(datos) {
            this.pagoDocumentoId = Number(datos.parent_pago_id > 0 ? datos.parent_pago_id : datos.id);
            this.mostrarDocumentoVisor = true;
        },
        async cargarDocumentoVisor() { return (await reportesDocumentosPagos.visualizar({ id_pago: this.pagoDocumentoId })).data; },
        async descargarDocumentoPdf(token) { return (await reportesDocumentosPagos.pdf(token)).data; },
        async enviarDocumentoCorreo(token, datos) { return (await reportesDocumentosPagos.correo(token, datos)).data; },
        closeFormularioPagos() {
            this.verFormularioPagos = false;
        },
        retorno_pagos(datos) {
            (async () => {
                try {
                    this.$vs.loading();
                    let res = await pagos.get_pago_id(datos.id_pago);
                    this.openReporte(res.data[0]);
                    this.$vs.loading.close();
                } catch (error) {
                    this.$vs.loading.close();
                    this.$vs.notify({
                        title: "Error",
                        text: "Ha ocurrido un error al tratar de cargar el recibo de pago, por favor recargue la página.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "9000",
                    });
                }
            })();
            /* recibe un arreglo con id del pago que se acaba de registrar */
        },
        /** Reinicia página automáticamente. Invalida la petición anterior al escribir,
         * antes del debounce, para que una respuesta vieja no reemplace filtros nuevos. */
        aplicarFiltros(filtros) {
            this.filtrosListado = { ...filtros };
            this.versionConsulta++;
            if (this.cancelacionConsulta) this.cancelacionConsulta();
            clearTimeout(this.temporizadorConsulta);
            this.temporizadorConsulta = setTimeout(() => this.get_data(1), 300);
        },
        /** Carga fija de 15 y catálogos una sola vez. No interfiere con pagos.cancel
         * utilizado por captura/cancelación en otros componentes. */
        async get_data(page) {
            const version = ++this.versionConsulta;
            if (this.cancelacionConsulta) this.cancelacionConsulta();
            const fuente = axios.CancelToken.source(); this.cancelacionConsulta = fuente.cancel;
            this.cargandoListado = true; this.errorListado = '';
            try {
                const res = await filtrarPagos.listar({ ...this.filtrosListado, page, catalogos: this.catalogosCargados ? 0 : 1 }, fuente.token);
                if (version !== this.versionConsulta || this.listadoDestruido) return;
                this.pagos = res.data.data; this.total = res.data.last_page;
                this.paginaCargada = res.data.current_page; this.actual = this.paginaCargada;
                if (res.data.catalogos) { this.catalogos = res.data.catalogos; this.catalogosCargados = true; }
                this.verPaginado = true;
                if (page > res.data.last_page) this.get_data(res.data.last_page);
            } catch (error) {
                if (axios.isCancel(error) || version !== this.versionConsulta || this.listadoDestruido) return;
                this.pagos = [];
                const datos = error.response && error.response.data;
                this.errorListado = datos && datos.errors ? Object.keys(datos.errors).map(k => datos.errors[k].join(' ')).join(' ') : 'No se pudieron cargar los pagos. Revisa los filtros e intenta nuevamente.';
            } finally { if (version === this.versionConsulta && !this.listadoDestruido) this.cargandoListado = false; }
        },
        closeStatus() {
            this.openStatus = false;
        },

        cancelarPago(id_pago) {
            this.id_pago = id_pago;
            this.openCancelar = true;
        },
        formulario(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioPagos = true;
        },

        closeListaReportes() {
            this.mostrarDocumentoVisor = false;
            this.id_pago = 0;
            (async () => {
                await this.get_data(this.actual);
            })();
        },
        closeCancelarPago(dato) {
            this.openCancelar = false;
            (async () => {
                await this.get_data(this.actual);
            })();
        },
        retorno_pago(dato) {
            this.openCancelar = false;
            this.$vs.loading();
            (async () => {
                try {
                    let res = await pagos.get_pago_id(dato);
                    this.openReporte(res.data[0]);
                    this.$vs.loading.close();
                } catch (error) {
                    this.$vs.loading.close();
                    this.$vs.notify({
                        title: "Error",
                        text: "Ha ocurrido un error al tratar de cargar el recibo de pago, por favor recargue la página.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "9000",
                    });
                }
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
        this.listadoDestruido = true; this.versionConsulta++;
        clearTimeout(this.temporizadorConsulta);
        if (this.cancelacionConsulta) this.cancelacionConsulta();
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>

<style scoped>
.pago-referencias{font-variant-numeric:tabular-nums;letter-spacing:.3px;overflow-wrap:anywhere}
.pagos-error{border-left:2px solid #b34949;background:#fcf7f7;color:#a14545;padding:10px 14px;font:12px/1.5 Arial,sans-serif;margin:12px 0 0}
.pagos-cargando{color:#858b90;font-size:12px;margin:10px 0 0}
</style>
