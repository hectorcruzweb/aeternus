  <template>
  <div class="flex flex-wrap">
    <div class="w-full input-text px-2 mt-5">
      <div class="float-left px-2" v-if="verLista">
        <img width="36px" src="@assets/images/articulos.svg" />
        <h3
          class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
        >
          Servicios y Artículos del Plan Funerario Seleccionado
        </h3>
      </div>
      <div class="flex flex-wrap">
        <div class="w-full mt-5">
          <vx-card no-radius>
            <div class="flex flex-wrap">
              <div class="w-full input-text md:text-right">
                <vs-button
                  size="small"
                  color="success"
                  @click="openBuscadorArticulos = true"
                >
                  <img
                    class="cursor-pointer img-btn"
                    src="@assets/images/boxes.svg"
                  />
                  <span class="texto-btn">Artículos / Servicios</span>
                </vs-button>
              </div>

              <div class="w-full input-text">
                <div class="flex flex-wrap">
                  <div class="w-full input-text">
                    <div class="w-full mt-5">
                      <vs-table
                        class="w-full"
                        :data="form.articulos_servicios"
                        noDataText="No se han agregado Artículos ni Servicios"
                      >
                        <template slot="header">
                          <h3>
                            Servicios y Artículos que Incluye el Servicio
                            Funerario
                          </h3>
                        </template>
                        <template slot="thead">
                          <vs-th>#</vs-th>
                          <vs-th>Tipo</vs-th>
                          <vs-th>Descripción</vs-th>
                          <vs-th>Lote</vs-th>
                          <vs-th>Cant.</vs-th>
                          <vs-th>Costo Neto</vs-th>
                          <vs-th>Descuento</vs-th>
                          <vs-th>Costo Neto Con Descuento</vs-th>
                          <vs-th>Importe</vs-th>
                          <vs-th>Plan Funerario</vs-th>
                          <vs-th>Facturable</vs-th>
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
                                {{ data[indextr].tipo }}
                              </div>
                            </vs-td>
                            <vs-td>
                              <div class="capitalize">
                                {{ data[indextr].descripcion }}
                              </div>
                            </vs-td>
                            <vs-td>
                              <div class="capitalize">
                                {{ data[indextr].num_lote_inventario }}
                              </div>
                            </vs-td>
                            <vs-td>
                              <vs-input
                                :name="'cantidad_articulos_servicios' + indextr"
                                data-vv-as=" "
                                data-vv-validate-on="blur"
                                v-validate="'required|integer|min_value:' + 1"
                                class="w-full sm:w-6/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mr-auto ml-auto cantidad"
                                maxlength="4"
                                v-model="
                                  form.articulos_servicios[indextr].cantidad
                                "
                              />
                              <div>
                                <span>
                                  {{
                                    errors.first(
                                      "cantidad_articulos_servicios" + indextr
                                    )
                                  }}
                                </span>
                              </div>
                            </vs-td>
                            <vs-td
                              v-if="
                                habilitar_plan_funerario_b == false ||
                                (habilitar_plan_funerario_b == true &&
                                  form.plan_funerario_futuro_b.value == 1 &&
                                  form.articulos_servicios[indextr].plan_b ==
                                    0) ||
                                (habilitar_plan_funerario_b == true &&
                                  form.plan_funerario_futuro_b.value == 0 &&
                                  form.plan_funerario_inmediato_b.value == 1)
                              "
                            >
                              <vs-input
                                :name="
                                  'costo_neto_normal_articulos_servicios' +
                                  indextr
                                "
                                data-vv-as=" "
                                data-vv-validate-on="blur"
                                v-validate="'required|decimal:2|min_value:' + 0"
                                class="w-full sm:w-8/12 md:w-7/12 lg:w-7/12 xl:w-7/12 mr-auto ml-auto cantidad"
                                maxlength="10"
                                v-model="
                                  form.articulos_servicios[indextr]
                                    .costo_neto_normal
                                "
                                :disabled="
                                  form.articulos_servicios[indextr]
                                    .descuento_b == 1
                                "
                              />
                              <div>
                                <span>
                                  {{
                                    errors.first(
                                      "costo_neto_normal_articulos_servicios" +
                                        indextr
                                    )
                                  }}
                                </span>
                              </div>
                            </vs-td>
                            <vs-td v-else>
                              <div class="capitalize">$ 0.00</div>
                            </vs-td>
                            <vs-td
                              v-if="
                                habilitar_plan_funerario_b == false ||
                                (habilitar_plan_funerario_b == true &&
                                  form.plan_funerario_futuro_b.value == 1 &&
                                  form.articulos_servicios[indextr].plan_b ==
                                    0) ||
                                (habilitar_plan_funerario_b == true &&
                                  form.plan_funerario_futuro_b.value == 0 &&
                                  form.plan_funerario_inmediato_b.value == 1)
                              "
                            >
                              <vs-switch
                                class="ml-auto mr-auto"
                                color="success"
                                icon-pack="feather"
                                v-model="
                                  form.articulos_servicios[indextr].descuento_b
                                "
                              >
                                <span slot="on">SI</span>
                                <span slot="off">NO</span>
                              </vs-switch>
                            </vs-td>
                            <vs-td v-else>
                              <div class="capitalize">N/A</div>
                            </vs-td>
                            <vs-td
                              v-if="
                                habilitar_plan_funerario_b == false ||
                                (habilitar_plan_funerario_b == true &&
                                  form.plan_funerario_futuro_b.value == 1 &&
                                  form.articulos_servicios[indextr].plan_b ==
                                    0) ||
                                (habilitar_plan_funerario_b == true &&
                                  form.plan_funerario_futuro_b.value == 0 &&
                                  form.plan_funerario_inmediato_b.value == 1)
                              "
                            >
                              <vs-input
                                :name="
                                  'costo_neto_descuento_articulos_servicios' +
                                  indextr
                                "
                                data-vv-as=" "
                                data-vv-validate-on="blur"
                                v-validate="
                                  'required|decimal:2|min_value:' +
                                  0 +
                                  '|max_value:' +
                                  form.articulos_servicios[indextr]
                                    .costo_neto_normal
                                "
                                class="w-full sm:w-8/12 md:w-7/12 lg:w-7/12 xl:w-7/12 mr-auto ml-auto cantidad"
                                maxlength="10"
                                v-model="
                                  form.articulos_servicios[indextr]
                                    .costo_neto_descuento
                                "
                                :disabled="
                                  form.articulos_servicios[indextr]
                                    .descuento_b == 0
                                "
                              />
                              <div>
                                <span>
                                  {{
                                    errors.first(
                                      "costo_neto_descuento_articulos_servicios" +
                                        indextr
                                    )
                                  }}
                                </span>
                              </div>
                            </vs-td>
                            <vs-td v-else>
                              <div class="capitalize">N/A</div>
                            </vs-td>
                            <vs-td
                              v-if="
                                habilitar_plan_funerario_b == false ||
                                (habilitar_plan_funerario_b == true &&
                                  form.plan_funerario_futuro_b.value == 1 &&
                                  form.articulos_servicios[indextr].plan_b ==
                                    0) ||
                                (habilitar_plan_funerario_b == true &&
                                  form.plan_funerario_futuro_b.value == 0 &&
                                  form.plan_funerario_inmediato_b.value == 1)
                              "
                            >
                              <div
                                class="capitalize"
                                v-if="
                                  form.articulos_servicios[indextr]
                                    .descuento_b == 1
                                "
                              >
                                $
                                {{
                                  (form.articulos_servicios[indextr]
                                    .costo_neto_descuento *
                                    form.articulos_servicios[indextr].cantidad)
                                    | numFormat("0,000.00")
                                }}
                              </div>
                              <div class="capitalize" v-else>
                                $
                                {{
                                  (form.articulos_servicios[indextr]
                                    .costo_neto_normal *
                                    form.articulos_servicios[indextr].cantidad)
                                    | numFormat("0,000.00")
                                }}
                              </div>
                            </vs-td>
                            <vs-td v-else>
                              <div class="capitalize">$ 0.00</div>
                            </vs-td>

                            <vs-td v-if="habilitar_plan_funerario_b">
                              <vs-switch
                                class="ml-auto mr-auto"
                                color="success"
                                icon-pack="feather"
                                v-model="
                                  form.articulos_servicios[indextr].plan_b
                                "
                                :disabled="!habilitar_plan_funerario_b"
                              >
                                <span slot="on">SI</span>
                                <span slot="off">NO</span>
                              </vs-switch>
                            </vs-td>
                            <vs-td v-else>
                              <div class="capitalize">N/A</div>
                            </vs-td>
                            <vs-td>
                              <vs-switch
                                class="ml-auto mr-auto"
                                color="success"
                                icon-pack="feather"
                                v-model="
                                  form.articulos_servicios[indextr].facturable_b
                                "
                              >
                                <span slot="on">SI</span>
                                <span slot="off">NO</span>
                              </vs-switch>
                            </vs-td>

                            <vs-td>
                              <div
                                @click="remover_articulo(indextr)"
                                v-if="!fueCancelada"
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

            <!--checkout-->
            <div class="w-full">
              <div class="flex flex-wrap my-6">
                <div class="w-full px-2">
                  <vs-divider />
                </div>
                <div class="w-full sm:w-12/12 md:w-8/12 lg:9/12 px-2">
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
                          Notas / Observaciones Sobre el Contrato
                        </h3>
                      </div>
                    </div>
                    <div class="w-full input-text px-2">
                      <label>NOTA U OBSERVACIÓN:</label>
                      <vs-textarea
                        height="240px"
                        :rows="9"
                        size="large"
                        ref="nota"
                        type="text"
                        class="w-full pt-3 pb-3 large_textarea"
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
                          Total del Contrato Funerario
                        </h3>
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-wrap">
                    <div class="w-full input-text px-2 text-center">
                      <label class="text-xl opacity-75">
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
                          'required|decimal:2|min_value:14|max_value:25'
                        "
                        type="text"
                        class="w-full texto-bold cantidad"
                        placeholder="Porcentaje IVA"
                        v-model="form.tasa_iva"
                        maxlength="2"
                      />
                      <div>
                        <span class="mensaje-requerido">
                          {{ errors.first("tasa_iva") }}
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
                      class="w-full input-text px-2 text-center"
                      v-if="verTotalUsoinmediato"
                    >
                      <label class="text-lg opacity-75"
                        >$ Total de plan de uso inmediato</label
                      >
                      <div class="mt-1 pb-2 text-center">
                        <span class="total_contrato text-xl font-bold">
                          $
                          {{ totalUsoInmediato | numFormat("0,000.00") }}
                        </span>
                      </div>
                    </div>
                    <div class="w-full input-text px-2 text-center">
                      <label class="text-xl opacity-75">$ Total a Pagar</label>
                      <div class="mt-3 text-center">
                        <span class="total_contrato text-3xl font-bold">
                          $
                          {{ totalContrato | numFormat("0,000.00") }}
                        </span>
                      </div>
                    </div>

                    <div class="w-full px-2 mt-2 text-center">
                      <p class="texto-ojo">
                        <span class="font-medium">Ojo:</span>
                        Los costos de los conceptos capturados ya incluyen el
                        IVA.
                      </p>
                      <vs-divider />
                    </div>

                    <div class="w-full input-text px-2">
                      <div class="flex flex-wrap">
                        <vs-button
                          v-if="!fueCancelada"
                          class="w-full ml-auto mr-auto"
                          @click="acceptAlert()"
                          color="success"
                          size="large"
                        >
                          <img
                            width="25px"
                            class="cursor-pointer img-btn"
                            src="@assets/images/save.svg"
                          />
                          <span class="texto-btn">Guardar Contrato</span>
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
  </div>
</template>
  
  
  