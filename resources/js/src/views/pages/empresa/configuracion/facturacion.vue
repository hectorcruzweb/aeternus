<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4">
        <vs-card class="cardx card-tarifas" fixedHeight>
          <!--Datos de la firma-->
          <div class="form-group mt-6">
            <div class="title-form-group">
              <span>Certificado digital</span>
            </div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text text-center firma-digital"
                >
                  <label class="">
                    Certificado Digital (.cer)
                    <span class="">(*)</span>
                  </label>
                  <vs-upload
                    accept=".cer"
                    single-upload
                    ref="uploadCer"
                    text="Certificado(.cer)"
                    limit="1"
                    :headers="requestHeaders"
                    fileName="certificate"
                    automatic
                    action="/empresa/facturacion/validateCER"
                    @on-success="successCertificate"
                    @on-error="errorCertificate"
                    @on-delete="clearCertificate"
                  />
                  <span class="" v-show="certificateError"
                    >No se ha subido archivo de certificado</span
                  >
                  <div class="">
                    <span class="" v-if="this.errores.nombre_comercial">{{
                      errores.nombre_comercial[0]
                    }}</span>
                  </div>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text text-center firma-digital"
                >
                  <label class="">
                    Llave Privada (.key)
                    <span class="">(*)</span>
                  </label>
                  <vs-upload
                    accept=".key"
                    single-upload
                    ref="uploadKey"
                    text="Llave Privada (.key)"
                    limit="1"
                    :headers="requestHeaders"
                    fileName="key"
                    automatic
                    action="/empresa/facturacion/validateKEY"
                    @on-success="successKey"
                    @on-error="errorKey"
                    @on-delete="clearKey"
                  />
                </div>
                <div class="w-full py-6 px-2 text-center">
                  <div
                    v-if="this.certificado_llave_capturados"
                    class="size-base color-success-900"
                  >
                    El sistema ya tiene registrado una firma electrónica, si
                    desea actualizar los archivos súbalos por favor.
                  </div>
                  <div
                    v-if="this.certificado_llave_validos"
                    class="size-base color-copy"
                  >
                    Seleccione certificado digital (.cer) y llave privada (.key)
                    que desea actualizar.
                  </div>
                  <div v-else class="size-base color-success-900">
                    Certificado (.cer) válido hasta {{ fecha_validez }}
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Contraseña
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="password"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="35"
                    type="password"
                    class="w-full"
                    placeholder="Contraseña"
                    v-model="form.password"
                  />
                  <span class="">{{ errors.first("password") }}</span>
                  <span class="" v-if="this.errores.password">{{
                    errores.password[0]
                  }}</span>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Repetir Contraseña
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    type="password"
                    name="passwordRepetir"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="35"
                    class="w-full"
                    placeholder="Repetir Contraseña"
                    v-model="form.passwordRepetir"
                  />
                  <span class="">{{ errors.first("passwordRepetir") }}</span>
                  <span class="" v-if="this.errores.passwordRepetir">{{
                    errores.passwordRepetir[0]
                  }}</span>
                </div>
              </div>
            </div>
          </div>
          <!--Datos de la firma-->

          <div class="bottom-buttons-section">
            <div class="text-advice">
              <span class="ojo-advice">Ojo:</span>
              Por favor revise la información ingresada, si todo es correcto de
              click en el "Botón de Abajo”.
            </div>
            <div class="w-full">
              <vs-button
                class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
                color="primary"
                @click="mandarModificar()"
              >
                <span>Actualizar Firma Electrónica</span>
              </vs-button>
            </div>
          </div>
        </vs-card>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from "vue-select";
import empresa from "@services/empresa";
import { tr } from "date-fns/locale";
export default {
  components: {
    "v-select": vSelect,
  },
  props: {
    datos: {
      type: Object,
      required: true,
      default: {},
    },
    erroresForm: {
      type: Object,
      required: true,
      default: {},
    },
  },
  watch: {
    datos: function (newValue, oldValue) {
      this.mostrarDatos();
    },
  },
  computed: {
    certificado_llave_capturados: function () {
      if (this.form.cerPath != null && this.form.keyPath != null) {
        return true;
      } else {
        return false;
      }
    },

    certificado_llave_validos: function () {
      if (this.form.certificateFile != null && this.form.keyFile != null) {
        return false;
      } else {
        return true;
      }
    },
    //decide si es posbile mandar el form  para actualizar
    validar_certificado_actualizar: function () {
      if (!this.certificado_llave_capturados) {
        if (!this.certificado_llave_validos) {
          return true;
        } else return false;
      } else return true;
    },
    getDatos: {
      get() {
        return this.datos;
      },
      set(newValue) {
        return newValue;
      },
    },
    errores: {
      get() {
        return this.erroresForm;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      requestHeaders: {
        Authorization: "Bearer " + localStorage.getItem("accessToken"),
      },
      keyError: false,
      certificateError: false,
      fecha_validez: "",
      form: {
        cerPath: "",
        keyPath: "",
        modulo: "facturacion",
        password: "",
        passwordRepetir: "",
        cer: "",
        key: "",
        certificateFile: null,
        keyFile: null,
      },
    };
  },
  methods: {
    mostrarDatos() {
      //lleno los datos mandados del parent
      if (this.getDatos.facturacion["cerfile"] !== null) {
        this.form.password = "nochanges";
        this.form.passwordRepetir = "nochanges";
      }

      this.form.cerPath = this.getDatos.facturacion["cerfile"];
      this.form.keyPath = this.getDatos.facturacion["keyfile"];
    },

    mandarModificar() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Actualizar Datos",
              text: "Error, verifique que ingresó todos los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 4000,
            });
            return;
          } else {
            //se manda el emit al parent para guardar datps
            if (!this.validar_certificado_actualizar) {
              this.$vs.notify({
                title: "Actualizar Datos",
                text: "Error, capture certificado y llave privada.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 4000,
              });
              return;
            }
            let formData = new FormData();
            formData.append("modulo", this.form.modulo);
            formData.append("certificateFile", this.form.certificateFile);
            formData.append("keyFile", this.form.keyFile);
            formData.append("password", this.form.password);
            formData.append("passwordRepetir", this.form.passwordRepetir);
            this.$emit("actualizar", formData);
          }
        })
        .catch(() => {});
    },

    successCertificate(event) {
      //obteniendo las fechas de valiz del certificado
      let res = JSON.parse(event.target.response);
      this.form.certificateFile = this.$refs.uploadCer.filesx[
        this.$refs.uploadCer.filesx.length - 1
      ];
      this.fecha_validez = res.fecha_validez;
      this.certificateError = false;
    },
    errorCertificate(event) {
      this.$refs.uploadCer.srcs[
        this.$refs.uploadCer.srcs.length - 1
      ].error = true;
      let data = JSON.parse(event.target.response);
      this.$vs.notify({
        position: "top-center",
        color: "danger",
        title: "Firma Electrónica",
        text: data.error.message,
      });
      this.clearCertificate();
    },
    clearCertificate() {
      this.form.certificateFile = null;
    },

    successKey(event) {
      let res = JSON.parse(event.target.response);
      this.form.keyFile = this.$refs.uploadKey.filesx[
        this.$refs.uploadKey.filesx.length - 1
      ];
      this.certificateError = false;
    },
    errorKey(event) {
      this.$refs.uploadKey.srcs[
        this.$refs.uploadKey.srcs.length - 1
      ].error = true;
      let data = JSON.parse(event.target.response);
      this.$vs.notify({
        position: "top-center",
        color: "danger",
        title: "Firma Electrónica",
        text: data.error.message,
      });
      this.clearKey();
    },
    clearKey() {
      this.form.keyFile = null;
    },
  },
  created() {},
};
</script>
