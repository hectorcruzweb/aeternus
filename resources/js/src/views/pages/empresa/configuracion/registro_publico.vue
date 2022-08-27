<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4">
        <vs-card class="cardx card-tarifas" fixedHeight>
          <!--Datos del registro publico-->
          <div class="form-group mt-6">
            <div class="title-form-group">
              <span>Escritura Pública</span>
            </div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Representante Legal
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="rep_legal"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Representante Legal"
                    v-model="form.rep_legal"
                  />
                  <span class="">{{ errors.first("rep_legal") }}</span>
                  <span class="" v-if="this.errores.rep_legal">{{
                    errores.rep_legal[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Núm. del testimonio
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="t_nep"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Núm. del testimonio"
                    v-model="form.t_nep"
                  />
                  <span class="">{{ errors.first("t_nep") }}</span>
                  <span class="" v-if="this.errores.t_nep">{{
                    errores.t_nep[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Fecha del Testimonio
                    <span class="">(*)</span>
                  </label>
                  <datepicker
                    :language="spanishDatepicker"
                    :disabled-dates="disabledDates"
                    name="fecha_tnep"
                    data-vv-as=" "
                    v-validate="'required'"
                    format="yyyy-MM-dd"
                    placeholder="Fecha del Testimonio"
                    v-model="form.fecha_tnep"
                    class="w-full"
                  ></datepicker>
                  <span class="">{{ errors.first("fecha_tnep") }}</span>
                  <span class="" v-if="this.errores.fecha_tnep">{{
                    errores.fecha_tnep[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Otorgada ente la fé del Lic.
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="fe_lic"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Otorgada ente la fé del Lic."
                    v-model="form.fe_lic"
                  />
                  <span class="">{{ errors.first("fe_lic") }}</span>
                  <span class="" v-if="this.errores.fe_lic">{{
                    errores.fe_lic[0]
                  }}</span>
                </div>
              </div>
            </div>
          </div>
          <!--Datos del registro publico-->

          <!--Datos del notario publico-->
          <div class="form-group">
            <div class="title-form-group">
              <span>Notario Público</span>
            </div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div
                  class="w-full md:w-12/12 lg:w-4/12 xl:w-4/12 px-2 input-text"
                >
                  <label class="">
                    Notario Público Núm.
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="num_np"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Notario Público Núm."
                    v-model="form.num_np"
                  />
                  <span class="">{{ errors.first("num_np") }}</span>
                  <span class="" v-if="this.errores.num_np">{{
                    errores.num_np[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-4/12 xl:w-4/12 px-2 input-text"
                >
                  <label class="">
                    Estado
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="estado_np"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Estado"
                    v-model="form.estado_np"
                  />
                  <span class="">{{ errors.first("estado_np") }}</span>
                  <span class="" v-if="this.errores.estado_np">{{
                    errores.estado_np[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-4/12 xl:w-4/12 px-2 input-text"
                >
                  <label class="">
                    Ciudad
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="ciudad_np"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Ciudad"
                    v-model="form.ciudad_np"
                  />
                  <span class="">{{ errors.first("ciudad_np") }}</span>
                  <span class="" v-if="this.errores.ciudad_np">{{
                    errores.ciudad_np[0]
                  }}</span>
                </div>
              </div>
            </div>
          </div>
          <!--Datos del notario publico-->

          <!--Datos del registro publico de comercio-->
          <div class="form-group">
            <div class="title-form-group">
              <span>Registro Público del Comercio</span>
            </div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Núm. Registro Público
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="num_rpc"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Núm. Registro Público"
                    v-model="form.num_rpc"
                  />
                  <span class="">{{ errors.first("num_rpc") }}</span>
                  <span class="" v-if="this.errores.num_rpc">{{
                    errores.num_rpc[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Fecha del Registro Público
                    <span class="">(*)</span>
                  </label>
                  <datepicker
                    :language="spanishDatepicker"
                    :disabled-dates="disabledDates"
                    name="fecha_rpc"
                    data-vv-as=" "
                    v-validate="'required'"
                    format="yyyy-MM-dd"
                    placeholder="Fecha del Registro Público"
                    v-model="form.fecha_rpc"
                    class="w-full"
                  ></datepicker>
                  <span class="">{{ errors.first("fecha_rpc") }}</span>
                  <span class="" v-if="this.errores.fecha_rpc">{{
                    errores.fecha_rpc[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Estado
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="estado_rpc"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Estado"
                    v-model="form.estado_rpc"
                  />
                  <span class="">{{ errors.first("estado_rpc") }}</span>
                  <span class="" v-if="this.errores.estado_rpc">{{
                    errores.estado_rpc[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Ciudad
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="ciudad_rpc"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Ciudad"
                    v-model="form.ciudad_rpc"
                  />
                  <span class="">{{ errors.first("ciudad_rpc") }}</span>
                  <span class="" v-if="this.errores.ciudad_rpc">{{
                    errores.ciudad_rpc[0]
                  }}</span>
                </div>
              </div>
            </div>
          </div>
          <!--Datos del registro publico de comercio-->

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
                <span>Actualizar Datos de Registro Público</span>
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
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";
import { parse } from "date-fns";
export default {
  components: {
    "v-select": vSelect,
    Datepicker,
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
      disabledDates: {
        from: new Date(),
      },
      spanishDatepicker: es,

      form: {
        modulo: "registro_publico",
        rep_legal: "",
        t_nep: "",
        fecha_tnep: "",
        fe_lic: "",
        //datos del notario publico
        num_np: "",
        ciudad_np: "",
        estado_np: "",
        //datos del reg publico de comercio
        num_rpc: "",
        fecha_rpc: "",
        ciudad_rpc: "",
        estado_rpc: "",
      },
    };
  },
  methods: {
    mostrarDatos() {
      //lleno los datos mandados dle parent
      this.form.rep_legal = this.getDatos.registro_publico.rep_legal;
      this.form.t_nep = this.getDatos.registro_publico.t_nep;
      this.form.fecha_tnep = parse(
        this.getDatos.registro_publico.fecha_tnep,
        "yyyy-MM-dd",
        new Date()
      );

      this.form.fe_lic = this.getDatos.registro_publico.fe_lic;
      this.form.num_np = this.getDatos.registro_publico.num_np;
      this.form.ciudad_np = this.getDatos.registro_publico.ciudad_np;
      this.form.estado_np = this.getDatos.registro_publico.estado_np;
      this.form.num_rpc = this.getDatos.registro_publico.num_rpc;
      this.form.fecha_rpc = parse(
        this.getDatos.registro_publico.fecha_rpc,
        "yyyy-MM-dd",
        new Date()
      );

      this.form.ciudad_rpc = this.getDatos.registro_publico.ciudad_rpc;
      this.form.estado_rpc = this.getDatos.registro_publico.estado_rpc;
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
            this.$emit("actualizar", this.form);
          }
        })
        .catch(() => {});
    },
  },
  created() {},
};
</script>
