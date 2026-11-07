<template>
  <main class="aeternus-reset">
    <!-- Misma identidad del login; el panel se vuelve cabecera en pantallas pequeñas. -->
    <aside class="aeternus-reset__brand" aria-label="Departamento de Sistemas">
      <div class="aeternus-reset__photo" aria-hidden="true"></div>
      <div class="aeternus-reset__brand-content">
        <p class="aeternus-reset__eyebrow">Departamento de Sistemas</p>
        <span class="aeternus-reset__line" aria-hidden="true"></span>
        <div class="aeternus-reset__message">
          <h1>Una nueva contraseña.<br />El mismo espacio de trabajo.</h1>
          <p>Actualiza tu acceso y continúa con tus actividades.</p>
        </div>
        <div class="aeternus-reset__siiga">
          <strong>SIIGA</strong>
          <p>Sistema Integral de Información y Gerencia Administrativa</p>
        </div>
      </div>
    </aside>
    <section class="aeternus-reset__access" aria-labelledby="reset-heading">
      <div class="aeternus-reset__content">
        <img src="@assets/images/logo/aeternus.jpg" alt="Aeternus Funerales" class="aeternus-reset__logo" />
        <div class="aeternus-reset__key" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M29 5a12 12 0 0 0-10.4 18L5 36.6V43h6.4v-5.4h5.4v-5.4l6.2-6.1A12 12 0 1 0 29 5Z" />
            <circle cx="31" cy="15" r="3.5" />
          </svg>
        </div>
        <header class="aeternus-reset__heading">
          <h2 id="reset-heading">Restablecer contraseña</h2>
          <p>Ingresa tu correo electrónico y confirma tu nueva contraseña.</p>
        </header>
        <!-- Formulario original: reglas, eventos Enter, nombres y modelos conservados.
             El token sigue leyéndose exclusivamente en submitForm desde la ruta. -->
<form @submit.prevent="submitForm" class="aeternus-reset__form">
                                    <div class="aeternus-reset__field">
                                        <label
                                            >Correo electrónico
                                            <span>(*)</span></label
                                        >
                                        <vs-input
                                            type="email"
                                            icon="icon icon-user"
                                            icon-pack="feather"
                                            v-validate="'required|email|min:3'"
                                            data-vv-validate-on="blur"
                                            autocomplete="username" placeholder="Correo electrónico" aria-label="Correo electrónico" v-model="email"
                                            class="w-full"
                                            name="email"
                                            data-vv-as="email"
                                            v-on:keyup.enter="submitForm"
                                        />
                                        <span class="aeternus-reset__error" role="alert">{{ errors.first("email") }}</span>
                                    </div>

                                    <div class="aeternus-reset__field">
                                        <label
                                            >Nueva Contraseña (Min 8 Caracteres)
                                            <span>(*)</span></label
                                        >
                                        <vs-input
                                            icon="icon icon-unlock"
                                            icon-pack="feather"
                                            type="password"
                                            v-validate="'required|min:3'"
                                            data-vv-validate-on="blur"
                                            autocomplete="new-password" placeholder="Nueva contraseña" aria-label="Nueva contraseña" v-model="password"
                                            class="w-full"
                                            name="password"
                                            data-vv-as="Contraseña"
                                            v-on:keyup.enter="submitForm"
                                        />
                                        <span class="aeternus-reset__error" role="alert">{{
                                            errors.first("password")
                                        }}</span>
                                    </div>

                                    <div class="aeternus-reset__field">
                                        <label
                                            >Confirmar nueva Contraseña
                                            <span>(*)</span></label
                                        >
                                        <vs-input
                                            icon="icon icon-unlock"
                                            icon-pack="feather"
                                            type="password"
                                            v-validate="'required|min:3'"
                                            data-vv-validate-on="blur"
                                            autocomplete="new-password" placeholder="Confirma tu contraseña" aria-label="Confirmar contraseña" v-model="password_confirmation"
                                            class="w-full"
                                            name="confirmar"
                                            data-vv-as="Confirmar Contraseña"
                                            v-on:keyup.enter="submitForm"
                                        />
                                        <span class="aeternus-reset__error" role="alert">{{
                                            errors.first("confirmar")
                                        }}</span>
                                    </div>
                                </form>
        <vs-button class="aeternus-reset__submit" @click.prevent="submitForm">
          <span>Cambiar contraseña</span><feather-icon icon="ArrowRightIcon" />
        </vs-button>
        <vs-button type="border" to="/pages/login" class="aeternus-reset__back">
          <feather-icon icon="ArrowLeftIcon" /><span>Volver a iniciar sesión</span>
        </vs-button>
        <footer class="aeternus-reset__footer">
          <p>Acceso exclusivo para personal autorizado.</p>
          <p>© {{ new Date().getFullYear() }} Aeternus Funerales</p>
        </footer>
      </div>
    </section>
  </main>
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
                            this.$router.push("/");
                        })
                        .catch((error) => {
                            if (error) {
                                this.$vs.notify({
                                    time: 8000,
                                    title: "Error",
                                    text: "Verifique sus datos: [usuario, contraseñas o cree un nuevo correo de renovación de contraseñas] e intente nuevamente.",
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
<style scoped>
/* Solo presentación. submitForm conserva el flujo original: validación,
   POST /password/reset, avisos, limpieza y redirección. No comparte las clases
   genéricas del login anterior para no interferir con otras pantallas. */
.aeternus-reset{display:flex;width:100%;min-height:100vh;background:#fff;color:#354047;font-family:Arial,sans-serif}
.aeternus-reset,.aeternus-reset *{box-sizing:border-box}
.aeternus-reset__brand{position:relative;flex:0 0 55%;min-width:0;overflow:hidden;background:#292e35}
.aeternus-reset__photo{position:absolute;left:0;right:0;bottom:8%;height:70%;background-image:linear-gradient(180deg,#292e35 0%,rgba(41,46,53,.94) 22%,rgba(41,46,53,.66) 40%,rgba(41,46,53,.08) 56%,rgba(24,29,34,.03) 78%,#292e35 100%),url('~@assets/images/funeraria.jpg');background-position:center,center bottom;background-size:100% 100%,cover;background-repeat:no-repeat}
.aeternus-reset__brand-content{position:relative;z-index:1;display:flex;flex-direction:column;min-height:100vh;padding:65px 7.5% 44px}
.aeternus-reset__eyebrow{margin:0;color:#d9b852;font-size:15px;letter-spacing:3px;line-height:1.5;text-transform:uppercase;font-weight:600}
.aeternus-reset__line{display:block;width:64px;height:3px;background:#c7a640;margin-top:20px}
.aeternus-reset__message{margin-top:34px}
/* Vuexy impone !important a los títulos: mantener la escala local aprobada. */
.aeternus-reset__message h1{font-size:48px!important;line-height:1.15;font-weight:700;letter-spacing:-.8px;margin:0;color:#fff}
.aeternus-reset__message p{font-size:19px;line-height:1.5;margin:20px 0 0;color:#d8dce0}
.aeternus-reset__siiga{margin-top:auto;padding:240px 0 0 16px;border-left:0;position:relative;color:#fff}
.aeternus-reset__siiga::before{content:'';position:absolute;bottom:0;left:0;width:3px;height:55px;background:#b99a39}
.aeternus-reset__siiga strong{font-size:29px;line-height:1.2;color:#fff}
.aeternus-reset__siiga p{margin:5px 0 0;font-size:14px;line-height:1.5;color:#d4d9dc}
.aeternus-reset__access{flex:1;min-width:0;display:flex;align-items:center;justify-content:center;padding:42px 5%}
.aeternus-reset__content{width:100%;max-width:540px;text-align:center}
.aeternus-reset__logo{display:block;width:78%;max-width:360px;height:auto;margin:0 auto 36px}
.aeternus-reset__key{display:flex;align-items:center;justify-content:center;width:65px;height:65px;margin:0 auto 20px;background:#fbf3dc;color:#a78522;border-radius:8px}
.aeternus-reset__key svg{width:40px;height:40px}
.aeternus-reset__heading h2{font-size:34px!important;line-height:1.2;letter-spacing:-.5px;margin:0;color:#354047;font-weight:700}
.aeternus-reset__heading p{margin:16px 0 0;color:#69757d;font-size:16px;line-height:1.5}
.aeternus-reset__form{text-align:left;margin-top:32px}
.aeternus-reset__form label{display:block;margin-bottom:10px;font-size:15px;font-weight:600;color:#45525c}
.aeternus-reset >>> .vs-input{width:100%}
.aeternus-reset >>> .vs-input--input{height:50px!important;background:#fff!important;border:1px solid #d3dae0!important;border-radius:4px!important;font-size:15px!important;color:#354047!important;padding-left:44px!important;box-shadow:none!important}
.aeternus-reset >>> .vs-input--input:focus{border-color:#a58828!important;box-shadow:0 0 0 3px rgba(165,136,40,.12)!important}
.aeternus-reset >>> .vs-input--icon{top:16px!important;left:13px!important;color:#7b8790!important;font-size:18px!important;border:0!important;padding:0!important}
.aeternus-reset >>> .vs-input--placeholder{top:3px!important;padding-left:44px!important;font-size:15px!important;color:#929ba3!important}
.aeternus-reset__error{display:block;font-size:12px;color:#ad4145;line-height:1.4;margin-top:6px}
.aeternus-reset__submit{width:100%;min-height:50px;margin-top:27px;background:#a58828!important;border-radius:4px!important;font-size:16px!important;font-weight:600;transition:background-color .18s ease,box-shadow .18s ease}
.aeternus-reset__submit:hover{background:#90721d!important;box-shadow:0 4px 12px rgba(110,86,22,.15)}
.aeternus-reset__submit >>> .vs-button-text,.aeternus-reset__back >>> .vs-button-text{display:flex;align-items:center;justify-content:center;gap:13px}
.aeternus-reset__submit >>> svg,.aeternus-reset__back >>> svg{width:21px;height:21px}
.aeternus-reset__back{display:block;margin:20px auto 0;border:0!important;background:transparent!important;color:#45525c!important;font-size:15px!important;padding:10px 12px!important;box-shadow:none!important}
.aeternus-reset__back:hover{color:#8c6c19!important;background:#faf8f1!important}
.aeternus-reset__footer{margin-top:28px;font-size:11px;color:#849098;line-height:1.6}
.aeternus-reset__footer p{margin:4px 0}
@media(min-width:1600px){.aeternus-reset__message h1{font-size:60px!important}.aeternus-reset__message p{font-size:22px}.aeternus-reset__heading h2{font-size:39px!important}.aeternus-reset__heading p{font-size:18px}.aeternus-reset__brand-content{padding-top:80px}.aeternus-reset__siiga p{font-size:16px}}
/* Ventana baja/laptop: reducir separaciones antes de reducir legibilidad. */
@media(min-width:1024px) and (max-height:800px){.aeternus-reset__brand-content{padding-top:36px;padding-bottom:30px}.aeternus-reset__message h1{font-size:39px!important}.aeternus-reset__message p{font-size:17px}.aeternus-reset__siiga{padding-top:180px}.aeternus-reset__photo{height:68%}.aeternus-reset__access{padding:26px 5%}.aeternus-reset__logo{max-width:280px;margin-bottom:24px}.aeternus-reset__key{width:52px;height:52px;margin-bottom:15px}.aeternus-reset__key svg{width:33px;height:33px}.aeternus-reset__heading h2{font-size:30px!important}.aeternus-reset__heading p{font-size:15px}.aeternus-reset__form{margin-top:24px}.aeternus-reset__footer{margin-top:20px}}
/* Tablet y móvil: cabecera fotográfica, sin texto largo; no fijar altura al
   formulario para que siga siendo accesible con zoom o teclado en pantalla. */
@media(max-width:1023px){.aeternus-reset{flex-direction:column}.aeternus-reset__brand{flex:none;min-height:180px}.aeternus-reset__brand-content{min-height:180px;padding:22px 6%}.aeternus-reset__photo{height:100%;bottom:0;background-image:linear-gradient(180deg,rgba(29,35,40,.68),rgba(29,35,40,.05) 65%,rgba(29,35,40,.12)),url('~@assets/images/funeraria.jpg');background-position:center,center 63%}.aeternus-reset__message,.aeternus-reset__siiga{display:none}.aeternus-reset__eyebrow{font-size:11px;letter-spacing:2px;color:#fff}.aeternus-reset__line{margin-top:12px;width:35px;height:2px}.aeternus-reset__access{padding:30px 24px;flex:1}.aeternus-reset__content{max-width:480px}.aeternus-reset__logo{max-width:240px;margin-bottom:24px}.aeternus-reset__heading h2{font-size:29px!important}.aeternus-reset__heading p{font-size:15px}.aeternus-reset__key{width:50px;height:50px;margin-bottom:16px}.aeternus-reset__key svg{width:31px;height:31px}}
@media(max-width:575px){.aeternus-reset__brand,.aeternus-reset__brand-content{min-height:125px}.aeternus-reset__brand-content{padding:18px 24px}.aeternus-reset__eyebrow{font-size:10px;letter-spacing:1.4px}.aeternus-reset__access{padding:24px}.aeternus-reset__logo{max-width:210px;margin-bottom:21px}.aeternus-reset__heading h2{font-size:26px!important}.aeternus-reset__heading p{font-size:14px}.aeternus-reset__form{margin-top:26px}.aeternus-reset >>> .vs-input--input{font-size:16px!important}.aeternus-reset__form label{font-size:13px}.aeternus-reset__back{font-size:14px!important}.aeternus-reset__footer{font-size:10px;margin-top:21px}}
@media(prefers-reduced-motion:reduce){.aeternus-reset__submit{transition:none}}
/* Tres campos: compactar separaciones para mantener los controles visibles
   en laptop. La altura continúa siendo flexible cuando se muestran errores. */
.aeternus-reset__field+.aeternus-reset__field{margin-top:17px}
.aeternus-reset__form label span{color:#a64a45;font-size:11px;font-weight:400}
.aeternus-reset__error:empty{display:none}
.aeternus-reset__logo{margin-bottom:24px;max-width:310px}
.aeternus-reset__key{width:52px;height:52px;margin-bottom:16px}
.aeternus-reset__key svg{width:33px;height:33px}
.aeternus-reset__form{margin-top:25px}
.aeternus-reset__footer{margin-top:20px}
@media(min-width:1024px) and (max-height:800px){
 .aeternus-reset__access{padding-top:24px;padding-bottom:24px}
 .aeternus-reset__logo{max-width:230px;margin-bottom:16px}
 .aeternus-reset__key{width:42px;height:42px;margin-bottom:12px}
 .aeternus-reset__key svg{width:27px;height:27px}
 .aeternus-reset__heading h2{font-size:28px!important}
 .aeternus-reset__heading p{margin-top:10px;font-size:14px}
 .aeternus-reset__form{margin-top:20px}
 .aeternus-reset__field+.aeternus-reset__field{margin-top:13px}
 .aeternus-reset__submit{margin-top:20px}
 .aeternus-reset__back{margin-top:12px}
 .aeternus-reset__footer{margin-top:15px}
}
@media(max-width:575px){.aeternus-reset__logo{max-width:210px}.aeternus-reset__heading h2{font-size:25px!important}.aeternus-reset__field+.aeternus-reset__field{margin-top:17px}}
</style>
