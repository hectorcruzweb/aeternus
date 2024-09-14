<template>
    <div class="centerx">
        <vs-popup class="forms-popup popup-90" fullscreen title="cuotas de mantenimiento en cementerio"
            :active.sync="showVentana" ref="planes_cementerio">
            <div class="w-full text-right">
                <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
                    type="border" @click="
                        openReporte(documentos[0].documento, documentos[0].url, '', '')
                        ">
                    <span>Imprimir lista de cuotas</span>
                </vs-button>
                <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                    @click="agregar()">
                    <span>Registrar Cuota</span>
                </vs-button>
            </div>

            <div class="form-group">
                <div class="title-form-group">Cuotas del cementerio registradas</div>
                <div class="form-group-content">
                    <div class="flex flex-wrap">
                        <vs-table :data="cuotas" noDataText="0 Resultados" class="tabla-datos w-full mt-6">
                            <template slot="header">
                                <h3>CUOTAS DE MANTENIMIENTO REGISTRADAS</h3>
                            </template>
                            <template slot="thead">
                                <vs-th>Clave</vs-th>
                                <vs-th>Descripción</vs-th>
                                <vs-th>Periodo</vs-th>
                                <vs-th>Monto Cuota ($)</vs-th>
                                <vs-th>Total de Propiedades</vs-th>
                                <vs-th>Total a Cobrar ($)</vs-th>
                                <vs-th>Total Cobrado ($)</vs-th>
                                <vs-th>Saldo Neto ($)</vs-th>
                                <vs-th>Estatus</vs-th>
                                <vs-th>Acciones</vs-th>
                            </template>
                            <template slot-scope="{ data }">
                                <vs-tr :data="cuotas" :key="index_cuota" v-for="(cuotas, index_cuota) in data">
                                    <vs-td :data="data[index_cuota].id">
                                        <span class="font-semibold">{{ cuotas.id }}</span>
                                    </vs-td>
                                    <vs-td :data="data[index_cuota].descripcion">{{
                                        cuotas.descripcion
                                        }}</vs-td>
                                    <vs-td :data="data[index_cuota].periodo">{{
                                        cuotas.periodo
                                        }}</vs-td>

                                    <vs-td :data="data[index_cuota].cuota_total">$ {{ cuotas.cuota_total |
                                        numFormat("0,000.00") }}</vs-td>

                                    <vs-td :data="data[index_cuota].num_pagos_programados_vigentes">{{
                                        cuotas.num_pagos_programados_vigentes }}</vs-td>

                                    <vs-td :data="data[index_cuota].total_x_cuota">$ {{ cuotas.total_x_cuota |
                                        numFormat("0,000.00") }}</vs-td>

                                    <vs-td :data="data[index_cuota].total_cubierto">$
                                        {{ cuotas.total_cubierto | numFormat("0,000.00") }}</vs-td>
                                    <vs-td :data="data[index_cuota].saldo_neto">$ {{ cuotas.saldo_neto |
                                        numFormat("0,000.00") }}</vs-td>
                                    <vs-td :data="data[index_cuota].status">
                                        <p v-if="data[index_cuota].status == 0">
                                            Cancelada
                                            <span class="dot-danger"></span>
                                        </p>
                                        <p v-else>
                                            Activo
                                            <span class="dot-success"></span>
                                        </p>
                                    </vs-td>

                                    <vs-td :data="data[index_cuota].status">
                                        <img class="cursor-pointer img-btn-22 mx-2 hidden" src="@assets/images/pdf.svg"
                                            title="Consultar Documento" @click="
                                                openReporte(
                                                    documentos[0].documento,
                                                    documentos[0].url,
                                                    data[index_cuota].id,
                                                    ''
                                                )
                                                " />
                                        <img v-if="data[index_cuota].status == 1" class="cursor-pointer img-btn-18 mx-2"
                                            src="@assets/images/edit.svg" title="Modificar"
                                            @click="openModificar(data[index_cuota].id)" />
                                        <img v-if="data[index_cuota].status == 1" class="img-btn-24 mx-2"
                                            src="@assets/images/trash.svg" title="Cancelar Cuota" @click="
                                                cancelar_cuotas(
                                                    data[index_cuota].id,
                                                    data[index_cuota].descripcion,
                                                    'cancelar'
                                                )
                                                " />
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                    </div>
                </div>
            </div>
            <!--componente de confirmar sin contraseña-->
            <ConfirmarDanger :z_index="'z-index58k'" :show="operConfirmar" :callback-on-success="callback"
                @closeVerificar="operConfirmar = false"
                :accion="'¿Desea eliminar este plan de mensualidades? Los datos quedarán eliminados del sistema.'"
                :confirmarButton="'Eliminar'"></ConfirmarDanger>
            <Password :show="openPassword" :callback-on-success="callbackPassword"
                @closeVerificar="openPassword = false" :accion="accionPassword"></Password>

            <FormularioCuotas :id_cuota="id_cuotas_modificar" :tipo="tipoFormulario" :show="verFormularioCuotas"
                @closeVentana="verFormularioCuotas = false" @retornar_id="retorno_id"></FormularioCuotas>

            <Reporteador :header="'Consultar cuotas x propiedad'" :show="openReportesLista"
                :listadereportes="ListaReportes" :request="request" @closeReportes="openReportesLista = false">
            </Reporteador>
            <!--fin de compornentes-->
        </vs-popup>
    </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import ConfirmarDanger from "@pages/ConfirmarDanger";
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
import FormularioCuotas from "@pages/cementerio/ventas/cuotas/FormularioCuotas";
import vSelect from "vue-select";
export default {
    props: {
        show: {
            type: Boolean,
            required: true,
        },
    },
    watch: {
        show: function (newValue, oldValue) {
            if (newValue == true) {
                this.$refs["planes_cementerio"].$el.querySelector(".vs-icon").onclick =
                    () => {
                        this.cancelar();
                    };

                (async () => {
                    /**manda traer los cuotas */
                    await this.get_cuotas();
                })();
            } else {
                /**cerrar ventana */
                this.datosVenta = [];
                this.total = 0;
            }
        },
    },
    components: {
        ConfirmarDanger,
        Password,
        "v-select": vSelect,
        FormularioCuotas,
        Reporteador,
    },
    data() {
        return {
            documentos: [
                {
                    documento: "Cuota de mantenimiento",
                    url: "/cementerio/get_cuota_pdf_todas",
                    tipo: "pdf",
                },
            ],
            /**reporteador */
            openReportesLista: false,
            ListaReportes: [],
            request: {
                destinatario: "",
                id_cuota: "",
                email: "",
            },
            /**reportrador */
            selected: [],

            cuotas: [],
            verFormularioCuotas: false,
            tipoFormulario: "",
            id_cuotas_modificar: 0,
            /** */
            operConfirmar: false,
            openPassword: false,
            accionPassword: "",
            callback: Function,
            callbackPassword: Function,
            //datos que mando para actualizar
        };
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
    },
    methods: {
        openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {
            this.ListaReportes = [];
            this.ListaReportes.push({
                nombre: nombre_reporte,
                url: link,
            });
            //estado de cuenta
            this.request.email = "";

            this.request.id_cuota = parametro;

            this.request.destinatario = "";
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        openModificar(id_cuotas) {
            this.tipoFormulario = "modificar";
            this.id_cuotas_modificar = id_cuotas;
            this.verFormularioCuotas = true;
        },
        agregar() {
            this.tipoFormulario = "agregar";
            this.verFormularioCuotas = true;
        },
        indexcuotass(index, propiedad_id) {
            if (this.propiedad_tipo.value != "") {
                if (propiedad_id != this.propiedad_tipo.value) {
                    return "hidden";
                }
            } else {
                if (index == 0) {
                    return "";
                } else {
                    return "pt-6";
                }
            }
        },
        async get_cuotas() {
            try {
                this.$vs.loading();
                let res = await cementerio.get_cuotas();
                this.cuotas = res.data;
                /**llenando los tipos de propiedad para el select */
                this.propiedades_tipos = [];
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
        cancelar() {
            this.$emit("closeCuotasCementerio");
            return;
        },
        retorno_id(dato) {
            (async () => {
                /**manda traer los cuotas */
                await this.get_cuotas();
            })();
        },
        cancelar_cuotas(id_cuotas, descripcion, accion) {
            this.accionPassword = accion + " cuota: " + descripcion;
            this.id_cuotas_modificar = id_cuotas;
            this.openPassword = true;
            this.callbackPassword = this.cancelar_cuota;
        },
        cancelar_cuota() {
            this.$vs.loading();
            let datos = {
                id_cuotas: this.id_cuotas_modificar,
            };
            cementerio
                .cancelar_cuota(datos)
                .then((res) => {
                    this.$vs.loading.close();
                    this.get_cuotas();
                    if (res.data >= 1) {
                        this.$vs.notify({
                            title: "Cancelar cuota de mantenimiento",
                            text: "Se ha cancelado la cuota exitosamente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "success",
                            time: 4000,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Cancelar cuota de mantenimiento",
                            text: "No se realizaron cambios.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "primary",
                            time: 4000,
                        });
                    }
                })
                .catch((err) => {
                    this.$vs.loading.close();
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
                        } else if (err.response.status == 422) {
                            /**error de validacion */
                            this.errores = err.response.data.error;
                        } else if (err.response.status == 409) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Cancelar cuota de mantenimiento",
                                text: err.response.data.error,
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                time: 8000,
                            });
                        }
                    }
                });
        },
    },
    created() { },
};
</script>
