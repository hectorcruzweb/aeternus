<template>
    <div class="centerx">
        <vs-popup
            :class="['forms-popup popup-80', z_index]"
            title="expediente de venta el general"
            :active="localShow"
            :ref="this.$options.name"
        >
            <div class="pb-6">
                <div class="flex flex-wrap">
                    <div class="w-full">
                        <vs-table
                            :data="documentos"
                            noDataText="0 Resultados"
                            class="tabla-datos"
                        >
                            <template slot="header">
                                <h3>Documentos de la Venta</h3>
                            </template>
                            <template slot="thead">
                                <vs-th>#</vs-th>
                                <vs-th>Documento</vs-th>
                                <vs-th>Seleccionar Documento</vs-th>
                            </template>
                            <template>
                                <vs-tr
                                    v-for="(
                                        documento, index_documento
                                    ) in documentos"
                                    v-bind:key="documento.documento_id"
                                    v-show="documento.visible"
                                >
                                    <vs-td>
                                        <span class="font-bold">{{
                                            index_documento + 1
                                        }}</span>
                                    </vs-td>
                                    <vs-td>
                                        <span class="">{{
                                            documento.documento
                                        }}</span>
                                    </vs-td>
                                    <vs-td>
                                        <img
                                            class="cursor-pointer img-btn-25 mx-4"
                                            src="@assets/images/signature.svg"
                                            title="Firmar Documento"
                                            @click="
                                                openFirmador(
                                                    documento.documento_id
                                                )
                                            "
                                            v-show="documento.firma"
                                        />
                                        <img
                                            v-if="documento.tipo == 'pdf'"
                                            class="cursor-pointer img-btn-24 mx-2"
                                            src="@assets/images/pdf.svg"
                                            title="Consultar Documento"
                                            @click="
                                                openReporte(
                                                    documento.documento,
                                                    documento.url,
                                                    '',
                                                    ''
                                                )
                                            "
                                        />
                                        <img
                                            width="30"
                                            v-else
                                            class="cursor-pointer ml-auto mr-auto"
                                            src="@assets/images/excel.svg"
                                            title="Consultar Documento"
                                            @click="
                                                openReporte(
                                                    documento.documento,
                                                    documento.url,
                                                    '',
                                                    ''
                                                )
                                            "
                                        />
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                    </div>
                </div>
                <div class="w-full py-6" v-if="datosSolicitud">
                    <vs-table
                        class="tabla-datos"
                        :data="datosSolicitud.pagos_programados"
                        noDataText="0 Resultados"
                        ref="tabla_pagos_programados"
                    >
                        <template slot="header">
                            <h3>Listado de Pagos programados</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>#</vs-th>
                            <vs-th>Referencia</vs-th>
                            <vs-th>Fecha Programada</vs-th>
                            <vs-th>Monto Pago</vs-th>
                            <vs-th>Restante a Pagar</vs-th>
                            <vs-th>Concepto</vs-th>
                            <vs-th>Estatus</vs-th>
                            <vs-th>Pagar Recibo</vs-th>
                        </template>
                        <template>
                            <vs-tr
                                v-show="programados.status == 1"
                                v-for="(
                                    programados, index_programado
                                ) in datosSolicitud.pagos_programados"
                                v-bind:key="programados.id"
                                ref="row"
                            >
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                >
                                    <span class="">{{
                                        programados.num_pago
                                    }}</span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                    >{{ programados.referencia_pago }}</vs-td
                                >
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                    >{{
                                        programados.fecha_programada_abr
                                    }}</vs-td
                                >
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                >
                                    $
                                    {{
                                        programados.monto_programado
                                            | numFormat("0,000.00")
                                    }}
                                </vs-td>
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                    >$
                                    {{
                                        programados.saldo_neto
                                            | numFormat("0,000.00")
                                    }}</vs-td
                                >
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                    >{{ programados.concepto_texto }}</vs-td
                                >
                                <vs-td>
                                    <p v-if="programados.status_pago == 0">
                                        {{ programados.status_pago_texto }}
                                        <span class="dot-danger"></span>
                                    </p>
                                    <p v-else-if="programados.status_pago == 1">
                                        {{ programados.status_pago_texto }}
                                        <span class="dot-warning"></span>
                                    </p>
                                    <p v-else>
                                        {{ programados.status_pago_texto }}
                                        <span class="dot-success"></span>
                                    </p>
                                </vs-td>
                                <vs-td>
                                    <div class="flex justify-center">
                                        <img
                                            v-if="programados.saldo_neto > 0"
                                            class="cursor-pointer img-btn-24 mx-2"
                                            src="@assets/images/dollar_bill.svg"
                                            title="Pagar Ficha"
                                            @click="
                                                pagar(
                                                    programados.referencia_pago
                                                )
                                            "
                                        />
                                        <img
                                            v-else
                                            class="cursor-pointer img-btn-20 mx-2"
                                            src="@assets/images/right.svg"
                                            title="ficha cubierta"
                                        />
                                    </div>
                                </vs-td>
                                <!-- <template class="expand-user" slot="expand">
                d
              </template>
        -->
                            </vs-tr>
                        </template>
                    </vs-table>
                </div>
                <div class="w-full pt-8" v-if="datosSolicitud">
                    <vs-table
                        class="tabla-datos"
                        :data="pagos"
                        noDataText="0 Resultados"
                    >
                        <template slot="header">
                            <h3>Listado de Abonos Recibidos</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Clave</vs-th>
                            <vs-th>Fecha Pago</vs-th>
                            <vs-th>Total Pago</vs-th>
                            <vs-th>Concepto</vs-th>
                            <vs-th>Cobrador</vs-th>
                            <vs-th>Estatus</vs-th>
                            <vs-th>Consultar</vs-th>
                        </template>
                        <template>
                            <vs-tr
                                v-for="(pago, index_pago) in pagos"
                                v-bind:key="pago.id"
                                ref="row"
                            >
                                <vs-td
                                    :class="[
                                        pago.status == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                >
                                    <span class>{{ pago.id }}</span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        pago.status == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                >
                                    <span class>{{
                                        pago.fecha_pago_texto
                                    }}</span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        pago.status == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                >
                                    <span class
                                        >$
                                        {{
                                            pago.total_pago
                                                | numFormat("0,000.00")
                                        }}</span
                                    >
                                </vs-td>
                                <vs-td
                                    :class="[
                                        pago.status == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                >
                                    <span class>{{
                                        pago.movimientos_pagos_texto
                                    }}</span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        pago.status == 0
                                            ? 'color-danger-900'
                                            : '',
                                    ]"
                                >
                                    <span class>{{
                                        pago.cobrador.nombre
                                    }}</span>
                                </vs-td>
                                <vs-td>
                                    <p v-if="pago.status == 0">
                                        {{ pago.status_texto }}
                                        <span class="dot-danger"></span>
                                    </p>

                                    <p v-else>
                                        {{ pago.status_texto }}
                                        <span class="dot-success"></span>
                                    </p>
                                </vs-td>

                                <vs-td>
                                    <div class="flex justify-center">
                                        <img
                                            class="cursor-pointer img-btn-24 mx-2"
                                            src="@assets/images/pdf.svg"
                                            title="Ver Nota de Pago"
                                            @click="
                                                openReporte(
                                                    'reporte de pago',
                                                    '/pagos/recibo_de_pago/',
                                                    pago.id,
                                                    'pago'
                                                )
                                            "
                                        />
                                        <img
                                            v-if="pago.status == 1"
                                            class="img-btn-20 mx-2"
                                            src="@assets/images/cancel.svg"
                                            title="Cancelar Movimiento"
                                            @click="cancelarPago(pago.id)"
                                        />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                </div>
            </div>
            <Reporteador
                v-if="openReportesLista"
                :header="'consultar documentos de venta de en general'"
                :show="openReportesLista"
                :listadereportes="ListaReportes"
                :request="request"
                @closeReportes="openReportesLista = false"
                :z_index="'z-index58k'"
            >
            </Reporteador>

            <FormularioPagos
                v-if="verFormularioPagos"
                :referencia="referencia"
                :show="verFormularioPagos"
                @closeVentana="closeFormularioPagos"
                @retorno_pagos="retorno_pagos"
                :z_index="'z-index58k'"
            ></FormularioPagos>
            <CancelarPago
                v-if="openCancelar"
                :z_index="'z-index58k'"
                :show="openCancelar"
                @closeCancelarPago="closeCancelarPago"
                @retorno_pago="retorno_pago"
                :id_pago="id_pago"
            ></CancelarPago>
            <Firmas
                v-if="openFirmas"
                :header="'Venta de Terrenos'"
                :show="openFirmas"
                :id_documento="id_documento"
                :operacion_id="operacion_id"
                :tipo="'solicitud'"
                @closeFirmas="openFirmas = false"
                :z_index="'z-index58k'"
            ></Firmas>
        </vs-popup>
    </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import funeraria from "@services/funeraria";
import pagos from "@services/pagos";
import CancelarPago from "@pages/pagos/CancelarPago";
import FormularioPagos from "@pages/pagos/FormularioPagos";
import Firmas from "@pages/Firmas";

export default {
    name: "ReportesServicios",
    components: {
        Reporteador,
        CancelarPago,
        FormularioPagos,
        Firmas,
    },
    props: {
        verAcuse: {
            type: Boolean,
            required: false,
            default: false,
        },
        show: {
            type: Boolean,
            required: true,
        },
        id_venta: {
            type: Number,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index55k",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    await this.get_ventas_gral();
                    if (this.operacion_id != "") {
                        await this.consultar_pagos_operacion_id();
                    }
                    /**checamos si esta ventana fue abierta con el fin de ver el acuse de cancelacion */
                    if (this.getVerAcuse == true) {
                        this.openReporte(
                            "Acuse de cancelación",
                            "/funeraria/servicio_funerario/acuse_cancelacion",
                            "",
                            ""
                        );
                    }
                    this.$popupManager.register(this, this.cancelar, null);
                } else {
                    /**cerrar ventana */
                    this.datosSolicitud = [];
                    this.total = 0;
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
    },
    computed: {
        get_venta_id: {
            get() {
                return this.id_venta;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    data() {
        return {
            localShow: false,
            id_pago: "",
            openCancelar: false,
            referencia: "",
            documentos: [
                {
                    documento: "Nota de Venta en Gral.",
                    url: "/funeraria/get_nota_venta_gral",
                    tipo: "pdf",
                    documento_id: 29,
                    firma: false,
                    visible: true,
                },
                {
                    documento: "Acuse de cancelación",
                    url: "/funeraria/get_acuse_cancelacion_venta_gral",
                    tipo: "pdf",
                    documento_id: 30,
                    firma: true,
                    visible: false,
                },
            ],
            total: 0 /**rows que se van a remplazar el click en el evento de las tablas para modificar el expand */,
            funcion_reemplazada: [],
            datosSolicitud: [],
            ListaReportes: [],
            request: {
                id_pago: "",
                id_venta: "",
                email: "",
                destinatario: "",
            },
            openReportesLista: false,
            verFormularioPagos: false,
            tipoFormularioPagos: "",
            operacion_id: "",
            id_venta: "",
            id_documento: "",
            pagos: [],
            openFirmas: false,
        };
    },
    methods: {
        cancelar() {
            this.$emit("closeListaReportes");
            return;
        },

        openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {
            this.ListaReportes = [];
            this.ListaReportes.push({
                nombre: nombre_reporte,
                url: link,
            });
            //estado de cuenta
            this.request.email = this.datosSolicitud.email;

            if (tipo == "pago") {
                this.request.id_pago = parametro;
            } else {
                this.request.id_venta = this.datosSolicitud.ventas_generales_id;
            }

            this.request.destinatario = this.datosSolicitud.nombre;
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        async get_ventas_gral() {
            this.ListaReportes = [];
            this.$vs.loading();
            try {
                this.operacion_id = "";
                let res = await funeraria.get_ventas_gral(
                    null,
                    false,
                    this.get_venta_id
                );
                this.datosSolicitud = res.data[0];
                this.operacion_id = this.datosSolicitud.operacion_id;
                //actualizamos si es posible firmar como entregado la nota
                this.datosSolicitud.venta_general.entregado_b == 1
                    ? (this.documentos[0].firma = true)
                    : (this.documentos[0].firma = false);
                this.datosSolicitud.operacion_status == 0
                    ? (this.documentos[1].visible = true)
                    : (this.documentos[1].visible = false);
                this.$vs.loading.close();
            } catch (err) {
                this.$vs.loading.close();
                if (err.response) {
                    if (err.response.status == 403) {
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
        async consultar_pagos_operacion_id() {
            this.$vs.loading();
            try {
                this.pagos = [];
                let datos_request = { operacion_id: this.operacion_id };
                let res = await pagos.consultar_pagos_operacion_id(
                    datos_request
                );
                this.pagos = res.data;
                this.$vs.loading.close();
            } catch (err) {
                this.$vs.loading.close();
                if (err.response) {
                    if (err.response.status == 403) {
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
        retorno_pago(dato) {
            this.openCancelar = false;
            this.$vs.loading();
            (async () => {
                try {
                    await this.get_ventas_gral();
                    await this.consultar_pagos_operacion_id();
                    this.openReporte(
                        "reporte de pago",
                        "/pagos/recibo_de_pago/",
                        dato,
                        "pago"
                    );
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
        cancelarPago(id_pago) {
            this.id_pago = id_pago;
            this.openCancelar = true;
        },
        closeCancelarPago(dato) {
            this.openCancelar = false;
            (async () => {
                await this.consultar_pagos_operacion_id();
            })();
        },
        openFirmador(id_documento) {
            this.id_documento = id_documento;
            this.openFirmas = true;
        },

        closeFormularioPagos() {
            this.verFormularioPagos = false;
        },
        retorno_pagos(datos) {
            (async () => {
                await this.get_ventas_gral();
                if (this.operacion_id != "") {
                    await this.consultar_pagos_operacion_id();
                    /**llamando el pago recien hecho */
                    this.openReporte(
                        "reporte de pago",
                        "/pagos/recibo_de_pago/",
                        datos.id_pago,
                        "pago"
                    );
                }
            })();
        },
        pagar(referencia) {
            this.referencia = referencia;
            this.verFormularioPagos = true;
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
