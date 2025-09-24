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
    <footer class="the-footer flex-wrap justify-between size-small" :class="classes">
        <span>COPYRIGHT &copy; {{ new Date().getFullYear() }} Aeternus Funerales, Todos
            los derechos reservados.</span>
        <span class="md:flex hidden items-center">
            <span class="hidden">Hand-crafted & Made with</span>
        </span>
        <PopupDebug />123
    </footer>
</template>
<script>
import PopupDebug from '../../views/PopupDebug.vue';
export default {
    name: "the-footer",
    components: { PopupDebug },
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
