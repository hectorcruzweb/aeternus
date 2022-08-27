<template >
  <div class="centerx">
    <vs-popup
      class="confirmarAceptar confirmar contrasena"
      close="cerrarar"
      title="contraseña"
      :active.sync="showNota"
      ref="nota"
    >
      <div class="text-center cantidad_icono"></div>
      <div class="text-center text-xl font-bold mt-3">INGRESAR CANTIDAD</div>
      <div class="flex flex-wrap mt-2">
        <div class="w-3/12 px-2"></div>
        <div class="w-6/12 px-2">
          <vs-input size="large" class="w-full mt-1" maxlength="6" />
        </div>
        <div class="w-3/12 px-2"></div>

        <div class="w-6/12 ml-auto mr-auto px-2 mt-5">
          <vs-button
            class="w-full ml-auto mr-auto"
            @click="acceptAlert()"
            color="primary"
            size="small"
          >
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
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() => {
          //this.$refs["nota"].$el.querySelector("textarea").focus();
        });
        this.$refs["nota"].$el.querySelector(".vs-icon").onclick = () => {
          this.cerrar();
        };

        /**cargando nota default */
      }
    }
  },

  data() {
    return {
      nota_text: ""
    };
  },
  computed: {
    showNota: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      }
    },
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
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", e => {
      if (e.keyCode === 27) {
        if (this.showNota) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cerrar();
        }
      }
    });
  }
};
</script>