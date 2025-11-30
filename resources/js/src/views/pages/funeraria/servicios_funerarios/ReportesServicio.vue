<template>
    <div class="centerx">
        <vs-popup class="forms-popup popup-80" title="expediente de servicio funerario" :active="localShow"
            :ref="this.$options.name">
            <div class="pb-6">
                <div class="flex flex-wrap">
                    <div class="w-full" v-if="datosSolicitud.servicio_id">
                        <vs-table :data="documentos" noDataText="0 Resultados" class="tabla-datos">
                            <template slot="header">
                                <h3>Documentos del contrato</h3>
                            </template>
                            <template slot="thead">
                                <vs-th>#</vs-th>
                                <vs-th>Documento</vs-th>
                                <vs-th>Seleccionar Documento</vs-th>
                            </template>
                            <template>
                                <vs-tr v-show="mostrarDocumento(documento.documento)
                                    " v-for="(
documento, index_documento
                                    ) in documentos" v-bind:key="documento.id">
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
                                        <img class="cursor-pointer img-btn-25 mx-4" src="@assets/images/signature.svg"
                                            title="Firmar Documento" @click="
                                                openFirmador(
                                                    documento.documento_id
                                                )
                                                " v-show="documento.firma" />
                                        <img v-if="documento.tipo == 'pdf'" class="cursor-pointer img-btn-24 mx-2"
                                            src="@assets/images/pdf.svg" title="Consultar Documento" @click="
                                                openReporte(
                                                    documento.documento,
                                                    documento.url,
                                                    '',
                                                    ''
                                                )
                                                " />
                                        <img width="30" v-else class="cursor-pointer ml-auto mr-auto"
                                            src="@assets/images/excel.svg" title="Consultar Documento" @click="
                                                openReporte(
                                                    documento.documento,
                                                    documento.url,
                                                    '',
                                                    ''
                                                )
                                                " />
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                    </div>
                </div>

                <div class="w-full py-6" v-if="datosSolicitud.operacion">
                    <vs-table class="tabla-datos" :data="datosSolicitud.operacion.pagos_programados"
                        noDataText="0 Resultados" ref="tabla_pagos_programados">
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
                            <vs-tr v-show="programados.status == 1" v-for="(
programados, index_programado
                                ) in datosSolicitud.operacion.pagos_programados" v-bind:key="programados.id" ref="row">
                                <vs-td :class="[
                                    programados.status_pago == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">
                                    <span class="">{{
                                        programados.num_pago
                                        }}</span>
                                </vs-td>
                                <vs-td :class="[
                                    programados.status_pago == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">{{ programados.referencia_pago }}</vs-td>
                                <vs-td :class="[
                                    programados.status_pago == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">{{
                                        programados.fecha_programada_abr
                                    }}</vs-td>
                                <vs-td :class="[
                                    programados.status_pago == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">
                                    $
                                    {{
                                        programados.monto_programado
                                        | numFormat("0,000.00")
                                    }}
                                </vs-td>
                                <vs-td :class="[
                                    programados.status_pago == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">$
                                    {{
                                        programados.saldo_neto
                                        | numFormat("0,000.00")
                                    }}</vs-td>
                                <vs-td :class="[
                                    programados.status_pago == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">{{ programados.concepto_texto }}</vs-td>
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
                                        <img v-if="programados.saldo_neto > 0" class="cursor-pointer img-btn-24 mx-2"
                                            src="@assets/images/dollar_bill.svg" title="Pagar Ficha" @click="
                                                pagar(
                                                    programados.referencia_pago
                                                )
                                                " />
                                        <img v-else class="cursor-pointer img-btn-20 mx-2"
                                            src="@assets/images/right.svg" title="ficha cubierta" />
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

                <div class="w-full pt-8" v-if="datosSolicitud.operacion">
                    <vs-table class="tabla-datos" :data="pagos" noDataText="0 Resultados">
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
                            <vs-tr v-for="(pago, index_pago) in pagos" v-bind:key="pago.id" ref="row">
                                <vs-td :class="[
                                    pago.status == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">
                                    <span class>{{ pago.id }}</span>
                                </vs-td>
                                <vs-td :class="[
                                    pago.status == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">
                                    <span class>{{
                                        pago.fecha_pago_texto
                                        }}</span>
                                </vs-td>
                                <vs-td :class="[
                                    pago.status == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">
                                    <span class>$
                                        {{
                                            pago.total_pago
                                            | numFormat("0,000.00")
                                        }}</span>
                                </vs-td>
                                <vs-td :class="[
                                    pago.status == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">
                                    <span class>{{
                                        pago.movimientos_pagos_texto
                                        }}</span>
                                </vs-td>
                                <vs-td :class="[
                                    pago.status == 0
                                        ? 'color-danger-900'
                                        : '',
                                ]">
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
                                        <img class="cursor-pointer img-btn-24 mx-2" src="@assets/images/pdf.svg"
                                            title="Ver Nota de Pago" @click="
                                                openReporte(
                                                    'reporte de pago',
                                                    '/pagos/recibo_de_pago/',
                                                    pago.id,
                                                    'pago'
                                                )
                                                " />
                                        <img v-if="pago.status == 1" class="img-btn-20 mx-2"
                                            src="@assets/images/cancel.svg" title="Cancelar Movimiento"
                                            @click="cancelarPago(pago.id)" />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                </div>
            </div>
            <Reporteador v-if="openReportesLista" :header="'consultar documentos de venta de propiedad'"
                :show="openReportesLista" :listadereportes="ListaReportes" :request="request"
                @closeReportes="openReportesLista = false">
            </Reporteador>
            <FormularioPagos v-if="verFormularioPagos" :referencia="referencia" :show="verFormularioPagos"
                @closeVentana="closeFormularioPagos" @retorno_pagos="retorno_pagos"></FormularioPagos>
            <CancelarPago v-if="openCancelar" :z_index="'z-index58k'" :show="openCancelar"
                @closeCancelarPago="closeCancelarPago" @retorno_pago="retorno_pago" :id_pago="id_pago"></CancelarPago>
            <Firmas v-if="openFirmas" :header="'Venta de Terrenos'" :show="openFirmas" :id_documento="id_documento"
                :operacion_id="get_solicitud_id" :tipo="'solicitud'" @closeFirmas="openFirmas = false"></Firmas>
        </vs-popup>
    </div>
</template>
<script>
import CancelarPago from "@pages/pagos/CancelarPago";
import Reporteador from "@pages/Reporteador";
import funeraria from "@services/funeraria";
import pagos from "@services/pagos";
import FormularioPagos from "@pages/pagos/FormularioPagos";
import Firmas from "@pages/Firmas";
export default {
    name: "ReportesServicioFuneraria",
    components: {
        Reporteador,
        FormularioPagos,
        Firmas,
        CancelarPago,
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
        id_solicitud: {
            type: Number,
            required: true,
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    await this.get_solicitudes_servicios_id();
                    if (this.operacion_id != "") {
                        this.solicitudes_id = this.operacion_id;
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
        get_solicitud_id: {
            get() {
                return this.id_solicitud;
            },
            set(newValue) {
                return newValue;
            },
        },
        getVerAcuse: {
            get() {
                return this.verAcuse;
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
                    documento: "Solicitud de Servicio",
                    url: "/funeraria/get_hoja_solicitud",
                    tipo: "pdf",
                    documento_id: 15,
                    firma: true,
                },
                {
                    documento: "Autorización de Servicio Funerario",
                    url: "/funeraria/hoja_preautorizacion",
                    tipo: "pdf",
                    documento_id: 16,
                    firma: true,
                },
                {
                    documento: "Certificado de Defunción",
                    url: "/funeraria/certificado_defuncion",
                    tipo: "pdf",
                    documento_id: 17,
                    firma: false,
                },
                {
                    documento: "Guía de Servicio Para el Cliente",
                    url: "/funeraria/instrucciones_servicio_funerario",
                    tipo: "pdf",
                    documento_id: 18,
                    firma: false,
                },
                {
                    documento: "Constancia de embalsamiento",
                    url: "/funeraria/contancia_de_embalsamiento",
                    tipo: "pdf",
                    documento_id: 20,
                    firma: true,
                },
                {
                    documento: "Material de Velación",
                    url: "/funeraria/material_velacion_rentado",
                    tipo: "pdf",
                    documento_id: 21,
                    firma: true,
                },
                {
                    documento: "Entrega de acta de defunción",
                    url: "/funeraria/entrega_acta_defuncion",
                    tipo: "pdf",
                    documento_id: 19,
                    firma: true,
                },
                {
                    documento: "Autorización de Cremación",
                    url: "/funeraria/autorizacion_cremacion",
                    tipo: "pdf",
                    documento_id: 28,
                    firma: true,
                },
                {
                    documento: "Entrega de cenizas",
                    url: "/funeraria/entrega_cenizas",
                    tipo: "pdf",
                    documento_id: 22,
                    firma: true,
                },
                {
                    documento: "Hoja de Servicio",
                    url: "/funeraria/orden_servicio",
                    tipo: "pdf",
                    documento_id: 23,
                    firma: false,
                },
                {
                    documento: "Constancia de Cremación",
                    url: "/funeraria/constancia_cremacion",
                    tipo: "pdf",
                    documento_id: 26,
                    firma: false,
                },
                {
                    documento: "Acuse de cancelación",
                    url: "/funeraria/servicio_funerario/acuse_cancelacion",
                    tipo: "pdf",
                    documento_id: 25,
                    firma: true,
                },
                {
                    documento: "Contrato",
                    url: "/funeraria/contrato_servicio_funerario",
                    tipo: "pdf",
                    documento_id: 24,
                    firma: true,
                },
                {
                    documento: "Constancia de no embalsamiento",
                    url: "/funeraria/contancia_de_no_embalsamiento",
                    tipo: "pdf",
                    documento_id: 27,
                    firma: true,
                },
            ],
            total: 0 /**rows que se van a remplazar el click en el evento de las tablas para modificar el expand */,
            funcion_reemplazada: [],
            datosSolicitud: [],
            ListaReportes: [],
            request: {
                id_pago: "",
                id_servicio: "",
                email: "",
                destinatario: "",
            },
            openReportesLista: false,
            verFormularioPagos: false,
            tipoFormularioPagos: "",
            operacion_id: "",
            solicitudes_id: "",
            id_documento: "",
            pagos: [],
            openFirmas: false,
        };
    },
    methods: {
        retorno_pago(dato) {
            this.openCancelar = false;
            this.$vs.loading();
            (async () => {
                try {
                    await this.get_solicitudes_servicios_id();
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
        retorno_pagos(datos) {
            (async () => {
                await this.get_solicitudes_servicios_id();
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

        pagar(referencia) {
            this.referencia = referencia;
            this.verFormularioPagos = true;
        },
        mostrarDocumento(documento) {
            if (
                documento != "Acuse de cancelación" &&
                documento != "Constancia de embalsamiento" &&
                documento != "Material de Velación" &&
                documento != "Entrega de acta de defunción" &&
                documento != "Entrega de cenizas" &&
                documento != "Hoja de Servicio" &&
                documento != "Contrato" &&
                documento != "Constancia de Cremación" &&
                documento != "Constancia de no embalsamiento" &&
                documento != "Autorización de Cremación"
            ) {
                return true;
            } else {
                if (documento == "Acuse de cancelación") {
                    /**chenado si esta cancelada la venta para mostrar este archivo de acuse de cancelacion */
                    if (this.datosSolicitud.operacion != null) {
                        if (
                            this.datosSolicitud.operacion.operacion_status == 0
                        ) {
                            return true;
                        } else return false;
                    } else {
                        return false;
                    }
                } else if (documento == "Constancia de embalsamiento") {
                    /**chenado si tiene saldo pendiente */
                    if (this.datosSolicitud.embalsamar_b == 1) {
                        return true;
                    } else return false;
                } else if (documento == "Constancia de no embalsamiento") {
                    if (this.datosSolicitud.embalsamar_b == 0 && this.datosSolicitud.operacion != null) {
                        return true;
                    } else return false;
                } else if (documento == "Material de Velación") {
                    /**chenado si tiene saldo pendiente */
                    if (this.datosSolicitud.material_velacion_b == 1) {
                        return true;
                    } else return false;
                } else if (documento == "Entrega de acta de defunción") {
                    /**chenado si tiene saldo pendiente */
                    if (this.datosSolicitud.acta_b == 1) {
                        return true;
                    } else return false;
                } else if (documento == "Entrega de cenizas") {
                    /**chenado si tiene saldo pendiente */
                    if (this.datosSolicitud.cremacion_b == 1) {
                        return true;
                    } else return false;
                } else if (documento == "Constancia de Cremación") {
                    /**chenado si tiene saldo pendiente */
                    if (this.datosSolicitud.cremacion_b == 1) {
                        return true;
                    } else return false;
                } else if (documento == "Autorización de Cremación") {
                    /**chenado si tiene saldo pendiente */
                    if (this.datosSolicitud.cremacion_b == 1) {
                        return true;
                    } else return false;
                } else if (documento == "Hoja de Servicio") {
                    /**chenado si tiene saldo pendiente */
                    if (this.datosSolicitud.operacion != null) {
                        return true;
                    } else return false;
                } else if (documento == "Contrato") {
                    /**chenado si esta cancelada la venta para mostrar este archivo de acuse de cancelacion */
                    if (this.datosSolicitud.operacion != null) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        },
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
                this.request.id_servicio = this.datosSolicitud.servicio_id;
            }

            this.request.destinatario = this.datosSolicitud.nombre;
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        async get_solicitudes_servicios_id() {
            this.ListaReportes = [];
            this.$vs.loading();
            try {
                this.operacion_id = "";
                let res = await funeraria.get_solicitudes_servicios_id(
                    this.get_solicitud_id
                );
                this.datosSolicitud = res.data[0];
                this.operacion_id = this.datosSolicitud.operacion.operacion_id;
                /*if (this.datosSolicitud.pagos_programados.length > 0) {
                  //calculando el total de rows
                  this.funcion_reemplazada = [];
                  for (
                    let index = 0;
                    index < this.datosSolicitud.pagos_programados.length;
                    index++
                  ) {
                    this.$nextTick(() => {
                      this.funcion_reemplazada.push(this.$refs["row"][index].clicktd);
                      this.$refs["row"][index].clicktd = e => {};
                      this.$refs["row"][index].$el
                        .querySelector(".vs-icon")
                        .addEventListener("click", this.funcion_reemplazada[index]);
                    });
                  }
                }
        */

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

<style lang="stylus">
.con-expand-users {
  width: 100%;

  .con-btns-user {
    display: flex;
    padding: 10px;
    padding-bottom: 0px;
    align-items: center;
    justify-content: space-between;

    .con-userx {
      display: flex;
      align-items: center;
      justify-content: flex-start;
    }
  }

  .list-icon {
    i {
      font-size: 0.9rem !important;
    }
  }
}
</style>
