<template>
    <div class="centerx">
        <vs-popup
            class="forms-popup popup-80"
            :title="title"
            :active.sync="showVentana"
            ref="lista_reportes"
        >
            <div class="pb-6">
                <div
                    class="mx-auto w-full lg:w-9/12 xl:w-9/12 my-8"
                    v-if="servicio"
                >
                    <div class="form-group">
                        <div class="title-form-group">
                            Información de la Propiedad
                        </div>
                        <div class="form-group-content flex flex-wrap">
                            <div class="w-full lg:w-6/12 xl:w-6/12 px-2">
                                <div class="bg-data-table">
                                    Número de venta de propiedad:
                                </div>
                                <div class="bg-data-table-text">
                                    <p>{{ servicio.ventas_terrenos_id }}</p>
                                </div>
                            </div>
                            <div class="w-full lg:w-6/12 xl:w-6/12 px-2">
                                <div class="bg-data-table">
                                    nombre del titular
                                </div>
                                <div class="bg-data-table-text">
                                    <p>{{ servicio.nombre }}</p>
                                </div>
                            </div>
                            <div class="w-full lg:w-6/12 xl:w-6/12 px-2">
                                <div class="bg-data-table">
                                    Fecha de la venta:
                                </div>
                                <div class="bg-data-table-text">
                                    <p>{{ servicio.fecha_operacion_texto }}</p>
                                </div>
                            </div>
                            <div class="w-full lg:w-6/12 xl:w-6/12 px-2">
                                <div class="bg-data-table">
                                    Nota:
                                </div>
                                <div class="bg-data-table-text">
                                    <p>{{ servicio.nota }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap">
                    <div class="w-full pb-4" v-if="servicio.sepultados">
                        <vs-table
                            class="tabla-datos"
                            :data="servicio.sepultados"
                            noDataText="0 Resultados"
                        >
                            <template slot="header">
                                <h3>Lista de personas sepultadas</h3>
                            </template>
                            <template slot="thead">
                                <vs-th class="w-1/5"
                                    ># Servicio Funerario</vs-th
                                >
                                <vs-th class="w-2/5">Fallecido (a)</vs-th>
                                <vs-th class="w-1/5">Fecha de Inhumación</vs-th>
                                <vs-th class="w-1/5"
                                    >Ver Órden de Servicio</vs-th
                                >
                            </template>
                            <template>
                                <vs-tr
                                    v-for="(sepultado,
                                    index_sepultado) in servicio.sepultados"
                                    v-bind:key="
                                        sepultado.servicios_funerarios_id
                                    "
                                >
                                    <vs-td>
                                        <span class="font-medium">
                                            {{
                                                sepultado.servicios_funerarios_id
                                            }}</span
                                        >
                                    </vs-td>
                                    <vs-td>
                                        <span class="">{{
                                            sepultado.nombre_afectado
                                        }}</span>
                                    </vs-td>
                                    <vs-td>
                                        <span class="">{{
                                            sepultado.fecha_inhumacion_texto
                                        }}</span>
                                    </vs-td>
                                    <vs-td>
                                        <div class="flex justify-center">
                                            <img
                                                class="cursor-pointer img-btn-25 mx-4"
                                                src="@assets/images/pdf.svg"
                                                title="Consultar Servicio"
                                                v-show="
                                                    sepultado.servicios_funerarios_id
                                                "
                                                @click="
                                                    openReporte(
                                                        'Hoja de Servicio',
                                                        '/funeraria/orden_servicio',
                                                        sepultado.servicios_funerarios_id,
                                                        'pdf',
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
            </div>
            <Reporteador
                :header="'consultar sepultados de venta de propiedad'"
                :show="openReportesLista"
                :listadereportes="ListaReportes"
                :request="request"
                @closeReportes="openReportesLista = false"
            ></Reporteador>
        </vs-popup>
    </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
export default {
    components: {
        Reporteador
    },
    props: {
        show: {
            type: Boolean,
            required: true
        },
        fallecidos: {
            type: Object,
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
            } else {
                /**cerrar ventana */
                this.servicio = [];
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
        servicio: {
            get() {
                return this.fallecidos;
            },
            set(newValue) {
                return newValue;
            }
        },
        title: function() {
            if (this.servicio.venta_terreno)
                return this.servicio.venta_terreno.ubicacion_texto;
            else return;
        }
    },
    data() {
        return {
            ListaReportes: [],
            request: {
                id_pago: "",
                venta_id: "",
                email: "",
                destinatario: ""
            },
            openReportesLista: false,
            openFirmas: false,
            operacion_id: ""
        };
    },
    methods: {
        mostrarsepultado(sepultado) {
            if (sepultado != "Acuse de cancelación") {
                return true;
            } else {
                /**chenado si esta cancelada la venta para mostrar este archivo de acuse de cancelacion */
                if (this.listaFallecidos.operacion_status == 0) {
                    return true;
                } else return false;
            }
        },
        cancelar() {
            this.$emit("closeListaFallecidos");
            return;
        },

        openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {
            this.ListaReportes = [];
            this.ListaReportes.push({
                nombre: nombre_reporte,
                url: link
            });
            //estado de cuenta
            this.request.email = this.servicio.email;
            this.request.id_servicio = parametro;
            this.request.destinatario = this.servicio.nombre;
            this.openReportesLista = true;
            this.$vs.loading.close();
        }
    },
    mounted() {
        //cerrando el confirmar con esc
        document.body.addEventListener("keyup", e => {
            if (e.keyCode === 27) {
                if (this.showVentana) {
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
