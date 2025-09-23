<template>
    <div>
        <vs-popup
            :class="['forms-popup', z_index]"
            fullscreen
            close="cancelar"
            :title="
                getTipoformulario == 'agregar'
                    ? 'Elaborar cotización'
                    : 'modificar cotización'
            "
            :active.sync="showVentana"
            ref="formulario"
        >
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
                                    <vs-input
                                        v-validate="'required'"
                                        ref="cliente"
                                        name="cliente"
                                        data-vv-as=" "
                                        type="text"
                                        class="w-full"
                                        focused
                                        placeholder="Ej. José Pérez"
                                        v-model="form.cliente"
                                        maxlength="100"
                                    />
                                    <span>{{ errors.first("cliente") }}</span>
                                    <span v-if="this.errores.cliente">{{
                                        errores.cliente[0]
                                    }}</span>
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Teléfono
                                        <span>(*)</span>
                                    </label>
                                    <vs-input
                                        v-validate="''"
                                        name="telefono"
                                        data-vv-as=" "
                                        type="text"
                                        class="w-full"
                                        placeholder="Ej. 6692145634"
                                        v-model="form.telefono"
                                        maxlength="100"
                                    />
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
                                    <vs-input
                                        v-validate="'email'"
                                        name="email"
                                        data-vv-as=" "
                                        type="email"
                                        class="w-full"
                                        placeholder="Ej. cliente@gmail.com"
                                        v-model="form.email"
                                        maxlength="100"
                                    />
                                    <span>{{ errors.first("email") }}</span>
                                    <span v-if="this.errores.email">{{
                                        errores.email[0]
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full xl:w-6/12 px-2 py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">
                            Datos de la Cotización
                        </div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-12/12 px-2 input-text">
                                    <label>
                                        Vendedor
                                        <span>(*)</span>
                                    </label>
                                    <v-select
                                        :options="vendedores"
                                        :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-validate:vendedor_computed.immediate="
                                            'required'
                                        "
                                        v-model="form.vendedor"
                                        class="w-full"
                                        name="vendedor"
                                        data-vv-as=" "
                                    >
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún
                                            Vendedor
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("vendedor") }}</span>
                                    <span
                                        v-if="this.errores['vendedor.value']"
                                        >{{
                                            errores["vendedor.value"][0]
                                        }}</span
                                    >
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label
                                        >Fecha de Cotizacion
                                        (Año-Mes-Dia)</label
                                    >
                                    <span>(*)</span>
                                    <flat-pickr
                                        name="fecha_cotizacion"
                                        data-vv-as=" "
                                        v-validate:fecha_cotizacion_validacion_computed="
                                            'required'
                                        "
                                        :config="configdateTimePicker"
                                        v-model="form.fecha_cotizacion"
                                        placeholder="Fecha de la Cotización"
                                        class="w-full"
                                    />
                                    <span>{{
                                        errors.first("fecha_cotizacion")
                                    }}</span>
                                    <span
                                        v-if="this.errores.fecha_cotizacion"
                                        >{{ errores.fecha_cotizacion[0] }}</span
                                    >
                                </div>
                                <div class="w-full md:w-6/12 px-2 input-text">
                                    <label>
                                        Periodo de Validéz
                                        <span>(*)</span>
                                    </label>
                                    <v-select
                                        :options="periodos_validez"
                                        v-validate:validez_validacion_computed.immediate="
                                            'required'
                                        "
                                        :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="form.validez"
                                        class="w-full"
                                        name=" validez"
                                        data-vv-as=" "
                                    >
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Periodo
                                            de Validéz
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("validez") }}</span>
                                    <span
                                        v-if="this.errores['validez.value']"
                                        >{{ errores["validez.value"][0] }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <vs-checkbox
                    ref="cotizaciones_predefinidas_b"
                    color="success"
                    v-model="form.cotizaciones_predefinidas_b"
                    class="ml-auto size-small font-medium color-copy pt-2 pr-2"
                    >INCLUIR COTIZACIONES PREDEFINIDAS</vs-checkbox
                >
                <div
                    class="w-full px-2 py-4"
                    v-show="form.cotizaciones_predefinidas_b"
                >
                    <div class="form-group py-6">
                        <div class="title-form-group">
                            Cotizaciones Predefinidas
                        </div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-6/12 px-2 py-2">
                                    <button
                                        class="w-full btn-icon-50"
                                        @click="openPaquetes('funeraria')"
                                    >
                                        ver planes de funeraria predefinidos
                                        <img src="@assets/images/folder.svg" />
                                    </button>
                                </div>
                                <div class="w-full lg:w-6/12 px-2 py-2">
                                    <button
                                        class="w-full btn-icon-50"
                                        @click="openPaquetes('cementerio')"
                                    >
                                        ver planes de cementerio predefinidos
                                        <img src="@assets/images/folder.svg" />
                                    </button>
                                </div>
                            </div>
                            <div class="w-full px-2 py-4">
                                <vs-table
                                    noDataText=""
                                    class="tabla-datos tabla-datos-no-data"
                                    :data="form.predefinidos"
                                >
                                    <template slot="header">
                                        <h3>
                                            Listado de Cotizaciones Agregadas
                                        </h3>
                                    </template>
                                    <template slot="thead">
                                        <vs-th>#</vs-th>
                                        <vs-th
                                            ><span class="px-2"
                                                >DESCRIPCIÓN</span
                                            ></vs-th
                                        >
                                        <vs-th
                                            ><span class="px-2"
                                                >COSTOS Y CONTENIDO</span
                                            ></vs-th
                                        >
                                        <vs-th
                                            ><span class="px-2"
                                                >QUITAR</span
                                            ></vs-th
                                        >
                                    </template>
                                    <template slot-scope="{ data }">
                                        <vs-tr
                                            :data="tr"
                                            :key="indextr"
                                            v-for="(tr, indextr) in data"
                                        >
                                            <vs-td>
                                                <span
                                                    class="font-semibold px-2"
                                                    >{{ indextr + 1 }}</span
                                                >
                                            </vs-td>
                                            <vs-td
                                                class="size-base padding-y-7"
                                            >
                                                <span class="px-2">{{
                                                    form.predefinidos[indextr]
                                                        .label
                                                }}</span>
                                            </vs-td>
                                            <vs-td>
                                                <span class="px-2"
                                                    ><img
                                                        class="cursor-pointer img-btn-14 mx-3"
                                                        src="@assets/images/folder.svg"
                                                        title=""
                                                        @click="
                                                            verCotizacion(
                                                                tr,
                                                                'ver'
                                                            )
                                                        "
                                                /></span>
                                            </vs-td>
                                            <vs-td>
                                                <span class="px-2">
                                                    <img
                                                        class="cursor-pointer img-btn-14 mx-3"
                                                        src="@assets/images/quitar.svg"
                                                        title=""
                                                        @click="
                                                            quitar_item(
                                                                indextr,
                                                                'predefinidos'
                                                            )
                                                        "
                                                /></span>
                                            </vs-td>
                                        </vs-tr>
                                    </template>
                                </vs-table>
                                <div
                                    class="size-small text-center pt-2 text-danger"
                                >
                                    <span v-if="this.errores['predefinidos']">{{
                                        errores["predefinidos"][0]
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full px-2 py-4">
                    <div class="form-group py-6">
                        <div class="title-form-group">
                            Cotización Personalizada
                        </div>
                        <div class="form-group-content">
                            <div
                                class="flex flex-wrap justify-between items-end px-2 w-full py-2"
                            >
                                <div
                                    class="hidden lg:flex flex-nonwrap justify-between w-full  lg:w-6/12 xl:w-5/12"
                                >
                                    <img
                                        class="img-btn-20 mr-2 mt-5"
                                        src="@assets/images/barcode.svg"
                                    />
                                    <div class="w-11/12 input-text">
                                        <label
                                            >Agregar Concepto por Clave o código
                                            de barras</label
                                        >
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
                                        class="cursor-pointer img-btn-20 ml-2 mt-5"
                                        src="@assets/images/searcharticulo.svg"
                                        title="Buscador de artículos y servicios"
                                        @click="openBuscadorArticulos = true"
                                    />
                                </div>
                                <button
                                    class="w-full hidden lg:flex lg:w-4/12 xl:w-3/12 btn-icon-50 warning-btn my-1"
                                    v-if="this.form.conceptos.length > 0"
                                    @click="limpiar_conceptos()"
                                >
                                    Limpiar lista personalizada
                                    <img
                                        class=""
                                        src="@assets/images/quitar.svg"
                                    />
                                </button>
                                <button
                                    class="lg:hidden w-full btn-icon-50"
                                    @click="openBuscadorArticulos = true"
                                >
                                    Abrir conceptos del inventario
                                    <img src="@assets/images/folder.svg" />
                                </button>
                            </div>

                            <div class="w-full px-2 py-4">
                                <vs-table
                                    noDataText=""
                                    class="tabla-datos tabla-datos-no-data"
                                    :data="form.conceptos"
                                    ref="conceptos"
                                >
                                    <template slot="header">
                                        <h3>Listado de Conceptos</h3>
                                    </template>
                                    <template slot="thead">
                                        <vs-th class="w-auto">#</vs-th>
                                        <vs-th
                                            ><span class="px-2"
                                                >DESCRIPCIÓN</span
                                            ></vs-th
                                        >
                                        <vs-th
                                            ><span class="px-2"
                                                >CANT.</span
                                            ></vs-th
                                        >
                                        <vs-th
                                            ><span class="px-2"
                                                >COSTO</span
                                            ></vs-th
                                        >
                                        <vs-th
                                            ><span class="px-2"
                                                >DESCUENTO</span
                                            ></vs-th
                                        >
                                        <vs-th
                                            ><span class="px-2"
                                                >COSTO CON DESC.</span
                                            ></vs-th
                                        >
                                        <vs-th
                                            ><span class="px-2"
                                                >IMPORTE</span
                                            ></vs-th
                                        >
                                        <vs-th
                                            ><span class="px-2"
                                                >QUITAR</span
                                            ></vs-th
                                        >
                                    </template>
                                    <template slot-scope="{ data }">
                                        <vs-tr
                                            :data="tr"
                                            :key="indextr"
                                            v-for="(tr, indextr) in data"
                                        >
                                            <vs-td class="">
                                                <span class="font-semibold">{{
                                                    indextr + 1
                                                }}</span>
                                            </vs-td>
                                            <vs-td class=" padding-y-7 w-8/12">
                                                <vs-input
                                                    :ref="
                                                        'descripcion' + indextr
                                                    "
                                                    :name="
                                                        'descripcion' + indextr
                                                    "
                                                    data-vv-as=" "
                                                    data-vv-validate-on="blur"
                                                    v-validate.disable="
                                                        'required'
                                                    "
                                                    class="px-2 mr-auto ml-auto w-full input-cantidad"
                                                    maxlength="150"
                                                    v-model="
                                                        form.conceptos[indextr]
                                                            .descripcion
                                                    "
                                                />
                                                <div
                                                    class="size-smallest text-danger"
                                                >
                                                    <span class="">
                                                        {{
                                                            errors.first(
                                                                "descripcion" +
                                                                    indextr
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="">
                                                <vs-input
                                                    :name="'cantidad' + indextr"
                                                    data-vv-as=" "
                                                    data-vv-validate-on="blur"
                                                    v-validate.disable="
                                                        'required|integer|min_value:1'
                                                    "
                                                    class="mr-auto ml-auto input-cantidad px-2 w-full"
                                                    maxlength="4"
                                                    v-model="
                                                        form.conceptos[indextr]
                                                            .cantidad
                                                    "
                                                />
                                                <div
                                                    class="size-smallest text-danger"
                                                >
                                                    <span class="">
                                                        {{
                                                            errors.first(
                                                                "cantidad" +
                                                                    indextr
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="">
                                                <vs-input
                                                    :name="
                                                        'costo_neto_normal' +
                                                            indextr
                                                    "
                                                    data-vv-as=" "
                                                    data-vv-validate-on="blur"
                                                    v-validate.disable="
                                                        'required|decimal:2|min_value:0'
                                                    "
                                                    class="mr-auto ml-auto input-cantidad px-2 w-full"
                                                    maxlength="10"
                                                    v-model="
                                                        form.conceptos[indextr]
                                                            .costo_neto_normal
                                                    "
                                                />
                                                <div
                                                    class="size-smallest text-danger"
                                                >
                                                    <span class="">
                                                        {{
                                                            errors.first(
                                                                "costo_neto_normal" +
                                                                    indextr
                                                            )
                                                                ? "$ Costo Neto."
                                                                : ""
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
                                                        form.conceptos[indextr]
                                                            .descuento_b
                                                    "
                                                    @change="
                                                        validarCostoNetoDescuento(
                                                            indextr
                                                        )
                                                    "
                                                >
                                                    <span slot="on">SI</span>
                                                    <span slot="off">NO</span>
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
                                                    v-validate.disable="
                                                        tr.descuento_b == 1
                                                            ? 'required|decimal:2|min_value:' +
                                                              0 +
                                                              '|max_value:' +
                                                              form.conceptos[
                                                                  indextr
                                                              ]
                                                                  .costo_neto_normal
                                                            : ''
                                                    "
                                                    class="mr-auto ml-auto input-cantidad px-2 w-full"
                                                    maxlength="10"
                                                    v-model="
                                                        form.conceptos[indextr]
                                                            .costo_neto_descuento
                                                    "
                                                    :disabled="
                                                        form.conceptos[indextr]
                                                            .descuento_b == 0
                                                    "
                                                />
                                                <div
                                                    class="size-smallest text-danger"
                                                >
                                                    <span class="">
                                                        {{
                                                            errors.first(
                                                                "costo_neto_descuento" +
                                                                    indextr
                                                            )
                                                                ? "$ Costo Neto con Descuento."
                                                                : ""
                                                        }}
                                                    </span>
                                                </div>
                                            </vs-td>
                                            <vs-td class="">
                                                <div
                                                    v-if="
                                                        form.conceptos[indextr]
                                                            .descuento_b == 1
                                                    "
                                                >
                                                    $
                                                    {{
                                                        (form.conceptos[indextr]
                                                            .costo_neto_descuento *
                                                            form.conceptos[
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
                                                        (form.conceptos[indextr]
                                                            .costo_neto_normal *
                                                            form.conceptos[
                                                                indextr
                                                            ].cantidad)
                                                            | numFormat(
                                                                "0,000.00"
                                                            )
                                                    }}
                                                </div>
                                            </vs-td>

                                            <vs-td class="">
                                                <div
                                                    class="flex justify-center"
                                                >
                                                    <img
                                                        class="img-btn-14 mx-3"
                                                        src="@assets/images/quitar.svg"
                                                        title="Quitar concepto de la lista."
                                                        @click="
                                                            quitar_item(
                                                                indextr,
                                                                'conceptos'
                                                            )
                                                        "
                                                    />
                                                </div>
                                            </vs-td>
                                        </vs-tr>
                                    </template>
                                </vs-table>
                                <div
                                    class="size-small text-center pt-2 text-danger"
                                >
                                    <span v-if="this.errores['conceptos']">{{
                                        errores["conceptos"][0]
                                    }}</span>
                                </div>
                                <button
                                    class="w-full btn-icon-50 my-4"
                                    @click="agregar_manual"
                                >
                                    AGREGAR CONCEPTO MANUALMENTE
                                    <img
                                        src="@assets/images/plus-success.svg"
                                    />
                                </button>
                            </div>
                            <div class="flex flex-wrap px-2 py-2">
                                <div
                                    class="w-full lg:w-7/12 xl:w-8/12 lg:pr-2 py-2"
                                >
                                    <NotasComponent
                                        :value="form.nota"
                                        @input="
                                            val => {
                                                this.form.nota = val;
                                            }
                                        "
                                    />
                                </div>
                                <div
                                    class="w-full lg:w-5/12 xl:w-4/12 lg:pl-2 pt-6 lg:py-2"
                                >
                                    <div class="form-group h-full mt-0">
                                        <div class="title-form-group">
                                            Finalizar Cotización
                                        </div>
                                        <div
                                            class="form-group-content h-full content-center md:content-end"
                                        >
                                            <div
                                                class="flex flex-wrap h-full content-between"
                                            >
                                                <div class="w-full">
                                                    <div
                                                        class="w-full justify-between px-2 flex flex-wrap h2 py-2 lg:py-0"
                                                    >
                                                        <span
                                                            >Total a Pagar</span
                                                        >
                                                        <span
                                                            :class="[
                                                                totalCotizacion <=
                                                                0
                                                                    ? 'text-danger'
                                                                    : ''
                                                            ]"
                                                            >$
                                                            {{
                                                                totalCotizacion
                                                                    | numFormat(
                                                                        "0,000.00"
                                                                    )
                                                            }}</span
                                                        >
                                                    </div>
                                                    <div
                                                        :class="[
                                                            'w-full justify-between px-2 flex flex-wrap lg:py-1',
                                                            descuentoTotal > 0
                                                                ? 'text-danger h5'
                                                                : 'size-base'
                                                        ]"
                                                    >
                                                        <span
                                                            >Descuento
                                                            Aplicado</span
                                                        >
                                                        <span
                                                            >$
                                                            {{
                                                                descuentoTotal
                                                                    | numFormat(
                                                                        "0,000.00"
                                                                    )
                                                            }}</span
                                                        >
                                                    </div>
                                                </div>
                                                <div
                                                    class="w-full lg:w-12/12 px-2 input-text large-size lg:py-0"
                                                >
                                                    <label>
                                                        MODALIDAD DE PAGOS
                                                        <span>(*)</span>
                                                    </label>
                                                    <v-select
                                                        :options="
                                                            modalidades_pago
                                                        "
                                                        :clearable="false"
                                                        :dir="
                                                            $vs.rtl
                                                                ? 'rtl'
                                                                : 'ltr'
                                                        "
                                                        v-model="
                                                            form.modalidad_pago
                                                        "
                                                        v-validate:modalidad_computed.immediate="
                                                            'required'
                                                        "
                                                        class="w-full"
                                                        name="modalidad_pago"
                                                        data-vv-as=" "
                                                    >
                                                        <div slot="no-options">
                                                            No Se Ha
                                                            Seleccionado Ninguna
                                                            Opción
                                                        </div>
                                                    </v-select>
                                                    <span>{{
                                                        errors.first(
                                                            "modalidad_pago"
                                                        )
                                                    }}</span>
                                                    <span
                                                        v-if="
                                                            this.errores[
                                                                'modalidad_pago.value'
                                                            ]
                                                        "
                                                        >{{
                                                            errores[
                                                                "modalidad_pago.value"
                                                            ][0]
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="w-full md:w-6/12 px-2 input-text large-size input-cantidad py-2 lg:py-0"
                                                >
                                                    <label>
                                                        $
                                                        {{
                                                            pago_inicial_validacion_computed.label
                                                        }}
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input
                                                        ref="pago_inicial"
                                                        @change="
                                                            changePagoInicial(
                                                                $event
                                                            )
                                                        "
                                                        v-validate="
                                                            pago_inicial_validacion_computed.rules
                                                        "
                                                        :disabled="
                                                            form.modalidad_pago
                                                                .value == 1
                                                        "
                                                        name="pago_inicial"
                                                        data-vv-as=" "
                                                        data-vv-validate-on="change"
                                                        type="text"
                                                        class="w-full"
                                                        placeholder=""
                                                        v-model="
                                                            form.pago_inicial
                                                        "
                                                        maxlength="12"
                                                    />
                                                    <span
                                                        v-show="
                                                            form.modalidad_pago
                                                                .value > 1
                                                        "
                                                        >{{
                                                            errors.first(
                                                                "pago_inicial"
                                                            )
                                                                ? pago_inicial_validacion_computed.errorMessage
                                                                : ""
                                                        }}</span
                                                    >
                                                    <span
                                                        v-if="
                                                            this.errores
                                                                .pago_inicial
                                                        "
                                                        >{{
                                                            errores
                                                                .pago_inicial[0]
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="w-full md:w-6/12 px-2 input-text large-size input-cantidad py-2 lg:py-0"
                                                >
                                                    <label>
                                                        %
                                                        {{
                                                            pago_inicial_validacion_computed.label
                                                        }}
                                                        <span>(*)</span>
                                                    </label>
                                                    <vs-input
                                                        ref="pago_inicial_porcentaje"
                                                        @change="
                                                            changePorcentaje(
                                                                $event
                                                            )
                                                        "
                                                        :disabled="
                                                            form.modalidad_pago
                                                                .value == 1
                                                        "
                                                        name="pago_inicial_porcentaje"
                                                        data-vv-as=" "
                                                        v-validate.disable="
                                                            'required|decimal:2|min_value:' +
                                                                0 +
                                                                '|max_value:100'
                                                        "
                                                        data-vv-validate-on="change"
                                                        type="text"
                                                        class="w-full "
                                                        v-model="
                                                            form.pago_inicial_porcentaje
                                                        "
                                                        maxlength="6"
                                                    />
                                                    <span
                                                        v-show="
                                                            form.modalidad_pago
                                                                .value > 1
                                                        "
                                                        >{{
                                                            errors.first(
                                                                "pago_inicial_porcentaje"
                                                            )
                                                                ? "Ingrese un valor entre el 1 y el 100 %"
                                                                : ""
                                                        }}</span
                                                    >
                                                    <span
                                                        v-if="
                                                            this.errores
                                                                .pago_inicial_porcentaje
                                                        "
                                                        >{{
                                                            errores
                                                                .pago_inicial_porcentaje[0]
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    :class="[
                                                        'w-full text-center px-2 py-4 lg:py-0'
                                                    ]"
                                                >
                                                    <span>{{
                                                        descripcion_pagos
                                                    }}</span>
                                                </div>
                                                <vs-button
                                                    @click="acceptAlert()"
                                                    class="w-full"
                                                    color="success"
                                                >
                                                    <span
                                                        v-if="
                                                            getTipoformulario ==
                                                                'agregar'
                                                        "
                                                        >Guardar
                                                        Cotización</span
                                                    >
                                                    <span v-else
                                                        >Modificar
                                                        Cotización</span
                                                    >
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
        <ArticulosBuscador
            :z_index="'z-index56k'"
            :show="openBuscadorArticulos"
            @closeBuscador="openBuscadorArticulos = false"
            @LoteSeleccionado="LoteSeleccionado"
        ></ArticulosBuscador>
        <ConfirmarDanger
            :z_index="'z-index58k'"
            :show="openConfirmarSinPassword"
            :callback-on-success="callBackConfirmar"
            @closeVerificar="openConfirmarSinPassword = false"
            :accion="accionConfirmarSinPassword"
            :confirmarButton="botonConfirmarSinPassword"
        ></ConfirmarDanger>
        <ConfirmarAceptar
            :z_index="'z-index58k'"
            :show="openConfirmarAceptar"
            :callback-on-success="callBackConfirmarAceptar"
            @closeVerificar="openConfirmarAceptar = false"
            :accion="
                'He revisado la información y quiero guardar la cotización'
            "
            :confirmarButton="'Guardar Cotización'"
        >
        </ConfirmarAceptar>
        <Paquetes
            :show="ver_cotizaciones"
            :tipo_cotizacion="tipo_cotizacion"
            @closeCotizaciones="ver_cotizaciones = false"
            @agregarCotizacion="agregarCotizacion"
            :cotizacionVer="cotizacionVer"
        >
        </Paquetes>
        <Password
            :z_index="'z-index56k'"
            :show="openPassword"
            :callback-on-success="callback"
            @closeVerificar="closePassword"
            :accion="'Modificar Cotización'"
        ></Password>
    </div>
</template>
<script>
import { VueEditor, Quill } from "vue2-editor";
import { ImageDrop } from "quill-image-drop-module";
import ImageResize from "quill-image-resize-vue";
Quill.register("modules/imageDrop", ImageDrop);
Quill.register("modules/imageResize", ImageResize);
//componente de password
import Password from "@pages/confirmar_password";
import ConfirmarDanger from "@pages/ConfirmarDanger";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import funeraria from "@services/funeraria";
import cotizaciones from "@services/cotizaciones";
import cementerio from "@services/cementerio";
import vSelect from "vue-select";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ArticulosBuscador from "@pages/funeraria/servicios_funerarios/searcher_articulos.vue";
import Paquetes from "@pages/cotizaciones/Paquetes.vue";
import { configdateTimePicker } from "@/VariablesGlobales";
import functions from "@/functions";
import NotasComponent from "@pages/NotasComponent";
export default {
    components: {
        "v-select": vSelect,
        flatPickr,
        ArticulosBuscador,
        VueEditor,
        Quill,
        ConfirmarDanger,
        Paquetes,
        ConfirmarAceptar,
        Password,
        NotasComponent
    },
    props: {
        show: {
            type: Boolean,
            required: true
        },
        tipo: {
            type: String,
            required: true
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index54k"
        },
        id_cotizacion: {
            type: Number,
            required: false,
            default: 0
        }
    },
    watch: {
        show: function(newValue, oldValue) {
            if (newValue == true) {
                this.limpiarVentana();
                this.$nextTick(() => {
                    this.$refs["cliente"].$el.querySelector("input").focus();
                });
                (async () => {
                    await this.get_vendedores();
                    if (this.getTipoformulario == "modificar") {
                        //es modificar
                        this.form.id_cotizacion = this.get_cotizacion_id;
                        /**se cargan los datos al formulario */
                        await this.consultar_cotizacion();
                    }
                    this.watchers = true;
                })();
            } else {
                /**acciones al cerrar el formulario */
                //Lmpiamos el form
                this.watchers = false;
            }
        },
        "form.modalidad_pago": function(newValue, oldValue) {
            if (this.watchers)
                //this.form.pago_inicial = ''
                this.$nextTick(() => {
                    this.$refs["pago_inicial"].$el
                        .querySelector("input")
                        .focus();
                });
        }
    },
    computed: {
        get_cotizacion_id: {
            get() {
                return this.id_cotizacion;
            },
            set(newValue) {
                return newValue;
            }
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
        vendedor_computed: function() {
            return this.form.vendedor.value;
        },
        modalidad_computed: function() {
            return this.form.modalidad_pago.value;
        },
        fecha_cotizacion_validacion_computed: function() {
            return this.form.fecha_cotizacion;
        },
        validez_validacion_computed: function() {
            return this.form.validez.value;
        },
        totalCotizacion: function() {
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
                if (this.getTipoformulario == "agregar") {
                    this.form.pago_inicial = "";
                    this.form.pago_inicial_porcentaje = "";
                }
            }
            return functions.parseToDecimal(total);
        },
        descuentoTotal: function() {
            let total = 0;
            this.form.conceptos.forEach((element, index) => {
                if (this.form.conceptos[index].descuento_b == 1) {
                    total +=
                        (this.form.conceptos[index].costo_neto_normal -
                            this.form.conceptos[index].costo_neto_descuento) *
                        this.form.conceptos[index].cantidad;
                }
            });
            return (total = functions.parseToDecimal(total));
        },
        pago_inicial_validacion_computed: function() {
            let montoTotal =
                functions.formatCurrency(this.totalCotizacion) + " MXN.";
            if (this.form.modalidad_pago.value > 1) {
                //será a futuro
                return {
                    rules:
                        "required|decimal:2|between:" +
                        0 +
                        "," +
                        this.totalCotizacion,
                    errorMessage:
                        "Cantidad mínima $ 0.00 y máxima " + montoTotal,
                    label: "Pago Inicial"
                };
            } else {
                //uso inmediato
                return {
                    rules:
                        "required|decimal:2|min_value:" +
                        this.totalCotizacion +
                        "|min_value:" +
                        this.totalCotizacion,
                    errorMessage: "El pago debe ser de : $" + montoTotal,
                    label: "Pago Único"
                };
            }
        },
        descripcion_pagos: function() {
            if (this.totalCotizacion <= 0)
                return "Total de la cotización $0.00 MXN.";
            let pago_inicial = functions.parseToDecimal(this.form.pago_inicial);
            if (pago_inicial <= 0) return "Ingrese el Pago Inicial";
            let modalidad = this.form.modalidad_pago.value;
            if (modalidad == 1)
                return (
                    "Pago Único de $" +
                    functions.formatCurrency(pago_inicial) +
                    " MXN."
                );
            else {
                if (pago_inicial == this.totalCotizacion)
                    return (
                        "Pago Único de $" +
                        functions.formatCurrency(pago_inicial) +
                        " MXN."
                    );
            }
            //si la modalidad es en abonos
            let abonos = (this.totalCotizacion - pago_inicial) / modalidad;
            if (abonos < 0) {
                return "Verifique su pago inicial.";
            }
            if (modalidad == 2) {
                //es a dos pagos
                return (
                    "Pago Inicial de $" +
                    functions.formatCurrency(pago_inicial) +
                    " MXN, y 2 pagos de $" +
                    functions.formatCurrency(abonos) +
                    " MXN."
                );
            } else {
                //es a mas pagos
                let decimales = abonos - Math.trunc(abonos);
                if (decimales > 0) {
                    //se hacen los diferentes pagos para poner todas las decimales en el primer pago
                    let abono_1 = Math.trunc(abonos) + modalidad * decimales; //1er abono con todo y decimales
                    modalidad -= 1;
                    abonos = Math.trunc(abonos);
                    if (abonos > 0)
                        return (
                            "Pago Inicial de $" +
                            functions.formatCurrency(pago_inicial) +
                            " MXN. Primer pago de  $" +
                            functions.formatCurrency(abono_1) +
                            " MXN y " +
                            modalidad +
                            " pagos de $" +
                            functions.formatCurrency(abonos) +
                            " MXN."
                        );
                    else
                        return (
                            "Pago Inicial de $" +
                            functions.formatCurrency(pago_inicial) +
                            " MXN. y un segundo pago de  $" +
                            functions.formatCurrency(abono_1) +
                            " MXN."
                        );
                } else {
                    //no hay decimales
                    return (
                        "Pago Inicial de $" +
                        functions.formatCurrency(pago_inicial) +
                        " MXN, y " +
                        modalidad +
                        " pagos de $" +
                        functions.formatCurrency(abonos) +
                        " MXN."
                    );
                }
            }
        }
    },
    data() {
        return {
            watchers: false,
            openConfirmarAceptar: false,
            callBackConfirmarAceptar: Function,
            customToolbar: [
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                ["bold", "italic", "underline", "strike"], // toggled buttons
                [
                    { align: "" },
                    { align: "center" },
                    { align: "right" },
                    { align: "justify" }
                ],
                //["blockquote", "code-block"],
                [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
                [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
                [{ color: [] }, { background: [] }], // dropdown with defaults from theme
                ["link", "image", "video"],
                ["clean"] // remove formatting button
            ],
            editorSettings: {
                modules: {
                    imageDrop: true,
                    imageResize: {}
                }
            },
            vendedores: [],
            modalidades_pago: [],
            periodos_validez: [
                { label: "Indefinido", value: "1" },
                { label: "Uso Inmediato (1 Día)", value: "2" },
                { label: "1 Semana", value: "3" },
                { label: "2 Semanas", value: "4" },
                { label: "3 Semanas", value: "5" },
                { label: "1 Mes", value: "6" }
            ],
            form: {
                cliente: "",
                telefono: "",
                email: "",
                vendedor: null,
                fecha_cotizacion: "",
                validez: { label: "Indefinido", value: "1" },
                cotizaciones_predefinidas_b: true,
                predefinidos: [],
                conceptos: [],
                nota: "",
                modalidad_pago: { label: "Pago Único", value: "1" },
                pago_inicial: "",
                pago_inicial_porcentaje: "",
                descripcion_pagos: "",
                total: "",
                descuento: "",
                id_cotizacion: ""
            },
            serverOptions: {
                numero_control: ""
            },
            configdateTimePicker: configdateTimePicker,
            openBuscadorArticulos: false,
            errores: [],
            openConfirmarSinPassword: false,
            botonConfirmarSinPassword: "",
            accionConfirmarSinPassword: "",
            callBackConfirmar: Function,
            errores: [],
            ver_cotizaciones: false,
            cotizacionVer: {},
            openPassword: false,
            callback: Function
        };
    },
    methods: {
        async consultar_cotizacion() {
            try {
                this.$vs.loading();
                let res = await cotizaciones.get_cotizaciones(
                    null,
                    false,
                    this.form.id_cotizacion
                );
                if (res.data.length > 0) {
                    res = res.data[0];
                    //veo si se puede modificar
                    //solo si es activa
                    //cargo informacion
                    this.form.cliente = res.cliente_nombre;
                    this.form.telefono = res.cliente_telefono;
                    this.form.email = res.cliente_email;
                    this.form.vendedor = {
                        value: res.vendedor.id,
                        label: res.vendedor.nombre
                    };
                    let partes_fecha = res.fecha.split("-");
                    //yyyy-mm-dd
                    this.form.fecha_cotizacion = new Date(
                        partes_fecha[0],
                        partes_fecha[1] - 1,
                        partes_fecha[2]
                    );
                    this.periodos_validez.forEach(element => {
                        if (element.value == res.periodo_validez_id) {
                            this.form.validez = element;
                            return;
                        }
                    });
                    //CARGO LOS PREDEFINIDOS
                    this.form.cotizaciones_predefinidas_b = res.predefinidas_b;
                    if (res.predefinidas_b == 1) {
                        //si tiene predefinidos los cargo
                        this.form.predefinidos = res.predefinidos;
                    }
                    //CARGO LOS CONCEPTOS PERSONALIZADOS
                    if (res.conceptos.length > 0) {
                        //si tiene personalizados los cargo
                        this.form.conceptos = res.conceptos;
                    }
                    this.form.nota = res.nota;
                    this.form.modalidad_pago = this.modalidades_pago[
                        res.modalidad - 1
                    ];
                    this.form.pago_inicial = res.pago_inicial;
                    this.changePagoInicial(null);
                } else {
                    //no se encontro la info
                    this.$vs.notify({
                        title: "Modificar Cotización",
                        text:
                            "No se ha encontrado esta cotización en el sistema.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000
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
                            text:
                                "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000
                        });
                    }
                }
            }
        },
        validarCostoNetoDescuento(index) {
            if (this.form.conceptos[index].descuento_b == 0) {
                if (
                    isNaN(this.form.conceptos[index].costo_neto_descuento) ||
                    this.form.conceptos[index].costo_neto_descuento == ""
                ) {
                    this.form.conceptos[index].costo_neto_descuento = 0;
                }
            }
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
                        this.form.descripcion_pagos = this.descripcion_pagos;
                        this.form.total = this.totalCotizacion;
                        this.form.descuento = this.descuentoTotal;
                        //AL LLEGAR AQUI SE SABE QUE EL FORMULARIO PASO LA VALIDACION
                        (async () => {
                            if (this.getTipoformulario == "agregar") {
                                this.callBackConfirmarAceptar = await this
                                    .control_cotizaciones;
                                this.openConfirmarAceptar = true;
                            } else {
                                this.callback = await this.control_cotizaciones;
                                this.openPassword = true;
                            }
                        })();
                    }
                })
                .catch(() => {});
        },
        async control_cotizaciones() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await cotizaciones.control_cotizaciones(
                    this.form,
                    this.getTipoformulario
                );
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Cotizaciones",
                        text:
                            this.getTipoformulario == "agregar"
                                ? "Se ha registrado la cotización correctamente."
                                : "Se ha modificado la cotización correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000
                    });
                    this.$emit("ConsultarCotizacion", {
                        id: res.data,
                        cliente_email: this.form.email,
                        cliente_nombre: this.form.cliente
                    });
                } else {
                    this.$vs.notify({
                        title: "Cotizaciones",
                        text:
                            "Error al registrar la cotización, por favor reintente.",
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
                            title: "Cotizaciones",
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
                            title: "Cotizaciones",
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
        //get vendedores
        async get_vendedores() {
            try {
                this.$vs.loading();
                let res = await cementerio.get_vendedores();
                //le agrego todos los usuarios vendedores
                this.vendedores = [];
                this.vendedores.push({ label: "Seleccione 1", value: "" });
                this.form.vendedor = this.vendedores[0];
                res.data.forEach(element => {
                    this.vendedores.push({
                        label: element.nombre,
                        value: element.id
                    });
                });
                this.$vs.loading.close();
            } catch (error) {
                /**error al cargar vendedores */
                this.$vs.notify({
                    title: "Error",
                    text:
                        "Ha ocurrido un error al tratar de cargar el catálogo de vendedores, por favor reintente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "9000"
                });
                this.$vs.loading.close();
                this.cerrarVentana();
            }
        },
        changePagoInicial(event) {
            let pago_inicial = functions.parseToDecimal(this.form.pago_inicial);
            if (pago_inicial > this.totalCotizacion || pago_inicial < 0) {
                this.form.pago_inicial = "";
                this.$nextTick(() => {
                    this.$refs["pago_inicial"].$el
                        .querySelector("input")
                        .focus();
                });
            } else if (this.form.modalidad_pago.value != 1) {
                this.form.pago_inicial_porcentaje = functions.parseToDecimal(
                    (pago_inicial * 100) / this.totalCotizacion
                );
            }
        },
        changePorcentaje(event) {
            let pago_inicial_porcentaje = functions.parseToDecimal(
                this.form.pago_inicial_porcentaje
            );
            if (pago_inicial_porcentaje > 100) {
                this.form.pago_inicial_porcentaje = "";
                this.$nextTick(() => {
                    this.$refs["pago_inicial_porcentaje"].$el
                        .querySelector("input")
                        .focus();
                });
            } else if (this.form.modalidad_pago.value != 1) {
                this.form.pago_inicial = functions.parseToDecimal(
                    (pago_inicial_porcentaje * this.totalCotizacion) / 100
                );
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
                .then(res => {
                    if (res.data.length > 0) {
                        let datos = res.data[0];
                        /**agrego el concepto al listado del contrato */
                        this.form.conceptos.push({
                            descripcion: datos.descripcion,
                            cantidad: 1,
                            costo_neto_normal: datos.precio_venta,
                            descuento_b: 0,
                            costo_neto_descuento: 0,
                            importe: 0
                        });
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
        LoteSeleccionado(datos) {
            this.form.conceptos.push(datos);
        },
        agregar_manual() {
            this.form.conceptos.push({
                descripcion: "",
                cantidad: 1,
                costo_neto_normal: "",
                descuento_b: 0,
                costo_neto_descuento: 0,
                importe: ""
            });
            this.$nextTick(() => {
                let index = this.form.conceptos.length - 1;
                this.$refs["descripcion" + index][0].$el
                    .querySelector("input")
                    .focus();
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
            } else {
                this.accionConfirmarSinPassword =
                    "Se quitará la cotización de " +
                    this.form.predefinidos[index_item].label +
                    " de la lista.";
                this.callBackConfirmar = this.quitar_predefinido_callback;
            }
        },
        //remover beneficiario callback quita del array al beneficiario seleccionado
        quitar_item_callback() {
            this.form.conceptos.splice(this.form.index_item, 1);
        },
        quitar_predefinido_callback() {
            this.form.predefinidos.splice(this.form.index_item, 1);
        },
        limpiar_conceptos() {
            if (this.form.conceptos.length == 0) return;
            this.botonConfirmarSinPassword = "Quitar Conceptos";
            this.accionConfirmarSinPassword =
                "Todos los conceptos capturados serán removidos.";
            this.callBackConfirmar = this.eliminar_conceptos_callback;
            this.openConfirmarSinPassword = true;
        },
        eliminar_conceptos_callback() {
            this.form.conceptos = [];
        },
        cancelar() {
            this.botonConfirmarSinPassword = "Salir y limpiar";
            this.accionConfirmarSinPassword =
                "Esta acción limpiará los datos que capturó en el formulario.";
            this.openConfirmarSinPassword = true;
            this.callBackConfirmar = this.cerrarVentana;
        },
        cerrarVentana() {
            this.openConfirmarSinPassword = false;
            this.limpiarVentana();
            this.$emit("closeVentana");
        },
        limpiarVentana() {
            this.form.cliente = "";
            this.form.telefono = "";
            this.form.email = "";
            this.form.vendedor = this.vendedores[0];
            this.form.fecha_cotizacion = "";
            this.form.validez = this.periodos_validez[0];
            this.form.cotizaciones_predefinidas_b = false;
            this.form.predefinidos = [];
            this.serverOptions.numero_control = "";
            this.form.conceptos = [];
            this.form.nota = "";
            this.form.pago_inicial = "0.00";
            this.form.pago_inicial_porcentaje = "0.00";
            this.form.modalidad_pago = this.modalidades_pago[0];
            this.$validator.reset();
        },
        openPaquetes(tipo = "") {
            this.tipo_cotizacion = tipo;
            this.ver_cotizaciones = true;
        },
        agregarCotizacion(cotizacion) {
            this.form.predefinidos.push(cotizacion);
        },
        verCotizacion(cotizacion) {
            this.tipo_cotizacion = "ver";
            this.cotizacionVer = cotizacion;
            this.ver_cotizaciones = true;
        },
        closePassword() {
            this.openPassword = false;
        }
    },
    created() {},
    updated() {},
    mounted() {
        this.modalidades_pago.push({ label: "Pago Único", value: "1" });
        this.modalidades_pago.push({ label: "2 Mensualidades", value: "2" });
        for (let index = 3; index <= 24; index++) {
            this.modalidades_pago.push({
                label: +index + " Mensualidades",
                value: index
            });
        }
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
            this.cancelar();
        };
    }
};
</script>

<style scoped></style>
