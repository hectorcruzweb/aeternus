<template >
  <div class="centerx">
    <vs-popup
      class="ver_disponibilidad"
      fullscreen
      close="cancelar"
      title="disponibilidad en cementerio"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="flex flex-wrap pb-5">
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-3">
          <h3 class="text-xl">
            <feather-icon icon="MapPinIcon" class="mr-2 mb-5" svgClasses="w-5 h-5" />
            <span class="font-bold text-primary uppercase">
              <span class="uppercase">Distribución del Área - {{getDatosAreas.nombre_area}}.</span>
            </span>
          </h3>
        </div>

        <div class="w-full px-2">
          <table class="tabla-descripciones py-5">
            <tr>
              <td class="text-center">
                <span class="p-4 bg-success text-white text-center font-bold">Disponible</span>
              </td>
              <td class="text-center">
                <span class="p-4 bg-danger text-white text-center font-bold">Vendido</span>
              </td>
              <td class="text-center">
                <span class="p-4 bg-info text-white text-center font-bold">Ocupado</span>
              </td>
              <td class="text-center">
                <span class="p-4 bg-espacio-muerto text-white text-center font-bold">Área de relleno</span>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <Distribucion :datosAreas="getDatosAreas"></Distribucion>
    </vs-popup>
  </div>
</template>
<script>
import Distribucion from "./Distribucion";
export default {
  components: { Distribucion },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    datosAreas: {
      required: true,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cerrarVentana();
        };
      } else {
      }
    },
  },

  computed: {
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    getDatosAreas: {
      get() {
        return this.datosAreas;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {};
  },
  methods: {
    cerrarVentana() {
      this.$emit("closeVentana");
    },
  },
  created() {},
};
</script>