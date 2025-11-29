<template>
    <div class="servicios-funerarios">
        <vue-slick-carousel class="" v-bind="settings">
            <div
                v-for="n in 4"
                :class="[
                    'slide-servicio',
                    n % 2 > 0 ? 'servicio-contratado' : 'servicio-contratado',
                ]"
                :key="n"
            >
                <div class="slide-header">
                    <div class="fallecido">
                        <h2>SUSANA DEL ROCIO GOMEZ NEGRETE</h2>
                    </div>
                    <div class="velacion">
                        <p v-if="n % 2 == 0">
                            <strong>Servicio por definir.</strong>
                        </p>
                        <p v-else>
                            <strong>Velación: </strong>
                            <span class="">Sala La Piedad.</span>
                        </p>
                        <div class="hidden">
                            <img
                                class="cursor-pointer img-btn-19"
                                src="@assets/images/folder.svg"
                                title="Ver Expediente"
                            />
                            <img
                                class="cursor-pointer img-btn-21"
                                src="@assets/images/whatsapp.svg"
                                title="Compartir Enlace"
                            />
                        </div>
                        <div class="">
                            <span class="">Ver Expediente</span>
                        </div>
                    </div>
                </div>
                <div class="slide-content">
                    <div class="contenido" v-if="n % 2 > 0">
                        <p>Datos del Servicio</p>
                        <div class="datos-contenido">
                            <div class="row-dato">
                                <div>
                                    <p class="dato bg-primary">si</p>
                                    <p class="texto-dato">Cremación</p>
                                </div>
                                <div>
                                    <p class="dato bg-primary">si</p>
                                    <p class="texto-dato">Inhumación</p>
                                </div>
                            </div>
                            <div class="row-dato">
                                <div>
                                    <p class="dato bg-primary">no</p>
                                    <p class="texto-dato">
                                        Ceremonía Religiosa
                                    </p>
                                </div>
                                <div>
                                    <p class="dato bg-danger">si</p>
                                    <p class="texto-dato">$ Saldo Pendiente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="contenido-null" v-else>
                        <p>En proceso de contratación...</p>
                    </div>
                    <div class="imagen">
                        <img src="@assets/images/luto.svg" />
                    </div>
                </div>
            </div>
        </vue-slick-carousel>
    </div>
</template>
<script>
export default {
    // Name of the component (optional)
    name: "ServiciosFunerarios(dashboard)",
    components: {},
    // Props: data passed from parent
    props: {
        servicios: {
            type: Object,
            required: true,
            default: {},
        },
    },
    watch: {},
    // Computed properties: derived reactive data
    computed: {},
    // Data function returns the component's reactive state
    data() {
        return {
            settings: {
                draggable: true, // Make the slider draggable
                swipe: true, // Enable swipe on touch devices
                arrows: false,
                dots: true,
                autoplay: true,
                infinite: true,
                slidesToShow: 1,
                adaptiveHeight: true,
                autoplaySpeed: 8000,
                pauseOnDotsHover: true,
                pauseOnFocus: true,
                pauseOnHover: true,
            },
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {},
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style lang="scss" scoped></style>
