<!--
IDS DE LOS PERMISOS QUE APLICAN EN ESTE MODULO DE CLIENTES
registrar proveedores 11
modificar informacion 12
eliminar proveedores 13
consultar información 14
consultar estados de cuenta15

para saber si se debe desplegar algun boton o accion se hace llamando la funcion global
this.$modulo.permiso(this.$route.path,11)
con la ruta especifica del modulo que se desea consultar y el id del permiso

-->
<template>
    <div>
        <div class="text-right buttons-container-header">
            <vs-button
                class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                color="primary"
                @click="openReporteLotes()"
                type="border"
            >
                <span>Ver Lotes del Inventario</span>
            </vs-button>
            <vs-button
                class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                color="success"
                @click="formulario('agregar')"
            >
                <span>Ajustar Inventario</span>
            </vs-button>
        </div>

        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card
                no-radius
                title="Filtros de selección"
                refresh-content-action
                @refresh="reset"
                :collapse-action="false"
            >
                <div class="flex flex-wrap">
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label class="text-sm opacity-75">Mostrar</label>
                        <v-select
                            :options="mostrarOptions"
                            :clearable="false"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="mostrar"
                            class="mb-4 sm:mb-0"
                        />
                    </div>

                    <div
                        class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2 input-text"
                    >
                        <label class="text-sm opacity-75"
                            >Filtrar Específico</label
                        >
                        <v-select
                            :options="filtrosEspecificos"
                            :clearable="false"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="filtroEspecifico"
                            class="mb-4 md:mb-0"
                        />
                    </div>
                    <div
                        class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-4 px-2 input-text"
                    >
                        <label class="text-sm opacity-75">{{
                            this.filtroEspecifico.label
                        }}</label>
                        <vs-input
                            class="w-full"
                            icon="search"
                            maxlength="75"
                            placeholder="Filtrar por dato específico"
                            v-model="serverOptions.numero_control"
                            v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')"
                        />
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
                    <vs-table
                        :sst="true"
                        :max-items="serverOptions.per_page.value"
                        :data="ajustes"
                        noDataText="0 Resultados"
                        class="tabla-datos"
                    >
                        <template slot="header">
                            <h3>Listado de Ajustes Realizados</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Núm. Ajuste</vs-th>
                            <vs-th>Tipo de Ajuste</vs-th>
                            <vs-th>Fecha del Ajuste</vs-th>
                            <vs-th>Realizado Por</vs-th>
                            <vs-th>Ver Detalle</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr
                                :data="tr"
                                :key="indextr"
                                v-for="(tr, indextr) in data"
                            >
                                <vs-td :data="data[indextr].id">
                                    <span class="font-semibold">{{
                                        data[indextr].id
                                    }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].tipo_ajuste_texto">
                                    <span class="font-semibold">{{
                                        data[indextr].tipo_ajuste_texto
                                    }}</span>
                                </vs-td>
                                <vs-td
                                    :data="data[indextr].fecha_registro_texto"
                                >
                                    <span class="font-semibold">{{
                                        data[indextr].fecha_registro_texto
                                    }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].registro.nombre">
                                    <span class="uppercase">
                                        {{ data[indextr].registro.nombre }}
                                    </span>
                                </vs-td>

                                <vs-td :data="data[indextr].id">
                                    <div class="flex flex-start">
                                        <img
                                            class="cursor-pointer img-btn ml-auto mr-auto"
                                            src="@assets/images/pdf.svg"
                                            title="Modificar"
                                            @click="
                                                openReporte(data[indextr].id)
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
                            :total="this.total"
                            v-model="actual"
                            class="mt-8"
                        ></vs-pagination>
                    </div>
                    <pre ref="pre"></pre>
                </div>
            </div>
        </div>
        <Password
            v-if="openStatus"
            :show="openStatus"
            :callback-on-success="callback"
            @closeVerificar="closeStatus"
            :accion="accionNombre"
        >
        </Password>
        <Reporteador
            v-if="openReportesLista"
            :header="'consultar reporte de venta'"
            :show="openReportesLista"
            :listadereportes="ListaReportes"
            :request="request"
            @closeReportes="closeReportes"
        ></Reporteador>
        <FormularioAjustes
            v-if="verFormularioAjustes"
            :show="verFormularioAjustes"
            @closeVentana="verFormularioAjustes = false"
            @retornar_id="retorno_id"
        >
        </FormularioAjustes>
    </div>
</template>

<script>
//planes de venta
import Reporteador from "@pages/Reporteador";

import inventario from "@services/inventario";

import FormularioAjustes from "@pages/inventarios/ajustes/FormularioAjustes";

//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */
import { mostrarOptions, PermisosModulo } from "@/VariablesGlobales";

import vSelect from "vue-select";

export default {
    name: "AjustesList",
    components: {
        "v-select": vSelect,
        Password,
        FormularioAjustes,
        Reporteador,
    },
    watch: {
        actual: function (newValue, oldValue) {
            this.get_data(this.actual);
        },
        mostrar: function (newValue, oldValue) {
            this.get_data(1);
        },
        estado: function (newVal, previousVal) {
            this.get_data(1);
        },
    },
    computed: {
        noDataFound() {
            return this.ajustes.length === 0;
        },
    },
    data() {
        return {
            //variable
            ListaReportes: [],
            PermisosModulo: PermisosModulo,
            openReportesLista: false,
            mostrarOptions: mostrarOptions,
            mostrar: { label: "15", value: "15" },
            estado: { label: "Todos", value: "" },
            estadosOptions: [
                {
                    label: "Todos",
                    value: "",
                },
                {
                    label: "Activos",
                    value: "1",
                },
                {
                    label: "Cancelados",
                    value: "0",
                },
            ],
            filtroEspecifico: { label: "Núm. Ajuste", value: "1" },
            filtrosEspecificos: [
                {
                    label: "Núm. Ajuste",
                    value: "1",
                },
            ],
            serverOptions: {
                page: "",
                per_page: "",
                filtro_especifico_opcion: "",
                numero_control: "",
            },
            verPaginado: true,
            total: 0,
            actual: 1,
            ajustes: [],
            //fin variables
            openStatus: false,
            callback: Function,
            accionNombre: "",
            verFormularioAjustes: false,
            verModificar: false,
            /**opciones para filtrar la peticion del server */
            /**user id para bajas y altas */
            ajuste_id: "",
            request: {
                id_ajuste: "",
                email: "",
            },
        };
    },
    methods: {
        openReporteLotes(nombre_reporte = "", link = "", parametro = "") {
            this.ListaReportes = [];
            /**agrego los reportes de manera manual */
            this.ListaReportes.push({
                nombre: "Lotes del Inventario",
                url: "/inventario/get_inventario_conteo_pdf",
            });
            //estado de cuenta
            this.request.email = "";
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        openReporte(parametro) {
            this.ListaReportes = [];
            /**agrego los reportes de manera manual */
            this.ListaReportes.push({
                nombre: "Detalle del ajuste de inventario",
                url: "/inventario/get_ajuste_pdf",
            });
            //estado de cuenta
            this.request.email = "";
            this.request.id_ajuste = parametro;
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        reset(card) {
            card.removeRefreshAnimation(500);
            this.filtroEspecifico = {
                label: "Núm. Ajuste",
                value: "1",
            };
            this.serverOptions.numero_control = "";
            this.mostrar = { label: "15", value: "15" };
            this.get_data(this.actual);
        },

        get_data(page, evento = "") {
            if (evento == "blur") {
                if (
                    this.serverOptions.numero_control == "" ||
                    this.serverOptions.numero_control != ""
                ) {
                    //la funcion no avanza
                    return false;
                }
            }
            let self = this;
            if (inventario.cancel) {
                inventario.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.verPaginado = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = this.mostrar.value;
            this.serverOptions.filtro_especifico_opcion =
                this.filtroEspecifico.value;
            inventario
                .get_ajustes(this.serverOptions)
                .then((res) => {
                    this.ajustes = res.data.data;
                    this.total = res.data.last_page;
                    this.actual = res.data.current_page;
                    this.verPaginado = true;
                    this.$vs.loading.close();
                })
                .catch((err) => {
                    this.$vs.loading.close();
                    this.ver = true;
                    if (err.response) {
                        if (err.response.status == 403) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Permiso denegado",
                                text: "Verifique sus permisos con el administrador del sistema.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "warning",
                                time: 4000,
                            });
                        }
                    }
                });
        },
        handleSearch(searching) {},
        handleChangePage(page) {},
        handleSort(key, active) {},

        //eliminar usuario logicamente

        closeModificar() {
            this.verModificar = false;
        },

        closeStatus() {
            this.openStatus = false;
        },

        formulario(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioAjustes = true;
        },
        openModificar(ajuste_id) {
            this.tipoFormulario = "modificar";
            this.id_articulo_modificar = ajuste_id;
            this.verFormularioAjustes = true;
        },
        retorno_id(dato) {
            this.openReporte(dato);
        },
        closeReportes() {
            this.openReportesLista = false;
            this.get_data(1);
        },
        deleteArticulo(ajuste_id, nombre) {
            this.accionNombre = "Deshabilitar Artículo " + nombre;
            this.ajuste_id = ajuste_id;
            this.openStatus = true;
            (async () => {
                this.callback = await this.delete_articulo;
            })();
        },

        altaArticulo(ajuste_id, nombre) {
            this.accionNombre = "Habilitar Artículo " + nombre;
            this.ajuste_id = ajuste_id;
            this.openStatus = true;
            (async () => {
                this.callback = await this.habilitar_articulo;
            })();
        },
        async delete_articulo() {
            this.$vs.loading();
            let datos = {
                ajuste_id: this.ajuste_id,
            };
            try {
                let res = await inventario.delete_articulo(datos);
                this.$vs.loading.close();
                this.get_data(this.actual);
                if (res.data >= 1) {
                    this.$vs.notify({
                        title: "Deshabilitar Artículo",
                        text: "Se ha deshabilitado el artículo exitosamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                } else {
                    this.$vs.notify({
                        title: "Deshabilitar Artículo",
                        text: "No se realizaron cambios.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "warning",
                        time: 5000,
                    });
                }
            } catch (err) {
                this.$vs.loading.close();
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text: "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 8000,
                        });
                    } else if (err.response.status == 422) {
                        /**error de validacion */
                        this.errores = err.response.data.error;
                    } else if (err.response.status == 409) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Control de Artículos",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 15000,
                        });
                    }
                }
            }
        },
        async habilitar_articulo() {
            this.$vs.loading();
            let datos = {
                ajuste_id: this.ajuste_id,
            };
            try {
                let res = await inventario.enable_disable(datos);
                this.$vs.loading.close();
                this.get_data(this.actual);
                if (res.data >= 1) {
                    this.$vs.notify({
                        title: "Habilitar Artículo",
                        text: "Se ha habilitado el artículo exitosamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                } else {
                    this.$vs.notify({
                        title: "Habilitar Artículo",
                        text: "No se realizaron cambios.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "warning",
                        time: 5000,
                    });
                }
            } catch (error) {
                this.$vs.loading.close();
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text: "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 8000,
                        });
                    } else if (err.response.status == 422) {
                        /**error de validacion */
                        this.errores = err.response.data.error;
                    }
                }
            }
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
        (async () => {
            await this.get_data(this.actual);
        })();
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
    },
    beforeDestroy() {},
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
