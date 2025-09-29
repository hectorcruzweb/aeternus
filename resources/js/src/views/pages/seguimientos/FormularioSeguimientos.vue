<template>
    <div>
        <vs-popup :class="['forms-popup', z_index]" fullscreen close="cancelar" title="Control de Seguimientos"
            :active.sync="showVentana" ref="formulario">
            <div class="contenido lg:h-full">
                <div class="dashboard-container grid w-full lg:h-full gap-4
            grid-cols-1 grid-rows-4
            lg:grid-cols-2 lg:grid-rows-2">
                    <!-- Top-left -->
                    <div class=" p-2">
                        <!-- Form -->
                        <div class="form-group flex flex-col h-full">
                            <div class="title-form-group">Datos del Cliente</div>
                            <div class="form-group-content flex flex-col justify-between flex-1 overflow-auto">
                                <!-- Cliente buttons and inputs -->
                                <div class="w-full h-full flex flex-col justify-between pt-4">
                                    <!-- Cliente buttons -->
                                    <div class="px-2 w-full space-y-4">
                                        <div class="flex justify-center px-2">
                                            <button v-if="!cliente.id" class=" btn-select btn-select--unselected"
                                                @click="ShowBuscadorClientes = true">
                                                <span class="">Seleccione un cliente</span>
                                            </button>
                                            <button v-else class=" btn-select btn-select--selected px-2">
                                                <div>
                                                    <span class="block">Clave: 3220, Nombre: VALENTIN AYALA
                                                        MUÑOZ</span>
                                                </div>
                                                <span class="action" data-tooltip="Cambiar Cliente" @click="
                                                    quitarCliente()
                                                    "></span>
                                            </button>
                                        </div>
                                        <!-- Input fields -->
                                        <div class="w-full flex flex-wrap">
                                            <div class="w-full xl:w-6/12 px-2 input-text">
                                                <label>
                                                    Nombre del cliente
                                                </label>
                                                <vs-input ref="cliente" name="cliente" type="text" class="w-full"
                                                    placeholder="" v-model="cliente.nombre" maxlength="100" />
                                            </div>
                                            <div class="w-full xl:w-6/12 px-2 input-text">
                                                <label>
                                                    Nombre del cliente
                                                </label>
                                                <vs-input ref="cliente" name="cliente" type="text" class="w-full"
                                                    placeholder="" v-model="cliente.nombre" maxlength="100" />
                                            </div>
                                            <div class="w-full px-2 input-text">
                                                <label>
                                                    Email del cliente
                                                </label>
                                                <vs-input ref="cliente" name="cliente" type="text" class="w-full"
                                                    placeholder="" v-model="cliente.nombre" maxlength="100" />
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Buttons -->
                                    <div
                                        class="w-full pt-4 flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 px-4">
                                        <vs-button class="w-full md:w-1/2 text-center" color="primary"
                                            :disabled="!cliente || !cliente.id">
                                            Programar Seguimiento
                                        </vs-button>
                                        <vs-button class="w-full md:w-1/2 text-center" color="success"
                                            :disabled="!cliente || !cliente.id">
                                            Registrar Seguimiento
                                        </vs-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top-right -->
                    <div class="p-2">
                        <div v-if="!cliente || !cliente.id" class="skeleton  flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                        </div>
                        <div v-else-if="OperacionesList.length === 0"
                            class="skeleton  flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">No results found</span>
                        </div>
                        <div v-else class="bg-gray-300 overflow-auto">
                            <!-- Table here -->
                        </div>
                    </div>

                    <!-- Bottom-left -->
                    <div class="p-2">
                        <div v-if="!cliente || !cliente.id" class="skeleton  flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                        </div>
                        <div v-else-if="OperacionesList.length === 0"
                            class="skeleton  flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">No results found</span>
                        </div>
                        <div v-else class="bg-gray-300 overflow-auto">
                            <!-- Table here -->
                        </div>
                    </div>

                    <!-- Bottom-right -->
                    <div class="p-2">
                        <div v-if="!cliente || !cliente.id" class="skeleton  flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                        </div>
                        <div v-else-if="OperacionesList.length === 0"
                            class="skeleton  flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">No results found</span>
                        </div>
                        <div v-else class="bg-gray-300 overflow-auto">
                            <!-- Table here -->
                        </div>
                    </div>
                </div>
            </div>
            <FormularioProgramarSeguimiento :show="ShowFormProgramarSeguimientos"
                @closeVentana="CloseFormProgramarSeguimientos" />
            <ClientesSearcherSeguimientos :show="ShowBuscadorClientes" @closeVentana="ShowBuscadorClientes = false"
                @cliente-seleccionado="onClienteSeleccionado"></ClientesSearcherSeguimientos>
            <ConfirmarDanger :z_index="'z-index58k'" :show="openConfirmarSinPassword"
                :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
                :accion="'QUITAR EL CLIENTE SELECCIONADO'" :confirmarButton="'Continuar'"></ConfirmarDanger>
        </vs-popup>
    </div>
</template>
<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
import FormularioProgramarSeguimiento from "./FormularioProgramarSeguimiento";
import ClientesSearcherSeguimientos from "../clientes/ClientesSearcherSeguimientos.vue";
import clientes from "../../../services/clientes";
export default {
    components: {
        ClientesSearcherSeguimientos,
        FormularioProgramarSeguimiento,
        ConfirmarDanger,
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
            openConfirmarSinPassword: false,
            callBackConfirmar: Function,
            OperacionesList: [],
            ProgramadosList: [],
            SeguimientosList: [],
            ShowFormProgramarSeguimientos: false,
            ShowBuscadorClientes: false,
            cliente: {
                id: null,
                nombre: null,
                email: null,
                telefono: "",
                tipo_cliente: "",
                tipo_cliente_id: null,
            },
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        onClienteSeleccionado(cliente) {
            console.log("Cliente seleccionado:", cliente);
            // do whatever you need — e.g. fill a form or close popup
            this.cliente.id = cliente.id;
            this.cliente.nombre = cliente.nombre;
            this.cliente.telefono = cliente.telefono;
            this.cliente.email = cliente.email;
            this.cliente.tipo_cliente = cliente.tipo_cliente;
            this.cliente.tipo_cliente_id = cliente.tipo_cliente_id;
            this.ShowBuscadorClientes = false;
        },
        quitarCliente() {
            this.callBackConfirmar = this.limpiarCliente;
            this.openConfirmarSinPassword = true;
        },
        limpiarCliente() {
            this.cliente.id = "";
        },
        cancelar() {
            this.resetData();
            this.$emit("closeVentana");
        },
        resetData() {
            // Clear VeeValidate errors
        },
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
        //this.ShowBuscadorClientes = true;
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        console.log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style scoped>
.dashboard-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    /* 2 columns */
    grid-template-rows: auto auto;
    /* rows grow with content */
    width: 100%;
    height: 100%;
    /* fill popup */
    gap: 1rem;
    /* spacing between children */
}

.dashboard-container>div {
    min-height: 0;
    /* allows shrinking */
    min-width: 0;
    /* prevents overflow */
    overflow: visible;
    /* so content defines height */
    display: flex;
    flex-direction: column;
    /* optional, if you have inner flex items */
}

@media (max-width: 1024px) {
    .dashboard-container {
        grid-template-columns: 1fr;
        /* single column */
        grid-template-rows: auto auto auto auto;
        /* each child grows naturally */
    }
}


/* Skeleton animation */
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.skeleton {
    display: flex;
    align-items: center;
    /* vertical center */
    justify-content: center;
    /* horizontal center */
    width: 100%;
    height: 100% !important;
    /* fills the grid cell */
    min-height: 150px;
    /* optional: ensures some height on very small cells */
    background-color: #f5f5f5;
    /* very light gray */
    border-radius: 4px;
    animation: pulse 5s ease-in-out infinite;
    /* optional: add padding for small screens */
    text-align: center;
    /* ensure text wraps nicely */
    box-sizing: border-box;
    /* padding included in size */
}
</style>
