<template>
  <div class="centerx">
    <vs-popup class="confirmarAceptar confirmar contrasena" close="cerrarar" title="contraseña" :active="localShow"
      :ref="this.$options.name">
      <div class="text-center cantidad_icono"></div>
      <div class="text-center text-xl font-bold mt-3">INGRESAR CANTIDAD</div>
      <div class="flex flex-wrap mt-2">
        <div class="w-3/12 px-2"></div>
        <div class="w-6/12 px-2">
          <vs-input size="large" class="w-full mt-1" maxlength="6" />
        </div>
        <div class="w-3/12 px-2"></div>

        <div class="w-6/12 ml-auto mr-auto px-2 mt-5">
          <vs-button class="w-full ml-auto mr-auto" @click="acceptAlert()" color="primary" size="small">
            <img width="25px" class="cursor-pointer" src="@assets/images/volver.svg" />
            <span class="texto-btn">Volver (Esc)</span>
          </vs-button>
        </div>
      </div>
    </vs-popup>
  </div>
</template>
<script>
export default {
  name: "CantidadForm",
  props: {
    show: {
      type: Boolean,
      required: true
    },
    articulo: {
      type: Array,
      required: true,
      default: []
    }
  },
  watch: {
    show: {
      immediate: true, // runs when component is mounted too
      async handler(newValue) {
        if (newValue) {
          this.$popupManager.register(this, this.cerrar, "nota");
        } else {
          this.$popupManager.unregister(this.$options.name);
        }
        this.localShow = newValue;
      },
    },
  },

  data() {
    return {
      localShow: false,
      nota_text: ""
    };
  },
  computed: {
    getArticulo: {
      get() {
        return this.articulo;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  methods: {
    acceptAlert() {
      this.cerrar();
    },
    cerrar() {
      this.$emit("closeCantidad");
    }
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