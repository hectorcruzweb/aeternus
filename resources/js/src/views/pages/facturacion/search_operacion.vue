<template>
    <div class="centerx">
        <vs-popup :class="['forms-popup popup-90', z_index]" fullscreen title="Catálogo de operaciones atendidas"
            :active.sync="showVentana" ref="buscador_operaciones">
            <!--inicio de buscador-->
            <div class="flex flex-wrap">
                <div class="w-full text-right">
                    <img class="cursor-pointer img-btn" src="@assets/images/reload.svg" @click="reset()" />
                </div>
                <div class="w-full pt-2">
                    <div class="form-group">
                        <div class="title-form-group">Filtro de Operaciones</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <!--INICIO DE FORM TIPO-->
                                <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 input-text">
                                    <label class="text-sm opacity-75 font-bold">
                                        Tipo de Operación
                                        <span class="">(*)</span>
                                    </label>
                                    <v-select :options="tipo_operaciones" :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="serverOptions.tipo_operacion"
                                        class="mb-4 sm:mb-0 pb-1 pt-1" name="categoria" data-vv-as=" ">
                                        <div slot="no-options">Seleccione 1</div>
                                    </v-select>
                                    <div>
                                        <span class="text-danger">{{
                                            errors.first("categoria")
                                            }}</span>
                                    </div>
                                </div>
                                <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2 input-text">
                                    <label class="text-sm opacity-75 font-bold">Número de operación</label>
                                    <vs-input name="numero_control" data-vv-as=" " type="text" class="w-full pb-1 pt-1"
                                        placeholder="Ej. 001" maxlength="15" v-model.trim="serverOptions.numero_control"
                                        v-on:keyup.enter="get_data('numero_control', 1)"
                                        v-on:blur="get_data('numero_control', 1, 'blur')" />
                                    <div>
                                        <span class="text-danger text-sm">{{
                                            errors.first("numero_control")
                                            }}</span>
                                    </div>
                                    <div class="mt-2"></div>
                                </div>
                                <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2 input-text">
                                    <label class="text-sm opacity-75 font-bold">
                                        Rango de Fechas año/mes/dia
                                        <span class="texto-importante">(*)</span>
                                    </label>

                                    <flat-pickr name="fecha_operacion" data-vv-as=" "
                                        v-validate:fechapago_validacion_computed.immediate="'required'
                                            " :config="configdateTimePickerRange"
                                        v-model="serverOptions.fecha_operacion" placeholder="Fecha(s) de la operación"
                                        class="w-full my-1" @on-close="onCloseDate" />
                                </div>
                                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text">
                                    <label class="text-sm opacity-75 font-bold">Nombre del cliente</label>
                                    <vs-input ref="cliente" name="cliente" data-vv-as=" " type="text"
                                        class="w-full pb-1 pt-1" placeholder="Ej. Juán Pérez" maxlength="150"
                                        v-model.trim="serverOptions.cliente" v-on:keyup.enter="get_data('cliente', 1)"
                                        v-on:blur="get_data('cliente', 1, 'blur')" />
                                    <div>
                                        <span class="text-danger text-sm">{{
                                            errors.first("cliente")
                                            }}</span>
                                    </div>
                                    <div class="mt-2"></div>
                                </div>

                                <!--FIN FORM TIPO-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10">
                <vs-table :sst="true" :max-items="serverOptions.per_page" :data="operaciones" stripe
                    noDataText="0 Resultados">
                    <template slot="header">
                        <h3>Lista de Operaciones a Facturar</h3>
                    </template>
                    <template slot="thead">
                        <vs-th>#</vs-th>
                        <vs-th>Núm. Operación</vs-th>
                        <vs-th>Tipo</vs-th>
                        <vs-th>Fecha</vs-th>
                        <vs-th>Cliente</vs-th>
                        <vs-th>Seleccionar</vs-th>
                    </template>
                    <template slot-scope="{ data }">
                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                            <vs-td :data="data[indextr].id">
                                <span class="font-semibold">{{ indextr + 1 }}</span>
                            </vs-td>
                            <vs-td :data="data[indextr].id">
                                <span class="font-semibold" v-if="data[indextr].empresa_operaciones_id == 1">{{
                                    data[indextr].ventas_terrenos_id }}</span>
                                <span class="font-semibold" v-else-if="data[indextr].empresa_operaciones_id == 2">Venta
                                    Cementerio > {{
                                        data[indextr].ventas_terrenos_id }}</span>
                                <span class="font-semibold" v-else-if="data[indextr].empresa_operaciones_id == 3">{{
                                    data[indextr].servicios_funerarios_id }}</span>
                                <span class="font-semibold" v-else-if="data[indextr].empresa_operaciones_id == 4">{{
                                    data[indextr].ventas_planes_id }}</span>
                                <span class="font-semibold" v-else-if="data[indextr].empresa_operaciones_id == 5">{{
                                    data[indextr].ventas_generales_id }}</span>
                            </vs-td>
                            <vs-td :data="data[indextr].tipo_operacion_texto">{{
                                data[indextr].tipo_operacion_texto
                            }}</vs-td>

                            <vs-td :data="data[indextr].fecha_operacion_texto">{{
                                data[indextr].fecha_operacion_texto
                            }}</vs-td>
                            <vs-td :data="data[indextr].nombre">{{
                                data[indextr].nombre
                            }}</vs-td>
                            <vs-td :data="data[indextr].id">
                                <img width="25" class="cursor-pointer" src="@assets/images/checked.svg"
                                    @click="retornarSeleccion(data[indextr])" />
                            </vs-td>
                        </vs-tr>
                    </template>
                </vs-table>
                <div>
                    <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-3"></vs-pagination>
                </div>
            </div>
            <!--fin de buscador-->
        </vs-popup>
    </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import facturacion from "@services/facturacion";
import funeraria from "@services/funeraria";
import vSelect from "vue-select";

import { configdateTimePickerRange } from "@/VariablesGlobales";
import { reset } from "numeral";
const moment = require("moment");

export default {
    components: {
        "v-select": vSelect,
        flatPickr,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index54k",
        },
    },
    watch: {
        "serverOptions.tipo_operacion": function (newVal, previousVal) {
            this.get_data("", 1);
        },
        actual: function (newValue, oldValue) {
            this.get_data("", this.actual);
        },
        show: function (newValue, oldValue) {
            if (newValue == true) {
                this.$nextTick(() =>
                    this.$refs["descripcion"].$el.querySelector("input").focus()
                );
                this.$refs["buscador_operaciones"].$el.querySelector(
                    ".vs-icon"
                ).onclick = () => {
                    this.cancelar();
                };
                (async () => {
                    await this.get_empresa_tipo_operaciones();
                })();
                this.get_data("", 1);
            } else {
                /**cerrar y limpiar el formulario */
                this.serverOptions.numero_control = "";
                this.serverOptions.titular = "";
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
        fechapago_validacion_computed: function () {
            return this.serverOptions.fecha_operacion;
        },
    },
    data() {
        return {
            configdateTimePickerRange: configdateTimePickerRange,
            /**VARIAVLES DEL MODULO */
            tipo_operaciones: [
                {
                    label: "Ver todas",
                    value: "",
                },
            ],
            serverOptions: {
                tipo_operacion: {
                    label: "Ver todas",
                    value: "",
                },
                tipo_operacion_id: "",
                fecha_operacion: [],
                fecha_inicio: "",
                fecha_fin: "",
                page: "",
                per_page: "",
                numero_control: "",
                cliente: "",
            },
            /**FIN DE VARIABLES DEL MODULO */

            categorias: [
                {
                    label: "Seleccione 1",
                    value: "",
                },
            ],
            selected: [],
            operaciones: [],
            lotes: [],

            verPaginado: true,
            total: 0,
            actual: 1,
        };
    },
    methods: {
        onCloseDate(selectedDates, dateStr, instance) {
            /**se valdiad que se busque la informacion solo en los casos donde la fechas cambien */
            let buscar = false;
            if (selectedDates.length == 0) {
                /**no hay fechas que buscar */
                if (
                    this.serverOptions.fecha_inicio != "" ||
                    this.serverOptions.fecha_fin != ""
                ) {
                    buscar = true;
                }
                this.serverOptions.fecha_inicio = "";
                this.serverOptions.fecha_fin = "";
            } else {
                /**hay fechas que buscar */
                if (
                    this.serverOptions.fecha_inicio !=
                    moment(selectedDates[0]).format("YYYY-MM-DD") ||
                    this.serverOptions.fecha_fin !=
                    moment(selectedDates[1]).format("YYYY-MM-DD")
                ) {
                    buscar = true;
                    /**agreggo la fecha 1 */
                    this.serverOptions.fecha_inicio = moment(selectedDates[0]).format(
                        "YYYY-MM-DD"
                    );
                    this.serverOptions.fecha_fin = moment(selectedDates[1]).format(
                        "YYYY-MM-DD"
                    );
                }
            }
            if (buscar) {
                this.get_data("fecha_operacion", 1, "select");
            }
        },
        async get_empresa_tipo_operaciones() {
            this.$vs.loading();
            await facturacion
                .get_empresa_tipo_operaciones()
                .then((res) => {
                    this.tipo_operaciones = [];
                    this.tipo_operaciones.push({ label: "Ver todas", value: "" });
                    res.data.forEach((element) => {
                        this.tipo_operaciones.push({
                            label: element.tipo,
                            value: element.id,
                        });
                    });
                    this.serverOptions.tipo_operacion = this.tipo_operaciones[0];
                    this.$vs.loading.close();
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },

        reset() {
            this.serverOptions.numero_control = "";
            this.serverOptions.cliente = "";
            this.serverOptions.fecha_operacion = "";
            this.serverOptions.fecha_fin = "";
            this.serverOptions.fecha_fin = "";
            this.serverOptions.tipo_operacion = this.tipo_operaciones[0];
            this.get_data("", this.actual);
        },
        cancelar() {
            this.$emit("closeBuscador");
            return;
        },
        get_data(origen = "", page, evento = "") {
            if (evento == "blur") {
                return;
            } else {
                /**checando el origen */
                if (origen == "cliente") {
                    if (this.serverOptions.cliente.trim() == "") {
                        //return;
                    }
                } else if (origen == "numero_control") {
                    if (this.serverOptions.numero_control.trim() == "") {
                        //return;
                    }
                } else if (origen == "fecha_operacion") {
                    if (this.serverOptions.fecha_operacion.trim() == "") {
                        //return;
                    }
                }
            }

            let self = this;
            if (facturacion.cancel) {
                facturacion.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.verPaginado = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = 24;
            this.serverOptions.tipo_operacion_id = this.serverOptions.tipo_operacion.value;
            facturacion
                .get_operaciones(this.serverOptions)
                .then((res) => {
                    this.operaciones = res.data.data;
                    this.total = res.data.last_page;
                    this.actual = res.data.current_page;
                    this.verPaginado = true;
                    this.$vs.loading.close();
                })
                .catch((err) => {
                    this.$vs.loading.close();
                    this.ver = true;
                    if (err.response) {
                        if (err.response.status == 403) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Permiso denegado",
                                text:
                                    "Verifique sus permisos con el administrador del sistema.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "warning",
                                time: 4000,
                            });
                        }
                    }
                });
        },
        handleSearch(searching) { },
        handleChangePage(page) { },
        handleSort(key, active) { },
        retornarSeleccion(datos) {
            this.$emit("OperacionSeleccionada", datos);
            this.$emit("closeBuscador");
        },
    },
};
</script>
