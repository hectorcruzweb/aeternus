<template>
    <div>
        <vs-popup :class="['forms-popup', z_index]" fullscreen close="cancelar" title="Control de Seguimientos"
            :active.sync="showVentana" ref="formulario">
            <div class="flex flex-wrap">
                <div class="w-full md:w-6/12 lg:w-4/12 px-2 py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">Datos del Cliente</div>
                        <div class="form-group-content">
                            <div class="w-full px-2">
                                <button class="btn-select btn-select--unselected" @click="ShowBuscadorClientes = true">
                                    <span class="texto">Debe seleccionar a un cliente</span>
                                </button>
                                <!--
                                <button class="btn-select btn-select--selected">
                                    <span class="texto"
                                        >Clave: 3220, Nombre: VALENTIN AYALA
                                        MUÑOZ, Dirección: CALLE EJIDATARIOS DEL
                                        CONCHI 11736 COLONIA VALLE DEL EJIDO
                                        82134 MAZATLAN, SINALOA</span
                                    >
                                    <span
                                        class="action"
                                        data-tooltip="Cambiar Cliente"
                                        @click="
                                            ShowFormProgramarSeguimientos = true
                                        "
                                    ></span>
                                </button>
                            -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--
            <vs-button
                class="w-full md:w-auto   md:ml-2 md:mt-0"
                color="success"
                @click="ShowFormProgramarSeguimientos = true"
            >
                <span>Registrar Seguimiento</span>
            </vs-button>
        -->

            <FormularioProgramarSeguimiento :show="ShowFormProgramarSeguimientos"
                @closeVentana="CloseFormProgramarSeguimientos" />
            <ClientesSearcherSeguimientos :show="ShowBuscadorClientes" @closeVentana="ShowBuscadorClientes = false"
                @cliente-seleccionado="onClienteSeleccionado"></ClientesSearcherSeguimientos>
        </vs-popup>
    </div>
</template>
<script>
import FormularioProgramarSeguimiento from "./FormularioProgramarSeguimiento";
import ClientesSearcherSeguimientos from "../clientes/ClientesSearcherSeguimientos.vue";

export default {
    components: {
        ClientesSearcherSeguimientos,
        FormularioProgramarSeguimiento,
    },
    // Name of the component (optional)
    name: "FormularioSeguimientos",
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
    },
    // Computed properties: derived reactive data
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
    // Data function returns the component's reactive state
    data() {
        return {
            ShowFormProgramarSeguimientos: false,
            ShowBuscadorClientes: false,
            form: {
                cliente: {
                    id: null,
                    nombre: null,
                    email: null,
                    telefono: '',
                    tipo_cliente: '',
                    tipo_cliente_id: null,
                },
            }
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        onClienteSeleccionado(cliente) {
            console.log("Cliente seleccionado:", cliente);
            // do whatever you need — e.g. fill a form or close popup
            this.form.id = cliente.id;
            this.form.nombre = cliente.nombre;
            this.form.telefono = cliente.telefono;
            this.form.email = cliente.email;
            this.form.tipo_cliente = cliente.tipo_cliente;
            this.form.tipo_cliente_id = cliente.tipo_cliente_id;
            this.ShowBuscadorClientes = false;
        },
        cancelar() {
            this.limpiarVentana();
            this.$emit("closeVentana");
        },
        limpiarVentana() { },
        //opening form programarSeguimientos
        CloseFormProgramarSeguimientos() {
            this.ShowFormProgramarSeguimientos = false;
        },
    },
    // Lifecycle hooks
    created() {
        console.log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        console.log("Component mounted! " + this.$options.name); // DOM is ready
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
            this.cancelar();
        };
        this.ShowBuscadorClientes = true;
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        console.log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
