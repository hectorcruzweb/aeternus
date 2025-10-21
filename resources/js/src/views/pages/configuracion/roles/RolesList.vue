<template>
    <div>
        <div class="text-right buttons-container-header">
            <vs-button class="w-full md:w-auto md:ml-2 md:mt-0" color="success" @click="verFormulario('agregar')">
                <span>Registrar Rol de Usuaurio</span>
            </vs-button>
        </div>

        <div class="pt-6 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 px-2">
                        <div class="w-full input-text pb-2">
                            <label class="">Mostrar</label>
                            <v-select :options="mostrarOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-model="mostrar" class="sm:mb-0" />
                        </div>
                    </div>
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 px-2">
                        <div class="w-full input-text pb-2">
                            <label class="">Estado</label>
                            <v-select :options="estadosOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-model="estado" class="md:mb-0" />
                        </div>
                    </div>
                    <div class="w-full sm:w-12/12 md:w-12/12 lg:w-6/12 xl:w-6/12 px-2">
                        <div class="w-full input-text pb-2">
                            <label class="text-sm opacity-75">Nombre del rol</label>
                            <vs-input class="w-full" icon="search" placeholder="Filtrar por nombre de rol"
                                v-model="nombre" v-on:keyup.enter="get_data(1)" v-on:blur="get_data(1, 'blur')" />
                        </div>
                    </div>
                </div>
            </vx-card>
        </div>

        <br />


        <div id="resultados" class="mt-5 flex flex-col flex-1">
            <div v-if="noDataFound" class="w-full skeleton flex-1 items-center justify-center">
                <span class="text-gray-600 text-lg font-normal">No hay datos que mostrar</span>
            </div>
            <div v-else id="results" class="w-full flex flex-wrap">
                <div class="w-full py-2">
                    <vs-table :sst="true" @search="handleSearch" @change-page="handleChangePage" @sort="handleSort"
                        :max-items="serverOptions.per_page.value" :data="roles" noDataText="0 Resultados"
                        class="tabla-datos">
                        <template slot="header">
                            <h3>Listado de Roles registrados</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Número de Rol</vs-th>
                            <vs-th>Rol</vs-th>

                            <vs-th>Estado</vs-th>

                            <vs-th>Acciones</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                <vs-td :data="data[indextr].id_rol">{{ data[indextr].id_rol }}</vs-td>
                                <vs-td :data="data[indextr].nombre">{{ data[indextr].rol }}</vs-td>

                                <vs-td :data="data[indextr].estado">
                                    <p v-if="data[indextr].status_rol == 1">
                                        Activo
                                        <span class="dot-success"></span>
                                    </p>
                                    <p v-else>
                                        Sin Acceso
                                        <span class="dot-danger"></span>
                                    </p>
                                </vs-td>
                                <vs-td :data="data[indextr].id_rol">
                                    <div class="flex justify-center">
                                        <img class="img-btn-18 mx-2" src="@assets/images/edit.svg" title="Modificar"
                                            @click="openModificar(data[indextr].id_rol)" />
                                        <img class="img-btn-24 mx-2" src="@assets/images/trash.svg" title="Eliminar Rol"
                                            @click="deleteRol(data[indextr].id_rol, data[indextr].rol)" />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                    <div>
                        <vs-pagination v-if="ver" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>

        <formularioRoles v-if="verFormularioRoles" :id_rol="id_rol_modificar" :tipo="tipoFormulario"
            :show="verFormularioRoles" @closeVentana="closeVentana" @get_data="get_data(actual)"></formularioRoles>

        <Password v-if="openStatus" :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus"
            :accion="accionNombre">
        </Password>
    </div>
</template>

<script>
import formularioRoles from "@pages/configuracion/roles/formularioRoles";

//componente de password
import Password from "@pages/confirmar_password";

import usuarios from "@services/Usuarios";
import roles from "@services/Roles";
/**VARIABLES GLOBALES */
import { mostrarOptions, estadosOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";

export default {
    name: "RolesList",
    components: {
        "v-select": vSelect,
        Password,
        formularioRoles,
    },
    computed: {
        noDataFound() {
            return (
                this.roles.length === 0
            );
        },
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
            tipoFormulario: "",
            id_rol_modificar: 0,
            openStatus: false,
            callback: Function,
            accionNombre: "",
            verModificar: false,
            datosModifcar: {},
            verFormularioRoles: false,
            activeTab: 0,
            ver: true,
            total: 0,
            actual: 1,
            mostrarOptions: mostrarOptions,
            estadosOptions: estadosOptions,
            rolesOptions: [],
            mostrar: { label: "15", value: "15" },
            estado: { label: "Todos", value: "" },
            nombre: "",
            selected: [],
            roles: [],
            /**opciones para filtrar la peticion del server */
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                rol_id: "",
                nombre: "",
            },
            /**user id para bajas y altas */
            rol_id: "",
        };
    },
    methods: {
        reset(card) {
            card.removeRefreshAnimation(500);
            this.mostrar = { label: "15", value: "15" };
            this.estado = { label: "Todos", value: "" };
            this.nombre = "";
            this.get_data(this.actual);
        },

        get_data(page, evento = "") {
            if (evento == "blur") {
                if (this.nombre != "") {
                    //la funcion no avanza
                    return false;
                }
            }
            let self = this;
            if (roles.cancel) {
                roles.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.ver = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = this.mostrar.value;
            this.serverOptions.status = this.estado.value;
            this.serverOptions.nombre = this.nombre;
            roles
                .get_roles(this.serverOptions)
                .then((res) => {
                    this.roles = res.data.data;
                    this.total = res.data.last_page;
                    this.actual = res.data.current_page;
                    this.ver = true;
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
        closeVentana() {
            this.verFormularioRoles = false;
        },
        openModificar(id_rol) {
            this.id_rol_modificar = id_rol;
            this.verFormulario("modificar");
        },
        //eliminar usuario logicamente
        deleteRol(rol_id, nombre) {
            this.accionNombre = "eliminar rol " + nombre;
            this.rol_id = rol_id;
            this.openStatus = true;
            this.callback = this.delete_rol;
        },
        delete_rol() {
            this.$vs.loading();
            let datos = {
                rol_id: this.rol_id,
            };
            roles
                .delete_rol(datos)
                .then((res) => {
                    this.$vs.loading.close();
                    this.get_data(this.actual);
                    if (res.data >= 1) {
                        this.$vs.notify({
                            title: "Eliminar Rol de Usuario",
                            text: "Se ha eliminado el rol exitosamente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "success",
                            time: 4000,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Eliminar Rol",
                            text: "No se realizaron cambios.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "primary",
                            time: 4000,
                        });
                    }
                })
                .catch((err) => {
                    this.$vs.loading.close();
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
                        } else if (err.response.status == 422) {
                            /**error de validacion */
                            this.errores = err.response.data.error;
                        } else if (err.response.status == 409) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Eliminar Rol",
                                text: err.response.data.error,
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                time: 8000,
                            });
                        }
                    }
                });
        },

        closeModificar() {
            this.verModificar = false;
        },

        closeStatus() {
            this.openStatus = false;
        },

        verFormulario(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioRoles = true;
        },
    },

    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
        this.get_data(this.actual);
    },
    beforeDestroy() {
        this.$log("Before Component destroyed! " + this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
