<template>
  <div>
    <vs-popup
      fullscreen
      close="cancelar"
      title="IMPRIMIR REPORTES"
      :active.sync="ver"
      button-close-hidden
    >
      <div class="flex flex-wrap">
        <div class="w-full pt-5">
          <vs-button
            @click="cancel()"
            color="danger"
            size="small"
            class="float-right mb-3"
            >(Esc) Cerrar</vs-button
          >
          <iframe :src="pdf_iframe" width="560" height="315"></iframe>
        </div>
      </div>
    </vs-popup>
  </div>
</template>


<script>
import usuarios from "@services/Usuarios";
export default {
  props: {
    pdf: {
      type: String,
      required: true,
    },
    show: {
      type: Boolean,
      required: true,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.get_pdf();
      } else {
        this.ver = false;
        this.pdf_iframe = "";
      }
    },
  },
  computed: {
    showPdf: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    pdfLink: {
      get() {
        return this.pdf;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      ver: false,
      pdf_iframe: "",
    };
  },
  methods: {
    get_pdf() {
      this.$vs.loading();
      usuarios
        .get_pdf(this.pdfLink)
        .then((res) => {
          this.ver = true;
          this.$vs.loading.close();
          const file = new Blob([res.data], { type: "application/pdf" });
          this.pdf_iframe = URL.createObjectURL(file);
          /*var reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onloadend = function() {
            var base64data = reader.result;
            this.pdf_iframe = base64data.replace("octet-stream", "pdf");
          };*/
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
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
          this.cancel();
        });
    },
    cancel() {
      this.ver = false;
      this.$emit("closePdf");
    },
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", (e) => {
      if (e.keyCode === 27) {
        if (this.showPdf) {
          //CIERRO EL pdf viewer AL PRESONAR ESC
          this.cancel();
        }
      }
    });
  },
};
</script>
<style lang="css" scoped>
iframe {
  display: block; /* iframes are inline by default */
  background: #000;
  border: none; /* Reset default border */
  height: 100vh; /* Viewport-relative units */
  width: 100%;
}
</style>
