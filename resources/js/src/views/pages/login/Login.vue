<template>
  <div
    class="h-screen flex w-full vx-row no-gutter items-center justify-center bg-login-password-recovery"
  >
    <div class="layer z-index-1"></div>
    <div class="vx-col sm:w-4/5 md:w-1/2 lg:w-3/4 xl:w-3/5 sm:m-0">
      <vx-card class="login-form py-20">
        <div slot="no-body" class>
          <div class="vx-row no-gutter justify-center items-center">
            <div class="vx-col hidden lg:block lg:w-1/2">
              <img
                src="@assets/images/sistemas_aeternus.svg"
                alt="login"
                class="mx-auto login-img"
              />
              <div class="flex flex-wrap">
                <div
                  class="w-full sm:w-12/12 md:w-11/12 lg:w-11/12 xl:w-11/12 mr-auto ml-auto"
                >
                  <h1
                    class="uppercase color-dark-800 font-medium h3 text-center pt-6"
                  >
                    SIIGA | Aeternus Funerales
                  </h1>
                  <h2 class="text-center size-base font-normal py-2">
                    Sistema Integral de Información y Gerencia Administrativa
                  </h2>
                  <p class="text-center size-small font-light py-2">
                    ©2020 Todos los Derechos Reservados
                  </p>
                </div>
              </div>
            </div>

            <div class="vx-col sm:w-full md:w-full lg:w-1/2">
              <div class="px-8">
                <div class="flex flex-wrap">
                  <div
                    class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mr-auto ml-auto px-2"
                  >
                    <div class="">
                      <h4 class="color-dark-800 h1 font-medium py-2">
                        Inicia sesión
                      </h4>
                      <div class="w-full input-text">
                        <label>Correo electrónico <span>(*)</span></label>
                        <vs-input
                          v-validate="'required|email|min:3|max:50'"
                          name="email"
                          maxlength="50"
                          icon-no-border
                          data-vv-as=" "
                          icon="icon icon-user"
                          icon-pack="feather"
                          placeholder="Correo electrónico"
                          v-model="email"
                          class="w-full"
                          ref="email"
                        />
                        <span>{{ errors.first("email") }}</span>
                      </div>
                      <div class="w-full input-text">
                        <label class="">Contraseña <span>(*)</span></label>
                        <vs-input
                          v-validate="'required|min:6|max:25'"
                          type="password"
                          name="password"
                          data-vv-as=" "
                          maxlength="25"
                          icon-no-border
                          icon="icon icon-lock"
                          icon-pack="feather"
                          placeholder="Contraseña"
                          v-model="password"
                          class="w-full"
                          v-on:keyup.enter="loginJWT"
                        />
                        <span>{{ errors.first("password") }}</span>
                      </div>

                      <div
                        class="flex flex-wrap justify-between size-small font-light smaller-line pt-2 pb-6"
                      >
                        Esta página está protegida y sujeta a las Políticas de
                        privacidad y a las Condiciones de la empresa. Acceso
                        solo a personal autorizado.
                      </div>

                      <!-- <div class="flex flex-wrap justify-between my-5">
                <vs-checkbox v-model="checkbox_remember_me" class="mb-3 hidden">Recordarme</vs-checkbox>
              </div>
                      -->
                      <div class="flex flex-wrap justify-between">
                        <vs-button class="w-full font-medium" @click="loginJWT"
                          >Inicia sesión</vs-button
                        >
                      </div>

                      <div class="flex flex-wrap">
                        <div class="w-full text-center size-normal pt-6">
                          <router-link to="/pages/forgot-password"
                            ><span
                              class="color-dark-700 color-primary-hover-900"
                              >¿Olvidaste tu contraseña?</span
                            ></router-link
                          >
                        </div>
                      </div>
                      <div class="flex flex-wrap justify-between">
                        <a
                          href="#"
                          target="_blank"
                          class="tutorial text-base hidden"
                        >
                          <feather-icon
                            icon="PlayIcon"
                            class="mr-2"
                            svgClasses="w-5 h-5"
                          />
                          <span class="mt-2 text-black"
                            >Ver guía de usuario "Inicio de Sesión"</span
                          >
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </vx-card>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import store from "../../../store/store";
export default {
  data() {
    return {
      //email: "admin@admin.com",
      //password: "password",
      email: "",
      password: "",
      checkbox_remember_me: false,
    };
  },
  computed: {
    validateForm() {
      return !this.errors.any() && this.email != "" && this.password != "";
    },
    ...mapGetters({
      isLoggedIn: "auth/isLoggedIn",
    }),
  },
  methods: {
    checkLogin() {
      // If user is already logged in notify
      if (localStorage.getItem("accessToken")) {
        location.reload();
        return false;
        // Close animation if passed as payload
        // this.$vs.loading.close()
        this.$vs.notify({
          title: "Atención",
          text: "Su cuenta ya ha iniciado sesión!",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "warning",
        });
        return false;
      }
      return true;
    },
    loginJWT() {
      if (!this.checkLogin()) return;

      // Loading
      this.$vs.loading();

      const payload = {
        checkbox_remember_me: this.checkbox_remember_me,
        userDetails: {
          email: this.email,
          password: this.password,
        },
      };

      this.$store
        .dispatch("auth/loginJWT", payload)
        .then(() => {
          this.$vs.loading.close();
        })
        .catch((error) => {
          this.$vs.loading.close();
          this.$vs.notify({
            time: 150000,
            title: "Ha ocurrido un error",
            text: error.response.data.error,
            color: "danger",
          });
        });
    },
  },
  created() {
    this.$nextTick(() =>
      this.$refs["email"].$el.querySelector("input").focus()
    );
  },
};
</script>
