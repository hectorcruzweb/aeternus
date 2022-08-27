<!-- =========================================================================================
    File Name: TheFooter.vue
    Description: Footer component
    Component Name: TheFooter
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template functional>
  <footer
    class="the-footer flex-wrap justify-between size-small"
    :class="classes"
  >
    <span
      >COPYRIGHT &copy; {{ new Date().getFullYear() }} Aeternus Funerales, Todos
      los derechos reservados.</span
    >
    <span class="md:flex hidden items-center">
      <span class="hidden">Hand-crafted & Made with</span>
    </span>
  </footer>
</template>
<script>
export default {
  name: "the-footer",
  props: {
    classes: {
      type: String,
    },
  },
  inject: {
    components: {
      default: {},
    },
  },
};
</script>
