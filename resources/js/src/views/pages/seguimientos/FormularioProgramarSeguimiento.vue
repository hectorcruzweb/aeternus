<template>
    <div>
        <vs-popup
            :class="['forms-popup popup-70', z_index]"
            close="cancelar"
            title="Programar Seguimiento"
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
                            <div class="highlighted-inputs-blue">
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
                        ></ProgramarSeguimientoDatos>
                        <div
                            class="flex flex-wrap items-center justify-between pr-2 pt-4"
                        >
                            <vs-checkbox
                                color="success"
                                class="size-small text-info"
                                v-model="formData.enviar_x_email"
                                :vs-value="formData.enviar_x_email"
                                >¿Enviar por correo electrónico?</vs-checkbox
                            >
                            <vs-button
                                class=""
                                color="success"
                                @click="submitForm"
                            >
                                Registrar
                            </vs-button>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
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
            default: "z-index55k",
        },
        filters: {
            type: Object,
            required: false,
            default: {
                cliente_id: null,
                tipo_cliente_id: null,
                operacion_id: null,
            },
        },
        tipo: {
            type: String,
            required: true,
            default: "agregar", //agregar, modificar, consulta
        },
    },
    // Computed properties: derived reactive data
    computed: {},
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    this.$popupManager.register(
                        this.$options.name,
                        this.cancelar
                    );
                } else {
                    this.$popupManager.unregister(this.$options.name);
                    this.localShow = false;
                }
            },
        },
    },
    // Data function returns the component's reactive state
    data() {
        return {
            localShow: false, // controls popup visibility
            //Datos del Formulario
            formData: {
                fecha_a_contactar: "",
                enviar_x_email: false,
                motivo: { label: "Seleccione 1", value: "" },
                medio: { label: "Seleccione 1", value: "" },
                email: "",
                comentario_programado: "",
            },
            errores: [], //from backend
            ready: {
                InfoOperacion: false,
                ProgramarSeguimientoDatos: false,
            },
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        // Called when InfoOperacion is ready
        resultado_datos_cliente(success) {
            if (success) {
                this.ready.InfoOperacion = true;
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
                this.localShow = true;
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
        async submitForm() {
            this.errores = [];
            /*
            const isValid = await this.$refs.seguimientoForm.validate();
            if (!isValid) {
                this.$vs.notify({
                    title: "Error",
                    text: "Por favor corrige los errores antes de continuar.",
                    color: "danger",
                });
                return;
            }
            // ✅ Continue submit logic here
            console.log("Form is valid:", this.formData);*/
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
                console.log("Success:", response);
                this.$vs.notify({
                    title: "Éxito",
                    text: "Seguimiento programado correctamente",
                    color: "success",
                });
            } catch (err) {
                console.log("🚀 ~ submitForm ~ err:", err);
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
        console.log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        console.log("Component mounted! " + this.$options.name); // DOM is ready
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
        console.log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style scoped></style>
