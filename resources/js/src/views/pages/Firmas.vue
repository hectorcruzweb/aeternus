<template >
  <div class="centerx">
    <vs-popup
      :title="HeaderNombre"
      :class="['forms-popup popup-50',z_index]"
      :active.sync="showChecker"
      ref="formulario"
    >
      <!-- <img style="width:100px;" src="@assets/images/pdf.svg" alt />-->
      <div class="flex flex-wrap">
        <div class="w-full px-2 pb-6">
          <div class="form-group">
            <div class="title-form-group">{{ documento }}</div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div
                  class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text"
                >
                  <label class=""> Seleccione quien firma </label>
                  <v-select
                    :options="firmasDisponibles"
                    v-model="firmaSeleccionada"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    class="w-full"
                    name="firma"
                    data-vv-as=" "
                  >
                    <div slot="no-options">Seleccione el Firmador</div>
                  </v-select>
                </div>
                <div class="w-full">
                  <div class="w-full text-center mt-12" v-if="!firmado">
                    <vs-button
                      class="w-full sm:w-full sm:w-auto md:w-auto mr-8 my-2 md:mt-0"
                      color="danger"
                      @click="undo"
                    >
                      <span>Limpiar</span>
                    </vs-button>
                    <vs-button
                      class="w-full sm:w-full sm:w-auto md:w-auto ml-8 my-2 md:mt-0"
                      color="success"
                      @click="acceptAlert"
                    >
                      <span>Firmar</span>
                    </vs-button>
                  </div>

                  <div class="signature">
                    <h3 class="mt-12">Registro de Firma Manuscrita</h3>
                    <div class="firma" v-show="!firmado">
                      <VueSignaturePad
                        ref="signaturePad"
                        width="400px"
                        height="200px"
                      />
                    </div>
                    <div v-show="firmado" class="firma">
                      <img :src="firma" width="400px" height="200px" alt="" />
                    </div>
                    <p :class="['color-copy']" v-if="!firmado">
                      Firme dentro de esta área
                    </p>
                    <p :class="['color-danger-900']" v-else>
                      La firma se registró el {{ datosFirma.fecha_hora_firma }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--datos de los reportes-->
          <!--fin de datos de los reportes-->
        </div>
      </div>
      <ConfirmarAceptar
        :show="openConfirmarAceptar"
        :callback-on-success="callBackConfirmar"
        @closeVerificar="openConfirmarAceptar = false"
        :accion="'Firmar Documento'"
        :confirmarButton="'Firmar'"
      ></ConfirmarAceptar>
    </vs-popup>
  </div>
</template>
<script>
import firmas from "@services/firmas";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
export default {
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == false) {
      } else {
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancel();
        };
        /**cargo el reporte que me llega y sus respectivas areas a firmar */
        (async () => {
          await this.get_areas_firmar();
          await this.get_firma();
        })();
        this.resizeCanvas();
      }
    },
    firmaSeleccionada: function (newValue, oldValue) {
      if (newValue.value != "") {
        (async () => {
          await this.get_firma();
        })();
      }
    },
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    header: {
      type: String,
      required: true,
    },
    tipo: {
      type: String,
      required: true,
      default: "operacion",
    },
    operacion_id: {
      type: Number,
      required: true,
    },
    id_documento: {
      type: Array,
      required: true,
      default: {},
    },
    z_index: {
      type: String,
      required: false,
      default: "z-index59k",
    },
  },
  components: {
    "v-select": vSelect,
    ConfirmarAceptar,
  },

  data() {
    return {
      openConfirmarAceptar: false,
      callBackConfirmar: Function,
      firmasDisponibles: [],
      firmaSeleccionada: {},
      documento: "Seleccione un documento",
      verFirma: false,
      firmado: false,
      firma: "",
      errores: [],
      datosFirma: [],
      form: {
        firma: "",
        id_area: 0,
        operacion_id: 0,
        tipo: "",
      },
    };
  },
  computed: {
    showChecker: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    HeaderNombre: {
      get() {
        return this.header;
      },
      set(newValue) {
        return newValue;
      },
    },
    documento_id: {
      get() {
        return this.id_documento;
      },
      set(newValue) {
        return newValue;
      },
    },
    id_operacion: {
      get() {
        return this.operacion_id;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  methods: {
    undo() {
      this.$refs.signaturePad.clearSignature();
    },
    save() {
      const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
      if (!isEmpty) {
        this.form.firma = data;
      } else {
        this.form.firma = "";
      }
      //this.$refs.signaturePad.lockSignaturePad();
    },
    resizeCanvas() {
      let canvas = this.$refs.signaturePad.$el.firstChild;
      var ratio = Math.max(window.devicePixelRatio || 1, 1);
      if (canvas.offsetWidth > 0) {
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
      } else {
        canvas.width = 400;
        canvas.height = 200;
      }
      //this.$refs.signaturePad.clearSignature(); // otherwise isEmpty() might return incorrect value
    },
    cancel() {
      this.$refs.signaturePad.clearSignature();
      this.$emit("closeFirmas");
    },

    async get_firma() {
      this.$vs.loading();
      try {
        let res = await firmas.get_firma(
          this.id_operacion,
          this.firmaSeleccionada.value,
          this.tipo
        );
        let data = [];
        if (res.data.length > 0) {
          data = res.data[0];
          this.firmado = true;
          this.firma = data.firma_path;
          this.datosFirma = data;
        } else {
          this.firma = "";
          this.firmado = false;
          this.datosFirma = [];
        }
        this.$vs.loading.close();
        this.resizeCanvas();
      } catch (err) {
        this.$vs.loading.close();
        this.firmado = false;
        this.datosFirma = [];
        this.firma = "";
        if (err.response) {
          if (err.response.status == 403) {
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000,
            });
          } else if (err.response.status == 422) {
            this.errores = JSON.parse(err.response);
          } else if (err.response.status == 409) {
            this.$vs.notify({
              title: "Ver Reportes",
              text: "Ha ocurrido un error, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 30000,
            });
          }
        }
        this.cancel();
      }
    },

    async get_areas_firmar() {
      this.$vs.loading();
      try {
        let res = await firmas.get_areas_firmar(this.id_documento);
        let data = [];
        if (res.data.length > 0) {
          data = res.data[0];
          this.documento = data.documento;
        }
        this.firmasDisponibles = [];
        if (res.data.length > 0) {
          if (data.areas.length > 0) {
            data.areas.forEach((element) => {
              this.firmasDisponibles.push({
                label: element.area,
                value: element.id,
              });
            });
            this.verFirma = true;
          }
        } else {
          this.firmasDisponibles.push({
            label: "Seleccione 1",
            value: "",
          });
          this.documento = "Seleccione un documento";
        }
        this.firmaSeleccionada = this.firmasDisponibles[0];
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
          } else if (err.response.status == 422) {
            /**error de validacion */
            this.errores = JSON.parse(err.response);
          } else if (err.response.status == 409) {
            //este error es por alguna condicion que el contrano no cumple para modificar
            //la propiedad esa ya ha sido vendido
            this.$vs.notify({
              title: "Ver Reportes",
              text: "Ha ocurrido un error, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 30000,
            });
          }
        }

        this.cancel();
      }
    },

    acceptAlert() {
      this.save();
      if (this.form.firma != "") {
        this.accionNombre = "Registrar Nuevo Usuario";
        this.openConfirmarAceptar = true;
        this.form.id_area = this.firmaSeleccionada.value;
        this.form.operacion_id = this.id_operacion;
        this.form.tipo = this.tipo;
        this.callBackConfirmar = this.firmar;
      } else {
        this.$vs.notify({
          title: "Firmar Documentos",
          text: "No se ha ingresado la firma.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          time: 6000,
        });
        return;
      }
    },

    firmar() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      firmas
        .firmar(this.form)
        .then((res) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: "Firma de Documentos",
            text: "Se ha firmado el documento correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 4000,
          });
          this.$refs.signaturePad.clearSignature();
          this.get_firma();
          //this.$emit("get_data");
          this.resizeCanvas();
          this.cerrarVentana();
        })
        .catch((err) => {
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
                time: 4000,
              });
            } else if (err.response.status == 422) {
              this.$vs.notify({
                title: "Error",
                text: "Verifique que todos los datos han sido capturados",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                position: "bottom-right",
                time: "4000",
              });
              /**error de validacion */
              this.errores = err.response.data.error;
            } else {
              this.$vs.notify({
                title: "Error",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                position: "bottom-right",
                time: "4000",
              });
            }
          }
        });
    },

    /**enviar pdf por mail */
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", (e) => {
      /*if (e.keyCode === 27) {
        if (this.showChecker) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cancel();
        }
      }*/
    });
  },
};
</script>
<style  scoped>
</style>