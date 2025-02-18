<template>
    <div>
        <vs-popup :class="['forms-popup', z_index]" fullscreen close="cancelar" :title="getTipoformulario == 'agregar'
            ? 'Elaborar cotización'
            : 'modificar venta en general'" :active.sync="showVentana">
            <!--inicio de cotizacion-->
            <div class="flex flex-wrap">
                <div class="w-full xl:w-6/12 px-2 py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">Datos del Cliente</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-12/12 px-2 input-text">
                                    <label>
                                        Nombre
                                        <span>(*)</span>
                                    </label>
                                    <vs-input v-validate="'required'
                                        " name="solicitud" data-vv-as=" " type="text" class="w-full" focused
                                        placeholder="Ej. José Pérez" v-model="form.solicitud" maxlength="12" />
                                    <span>{{ errors.first("solicitud") }}</span>
                                    <span v-if="this.errores.solicitud">{{
                                        errores.solicitud[0]
                                        }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Teléfono
                                        <span>(*)</span>
                                    </label>
                                    <vs-input v-validate="'required'
                                        " name="solicitud" data-vv-as=" " type="text" class="w-full"
                                        placeholder="Ej. 6692145634" v-model="form.solicitud" maxlength="12" />
                                    <span>{{ errors.first("solicitud") }}</span>
                                    <span v-if="this.errores.solicitud">{{
                                        errores.solicitud[0]
                                        }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Email
                                        <span>(*)</span>
                                    </label>
                                    <vs-input v-validate="'required'
                                        " name="email" data-vv-as=" " type="text" class="w-full"
                                        placeholder="Ej. jose@gmail.com" v-model="form.solicitud" maxlength="12" />
                                    <span>{{ errors.first("solicitud") }}</span>
                                    <span v-if="this.errores.solicitud">{{
                                        errores.solicitud[0]
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full xl:w-6/12 px-2 py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">Datos de la Cotización</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-12/12 px-2 input-text">
                                    <label>
                                        Seleccione un Plan Funerario
                                        <span>(*)</span>
                                    </label>
                                    <v-select :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " :options="planes_funerarios" :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="form.plan_funerario" class="w-full"
                                        name=" plan_validacion" data-vv-as=" ">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Plan
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("plan_validacion") }}</span>
                                    <span v-if="this.errores['plan_funerario.value']">{{
                                        errores["plan_funerario.value"][0]
                                    }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>Fecha de la Venta (Año-Mes-Dia)</label>
                                    <span>(*)</span>
                                    <flat-pickr :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " name="fecha_venta" data-vv-as=" " :config="configdateTimePicker"
                                        v-model="form.fecha_venta" placeholder="Fecha de la Venta" class="w-full" />
                                    <span>{{ errors.first("fecha_venta") }}</span>
                                    <span v-if="this.errores.fecha_venta">{{
                                        errores.fecha_venta[0]
                                    }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Seleccione un Plan Funerario
                                        <span>(*)</span>
                                    </label>
                                    <v-select :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " :options="planes_funerarios" :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="form.plan_funerario" class="w-full"
                                        name=" plan_validacion" data-vv-as=" ">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Plan
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("plan_validacion") }}</span>
                                    <span v-if="this.errores['plan_funerario.value']">{{
                                        errores["plan_funerario.value"][0]
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <vs-checkbox ref="permisos_modulo" color="success" v-model="form.planes_predefinidos_b"
                    class="ml-auto size-small font-medium color-copy pt-2">INCLUIR PLANES
                    PREDEFINIDOS</vs-checkbox>
                <div class="w-full px-2 py-4" v-show="form.planes_predefinidos_b">
                    <div class="form-group py-6">
                        <div class="title-form-group">Presupuestos Predefinidos</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-6/12 px-2 py-2">
                                    <button class="w-full btn-icon-50-dark">
                                        ver planes de funeraria predefinidos <img src="@assets/images/folder.svg" />
                                    </button>
                                </div>
                                <div class="w-full lg:w-6/12 px-2 py-2">
                                    <button class="w-full btn-icon-50-dark">
                                        ver planes de funeraria predefinidos <img src="@assets/images/folder.svg" />
                                    </button>
                                </div>
                            </div>
                            <div class="w-full px-2 py-4">
                                <vs-table noDataText="" class="tabla-datos tabla-datos-no-data">
                                    <template slot="header">
                                        <h3>Listado de Ventas en Gral.</h3>
                                    </template>
                                    <template slot="thead">
                                        <vs-th>#</vs-th>
                                        <vs-th><span class="px-2">DESCRIPCIÓN</span></vs-th>
                                        <vs-th><span class="px-2">COSTOS Y CONTENIDO</span></vs-th>
                                        <vs-th><span class="px-2">QUITAR</span></vs-th>
                                    </template>
                                    <template slot-scope="{ data }">
                                        <vs-tr :data="tr" :key="n" v-for="n in 2">
                                            <vs-td>
                                                <span class="font-semibold px-2">{{ n }}</span>
                                            </vs-td>
                                            <vs-td class="size-base padding-y-7">
                                                <span class="px-2">PAQUETE PRIORITY PLUS (CREMACION). 1, 6, 12, 18 PAGO
                                                    (S)</span>

                                            </vs-td>
                                            <vs-td>
                                                <span class="px-2"><img class="cursor-pointer img-btn-14 mx-3"
                                                        src="@assets/images/folder.svg" title="" /></span>
                                            </vs-td>
                                            <vs-td>
                                                <span class="px-2"> <img class="cursor-pointer img-btn-14 mx-3"
                                                        src="@assets/images/quitar.svg" title="" /></span>
                                            </vs-td>
                                        </vs-tr>
                                    </template>
                                </vs-table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="w-full px-2 py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">Cotización Personalizada</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <img class="img-btn-20 mx-3 mt-5 hidden lg:block" src="@assets/images/barcode.svg" />
                                <div class="w-auto md:w-6/12 lg:w-4/12 xl:w-3/12 px-2 input-text hidden lg:block">
                                    <label>Clave o código de barras</label>
                                    <vs-input ref="codigo_barras" name="codigo_barras" data-vv-as=" " type="text"
                                        class="w-full" placeholder="Ej. 0000000123" maxlength="28"
                                        v-model.trim="serverOptions.numero_control" v-on:keyup.enter="
                                            get_concepto_por_codigo('codigo_barras')
                                            " v-on:blur="
                                                get_concepto_por_codigo('codigo_barras', 'blur')
                                                " />
                                </div>
                                <img class="cursor-pointer img-btn-20 mx-3 mt-5 hidden lg:block"
                                    src="@assets/images/searcharticulo.svg" title="Buscador de artículos y servicios"
                                    @click="openBuscadorArticulos = true" />
                                <div class="w-full text-right block lg:hidden">
                                    <vs-button class="sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
                                        @click="openBuscadorArticulos = true">
                                        <span>Buscar artículos</span>
                                    </vs-button>
                                </div>
                            </div>

                            <div class="w-full px-2 py-4">
                                <vs-table noDataText="" class="tabla-datos tabla-datos-no-data" :data="form.conceptos"
                                    ref="conceptos">
                                    <template slot="header">
                                        <h3>Listado de Ventas en Gral.</h3>
                                    </template>
                                    <template slot="thead">
                                        <vs-th class="w-auto">#</vs-th>
                                        <vs-th>DESCRIPCIÓN</vs-th>
                                        <vs-th>CANT.</vs-th>
                                        <vs-th>COSTO</vs-th>
                                        <vs-th>DESCUENTO</vs-th>
                                        <vs-th>COSTO CON DESC.</vs-th>
                                        <vs-th>IMPORTE</vs-th>
                                        <vs-th>QUITAR</vs-th>
                                    </template>
                                    <template slot-scope="{ data }">
                                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                            <vs-td class="w-auto">
                                                <span class="font-semibold">{{ indextr + 1 }}</span>
                                            </vs-td>
                                            <vs-td class="size-base padding-y-7 w-5/12">
                                                <vs-input :ref="'descripcion' + indextr" data-vv-as=" "
                                                    data-vv-validate-on="blur" v-validate="'required'"
                                                    class="px-2 mr-auto ml-auto w-full input-cantidad" maxlength="150"
                                                    v-model="form.conceptos[indextr].descripcion
                                                        " />
                                                <div class="input-text">
                                                    <span>
                                                        {{
                                                            errors.first(
                                                                "descripcion" + indextr
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="size-base w-1/12">
                                                <vs-input :name="'cantidad' + indextr" data-vv-as=" "
                                                    data-vv-validate-on="blur" v-validate="'required|integer|min_value:1'
                                                        " class="px-2 mr-auto ml-auto input-cantidad no-min-width"
                                                    maxlength="4" v-model="form.conceptos[indextr].cantidad
                                                        " />
                                                <div class="input-text">
                                                    <span>
                                                        {{
                                                            errors.first(
                                                                "cantidad" + indextr
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="size-base w-1/12">
                                                <vs-input :name="'costo_neto_normal' + indextr" data-vv-as=" "
                                                    data-vv-validate-on="blur"
                                                    v-validate="'required|decimal:2|min_value:0'" class="mr-auto
                                                    ml-auto input-cantidad px-2 w-full" maxlength="10"
                                                    v-model="form.conceptos[indextr].costo_neto_normal" />
                                                <div class="input-text">
                                                    <span>
                                                        {{
                                                            errors.first(
                                                                "costo_neto_normal" + indextr
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="size-base w-1/12">
                                                <vs-switch class="ml-auto mr-auto" color="success" icon-pack="feather"
                                                    v-model="form.conceptos[indextr].descuento_b">
                                                    <span slot="on">SI</span>
                                                    <span slot="off">NO</span>
                                                </vs-switch>
                                            </vs-td>
                                            <vs-td class="size-base w-1/12">
                                                <vs-input :name="'costo_neto_descuento' + indextr" data-vv-as=" "
                                                    data-vv-validate-on="blur" v-validate="'required|decimal:2|min_value:' +
                                                        0 +
                                                        '|max_value:' +
                                                        form.conceptos[indextr].costo_neto_normal
                                                        " class="mr-auto ml-auto input-cantidad px-2 w-full"
                                                    maxlength="10" v-model="form.conceptos[indextr].costo_neto_descuento
                                                        " :disabled="form.conceptos[indextr].descuento_b == 0
                                                            " />
                                                <div class="input-text">
                                                    <span>
                                                        {{
                                                            errors.first(
                                                                "costo_neto_descuento" + indextr
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="w-1/12">
                                                <div v-if="form.conceptos[indextr].descuento_b == 1">
                                                    $
                                                    {{
                                                        (form.conceptos[indextr]
                                                            .costo_neto_descuento *
                                                            form.conceptos[indextr].cantidad)
                                                        | numFormat("0,000.00")
                                                    }}
                                                </div>
                                                <div v-else>
                                                    $
                                                    {{
                                                        (form.conceptos[indextr].costo_neto_normal *
                                                            form.conceptos[indextr].cantidad)
                                                        | numFormat("0,000.00")
                                                    }}
                                                </div>
                                            </vs-td>

                                            <vs-td class="w-1/12">
                                                <div class="flex justify-center">
                                                    <img class="img-btn-14 mx-3" src="@assets/images/quitar.svg"
                                                        title="Esta venta ya fue cancelada, puede hacer click aquí para consultar" />
                                                </div>
                                            </vs-td>
                                        </vs-tr>
                                    </template>
                                </vs-table>
                                <button class="w-full btn-icon-50-dark my-4" @click="agregar_manual">
                                    AGREGAR CONCEPTO MANUALMENTE <img src="@assets/images/plus-success.svg" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>
        <ArticulosBuscador :z_index="'z-index56k'" :show="openBuscadorArticulos"
            @closeBuscador="openBuscadorArticulos = false" @LoteSeleccionado="LoteSeleccionado"></ArticulosBuscador>
    </div>
</template>
<script>
import funeraria from "@services/funeraria";
import vSelect from "vue-select";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ArticulosBuscador from "@pages/funeraria/servicios_funerarios/searcher_articulos.vue";
export default {
    components: {
        "v-select": vSelect,
        flatPickr,
        ArticulosBuscador
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        tipo: {
            type: String,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index54k",
        },
    },
    watch: {
        show: function (newValue, oldValue) {
            if (newValue == true) {
                this.$nextTick(
                    () => { }
                    // this.$refs["fallecido_ref"].$el.querySelector("input").focus()
                );
                this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
                    this.cancelar();
                };
            } else {
                /**acciones al cerrar el formulario */
            }
        },
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
        getTipoformulario: {
            get() {
                return this.tipo;
            },
            set(newValue) {
                return newValue;
            },
        },
        solicitud_validacion_computed: function () {
            //checo que el dato venta a futuro este activo
            if (this.form.tipo_financiamiento == 2) {
                return this.form.solicitud;
            } else return true;
        },
    },
    data() {
        return {
            form: {
                solicitud: '',
                planes_predefinidos_b: false,
                conceptos: [
                    {
                        descripcion: 'Ataúd Metálico Estándar Celestial',
                        costo_neto_normal: 1500,
                        descuento_b: 0,
                        costo_neto_descuento: 1000,
                        cantidad: 1,
                        importe: 0
                    }
                ]
            },
            serverOptions: {
                numero_control: "",
            },
            openBuscadorArticulos: false,
            errores: [],
        };
    },
    methods: {
        get_concepto_por_codigo(origen = "", evento = "") {
            if (evento == "blur") {
                return;
            } else {
                /**checando el origen */
                if (origen == "codigo_barras") {
                    if (this.serverOptions.numero_control.trim() == "") {
                        //return;
                    }
                }
            }

            let self = this;
            if (funeraria.cancel) {
                funeraria.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            funeraria
                .get_inventario_servicios_codigos(this.serverOptions)
                .then((res) => {
                    if (res.data.length > 0) {
                        let datos = res.data[0];
                        /**agrego el concepto al listado del contrato */
                        this.form.conceptos.push({
                            descripcion: datos.descripcion,
                            cantidad: 1,
                            costo_neto_normal: datos.precio_venta,
                            descuento_b: 0,
                            costo_neto_descuento: 0,
                            importe: 0,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Buscar artículos",
                            text: "No se ha encontrado el concepto con el número de clave ingresado.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 8000,
                        });
                    }
                    this.$vs.loading.close();
                    this.serverOptions.numero_control = "";
                    this.$nextTick(() =>
                        this.$refs["codigo_barras"].$el.querySelector("input").focus()
                    );
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
                                time: 8000,
                            });
                        }
                    }
                });
        },
        LoteSeleccionado(datos) {
            this.form.conceptos.push(datos);
        },
        agregar_manual() {
            this.form.conceptos.push({
                descripcion: '',
                cantidad: 1,
                costo_neto_normal: '',
                descuento_b: '',
                costo_neto_descuento: '',
                importe: ''
            });
            this.$nextTick(() => {
                let index = this.form.conceptos.length - 1;
                this.$refs['descripcion' + index][0].$el.querySelector("input").focus()
            });
        }
    },
    created() { },
    updated() {
    }
};
</script>

<style scoped></style>
