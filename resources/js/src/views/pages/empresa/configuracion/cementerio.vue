<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4">
        <vs-card class="cardx card-tarifas" fixedHeight>
          <!--Datos del cementerio-->
          <div class="form-group mt-6">
            <div class="title-form-group">
              <span>Datos del cementerio</span>
            </div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Nombre del Cementerio
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="cementerio"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Nombre del Cementerio"
                    v-model="form.cementerio"
                  />
                  <span class="">{{ errors.first("cementerio") }}</span>
                  <span class="" v-if="this.errores.cementerio">{{
                    errores.cementerio[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Teléfono
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="telefono"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Teléfono(s)"
                    v-model="form.telefono"
                  />
                  <span class="">{{ errors.first("telefono") }}</span>
                  <span class="" v-if="this.errores.telefono">{{
                    errores.telefono[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Correo Electrónico
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="email"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Correo Electrónico"
                    v-model="form.email"
                  />
                  <span class="">{{ errors.first("email") }}</span>
                  <span class="" v-if="this.errores.email">{{
                    errores.email[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">Fax</label>
                  <vs-input
                    name="fax"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Fax"
                    v-model="form.fax"
                  />
                </div>

                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Calle
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="calle"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Calle"
                    v-model="form.calle"
                  />
                  <span class="">{{ errors.first("calle") }}</span>
                  <span class="" v-if="this.errores.calle">{{
                    errores.calle[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 input-text"
                >
                  <label class="">
                    Colonia
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="colonia"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Colonia"
                    v-model="form.colonia"
                  />
                  <span class="">{{ errors.first("colonia") }}</span>
                  <span class="" v-if="this.errores.colonia">{{
                    errores.colonia[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-4/12 xl:w-4/12 px-2 input-text"
                >
                  <label class="">
                    Núm. Exterior
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="num_ext"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="6"
                    type="text"
                    class="w-full"
                    placeholder="Núm. Exterior"
                    v-model="form.num_ext"
                  />
                  <span class="">{{ errors.first("num_ext") }}</span>
                  <span class="" v-if="this.errores.num_ext">{{
                    errores.num_ext[0]
                  }}</span>
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-4/12 xl:w-4/12 px-2 input-text"
                >
                  <label class="">Núm. Interior</label>
                  <vs-input
                    name="num_int"
                    maxlength="6"
                    type="text"
                    class="w-full"
                    placeholder="Núm. Interior"
                    v-model="form.num_int"
                  />
                </div>
                <div
                  class="w-full md:w-12/12 lg:w-4/12 xl:w-4/12 px-2 input-text"
                >
                  <label class="">
                    C.P
                    <span class="">(*)</span>
                  </label>
                  <vs-input
                    name="cp"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="6"
                    type="text"
                    class="w-full"
                    placeholder="C.P"
                    v-model="form.cp"
                  />
                  <span class="">{{ errors.first("cp") }}</span>
                  <span class="" v-if="this.errores.cp">{{
                    errores.cp[0]
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
                    name="ciudad"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Ciudad"
                    v-model="form.ciudad"
                  />
                  <span class="">{{ errors.first("ciudad") }}</span>
                  <span class="" v-if="this.errores.ciudad">{{
                    errores.ciudad[0]
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
                    name="estado"
                    data-vv-as=" "
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full"
                    placeholder="Estado"
                    v-model="form.estado"
                  />
                  <span class="">{{ errors.first("estado") }}</span>
                  <span class="" v-if="this.errores.estado">{{
                    errores.estado[0]
                  }}</span>
                </div>
              </div>
            </div>
          </div>
          <!--Datos del cementerio-->
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
                <span>Actualizar Datos de Cementerio</span>
              </vs-button>
            </div>
          </div>
        </vs-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  components: {},
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
      form: {
        modulo: "cementerio",
        cementerio: "",
        telefono: "",
        email: "",
        fax: "",
        calle: "",
        colonia: "",
        num_ext: "",
        num_int: "",
        cp: "",
        ciudad: "",
        estado: "",
      },
    };
  },
  methods: {
    mostrarDatos() {
      //lleno los datos mandados dle parent
      this.form.cementerio = this.getDatos.cementerio.cementerio;
      this.form.telefono = this.getDatos.cementerio.telefono;
      this.form.email = this.getDatos.cementerio.email;
      this.form.fax = this.getDatos.cementerio.fax;
      this.form.calle = this.getDatos.cementerio.calle;
      this.form.colonia = this.getDatos.cementerio.colonia;
      this.form.num_ext = this.getDatos.cementerio.num_ext;
      this.form.num_int = this.getDatos.cementerio.num_int;
      this.form.cp = this.getDatos.cementerio.cp;
      this.form.ciudad = this.getDatos.cementerio.ciudad;
      this.form.estado = this.getDatos.cementerio.estado;
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
