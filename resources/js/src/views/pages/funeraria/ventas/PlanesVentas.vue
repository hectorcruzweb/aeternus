<template>
    <div class="centerx">
        <vs-popup class="forms-popup popup-85" fullscreen title="listado de planes funerarios"
            :active.sync="showVentana" ref="planes_planes">
            <div class="form-group">
                <div class="title-form-group">Planes Funerarios Registrados</div>
                <div class="form-group-content">
                    <div class="flex flex-wrap">
                        <div class="w-full">
                            <div class="flex flex-wrap-reverse xl:flex-wrap">
                                <div class="w-full xl:w-5/12">
                                    <label>
                                        <span>Filtrar por plan Funerario:</span>
                                    </label>
                                    <v-select :options="tipo_planes" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="plan_tipo" class="w-full">
                                        <div slot="no-options">Seleccione un Plan Funerario</div>
                                    </v-select>
                                </div>
                                <div class="w-full sm:w-12/12 xl:w-7/12 py-6 xl:pt-0">
                                    <div class="w-full text-right">
                                        <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 xl:mt-6"
                                            color="primary" type="border" @click="
                                                openReporte(
                                                    'Todos los Planes (Español)',
                                                    '/funeraria/planes_funerarios',
                                                    ''
                                                )
                                                ">
                                            <span>Imprimir lista de planes</span>
                                        </vs-button>
                                        <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 xl:mt-6"
                                            color="primary" @click="agregarPrecios()">
                                            <span>Agregar precios</span>
                                        </vs-button>
                                        <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 xl:mt-6"
                                            color="success" @click="agregarPlan()">
                                            <span>Crear planes funerarios</span>
                                        </vs-button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="planes.length > 0" class="w-full pt-6">
                            <div v-for="(plan, index) in planes" :key="index"
                                :class="[indexPrecios(index, plan.id), 'w-full']">
                                <vs-table :data="plan.precios" noDataText="0 Resultados" class="tabla-datos">
                                    <template slot="header">
                                        <div class="w-full py-2">
                                            <div class="flex flex-wrap">
                                                <div class="w-full lg:w-8/12 px-2 text-center lg:text-left">
                                                    <span class="uppercase size-base font-medium">
                                                        {{ plan.plan }}
                                                    </span>
                                                </div>
                                                <div class="w-full lg:w-4/12 px-2">
                                                    <div class="w-full text-center lg:text-right">
                                                        <vs-button class="sm:w-auto md:ml-2 my-2 lg:my-0"
                                                            color="primary" @click="openModificarPlan(plan.id)"
                                                            type="line">
                                                            <span>Editar</span>
                                                        </vs-button>

                                                        <vs-button v-if="plan.status == 1"
                                                            class="sm:w-auto md:ml-2 my-2 lg:my-0" color="danger"
                                                            @click="
                                                                enable_disable_plan(
                                                                    plan.id,
                                                                    plan.plan,
                                                                    'deshabilitar'
                                                                )
                                                                " type="line">
                                                            <span>Desactivar</span>
                                                        </vs-button>
                                                        <vs-button v-else class="sm:w-auto md:ml-2 my-2 lg:my-0"
                                                            color="success" @click="
                                                                enable_disable_plan(
                                                                    plan.id,
                                                                    plan.plan,
                                                                    'habilitar'
                                                                )
                                                                " type="line">
                                                            <span>Habilitar</span>
                                                        </vs-button>
                                                        <vs-button class="sm:w-auto md:ml-2 my-2 lg:my-0" color="gray"
                                                            @click="
                                                                openReportePlan(
                                                                    'Precios x Plan (Español)',
                                                                    '/funeraria/pdf_plan_funerario',
                                                                    plan.id
                                                                )
                                                                " type="line">
                                                            <span>Imprimir</span>
                                                        </vs-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template slot="thead">
                                        <vs-th>#</vs-th>
                                        <vs-th>Pago Inicial ($)</vs-th>
                                        <vs-th>Costo Neto ($)</vs-th>

                                        <vs-th>Abono Mensual ($)</vs-th>

                                        <vs-th>Financiamiento</vs-th>
                                        <vs-th>Descripción</vs-th>
                                        <vs-th>Acciones</vs-th>
                                    </template>
                                    <template slot-scope="{ data }">
                                        <vs-tr :data="precio" :key="index_precio"
                                            v-for="(precio, index_precio) in data">
                                            <vs-td>
                                                <span class="font-semibold">{{
                                                    index_precio + 1
                                                    }}</span>
                                            </vs-td>
                                            <vs-td>$
                                                {{ precio.pago_inicial | numFormat("0,000.00") }}</vs-td>
                                            <vs-td>$
                                                {{ precio.costo_neto | numFormat("0,000.00") }}</vs-td>

                                            <vs-td :data="data[index_precio].pago_mensual">$
                                                {{ precio.pago_mensual | numFormat("0,000.00") }}</vs-td>

                                            <vs-td>{{ precio.tipo_financiamiento }}</vs-td>
                                            <vs-td>{{ precio.descripcion }}</vs-td>

                                            <vs-td>
                                                <img class="img-btn-18 mx-3" src="@assets/images/edit.svg"
                                                    title="Modificar" @click="openModificar(data[index_precio].id)" />
                                                <img v-if="data[index_precio].status == 1" class="img-btn-24 mx-3"
                                                    src="@assets/images/switchon.svg" title="Deshabilitar" @click="
                                                        enable_disable_precio(
                                                            data[index_precio].id,
                                                            data[index_precio].descripcion,
                                                            'deshabilitar'
                                                        )
                                                        " />

                                                <img v-else class="img-btn-24 mx-3" src="@assets/images/switchoff.svg"
                                                    title="Habilitar" @click="
                                                        enable_disable_precio(
                                                            data[index_precio].id,
                                                            data[index_precio].descripcion,
                                                            'habilitar'
                                                        )
                                                        " />
                                            </vs-td>
                                        </vs-tr>
                                    </template>
                                </vs-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--componente de confirmar sin contraseña-->
            <ConfirmarDanger :z_index="'z-index58k'" :show="operConfirmar" :callback-on-success="callback"
                @closeVerificar="operConfirmar = false"
                :accion="'¿Desea eliminar este plan de mensualidades? Los datos quedarán eliminados del sistema.'"
                :confirmarButton="'Eliminar'"></ConfirmarDanger>
            <Password :show="openPassword" :callback-on-success="callbackPassword"
                @closeVerificar="openPassword = false" :accion="accionPassword"></Password>

            <FormularioPrecios :id_precio="id_precio_modificar" :tipo="tipoFormulario" :show="verFormularioPrecios"
                @closeVentana="verFormularioPrecios = false" @retornar_id="retorno_plan"></FormularioPrecios>

            <FormularioPlanes :id_plan="id_plan_modificar" :tipo="tipoFormularioPlan" :show="verFormularioPlan"
                @closeVentana="verFormularioPlan = false" @retornar="retorno_plan"></FormularioPlanes>

            <Reporteador :header="'Consultar precios x propiedad'" :show="openReportesLista"
                :listadereportes="ListaReportes" :request="request" @closeReportes="openReportesLista = false">
            </Reporteador>
            <!--fin de compornentes-->
        </vs-popup>
    </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import ConfirmarDanger from "@pages/ConfirmarDanger";
import Password from "@pages/confirmar_password";
import planes from "@services/planes";
import FormularioPrecios from "@pages/funeraria/ventas/FormularioPrecios";
import FormularioPlanes from "@pages/funeraria/ventas/FormularioPlanes";
import vSelect from "vue-select";
export default {
    props: {
        show: {
            type: Boolean,
            required: true,
        },
    },
    watch: {
        show: function (newValue, oldValue) {
            if (newValue == true) {
                this.$refs["planes_planes"].$el.querySelector(
                    ".vs-icon"
                ).onclick = () => {
                    this.cancelar();
                };

                (async () => {
                    await this.get_planes();
                })();
            } else {
                /**cerrar ventana */
                this.datosVenta = [];
                this.total = 0;
            }
        },
    },
    components: {
        ConfirmarDanger,
        Password,
        "v-select": vSelect,
        FormularioPrecios,
        Reporteador,
        FormularioPlanes,
    },
    data() {
        return {
            plan_tipo: {},
            tipo_planes: [],
            tipoFormularioPlan: "",
            verFormularioPlan: false,
            id_plan_modificar: 0,
            /**variables del modulo */
            /**reporteador */
            openReportesLista: false,
            ListaReportes: [],
            request: {
                destinatario: "",
                id_tipo_propiedad: "",
                email: "",
            },

            tipoFormularioPrecio: "",
            /**reportrador */
            selected: [],
            planes: [],
            planes_tipos: [],
            propiedad_tipo: {},
            verFormularioPrecios: false,
            tipoFormulario: "",
            id_precio_modificar: 0,
            /** */
            operConfirmar: false,
            openPassword: false,
            accionPassword: "",
            callback: Function,
            callbackPassword: Function,
            //datos que mando para actualizar
        };
    },
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
    methods: {
        async get_planes() {
            try {
                this.$vs.loading();
                let res = await planes.get_planes(false, "");
                this.planes = res.data;
                /**llenando los tipos de propiedad para el select */
                this.tipo_planes = [];
                this.tipo_planes.push({
                    label: "Todos los Planes",
                    value: "",
                });
                res.data.forEach((element) => {
                    this.tipo_planes.push({
                        label: element.plan,
                        value: element.id,
                    });
                    //la primero opcion
                    this.plan_tipo = this.tipo_planes[0];
                });
                this.$vs.loading.close();
            } catch (err) {
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
            }
        },
        agregarPlan() {
            this.tipoFormularioPlan = "agregar";
            this.verFormularioPlan = true;
        },
        openModificarPlan(id_plan) {
            this.tipoFormularioPlan = "modificar";
            this.id_plan_modificar = id_plan;
            this.verFormularioPlan = true;
        },
        enable_disable_plan(id_plan, plan, accion) {
            this.accionPassword = accion + " plan " + plan;
            this.id_plan_modificar = id_plan;
            this.openPassword = true;
            this.callbackPassword = this.enable_disable_planes;
        },
        enable_disable_planes() {
            this.$vs.loading();
            let datos = {
                id_plan: this.id_plan_modificar,
            };
            planes
                .enable_disable_planes(datos)
                .then((res) => {
                    this.$vs.loading.close();
                    (async () => {
                        await this.get_planes();
                    })();
                    if (res.data >= 1) {
                        this.$vs.notify({
                            title: "Cambiar estatus del plan funerario",
                            text: "Se ha actualizado el estatus exitosamente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "success",
                            time: 4000,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Cambiar estatus del plan funerario",
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
                                title: "Cambiar estatus del precio",
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

        agregarPrecios() {
            this.tipoFormulario = "agregar";
            this.verFormularioPrecios = true;
        },
        /**funciones del modulo */
        openReportePlan(nombre_reporte = "", link = "", parametro = "") {
            this.ListaReportes = [];
            /**agrego los reportes de manera manual */
            this.ListaReportes.push({
                nombre: "Precios x Plan (Español)",
                url: "/funeraria/pdf_plan_funerario/es",
            });
            this.ListaReportes.push({
                nombre: "Precios x Plan (Inglés)",
                url: "/funeraria/pdf_plan_funerario/en",
            });
            //estado de cuenta
            this.request.email = "";
            this.request.id_plan = parametro;
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        openReporte(nombre_reporte = "", link = "", parametro = "") {
            this.ListaReportes = [];
            /**agrego los reportes de manera manual */
            this.ListaReportes.push({
                nombre: "Todos los Planes (Español)",
                url: "/funeraria/planes_funerarios/es",
            });
            this.ListaReportes.push({
                nombre: "Todos los Planes (Inglés)",
                url: "/funeraria/planes_funerarios/en",
            });
            //estado de cuenta
            this.request.email = "";
            this.request.id_tipo_propiedad = this.propiedad_tipo.value;
            this.openReportesLista = true;
            this.$vs.loading.close();
        },
        openModificar(id_precio) {
            this.tipoFormulario = "modificar";
            this.id_precio_modificar = id_precio;
            this.verFormularioPrecios = true;
        },
        agregar() {
            this.tipoFormulario = "agregar";
            this.verFormularioPrecios = true;
        },
        indexPrecios(index, plan_id) {
            if (this.plan_tipo.value != "") {
                if (plan_id != this.plan_tipo.value) {
                    return "hidden";
                }
            } else {
                if (index == 0) {
                    return "";
                } else {
                    return "pt-16";
                }
            }
        },
        async get_financiamientos() {
            try {
                this.$vs.loading();
                let res = await planes.get_financiamientos();
                this.planes = res.data;
                /**llenando los tipos de propiedad para el select */
                this.planes_tipos = [];
                this.planes_tipos.push({
                    label: "Todas las Propiedades",
                    value: "",
                });
                this.planes.forEach((element) => {
                    this.planes_tipos.push({
                        label: "Tipo " + element.tipo,
                        value: element.id,
                    });
                    //la primero opcion
                    this.propiedad_tipo = this.planes_tipos[0];
                });
                this.$vs.loading.close();
            } catch (err) {
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
            }
        },
        cancelar() {
            this.$emit("closePlanesFuneraria");
            return;
        },
        retorno_plan(dato) {
            (async () => {
                await this.get_planes();
            })();
        },
        enable_disable_precio(id_precio, precio, accion) {
            this.accionPassword = accion + " precio " + precio;
            this.id_precio_modificar = id_precio;
            this.openPassword = true;
            this.callbackPassword = this.enable_disable;
        },
        enable_disable() {
            this.$vs.loading();
            let datos = {
                id_precio: this.id_precio_modificar,
            };
            planes
                .enable_disable(datos)
                .then((res) => {
                    this.$vs.loading.close();
                    (async () => {
                        await this.get_planes();
                    })();
                    if (res.data >= 1) {
                        this.$vs.notify({
                            title: "Cambiar estatus del precio",
                            text: "Se ha actualizado el estatus exitosamente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "success",
                            time: 4000,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Cambiar estatus del precio",
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
                                title: "Cambiar estatus del precio",
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
    },
    created() { },
};
</script>
