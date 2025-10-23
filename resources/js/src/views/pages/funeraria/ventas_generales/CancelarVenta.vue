<template>
    <div class="centerx">
        <vs-popup
            class="forms-popup fullscreen"
            title="Cancelar Venta en General"
            :active="localShow"
            :ref="this.$options.name"
        >
            <div class="flex flex-wrap">
                <div class="w-full alerta pt-4 pb-6 px-2">
                    <div class="danger">
                        <h3>¿Está seguro de querer cancelar esta venta?</h3>
                        <p>
                            Una vez realizado el proceso de cancelación no habrá
                            manera de volver a habilitar la venta. Es
                            recomendable llevar a cabo este proceso una vez esté
                            seguro de que es necesario.
                        </p>
                    </div>
                </div>
                <div class="w-full">
                    <ResumenVenta :id_venta="getVentaId"></ResumenVenta>
                    <div class="flex flex-wrap">
                        <div class="w-full xl:w-12/12 pt-6">
                            <div class="flex flex-wrap">
                                <div class="w-full xl:w-4/12 input-text px-2">
                                    <label>$ Monto a devolver:</label>
                                    <vs-input
                                        v-model="form.cantidad"
                                        v-validate="
                                            'required|decimal:2|min_value:0'
                                        "
                                        name="cantidad"
                                        data-vv-as=" "
                                        type="text"
                                        class="w-full"
                                        placeholder=" $ 00.00 Pesos MXN"
                                        maxlength="7"
                                    />
                                    <span>{{ errors.first("cantidad") }}</span>
                                    <span v-if="this.errores.cantidad">{{
                                        errores.cantidad[0]
                                    }}</span>
                                </div>

                                <div class="w-full xl:w-8/12 input-text px-2">
                                    <label
                                        >Seleccione un motivo de
                                        cancelación:</label
                                    >
                                    <v-select
                                        :options="motivos"
                                        :clearable="false"
                                        v-model="form.motivo"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        class="w-full"
                                        v-validate:motivo_computed.immediate="
                                            'required'
                                        "
                                        name="plan_venta"
                                        data-vv-as=" "
                                    >
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Motivo
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("motivo") }}</span>
                                    <span v-if="this.errores['motivo.value']">{{
                                        errores["motivo.value"][0]
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-full input-text px-2">
                            <label
                                >Agregue un comentario respecto a la cancelación
                                de esta venta:</label
                            >
                            <vs-textarea
                                class="w-full"
                                label="Detalle de la cancelación..."
                                height="150px"
                                v-model="form.comentario"
                                ref="comentario"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-buttons-section">
                <div class="text-advice">
                    <span class="ojo-advice">Ojo:</span>
                    Por favor revise la información ingresada, si todo es
                    correcto de click en el "Botón de Abajo”.
                </div>
                <div class="w-full">
                    <vs-button
                        v-if="!fueCancelada"
                        class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
                        color="danger"
                        @click="acceptAlert()"
                    >
                        <span>Cancelar Venta</span>
                    </vs-button>
                </div>
            </div>
            <Password
                v-if="openConfirmar"
                :show="openConfirmar"
                :callback-on-success="callback"
                @closeVerificar="openConfirmar = false"
                :accion="'Cancelar venta de propiedad'"
            ></Password>
            <ConfirmarDanger
                v-if="openConfirmarSinPassword"
                :z_index="'z-index58k'"
                :show="openConfirmarSinPassword"
                :callback-on-success="callBackConfirmar"
                @closeVerificar="openConfirmarSinPassword = false"
                :accion="accionConfirmarSinPassword"
                :confirmarButton="botonConfirmarSinPassword"
            ></ConfirmarDanger>
        </vs-popup>
    </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import vSelect from "vue-select";
import funeraria from "@services/funeraria";
import ConfirmarDanger from "@pages/ConfirmarDanger";
import ResumenVenta from "@pages/funeraria/ventas_generales/ResumenVenta";
export default {
    name: "CancelarVentaGeneral",
    components: {
        Password,
        "v-select": vSelect,
        ConfirmarDanger,
        ResumenVenta,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        id_venta: {
            type: Number,
            required: true,
            default: 0,
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    this.form.cantidad = "0.00";
                    this.$vs.loading();
                    try {
                        let res = await funeraria.get_ventas_gral(
                            null,
                            false,
                            this.getVentaId
                        );
                        this.datosVenta = res.data[0];
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
                            } else {
                                this.$vs.notify({
                                    title: "Error",
                                    text: "Ha ocurrido un error al tratar de cargar la información solicitada, por favor reintente.",
                                    iconPack: "feather",
                                    icon: "icon-alert-circle",
                                    color: "danger",
                                    position: "bottom-right",
                                    time: "9000",
                                });
                            }
                        }

                        this.cerrarVentana();
                    }
                    this.$popupManager.register(
                        this,
                        this.cancelar,
                        "comentario"
                    );
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
    },
    computed: {
        motivo_computed: function () {
            return this.form.motivo.value;
        },
        getVentaId: {
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
            botonConfirmarSinPassword: "",
            accionConfirmarSinPassword: "",
            callBackConfirmar: Function,
            openConfirmarSinPassword: false,
            callback: Function,
            openConfirmar: false,
            errores: [],
            datosVenta: [],
            motivos: [
                {
                    label: "FALTA DE PAGO",
                    value: 1,
                },
                {
                    label: "A PETICIÓN DEL CLIENTE",
                    value: 2,
                },
                {
                    label: "ERROR AL CAPTURAR",
                    value: 3,
                },
            ],
            form: {
                venta_id: "",
                motivo: {
                    label: "FALTA DE PAGO",
                    value: 1,
                },
                cantidad: "0.00",
                comentario: "",
            },
        };
    },
    methods: {
        acceptAlert() {
            this.$validator
                .validateAll()
                .then((result) => {
                    if (!result) {
                        this.$vs.notify({
                            title: "Error",
                            text: "Verifique que todos los datos han sido capturados",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "4000",
                        });
                        return;
                    } else {
                        (async () => {
                            this.callback = await this.cancelar_venta;
                            this.openConfirmar = true;
                        })();
                    }
                })
                .catch(() => {});
        },

        cancelar() {
            this.botonConfirmarSinPassword = "Salir y limpiar";
            this.accionConfirmarSinPassword =
                "Esta acción limpiará los datos que capturó en el formulario.";
            this.openConfirmarSinPassword = true;
            this.callBackConfirmar = this.cerrarVentana;
        },
        cerrarVentana() {
            this.openConfirmarSinPassword = false;
            this.form.comentario = "";
            this.form.cantidad = 0;
            (this.form.motivo = {
                label: "FALTA DE PAGO",
                value: 1,
            }),
                this.$emit("closeCancelarVenta");
            return;
        },

        async cancelar_venta() {
            this.errores = [];
            this.$vs.loading();
            this.form.venta_id = this.getVentaId;
            try {
                let res = await funeraria.cancelar_venta_gral(this.form);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Ventas en Gral.",
                        text: "Se ha cancelado la venta correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.$emit("closeCancelarVenta", res.data);
                            this.$emit("ConsultarVenta", res.data);
                        }, 50);
                    });
                } else {
                    this.$vs.notify({
                        title: "Ventas en Gral.",
                        text: "Error al cancelar la venta, por favor reintente.",
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
                        if (this.errores.venta_id) {
                            //la propiedad esa ya ha sido vendida
                            this.$vs.notify({
                                title: "Seleccione una venta",
                                text: "No se ha seleccionado una venta a cancelar",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                time: 5000,
                            });
                        }

                        this.$vs.notify({
                            title: "Cancelar Venta",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        //este error es por alguna condicion que el contrano no cumple para modificar
                        //la propiedad esa ya ha sido vendida
                        this.$vs.notify({
                            title: "Cancelar información de la venta",
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
