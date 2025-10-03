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
            console.log("Form is valid:", this.formData);
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
