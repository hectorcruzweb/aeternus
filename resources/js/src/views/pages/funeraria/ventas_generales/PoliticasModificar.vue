<template>
    <div class="centerx">
        <vs-popup
            :class="['confirm-form width-auto popup-40', z_index]"
            title="Modificar Ventas en General"
            :active="localShow"
            :ref="this.$options.name"
        >
            <div class="flex flex-col justify-evenly text-center">
                <h5 class="text-primary uppercase title">
                    Políticas de Modificación
                </h5>
                <p class="content-text">
                    Para poder modificar una venta en general, debe de estar
                    <span class="text-primary">Vigente</span> y no haber sido
                    marcada como <span class="text-primary">Entregada</span>.
                    <span v-if="!permisoModificar"
                        >Esta venta
                        <span class="text-danger" v-if="!permisoModificar"
                            >NO</span
                        >
                        cumple con estas características.</span
                    >
                </p>
                <div class="items-center justify-center">
                    <vs-button
                        class="sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                        color="success"
                        @click="aceptar()"
                    >
                        <span>Entendido</span>
                    </vs-button>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
export default {
    name: "VerPoliticasDeModificarVenta",
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        permiso_modificar: {
            type: Boolean,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index100k",
        },
    },

    data() {
        return {
            localShow: false,
        };
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    this.$popupManager.register(this, this.aceptar, null);
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
    },
    computed: {
        permisoModificar: {
            get() {
                return this.permiso_modificar;
            },
            set(newValue) {
                return newValue;
            },
        },
    },
    methods: {
        aceptar() {
            this.$nextTick(() => {
                setTimeout(() => {
                    this.$emit("closeForm", this.permisoModificar);
                }, 50);
            });
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
