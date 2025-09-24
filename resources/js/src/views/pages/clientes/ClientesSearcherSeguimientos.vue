<template>
    <div class="centerx">
        <vs-popup
            :class="['forms-popup popup-70', z_index]"
            fullscreen
            title="Catálogo de clientes registrados"
            :active.sync="showVentana"
            :ref="this.$options.name"
        >
        </vs-popup>
    </div>
</template>
<script>
export default {
    // Name of the component (optional)
    name: "ClientesSearcherSeguimientos",
    components: {},
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
    },
    watch: {
        show(newVal) {
            // Only listen when visible = true
            if (newVal) {
                this.$popupManager.register(this.$options.name, this.cancelar);
            } else {
                this.$popupManager.unregister(this.$options.name);
            }
        },
    },
    computed: {
        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    data() {
        return {};
    },
    methods: {
        cancelar() {
            this.$emit("closeVentana");
        },
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
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        console.log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
