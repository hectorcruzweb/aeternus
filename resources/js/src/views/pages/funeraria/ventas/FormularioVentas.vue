<template>
    <div class="centerx">
        <vs-popup :class="['forms-popup', z_index]" fullscreen close="cancelar" :title="getTipoformulario == 'modificar'
            ? 'Modificar Venta de Plan Funerario a Futuro'
            : 'Registrar Venta de Plan Funerario a Futuro'
            " :active.sync="showVentana" ref="formulario">
            <!--inicio venta-->
            <div class="flex flex-wrap">
                <div class="w-full lg:w-7/12 px-4">
                    <!--Contenido del plan-->
                    <div class="form-group">
                        <div class="title-form-group">Contenido del Plan Funerario</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full py-6" v-if="verLista">
                                    <vs-table class="tabla-datos" :data="datos"
                                        noDataText="No se han agregado Artículos ni Servicios">
                                        <template slot="header">
                                            <h3>Servicios y Artículos que Incluye el Paquete</h3>
                                        </template>
                                        <template slot="thead">
                                            <vs-th>#</vs-th>
                                            <vs-th>Artículo/Servicio</vs-th>
                                            <vs-th>Aplicar en</vs-th>
                                        </template>
                                        <template slot-scope="{ data }">
                                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                                <vs-td>
                                                    <div class="px-2 font-bold">
                                                        {{ alfabeto[indextr] }})
                                                    </div>
                                                </vs-td>
                                                <vs-td>
                                                    <div class="px-2">{{ tr.concepto }}</div>
                                                </vs-td>
                                                <vs-td>
                                                    <div class="px-2 font-medium capitalize">
                                                        {{ tr.aplicar }}
                                                    </div>
                                                </vs-td>
                                            </vs-tr>
                                        </template>
                                    </vs-table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--fin del contenido del plan -->
                </div>

                <div class="w-full lg:w-5/12 px-4">
                    <!-- Seleccionar el plan -->
                    <div class="form-group">
                        <div class="title-form-group">Plan Funerario y Cliente</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full input-text px-2">
                                    <label>
                                        Seleccione un Plan Funerario
                                        <span>(*)</span>
                                    </label>
                                    <v-select :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " :options="planes_funerarios" :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="form.plan_funerario" class="w-full"
                                        v-validate:plan_funerario_validacion_computed.immediate="'required'
                                            " name="plan_validacion" data-vv-as=" ">
                                        <div slot="no-options">
                                            No Se Ha Seleccionado Ningún Plan
                                        </div>
                                    </v-select>
                                    <span>{{ errors.first("plan_validacion") }}</span>
                                    <span v-if="this.errores['plan_funerario.value']">{{
                                        errores["plan_funerario.value"][0]
                                    }}</span>
                                </div>
                                <div class="w-full input-text px-2">
                                    <label>
                                        Tipo de Venta
                                        <span>(*)</span>
                                    </label>
                                    <v-select :disabled="ModificarVenta" :options="ventasAntiguedad" :clearable="false"
                                        :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="form.ventaAntiguedad" class="w-full"
                                        v-validate:antiguedad_validacion_computed.immediate="'required'
                                            " name="antiguedad_validacion" data-vv-as=" ">
                                        <div slot="no-options">Seleccione 1</div>
                                    </v-select>
                                    <span>
                                        {{ errors.first("antiguedad_validacion") }}
                                    </span>
                                    <span v-if="this.errores['ventaAntiguedad.value']">{{
                                        errores["ventaAntiguedad.value"][0]
                                    }}</span>
                                </div>
                                <div class="w-full px-2">
                                    <div class="py-3 size-small">
                                        <span class="color-danger-900 font-medium">Ojo:</span>
                                        Debe seleccionar la modalidad de la venta que se esté
                                        registrando en caso de que haya sido realizada fuera del
                                        control del sistema, ya que ese tipo de ventas cuenta con un
                                        control especial de números de órden.
                                    </div>
                                </div>

                                <div class="w-full py-2">
                                    <div class="w-full px-2" v-if="fueCancelada">
                                        <div
                                            class="theme-background text-center py-2 px-2 size-base border-gray-solid-1">
                                            <span class="font-medium"> Clave: </span>
                                            {{ form.id_cliente }},
                                            <span class="font-medium"> Nombre: </span>
                                            {{ form.cliente }},
                                            <span class="font-medium"> Dirección: </span>
                                            {{ form.direccion_cliente }}
                                        </div>
                                    </div>
                                    <div class="w-full px-2" v-else-if="form.id_cliente == ''">
                                        <div class="bg-danger-50 text-center py-2 px-2 size-base border-danger-solid-1 cursor-pointer color-danger-900"
                                            @click="openBuscador = true">
                                            Debe seleccionar a un cliente
                                        </div>
                                    </div>
                                    <div class="w-full px-2" v-else>
                                        <div class="bg-success-50 py-2 px-2 size-base border-success-solid-2 uppercase">
                                            <div class="flex flex-wrap">
                                                <div class="w-full lg:w-9/12 py-1">
                                                    <span class="font-medium"> Clave: </span>
                                                    {{ form.id_cliente }},
                                                    <span class="font-medium"> Nombre: </span>
                                                    {{ form.cliente }},
                                                    <span class="font-medium"> Dirección: </span>
                                                    {{ form.direccion_cliente }}
                                                </div>
                                                <div class="w-full lg:w-3/12 text-center py-1">
                                                    <span @click="quitarCliente()"
                                                        class="color-danger-900 cursor-pointer">X Cambiar
                                                        cliente
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="title-form-group">Control de Venta</div>
                        <div class="form-group-content">
                            <div class="flex flex-wrap">
                                <div class="w-full px-2 input-text">
                                    <label>
                                        Seleccione 1 vendedor:
                                        <span>(*)</span>
                                    </label>
                                    <v-select :options="vendedores" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-model="form.vendedor" class="w-full"
                                        v-validate:vendedor_validacion_computed.immediate="'required'
                                            " name="vendedor" data-vv-as=" " :disabled="fueCancelada">
                                        <div slot="no-options">Seleccione un vendedor</div>
                                    </v-select>
                                    <span>{{ errors.first("vendedor") }}</span>
                                    <span v-if="this.errores['vendedor.value']">{{
                                        errores["vendedor.value"][0]
                                    }}</span>
                                </div>
                                <div class="w-full px-2 input-text">
                                    <label>Fecha de la Venta (Año-Mes-Dia)</label>
                                    <span>(*)</span>
                                    <flat-pickr :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " name="fecha_venta" data-vv-as=" "
                                        v-validate:fecha_venta_validacion_computed.immediate="'required'
                                            " :config="configdateTimePicker" v-model="form.fecha_venta" placeholder="Fecha de la Venta"
                                        class="w-full" />
                                    <span>{{ errors.first("fecha_venta") }}</span>
                                    <span v-if="this.errores.fecha_venta">{{
                                        errores.fecha_venta[0]
                                    }}</span>
                                </div>
                                <div class="w-full lg:w-6/12 px-2 input-text">
                                    <label>
                                        Núm. Solicitud
                                        <span>(*)</span>
                                    </label>
                                    <vs-input v-validate:solicitud_validacion_computed.immediate="'required'
                                        " name="solicitud" data-vv-as=" " type="text" class="w-full"
                                        placeholder=" Núm. Solicitud" v-model="form.solicitud" :disabled="fueCancelada"
                                        maxlength="12" />
                                    <span>{{ errors.first("solicitud") }}</span>
                                    <span v-if="this.errores.solicitud">{{
                                        errores.solicitud[0]
                                    }}</span>
                                </div>
                                <div class="w-full lg:w-6/12 px-2 input-text">
                                    <label>
                                        Núm. Convenio
                                        <span>(*)</span>
                                    </label>
                                    <vs-input v-validate:num_convenio_validacion_computed.immediate="'required'
                                        " name="num_convenio" data-vv-as=" " type="text" class="w-full"
                                        placeholder="Núm. Convenio" v-model="form.convenio"
                                        :disabled="!capturar_num_convenio || fueCancelada" maxlength="16" />
                                    <span>{{ errors.first("num_convenio") }}</span>
                                    <span v-if="this.errores.convenio">{{
                                        errores.convenio[0]
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Seleccionar el plan -->
                </div>
            </div>
            <!--Fin venta-->

            <!--Titular sustituo-->
            <div class="form-group">
                <div class="title-form-group">Titular Sustituto del Contrato</div>
                <div class="form-group-content">
                    <div class="flex flex-wrap">
                        <div class="w-full lg:w-4/12 xl:w-4/12 px-2 input-text">
                            <label class=" ">
                                Nombre del titular sustituto
                                <span>(*)</span>
                            </label>
                            <vs-input name="titular_sustituto" data-vv-as=" " data-vv-validate-on="blur"
                                v-validate="'required'" maxlength="150" type="text" class="w-full"
                                placeholder="Nombre del titular sustituto" v-model="form.titular_sustituto"
                                :disabled="fueCancelada" />
                            <span>{{ errors.first("titular_sustituto") }}</span>
                            <span v-if="this.errores.titular_sustituto">{{
                                errores.titular_sustituto[0]
                            }}</span>
                        </div>
                        <div class="w-full md:w-6/12 lg:w-4/12 xl:w-4/12 px-2 input-text">
                            <label class=" ">
                                Parentesco con el titular sustituto
                                <span>(*)</span>
                            </label>
                            <vs-input name="parentesco_titular_sustituto" data-vv-as=" " data-vv-validate-on="blur"
                                v-validate="'required'" maxlength="45" type="text" class="w-full"
                                placeholder="Ej. Hermano" v-model="form.parentesco_titular_sustituto"
                                :disabled="fueCancelada" />
                            <span>
                                {{ errors.first("parentesco_titular_sustituto") }}
                            </span>
                            <span v-if="this.errores.parentesco_titular_sustituto">{{
                                errores.parentesco_titular_sustituto[0]
                            }}</span>
                        </div>
                        <div class="w-full md:w-6/12 lg:w-4/12 xl:w-4/12 px-2 input-text">
                            <label class=" ">
                                Teléfono de contacto
                                <span>(*)</span>
                            </label>
                            <vs-input name="telefono_titular_sustituto" data-vv-as=" " data-vv-validate-on="blur"
                                v-validate="'required'" maxlength="45" type="text" class="w-full"
                                placeholder="Ingrese un teléfono" v-model="form.telefono_titular_sustituto"
                                :disabled="fueCancelada" />
                            <span>{{ errors.first("telefono_titular_sustituto") }}</span>
                            <span v-if="this.errores.telefono_titular_sustituto">{{
                                errores.telefono_titular_sustituto[0]
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--Titular sustituo-->

            <!--datos del titular y beneficiarios-->
            <div class="w-full text-right mt-6" v-if="!fueCancelada">
                <span @click="agregar_beneficiario()" class="color-success-900 cursor-pointer">+ Agregar
                    beneficiarios</span>
            </div>
            <div class="form-group">
                <div class="title-form-group">Beneficiarios del contrato</div>
                <div class="form-group-content">
                    <div v-if="form.beneficiarios.length > 0">
                        <div :key="index" v-for="(beneficiario, index) in form.beneficiarios" class="flex flex-wrap">
                            <div class="w-full md:w-11/12">
                                <div class="flex flex-wrap">
                                    <div class="w-full lg:w-4/12 xl:w-4/12 px-2 input-text">
                                        <label>
                                            Beneficiario {{ index + 1 }} - Nombre completo
                                            <span>(*)</span>
                                        </label>
                                        <vs-input :name="'beneficiario' + index" data-vv-as=" "
                                            data-vv-validate-on="blur" v-validate="'required'" maxlength="75"
                                            type="text" class="w-full" placeholder="Ingrese el nombre del benericiario"
                                            v-model="beneficiario.nombre" />
                                        <span>{{ errors.first("beneficiario" + index) }}</span>
                                        <span v-if="errores['beneficiarios.' + index + '.nombre']">{{
                                            errores["beneficiarios." + index + ".nombre"][0]
                                        }}</span>
                                    </div>
                                    <div class="w-full lg:w-4/12 xl:w-4/12 px-2 input-text">
                                        <label>
                                            Beneficiario {{ index + 1 }} - Parentesco
                                            <span>(*)</span>
                                        </label>
                                        <vs-input :name="'parentesco' + index" data-vv-as=" " data-vv-validate-on="blur"
                                            v-validate="'required'" maxlength="35" type="text" class="w-full"
                                            placeholder="Parentesco" v-model="beneficiario.parentesco" />
                                        <span>{{ errors.first("parentesco" + index) }}</span>
                                        <span v-if="errores['beneficiarios.' + index + '.parentesco']">
                                            {{ errores["beneficiarios." + index + ".parentesco"][0] }}
                                        </span>
                                    </div>
                                    <div class="w-full lg:w-4/12 xl:w-4/12 px-2 input-text">
                                        <label> Beneficiario {{ index + 1 }} - Teléfono </label>
                                        <vs-input :name="'telefono' + index" maxlength="35" type="text" class="w-full"
                                            placeholder="Teléfono" v-model="beneficiario.telefono" />
                                    </div>
                                </div>
                            </div>
                            <div class="w-full md:w-1/12 px-2">
                                <div class="table h-full mx-auto mt-2">
                                    <span @click="remover_beneficiario(index)" v-if="!fueCancelada"
                                        class="color-danger-900 cursor-pointer table-cell align-middle">X remover</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="px-2">
                        <div class="w-full px-2 size-base color-copy pt-2">
                            <span class="color-danger-900 font-medium">Ojo:</span>
                            No se han ingresado datos de los beneficiarios de este contrato.
                        </div>
                    </div>
                </div>
            </div>
            <!--fin de datos del titular y beneficiarios-->

            <!--Checkout-->
            <div class="form-group">
                <div class="title-form-group">$ Información resumida de la venta</div>
                <div class="form-group-content">
                    <div class="flex flex-wrap">
                        <div class="w-full lg:w-6/12 px-2">
                            <!--checkout-->
                            <div class="flex flex-wrap">
                                <div class="w-full md:w-6/12 input-text px-2">
                                    <label class=" ">
                                        Número de Pagos
                                        <span>(*)</span>
                                    </label>
                                    <vs-input :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " size="large" v-validate.disabled="'required|integer|min_value:' +
                                            minimo_financiamiento +
                                            '|max_value:' +
                                            maximo_financiamiento
                                            " name="financiamiento" data-vv-as=" " type="text" class="w-full" placeholder="Número de pagos"
                                        v-model="form.financiamiento" maxlength="3" />
                                    <span>{{ errors.first("financiamiento") }}</span>
                                    <span v-if="this.errores.financiamiento">{{
                                        errores.financiamiento[0]
                                    }}</span>
                                </div>

                                <div class="w-full md:w-6/12 input-text px-2">
                                    <label class=" ">
                                        Tasa IVA %
                                        <span>(*)</span>
                                    </label>
                                    <vs-input :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " size="large" name="tasa_iva" data-vv-as=" "
                                        v-validate="'required|decimal:2|min_value:0|max_value:25'" type="text"
                                        class="w-full" placeholder="Porcentaje IVA" v-model="form.tasa_iva"
                                        maxlength="2" />
                                    <span>{{ errors.first("tasa_iva") }}</span>
                                    <span v-if="this.errores.tasa_iva">{{
                                        errores.tasa_iva[0]
                                    }}</span>
                                </div>
                                <!--aqui-->
                                <div class="w-full input-text px-2">
                                    <label class=" ">
                                        $ Costo neto del paquete
                                        <span>(*)</span>
                                    </label>
                                    <vs-input :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " size="large" name="costo_neto" data-vv-as=" "
                                        v-validate="'required|decimal:2|min_value:1'" type="text" class="w-full"
                                        placeholder="" v-model="form.costo_neto" />
                                    <span>{{ errors.first("costo_neto") }}</span>
                                    <span v-if="this.errores.costo_neto">{{
                                        errores.costo_neto[0]
                                    }}</span>
                                </div>

                                <div class="w-full input-text px-2">
                                    <label class=" ">
                                        $ Descuento
                                        <span>(*)</span>
                                    </label>
                                    <vs-input :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " size="large" name="descuento" data-vv-as=" " v-validate="'required|decimal:2|min_value:0|max_value:' +
                                            form.costo_neto
                                            " type="text" class="w-full" placeholder="" v-model="form.descuento" />
                                    <span>{{ errors.first("descuento") }}</span>
                                    <span v-if="this.errores.descuento">{{
                                        errores.descuento[0]
                                    }}</span>
                                </div>

                                <div class="w-full md:w-6/12 input-text px-2">
                                    <label class=" ">
                                        $ Total a Pagar
                                        <span>(*)</span>
                                    </label>
                                    <vs-input size="large" name="total_a_pagar" data-vv-as=" "
                                        v-validate="'required|decimal:2|min_value:0'" type="text" class="w-full"
                                        v-model="total_a_pagar_computed" :disabled="true" readonly />
                                    <span>{{ errors.first("total_a_pagar") }}</span>
                                </div>
                                <div class="w-full md:w-6/12 input-text px-2">
                                    <label class=" ">
                                        $ Pago Inicial
                                        <span>(*)</span>
                                    </label>
                                    <vs-input :disabled="tiene_pagos_realizados || ventaLiquidada || fueCancelada
                                        " size="large" name="pago_inicial" data-vv-as=" " v-validate="'required|decimal:2|min_value:' +
                                            valor_minimo_pago_inicial +
                                            '|max_value:' +
                                            valor_maximo_pago_inicial
                                            " maxlength="10" type="text" class="w-full" v-model="form.pago_inicial" placeholder="" />
                                    <span>{{ errors.first("pago_inicial") }}</span>

                                    <span v-if="this.errores.pago_inicial">{{
                                        errores.pago_inicial[0]
                                    }}</span>
                                </div>

                                <div class="w-full input-text px-2">
                                    <label class=" "> Ingrese alguna nota o comentario </label>
                                    <vs-textarea :rows="4" ref="nota" type="text" class="w-full h-full"
                                        v-model.trim="form.nota" />
                                </div>
                            </div>
                            <!--checkout-->
                        </div>

                        <div class="w-full lg:w-6/12 px-2 lg:mt-16">
                            <div class="flex flex-wrap">
                                <!--Resumen checkout-->
                                <div
                                    class="p-4 w-full md:w-10/12 mx-auto rounded-lg size-base border-gray-solid-1 hidden xl:block rounded-lg">
                                    <div class="size-base font-bold color-black-900 uppercase pb-6 text-center">
                                        Resumen de la venta
                                    </div>
                                    <div class="flex flex-wrap color-copy">
                                        <div class="w-full sm:w-6/12 px-2 text-left font-medium pb-2">
                                            Cliente
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 font-medium text-right pb-2">
                                            {{ form.cliente }}
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 text-left font-medium py-2">
                                            Paquete Funerario
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 font-medium text-right py-2">
                                            {{ form.plan_funerario.label }}
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 text-left font-medium py-2">
                                            Vendedor
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 font-medium text-right py-2">
                                            <span v-if="this.form.vendedor.value != ''">{{
                                                this.form.vendedor.label
                                            }}</span>
                                            <span v-else class="">Seleccione un Vendedor</span>
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 text-left font-medium py-2">
                                            $ Costo neto
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 font-medium text-right py-2">
                                            $ {{ form.costo_neto | numFormat("0,000.00") }}
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 text-left font-medium py-2">
                                            $ Descuento
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 font-medium text-right py-2">
                                            $ {{ form.descuento | numFormat("0,000.00") }}
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 text-left font-medium py-2">
                                            $ Total a pagar
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 font-medium text-right py-2">
                                            $ {{ total_a_pagar_computed | numFormat("0,000.00") }}
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 text-left font-medium py-2">
                                            $ Pago inicial
                                        </div>
                                        <div class="w-full sm:w-6/12 px-2 font-medium text-right py-2">
                                            $ {{ form.pago_inicial | numFormat("0,000.00") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full md:w-10/12 px-2 size-base color-copy pt-6 text-center mx-auto">
                                    <span class="color-danger-900 font-medium">Ojo:</span>
                                    Al hacer hacer click, se está actuando en representación del
                                    cliente para la compra de la propiedad que se ha seleccionado.
                                </div>
                                <div class="w-full px-2 pt-6 text-center mx-auto">
                                    <vs-button v-if="!fueCancelada" class="w-full md:w-10/12" @click="acceptAlert()"
                                        color="success">
                                        <span v-if="this.getTipoformulario == 'agregar'">Guardar Venta</span>
                                        <span v-else>Modificar Venta</span>
                                    </vs-button>
                                    <vs-button v-else class="w-full md:w-10/12" color="success">
                                        <span v-if="this.getTipoformulario == 'agregar'">Guardar Venta</span>
                                        <span v-else>Modificar Venta</span>
                                    </vs-button>
                                </div>
                                <!--Resumen checkout-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>
        <Password :show="openPassword" :callback-on-success="callback" @closeVerificar="closePassword"
            :accion="accionNombre">
        </Password>
        <ConfirmarDanger :z_index="'z-index58k'" :show="openConfirmarSinPassword"
            :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
            :accion="accionConfirmarSinPassword" :confirmarButton="botonConfirmarSinPassword"></ConfirmarDanger>

        <ConfirmarAceptar :z_index="'z-index58k'" :show="openConfirmarAceptar"
            :callback-on-success="callBackConfirmarAceptar" @closeVerificar="openConfirmarAceptar = false"
            :accion="'He revisado la información y quiero guardar la venta'" :confirmarButton="'Guardar Venta'">
        </ConfirmarAceptar>

        <ClientesBuscador :z_index="'z-index55k'" :show="openBuscador" @closeBuscador="openBuscador = false"
            @retornoCliente="clienteSeleccionado"></ClientesBuscador>
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
import planes from "@services/planes";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ClientesBuscador from "@pages/clientes/searcher.vue";

/**VARIABLES GLOBALES */
import { alfabeto, configdateTimePicker } from "@/VariablesGlobales";

export default {
    components: {
        "v-select": vSelect,
        flatPickr,
        Password,
        ConfirmarDanger,
        ConfirmarAceptar,
        ClientesBuscador,
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
        id_venta: {
            type: Number,
            required: false,
            default: 0,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index54k",
        },
    },
    watch: {
        show: function (newValue, oldValue) {
            this.limpiarValidation();
            if (newValue == true) {
                this.$nextTick(() =>
                    this.$refs["cliente_ref"].$el.querySelector("input").focus()
                );
                this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
                    this.cancelar();
                };
                (async () => {
                    await this.get_planes_funerarios();
                    await this.get_vendedores();
                    if (this.getTipoformulario == "agregar") {
                        /**acciones cuando el formulario es de agregar */
                    } else {
                        /**es modificar */
                        /**pasando el valor de la venta id */
                        this.form.id_venta = this.get_venta_id;
                        /**se cargan los datos al formulario */
                        await this.consultar_venta_id();
                    }
                })();
            } else {
                /**acciones al cerrar el formulario */
            }
        },
    },
    computed: {
        minimo_financiamiento: function () {
            return 1;
        },
        maximo_financiamiento: function () {
            return 120;
        },
        /**sacando los valores para aplicar los descuentos respectivos */
        tasa_iva_porcentaje_computed: function () {
            /**calculando el iva */
            let tasa_iva = (Number(this.form.tasa_iva) / 100).toFixed(2);
            return tasa_iva;
        },

        tasa_iva_computed: function () {
            /**calculando el iva */
            let tasa_iva = (Number(this.form.tasa_iva) / 100 + 1).toFixed(2);
            return tasa_iva;
        },

        total_a_pagar_computed: function () {
            return (this.form.costo_neto - this.form.descuento).toFixed(2);
        },

        verLista: function () {
            if (this.form.plan_funerario.value != "") {
                let mostrar = false;
                this.datos = [];
                this.form.plan_funerario.secciones.forEach((element, index_seccion) => {
                    if (element.conceptos) {
                        if (element.conceptos.length > 0) {
                            element.conceptos.forEach((concepto, index_concepto) => {
                                this.datos.push({
                                    concepto: concepto.concepto,
                                    concepto_ingles: concepto.concepto_ingles,
                                    aplicar: concepto.aplicar_en,
                                });
                            });
                            mostrar = true;
                        }
                    }
                });
                if (mostrar == true) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        },
        /**dle modulo */

        /**fin de valores para la aplicacion de toales e impuestos */
        /**minimo valor permitido del enganche */
        valor_minimo_pago_inicial: function () {
            let pago_inicial_minimo = 0;
            if (this.form.financiamiento == 1) {
                pago_inicial_minimo = this.total_a_pagar_computed;
            } else {
                pago_inicial_minimo = (this.total_a_pagar_computed * 0.1).toFixed(2);
            }
            if (isNaN(pago_inicial_minimo)) {
                return 0;
            } else {
                return pago_inicial_minimo;
            }
        },
        /**maximo valor permitido del enganche */
        valor_maximo_pago_inicial: function () {
            let pago_inicial_maximo = 0;
            if (this.form.financiamiento == 1) {
                pago_inicial_maximo = this.total_a_pagar_computed;
            } else {
                pago_inicial_maximo = (this.total_a_pagar_computed * 0.7).toFixed(2);
            }
            if (isNaN(pago_inicial_maximo)) {
                return 0;
            } else {
                return pago_inicial_maximo;
            }
        },

        /**checando si la venta ya fue liquidada*/
        ventaLiquidada: function () {
            if (this.getTipoformulario == "modificar") {
                if (this.datosVenta.saldo_neto <= 0) {
                    return true;
                } else return false;
            } else return false;
        },
        /**checando si ya hay pagos vigentes realizados en la venta, por lo cual no puede cambiar la fecha de la venta */
        tiene_pagos_realizados: function () {
            if (this.getTipoformulario == "modificar") {
                if (this.datosVenta.pagos_realizados > 0) {
                    return true;
                } else return false;
            } else return false;
        },
        /**checar si tiene pagos vencidos */
        tienePagosVencidos: function () {
            if (this.getTipoformulario == "modificar") {
                if (this.datosVenta.pagos_vencidos > 0) {
                    return true;
                } else return false;
            } else return false;
        },
        fueCancelada: function () {
            if (this.getTipoformulario == "modificar") {
                if (this.datosVenta.operacion_status == 0) {
                    return true;
                } else return false;
            } else return false;
        },
        /**validar si es modificar el formulario */
        ModificarVenta: function () {
            if (this.getTipoformulario == "modificar") {
                return true;
            } else return false;
        },

        getTipoformulario: {
            get() {
                return this.tipo;
            },
            set(newValue) {
                return newValue;
            },
        },
        get_venta_id: {
            get() {
                return this.id_venta;
            },
            set(newValue) {
                return newValue;
            },
        },

        showVentana: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            },
        },
        capturar_num_convenio: function () {
            if (this.form.ventaAntiguedad.value > 1) {
                return true;
            } else {
                return false;
            }
        },

        //validaciones calculadas
        //valido que elija un plan de venta

        plan_funerario_validacion_computed: function () {
            return this.form.plan_funerario.value;
        },

        antiguedad_validacion_computed: function () {
            return this.form.ventaAntiguedad.value;
        },

        vendedor_validacion_computed: function () {
            return this.form.vendedor.value;
        },
        fecha_venta_validacion_computed: function () {
            return this.form.fecha_venta;
        },

        solicitud_validacion_computed: function () {
            //checo que el dato venta a futuro este activo
            if (this.form.tipo_financiamiento == 2) {
                return this.form.solicitud;
            } else return true;
        },

        num_convenio_validacion_computed: function () {
            //checo que el dato venta a futuro este activo y que sea de venta antes del sistema
            if (this.form.ventaAntiguedad.value >= 2) {
                return this.form.convenio;
            } else return true;
        },

        //fin de validaciones calculadas
    },
    data() {
        return {
            planes_funerarios: [],
            datos: [],
            /**variables dle modulo */
            configdateTimePicker: configdateTimePicker,
            verDisponibilidad: false,
            openBuscador: false,
            accionConfirmarSinPassword: "",
            botonConfirmarSinPassword: "",
            alfabeto: alfabeto,
            openPassword: false,
            openConfirmarSinPassword: false,
            callback: Function,
            callBackConfirmar: Function,
            openConfirmarAceptar: false,
            callBackConfirmarAceptar: Function,
            accionNombre: "Modificar Venta",
            ventasAntiguedad: [
                {
                    label: "NUEVA VENTA",
                    value: 1,
                },
                {
                    label: "A/S SIN LIQUIDAR",
                    value: 2,
                },
                {
                    label: "A/S - LIQUIDADA",
                    value: 3,
                },
            ],
            vendedores: [],
            //variables con mapa

            /**para modificar, se traen los datos aqui */
            datosVenta: [],
            //fin var con mapa
            form: {
                tipo_financiamiento: 2 /**directamente solo a futuro */,
                plan_funerario: {
                    label: "Seleccione 1",
                    plan: "",
                    plan_ingles: "",
                    nota: "",
                    nota_ingles: "",
                    value: "",
                    secciones: [],
                    precios: [],
                },
                /**varaibles del modulo */
                salarios_minimos: "12",
                id_venta: "",
                /**datos del cliente seleccionado */
                cliente: "seleccione 1 cliente",
                direccion_cliente: "",
                id_cliente: "",
                fecha_venta: "",
                /**titular substituto */
                titular_sustituto: "",
                parentesco_titular_sustituto: "",
                telefono_titular_sustituto: "",
                //
                solicitud: "",
                convenio: "",
                titulo: "",
                /**datos origen */
                //variables con mapa

                ventaAntiguedad: {
                    label: "NUEVA VENTA",
                    value: 1,
                },
                /**muestra el enganche original con el que se hizo la venta */

                financiamiento: 1,
                tasa_iva: 16,
                costo_neto: "",
                descuento: 0,
                pago_inicial: "",

                vendedor: {
                    label: "Seleccione 1",
                    value: "",
                },
                beneficiarios: [],
                index_beneficiario: 0,
                nota: "",
                //fin var con mapa
            },
            errores: [],
        };
    },
    methods: {
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
                        /**aqui se hace la validacion en los totales de la venta */
                        //se confirma la cntraseña
                        /**actualizando los valores de total de venta */
                        //fin de actualizar datos de ubicacion
                        (async () => {
                            if (this.getTipoformulario == "agregar") {
                                this.callBackConfirmarAceptar = await this.guardar_venta;
                                this.openConfirmarAceptar = true;
                            } else {
                                /**es modificacion */
                                this.callback = await this.modificar_venta;
                                this.openPassword = true;
                            }
                        })();
                    }
                })
                .catch(() => { });
        },

        async guardar_venta() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await planes.guardar_venta(this.form);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Ventas de Propiedades",
                        text: "Se ha guardado la venta correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.$emit("ver_pdfs_nueva_venta", res.data);
                    this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Ventas de Propiedades",
                        text: "Error al guardar la venta, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000,
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
                            title: "Guardar Venta",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Guardar Venta",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 15000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },

        async modificar_venta() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                let res = await planes.modificar_venta(this.form);
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Ventas de Propiedades",
                        text: "Se ha modificado la venta correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.$emit("ver_pdfs_nueva_venta", res.data);
                    this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Ventas de Propiedades",
                        text: "Error al modificar la venta, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000,
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
                            title: "Modificar Venta",
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
                            title: "Modificar información de la venta",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 30000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },
        cancel() {
            this.$emit("closeVentana");
        },
        async get_planes_funerarios() {
            try {
                this.$vs.loading();
                let res = await planes.get_planes(false, "");
                //le agrego todos los usuarios vendedores
                this.planes_funerarios = [];
                this.planes_funerarios.push({
                    label: "Seleccione 1",
                    plan: "",
                    plan_ingles: "",
                    nota: "",
                    nota_ingles: "",
                    value: "",
                    secciones: [],
                    precios: [],
                });
                res.data.forEach((element) => {
                    this.planes_funerarios.push({
                        label: element.plan,
                        plan: element.plan,
                        plan_ingles: element.plan_ingles,
                        nota: element.nota,
                        nota_ingles: element.nota_ingles,
                        value: element.id,
                        secciones: element.secciones,
                        precios: element.precios,
                    });
                });
                if (this.getTipoformulario == "agregar") {
                    if (this.planes_funerarios.length > 1) {
                        /**selecciona el primer plan que venga en el arreglo */
                        this.form.plan_funerario = this.planes_funerarios[1];
                    } else {
                        /**selecciona el "Seleccione 1" */
                        this.form.plan_funerario = this.planes_funerarios[0];
                    }
                }
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
                    time: "9000",
                });
                this.$vs.loading.close();
                this.cerrarVentana();
            }
        },
        //get vendedores
        async get_vendedores() {
            try {
                let res = await planes.get_vendedores();
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
                    text:
                        "Ha ocurrido un error al tratar de cargar el catálogo de vendedores, por favor reintente.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "9000",
                });
                this.cerrarVentana();
            }
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
        //regresa los datos a su estado inicial
        limpiarVentana() {
            this.form.plan_funerario = {
                label: "Seleccione 1",
                plan: "",
                plan_ingles: "",
                nota: "",
                nota_ingles: "",
                value: "",
                secciones: [],
                precios: [],
            };
            this.form.ventaAntiguedad = this.ventasAntiguedad[0];
            this.form.solicitud = "";
            this.form.convenio = "";
            this.form.titulo = "";
            this.form.cliente = "";
            this.form.id_cliente = "";
            this.form.direccion_cliente = "";
            this.form.vendedor = { label: "Seleccione 1", value: "" };
            this.form.fecha_venta = "";
            this.form.titular_sustituto = "";
            this.form.parentesco_titular_sustituto = "";
            this.form.telefono_titular_sustituto = "";
            this.form.beneficiarios = [];

            this.form.tasa_iva = 16;
            this.form.financiamiento = 1;
            this.form.costo_neto = "";
            this.form.descuento = 0;
            this.form.pago_inicial = "";
            this.form.nota = "";
        },

        closePassword() {
            this.openPassword = false;
        },

        //agregar beneficiario
        agregar_beneficiario() {
            //verifico si todos los datos estan completos para dejarle agregar nuevos
            let errores_de_captura_en_datos = 0;
            /**maximo 10 beneficiarios */
            if (this.form.beneficiarios.length < 10) {
                this.form.beneficiarios.forEach((element) => {
                    if (
                        element.nombre === "" ||
                        element.parentesco === "" ||
                        element.telefono === ""
                    ) {
                        errores_de_captura_en_datos += 1;
                    }
                });
                if (errores_de_captura_en_datos > 0) {
                    //no paso
                    this.$vs.notify({
                        title: "Error",
                        text: "Verifique que todos los datos han sido capturados",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "9000",
                    });
                } else {
                    //si paso la prueba de toodos los datos
                    this.form.beneficiarios.push({
                        nombre: "",
                        parentesco: "",
                        telefono: "",
                    });
                }
            } else {
                //pasa de 5 beneficiarios
                this.$vs.notify({
                    title: "Límite de Beneficiarios",
                    text: "Ha llegado a 10, el límite de beneficiarios",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "9000",
                });
            }
        },
        //remover beneficiario
        remover_beneficiario(index_beneficiario) {
            this.botonConfirmarSinPassword = "eliminar";
            this.accionConfirmarSinPassword =
                "¿Desea eliminar este beneficiario? Los datos quedarán eliminados del sistema?";
            this.form.index_beneficiario = index_beneficiario;
            this.callBackConfirmar = this.remover_beneficiario_callback;
            this.openConfirmarSinPassword = true;
        },
        //remover beneficiario callback quita del array al beneficiario seleccionado
        remover_beneficiario_callback() {
            this.form.beneficiarios.splice(this.form.index_beneficiario, 1);
        },
        clienteSeleccionado(datos) {
            /**obtiene los datos retornados del buscar cliente */
            this.form.cliente = datos.nombre;
            this.form.id_cliente = datos.id_cliente;
            this.form.direccion_cliente = datos.datos.direccion;

            //alert(datos.id_cliente);
        },

        limpiarCliente() {
            this.form.id_cliente = "";
            this.form.cliente = "seleccione 1 cliente";
            this.form.direccion_cliente = "";
        },
        quitarCliente() {
            this.botonConfirmarSinPassword = "Cambiar cliente";
            this.accionConfirmarSinPassword =
                "¿Desea cambiar de cliente para este contrato?";
            this.callBackConfirmar = this.limpiarCliente;
            this.openConfirmarSinPassword = true;
        },
        async consultar_venta_id() {
            try {
                this.$vs.loading();
                let res = await planes.consultar_venta_id(this.form.id_venta);
                this.datosVenta = res.data[0];
                /**se comienza a llenar la informacion de los datos */
                /**verificar si el plan funerario se ha mantenido igual que cuando se vendio */
                /**aqui se se recorrer el array de planes funerarios con la informacion original del plan */
                let plan_original = {
                    plan: this.datosVenta.venta_plan.nombre_original,
                    plan_ingles: this.datosVenta.venta_plan.nombre_original_ingles,
                    nota: this.datosVenta.venta_plan.nota_original,
                    nota_ingles: this.datosVenta.venta_plan.nota_original_ingles,
                    value: this.datosVenta.venta_plan.planes_funerarios_id,
                    secciones: this.datosVenta.venta_plan.secciones_original,
                };
                /**guarda los precios en caso de que no se encuentre el plan original y se deba agregar precios por separado */
                let precios_plan = [];
                let es_igual = true;
                this.planes_funerarios.forEach((element, index_element) => {
                    if (index_element > 0) {
                        /**capturando los precios del plan  de la venta original*/
                        if (element.value == plan_original.value) {
                            precios_plan = element.precios;
                        }
                        if (
                            element.value == plan_original.value &&
                            element.plan == plan_original.plan &&
                            element.plan_ingles == plan_original.plan_ingles &&
                            element.nota == plan_original.nota &&
                            element.nota_ingles == plan_original.nota_ingles
                        ) {
                            /**el plan se mantiente tal y como se vendio
                             * se procede a ver si los conceptos se mantienen de igual manera
                             */
                            element.secciones.forEach(function callback(
                                seccion,
                                index_seccion
                            ) {
                                if (es_igual == true) {
                                    if (
                                        !(
                                            plan_original.secciones[index_seccion].conceptos.length ==
                                            seccion.conceptos.length
                                        )
                                    ) {
                                        /**no es igual */
                                        es_igual = false;
                                    }
                                    if (es_igual == true) {
                                        /**verificando si cambio algun concepto */
                                        seccion.conceptos.forEach(function callback(
                                            concepto,
                                            index_concepto
                                        ) {
                                            if (
                                                concepto.concepto !=
                                                plan_original.secciones[index_seccion].conceptos[
                                                    index_concepto
                                                ].concepto
                                            ) {
                                                es_igual = false;
                                                return;
                                            }
                                        });
                                    }
                                }
                            });
                            /**si se encontro */
                            if (es_igual == true) {
                                this.form.plan_funerario = element;
                                return;
                            }
                            return;
                        } else {
                            /**no esta */
                            es_igual = false;
                        }
                    }
                });

                if (es_igual == false) {
                    plan_original = {
                        label:
                            this.datosVenta.venta_plan.nombre_original +
                            "(Original de Venta)",
                        plan: this.datosVenta.venta_plan.nombre_original,
                        plan_ingles: this.datosVenta.venta_plan.nombre_original_ingles,
                        nota: this.datosVenta.venta_plan.nota_original,
                        nota_ingles: this.datosVenta.venta_plan.nota_original_ingles,
                        value: this.datosVenta.venta_plan.planes_funerarios_id,
                        secciones: this.datosVenta.venta_plan.secciones_original,
                        precios: precios_plan,
                    };
                    this.planes_funerarios.push(plan_original);
                    this.form.plan_funerario = plan_original;
                    /**si no esta, se agrega el concepto original*/
                }
                /**cargando la antiguedad de la venta */
                this.ventasAntiguedad.forEach((element) => {
                    if (element.value == this.datosVenta.antiguedad_operacion_id) {
                        this.form.ventaAntiguedad = element;
                        return;
                    }
                });
                this.form.id_cliente = this.datosVenta.cliente_id;
                this.form.cliente = this.datosVenta.nombre;
                this.form.direccion_cliente = this.datosVenta.direccion;
                /**verificando si existe el vendedor o si no para crearlo, podria no existir en caso de que haya sido cancelado */
                this.vendedores.forEach((element) => {
                    if (element.value == this.datosVenta.venta_plan.vendedor.id) {
                        this.form.vendedor = element;
                    }
                });
                if (this.form.vendedor.value == "") {
                    let vendedor = {
                        value: this.datosVenta.venta_plan.vendedor.id,
                        label:
                            "(" +
                            this.datosVenta.venta_plan.vendedor.nombre +
                            ", vendedor de origen)",
                    };
                    this.vendedores.push(vendedor);
                    this.form.vendedor = vendedor;
                    /**se agrega el original y se selecciona */
                }
                //fin seleccionar vendedor
                /**fecha de la venta */
                var partes_fecha = this.datosVenta.fecha_operacion.split("-");
                //yyyy-mm-dd
                this.form.fecha_venta = new Date(
                    partes_fecha[0],
                    partes_fecha[1] - 1,
                    partes_fecha[2]
                );
                /**numeros de control */
                this.form.solicitud = this.datosVenta.numero_solicitud;
                this.form.convenio = this.datosVenta.numero_convenio;
                //this.form.titulo = this.datosVenta.numero_titulo;
                /**datos del titular sustituto */
                this.form.titular_sustituto = this.datosVenta.titular_sustituto;
                this.form.parentesco_titular_sustituto = this.datosVenta.parentesco_titular_sustituto;
                this.form.telefono_titular_sustituto = this.datosVenta.telefono_titular_sustituto;
                /**beneficairios */
                this.form.beneficiarios = this.datosVenta.beneficiarios;
                this.form.financiamiento = this.datosVenta.financiamiento;
                this.form.subtotal = this.datosVenta.subtotal;
                this.form.descuento = this.datosVenta.descuento;
                this.form.tasa_iva =
                    Number(this.datosVenta.tasa_iva) <= 0 ? 16 : this.datosVenta.tasa_iva;

                this.form.costo_neto = this.datosVenta.costo_neto_calculado;
                this.form.descuento = this.datosVenta.descuento_neto_calculado;

                this.form.pago_inicial = this.datosVenta.pagos_programados[0].monto_programado;
                this.form.nota = this.datosVenta.nota;
                /**mostrando los datos relacionados al pago */
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
        respuestaDeshabilitado() {
            if (this.tienePagosVencidos) {
                this.$vs.notify({
                    title: "Seleccionar Área del planes",
                    text:
                        "No está permitido cambiar la ubicación de la propiedad mientras no esté al corriente con los pagos establecidos.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "10000",
                });
            } else if (this.ventaLiquidada) {
                this.$vs.notify({
                    title: "Seleccionar Área del planes",
                    text:
                        "No está permitido cambiar la ubicación de la propiedad una vez ya liquidado el total de la cuenta.",
                    iconPack: "feather",
                    icon: "icon-alert-circle",
                    color: "danger",
                    position: "bottom-right",
                    time: "10000",
                });
            }
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
    },
    created() { },
};
</script>
