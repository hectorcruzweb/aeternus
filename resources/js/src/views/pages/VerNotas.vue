<template>
    <div class="centerx">
        <vs-popup
            :class="['forms-popup', z_index]"
            close="cancelar"
            :title="getTitle"
            :active="localShow"
            :ref="this.$options.name"
        >
            <div class="p-8">
                {{ getNota }}
            </div>
        </vs-popup>
    </div>
</template>
<script>
export default {
    name: "VerNotas",
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        nota: {
            type: String,
            required: false,
            default: "",
        },
        title: {
            type: String,
            required: false,
            default: "",
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index58k",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    this.$popupManager.register(this, this.cerrar, null);
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
    },
    data() {
        return {
            localShow: false,
        };
    },
    computed: {
        getNota: {
            get() {
                return this.nota;
            },
            set(newValue) {
                return newValue;
            },
        },
        getTitle: {
            get() {
                return this.title;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    methods: {
        cerrar() {
            this.$emit("closeVerNotas");
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
