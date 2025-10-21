<template>
    <div class="centerx">
        <vs-popup :title="HeaderNombre" :class="['forms-popup', z_index]" fullscreen :active="localShow"
            :ref="this.$options.name">
            <div class="flex flex-wrap">
                <div class="w-full xl:w-3/12 px-2">
                    <div class="form-group">
                        <div class="title-form-group">Formatos del Documento</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text">
                                    <label class=""> Seleccione 1 </label>
                                    <v-select :options="reportesDisponible" v-model="reporteSeleccionado"
                                        :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" class="w-full" name="vendedor"
                                        data-vv-as=" ">
                                        <div slot="no-options">Seleccione un reporte</div>
                                    </v-select>
                                </div>
                                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text">
                                    <label class="">Nombre destinatario</label>
                                    <vs-input ref="destinatario" name="destinatario" data-vv-as=" "
                                        data-vv-validate-on="blur" v-validate="'required'" maxlength="75" type="text"
                                        class="w-full" placeholder="Nombre destinatario"
                                        v-model="request_datos.destinatario" />
                                    <span class="">{{ errors.first("destinatario") }}</span>
                                    <span class="" v-if="this.errores.destinatario">{{
                                        errores.destinatario[0]
                                        }}</span>
                                </div>
                                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text">
                                    <label class="">Enviar por Correo</label>
                                    <vs-input name="email" data-vv-as=" " data-vv-validate-on="blur"
                                        v-validate="'required|email'" maxlength="75" type="email" class="w-full"
                                        placeholder="Ingrese el email" v-model="request_datos.email_address" />
                                    <span class="">{{ errors.first("email") }}</span>
                                    <span class="" v-if="this.errores.email">{{
                                        errores.email[0]
                                        }}</span>
                                </div>
                                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                                    <vs-button color="success" class="w-full mt-6" @click="acceptAlert()">
                                        <span class="">Enviar por Correo</span>
                                    </vs-button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--datos de los reportes-->

                    <!--fin de datos de los reportes-->
                </div>
                <div class="w-full xl:w-9/12 px-2">
                    <div class="pdf_layout h-screen bg-grey-light">
                        <div class="flex inline-block h-screen bg-grey-light">
                            <span v-if="pdf_iframe_source == ''"
                                class="mb-auto mt-auto mr-auto ml-auto bg-blue-200">Debe seleccionar un reporte para
                                visualizar.</span>

                            <iframe v-else :src="pdf_iframe_source" class="iframe_viewer"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <ConfirmarAceptar v-if="openConfirmarAceptar" :show="openConfirmarAceptar"
                :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarAceptar = false"
                :accion="'Enviar el documento por correo'" :confirmarButton="'Enviar Documento'"
                :z_index="'z-index70k'"></ConfirmarAceptar>
        </vs-popup>
    </div>
</template>
<script>
import pdf from "@services/pdf";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
export default {
    name: 'Reporteador',
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    this.request_datos.request_parent = [];
                    this.request_datos.destinatario = this.Request.destinatario;
                    this.request_datos.email_address = this.Request.email;
                    this.request_datos.request_parent.push(this.Request);
                    this.$popupManager.register(
                        this,
                        this.cancel,
                        "destinatario"
                    );
                } else {
                    this.pdf_iframe_source = "";
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
        listadereportes: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue.length > 0) {
                    this.reportesDisponible = [];
                    newValue.forEach((element) => {
                        this.reportesDisponible.push({
                            label: element.nombre,
                            value: element.url,
                        });
                    });
                    this.reporteSeleccionado = this.reportesDisponible[0];
                }
            },
        },
        reporteSeleccionado: {
            immediate: true,
            async handler(newVal) {
                this.request_datos.email_send = false;
                await this.get_pdf();
            }
        }
    }
    ,
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        header: {
            type: String,
            required: true,
        },
        listadereportes: {
            type: Array,
            required: true,
            default: {},
        },
        request: {
            type: Object,
            required: true,
            default: [],
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index68k",
        },
    },
    components: {
        "v-select": vSelect,
        ConfirmarAceptar,
    },

    data() {
        return {
            localShow: false,
            openConfirmarAceptar: false,
            callBackConfirmar: Function,
            reportesDisponible: [],
            reporteSeleccionado: {},
            pdf_iframe_source: "",
            errores: [],
            request_datos: {
                email_address: "",
                email_send: false,
                request_parent: [],
                destinatario: "",
            },
        };
    },
    computed: {
        HeaderNombre: {
            get() {
                return this.header;
            },
            set(newValue) {
                return newValue;
            },
        },
        reportes: {
            get() {
                return this.listadereportes;
            },
            set(newValue) {
                return newValue;
            },
        },
        Request: {
            get() {
                return this.request;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    methods: {
        cancel() {
            this.$emit("closeReportes");
        },
        async get_pdf() {
            this.$vs.loading();
            try {
                let res = await pdf.get_pdf(
                    this.reporteSeleccionado.value,
                    this.request_datos
                );
                const file = new Blob([res.data], { type: "application/pdf" });
                this.pdf_iframe_source = URL.createObjectURL(file);
                if (res.data.type != "application/pdf") {
                    this.pdf_iframe_source = "";

                    if (res.status == 204) {
                        this.$vs.notify({
                            title: "Consultar documento",
                            text: "No se encontró información con estos parámetros.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "4000",
                        });
                    } else {
                        this.$vs.notify({
                            title: "Consultar documento",
                            text: "No se encontró este documento",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "4000",
                        });
                    }

                    this.cancel();
                }
                this.$vs.loading.close();
            } catch (err) {
                this.pdf_iframe_source = "";
                this.pdf_iframe_source = "";
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
                        this.errores = JSON.parse(err.response);
                    } else if (err.response.status == 409) {
                        //este error es por alguna condicion que el contrano no cumple para modificar
                        //la propiedad esa ya ha sido vendido
                        this.$vs.notify({
                            title: "Ver Reportes",
                            text: "Ha ocurrido un error, por favor reintente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 30000,
                        });
                    }
                }
                this.cancel();
            }
        },

        acceptAlert() {
            this.$validator
                .validateAll()
                .then((result) => {
                    if (!result) {
                        this.$vs.notify({
                            title: "Error",
                            text: "Verifique que capturó un email y un destinatario",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "4000",
                        });
                        return;
                    } else {
                        if (this.pdf_iframe_source != "") {
                            if (this.request_datos.email_address != "") {
                                this.openConfirmarAceptar = true;
                                (async () => {
                                    this.callBackConfirmar = await this.send_pdf;
                                })();
                            }
                        }
                    }
                })
                .catch(() => { });
        },

        /**enviar pdf por mail */
        async send_pdf() {
            this.request_datos.email_send = true;
            this.$vs.loading();
            try {
                let res = await pdf.send_pdf(
                    this.reporteSeleccionado.value,
                    this.request_datos
                );
                this.$vs.loading.close();
                if (res.data == 1) {
                    this.$vs.notify({
                        title: "Enviar documento por correo",
                        text: "Se ha enviado el correo.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 6000,
                    });
                    this.request_datos.email_address = "";
                    this.request_datos.destinatario = "";
                } else {
                    this.$vs.notify({
                        title: "Enviar documento por correo",
                        text: "Error al enviar el documento, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 6000,
                    });
                }
            } catch (err) {
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
<style scoped></style>
