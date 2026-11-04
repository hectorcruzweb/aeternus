<template>
    <label class="reporte-idioma">
        <feather-icon icon="GlobeIcon" class="reporte-idioma__icono" aria-hidden="true" />
        <span class="reporte-idioma__etiqueta">Idioma</span>
        <select :value="value" aria-label="Idioma del reporte" @change="cambiarIdioma">
            <option value="es">Español</option><option value="en">Inglés</option>
        </select>
        <feather-icon icon="ChevronDownIcon" class="reporte-idioma__flecha" aria-hidden="true" />
    </label>
</template>
<script>
/** Selector compartido para reportes. El padre controla la consulta mediante v-model. */
export default {
    name: "IdiomaReporte",
    props: { value: { type: String, default: "es" } },
    methods: {
        /** Emite el código elegido; no genera consultas ni modifica el idioma de la aplicación. */
        cambiarIdioma(evento) { this.$emit("input", evento.target.value); },
    },
};
</script>
<style scoped>
/* Encapsulado para no modificar los selectores de otros formularios. */
.reporte-idioma { display:inline-flex; align-items:center; position:relative; height:38px; margin:0 10px 0 0; vertical-align:middle; background:#fff; border:1px solid #ded8c8; border-radius:3px; color:#646464; box-sizing:border-box; }
.reporte-idioma:focus-within { border-color:#b18b1e; box-shadow:0 0 0 2px rgba(177,139,30,.13); }
.reporte-idioma__icono { width:16px; height:16px; margin-left:11px; color:#a28225; }
.reporte-idioma__etiqueta { font-size:11px; margin-left:7px; color:#777; }
.reporte-idioma select { appearance:none; -webkit-appearance:none; border:0; background:transparent; color:#464646; font-family:inherit; font-size:12px; height:100%; padding:0 29px 0 10px; min-width:100px; cursor:pointer; }
.reporte-idioma__flecha { position:absolute; right:9px; width:13px; height:13px; pointer-events:none; color:#a28225; }
@media (max-width:575px) { .reporte-idioma { margin-bottom:8px; } }
</style>
