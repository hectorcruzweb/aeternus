<template>
  <div>
    <vs-popup class="forms-popup popup-80" title="Mapa visual del cementerio" :active="show" ref="MapaCementerioReporte" @click.native.capture="bloquearExterior" @update:active="alCambiarVisibilidad">
      <div class="mapa-opciones">
        <p class="mapa-opciones__intro">Selecciona las áreas y la información que deseas consultar en el mapa.</p>
        <fieldset><legend>Contenido del reporte</legend><div class="mapa-opciones__modos">
          <button v-for="opcion in contenidos" :key="opcion.value" type="button"
            class="mapa-opciones__modo" :class="{ activo: contenido === opcion.value }"
            :aria-pressed="contenido === opcion.value" @click="contenido = opcion.value">
            <feather-icon :icon="opcion.icon" />
            <span class="mapa-opciones__texto">
              <strong>{{ opcion.label }}</strong>
              <small>{{ opcion.descripcion }}</small>
            </span>
          </button>
        </div></fieldset>
        <fieldset v-if="!esResumen"><legend>Áreas del cementerio</legend>
          <label class="mapa-opciones__label" for="mapa-tipo">Tipo de sección</label><select id="mapa-tipo" v-model="tipo"><option value="">Todas las secciones</option><option v-for="t in tipos" :key="t.value" :value="t.value">{{ t.label }}</option></select>
          <div class="mapa-opciones__seleccion"><span>{{ seleccion.length }} de {{ areasVisibles.length }} áreas seleccionadas</span><button type="button" @click="seleccionarTodas">{{ todas ? 'Quitar selección' : 'Seleccionar todas' }}</button></div>
          <div class="mapa-opciones__areas"><label v-for="area in areasVisibles" :key="area.id"><input type="checkbox" :value="String(area.id)" v-model="seleccion" /><span>{{ area.nombre_area }}</span></label><p v-if="!areasVisibles.length">No hay áreas disponibles para esta sección.</p></div>
        </fieldset>
        <p v-if="esResumen" class="mapa-opciones__nota">El reporte incluye todas las áreas del cementerio.</p>
        <p class="mapa-opciones__nota"><feather-icon icon="InfoIcon" />La disponibilidad corresponde a propiedades para venta, no a gavetas libres. El reporte se imprime en carta {{ esResumen ? 'vertical' : 'horizontal' }}.</p>
        <div class="mapa-opciones__acciones"><button type="button" class="mapa-opciones__cancelar" @click="cancelar">Cancelar</button><button type="button" class="mapa-opciones__generar" :disabled="!esResumen && !seleccion.length" @click="generar"><feather-icon :icon="esResumen ? 'FileTextIcon' : 'MapIcon'" />{{ esResumen ? 'Ver reporte' : 'Ver mapa' }}</button></div>
      </div>
    </vs-popup>
    <VisorReportes v-if="mostrarVisor" :show="mostrarVisor" :cargar="cargarMapa" :descargar-pdf="descargarPdf" @close="mostrarVisor = false" />
  </div>
</template>
<script>
import axios from '@/axios.js';
import VisorReportes from '@pages/VisorReportes.vue';
/** Configuración aislada: no cambia los filtros del listado ni utiliza get_mapeado.
 * Selección explícita de áreas; HTML inicial y PDF bajo demanda mediante el visor común. */
export default {
  name:'MapaCementerioReporte',components:{VisorReportes},
  props:{show:{type:Boolean,required:true},areas:{type:Array,default:()=>[]},tipoInicial:{type:String,default:''},areaInicial:{type:String,default:''}},
  data(){return {tipo:this.tipoInicial,seleccion:this.areaInicial?[this.areaInicial]:[],contenido:'disponibilidad',mostrarVisor:false,parametros:{},contenidos:[
    {value:'disponibilidad',label:'Disponibilidad con mapa',icon:'GridIcon',descripcion:'Mapa de las áreas seleccionadas.'},
    {value:'resumida',label:'Resumen general',icon:'BarChart2Icon',descripcion:'Totales por tipo de propiedad.'},
    {value:'resumida_desglose',label:'Resumen por ubicación',icon:'ListIcon',descripcion:'Detalle por terraza, sección o columna.'},
    {value:'ventas',label:'Propiedades vendidas',icon:'FileTextIcon',descripcion:'Mapa y datos de la venta, titular, contacto e importes.'},
    {value:'servicios',label:'Con servicios funerarios',icon:'UsersIcon',descripcion:'Áreas con servicios registrados y nombres de fallecidos.'},
    {value:'general',label:'Estado general',icon:'LayersIcon',descripcion:'Mapa, ventas y servicios por propiedad.'}
  ]};},
  computed:{
    esResumen() {
      return this.contenido === 'resumida' || this.contenido === 'resumida_desglose';
    },
    tipos(){const t=new Map();this.areas.forEach(a=>t.set(String(a.tipo_propiedades_id),{value:String(a.tipo_propiedades_id),label:(a.tipo_propiedad||{}).tipo||a.nombre_area}));return Array.from(t.values()).sort((a,b)=>a.label.localeCompare(b.label,'es'));},
    areasVisibles(){return this.tipo?this.areas.filter(a=>String(a.tipo_propiedades_id)===this.tipo):this.areas;},
    todas(){return this.areasVisibles.length>0&&this.areasVisibles.every(a=>this.seleccion.includes(String(a.id)));}
  },
  watch:{tipo(){this.seleccion=[];},show:{immediate:true,handler(v){if(v)this.$popupManager.register(this,this.cancelar,'input');else this.$popupManager.unregister(this.$options.name);}}},
  methods:{
    seleccionarTodas(){this.seleccion=this.todas?[]:this.areasVisibles.map(a=>String(a.id));},
    /** Congela los parámetros para que actualizar el visor repita la misma selección. */
    generar() {
      if (!this.esResumen && !this.seleccion.length) return;
      this.parametros = this.esResumen
        ? { contenido: this.contenido }
        : { tipo: this.tipo, areas: this.seleccion.slice(), contenido: this.contenido };
      this.mostrarVisor = true;
    },
    async cargarMapa(){return (await axios.get('/cementerio/reportes/mapa-visual',{params:this.parametros})).data;},
    async descargarPdf(token){return (await axios.get('/cementerio/reportes/mapa-visual/'+token+'/pdf',{responseType:'blob'})).data;},
    bloquearExterior(e){if(e.target.classList&&e.target.classList.contains('vs-popup--background')){e.preventDefault();e.stopImmediatePropagation();}},
    alCambiarVisibilidad(v){if(!v)this.cancelar();},
    cancelar(){if(this.mostrarVisor)return;this.$emit('close');}
  },
  beforeDestroy(){this.$popupManager.unregister(this.$options.name);}
};
</script>
<style scoped>
.mapa-opciones{padding:4px 8px 12px;color:#626970;font:13px Arial,sans-serif}.mapa-opciones__intro{margin:0 0 18px;line-height:1.5}
.mapa-opciones fieldset{margin:0 0 18px;padding:16px;border:1px solid #dce0e4;border-radius:4px;min-width:0}.mapa-opciones legend{padding:0 10px;color:#a18025;font-size:14px}
.mapa-opciones__modos{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}
.mapa-opciones__modo{display:flex;align-items:flex-start;gap:8px;min-width:0;padding:8px 10px;border:1px solid #ddd;background:white;color:#626970;text-align:left;cursor:pointer;font:13px Arial,sans-serif}
.mapa-opciones__modo:hover{border-color:#b69a4c}
.mapa-opciones__modo.activo{border-color:#a58828;background:#faf7ed;color:#795f17}
.mapa-opciones__texto strong,.mapa-opciones__texto small{display:block;line-height:1.25}
.mapa-opciones__texto small{font-size:11px;margin-top:3px}
.mapa-opciones__modo >>> .feather-icon{flex-shrink:0}
.mapa-opciones svg{width:18px;height:18px}.mapa-opciones__modos >>> svg{width:16px;height:16px}.mapa-opciones__label{display:block;margin-bottom:6px;font-size:11px;text-transform:uppercase}.mapa-opciones select{width:100%;height:38px;border:1px solid #c9c9c9;border-radius:0;background:white;color:#666;padding:0 10px;font:13px Arial,sans-serif}
.mapa-opciones__seleccion{display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;margin:14px 0;font-size:12px}.mapa-opciones__seleccion button{border:0;background:none;color:#96771e;cursor:pointer;text-decoration:underline}
.mapa-opciones__areas{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;max-height:230px;overflow-y:auto;padding:4px}.mapa-opciones__areas label{display:flex;align-items:center;gap:9px;padding:9px;border:1px solid #e2e2e2;cursor:pointer;line-height:1.4;overflow-wrap:anywhere}.mapa-opciones__areas input{accent-color:#a58828;flex-shrink:0}
.mapa-opciones__nota{display:flex;align-items:flex-start;gap:8px;line-height:1.5;color:#7a8187;font-size:12px}.mapa-opciones__nota >>> svg{width:16px;height:16px;flex-shrink:0}
.mapa-opciones__acciones{display:flex;justify-content:flex-end;gap:12px;padding-top:14px}.mapa-opciones__acciones button{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:8px 18px;border-radius:0;font:13px Arial,sans-serif;cursor:pointer}.mapa-opciones__cancelar{border:1px solid #b14545;background:white;color:#b14545}.mapa-opciones__generar{border:1px solid #a58828;background:#a58828;color:white}.mapa-opciones__generar:disabled{opacity:.45;cursor:not-allowed}.mapa-opciones button:focus-visible,.mapa-opciones select:focus-visible,.mapa-opciones input:focus-visible{outline:2px solid #a58828;outline-offset:3px}
@media(max-width:760px){.mapa-opciones__modos,.mapa-opciones__areas{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:520px){.mapa-opciones__modos,.mapa-opciones__areas{grid-template-columns:1fr}.mapa-opciones fieldset{padding:12px}.mapa-opciones__acciones button{flex:1}.mapa-opciones select{font-size:16px}}
</style>
