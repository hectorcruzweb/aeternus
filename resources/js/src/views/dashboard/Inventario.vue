<template>
    <div class="inventario" v-if="data.length > 0">
        <div class="seccion-title">
            <h2 class="">Estado del Inventario</h2>
            <div class="">
                <span class="text-white">Reporte General</span>
            </div>
        </div>
        <div class="reportes">
            <div class="lista">
                <div class="reporte">
                    <div class="imagen bg-primary-400">
                        <img class="" src="@assets/images/coffin.svg" />
                    </div>
                    <div>
                        <p class="cantidad">150</p>
                        <p class="descripcion">Conceptos</p>
                    </div>
                </div>
                <div class="reporte">
                    <div class="imagen bg-danger-400">
                        <img class="" src="@assets/images/low.svg" />
                    </div>
                    <div>
                        <p class="cantidad">150</p>
                        <p class="descripcion">Desabastecido</p>
                    </div>
                </div>
                <div class="reporte">
                    <div class="imagen bg-warning-400">
                        <img class="" src="@assets/images/alto.svg" />
                    </div>
                    <div>
                        <p class="cantidad">150</p>
                        <p class="descripcion">Sobrestock</p>
                    </div>
                </div>
                <div class="reporte">
                    <div class="imagen bg-success-400">
                        <img class="" src="@assets/images/dollar_bill.svg" />
                    </div>
                    <div>
                        <p class="cantidad">$150</p>
                        <p class="descripcion">$Costo Total</p>
                    </div>
                </div>
            </div>
            <div class="imagen">
                <img class="" src="@assets/images/conserviciosfunerarios.svg" />
            </div>
        </div>
    </div>
    <div v-else class="skeleton">
        <p>Obteniendo información del inventario.</p>
    </div>
</template>
<script>
export default {
    // Name of the component (optional)
    name: "Inventario(dashboard)",
    components: {
    },
    // Props: data passed from parent
    props: {
        data: {
            type: Array,
            required: true
        }
    },
    watch: {},
    // Computed properties: derived reactive data
    computed: {},
    // Data function returns the component's reactive state
    data() {
        return {
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {

    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style lang="scss" scoped></style>
