<template>
    <div class="centerx">
        <vs-popup
            close="cancelar"
            :title="'CONSULTA DE CFDI'"
            :active="localShow"
            :ref="this.$options.name"
            fullscreen
            class="forms-popup popup-70"
        >
            <div class="cfdi-contenido">
                <vs-table
                    stripe
                    class="cfdis-table"
                    :noDataText="' '"
                    :sst="true"
                >
                    <template slot="header">
                        <h3>Datos del Receptor</h3>
                    </template>
                    <template slot="thead">
                        <vs-th>RFC</vs-th>
                        <vs-th>CLIENTE</vs-th>
                        <vs-th>RAZON SOCIAL</vs-th>
                    </template>
                    <tbody>
                        <vs-tr>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.rfc_receptor
                                }}</span>
                            </vs-td>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.cliente_nombre
                                }}</span>
                            </vs-td>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.nombre_receptor
                                }}</span>
                            </vs-td>
                        </vs-tr>
                    </tbody>
                </vs-table>
                <vs-table
                    stripe
                    class="cfdis-table"
                    :noDataText="' '"
                    :sst="true"
                >
                    <template slot="header">
                        <h3>Datos del CFDI</h3>
                    </template>
                    <template slot="thead">
                        <vs-th>FOLIO</vs-th>
                        <vs-th>UUID</vs-th>
                        <vs-th>TIPO</vs-th>
                        <vs-th>FECHA TIMBRADO</vs-th>
                    </template>
                    <tbody>
                        <vs-tr>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.id
                                }}</span>
                            </vs-td>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.uuid
                                }}</span>
                            </vs-td>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.tipo_comprobante_texto
                                }}</span>
                            </vs-td>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.fecha_timbrado_texto
                                }}</span>
                            </vs-td>
                        </vs-tr>
                    </tbody>
                </vs-table>

                <vs-table
                    stripe
                    class="cfdis-table"
                    :noDataText="' '"
                    :sst="true"
                >
                    <template slot="header">
                        <h3>Datos del CFDI</h3>
                    </template>
                    <template slot="thead">
                        <vs-th>FORMA PAGO</vs-th>
                        <vs-th>MÉTODO</vs-th>
                        <vs-th>TOTAL</vs-th>
                    </template>
                    <tbody>
                        <vs-tr>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.sat_formas_pago_texto
                                }}</span>
                            </vs-td>
                            <vs-td>
                                <span class="font-semibold uppercase">{{
                                    cfdi.sat_metodos_pago_texto
                                }}</span>
                            </vs-td>
                            <vs-td>
                                <span class="font-semibold uppercase">
                                    {{
                                        cfdi.total | numFormat("0,000.00")
                                    }}</span
                                >
                            </vs-td>
                        </vs-tr>
                    </tbody>
                </vs-table>

                <div>
                    <vs-table
                        stripe
                        class="cfdis-table"
                        :noDataText="' '"
                        :sst="true"
                    >
                        <template slot="header">
                            <h3>Acciones sobre el CFDI</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Cancelar CFDI</vs-th>
                        </template>
                        <tbody>
                            <vs-tr>
                                <vs-td :colspan="2">
                                    <div
                                        class="py-3 px-2 text-center"
                                        v-if="cfdi.status == 1"
                                    >
                                        <div>
                                            <span class="text-danger"
                                                >Cancelar CFDI</span
                                            >
                                        </div>
                                        <div
                                            class="w-full md:w-6/12 px-2 mx-auto mt-4"
                                        >
                                            <label class="">
                                                Motivo de Cancelación
                                                <span class="">(*)</span>
                                            </label>
                                            <v-select
                                                :options="motivos"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="motivo"
                                                class="w-full"
                                                name="motivo"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione una opción
                                                </div>
                                            </v-select>
                                        </div>
                                        <div
                                            class="w-full alerta pt-4 pb-6 px-2"
                                            v-if="motivo.value == '03'"
                                        >
                                            <div class="success">
                                                <h3>Cancelar CFDI</h3>
                                                <p>
                                                    Puede proceder a cancelar el
                                                    CFDI.
                                                </p>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div
                                                class="w-full alerta pt-4 pb-6 px-2"
                                                v-if="uuid_a_sustituir != ''"
                                            >
                                                <div class="success">
                                                    <h3>Cancelar CFDI</h3>
                                                    <p>
                                                        Este CFDI será cancelado
                                                        y sustituido por el CFDI
                                                        con UUID:
                                                        {{ uuid_a_sustituir }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div
                                                class="w-full alerta pt-4 pb-6 px-2"
                                                v-else
                                            >
                                                <div class="danger">
                                                    <h3>Cancelar CFDI</h3>
                                                    <p>
                                                        No se ha seleccionado el
                                                        CFDI que sustituye a
                                                        este CFDI.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full">
                                            <vs-button
                                                class="w-full sm:w-full md:w-auto md:ml-2 my-2 mt-4"
                                                color="danger"
                                                @click="Cancelar()"
                                            >
                                                Cancelar CFDI
                                            </vs-button>
                                        </div>
                                    </div>
                                    <div class="py-3 px-2 text-center" v-else>
                                        <img
                                            width="36px"
                                            src="@assets/images/pdf.svg"
                                            class="cursor-pointer"
                                            @click="
                                                openReporte(
                                                    'Ver Acuse de Cancelación',
                                                    '/facturacion/get_acuse_cancelacion_pdf/',
                                                    cfdi.id,
                                                    'cfdi'
                                                )
                                            "
                                        />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </tbody>
                    </vs-table>

                    <vs-table
                        stripe
                        class="cfdis-table"
                        :noDataText="' '"
                        :sst="true"
                    >
                        <template slot="header">
                            <h3>CONSULTAR CFDI</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Ver CFDI</vs-th>
                            <vs-th>Descargar</vs-th>
                        </template>
                        <tbody>
                            <vs-tr>
                                <vs-td>
                                    <div class="py-3 px-2 text-center">
                                        <img
                                            width="36px"
                                            src="@assets/images/qr.svg"
                                            class="cursor-pointer"
                                            @click="
                                                openReporte(
                                                    'Ver CFDI',
                                                    '/facturacion/get_cfdi_pdf/',
                                                    cfdi.id,
                                                    'cfdi'
                                                )
                                            "
                                        />
                                    </div>
                                </vs-td>
                                <vs-td>
                                    <div class="py-3 px-2 text-center">
                                        <img
                                            width="36px"
                                            src="@assets/images/downloadcfdi.svg"
                                            class="cursor-pointer"
                                            @click="DownloadCFDI(cfdi.id)"
                                        />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </tbody>
                    </vs-table>
                </div>
            </div>
        </vs-popup>
        <Password
            v-if="openPassword"
            :show="openPassword"
            :callback-on-success="callback"
            @closeVerificar="closePassword"
            :accion="accionNombre"
        ></Password>
        <Reporteador
            v-if="openReportesLista"
            :header="'consultar CFDIs'"
            :show="openReportesLista"
            :listadereportes="ListaReportes"
            :request="request"
            @closeReportes="openReportesLista = false"
        ></Reporteador>
    </div>
</template>
<script>
//componente de password
import Password from "@pages/confirmar_password";
import facturacion from "@services/facturacion";
import Reporteador from "@pages/Reporteador";
import vSelect from "vue-select";
export default {
    name: "ActionsForm",
    components: {
        "v-select": vSelect,
        Password,
        Reporteador,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        //para saber que tipo de formulario es
        id_cfdi: {
            type: Number,
            required: true,
        },
        uuid_a_sustituir: {
            type: String,
            required: false,
            default: "",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    await this.get_cfdi_id();
                    this.uuid_a_sustituir_cancelar = this.getUuidSustituir;
                    this.$popupManager.register(this, this.cerrarVentana, null);
                }
                this.localShow = newValue;
            },
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
        getFolio: {
            get() {
                return this.id_cfdi;
            },
            set(newValue) {
                return newValue;
            },
        },
        getUuidSustituir: {
            get() {
                return this.uuid_a_sustituir;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    data() {
        return {
            localShow: false,
            cfdi: [],
            openReportesLista: false,
            folio_id: "",
            uuid_a_sustituir_cancelar: "",
            motivos: [
                {
                    value: "01",
                    label: "Comprobante emitido con errores con relación.",
                },
                {
                    value: "02",
                    label: "Comprobante emitido con errores sin relación.",
                },
                { value: "03", label: "No se llevó a cabo la operación." },
                {
                    value: "04",
                    label: "Operación nominativa relacionada en la factura global.",
                },
            ],
            motivo: {
                value: "01",
                label: "Comprobante emitido con errores con relación.",
            },
            ListaReportes: [],
            request: {
                folio_id: "",
                email: "",
                destinatario: "",
            },
            openPassword: false,
            accionNombre: "Timbrar CFDI",
            callback: Function,
        };
    },
    methods: {
        closePassword() {
            this.openPassword = false;
        },
        openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {
            this.ListaReportes = [];
            this.request.folio_id = this.cfdi.id;
            this.request.email = this.cfdi.cliente_email;
            this.request.destinatario = this.cfdi.cliente_nombre;
            this.ListaReportes.push({
                nombre: nombre_reporte,
                url: link,
            });
            this.openReportesLista = true;
        },

        DownloadCFDI(folio) {
            (async () => {
                this.$vs.loading();
                try {
                    let res = await facturacion.get_cfdi_download(folio);
                    const downloadUrl = window.URL.createObjectURL(
                        new Blob([res.data])
                    );
                    const link = document.createElement("a");
                    link.href = downloadUrl;
                    link.setAttribute(
                        "download",
                        "CFDI Folio " + this.cfdi.id + ".zip"
                    ); //any other extension
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    this.$vs.loading.close();
                    this.$vs.notify({
                        title: "Descargar CFDI 4.0",
                        text: "Se ha descargado el CFDI correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                } catch (error) {
                    /**error al cargar vendedores */
                    this.$vs.notify({
                        title: "Error",
                        text: "Ha ocurrido un error al tratar de descargar el CFDI seleccionado.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "9000",
                    });
                    this.$vs.loading.close();
                    this.cerrarVentana();
                }
            })();
        },
        cerrarVentana() {
            this.$emit("reset_uuid_a_sustituir");
            this.$emit("closeVentana");
        },
        async get_cfdi_id() {
            this.$vs.loading();
            try {
                let res = await facturacion.get_cfdi_id(this.getFolio);
                this.cfdi = res.data[0];
                this.$vs.loading.close();
            } catch (error) {
                /**error al cargar vendedores */
                this.$vs.notify({
                    title: "Error",
                    text: "Ha ocurrido un error al tratar de cargar el CFDI seleccionado.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "9000",
                });
                this.$vs.loading.close();
                this.cerrarVentana();
            }
        },
        handleSearch(searching) {},
        handleChangePage(page) {},
        handleSort(key, active) {},
        Cancelar() {
            this.accionNombre = "Cancelar CFDI";
            try {
                (async () => {
                    /**ingreso */
                    if (this.cfdi.id > 0) {
                        this.callback = await this.cancelar_cfdi_folio;
                        this.openPassword = true;
                    } else {
                        this.$vs.notify({
                            title: "Error",
                            text: "Seleccione 1 CFDI",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "8000",
                        });
                    }
                })();
            } catch (error) {}
        },
        async cancelar_cfdi_folio() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let motivos = {
                    motivo: this.motivo.value,
                    uuid_a_sustituir_cancelar: this.uuid_a_sustituir_cancelar,
                };
                let datos_cfdi = {};
                datos_cfdi = {
                    ...this.cfdi,
                    ...motivos,
                };
                let res = await facturacion.cancelar_cfdi_folio(datos_cfdi);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Cancelar CFDI 4.0",
                        text: "Se ha cancelado el CFDI correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    await this.get_cfdi_id();
                    //this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Cancelar CFDI 4.0",
                        text: "Error al cancelar el CFDI, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000,
                    });
                }

                this.$vs.loading.close();
            } catch (err) {
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
                        //checo si existe cada error
                        this.errores = err.response.data.error;
                        this.$vs.notify({
                            title: "Timbrar CFDI 4.0",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        this.$vs.notify({
                            title: "Timbrar CFDI 4.0",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 30000,
                        });
                    }
                }
                this.$vs.loading.close();
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
