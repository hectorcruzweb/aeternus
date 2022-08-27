<template >
  <div class="centerx">
    <vs-popup
      :class="['forms-popup', !mostrar_datos_operacion ? 'popup-85' : '']"
      :fullscreen="mostrar_datos_operacion"
      close="cancelar"
      :title="'control de cobranza'"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="form-group">
        <div class="title-form-group">
          <span>Referencia de pago</span>
        </div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full md:w-6/12 xl:w-4/12 px-2 input-text">
              <label>
                Referencia de Pago
                <span>(*)</span>
              </label>

              <vs-input
                data-vv-scope="form_calcular_adeudo"
                ref="referencia"
                name="referencia"
                data-vv-as=" "
                data-vv-validate-on="blur"
                maxlength="25"
                type="text"
                class="w-full"
                v-validate="'required'"
                placeholder="Núm. de referencia del pago"
                v-model.trim="form.referencia"
                :disabled="getReferencia != '' ? true : false"
                @keypress.enter="CalcularPago()"
              />
              <span>{{ errors.first("form_calcular_adeudo.referencia") }}</span>
              <span v-if="this.errores.referencia">{{
                errores.referencia[0]
              }}</span>
            </div>
            <div class="w-full md:w-6/12 xl:w-4/12 px-3 input-text">
              <label>
                Fecha y Hora del Pago
                <span>(*)</span>
              </label>
              <flat-pickr
                data-vv-scope="form_calcular_adeudo"
                name="fecha_pago"
                data-vv-as=" "
                v-validate:fecha_pago_validacion_computed.immediate="'required'"
                :config="configdateTimePickerWithTime"
                v-model="form.fecha_pago"
                placeholder="Fecha del Pago"
                class="w-full"
              />
              <span>{{ errors.first("form_calcular_adeudo.fecha_pago") }}</span>
              <span v-if="this.errores.fecha_pago">{{
                errores.fecha_pago[0]
              }}</span>
            </div>
            <div class="w-full md:w-6/12 xl:w-2/12 px-2 text-center input-text">
              <label>Habilitar Multipago</label>
              <div>
                <vs-switch
                  class="mx-auto mt-2"
                  ref="permiso"
                  color="success"
                  v-model="form.multipago"
                  :vs-value="false"
                />
              </div>
            </div>
            <div
              class="w-full md:w-6/12 xl:w-2/12 px-2 text-center xl:text-right"
            >
              <vs-button
                class="w-full my-2 mt-6 md:mt-4"
                @click="CalcularPago()"
              >
                <span>Calcular Adeudo</span>
              </vs-button>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group" v-if="mostrar_datos_operacion">
        <div class="title-form-group">
          <span>Referencias a Pagar</span>
        </div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <!--incio de tabla-->
            <div class="w-full">
              <vs-table
                :data="form.datos_operacion.pagos_programados"
                noDataText="0 Resultados"
                class="tabla-datos"
              >
                <template slot="header">
                  <h3>Listado de pagos programados</h3>
                </template>
                <template slot="thead">
                  <vs-th>
                    <vs-checkbox
                      ref="seleccionar_todos"
                      color="primary"
                      class="capitalize font-medium mt-2 permiso"
                      :vs-value="true"
                      v-model="seleccionar_todos"
                    ></vs-checkbox>
                  </vs-th>
                  <vs-th>#</vs-th>
                  <vs-th>Concepto</vs-th>
                  <vs-th>Referencia</vs-th>
                  <vs-th>Fecha Programada</vs-th>
                  <vs-th>Monto Programado $</vs-th>
                  <vs-th>Intereses Generados $</vs-th>
                  <vs-th hidden>Descuento Disponible $</vs-th>
                  <vs-th> Saldo del Pago $</vs-th>
                </template>
                <template slot-scope="{ data }">
                  <vs-tr
                    :data="programados"
                    v-show="
                      programados.status == 1 && programados.status_pago != 2
                    "
                    v-for="(programados, indextr) in form.datos_operacion
                      .pagos_programados"
                    v-bind:key="programados.id"
                    ref="row"
                  >
                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr].num_pago
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                    >
                      <vs-checkbox
                        ref="pago_seleccionado"
                        color="primary"
                        :vs-value="programados"
                        v-model="form.pagos_a_cubrir"
                      ></vs-checkbox>
                    </vs-td>
                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr].num_pago
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                    >
                      <span class="font-semibold">{{
                        programados.num_pago
                      }}</span>
                    </vs-td>
                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr]
                          .concepto_texto
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                      >{{ programados.concepto_texto }}</vs-td
                    >
                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr]
                          .referencia_pago
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                      >{{ programados.referencia_pago }}</vs-td
                    >
                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr]
                          .fecha_programada_abr
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                      >{{ programados.fecha_programada_abr }}</vs-td
                    >

                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr]
                          .status_pago
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                      >$
                      {{
                        (programados.monto_programado -
                          programados.total_cubierto)
                          | numFormat("0,000.00")
                      }}</vs-td
                    >
                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr]
                          .intereses
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                      >$
                      {{ programados.intereses | numFormat("0,000.00") }}</vs-td
                    >
                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr]
                          .status_pago
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                      hidden
                      >$
                      {{
                        programados.descuento_pronto_pago
                          | numFormat("0,000.00")
                      }}</vs-td
                    >
                    <vs-td
                      :data="
                        form.datos_operacion.pagos_programados[indextr]
                          .saldo_neto
                      "
                      :class="[
                        programados.status_pago == 0 ? 'color-danger-900' : '',
                      ]"
                      >$
                      {{
                        programados.saldo_neto | numFormat("0,000.00")
                      }}</vs-td
                    >
                  </vs-tr>
                  <vs-tr class="font-medium color-primary-900 size-base">
                    <vs-td colspan="5">
                      <div class="py-2"></div>
                    </vs-td>
                    <vs-td>
                      <div class="py-2 uppercase">Monto Programado $</div>
                    </vs-td>
                    <vs-td>
                      <div class="py-2 uppercase">Intereses Generados $</div>
                    </vs-td>
                    <vs-td hidden>
                      <div class="py-2 uppercase">Descuento Disponible $</div>
                    </vs-td>
                    <vs-td>
                      <div class="py-2 uppercase">Saldo del Pago $</div>
                    </vs-td>
                  </vs-tr>
                  <vs-tr class="font-medium color-black-900 size-base">
                    <vs-td colspan="5">
                      <div class="py-2 font-bold uppercase">Totales $</div>
                    </vs-td>
                    <vs-td>
                      <div class="py-2">
                        $ {{ maximo_abono | numFormat("0,000.00") }}
                      </div>
                    </vs-td>
                    <vs-td>
                      <div class="py-2">
                        $ {{ maximo_interes | numFormat("0,000.00") }}
                      </div>
                    </vs-td>
                    <vs-td hidden>
                      <div class="py-2">
                        $ {{ maximo_descuento | numFormat("0,000.00") }}
                      </div>
                    </vs-td>
                    <vs-td>
                      <div class="py-2">
                        $ {{ maximo_saldo | numFormat("0,000.00") }}
                      </div>
                    </vs-td>
                  </vs-tr>
                </template>
              </vs-table>
            </div>
            <!--FIN de tabla-->
            <div class="w-full">
              <div class="flex flex-wrap">
                <div class="w-full lg:w-8/12">
                  <div class="flex flex-wrap">
                    <div class="w-full xl:w-6/12 px-2">
                      <div class="form-group mt-6">
                        <div class="title-form-group">
                          <span>Método del pago</span>
                        </div>
                        <div class="form-group-content">
                          <div class="flex flex-wrap">
                            <!--Forma de pago-->
                            <div class="w-full px-2 input-text">
                              <label>
                                Forma de Pago
                                <span>(*)</span>
                              </label>
                              <v-select
                                v-validate:forma_pago_validacion_computed.immediate="
                                  'required'
                                "
                                data-vv-scope="pago_form"
                                :options="FormasPago"
                                :clearable="false"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-model="form.formaPago"
                                class="large_select"
                                name="forma_pago"
                                data-vv-as=" "
                              >
                                <div slot="no-options">
                                  No Se Ha Seleccionado Ninguna Opción
                                </div>
                              </v-select>
                              <span>{{
                                errors.first("pago_form.forma_pago")
                              }}</span>
                              <span v-if="this.errores['formaPago.value']">{{
                                errores["formaPago.value"][0]
                              }}</span>
                            </div>

                            <div class="w-full px-2 input-text">
                              <label>
                                Cobrado Por:
                                <span>(*)</span>
                              </label>

                              <v-select
                                v-validate:cobrador_validacion_computed.immediate="
                                  'required'
                                "
                                data-vv-scope="pago_form"
                                :options="Cobradores"
                                :clearable="false"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-model="form.cobrador"
                                class="large_select"
                                name="forma_pago"
                                data-vv-as=" "
                              >
                                <div slot="no-options">
                                  No Se Ha Seleccionado Ninguna Opción
                                </div>
                              </v-select>

                              <span>{{
                                errors.first("pago_form.forma_pago")
                              }}</span>

                              <span v-if="this.errores['formaPago.value']">{{
                                errores["formaPago.value"][0]
                              }}</span>
                            </div>

                            <div class="w-full px-2 input-text">
                              <label
                                >Referencia de abono
                                (depósito/transferencia)</label
                              >

                              <vs-input
                                data-vv-scope="pago_form"
                                size="large"
                                name="referencia_sobre_pago"
                                data-vv-as=" "
                                type="text"
                                class="w-full"
                                placeholder="Ej. cheque número 1"
                                v-model="form.referencia_sobre_pago"
                              />

                              <span>{{
                                errors.first("pago_form.referencia")
                              }}</span>

                              <span v-if="this.errores.referencia_sobre_pago">{{
                                errores.referencia_sobre_pago[0]
                              }}</span>
                            </div>
                            <div class="w-full px-2 input-text">
                              <label>Nombre del banco </label>
                              <vs-input
                                data-vv-scope="pago_form"
                                size="large"
                                name="banco"
                                data-vv-as=" "
                                type="text"
                                class="w-full"
                                placeholder="Ej. Santander"
                                v-model="form.banco"
                              />
                              <span>{{ errors.first("pago_form.banco") }}</span>
                              <span v-if="this.errores.banco">{{
                                errores.banco[0]
                              }}</span>
                            </div>
                            <!--Forma de pago-->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="w-full lg:w-6/12 hidden xl:block">
                      <!--Datos de resumen-->
                      <div class="flex pt-6 px-2">
                        <!--Resumen de pago-->
                        <div
                          class="w-full p-4 border-gray-solid-1 mx-auto rounded-lg"
                        >
                          <h3
                            class="size-base font-bold color-black-900 text-center uppercase"
                          >
                            Resumen del pago
                          </h3>
                          <div class="flex pt-6 pb-2">
                            <div class="w-6/12 text-left font-medium">
                              Fecha del pago
                            </div>
                            <div class="w-6/12 text-right">
                              {{ form.datos_operacion.fecha_a_pagar_texto }}
                            </div>
                          </div>
                          <div class="flex py-2">
                            <div class="w-6/12 text-left font-medium">
                              Beneficiario
                            </div>
                            <div class="w-6/12 text-right">
                              {{ form.datos_operacion.nombre }}
                            </div>
                          </div>
                          <div class="flex py-2">
                            <div class="w-6/12 text-left font-medium">
                              Concepto del pago
                            </div>
                            <div class="w-6/12 text-right">
                              {{ form.datos_operacion.operacion_texto }}
                            </div>
                          </div>
                          <div class="flex py-2">
                            <div class="w-6/12 text-left font-medium">
                              Tipo de Cambio
                            </div>
                            <div class="w-6/12 text-right uppercase">
                              peso mexicano ($1.00 MXN)
                            </div>
                          </div>
                          <div class="flex py-2">
                            <div class="w-6/12 text-left font-medium">
                              Forma de pago
                            </div>
                            <div class="w-6/12 text-right uppercase">
                              {{ form.formaPago["label"] }}
                            </div>
                          </div>
                          <div class="flex py-2">
                            <div class="w-6/12 text-left font-medium">
                              Cobrado por:
                            </div>
                            <div class="w-6/12 text-right uppercase">
                              {{ form.cobrador.label }}
                            </div>
                          </div>
                          <div
                            class="flex flex-wrap mt-2 theme-background py-6"
                          >
                            <div
                              class="w-full text-center font-medium color-black-900 uppercase"
                            >
                              Total a pagar
                            </div>
                            <div
                              class="w-full text-center font-medium color-black-700 uppercase pt-2"
                            >
                              <span v-if="total_pagar > 0" class="uppercase">
                                $
                                {{ total_pagar | numFormat("0,000.00") }}
                                Pesos mexicanos
                              </span>
                              <span
                                v-else
                                class="uppercase color-danger-900 font-normal"
                              >
                                no se ha ingresado la cantidad a pagar
                              </span>
                            </div>
                          </div>
                        </div>
                        <!--Fin de Resumen de pago-->
                      </div>
                      <!--FIN de resumen-->
                    </div>
                    <div class="w-full input-text mt-6 px-2">
                      <label> Nota u observación: </label>
                      <vs-textarea
                        :rows="6"
                        size="large"
                        ref="nota"
                        type="text"
                        class="w-full"
                        placeholder="Ingrese una nota..."
                        v-model.trim="form.nota"
                      />
                    </div>
                  </div>
                </div>
                <div class="w-full lg:w-4/12 px-2">
                  <div class="form-group lg:mt-6">
                    <div class="title-form-group">
                      <span>Realizar Pago</span>
                    </div>
                    <div class="form-group-content">
                      <div class="flex flex-wrap">
                        <!-- realizar pago -->
                        <div class="w-full input-text px-2">
                          <label>
                            $ ABONO A CAPITAL
                            <span>(*)</span>
                          </label>
                          <vs-input
                            data-vv-scope="pago_form"
                            maxlength="9"
                            size="large"
                            name="abono"
                            data-vv-as=" "
                            v-validate="
                              'required|decimal:2|min_value:' +
                              minimo_abono +
                              '|max_value:' +
                              maximo_abono
                            "
                            type="text"
                            class="w-full"
                            placeholder="$ 0.00"
                            v-model.trim="form.abono"
                          />
                          <span>{{ errors.first("pago_form.abono") }}</span>
                          <span v-if="this.errores.abono">{{
                            errores.abono[0]
                          }}</span>
                        </div>
                        <div class="w-full input-text px-2">
                          <label>
                            $ INTERESES MORATORIOS
                            <span>(*)</span>
                          </label>
                          <vs-input
                            data-vv-scope="pago_form"
                            maxlength="9"
                            size="large"
                            name="intereses"
                            data-vv-as=" "
                            v-validate="
                              'required|decimal:2|min_value:0|max_value:' +
                              maximo_interes
                            "
                            type="text"
                            class="w-full"
                            placeholder="$ 0.00"
                            v-model.trim="form.intereses"
                          />
                          <span>{{ errors.first("pago_form.intereses") }}</span>
                          <span v-if="this.errores.intereses">{{
                            errores.intereses[0]
                          }}</span>
                        </div>
                        <div class="w-full input-text px-2 hidden">
                          <label>
                            $ DESCUENTO POR PRONTO PAGO
                            <span>(*)</span>
                          </label>
                          <vs-input
                            data-vv-scope="pago_form"
                            maxlength="9"
                            size="large"
                            name="descuento"
                            data-vv-as=" "
                            v-validate="
                              'required|decimal:2|min_value:0|max_value:' +
                              maximo_descuento
                            "
                            type="text"
                            class="w-full"
                            placeholder="$ 0.00"
                            v-model.trim="form.descuento_pronto_pago"
                          />
                          <span>{{ errors.first("pago_form.descuento") }}</span>
                          <span v-if="this.errores.descuento">{{
                            errores.descuento[0]
                          }}</span>
                        </div>
                        <div class="w-full input-text px-2">
                          <label>
                            $ TOTAL A PAGAR
                            <span>(*)</span>
                          </label>
                          <vs-input
                            data-vv-scope="pago_form"
                            maxlength="9"
                            size="large"
                            name="total"
                            data-vv-as=" "
                            v-validate="
                              'required|decimal:2|min_value:0|max_value:' +
                              maximo_saldo
                            "
                            type="text"
                            class="w-full"
                            placeholder=""
                            v-model.trim="total_pagar"
                            readonly
                            :disabled="true"
                          />
                          <span>{{ errors.first("pago_form.total") }}</span>
                          <span v-if="this.errores.total">{{
                            errores.total[0]
                          }}</span>
                        </div>

                        <div class="w-full input-text px-2">
                          <label>
                            $ CANTIDAD CON QUE PAGÓ
                            <span>(*)</span>
                          </label>

                          <vs-input
                            data-vv-scope="pago_form"
                            maxlength="9"
                            size="large"
                            name="pago_con_cantidad"
                            data-vv-as=" "
                            v-validate="
                              'required|decimal:2|min_value:' +
                              cantidad_a_regresar +
                              '|max_value:' +
                              maximo_cantidad_pago
                            "
                            type="text"
                            class="w-full"
                            placeholder=""
                            v-model.trim="form.pago_con_cantidad"
                            :disabled="
                              this.form.formaPago.value != 1 ? true : false
                            "
                          />
                          <span>{{
                            errors.first("pago_form.pago_con_cantidad")
                          }}</span>

                          <span v-if="this.errores.pago_con_cantidad">{{
                            errores.pago_con_cantidad[0]
                          }}</span>
                        </div>

                        <div class="w-full input-text px-2">
                          <label>
                            $ CAMBIO A REGRESAR
                            <span>(*)</span>
                          </label>
                          <vs-input
                            data-vv-scope="pago_form"
                            maxlength="9"
                            size="large"
                            name="cambio_pago"
                            data-vv-as=" "
                            v-validate="'required|decimal:2|min_value:0'"
                            type="text"
                            class="w-full"
                            placeholder=""
                            v-model.trim="cantidad_a_regresar"
                            readonly
                            :disabled="true"
                          />
                          <span>{{
                            errors.first("pago_form.cambio_pago")
                          }}</span>
                          <span v-if="this.errores.cambio_pago">{{
                            errores.cambio_pago[0]
                          }}</span>
                        </div>

                        <div class="w-full px-2">
                          <div class="text-center py-6">
                            <span class="color-danger-900">Ojo:</span>
                            Por favor revise la información ingresada, si todo
                            es correcto de click en "Botón de Abajo”.
                          </div>
                          <div v-if="form.formaPago.value == 7">
                            <h3
                              class="w-full size-base color-danger-900 pb-6 font-normal"
                            >
                              Advertencia, la forma de pago seleccionada se
                              tomará como un descuento.
                            </h3>
                          </div>
                          <vs-button
                            class="w-full"
                            @click="acceptAlert()"
                            color="success"
                          >
                            <span class="">Guardar Pago</span>
                          </vs-button>
                        </div>

                        <!-- realizar pago -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group" v-if="error != ''">
        <div class="title-form-group">
          <span>Errores encontrados</span>
        </div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full px-2">
              <div class="font-normal size-base color-danger-900">
                {{ error }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--fin mostrar datos de operacion-->
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
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
      :accion="'He revisado la información y quiero registrar este pago'"
      :confirmarButton="'Registrar Pago'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import pagos from "@services/pagos";
import vSelect from "vue-select";

import ConfirmarAceptar from "@pages/confirmarAceptar.vue";

/**VARIABLES GLOBALES */
import { alfabeto, configdateTimePickerWithTime } from "@/VariablesGlobales";
export default {
  components: {
    "v-select": vSelect,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
    flatPickr,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    referencia: {
      type: String,
      required: false,
      default: "",
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["referencia"].$el.querySelector("input").focus()
        );

        //yyyy-mm-dd
        let today = new Date();

        var mm =
          (today.getMinutes() + 1 < 10 ? "0" : "") + (today.getMinutes() + 1);
        var hh =
          (today.getHours() + 1 < 10 ? "0" : "") + (today.getHours() + 1);

        // 1970, 1971, ... 2015, 2016, ...
        var yyyy = today.getFullYear();
        var month =
          (today.getMonth() + 1 < 10 ? "0" : "") + (today.getMonth() + 1);
        // 01, 02, 03, ... 29, 30, 31
        var dd = (today.getDate() < 10 ? "0" : "") + today.getDate();
        let fecha = yyyy + "-" + month + "-" + dd + " " + hh + ":" + mm;
        // 01, 02, 03, ... 10, 11, 12
        this.form.fecha_pago = fecha;

        /**de manera asincrona para evitar errores en popular los selects */
        /**cargando los tipos de propeidades */
        this.title = "control de cobranza";
        /**cargando por defecto la referencia enviada */
        this.form.referencia = this.getReferencia;

        (async () => {
          await this.get_formas_pago_sat();
          await this.get_monedas_sat();
          await this.get_cobradores();
        })();

        if (this.getReferencia != "") {
          /**se envio una referencia por default
           * se debe de llamar el servicio de calcular pago
           */
          this.CalcularPago();
        }
      } else {
        this.$validator.pause();
        this.$nextTick(() => {
          this.$validator.errors.clear();
          this.$validator.fields.items.forEach((field) => field.reset());
          this.$validator.fields.items.forEach((field) =>
            this.errors.remove(field)
          );
          this.$validator.resume();
        });
      }
    },
    "form.formaPago": function (newValue, oldValue) {
      if (newValue.value != 1) {
        /**no es efectivo */
        this.form.pago_con_cantidad = this.form.total;
      } else {
        /**no es efectivo */
        this.form.pago_con_cantidad = "0";
      }
    },
  },
  computed: {
    cantidad_a_regresar: function () {
      let total = 0;
      if (Number(this.form.formaPago.value) == 1) {
        /**efectivo */
        total =
          parseFloat(this.form.pago_con_cantidad) - parseFloat(this.form.total);
        this.form.cambio_pago = parseFloat(total).toFixed(2);
      }
      return parseFloat(total).toFixed(2);
    },
    total_pagar: function () {
      let total =
        parseFloat(this.form.abono) -
        parseFloat(this.form.descuento_pronto_pago) +
        parseFloat(this.form.intereses);
      this.form.total = parseFloat(total).toFixed(2);
      return this.form.total;
    },

    minimo_abono: function () {
      let minimo = this.form.descuento_pronto_pago;
      return minimo;
    },

    maximo_abono: function () {
      let maximo = 0;
      if (this.form.pagos_a_cubrir.length > 0) {
        this.form.pagos_a_cubrir.forEach((element) => {
          if (element.status_pago != 2) {
            /**diferente de pagado */
            maximo +=
              Number(element.monto_programado) - Number(element.total_cubierto);
          }
        });
      }
      return maximo.toFixed(2);
    },
    maximo_interes: function () {
      let maximo = 0;
      if (this.form.pagos_a_cubrir.length > 0) {
        /**verificando que el la forma de pago no es remision de deuda, en caso de ser remision de deuda(descuento al capital) este deberia ser 0 */
        if (this.form.formaPago["value"] != 7) {
          this.form.pagos_a_cubrir.forEach((element) => {
            if (element.status_pago != 2) {
              /**diferente de pagado */
              maximo += element.intereses;
            }
          });
        } else {
          /**es descuento directo al capital */
          maximo = 0;
        }
      }
      return parseFloat(maximo).toFixed(2);
    },
    maximo_descuento: function () {
      let maximo = 0;
      if (this.form.pagos_a_cubrir.length > 0) {
        /**verificando que el la forma de pago no es remision de deuda, en caso de ser remision de deuda(descuento al capital) este deberia ser 0 */
        if (this.form.formaPago["value"] != 7) {
          this.form.pagos_a_cubrir.forEach((element) => {
            if (element.status_pago != 2) {
              /**diferente de pagado */
              maximo += element.descuento_pronto_pago;
            }
          });
        } else {
          /**es descuento directo al capital */
          maximo = 0;
        }
      }
      return parseFloat(maximo).toFixed(2);
    },
    maximo_saldo: function () {
      let maximo = 0;
      if (this.form.pagos_a_cubrir.length > 0) {
        this.form.pagos_a_cubrir.forEach((element) => {
          if (element.status_pago != 2) {
            /**diferente de pagado */
            maximo += element.saldo_neto;
          }
        });
      }
      return parseFloat(maximo).toFixed(2);
    },

    maximo_cantidad_pago: function () {
      /**verificando que el la forma de pago no es remision de deuda, en caso de ser remision de deuda(descuento al capital) este deberia ser 0 */
      if (this.form.formaPago["value"] == 7) {
        this.form.pago_con_cantidad = 0;
        return 0;
      } else {
        return this.form.pago_con_cantidad;
      }
    },

    mostrar_datos_operacion: function () {
      if (this.form.datos_operacion) {
        if (this.form.datos_operacion.operacion_id) {
          return true;
        } else return false;
      } else return false;
    },
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },

    getReferencia: {
      get() {
        return this.referencia;
      },
      set(newValue) {
        return newValue;
      },
    },
    fecha_pago_validacion_computed: function () {
      return this.form.fecha_pago;
    },
    forma_pago_validacion_computed: function () {
      return this.form.formaPago;
    },
    cobrador_validacion_computed: function () {
      return this.form.cobrador.value;
    },
  },
  data() {
    return {
      error: "",
      configdateTimePickerWithTime: configdateTimePickerWithTime,
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Precio",
      seleccionar_todos: false,
      FormasPago: [],
      Monedas: [],
      Cobradores: [],
      form: {
        empresa_operaciones_id: "",
        cobrador: {},
        banco: "",
        referencia_sobre_pago: "",
        formaPago: {},
        moneda: {},
        pagos_a_cubrir: [],
        referencia: "",
        multipago: false,
        fecha_pago: "",
        datos_operacion: [],
        /**datos */
        nota: "",
        total: "0",
        abono: "0",
        intereses: "0",
        descuento_pronto_pago: "0",
        banco: "",
        pago_con_cantidad: 0,
        cambio_pago: 0,
      },
      errores: [],
    };
  },
  methods: {
    async get_cobradores() {
      try {
        this.Cobradores = [];
        this.form.cobrador = {};
        let res = await pagos.get_cobradores();
        if (typeof res.data.data !== undefined) {
          if (res.data.data.length > 0) {
            res.data.data.forEach((element) => {
              this.Cobradores.push({
                value: element.id,
                label: element.nombre,
              });
            });
            this.form.cobrador = this.Cobradores[0];
          }
        }
      } catch (err) {
        /**error al cargar vendedores */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar el catálogo de cobradores, por favor reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000",
        });
        this.cerrarVentana();
      }
    },
    /**get monedas */
    async get_monedas_sat() {
      try {
        this.Monedas = [];
        this.form.moneda = {};
        let res = await pagos.get_monedas_sat();
        if (typeof res.data.data !== undefined) {
          if (res.data.data.length > 0) {
            res.data.data.forEach((element) => {
              this.Monedas.push({
                value: element.id,
                codigo_moneda: element.codigo_moneda,
                label: element.descripcion,
              });
            });
            this.form.moneda = this.Monedas[0];
          }
        }
      } catch (err) {
        /**error al cargar vendedores */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar el catálogo de moneda de pago, por favor reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000",
        });
        this.cerrarVentana();
      }
    },
    /**get formas de pago sat */
    async get_formas_pago_sat() {
      try {
        this.FormasPago = [];
        this.form.formaPago = {};
        let res = await pagos.get_formas_pago_sat();
        if (typeof res.data.data !== undefined) {
          if (res.data.data.length > 0) {
            res.data.data.forEach((element) => {
              this.FormasPago.push({
                value: element.id,
                clave: element.clave,
                label: element.forma,
              });
            });
            this.form.formaPago = this.FormasPago[0];
          }
        }
      } catch (err) {
        /**error al cargar vendedores */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar el catálogo de formas de pago, por favor reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000",
        });
        this.cerrarVentana();
      }
    },
    checarPagos(event) {
      let index_pago = 0;
      this.form.pagos_a_cubrir = [];
      if (event.target.checked == true) {
        let index_pago = 0;
        if (this.form.datos_operacion.pagos_programados) {
          this.form.datos_operacion.pagos_programados.forEach((pago) => {
            if (pago.status_pago != 2) {
              /**no pagados */
              this.$refs["pago_seleccionado"][index_pago].$el.querySelector(
                "input"
              ).checked = true;
              this.form.pagos_a_cubrir.push(pago);
              index_pago++;
            }
          });
        }
      }
    },
    checkSeleccionarTodos() {
      this.$nextTick(() => {
        if (this.form.datos_operacion.pagos_programados) {
          if (
            this.form.datos_operacion.pagos_programados.length ==
            this.form.pagos_a_cubrir.length
          ) {
            // Dispatch it.
            this.$refs["seleccionar_todos"].$el.querySelector(
              "input"
            ).checked = true;
            this.seleccionar_todos = true;
            /**se activa el seleccionar todos */
          } else {
            this.$refs["seleccionar_todos"].$el.querySelector(
              "input"
            ).checked = false;
            this.seleccionar_todos = false;
          }
        }
      });
    },

    removeA(arr) {
      var what,
        a = arguments,
        L = a.length,
        ax;
      while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax = arr.indexOf(what)) !== -1) {
          arr.splice(ax, 1);
        }
      }
      return arr;
    },

    async guardar_pago() {
      this.$vs.loading();
      this.error = "";
      this.errores = [];
      try {
        let res = await pagos.guardar_pago(this.form);
        let retornar = {
          id_pago: res.data,
        };
        this.$emit("retorno_pagos", retornar);
        this.$vs.loading.close();
        this.cerrarVentana();
      } catch (err) {
        /**error al cargar vendedores */
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
            //checo si existe cada errores
            this.errores = [];
            this.errores = err.response.data.error;
            this.$vs.notify({
              title: "Registro de Pagos",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          } else if (err.response.status == 409) {
            this.error = err.response.data.error;
            this.$vs.notify({
              title: "Registro de Pagos",
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

    async calcular_adeudo() {
      this.$vs.loading();
      this.error = "";
      this.form.datos_operacion = [];
      this.errores = [];
      try {
        let res = await pagos.calcular_adeudo({
          fecha_pago: this.form.fecha_pago,
          referencia: this.form.referencia,
          multipago: this.form.multipago,
        });
        this.form.empresa_operaciones_id = res.data[0].empresa_operaciones_id;
        this.form.pagos_a_cubrir = [];
        this.form.datos_operacion = res.data[0];
        this.$nextTick(() => {
          let index_pago = 0;
          if (this.form.datos_operacion.pagos_programados.length > 0) {
            this.form.datos_operacion.pagos_programados.forEach((pago) => {
              if (pago.status_pago != 2) {
                this.$refs["pago_seleccionado"][index_pago].$el.querySelector(
                  "input"
                ).onchange = ($event) => {
                  /**revisar si se activo el check */
                  this.checkSeleccionarTodos();
                };
                index_pago++;
              }
            });
          }

          /**checando si se debe de checar el seleccionar todos */
          //seleccionar_todos
          this.$refs["seleccionar_todos"].$el.querySelector(
            "input"
          ).onchange = ($event) => {
            /**revisar si se activo el check */
            this.checarPagos($event);
          };

          this.$refs["seleccionar_todos"].$el.querySelector(
            "input"
          ).checked = true;
          // Dispatch it.
          this.$refs["seleccionar_todos"].$el
            .querySelector("input")
            .dispatchEvent(new Event("change"));
          this.seleccionar_todos = true;
        });

        this.$vs.loading.close();
      } catch (err) {
        /**error al cargar vendedores */
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
            this.errores = err.response.data;
            this.$vs.notify({
              title: "Registro de Pagos",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          } else if (err.response.status == 409) {
            this.error = err.response.data.error;
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Registro de Pagos",
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
    CalcularPago() {
      this.$validator
        .validateAll("form_calcular_adeudo")
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Calcular Adeudo a Pagar",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
          } else {
            /**obteniendo los datos del pago segun la referencia y fecha */
            (async () => {
              await this.calcular_adeudo();
            })();
          }
        })
        .catch(() => {});
    },

    acceptAlert() {
      this.$validator
        .validateAll("pago_form")
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Registro de Pagos",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
          } else {
            /**checando que exista al menos un pago a cubrir */
            if (this.form.pagos_a_cubrir.length > 0) {
              this.errores = [];
              (async () => {
                this.callBackConfirmarAceptar = await this.guardar_pago;
                this.openConfirmarAceptar = true;
              })();
            } else {
              this.$vs.notify({
                title: "Registro de Pagos",
                text: "Verifique que seleccionó al menos un pago para abonar.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                position: "bottom-right",
                time: "8000",
              });
            }
          }
        })
        .catch(() => {});
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
      this.form.datos_operacion = [];
      this.form.referencia = "";
      this.form.multipago = false;
      this.form.total = "0";
      this.form.descuento_pronto_pago = "0";
      this.form.intereses = "0";
      this.form.abono = "0";
      this.form.banco = "";
      this.form.referencia_sobre_pago = "";
      this.form.nota = "";
      this.error = "";
    },

    closeChecker() {
      this.operConfirmar = false;
    },
  },
  created() {},
};
</script>