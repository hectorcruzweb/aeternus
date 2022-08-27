<template >
  <div class="centerx">
    <vs-popup
      :class="['confirm-form', z_index]"
      close="cancelar"
      title="contraseña"
      :active.sync="showChecker"
      ref="contra"
    >
      <div class="text-center password_icono"></div>
      <div
        class="w-full text-center mt-3 h2 color-copy font-medium capitalize px-2"
      >
        confirmar contraseña
      </div>
      <div class="mt-3 text-center hidden">
        <span class="color-primary-900 size-smaller uppercase">{{
          accionNombre
        }}</span>
      </div>
      <div class="w-full text-center mt-3 color-copy size-small px-2">
        Para mayor seguridad debe ingresar su contraseña para confirmar que es
        un usuario autorizado para realizar esta operación.
      </div>

      <div class="w-full px-2 mt-6 mx-auto">
        <vs-input
          maxlength="50"
          size="large"
          ref="contra"
          type="password"
          class="w-full"
          placeholder="Contraseña"
          v-model.trim="pass"
          @keyup.enter="acceptAlert"
        />
      </div>

      <div class="w-full text-right px-2 mt-6">
        <span @click="cancel" class="color-danger-900 my-2 mr-8 cursor-pointer"
          >(Esc) Cerrar Ventana</span
        >
        <vs-button
          class="w-auto md:ml-2 my-2 md:mt-0"
          color="success"
          @click="acceptAlert"
        >
          <span>Continuar</span>
        </vs-button>
      </div>
    </vs-popup>
  </div>
</template>
<script>
import usuarios from "@services/Usuarios";
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
    z_index: {
      type: String,
      required: false,
      default: "z-index55k",
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["contra"].$el.querySelector("input").focus()
        );
        this.$refs["contra"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancel();
        };
      }
    },
  },

  data() {
    return {
      pass: "",
    };
  },
  computed: {
    validPassword() {
      return !!this.pass;
    },
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
  },
  methods: {
    acceptAlert() {
      if (!this.validPassword) {
        this.pass = "";
        return;
      }
      if (usuarios.cancel) {
        usuarios.cancel("Operation canceled by the user.");
      }
      //se verificq que exista una contraseña y se procede a realizar la confirmacion al servidor
      usuarios
        .confirmPassword(this.pass)
        .then((res) => {
          if (res.status == 200) {
            //preocede a cimplir la peticion
            this.pass = "";
            this.cancel();
            this.callbackOnSuccess();
          }
        })
        .catch((err) => {
          this.$vs.notify({
            title: "Permiso denegado",
            text: err.response.data.error,
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "top-center",
            time: "40000",
          });
          this.pass = "";
          this.$nextTick(() =>
            this.$refs["contra"].$el.querySelector("input").focus()
          );
        });
    },
    cancel() {
      this.pass = "";
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
  },
};
</script>