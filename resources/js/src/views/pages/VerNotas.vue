<template >
  <div class="centerx">
    <vs-popup
      class="forms-popup"
      close="cancelar"
      :title="getTitle"
      :active.sync="showNota"
      ref="formulario"
    >
      <div class="p-8">
        {{ getNota }}
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
    title: {
      type: String,
      required: false,
      default: "",
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cerrar();
        };
      }
    },
  },

  data() {
    return {};
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
    getTitle: {
      get() {
        return this.title;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  methods: {
    cerrar() {
      this.$emit("closeVerNotas");
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