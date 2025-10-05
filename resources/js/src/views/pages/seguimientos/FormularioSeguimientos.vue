<template>
    <div>
        <vs-popup :class="['forms-popup', z_index]" fullscreen close="cancelar" title="Control de Seguimientos"
            :active="localShow" :ref="this.$options.name">
            <div class="contenido lg:h-full">
                <div
                    class="dashboard-container grid w-full lg:h-full gap-4 grid-cols-1 grid-rows-4 lg:grid-cols-2 lg:grid-rows-2">
                    <!-- Top-left -->
                    <div class="">
                        <!-- Form -->
                        <div class="form-group flex flex-col h-full">
                            <div class="title-form-group">
                                Datos del Cliente
                            </div>
                            <div class="form-group-content flex flex-col justify-between flex-1 overflow-auto">
                                <!-- Cliente buttons and inputs -->
                                <div class="w-full h-full flex flex-col justify-between pt-4">
                                    <!-- Cliente buttons -->
                                    <div class="w-full">
                                        <div class="flex justify-center px-4">
                                            <button v-if="!cliente.id" class="btn-select btn-select--unselected" @click="
                                                ShowBuscadorClientes = true
                                                ">
                                                <span class="">Seleccione un cliente</span>
                                            </button>
                                            <div v-else class="w-full px-2">
                                                <button class="btn-select btn-select--selected">
                                                    <div>
                                                        <span class="block"><span class="bold">Clave</span>: {{
                                                            cliente.id }},
                                                            <span class="bold">Nombre</span>:
                                                            {{
                                                                cliente.nombre
                                                            }}</span>
                                                    </div>
                                                    <span class="action" data-tooltip="Cambiar Cliente"
                                                        @click="quitarCliente()"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 mt-3 h-full">
                                        <div v-if="!cliente || !cliente.id"
                                            class="skeleton h-full flex items-center justify-center">
                                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                                        </div>
                                        <div v-else class="h-full flex flex-col justify-between">
                                            <!-- Input fields -->
                                            <div class="w-full flex flex-wrap">
                                                <div class="w-full xl:w-6/12 px-2 input-text">
                                                    <label>
                                                        Tipo de cliente
                                                    </label>
                                                    <vs-input ref="tipo_cliente" name="tipo_cliente" type="text"
                                                        class="w-full" placeholder="" v-model="cliente.tipo_cliente
                                                            " maxlength="100" :readonly="true" :disabled="!cliente ||
                                                                !cliente.id
                                                                " />
                                                </div>
                                                <div class="w-full xl:w-6/12 px-2 input-text">
                                                    <label>
                                                        Tel. / Celular
                                                    </label>
                                                    <vs-input ref="telefono" name="telefono" type="text" class="w-full"
                                                        placeholder="" v-model="cliente.telefono
                                                            " maxlength="100" :readonly="true" :disabled="!cliente ||
                                                                !cliente.id
                                                                " />
                                                </div>

                                                <div class="w-full px-2 input-text">
                                                    <label> Dirección </label>
                                                    <vs-input ref="direccion_completa" name="direccion_completa"
                                                        type="text" class="w-full" placeholder="" v-model="cliente.direccion_completa
                                                            " maxlength="250" :readonly="true" :disabled="!cliente ||
                                                                !cliente.id
                                                                " />
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap justify-center px-2">
                                                <div class="w-full text-center disabled-info justify-center py-4 font-medium"
                                                    v-if="!selectedRow">
                                                    Registro de Seguimientos
                                                    Libres
                                                </div>
                                                <div class="w-full alerta" v-else>
                                                    <div
                                                        class="w-full info operacion-seleccionada flex flex-wrap items-center justify-between">
                                                        <span>
                                                            <h3>
                                                                Operación
                                                                Seleccionada
                                                            </h3>
                                                            <p>
                                                                {{
                                                                    selectedRow.descripcion
                                                                }}
                                                            </p>
                                                        </span>
                                                        <span class="action-quitar"
                                                            data-tooltip="Quitar Operación Seleccionada"
                                                            @click="selectedRow = null">
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- Buttons -->
                                                <div
                                                    class="w-full pt-4 flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
                                                    <vs-button class="w-full md:w-1/2 text-center" color="primary"
                                                        :disabled="!cliente ||
                                                            !cliente.id
                                                            " @click.stop="
                                                                ShowFormProgramarSeguimientos = true
                                                                ">
                                                        Programar
                                                    </vs-button>
                                                    <vs-button class="w-full md:w-1/2 text-center" color="success"
                                                        :disabled="!cliente ||
                                                            !cliente.id
                                                            ">
                                                        Registrar
                                                    </vs-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top-right -->
                    <div class="p-2 mt-2 border-children">
                        <div v-if="!cliente || !cliente.id" class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                        </div>
                        <div v-else-if="OperacionesList.length === 0" class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">No hay operaciones que mostrar</span>
                        </div>
                        <div v-else class="h-full w-full flex flex-col justify-start overflow-auto p-2">
                            <vs-table :sst="false" :data="OperacionesList" stripe pagination max-items="8"
                                noDataText="0 Resultados" class="w-full tabla-datos">
                                <template slot="header">
                                    <h3>Operaciones del Cliente</h3>
                                </template>
                                <template slot="thead">
                                    <vs-th>Operación</vs-th>
                                    <vs-th>$ Saldo</vs-th>
                                </template>
                                <template slot-scope="{ data }">
                                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data"
                                        @click.native="toggleRow(tr)" :class="[
                                            'cursor-pointer',
                                            selectedRow &&
                                                selectedRow.operacion_id ===
                                                tr.operacion_id
                                                ? 'text-primary'
                                                : '',
                                        ]">
                                        <!-- Main columns -->
                                        <vs-td>{{ tr.descripcion }}</vs-td>
                                        <vs-td>{{ tr.saldo }}</vs-td>
                                    </vs-tr>
                                </template>
                            </vs-table>
                        </div>
                    </div>
                    <!-- Bottom-left -->
                    <div class="p-2 border-children">
                        <div v-if="!cliente || !cliente.id" class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                        </div>
                        <div v-else-if="OperacionesList.length === 0" class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">No hay operaciones que mostrar</span>
                        </div>
                        <div v-else class="overflow-auto">
                            <!-- Table here -->

                        </div>
                    </div>

                    <!-- Bottom-right -->
                    <div class="p-2 border-children">
                        <div v-if="!cliente || !cliente.id" class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                        </div>
                        <div v-else-if="ProgramadosList.length === 0" class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">No hay seguimientos programados</span>
                        </div>
                        <div v-else class="overflow-auto">
                            <!-- Table here -->
                            <vs-table :sst="false" :data="ProgramadosList" stripe pagination max-items="8"
                                noDataText="0 Resultados" class="w-full tabla-datos">
                                <template slot="header">
                                    <h3>Seguimientos Programados Pendientes</h3>
                                </template>
                                <template slot="thead">
                                    <vs-th>Acciones</vs-th>
                                    <vs-th>Motivo </vs-th>
                                    <vs-th>Fecha Programada</vs-th>
                                    <vs-th>Cancelar</vs-th>
                                </template>
                                <template slot-scope="{ data }">
                                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                        <!-- Main columns -->
                                        <vs-td>
                                            <div class="flex justify-center">
                                                <img class="cursor-pointer img-btn-20 mx-4"
                                                    src="@assets/images/folder.svg" title="Ver detalle"
                                                    @click="openModificar(data[indextr].id)" />
                                                <img class="cursor-pointer img-btn-20 mx-4"
                                                    src="@assets/images/edit.svg" title="Modificar Seguimiento"
                                                    @click="openModificar(data[indextr].id)" />
                                                <img class="img-btn-20 mx-4" src="@assets/images/seguimientos.svg"
                                                    title="Atender Seguimiento" @click="OpenFormSeguimientos(tr)" />
                                            </div>
                                        </vs-td>
                                        <vs-td>{{ tr.motivo_texto }}</vs-td>
                                        <vs-td>{{ tr.fechahora_programada_texto_abr }}</vs-td>
                                        <vs-td>
                                            <div class="flex justify-center">
                                                <img class="img-btn-20 mx-3" src="@assets/images/trash.svg"
                                                    title="Cancelar Seguimiento"
                                                    @click="cancelarCotizacion(data[indextr])" />
                                            </div>
                                        </vs-td>
                                    </vs-tr>
                                </template>
                            </vs-table>
                        </div>
                    </div>
                </div>
            </div>
            <FormularioProgramarSeguimiento v-if="ShowFormProgramarSeguimientos" :show="ShowFormProgramarSeguimientos"
                :filters="{
                    cliente_id: cliente.id,
                    tipo_cliente_id: cliente.tipo_cliente_id,
                    operacion_id: selectedRow ? selectedRow.operacion_id : '',
                }" :tipo="'agregar'" @closeVentana="CloseFormProgramarSeguimientos"
                @agregar_modificar_success_seguimiento="agregar_modificar_success_seguimiento" />
            <ClientesSearcherSeguimientos v-if="ShowBuscadorClientes" :show="ShowBuscadorClientes"
                @closeVentana="ShowBuscadorClientes = false" @cliente-seleccionado="onClienteSeleccionado">
            </ClientesSearcherSeguimientos>
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
import seguimientos from "../../../services/seguimientos";
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
        filters: {
            type: Object,
            required: false,
            default: {
                cliente_id: null,
                tipo_cliente_id: null,
                operacion_id: null,
                origen: 1, //Formulario de Origen. 1-seguimientos,2-clientes,3-operacion
            },
        },
    },
    // Computed properties: derived reactive data
    computed: {},
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    this.$popupManager.register(this.$options.name, this.cancelar);
                    //verificamos el origen del form para determinar que haremos justo al abrir el form.
                    if (this.filters.origen == 1) {
                        //abeierto desde seguimientos
                        await this.simularClienteSeleccionado();
                    } else if (this.filters.origen == 2) {
                        //abeierto desde clientes
                        this.cliente.id = this.filters.cliente_id;
                        this.cliente.tipo_cliente_id = this.filters.tipo_cliente_id;
                        await this.updateClienteInfo();
                    }
                    //obtener datos del cliente
                    this.localShow = true;
                    document.body.classList.add("overflow-hidden");
                } else {
                    this.$popupManager.unregister(this.$options.name);
                    this.resetData();
                    this.localShow = false;
                    document.body.classList.remove("overflow-hidden");
                }
            }
        },
        /*
        "cliente.id": {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                if (!newVal && this.show) {
                    console.log("🚀 ~ handler ~ newVal:", newVal)/cliente seleccionado
                    await this._getSeguimientosProgramados();
                } else {
                     
                }
            }
                
        },*/
    },
    // Data function returns the component's reactive state
    data() {
        return {
            localShow: false, // controls popup visibility
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
                direccion_completa: null,
            },
            selectedRow: null, // keep track of selection
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        toggleRow(row) {
            // if row already selected → deselect it
            if (
                this.selectedRow &&
                this.selectedRow.operacion_id === row.operacion_id
            ) {
                this.selectedRow = null;
            } else {
                this.selectedRow = row;
            }
        },
        async _fetchData() {
            if (!this.show) return; // stop here if not visible
            const params = {
                id: this.cliente.id,
                tipo_cliente_id: this.cliente.tipo_cliente_id,
                filtrar_x_operaciones: 1,
            };
            //this.$vs.loading();
            try {
                // Call the API from clientes service
                const result = await clientes.fetchClientes(params);
                const data = result.length ? result[0] : result;
                if (!data || typeof data !== "object") {
                    throw new Error("Respuesta inválida en _fetchData");
                }
                console.log("🚀 ~ _fetchData ~ data:", data)
                return data;
            } catch (error) {
                console.error("Error fetching clientes:", error);
                this.cancelar();
            } finally {
                //this.$vs.loading.close();
            }
        },

        async _getSeguimientosProgramados() {
            if (!this.show) return; // stop here if not visible
            const params = {
                cliente_id: this.cliente.id,
                tipo_cliente_id: this.cliente.tipo_cliente_id,
                programado_b: 1,
                status: 1
            };
            console.log("🚀 ~ _getSeguimientosProgramados ~ params:", params)
            //this.$vs.loading();
            try {
                // Call the API from seguimientos service
                const result = await seguimientos.getSeguimientosProgramados(params);
                console.log("🚀 ~ _getSeguimientosProgramados ~ result:", result)
                if (!result || typeof result !== "object") {
                    throw new Error("Respuesta inválida en _getSeguimientosProgramados");
                }
                return result.length ? result : [];

            } catch (error) {
                console.error("Error fetching seguimientos:", error);
                this.cancelar();
            } finally {
                //this.$vs.loading.close();
            }
        },
        async simularClienteSeleccionado() {
            this.cliente.id = 12;
            this.cliente.tipo_cliente_id = 1;
            await this.onClienteSeleccionado(this.cliente);
        },
        async updateClienteInfo() {
            this.$vs.loading();
            try {
                let cliente = await this._fetchData();
                this.cliente = { ...cliente };
                this.OperacionesList = cliente.operaciones;
                this.ProgramadosList = await this._getSeguimientosProgramados();
            } catch (error) {
                console.log("🚀 ~ updateClienteInfo ~ error:", error)
            } finally {
                this.$vs.loading.close();
            }
        },
        async onClienteSeleccionado(cliente) {
            //this.cliente.id = cliente.id;
            //this.cliente.tipo_cliente_id = cliente.tipo_cliente_id;
            this.cliente = { ...cliente };
            //get all cliente data
            console.log("✅ Cliente seleccionado:", cliente);
            await this.updateClienteInfo();
            setTimeout(() => {
                this.ShowBuscadorClientes = false;
            }, 50);
        },
        quitarCliente() {
            if (this.filters.origen != 1) {
                /**no se puede cambiar de cliente */
                this.$vs.notify({
                    title: "Cambio de Cliente",
                    text: "Para cambiar de cliente abra el módulo desde el apartado de Seguimientos.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "warning",
                    time: 8000,
                });
                return;
            }
            this.callBackConfirmar = this.resetData;
            this.openConfirmarSinPassword = true;
        },
        cancelar() {
            this.resetData();
            this.$emit("closeVentana");
        },
        resetData() {
            this.cliente.id = "";
            // reset data
            this.cliente.email = "";
            this.cliente.telefono = "";
            this.cliente.tipo_cliente = "";
            this.cliente.tipo_cliente_id = "";
            this.cliente.direccion_completa = "";
            this.selectedRow = null;
            this.OperacionesList = [];
            this.ProgramadosList = [];
        },
        //opening form programarSeguimientos
        CloseFormProgramarSeguimientos() {
            this.ShowFormProgramarSeguimientos = false;
        },
        async agregar_modificar_success_seguimiento() {
            //success after insert or update ()
            await this.updateClienteInfo();
            this.ShowFormProgramarSeguimientos = false;
        }
    },
    // Lifecycle hooks
    created() {
        console.log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        console.log("Component mounted! " + this.$options.name);
        const icon = this.$refs[this.$options.name].$el.querySelector(".vs-icon");
        if (icon) {
            icon.addEventListener("click", (e) => {
                e.preventDefault(); // stop form submission / page reload
                e.stopPropagation(); // stop bubbling if needed
                this.cancelar();
            });
        }
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

.border-children {
    border: 1px solid #e6eaed !important;
    border-radius: 4px;
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
