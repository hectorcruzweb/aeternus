<template>
  <div class="centerx">
    <vs-popup
      fullscreen
      close="cancelar"
      :title="
        getTipoformulario == 'facturar' ? 'Emitir CFDI' : 'POR DEFINIR FUNCION'
      "
      :active.sync="showVentana"
      ref="formulario"
      class="forms-popup"
    >
      <div class="flex flex-wrap">
        <div class="w-full py-6">
          <!--Contenido del plan-->
          <div class="form-group">
            <div class="title-form-group">Datos del Comprobante</div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <!--INICIO DE FORM TIPO-->
                <div class="w-full xl:w-2/12 px-2 input-text">
                  <label>
                    Tipo de Comprobante
                    <span>(*)</span>
                  </label>
                  <v-select
                    data-vv-scope="form"
                    v-validate:tipo_comprobante_validacion_computed.immediate="
                      'required'
                    "
                    :options="tipos_comprobante"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.tipo_comprobante"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="tipo_comprobante"
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <span v-if="errors.first('form.tipo_comprobante')">
                    Ingrese este dato
                  </span>
                  <span v-if="this.errores['tipo_comprobante.value']">{{
                    errores["tipo_comprobante.value"][0]
                  }}</span>
                </div>
                <div class="w-full xl:w-5/12 px-2 input-text">
                  <label>
                    Método de Pago
                    <span>(*)</span>
                  </label>
                  <v-select
                    data-vv-scope="form"
                    v-validate:metodo_pago_validacion_computed.immediate="
                      'required'
                    "
                    :disabled="form.tipo_comprobante.value > 1"
                    :options="metodos_pago"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.metodo_pago"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="metodo_pago"
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <span
                    class="text-danger"
                    v-if="errors.first('form.metodo_pago')"
                  >
                    Ingrese el método de pago
                  </span>
                  <span
                    class="text-danger"
                    v-if="this.errores['metodo_pago.value']"
                    >{{ errores["metodo_pago.value"][0] }}</span
                  >
                </div>
                <div class="w-full xl:w-5/12 px-2 input-text">
                  <label>
                    Forma de Pago
                    <span>(*)</span>
                  </label>
                  <v-select
                    data-vv-scope="form"
                    v-validate:forma_pago_validacion_computed.immediate="
                      'required'
                    "
                    :disabled="form.metodo_pago.value == 2"
                    :options="formas_pago"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.forma_pago"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="forma_pago"
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <span
                    class="text-danger"
                    v-if="errors.first('form.forma_pago')"
                  >
                    Ingrese la forma de pago
                  </span>
                  <span
                    class="text-danger"
                    v-if="this.errores['forma_pago.value']"
                    >{{ errores["forma_pago.value"][0] }}</span
                  >
                </div>

                <div
                  class="w-full xl:w-2/12 px-2 input-text"
                  v-if="form.tipo_comprobante.value == 5"
                >
                  <label>
                    Fecha del Pago
                    <span>(*)</span>
                  </label>
                  <flat-pickr
                    data-vv-scope="form"
                    name="fecha_pago"
                    data-vv-as=" "
                    v-validate:fechapago_validacion_computed.immediate="
                      'required'
                    "
                    :config="configdateTimePicker"
                    v-model="form.fecha_pago"
                    placeholder="Fecha del Pago"
                    class="w-full my-1"
                  />
                  <span
                    class="text-danger"
                    v-if="errors.first('form.fecha_pago')"
                  >
                    Fecha de pago
                  </span>
                  <span class="text-danger" v-if="this.errores.fecha_pago">{{
                    errores.fecha_pago[0]
                  }}</span>
                </div>

                <div class="w-full xl:w-2/12 px-2 input-text" v-else>
                  <label> Fecha del Pago </label>
                  <vs-input
                    :disabled="true"
                    value="N/A"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                  />
                </div>
                <div class="w-full xl:w-5/12 px-2 input-text">
                  <label>
                    Uso del CFDI
                    <span v-if="form.tipo_comprobante.value == 1">(*)</span>
                  </label>
                  <v-select
                    data-vv-scope="form"
                    v-validate:uso_cfdi_validacion_computed.immediate="
                      'required'
                    "
                    :disabled="form.tipo_comprobante.value == 5"
                    :options="usos_cfdi"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.uso_cfdi"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="usos_cfdi"
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <span
                    class="text-danger"
                    v-if="errors.first('form.usos_cfdi')"
                  >
                    Seleccione el uso del CFDI
                  </span>
                  <span
                    class="text-danger"
                    v-if="this.errores['usos_cfdi.value']"
                    >{{ errores["usos_cfdi.value"][0] }}</span
                  >
                </div>

                <div class="w-full xl:w-5/12 px-2 input-text">
                  <label>
                    Tipo de Relación
                    <span>(*)</span>
                  </label>
                  <v-select
                    data-vv-scope="form"
                    v-validate:tipo_relacion_validacion_computed.immediate="
                      'required'
                    "
                    :options="tipos_relacion"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.tipo_relacion"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="tipo_relacion"
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <span
                    class="text-danger"
                    v-if="errors.first('form.tipo_relacion')"
                  >
                    Seleccione el uso del CFDI
                  </span>
                </div>

                <!--FIN FORM TIPO-->
              </div>
            </div>
          </div>
        </div>

        <div class="w-full py-6">
          <!--Contenido del receptor-->
          <div class="form-group">
            <div class="title-form-group">Datos del Receptor</div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div
                  class="w-full px-2"
                  v-if="cliente_datos_validos_cfdi != null"
                >
                  <div
                    class="w-full alerta pt-4 pb-6 px-2"
                    v-if="cliente_datos_validos_cfdi == true"
                  >
                    <div class="info">
                      <p>El cliente tiene sus datos en orden para facturas.</p>
                    </div>
                  </div>
                  <div class="w-full alerta pt-4 pb-6 px-2" v-else>
                    <div class="danger">
                      <p>
                        Actualice la información del cliente si requiere
                        facturar con su RFC. Hágalo desde el módulo de clientes.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="w-full xl:w-6/12">
                  <div
                    class="w-full px-2 input-text mt-1"
                    v-if="form.id_cliente == ''"
                  >
                    <label>
                      Receptor
                      <span>(*)</span>
                    </label>
                    <div
                      class="bg-danger-50 text-center py-2 px-2 size-base border-danger-solid-1 cursor-pointer color-danger-900"
                      @click="openBuscadorCliente = true"
                    >
                      Click para seleccionar al Receptor
                    </div>
                  </div>
                  <div class="w-full px-2 input-text mt-1" v-else>
                    <label>
                      Receptor
                      <span>(*)</span>
                    </label>
                    <div
                      class="bg-success-50 py-2 px-2 size-base border-success-solid-2 uppercase"
                    >
                      <div class="flex flex-wrap">
                        <div class="w-full xl:w-8/12">
                          <span class="font-medium"> Clave: </span>
                          {{ form.id_cliente }},
                          <span class="font-medium"> Nombre: </span>
                          {{ form.cliente }}
                          <span class="font-medium hidden"> Dirección: </span>
                        </div>
                        <div class="w-full xl:w-4/12 text-center xl:text-right">
                          <span
                            @click="quitarCliente()"
                            class="color-danger-900 cursor-pointer"
                            >X Cambiar Receptor
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="px-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['id_cliente']"
                      >{{ errores["id_cliente"][0] }}</span
                    >
                  </div>
                </div>

                <div class="w-full xl:w-3/12 px-2 input-text">
                  <label>
                    Tipo de RFC
                    <span>(*)</span>
                  </label>
                  <v-select
                    :options="tipos_rfc"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.tipo_rfc"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="tipo_rfc"
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <span class="text-danger">
                    {{ errors.first("form.tipo_rfc") }}
                  </span>
                  <span
                    class="text-danger"
                    v-if="this.errores['tipo_rfc.value']"
                    >{{ errores["tipo_rfc.value"][0] }}</span
                  >
                </div>

                <div
                  class="w-full xl:w-3/12 px-2 input-text"
                  v-if="form.tipo_rfc.value == 1"
                >
                  <label>
                    RFC del Cliente
                    <span>(*)</span>
                  </label>
                  <vs-input
                    data-vv-scope="form"
                    v-validate.disabled="'required'"
                    name="rfc"
                    maxlength="13"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ingrese el RFC"
                    v-model="form.rfc"
                    :disabled="true"
                  />
                  <span class="text-danger" v-if="errors.first('form.rfc')">
                    Ingrese un RFC
                  </span>
                  <span class="text-danger" v-if="this.errores.rfc">{{
                    errores.rfc[0]
                  }}</span>
                </div>

                <div
                  class="w-full xl:w-3/12 px-2 input-text"
                  v-else-if="form.tipo_rfc.value == 2"
                >
                  <label>
                    RFC Púb. En General
                    <span>(*)</span>
                  </label>
                  <vs-input
                    :disabled="true"
                    name="rfc"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ingrese el RFC"
                    value="XAXX010101000"
                  />
                  <span class="text-danger" v-if="this.errores.rfc">{{
                    errores.rfc[0]
                  }}</span>
                </div>

                <div class="w-full xl:w-3/12 px-2 input-text" v-else>
                  <label>
                    RFC Púb. En General Extranjero
                    <span>(*)</span>
                  </label>
                  <vs-input
                    :disabled="true"
                    name="rfc"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ingrese el RFC"
                    value="XEX010101000"
                  />
                  <span class="text-danger" v-if="this.errores.rfc">{{
                    errores.rfc[0]
                  }}</span>
                </div>
                <div
                  class="w-full xl:w-6/12 px-2 input-text"
                  v-if="form.tipo_rfc.value == 1"
                >
                  <label>
                    Razón Social
                    <span>(*)</span>
                  </label>
                  <vs-input
                    data-vv-scope="form"
                    name="razon_social"
                    v-validate.disabled="'required'"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Razón social del contribuyente"
                    v-model="form.razon_social"
                    :disabled="true"
                  />
                  <span
                    class="text-danger"
                    v-if="errors.first('form.razon_social')"
                  >
                    Ingrese la razón social
                  </span>
                  <span class="text-danger" v-if="this.errores.razon_social">{{
                    errores.razon_social[0]
                  }}</span>
                </div>

                <div class="w-full xl:w-6/12 px-2 input-text" v-else>
                  <label>
                    Razón Social
                    <span>(*)</span>
                  </label>
                  <vs-input
                    :disabled="true"
                    name="razon_social"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Razón social del contribuyente"
                    value="Público en General"
                  />
                  <span class="text-danger" v-if="this.errores.razon_social">{{
                    errores.razon_social[0]
                  }}</span>
                  <span class="text-danger" v-if="this.errores.razon_social">{{
                    errores.razon_social[0]
                  }}</span>
                </div>

                <div
                  class="w-full xl:w-6/12 px-2 input-text"
                  v-if="form.tipo_rfc.value == 1"
                >
                  <label>
                    Dirección Fiscal
                    <span>(*)</span>
                  </label>
                  <vs-input
                    name="direccion_fiscal"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Dirección fiscal del contribuyente"
                    v-model="form.direccion_fiscal"
                    :disabled="true"
                  />
                  <span class="text-danger" v-if="this.errores.razon_social">{{
                    errores.razon_social[0]
                  }}</span>
                  <span class="text-danger" v-if="this.errores.razon_social">{{
                    errores.razon_social[0]
                  }}</span>
                </div>

                <div class="w-full xl:w-6/12 px-2 input-text" v-else>
                  <label>
                    Dirección Fiscal
                    <span>(*)</span>
                  </label>
                  <vs-input
                    :disabled="true"
                    name="direccion_fiscal"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Dirección fiscal del contribuyente"
                    value="N/A"
                  />
                </div>
                <div class="w-full xl:w-6/12 px-2 input-text">
                  <label>
                    País de Residencia
                    <span>(*)</span>
                  </label>
                  <v-select
                    data-vv-scope="form"
                    :disabled="form.tipo_rfc.value < 3"
                    :options="sat_paises"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.sat_pais"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="sat_pais"
                    v-validate:sat_pais_validacion_computed.immediate="
                      'required'
                    "
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <span
                    class="text-danger"
                    v-if="errors.first('form.sat_pais')"
                  >
                    Seleccione el país de residencia
                  </span>
                  <span
                    class="text-danger"
                    v-if="this.errores['sat_pais.value']"
                    >{{ errores["sat_pais.value"][0] }}</span
                  >
                </div>

                <div
                  class="w-full xl:w-6/12 px-2 input-text"
                  v-if="form.tipo_rfc.value == 1"
                >
                  <label>
                    Código Postal Fiscal
                    <span>(*)</span>
                  </label>
                  <vs-input
                    name="direccion_fiscal_cp"
                    maxlength="8"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="CP de dirección fiscal del contribuyente"
                    v-model="form.direccion_fiscal_cp"
                    :disabled="true"
                  />
                  <span
                    class="text-danger"
                    v-if="this.errores.direccion_fiscal_cp"
                    >{{ errores.direccion_fiscal_cp[0] }}</span
                  >
                  <span
                    class="text-danger"
                    v-if="this.errores.direccion_fiscal_cp"
                    >{{ errores.direccion_fiscal_cp[0] }}</span
                  >
                </div>

                <div class="w-full xl:w-6/12 px-2 input-text" v-else>
                  <label>
                    Código Postal Fiscal
                    <span>(*)</span>
                  </label>
                  <vs-input
                    :disabled="true"
                    name="direccion_fiscal_cp"
                    maxlength="8"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="CP de dirección fiscal del contribuyente"
                    value="N/A"
                  />
                </div>

                <div class="w-full xl:w-12/12 px-2 input-text">
                  <label>
                    Régimen Fiscal
                    <span>(*)</span>
                  </label>
                  <v-select
                    :options="regimenes"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.regimen"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="regimen"
                    :disabled="true"
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <span class="text-danger">
                    {{ errors.first("form.regimen") }}
                  </span>
                  <span
                    class="text-danger"
                    v-if="this.errores['regimen.value']"
                    >{{ errores["regimen.value"][0] }}</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="w-full py-6" v-if="form.tipo_relacion.value > 0">
          <!--Contenido del receptor-->
          <div class="form-group">
            <div class="title-form-group">
              CFDIS relacionados con el documento a generar
            </div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div class="w-full text-right">
                  <vs-button
                    class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                    color="primary"
                    @click="openBuscadorCfdiPagar('relacionar')"
                  >
                    <span>Buscar CFDI</span>
                  </vs-button>
                </div>
                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12"
                    >
                      <div class="w-full mt-5">
                        <vs-table
                          class="tabla-datos"
                          :data="form.cfdis_relacionados"
                          noDataText="No se han agregado documentos a relacionar"
                        >
                          <template slot="header">
                            <h3>Facturas Relacionadas al CFDI</h3>
                          </template>
                          <template slot="thead">
                            <vs-th>#</vs-th>
                            <vs-th>Folio</vs-th>
                            <vs-th>UUID</vs-th>
                            <vs-th hidden>Cliente</vs-th>
                            <vs-th hidden>Fecha Timbrado</vs-th>
                            <vs-th>RFC</vs-th>
                            <vs-th>$ Total</vs-th>
                            <vs-th>$ Saldo Actual</vs-th>
                            <vs-th>Tipo</vs-th>
                            <vs-th>Método de Págo</vs-th>
                            <vs-th>Ver PDF</vs-th>
                            <vs-th>Quitar</vs-th>
                          </template>
                          <template slot-scope="{ data }">
                            <vs-tr
                              :data="tr"
                              :key="indextr"
                              v-for="(tr, indextr) in data"
                            >
                              <vs-td>
                                <div class="capitalize">
                                  <span class="lowercase">{{
                                    indextr + 1
                                  }}</span>
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].id }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].uuid }}
                                </div>
                              </vs-td>
                              <vs-td hidden>
                                <div class="capitalize">
                                  {{ data[indextr].cliente_nombre }}
                                </div>
                              </vs-td>
                              <vs-td hidden>
                                <div class="capitalize">
                                  {{ data[indextr].fecha_timbrado_texto }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].rfc_receptor }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{
                                    data[indextr].total | numFormat("0,000.00")
                                  }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{
                                    data[indextr].saldo_cfdi
                                      | numFormat("0,000.00")
                                  }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].tipo_comprobante_texto }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].sat_metodos_pago_texto }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div
                                  class=""
                                  @click="
                                    openReporte(
                                      'Ver CFDI',
                                      '/facturacion/get_cfdi_pdf/',
                                      data[indextr],
                                      'cfdi'
                                    )
                                  "
                                >
                                  <img
                                    class="cursor-pointer img-btn"
                                    src="@assets/images/pdf.svg"
                                  />
                                </div>
                              </vs-td>
                              <vs-td>
                                <div
                                  class=""
                                  @click="remover_cfdi_a_relacionar(indextr)"
                                >
                                  <img
                                    class="cursor-pointer img-btn"
                                    src="@assets/images/minus.svg"
                                  />
                                </div>
                              </vs-td>
                            </vs-tr>
                          </template>
                        </vs-table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!---cfdis a pagar-->
        <div class="w-full py-6" v-if="form.tipo_comprobante.value == 5">
          <!--Contenido del receptor-->
          <div class="form-group">
            <div class="title-form-group">CFDIS a Pagar</div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div class="w-full text-right">
                  <vs-button
                    class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                    color="primary"
                    @click="openBuscadorCfdiPagar('pagar')"
                  >
                    <span>Buscar CFDI</span>
                  </vs-button>
                </div>
                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12"
                    >
                      <div class="w-full mt-5">
                        <vs-table
                          class="tabla-datos"
                          :data="form.cfdis_a_pagar"
                          noDataText="No se han agregado documentos a relacionar"
                        >
                          <template slot="header">
                            <h3>Facturas Relacionadas al CFDI Para Pagar</h3>
                          </template>
                          <template slot="thead">
                            <vs-th>#</vs-th>
                            <vs-th>Folio</vs-th>
                            <vs-th>UUID</vs-th>
                            <vs-th hidden>Cliente</vs-th>
                            <vs-th hidden>Fecha Timbrado</vs-th>
                            <vs-th>RFC</vs-th>
                            <vs-th>$ Total</vs-th>
                            <vs-th>$ Saldo Actual</vs-th>
                            <vs-th>$ Monto a Pagar</vs-th>
                            <vs-th>$ Nuevo Saldo</vs-th>
                            <vs-th>Ver PDF</vs-th>
                            <vs-th>Quitar</vs-th>
                          </template>
                          <template slot-scope="{ data }">
                            <vs-tr
                              :data="tr"
                              :key="indextr"
                              v-for="(tr, indextr) in data"
                            >
                              <vs-td>
                                <div class="capitalize">
                                  <span class="lowercase">{{
                                    indextr + 1
                                  }}</span>
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].id }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].uuid }}
                                </div>
                              </vs-td>
                              <vs-td hidden>
                                <div class="capitalize">
                                  {{ data[indextr].cliente_nombre }}
                                </div>
                              </vs-td>
                              <vs-td hidden>
                                <div class="capitalize">
                                  {{ data[indextr].fecha_timbrado_texto }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].rfc_receptor }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{
                                    data[indextr].total | numFormat("0,000.00")
                                  }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{
                                    data[indextr].saldo_cfdi
                                      | numFormat("0,000.00")
                                  }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <vs-input
                                  data-vv-scope="form"
                                  :name="'pago_cfdi' + indextr"
                                  data-vv-as=" "
                                  data-vv-validate-on="blur"
                                  v-validate="
                                    'required|decimal:2|min_value:' +
                                    0.01 +
                                    '|max_value:' +
                                    form.cfdis_a_pagar[indextr].saldo_cfdi
                                  "
                                  class="w-full sm:w-6/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mr-auto ml-auto mt-1 cantidad"
                                  maxlength="8"
                                  v-model="
                                    form.cfdis_a_pagar[indextr].monto_pago
                                  "
                                />
                                <div>
                                  <span class="text-danger text-xs">
                                    {{
                                      errors.first("form.pago_cfdi" + indextr)
                                    }}
                                  </span>
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{
                                    (data[indextr].saldo_cfdi -
                                      data[indextr].monto_pago)
                                      | numFormat("0,000.00")
                                  }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div
                                  class=""
                                  @click="
                                    openReporte(
                                      'Ver CFDI',
                                      '/facturacion/get_cfdi_pdf/',
                                      data[indextr],
                                      'cfdi'
                                    )
                                  "
                                >
                                  <img
                                    class="cursor-pointer img-btn"
                                    src="@assets/images/pdf.svg"
                                  />
                                </div>
                              </vs-td>
                              <vs-td>
                                <div
                                  class=""
                                  @click="remover_cfdi_a_pagar(indextr)"
                                >
                                  <img
                                    class="cursor-pointer img-btn"
                                    src="@assets/images/minus.svg"
                                  />
                                </div>
                              </vs-td>
                            </vs-tr>
                          </template>
                        </vs-table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!---conceptos operaciones-->

        <div class="w-full py-6" v-if="form.tipo_comprobante.value == 1">
          <!--Contenido del receptor-->
          <div class="form-group">
            <div class="title-form-group">Operaciones Relacionadas al CFDI</div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div class="w-full text-right">
                  <vs-button
                    class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                    color="primary"
                    @click="openBuscadorOperacion = true"
                  >
                    <span>Buscar Operación</span>
                  </vs-button>
                </div>
                <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12"
                    >
                      <div class="w-full mt-5">
                        <vs-table
                          class="tabla-datos"
                          :data="form.operaciones_relacionadas"
                          noDataText="No se han agregado conceptos a facturar"
                        >
                          <template slot="header">
                            <h3>Lista de Operaciones a Facturar</h3>
                          </template>
                          <template slot="thead">
                            <vs-th>#</vs-th>
                            <vs-th>Número de operacion</vs-th>
                            <vs-th>Tipo</vs-th>
                            <vs-th>Cliente</vs-th>
                            <vs-th>Fecha</vs-th>
                            <vs-th>Quitar</vs-th>
                          </template>
                          <template slot-scope="{ data }">
                            <vs-tr
                              :data="tr"
                              :key="indextr"
                              v-for="(tr, indextr) in data"
                            >
                              <vs-td>
                                <div class="capitalize">
                                  <span class="lowercase">{{
                                    indextr + 1
                                  }}</span>
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].clave_operacion_por_tipo }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].tipo_operacion_texto }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].cliente }}
                                </div>
                              </vs-td>
                              <vs-td>
                                <div class="capitalize">
                                  {{ data[indextr].fecha_operacion_texto }}
                                </div>
                              </vs-td>

                              <vs-td>
                                <div
                                  class=""
                                  @click="remover_operacion(indextr)"
                                >
                                  <img
                                    class="cursor-pointer img-btn"
                                    src="@assets/images/minus.svg"
                                  />
                                </div>
                              </vs-td>
                            </vs-tr>
                          </template>
                        </vs-table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="cfdi-contenido">
        <div
          class="flex flex-wrap"
          v-if="
            form.tipo_comprobante.value != 5 && form.tipo_comprobante.value > 0
          "
        >
          <div class="w-full">
            <!--Contenido del plan-->
            <div class="form-group">
              <div class="title-form-group">Conceptos del CFDI</div>
              <div class="form-group-content">
                <div class="flex flex-wrap">
                  <!--INICIO DE FORM CONCEPTOS-->
                  <div class="w-full xl:w-6/12 px-2 input-text">
                    <label>
                      Clave de Producto o Servicio
                      <span>(*)</span>
                    </label>
                    <v-select
                      data-vv-scope="conceptos"
                      :options="claves_sat"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.clave_sat"
                      class="mb-4 sm:mb-0 pb-1 pt-1"
                      name="clave_sat"
                      v-validate.disable="'required'"
                      v-validate:clave_sat_validacion_computed.immediate="
                        'required'
                      "
                    >
                      <div slot="no-options">Seleccione 1</div>
                    </v-select>
                    <span
                      class="text-danger"
                      v-if="errors.first('conceptos.clave_sat')"
                    >
                      Seleccione una clave del Sat
                    </span>
                    <span
                      class="text-danger"
                      v-if="this.errores['clave_sat.value']"
                      >{{ errores["clave_sat.value"][0] }}</span
                    >
                  </div>

                  <div class="w-full xl:w-4/12 px-2 input-text">
                    <label>
                      Clave de Unidad
                      <span>(*)</span>
                    </label>
                    <v-select
                      data-vv-scope="conceptos"
                      :options="unidades_sat"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.unidad_sat"
                      class="mb-4 sm:mb-0 pb-1 pt-1"
                      name="unidad_sat"
                      v-validate:unidad_sat_validacion_computed.immediate="
                        'required'
                      "
                    >
                      <div slot="no-options">Seleccione 1</div>
                    </v-select>
                    <span
                      class="text-danger"
                      v-if="errors.first('conceptos.unidad_sat')"
                    >
                      Seleccione la unidad del concepto
                    </span>
                    <span
                      class="text-danger"
                      v-if="this.errores['unidad_sat.value']"
                      >{{ errores["unidad_sat.value"][0] }}</span
                    >
                  </div>

                  <div class="w-full xl:w-2/12 px-2 input-text">
                    <label>
                      Cantidad
                      <span>(*)</span>
                    </label>
                    <vs-input
                      ref="cantidad_agregar"
                      data-vv-as="cantidad"
                      data-vv-scope="conceptos"
                      name="cantidad"
                      maxlength="6"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Cantidad a agregar"
                      v-model="form.cantidad"
                      v-validate="'required|integer|min_value:' + 1"
                    />
                    <span class="text-danger">
                      {{ errors.first("conceptos.cantidad") }}
                    </span>
                    <span class="text-danger" v-if="this.errores.cantidad">{{
                      errores.cantidad[0]
                    }}</span>
                  </div>
                  <div class="w-full xl:w-12/12 px-2 input-text">
                    <label>
                      Descripción
                      <span>(*)</span>
                    </label>
                    <vs-input
                      data-vv-as="Descripción"
                      data-vv-scope="conceptos"
                      name="descripcion"
                      maxlength="150"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Descripción del concepto"
                      v-validate="'required'"
                      v-model="form.descripcion"
                    />
                    <span class="text-danger">
                      {{ errors.first("conceptos.descripcion") }}
                    </span>
                    <span class="text-danger" v-if="this.errores.descripcion">{{
                      errores.descripcion[0]
                    }}</span>
                  </div>

                  <div class="w-full xl:w-4/12 px-2 input-text">
                    <label>
                      $ Precio Neto
                      <span>(*)</span>
                    </label>
                    <vs-input
                      data-vv-as="Precio Neto"
                      data-vv-scope="conceptos"
                      name="precio_neto"
                      maxlength="10"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Ingrese el precio neto"
                      v-model="form.precio_neto"
                      v-validate="'required|decimal:2|min_value:' + 0"
                    />
                    <span class="text-danger">
                      {{ errors.first("conceptos.precio_neto") }}
                    </span>
                    <span class="text-danger" v-if="this.errores.precio_neto">{{
                      errores.precio_neto[0]
                    }}</span>
                  </div>

                  <div class="w-full xl:w-4/12 px-2 input-text">
                    <label>
                      ¿Aplica Descuento?
                      <span>(*)</span>
                    </label>
                    <v-select
                      :disabled="form.tipo_comprobante.value == 2"
                      data-vv-scope="conceptos"
                      v-validate:descuento_b_validacion_computed.immediate="
                        'required'
                      "
                      name="descuento_b"
                      :options="sino"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.descuento_b"
                      class="mb-4 sm:mb-0 pb-1 pt-1"
                    >
                      <div slot="no-options">Seleccione 1</div>
                    </v-select>
                    <span
                      class="text-danger"
                      v-if="errors.first('conceptos.descuento_b')"
                    >
                      Seleccione una opción
                    </span>
                    <span class="text-danger" v-if="this.errores.precio_neto">{{
                      errores.precio_neto[0]
                    }}</span>
                  </div>

                  <div class="w-full xl:w-4/12 px-2 input-text">
                    <label>
                      $ Precio con Descuento
                      <span>(*)</span>
                    </label>
                    <vs-input
                      :disabled="form.descuento_b.value == 1 ? false : true"
                      data-vv-as="Precio con descuento"
                      data-vv-scope="conceptos"
                      name="precio_descuento"
                      maxlength="10"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Precio con el descuento"
                      v-model="form.precio_descuento"
                      v-validate="
                        form.tipo_comprobante.value == 1 &&
                        form.descuento_b.value == 0
                          ? 'required|'
                          : '' +
                            'decimal:2|min_value:' +
                            0 +
                            '|max_value:' +
                            form.precio_neto
                      "
                    />
                    <span class="text-danger">
                      {{ errors.first("conceptos.precio_descuento") }}
                    </span>
                    <span
                      class="text-danger"
                      v-if="this.errores.precio_descuento"
                      >{{ errores.precio_descuento[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="w-full text-right pt-4 md:mt-0">
            <vs-button
              class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
              color="success"
              @click="AgregarConcepto"
              v-if="!ModificandoArticulo"
            >
              <span>Agregar Concepto</span>
            </vs-button>

            <vs-button
              class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
              color="danger"
              @click="CancelarModificacion"
              v-if="ModificandoArticulo"
            >
              <span>Cancelar Modificación</span>
            </vs-button>

            <vs-button
              class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
              color="success"
              @click="ActualizarModificacion"
              v-if="ModificandoArticulo"
            >
              <span>Modificar Concepto</span>
            </vs-button>
          </div>

          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                <div class="w-full mt-5">
                  <vs-table
                    class="tabla-datos"
                    :data="form.conceptos"
                    noDataText="No se han agregado conceptos a facturar"
                  >
                    <template slot="header">
                      <h3>Artículos y Servicios a Facturar</h3>
                    </template>
                    <template slot="thead">
                      <vs-th>#</vs-th>
                      <vs-th>Prod/Servicio SAT</vs-th>
                      <vs-th>Unidad</vs-th>
                      <vs-th>Descripcion</vs-th>
                      <vs-th>$ Costo Neto</vs-th>
                      <vs-th>$ Descuento</vs-th>
                      <vs-th>Cantidad</vs-th>
                      <vs-th>$ Importe</vs-th>
                      <vs-th>Modificar</vs-th>
                      <vs-th>Quitar</vs-th>
                    </template>
                    <template slot-scope="{ data }">
                      <vs-tr
                        :data="tr"
                        :key="indextr"
                        v-for="(tr, indextr) in data"
                      >
                        <vs-td>
                          <div class="capitalize">
                            <span class="lowercase">{{ indextr + 1 }}</span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].clave_sat.label }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].unidad_sat.label }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].descripcion }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            <span>
                              {{
                                data[indextr].precio_neto
                                  | numFormat("0,000.00")
                              }}
                            </span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            <span v-if="data[indextr].descuento_b.value == 0">
                              {{ 0 | numFormat("0,000.00") }}
                            </span>
                            <span v-else>
                              {{
                                (data[indextr].precio_neto -
                                  data[indextr].precio_descuento)
                                  | numFormat("0,000.00")
                              }}
                            </span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].cantidad }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            <span v-if="data[indextr].descuento_b.value == 0">
                              {{
                                (data[indextr].precio_neto *
                                  data[indextr].cantidad)
                                  | numFormat("0,000.00")
                              }}
                            </span>
                            <span v-else>
                              {{
                                (data[indextr].precio_descuento *
                                  data[indextr].cantidad)
                                  | numFormat("0,000.00")
                              }}
                            </span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <div
                            v-if="data[indextr].modifica_b == 1"
                            class=""
                            @click="CargarModificarConcepto(indextr)"
                          >
                            <img
                              class="cursor-pointer img-btn"
                              src="@assets/images/edit.svg"
                            />
                          </div>
                          <div v-else>N/A</div>
                        </vs-td>
                        <vs-td>
                          <div class="" @click="remover_concepto(indextr)">
                            <img
                              class="cursor-pointer img-btn"
                              src="@assets/images/minus.svg"
                            />
                          </div>
                        </vs-td>
                      </vs-tr>
                    </template>
                  </vs-table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="w-full px-2"
          v-if="
            form.tipo_comprobante.value != 5 && form.tipo_comprobante.value > 0
          "
        >
          <vs-divider />
        </div>
        <div class="w-full px-2 mt-10" v-else></div>
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
            <vx-card no-radius>
              <!--checkout-->
              <div class="w-full">
                <div class="flex flex-wrap my-6">
                  <div class="w-full px-2">
                    <vs-divider />
                  </div>
                  <div class="w-full sm:w-12/12 md:w-8/12 lg:9/12">
                    <div class="flex flex-wrap">
                      <div class="w-full pt-3 pb-3 px-2">
                        <div class="float-left">
                          <img
                            class="float-left"
                            width="36px"
                            src="@assets/images/notas_add.svg"
                          />
                          <h3
                            class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                          >
                            Notas / Observaciones Sobre la Factura
                          </h3>
                        </div>
                      </div>
                      <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                      >
                        <label class="text-sm opacity-75 font-bold"
                          >NOTA U OBSERVACIÓN:</label
                        >
                        <vs-textarea
                          height="240px"
                          maxlength="350"
                          size="large"
                          ref="nota"
                          type="text"
                          class="w-full pt-3 pb-3 mt-1 large_textarea"
                          placeholder="Ingrese una nota..."
                          v-model.trim="form.nota"
                        />
                      </div>
                    </div>
                    <!--fin del resumen de la venta-->
                  </div>
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:3/12 px-2">
                    <div class="flex flex-wrap">
                      <div class="w-full pt-3 pb-3 px-2">
                        <div class="float-left">
                          <img
                            class="float-left"
                            width="36px"
                            src="@assets/images/payments.svg"
                          />
                          <h3
                            class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                          >
                            Total del Comprobante
                          </h3>
                        </div>
                      </div>
                    </div>
                    <div class="flex flex-wrap">
                      <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 text-center"
                      >
                        <label class="text-xl opacity-75">
                          Tasa IVA %
                          <span class="texto-importante">(*)</span>
                        </label>
                        <vs-input
                          data-vv-scope="form"
                          :disabled="true"
                          size="large"
                          name="tasa_iva"
                          data-vv-as=" "
                          v-validate="
                            'required|decimal:2|min_value:16|max_value:16'
                          "
                          type="text"
                          class="w-full pb-1 pt-1 texto-bold cantidad"
                          placeholder="Porcentaje IVA"
                          v-model="form.tasa_iva"
                          maxlength="2"
                        />
                        <div>
                          <span
                            class="mensaje-requerido"
                            v-if="errors.first('form.tasa_iva')"
                          >
                            Ingrese la tasa del IVA (%)
                          </span>
                        </div>
                        <div class="mt-2">
                          <span
                            class="mensaje-requerido"
                            v-if="this.errores.tasa_iva"
                            >{{ errores.tasa_iva[0] }}</span
                          >
                        </div>
                      </div>

                      <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 text-center"
                      >
                        <label class="text-xl opacity-75"
                          >$ Total a Facturar</label
                        >
                        <div class="mt-3 text-center">
                          <span class="total_contrato text-3xl font-bold">
                            $
                            {{ total_facturar | numFormat("0,000.00") }}
                          </span>
                        </div>
                      </div>

                      <div class="w-full px-2 mt-2 text-center">
                        <p class="texto-ojo">
                          <span class="text-danger font-medium">Ojo:</span>
                          Los costos de los conceptos capturados ya incluyen el
                          IVA.
                        </p>
                        <vs-divider />
                      </div>

                      <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                      >
                        <div class="flex flex-wrap">
                          <vs-button
                            class="w-full ml-auto mr-auto mt-1"
                            @click="acceptAlert()"
                            color="success"
                            size="large"
                          >
                            <span>Timbrar CFDI</span>
                          </vs-button>
                        </div>
                      </div>
                      <!--fin de precios-->
                    </div>
                  </div>
                </div>
              </div>
              <!--fin del checkout-->
            </vx-card>
          </div>
        </div>
      </div>
    </vs-popup>
    <ConfirmarDanger
      :z_index="'z-index58k'"
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>
    <ClientesBuscador
      :z_index="'z-index58k'"
      :show="openBuscadorCliente"
      @closeBuscador="openBuscadorCliente = false"
      @retornoCliente="clienteSeleccionado"
    ></ClientesBuscador>
    <SearchOperacion
      :z_index="'z-index58k'"
      :show="openBuscadorOperacion"
      @closeBuscador="openBuscadorOperacion = false"
      @OperacionSeleccionada="OperacionSeleccionada"
    ></SearchOperacion>

    <SearchCfdi
      :z_index="'z-index58k'"
      :show="openBuscadorCfdi"
      :tipo_search="tipo_search"
      @closeBuscador="openBuscadorCfdi = false"
      @CfdiPagarSeleccionado="CfdiPagarSeleccionado"
      @CfdiRelacionarSeleccionado="CfdiRelacionarSeleccionado"
    ></SearchCfdi>
    <Password
      :show="openPassword"
      :callback-on-success="callback"
      @closeVerificar="closePassword"
      :accion="accionNombre"
    >
    </Password>
    <Reporteador
      :header="'consultar CFDIs'"
      :show="openReportesLista"
      :listadereportes="ListaReportes"
      :request="request"
      @closeReportes="openReportesLista = false"
    ></Reporteador>
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
import facturacion from "@services/facturacion";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import SearchOperacion from "@pages/facturacion/search_operacion.vue";
import SearchCfdi from "@pages/facturacion/search_cfdi.vue";
import sat from "@services/sat";
import ClientesBuscador from "@pages/clientes/searcher.vue";
import Reporteador from "@pages/Reporteador";

/**VARIABLES GLOBALES */
import {
  configdateTimePicker,
  configdateTimePickerWithTime,
} from "@/VariablesGlobales";

export default {
  components: {
    "v-select": vSelect,
    flatPickr,
    ConfirmarDanger,
    ClientesBuscador,
    SearchOperacion,
    Password,
    SearchCfdi,
    Reporteador,
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
    id_cfdi: {
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
      this.limpiarValidationConcepto();
      if (newValue == true) {
        this.$nextTick(() => {
          //this.$refs["fallecido_ref"].$el.querySelector("input").focus();
        });
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        (async () => {
          if (this.getTipoformulario == "facturar") {
            await this.get_regimenes();
            await this.get_tipos_comprobante();
            await this.get_metodos_pago();
            await this.get_sat_formas_pago();
            await this.get_tipos_relacion();
            await this.get_claves_productos_sat();
            await this.get_sat_unidades();
            await this.get_usos_cfdi();
            await this.get_sat_paises();
          }
        })();
      } else {
        /**acciones al cerrar el formulario */
      }
    },

    "form.tipo_rfc": function (newValue, oldValue) {
      if (newValue.value < 3) {
        this.form.sat_pais = this.sat_paises[151];
      } else {
        this.form.sat_pais = this.sat_paises[0];
      }
      if (newValue.value == 1) {
        if (this.datos_cliente != null) {
          if (this.datos_cliente.datos.regimen != null) {
            this.form.regimen = {
              value: this.datos_cliente.datos.regimen.id,
              label: this.datos_cliente.datos.regimen.regimen,
            };
          } else {
            this.form.regimen = this.regimenes[0];
          }
        }
      } else {
        this.form.regimen = this.regimenes[11];
      }
    },

    "form.tipo_comprobante": function (newValue, oldValue) {
      if (newValue.value > 1) {
        /**Al no ser de ingreso se marca automaticamente el metodo de pago PUE*/
        this.form.metodo_pago = this.metodos_pago[1];
        if (this.form.metodo_pago.value > 1) {
          this.form.forma_pago = this.formas_pago[0];
        }
        this.form.uso_cfdi = this.usos_cfdi[this.usos_cfdi.length - 1];

        if (newValue.value == 2) {
          /**egreso */
          this.form.descuento_b = this.sino[1];
        }
      } else {
        this.form.metodo_pago = this.metodos_pago[0];
      }
    },

    /**cambiando a por definir la forma de pago cuando el metodo es ppd */
    "form.metodo_pago": function (newValue, oldValue) {
      if (newValue.value == 2) {
        this.form.forma_pago = this.formas_pago[this.formas_pago.length - 1];
      } else {
        this.form.forma_pago = this.formas_pago[0];
      }
    },

    "form.forma_pago": function (newValue, oldValue) {
      if (this.form.tipo_comprobante.value > 1) {
        /**si es de tipo pago o egreso no puede ser de tipo por definir */
        if (newValue == this.formas_pago[this.formas_pago.length - 1]) {
          //lo regreso a elegir una nueva forma de pago
          this.form.forma_pago = this.formas_pago[0];
          this.$vs.notify({
            title: "Error en forma de pago",
            text: "Debe seleccionar una forma de pago diferente a 'Por Definir' cuando se trata de un comprobante de Pago o Egreso.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
        }
      } else {
        if (this.form.metodo_pago.value == 1) {
          /**si es de tipo ingreso y tiene como metodo de pago PUE */
          if (newValue == this.formas_pago[this.formas_pago.length - 1]) {
            //lo regreso a elegir una nueva forma de pago
            this.form.forma_pago = this.formas_pago[0];
            this.$vs.notify({
              title: "Error en forma de pago",
              text: "Debe seleccionar una forma de pago diferente a 'Por Definir' cuando se trata de un comprobante de ingreso con método de pago PUE.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 8000,
            });
          }
        }
      }
    },
  },
  computed: {
    //valido si el cliente cumple con los cambios de datos para el cfdi 4.0
    cliente_datos_validos_cfdi: function () {
      if (this.datos_cliente != null) {
        if (this.datos_cliente.datos) {
          if (this.form.tipo_rfc.value == 1) {
            //revisa si el cliente cumple con los datos requeridos
            if (
              this.datos_cliente.datos.rfc != null &&
              this.datos_cliente.datos.cp != null &&
              this.datos_cliente.datos.regimen != null
            ) {
              return true;
            } else {
              return false;
            }
          }
        }
      } else return null;
    },

    total_facturar: function () {
      let total = 0;

      if (this.form.tipo_comprobante.value == 1) {
        /**ingreso */
        this.form.conceptos.forEach((element) => {
          if (element.descuento_b.value == 0) {
            total += Number(element.precio_neto) * Number(element.cantidad);
          } else {
            total +=
              Number(element.precio_descuento) * Number(element.cantidad);
          }
        });
      } else if (this.form.tipo_comprobante.value == 2) {
        /**egreso */
        this.form.conceptos.forEach((element) => {
          if (element.descuento_b.value == 0) {
            total += Number(element.precio_neto) * Number(element.cantidad);
          } else {
            total +=
              Number(element.precio_descuento) * Number(element.cantidad);
          }
        });
      } else {
        if (this.form.tipo_comprobante.value == 5) {
          /**pago*/
          this.form.cfdis_a_pagar.forEach((element) => {
            total += Number(element.monto_pago);
          });
        }
      }

      return total;
    },

    descuento_b_validacion_computed: function () {
      return this.form.descuento_b.value;
    },

    sat_pais_validacion_computed: function () {
      return this.form.sat_pais.value;
    },
    tipo_comprobante_validacion_computed: function () {
      return this.form.tipo_comprobante.value;
    },

    metodo_pago_validacion_computed: function () {
      return this.form.metodo_pago.value;
    },

    forma_pago_validacion_computed: function () {
      return this.form.forma_pago.value;
    },

    uso_cfdi_validacion_computed: function () {
      return this.form.uso_cfdi.value;
    },

    tipo_relacion_validacion_computed: function () {
      if (this.form.tipo_comprobante.value == 2) {
        /**egreso */
        return this.form.tipo_relacion.value;
      } else {
        return true;
      }
    },

    clave_sat_validacion_computed: function () {
      return this.form.clave_sat.value;
    },
    unidad_sat_validacion_computed: function () {
      return this.form.unidad_sat.value;
    },
    fechapago_validacion_computed: function () {
      return this.form.fecha_pago;
    },

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
    get_id_cfdi: {
      get() {
        return this.id_cfdi;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      openReportesLista: false,
      ListaReportes: [],
      request: {
        folio_id: "",
        email: "",
        destinatario: "",
      },
      /**control del buscador de cfdis */
      tipo_search: "",
      openBuscadorCfdi: false,
      indexCfdiSearch: 0,
      /**control del componente de operaciones */
      openBuscadorOperacion: false,
      /**modificando articulo */
      ModificandoArticulo: false,
      indextrArticuloModificando: "",
      indextrArticuloRemoviendo: "",
      indextrOperacionRemoviendo: "",
      /**variables para el control del formulario */
      tipo: "",
      /**buscador del cliente */
      openBuscadorCliente: false,
      /**control del popup de confirmar accion */
      openConfirmarSinPassword: false,
      openPassword: false,
      accionNombre: "Timbrar CFDI",
      botonConfirmarSinPassword: "",
      accionConfirmarSinPassword: "",
      callBackConfirmar: Function,
      callback: Function,
      datos_cliente: [],
      tipos_rfc: [
        {
          value: "1",
          label: "Cliente con RFC",
        },
        {
          value: "2",
          label: "Púb. General",
        },
        {
          value: "3",
          label: "Púb. General Extranjero",
        },
      ],
      tipos_comprobante: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      metodos_pago: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      formas_pago: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      tipos_relacion: [
        {
          value: "",
          label: "Sin documentos relacionados",
        },
      ],
      claves_sat: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      unidades_sat: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      usos_cfdi: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      sat_paises: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      regimenes: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      sino: [
        {
          value: "1",
          label: "SI",
        },
        {
          value: "0",
          label: "NO",
        },
      ],
      /**DATOS DEL FORMULARIO */
      form: {
        /**datos del cliente */
        id_cliente: "",
        cliente: "",
        direccion_cliente: "",
        tipo_rfc: {
          value: "1",
          label: "Cliente con RFC",
        },
        rfc: "",
        razon_social: "",
        direccion_fiscal: "",
        direccion_fiscal_cp: "",
        regimen_receptor_clave: "",
        /**FIN DE datos del cliente */

        /**TIPO DE COMPROBANTE */
        tipo_comprobante: {
          value: "",
          label: "Seleccione 1",
        },
        metodo_pago: {
          value: "",
          label: "Seleccione 1",
        },
        forma_pago: {
          value: "",
          label: "Seleccione 1",
        },
        fecha_pago: "",
        tipo_relacion: {
          value: "",
          label: "Sin documentos relacionados",
        },
        uso_cfdi: {
          value: "",
          label: "Seleccione 1",
        },
        sat_pais: {
          value: "",
          label: "Seleccione 1",
        },
        monto_pago: {},
        cfdis_a_pagar: [],
        /**CFDIS RELACIONADOS */
        cfdis_relacionados: [],
        operaciones_relacionadas: [],
        conceptos: [],
        /**articulos a agregar fuera de operacion */
        clave_sat: {
          value: "",
          label: "Seleccione 1",
        },

        unidad_sat: {
          value: "",
          label: "Seleccione 1",
        },
        regimen: {
          value: "",
          label: "Seleccione 1",
        },
        cantidad: "",
        descripcion: "",
        precio_neto: "",
        descuento_b: {
          value: "0",
          label: "NO",
        },
        precio_descuento: "",
        /**FIN DE articulos a agregar fuera de operacion */
        nota: "",
        tasa_iva: 16,
      },
      errores: [],
    };
  },
  methods: {
    openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {
      this.ListaReportes = [];
      this.request.folio_id = parametro.id;
      this.request.email = parametro.cliente_email;
      this.request.destinatario = parametro.cliente_nombre;
      this.ListaReportes.push({
        nombre: nombre_reporte,
        url: link,
      });
      this.openReportesLista = true;
    },
    acceptAlert() {
      try {
        this.$validator
          .validateAll("form")
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
              (async () => {
                if (this.getTipoformulario == "facturar") {
                  if (!this.datos_cliente.datos) {
                    //pido que verifique los datos del cliente
                    this.$vs.notify({
                      title: "Error",
                      text: "Verifique los datos del receptor.",
                      iconPack: "feather",
                      icon: "icon-alert-circle",
                      color: "danger",
                      position: "bottom-right",
                      time: "8000",
                    });
                    return;
                  }

                  /**validando que tenga los cfdis_relacionados en caso de aplicar */
                  if (this.form.tipo_relacion.value > 0) {
                    if (this.form.cfdis_relacionados.length > 0) {
                      if (
                        this.form.tipo_comprobante.value == 1 ||
                        this.form.tipo_comprobante.value == 5
                      ) {
                        /**es pago o ingreso */
                        /**validando que sean del tipo que es el nuevo documento */
                        this.form.cfdis_relacionados.forEach((element) => {
                          if (
                            element.sat_tipo_comprobante_id !=
                            this.form.tipo_comprobante.value
                          ) {
                            this.$vs.notify({
                              title: "Error",
                              text: "Los CFDIs que está relacionando deben ser del mismo tipo que este documento.",
                              iconPack: "feather",
                              icon: "icon-alert-circle",
                              color: "danger",
                              position: "bottom-right",
                              time: "8000",
                            });
                            /**no aplica porque el cfdi relacionado debe ser del mismo tipo que el que se esta generando */
                            throw "exit";
                          }
                        });
                      } else {
                        /**es egreso y se debe validar que el documento relacionado sea de tipo ingreso y que sea solo un documento */
                        if (this.form.cfdis_relacionados.length == 1) {
                          if (
                            this.form.cfdis_relacionados[0]
                              .sat_tipo_comprobante_id != 1
                          ) {
                            this.$vs.notify({
                              title: "Error",
                              text: "Ingrese un cfdi de tipo ingreso para aplicar Egresos o Nota de Crédito.",
                              iconPack: "feather",
                              icon: "icon-alert-circle",
                              color: "danger",
                              position: "bottom-right",
                              time: "8000",
                            });
                            throw "exit";
                          }
                        } else {
                          this.$vs.notify({
                            title: "Error",
                            text: "Ingrese solo un cfdi para relacionar a este nuevo documento.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "8000",
                          });
                          throw "exit";
                        }
                      }
                    } else {
                      this.$vs.notify({
                        title: "Error",
                        text: "Ingrese al menos un cfdi para relacionar a este nuevo documento.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "8000",
                      });
                      throw "exit";
                    }
                  }

                  if (
                    this.form.tipo_comprobante.value == 1 ||
                    this.form.tipo_comprobante.value == 2
                  ) {
                    if (this.form.uso_cfdi == this.usos_cfdi[10]) {
                      this.$vs.notify({
                        title: "Error",
                        text: "Seleccione el uso de CFDI Correcto.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "8000",
                      });
                      throw "exit";
                    }
                    /**ingreso */
                    if (this.form.conceptos.length > 0) {
                      this.callback = await this.timbrar_cfdi;
                      this.openPassword = true;
                    } else {
                      /**agregue al menos un concepto */
                      this.$vs.notify({
                        title: "Error",
                        text: "Ingrese al menos un concepto a facturar",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "8000",
                      });
                    }
                  } else if (this.form.tipo_comprobante.value == 5) {
                    /**pago */
                    if (this.form.cfdis_a_pagar.length > 0) {
                      this.callback = await this.timbrar_cfdi;
                      this.openPassword = true;
                    } else {
                      /**agregue al menos un concepto */
                      this.$vs.notify({
                        title: "Error",
                        text: "Ingrese al menos un CFDI a pagar",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        position: "bottom-right",
                        time: "8000",
                      });
                    }
                  }
                }
              })();
            }
          })
          .catch(() => {});
      } catch (error) {}
    },

    async timbrar_cfdi() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await facturacion.timbrar_cfdi(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Timbrar CFDI 4.0",
            text: "Se ha timbrado el CFDI correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.$emit("openActions", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Timbrar CFDI 4.0",
            text: "Error al timbrar el CFDI, por favor reintente.",
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
              title: "Timbrar CFDI 4.0",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          } else if (err.response.status == 409) {
            this.$vs.notify({
              title: "Timbrar CFDI 4.0",
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

    /**retorno de la operacion selecciona para cargar */
    OperacionSeleccionada(datos) {
      /**primero hacemos el agregado de las operaciones relacionadas */
      let esta_operacion = false;
      this.form.operaciones_relacionadas.forEach((element) => {
        if (element.operacion_id == datos.operacion_id) {
          esta_operacion = true;
        }
      });

      if (esta_operacion == false) {
        let clave_operacion_por_tipo = 0;
        if (datos.empresa_operaciones_id == 1) {
          clave_operacion_por_tipo = datos.ventas_terrenos_id;
        } else if (datos.empresa_operaciones_id == 2) {
          clave_operacion_por_tipo = datos.cuotas_cementerio_id;
        } else if (datos.empresa_operaciones_id == 3) {
          clave_operacion_por_tipo = datos.servicios_funerarios_id;
        } else if (datos.empresa_operaciones_id == 4) {
          clave_operacion_por_tipo = datos.ventas_planes_id;
        }

        this.form.operaciones_relacionadas.push({
          clave_operacion_por_tipo: clave_operacion_por_tipo,
          operacion_id: datos.operacion_id,
          cliente: datos.nombre,
          fecha_operacion_texto: datos.fecha_operacion_texto,
          tipo_operacion_texto: datos.tipo_operacion_texto,
        });

        /**agregando los conceptops */
        datos.conceptos.forEach((concepto) => {
          this.form.conceptos.push({
            clave_sat: concepto.clave_sat,
            unidad_sat: concepto.unidad_sat,
            cantidad: concepto.cantidad,
            descripcion: concepto.descripcion,
            precio_neto: concepto.precio_neto,
            descuento_b: concepto.descuento_b,
            precio_descuento: concepto.precio_descuento,
            modifica_b: concepto.modifica_b,
            concepto_operacion_ver_b: 1,
            concepto_operacion_id: datos.operacion_id,
          });
        });

        if (datos.conceptos.length > 0) {
          this.$vs.notify({
            title: "Operaciones relacionadas al CFDI",
            text: "Se han agregado los conceptos facturables al CFDI",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            position: "bottom-right",
            time: "12000",
          });
        } else {
          this.$vs.notify({
            title: "Operaciones relacionadas al CFDI",
            text: "La operación fue agregada al CFDI pero no se encontraron conceptos facturables en esta operación.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "warning",
            position: "bottom-right",
            time: "12000",
          });
        }
      } else {
        this.$vs.notify({
          title: "Operaciones relacionadas al CFDI",
          text: "Ya se encuentra agregada esta operación",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "12000",
        });
      }
    },

    openBuscadorCfdiPagar(tipo) {
      this.tipo_search = tipo;
      this.openBuscadorCfdi = true;
    },

    CfdiPagarSeleccionado(datos) {
      /**primero hacemos el agregado de los cfdis */
      let esta_cfdi = false;
      this.form.cfdis_a_pagar.forEach((element) => {
        if (element.id == datos.id) {
          esta_cfdi = true;
        }
      });

      if (esta_cfdi == false) {
        let agregado = false;
        if (datos.sat_tipo_comprobante_id == 1) {
          /**pago*/
          if (datos.sat_metodos_pago_id == 2) {
            /**es de ppd */
            /**se agrega */
            this.form.cfdis_a_pagar.push({
              id: datos.id,
              uuid: datos.uuid,
              cliente_nombre: datos.cliente_nombre,
              cliente_email: datos.cliente_email,
              serie: datos.serie,
              fecha_timbrado_texto: datos.fecha_timbrado_texto,
              total: datos.total,
              monto_pago: 0,
              saldo_cfdi: parseFloat(datos.saldo_cfdi).toFixed(2),
              tipo_comprobante_texto: datos.tipo_comprobante_texto,
              rfc_receptor: datos.rfc_receptor,
              nombre_receptor: datos.nombre_receptor,
              sat_metodos_pago_texto: datos.sat_metodos_pago_texto,
              status: datos.status,
            });
            agregado = true;
          }
        }
        if (!agregado) {
          this.$vs.notify({
            title: "CFDIs a pagar",
            text: "Verique que el CFDI a pagar sea de tipo ingreso y método de pago PPD",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "12000",
          });
        }
      } else {
        this.$vs.notify({
          title: "CFDIs a pagar",
          text: "Ya se encuentra agregado este CFDI para pagar",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "12000",
        });
      }
    },
    CfdiRelacionarSeleccionado(datos) {
      /**primero hacemos el agregado de los cfdis */
      let esta_cfdi = false;
      this.form.cfdis_relacionados.forEach((element) => {
        if (element.id == datos.id) {
          esta_cfdi = true;
        }
      });

      if (esta_cfdi == false) {
        let agregado = false;

        /**se agrega */
        this.form.cfdis_relacionados.push({
          id: datos.id,
          uuid: datos.uuid,
          cliente_nombre: datos.cliente_nombre,
          cliente_email: datos.cliente_email,
          serie: datos.serie,
          fecha_timbrado_texto: datos.fecha_timbrado_texto,
          total: datos.total,
          monto_pago: 0,
          saldo_cfdi: datos.saldo_cfdi,
          tipo_comprobante_texto: datos.tipo_comprobante_texto,
          sat_tipo_comprobante_id: datos.sat_tipo_comprobante_id,
          rfc_receptor: datos.rfc_receptor,
          nombre_receptor: datos.nombre_receptor,
          sat_metodos_pago_id: datos.sat_metodos_pago_id,
          sat_metodos_pago_texto: datos.sat_metodos_pago_texto,
          status: datos.status,
        });
        agregado = true;
      } else {
        this.$vs.notify({
          title: "CFDIs a relacionar",
          text: "Ya se encuentra agregado este CFDI para relacionar",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "12000",
        });
      }
    },

    /**metodo para agregar articulos manualmente */
    AgregarConcepto() {
      this.$validator
        .validateAll("conceptos")
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Agregar Artículos/Servicios al CFDI",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
          } else {
            this.form.conceptos.push({
              clave_sat: this.form.clave_sat,
              unidad_sat: this.form.unidad_sat,
              cantidad: this.form.cantidad,
              descripcion: this.form.descripcion,
              precio_neto: this.form.precio_neto,
              descuento_b: this.form.descuento_b,
              precio_descuento: this.form.precio_descuento,
              modifica_b: 1,
              concepto_operacion_ver_b: 1,
              concepto_operacion_id: "",
            });
            this.LimpiarAddArticulo();
          }
        })
        .catch(() => {});
    },

    CargarModificarConcepto(indextr) {
      if (this.form.conceptos[indextr].modifica_b != 0) {
        this.ModificandoArticulo = true;
        this.indextrArticuloModificando = indextr;
        this.form.clave_sat = this.form.conceptos[indextr].clave_sat;
        this.form.unidad_sat = this.form.conceptos[indextr].unidad_sat;
        this.form.cantidad = this.form.conceptos[indextr].cantidad;
        this.form.descripcion = this.form.conceptos[indextr].descripcion;
        this.form.precio_neto = this.form.conceptos[indextr].precio_neto;
        this.form.descuento_b = this.form.conceptos[indextr].descuento_b;
        this.form.precio_descuento =
          this.form.conceptos[indextr].precio_descuento;
        this.$nextTick(() => {
          this.$refs["cantidad_agregar"].$el.querySelector("input").focus();
        });
      } else {
        alert("no modifica");
      }
    },

    CancelarModificacion() {
      this.LimpiarAddArticulo();
    },

    ActualizarModificacion() {
      this.form.conceptos[this.indextrArticuloModificando].clave_sat =
        this.form.clave_sat;
      this.form.conceptos[this.indextrArticuloModificando].unidad_sat =
        this.form.unidad_sat;
      (this.form.conceptos[this.indextrArticuloModificando].cantidad =
        this.form.cantidad),
        (this.form.conceptos[this.indextrArticuloModificando].descripcion =
          this.form.descripcion);
      this.form.conceptos[this.indextrArticuloModificando].precio_neto =
        this.form.precio_neto;
      this.form.conceptos[this.indextrArticuloModificando].descuento_b =
        this.form.descuento_b;
      this.form.conceptos[this.indextrArticuloModificando].precio_descuento =
        this.form.precio_descuento;
      this.form.conceptos[this.indextrArticuloModificando].descripcion =
        this.form.descripcion;
      this.form.conceptos[this.indextrArticuloModificando].descripcion =
        this.form.descripcion;
      this.form.conceptos[this.indextrArticuloModificando].descripcion =
        this.form.descripcion;
      this.LimpiarAddArticulo();
    },

    LimpiarAddArticulo() {
      this.ModificandoArticulo = false;
      this.indextrArticuloModificando = "";
      this.form.clave_sat = this.claves_sat[0];
      this.form.unidad_sat = this.unidades_sat[0];
      this.form.cantidad = "";
      this.form.descripcion = "";
      this.form.precio_neto = "";
      this.form.precio_descuento = "";
      this.indextrArticuloRemoviendo = "";
      this.form.descuento_b = this.sino[1];
      this.limpiarValidationConcepto();
    },

    //remover beneficiario
    remover_concepto(indextr) {
      this.botonConfirmarSinPassword = "eliminar";
      this.accionConfirmarSinPassword =
        "¿Desea eliminar este concepto? Los datos quedarán eliminados del CFDI?";
      this.indextrArticuloRemoviendo = indextr;
      this.callBackConfirmar = this.remover_concepto_callback;
      this.openConfirmarSinPassword = true;
    },
    //remover el concepto seleccionado
    remover_concepto_callback() {
      /**antes de remover verifica si este era el ultimo concepto de alguna operacion asociada y la remueve junto con la operacion */

      if (
        Number(
          this.form.conceptos[this.indextrArticuloRemoviendo]
            .concepto_operacion_id
        ) > 0
      ) {
        /**tiene asociada una operacion y se debe de revisar si quedan conceptos */
        let conceptos_de_la_operacion = 0;
        for (let index = 0; index < this.form.conceptos.length; index++) {
          if (
            this.form.conceptos[index].concepto_operacion_id ==
            this.form.conceptos[this.indextrArticuloRemoviendo]
              .concepto_operacion_id
          ) {
            conceptos_de_la_operacion++;
          }
        }

        if (conceptos_de_la_operacion == 1) {
          /**remueve tambien la operacion pues no quedaran conceptos que relacionar */
          for (
            let index = 0;
            index < this.form.operaciones_relacionadas.length;
            index++
          ) {
            if (
              this.form.operaciones_relacionadas[index].operacion_id ==
              this.form.conceptos[this.indextrArticuloRemoviendo]
                .concepto_operacion_id
            ) {
              /**remueve la operacion */
              this.form.operaciones_relacionadas.splice(index, 1);
              break;
            }
          }
        }
      }
      this.form.conceptos.splice(this.indextrArticuloRemoviendo, 1);
      this.LimpiarAddArticulo();
    },

    remover_operacion(indextr) {
      this.botonConfirmarSinPassword = "eliminar";
      this.accionConfirmarSinPassword =
        "¿Desea eliminar esta operación? Los datos quedarán eliminados del CFDI?";
      this.indextrOperacionRemoviendo = indextr;
      this.callBackConfirmar = this.remover_operacion_callback;
      this.openConfirmarSinPassword = true;
    },

    remover_operacion_callback() {
      /**antes de remover remueve todos los conceptos que pertenecen a la operacion */
      for (let index = 0; index < this.form.conceptos.length; index++) {
        if (
          this.form.conceptos[index].concepto_operacion_id ==
          this.form.operaciones_relacionadas[this.indextrOperacionRemoviendo]
            .operacion_id
        ) {
          //remueve el concepto
          this.form.conceptos.splice(index, 1);
          index--;
        }
      }
      this.form.operaciones_relacionadas.splice(
        this.indextrOperacionRemoviendo,
        1
      );
      this.LimpiarAddArticulo();
    },

    async get_tipos_comprobante() {
      this.$vs.loading();
      await facturacion
        .get_tipos_comprobante()
        .then((res) => {
          this.tipos_comprobante = [];
          this.tipos_comprobante.push({
            label: "Seleccione 1",
            value: "",
          });
          res.data.forEach((element) => {
            this.tipos_comprobante.push({
              label: element.tipo,
              value: element.id,
            });
          });
          this.form.tipo_comprobante = this.tipos_comprobante[0];
          //this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el catálogo de tipo de comprobantes",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },
    async get_metodos_pago() {
      //this.$vs.loading();
      await facturacion
        .get_metodos_pago()
        .then((res) => {
          this.metodos_pago = [];
          this.metodos_pago.push({
            label: "Seleccione 1",
            value: "",
          });
          res.data.forEach((element) => {
            this.metodos_pago.push({
              label: element.metodo,
              value: element.id,
            });
          });
          this.form.metodo_pago = this.metodos_pago[0];
          //this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el catálogo de métodos de pago",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },
    async get_sat_formas_pago() {
      //this.$vs.loading();
      await facturacion
        .get_sat_formas_pago()
        .then((res) => {
          this.formas_pago = [];
          this.formas_pago.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.formas_pago.push({
              label: element.forma,
              value: element.id,
            });
          });
          this.form.forma_pago = this.formas_pago[0];
          // this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el catálogo de formas de pago",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },

    async get_tipos_relacion() {
      //  this.$vs.loading();
      await facturacion
        .get_tipos_relacion()
        .then((res) => {
          this.tipos_relacion = [];
          this.tipos_relacion.push({
            label: "Sin documentos relacionados",
            value: "",
          });
          res.data.forEach((element) => {
            this.tipos_relacion.push({
              label: element.tipo,
              value: element.id,
            });
          });
          this.form.tipo_relacion = this.tipos_relacion[0];
          //this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el catálogo de tipo de relación",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },
    async get_claves_productos_sat() {
      //this.$vs.loading();
      await facturacion
        .get_claves_productos_sat()
        .then((res) => {
          this.claves_sat = [];
          this.claves_sat.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.claves_sat.push({
              label: element.clave,
              value: element.id,
            });
          });
          this.form.clave_sat = this.claves_sat[0];
          //this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el catálogo de claves y productos del sat",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },

    async get_sat_unidades() {
      //this.$vs.loading();
      await facturacion
        .get_sat_unidades()
        .then((res) => {
          this.unidades_sat = [];
          this.unidades_sat.push({
            label: "Seleccione 1",
            value: "",
          });
          res.data.forEach((element) => {
            this.unidades_sat.push({
              label: element.clave,
              value: element.id,
            });
          });
          this.form.unidad_sat = this.unidades_sat[0];
          //this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el catálogo de unidades del sat",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },

    async get_usos_cfdi() {
      //this.$vs.loading();
      await facturacion
        .get_usos_cfdi()
        .then((res) => {
          this.usos_cfdi = [];
          this.usos_cfdi.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.usos_cfdi.push({
              label: element.uso,
              value: element.id,
            });
          });
          this.form.uso_cfdi = this.usos_cfdi[0];
          //this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el catálogo de usos del cfdi del sat",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },

    async get_sat_paises() {
      //this.$vs.loading();
      await facturacion
        .get_sat_paises()
        .then((res) => {
          this.sat_paises = [];
          this.sat_paises.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.sat_paises.push({
              label: element.pais,
              value: element.id,
            });
          });
          this.form.sat_pais = this.sat_paises[151];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el catálogo de países del sat",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },

    async get_regimenes() {
      //this.$vs.loading();
      await sat
        .getRegimenes()
        .then((res) => {
          this.regimenes = [];
          this.regimenes.push({ label: "Seleccione 1", value: "" });
          res.data.data.forEach((element) => {
            this.regimenes.push({
              label: element.regimen,
              value: element.id,
            });
          });
          this.form.regimen = this.regimenes[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Emitir CFDIS 4.0",
            text: "Error al cargar el régimen fiscal",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
          this.$vs.loading.close();
        });
    },

    clienteSeleccionado(datos) {
      /**obtiene los datos retornados del buscar cliente */
      this.form.cliente = datos.nombre;
      this.form.id_cliente = datos.id_cliente;
      this.datos_cliente = datos;
      if (datos.datos.rfc != "" && datos.datos.rfc != "N/A") {
        this.form.rfc = datos.datos.rfc;
      }
      if (datos.datos.razon_social != "" && datos.datos.razon_social != "N/A") {
        this.form.razon_social = datos.datos.razon_social;
      }

      if (
        datos.datos.direccion_fiscal != "" &&
        datos.datos.direccion_fiscal != "N/A"
      ) {
        this.form.direccion_fiscal = datos.datos.direccion_fiscal;
        this.form.direccion_fiscal_cp = datos.datos.direccion_fiscal_cp;
      }

      if (this.datos_cliente != null) {
        if (this.datos_cliente.datos.regimen != null) {
          this.form.regimen = {
            value: datos.datos.regimen.id,
            label: datos.datos.regimen.regimen,
          };
        }
      }
      //alert(datos.id_cliente);
    },
    limpiarCliente() {
      this.form.id_cliente = "";
      this.form.cliente = "";
      this.form.tipo_rfc = this.tipos_rfc[0];
      this.form.rfc = "";
      this.form.razon_social = "";
      this.form.direccion_fiscal = "";
      this.form.direccion_fiscal_cp = "";
      this.form.regimen = { label: "Seleccione 1", value: "" };
      this.datos_cliente = null;
    },
    quitarCliente() {
      this.botonConfirmarSinPassword = "Cambiar cliente";
      this.accionConfirmarSinPassword =
        "¿Desea cambiar de cliente para esta factura?";
      this.callBackConfirmar = this.limpiarCliente;
      this.openConfirmarSinPassword = true;
    },

    cancel() {
      this.$emit("closeVentana");
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

    closePassword() {
      this.openPassword = false;
    },

    //remover beneficiario
    remover_cfdi_a_pagar(indextr) {
      this.botonConfirmarSinPassword = "eliminar";
      this.accionConfirmarSinPassword =
        "¿Desea quitar este cfdi? no se registrarán los datos del CFDI?";
      this.indexCfdiSearch = indextr;
      this.callBackConfirmar = this.remover_cfdi_a_pagar_callback;
      this.openConfirmarSinPassword = true;
    },
    //remover el cfdi seleccionado
    remover_cfdi_a_pagar_callback() {
      this.form.cfdis_a_pagar.splice(this.indexCfdiSearch, 1);
    },

    remover_cfdi_a_relacionar(indextr) {
      this.botonConfirmarSinPassword = "eliminar";
      this.accionConfirmarSinPassword =
        "¿Desea quitar este cfdi? no se registrarán los datos del CFDI?";
      this.indexCfdiSearch = indextr;
      this.callBackConfirmar = this.remover_cfdi_a_relacionar_callback;
      this.openConfirmarSinPassword = true;
    },

    //remover el cfdi seleccionado
    remover_cfdi_a_relacionar_callback() {
      this.form.cfdis_relacionados.splice(this.indexCfdiSearch, 1);
    },

    //regresa los datos a su estado inicial
    limpiarVentana() {
      this.datos_cliente = null;
      this.form.id_cliente = "";
      this.form.cliente = "";
      this.form.tipo_rfc = {
        value: "1",
        label: "Cliente con RFC",
      };
      this.form.rfc = "";
      this.form.razon_social = "";
      this.form.direccion_fiscal = "";
      this.form.direccion_fiscal_cp = "";
      this.form.tipo_comprobante = {
        value: "",
        label: "Seleccione 1",
      };
      this.form.metodo_pago = {
        value: "",
        label: "Seleccione 1",
      };
      this.form.forma_pago = {
        value: "",
        label: "Seleccione 1",
      };
      this.form.fecha_pago = "";
      this.form.tipo_relacion = {
        value: "",
        label: "Sin documentos relacionados",
      };
      this.form.uso_cfdi = {
        value: "",
        label: "Seleccione 1",
      };
      this.form.sat_pais = {
        value: "",
        label: "Seleccione 1",
      };
      this.form.monto_pago = {};
      this.form.cfdis_a_pagar = [];
      this.form.operaciones_relacionadas = [];
      this.form.cfdis_relacionados = [];
      this.form.conceptos = [];
      this.form.clave_sat = {
        value: "",
        label: "Seleccione 1",
      };
      this.form.unidad_sat = {
        value: "",
        label: "Seleccione 1",
      };
      this.form.cantidad = "";
      this.form.descripcion = "";
      this.form.precio_neto = "";
      this.form.descuento_b = {
        value: "0",
        label: "NO",
      };
      this.form.precio_descuento = "";
      this.form.nota = "";
      this.form.tasa_iva = 16;
      this.errores = [];
      this.limpiarValidationComprobante();
      this.limpiarValidationConcepto();
    },

    limpiarValidationConcepto() {
      this.$nextTick(() => {
        let matcher = {
          scope: "conceptos",
          vmId: this.$validator.id,
        };
        this.$validator.reset(matcher);
      });
    },

    limpiarValidationComprobante() {
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
