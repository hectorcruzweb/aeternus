<template>
    <div>
        <ListadoRegistrosChecadorFiltros @abrir-tarjetas="mostrarTarjetas = true" />
        <TarjetasAsistenciaModal v-if="mostrarTarjetas" @close="mostrarTarjetas = false" />
    </div>
</template>

<script>
import ListadoRegistrosChecadorFiltros from "./ListadoRegistrosChecadorFiltros.vue";
import TarjetasAsistenciaModal from "./TarjetasAsistenciaModal.vue";

export default {
    name: "ChecadorRegistros",
    components: { ListadoRegistrosChecadorFiltros, TarjetasAsistenciaModal },
    data() {
        return { mostrarTarjetas: false };
    },
};
</script>
