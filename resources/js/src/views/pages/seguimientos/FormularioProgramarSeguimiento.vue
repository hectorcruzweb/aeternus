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
            <div class="alerta">
                <div class="w-full info">
                    <h3>
                        Datos del Cliente
                        <span
                            v-if="filters.operacion_id"
                            class="uppercase text-primary"
                        >
                            ({{ operacion_descripcion }})
                        </span>
                        <span v-else class="text-danger">
                            (No ha seleccionado ninguna operación es específico)
                        </span>
                    </h3>
                    <p>
                        <span class="font-medium">Clave: </span>
                        {{ cliente.id }},
                        <span class="font-medium">Nombre: </span>
                        {{ cliente.nombre }},
                        <span class="font-medium">Dirección: </span>
                        {{ cliente.direccion_completa }}.
                    </p>
                </div>
            </div>

            <div class="form-group">
                <div class="title-form-group">Datos del Cliente</div>
                <div class="form-group-content">
                    <!-- Contenido Formulario -->
                    <ProgramarSeguimientoDatos></ProgramarSeguimientoDatos>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import clientes from "../../../services/clientes";
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
        filters: {
            type: Object,
            required: false,
            default: {
                cliente_id: null,
                tipo_cliente_id: null,
                operacion_id: null,
            },
        },
        tipo: {
            type: String,
            required: true,
            default: "agregar", //agregar, modificar, consulta
        },
    },
    // Computed properties: derived reactive data
    computed: {},
    watch: {
        async show(newVal) {
            // Only listen when visible = true
            if (newVal) {
                this.$popupManager.register(this.$options.name, this.cancelar);
                await this._fetchData();
                this.localShow = true;
            } else {
                this.$popupManager.unregister(this.$options.name);
                this.localShow = false;
            }
        },
    },
    // Data function returns the component's reactive state
    data() {
        return {
            localShow: false, // controls popup visibility
            cliente: {
                id: "",
                nombre: "",
                direccion_completa: "",
            },
            operacion_descripcion: "",
            //Datos del Formulario
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        async _fetchData() {
            if (!this.show) return; // stop here if not visible
            const params = {
                id: this.filters.cliente_id,
                filtro_especifico: this.filters.tipo_cliente_id,
                filtrar_x_operaciones: 1,
            };
            this.$vs.loading();
            try {
                // Call the API from clientes service
                const result = await clientes.fetchClientes(params);
                const data = result.length ? result[0] : result;
                if (data) {
                    this.cliente.id = data.id;
                    this.cliente.nombre = data.nombre;
                    this.cliente.direccion_completa = data.direccion_completa;
                    if (this.filters.operacion_id) {
                        //buscar la operacion del id correspondiente
                        const match = data.operaciones.find(
                            (operacion) =>
                                operacion.operacion_id ===
                                this.filters.operacion_id
                        );
                        if (match) {
                            this.operacion_descripcion = match.descripcion;
                            console.log("asignado");
                        } else {
                            console.log("no match found");
                        }
                    }
                }
            } catch (error) {
                console.error("Error fetching clientes:", error);
                this.cancelar();
            } finally {
                this.$vs.loading.close();
            }
        },
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
