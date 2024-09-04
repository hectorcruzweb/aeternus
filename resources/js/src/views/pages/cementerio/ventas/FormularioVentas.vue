<template>
  <div class="centerx">
    <vs-popup
      :class="['forms-popup', z_index]"
      fullscreen
      close="cancelar"
      :title="
        getTipoformulario == 'modificar'
          ? 'Modificar Venta de Propiedad'
          : 'Registrar Venta de Propiedad'
      "
      :active.sync="showVentana"
      ref="formulario"
    >
      <!--inicio venta-->
      <div>
        <div class="flex flex-wrap">
          <div class="w-full lg:w-6/12 xl:w-6/12 px-4">
            <!--mapa del cementerio-->
            <div>
              <Mapa
                :disabled="
                  tiene_pagos_realizados || ventaLiquidada || fueCancelada
                "
                :idAreaInicial="idAreaInicial"
                @getDatosTipoPropiedad="getDatosTipoPropiedad"
                @respuestaDeshabilitado="respuestaDeshabilitado"
              ></Mapa>
            </div>
            <!--fin del mapa del cementerio-->
          </div>
          <div class="w-full lg:w-6/12 xl:w-6/12 px-2 xl:pt-8">
            <!--Datos del area-->
            <div class="form-group">
              <div class="title-form-group">
                <span v-if="!this.datosAreas.tipo_propiedades_id"
                  >Ubicación y tipo de venta</span
                >
                <span v-else class="uppercase">
                  {{ this.datosAreas.nombre_area }}
                </span>
              </div>
              <div class="form-group-content">
                <div class="flex flex-wrap">
                  <div class="w-full">
                    <div class="flex flex-wrap"></div>
                  </div>
                  <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
                    <label v-if="this.datosAreas.tipo_propiedades_id">
                      <label v-if="this.datosAreas.tipo_propiedades_id == 4"
                        >Fila <span>(*)</span></label
                      >
                      <label v-else>Ubicación <span>(*)</span></label>
                    </label>
                    <label v-else>
                      Seleccione un Área
                      <span>(*)</span>
                    </label>

                    <v-select
                      :disabled="
                        tiene_pagos_realizados || ventaLiquidada || fueCancelada
                      "
                      :options="filas"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.filas"
                      v-validate:fila_validacion_computed.immediate="'required'"
                      name="fila_validacion"
                      data-vv-as=" "
                    >
                      <div slot="no-options">
                        No Se Ha Seleccionado Ningún Área
                      </div>
                    </v-select>

                    <span>{{ errors.first("fila_validacion") }}</span>

                    <span v-if="this.errores['filas.value']">{{
                      errores["filas.value"][0]
                    }}</span>
                  </div>

                  <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
                    <label v-if="this.datosAreas.tipo_propiedades_id == 4">
                      Ubicación <span>(*)</span>
                    </label>
                    <label v-else> No Aplica </label>
                    <v-select
                      :options="lotes"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.lotes"
                      :disabled="
                        this.datosAreas.tipo_propiedades_id != 4 ||
                        tiene_pagos_realizados ||
                        ventaLiquidada ||
                        fueCancelada
                      "
                      v-validate:ubicacion_validacion_computed.immediate="
                        'required'
                      "
                      name="ubicacion_validacion"
                      data-vv-as=" "
                    >
                      <div slot="no-options">Seleccione 1 Área</div>
                    </v-select>
                    <span>
                      {{ errors.first("ubicacion_validacion") }}
                    </span>
                    <span v-if="this.errores['lotes.value']">{{
                      errores["lotes.value"][0]
                    }}</span>
                  </div>

                  <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
                    <label class="">
                      Tipo de Venta
                      <span>(*)</span>
                    </label>
                    <v-select
                      :disabled="ModificarVenta"
                      :options="ventasAntiguedad"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.ventaAntiguedad"
                      v-validate:antiguedad_validacion_computed.immediate="
                        'required'
                      "
                      name="antiguedad_validacion"
                      data-vv-as=" "
                    >
                      <div slot="no-options">Seleccione 1</div>
                    </v-select>

                    <span>
                      {{ errors.first("antiguedad_validacion") }}
                    </span>

                    <span v-if="this.errores['ventaAntiguedad.value']">{{
                      errores["ventaAntiguedad.value"][0]
                    }}</span>
                  </div>

                  <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
                    <label>Tipo de Financiamiento</label>
                    <div class="mt-3">
                      <vs-radio
                        vs-name="tipoFinanciamiento"
                        v-model="form.tipo_financiamiento"
                        :vs-value="1"
                        class="mr-4"
                        :disabled="
                          tiene_pagos_realizados ||
                          ventaLiquidada ||
                          fueCancelada
                        "
                        >Uso inmediato</vs-radio
                      >
                      <vs-radio
                        vs-name="tipoFinanciamiento"
                        v-model="form.tipo_financiamiento"
                        :vs-value="2"
                        class="mr-4"
                        :disabled="
                          tiene_pagos_realizados ||
                          ventaLiquidada ||
                          fueCancelada
                        "
                        >A futuro</vs-radio
                      >
                    </div>
                  </div>
                  <div class="w-full px-2 size-small color-copy pt-2">
                    <span
                      class="font-medium uppercase"
                      v-if="disponibilidad.disponibilidad_terreno == 1"
                    >
                      <span class="dot-success"></span>
                      Propiedad disponible para venta</span
                    >
                    <span class="uppercase font-medium" v-else>
                          <span class="dot-danger"></span>
                      vendida a cliente (Titular): {{ disponibilidad.cliente }}
                      <img
                                class="cursor-pointer img-btn-28 mx-3"
                                src="@assets/images/folder.svg"
                                title="Expediente"
                                @click="
                                    openReportes=true;
                                "
                            /></span
                    >
                  </div>
                  <div class="w-full px-2 size-small color-copy pt-2">
                    <span class="color-danger-900 font-medium">Ojo:</span>
                    Debe seleccionar la modalidad de la venta que se esté
                    registrando en caso de que haya sido realizada fuera del
                    control del sistema, ya que ese tipo de ventas cuenta con un
                    control especial de números de órden.
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="title-form-group">Control de cliente y venta</div>
              <div class="form-group-content">
                <div class="flex flex-wrap">
                  <div class="w-full py-2">
                    <div class="w-full px-2" v-if="fueCancelada">
                      <div
                        class="
                          theme-background
                          text-center
                          py-2
                          px-2
                          size-base
                          border-gray-solid-1
                        "
                      >
                        <span class="font-medium"> Clave: </span>
                        {{ form.id_cliente }},
                        <span class="font-medium"> Nombre: </span>
                        {{ form.cliente }},
                        <span class="font-medium"> Dirección: </span>
                        {{ form.direccion_cliente }}
                      </div>
                    </div>
                    <div class="w-full px-2" v-else-if="form.id_cliente == ''">
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
                        @click="openBuscador = true"
                      >
                        Debe seleccionar a un cliente
                      </div>
                    </div>
                    <div class="w-full px-2" v-else>
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
                          <div class="w-full lg:w-10/12 py-1">
                            <span class="font-medium"> Clave: </span>
                            {{ form.id_cliente }},
                            <span class="font-medium"> Nombre: </span>
                            {{ form.cliente }},
                            <span class="font-medium"> Dirección: </span>
                            {{ form.direccion_cliente }}
                          </div>
                          <div class="w-full lg:w-2/12 text-center py-1">
                            <span
                              @click="quitarCliente()"
                              class="color-danger-900 cursor-pointer"
                              >X Cambiar cliente
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="w-full px-2 input-text">
                    <label>
                      Seleccione al vendedor
                      <span>(*)</span>
                    </label>
                    <v-select
                      :options="vendedores"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.vendedor"
                      class="w-full"
                      v-validate:vendedor_validacion_computed.immediate="
                        'required'
                      "
                      name="vendedor"
                      data-vv-as=" "
                      :disabled="fueCancelada"
                    >
                      <div slot="no-options">Seleccione un vendedor</div>
                    </v-select>
                    <span>{{ errors.first("vendedor") }}</span>
                    <span v-if="this.errores['vendedor.value']">{{
                      errores["vendedor.value"][0]
                    }}</span>
                  </div>

                  <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
                    <label class="">
                      Fecha de la Venta (Año-Mes-Dia)
                      <span>(*)</span>
                    </label>
                    <flat-pickr
                      :disabled="
                        tiene_pagos_realizados ||
                        ventaLiquidada ||
                        fueCancelada ||
                        ModificarVenta
                      "
                      name="fecha_venta"
                      data-vv-as=" "
                      v-validate:fecha_venta_validacion_computed.immediate="
                        'required'
                      "
                      :config="configdateTimePicker"
                      v-model="form.fecha_venta"
                      placeholder="Fecha de la Venta"
                      class="w-full"
                    />
                    <span>{{ errors.first("fecha_venta") }}</span>
                    <span v-if="this.errores.fecha_venta">{{
                      errores.fecha_venta[0]
                    }}</span>
                  </div>
                  <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
                    <label class="">
                      Núm. Solicitud
                      <span>(*)</span>
                    </label>
                    <vs-input
                      v-validate:solicitud_validacion_computed.immediate="
                        'required'
                      "
                      name="solicitud"
                      data-vv-as=" "
                      type="text"
                      class="w-full"
                      placeholder=" Núm. Solicitud"
                      v-model="form.solicitud"
                      :disabled="tipo_venta || fueCancelada"
                    />
                    <span>{{ errors.first("solicitud") }}</span>
                    <span v-if="this.errores.solicitud">{{
                      errores.solicitud[0]
                    }}</span>
                  </div>
                  <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
                    <label class="">
                      Núm. Convenio
                      <span>(*)</span>
                    </label>
                    <vs-input
                      v-validate:num_convenio_validacion_computed.immediate="
                        'required'
                      "
                      name="num_convenio"
                      data-vv-as=" "
                      type="text"
                      class="w-full"
                      placeholder="Núm. Convenio"
                      v-model="form.convenio"
                      :disabled="!capturar_num_convenio || fueCancelada"
                    />
                    <span>{{ errors.first("num_convenio") }}</span>
                    <span v-if="this.errores.convenio">{{
                      errores.convenio[0]
                    }}</span>
                  </div>
                  <div class="w-full lg:w-6/12 xl:w-6/12 px-2 input-text">
                    <label class="">
                      Núm. Título
                      <span>(*)</span>
                    </label>
                    <vs-input
                      v-validate:num_titulo_validacion_computed.immediate="
                        'required'
                      "
                      name="num_titulo"
                      data-vv-as=" "
                      type="text"
                      class="w-full"
                      placeholder="Núm. Título"
                      v-model="form.titulo"
                      :disabled="
                        !(
                          tipo_venta * capturar_num_titulo +
                          capturar_num_titulo
                        ) || fueCancelada
                      "
                    />
                    <span>{{ errors.first("num_titulo") }}</span>
                    <span v-if="this.errores.titulo">{{
                      errores.titulo[0]
                    }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Datos del area-->

        <div class="form-group">
          <div class="title-form-group">Titular Sustituto del Contrato</div>
          <div class="form-group-content">
            <div class="flex flex-wrap">
              <div class="w-full lg:w-4/12 xl:w-4/12 px-2 input-text">
                <label class="">
                  Nombre del titular sustituto
                  <span>(*)</span>
                </label>
                <vs-input
                  name="titular_sustituto"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="150"
                  type="text"
                  class="w-full"
                  placeholder="Nombre del titular sustituto"
                  v-model="form.titular_sustituto"
                  :disabled="fueCancelada"
                />
                <span>{{ errors.first("titular_sustituto") }}</span>
                <span v-if="this.errores.titular_sustituto">{{
                  errores.titular_sustituto[0]
                }}</span>
              </div>
              <div class="w-full md:w-6/12 lg:w-4/12 xl:w-4/12 px-2 input-text">
                <label class="">
                  Parentesco con el titular sustituto
                  <span>(*)</span>
                </label>
                <vs-input
                  name="parentesco_titular_sustituto"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="45"
                  type="text"
                  class="w-full"
                  placeholder="Ej. Hermano"
                  v-model="form.parentesco_titular_sustituto"
                  :disabled="fueCancelada"
                />
                <span>
                  {{ errors.first("parentesco_titular_sustituto") }}
                </span>
                <span v-if="this.errores.parentesco_titular_sustituto">{{
                  errores.parentesco_titular_sustituto[0]
                }}</span>
              </div>
              <div class="w-full md:w-6/12 lg:w-4/12 xl:w-4/12 px-2 input-text">
                <label class="">
                  Teléfono de contacto
                  <span>(*)</span>
                </label>
                <vs-input
                  name="telefono_titular_sustituto"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="45"
                  type="text"
                  class="w-full"
                  placeholder="Ingrese un teléfono"
                  v-model="form.telefono_titular_sustituto"
                  :disabled="fueCancelada"
                />
                <span>{{ errors.first("telefono_titular_sustituto") }}</span>
                <span v-if="this.errores.telefono_titular_sustituto">{{
                  errores.telefono_titular_sustituto[0]
                }}</span>
              </div>
            </div>
          </div>
        </div>
        <!--datos del titular y beneficiarios-->
        <div class="w-full text-right mt-6" v-if="!fueCancelada">
          <span
            @click="agregar_beneficiario()"
            class="color-success-900 cursor-pointer"
            >+ Agregar beneficiarios</span
          >
        </div>
        <div class="form-group">
          <div class="title-form-group">Beneficiarios del contrato</div>
          <div class="form-group-content">
            <div v-if="form.beneficiarios.length > 0">
              <div
                :key="index"
                v-for="(beneficiario, index) in form.beneficiarios"
                class="flex flex-wrap"
              >
                <div class="w-full md:w-11/12">
                  <div class="flex flex-wrap">
                    <div class="w-full lg:w-4/12 xl:w-4/12 px-2 input-text">
                      <label>
                        Beneficiario {{ index + 1 }} - Nombre completo
                        <span>(*)</span>
                      </label>
                      <vs-input
                        :name="'beneficiario' + index"
                        data-vv-as=" "
                        data-vv-validate-on="blur"
                        v-validate="'required'"
                        maxlength="75"
                        type="text"
                        class="w-full"
                        placeholder="Ingrese el nombre del benericiario"
                        v-model="beneficiario.nombre"
                      />
                      <span>{{ errors.first("beneficiario" + index) }}</span>
                      <span
                        v-if="errores['beneficiarios.' + index + '.nombre']"
                        >{{
                          errores["beneficiarios." + index + ".nombre"][0]
                        }}</span
                      >
                    </div>
                    <div class="w-full lg:w-4/12 xl:w-4/12 px-2 input-text">
                      <label>
                        Beneficiario {{ index + 1 }} - Parentesco
                        <span>(*)</span>
                      </label>
                      <vs-input
                        :name="'parentesco' + index"
                        data-vv-as=" "
                        data-vv-validate-on="blur"
                        v-validate="'required'"
                        maxlength="35"
                        type="text"
                        class="w-full"
                        placeholder="Parentesco"
                        v-model="beneficiario.parentesco"
                      />
                      <span>{{ errors.first("parentesco" + index) }}</span>
                      <span
                        v-if="errores['beneficiarios.' + index + '.parentesco']"
                      >
                        {{
                          errores["beneficiarios." + index + ".parentesco"][0]
                        }}
                      </span>
                    </div>
                    <div class="w-full lg:w-4/12 xl:w-4/12 px-2 input-text">
                      <label> Beneficiario {{ index + 1 }} - Teléfono </label>
                      <vs-input
                        :name="'telefono' + index"
                        maxlength="35"
                        type="text"
                        class="w-full"
                        placeholder="Teléfono"
                        v-model="beneficiario.telefono"
                      />
                    </div>
                  </div>
                </div>
                <div class="w-full md:w-1/12 px-2">
                  <div class="table h-full mx-auto mt-2">
                    <span
                      @click="remover_beneficiario(index)"
                      v-if="!fueCancelada"
                      class="
                        color-danger-900
                        cursor-pointer
                        table-cell
                        align-middle
                      "
                      >X remover</span
                    >
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

        <div class="form-group">
          <div class="title-form-group">$ Información resumida de la venta</div>
          <div class="form-group-content">
            <div class="flex flex-wrap">
              <div class="w-full lg:w-6/12 px-2">
                <!--checkout-->
                <div class="flex flex-wrap">
                  <div class="w-full md:w-6/12 input-text px-2">
                    <label class="">
                      Número de Pagos
                      <span>(*)</span>
                    </label>
                    <vs-input
                      :disabled="
                        tiene_pagos_realizados || ventaLiquidada || fueCancelada
                      "
                      size="large"
                      v-validate.disabled="
                        'required|integer|min_value:' +
                        minimo_financiamiento +
                        '|max_value:' +
                        maximo_financiamiento
                      "
                      name="financiamiento"
                      data-vv-as=" "
                      type="text"
                      class="w-full"
                      placeholder="Número de pagos"
                      v-model="form.financiamiento"
                      maxlength="3"
                    />
                    <span>{{ errors.first("financiamiento") }}</span>
                    <span v-if="this.errores.financiamiento">{{
                      errores.financiamiento[0]
                    }}</span>
                  </div>

                  <div class="w-full md:w-6/12 px-2 input-text hidden">
                    <label class="">
                      Salarios Mínimos x Mantenimiento
                      <span>(*)</span>
                    </label>
                    <vs-input
                      size="large"
                      v-validate="'required|integer|min_value:1|max_value:150'"
                      name="salarios_minimos"
                      data-vv-as=" "
                      type="text"
                      class="w-full"
                      placeholder="Ingrese el número de salarios"
                      v-model="form.salarios_minimos"
                      maxlength="3"
                    />

                    <span>{{ errors.first("salarios_minimos") }}</span>

                    <span v-if="this.errores.salarios_minimos">{{
                      errores.salarios_minimos[0]
                    }}</span>
                  </div>

                  <div class="w-full md:w-6/12 input-text px-2">
                    <label class="">
                      Tasa IVA %
                      <span>(*)</span>
                    </label>
                    <vs-input
                      :disabled="
                        tiene_pagos_realizados || ventaLiquidada || fueCancelada
                      "
                      size="large"
                      name="tasa_iva"
                      data-vv-as=" "
                      v-validate="'required|decimal:2|min_value:0|max_value:25'"
                      type="text"
                      class="w-full"
                      placeholder="Porcentaje IVA"
                      v-model="form.tasa_iva"
                      maxlength="2"
                    />

                    <span>{{ errors.first("tasa_iva") }}</span>

                    <span v-if="this.errores.tasa_iva">{{
                      errores.tasa_iva[0]
                    }}</span>
                  </div>
                  <!--aqui-->
                  <div class="w-full input-text px-2">
                    <label class="">
                      $ Costo neto de la propiedad
                      <span>(*)</span>
                    </label>
                    <vs-input
                      :disabled="
                        tiene_pagos_realizados || ventaLiquidada || fueCancelada
                      "
                      size="large"
                      name="costo_neto"
                      data-vv-as=" "
                      v-validate="'required|decimal:2|min_value:1'"
                      type="text"
                      class="w-full"
                      placeholder=""
                      v-model="form.costo_neto"
                    />
                    <span>{{ errors.first("costo_neto") }}</span>
                    <span v-if="this.errores.costo_neto">{{
                      errores.costo_neto[0]
                    }}</span>
                  </div>

                  <div class="w-full input-text px-2">
                    <label class="">
                      $ Descuento
                      <span>(*)</span>
                    </label>
                    <vs-input
                      :disabled="
                        tiene_pagos_realizados || ventaLiquidada || fueCancelada
                      "
                      size="large"
                      name="descuento"
                      data-vv-as=" "
                      v-validate="'required|decimal:2|min_value:0'"
                      type="text"
                      class="w-full"
                      placeholder=""
                      v-model="form.descuento"
                    />
                    <span>{{ errors.first("descuento") }}</span>
                    <span v-if="this.errores.descuento">{{
                      errores.descuento[0]
                    }}</span>
                  </div>

                  <div class="w-full md:w-6/12 input-text px-2">
                    <label class="">
                      $ Total a Pagar
                      <span>(*)</span>
                    </label>
                    <vs-input
                      size="large"
                      name="total_a_pagar"
                      data-vv-as=" "
                      v-validate="'required|decimal:2|min_value:0'"
                      type="text"
                      class="w-full"
                      v-model="total_a_pagar_computed"
                      :disabled="true"
                      readonly
                    />
                    <span>{{ errors.first("total_a_pagar") }}</span>
                  </div>
                  <div class="w-full md:w-6/12 input-text px-2">
                    <label class="">
                      $ Pago Inicial
                      <span>(*)</span>
                    </label>
                    <vs-input
                      :disabled="
                        tiene_pagos_realizados || ventaLiquidada || fueCancelada
                      "
                      size="large"
                      name="pago_inicial"
                      data-vv-as=" "
                      v-validate="
                        'required|decimal:2|min_value:' +
                        this.valor_minimo_pago_inicial +
                        '|max_value:' +
                        this.valor_maximo_pago_inicial
                      "
                      maxlength="10"
                      type="text"
                      class="w-full"
                      v-model="form.pago_inicial"
                      placeholder=""
                    />

                    <span>{{ errors.first("pago_inicial") }}</span>

                    <span v-if="this.errores.pago_inicial">{{
                      errores.pago_inicial[0]
                    }}</span>
                  </div>

                  <div class="w-full input-text px-2">
                    <label class=""> Ingrese alguna nota o comentario </label>
                    <vs-textarea
                      :rows="4"
                      ref="nota"
                      type="text"
                      class="w-full h-full"
                      v-model.trim="form.nota"
                    />
                  </div>
                </div>
                <!--checkout-->
              </div>
              <div class="w-full lg:w-6/12 px-2 lg:mt-16">
                <div class="flex flex-wrap">
                  <!--Resumen checkout-->
                  <div
                    class="
                      p-4
                      w-full
                      md:w-10/12
                      mx-auto
                      rounded-lg
                      size-base
                      border-gray-solid-1
                      hidden
                      xl:block
                      rounded-lg
                    "
                  >
                    <div
                      class="
                        size-base
                        font-bold
                        color-black-900
                        uppercase
                        pb-6
                        text-center
                      "
                    >
                      Resumen de la venta
                    </div>
                    <div class="flex flex-wrap color-copy">
                      <div
                        class="w-full sm:w-6/12 px-2 text-left font-medium pb-2"
                      >
                        Cliente
                      </div>
                      <div
                        class="
                          w-full
                          sm:w-6/12
                          px-2
                          font-medium
                          text-right
                          pb-2
                        "
                      >
                        {{ form.cliente }}
                      </div>
                      <div
                        class="w-full sm:w-6/12 px-2 text-left font-medium py-2"
                      >
                        Ubicación
                      </div>
                      <div
                        class="
                          w-full
                          sm:w-6/12
                          px-2
                          font-medium
                          text-right
                          py-2
                        "
                      >
                        <span v-if="this.datosAreas.id">
                          <span
                            v-if="
                              this.datosAreas.tipo_propiedades_id == 1 ||
                              this.datosAreas.tipo_propiedades_id == 2 ||
                              this.datosAreas.tipo_propiedades_id == 3 ||
                              this.datosAreas.tipo_propiedades_id == 5 ||
                              this.datosAreas.tipo_propiedades_id == 6
                            "
                          >
                            <span v-if="this.form.filas.value != ''">
                              Propiedad
                              {{
                                this.datosAreas["tipo_propiedad"].tipo +
                                " Ubicación " +
                                this.form.filas.label
                              }}
                            </span>
                            <span v-else class=""
                              >Seleccione una ubicación</span
                            >
                          </span>
                          <span v-else>
                            <span
                              v-if="
                                this.form.filas.value != '' &&
                                this.form.lotes.value != ''
                              "
                            >
                              Propiedad
                              {{
                                this.datosAreas["nombre_area"] +
                                " Ubicación " +
                                this.form.lotes.label
                              }}
                            </span>
                            <span v-else class=""
                              >Seleccione una ubicación</span
                            >
                          </span>
                        </span>
                        <span v-else class=""
                          >Seleccione un Área del Cementerio</span
                        >
                      </div>
                      <div
                        class="w-full sm:w-6/12 px-2 text-left font-medium py-2"
                      >
                        Vendedor
                      </div>
                      <div
                        class="
                          w-full
                          sm:w-6/12
                          px-2
                          font-medium
                          text-right
                          py-2
                        "
                      >
                        <span v-if="this.form.vendedor.value != ''">{{
                          this.form.vendedor.label
                        }}</span>
                        <span v-else class="">Seleccione un Vendedor</span>
                      </div>
                      <div
                        class="w-full sm:w-6/12 px-2 text-left font-medium py-2"
                      >
                        $ Costo neto
                      </div>
                      <div
                        class="
                          w-full
                          sm:w-6/12
                          px-2
                          font-medium
                          text-right
                          py-2
                        "
                      >
                        $ {{ form.costo_neto | numFormat("0,000.00") }}
                      </div>
                      <div
                        class="w-full sm:w-6/12 px-2 text-left font-medium py-2"
                      >
                        $ Descuento
                      </div>
                      <div
                        class="
                          w-full
                          sm:w-6/12
                          px-2
                          font-medium
                          text-right
                          py-2
                        "
                      >
                        $ {{ form.descuento | numFormat("0,000.00") }}
                      </div>
                      <div
                        class="w-full sm:w-6/12 px-2 text-left font-medium py-2"
                      >
                        $ Total a pagar
                      </div>
                      <div
                        class="
                          w-full
                          sm:w-6/12
                          px-2
                          font-medium
                          text-right
                          py-2
                        "
                      >
                        $ {{ total_a_pagar_computed | numFormat("0,000.00") }}
                      </div>
                      <div
                        class="w-full sm:w-6/12 px-2 text-left font-medium py-2"
                      >
                        $ Pago inicial
                      </div>
                      <div
                        class="
                          w-full
                          sm:w-6/12
                          px-2
                          font-medium
                          text-right
                          py-2
                        "
                      >
                        $ {{ form.pago_inicial | numFormat("0,000.00") }}
                      </div>
                    </div>
                  </div>
                  <div
                    class="
                      w-full
                      md:w-10/12
                      px-2
                      size-base
                      color-copy
                      pt-6
                      text-center
                      mx-auto
                    "
                  >
                    <span class="color-danger-900 font-medium">Ojo:</span>
                    Al hacer hacer click, se está actuando en representación del
                    cliente para la compra de la propiedad que se ha
                    seleccionado.
                  </div>
                  <div class="w-full px-2 pt-6 text-center mx-auto">
                    <vs-button
                      v-if="!fueCancelada"
                      class="w-full md:w-10/12"
                      @click="acceptAlert()"
                      color="success"
                    >
                      <span v-if="this.getTipoformulario == 'agregar'"
                        >Guardar Venta</span
                      >
                      <span v-else>Modificar Venta</span>
                    </vs-button>
                    <vs-button v-else class="w-full md:w-10/12" color="success">
                      <span v-if="this.getTipoformulario == 'agregar'"
                        >Guardar Venta</span
                      >
                      <span v-else>Modificar Venta</span>
                    </vs-button>
                  </div>
                  <!--Resumen checkout-->
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--checkout-->
      </div>
      <!--fin venta-->
    </vs-popup>
    <Password
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
      :z_index="'z-index58k'"
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero guardar la venta'"
      :confirmarButton="'Guardar Venta'"
    ></ConfirmarAceptar>

    <ClientesBuscador
      :show="openBuscador"
      :z_index="'z-index58k'"
      @closeBuscador="openBuscador = false"
      @retornoCliente="clienteSeleccionado"
    ></ClientesBuscador>
    <ReportesVentas
    :z_index="'z-index58k'"
            :verAcuse="verAcuse"
            :show="openReportes"
            @closeListaReportes="closeListaReportes"
            :id_venta="id_venta_ver"
        ></ReportesVentas>
  </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ReportesVentas from "../ventas/ReportesVentas";
import Mapa from "../Mapa";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
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
    Mapa,
    ConfirmarDanger,
    ConfirmarAceptar,
    ClientesBuscador,
    ReportesVentas
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
          await this.get_vendedores();
          if (this.getTipoformulario == "agregar") {
            this.idAreaInicial = 29;
            this.form.tipo_financiamiento = 1;
          } else {
            /**pasando el valor de la venta id */
            this.form.id_venta = this.get_venta_id;
            /**se cargan los datos al formulario */
            await this.consultar_venta_id();
          }
        })();
      } else {
        this.idAreaInicial = 0;
        /**forzando el cambio de area para que se actualice al reabrir la ventana */
        /*if (this.idAreaInicial > 2) {
          this.idAreaInicial -= 1;
        } else {
          this.idAreaInicial += 1;
        }*/
      }
    },

    //aqui obtengo los datos necesarios para poder saber cuantas filas tiene una propiedad
    "form.filas": function (newValue, oldValue) {
      if (newValue.value != "") {
        if (this.datosAreas.tipo_propiedades_id == 4) {
          this.lotes = [];
          this.lotes.push({ label: "Seleccione 1", value: "" });
          this.datosAreas["filas_columnas"].forEach((element) => {
            //aqui obtengo los datos para poder llamar la funcion que me traera los
            //valores necesarios para poder cargar los lotes que existen segun cada fila
            if (element.fila == this.form.filas.value) {
              //aqui tengo los valores para saber en que columna empieza y acaba cada fila
              for (let index = 1; index <= this.datosAreas.columnas; index++) {
                //aqui se debe crear unicamente los lotes que estan en cada fila segun la terraza
                if (
                  index >= element.empieza_columna &&
                  index <= element.fin_columna
                ) {
                  this.lotes.push({
                    label:
                      "Fila " +
                      this.alfabeto[this.form.filas.value - 1] +
                      " - Lote " +
                      index,
                    value: index,
                  });
                }
              }
              //la primero opcion
              this.form.lotes = this.lotes[1];
              return;
            }
          });
          /**revisnado si es form modificar para actualizar el lote seleccionado */
          //la primero opcion
          this.form.lotes = this.lotes[1];
          if (this.getTipoformulario == "modificar") {
            /**cuando el form es para modificar */
            /**buscando la fila que le corresponde segun la propiedad */
            if (this.datosVenta) {
              this.lotes.forEach((element) => {
                if (element.value == this.datosVenta.venta_terreno.lote_raw) {
                  this.form.lotes = element;
                  return;
                }
              });
            }
          }
        } else {
          this.lotes = [];
          this.lotes.push({ label: "Seleccione 1", value: "" });
          this.form.lotes = { label: "Seleccione 1", value: "" };
        }
      } else {
        this.lotes = [];
        this.lotes.push({ label: "Seleccione 1", value: "" });
        this.form.lotes = { label: "Seleccione 1", value: "" };
      }
    },
    //watchs con mapa
    datosAreas: function (newValue, oldValue) {
      //creo las posibles opciones para filas y modulos
      if (newValue != []) {
        //actualizo el id del tipo de propiedad
        this.form.propiedades_id = this.datosAreas.tipo_propiedades_id;
        //creo uniplex, duplex, triplex y cuadruplex sin nicho
        this.filas = [];
        this.filas.push({ label: "Seleccione 1", value: "" });
        this.form.filas = this.filas[0];
        if (this.datosAreas.tipo_propiedades_id != 4) {
          this.lotes = [];
          this.lotes.push({ label: "Seleccione 1", value: "" });
          this.form.lotes = { label: "Seleccione 1", value: "" };
          //al encontrar el numero de filas que les corresponden segun la propiedad creo las filas para que la seleccione el usuario
          let indicador_propiedad = "";
          if (
            this.datosAreas.tipo_propiedades_id != 4 ||
            this.datosAreas.tipo_propiedades_id == 3
          ) {
            indicador_propiedad = "Módulo ";
          } else if (this.datosAreas.tipo_propiedades_id == 3) {
            indicador_propiedad = "Fila ";
          }
          for (let index = 1; index <= this.datosAreas.filas; index++) {
            this.filas.push({
              label: indicador_propiedad + " - " + index,
              value: index,
            });
          }
        } else {
          //filas para las terrazas
          for (let index = 1; index <= this.datosAreas.filas; index++) {
            this.filas.push({
              label: "Fila - " + this.alfabeto[index - 1],
              value: index,
            });
          }
        }
        //la primero opcion
        this.form.filas = this.filas[1];
        if (this.getTipoformulario == "modificar") {
          /**cuando el form es para modificar */
          /**buscando la fila que le corresponde segun la propiedad */
          if (this.datosVenta) {
            this.filas.forEach((element) => {
              if (element.value == this.datosVenta.venta_terreno.fila_raw) {
                this.form.filas = element;
                return;
              }
            });
          }
        }
      }
    },
    crear_ubicacion_computed: function (newValue, oldValue) {
      (async () => {
        await this.get_disponibilidad();
      })();
    },

    //fin de watchs con mapa
  },
  computed: {
    /**verifico la disponibilidad de la propiedad */
    //aqui estoy

    minimo_financiamiento: function () {
      if (this.form.tipo_financiamiento == 1) {
        return 1;
      } else {
        return 1;
        //return 2;
      }
    },
    maximo_financiamiento: function () {
      if (this.form.tipo_financiamiento == 1) {
        return 1;
      } else {
        return 120;
      }
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

    subtotal_computed: function () {
      let tasa_iva = this.tasa_iva_computed;
      let costo_neto = this.form.costo_neto;
      let descuento = this.form.descuento;
      let subtotal = costo_neto / tasa_iva;
      return subtotal.toFixed(2);
    },

    total_a_pagar_computed: function () {
      return (this.form.costo_neto - this.form.descuento).toFixed(2);
    },

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

    /**maximo valor permitido del enganche */
    minimo_pronto_pago: function () {
      let minimo_pronto_pago = 0;
      if (this.form.financiamiento == 1) {
        minimo_pronto_pago = this.total_a_pagar_computed;
      } else {
        minimo_pronto_pago = 0;
      }

      if (isNaN(minimo_pronto_pago)) {
        return 0;
      } else {
        return minimo_pronto_pago;
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
    tipo_venta: function () {
      //definiendo el tipo de uso, si es true es venta de uso inmediato si es false es venta de uso futuro
      if (this.form.tipo_financiamiento == 1) {
        //uso inmediato
        return true;
      } else {
        //a futuro
        return false;
      }
    },

    capturar_num_convenio: function () {
      if (this.form.ventaAntiguedad.value > 1) {
        return true;
      } else {
        return false;
      }
    },
    capturar_num_titulo: function () {
      if (this.form.ventaAntiguedad.value == 3) {
        return true;
      } else {
        return false;
      }
    },

    //validaciones calculadas
    fila_validacion_computed: function () {
      return this.form.filas.value;
    },
    ubicacion_validacion_computed: function () {
      if (this.form.propiedades_id == 4) {
        //terrazas
        return this.form.lotes.value;
      } else {
        return true;
      }
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
    num_titulo_validacion_computed: function () {
      //checo que el dato venta a futuro este activo
      if (this.form.ventaAntiguedad.value == 3) {
        return this.form.titulo;
      } else return true;
    },

    //fin de validaciones calculadas
    //crear ubicacion
    crear_ubicacion_computed: function () {
      if (this.datosAreas.tipo_propiedades_id == 4) {
        //ubicacion para cuadriplex de terrazas
        //id del tipo de propiedad - id de la propiedad - num fila - num columna
        return (
          this.datosAreas.tipo_propiedades_id +
          "-" +
          this.datosAreas.id +
          "-" +
          this.form.filas.value +
          "-" +
          this.form.lotes.value
        );
      } else {
        //id del tipo de propiedad - id de la propiedad - num fila - 1
        return (
          this.datosAreas.tipo_propiedades_id +
          "-" +
          this.datosAreas.id +
          "-" +
          this.form.filas.value +
          "-" +
          1
        );
      }
    },
    //fin de crear ubicacion
  },
  data() {
    return {
      openReportes :false,
            verAcuse : false,
          id_venta_ver:0,
      disponibilidad:{disponibilidad_terreno:1,cliente:"",fecha_venta:""},
      configdateTimePicker: configdateTimePicker,
      verDisponibilidad: false,
      openBuscador: false,
      idAreaInicial: 1,
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
      filas: [],
      lotes: [],
      vendedores: [],
      //variables con mapa

      /**datos del area seleccionada */
      datosAreas: [],
      /**para modificar, se traen los datos aqui */
      datosVenta: [],
      //fin var con mapa
      form: {
        salarios_minimos: "12",
        id_venta: "",
        /**datos del cliente seleccionado */
        cliente: "seleccione 1 cliente",
        id_cliente: "",
        direccion_cliente: "",
        //ubicacion
        tipo_propiedades_id: 0,
        propiedades_id: 0,
        ubicacion: "",
        //fin de ubicacion
        fecha_venta: "",
        /**titular substituto */
        titular_sustituto: "",
        parentesco_titular_sustituto: "",
        telefono_titular_sustituto: "",
        //
        solicitud: "",
        convenio: "",
        titulo: "",
        tipo_financiamiento: "",
        filas: {
          label: "Seleccione 1",
          value: "",
        },
        lotes: {
          label: "Seleccione 1",
          value: "",
        },

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
    closeListaReportes() {
            this.openReportes = false;
        },


    async get_disponibilidad() {
      try {
        let res = await cementerio.get_ventas({
          ubicacion_raw: this.crear_ubicacion_computed,
        });
        if (res.data.data.length > 0) {
          /**ocupado */
          this.disponibilidad.disponibilidad_terreno = 0;
          this.disponibilidad.cliente = res.data.data[0].nombre;
          this.disponibilidad.fecha_operacion = res.data.data[0].fecha_operacion_texto;
          this.id_venta_ver=res.data.data[0].ventas_terrenos_id;
        } else {
          /**disponible para venta */
          this.disponibilidad.disponibilidad_terreno = 1;
        }
      } catch (error) {
        return error;
      }
    },

    //obtengo los datos del area seleccionada desde el mapa  "child"
    getDatosTipoPropiedad(datos) {
      //actualizo los datos que se ocupan para manejar las ubicaciones de las propiedades
      this.datosAreas = datos;
      this.form.propiedades_id = this.datosAreas.tipo_propiedades_id;
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
            /**aqui se hace la validacion en los totales de la venta */
            //se confirma la cntraseña
            //una vez todo validado, actualizo los ultimos datos de ubicacion
            this.form.propiedades_id = this.datosAreas.id;
            this.form.tipo_propiedades_id = this.datosAreas.tipo_propiedades_id;
            this.form.ubicacion = this.crear_ubicacion_computed;
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
        .catch(() => {});
    },

    async guardar_venta() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await cementerio.guardar_venta(this.form);
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
            if (this.errores.ubicacion) {
              //la propiedad esa ya ha sido vendida
              this.$vs.notify({
                title: "Seleccionar Terreno",
                text: "Este terreno ya ha sido vendido previamente.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            }

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
        let res = await cementerio.modificar_venta(this.form);
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
      this.form.ventaAntiguedad = this.ventasAntiguedad[0];
      //area de la terraza
      this.form.lotes = this.lotes[0];
      this.form.tipo_financiamiento = "";
      this.form.solicitud = "";
      this.form.convenio = "";
      this.form.titulo = "";
      this.form.titular_sustituto = "";
      this.form.parentesco_titular_sustituto = "";
      this.form.telefono_titular_sustituto = "";
      this.form.cliente = "";
      this.form.id_cliente = "";
      this.form.direccion_cliente = "";
      this.form.beneficiarios = [];
      this.form.fecha_venta = "";
      this.form.vendedor = { label: "Seleccione 1", value: "" };
      this.form.tasa_iva = 16;
      this.form.financiamiento = "";
      this.form.costo_neto = "";
      this.form.descuento = 0;
      this.form.pago_inicial = "";
      this.idAreaInicial = 0;
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
        let res = await cementerio.consultar_venta_id(this.form.id_venta);
        this.datosVenta = res.data[0];

        /**actualizo el tipo_financimiamiento para que cargue los planes */
        this.form.tipo_financiamiento =
          this.datosVenta.venta_terreno.tipo_financiamiento;
        this.idAreaInicial = this.datosVenta.venta_terreno.propiedades_id;
        /**se comienza a llenar la informacion de los datos */
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
          if (element.value == this.datosVenta.venta_terreno.vendedor.id) {
            this.form.vendedor = element;
          }
        });
        if (this.form.vendedor.value == "") {
          let vendedor = {
            value: this.datosVenta.venta_terreno.vendedor.id,
            label:
              "(" +
              this.datosVenta.venta_terreno.vendedor.nombre +
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
        this.form.titulo = this.datosVenta.numero_titulo;

        /**datos del titular sustituto */
        this.form.titular_sustituto = this.datosVenta.titular_sustituto;
        this.form.parentesco_titular_sustituto =
          this.datosVenta.parentesco_titular_sustituto;
        this.form.telefono_titular_sustituto =
          this.datosVenta.telefono_titular_sustituto;

        /**beneficairios */
        this.form.beneficiarios = this.datosVenta.beneficiarios;
        this.form.financiamiento = this.datosVenta.financiamiento;

        this.form.tasa_iva =
          Number(this.datosVenta.tasa_iva) <= 0 ? 16 : this.datosVenta.tasa_iva;
        this.form.costo_neto = this.datosVenta.costo_neto_calculado;
        this.form.descuento = this.datosVenta.descuento_neto_calculado;

        this.form.pago_inicial =
          this.datosVenta.pagos_programados[0].monto_programado;
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
          title: "Seleccionar Área del Cementerio",
          text: "No está permitido cambiar la ubicación de la propiedad mientras no esté al corriente con los pagos establecidos.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "10000",
        });
      } else if (this.ventaLiquidada) {
        this.$vs.notify({
          title: "Seleccionar Área del Cementerio",
          text: "No está permitido cambiar la ubicación de la propiedad una vez ya liquidado el total de la cuenta.",
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
  created() {},
};
</script>
