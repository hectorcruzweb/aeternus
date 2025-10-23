<template>
    <div class="centerx">
        <vs-popup
            :class="['forms-popup popup-85', z_index]"
            fullscreen
            title="Catálogo de clientes"
            :active="localShow"
            :ref="this.$options.name"
        >
            <div class="flex flex-col flex-1 h-full bg-danger">
                <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
                    <vx-card
                        no-radius
                        title="Filtros de selección"
                        refresh-content-action
                        @refresh="reset"
                        :collapse-action="false"
                    >
                        <div class="flex flex-wrap pb-6">
                            <div
                                class="w-full sm:w-6/12 lg:w-3/12 input-text px-2"
                            >
                                <label>Filtrar x Tipo de Clientes</label>
                                <v-select
                                    :options="filtrosEspecificos"
                                    v-model="serverOptions.tipo_cliente_id"
                                    :clearable="false"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                    class="w-full"
                                    data-vv-as=" "
                                    @input="onFilterChange"
                                >
                                    <div slot="no-options">Seleccione 1</div>
                                </v-select>
                            </div>
                            <div
                                class="w-full sm:w-6/12 lg:w-3/12 input-text px-2"
                            >
                                <label>Núm. Cliente</label>
                                <vs-input
                                    v-model="serverOptions.id"
                                    name="id"
                                    type="text"
                                    class="w-full"
                                    placeholder="Ej. 1258"
                                    maxlength="6"
                                    v-validate="'integer|min_value:1'"
                                    data-vv-as="Número de Cliente"
                                    @keyup.enter="onEnterPress('id')"
                                    @blur="onBlurFetch('id')"
                                ></vs-input>
                                <span>
                                    {{ errors.first("id") }}
                                </span>
                            </div>
                            <div class="w-full lg:w-6/12 input-text px-2">
                                <label>Nombre del Cliente</label>
                                <vs-input
                                    v-model="serverOptions.nombre"
                                    name="nombre"
                                    data-vv-as=" "
                                    type="text"
                                    class="w-full"
                                    placeholder="Ej. Juán Pérez"
                                    maxlength="150"
                                    @keyup.enter="onEnterPress('nombre')"
                                    @blur="onBlurFetch('nombre')"
                                />
                                <span>
                                    {{ errors.first("nombre") }}
                                </span>
                            </div>
                        </div>
                    </vx-card>
                </div>
                <div id="resultados" class="mt-5 flex flex-col flex-1">
                    <div
                        v-if="noDataFound"
                        class="w-full skeleton flex-1 items-center justify-center"
                    >
                        <span class="text-gray-600 text-lg font-normal"
                            >No hay datos que mostrar</span
                        >
                    </div>
                    <div v-else id="results" class="w-full flex flex-wrap">
                        <div class="w-full py-2">
                            <div class="flex flex-wrap px-2">
                                <div
                                    class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2"
                                >
                                    <vx-card
                                        no-radius
                                        title="Filtros de selección"
                                        :collapse-action="false"
                                    >
                                        <div class="flex flex-wrap">
                                            <div class="w-full text-right">
                                                <vs-button
                                                    v-if="
                                                        form.ajuste.length > 0
                                                    "
                                                    class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                                                    color="danger"
                                                    @click="quitarTodos"
                                                >
                                                    <span
                                                        >Quitar todos los
                                                        Artículos</span
                                                    >
                                                </vs-button>
                                            </div>
                                            <div
                                                class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2 input-text"
                                            >
                                                <label
                                                    class="text-base opacity-75 font-medium"
                                                    >Datos del Ajuste</label
                                                >
                                                <v-select
                                                    :options="tipoAjustes"
                                                    :clearable="false"
                                                    :dir="
                                                        $vs.rtl ? 'rtl' : 'ltr'
                                                    "
                                                    v-model="form.tipoAjuste"
                                                    class="mb-4 md:mb-0 mt-1"
                                                    :disabled="
                                                        this.form.ajuste
                                                            .length > 0
                                                    "
                                                />
                                            </div>
                                            <div
                                                class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 mb-4 px-2 input-text"
                                            >
                                                <label
                                                    class="text-base opacity-75 font-medium"
                                                    >Nota:</label
                                                >
                                                <vs-input
                                                    class="w-full mt-1"
                                                    maxlength="250"
                                                    placeholder="Nota sobre el ajuste"
                                                    v-model.trim="form.nota"
                                                />
                                            </div>
                                        </div>
                                    </vx-card>
                                </div>
                                <div
                                    class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 mt-5"
                                >
                                    <vs-table
                                        :sst="true"
                                        :max-items="serverOptions.per_page"
                                        :data="clientesList"
                                        stripe
                                        noDataText="0 Resultados"
                                        class="tabla-datos"
                                    >
                                        <template slot="header">
                                            <h3>
                                                Lista actualizada de clientes
                                                registrados
                                            </h3>
                                        </template>
                                        <template slot="thead">
                                            <vs-th>Núm. Cliente</vs-th>
                                            <vs-th>Nombre</vs-th>
                                            <vs-th>Tipo de Cliente</vs-th>
                                            <vs-th>Seleccionar</vs-th>
                                        </template>
                                        <template slot-scope="{ data }">
                                            <vs-tr
                                                :data="tr"
                                                :key="indextr"
                                                v-for="(tr, indextr) in data"
                                            >
                                                <!-- Main columns -->
                                                <vs-td>{{ tr.id }}</vs-td>
                                                <vs-td>{{ tr.nombre }}</vs-td>
                                                <vs-td>{{
                                                    tr.tipo_cliente
                                                }}</vs-td>
                                                <vs-td>
                                                    <div
                                                        class="flex justify-center"
                                                    >
                                                        <img
                                                            class="cursor-pointer img-btn-20 mx-3"
                                                            src="@assets/images/checked.svg"
                                                            @click="
                                                                $emit(
                                                                    'cliente-seleccionado',
                                                                    tr
                                                                )
                                                            "
                                                        />
                                                    </div>
                                                </vs-td>
                                            </vs-tr>
                                        </template>
                                    </vs-table>
                                    <div>
                                        <vs-pagination
                                            v-if="verPaginado"
                                            :total="total"
                                            :max="serverOptions.per_page"
                                            v-model="actual"
                                            class="mt-6"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap px-2 mt-10">
                                <div class="w-full px-2">
                                    <div class="mt-2">
                                        <p class="text-center">
                                            <span
                                                class="text-danger font-medium"
                                                >Ojo:</span
                                            >
                                            Por favor revise la información
                                            ingresada, si todo es correcto de
                                            click en "Botón de Abajo”.
                                        </p>
                                    </div>
                                </div>
                                <div class="w-full text-center">
                                    <vs-button
                                        class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2"
                                        color="success"
                                        @click="acceptAlert()"
                                    >
                                        <span>Ajustar Inventario</span>
                                    </vs-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import clientes from "../../../services/clientes";
import debounce from "lodash/debounce";
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
            default: "z-index55k",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                // Only listen when visible = true
                if (newVal) {
                    this.$popupManager.register(this, this.cancelar);
                    // Initial load (immediate, not debounced)
                    await this._fetchData();
                }
                this.localShow = newVal;
            },
        },
        actual: {
            immediate: true, // runs when component is mounted too
            async handler(newVal) {
                if (this.localShow) {
                    this.serverOptions.page = newVal;
                    await this._fetchData();
                }
            },
        },
    },
    computed: {
        noDataFound() {
            return this.clientesList.length === 0;
        },
    },
    data() {
        return {
            localShow: false, // controls popup visibility
            isLoading: false,
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
            verPaginado: true,
            total: 0,
            actual: 1,
            serverOptions: {
                page: 1,
                per_page: 15,
                tipo_cliente_id: {
                    label: "Listar Todos",
                    value: "",
                },
                id: "",
                nombre: "",
            },
            previousServerOptions: {
                tipo_cliente_id: {
                    label: "Listar Todos",
                    value: "",
                },
                id: "",
                nombre: "",
            },
            clientesList: [],
        };
    },
    methods: {
        resetData() {
            this.verPaginado = false;
            // Reset all fields to their default values
            this.serverOptions = {
                page: 1, // reset page
                per_page: 15, // default per page
                tipo_cliente_id: {
                    // default select option
                    label: "Listar Todos",
                    value: "",
                },
                id: "", // clear id
                nombre: "", // clear name
            };
            // Clear VeeValidate errors
            this.$validator.reset();
            this.clientesList = [];
        },
        reset(card) {
            card.removeRefreshAnimation(500);
            // Optionally, fetch all clients again
            this.resetData();
            this.fetchData();
        },
        cancelar() {
            this.$emit("closeVentana");
        },
        async _fetchData() {
            if (!this.show) return; // stop here if not visible
            // Validate all fields first
            const isValid = await this.$validator.validateAll(); // returns true if all valid
            if (!isValid) {
                this.$log("Validation failed. API call skipped.");
                return; // stop here if validation fails
            }
            if (this.isLoading) {
                this.$log(
                    "Validation failed. API call skipped due to loading."
                );
                return; // ✅ Prevents multiple calls while loading
            }
            const params = {
                page: this.serverOptions.page || 1,
                per_page: this.serverOptions.per_page || 15,
                tipo_cliente_id: this.serverOptions.tipo_cliente_id.value || "",
                id: this.serverOptions.id.trim(),
                nombre: this.serverOptions.nombre.trim(),
            };
            this.isLoading = true;
            this.$vs.loading();
            try {
                // Call the API from clientes service
                const data = await clientes.fetchClientes(params);
                this.clientesList = data.data; // assuming API returns { items: [], total: 100 }
                this.$log(
                    "🚀 ~ _fetchData ~ this.clientesList:",
                    this.clientesList
                );
                this.total = data.last_page;
                this.actual = data.current_page;
            } catch (error) {
                this.cancelar();
                this.$error("Error fetching clientes:", error);
            } finally {
                this.isLoading = false;
                this.verPaginado = true;
                this.$vs.loading.close();
            }
        },
        onFilterChange() {
            this.serverOptions.page = 1; // reset page
            this.fetchData(); // debounced
        },
        // Enter key triggers immediate fetch
        async onEnterPress(field) {
            const value = this.serverOptions[field].trim();
            this.serverOptions.page = 1;
            await this._fetchData();
            // Update previous value
            this.previousServerOptions[field] = value;
        },
        // Blur triggers fetch only if value has changed
        onBlurFetch(field) {
            const value = this.serverOptions[field].trim();
            if (value !== this.previousServerOptions[field]) {
                this.serverOptions.page = 1;
                this.fetchData();
                this.previousServerOptions[field] = value; // update tracker
            }
        },
        handleSearch(searching) {},
        handleChangePage(page) {},
        handleSort(key, active) {},
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
        // Debounced version, called on typing or filter changes
        this.fetchData = debounce(this._fetchData, 400);
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
        const icon =
            this.$refs[this.$options.name].$el.querySelector(".vs-icon");
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
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
