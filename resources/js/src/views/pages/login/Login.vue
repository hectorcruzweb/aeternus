<template>
  <main class="aeternus-login">
    <!-- Panel institucional: foto original; en tablet/móvil se convierte en cabecera. -->
    <aside
      class="aeternus-login__brand"
      aria-label="Departamento de Sistemas de Aeternus Funerales"
    >
      <div class="aeternus-login__photo" aria-hidden="true"></div>
      <div class="aeternus-login__brand-content">
        <p class="aeternus-login__eyebrow">
          <span aria-hidden="true"></span>Departamento de Sistemas
        </p>
        <div class="aeternus-login__message">
          <h1>
            Tecnología al servicio<br class="aeternus-login__desktop-break" />
            de nuestra empresa.
          </h1>
          <p>Herramientas que conectan al equipo de Aeternus Funerales.</p>
        </div>
        <div class="aeternus-login__siiga">
          <strong>SIIGA</strong>
          <p>Sistema Integral de Información y Gerencia Administrativa</p>
        </div>
      </div>
    </aside>

    <section class="aeternus-login__access" aria-labelledby="login-heading">
      <div class="aeternus-login__content">
        <img
          src="@assets/images/logo/aeternus.jpg"
          alt="Aeternus Funerales"
          class="aeternus-login__logo"
        />
        <div class="aeternus-login__heading">
          <h2 id="login-heading">Inicia sesión</h2>
          <p>Ingresa tus datos para acceder al sistema.</p>
        </div>

        <!-- Se conservan vs-input, nombres, reglas, modelos, ref y Enter originales.
             El ojo solo alterna la visibilidad local de la contraseña. -->
        <div class="aeternus-login__field">
          <label for="login-email">Correo electrónico</label>
          <vs-input
            id="login-email"
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
            autocomplete="username"
            aria-label="Correo electrónico"
          />
          <span
            v-if="errors.first('email')"
            class="aeternus-login__error"
            role="alert"
            >{{ errors.first("email") }}</span
          >
        </div>
        <div class="aeternus-login__field">
          <label for="login-password">Contraseña</label>
          <div class="aeternus-login__password">
            <vs-input
              id="login-password"
              v-validate="'required|min:6|max:25'"
              :type="mostrarPassword ? 'text' : 'password'"
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
              autocomplete="current-password"
              aria-label="Contraseña"
            />
            <button
              type="button"
              class="aeternus-login__eye"
              @click="mostrarPassword = !mostrarPassword"
              :aria-label="
                mostrarPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'
              "
              :aria-pressed="mostrarPassword"
            >
              <feather-icon
                :icon="mostrarPassword ? 'EyeOffIcon' : 'EyeIcon'"
              />
            </button>
          </div>
          <span
            v-if="errors.first('password')"
            class="aeternus-login__error"
            role="alert"
            >{{ errors.first("password") }}</span
          >
        </div>

        <div class="aeternus-login__recovery">
          <router-link to="/pages/forgot-password"
            >¿Olvidaste tu contraseña?</router-link
          >
        </div>
        <vs-button class="aeternus-login__submit" @click="loginJWT">
          <span>Iniciar sesión</span><feather-icon icon="ArrowRightIcon" />
        </vs-button>
        <footer class="aeternus-login__footer">
          <p>Acceso exclusivo para personal autorizado.</p>
          <p>
            Esta página está sujeta a las políticas de privacidad y a las
            condiciones de la empresa.
          </p>
          <p class="aeternus-login__copyright">
            © {{ anioActual }} Aeternus Funerales
          </p>
        </footer>
      </div>
    </section>
  </main>
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
      // Estado visual local: no cambia el payload ni la autenticación.
      mostrarPassword: false,
      anioActual: new Date().getFullYear(),
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
<style scoped>
/* Estilos exclusivos del login. No reutilizar bg-login-password-recovery:
   recuperación conserva su presentación. Altura mínima permite scroll con
   teclado virtual, ventanas bajas o zoom; nunca recorta los controles. */
.aeternus-login {
  display: flex;
  min-height: 100vh;
  width: 100%;
  background: #fff;
  color: #354047;
}
.aeternus-login,
.aeternus-login * {
  box-sizing: border-box;
}
.aeternus-login__brand {
  position: relative;
  flex: 0 0 55%;
  min-width: 0;
  background-color: #293334;
  background-image: linear-gradient(
      180deg,
      rgba(29, 35, 40, 0.86) 0%,
      rgba(29, 35, 40, 0.66) 36%,
      rgba(29, 35, 40, 0.08) 65%,
      rgba(22, 28, 33, 0.84) 100%
    ),
    url("~@assets/images/funeraria.jpg");
  background-position: center, center 64%;
  background-repeat: no-repeat;
  background-size: cover;
}
.aeternus-login__brand-content {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  padding: 60px 7.5% 42px;
}
.aeternus-login__eyebrow {
  display: flex;
  align-items: center;
  gap: 15px;
  margin: 0;
  color: #f3f2ee;
  font-size: 13px;
  letter-spacing: 2px;
  text-transform: uppercase;
  line-height: 1.5;
}
.aeternus-login__eyebrow span {
  display: block;
  flex: 0 0 40px;
  height: 3px;
  background: #b59a43;
}
.aeternus-login__message {
  margin-top: 64px;
  max-width: 660px;
}
.aeternus-login__message h1 {
  margin: 0;
  color: #fff;
  font-size: 42px !important;
  line-height: 1.18;
  font-weight: 600;
  letter-spacing: -0.8px;
}
.aeternus-login__message p {
  margin: 20px 0 0;
  color: #d8dce0;
  font-size: 17px;
  line-height: 1.6;
  max-width: 570px;
}
.aeternus-login__siiga {
  margin-top: auto;
  padding-top: 260px;
  color: #fff;
}
.aeternus-login__siiga strong {
  display: block;
  color: #fff;
  font-size: 25px;
  line-height: 1.2;
}
.aeternus-login__siiga p {
  margin: 6px 0 0;
  max-width: 530px;
  font-size: 13px;
  line-height: 1.5;
  color: #d4d9dc;
}
.aeternus-login__access {
  display: flex;
  align-items: center;
  justify-content: center;
  flex: 1;
  min-width: 0;
  padding: 44px 7%;
}
.aeternus-login__content {
  width: 100%;
  max-width: 460px;
}
.aeternus-login__logo {
  display: block;
  width: 82%;
  max-width: 335px;
  height: auto;
  margin: 0 auto 54px;
}
.aeternus-login__heading {
  margin-bottom: 34px;
}
.aeternus-login__heading h2 {
  font-size: 32px !important;
  line-height: 1.2;
  font-weight: 600;
  letter-spacing: -0.5px;
  color: #354047;
  margin: 0;
}
.aeternus-login__heading p {
  margin: 12px 0 0;
  font-size: 14px;
  line-height: 1.5;
  color: #737f87;
}
.aeternus-login__field {
  margin-top: 22px;
}
.aeternus-login__field label {
  display: block;
  margin-bottom: 9px;
  font-size: 13px;
  line-height: 1.4;
  font-weight: 600;
  color: #45525c;
}
.aeternus-login >>> .vs-input {
  width: 100%;
}
.aeternus-login >>> .vs-input--input {
  height: 50px !important;
  background: #fff !important;
  border: 1px solid #d3dae0 !important;
  border-radius: 4px !important;
  font-size: 14px !important;
  color: #354047 !important;
  padding-left: 42px !important;
  box-shadow: none !important;
}
.aeternus-login >>> .vs-input--input:focus {
  border-color: #a58828 !important;
  box-shadow: 0 0 0 3px rgba(165, 136, 40, 0.12) !important;
}
.aeternus-login >>> .vs-input--icon {
  top: 16px !important;
  left: 13px !important;
  color: #7b8790 !important;
  font-size: 17px !important;
  border: 0 !important;
  padding: 0 !important;
}
.aeternus-login >>> .vs-input--placeholder {
  top: 3px !important;
  padding-left: 42px !important;
  font-size: 14px !important;
  color: #929ba3 !important;
}
.aeternus-login__password {
  position: relative;
}
.aeternus-login__password >>> .vs-input--input {
  padding-right: 47px !important;
}
.aeternus-login__eye {
  position: absolute;
  right: 3px;
  top: 3px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  padding: 0;
  border: 0;
  background: transparent;
  color: #79858c;
  cursor: pointer;
  border-radius: 3px;
}
.aeternus-login__eye:hover {
  color: #92731e;
  background: #f7f5ed;
}
.aeternus-login__eye:focus-visible {
  outline: 2px solid #a58828;
  outline-offset: 1px;
}
.aeternus-login__eye >>> svg {
  display: block;
  width: 19px;
  height: 19px;
}
.aeternus-login__error {
  display: block;
  color: #ad4145;
  font-size: 12px;
  line-height: 1.4;
  margin-top: 6px;
}
.aeternus-login__recovery {
  margin-top: 17px;
  text-align: right;
}
.aeternus-login__recovery a {
  font-size: 13px;
  color: #8c6c19;
  text-decoration: none;
}
.aeternus-login__recovery a:hover {
  text-decoration: underline;
  color: #715613;
}
.aeternus-login__submit {
  width: 100%;
  margin-top: 30px;
  background: #a58828 !important;
  border-radius: 4px !important;
  min-height: 50px;
  font-weight: 600;
  font-size: 15px !important;
  transition: background-color 0.18s ease, box-shadow 0.18s ease;
}
.aeternus-login__submit:hover {
  background: #90721d !important;
  box-shadow: 0 4px 12px rgba(110, 86, 22, 0.15);
}
.aeternus-login__submit >>> .vs-button-text {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
}
.aeternus-login__submit >>> svg {
  width: 20px;
  height: 20px;
}
.aeternus-login__footer {
  margin-top: 32px;
  text-align: center;
  color: #849098;
  font-size: 11px;
  line-height: 1.6;
}
.aeternus-login__footer p {
  margin: 4px 0;
}
.aeternus-login__copyright {
  margin-top: 12px !important;
}
/* Laptop: mantener las columnas reduciendo espacios antes que el texto. */
@media (min-width: 1024px) and (max-height: 800px) {
  .aeternus-login__brand-content {
    padding-top: 36px;
    padding-bottom: 30px;
  }
  .aeternus-login__message {
    margin-top: 38px;
  }
  .aeternus-login__message h1 {
    font-size: 34px !important;
  }
  .aeternus-login__message p {
    font-size: 15px;
  }
  .aeternus-login__siiga {
    padding-top: 190px;
  }
  .aeternus-login__access {
    padding: 28px 5%;
  }
  .aeternus-login__logo {
    max-width: 275px;
    margin-bottom: 30px;
  }
  .aeternus-login__heading {
    margin-bottom: 22px;
  }
  .aeternus-login__heading h2 {
    font-size: 28px !important;
  }
  .aeternus-login__field {
    margin-top: 17px;
  }
  .aeternus-login__submit {
    margin-top: 23px;
  }
  .aeternus-login__footer {
    margin-top: 24px;
  }
}
/* Tablet: foto como cabecera; formulario legible, sin el texto promocional. */
@media (max-width: 1023px) {
  .aeternus-login {
    flex-direction: column;
  }
  .aeternus-login__brand {
    flex: none;
    min-height: 210px;
    background-position: center, center 62%;
  }
  .aeternus-login__brand-content {
    min-height: 210px;
    padding: 26px 6%;
  }
  .aeternus-login__message,
  .aeternus-login__siiga {
    display: none;
  }
  .aeternus-login__access {
    flex: 1;
    padding: 30px 24px 26px;
  }
  .aeternus-login__content {
    max-width: 480px;
  }
  .aeternus-login__logo {
    width: 250px;
    margin-bottom: 25px;
  }
  .aeternus-login__heading {
    margin-bottom: 24px;
  }
  .aeternus-login__heading h2 {
    font-size: 28px !important;
  }
  .aeternus-login__heading p {
    margin-top: 8px;
  }
  .aeternus-login__field {
    margin-top: 19px;
  }
  .aeternus-login__footer {
    margin-top: 25px;
  }
}
/* Móvil: cabecera compacta y controles de al menos 44px; campos sin zoom iOS. */
@media (max-width: 575px) {
  .aeternus-login__brand,
  .aeternus-login__brand-content {
    min-height: 135px;
  }
  .aeternus-login__brand-content {
    padding: 18px 22px;
  }
  .aeternus-login__eyebrow {
    font-size: 10px;
    letter-spacing: 1.2px;
    gap: 10px;
  }
  .aeternus-login__eyebrow span {
    flex-basis: 25px;
    height: 2px;
  }
  .aeternus-login__access {
    padding: 22px 24px;
  }
  .aeternus-login__logo {
    width: 210px;
    margin-bottom: 20px;
  }
  .aeternus-login__heading {
    margin-bottom: 22px;
  }
  .aeternus-login__heading h2 {
    font-size: 26px !important;
  }
  .aeternus-login__heading p {
    font-size: 13px;
  }
  .aeternus-login__field {
    margin-top: 18px;
  }
  .aeternus-login >>> .vs-input--input {
    font-size: 16px !important;
  }
  .aeternus-login__submit {
    margin-top: 24px;
  }
  .aeternus-login__footer {
    font-size: 10px;
    margin-top: 24px;
  }
}
@media (prefers-reduced-motion: reduce) {
  .aeternus-login__submit {
    transition: none;
  }
}

/* Corrección de fidelidad visual: _typography.scss impone h1/h2 !important.
   Aislar la jerarquía del login y reservar a la fotografía la zona inferior,
   evitando el recorte de cover sobre una columna de pantalla completa. */
.aeternus-login{font-family:Arial,sans-serif}
.aeternus-login__brand{overflow:hidden;background:#292e35}
.aeternus-login__photo{position:absolute;left:0;right:0;bottom:8%;height:70%;background-image:linear-gradient(180deg,#292e35 0%,rgba(41,46,53,.94) 22%,rgba(41,46,53,.66) 40%,rgba(41,46,53,.08) 56%,rgba(24,29,34,.03) 78%,#292e35 100%),url('~@assets/images/funeraria.jpg');background-position:center,center bottom;background-size:100% 100%,cover;background-repeat:no-repeat}
.aeternus-login__brand-content{position:relative;z-index:1}
.aeternus-login__message h1{font-size:52px!important;line-height:1.13;font-weight:700}
.aeternus-login__message p{font-size:19px;line-height:1.5;max-width:650px}
.aeternus-login__heading h2{font-size:40px!important;font-weight:700}
.aeternus-login__heading p{font-size:17px}
.aeternus-login__field label{font-size:15px}
.aeternus-login__content{max-width:490px}
@media(min-width:1600px){
 .aeternus-login__message{max-width:810px;margin-top:74px}
 .aeternus-login__message h1{font-size:64px!important}
 .aeternus-login__message p{font-size:22px;max-width:800px}
 .aeternus-login__heading h2{font-size:46px!important}
 .aeternus-login__heading p{font-size:18px}
 .aeternus-login__content{max-width:540px}
 .aeternus-login__logo{max-width:360px}
 .aeternus-login__siiga strong{font-size:32px}
 .aeternus-login__siiga p{font-size:16px;max-width:720px}
}
@media(min-width:1024px) and (max-height:800px){
 .aeternus-login__message h1{font-size:42px!important}
 .aeternus-login__message p{font-size:17px}
 .aeternus-login__heading h2{font-size:34px!important}
 .aeternus-login__heading p{font-size:15px}
 .aeternus-login__photo{height:68%}
}
@media(max-width:1023px){
 .aeternus-login__photo{height:100%;bottom:0;background-image:linear-gradient(180deg,rgba(29,35,40,.68),rgba(29,35,40,.05) 65%,rgba(29,35,40,.12)),url('~@assets/images/funeraria.jpg');background-size:100% 100%,cover;background-position:center,center 63%}
 .aeternus-login__heading h2{font-size:32px!important}
 .aeternus-login__heading p{font-size:15px}
 .aeternus-login__content{max-width:480px}
}
@media(max-width:575px){
 .aeternus-login__heading h2{font-size:28px!important}
 .aeternus-login__heading p{font-size:13px}
 .aeternus-login__field label{font-size:13px}
}
</style>
