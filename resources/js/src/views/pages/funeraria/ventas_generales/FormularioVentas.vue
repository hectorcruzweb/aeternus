<template>
    <div>
        <vs-popup :class="['forms-popup', z_index]" fullscreen close="cancelar" :title="getTipoformulario == 'agregar'
            ? 'hacer venta en general'
            : 'modificar venta en general'
            " :active.sync="showVentana" ref="formulario">
            <!--inicio venta-->
            <div class="flex flex-wrap">
                <div class="w-full py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">Datos de la venta</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full xl:w-2/12 px-2 input-text">
                                    <label>
                                        Fecha de la venta
                                        <span>(*)</span>
                                    </label>
                                    <flat-pickr name="fecha_venta" data-vv-as=" "
                                        v-validate:fecha_venta_validacion_computed.immediate="'required'
                                            " :config="configdateTimePicker" v-model="form.fecha_venta"
                                        placeholder="Fecha de la venta" class="w-full" />
                                    <span>
                                        {{ errors.first("fecha_venta") }}
                                    </span>
                                    <span v-if="this.errores.fecha_venta">{{
                                        errores.fecha_venta[0]
                                        }}</span>
                                </div>
                                <div class="w-full xl:w-7/12">
                                    <div class="w-full px-2 input-text" v-if="form.id_cliente == ''">
                                        <label>
                                            cliente
                                            <span>(*)</span>
                                        </label>
                                        <div class="bg-danger-50 text-center py-2 px-2 size-base border-danger-solid-1 cursor-pointer color-danger-900"
                                            @click="openBuscadorClientes = true">
                                            Click para seleccionar al cliente
                                        </div>
                                    </div>
                                    <div class="w-full px-2 input-text" v-else>
                                        <label>
                                            Cliente
                                            <span>(*)</span>
                                        </label>
                                        <div class="bg-success-50 py-2 px-2 size-base border-success-solid-2 uppercase">
                                            <div class="flex flex-wrap">
                                                <div class="w-full xl:w-8/12">
                                                    <span class="font-medium"> Clave: </span>
                                                    {{ form.id_cliente }},
                                                    <span class="font-medium"> Nombre: </span>
                                                    {{ form.cliente }}
                                                </div>
                                                <div class="w-full xl:w-4/12 text-center xl:text-right">
                                                    <span @click="quitarCliente()"
                                                        class="color-danger-900 cursor-pointer">X Cambiar Cliente
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full input-text xl:w-3/12 px-2">
                                    <label class="">
                                        Vendedor
                                        <span class="">(*)</span>
                                    </label>
                                    <v-select :options="vendedores" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="form.vendedor" class="w-full"
                                        v-validate:vendedor_computed.immediate="'required'" name="vendedor"
                                        data-vv-as=" ">
                                        <div slot="no-options">Seleccione una opción</div>
                                    </v-select>
                                    <span class="">
                                        {{ errors.first("vendedor") }}
                                    </span>
                                    <span class="" v-if="this.errores['vendedor.value']">{{
                                        errores["vendedor.value"][0]
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--inicio de carga de artiuculos-->
                    <div class="form-group">
                        <div class="title-form-group">
                            Desglose de Artículos y Servicios
                        </div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full">
                                    <div class="flex flex-wrap">
                                        <img class="img-btn-20 mx-3 mt-4 hidden lg:block"
                                            src="@assets/images/barcode.svg" />
                                        <div
                                            class="w-auto md:w-6/12 lg:w-4/12 xl:w-3/12 px-2 input-text hidden lg:block">
                                            <label>Clave o código de barras</label>
                                            <vs-input ref="codigo_barras" name="codigo_barras" data-vv-as=" "
                                                type="text" class="w-full" placeholder="Ej. 0000000123" maxlength="28"
                                                v-model.trim="serverOptions.numero_control" v-on:keyup.enter="
                                                    get_concepto_por_codigo('codigo_barras')
                                                    " v-on:blur="
                                                        get_concepto_por_codigo('codigo_barras', 'blur')
                                                        " />
                                        </div>
                                        <img class="cursor-pointer img-btn-20 mx-3 mt-4 hidden lg:block"
                                            src="@assets/images/searcharticulo.svg"
                                            title="Buscador de artículos y servicios"
                                            @click="openBuscadorArticulos = true" />
                                        <div class="w-full text-right block lg:hidden">
                                            <vs-button class="sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="primary"
                                                @click="openBuscadorArticulos = true">
                                                <span>Buscar artículos</span>
                                            </vs-button>
                                        </div>
                                        <div class="w-full py-6 px-2">
                                            <vs-table class="tabla-datos" :data="form.articulos"
                                                noDataText="No se han agregado Artículos ni Servicios">
                                                <template slot="header">
                                                    <h3>Artículos que Incluye la Venta</h3>
                                                </template>
                                                <template slot="thead">
                                                    <vs-th>#</vs-th>
                                                    <vs-th>Descripción</vs-th>
                                                    <vs-th>Cant.</vs-th>
                                                    <vs-th>Costo Neto</vs-th>
                                                    <vs-th>Descuento</vs-th>
                                                    <vs-th>Costo Neto Con Descuento</vs-th>
                                                    <vs-th>Importe</vs-th>
                                                    <vs-th>Facturable</vs-th>
                                                    <vs-th>Quitar</vs-th>
                                                </template>
                                                <template slot-scope="{ data }">
                                                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                                        <vs-td class="">
                                                            <div>
                                                                <span>{{ indextr + 1 }}</span>
                                                            </div>
                                                        </vs-td>

                                                        <vs-td class="">
                                                            <div class="uppercase">
                                                                {{ data[indextr].descripcion }},
                                                                {{ data[indextr].tipo }}
                                                            </div>
                                                        </vs-td>

                                                        <vs-td class="">
                                                            <vs-input :name="'cantidad_articulos' + indextr"
                                                                data-vv-as=" " data-vv-validate-on="blur" v-validate="'required|integer|min_value:' +
                                                                    1 +
                                                                    '|max_value:1000'
                                                                    " class="mr-auto ml-auto input-cantidad"
                                                                maxlength="4"
                                                                v-model="form.articulos[indextr].cantidad" />
                                                            <div class="input-text">
                                                                <span>
                                                                    {{
                                                                        errors.first("cantidad_articulos" + indextr)
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </vs-td>

                                                        <vs-td class="">
                                                            <vs-input :name="'costo_neto_normal_articulos' + indextr"
                                                                data-vv-as=" " data-vv-validate-on="blur"
                                                                v-validate="'required|decimal:2|min_value:' + 0"
                                                                class="mr-auto ml-auto input-cantidad" maxlength="10"
                                                                v-model="form.articulos[indextr].costo_neto_normal
                                                                    " :disabled="form.articulos[indextr].descuento_b == 1
                                                                        " />
                                                            <div class="input-text">
                                                                <span>
                                                                    {{
                                                                        errors.first(
                                                                            "costo_neto_normal_articulos" + indextr
                                                                        )
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </vs-td>

                                                        <vs-td class="">
                                                            <vs-switch class="ml-auto mr-auto" color="success"
                                                                icon-pack="feather"
                                                                v-model="form.articulos[indextr].descuento_b">
                                                                <span slot="on">SI</span>
                                                                <span slot="off">NO</span>
                                                            </vs-switch>
                                                        </vs-td>
                                                        <vs-td class="">
                                                            <vs-input :name="'costo_neto_descuento' + indextr"
                                                                data-vv-as=" " data-vv-validate-on="blur" v-validate="'required|decimal:2|min_value:' +
                                                                    0 +
                                                                    '|max_value:' +
                                                                    form.articulos[indextr].costo_neto_normal
                                                                    " class="mr-auto ml-auto input-cantidad"
                                                                maxlength="10" v-model="form.articulos[indextr].costo_neto_descuento
                                                                    " :disabled="form.articulos[indextr].descuento_b == 0
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

                                                        <vs-td class="">
                                                            <div v-if="form.articulos[indextr].descuento_b == 1">
                                                                $
                                                                {{
                                                                    (form.articulos[indextr]
                                                                        .costo_neto_descuento *
                                                                        form.articulos[indextr].cantidad)
                                                                    | numFormat("0,000.00")
                                                                }}
                                                            </div>
                                                            <div v-else>
                                                                $
                                                                {{
                                                                    (form.articulos[indextr].costo_neto_normal *
                                                                        form.articulos[indextr].cantidad)
                                                                    | numFormat("0,000.00")
                                                                }}
                                                            </div>
                                                        </vs-td>

                                                        <vs-td class="">
                                                            <vs-switch class="ml-auto mr-auto" color="success"
                                                                icon-pack="feather"
                                                                v-model="form.articulos[indextr].facturable_b">
                                                                <span slot="on">SI</span>
                                                                <span slot="off">NO</span>
                                                            </vs-switch>
                                                        </vs-td>

                                                        <vs-td class="">
                                                            <div class="flex justify-center"
                                                                @click="remover_articulo(indextr)" v-if="!fueCancelada">
                                                                <img class="cursor-pointer img-btn-20 mx-3"
                                                                    src="@assets/images/minus.svg" />
                                                            </div>
                                                        </vs-td>
                                                    </vs-tr>
                                                </template>
                                            </vs-table>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="flex flex-wrap">
                                        <!--items nota y guardar-->
                                        <div class="w-full xl:w-8/12 px-2">
                                            <div class="flex flex-wrap">
                                                <div class="w-full input-text px-2">
                                                    <label>Nota u Observación:</label>
                                                    <vs-textarea height="225px" :rows="5" size="large" ref="nota"
                                                        type="text" class="w-full" v-model.trim="form.nota" />
                                                </div>
                                            </div>
                                            <!--fin del resumen de la venta-->
                                        </div>
                                        <div class="w-full xl:w-4/12 px-2">
                                            <div class="flex flex-wrap">
                                                <div class="w-full input-text px-2 text-center">
                                                    <label>
                                                        Tasa IVA %
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input :disabled="tiene_pagos_realizados ||
                                                        ventaLiquidada ||
                                                        fueCancelada
                                                        " size="large" name="tasa_iva" data-vv-as=" " v-validate="'required|decimal:2|min_value:0|max_value:25'
                                                            " type="text" class="w-full cantidad"
                                                        placeholder="Porcentaje IVA" v-model="form.tasa_iva"
                                                        maxlength="2" />
                                                    <span class="mensaje-requerido">
                                                        {{ errors.first("tasa_iva") }}
                                                    </span>
                                                    <span class="mensaje-requerido" v-if="this.errores.tasa_iva">{{
                                                        errores.tasa_iva[0] }}</span>
                                                </div>
                                                <div class="w-full px-2 text-center mt-3">
                                                    <label class="h4 font-medium color-copy">$ Total de la Venta</label>
                                                    <div class="mt-3 text-center">
                                                        <span class="h1">
                                                            $
                                                            {{ totalVenta | numFormat("0,000.00") }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-full px-2 size-base color-copy mt-3 text-center">
                                                    <span class="color-danger-900 font-medium">Ojo:</span>
                                                    Los costos de los conceptos capturados ya incluyen el
                                                    IVA.
                                                </div>

                                                <div class="w-full input-text px-2">
                                                    <vs-button v-if="!fueCancelada" class="w-full ml-auto mr-auto mt-3"
                                                        @click="acceptAlert()" color="success">
                                                        <span v-if="getTipoformulario == 'agregar'">Guardar Venta</span>
                                                        <span v-else>Modificar Venta</span>
                                                    </vs-button>
                                                </div>
                                                <!--fin de precios-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--fin venta-->
        </vs-popup>
        <Password :z_index="'z-index56k'" :show="openPassword" :callback-on-success="callback"
            @closeVerificar="closePassword" :accion="accionNombre"></Password>
        <ConfirmarDanger :z_index="'z-index58k'" :show="openConfirmarSinPassword"
            :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
            :accion="accionConfirmarSinPassword" :confirmarButton="botonConfirmarSinPassword"></ConfirmarDanger>

        <ConfirmarAceptar :show="openConfirmarAceptar" :callback-on-success="callBackConfirmarAceptar"
            @closeVerificar="openConfirmarAceptar = false"
            :accion="'He revisado la información y quiero guardar la venta'" :confirmarButton="'Guardar Venta'">
        </ConfirmarAceptar>

        <ClientesBuscador :z_index="'z-index55k'" :show="openBuscadorClientes"
            @closeBuscador="openBuscadorClientes = false" @retornoCliente="clienteSeleccionado"></ClientesBuscador>

        <ArticulosBuscador :z_index="'z-index56k'" :show="openBuscadorArticulos"
            @closeBuscador="openBuscadorArticulos = false" @LoteSeleccionado="LoteSeleccionado"></ArticulosBuscador>

        <PoliticasModificar :z_index="'z-index56k'" :show="verPoliticasModificar" :permiso_modificar="permiso_modificar"
            @closeForm="closeForm">
        </PoliticasModificar>
    </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import funeraria from "@services/funeraria";
import cementerio from "@services/cementerio";
import ClientesBuscador from "@pages/clientes/searcher.vue";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ArticulosBuscador from "@pages/funeraria/servicios_funerarios/searcher_articulos.vue";
import PoliticasModificar from "@pages/funeraria/ventas_generales/PoliticasModificar";
/**VARIABLES GLOBALES */
import { configdateTimePicker } from "@/VariablesGlobales";

export default {
    components: {
        "v-select": vSelect,
        flatPickr,
        Password,
        ConfirmarDanger,
        ConfirmarAceptar,
        ArticulosBuscador,
        ClientesBuscador,
        PoliticasModificar,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        //para saber que tipo de formulario es
        tipo: {
            type: String,
            required: true,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index54k",
        },
        id_venta: {
            type: Number,
            required: false,
            default: 0,
        },
    },
    watch: {
        show: function (newValue, oldValue) {
            this.limpiarValidation();
            if (newValue == true) {
                this.$nextTick(
                    () => { }
                    // this.$refs["fallecido_ref"].$el.querySelector("input").focus()
                );
                this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
                    this.cancelar();
                };
                (async () => {
                    await this.get_vendedores();
                    if (this.getTipoformulario == "agregar") {
                    } else {
                        this.form.venta_id = this.get_venta_id;
                        /**se cargan los datos al formulario */
                        await this.consultar_venta_id();
                        this.verPoliticasModificar = true;
                    }
                })();
            } else {
                /**acciones al cerrar el formulario */
            }
        },
    },
    computed: {
        get_venta_id: {
            get() {
                return this.id_venta;
            },
            set(newValue) {
                return newValue;
            },
        },
        /**validaciones de los selects */
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
        vendedor_computed: function () {
            return this.form.vendedor.value;
        },
        totalVenta: function () {
            let total = 0;
            this.form.articulos.forEach((element, index) => {
                if (this.form.articulos[index].descuento_b == 1) {
                    total +=
                        this.form.articulos[index].cantidad *
                        this.form.articulos[index].costo_neto_descuento;
                } else {
                    total +=
                        this.form.articulos[index].cantidad *
                        this.form.articulos[index].costo_neto_normal;
                }
            });
            return total;
        },
        fecha_venta_validacion_computed: function () {
            return this.form.fecha_venta;
        },
    },

    data() {
        return {
            permiso_modificar: false,
            verPoliticasModificar: false,
            ///form
            vendedores: [],
            //form
            serverOptions: {
                numero_control: "",
            },
            form: {
                venta_id: "", //para modificaciones
                fecha_venta: "",
                vendedor: { label: "Seleccione 1", value: "" },
                id_cliente: "",
                cliente: "",
                direccion_cliente: "",
                articulos: [],
                tasa_iva: 16,
                nota: "",
            },
            /**variables dle modulo */
            openBuscadorArticulos: false,
            openBuscadorClientes: false,
            configdateTimePicker: configdateTimePicker,
            accionConfirmarSinPassword: "",
            botonConfirmarSinPassword: "",
            openPassword: false,
            openConfirmarSinPassword: false,
            callback: Function,
            callBackConfirmar: Function,
            openConfirmarAceptar: false,
            callBackConfirmarAceptar: Function,
            accionNombre: "Modificar Venta",
            errores: [],
        };
    },
    methods: {
        async consultar_venta_id() {
            try {
                this.$vs.loading();
                let res = await funeraria.get_ventas_gral(
                    null,
                    false,
                    this.form.venta_id
                );
                if (res.data.length > 0) {
                    res = res.data[0];
                    //veo si se puede modificar
                    this.permiso_modificar = (res.operacion_status != 0 && res.venta_general.entregado_b == 0) ? true : false;
                    //cargo informacion
                    let partes_fecha = res.fecha_operacion.split("-");
                    //yyyy-mm-dd
                    this.form.fecha_venta = new Date(
                        partes_fecha[0],
                        partes_fecha[1] - 1,
                        partes_fecha[2]
                    );

                    this.form.id_cliente = res.cliente_id;
                    this.form.cliente = res.nombre;
                    this.form.direccion_cliente = res.direccion;
                    /**verificando si existe el vendedor o si no para crearlo, podria no existir en caso de que haya sido cancelado */
                    this.vendedores.forEach((element) => {
                        if (element.value == res.venta_general.vendedor.id) {
                            this.form.vendedor = element;
                            return;
                        }
                    });
                    if (this.form.vendedor.value == "") {
                        let vendedor = {
                            value: res.venta_general.vendedor.id,
                            label:
                                "(" +
                                res.venta_general.vendedor.nombre +
                                ", vendedor original)",
                        };
                        this.vendedores.push(vendedor);
                        this.form.vendedor = vendedor;
                        /**se agrega el original y se selecciona */
                    }
                    this.form.tasa_iva = Number(res.tasa_iva) <= 0 ? 16 : res.tasa_iva;
                    res.conceptos_temporales.forEach((element) => {
                        this.form.articulos.push({
                            id: element.articulos_id,
                            descripcion: element.descripcion,
                            cantidad: element.cantidad,
                            costo_neto_normal: element.costo_neto_normal,
                            descuento_b: element.descuento_b,
                            costo_neto_descuento: element.costo_neto_descuento,
                            facturable_b: element.facturable_b,
                        });
                    });
                    this.form.nota = res.nota;
                } else {
                    //no se encontro la info
                    this.$vs.notify({
                        title: "Modificar Venta",
                        text: "No se ha encontrado esta venta en el sistema.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000,
                    });
                    this.cerrarVentana();
                }
                this.$vs.loading.close();
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
                            time: 4000,
                        });
                    }
                }
            }
        },
        //get vendedores
        async get_vendedores() {
            try {
                let res = await cementerio.get_vendedores();
                //le agrego todos los usuarios vendedores
                this.vendedores = [];
                this.vendedores.push({ label: "Seleccione 1", value: "" });
                if (this.getTipoformulario == "agregar") {
                    this.form.vendedor = this.vendedores[0];
                }
                res.data.forEach((element) => {
                    this.vendedores.push({
                        label: element.nombre,
                        value: element.id,
                    });
                });
            } catch (error) {
                /**error al cargar vendedores */
                this.$vs.notify({
                    title: "Error",
                    text: "Ha ocurrido un error al tratar de cargar el catálogo de vendedores, por favor reintente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "9000",
                });
                this.cerrarVentana();
            }
        },
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
                        this.form.articulos.push({
                            id: datos.id,
                            codigo_barras: datos.codigo_barras,
                            tipo: datos.tipo,
                            tipo_articulos_id: datos.tipo_articulos_id,
                            categoria: datos.categoria.categoria,
                            descripcion: datos.descripcion,
                            //lote: lote.lotes_id,
                            //num_lote_inventario: lote.num_lote_inventario,
                            cantidad: 1,
                            costo_neto_normal: datos.precio_venta,
                            descuento_b: 0,
                            costo_neto_descuento: 0,
                            importe: 0,
                            facturable_b: 1,
                            existencia: 0,
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

        acceptAlert() {
            this.$validator
                .validateAll()
                .then((result) => {
                    if (!result) {
                        this.$vs.notify({
                            title: "Error",
                            text: "Verifique que todos los datos han sido capturados",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "12000",
                        });
                        return;
                    } else {
                        //AL LLEGAR AQUI SE SABE QUE EL FORMULARIO PASO LA VALIDACION
                        if (this.form.articulos.length == 0) {
                            this.$vs.notify({
                                title: "Error",
                                text: "Verifique que capturó los artículos de la venta",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                position: "bottom-right",
                                time: "12000",
                            });
                            return;
                        }
                        if (this.form.id_cliente <= 0) {
                            this.$vs.notify({
                                title: "Error",
                                text: "Verifique que capturó el cliente de esta venta",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                position: "bottom-right",
                                time: "12000",
                            });
                            return;
                        }
                        (async () => {
                            if (this.getTipoformulario == "agregar") {
                                this.callBackConfirmarAceptar = await this.control_ventas_gral;
                                this.openConfirmarAceptar = true;
                            } else {
                                this.callback = await this.control_ventas_gral;
                                this.openPassword = true;
                            }
                        })();
                    }
                })
                .catch(() => { });
        },

        async control_ventas_gral() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await funeraria.control_ventas_gral(
                    this.form,
                    this.getTipoformulario
                );
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Ventas en Gral.",
                        text: this.getTipoformulario == "agregar" ? "Se ha registrado la venta correctamente." : "Se ha modificado la venta correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Ventas en Gral.",
                        text: "Error al registrar la venta, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 6000,
                    });
                }
                this.$vs.loading.close();
            } catch (err) {
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
                    } else if (err.response.status == 422) {
                        //checo si existe cada error
                        this.errores = err.response.data.error;
                        this.$vs.notify({
                            title: "Registro de Ventas en Gral.",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        //este error es por alguna condicion que el contrano no cumple para modificar
                        //la propiedad esa ya ha sido vendida
                        this.$vs.notify({
                            title: "Registro de Ventas en Gral.",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 8000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },
        cancel() {
            this.$emit("closeVentana");
        },

        LoteSeleccionado(datos) {
            /**agregando los datos a la lista de articulos y servicios */
            this.form.articulos.push(datos);
        },

        cancelar() {
            this.botonConfirmarSinPassword = "Salir y limpiar";
            this.accionConfirmarSinPassword =
                "Esta acción limpiará los datos que capturó en el formulario.";
            this.openConfirmarSinPassword = true;
            this.callBackConfirmar = this.cerrarVentana;
        },

        //remover beneficiario
        remover_articulo(index_articulo_servicio) {
            this.botonConfirmarSinPassword = "eliminar";
            this.accionConfirmarSinPassword =
                "¿Desea eliminar este concepto? Los datos quedarán eliminados del sistema?";
            this.form.index_articulo_servicio = index_articulo_servicio;
            this.callBackConfirmar = this.remover_articulo_callback;
            this.openConfirmarSinPassword = true;
        },
        //remover beneficiario callback quita del array al beneficiario seleccionado
        remover_articulo_callback() {
            this.form.articulos.splice(this.form.index_articulo_servicio, 1);
        },

        cerrarVentana() {
            this.openConfirmarSinPassword = false;
            this.limpiarVentana();
            this.$emit("closeVentana");
        },

        closeForm(res) {
            this.verPoliticasModificar = false;
            if (!res) {
                this.limpiarVentana();
                this.$emit("closeVentana");
            }

        },
        //regresa los datos a su estado inicial
        limpiarVentana() {
            this.form.articulos = [];
            this.form.tasa_iva = 16;
            this.form.nota = "";
            this.form.fecha_venta = "";
            this.form.id_cliente = "";
            this.form.cliente = "";
            this.form.vendedor = this.vendedores[0];
        },

        closePassword() {
            this.openPassword = false;
        },
        limpiarValidation() {
            this.$validator.pause();
            this.$nextTick(() => {
                this.$validator.errors.clear();
                this.$validator.fields.items.forEach((field) => field.reset());
                this.$validator.fields.items.forEach((field) =>
                    this.errors.remove(field)
                );
                this.$validator.resume();
            });
        },

        clienteSeleccionado(datos) {
            /**obtiene los datos retornados del buscar cliente */
            this.form.cliente = datos.nombre;
            this.form.id_cliente = datos.id_cliente;
            this.form.direccion_cliente = datos.datos.direccion;
        },

        quitarCliente() {
            this.botonConfirmarSinPassword = "Cambiar cliente";
            this.accionConfirmarSinPassword =
                "¿Desea cambiar de cliente para esta venta?";
            this.callBackConfirmar = this.limpiarCliente;
            this.openConfirmarSinPassword = true;
        },

        limpiarCliente() {
            this.form.id_cliente = "";
            this.form.cliente = "";
            this.form.direccion_cliente = "";
        },
    },
    created() { },
};
</script>
<style scoped>
.he {
    max-height: 50% !important;
}
</style>
