<template>
    <div>
        <vs-popup :class="['forms-popup popup-70', z_index]" fullscreen close="cancelar" :title="popupTitle"
            :active="localShow" :ref="this.$options.name">
            <div class="pb-4">
                {{ filters }} / {{ tipo }}

            </div>
        </vs-popup>
    </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import seguimientos from "../../../services/seguimientos";
import ProgramarSeguimientoDatos from "./ProgramarSeguimientoDatos.vue";
import InfoOperacion from "./InfoOperacion.vue";
import vSelect from "vue-select";
export default {
    // Name of the component (optional)
    name: "FormularioRegistrarSeguimiento",
    components: {
        "v-select": vSelect,
        ProgramarSeguimientoDatos,
        InfoOperacion,
        Password
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
            default: {},
        },
        tipo: {
            type: String,
            required: true,
            default: "agregar", //agregar, modificar, consultar, cancelar, atender_seguimiento_programado
        },
    },
    // Computed properties: derived reactive data
    computed: {
        isReadOnly() {
            return this.tipo !== "agregar" && this.tipo !== "modificar" && this.tipo !== "cancelar";
        },
        verEnviarEmailChk() {
            if (this.tipo === 'cancelar') {
                return this.formData.email ? true : false;
            } else
                return true;
        },
        popupTitle() {
            switch (this.tipo) {
                case 'agregar':
                    return 'Registrar Seguimiento';
                case 'modificar':
                    return 'Modificar Seguimiento';
                case 'consultar':
                    return 'Consultar Seguimiento';
                case 'cancelar':
                    return 'Cancelar Seguimiento';
                case 'atender_seguimiento_programado':
                    return 'Atender Seguimiento Programado';
                default:
                    return 'N/A';
            }
        },
        successTextRespnse() {
            switch (this.tipo) {
                case 'agregar':
                    return 'Seguimiento programado correctamente';
                case 'modificar':
                    return 'Seguimiento actualizado correctamente';
                case 'cancelar':
                    return 'Seguimiento cancelado correctamente';
                default:
                    return 'N/A';
            }
        },
        buttonTitle() {
            switch (this.tipo) {
                case 'modificar':
                    return 'Modificar Seguimiento';
                case 'cancelar':
                    return 'Cancelar Seguimiento';
                default:
                    return 'Programar Seguimiento';
            }
        },
        buttonColor() {
            switch (this.tipo) {
                case 'cancelar':
                    return 'danger';
                default:
                    return 'success';
            }
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    this.$popupManager.register(this, this.cancelar);
                    this.localShow = true;
                    //i have to load data if not 'agregar'
                    /*if (this.tipo != 'agregar') {
                        this.formData.seguimiento_id = this.filters.seguimiento_id;
                        await this._loadSeguimientosDatos();
                    }*/
                }
            },
        },
    },
    // Data function returns the component's reactive state
    data() {
        return {
            localShow: false, // controls popup visibility
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        cancelar() {
            this.limpiarVentana();
            this.$emit("closeVentana");
        },
        limpiarVentana() { },

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
