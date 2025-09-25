<template>
    <div class="centerx">
        <vs-popup
            :class="['forms-popup popup-90', z_index]"
            fullscreen
            title="Catálogo de clientes"
            :active.sync="showVentana"
            :ref="this.$options.name"
        >
            <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
                <vx-card
                    no-radius
                    title="Filtros de selección"
                    refresh-content-action
                    @refresh="reset"
                    :collapse-action="false"
                >
                    <div class="flex flex-wrap pb-6">
                        <div class="w-full sm:w-6/12 lg:w-3/12 input-text px-2">
                            <label>Filtrar x Tipo de Clientes</label>
                            <v-select
                                :options="filtrosEspecificos"
                                v-model="serverOptions.filtro_especifico"
                                :clearable="false"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                class="w-full"
                                data-vv-as=" "
                            >
                                <div slot="no-options">Seleccione 1</div>
                            </v-select>
                        </div>

                        <div class="w-full sm:w-6/12 lg:w-3/12 input-text px-2">
                            <label>Núm. Cliente</label>
                            <vs-input
                                v-model="serverOptions.id"
                                name="num_cliente"
                                data-vv-as=" "
                                type="text"
                                class="w-full"
                                placeholder="Ej. 1258"
                                maxlength="6"
                            />
                            <span class=""></span>
                        </div>
                        <div class="w-full lg:w-6/12 input-text px-2">
                            <label>Nombre del Cliente</label>
                            <vs-input
                                v-model="serverOptions.nombre"
                                name="nombre_cliente"
                                data-vv-as=" "
                                type="text"
                                class="w-full"
                                placeholder="Ej. Juán Pérez"
                                maxlength="150"
                            />
                            <span class=""></span>
                        </div>
                    </div>
                </vx-card>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import vSelect from "vue-select";
export default {
    // Name of the component (optional)
    name: "ClientesSearcherSeguimientos",
    components: { "v-select": vSelect },
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
        return {
            filtrosEspecificos: [
                {
                    label: "Listar Todos",
                    value: "",
                },
                {
                    label: "Clientes con operaciones",
                    value: "1",
                },
                {
                    label: "Clientes en cotización",
                    value: "2",
                },
            ],

            serverOptions: {
                page: "",
                per_page: "",
                filtro_especifico: {
                    label: "Listar Todos",
                    value: "",
                },
                id: "",
                nombre: "",
            },
        };
    },
    methods: {
        reset(card) {
            card.removeRefreshAnimation(500);
        },
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
