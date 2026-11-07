<template>
  <main class="aeternus-recovery">
    <!-- Misma identidad del login; el panel se vuelve cabecera en pantallas pequeñas. -->
    <aside class="aeternus-recovery__brand" aria-label="Departamento de Sistemas">
      <div class="aeternus-recovery__photo" aria-hidden="true"></div>
      <div class="aeternus-recovery__brand-content">
        <p class="aeternus-recovery__eyebrow">Departamento de Sistemas</p>
        <span class="aeternus-recovery__line" aria-hidden="true"></span>
        <div class="aeternus-recovery__message">
          <h1>Recupera el acceso<br />a tu espacio de trabajo.</h1>
          <p>Mantén tus herramientas de trabajo a tu alcance.</p>
        </div>
        <div class="aeternus-recovery__siiga">
          <strong>SIIGA</strong>
          <p>Sistema Integral de Información y Gerencia Administrativa</p>
        </div>
      </div>
    </aside>
    <section class="aeternus-recovery__access" aria-labelledby="recovery-heading">
      <div class="aeternus-recovery__content">
        <img src="@assets/images/logo/aeternus.jpg" alt="Aeternus Funerales" class="aeternus-recovery__logo" />
        <div class="aeternus-recovery__key" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M29 5a12 12 0 0 0-10.4 18L5 36.6V43h6.4v-5.4h5.4v-5.4l6.2-6.1A12 12 0 1 0 29 5Z" />
            <circle cx="31" cy="15" r="3.5" />
          </svg>
        </div>
        <header class="aeternus-recovery__heading">
          <h2 id="recovery-heading">Recuperar contraseña</h2>
          <p>Ingresa tu correo electrónico y te enviaremos las instrucciones para recuperar tu cuenta.</p>
        </header>
        <!-- Conservar nombre, reglas, modelo, ref y envío por Enter originales.
             El botón permanece fuera del form con su mismo @click.prevent:
             así un clic no dispara también un segundo submit. -->
        <form @submit.prevent="submitForm" class="aeternus-recovery__form">
          <label for="recovery-email">Correo electrónico</label>
          <vs-input id="recovery-email" v-validate="'required|email|min:3|max:50'" name="email" maxlength="50"
            icon-no-border data-vv-as=" " icon="icon icon-mail" icon-pack="feather"
            placeholder="nombre@aeternus.com.mx" v-model="email" class="w-full" ref="email"
            autocomplete="email" aria-label="Correo electrónico" />
          <span v-if="errors.first('email')" class="aeternus-recovery__error" role="alert">{{ errors.first('email') }}</span>
        </form>
        <vs-button class="aeternus-recovery__submit" @click.prevent="submitForm">
          <span>Recuperar contraseña</span><feather-icon icon="ArrowRightIcon" />
        </vs-button>
        <vs-button type="border" to="/pages/login" class="aeternus-recovery__back">
          <feather-icon icon="ArrowLeftIcon" /><span>Volver a iniciar sesión</span>
        </vs-button>
        <footer class="aeternus-recovery__footer">
          <p>Acceso exclusivo para personal autorizado.</p>
          <p>© {{ new Date().getFullYear() }} Aeternus Funerales</p>
        </footer>
      </div>
    </section>
  </main>
</template>
<script>
import axios from "../../http/axios/index";
export default {
    data() {
        return {
            email: "",
        };
    },
    methods: {
        submitForm() {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    const payload = {
                        email: this.email,
                    };
                    this.$vs.loading();

                    axios
                        .post("/password/email", payload)
                        .then((resp) => {
                            // Success
                            this.$vs.notify({
                                time: 6000,
                                title: "Recuperar contraseña",
                                text: resp.data,
                                color: "success",
                            });
                            this.email = "";
                            this.$validator.reset(); // ✅ Reset all validation errors
                            this.$vs.loading.close();
                            this.$router.push("/");
                        })
                        .catch((error) => {
                            // Extract server message if exists
                            let message =
                                "Ocurrió un error, intente nuevamente.";
                            if (
                                error.response &&
                                error.response.data &&
                                error.response.data.error
                            ) {
                                message = error.response.data.error;
                            }
                            this.$vs.notify({
                                time: 6000,
                                title: "Recuperar contraseña",
                                text: message,
                                color: "danger",
                            });
                            this.$vs.loading.close();
                        });
                } else {
                    // Validation failed
                    this.$vs.notify({
                        time: 6000,
                        title: "Recuperar contraseña",
                        text: "Por favor corrija los errores de validación.",
                        color: "warning",
                    });
                }
            });

            return false;
        },
    },
};
</script>
<style scoped>
/* Solo presentación. submitForm conserva el flujo original: validación,
   POST /password/email, avisos, limpieza y redirección. No comparte las clases
   genéricas del login anterior para no interferir con otras pantallas. */
.aeternus-recovery{display:flex;width:100%;min-height:100vh;background:#fff;color:#354047;font-family:Arial,sans-serif}
.aeternus-recovery,.aeternus-recovery *{box-sizing:border-box}
.aeternus-recovery__brand{position:relative;flex:0 0 55%;min-width:0;overflow:hidden;background:#292e35}
.aeternus-recovery__photo{position:absolute;left:0;right:0;bottom:8%;height:70%;background-image:linear-gradient(180deg,#292e35 0%,rgba(41,46,53,.94) 22%,rgba(41,46,53,.66) 40%,rgba(41,46,53,.08) 56%,rgba(24,29,34,.03) 78%,#292e35 100%),url('~@assets/images/funeraria.jpg');background-position:center,center bottom;background-size:100% 100%,cover;background-repeat:no-repeat}
.aeternus-recovery__brand-content{position:relative;z-index:1;display:flex;flex-direction:column;min-height:100vh;padding:65px 7.5% 44px}
.aeternus-recovery__eyebrow{margin:0;color:#d9b852;font-size:15px;letter-spacing:3px;line-height:1.5;text-transform:uppercase;font-weight:600}
.aeternus-recovery__line{display:block;width:64px;height:3px;background:#c7a640;margin-top:20px}
.aeternus-recovery__message{margin-top:34px}
/* Vuexy impone !important a los títulos: mantener la escala local aprobada. */
.aeternus-recovery__message h1{font-size:48px!important;line-height:1.15;font-weight:700;letter-spacing:-.8px;margin:0;color:#fff}
.aeternus-recovery__message p{font-size:19px;line-height:1.5;margin:20px 0 0;color:#d8dce0}
.aeternus-recovery__siiga{margin-top:auto;padding:240px 0 0 16px;border-left:0;position:relative;color:#fff}
.aeternus-recovery__siiga::before{content:'';position:absolute;bottom:0;left:0;width:3px;height:55px;background:#b99a39}
.aeternus-recovery__siiga strong{font-size:29px;line-height:1.2;color:#fff}
.aeternus-recovery__siiga p{margin:5px 0 0;font-size:14px;line-height:1.5;color:#d4d9dc}
.aeternus-recovery__access{flex:1;min-width:0;display:flex;align-items:center;justify-content:center;padding:42px 5%}
.aeternus-recovery__content{width:100%;max-width:540px;text-align:center}
.aeternus-recovery__logo{display:block;width:78%;max-width:360px;height:auto;margin:0 auto 36px}
.aeternus-recovery__key{display:flex;align-items:center;justify-content:center;width:65px;height:65px;margin:0 auto 20px;background:#fbf3dc;color:#a78522;border-radius:8px}
.aeternus-recovery__key svg{width:40px;height:40px}
.aeternus-recovery__heading h2{font-size:34px!important;line-height:1.2;letter-spacing:-.5px;margin:0;color:#354047;font-weight:700}
.aeternus-recovery__heading p{margin:16px 0 0;color:#69757d;font-size:16px;line-height:1.5}
.aeternus-recovery__form{text-align:left;margin-top:32px}
.aeternus-recovery__form label{display:block;margin-bottom:10px;font-size:15px;font-weight:600;color:#45525c}
.aeternus-recovery >>> .vs-input{width:100%}
.aeternus-recovery >>> .vs-input--input{height:50px!important;background:#fff!important;border:1px solid #d3dae0!important;border-radius:4px!important;font-size:15px!important;color:#354047!important;padding-left:44px!important;box-shadow:none!important}
.aeternus-recovery >>> .vs-input--input:focus{border-color:#a58828!important;box-shadow:0 0 0 3px rgba(165,136,40,.12)!important}
.aeternus-recovery >>> .vs-input--icon{top:16px!important;left:13px!important;color:#7b8790!important;font-size:18px!important;border:0!important;padding:0!important}
.aeternus-recovery >>> .vs-input--placeholder{top:3px!important;padding-left:44px!important;font-size:15px!important;color:#929ba3!important}
.aeternus-recovery__error{display:block;font-size:12px;color:#ad4145;line-height:1.4;margin-top:6px}
.aeternus-recovery__submit{width:100%;min-height:50px;margin-top:27px;background:#a58828!important;border-radius:4px!important;font-size:16px!important;font-weight:600;transition:background-color .18s ease,box-shadow .18s ease}
.aeternus-recovery__submit:hover{background:#90721d!important;box-shadow:0 4px 12px rgba(110,86,22,.15)}
.aeternus-recovery__submit >>> .vs-button-text,.aeternus-recovery__back >>> .vs-button-text{display:flex;align-items:center;justify-content:center;gap:13px}
.aeternus-recovery__submit >>> svg,.aeternus-recovery__back >>> svg{width:21px;height:21px}
.aeternus-recovery__back{display:block;margin:20px auto 0;border:0!important;background:transparent!important;color:#45525c!important;font-size:15px!important;padding:10px 12px!important;box-shadow:none!important}
.aeternus-recovery__back:hover{color:#8c6c19!important;background:#faf8f1!important}
.aeternus-recovery__footer{margin-top:28px;font-size:11px;color:#849098;line-height:1.6}
.aeternus-recovery__footer p{margin:4px 0}
@media(min-width:1600px){.aeternus-recovery__message h1{font-size:60px!important}.aeternus-recovery__message p{font-size:22px}.aeternus-recovery__heading h2{font-size:39px!important}.aeternus-recovery__heading p{font-size:18px}.aeternus-recovery__brand-content{padding-top:80px}.aeternus-recovery__siiga p{font-size:16px}}
/* Ventana baja/laptop: reducir separaciones antes de reducir legibilidad. */
@media(min-width:1024px) and (max-height:800px){.aeternus-recovery__brand-content{padding-top:36px;padding-bottom:30px}.aeternus-recovery__message h1{font-size:39px!important}.aeternus-recovery__message p{font-size:17px}.aeternus-recovery__siiga{padding-top:180px}.aeternus-recovery__photo{height:68%}.aeternus-recovery__access{padding:26px 5%}.aeternus-recovery__logo{max-width:280px;margin-bottom:24px}.aeternus-recovery__key{width:52px;height:52px;margin-bottom:15px}.aeternus-recovery__key svg{width:33px;height:33px}.aeternus-recovery__heading h2{font-size:30px!important}.aeternus-recovery__heading p{font-size:15px}.aeternus-recovery__form{margin-top:24px}.aeternus-recovery__footer{margin-top:20px}}
/* Tablet y móvil: cabecera fotográfica, sin texto largo; no fijar altura al
   formulario para que siga siendo accesible con zoom o teclado en pantalla. */
@media(max-width:1023px){.aeternus-recovery{flex-direction:column}.aeternus-recovery__brand{flex:none;min-height:180px}.aeternus-recovery__brand-content{min-height:180px;padding:22px 6%}.aeternus-recovery__photo{height:100%;bottom:0;background-image:linear-gradient(180deg,rgba(29,35,40,.68),rgba(29,35,40,.05) 65%,rgba(29,35,40,.12)),url('~@assets/images/funeraria.jpg');background-position:center,center 63%}.aeternus-recovery__message,.aeternus-recovery__siiga{display:none}.aeternus-recovery__eyebrow{font-size:11px;letter-spacing:2px;color:#fff}.aeternus-recovery__line{margin-top:12px;width:35px;height:2px}.aeternus-recovery__access{padding:30px 24px;flex:1}.aeternus-recovery__content{max-width:480px}.aeternus-recovery__logo{max-width:240px;margin-bottom:24px}.aeternus-recovery__heading h2{font-size:29px!important}.aeternus-recovery__heading p{font-size:15px}.aeternus-recovery__key{width:50px;height:50px;margin-bottom:16px}.aeternus-recovery__key svg{width:31px;height:31px}}
@media(max-width:575px){.aeternus-recovery__brand,.aeternus-recovery__brand-content{min-height:125px}.aeternus-recovery__brand-content{padding:18px 24px}.aeternus-recovery__eyebrow{font-size:10px;letter-spacing:1.4px}.aeternus-recovery__access{padding:24px}.aeternus-recovery__logo{max-width:210px;margin-bottom:21px}.aeternus-recovery__heading h2{font-size:26px!important}.aeternus-recovery__heading p{font-size:14px}.aeternus-recovery__form{margin-top:26px}.aeternus-recovery >>> .vs-input--input{font-size:16px!important}.aeternus-recovery__form label{font-size:13px}.aeternus-recovery__back{font-size:14px!important}.aeternus-recovery__footer{font-size:10px;margin-top:21px}}
@media(prefers-reduced-motion:reduce){.aeternus-recovery__submit{transition:none}}
</style>
