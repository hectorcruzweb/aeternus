<template>
    <div class="centerx">
        <vs-popup class="forms-popup popup-90" title="Entregar entrega en General" :active.sync="showVentana"
            ref="entregar_venta">
            <div class="flex flex-wrap">
                <div class="w-full">
                    <ResumenVenta :id_venta="getVentaId" @showNoInvetario="showNoInvetario"></ResumenVenta>
                    <div class="flex flex-wrap py-6">
                        <div class="w-full form-group">
                            <div class="title-form-group">
                                Datos de la entrega
                            </div>
                            <div class="form-group-content">
                                <div class="w-full xl:w-12/12 pt-6">
                                    <div class="flex flex-wrap">
                                        <div class="w-full xl:w-4/12 input-text px-2 large-size">
                                            <label>
                                                Fecha de Entrega
                                                <span>(*)</span>
                                            </label>
                                            <flat-pickr name="fecha_entrega" data-vv-as=" "
                                                v-validate:fecha_entrega_validacion_computed.immediate="'required'
                                                    " :config="configdateTimePickerFullMonth"
                                                v-model="form.fecha_entrega" placeholder="Fecha de la entrega"
                                                class="w-full" />
                                            <span>
                                                {{ errors.first("fecha_entrega") }}
                                            </span>
                                            <span v-if="this.errores.fecha_entrega">{{
                                                errores.fecha_entrega[0]
                                            }}</span>
                                        </div>
                                        <div class="w-full xl:w-8/12 input-text px-2 ">
                                            <label>Responsable de entrega:</label>
                                            <v-select disabled :options="entregadores" :clearable="false"
                                                v-model="form.entregador" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                class="w-full large_select" name="entregador_entrega" data-vv-as=" ">
                                                <div slot="no-options">
                                                    No Se Ha Seleccionado Ningún Motivo
                                                </div>
                                            </v-select>
                                            <span>{{ errors.first("entregador") }}</span>
                                            <span v-if="this.errores['entregador.value']">{{
                                                errores["entregador.value"][0]
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full input-text px-2">
                                    <label>Agregue un comentario respecto a la entrega de esta
                                        entrega:</label>
                                    <vs-textarea class="w-full" label="Detalle de la entrega..." height="120px"
                                        v-model="form.comentario" ref="comentario" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-buttons-section">
                <div class="text-advice">
                    <span class="ojo-advice">Ojo:</span>
                    Por favor revise la información ingresada, si todo es correcto de
                    click en el "Botón de Abajo”.
                </div>
                <div class="w-full">
                    <vs-button class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                        @click="acceptAlert()">
                        <span>Guardar Entrega</span>
                    </vs-button>
                </div>
            </div>
            <Password :show="openConfirmar" :callback-on-success="callback" @closeVerificar="openConfirmar = false"
                :accion="'Marcar Venta Como Entregada'"></Password>
            <ConfirmarDanger :z_index="'z-index58k'" :show="openConfirmarSinPassword"
                :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
                :accion="accionConfirmarSinPassword" :confirmarButton="botonConfirmarSinPassword"></ConfirmarDanger>

        </vs-popup>
    </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import vSelect from "vue-select";
import funeraria from "@services/funeraria";
import ConfirmarDanger from "@pages/ConfirmarDanger";
import ResumenVenta from "@pages/funeraria/ventas_generales/ResumenVenta";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
/**VARIABLES GLOBALES */
import { configdateTimePickerFullMonth } from "@/VariablesGlobales";
export default {
    components: {
        Password,
        "v-select": vSelect,
        ConfirmarDanger,
        ResumenVenta,
        flatPickr
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
        show: function (newValue, oldValue) {
            if (newValue == true) {
                this.$refs["entregar_venta"].$el.querySelector(".vs-icon").onclick =
                    () => {
                        this.cancelar();
                    };
                this.$nextTick(() => {
                    this.$refs["comentario"].$el.querySelector("textarea").focus();
                });

                (async () => {
                    let activeUserInfo = JSON.parse(localStorage.getItem("userInfo"));
                    this.entregadores.push({ value: activeUserInfo.user_id, label: activeUserInfo.nombre });
                    this.form.entregador = this.entregadores[0];
                })();
            }
        },
    },
    computed: {
        fecha_entrega_validacion_computed: function () {
            return this.form.fecha_entrega;
        },
        motivo_computed: function () {
            return this.form.motivo.value;
        },
        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            },
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
            configdateTimePickerFullMonth: configdateTimePickerFullMonth,
            botonConfirmarSinPassword: "",
            accionConfirmarSinPassword: "",
            callBackConfirmar: Function,
            openConfirmarSinPassword: false,
            callback: Function,
            openConfirmar: false,
            errores: [],
            entregadores: [],
            datosVenta: [],
            form: {
                fecha_entrega: '',
                entregador: {},
                venta_id: "",
                comentario: "",
            },
        };
    },
    methods: {
        showNoInvetario() {
            this.$vs.notify({
                title: "Entrega de Ventas",
                text: "Verifique los artículos sin existencia de esta venta.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 5000,
            });
        },
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
                            this.callback = await this.entregar_venta;
                            this.openConfirmar = true;
                        })();
                    }
                })
                .catch(() => { });
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
            this.form.entregadores = [];
            this.entregadores.push({ value: "", label: "Seleccione 1" });
            this.form.entregador = this.entregadores[0]
            this.form.fecha_entrega = ""
            this.$emit("closeEntregarVenta");
            return;
        },
        async entregar_venta() {
            this.errores = [];
            this.$vs.loading();
            this.form.venta_id = this.getVentaId;
            try {
                let res = await funeraria.entregar_venta_gral(this.form);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Ventas en Gral.",
                        text: "Se ha registrado la entrega correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.cerrarVentana();
                    this.$emit("ConsultarVenta", res.data);
                } else {
                    this.$vs.notify({
                        title: "Ventas en Gral.",
                        text: "Error al registrar la entrega, por favor reintente.",
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
                                title: "Seleccione una venta para entregar entrega",
                                text: "No se ha seleccionado una venta a entregar",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                time: 5000,
                            });
                        }

                        this.$vs.notify({
                            title: "Registrar entrega",
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
                            title: "Registrar entrega",
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
    created() { },
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
