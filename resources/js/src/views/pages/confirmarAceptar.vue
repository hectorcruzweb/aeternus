<template >
  <div class="centerx">
    <vs-prompt
      :class="['confirm-form confirmarAceptar', z_index]"
      type="confirm"
      title="¿Desea continuar?"
      :active.sync="showChecker"
      buttons-hidden
    >
      <div class="text-center icono"></div>

      <div
        class="w-full text-center mt-3 h2 color-copy font-medium capitalize px-2"
      >
        ¿Seguro de continuar?
      </div>

      <div class="w-full text-center mt-3 color-copy size-small px-2">
        {{ accionNombre }}
      </div>

      <div class="w-full text-right px-2 mt-6 pb-3">
        <span @click="cancel" class="color-danger-900 my-2 mr-8 cursor-pointer"
          >(Esc) Cancelar</span
        >

        <vs-button
          class="w-auto md:ml-2 my-2 md:mt-0"
          :color="confirmarColorTexto"
          @click="aceptar"
        >
          <span>{{ confirmarButtonTexto }}</span>
        </vs-button>
      </div>
    </vs-prompt>
  </div>
</template>
<script>
export default {
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    callbackOnSuccess: {
      type: Function,
      required: true,
    },
    accion: {
      type: String,
      required: true,
    },
    confirmarButton: {
      type: String,
      default: "Aceptar",
    },
    confirmarColor: {
      type: String,
      default: "success",
    },
    z_index: {
      type: String,
      required: false,
      default: "z-index55k",
    },
  },

  data() {
    return {};
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
    accionNombre() {
      return this.accion;
    },
    confirmarButtonTexto() {
      return this.confirmarButton;
    },
    confirmarColorTexto() {
      return this.confirmarColor;
    },
  },
  methods: {
    aceptar() {
      this.callbackOnSuccess();
      this.cancel();
    },
    cancel() {
      this.$emit("closeVerificar");
    },
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", (e) => {
      if (e.keyCode === 27) {
        if (this.showChecker) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cancel();
        }
      }
    });

    /*document.body.addEventListener("keyup", e => {
      if (e.keyCode === 13) {
        if (this.showChecker) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.aceptar();
        }
      }
    });*/
  },
};
</script>
