<template>
  <div class="centerx">
    <vs-popup class="forms-popup popup-50" title="Cancelar Solicitud" :active="localShow" :ref="this.$options.name">
      <div class="form-group">
        <div class="title-form-group">Formulario de Cancelación</div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full alerta pt-4 pb-6 px-2">
              <div class="danger">
                <h3>¿Está seguro de querer cancelar este contrato?</h3>
                <p>
                  Una vez realizado el proceso de cancelación no habrá manera de
                  volver a habilitar este servicio. Es recomendable llevar a
                  cabo este proceso una vez esté seguro de que es necesario.
                </p>
              </div>
            </div>
            <div class="w-full">
              <div class="flex flex-wrap">
                <div class="w-full xl:w-12/12 px-2">
                  <div class="flex flex-wrap">
                    <div class="p-4 w-full mx-auto rounded-lg size-base border-gray-solid-1 rounded-lg">
                      <div class="size-base font-bold color-black-900 uppercase pb-6 text-center">
                        Resumen del Servicio
                      </div>
                      <div class="flex flex-wrap color-copy">
                        <div class="w-full">
                          <div class="flex flex-wrap pb-6">
                            <div class="w-full text-center font-medium color-black-900 uppercase">
                              Tipo de Servicio
                            </div>
                            <div class="w-full text-center font-medium color-copy pt-2">
                              <span class="capitalize">
                                Servicio Funerario
                              </span>
                            </div>
                          </div>
                        </div>

                        <div class="w-full">
                          <div class="flex flex-wrap">
                            <div class="w-full xl:w-6/12 text-center font-medium color-black-900 uppercase">
                              Nombre del Finado
                            </div>
                            <div class="w-full xl:w-6/12 text-center font-medium color-copy">
                              <span class="capitalize" v-if="datosVenta.nombre_afectado">{{ datosVenta.nombre_afectado
                              }}</span>
                            </div>
                          </div>
                        </div>

                        <div class="w-full">
                          <div class="flex flex-wrap mt-2 theme-background py-2">
                            <div class="w-full text-center font-medium color-black-900 uppercase">
                              Cantidad cubierta de capital hasta la fecha:
                            </div>
                            <div class="w-full text-center font-medium color-black-700 uppercase pt-2">
                              <span class="capitalize" v-if="datosVenta.operacion">
                                $
                                {{
                                  datosVenta.operacion.total_cubierto
                                  | numFormat("0,000.00")
                                }}
                                Pesos mxn
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="w-full xl:w-12/12 pt-6">
                  <div class="flex flex-wrap">
                    <div class="w-full xl:w-4/12 input-text px-2">
                      <label>$ Monto a devolver:</label>
                      <vs-input v-model="form.cantidad" v-validate="'required|decimal:2|min_value:0'" name="cantidad"
                        data-vv-as=" " type="text" class="w-full" placeholder=" $ 00.00 Pesos MXN" maxlength="7" />
                      <span>{{ errors.first("cantidad") }}</span>
                      <span v-if="this.errores.cantidad">{{
                        errores.cantidad[0]
                      }}</span>
                    </div>

                    <div class="w-full xl:w-8/12 input-text px-2">
                      <label>Seleccione un motivo de cancelación:</label>
                      <v-select :options="motivos" :clearable="false" v-model="form.motivo"
                        :dir="$vs.rtl ? 'rtl' : 'ltr'" class="w-full " v-validate:motivo_computed.immediate="'required'"
                        name="plan_venta" data-vv-as=" ">
                        <div slot="no-options">
                          No Se Ha Seleccionado Ningún Motivo
                        </div>
                      </v-select>
                      <span>{{ errors.first("motivo") }}</span>
                      <span v-if="this.errores['motivo.value']">{{
                        errores["motivo.value"][0]
                      }}</span>
                    </div>
                  </div>
                </div>

                <div class="w-full input-text px-2">
                  <label>Agregue un comentario respecto a la cancelación de esta
                    venta:</label>
                  <vs-textarea class="w-full" label="Detalle de la cancelación..." height="170px"
                    v-model="form.comentario" ref="comentario" />
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
          <vs-button v-if="!fueCancelada" class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="danger"
            @click="acceptAlert()">
            <span>Cancelar Contrato</span>
          </vs-button>
        </div>
      </div>

      <Password v-if="openConfirmar" :show="openConfirmar" :callback-on-success="callback"
        @closeVerificar="openConfirmar = false" :accion="'Cancelar servicio funerario'"></Password>

      <ConfirmarDanger v-if="openConfirmarSinPassword" :show="openConfirmarSinPassword"
        :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
        :accion="accionConfirmarSinPassword" :confirmarButton="botonConfirmarSinPassword"></ConfirmarDanger>
    </vs-popup>
  </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import vSelect from "vue-select";
import funeraria from "@services/funeraria";
import ConfirmarDanger from "@pages/ConfirmarDanger";
export default {
  name: "CancelarVentaServicioFunerario",
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
    id_solicitud: {
      type: Number,
      required: true,
      default: 0,
    },
  },
  watch: {
    show: {
      immediate: true, // runs when component is mounted too
      async handler(newValue) {
        if (newValue) {
          this.form.cantidad = "0.00";
          this.$vs.loading();
          try {
            let res = await funeraria.get_solicitudes_servicios_id(
              this.getSolicitudId
            );
            this.datosVenta = res.data[0];
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
                  time: 4000,
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
          this.$popupManager.register(this, this.cancelar, "comentario");
        } else {
          this.$popupManager.unregister(this.$options.name);
        }
        this.localShow = newValue;
      },
    },
  },
  computed: {
    motivo_computed: function () {
      return this.form.motivo.value;
    },
    getSolicitudId: {
      get() {
        return this.id_solicitud;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      localShow: false,
      botonConfirmarSinPassword: "",
      accionConfirmarSinPassword: "",
      callBackConfirmar: Function,
      openConfirmarSinPassword: false,
      callback: Function,
      openConfirmar: false,
      errores: [],
      datosVenta: [],
      motivos: [
        {
          label: "FALTA DE PAGO",
          value: 1,
        },
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
        solicitud_id: "",
        motivo: {
          label: "FALTA DE PAGO",
          value: 1,
        },
        cantidad: "0.00",
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
              time: "4000",
            });
            return;
          } else {
            (async () => {
              this.callback = await this.cancelar_solicitud;
              this.openConfirmar = true;
            })();
          }
        })
        .catch(() => { });
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
      this.form.comentario = "";
      this.form.cantidad = 0;
      (this.form.motivo = {
        label: "FALTA DE PAGO",
        value: 1,
      }),
        this.$emit("closeCancelarVenta");
      return;
    },
    async cancelar_solicitud() {
      this.errores = [];
      this.$vs.loading();
      this.form.solicitud_id = this.getSolicitudId;
      try {
        let res = await funeraria.cancelar_solicitud(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Servicios Funerarios",
            text: "Se ha cancelado la venta correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.$emit("closeCancelarVenta", res.data);
          this.$emit("ConsultarVenta", res.data);
        } else {
          this.$vs.notify({
            title: "Servicios Funerarios",
            text: "Error al cancelar la venta, por favor reintente.",
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
            if (this.errores.solicitud_id) {
              //la propiedad esa ya ha sido vendida
              this.$vs.notify({
                title: "Seleccione una venta",
                text: "No se ha seleccionado una venta a cancelar",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            }

            this.$vs.notify({
              title: "Cancelar Venta",
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
              title: "Cancelar información de la venta",
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
  },
  // Lifecycle hooks
  created() {
    this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
  },
  mounted() {
    this.$log("Component mounted! " + this.$options.name);
  },
  beforeDestroy() {
    this.$popupManager.unregister(this.$options.name);
  },
  destroyed() {
    this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
  },
};
</script>
