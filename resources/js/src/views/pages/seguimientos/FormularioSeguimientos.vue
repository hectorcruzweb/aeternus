<template>
    <!-- HTML goes here -->
    <div>
        <vs-popup
            :class="['forms-popup popup-90', z_index]"
            fullscreen
            close="cancelar"
            title="Control de Seguimientos"
            :active.sync="showVentana"
            ref="formulario"
        >
        </vs-popup>
    </div>
</template>

<script>
export default {
    // Name of the component (optional)
    name: "MyComponent",

    // Data function returns the component's reactive state
    data() {
        return {};
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
        //cerrando el confirmar con esc
        document.body.addEventListener("keyup", e => {
            if (e.key === "Escape" && this.showVentana) {
                this.cancelar();
                console.log("pressed");
            }
        });
    },
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
    }
};
</script>
