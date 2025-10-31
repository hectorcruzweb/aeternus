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
    <footer class="the-footer xl:py-2 flex-wrap justify-between size-small" :class="classes">
        <div class="w-full flex flex-wrap justify-between items-center">
            <div>
                <span>COPYRIGHT &copy; {{ new Date().getFullYear() }} Aeternus Funerales,
                    Todos los derechos reservados.</span>
            </div>
            <clock class="hidden xl:block"></clock>
        </div>


    </footer>
</template>
<script>
export default {
    name: "the-footer",
    components: {},
    props: {
        classes: {
            type: String
        }
    }
    /*inject: {
        components: {
            default: {}
        }
    }*/
};
</script>
