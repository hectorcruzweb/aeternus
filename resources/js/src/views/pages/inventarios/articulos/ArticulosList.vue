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
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
                @click="openFormLabels()" type="border">
                <span>Imprimir Etiquetas</span>
            </vs-button>
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
                @click="openReporte()" type="border">
                <span>Ver Inventario</span>
            </vs-button>
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                @click="formulario('agregar')">
                <span>Agregar Artículos</span>
            </vs-button>
        </div>

        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset"
                :collapse-action="false">
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 mb-1 px-2 input-text">
                        <label class="">Mostrar</label>
                        <v-select :options="mostrarOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="mostrar" class="sm:mb-0" />
                    </div>
                    <div class="w-full sm:w-12/12 md:w-6/12 lg:w-3/12 xl:w-3/12 mb-1 px-2 input-text">
                        <label class="">Estado</label>
                        <v-select :options="estadosOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="estado" class="" />
                    </div>
                    <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2 input-text">
                        <label class="text-sm opacity-75">Filtrar Específico</label>
                        <v-select :options="filtrosEspecificos" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-model="filtroEspecifico" class="mb-4 md:mb-0" />
                    </div>
                    <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-4 px-2 input-text">
                        <label class="text-sm opacity-75">
                            {{ this.filtroEspecifico.label }}
                        </label>
                        <vs-input class="w-full" icon="search" maxlength="75" placeholder="Filtrar por dato específico"
                            v-model="serverOptions.numero_control" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" />
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <div class="w-full px-2">
                        <h3 class="text-base font-semibold my-3">
                            <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />Filtrar por Nombre del
                            Artículo o Servicio
                        </h3>
                    </div>
                    <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text">
                        <label class="text-sm opacity-75">Nombre del Artículo</label>
                        <vs-input class="w-full" icon="search" placeholder="Filtrar por Nombre del Artículo o Servicio"
                            v-model="serverOptions.articulo" v-on:keyup.enter="get_data(1)"
                            v-on:blur="get_data(1, 'blur')" maxlength="75" />
                    </div>
                </div>
            </vx-card>
        </div>

        <br />
        <vs-table :sst="true" @search="handleSearch" @change-page="handleChangePage" @sort="handleSort"
            :max-items="serverOptions.per_page.value" :data="articulos" noDataText="0 Resultados" class="tabla-datos">
            <template slot="header">
                <h3>Listado de Artículos y Servicios Registrados</h3>
            </template>
            <template slot="thead">
                <vs-th>Núm. Artículo</vs-th>
                <vs-th>Código Barras</vs-th>
                <vs-th>Descripción</vs-th>
                <vs-th>Tipo Artículo</vs-th>
                <vs-th hidden>Caduca</vs-th>
                <vs-th>($) Precio Compra</vs-th>
                <vs-th>($) Precio Venta</vs-th>
                <vs-th>Existencias</vs-th>
                <vs-th>Status</vs-th>
                <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="data[indextr].id">
                        <span class="font-semibold">{{ data[indextr].id }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].codigo_barras">
                        <span class="font-semibold">{{ data[indextr].codigo_barras }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].descripcion">
                        <span class="uppercase">{{ data[indextr].descripcion }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].tipo_articulo.tipo">
                        <span class="uppercase">{{
                            data[indextr].tipo_articulo.tipo
                            }}</span>
                    </vs-td>
                    <vs-td hidden :data="data[indextr].caduca_texto">
                        <span class="uppercase">{{ data[indextr].caduca_texto }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].precio_compra">
                        <span class="uppercase">$ {{ data[indextr].precio_compra | numFormat("0,000.00") }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].precio_venta">
                        <span class="uppercase">$ {{ data[indextr].precio_venta | numFormat("0,000.00") }}</span>
                    </vs-td>
                    <vs-td :data="data[indextr].existencia">
                        <span class="uppercase">{{ data[indextr].existencia }}</span>
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        <p v-if="data[indextr].status == 0">
                            Deshabilitado
                            <span class="dot-danger"></span>
                        </p>
                        <p v-else-if="data[indextr].status == 1">
                            Activo
                            <span class="dot-success"></span>
                        </p>
                    </vs-td>
                    <vs-td :data="data[indextr].id_user">
                        <div class="flex justify-center">
                            <img class="img-btn-18 mx-3" src="@assets/images/edit.svg" title="Modificar"
                                @click="openModificar(data[indextr].id)" />
                            <img v-if="data[indextr].status == 1" class="img-btn-22 mx-3"
                                src="@assets/images/switchon.svg" title="Deshabilitar" @click="
                                    deleteArticulo(data[indextr].id, data[indextr].descripcion)
                                    " />
                            <img v-else class="img-btn-22 mx-3" src="@assets/images/switchoff.svg" title="Habilitar"
                                @click="
                                    altaArticulo(data[indextr].id, data[indextr].descripcion)
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
        <Reporteador :header="'Consultar Inventario'" :show="openReportesLista" :listadereportes="ListaReportes"
            :request="request" @closeReportes="openReportesLista = false"></Reporteador>
        <FormularioArticulos :id_articulo="id_articulo_modificar" :tipo="tipoFormulario" :show="verFormularioArticulos"
            @closeVentana="verFormularioArticulos = false" @retornar_id="retorno_id"></FormularioArticulos>
        <FormularioLabel :show="verFormularioLabels" @closeVentana="verFormularioLabels = false"
            @retornar_id="retorno_id">
        </FormularioLabel>
    </div>
</template>

<script>
//planes de venta
import Reporteador from "@pages/Reporteador";

import inventario from "@services/inventario";

import FormularioArticulos from "@pages/inventarios/articulos/FormularioArticulos";

import FormularioLabel from "@pages/inventarios/articulos/FormLabel";

//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */
import { mostrarOptions, PermisosModulo } from "@/VariablesGlobales";

import vSelect from "vue-select";

export default {
    components: {
        "v-select": vSelect,
        Password,
        FormularioArticulos,
        Reporteador,
        FormularioLabel,
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
            request: {
                destinatario: "",
                id_tipo_propiedad: "",
                email: "",
            },
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
            filtroEspecifico: { label: "Núm. Artículo", value: "1" },
            filtrosEspecificos: [
                {
                    label: "Núm. Artículo",
                    value: "1",
                },
                {
                    label: "Código de Barras",
                    value: "2",
                },
            ],
            serverOptions: {
                page: "",
                per_page: "",
                status: "",
                filtro_especifico_opcion: "",
                numero_control: "",
                articulo: "",
            },
            verPaginado: true,
            total: 0,
            actual: 1,
            articulos: [],
            //fin variables
            openStatus: false,
            callback: Function,
            accionNombre: "",
            datosModifcar: {},
            tipoFormulario: "",
            verFormularioArticulos: false,
            verModificar: false,
            id_articulo_modificar: 0,
            /**opciones para filtrar la peticion del server */
            /**user id para bajas y altas */
            articulo_id: "",
            request: {
                venta_id: "",
                email: "",
            },
            id_articulo_label: 0,
            verFormularioLabels: false,
        };
    },
    methods: {
        openReporte(nombre_reporte = "", link = "", parametro = "") {
            this.ListaReportes = [];
            /**agrego los reportes de manera manual */
            this.ListaReportes.push({
                nombre: "Inventario General",
                url: "/inventario/get_inventario_pdf",
            });
            //estado de cuenta
            this.request.email = "";
            this.request.id_tipo_propiedad = "";
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        reset(card) {
            card.removeRefreshAnimation(500);
            this.filtroEspecifico = {
                label: "Núm. Artículo",
                value: "1",
            };
            this.serverOptions.numero_control = "";
            this.mostrar = { label: "15", value: "15" };
            this.estado = { label: "Todos", value: "" };
            this.serverOptions.articulo = "";
            this.get_data(this.actual);
        },

        get_data(page, evento = "") {
            if (evento == "blur") {
                if (
                    this.serverOptions.articulo != "" ||
                    this.serverOptions.articulo == ""
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
            if (inventario.cancel) {
                inventario.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            this.verPaginado = false;
            this.serverOptions.page = page;
            this.serverOptions.per_page = this.mostrar.value;
            this.serverOptions.status = this.estado.value;
            this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;
            inventario
                .get_inventario(this.serverOptions)
                .then((res) => {
                    this.articulos = res.data.data;
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
            this.verFormularioArticulos = true;
        },
        openModificar(articulo_id) {
            this.tipoFormulario = "modificar";
            this.id_articulo_modificar = articulo_id;
            this.verFormularioArticulos = true;
        },
        openFormLabels() {
            this.verFormularioLabels = true;
        },

        retorno_id(dato) {
            this.get_data(this.actual);
        },
        deleteArticulo(articulo_id, nombre) {
            this.accionNombre = "Deshabilitar Artículo " + nombre;
            this.articulo_id = articulo_id;
            this.openStatus = true;
            (async () => {
                this.callback = await this.delete_articulo;
            })();
        },

        altaArticulo(articulo_id, nombre) {
            this.accionNombre = "Habilitar Artículo " + nombre;
            this.articulo_id = articulo_id;
            this.openStatus = true;
            (async () => {
                this.callback = await this.habilitar_articulo;
            })();
        },
        async delete_articulo() {
            this.$vs.loading();
            let datos = {
                articulo_id: this.articulo_id,
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
                articulo_id: this.articulo_id,
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
    created() {
        this.get_data(this.actual);
    },
};
</script>
