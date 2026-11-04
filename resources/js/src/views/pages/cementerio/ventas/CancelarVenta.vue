<template>
  <div class="centerx">
    <vs-popup class="forms-popup popup-50 cancelar-venta-compacta" title="Cancelar Venta de Terreno" :active="localShow"
      :ref="this.$options.name">
      <!-- Conserva encabezado y contenedores originales. Las clases nuevas
           solo organizan visualmente el resumen y los campos existentes. -->
      <div class="form-group">
        <div class="title-form-group">Formulario de Cancelación</div>
        <div class="form-group-content">
          <div class="flex flex-wrap">
            <div class="w-full alerta pt-4 pb-6 px-2">
              <div class="danger cancelacion-advertencia">
                <div class="cancelacion-advertencia-titulo">
                  <feather-icon icon="AlertTriangleIcon" class="cancelacion-alerta-icono" aria-hidden="true" />
                  <h3>¿Está seguro de querer cancelar este contrato?</h3>
                </div>
                <p>
                  Una vez realizada la cancelación, no será posible volver a
                  habilitar la venta. Continúe únicamente si está seguro de que es necesario.
                </p>
              </div>
            </div>
            <div class="w-full">
              <div class="flex flex-wrap">
                <div class="w-full xl:w-12/12 px-2">
                  <!--resumen-->
                  <div class="flex flex-wrap">
                    <div class="p-4 w-full mx-auto rounded-lg size-base border-gray-solid-1 cancelacion-resumen">
                      <div class="size-base font-bold color-black-900 uppercase pb-6 text-center">
                        Resumen de la venta
                      </div>
                      <div class="flex flex-wrap color-copy">
                        <div class="w-full">
                          <div class="flex flex-wrap pb-6 cancelacion-ubicacion">
                            <div class="w-full text-center font-medium color-black-900 uppercase">
                              <feather-icon icon="MapPinIcon" aria-hidden="true" /> Ubicación de la propiedad
                            </div>
                            <div class="w-full text-center font-medium color-copy pt-2">
                              <span class="capitalize">
                                <span class="capitalize" v-if="datosVenta.venta_terreno">{{
                                  datosVenta.venta_terreno.ubicacion_texto
                                }}</span>
                              </span>
                            </div>
                          </div>
                        </div>

                        <div class="w-full">
                          <div class="flex flex-wrap cancelacion-titular">
                            <div class="w-full xl:w-6/12 text-center font-medium color-black-900 uppercase">
                              <feather-icon icon="UserIcon" aria-hidden="true" /> Titular de la Propiedad
                            </div>
                            <div class="w-full xl:w-6/12 text-center font-medium color-copy">
                              <span class="capitalize" v-if="datosVenta.nombre">{{ datosVenta.nombre }}</span>
                            </div>
                          </div>
                        </div>

                        <div class="w-full">
                          <div class="flex flex-wrap mt-2 theme-background py-2 cancelacion-capital">
                            <div class="w-full text-center font-medium color-black-900 uppercase">
                              Capital cubierto a la fecha
                            </div>
                            <div class="w-full text-center font-medium color-black-700 uppercase pt-2">
                              <span class="capitalize" v-if="datosVenta">
                                $
                                {{
                                  datosVenta.abonado_capital
                                  | numFormat("0,000.00")
                                }}
                                <small>MXN</small>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--resumen-->
                </div>
                <div class="w-full xl:w-12/12">
                  <div class="flex flex-wrap cancelacion-campos">
                    <div class="w-full xl:w-4/12 input-text px-2">
                      <label><feather-icon icon="DollarSignIcon" aria-hidden="true" /> Monto a devolver · MXN</label>
                      <vs-input v-model="form.cantidad" v-validate="'required|decimal:2|min_value:0'" name="cantidad"
                        data-vv-as=" " type="text" class="w-full" placeholder=" $ 00.00 Pesos MXN" maxlength="7" />
                      <span>{{ errors.first("cantidad") }}</span>
                      <span v-if="this.errores.cantidad">{{
                        errores.cantidad[0]
                        }}</span>
                    </div>

                    <div class="w-full xl:w-8/12 input-text px-2">
                      <label><feather-icon icon="ListIcon" aria-hidden="true" /> Motivo de cancelación</label>
                      <v-select :options="motivos" :clearable="false" v-model="form.motivo"
                        :dir="$vs.rtl ? 'rtl' : 'ltr'" class="w-full large_select"
                        v-validate:motivo_computed.immediate="'required'" name="plan_venta" data-vv-as=" ">
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
                  <label><feather-icon icon="MessageSquareIcon" aria-hidden="true" /> Comentario de la cancelación</label>
                  <vs-textarea class="w-full cancelacion-detalle" placeholder="Detalle de la cancelación…" height="120px"
                    aria-label="Detalle de la cancelación"
                    v-model="form.comentario" ref="comentario" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bottom-buttons-section">
        <div class="text-advice">
          <feather-icon icon="InfoIcon" aria-hidden="true" />
          Revise la información antes de continuar.
        </div>
        <div class="w-full">
          <vs-button v-if="!fueCancelada" class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0" color="danger"
            @click="acceptAlert()">
            <feather-icon icon="FileMinusIcon" aria-hidden="true" />
            <span>Cancelar contrato</span>
          </vs-button>
        </div>
      </div>
      <Password v-if="openConfirmar" :show="openConfirmar" :callback-on-success="callback"
        @closeVerificar="openConfirmar = false" :accion="'Cancelar venta de propiedad'"></Password>
      <ConfirmarDanger v-if="openConfirmarSinPassword" :z_index="'z-index58k'" :show="openConfirmarSinPassword"
        :callback-on-success="callBackConfirmar" @closeVerificar="openConfirmarSinPassword = false"
        :accion="accionConfirmarSinPassword" :confirmarButton="botonConfirmarSinPassword"></ConfirmarDanger>
    </vs-popup>
  </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import vSelect from "vue-select";
import cementerio from "@services/cementerio";
import ConfirmarDanger from "@pages/ConfirmarDanger";
export default {
  name: "CancelarVentaCementerio",
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
    id_venta: {
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
            let res = await cementerio.consultar_venta_id(this.getVentaId);
            this.datosVenta = res.data[0];
            this.$popupManager.register(this, this.cancelar, "comentario");
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
          } finally {
            this.$vs.loading.close();
          }
        }
        this.localShow = newValue;
      },
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
    getVentaId: {
      get() {
        return this.id_venta;
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
        venta_id: "",
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
              this.callback = await this.cancelar_venta;
              this.openConfirmar = true;
            })();
          }
        })
        .catch(() => { });
    },

    cancelar() {
      /*this.botonConfirmarSinPassword = "Salir y limpiar";
      this.accionConfirmarSinPassword =
        "Esta acción limpiará los datos que capturó en el formulario.";
      this.openConfirmarSinPassword = true;
      this.callBackConfirmar = this.cerrarVentana;
      */
      this.$emit("closeCancelarVenta");
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

    async cancelar_venta() {
      this.errores = [];
      this.$vs.loading();
      this.form.venta_id = this.getVentaId;
      try {
        let res = await cementerio.cancelar_venta(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Ventas de Propiedades",
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
            title: "Ventas de Propiedades",
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
            if (this.errores.venta_id) {
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
<style>
/* Remodelación visual aislada: conserva el header nativo de forms-popup,
   sus contenedores, validaciones y confirmaciones originales. */
.cancelar-venta-compacta .vs-popup {
  background: #fff !important;
  display: flex; flex-direction: column;
  max-height: calc(100vh - 32px) !important;
  overflow: hidden;
}
/* El cuerpo usa el espacio restante; no combina el 90% global con el header
   y sus márgenes, combinación que hacía desbordar el scroll por debajo. */
.cancelar-venta-compacta .vs-popup--header { flex: 0 0 auto; }
.cancelar-venta-compacta .vs-popup--content {
  color: #555d64; flex: 1 1 auto; min-height: 0;
  height: auto !important; max-height: none !important;
  width: 100% !important; margin: 0 !important;
  overflow-y: auto; overflow-x: hidden;
}
.cancelar-venta-compacta .form-group { margin-bottom: 14px; padding: 22px !important; }
.cancelar-venta-compacta .form-group-content { padding: 10px 0 0 !important; }
.cancelar-venta-compacta .alerta { padding-top: 0 !important; padding-bottom: 18px !important; }
.cancelar-venta-compacta .cancelacion-advertencia {
  position: relative; padding: 15px 17px !important;
  background: #fcf4f4 !important; border-left: 2px solid #b94b50 !important;
  color: #a74449 !important;
}
.cancelar-venta-compacta .cancelacion-advertencia-titulo { display: flex; align-items: flex-start; gap: 10px; }
.cancelar-venta-compacta .cancelacion-alerta-icono {
  position: static !important; transform: none !important;
  flex: 0 0 20px; display: inline-flex; margin-top: 1px;
}
.cancelar-venta-compacta .cancelacion-alerta-icono svg { width: 20px; height: 20px; }
.cancelar-venta-compacta .cancelacion-advertencia h3 { margin: 0; font-size: 14px; font-weight: 600; line-height: 1.5; }
.cancelar-venta-compacta .cancelacion-advertencia p { margin: 9px 0 0 30px; font-size: 13px; line-height: 1.7; color: #8c6568 !important; }
.cancelar-venta-compacta .cancelacion-resumen { padding: 17px !important; margin-bottom: 18px; }
.cancelar-venta-compacta .cancelacion-resumen > div:first-child { font-size: 12px; padding-bottom: 20px !important; }
.cancelar-venta-compacta .cancelacion-ubicacion { padding-bottom: 20px !important; }
.cancelar-venta-compacta .cancelacion-resumen .font-medium { font-size: 13px; }
/* Las utilidades uppercase/color y los estilos globales de labels son
   importantes; neutralizarlos aquí para igualar la propuesta aprobada. */
.cancelar-venta-compacta .cancelacion-resumen .uppercase { text-transform: none !important; }
.cancelar-venta-compacta .cancelacion-resumen > div:first-child { text-transform: uppercase !important; }
.cancelar-venta-compacta .cancelacion-ubicacion > div:first-child,
.cancelar-venta-compacta .cancelacion-titular > div:first-child { color: #7a838a !important; font-weight: 400 !important; }
.cancelar-venta-compacta .cancelacion-ubicacion > div:last-child,
.cancelar-venta-compacta .cancelacion-titular > div:last-child { font-weight: 600 !important; color: #555d64 !important; }
.cancelar-venta-compacta .cancelacion-titular { display: grid !important; grid-template-columns: 1fr 1.3fr; align-items: center; gap: 12px; }
.cancelar-venta-compacta .cancelacion-titular > div { width: 100% !important; }
.cancelar-venta-compacta .cancelacion-resumen .feather-icon { display: inline-flex; vertical-align: middle; margin-right: 5px; color: #7a838a; }
.cancelar-venta-compacta .cancelacion-resumen .feather-icon svg { width: 15px; height: 15px; }
.cancelar-venta-compacta .cancelacion-capital {
  align-items: center; justify-content: space-between; gap: 10px;
  margin-top: 16px !important; padding: 12px !important; background: #f7f7f5 !important;
}
.cancelar-venta-compacta .cancelacion-capital > div { width: auto !important; padding-top: 0 !important; }
.cancelar-venta-compacta .cancelacion-capital > div:first-child { font-size: 12px; text-transform: none !important; }
.cancelar-venta-compacta .cancelacion-capital > div:last-child { font-size: 19px; color: #95771f !important; }
.cancelar-venta-compacta .cancelacion-capital small { font-size: 10px; }
.cancelar-venta-compacta .cancelacion-campos {
  /* La utilidad global .flex es !important; sobrescribirla explícitamente. */
  display: grid !important; grid-template-columns: minmax(0, 1fr) minmax(0, 1.7fr);
  width: 100%;
}
.cancelar-venta-compacta .cancelacion-campos > div { width: 100% !important; min-width: 0; }
.cancelar-venta-compacta .input-text label {
  display: flex; align-items: center; gap: 7px; font-size: 12px;
  font-weight: 400 !important; text-transform: none !important; color: #646c73 !important; margin-bottom: 8px;
}
.cancelar-venta-compacta .input-text label .feather-icon { color: #7a838a !important; }
.cancelar-venta-compacta .vs-input--input,
.cancelar-venta-compacta .vs__selected,
.cancelar-venta-compacta .vs-textarea { font-size: 14px !important; font-weight: 400 !important; color: #555d64 !important; }
.cancelar-venta-compacta .vs__selected { text-transform: lowercase; }
.cancelar-venta-compacta .vs__selected::first-letter { text-transform: uppercase; }
.cancelar-venta-compacta .input-text label svg { width: 16px; height: 16px; }
.cancelar-venta-compacta .vs-input--input,
.cancelar-venta-compacta .vs__dropdown-toggle,
.cancelar-venta-compacta .vs-con-textarea { border-radius: 0 !important; }
/* Placeholder real en lugar del h4 de Vuesax: evita el título grande/negrita
   dentro del campo y conserva v-model y ref usados por el formulario. */
.cancelar-venta-compacta .cancelacion-detalle {
  border: 1px solid #d2d7da !important;
  background: #fff !important; padding: 0 !important; margin-bottom: 0;
}
.cancelar-venta-compacta .cancelacion-detalle .vs-textarea {
  box-sizing: border-box; width: 100%; height: 120px; min-height: 120px;
  padding: 13px 14px !important; margin: 0;
  font-family: inherit; font-size: 14px !important; font-weight: 400 !important;
  line-height: 1.6; color: #555d64 !important; background: #fff;
  text-transform: none !important; resize: none !important;
}
.cancelar-venta-compacta .cancelacion-detalle .vs-textarea::placeholder {
  color: #858b90; opacity: 1; font-weight: 400; font-size: 14px;
}
.cancelar-venta-compacta .cancelacion-detalle.focusx {
  border-color: #ae8b2a !important; box-shadow: 0 0 0 2px rgba(175,139,40,.12);
}
.cancelar-venta-compacta .bottom-buttons-section {
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
  gap: 14px; padding: 8px 8px 14px;
}
.cancelar-venta-compacta .bottom-buttons-section .text-advice {
  display: flex; align-items: center; gap: 7px; margin: 0; font-size: 11px;
}
.cancelar-venta-compacta .bottom-buttons-section .text-advice svg { width: 16px; height: 16px; }
.cancelar-venta-compacta .bottom-buttons-section > .w-full { width: auto; margin-left: auto; }
.cancelar-venta-compacta .bottom-buttons-section .vs-button {
  background: #b64c50 !important; border-radius: 0 !important;
  font-size: 13px !important; text-transform: none !important;
}
.cancelar-venta-compacta .bottom-buttons-section .vs-button:hover { background: #963b3f !important; }
.cancelar-venta-compacta .bottom-buttons-section .vs-button-text { display: inline-flex; align-items: center; gap: 8px; }
.cancelar-venta-compacta .bottom-buttons-section .vs-button-text svg { width: 16px; height: 16px; }
@media (max-width: 600px) {
  .cancelar-venta-compacta .form-group { padding: 18px 8px !important; }
  .cancelar-venta-compacta .cancelacion-titular { grid-template-columns: 1fr; }
  .cancelar-venta-compacta .cancelacion-campos { grid-template-columns: 1fr; }
  .cancelar-venta-compacta .cancelacion-capital { justify-content: center; }
}
</style>
