<template>
    <div>
        <!-- Datos del Cliente y Operación Seleccionada -->
        <div class="flex flex-wrap">
            <div class="w-full xl:w-6/12 px-2 input-text">
                <label> Cliente </label>
                <vs-input
                    ref="nombre"
                    name="nombre"
                    type="text"
                    class="w-full"
                    placeholder=""
                    maxlength="100"
                    :readonly="true"
                    v-model="cliente.nombre"
                />
            </div>
            <div class="w-full xl:w-6/12 px-2 input-text">
                <label> Operación Seleccionada </label>
                <vs-input
                    ref="operacion"
                    name="operacion"
                    type="text"
                    class="w-full"
                    placeholder=""
                    maxlength="100"
                    :readonly="true"
                    v-model="operacion_descripcion"
                />
            </div>
            <div class="w-full px-2 input-text">
                <label> Dirección </label>
                <vs-input
                    ref="direccion"
                    name="direccion"
                    type="text"
                    class="w-full"
                    placeholder=""
                    maxlength="100"
                    :readonly="true"
                    v-model="cliente.direccion_completa"
                />
            </div>
        </div>
    </div>
</template>
<script>
import clientes from "../../../services/clientes";
export default {
    // Name of the component (optional)
    name: "InfoOperacion",
    // Props: data passed from parent
    props: {
        filters: {
            type: Object,
            required: true,
            default: {
                cliente_id: null,
                tipo_cliente_id: null,
                operacion_id: null,
            },
        },
    },
    // Computed properties: derived reactive data
    computed: {},
    watch: {
        "filters.cliente_id": {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                if (newVal) {
                    //Cargo los datos del cliente
                    const data = await this._fetchData();
                }
            },
        },
    },
    // Data function returns the component's reactive state
    data() {
        return {
            cliente: {},
            operacion_descripcion: null,
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        async _fetchData() {
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
                            this.operacion_descripcion =
                                match.descripcion + " Saldo: " + match.saldo;
                        }
                    } else
                        this.operacion_descripcion =
                            "No se seleccionó ninguna operación";
                }
                this.$emit("resultado", true);
            } catch (error) {
                this.$vs.notify({
                    title: "Datos del Cliente",
                    text: "Error al cargar los datos del cliente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    time: 8000,
                });
                this.$emit("resultado", false);
            } finally {
                this.$vs.loading.close();
            }
        },
    },
    // Lifecycle hooks
    created() {
        console.log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        console.log("Component mounted! " + this.$options.name); // DOM is ready
    },
    beforeDestroy() {},
    destroyed() {
        console.log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
