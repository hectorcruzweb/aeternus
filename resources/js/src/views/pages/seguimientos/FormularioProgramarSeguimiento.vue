<template>
    <div>
        <vs-popup
            :class="['forms-popup popup-70', z_index]"
            close="cancelar"
            :title="popupTitle"
            :active="localShow"
            :fullscreen="false"
            :ref="this.$options.name"
        >
            <div class="pb-4">
                <div class="form-group">
                    <div class="title-form-group">
                        Datos del Seguimiento Programado
                    </div>
                    <div class="form-group-content">
                        <div class="px-2 mb-2">
                            <div class="highlighted-inputs-primary">
                                <span
                                    v-if="
                                        this.errores.cliente_id ||
                                        this.errores.tipo_cliente_id
                                    "
                                    class="text-danger text-sm block"
                                    >Verifique que el cliente seleccionado sea
                                    válido.</span
                                >
                                <span
                                    v-if="this.errores.operacion_id"
                                    class="text-danger text-sm block"
                                    >{{ errores.operacion_id[0] }}</span
                                >
                                <InfoOperacion
                                    ref="InfoOperacion"
                                    v-if="show"
                                    :filters="filters"
                                    @resultado="resultado_datos_cliente"
                                >
                                </InfoOperacion>
                            </div>
                        </div>
                        <!-- Contenido Formulario -->
                        <ProgramarSeguimientoDatos
                            ref="seguimientoForm"
                            v-model="formData"
                            :errores="errores"
                            @resultado="resultado_seguimientos_datos"
                            :tipo="tipo"
                        ></ProgramarSeguimientoDatos>
                        <div
                            v-if="!isReadOnly"
                            class="flex flex-wrap items-center justify-between pr-2 pt-4"
                        >
                            <vs-checkbox
                                v-show="verEnviarEmailChk"
                                color="success"
                                class="size-small text-info"
                                v-model="formData.enviar_x_email"
                                :vs-value="formData.enviar_x_email"
                                >¿Enviar por correo electrónico?</vs-checkbox
                            >
                            <vs-button
                                ref="programar_seguimiento_button"
                                class="ml-auto"
                                :color="buttonColor"
                                @click="EnviarForm"
                            >
                                {{ buttonTitle }}
                            </vs-button>
                        </div>
                    </div>
                </div>
            </div>
            <Password
                v-if="openPassword"
                :show="openPassword"
                :callback-on-success="callback"
                @closeVerificar="openPassword = false"
                :accion="accionNombre"
            >
            </Password>
        </vs-popup>
    </div>
</template>
<script>
//componente de password
import Password from "@pages/confirmar_password";
import seguimientos from "../../../services/seguimientos";
import ProgramarSeguimientoDatos from "./ProgramarSeguimientoDatos.vue";
import InfoOperacion from "./InfoOperacion.vue";
import vSelect from "vue-select";
export default {
    // Name of the component (optional)
    name: "FormularioProgramarSeguimiento",
    components: {
        "v-select": vSelect,
        ProgramarSeguimientoDatos,
        InfoOperacion,
        Password,
    },
    // Props: data passed from parent
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index59k",
        },
        filters: {
            type: Object,
            required: false,
            default: {
                cliente_id: null,
                tipo_cliente_id: null,
                operacion_id: null,
                seguimiento_id: null, //to modify
            },
        },
        tipo: {
            type: String,
            required: true,
            default: "agregar", //agregar, modificar, consultar, cancelar
        },
    },
    // Computed properties: derived reactive data
    computed: {
        isReadOnly() {
            return (
                this.tipo !== "agregar" &&
                this.tipo !== "modificar" &&
                this.tipo !== "cancelar"
            );
        },
        verEnviarEmailChk() {
            if (this.tipo === "cancelar") {
                return this.formData.email ? true : false;
            } else return true;
        },
        popupTitle() {
            switch (this.tipo) {
                case "agregar":
                    return "Programar Seguimiento";
                case "modificar":
                    return "Modificar Seguimiento Programado";
                case "consultar":
                    return "Consultar Seguimiento Programado";
                case "cancelar":
                    return "Cancelar Seguimiento Programado";
                default:
                    return "Programar Seguimiento";
            }
        },
        successTextRespnse() {
            switch (this.tipo) {
                case "agregar":
                    return "Seguimiento programado correctamente";
                case "modificar":
                    return "Seguimiento actualizado correctamente";
                case "cancelar":
                    return "Seguimiento cancelado correctamente";
                default:
                    return "N/A";
            }
        },
        buttonTitle() {
            switch (this.tipo) {
                case "modificar":
                    return "Modificar Seguimiento";
                case "cancelar":
                    return "Cancelar Seguimiento";
                default:
                    return "Programar Seguimiento";
            }
        },
        buttonColor() {
            switch (this.tipo) {
                case "cancelar":
                    return "danger";
                default:
                    return "success";
            }
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    await this.$popupManager.register(
                        this,
                        this.cancelar,
                        "programar_seguimiento_button"
                    );
                    //i have to load data if not 'agregar'
                    if (this.tipo != "agregar") {
                        this.formData.seguimiento_id =
                            this.filters.seguimiento_id;
                        await this._loadSeguimientosDatos();
                    }
                }
            },
        },
    },
    // Data function returns the component's reactive state
    data() {
        return {
            callback: Function,
            accionNombre: "",
            openPassword: false,
            localShow: false, // controls popup visibility
            //Datos del Formulario
            formData: {
                seguimiento_id: null,
                fecha_a_contactar: "",
                enviar_x_email: false,
                motivo: { label: "Seleccione 1", value: "" },
                medio: { label: "Seleccione 1", value: "" },
                motivo_cancelacion: { label: "Seleccione 1", value: "" },
                email: "",
                comentario_programado: "",
                comentario_cancelacion: "",
            },
            errores: [], //from backend
            ready: {
                InfoOperacion: false,
                ProgramarSeguimientoDatos: false,
                loadSeguimientoDatos: false,
            },
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        async _loadSeguimientosDatos() {
            if (!this.show) return; // stop here if not visible
            const params = {
                cliente_id: this.filters.cliente_id,
                tipo_cliente_id: this.filters.tipo_cliente_id,
                programado_b: 1,
                status: 1,
                id: this.filters.seguimiento_id,
            };
            this.$log("🚀 ~ _getSeguimientosProgramados ~ params:", params);
            //this.$vs.loading();
            try {
                // Call the API from seguimientos service
                const result = await seguimientos.getSeguimientos(params);
                this.$log("🚀 ~ _getSeguimientosProgramados ~ result:", result);
                if (
                    !result ||
                    typeof result !== "object" ||
                    Object.keys(result).length === 0
                ) {
                    throw new Error(
                        "Respuesta inválida en al obtener los datos."
                    );
                }
                this.formData.email = result[0].email_programado;
                this.formData.comentario_programado =
                    result[0].comentario_programado;
                this.formData.motivo = {
                    label: result[0].motivo_texto,
                    value: result[0].motivo_id,
                };
                this.formData.medio = {
                    label: result[0].medio_texto,
                    value: result[0].medio_preferido_programado_id,
                };
                let fecha_contacto = result[0].fecha_programada.split("-");
                let hora_contacto = result[0].hora_programada.split(":");
                //yyyy-mm-dd hh:mm
                this.formData.fecha_a_contactar = new Date(
                    fecha_contacto[0],
                    fecha_contacto[1] - 1,
                    fecha_contacto[2],
                    hora_contacto[0],
                    hora_contacto[1]
                );
                this.ready.loadSeguimientoDatos = true;
                this.checkReady();
            } catch (error) {
                this.$vs.notify({
                    title: "Error inesperado",
                    text: error,
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    time: 5000,
                });
                this.ready.loadSeguimientoDatos = false;
                this.$error("Error fetching seguimientos:", error);
                this.cancelar();
            } finally {
                //this.$vs.loading.close();
            }
        },
        // Called when InfoOperacion is ready
        resultado_datos_cliente(success, email) {
            if (success) {
                this.ready.InfoOperacion = true;
                if (this.tipo == "agregar") {
                    this.formData.email = email;
                }
                this.checkReady();
            } else {
                this.cancelar();
            }
        },
        // Called when ProgramarSeguimientoDatos is ready
        resultado_seguimientos_datos(success) {
            if (success) {
                this.ready.ProgramarSeguimientoDatos = true;
                this.checkReady();
            } else {
                this.cancelar();
            }
        },
        checkReady() {
            if (
                this.ready.InfoOperacion &&
                this.ready.ProgramarSeguimientoDatos
            ) {
                if (this.tipo == "agregar") this.localShow = true;
                else {
                    if (this.ready.loadSeguimientoDatos) {
                        this.localShow = true;
                    }
                }
            }
        },
        cancelar() {
            this.resetData();
            this.$emit("closeVentana");
        },
        resetData() {
            this.localShow = false;
            this.ready = { info: false, seguimiento: false }; // reset state
        },
        async EnviarForm() {
            this.errores = [];
            const isValid = await this.$refs.seguimientoForm.validate();
            if (!isValid) {
                this.$vs.notify({
                    title: "Error",
                    text: "Por favor corrige los errores antes de continuar.",
                    color: "danger",
                });
                return;
            }
            if (this.tipo === "modificar") {
                this.accionNombre = "Actualizar Seguimiento Programado";
                this.callback = await this.submitForm;
                this.openPassword = true;
                return;
            } else if (this.tipo === "cancelar") {
                this.accionNombre = "Cancelar Seguimiento Programado";
                this.callback = await this.submitForm;
                this.openPassword = true;
                return;
            }
            await this.submitForm();
        },
        async submitForm() {
            // ✅ Continue submit logic here
            this.$vs.loading();
            try {
                // Merge formData + filters into one object
                const payload = {
                    ...this.formData,
                    ...this.filters, // props object
                };
                const response = await seguimientos.programarSeguimiento(
                    this.tipo, //tipo prompt
                    payload
                );
                this.$log("Success:", response);
                let success_text = this.successTextRespnse;
                this.$vs.notify({
                    title: "Éxito",
                    text: success_text,
                    color: "success",
                    time: 5000,
                });
                //close form and call the api to reload the form
                this.$emit("agregar_modificar_success_seguimiento");
            } catch (err) {
                this.$log("🚀 ~ submitForm ~ err:", err);
                const status = err.status || 500;
                if (status === 403) {
                    // Forbidden
                    this.$vs.notify({
                        title: "Permiso denegado",
                        text: "Verifique sus permisos con el administrador del sistema.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "warning",
                        time: 4000,
                    });
                } else if (status === 422) {
                    // Validation errors
                    this.errores =
                        err.data && err.data.error ? err.data.error : {};
                    this.$vs.notify({
                        title: "Validación",
                        text: "Verifique los errores encontrados en los datos.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 5000,
                    });
                } else if (status === 409) {
                    // Business rule conflict
                    this.$vs.notify({
                        title: "Programar seguimientos",
                        text:
                            (err.data && err.data.error) ||
                            "Conflicto en la operación.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 8000,
                    });
                } else {
                    // Fallback error
                    this.$vs.notify({
                        title: "Error inesperado",
                        text:
                            err.message || "Error desconocido en el servidor.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 5000,
                    });
                }
            } finally {
                this.$vs.loading.close();
            }
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
        const icon =
            this.$refs[this.$options.name].$el.querySelector(".vs-icon");
        if (icon) {
            icon.addEventListener("click", (e) => {
                e.preventDefault(); // stop form submission / page reload
                e.stopPropagation(); // stop bubbling if needed
                this.cancelar();
            });
        }
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
