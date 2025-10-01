script
<template>
    <div>Formulario programar Seguimiento</div>
</template>
<script>
export default {
    // Name of the component (optional)
    name: "ProgramarSeguimientoDatos",
    // Props: data passed from parent
    props: {},
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
