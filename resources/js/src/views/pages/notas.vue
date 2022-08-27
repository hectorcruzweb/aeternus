<template >
  <div class="centerx">
    <vs-popup
      class="confirmarAceptar confirmar contrasena"
      close="cerrarar"
      title="contraseña"
      :active.sync="showNota"
      ref="nota"
    >
      <div class="text-center nota_icono"></div>
      <div class="text-center seguro-mensaje mt-3">Notas</div>
      <div class="text-center seguro-texto mt-3">
        ingrese alguna nota o referencia sobre esta operación para su mayor
        control.
      </div>
      <div class="flex flex-wrap mt-2">
        <div class="w-full px-5">
          <vs-textarea
            height="200px"
            :rows="7"
            size="large"
            ref="nota"
            type="text"
            class="w-full pt-3 pb-3"
            placeholder="Ingrese una nota..."
            v-model.trim="nota_text"
          />
        </div>
        <div
          class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 mt-5"
        >
          <vs-button
            class="w-full ml-auto mr-auto"
            @click="acceptAlert()"
            color="primary"
            size="small"
          >
            <img
              width="25px"
              class="cursor-pointer"
              src="@assets/images/volver.svg"
            />
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
      required: true,
    },
    nota: {
      type: String,
      required: false,
      default: "",
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["nota"].$el.querySelector("textarea").focus()
        );
        this.$refs["nota"].$el.querySelector(".vs-icon").onclick = () => {
          this.cerrar();
        };

        /**cargando nota default */
        this.nota_text = this.getNota;
      }
    },
  },

  data() {
    return {
      nota_text: "",
    };
  },
  computed: {
    showNota: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    getNota: {
      get() {
        return this.nota;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  methods: {
    acceptAlert() {
      this.cerrar();
    },
    cerrar() {
      this.$emit("closeNotas", this.nota_text);
    },
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", (e) => {
      if (e.keyCode === 27) {
        if (this.showNota) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cerrar();
        }
      }
    });
  },
};
</script>