<template>
    <DashboardSkeleton v-if="cargando" variante="servicios" titulo="servicios funerarios" />
<div v-else class="servicios-funerarios">
        <vue-slick-carousel ref="carousel" class="" v-bind="settings" v-if="data.length > 0">
            <div v-for="item in data" :key="item.id" :class="[
                'slide-servicio',
                item.datosoperacion !== null ? 'servicio-contratado' : 'servicio-contratado',
            ]">
                <div class="slide-header">
                    <div class="fallecido">
                        <h2>{{ item.nombre_afectado }}</h2>
                    </div>
                    <div class="velacion">
                        <p v-if="item.datosoperacion === null">
                            <strong>Servicio por definir.</strong>
                        </p>
                        <p v-else>
                            <strong>Velación: </strong>
                            <span class="" v-if="item.velacion_b === 1">{{ item.lugarvelacion.lugar }}</span>
                            <span class="" v-else>Sin velación</span>
                        </p>
                        <div class="hidden">
                            <img class="cursor-pointer img-btn-19" src="@assets/images/folder.svg"
                                title="Ver Expediente" />
                            <img class="cursor-pointer img-btn-21" src="@assets/images/whatsapp.svg"
                                title="Compartir Enlace" />
                        </div>
                        <div class="">
                            <p class="btn-text" @click="
                                ConsultarVenta(
                                    item.id
                                )
                                ">Ver Expediente</p>
                        </div>
                    </div>
                </div>
                <div class="slide-content">
                    <div class="contenido" v-if="item.datosoperacion !== null">
                        <p>Datos del Servicio</p>
                        <div class="datos-contenido">
                            <div class="row-dato">
                                <div>
                                    <p class="dato bg-primary">{{ item.cremacion }}</p>
                                    <p class="texto-dato">Cremación</p>
                                </div>
                                <div>
                                    <p class="dato bg-primary">{{ item.inhumacion }}</p>
                                    <p class="texto-dato">Inhumación</p>
                                </div>
                            </div>
                            <div class="row-dato">
                                <div>
                                    <p class="dato bg-primary">{{ item.misa }}</p>
                                    <p class="texto-dato">
                                        Ceremonía Religiosa
                                    </p>
                                </div>
                                <div>
                                    <p class="dato"
                                        :class="[item.datosoperacion.saldo > 0 ? 'bg-danger' : 'bg-primary']"><span
                                            v-if="item.datosoperacion.saldo > 0">Si</span><span v-else>No</span></p>
                                    <p class="texto-dato">Saldo Pendiente</p>
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
        <div v-else class="skeleton">
            <p>No hay servicios funerarios en proceso.</p>
        </div>
        <ReportesServicio v-if="openReportes" :show="openReportes" @closeListaReportes="closeListaReportes"
            :id_solicitud="id_solicitud"></ReportesServicio>
    </div>
</template>
<script>
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Carrusel de servicios en proceso, datos operativos y acceso al expediente.
 * Dónde ajustar: Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos
 * proceden de HomeInfo y del backend.
 * Relacionado: HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

import DashboardSkeleton from "./DashboardSkeleton.vue";
import ReportesServicio from '../pages/funeraria/servicios_funerarios/ReportesServicio.vue';
export default {
    // Name of the component (optional)
    name: "ServiciosFunerarios(dashboard)",
    components: {
    DashboardSkeleton,
        ReportesServicio
    },
    // Props: data passed from parent
    props: {
    // Ocultar importes/acciones mientras el padre espera datos.
    cargando: { type: Boolean, default: false },
        data: {
            type: Array,
            required: true
        }
    },
    watch: {},
    // Computed properties: derived reactive data
    computed: {},
    // Data function returns the component's reactive state
    /**
     * Detalle del método: Devuelve el estado reactivo inicial por instancia de ServiciosFunerarios; no
     * consulta al servidor por sí mismo.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    data() {
        return {
            id_solicitud: 0 /**para consultar los reportes */,
            openReportes: false,
            settings: {
                draggable: true, // Make the slider draggable
                swipe: true, // Enable swipe on touch devices
                arrows: false,
                dots: true,
                autoplay: true,
                infinite: true,
                slidesToShow: 1,
                adaptiveHeight: false, // La card toma toda la altura de su fila, no solo la del texto.
                autoplaySpeed: 10000,
                pauseOnDotsHover: true,
                pauseOnFocus: true,
                pauseOnHover: true
            },
        };
    },
    // Methods: functions you can call in template or other hooks
    methods: {
        /**
         * Detalle del método: Selecciona el servicio y abre su consulta/expediente existente sin modificar la
         * contratación.
         * Entradas: id_solicitud (número del servicio funerario a consultar).
         */
        ConsultarVenta(id_solicitud) {
      if (this.cargando) return;
            this.$refs.carousel.pause(); //pauso el carrusel
            this.id_solicitud = id_solicitud;
            this.openReportes = true;
        },
        /**
         * Detalle del método: Cierra la lista de reportes del servicio funerario que estaba abierta.
         * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
         */
        closeListaReportes() {
            this.openReportes = false;
            this.$refs.carousel.play(); //reanudamos el carrusel
        },
    },
    // Lifecycle hooks
    /**
     * Detalle del método: Inicializa el estado previo al montaje de ServiciosFunerarios; Vue lo ejecuta al
     * crear la instancia, sin valor de retorno.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    /**
     * Detalle del método: Completa la preparación de ServiciosFunerarios cuando sus referencias de DOM ya
     * están disponibles.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    mounted() {
        this.$log("Component mounted! " + this.$options.name); // DOM is ready
    },
    /**
     * Detalle del método: Libera recursos o invalida respuestas pendientes de ServiciosFunerarios al
     * destruirse; evita listeners, tareas o popups huérfanos según la limpieza implementada.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    /**
     * Detalle del método: Libera recursos o invalida respuestas pendientes de ServiciosFunerarios al
     * destruirse; evita listeners, tareas o popups huérfanos según la limpieza implementada.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     */
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
<style lang="scss" scoped></style>
