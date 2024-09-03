<template>
  <div class="centerx">
    <vs-popup
      class="forms-popup popup-80"
      title="expediente de venta en cementerio"
      :active.sync="showVentana"
      ref="lista_reportes"
    >
      <div class="pb-6">
        <div class="flex flex-wrap">
          <div class="w-full" v-if="datosVenta.operacion_id">
            <vs-table
              class="tabla-datos"
              :data="documentos"
              noDataText="0 Resultados"
            >
              <template slot="header">
                <h3>Documentos del contrato</h3>
              </template>
              <template slot="thead">
                <vs-th class="w-1/5"> #</vs-th>

                <vs-th class="w-3/5">Documento</vs-th>

                <vs-th class="w-1/5">Seleccionar Documento</vs-th>
              </template>
              <template>
                <vs-tr
                  v-show="mostrarDocumento(documento.documento)"
                  v-for="(documento, index_documento) in documentos"
                  v-bind:key="documento.id"
                >
                  <vs-td>
                    <span class="font-medium"> {{ index_documento + 1 }}</span>
                  </vs-td>

                  <vs-td>
                    <span class="">{{ documento.documento }}</span>
                  </vs-td>

                  <vs-td>
                    <div class="flex justify-center">
                      <img
                        class="cursor-pointer img-btn-25 mx-4"
                        src="@assets/images/signature.svg"
                        title="Firmar Documento"
                        @click="openFirmador(documento.documento_id)"
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
                    </div>
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </div>

        <div class="w-full mt-6" v-if="datosVenta.operacion_id">
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
              <vs-th> #</vs-th>

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
                v-for="(
                  programados, index_programado
                ) in datosVenta.pagos_programados"
                v-bind:key="programados.id"
                ref="row"
              >
                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                >
                  <span class="">{{ programados.num_pago }}</span>
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                >
                  {{ programados.referencia_pago }}
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                  >{{ programados.fecha_programada_abr }}</vs-td
                >

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                >
                  <span v-if="programados.saldo_neto > 0">
                    {{ programados.fecha_a_pagar_abr }}
                  </span>

                  <span v-else>{{ programados.fecha_ultimo_pago_abr }} </span>
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                  >$ {{ programados.monto_programado | numFormat("0,000.00") }}
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                  >$ {{ programados.intereses | numFormat("0,000.00") }}</vs-td
                >

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                  >$ {{ programados.saldo_neto | numFormat("0,000.00") }}</vs-td
                >

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
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
                      @click="pagar(programados.referencia_pago)"
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

        <div class="w-full mt-6" v-if="pagos_programados_cuotas.length > 0">
          <vs-table
            class="tabla-datos"
            :data="pagos_programados_cuotas"
            noDataText="0 Resultados"
          >
            <template slot="header">
              <h3>Cuotas de Mantenimiento</h3>
            </template>
            <template slot="thead">
              <vs-th> #</vs-th>

              <vs-th>Referencia</vs-th>

              <vs-th>Periodo</vs-th>

              <vs-th>Fecha Programada</vs-th>

              <vs-th>Nueva Fecha de Pago</vs-th>

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
                ) in pagos_programados_cuotas"
                v-bind:key="programados.id"
                ref="row"
              >
                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                >
                  <span class="">{{ index_programado + 1 }}</span>
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                >
                  {{ programados.referencia_pago }}
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                >
                  {{ programados.concepto_texto }}
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                  >{{ programados.fecha_programada_abr }}</vs-td
                >

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                >
                  <span v-if="programados.saldo_neto > 0">
                    {{ programados.fecha_a_pagar_abr }}
                  </span>

                  <span v-else>{{ programados.fecha_ultimo_pago_abr }} </span>
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                  >$ {{ programados.monto_programado | numFormat("0,000.00") }}
                </vs-td>

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
                  ]"
                  >$ {{ programados.saldo_neto | numFormat("0,000.00") }}</vs-td
                >

                <vs-td
                  :class="[
                    programados.status_pago == 0 ? 'text-danger-900' : '',
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
                  <div
                    class="flex justify-center"
                    v-if="programados.status != 0"
                  >
                    <img
                      v-if="programados.saldo_neto > 0"
                      class="cursor-pointer img-btn-24 mx-2"
                      src="@assets/images/dollar_bill.svg"
                      title="Pagar Ficha"
                      @click="pagar(programados.referencia_pago)"
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
        <div class="w-full mt-6" v-if="datosVenta.operacion_id">
          <vs-table class="tabla-datos" :data="pagos" noDataText="0 Resultados">
            <template slot="header">
              <h3>Listado de Abonos Recibidos</h3>
            </template>
            <template slot="thead">
              <vs-th> Clave</vs-th>

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
                <vs-td :class="[pago.status == 0 ? 'text-danger-900' : '']">
                  <span class="">{{ pago.id }}</span>
                </vs-td>

                <vs-td :class="[pago.status == 0 ? 'text-danger-900' : '']">
                  <span class="">{{ pago.fecha_pago_texto }}</span>
                </vs-td>

                <vs-td :class="[pago.status == 0 ? 'text-danger-900' : '']">
                  <span class=""
                    >$ {{ pago.total_pago | numFormat("0,000.00") }}</span
                  >
                </vs-td>

                <vs-td :class="[pago.status == 0 ? 'text-danger-900' : '']">
                  <span class="">{{ pago.movimientos_pagos_texto }}</span>
                </vs-td>

                <vs-td :class="[pago.status == 0 ? 'text-danger-900' : '']">
                  <span class="">{{ pago.cobrador.nombre }}</span>
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
      <Reporteador
        :header="'consultar documentos de venta de propiedad'"
        :show="openReportesLista"
        :listadereportes="ListaReportes"
        :request="request"
        @closeReportes="openReportesLista = false"
      ></Reporteador>
      <Firmas
        :header="'Venta de Terrenos'"
        :show="openFirmas"
        :id_documento="id_documento"
        :operacion_id="operacion_id"
        :tipo="'operacion'"
        @closeFirmas="openFirmas = false"
      ></Firmas>

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
import Firmas from "@pages/Firmas";
import cementerio from "@services/cementerio";
import pagos from "@services/pagos";
import FormularioPagos from "@pages/pagos/FormularioPagos";
export default {
  components: {
    Reporteador,
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
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["lista_reportes"].$el.querySelector(".vs-icon").onclick =
          () => {
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
              "/cementerio/acuse_cancelacion",
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
    },
  },
  computed: {
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_venta_id: {
      get() {
        return this.id_venta;
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
      referencia: "",
      documentos: [
        {
          documento: "Formato de Solicitud",
          url: "/cementerio/documento_solicitud",
          tipo: "pdf",
          documento_id: 1,
          firma: true,
        },
        {
          documento: "Convenio",
          url: "/cementerio/documento_convenio",
          tipo: "pdf",
          documento_id: 2,
          firma: true,
        },
        {
          documento: "Título",
          url: "/cementerio/documento_titulo",
          tipo: "pdf",
          documento_id: 3,
          firma: false,
        },
        {
          documento: "Estado de cuenta",
          url: "/cementerio/documento_estado_de_cuenta_cementerio",
          tipo: "pdf",
          documento_id: 4,
          firma: false,
        },
        {
          documento: "Talonario de Pagos",
          url: "/cementerio/referencias_de_pago",
          tipo: "pdf",
          documento_id: 5,
          firma: false,
        },
        {
          documento: "Reglamento de Pago",
          url: "/cementerio/reglamento_pago",
          tipo: "pdf",
          documento_id: 6,
          firma: true,
        },
        {
          documento: "Acuse de cancelación",
          url: "/cementerio/acuse_cancelacion",
          tipo: "pdf",
          documento_id: 7,
          firma: true,
        },
        {
          documento: "Servicios en la propiedad",
          url: "/cementerio/servicios_propiedad",
          tipo: "pdf",
          documento_id: 8,
          firma: false,
        },
      ],
      total: 0 /**rows que se van a remplazar el click en el evento de las tablas para modificar el expand */,
      funcion_reemplazada: [],
      datosVenta: [],
      ListaReportes: [],
      request: {
        id_pago: "",
        venta_id: "",
        email: "",
        destinatario: "",
      },
      openReportesLista: false,
      openFirmas: false,
      verFormularioPagos: false,
      tipoFormularioPagos: "",
      operacion_id: "",
      pagos: [],
      pagos_programados_cuotas: [],
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
          //llamando el pago recien hecho
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
      if (documento != "Acuse de cancelación") {
        return true;
      } else {
        /**chenado si esta cancelada la venta para mostrar este archivo de acuse de cancelacion */
        if (this.datosVenta.operacion_status == 0) {
          return true;
        } else return false;
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
      this.request.email = this.datosVenta.email;

      if (tipo == "pago") {
        this.request.id_pago = parametro;
      } else {
        this.request.venta_id = this.datosVenta.ventas_terrenos_id;
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
        let res = await cementerio.consultar_venta_id(this.get_venta_id);
        this.datosVenta = res.data[0];
        this.operacion_id = this.datosVenta.operacion_id;
        this.pagos_programados_cuotas = [];
        if (this.datosVenta.cuota_cementerio_terreno.length > 0) {
          this.datosVenta.cuota_cementerio_terreno.forEach((element) => {
            this.pagos_programados_cuotas.push(element.pagos_programados[0]);
          });
          //this.pagos_programados_cuotas =this.datosVenta.cuota_cementerio_terreno[0].pagos_programados;
        }

        /**aqui voy */

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
          datos_request,
          false
        );
        this.pagos = res.data;
        //agrego al arreglo de pagos los pagos realizados por concepto de mantenimiento.

        datos_request = { operaciones_id: [] };
        if (this.datosVenta.cuota_cementerio_terreno.length > 0) {
          this.datosVenta.cuota_cementerio_terreno.forEach((cuota) => {
            datos_request.operaciones_id.push(cuota.id);
          });
        }
        res = await pagos.consultar_pagos_operacion_id(datos_request, false);
        res.data.forEach((element) => {
          element.movimientos_pagos_texto += " > Cuota de Mantto.";
          this.pagos.push(element);
        });

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
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", (e) => {
      if (e.keyCode === 27) {
        if (this.showVentana) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          //this.cancelar();
        }
      }
    });
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
