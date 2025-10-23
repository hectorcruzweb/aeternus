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
                                                        class="w-full info operacion-seleccionada flex flex-nowrap items-center justify-between">
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
                                                            data-tooltip="Quitar Operación Seleccionada" @click="
                                                                quitarOperacion
                                                            ">
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- Buttons -->
                                                <div
                                                    class="w-full pt-4 flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
                                                    <vs-button class="w-full md:w-1/2 text-center" color="primary"
                                                        :disabled="!cliente ||
                                                            !cliente.id
                                                            " @click="
                                                                programarSeguimiento(
                                                                    'agregar',
                                                                    null
                                                                )
                                                                ">
                                                        Programar
                                                    </vs-button>
                                                    <vs-button class="w-full md:w-1/2 text-center" color="success"
                                                        :disabled="!cliente ||
                                                            !cliente.id
                                                            " @click="
                                                                registrarSeguimiento(
                                                                    'agregar',
                                                                    null
                                                                )
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
                                    <h3>
                                        <span v-if="filters.operacion_id === null">Operaciones del Cliente</span><span
                                            v-else>Operación Seleccionada</span>
                                    </h3>
                                </template>
                                <template slot="thead">
                                    <vs-th>Acciones</vs-th>
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
                                        <vs-td>
                                            <div class="flex justify-center">
                                                <span v-if="$isDev">
                                                    {{ tr.operacion_id }}
                                                </span>
                                                <img class="cursor-pointer img-btn-18 mx-4"
                                                    src="@assets/images/seguimientos.svg" title="Registrar Seguimiento"
                                                    @click="
                                                        registrarSeguimiento(
                                                            'agregar',
                                                            tr
                                                        )
                                                        " />
                                                <img class="cursor-pointer img-btn-18 mx-4"
                                                    src="@assets/images/appointment.svg" title="Programar Seguimiento"
                                                    @click="
                                                        programarSeguimientoOperacion(
                                                            tr
                                                        )
                                                        " />
                                            </div>
                                        </vs-td>
                                        <vs-td>{{ tr.descripcion }}</vs-td>
                                        <vs-td>{{ tr.saldo }}</vs-td>
                                    </vs-tr>
                                </template>
                            </vs-table>
                        </div>
                    </div>
                    <!-- Bottom-left -->
                    <div class="border-children" :class="{
                        'full-row': hasSeguimientos && !hasProgramados, // expand full width
                        hidden: !hasSeguimientos, // hide both if none
                    }">
                        <div v-if="!cliente || !cliente.id" class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                        </div>
                        <div v-else-if="filteredSeguimientos.length === 0"
                            class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">No hay operaciones que mostrar</span>
                        </div>
                        <div v-else class="overflow-auto p-2">
                            <!-- Table here -->
                            <vs-table :sst="false" :data="filteredSeguimientos" stripe pagination max-items="8"
                                noDataText="0 Resultados" class="w-full tabla-datos">
                                <template slot="header">
                                    <h3>Seguimientos Realizados</h3>
                                </template>
                                <template slot="thead">
                                    <vs-th>Acciones</vs-th>
                                    <vs-th>Motivo </vs-th>
                                    <vs-th v-if="!hasProgramados">Resultado Obtenido</vs-th>
                                    <vs-th v-if="!hasProgramados">Fue Programado</vs-th>
                                    <vs-th>Fecha Realizado</vs-th>
                                    <vs-th>Cancelar</vs-th>
                                </template>
                                <template slot-scope="{ data }">
                                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                        <!-- Main columns -->
                                        <vs-td>
                                            <div class="flex justify-center">
                                                <span v-if="$isDev">
                                                    {{ tr.id }} /
                                                    {{
                                                        tr.operaciones_id
                                                            ? tr.operaciones_id
                                                            : "NA"
                                                    }}
                                                </span>
                                                <img class="cursor-pointer img-btn-20 mx-4"
                                                    src="@assets/images/folder.svg" title="Consultar Seguimiento"
                                                    @click="
                                                        registrarSeguimiento(
                                                            'consultar',
                                                            tr
                                                        )
                                                        " />
                                                <img class="cursor-pointer img-btn-20 mx-4"
                                                    src="@assets/images/edit.svg" title="Modificar Seguimiento" @click="
                                                        registrarSeguimiento(
                                                            'modificar',
                                                            tr
                                                        )
                                                        " />
                                            </div>
                                        </vs-td>
                                        <vs-td>{{ tr.motivo_texto }}</vs-td>
                                        <vs-td v-if="!hasProgramados">{{
                                            tr.resultado_texto
                                            }}</vs-td>
                                        <vs-td v-if="!hasProgramados">{{
                                            tr.tipo_programado_texto
                                            }}</vs-td>
                                        <vs-td>{{
                                            tr.fechahora_seguimiento_texto_abr
                                            }}</vs-td>
                                        <vs-td>
                                            <div class="flex justify-center">
                                                <img class="img-btn-20 mx-3" src="@assets/images/trash.svg"
                                                    title="Cancelar Seguimiento" @click="
                                                        registrarSeguimiento(
                                                            'cancelar',
                                                            tr
                                                        )
                                                        " />
                                            </div>
                                        </vs-td>
                                    </vs-tr>
                                </template>
                            </vs-table>
                        </div>
                    </div>

                    <!-- Bottom-right Programados-->
                    <div class="border-children" :class="{
                        'full-row': hasProgramados && !hasSeguimientos,
                        hidden: !hasProgramados,
                    }">
                        <div v-if="!cliente || !cliente.id" class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                        </div>
                        <div v-else-if="filteredProgramados.length === 0"
                            class="skeleton flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-normal">No hay seguimientos programados</span>
                        </div>
                        <div v-else class="overflow-auto p-2">
                            <!-- Table here -->
                            <vs-table :sst="false" :data="filteredProgramados" stripe pagination max-items="8"
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
                                                <span v-if="$isDev">
                                                    {{ tr.id }} /
                                                    {{
                                                        tr.operaciones_id
                                                            ? tr.operaciones_id
                                                            : "NA"
                                                    }}
                                                </span>
                                                <img class="cursor-pointer img-btn-20 mx-4"
                                                    src="@assets/images/folder.svg" title="Consultar Seguimiento"
                                                    @click="
                                                        programarSeguimiento(
                                                            'consultar',
                                                            tr
                                                        )
                                                        " />
                                                <img class="cursor-pointer img-btn-20 mx-4"
                                                    src="@assets/images/edit.svg" title="Modificar Seguimiento" @click="
                                                        programarSeguimiento(
                                                            'modificar',
                                                            tr
                                                        )
                                                        " />
                                                <img class="img-btn-20 mx-4" src="@assets/images/seguimientos.svg"
                                                    title="Atender Seguimiento" @click="
                                                        registrarSeguimiento(
                                                            'atender_seguimiento_programado',
                                                            tr
                                                        )
                                                        " />
                                            </div>
                                        </vs-td>
                                        <vs-td>{{ tr.motivo_texto }}</vs-td>
                                        <vs-td>{{
                                            !hasSeguimientos
                                                ? tr.fechahora_programada_texto
                                                : tr.fechahora_programada_texto_abr
                                        }}</vs-td>
                                        <vs-td>
                                            <div class="flex justify-center">
                                                <img class="img-btn-20 mx-3" src="@assets/images/trash.svg"
                                                    title="Cancelar Seguimiento" @click="
                                                        programarSeguimiento(
                                                            'cancelar',
                                                            tr
                                                        )
                                                        " />
                                            </div>
                                        </vs-td>
                                    </vs-tr>
                                </template>
                            </vs-table>
                        </div>
                    </div>
                    <!-- Skeleton / No data -->
                    <div v-if="
                        !hasSeguimientos &&
                        !hasProgramados &&
                        (!cliente || !cliente.id)
                    " class="skeleton border-children full-row">
                        <span class="text-gray-600 text-lg font-normal">Seleccione 1 Cliente</span>
                    </div>
                    <div v-else-if="!hasSeguimientos && !hasProgramados" class="skeleton border-children full-row">
                        <span class="text-gray-600 text-lg font-normal">No hay seguimientos que mostrar</span>
                    </div>
                </div>
            </div>
            <FormularioProgramarSeguimiento v-if="ShowFormProgramarSeguimientos" :show="ShowFormProgramarSeguimientos"
                :filters="FormularioProgramarSeguimientoFilters" :tipo="tipoFormProgramarSeguimiento"
                @closeVentana="CloseFormProgramarSeguimientos" @agregar_modificar_success_seguimiento="
                    agregar_modificar_success_seguimiento
                " />
            <ClientesSearcherSeguimientos v-if="ShowBuscadorClientes" :show="ShowBuscadorClientes"
                @closeVentana="ShowBuscadorClientes = false" @cliente-seleccionado="onClienteSeleccionado">
            </ClientesSearcherSeguimientos>
            <ConfirmarDanger :z_index="'z-index58k'" v-if="openConfirmarSinPassword" :show="openConfirmarSinPassword"
                :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
                :accion="'QUITAR EL CLIENTE SELECCIONADO'" :confirmarButton="'Continuar'"></ConfirmarDanger>
            <FormularioRegistrarSeguimiento v-if="ShowFormRegistrarSeguimientos" :show="ShowFormRegistrarSeguimientos"
                :filters="FormularioRegistrarSeguimientoFilters" :tipo="tipoFormRegistrarSeguimiento"
                @closeVentana="CloseFormRegistrarSeguimientos" @agregar_modificar_success_seguimiento="
                    agregar_modificar_success_seguimiento
                " />
        </vs-popup>
    </div>
</template>
<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
import FormularioProgramarSeguimiento from "./FormularioProgramarSeguimiento";
import ClientesSearcherSeguimientos from "../clientes/ClientesSearcherSeguimientos.vue";
import clientes from "../../../services/clientes";
import seguimientos from "../../../services/seguimientos";
import FormularioRegistrarSeguimiento from "./FormularioRegistrarSeguimiento.vue";
export default {
    components: {
        ClientesSearcherSeguimientos,
        FormularioProgramarSeguimiento,
        ConfirmarDanger,
        FormularioRegistrarSeguimiento,
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
    computed: {
        hasSeguimientos() {
            return (
                Array.isArray(this.filteredSeguimientos) &&
                this.filteredSeguimientos.length > 0
            );
        },
        hasProgramados() {
            return (
                Array.isArray(this.filteredProgramados) &&
                this.filteredProgramados.length > 0
            );
        },

        filteredProgramados() {
            if (this.selectedRow) {
                return this.ProgramadosList.filter(
                    (s) => s.operaciones_id === this.selectedRow.operacion_id
                );
            }
            return this.ProgramadosList;
        },
        filteredSeguimientos() {
            if (this.selectedRow) {
                return this.SeguimientosList.filter(
                    (s) => s.operaciones_id === this.selectedRow.operacion_id
                );
            }
            return this.SeguimientosList;
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    //verificamos el origen del form para determinar que haremos justo al abrir el form.
                    if (this.filters.origen == 1) {
                        //abeierto desde seguimientos
                        if (this.$isDev)
                            await this.simularClienteSeleccionado();
                    } else if (this.filters.origen == 2) {
                        //abeierto desde clientes
                        this.cliente.id = this.filters.cliente_id;
                        this.cliente.tipo_cliente_id =
                            this.filters.tipo_cliente_id;
                        await this.updateClienteInfo();
                    }
                    this.$popupManager.register(this, this.cancelar, null);
                    //obtener datos del cliente
                }
                this.localShow = newVal;
            },
        },
    },
    // Data function returns the component's reactive state
    data() {
        return {
            tipoFormProgramarSeguimiento: null,
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
            selectedRow: null, // keep track of selection,
            FormularioProgramarSeguimientoFilters: null,
            //Registrar Seguimiento Data
            FormularioRegistrarSeguimientoFilters: null,
            ShowFormRegistrarSeguimientos: false,
            tipoFormRegistrarSeguimiento: null,
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        programarSeguimiento(tipo = "", datos_seguimiento = null) {
            this.tipoFormProgramarSeguimiento = tipo;
            //es agregar
            if (tipo === "agregar") {
                this.FormularioProgramarSeguimientoFilters = {
                    cliente_id: this.cliente.id,
                    tipo_cliente_id: this.cliente.tipo_cliente_id,
                    operacion_id: this.selectedRow
                        ? this.selectedRow.operacion_id
                        : "",
                };
            } else {
                //modificar o consultar
                this.FormularioProgramarSeguimientoFilters = {
                    cliente_id: this.cliente.id,
                    tipo_cliente_id: this.cliente.tipo_cliente_id,
                    operacion_id: datos_seguimiento.operaciones_id,
                    seguimiento_id: datos_seguimiento.id,
                };
            }
            this.ShowFormProgramarSeguimientos = true;
        },
        programarSeguimientoOperacion(datos_seguimiento = null) {
            this.$log(
                "🚀 ~ programarSeguimientoOperacion ~ datos_seguimiento:",
                datos_seguimiento
            );
            this.tipoFormProgramarSeguimiento = "agregar";
            //es agregar
            this.FormularioProgramarSeguimientoFilters = {
                cliente_id: this.cliente.id,
                tipo_cliente_id: this.cliente.tipo_cliente_id,
                operacion_id: datos_seguimiento.operacion_id,
            };

            this.ShowFormProgramarSeguimientos = true;
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
                this.$log("🚀 ~ _fetchData ~ data:", data);
                return data;
            } catch (error) {
                this.$error("Error fetching clientes:", error);
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
                status: 1,
            };
            this.$log("🚀 ~ _getSeguimientosProgramados ~ params:", params);
            //this.$vs.loading();
            try {
                // Call the API from seguimientos service
                const result = await seguimientos.getSeguimientos(params);
                this.$log("🚀 ~ _getSeguimientosProgramados ~ result:", result);
                if (!result || typeof result !== "object") {
                    throw new Error(
                        "Respuesta inválida en _getSeguimientosProgramados"
                    );
                }
                return result.length ? result : [];
            } catch (error) {
                this.$error("Error fetching seguimientos:", error);
            } finally {
                //this.$vs.loading.close();
            }
        },
        async _getSeguimientosRealizados() {
            if (!this.show) return; // stop here if not visible
            const params = {
                cliente_id: this.cliente.id,
                tipo_cliente_id: this.cliente.tipo_cliente_id,
                programado_b: 0,
                status: 1,
            };
            this.$log("🚀 ~ _getSeguimientosRealizados ~ params:", params);
            //this.$vs.loading();
            try {
                // Call the API from seguimientos service
                const result = await seguimientos.getSeguimientos(params);
                this.$log("🚀 ~ _getSeguimientosRealizados ~ result:", result);
                if (!result || typeof result !== "object") {
                    throw new Error(
                        "Respuesta inválida en _getSeguimientosRealizados"
                    );
                }
                return result.length ? result : [];
            } catch (error) {
                this.$error("Error fetching seguimientos:", error);
                this.cancelar();
            } finally {
                //this.$vs.loading.close();
            }
        },
        async simularClienteSeleccionado() {
            this.cliente.id = 5;
            this.cliente.tipo_cliente_id = 1;
            await this.onClienteSeleccionado(this.cliente);
        },
        async updateClienteInfo() {
            this.$vs.loading();
            try {
                let cliente = await this._fetchData();
                this.cliente = { ...cliente };
                // Filter programados and realizados
                const allProgramados = await this._getSeguimientosProgramados();
                const allRealizados = await this._getSeguimientosRealizados();
                if (this.filters.operacion_id !== null) {
                    //operciones selecioanda
                    const selectedOperacion = cliente.operaciones.find(
                        (op) => op.operacion_id === this.filters.operacion_id
                    );
                    if (selectedOperacion) {
                        // do something with the selected operation
                        this.$log("✅ Found operation:", selectedOperacion);
                        // Example: set as selected
                        this.OperacionesList = [selectedOperacion];
                        this.selectedRow = selectedOperacion; // optional: pre-select it
                        /*this.ProgramadosList = allProgramados.filter(
                            (s) =>
                                s.operaciones_id === this.filters.operacion_id
                        );
                        this.SeguimientosList = allRealizados.filter(
                            (s) =>
                                s.operaciones_id === this.filters.operacion_id
                        );*/
                    } else {
                        this.$warn(
                            "⚠️ Operation ID not found:",
                            this.filters.operacion_id
                        );
                        this.OperacionesList = [];
                        this.ProgramadosList = [];
                        this.SeguimientosList = [];
                    }
                } else {
                    //todas las operaciones
                    this.OperacionesList = cliente.operaciones;
                }
                this.ProgramadosList = allProgramados;
                this.SeguimientosList = allRealizados;
            } catch (error) {
                this.$vs.notify({
                    title: "Seguimientos",
                    text: "Ha ocurrido un error al cargar la información.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    time: 4000,
                });
                this.$emit("closeVentana");
            } finally {
                this.$vs.loading.close();
            }
        },
        async onClienteSeleccionado(cliente) {
            //this.cliente.id = cliente.id;
            //this.cliente.tipo_cliente_id = cliente.tipo_cliente_id;
            this.cliente = { ...cliente };
            //get all cliente data
            this.$log("✅ Cliente seleccionado:", cliente);
            await this.updateClienteInfo();
            setTimeout(() => {
                this.ShowBuscadorClientes = false;
            }, 50);
        },
        quitarOperacion() {
            if (this.filters.operacion_id !== null) {
                /**no se puede cambiar de operacion */
                this.$vs.notify({
                    title: "Cambio de Operación",
                    text: "Para cambiar de operación abra el módulo desde el apartado de Seguimientos.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "warning",
                    time: 8000,
                });
            } else {
                this.selectedRow = null;
            }
        },
        toggleRow(row) {
            if (this.filters.operacion_id !== null) {
                //no se puede cambiar de operacion
                return;
            }
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

        CloseFormRegistrarSeguimientos() {
            this.ShowFormRegistrarSeguimientos = false;
        },

        async agregar_modificar_success_seguimiento() {
            //success after insert or update ()
            this.ShowFormProgramarSeguimientos = false;
            this.ShowFormRegistrarSeguimientos = false;
            await this.updateClienteInfo();
        },
        //Registrar Seguimientos Methods
        registrarSeguimiento(tipo = "", datos_seguimiento = null) {
            let operacion_id = this.selectedRow
                ? this.selectedRow.operacion_id
                : null;
            if (
                datos_seguimiento &&
                typeof datos_seguimiento.operacion_id !== "undefined"
            ) {
                operacion_id = datos_seguimiento.operacion_id;
            }

            this.tipoFormRegistrarSeguimiento = tipo;
            this.FormularioRegistrarSeguimientoFilters = {
                cliente_id: this.cliente.id,
                tipo_cliente_id: this.cliente.tipo_cliente_id,
                operacion_id: operacion_id,
                seguimiento_id: null,
            };
            // es agregar
            if (tipo !== "agregar" && datos_seguimiento) {
                // modificar o consultar
                this.FormularioRegistrarSeguimientoFilters.seguimiento_id =
                    datos_seguimiento.id;
                this.FormularioRegistrarSeguimientoFilters.operacion_id =
                    datos_seguimiento.operaciones_id;
            }
            this.ShowFormRegistrarSeguimientos = true;
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
    },
    beforeDestroy() {
        this.$nextTick(() => {
            setTimeout(() => {
                this.$popupManager.unregister(this.$options.name);
            }, 50);
        });
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
