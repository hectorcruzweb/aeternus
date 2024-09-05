<template >
  <div class="centerx">
    <vs-popup
      :class="['forms-popup popup-50',z_index]"
      title="Cancelar Movimientos de Cobranza"
      :active.sync="showVentana"
      ref="cancelar_pago"
    >
      <div class="form-group">
        <div class="title-form-group">Formulario de Cancelación</div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full alerta pt-4 pb-6 px-2">
              <div class="danger">
                <h3>¿Está seguro de querer cancelar este pago?</h3>
                <p>
                  Una vez realizado el proceso de cancelación no habrá manera de
                  volver a habilitar este pago. Es recomendable llevar a cabo
                  este proceso una vez esté seguro de que es necesario.
                </p>
              </div>
            </div>
            <div class="w-full">
              <div class="flex flex-wrap">
                <div class="w-full xl:w-12/12 px-2">
                  <div class="flex flex-wrap">
                    <div
                      class="p-4 w-full mx-auto rounded-lg size-base border-gray-solid-1 rounded-lg"
                    >
                      <div
                        class="size-base font-bold color-black-900 uppercase pb-6 text-center"
                      >
                        Resumen del Pago
                      </div>
                      <div class="flex flex-wrap color-copy">
                        <div class="w-full">
                          <div class="flex flex-wrap pb-6">
                            <div
                              class="w-full text-center font-medium color-black-900 uppercase"
                            >
                              Operación de Tipo
                            </div>
                            <div
                              class="w-full text-center font-medium color-copy pt-2"
                            >
                              <span
                                class="capitalize"
                                v-if="datosPago.tipo_operacion_texto"
                                >{{ datosPago.tipo_operacion_texto }}</span
                              >
                            </div>
                          </div>
                        </div>

                        <div class="w-full">
                          <div
                            class="flex flex-wrap mt-2 theme-background py-2"
                          >
                            <div
                              class="w-full text-center font-medium color-black-900 uppercase"
                            >
                            
                              Saldo de la Operación Hasta la Fecha: {{
                                     saldo_operacion
                                        | numFormat("0,000.00")
                                    }}
                                    Pesos mxn:
                            </div>
                            <div
                              class="w-full text-center font-medium color-black-700 uppercase pt-2"
                            >
                              <div class="py-1" v-if="datosPago">
                                <div class="capitalize">
                                  Clave
                                  <span class="font-medium text-black">{{
                                    datosPago.id
                                  }}</span
                                  >, {{ datosPago.movimientos_pagos_texto }}, $
                                  <span class="font-medium text-black"
                                    >{{
                                      datosPago.monto_pago
                                        | numFormat("0,000.00")
                                    }}
                                    Pesos mxn</span
                                  >
                                </div>
                                <div
                                  class="capitalize py-2"
                                  :key="indextr"
                                  v-for="(
                                    subpago, indextr
                                  ) in datosPago.subpagos"
                                >
                                  Clave
                                  <span class="font-medium text-black">{{
                                    subpago.id
                                  }}</span
                                  >, {{ subpago.movimientos_pagos_texto }}, $
                                  <span class="font-medium text-black"
                                    >{{
                                      subpago.monto_pago | numFormat("0,000.00")
                                    }}
                                    Pesos mxn</span
                                  >
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="w-full xl:w-12/12 pt-6">
                  <div class="flex flex-wrap">
                    <div class="w-full input-text px-2">
                      <label>Seleccione un motivo de cancelación:</label>
                      <v-select
                        :options="motivos"
                        :clearable="false"
                        v-model="form.motivo"
                        :dir="$vs.rtl ? 'rtl' : 'ltr'"
                        class="pb-1 pt-1 large_select"
                        v-validate:motivo_computed.immediate="'required'"
                        name="plan_venta"
                        data-vv-as=" "
                      >
                        <div slot="no-options">
                          No Se Ha Seleccionado Ningún Motivo
                        </div>
                      </v-select>
                      <span class="text-danger text-sm">{{
                        errors.first("motivo")
                      }}</span>
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores['motivo.value']"
                        >{{ errores["motivo.value"][0] }}</span
                      >
                    </div>
                  </div>
                </div>

                <div class="w-full input-text px-2">
                  <label
                    >Agregue un comentario respecto a la cancelación de este
                    movimiento:</label
                  >
                  <vs-textarea
                    class="w-full"
                    label="Detalle de la cancelación..."
                    height="170px"
                    v-model="form.comentario"
                    ref="comentario"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="bottom-buttons-section">
        <div class="text-advice">
          <span class="ojo-advice">Ojo:</span>
          Por favor revise la información ingresada, si todo es correcto de
          click en el "Botón de Abajo”.
        </div>

        <div class="w-full">
          <vs-button
            v-if="!fueCancelada"
            class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
            color="danger"
            @click="acceptAlert()"
          >
            <span>Cancelar Pago</span>
          </vs-button>
        </div>
      </div>

      <Password
        :show="openConfirmar"
        :callback-on-success="callback"
        @closeVerificar="openConfirmar = false"
        :accion="'Cancelar Movimiento de Cobranza'"
      ></Password>

      <ConfirmarDanger
        :z_index="'z-index59k'"
        :show="openConfirmarSinPassword"
        :callback-on-success="callBackConfirmar"
        @closeVerificar="openConfirmarSinPassword = false"
        :accion="accionConfirmarSinPassword"
        :confirmarButton="botonConfirmarSinPassword"
      ></ConfirmarDanger>
    </vs-popup>
  </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import vSelect from "vue-select";
import pagos from "@services/pagos";
import ConfirmarDanger from "@pages/ConfirmarDanger";
export default {
  components: {
    Password,
    "v-select": vSelect,
    ConfirmarDanger,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    id_pago: {
      type: Number,
      required: true,
      default: 0,
    },
    z_index: {
      type: String,
      required: false,
      default: "z-index55k",
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["cancelar_pago"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() => {
          this.$refs["comentario"].$el.querySelector("textarea").focus();
        });

        (async () => {
          this.$vs.loading();
          try {
            let res = await pagos.get_pago_id(this.getPagoId);
            this.datosPago = res.data[0];
            this.saldo_operacion=this.datosPago.referencias_cubiertas[0].operacion_del_pago.saldo
            this.$vs.loading.close();
          } catch (err) {
            this.$vs.loading.close();
            if (err.response) {
              if (err.response.status == 403) {
                this.$vs.notify({
                  title: "Permiso denegado",
                  text:
                    "Verifique sus permisos con el administrador del sistema.",
                  iconPack: "feather",
                  icon: "icon-alert-circle",
                  color: "warning",
                  time: 8000,
                });
              } else {
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
              }
            }
            this.cerrarVentana();
          }
        })();
      }
    },
  },
  computed: {
    motivo_computed: function () {
      return this.form.motivo.value;
    },
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    getPagoId: {
      get() {
        return this.id_pago;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      botonConfirmarSinPassword: "",
      accionConfirmarSinPassword: "",
      callBackConfirmar: Function,
      openConfirmarSinPassword: false,
      callback: Function,
      openConfirmar: false,
      errores: [],
      datosPago: [],
      saldo_operacion:0,
      motivos: [
        {
          label: "A PETICIÓN DEL CLIENTE",
          value: 2,
        },
        {
          label: "ERROR AL CAPTURAR",
          value: 3,
        },
      ],
      form: {
        pago_id: "",
        motivo: {
          label: "ERROR AL CAPTURAR",
          value: 3,
        },
        comentario: "",
      },
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
              time: "8000",
            });
            return;
          } else {
            (async () => {
              this.callback = await this.cancelar_pago;
              this.openConfirmar = true;
            })();
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
      this.$emit("closeCancelarPago");
      return;
    },
    async cancelar_pago() {
      this.errores = [];
      this.$vs.loading();
      this.form.pago_id = this.getPagoId;
      try {
        let res = await pagos.cancelar_pago(this.form);
        this.$vs.loading.close();
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Movimientos de Cobranza",
            text: "Se ha cancelado el movimiento correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.limpiarVentana();
          this.$emit("retorno_pago", res.data);
        } else {
          this.$vs.notify({
            title: "Movimientos de Cobranza",
            text: "Error al cancelar el movimiento, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
        }
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
              time: 8000,
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;
            if (this.errores.pago_id) {
              //la propiedad esa ya ha sido vendida
              this.$vs.notify({
                title: "Movimientos de Cobranza",
                text: "No se ha seleccionado un movimiento a cancelar",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            }

            this.$vs.notify({
              title: "Movimientos de Cobranza",
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
              title: "Movimientos de Cobranza",
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
    limpiarVentana() {
      this.form.comentario = "";
      this.form.motivo = {
        label: "ERROR AL CAPTURAR",
        value: 3,
      };
    },
  },
  created() {},
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", (e) => {
      if (e.keyCode === 27) {
        if (this.showVentana) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          //this.cancelar();
        }
      }
    });
  },
};
</script>
