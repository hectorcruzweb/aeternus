<template>
    <div>
        <vs-popup
            :class="['forms-popup bg-content-theme', z_index]"
            fullscreen
            close="cancelar"
            :title="
                getTipoformulario == 'agregar'
                    ? 'compras a proveedor'
                    : 'POR DEFINIR FUNCION'
            "
            :active.sync="showVentana"
            ref="formulario"
        >
            <!--inicio venta-->
            <div class="flex flex-wrap">
                <div class="w-full py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">Datos de la compra</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full xl:w-2/12 px-2 input-text">
                                    <label>
                                        Fecha de la compra
                                        <span>(*)</span>
                                    </label>
                                    <flat-pickr
                                        name="fecha_compra"
                                        data-vv-as=" "
                                        v-validate:fecha_compra_validacion_computed.immediate="
                                            'required'
                                        "
                                        :config="configdateTimePicker"
                                        v-model="form.fecha_compra"
                                        placeholder="Fecha de la compra"
                                        class="w-full"
                                    />
                                    <span>
                                        {{ errors.first("fecha_compra") }}
                                    </span>
                                    <span v-if="this.errores.fecha_compra">{{
                                        errores.fecha_compra[0]
                                    }}</span>
                                </div>

                                <div class="w-full xl:w-7/12">
                                    <div
                                        class="w-full px-2 input-text"
                                        v-if="form.id_proveedor == ''"
                                    >
                                        <label>
                                            Proveedor
                                            <span>(*)</span>
                                        </label>
                                        <div
                                            class="
                        bg-danger-50
                        text-center
                        py-2
                        px-2
                        size-base
                        border-danger-solid-1
                        cursor-pointer
                        color-danger-900
                      "
                                            @click="
                                                openBuscadorProveedores = true
                                            "
                                        >
                                            Click para seleccionar al proveedor
                                        </div>
                                    </div>
                                    <div class="w-full px-2 input-text" v-else>
                                        <label>
                                            Proveedor
                                            <span>(*)</span>
                                        </label>
                                        <div
                                            class="
                        bg-success-50
                        py-2
                        px-2
                        size-base
                        border-success-solid-2
                        uppercase
                      "
                                        >
                                            <div class="flex flex-wrap">
                                                <div class="w-full xl:w-8/12">
                                                    <span class="font-medium">
                                                        Clave:
                                                    </span>
                                                    {{ form.id_proveedor }},
                                                    <span class="font-medium">
                                                        Nombre:
                                                    </span>
                                                    {{ form.proveedor }}
                                                </div>
                                                <div
                                                    class="w-full xl:w-4/12 text-center xl:text-right"
                                                >
                                                    <span
                                                        @click="
                                                            quitarProveedor()
                                                        "
                                                        class="color-danger-900 cursor-pointer"
                                                        >X Cambiar proveedor
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full input-text xl:w-3/12 px-2">
                                    <label
                                        >Núm. Referencia <span>(*)</span></label
                                    >
                                    <vs-input
                                        name="referencia"
                                        v-validate="'required'"
                                        maxlength="45"
                                        type="text"
                                        class="w-full"
                                        placeholder="Referencia de nota o factura"
                                        v-model="form.referencia"
                                    />
                                    <span>
                                        {{ errors.first("referencia") }}
                                    </span>
                                    <span v-if="this.errores.referencia">{{
                                        errores.referencia[0]
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="title-form-group">
                            Desglose de Artículos y Servicios a Pagar
                        </div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <img
                                    class="img-btn-20 mx-3 mt-4 hidden lg:block"
                                    src="@assets/images/barcode.svg"
                                />
                                <div
                                    class="
                    w-auto
                    lg:w-4/12
                    xl:w-2/12
                    px-2
                    input-text
                    hidden
                    lg:block
                  "
                                >
                                    <label>Clave o código de barras</label>
                                    <vs-input
                                        ref="codigo_barras"
                                        name="codigo_barras"
                                        data-vv-as=" "
                                        type="text"
                                        class="w-full"
                                        placeholder="Ej. 0000000123"
                                        maxlength="28"
                                        v-model.trim="
                                            serverOptions.numero_control
                                        "
                                        v-on:keyup.enter="
                                            get_concepto_por_codigo(
                                                'codigo_barras'
                                            )
                                        "
                                        v-on:blur="
                                            get_concepto_por_codigo(
                                                'codigo_barras',
                                                'blur'
                                            )
                                        "
                                    />
                                </div>
                                <img
                                    class="cursor-pointer img-btn-20 mx-3 mt-4 hidden lg:block"
                                    src="@assets/images/searcharticulo.svg"
                                    title="Buscador de artículos y servicios"
                                    @click="openBuscadorArticulos = true"
                                />

                                <div class="w-full text-right block lg:hidden">
                                    <vs-button
                                        class="sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                                        color="primary"
                                        @click="openBuscadorArticulos = true"
                                    >
                                        <span>Buscar artículos</span>
                                    </vs-button>
                                </div>

                                <div class="w-full my-6 px-2">
                                    <vs-table
                                        class="tabla-datos"
                                        :data="form.articulos"
                                        noDataText="No se han agregado Artículos ni Servicios"
                                    >
                                        <template slot="header">
                                            <h3>
                                                Artículos que Incluye la Compra
                                            </h3>
                                        </template>
                                        <template slot="thead">
                                            <vs-th>#</vs-th>
                                            <vs-th>Descripción</vs-th>
                                            <vs-th>Cant.</vs-th>
                                            <vs-th>Costo Neto</vs-th>
                                            <vs-th>Descuento</vs-th>
                                            <vs-th
                                                >Costo Neto Con Descuento</vs-th
                                            >
                                            <vs-th>Importe</vs-th>
                                            <vs-th>Facturable</vs-th>
                                            <vs-th>Quitar</vs-th>
                                        </template>
                                        <template slot-scope="{ data }">
                                            <vs-tr
                                                :data="tr"
                                                :key="indextr"
                                                v-for="(tr, indextr) in data"
                                            >
                                                <vs-td class="">
                                                    <div>
                                                        <span>{{
                                                            indextr + 1
                                                        }}</span>
                                                    </div>
                                                </vs-td>

                                                <vs-td class="">
                                                    <div class="uppercase">
                                                        {{
                                                            data[indextr]
                                                                .descripcion
                                                        }},
                                                        {{ data[indextr].tipo }}
                                                    </div>
                                                </vs-td>

                                                <vs-td class="">
                                                    <vs-input
                                                        :name="
                                                            'cantidad_articulos' +
                                                                indextr
                                                        "
                                                        data-vv-as=" "
                                                        data-vv-validate-on="blur"
                                                        v-validate="
                                                            'required|integer|min_value:' +
                                                                1 +
                                                                '|max_value:1000'
                                                        "
                                                        class="mr-auto ml-auto input-cantidad"
                                                        maxlength="4"
                                                        v-model="
                                                            form.articulos[
                                                                indextr
                                                            ].cantidad
                                                        "
                                                    />
                                                    <div class="input-text">
                                                        <span>
                                                            {{
                                                                errors.first(
                                                                    "cantidad_articulos" +
                                                                        indextr
                                                                )
                                                            }}
                                                        </span>
                                                    </div>
                                                </vs-td>

                                                <vs-td class="">
                                                    <vs-input
                                                        :name="
                                                            'costo_neto_normal_articulos' +
                                                                indextr
                                                        "
                                                        data-vv-as=" "
                                                        data-vv-validate-on="blur"
                                                        v-validate="
                                                            'required|decimal:2|min_value:' +
                                                                0
                                                        "
                                                        class="mr-auto ml-auto input-cantidad"
                                                        maxlength="10"
                                                        v-model="
                                                            form.articulos[
                                                                indextr
                                                            ].costo_neto_normal
                                                        "
                                                        :disabled="
                                                            form.articulos[
                                                                indextr
                                                            ].descuento_b == 1
                                                        "
                                                    />
                                                    <div class="input-text">
                                                        <span>
                                                            {{
                                                                errors.first(
                                                                    "costo_neto_normal_articulos" +
                                                                        indextr
                                                                )
                                                            }}
                                                        </span>
                                                    </div>
                                                </vs-td>

                                                <vs-td class="">
                                                    <vs-switch
                                                        class="ml-auto mr-auto"
                                                        color="success"
                                                        icon-pack="feather"
                                                        v-model="
                                                            form.articulos[
                                                                indextr
                                                            ].descuento_b
                                                        "
                                                    >
                                                        <span slot="on"
                                                            >SI</span
                                                        >
                                                        <span slot="off"
                                                            >NO</span
                                                        >
                                                    </vs-switch>
                                                </vs-td>
                                                <vs-td class="">
                                                    <vs-input
                                                        :name="
                                                            'costo_neto_descuento' +
                                                                indextr
                                                        "
                                                        data-vv-as=" "
                                                        data-vv-validate-on="blur"
                                                        v-validate="
                                                            'required|decimal:2|min_value:' +
                                                                0 +
                                                                '|max_value:' +
                                                                form.articulos[
                                                                    indextr
                                                                ]
                                                                    .costo_neto_normal
                                                        "
                                                        class="mr-auto ml-auto input-cantidad"
                                                        maxlength="10"
                                                        v-model="
                                                            form.articulos[
                                                                indextr
                                                            ]
                                                                .costo_neto_descuento
                                                        "
                                                        :disabled="
                                                            form.articulos[
                                                                indextr
                                                            ].descuento_b == 0
                                                        "
                                                    />
                                                    <div class="input-text">
                                                        <span>
                                                            {{
                                                                errors.first(
                                                                    "costo_neto_descuento" +
                                                                        indextr
                                                                )
                                                            }}
                                                        </span>
                                                    </div>
                                                </vs-td>

                                                <vs-td class="">
                                                    <div
                                                        v-if="
                                                            form.articulos[
                                                                indextr
                                                            ].descuento_b == 1
                                                        "
                                                    >
                                                        $
                                                        {{
                                                            (form.articulos[
                                                                indextr
                                                            ]
                                                                .costo_neto_descuento *
                                                                form.articulos[
                                                                    indextr
                                                                ].cantidad)
                                                                | numFormat(
                                                                    "0,000.00"
                                                                )
                                                        }}
                                                    </div>
                                                    <div v-else>
                                                        $
                                                        {{
                                                            (form.articulos[
                                                                indextr
                                                            ]
                                                                .costo_neto_normal *
                                                                form.articulos[
                                                                    indextr
                                                                ].cantidad)
                                                                | numFormat(
                                                                    "0,000.00"
                                                                )
                                                        }}
                                                    </div>
                                                </vs-td>

                                                <vs-td class="">
                                                    <vs-switch
                                                        class="ml-auto mr-auto"
                                                        color="success"
                                                        icon-pack="feather"
                                                        v-model="
                                                            form.articulos[
                                                                indextr
                                                            ].facturable_b
                                                        "
                                                    >
                                                        <span slot="on"
                                                            >SI</span
                                                        >
                                                        <span slot="off"
                                                            >NO</span
                                                        >
                                                    </vs-switch>
                                                </vs-td>

                                                <vs-td class="">
                                                    <div
                                                        class="flex justify-center"
                                                        @click="
                                                            remover_articulo(
                                                                indextr
                                                            )
                                                        "
                                                        v-if="!fueCancelada"
                                                    >
                                                        <img
                                                            class="cursor-pointer img-btn-20 mx-3"
                                                            src="@assets/images/minus.svg"
                                                        />
                                                    </div>
                                                </vs-td>
                                            </vs-tr>
                                        </template>
                                    </vs-table>
                                </div>

                                <div class="w-full">
                                    <div class="flex flex-wrap my-6">
                                        <div class="w-full xl:w-8/12 px-2">
                                            <div class="form-group py-6">
                                                <div class="title-form-group">
                                                    Gastos Incurridos de la
                                                    Compra
                                                </div>
                                                <div class="form-group-content">
                                                    <div class="flex flex-wrap">
                                                        <div
                                                            class="w-full input-text xl:w-7/12 px-2"
                                                        >
                                                            <label
                                                                >Concepto del
                                                                costo incurrido
                                                                <span
                                                                    v-if="
                                                                        this
                                                                            .form
                                                                            .costo_incurrido_detalle
                                                                    "
                                                                    >(*)</span
                                                                ></label
                                                            >
                                                            <vs-input
                                                                name="costo_incurrido_detalle"
                                                                maxlength="150"
                                                                data-vv-as=" "
                                                                v-validate="
                                                                    this
                                                                        .validar_costo_incurrido_detalle
                                                                "
                                                                type="text"
                                                                class="w-full"
                                                                placeholder=""
                                                                v-model="
                                                                    form.costo_incurrido_detalle
                                                                "
                                                            />
                                                            <span>
                                                                {{
                                                                    errors.first(
                                                                        "costo_incurrido_detalle"
                                                                    )
                                                                }}
                                                            </span>
                                                            <span
                                                                v-if="
                                                                    this.errores
                                                                        .costo_incurrido_detalle
                                                                "
                                                                >{{
                                                                    errores
                                                                        .costo_incurrido_detalle[0]
                                                                }}</span
                                                            >
                                                        </div>
                                                        <div
                                                            class="w-full input-text xl:w-3/12 px-2"
                                                        >
                                                            <label
                                                                >Total del costo
                                                                incurrido<span
                                                                    v-if="
                                                                        this
                                                                            .form
                                                                            .costo_incurrido_detalle
                                                                    "
                                                                    >(*)</span
                                                                ></label
                                                            >
                                                            <vs-input
                                                                name="costo_incurrido_costo"
                                                                data-vv-as=" "
                                                                v-validate="
                                                                    this
                                                                        .validar_costo_incurrido_costo
                                                                "
                                                                maxlength="12"
                                                                type="text"
                                                                class="w-full"
                                                                placeholder=""
                                                                v-model="
                                                                    form.costo_incurrido_costo
                                                                "
                                                            />
                                                            <span>
                                                                {{
                                                                    errors.first(
                                                                        "costo_incurrido_costo"
                                                                    )
                                                                }}
                                                            </span>
                                                            <span
                                                                v-if="
                                                                    this.errores
                                                                        .costo_incurrido_costo
                                                                "
                                                                >{{
                                                                    errores
                                                                        .costo_incurrido_costo[0]
                                                                }}</span
                                                            >
                                                        </div>

                                                        <div
                                                            class="
                                w-full
                                input-text
                                xl:w-2/12
                                px-2
                                text-center
                              "
                                                        >
                                                            <label
                                                                >Facturable</label
                                                            >
                                                            <vs-switch
                                                                class="ml-auto mr-auto mt-3"
                                                                color="success"
                                                                icon-pack="feather"
                                                                v-model="
                                                                    form.facturable_b_gasto_incurrido
                                                                "
                                                            >
                                                                <span slot="on"
                                                                    >SI</span
                                                                >
                                                                <span slot="off"
                                                                    >NO</span
                                                                >
                                                            </vs-switch>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap">
                                                <div
                                                    class="w-full input-text px-2"
                                                >
                                                    <label
                                                        >Nota u
                                                        Observación:</label
                                                    >
                                                    <vs-textarea
                                                        height="400px"
                                                        :rows="9"
                                                        size="large"
                                                        ref="nota"
                                                        type="text"
                                                        class="w-full"
                                                        v-model.trim="form.nota"
                                                    />
                                                </div>
                                            </div>
                                            <!--fin del resumen de la venta-->
                                        </div>
                                        <div class="w-full xl:w-4/12 px-2">
                                            <div class="flex flex-wrap">
                                                <div
                                                    class="w-full input-text px-2 text-center"
                                                >
                                                    <label>
                                                        Tasa IVA %
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input
                                                        :disabled="
                                                            tiene_pagos_realizados ||
                                                                ventaLiquidada ||
                                                                fueCancelada
                                                        "
                                                        size="large"
                                                        name="tasa_iva"
                                                        data-vv-as=" "
                                                        v-validate="
                                                            'required|decimal:2|min_value:0|max_value:25'
                                                        "
                                                        type="text"
                                                        class="w-full cantidad"
                                                        placeholder="Porcentaje IVA"
                                                        v-model="form.tasa_iva"
                                                        maxlength="2"
                                                    />
                                                    <span
                                                        class="mensaje-requerido"
                                                    >
                                                        {{
                                                            errors.first(
                                                                "tasa_iva"
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        class="mensaje-requerido"
                                                        v-if="
                                                            this.errores
                                                                .tasa_iva
                                                        "
                                                        >{{
                                                            errores.tasa_iva[0]
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="w-full px-2 text-center mt-3"
                                                >
                                                    <label
                                                        class="h4 font-medium color-copy"
                                                        >$ Total de la
                                                        Compra</label
                                                    >
                                                    <div
                                                        class="mt-3 text-center"
                                                    >
                                                        <span class="h5">
                                                            $
                                                            {{
                                                                totalCompra
                                                                    | numFormat(
                                                                        "0,000.00"
                                                                    )
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div
                                                    class="w-full input-text px-2 text-center"
                                                >
                                                    <label class="font-bold">
                                                        $ Pago con Efectivo
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input
                                                        size="large"
                                                        name="pago_efectivo"
                                                        data-vv-as=" "
                                                        v-validate="
                                                            'required|decimal:2|min_value:0'
                                                        "
                                                        type="text"
                                                        class="w-full cantidad"
                                                        placeholder=""
                                                        v-model="
                                                            form.pago_efectivo
                                                        "
                                                        maxlength="12"
                                                    />
                                                    <span
                                                        class="mensaje-requerido"
                                                    >
                                                        {{
                                                            errors.first(
                                                                "pago_efectivo"
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        class="mensaje-requerido"
                                                        v-if="
                                                            this.errores
                                                                .pago_efectivo
                                                        "
                                                        >{{
                                                            errores
                                                                .pago_efectivo[0]
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="w-full input-text px-2 text-center"
                                                >
                                                    <label>
                                                        $ Pago con Cheque
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input
                                                        size="large"
                                                        name="pago_cheque"
                                                        data-vv-as=" "
                                                        v-validate="
                                                            'required|decimal:2|min_value:0'
                                                        "
                                                        type="text"
                                                        class="w-full cantidad"
                                                        placeholder=""
                                                        v-model="
                                                            form.pago_cheque
                                                        "
                                                        maxlength="12"
                                                    />
                                                    <span
                                                        class="mensaje-requerido"
                                                    >
                                                        {{
                                                            errors.first(
                                                                "pago_cheque"
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        class="mensaje-requerido"
                                                        v-if="
                                                            this.errores
                                                                .pago_cheque
                                                        "
                                                        >{{
                                                            errores
                                                                .pago_cheque[0]
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="w-full input-text px-2 text-center"
                                                >
                                                    <label>
                                                        $ Pago con Tarjeta
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input
                                                        size="large"
                                                        name="pago_tarjeta"
                                                        data-vv-as=" "
                                                        v-validate="
                                                            'required|decimal:2|min_value:0'
                                                        "
                                                        type="text"
                                                        class="w-full cantidad"
                                                        placeholder=""
                                                        v-model="
                                                            form.pago_tarjeta
                                                        "
                                                        maxlength="12"
                                                    />
                                                    <span
                                                        class="mensaje-requerido"
                                                    >
                                                        {{
                                                            errors.first(
                                                                "pago_tarjeta"
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        class="mensaje-requerido"
                                                        v-if="
                                                            this.errores
                                                                .pago_tarjeta
                                                        "
                                                        >{{
                                                            errores
                                                                .pago_tarjeta[0]
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="w-full input-text px-2 text-center"
                                                >
                                                    <label>
                                                        $ Pago con Transferencia
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input
                                                        size="large"
                                                        name="pago_transferencia"
                                                        data-vv-as=" "
                                                        v-validate="
                                                            'required|decimal:2|min_value:0'
                                                        "
                                                        type="text"
                                                        class="w-full cantidad"
                                                        placeholder=""
                                                        v-model="
                                                            form.pago_transferencia
                                                        "
                                                        maxlength="12"
                                                    />
                                                    <span
                                                        class="mensaje-requerido"
                                                    >
                                                        {{
                                                            errors.first(
                                                                "pago_transferencia"
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        class="mensaje-requerido"
                                                        v-if="
                                                            this.errores
                                                                .pago_transferencia
                                                        "
                                                        >{{
                                                            errores
                                                                .pago_transferencia[0]
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="w-full px-2 text-center mt-3 hidden"
                                                >
                                                    <label
                                                        class="h4 font-medium color-copy"
                                                        >$ Resto a Pagar a
                                                        Crédito</label
                                                    >
                                                    <div
                                                        class="mt-3 text-center"
                                                    >
                                                        <span class="h5">
                                                            <span
                                                                v-if="
                                                                    totalCredito >=
                                                                        0
                                                                "
                                                            >
                                                                $
                                                                {{
                                                                    totalCredito
                                                                        | numFormat(
                                                                            "0,000.00"
                                                                        )
                                                                }}
                                                            </span>
                                                            <span
                                                                v-else
                                                                class="text-danger size-base"
                                                            >
                                                                El total a pagar
                                                                sobrepasa el
                                                                total de la
                                                                compra
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div
                                                    class="
                            w-full
                            px-2
                            size-base
                            color-copy
                            mt-3
                            text-center
                          "
                                                >
                                                    <span
                                                        class="color-danger-900 font-medium"
                                                        >Ojo:</span
                                                    >
                                                    Los costos de los conceptos
                                                    capturados ya incluyen el
                                                    IVA.
                                                </div>

                                                <div
                                                    class="w-full input-text px-2"
                                                >
                                                    <vs-button
                                                        v-if="!fueCancelada"
                                                        class="w-full ml-auto mr-auto mt-3"
                                                        @click="acceptAlert()"
                                                        color="success"
                                                    >
                                                        <span
                                                            >Guardar
                                                            Compra</span
                                                        >
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
        <Password
            :z_index="'z-index56k'"
            :show="openPassword"
            :callback-on-success="callback"
            @closeVerificar="closePassword"
            :accion="accionNombre"
        ></Password>
        <ConfirmarDanger
            :z_index="'z-index58k'"
            :show="openConfirmarSinPassword"
            :callback-on-success="callBackConfirmar"
            @closeVerificar="openConfirmarSinPassword = false"
            :accion="accionConfirmarSinPassword"
            :confirmarButton="botonConfirmarSinPassword"
        ></ConfirmarDanger>

        <ConfirmarAceptar
            :show="openConfirmarAceptar"
            :callback-on-success="callBackConfirmarAceptar"
            @closeVerificar="openConfirmarAceptar = false"
            :accion="'He revisado la información y quiero guardar la venta'"
            :confirmarButton="'Guardar Venta'"
        ></ConfirmarAceptar>

        <ClientesBuscador
            :z_index="'z-index55k'"
            :show="openBuscadorProveedores"
            @closeBuscador="openBuscadorProveedores = false"
            @retornoCliente="clienteSeleccionado"
        ></ClientesBuscador>

        <ArticulosBuscador
            :z_index="'z-index56k'"
            :show="openBuscadorArticulos"
            @closeBuscador="openBuscadorArticulos = false"
            @LoteSeleccionado="LoteSeleccionado"
        ></ArticulosBuscador>
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
import inventario from "@services/inventario";
import funeraria from "@services/funeraria";
import ClientesBuscador from "@pages/inventarios/compras/searcher.vue";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ArticulosBuscador from "@pages/funeraria/servicios_funerarios/searcher_articulos.vue";

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
        ClientesBuscador
    },
    props: {
        show: {
            type: Boolean,
            required: true
        },
        //para saber que tipo de formulario es
        tipo: {
            type: String,
            required: true
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index54k"
        }
    },
    watch: {
        show: function(newValue, oldValue) {
            this.limpiarValidation();
            if (newValue == true) {
                this.$nextTick(
                    () => {}
                    // this.$refs["fallecido_ref"].$el.querySelector("input").focus()
                );
                this.$refs["formulario"].$el.querySelector(
                    ".vs-icon"
                ).onclick = () => {
                    this.cancelar();
                };
                (async () => {
                    if (this.getTipoformulario == "agregar") {
                        //await this.get_solicitudes_servicios_id();
                    }
                })();
            } else {
                /**acciones al cerrar el formulario */
            }
        }
    },
    computed: {
        /**validaciones de los selects */

        /* afiliacion_validacion_computed: function () {
      return this.form.afiliacion.value;
    },
*/

        validar_costo_incurrido_costo: function() {
            if (this.form.costo_incurrido_detalle != "") {
                return "decimal:2|min_value:0|required";
            } else return "decimal:2|min_value:0";
        },

        validar_costo_incurrido_detalle: function() {
            if (this.form.costo_incurrido_costo > 0) {
                return "required";
            } else return "";
        },

        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            }
        },
        getTipoformulario: {
            get() {
                return this.tipo;
            },
            set(newValue) {
                return newValue;
            }
        },

        totalCompra: function() {
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
            if (this.form.costo_incurrido_costo <= 0) {
                //this.form.costo_incurrido_costo = 0;
            } else {
                total += parseFloat(this.form.costo_incurrido_costo);
            }

            return total;
        },

        totalCredito: function() {
            let total = 0;
            let totalPagado =
                this.form.pago_efectivo +
                this.form.pago_cheque +
                this.form.pago_tarjeta +
                this.form.pago_transferencia;
            total = this.totalCompra - totalPagado;
            return total;
        },

        fecha_compra_validacion_computed: function() {
            return this.form.fecha_compra;
        }
    },

    data() {
        return {
            sino: [
                {
                    value: "1",
                    label: "SI"
                },
                {
                    value: "0",
                    label: "NO"
                }
            ],

            serverOptions: {
                numero_control: ""
            },
            form: {
                fecha_compra: "",
                id_proveedor: "",
                proveedor: "",
                referencia: "",
                articulos: [],
                tasa_iva: 16,
                pago_efectivo: 0,
                pago_cheque: 0,
                pago_tarjeta: 0,
                pago_transferencia: 0,
                pago_credito: 0,
                nota: "",
                costo_incurrido_detalle: "",
                costo_incurrido_costo: "",
                facturable_b_gasto_incurrido: 0
            },
            /**variables dle modulo */
            openBuscadorArticulos: false,
            openBuscadorProveedores: false,

            configdateTimePicker: configdateTimePicker,
            accionConfirmarSinPassword: "",
            botonConfirmarSinPassword: "",
            openPassword: false,
            openConfirmarSinPassword: false,
            callback: Function,
            callBackConfirmar: Function,
            openConfirmarAceptar: false,
            callBackConfirmarAceptar: Function,
            accionNombre: "Actualizar Contrato",
            errores: []
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
                .then(res => {
                    if (res.data.length > 0) {
                        let datos = res.data[0];
                        /**agrego el concepto al listado del contrato */
                        if (datos.tipo_articulos_id == 1) {
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
                                //costo_neto_normal: datos.precio_venta,
                                costo_neto_normal: "",
                                descuento_b: 0,
                                costo_neto_descuento: 0,
                                importe: 0,
                                facturable_b: 1,
                                existencia: 0
                            });
                        } else {
                            this.$vs.notify({
                                title: "Buscar artículos",
                                text:
                                    "No se puede agregar conceptos de tipo servicio en una compra.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "warning",
                                time: 4000
                            });
                        }
                    } else {
                        this.$vs.notify({
                            title: "Buscar artículos",
                            text:
                                "No se ha encontrado el concepto con el número de clave ingresado.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 8000
                        });
                    }
                    this.$vs.loading.close();
                    this.serverOptions.numero_control = "";
                    this.$nextTick(() =>
                        this.$refs["codigo_barras"].$el
                            .querySelector("input")
                            .focus()
                    );
                })
                .catch(err => {
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
                                time: 8000
                            });
                        }
                    }
                });
        },

        acceptAlert() {
            this.$validator
                .validateAll()
                .then(result => {
                    if (!result) {
                        this.$vs.notify({
                            title: "Error",
                            text:
                                "Verifique que todos los datos han sido capturados",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "12000"
                        });
                        return;
                    } else {
                        //AL LLEGAR AQUI SE SABE QUE EL FORMULARIO PASO LA VALIDACION
                        if (this.form.articulos.length == 0) {
                            this.$vs.notify({
                                title: "Error",
                                text:
                                    "Verifique que capturó los artículos de la compra",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "danger",
                                position: "bottom-right",
                                time: "12000"
                            });
                            return;
                        }
                        (async () => {
                            if (this.getTipoformulario == "agregar") {
                                /**EL FORMULARIO ES SOLO DE VALIDACION */
                                this.callBackConfirmarAceptar = await this
                                    .control_compras;
                                this.openConfirmarAceptar = true;
                            }
                        })();
                    }
                })
                .catch(() => {});
        },

        async control_compras() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await inventario.control_compras(this.form);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Compra a Proveedores",
                        text: "Se ha registrado la compra correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000
                    });
                    this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Compra a Proveedores",
                        text:
                            "Error al registrar la compra, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 6000
                    });
                }
                this.$vs.loading.close();
            } catch (err) {
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
                            time: 4000
                        });
                    } else if (err.response.status == 422) {
                        //checo si existe cada error
                        this.errores = err.response.data.error;
                        this.$vs.notify({
                            title: "Registro de Compras",
                            text:
                                "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000
                        });
                    } else if (err.response.status == 409) {
                        //este error es por alguna condicion que el contrano no cumple para modificar
                        //la propiedad esa ya ha sido vendida
                        this.$vs.notify({
                            title: "Registro de Compras",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 8000
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
            if (datos.tipo_articulos_id == 1) {
                /**agregando los datos a la lista de articulos y servicios */
                this.form.articulos.push(datos);
            } else {
                this.$vs.notify({
                    title: "Buscar artículos",
                    text:
                        "No se puede agregar conceptos de tipo servicio en una compra.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "warning",
                    time: 4000
                });
            }
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
        //regresa los datos a su estado inicial
        limpiarVentana() {
            this.form.articulos = [];
            this.form.tasa_iva = 16;
            this.form.nota = "";
            this.form.fecha_compra = "";
            this.form.id_proveedor = "";
            this.form.proveedor = "";
            this.form.referencia = "";
            this.form.pago_efectivo = 0;
            this.form.pago_cheque = 0;
            this.form.pago_tarjeta = 0;
            this.form.pago_transferencia = 0;
        },

        closePassword() {
            this.openPassword = false;
        },
        limpiarValidation() {
            this.$validator.pause();
            this.$nextTick(() => {
                this.$validator.errors.clear();
                this.$validator.fields.items.forEach(field => field.reset());
                this.$validator.fields.items.forEach(field =>
                    this.errors.remove(field)
                );
                this.$validator.resume();
            });
        },

        clienteSeleccionado(datos) {
            /**obtiene los datos retornados del buscar cliente */
            this.form.proveedor = datos.nombre;
            this.form.id_proveedor = datos.numero_control;
            //this.form.direccion_cliente = datos.datos.direccion;
        },

        quitarProveedor() {
            this.botonConfirmarSinPassword = "Cambiar proveedor";
            this.accionConfirmarSinPassword =
                "¿Desea cambiar de proveedor para esta compra?";
            this.callBackConfirmar = this.limpiarProveedor;
            this.openConfirmarSinPassword = true;
        },

        limpiarProveedor() {
            this.form.id_proveedor = "";
            this.form.proveedor = "";
        }
    },
    created() {}
};
</script>
