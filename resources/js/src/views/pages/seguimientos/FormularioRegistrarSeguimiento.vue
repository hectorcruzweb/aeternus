<template>
    <div>
        <vs-popup
            :class="['forms-popup popup-50', z_index]"
            fullscreen
            close="cancelar"
            title="Programar Seguimiento"
            :active.sync="showVentana"
            ref="formulario"
        >
        </vs-popup>
    </div>
</template>
<script>
import PopupManager from "@/utils/PopupManager";
export default {
    // Name of the component (optional)
    name: "FormularioProgramarSeguimiento",
    // Props: data passed from parent
    props: {
        show: {
            type: Boolean,
            required: true
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index54k"
        }
    },
    // Computed properties: derived reactive data
    computed: {
        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            }
        }
    },
    watch: {
        show(newVal) {
            // Only listen when visible = true
            if (newVal) {
                PopupManager.register(this.$options.name, this.cancelar);
            } else {
                PopupManager.unregister(this.$options.name);
            }
        }
    },
    // Data function returns the component's reactive state
    data() {
        return {};
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        cancelar() {
            this.limpiarVentana();
            this.$emit("closeVentana");
        },
        limpiarVentana() {}
    },
    // Lifecycle hooks
    created() {
        console.log("Component created!"); // reactive data is ready, DOM not yet
    },
    mounted() {
        console.log("Component mounted!"); // DOM is ready
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
            this.cancelar();
        };
    },
    beforeDestroy() {
        PopupManager.unregister(this.$options.name);
    },
    destroyed() {
        console.log("Component destroyed!"); // reactive data is ready, DOM not yet
    }
};
</script>
