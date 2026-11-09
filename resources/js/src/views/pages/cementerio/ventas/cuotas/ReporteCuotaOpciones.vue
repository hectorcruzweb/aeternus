<template>
    <div>
        <vs-popup class="forms-popup popup-50 z-index58k reporte-cuota-opciones" style="z-index:58000 !important" title="Reporte de cuota"
            ref="ReporteCuotaOpciones" :active="show"
            @click.native.capture="bloquearExterior" @update:active="cambioVisibilidad">
            <div class="opciones-cuota">
                <div class="cuota-contexto">
                    <feather-icon icon="FileTextIcon" class="cuota-contexto__icono" svgClasses="w-5 h-5" />
                    <div><span>Cuota seleccionada</span><strong>{{ descripcion }}</strong></div>
                </div>
                <label for="estado-reporte-cuota">Propiedades a incluir</label>
                <select id="estado-reporte-cuota" ref="estado" v-model="estado" class="cuota-selector">
                    <option value="todos">Todas las propiedades</option>
                    <option value="pagados">Solo las pagadas</option>
                    <option value="adeudo">Solo las pendientes de pago</option>
                </select>
                <div class="cuota-acciones"><span class="cuota-formatos">PDF y Excel</span><vs-button @click="$emit('seleccionar', estado)"><span class="cuota-boton"><feather-icon icon="FileTextIcon" svgClasses="w-4 h-4" />Ver reporte</span></vs-button></div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
/** Modal independiente: z-index explícito al nivel del visor; PopupManager controla foco, Escape y
 * registro de depuración sin reemplazar la entrada del listado de cuotas. */
export default {
    name: 'ReporteCuotaOpciones',
    props: { show: { type: Boolean, required: true }, descripcion: { type: String, default: '' } },
    data() { return { estado: 'todos' }; },
    watch: { show: { immediate: true, handler(abierto) {
        if (abierto) this.$popupManager.register(this, this.cerrar, 'estado');
        else this.$popupManager.unregister(this.$options.name);
    } } },
    methods: {
        cerrar() { this.$emit('close'); },
        cambioVisibilidad(abierto) { if (!abierto) this.cerrar(); },
        bloquearExterior(evento) {
            if (evento.target.classList && evento.target.classList.contains('vs-popup--background')) {
                evento.preventDefault(); evento.stopImmediatePropagation();
            }
        }
    },
    beforeDestroy() { this.$popupManager.unregister(this.$options.name); }
};
</script>
<style scoped>
/* Tamaño local: no altera las proporciones de los demás formularios popup. */
.reporte-cuota-opciones >>> .vs-popup{width:540px!important;max-width:calc(100vw - 32px)!important;height:auto!important;max-height:calc(100vh - 32px)!important}
.reporte-cuota-opciones >>> .vs-popup--content{width:100%;height:auto!important;max-height:calc(100vh - 115px);overflow-y:auto;padding:16px 20px!important;box-sizing:border-box}
.opciones-cuota{padding:0 0 4px;color:#515960;font:13px Arial,sans-serif}
.cuota-contexto{display:flex;align-items:center;gap:12px;border-left:3px solid #ab891d;padding:2px 0 2px 12px;margin:0 0 20px}
.cuota-contexto__icono{color:#a18025;flex-shrink:0}.cuota-contexto span{display:block;color:#7c8389;font-size:11px;margin-bottom:5px}.cuota-contexto strong{display:block;font-size:14px;line-height:1.4;overflow-wrap:break-word}
.opciones-cuota label{display:block;font-size:12px;font-weight:600;margin-bottom:7px}
.cuota-selector{display:block;width:100%;height:40px;border:1px solid #cbd0d4;background:white;color:#515960;padding:0 10px;border-radius:0;font:13px Arial,sans-serif}.cuota-selector:focus{outline:0;border-color:#ab891d;box-shadow:0 0 0 2px rgba(171,137,29,.12)}
.cuota-acciones{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:18px}.cuota-formatos{font-size:11px;color:#858b90}.cuota-boton{display:inline-flex;align-items:center;justify-content:center;gap:8px}
@media(max-width:520px){.reporte-cuota-opciones >>> .vs-popup--content{padding:14px!important}.cuota-selector{font-size:16px}.cuota-contexto strong{font-size:13px}}
</style>
