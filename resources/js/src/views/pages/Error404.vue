<template>
  <main class="aeternus-error404">
    <section class="aeternus-error404__content" aria-labelledby="error404-heading">
      <img src="/images/logo/logo_png.png" alt="Aeternus Funerales" class="aeternus-error404__logo" />
      <!-- Ficha horizontal en escritorio; se apila en móviles para mantener la lectura. -->
      <div class="aeternus-error404__card">
        <p class="aeternus-error404__code" aria-label="Error 404">404</p>
        <div class="aeternus-error404__message">
          <h1 id="error404-heading">Página no encontrada</h1>
          <p>La página que buscas no existe o no está habilitada.<br class="aeternus-error404__break" /> Vuelve al inicio para continuar utilizando el sistema.</p>
          <!-- Se conserva la navegación original; esta vista no modifica la sesión. -->
          <vs-button size="large" to="/" class="aeternus-error404__submit">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9" /></svg>
            <span>Volver al sistema</span>
          </vs-button>
        </div>
      </div>
      <footer class="aeternus-error404__footer">Departamento de Sistemas · SIIGA</footer>
    </section>
  </main>
</template>

<style scoped>
/* Estilos exclusivos de la pantalla 404: no afectan al login ni a otros módulos. */
.aeternus-error404{display:flex;align-items:center;justify-content:center;min-height:100vh;width:100%;padding:40px 28px;background:#f6f7f8;color:#354047;font-family:Arial,sans-serif}
.aeternus-error404,.aeternus-error404 *{box-sizing:border-box}
.aeternus-error404__content{width:100%;max-width:960px}
.aeternus-error404__logo{display:block;width:250px;max-width:70%;height:auto;margin:0 auto 36px}
.aeternus-error404__card{display:flex;align-items:center;gap:38px;padding:42px;background:#fff;border:1px solid #ded9c9;border-radius:8px}
.aeternus-error404__code{flex:0 0 210px;margin:0;text-align:center;color:#a58828;font-size:100px;line-height:1;font-weight:700;letter-spacing:1px}
.aeternus-error404__message{min-width:0;flex:1;border-left:1px solid #e1e3e5;padding-left:38px}
/* Vuexy aplica !important a los títulos; la escala se limita a esta pantalla. */
.aeternus-error404__message h1{margin:0;color:#30383d;font-size:28px!important;line-height:1.25;font-weight:700}
.aeternus-error404__message p{margin:14px 0 22px;color:#68737b;font-size:15px;line-height:1.65}
.aeternus-error404__submit{display:inline-flex;align-items:center;justify-content:center;min-height:46px;margin:0;padding:12px 21px!important;background:#a58828!important;color:#fff!important;border-radius:4px!important;font-size:15px!important;font-weight:600;transition:background-color .18s ease}
.aeternus-error404__submit >>> .vs-button-text{display:flex;align-items:center;justify-content:center;gap:10px;color:#fff;font-size:15px;line-height:22px}
.aeternus-error404__submit svg{display:block;flex:none;width:21px;height:21px;color:#fff}
.aeternus-error404__submit:hover{background:#8e731e!important}
.aeternus-error404__submit:focus-visible{outline:3px solid #c7ad62;outline-offset:4px}
.aeternus-error404__footer{margin-top:25px;text-align:center;color:#78828a;font-size:12px;line-height:1.5}
@media(max-width:767px){.aeternus-error404__card{gap:24px;padding:30px 24px}.aeternus-error404__code{flex-basis:130px;font-size:76px}.aeternus-error404__message{padding-left:24px}.aeternus-error404__message h1{font-size:24px!important}.aeternus-error404__break{display:none}}
@media(max-width:575px){.aeternus-error404{padding:28px 20px}.aeternus-error404__logo{width:210px;margin-bottom:28px}.aeternus-error404__card{flex-direction:column;gap:22px;padding:30px 23px;text-align:center}.aeternus-error404__code{flex:none;font-size:80px}.aeternus-error404__message{width:100%;padding:22px 0 0;border-left:0;border-top:1px solid #e1e3e5}.aeternus-error404__message h1{font-size:23px!important}.aeternus-error404__message p{font-size:14px;margin-bottom:20px}.aeternus-error404__footer{font-size:11px;margin-top:22px}}
@media(prefers-reduced-motion:reduce){.aeternus-error404__submit{transition:none}}
</style>
