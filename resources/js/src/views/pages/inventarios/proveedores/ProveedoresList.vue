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
        <div class="w-full text-right">
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                @click="formulario('agregar')">
                <span>Registrar Proveedor</span>
            </vs-button>
        </div>

        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label>Mostrar</label>
                        <v-select :options="mostrarOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="mostrar" class="mb-4 sm:mb-0" />
                    </div>
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label>Estado</label>
                        <v-select :options="estadosOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="estado" class="mb-4 md:mb-0" />
                    </div>
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label>Filtrar Específico</label>
                        <v-select :options="filtrosEspecificos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="filtroEspecifico" class="mb-4 md:mb-0" />
                    </div>
                    <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
                        <label>{{ this.filtroEspecifico.label }}</label>
                        <vs-input class="w-full" icon="search" maxlength="75" placeholder="Filtrar por dato específico"
                            v-model="serverOptions.numero_control" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" />
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <div class="w-full px-2 py-4">
                        <h3 class="text-base">
                            <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />Filtrar por Nombre del
                            Titular
                        </h3>
                    </div>
                    <div class="w-full mb-1 px-2 input-text">
                        <label>Nombre del Proveedor</label>
                        <vs-input class="w-full" icon="search" placeholder="Filtrar por Nombre del Proveedor"
                            v-model="serverOptions.nombre_comercial" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" maxlength="75" />
                    </div>
                </div>
            </vx-card>
        </div>

        <br />
        <vs-table :sst="true" :max-items="serverOptions.per_page.value" :data="proveedores" noDataText="0 Resultados"
            class="tabla-datos">
            <template slot="header">
                <h3>Listado de Proveedores Registrados</h3>
            </template>
            <template slot="thead">
                <vs-th>Clave</vs-th>
                <vs-th>Proveedor</vs-th>
                <vs-th>Contacto</vs-th>
                <vs-th>Teléfono</vs-th>
                <vs-th>Status</vs-th>
                <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="data[indextr].id">
                        <span class="font-semibold">{{ data[indextr].id }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].nombre_comercial">
                        <span class="uppercase">
                            {{ data[indextr].nombre_comercial }}
                        </span>
                    </vs-td>
                    <vs-td :data="data[indextr].nombre_contacto">
                        <span class="uppercase">
                            {{ data[indextr].nombre_contacto }}
                        </span>
                    </vs-td>
                    <vs-td :data="data[indextr].telefono">
                        <span class="">{{ data[indextr].telefono }}</span>
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        <p v-if="data[indextr].status == 1">
                            {{ data[indextr].status_texto }}
                            <span class="dot-success"></span>
                        </p>
                        <p v-else-if="data[indextr].status == 0">
                            {{ data[indextr].status_texto }}
                            <span class="dot-danger"></span>
                        </p>
                    </vs-td>
                    <vs-td :data="data[indextr].id_user">
                        <div class="flex justify-center">
                            <img class="img-btn-18 mx-3" src="@assets/images/edit.svg" title="Modificar Proveedor"
                                @click="openModificar(data[indextr].id)" />
                            <img v-if="data[indextr].status == 1" class="img-btn-22 mx-3" src="@assets/images/trash.svg"
                                title="Desactivar Proveedor" @click="
                                    deleteProveedor(
                                        data[indextr].id,
                                        data[indextr].nombre_comercial
                                    )
                                    " />
                            <img v-else class="img-btn-22 mx-3" src="@assets/images/trash-open.svg" title="Habilitar"
                                @click="
                                    altaProveedor(
                                        data[indextr].id,
                                        data[indextr].nombre_comercial
                                    )
                                    " />
                        </div>
                    </vs-td>
                </vs-tr>
            </template>
        </vs-table>
        <div>
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
        </div>
        <pre ref="pre"></pre>

        <Password :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus"
            :accion="accionNombre">
        </Password>
        <Reporteador :header="'consultar reporte de venta'" :show="openReportesLista" :listadereportes="ListaReportes"
            :request="request" @closeReportes="openReportesLista = false"></Reporteador>
        <FormularioProveedores :id_proveedor="id_proveedor_modificar" :tipo="tipoFormulario"
            :show="verFormularioProveedores" @closeVentana="verFormularioProveedores = false" @retornar_id="retorno_id">
        </FormularioProveedores>
    </div>
</template>

<script>
//planes de venta
import Reporteador from "@pages/Reporteador";

import proveedores from "@services/proveedores";

import FormularioProveedores from "@pages/inventarios/proveedores/FormularioProveedores";

//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */
import { mostrarOptions, PermisosModulo } from "@/VariablesGlobales";

import vSelect from "vue-select";

export default {
    components: {
        "v-select": vSelect,
        Password,
        FormularioProveedores,
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
    data() {
        return {
            //variable
            ListaReportes: [],
            PermisosModulo: PermisosModulo,
            openReportesLista: false,
            mostrarOptions: mostrarOptions,
            mostrar: { label: "15", value: "15" },
            estado: { label: "Todas", value: "" },
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
            filtroEspecifico: { label: "Núm. Proveedor", value: "1" },
            filtrosEspecificos: [
                {
                    label: "Núm. Proveedor",
                    value: "1",
                },
                {
                    label: "Núm. Teléfono",
                    value: "2",
                },
            ],
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                filtro_especifico_opcion: "",
                numero_control: "",
                nombre_comercial: "",
            },
            verPaginado: true,
            total: 0,
            actual: 1,
            proveedores: [],
            //fin variables
            openStatus: false,
            callback: Function,
            accionNombre: "",
            datosModifcar: {},
            tipoFormulario: "",
            verFormularioProveedores: false,
            verModificar: false,
            id_proveedor_modificar: 0,
            /**opciones para filtrar la peticion del server */
            /**user id para bajas y altas */
            proveedor_id: "",
            request: {
                venta_id: "",
                email: "",
            },
        };
    },
    methods: {
        reset(card) {
            card.removeRefreshAnimation(500);
            this.filtroEspecifico = {
                label: "Núm. Proveedor",
                value: "1",
            };
            this.serverOptions.numero_control = "";
            this.mostrar = { label: "15", value: "15" };
            this.estado = { label: "Todos", value: "" };
            this.serverOptions.nombre_comercial = "";
            this.get_data(this.actual);
        },

        get_data(page, evento = "") {
            if (evento == "blur") {
                if (
                    this.serverOptions.nombre_comercial != "" ||
                    this.serverOptions.nombre_comercial == ""
                ) {
                    //la funcion no avanza

                    return false;
                }
                if (
                    this.serverOptions.numero_control == "" ||
                    this.serverOptions.numero_control != ""
                ) {
                    //la funcion no avanza

                    return false;
                }
            }
            let self = this;
            if (proveedores.cancel) {
                proveedores.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.verPaginado = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = this.mostrar.value;
            this.serverOptions.status = this.estado.value;
            this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;
            proveedores
                .get_proveedores(this.serverOptions)
                .then((res) => {
                    this.proveedores = res.data.data;
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
                                text:
                                    "Verifique sus permisos con el administrador del sistema.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "warning",
                                time: 4000,
                            });
                        }
                    }
                });
        },
        handleSearch(searching) { },
        handleChangePage(page) { },
        handleSort(key, active) { },

        //eliminar usuario logicamente

        closeModificar() {
            this.verModificar = false;
        },

        closeStatus() {
            this.openStatus = false;
        },

        formulario(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioProveedores = true;
        },
        openModificar(proveedor_id) {
            this.tipoFormulario = "modificar";
            this.id_proveedor_modificar = proveedor_id;
            this.verFormularioProveedores = true;
        },
        retorno_id(dato) {
            this.get_data(this.actual);
        },
        deleteProveedor(proveedor_id, nombre) {
            this.accionNombre = "deshabilitar proveedor " + nombre;
            this.proveedor_id = proveedor_id;
            this.openStatus = true;
            (async () => {
                this.callback = await this.delete_proveedor;
            })();
        },

        altaProveedor(proveedor_id, nombre) {
            this.accionNombre = "Habilitar proveedor " + nombre;
            this.proveedor_id = proveedor_id;
            this.openStatus = true;
            (async () => {
                this.callback = await this.habilitar_proveedor;
            })();
        },
        async delete_proveedor() {
            this.$vs.loading();
            let datos = {
                proveedor_id: this.proveedor_id,
            };
            try {
                let res = await proveedores.delete_proveedor(datos);
                this.$vs.loading.close();
                this.get_data(this.actual);
                if (res.data >= 1) {
                    this.$vs.notify({
                        title: "Deshabilitar Proveedor",
                        text: "Se ha deshabilitado al proveedor exitosamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                } else {
                    this.$vs.notify({
                        title: "Deshabilitar Proveedor",
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
        async habilitar_proveedor() {
            this.$vs.loading();
            let datos = {
                proveedor_id: this.proveedor_id,
            };
            try {
                let res = await proveedores.alta_proveedor(datos);
                this.$vs.loading.close();
                this.get_data(this.actual);
                if (res.data >= 1) {
                    this.$vs.notify({
                        title: "Habilitar Proveedor",
                        text: "Se ha habilitado al cliente exitosamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                } else {
                    this.$vs.notify({
                        title: "Habilitar Proveedor",
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
    created() {
        this.get_data(this.actual);
    },
};
</script>
