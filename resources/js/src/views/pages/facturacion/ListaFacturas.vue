<template>
    <div>
        <div class="w-full text-right">
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                @click="TipoFormulario('facturar')">
                <span>Crear Factura</span>
            </vs-button>
        </div>

        <!--inicio de buscador-->
        <div class="py-3">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="
              w-full
              sm:w-12/12
              md:w-6/12
              lg:w-6/12
              xl:w-6/12
              mb-1
              px-2
              input-text
            ">
                        <label class="">Tipo de Comprobante</label>
                        <v-select :options="tipos_comprobante" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="serverOptions.tipo_comprobante" class="sm:mb-0" />
                    </div>

                    <div class="
              w-full
              sm:w-12/12
              md:w-6/12
              lg:w-2/12
              xl:w-2/12
              px-2
              input-text
            ">
                        <label class="">Número de Folio</label>
                        <vs-input class="w-full" icon="search" maxlength="14" placeholder="Filtrar por Número de Folio"
                            v-model="serverOptions.numero_control" v-on:keyup.enter="get_data('numero_control', 1)"
                            v-on:blur="get_data('numero_control', 1, 'blur')" />
                    </div>

                    <div class="
              w-full
              sm:w-12/12
              md:w-6/12
              lg:w-4/12
              xl:w-4/12
              px-2
              input-text
            ">
                        <label class="">Rango de Fechas año/mes/dia</label>

                        <flat-pickr name="fecha_timbrado" data-vv-as=" "
                            v-validate:fechatimbrado_validacion_computed.immediate="'required'
                                " :config="configdateTimePickerRange" v-model="serverOptions.fecha_timbrado"
                            placeholder="Fecha(s) de timbrado" class="w-full" @on-close="onCloseDate" />
                    </div>
                    <div class="
              w-full
              sm:w-12/12
              md:w-6/12
              lg:w-6/12
              xl:w-6/12
              px-2
              input-text
            ">
                        <label class="">Nombre del cliente</label>
                        <vs-input ref="cliente" name="cliente" type="text" class="w-full" maxlength="150"
                            placeholder="Ej. Juán Pérez" v-model="serverOptions.cliente"
                            v-on:keyup.enter="get_data('cliente', 1)" v-on:blur="get_data('cliente', 1, 'blur')" />
                    </div>

                    <div class="
              w-full
              sm:w-12/12
              md:w-6/12
              lg:w-6/12
              xl:w-6/12
              px-2
              input-text
            ">
                        <label class="">RFC</label>
                        <vs-input ref="rfc" name="rfc" class="w-full" icon="search" maxlength="13"
                            placeholder="Ej. XAXX010101000" v-model.trim="serverOptions.rfc"
                            v-on:keyup.enter="get_data('rfc', 1)" v-on:blur="get_data('rfc', 1, 'blur')" />
                    </div>
                </div>
            </vx-card>
            <div class="mt-10">
                <vs-table :sst="true" :max-items="serverOptions.per_page" :data="cfdis" stripe noDataText="0 Resultados"
                    class="tabla-datos">
                    <template slot="header">
                        <h3>Lista de Artículos y Servicios por Lotes</h3>
                    </template>
                    <template slot="thead">
                        <vs-th>Folio</vs-th>
                        <vs-th>UUID</vs-th>
                        <vs-th>Fecha</vs-th>
                        <vs-th>Cliente</vs-th>
                        <vs-th>RFC</vs-th>
                        <vs-th>Tipo</vs-th>
                        <vs-th>M. de Pago</vs-th>
                        <vs-th>Estatus</vs-th>
                        <vs-th>$ Monto</vs-th>
                        <vs-th>$ Saldo</vs-th>
                        <vs-th>Acciones</vs-th>
                    </template>
                    <template slot-scope="{ data }">
                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data"
                            :class="[tr.status == 0 ? 'text-danger' : '']">
                            <vs-td :data="data[indextr].id">
                                <span class="font-semibold">{{
                                    data[indextr].id
                                }}</span>
                            </vs-td>
                            <vs-td :data="data[indextr].uuid">
                                {{ data[indextr].uuid }}
                                <img title="De click para seleccionar factura a sustituir."
                                    v-if="data[indextr].status == 1" width="16" class="cursor-pointer"
                                    src="@assets/images/copytoclipboard.svg" v-clipboard:copy="data[indextr].uuid"
                                    v-clipboard:success="onCopy" v-clipboard:error="onError" />
                            </vs-td>
                            <vs-td :data="data[indextr].fecha_timbrado_texto">{{
                                data[indextr].fecha_timbrado_texto
                            }}</vs-td>
                            <vs-td :data="data[indextr].cliente_nombre">{{
                                data[indextr].cliente_nombre
                            }}</vs-td>
                            <vs-td :data="data[indextr].rfc_receptor">{{
                                data[indextr].rfc_receptor
                            }}</vs-td>
                            <vs-td :data="data[indextr].tipo_comprobante_texto">{{
                                data[indextr].tipo_comprobante_texto
                                }}</vs-td>
                            <vs-td :data="data[indextr].sat_metodos_pago_texto">{{
                                data[indextr].sat_metodos_pago_texto
                                }}</vs-td>
                            <vs-td :data="data[indextr].status_texto">{{ data[indextr].status_texto }}
                            </vs-td>
                            <vs-td :data="data[indextr].total">
                                {{
                                    data[indextr].total | numFormat("0,000.00")
                                }}</vs-td>
                            <vs-td :data="data[indextr].saldo_cfdi">
                                {{
                                    data[indextr].saldo_cfdi
                                    | numFormat("0,000.00")
                                }}</vs-td>
                            <vs-td :data="data[indextr].id">
                                <img v-if="data[indextr].nota" class="cursor-pointer img-btn-20 mr-6"
                                    src="@assets/images/notepad_ver.svg" title="Notas" @click="
                                        verNota(
                                            data[indextr].nota.trim(),
                                            data[indextr].cliente_nombre
                                        )
                                        " />
                                <img v-else class="cursor-pointer img-btn-20 mr-6"
                                    src="@assets/images/notepad_ver_no.svg" title="Notas" />

                                <img width="25" class="cursor-pointer" src="@assets/images/cfdicog.svg"
                                    @click="openActions(data[indextr].id)" />
                            </vs-td>
                        </vs-tr>
                    </template>
                </vs-table>
                <div>
                    <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-3"></vs-pagination>
                </div>
            </div>
        </div>

        <!--fin de buscador-->

        <FormularioCFDI :id_cfdi="id_cfdi" :tipo="TipodeFormulario" :show="verFormularioCFDI"
            @closeVentana="closeVentana" @openActions="openActions"></FormularioCFDI>

        <ActionsForm :id_cfdi="id_cfdi" :tipo="TipodeFormulario" :show="verConsultarCfdi"
            :uuid_a_sustituir="uuid_a_sustituir" @closeVentana="closeVentanaActions"
            @reset_uuid_a_sustituir="reset_uuid_a_sustituir"></ActionsForm>
        <VerNotas :show="openVerNotas" :nota="nota_contenido" :title="titulo_nota"
            @closeVerNotas="openVerNotas = false">
        </VerNotas>
    </div>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
//services para la facturacion
import facturacion from "@services/facturacion";
import VerNotas from "@pages/VerNotas";
import FormularioCFDI from "../facturacion/FormularioCFDI";
import ActionsForm from "../facturacion/ActionsForm";
/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import { configdateTimePickerRange } from "@/VariablesGlobales";
const moment = require("moment");
import vSelect from "vue-select";
export default {
    components: {
        "v-select": vSelect,
        FormularioCFDI,
        flatPickr,
        ActionsForm,
        VerNotas
    },
    watch: {
        "serverOptions.tipo_comprobante": function (newVal, previousVal) {
            (async () => {
                await this.get_data("", 1);
            })();
        },
        actual: function (newValue, oldValue) {
            (async () => {
                await this.get_data("", this.actual);
            })();
        }
    },
    computed: {
        fechatimbrado_validacion_computed: function () {
            return this.serverOptions.fecha_timbrado;
        }
    },
    data() {
        return {
            openVerNotas: false,
            nota_contenido: "",
            titulo_nota: "",
            /**consultar cfdi */
            id_cfdi: 0,
            verConsultarCfdi: false,
            uuid_a_sustituir: "",
            configdateTimePickerRange: configdateTimePickerRange,
            /**VARIAVLES DEL MODULO */
            verFormularioCFDI: false,
            tipos_comprobante: [
                {
                    label: "Ver todos",
                    value: ""
                }
            ],
            serverOptions: {
                tipo_comprobante: {
                    label: "Ver todos",
                    value: ""
                },
                tipo_comprobante_id: "",
                fecha_timbrado: [],
                fecha_inicio: "",
                fecha_fin: "",
                page: "",
                per_page: "",
                numero_control: "",
                cliente: "",
                rfc: ""
            },
            selected: [],
            cfdis: [],
            //variable
            verPaginado: true,
            total: 0,
            actual: 1
        };
    },
    methods: {
        verNota(nota, title) {
            this.openVerNotas = true;
            this.nota_contenido = nota;
            this.titulo_nota = title;
        },
        reset_uuid_a_sustituir: function () {
            this.uuid_a_sustituir = "";
        },
        onCopy: function (e) {
            this.uuid_a_sustituir = e.text;
            this.$vs.notify({
                title: "Seleccionar CFDI",
                text:
                    "Se ha seleccionado el CFDI UUID: " +
                    e.text +
                    " para ser sustituido por el CFDI  a cancelar.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "success",
                time: 8000,
                position: "bottom-right"
            });
        },
        onError: function (e) {
            this.$vs.notify({
                title: "Seleccionar CFDI",
                text: "Error al copiar, por favor reintente.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
                position: "bottom-right"
            });
        },
        openActions(folio) {
            this.id_cfdi = folio;
            this.verConsultarCfdi = true;
        },
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
                    this.serverOptions.fecha_inicio = moment(
                        selectedDates[0]
                    ).format("YYYY-MM-DD");
                    this.serverOptions.fecha_fin = moment(
                        selectedDates[1]
                    ).format("YYYY-MM-DD");
                }
            }
            if (buscar) {
                this.get_data("fecha_timbrado", 1, "select");
            }
        },
        async get_tipos_comprobante() {
            this.$vs.loading();
            await facturacion
                .get_tipos_comprobante()
                .then(res => {
                    this.tipos_comprobante = [];
                    this.tipos_comprobante.push({
                        label: "Ver todos",
                        value: ""
                    });
                    res.data.forEach(element => {
                        this.tipos_comprobante.push({
                            label: element.tipo,
                            value: element.id
                        });
                    });
                    this.serverOptions.tipo_comprobante = this.tipos_comprobante[0];
                    this.$vs.loading.close();
                })
                .catch(err => {
                    this.$vs.loading.close();
                });
        },
        TipoFormulario(tipo) {
            this.TipodeFormulario = tipo;
            this.verFormularioCFDI = true;
        },

        closeVentana() {
            this.verFormularioCFDI = false;
            this.get_data(this.actual);
        },
        closeVentanaActions() {
            this.verConsultarCfdi = false;
            this.get_data(this.actual);
        },
        reset(card) {
            card.removeRefreshAnimation(500);
            this.serverOptions.numero_control = "";
            this.serverOptions.cliente = "";
            this.serverOptions.fecha_timbrado = "";
            this.serverOptions.rfc = "";
            this.serverOptions.tipo_comprobante = {
                label: "Ver todos",
                value: ""
            };
            //this.get_data(this.actual);
        },
        async get_data(origen = "", page, evento = "") {
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
                } else if (origen == "fecha_timbrado") {
                    if (this.serverOptions.fecha_timbrado.trim() == "") {
                        //return;
                    }
                } else if (origen == "rfc") {
                    if (this.serverOptions.rfc.trim() == "") {
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
            this.serverOptions.tipo_comprobante_id = this.serverOptions.tipo_comprobante.value;
            facturacion
                .get_cfdis_timbrados(this.serverOptions)
                .then(res => {
                    this.cfdis = res.data.data;
                    this.total = res.data.last_page;
                    this.actual = res.data.current_page;
                    this.verPaginado = true;
                    this.$vs.loading.close();
                })
                .catch(err => {
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
                                time: 4000
                            });
                        }
                    }
                });
        },
        handleSearch(searching) { },
        handleChangePage(page) { },
        handleSort(key, active) { }
    },
    created() {
        (async () => {
            await this.get_tipos_comprobante();
            //await this.get_data(this.actual);
        })();
    }
};
</script>
