<!-- =========================================================================================
    File Name: Maintenance.vue
    Description: Maintenance Page
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
      <img
        src="@assets/images/pages/maintenance-2.png"
        alt="graphic-maintenance"
        class="mx-auto mb-4"
      />
      <h1 class="sm:mx-0 mx-4 mb-6 text-5xl bg-danger text-white">Sección en desarrollo!</h1>
      <p
        class="sm:mx-0 mx-4 mb-6 d-theme-text-inverse"
      >Si está viendo esta sección es porque la sección que seleccionó está en lista de prioridad para el desarrollo del sistema.</p>
      <vs-button size="large" to="/">Regresar el Inicio</vs-button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      value1: ""
    };
  }
};
</script>
