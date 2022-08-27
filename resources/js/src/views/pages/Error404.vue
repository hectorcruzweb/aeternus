<!-- =========================================================================================
    File Name: Error404.vue
    Description: 404 Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <div class="h-screen flex w-full bg-img">
    <div class="layer"></div>
    <div
      class="zindex1000 vx-col flex items-center justify-center flex-col sm:w-1/2 md:w-3/5 lg:w-3/4 xl:w-1/2 mx-auto text-center"
    >
      <img src="@assets/images/pages/404.png" alt="graphic-404" class="mx-auto mb-4 max-w-full" />
      <h1 class="sm:mx-0 mx-4 mb-12 text-5xl bg-danger text-white">Error - Página no encontrada!</h1>
      <p
        class="sm:mx-0 mx-4 mb-4 sm:mb-16 bg-danger text-white"
      >Al parecer estas ingresando a un área del sistema que no existe o no esta habilitada. Para volver al menu inicial de click en el botón de abajo.</p>
      <vs-button size="large" to="/">Volver al sistema</vs-button>
    </div>
  </div>
</template>
