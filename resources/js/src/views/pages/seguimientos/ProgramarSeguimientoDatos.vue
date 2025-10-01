script
<template>
    <div>
        <div class="alerta">
            <div class="w-full info">
                <h3>
                    Datos del Cliente
                    <span v-if="operacion" class="uppercase text-primary">
                        ({{ operacion.descripcion }})
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
    </div>
</template>
<script>
export default {
    // Name of the component (optional)
    name: "ProgramarSeguimientoDatos",
    // Props: data passed from parent
    props: {
        cliente: {
            type: Object,
            required: true,
            default: null,
        },
        operacion: {
            type: Object,
            required: true,
            default: null,
        },
        enabled: {
            type: Boolean,
            required: true,
            default: true,
        },
    },
    // Computed properties: derived reactive data
    computed: {},
    watch: {},
    // Data function returns the component's reactive state
    data() {
        return {};
    },
    // Methods: functions you can call in template or other hooks
    methods: {},
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
