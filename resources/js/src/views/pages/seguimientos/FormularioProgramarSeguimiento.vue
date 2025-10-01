script
<template>
    <div>
        <vs-popup
            :class="['forms-popup popup-70', z_index]"
            fullscreen
            close="cancelar"
            title="Programar Seguimiento"
            :active="localShow"
            :ref="this.$options.name"
        >
            <ProgramarSeguimientoDatos
                :cliente="cliente"
                :operacion="operacion"
            ></ProgramarSeguimientoDatos>
        </vs-popup>
    </div>
</template>
<script>
import PopupManager from "@/utils/PopupManager";
import ProgramarSeguimientoDatos from "./ProgramarSeguimientoDatos.vue";
export default {
    // Name of the component (optional)
    name: "FormularioProgramarSeguimiento",
    components: {
        ProgramarSeguimientoDatos,
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
            default: "z-index54k",
        },
        cliente: {
            type: Object,
            required: true,
            default: null,
        },
        operacion: {
            type: Object,
            required: true,
            default: null,
        },
        tipo: {
            type: String,
            required: true,
            default: "agregar", //agregar, modificar, registrar
        },
    },
    // Computed properties: derived reactive data
    computed: {},
    watch: {
        async show(newVal) {
            // Only listen when visible = true
            if (newVal) {
                this.$popupManager.register(this.$options.name, this.cancelar);
                this.localShow = true;
            } else {
                this.$popupManager.unregister(this.$options.name);
                this.resetData();
                this.localShow = false;
            }
        },
    },
    // Data function returns the component's reactive state
    data() {
        return {
            localShow: false, // controls popup visibility
            //Datos del Formulario
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        cancelar() {
            this.resetData();
            this.$emit("closeVentana");
        },
        resetData() {},
    },
    // Lifecycle hooks
    created() {
        console.log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        console.log("Component mounted! " + this.$options.name); // DOM is ready
        this.$refs[this.$options.name].$el.querySelector(".vs-icon").onclick =
            () => {
                this.cancelar();
            };
    },
    beforeDestroy() {
        PopupManager.unregister(this.$options.name);
    },
    destroyed() {
        console.log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
