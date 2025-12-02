<template>
    <div class="inventario" v-if="data.total_articulos !== undefined">
        <div class="seccion-title">
            <h2 class="">Estado del Inventario</h2>
            <div class="">
                <p class="text-white btn-text" @click="downloadExcel">Reporte General</p>
            </div>
        </div>
        <div class="reportes">
            <div class="lista">
                <div class="reporte">
                    <div class="imagen bg-primary-400">
                        <img class="" src="@assets/images/coffin.svg" />
                    </div>
                    <div>
                        <p class="cantidad"><count-to :start-val="0" :end-val="data.total_articulos" :duration="2000" />
                        </p>
                        <p class="descripcion">Conceptos
                        </p>
                    </div>
                </div>
                <div class="reporte">
                    <div class="imagen bg-danger-400">
                        <img class="" src="@assets/images/low.svg" />
                    </div>
                    <div>
                        <p class="cantidad"><count-to :start-val="0" :end-val="data.articulos_bajo_minimo"
                                :duration="2000" /></p>
                        <p class="descripcion btn-text info" @click="downloadExcel('low')">Desabastecidos</p>
                    </div>
                </div>
                <div class="reporte">
                    <div class="imagen bg-warning-400">
                        <img class="" src="@assets/images/alto.svg" />
                    </div>
                    <div>
                        <p class="cantidad"><count-to :start-val="0" :end-val="data.articulos_sobre_maximo"
                                :duration="2000" /></p>
                        <p class="descripcion btn-text info" @click="downloadExcel('over')">Sobreabastecido</p>
                    </div>
                </div>
                <div class="reporte">
                    <div class="imagen bg-success-400">
                        <img class="" src="@assets/images/dollar_bill.svg" />
                    </div>
                    <div>
                        <p class="cantidad">$ <count-to :start-val="0" :end-val="data.costo_total_inventario"
                                :duration="2000" /> MXN</p>
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
import inventario from '../../services/inventario';

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
        async downloadExcel(filtro = "all") {
            this.$vs.loading();

            try {
                const res = await inventario.descargarExcel(filtro);

                let filename = "";

                switch (filtro) {
                    case "low":
                        filename = "inventario_desabastecidos";
                        break;
                    case "over":
                        filename = "inventario_sobreabastecido";
                        break;
                    default:
                        filename = "inventario_general";
                }
                this.$downloadFileExcel(res.data, `${filename} ${this.$fechaHora()}`);
            } catch (err) {
                this.$error("🚀 ~ err:", err)
            } finally {
                this.$vs.loading.close();
            }
        }
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
