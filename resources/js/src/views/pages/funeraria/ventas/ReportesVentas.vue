<template>
    <div class="centerx">
        <vs-popup
            class="forms-popup popup-80"
            title="expediente de venta en planes funerarios"
            :active.sync="showVentana"
            ref="lista_reportes"
        >
            <div class="pb-6">
                <div class="flex flex-wrap">
                    <div class="w-full" v-if="datosVenta.operacion_id">
                        <vs-table
                            :data="documentos"
                            noDataText="0 Resultados"
                            class="tabla-datos"
                        >
                            <template slot="header">
                                <h3>Documentos del contrato</h3>
                            </template>
                            <template slot="thead">
                                <vs-th class="w-1/5">#</vs-th>
                                <vs-th class="w-3/5">Documento</vs-th>
                                <vs-th class="w-1/5"
                                    >Seleccionar Documento</vs-th
                                >
                            </template>
                            <template>
                                <vs-tr
                                    v-show="
                                        mostrarDocumento(documento.documento)
                                    "
                                    v-for="(documento,
                                    index_documento) in documentos"
                                    v-bind:key="documento.id"
                                >
                                    <vs-td>
                                        <span class="font-semibold">
                                            {{ index_documento + 1 }}</span
                                        >
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
                                            v-else
                                            class="cursor-pointer img-btn-24 mx-2"
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
                <div class="w-full py-6" v-if="datosVenta.status_usado_b > 0">
                    <vs-table
                        class="tabla-datos"
                        :data="datosVenta.usuarios_plan_futuro"
                        noDataText="0 Resultados"
                        ref="tabla_pagos_servicios_efectuados"
                    >
                        <template slot="header">
                            <h3>Listado de Servicios Funerarios Efectuados</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>#</vs-th>
                            <vs-th>Fallecido</vs-th>
                            <vs-th
                                >Núm. de Contrado de Servicio Funerario</vs-th
                            >
                        </template>
                        <template>
                            <vs-tr
                                v-show="datosVenta.status_usado_b > 0"
                                v-for="(servicios,
                                index_servicio) in datosVenta.usuarios_plan_futuro"
                                v-bind:key="servicios.id"
                                ref="row"
                            >
                                <vs-td> {{ index_servicio + 1 }} </vs-td>

                                <vs-td>
                                    (+) {{ servicios.nombre_afectado }}
                                </vs-td>
                                <vs-td>
                                    {{ servicios.id }}
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                </div>

                <div class="w-full py-6" v-if="datosVenta.operacion_id">
                    <vs-table
                        class="tabla-datos"
                        :data="datosVenta.pagos_programados"
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
                            <vs-th>Nueva Fecha de Pago</vs-th>
                            <vs-th>Monto Pago</vs-th>
                            <vs-th>Intereses Generados</vs-th>
                            <vs-th>Restante a Pagar</vs-th>
                            <vs-th>Concepto</vs-th>
                            <vs-th>Estatus</vs-th>
                            <vs-th>Pagar Recibo</vs-th>
                        </template>
                        <template>
                            <vs-tr
                                v-show="programados.status == 1"
                                v-for="(programados,
                                index_programado) in datosVenta.pagos_programados"
                                v-bind:key="programados.id"
                                ref="row"
                            >
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                >
                                    <span class="font-semibold">{{
                                        programados.num_pago
                                    }}</span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                >
                                    {{ programados.referencia_pago }}
                                </vs-td>
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                    >{{
                                        programados.fecha_programada_abr
                                    }}</vs-td
                                >
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                >
                                    <span v-if="programados.saldo_neto > 0">
                                        {{ programados.fecha_a_pagar_abr }}
                                    </span>
                                    <span v-else
                                        >{{ programados.fecha_ultimo_pago_abr }}
                                    </span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                    >$
                                    {{
                                        programados.monto_programado
                                            | numFormat("0,000.00")
                                    }}</vs-td
                                >
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                    >$
                                    {{
                                        programados.intereses
                                            | numFormat("0,000.00")
                                    }}</vs-td
                                >
                                <vs-td
                                    :class="[
                                        programados.status_pago == 0
                                            ? 'text-danger-900'
                                            : ''
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
                                            ? 'text-danger-900'
                                            : ''
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

                <div class="w-full" v-if="datosVenta.operacion_id">
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
                                            ? 'text-danger-900'
                                            : '',
                                        'font-bold'
                                    ]"
                                >
                                    <span class="">{{ pago.id }}</span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        pago.status == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                >
                                    <span class="">{{
                                        pago.fecha_pago_texto
                                    }}</span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        pago.status == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                >
                                    <span class=""
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
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                >
                                    <span class="">{{
                                        pago.movimientos_pagos_texto
                                    }}</span>
                                </vs-td>
                                <vs-td
                                    :class="[
                                        pago.status == 0
                                            ? 'text-danger-900'
                                            : ''
                                    ]"
                                >
                                    <span class="">{{
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
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                </div>
            </div>

            <Firmas
                :header="'Venta de Terrenos'"
                :show="openFirmas"
                :id_documento="id_documento"
                :operacion_id="operacion_id"
                :tipo="'operacion'"
                @closeFirmas="openFirmas = false"
            ></Firmas>

            <Reporteador
                :header="'consultar documentos de venta de propiedad'"
                :show="openReportesLista"
                :listadereportes="ListaReportes"
                :request="request"
                @closeReportes="openReportesLista = false"
            ></Reporteador>

            <FormularioPagos
                :referencia="referencia"
                :show="verFormularioPagos"
                @closeVentana="closeFormularioPagos"
                @retorno_pagos="retorno_pagos"
            ></FormularioPagos>
        </vs-popup>
    </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import planes from "@services/planes";
import pagos from "@services/pagos";
import FormularioPagos from "@pages/pagos/FormularioPagos";
import Firmas from "@pages/Firmas";
export default {
    components: {
        Reporteador,
        FormularioPagos,
        Firmas
    },
    props: {
        verAcuse: {
            type: Boolean,
            required: false,
            default: false
        },
        show: {
            type: Boolean,
            required: true
        },
        id_venta: {
            type: Number,
            required: true
        }
    },
    watch: {
        show: function(newValue, oldValue) {
            if (newValue == true) {
                this.$refs["lista_reportes"].$el.querySelector(
                    ".vs-icon"
                ).onclick = () => {
                    this.cancelar();
                };
                (async () => {
                    await this.consultar_venta_id();
                    if (this.operacion_id != "") {
                        await this.consultar_pagos_operacion_id();
                    }
                    /**checamos si esta ventana fue abierta con el fin de ver el acuse de cancelacion */
                    if (this.getVerAcuse == true) {
                        this.openReporte(
                            "Acuse de cancelación",
                            "/funeraria/acuse_cancelacion",
                            "",
                            ""
                        );
                    }
                })();
            } else {
                /**cerrar ventana */
                this.datosVenta = [];
                this.total = 0;
            }
        }
    },
    computed: {
        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            }
        },
        get_venta_id: {
            get() {
                return this.id_venta;
            },
            set(newValue) {
                return newValue;
            }
        },
        getVerAcuse: {
            get() {
                return this.verAcuse;
            },
            set(newValue) {
                return newValue;
            }
        }
    },
    data() {
        return {
            referencia: "",
            documentos: [
                {
                    documento: "Formato de Solicitud",
                    url: "/funeraria/documento_solicitud",
                    tipo: "pdf",
                    documento_id: 8,
                    firma: true
                },
                {
                    documento: "Convenio",
                    url: "/funeraria/documento_convenio",
                    tipo: "pdf",
                    documento_id: 9,
                    firma: true
                },
                {
                    documento: "Constancia de Finiquito",
                    url: "/funeraria/documento_finiquitado",
                    tipo: "pdf",
                    documento_id: 10,
                    firma: false
                },
                {
                    documento: "Estado de cuenta",
                    url: "/funeraria/documento_estado_de_cuenta_planes",
                    tipo: "pdf",
                    documento_id: 11,
                    firma: false
                },
                {
                    documento: "Talonario de Pagos",
                    url: "/funeraria/referencias_de_pago",
                    tipo: "pdf",
                    documento_id: 12,
                    firma: false
                },
                {
                    documento: "Reglamento de Pago",
                    url: "/funeraria/reglamento_pago",
                    tipo: "pdf",
                    documento_id: 13,
                    firma: true
                },
                {
                    documento: "Acuse de cancelación",
                    url: "/funeraria/acuse_cancelacion",
                    tipo: "pdf",
                    documento_id: 14,
                    firma: true
                }
            ],
            total: 0 /**rows que se van a remplazar el click en el evento de las tablas para modificar el expand */,
            funcion_reemplazada: [],
            datosVenta: [],
            ListaReportes: [],
            request: {
                id_pago: "",
                venta_id: "",
                email: "",
                destinatario: ""
            },
            openReportesLista: false,
            openFirmas: false,
            verFormularioPagos: false,
            tipoFormularioPagos: "",
            operacion_id: "",
            pagos: []
        };
    },
    methods: {
        openFirmador(id_documento) {
            this.id_documento = id_documento;
            this.openFirmas = true;
        },

        closeFormularioPagos() {
            this.verFormularioPagos = false;
        },
        retorno_pagos(datos) {
            (async () => {
                await this.consultar_venta_id();
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
        mostrarDocumento(documento) {
            if (
                documento != "Acuse de cancelación" &&
                documento != "Constancia de Finiquito"
            ) {
                return true;
            } else {
                if (documento == "Acuse de cancelación") {
                    /**chenado si esta cancelada la venta para mostrar este archivo de acuse de cancelacion */
                    if (this.datosVenta.operacion_status == 0) {
                        return true;
                    } else return false;
                } else if (documento == "Constancia de Finiquito") {
                    /**chenado si tiene saldo pendiente */
                    if (this.datosVenta.saldo_neto <= 0) {
                        return true;
                    } else return false;
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
                url: link
            });
            //estado de cuenta
            this.request.email = this.datosVenta.email;

            if (tipo == "pago") {
                this.request.id_pago = parametro;
            } else {
                this.request.venta_id = this.datosVenta.ventas_planes_id;
            }

            this.request.destinatario = this.datosVenta.nombre;
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        async consultar_venta_id() {
            this.ListaReportes = [];
            this.$vs.loading();
            try {
                this.operacion_id = "";
                let res = await planes.consultar_venta_id(this.get_venta_id);
                this.datosVenta = res.data[0];
                this.operacion_id = this.datosVenta.operacion_id;
                /*if (this.datosVenta.pagos_programados.length > 0) {
          //calculando el total de rows 
          this.funcion_reemplazada = [];
          for (
            let index = 0;
            index < this.datosVenta.pagos_programados.length;
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
                            text:
                                "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000
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
                            text:
                                "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000
                        });
                    }
                }
            }
        }
    },
    mounted() {
        //cerrando el confirmar con esc
        document.body.addEventListener("keyup", e => {
            if (e.keyCode === 27) {
                if (this.showVentana) {
                    //CIERRO EL CONFIRMAR AL PRESONAR ESC
                    //this.cancelar();
                }
            }
        });
    }
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
