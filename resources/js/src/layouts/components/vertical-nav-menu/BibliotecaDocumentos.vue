<template>
  <div>
    <vs-popup class="forms-popup biblioteca-documentos biblioteca-documentos--completa" :fullscreen="true" style="z-index:57000!important" :title="titulo"
      ref="BibliotecaDocumentos" :active="true" @click.native.capture="bloquearExterior"
      @update:active="cambioVisibilidad">
      <div class="biblioteca-cuerpo">
        <div v-if="categoria !== 'guias'" class="biblioteca-controles">
          <label>Idioma<select v-model="idioma"><option value="es">Español</option><option value="en">Inglés</option></select></label>
        </div>
        <p v-if="cargando" role="status">Cargando documentos disponibles…</p>
        <div v-else-if="error" class="biblioteca-error" role="alert">{{ error }} <button type="button" @click="cargarCatalogo">Reintentar</button></div>
        <fieldset v-else class="biblioteca-grupo"><legend>Selecciona un documento</legend>
          <section v-for="grupo in gruposDocumentos" :key="grupo.nombre" class="biblioteca-seccion">
          <h3 v-if="categoria === 'guias'" class="biblioteca-seccion-titulo">{{ grupo.nombre }}</h3>
          <div class="biblioteca-hojas">
            <button v-for="documento in grupo.documentos" :key="documento.clave" type="button" class="biblioteca-documento"
              @click="abrir(documento)" :aria-label="'Abrir ' + documento.nombre">
              <span class="biblioteca-papel" aria-hidden="true">
                <span class="biblioteca-doblez"></span>
                <svg v-if="documento.icono === 'urna'" class="biblioteca-icono" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M16 9h16v5H16zM20 5h8v4h-8zM14 16h20c4 11-1 20-6 26h-8c-5-6-10-15-6-26zM12 20h24M14 25h20" /></svg>
                <svg v-else-if="documento.icono === 'cruz'" class="biblioteca-icono" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 6h6v9h9v6h-9v17h-6V21h-9v-6h9zM14 38h20v5H14z" /></svg>
                <feather-icon v-else :icon="documento.icono" class="biblioteca-icono-feather" />
                <span class="biblioteca-lineas"><i></i><i></i><i></i></span>
              </span>
              <strong>{{ documento.nombre }}</strong><small>{{ documento.detalle || 'Documento PDF' }}</small>
            </button>
          </div>
          </section>
          <p v-if="!documentos.length" class="biblioteca-vacio">No hay documentos disponibles en esta sección.</p>
        </fieldset>
        <p class="biblioteca-ayuda"><feather-icon icon="MousePointerIcon" svgClasses="w-4 h-4" />Selecciona una hoja para abrir el visor.</p>
      </div>
    </vs-popup>
    <VisorReportes v-if="seleccion" :key="seleccion.clave" :show="true" :cargar="cargarReporte"
      :descargar-pdf="descargarPdf" :enviar-correo="enviarCorreo" @close="seleccion = null" />
  </div>
</template>
<script>
import VisorReportes from '@pages/VisorReportes.vue';
import funeraria from '@services/funeraria';
import cementerio from '@services/cementerio';
import servicios from '@services/reportesDocumentosServicio';
import planes from '@services/reportesDocumentosPlanes';
import terrenos from '@services/reportesDocumentosCementerio';
/** Biblioteca de consulta del menú. Las hojas abren HTML en el visor; el PDF se
 * genera solo al descargar/imprimir o enviar. No abre formularios de edición ni
 * usa expedientes de clientes como documentos de ejemplo. Catálogos: BD actual. */
export default {
  name: 'BibliotecaDocumentos', components: { VisorReportes },
  props: { categoria: { type: String, required: true } },
  data() { return { documentos: [], idioma: 'es', cargando: false, error: '', seleccion: null }; },
  computed: {
    /** Agrupar solo guías; los catálogos comerciales conservan su cuadrícula. */
    gruposDocumentos() {
      if (this.categoria !== 'guias') return [{ nombre: 'Documentos', documentos: this.documentos }];
      return this.documentos.reduce((grupos, documento) => {
        let grupo = grupos.find(g => g.nombre === documento.grupo);
        if (!grupo) { grupo = { nombre: documento.grupo, documentos: [] }; grupos.push(grupo); }
        grupo.documentos.push(documento); return grupos;
      }, []);
    },
    titulo() { return { guias: 'Guías de servicio', planes: 'Planes funerarios', cementerio: 'Ventas en cementerio' }[this.categoria]; }
  },
  methods: {
    /** Carga únicamente el catálogo de la sección abierta. No conserva precios en
     * el navegador: cada reporte consulta el formato vigente en su controlador. */
    async cargarCatalogo() {
      this.cargando = true; this.error = '';
      try {
        if (this.categoria === 'guias') {
          const respuesta = await servicios.visualizar('guias', {});
          if (this._isDestroyed) return;
          this.documentos = respuesta.data;
        } else if (this.categoria === 'planes') {
          const respuesta = await funeraria.get_planes();
          if (this._isDestroyed) return;
          this.documentos = [{ clave: 'catalogo', nombre: 'Lista de planes y precios', icono: 'LayersIcon' }].concat(
            respuesta.data.filter(p => Number(p.status) === 1).map(p => ({ clave: 'plan-' + p.id, documento: 'plan', id: p.id, nombre: p.plan, icono: 'FileTextIcon', detalle: 'Plan funerario · PDF' }))
          );
        } else if (this.categoria === 'cementerio') {
          const respuesta = await cementerio.get_financiamientos();
          if (this._isDestroyed) return;
          this.documentos = [{ clave: 'planes-venta', nombre: 'Todos los planes de venta', icono: 'MapIcon' }].concat(
            respuesta.data.map(p => ({ clave: 'propiedad-' + p.id, documento: 'planes-venta', id: p.id, nombre: p.tipo, icono: 'cruz', detalle: 'Planes y precios · PDF' }))
          );
        }
      } catch (e) { this.error = 'No se pudieron cargar los documentos. Intenta nuevamente.'; }
      finally { this.cargando = false; }
    },
    /** Congela sección, documento, filtro e idioma para que descargar/correo
     * utilicen la misma selección aun si el usuario regresa a la biblioteca. */
    abrir(documento) {
      this.seleccion = { clave: documento.clave, documento: documento.documento || documento.clave,
        categoria: this.categoria, idioma: this.idioma, id: documento.id || null };
    },
    servicioReporte() { return this.seleccion.categoria === 'guias' ? servicios : this.seleccion.categoria === 'planes' ? planes : terrenos; },
    async cargarReporte() {
      const s = this.seleccion;
      return (await this.servicioReporte().visualizar(s.documento,
        s.categoria === 'guias' ? {} : s.categoria === 'planes' ? { idioma: s.idioma, id_plan: s.id } : { idioma: s.idioma, id_tipo_propiedad: s.id })).data;
    },
    async descargarPdf(token) { return (await this.servicioReporte().pdf(this.seleccion.documento, token)).data; },
    async enviarCorreo(token, datos) { return (await this.servicioReporte().correo(this.seleccion.documento, token, datos)).data; },
    cerrar() { this.$emit('close'); },
    cambioVisibilidad(activa) { if (!activa) this.cerrar(); },
    bloquearExterior(evento) {
      if (evento.target.classList && evento.target.classList.contains('vs-popup--background')) {
        evento.preventDefault(); evento.stopImmediatePropagation();
      }
    }
  },
  mounted() { this.$popupManager.register(this, this.cerrar); this.cargarCatalogo(); },
  beforeDestroy() { this.$popupManager.unregister(this.$options.name); }
};
</script>
<style scoped>
/* Estilos locales: mantiene header/X del sistema y no altera el resto del menú.
   El selector queda debajo del visor (58000); PopupManager controla Escape/foco. */
.biblioteca-documentos >>> .vs-popup--background{background:rgba(0,0,0,.78)!important}
.biblioteca-documentos >>> .vs-popup{width:900px!important;max-width:calc(100vw - 32px)!important;height:auto!important;max-height:calc(100vh - 40px)!important;background:white}
.biblioteca-documentos >>> .vs-popup--content{width:100%;height:auto!important;max-height:calc(100vh - 140px);overflow-y:auto;box-sizing:border-box;padding:18px 24px 21px!important}
.biblioteca-cuerpo{font:13px Arial,sans-serif;color:#555e64}
.biblioteca-controles{display:flex;justify-content:flex-end;margin:0 0 12px}
.biblioteca-controles label{display:flex;align-items:center;gap:10px;font-size:12px}
.biblioteca-controles select{height:34px;border:1px solid #cbd0d4;border-radius:0;background:white;padding:0 26px 0 10px;color:#555e64}
.biblioteca-grupo{border:1px solid #dde1e5;border-radius:5px;padding:20px 16px;margin:0;min-width:0}
.biblioteca-grupo legend{padding:0 10px;color:#a08125;font-size:14px}
.biblioteca-seccion + .biblioteca-seccion{border-top:1px solid #e5e6e8;margin-top:18px;padding-top:18px}
.biblioteca-seccion-titulo{font:600 13px Arial,sans-serif;color:#8c7125;margin:0 0 18px}
.biblioteca-hojas{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:26px 20px}
.biblioteca-documento{display:flex;flex-direction:column;align-items:center;border:0;background:transparent;padding:4px 6px 10px;color:#515960;cursor:pointer;min-width:0;font-family:Arial,sans-serif}
.biblioteca-papel{position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;width:132px;height:170px;max-width:100%;border:1px solid #cbd0d4;background:#fff;box-shadow:0 2px 4px #0000000a;margin-bottom:13px;transition:transform .15s,border-color .15s,box-shadow .15s}
.biblioteca-doblez{position:absolute;right:-1px;top:-1px;width:25px;height:25px;background:linear-gradient(45deg,#f2f3f3 49%,white 50%);border-left:1px solid #cbd0d4;border-bottom:1px solid #cbd0d4}
.biblioteca-icono{width:54px;height:54px;color:#a1842c}
.biblioteca-icono-feather{color:#a1842c}.biblioteca-icono-feather >>> svg{width:48px;height:48px}
.biblioteca-lineas{display:block;width:70%;margin-top:18px}.biblioteca-lineas i{display:block;height:3px;background:#e7e8e8;margin:5px 0}.biblioteca-lineas i:last-child{width:70%}
.biblioteca-documento strong{font-size:13px;line-height:1.45;text-align:center;overflow-wrap:break-word;font-weight:600}
.biblioteca-documento small{font-size:11px;color:#8a9095;margin-top:5px;text-align:center}
.biblioteca-documento:hover .biblioteca-papel,.biblioteca-documento:focus-visible .biblioteca-papel{border-color:#aa8a22;box-shadow:0 3px 10px #aa8a2228;transform:translateY(-3px)}
.biblioteca-documento:focus-visible{outline:2px solid #aa8a22;outline-offset:3px}
.biblioteca-ayuda{display:flex;align-items:center;justify-content:center;gap:8px;font-size:11px;color:#858b90;margin:16px 0 0}
.biblioteca-error{padding:15px;border-left:2px solid #b14a4a;background:#faf5f5;line-height:1.6}.biblioteca-error button{border:0;background:none;color:#93751f;text-decoration:underline;cursor:pointer}
.biblioteca-vacio{margin:15px;text-align:center}
@media(max-width:650px){.biblioteca-hojas{grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 12px}.biblioteca-documentos >>> .vs-popup--content{padding:14px!important}.biblioteca-papel{width:112px;height:145px}.biblioteca-grupo{padding:15px 8px}.biblioteca-documento strong{font-size:12px}}
@media(max-width:350px){.biblioteca-hojas{grid-template-columns:1fr}}
/* Catálogos extensos: pantalla completa y columnas según el ancho disponible.
   Guías y catálogos comparten pantalla completa; el visor conserva su configuración. */
.biblioteca-documentos--completa >>> .vs-popup{width:100vw!important;max-width:100vw!important;height:100vh!important;max-height:100vh!important;margin:0!important;border-radius:0;display:flex;flex-direction:column}
.biblioteca-documentos--completa >>> .vs-popup--content{flex:1;min-height:0;height:auto!important;max-height:none!important;overflow-y:auto;margin-bottom:21px!important}
.biblioteca-documentos--completa .biblioteca-hojas{grid-template-columns:repeat(auto-fill,minmax(170px,1fr));align-items:start}
@media(max-width:650px){.biblioteca-documentos--completa .biblioteca-hojas{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:350px){.biblioteca-documentos--completa .biblioteca-hojas{grid-template-columns:1fr}}
@media(prefers-reduced-motion:reduce){.biblioteca-papel{transition:none}}
</style>
