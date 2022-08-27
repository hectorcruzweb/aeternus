<!-- =========================================================================================
    File Name: ResetPassword.vue
    Description: Reset Password Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <div class="h-screen flex w-full bg-login-password-recovery">
    <div class="layer"></div>
    <div class="vx-col sm:w-3/5 md:w-3/5 lg:w-3/4 xl:w-3/5 mx-auto self-center">
      <vx-card class="login-form py-20">
        <div slot="no-body">
          <div class="vx-row">
            <div
              class="vx-col hidden sm:hidden md:hidden lg:hidden xl:block lg:w-1/2 mx-auto self-center"
            >
              <img
                src="@assets/images/pages/reset-password.png"
                alt="login"
                class="mx-auto"
              />
            </div>
            <div
              class="vx-col sm:w-full md:w-full lg:w-full xl:w-1/2 mx-auto self-center"
            >
              <div class="px-8">
                <div class="vx-card__title mb-4">
                  <h4 class="color-dark-800 h1 font-medium py-2">
                    Cambiar Contraseña
                  </h4>
                  <p class="size-base font-normal normal-line">
                    Por favor ingrese su correo y su nueva contraseña.
                  </p>
                </div>
                <form @submit.prevent="submitForm">
                  <div class="w-full input-text">
                    <label>Correo electrónico <span>(*)</span></label>
                    <vs-input
                      type="email"
                      icon="icon icon-user"
                      icon-pack="feather"
                      v-validate="'required|email|min:3'"
                      data-vv-validate-on="blur"
                      v-model="email"
                      class="w-full"
                      name="email"
                      data-vv-as="email"
                      v-on:keyup.enter="submitForm"
                    />
                    <span>{{ errors.first("email") }}</span>
                  </div>

                  <div class="w-full input-text">
                    <label>Nueva Contraseña <span>(*)</span></label>
                    <vs-input
                      icon="icon icon-unlock"
                      icon-pack="feather"
                      type="password"
                      v-validate="'required|min:3'"
                      data-vv-validate-on="blur"
                      v-model="password"
                      class="w-full"
                      name="password"
                      data-vv-as="Contraseña"
                      v-on:keyup.enter="submitForm"
                    />
                    <span>{{ errors.first("password") }}</span>
                  </div>

                  <div class="w-full input-text">
                    <label>Confirmar nueva Contraseña <span>(*)</span></label>
                    <vs-input
                      icon="icon icon-unlock"
                      icon-pack="feather"
                      type="password"
                      v-validate="'required|min:3'"
                      data-vv-validate-on="blur"
                      v-model="password_confirmation"
                      class="w-full"
                      name="confirmar"
                      data-vv-as="Confirmar Contraseña"
                      v-on:keyup.enter="submitForm"
                    />
                    <span>{{ errors.first("confirmar") }}</span>
                  </div>
                </form>

                <vs-button
                  type="border"
                  to="/pages/login"
                  class="px-4 w-full sm:w-full md:w-full lg:w-full xl:w-auto mt-4"
                  >Regresar al login</vs-button
                >
                <vs-button
                  class="float-right px-4 w-full sm:w-full md:w-full lg:w-full xl:w-auto mt-4"
                  @click.prevent="submitForm"
                  >Cambiar contraseña</vs-button
                >
              </div>
            </div>
          </div>
        </div>
      </vx-card>
    </div>
  </div>
</template>

<script>
import axios from "../../http/axios/index";
/**VARIABLES GLOBALES */
export default {
  data() {
    return {
      email: "",
      password: "",
      password_confirmation: "",
    };
  },
  methods: {
    submitForm() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          const payload = {
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation,
            token: this.$route.params.token,
          };
          this.$vs.loading();
          axios
            .post("/password/reset", payload)
            .then((resp) => {
              //exito con la peticion
              this.$vs.notify({
                time: 6000,
                title: "Cambiar Contraseña",
                text: resp.data,
                color: "success",
              });
              this.email = "";
              this.password = "";
              this.password_confirmation = "";
              this.$vs.loading.close();
            })
            .catch((error) => {
              if (error) {
                this.$vs.notify({
                  time: 8000,
                  title: "Error",
                  text:
                    "Verifique sus datos: [usuario, contraseñas o cree un nuevo correo de renovación de contraseñas] e intente nuevamente.",
                  color: "danger",
                });
              }
              this.$vs.loading.close();
            });
        } else {
          // error de validacion de datos
        }
      });
      return false;
    },
  },
};
</script>
