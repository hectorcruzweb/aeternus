<template>
    <div>
        <vs-popup :class="['forms-popup', z_index]" fullscreen close="cancelar" :title="getTipoformulario == 'agregar'
            ? 'Elaborar cotización'
            : 'modificar venta en general'
            " :active.sync="showVentana">
            <!--inicio de cotizacion-->
            <div class="flex flex-wrap">
                <div class="w-full xl:w-6/12 px-2 py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">Datos del Cliente</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-12/12 px-2 input-text">
                                    <label>
                                        Nombre del cliente
                                        <span>(*)</span>
                                    </label>
                                    <vs-input v-validate="'required'" name="nombre" data-vv-as=" " type="text"
                                        class="w-full" focused placeholder="Ej. José Pérez" v-model="form.nombre"
                                        maxlength="100" />
                                    <span>{{ errors.first("nombre") }}</span>
                                    <span v-if="this.errores.nombre">{{
                                        errores.nombre[0]
                                    }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Teléfono
                                        <span>(*)</span>
                                    </label>
                                    <vs-input v-validate="'required'" name="telefono" data-vv-as=" " type="text"
                                        class="w-full" placeholder="Ej. 6692145634" v-model="form.telefono"
                                        maxlength="100" />
                                    <span>{{ errors.first("telefono") }}</span>
                                    <span v-if="this.errores.telefono">{{
                                        errores.telefono[0]
                                    }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Email
                                        <span></span>
                                    </label>
                                    <vs-input v-validate="'email'" name="email" data-vv-as=" " type="email"
                                        class="w-full" placeholder="Ej. cliente@gmail.com" v-model="form.email"
                                        maxlength="100" />
                                    <span>{{ errors.first("email") }}</span>
                                    <span v-if="this.errores.email">{{ errores.email[0] }}</span>
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
                                        Vendedor
                                        <span>(*)</span>
                                    </label>
                                    <v-select :options="vendedores" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-validate:vendedor_computed.immediate="'required'" v-model="form.vendedor"
                                        class="w-full" name=" vendedor" data-vv-as=" ">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Vendedor
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("vendedor") }}</span>
                                    <span v-if="this.errores['vendedor.value']">{{
                                        errores["vendedor.value"][0]
                                    }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>Fecha de Cotizacion (Año-Mes-Dia)</label>
                                    <span>(*)</span>
                                    <flat-pickr name="fecha_cotizacion" data-vv-as=" "
                                        v-validate:fecha_cotizacion_validacion_computed="'required'"
                                        :config="configdateTimePicker" v-model="form.fecha_cotizacion"
                                        placeholder="Fecha de la Cotización" class="w-full" />
                                    <span>{{ errors.first("fecha_cotizacion") }}</span>
                                    <span v-if="this.errores.fecha_cotizacion">{{
                                        errores.fecha_cotizacion[0]
                                    }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Periodo de Validéz
                                        <span>(*)</span>
                                    </label>
                                    <v-select :options="periodos_validez"
                                        v-validate:validez_validacion_computed.immediate="'required'" :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="form.validez" class="w-full"
                                        name=" validez" data-vv-as=" ">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Periodo de Validéz
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("validez") }}</span>
                                    <span v-if="this.errores['validez.value']">{{
                                        errores["validez.value"][0]
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <vs-checkbox ref="cotizaciones_predefinidas_b" color="success"
                    v-model="form.cotizaciones_predefinidas_b"
                    class="ml-auto size-small font-medium color-copy pt-2 pr-2">INCLUIR COTIZACIONES
                    PREDEFINIDAS</vs-checkbox>
                <div class="w-full px-2 py-4" v-show="form.cotizaciones_predefinidas_b">
                    <div class="form-group py-6">
                        <div class="title-form-group">Cotizaciones Predefinidas</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-6/12 px-2 py-2">
                                    <button class="w-full btn-icon-50">
                                        ver planes de funeraria predefinidos
                                        <img src="@assets/images/folder.svg" />
                                    </button>
                                </div>
                                <div class="w-full lg:w-6/12 px-2 py-2">
                                    <button class="w-full btn-icon-50">
                                        ver planes de cementerio predefinidos
                                        <img src="@assets/images/folder.svg" />
                                    </button>
                                </div>
                            </div>
                            <div class="w-full px-2 py-4">
                                <vs-table noDataText="" class="tabla-datos tabla-datos-no-data">
                                    <template slot="header">
                                        <h3>Listado de Cotizaciones Agregadas</h3>
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
                                                <span class="px-2">
                                                    <img class="cursor-pointer img-btn-14 mx-3"
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
                            <div class="flex flex-wrap justify-between items-end px-2 w-full py-2">
                                <div class="hidden lg:flex flex-nonwrap justify-between w-full  lg:w-6/12 xl:w-5/12">
                                    <img class="img-btn-20 mr-2 mt-5" src="@assets/images/barcode.svg" />
                                    <div class="w-11/12 input-text">
                                        <label>Agregar Concepto por Clave o código de barras</label>
                                        <vs-input ref="codigo_barras" name="codigo_barras" data-vv-as=" " type="text"
                                            class="w-full" placeholder="Ej. 0000000123" maxlength="28"
                                            v-model.trim="serverOptions.numero_control"
                                            v-on:keyup.enter="get_concepto_por_codigo('codigo_barras')"
                                            v-on:blur="get_concepto_por_codigo('codigo_barras', 'blur')" />
                                    </div>
                                    <img class="cursor-pointer img-btn-20 ml-2 mt-5"
                                        src="@assets/images/searcharticulo.svg"
                                        title="Buscador de artículos y servicios"
                                        @click="openBuscadorArticulos = true" />
                                </div>
                                <button class="w-full hidden lg:flex lg:w-4/12 xl:w-3/12 btn-icon-50 warning-btn my-1"
                                    v-if="this.form.conceptos.length > 0" @click="limpiar_conceptos()">
                                    Limpiar lista personalizada
                                    <img class="" src="@assets/images/quitar.svg" />
                                </button>
                                <button class="lg:hidden w-full btn-icon-50" @click="openBuscadorArticulos = true">
                                    Abrir conceptos del inventario
                                    <img src="@assets/images/folder.svg" />
                                </button>
                            </div>

                            <div class="w-full px-2 py-4">
                                <vs-table noDataText="" class="tabla-datos tabla-datos-no-data" :data="form.conceptos"
                                    ref="conceptos">
                                    <template slot="header">
                                        <h3>Listado de Conceptos</h3>
                                    </template>
                                    <template slot="thead">
                                        <vs-th class="w-auto">#</vs-th>
                                        <vs-th><span class="px-2">DESCRIPCIÓN</span></vs-th>
                                        <vs-th><span class="px-2">CANT.</span></vs-th>
                                        <vs-th><span class="px-2">COSTO</span></vs-th>
                                        <vs-th><span class="px-2">DESCUENTO</span></vs-th>
                                        <vs-th><span class="px-2">COSTO CON DESC.</span></vs-th>
                                        <vs-th><span class="px-2">IMPORTE</span></vs-th>
                                        <vs-th><span class="px-2">QUITAR</span></vs-th>
                                    </template>
                                    <template slot-scope="{ data }">
                                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                            <vs-td class="">
                                                <span class="font-semibold">{{ indextr + 1 }}</span>
                                            </vs-td>
                                            <vs-td class=" padding-y-7 w-8/12">
                                                <vs-input :ref="'descripcion' + indextr" :name="'descripcion' + indextr"
                                                    data-vv-as=" " data-vv-validate-on="blur" v-validate="'required'"
                                                    class="px-2 mr-auto ml-auto w-full input-cantidad" maxlength="150"
                                                    v-model="form.conceptos[indextr].descripcion" />
                                                <div class="size-smallest text-danger">
                                                    <span class="">
                                                        {{ errors.first("descripcion" + indextr) }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="">
                                                <vs-input :name="'cantidad' + indextr" data-vv-as=" "
                                                    data-vv-validate-on="blur"
                                                    v-validate="'required|integer|min_value:1'"
                                                    class="mr-auto ml-auto input-cantidad px-2 w-full" maxlength="4"
                                                    v-model="form.conceptos[indextr].cantidad" />
                                                <div class="size-smallest text-danger">
                                                    <span class="">
                                                        {{ errors.first("cantidad" + indextr) }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="">
                                                <vs-input :name="'costo_neto_normal' + indextr" data-vv-as=" "
                                                    data-vv-validate-on="blur"
                                                    v-validate="'required|decimal:2|min_value:0'"
                                                    class="mr-auto ml-auto input-cantidad px-2 w-full" maxlength="10"
                                                    v-model="form.conceptos[indextr].costo_neto_normal" />
                                                <div class="size-smallest text-danger">
                                                    <span class="">
                                                        {{ errors.first("costo_neto_normal" + indextr)
                                                            ? '$ Costo Neto.' : ''
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="">
                                                <vs-switch class="ml-auto mr-auto" color="success" icon-pack="feather"
                                                    v-model="form.conceptos[indextr].descuento_b">
                                                    <span slot="on">SI</span>
                                                    <span slot="off">NO</span>
                                                </vs-switch>
                                            </vs-td>
                                            <vs-td class="">
                                                <vs-input :name="'costo_neto_descuento' + indextr" data-vv-as=" "
                                                    data-vv-validate-on="blur" v-validate="'required|decimal:2|min_value:' +
                                                        0 +
                                                        '|max_value:' +
                                                        form.conceptos[indextr].costo_neto_normal
                                                        " class="mr-auto ml-auto input-cantidad px-2 w-full"
                                                    maxlength="10"
                                                    v-model="form.conceptos[indextr].costo_neto_descuento"
                                                    :disabled="form.conceptos[indextr].descuento_b == 0" />
                                                <div class="size-smallest text-danger">
                                                    <span class="">
                                                        {{ errors.first("costo_neto_descuento" + indextr)
                                                            ? '$ Costo Neto con Descuento.' : ''
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="">
                                                <div v-if="form.conceptos[indextr].descuento_b == 1">
                                                    $
                                                    {{
                                                        (form.conceptos[indextr].costo_neto_descuento *
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

                                            <vs-td class="">
                                                <div class="flex justify-center">
                                                    <img class="img-btn-14 mx-3" src="@assets/images/quitar.svg"
                                                        title="Esta venta ya fue cancelada, puede hacer click aquí para consultar"
                                                        @click="quitar_item(indextr, 'conceptos')" />
                                                </div>
                                            </vs-td>
                                        </vs-tr>
                                    </template>
                                </vs-table>
                                <button class="w-full btn-icon-50 my-4" @click="agregar_manual">
                                    AGREGAR CONCEPTO MANUALMENTE
                                    <img src="@assets/images/plus-success.svg" />
                                </button>
                            </div>
                            <div class="flex flex-wrap px-2 py-2">
                                <div class="w-full lg:w-7/12 xl:w-8/12 lg:pr-2 py-2">
                                    <vue-editor placeholder="Comentarios..." id="editor" v-model="form.nota"
                                        useCustomImageHandler @image-added="handleImageAdded"
                                        :editorOptions="editorSettings" :editorToolbar="customToolbar"></vue-editor>
                                </div>
                                <div class="w-full lg:w-5/12 xl:w-4/12 lg:pl-2 pt-6 lg:py-2">
                                    <div class="form-group h-full mt-0">
                                        <div class="title-form-group">Finalizar Cotización</div>
                                        <div class="form-group-content h-full content-center md:content-end">
                                            <div class="flex flex-wrap h-full content-between">
                                                <div class="w-full">
                                                    <div
                                                        class="w-full justify-between px-2 flex flex-wrap h2 py-2 lg:py-0">
                                                        <span>Total a Pagar</span>
                                                        <span :class="[totalCotizacion <= 0 ? 'text-danger' : '']">$ {{
                                                            totalCotizacion
                                                            | numFormat("0,000.00") }}</span>
                                                    </div>
                                                    <div
                                                        :class="['w-full justify-between px-2 flex flex-wrap lg:py-1', descuentoTotal > 0 ? 'text-danger h5' : 'size-base']">
                                                        <span>Descuento Aplicado</span>
                                                        <span>$ {{ descuentoTotal | numFormat("0,000.00") }}</span>
                                                    </div>
                                                </div>
                                                <div class="w-full lg:w-12/12 px-2 input-text large-size lg:py-0">
                                                    <label>
                                                        MODALIDAD DE PAGOS
                                                        <span>(*)</span>
                                                    </label>
                                                    <v-select :options="modalidades_pago" :clearable="false"
                                                        :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="form.modalidad_pago"
                                                        v-validate:modalidad_computed.immediate="'required'"
                                                        class="w-full" name="modalidad_pago" data-vv-as=" ">
                                                        <div slot="no-options">
                                                            No Se Ha Seleccionado Ninguna Opción
                                                        </div>
                                                    </v-select>
                                                    <span>{{ errors.first("modalidad_pago") }}</span>
                                                    <span v-if="this.errores['modalidad_pago.value']">{{
                                                        errores["modalidad_pago.value"][0]
                                                    }}</span>
                                                </div>
                                                <div
                                                    class="w-full md:w-6/12 px-2 input-text large-size input-cantidad py-2 lg:py-0">
                                                    <label>
                                                        $ {{ pago_inicial_validacion_computed.label }}
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input ref="pago_inicial" @change="changePagoInicial($event)"
                                                        v-validate.disable="pago_inicial_validacion_computed.rules"
                                                        :disabled="form.modalidad_pago.value == 1" name="pago_inicial"
                                                        data-vv-as=" " data-vv-validate-on="blur" type="text"
                                                        class="w-full" placeholder="" v-model="form.pago_inicial"
                                                        maxlength="12" />
                                                    <span v-show="form.modalidad_pago.value > 1">{{
                                                        errors.first("pago_inicial") ?
                                                            pago_inicial_validacion_computed.errorMessage :
                                                            '' }}</span>
                                                    <span v-if="this.errores.pago_inicial">{{
                                                        errores.pago_inicial[0]
                                                        }}</span>
                                                </div>
                                                <div
                                                    class="w-full md:w-6/12 px-2 input-text large-size input-cantidad py-2 lg:py-0">
                                                    <label>
                                                        % {{ pago_inicial_validacion_computed.label
                                                        }}
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input ref="pago_inicial_porcentaje"
                                                        @change="changePorcentaje($event)"
                                                        :disabled="form.modalidad_pago.value == 1"
                                                        name="pago_inicial_porcentaje" data-vv-as=" "
                                                        v-validate.disable="'required|decimal:2|min_value:' + 0 + '|max_value:100'"
                                                        data-vv-validate-on="blur" type="text" class="w-full "
                                                        v-model="form.pago_inicial_porcentaje" maxlength="6" />
                                                    <span v-show="form.modalidad_pago.value > 1">{{
                                                        errors.first("pago_inicial_porcentaje") ?
                                                            'Ingrese un valor entre el 1 y el 100 %' :
                                                            ''
                                                    }}</span>
                                                    <span v-if="this.errores.pago_inicial_porcentaje">{{
                                                        errores.pago_inicial_porcentaje[0]
                                                        }}</span>
                                                </div>
                                                <div :class="['w-full text-center px-2 py-4 lg:py-0']">
                                                    <span>{{ descripcion_pagos }}</span>
                                                </div>
                                                <vs-button class="w-full" color="success">
                                                    <span>Hacer Cotización</span>
                                                </vs-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>
        <ArticulosBuscador :z_index="'z-index56k'" :show="openBuscadorArticulos"
            @closeBuscador="openBuscadorArticulos = false" @LoteSeleccionado="LoteSeleccionado"></ArticulosBuscador>
        <ConfirmarDanger :z_index="'z-index58k'" :show="openConfirmarSinPassword"
            :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
            :accion="accionConfirmarSinPassword" :confirmarButton="botonConfirmarSinPassword"></ConfirmarDanger>
    </div>
</template>
<script>
import { VueEditor, Quill } from "vue2-editor";

import { ImageDrop } from "quill-image-drop-module";
import ImageResize from "quill-image-resize-vue";

Quill.register("modules/imageDrop", ImageDrop);
Quill.register("modules/imageResize", ImageResize);

import ConfirmarDanger from "@pages/ConfirmarDanger";
import funeraria from "@services/funeraria";
import cotizaciones from "@services/cotizaciones";
import vSelect from "vue-select";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ArticulosBuscador from "@pages/funeraria/servicios_funerarios/searcher_articulos.vue";
import { configdateTimePicker } from "@/VariablesGlobales";
import functions from "@/functions";
export default {
    components: {
        "v-select": vSelect,
        flatPickr,
        ArticulosBuscador,
        VueEditor,
        Quill,
        ConfirmarDanger,
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
        "form.modalidad_pago": function (newValue, oldValue) {
            this.form.pago_inicial = ''
            this.$nextTick(
                () => { this.$refs["pago_inicial"].$el.querySelector("input").focus() }
            );
        }
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
        vendedor_computed: function () {
            return this.form.vendedor.value;
        },
        modalidad_computed: function () {
            return this.form.modalidad_pago.value;
        },
        fecha_cotizacion_validacion_computed: function () {
            return this.form.fecha_cotizacion;
        },
        validez_validacion_computed: function () {
            return this.form.validez.value;
        },
        totalCotizacion: function () {
            let total = 0;
            this.form.conceptos.forEach((element, index) => {
                if (this.form.conceptos[index].descuento_b == 1) {
                    total +=
                        this.form.conceptos[index].cantidad *
                        this.form.conceptos[index].costo_neto_descuento;
                } else {
                    total +=
                        this.form.conceptos[index].cantidad *
                        this.form.conceptos[index].costo_neto_normal;
                }
            });
            if (this.form.modalidad_pago.value == 1) {
                this.form.pago_inicial = total;
                this.form.pago_inicial_porcentaje = 100;
            } else {
                this.form.pago_inicial = '';
                this.form.pago_inicial_porcentaje = '';
            }
            return functions.parseToDecimal(total);
        },
        descuentoTotal: function () {
            let total = 0;
            this.form.conceptos.forEach((element, index) => {
                if (this.form.conceptos[index].descuento_b == 1) {
                    total +=
                        (this.form.conceptos[index].costo_neto_normal - this.form.conceptos[index].costo_neto_descuento) * this.form.conceptos[index].cantidad;
                }
            });
            return total = functions.parseToDecimal(total);
        },
        pago_inicial_validacion_computed: function () {
            let montoTotal = Intl.NumberFormat(undefined,
                { minimumFractionDigits: 2 }).format(this.totalCotizacion) + " MXN.";
            if (this.form.modalidad_pago.value > 1) {
                //será a futuro
                return {
                    'rules': 'required|decimal:2|between:' + 0 + ',' + this.totalCotizacion,
                    'errorMessage': 'Cantidad mínima $ 0.00 y máxima ' + montoTotal,
                    'label': 'Pago Inicial'
                }
            } else {
                //uso inmediato
                return {
                    'rules': 'required|decimal:2|is:' + this.totalCotizacion,
                    'errorMessage': 'El pago debe ser de : $' + montoTotal,
                    'label': 'Pago Único'
                }
            }
        },
        descripcion_pagos: function () {
            if (this.totalCotizacion <= 0) return "Total de la cotización $0.00 MXN.";
            let pago_inicial = functions.parseToDecimal(this.form.pago_inicial);
            if (pago_inicial <= 0) return "Ingrese el Pago Inicial";
            let modalidad = this.form.modalidad_pago.value;
            if (modalidad == 1) return "Pago Único de $" + functions.formatCurrency(pago_inicial) + " MXN.";
            else {
                if (pago_inicial == this.totalCotizacion) return "Pago Único de $" + functions.formatCurrency(pago_inicial) + " MXN.";
            }
            //si la modalidad es en abonos
            let abonos = (this.totalCotizacion - pago_inicial) / modalidad;
            if (abonos < 0) {
                return "Verifique su pago inicial.";
            }
            if (modalidad == 2) {
                //es a dos pagos
                return "Pago Inicial de $" + functions.formatCurrency(pago_inicial) + " MXN, y 2 pagos de $" + functions.formatCurrency(abonos) + " MXN.";
            } else {
                //es a mas pagos
                let decimales = abonos - Math.trunc(abonos);
                if (decimales > 0) {
                    //se hacen los diferentes pagos para poner todas las decimales en el primer pago
                    let abono_1 = Math.trunc(abonos) + (modalidad * decimales);//1er abono con todo y decimales
                    modalidad -= 1;
                    abonos = Math.trunc(abonos);
                    if (abonos > 0)
                        return "Pago Inicial de $" + functions.formatCurrency(pago_inicial) + " MXN. Primer pago de  $" + functions.formatCurrency(abono_1) + " MXN y " + modalidad + " pagos de $" + functions.formatCurrency(abonos) + " MXN.";
                    else
                        return "Pago Inicial de $" + functions.formatCurrency(pago_inicial) + " MXN. y un segundo pago de  $" + functions.formatCurrency(abono_1) + " MXN.";
                } else {
                    //no hay decimales
                    return "Pago Inicial de $" + functions.formatCurrency(pago_inicial) + " MXN, y " + modalidad + " pagos de $" + functions.formatCurrency(abonos) + " MXN.";
                }
            }
        }
    },
    data() {
        return {
            customToolbar: [
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                ["bold", "italic", "underline", "strike"], // toggled buttons
                [
                    { align: "" },
                    { align: "center" },
                    { align: "right" },
                    { align: "justify" },
                ],
                //["blockquote", "code-block"],
                [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
                [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
                [{ color: [] }, { background: [] }], // dropdown with defaults from theme
                ["link", "image", "video"],
                ["clean"], // remove formatting button
            ],
            editorSettings: {
                modules: {
                    imageDrop: true,
                    imageResize: {},
                },
            },
            vendedores: [],
            modalidades_pago: [
                { label: "Pago Único", value: "1" },
                { label: "2 Pagos", value: "2" },
                { label: "3 Pagos", value: "3" },
                { label: "4 Pagos", value: "4" },
                { label: "5 Pagos", value: "5" },
                { label: "6 Pagos", value: "6" },
                { label: "7 Pagos", value: "7" },
                { label: "8 Pagos", value: "8" },
                { label: "9 Pagos", value: "9" },
            ],
            periodos_validez: [
                { label: "Uso Inmediato (1 Día)", value: "1" },
                { label: "1 Semana", value: "2" },
                { label: "2 Semanas", value: "3" },
                { label: "3 Semanas", value: "4" },
                { label: "1 Mes", value: "5" },
            ],
            oldValuePorcentaje: '',
            form: {
                cliente: "",
                telefono: "",
                email: "",
                vendedor: { label: "Seleccione 1", value: "" },
                fecha_cotizacion: "",
                validez: { label: "Uso Inmediato (1 Día)", value: "1" },
                cotizaciones_predefinidas_b: false,
                predefinidos: [],
                conceptos: [
                    {
                        descripcion: "Ataúd Metálico Estándar Celestial",
                        costo_neto_normal: 1500,
                        descuento_b: 0,
                        costo_neto_descuento: 1000,
                        cantidad: 1,
                        importe: 0,
                    },
                ],
                nota: "",
                modalidad_pago: { label: "Pago Único", value: "1" },
                pago_inicial: "",
                pago_inicial_porcentaje: "",
            },
            serverOptions: {
                numero_control: "",
            },
            configdateTimePicker: configdateTimePicker,
            openBuscadorArticulos: false,
            errores: [],
            openConfirmarSinPassword: false,
            botonConfirmarSinPassword: "",
            accionConfirmarSinPassword: "",
            callBackConfirmar: Function,
            errores: [],
        };
    },
    methods: {
        changePagoInicial(event) {
            let pago_inicial = functions.parseToDecimal(this.form.pago_inicial);
            if (pago_inicial > this.totalCotizacion || pago_inicial < 0) {
                this.form.pago_inicial = '';
                this.$nextTick(
                    () => { this.$refs["pago_inicial"].$el.querySelector("input").focus() }
                );
            } else if (this.form.modalidad_pago.value != 1) {
                this.form.pago_inicial_porcentaje = functions.parseToDecimal((pago_inicial * 100) / this.totalCotizacion);
            }
        },
        changePorcentaje(event) {
            let pago_inicial_porcentaje = functions.parseToDecimal(this.form.pago_inicial_porcentaje);
            if (pago_inicial_porcentaje > 100) {
                this.form.pago_inicial_porcentaje = '';
                this.$nextTick(
                    () => { this.$refs["pago_inicial_porcentaje"].$el.querySelector("input").focus() }
                );
            }
            else if (this.form.modalidad_pago.value != 1) {
                this.form.pago_inicial = functions.parseToDecimal((pago_inicial_porcentaje * this.totalCotizacion) / 100);
            }
        },
        handleImageAdded: function (file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("image", file);
            cotizaciones
                .upload(formData)
                .then((result) => {
                    const url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch((err) => {
                    console.log(err);
                });
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
                descripcion: "",
                cantidad: 1,
                costo_neto_normal: "",
                descuento_b: "",
                costo_neto_descuento: "",
                importe: "",
            });
            this.$nextTick(() => {
                let index = this.form.conceptos.length - 1;
                this.$refs["descripcion" + index][0].$el.querySelector("input").focus();
            });
        },
        //remover beneficiario
        quitar_item(index_item, nombre_lista) {
            this.botonConfirmarSinPassword = "Eliminar";
            //variables del modal confirmar sin password
            this.form.index_item = index_item;
            this.openConfirmarSinPassword = true;
            if (nombre_lista == "conceptos") {
                this.accionConfirmarSinPassword =
                    "Se quitará el concepto: " +
                    this.form.conceptos[index_item].descripcion +
                    " de la lista.";
                this.callBackConfirmar = this.quitar_item_callback;
            }
        },
        //remover beneficiario callback quita del array al beneficiario seleccionado
        quitar_item_callback() {
            this.form.conceptos.splice(this.form.index_item, 1);
        },
        limpiar_conceptos() {
            if (this.form.conceptos.length == 0) return;
            this.botonConfirmarSinPassword = "Quitar Conceptos";
            this.accionConfirmarSinPassword = "Todos los conceptos capturados serán removidos.";
            this.callBackConfirmar = this.eliminar_conceptos_callback;
            this.openConfirmarSinPassword = true;
        },
        eliminar_conceptos_callback() {
            this.form.conceptos = [];
        },
    },
    created() { },
    updated() { },
};
</script>

<style scoped></style>
