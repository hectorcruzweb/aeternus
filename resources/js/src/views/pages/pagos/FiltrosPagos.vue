<template>
  <vx-card class="pagos-filtros" no-radius title="Filtros de selección" :collapse-action="false">
    <template slot="actions"><button type="button" class="pf-reporte" :disabled="reporteDeshabilitado" @click="$emit('reporte')"><feather-icon icon="FileTextIcon" />Reporte del listado<feather-icon icon="ArrowUpRightIcon" /></button><button type="button" class="pf-limpiar" @click="limpiar"><feather-icon icon="RefreshCwIcon" />Limpiar filtros</button></template>
    <div class="pf-fila pf-principal">
      <div><label for="pf-numero">Número de pago / referencia</label><div class="pf-numero"><select v-model="f.buscar_por" aria-label="Buscar número de pago o referencia"><option value="pago">Núm. de pago</option><option value="referencia">Núm. referencia</option></select><input id="pf-numero" v-model="f.numero" :inputmode="f.buscar_por === 'pago' ? 'numeric' : 'text'" maxlength="100" :placeholder="f.buscar_por === 'pago' ? 'Número de pago' : 'Referencia del pago'" /></div></div>
      <div><label for="pf-cliente">Nombre del cliente</label><input id="pf-cliente" v-model="f.cliente" maxlength="100" placeholder="Buscar por nombre del cliente" /></div>
      <div v-for="fecha in fechas" :key="fecha.key"><label :for="'pf-'+fecha.key">{{ fecha.label }}</label><div class="pf-fecha"><flat-pickr :id="'pf-'+fecha.key" v-model="f[fecha.key]" :config="calendario" placeholder="Selecciona una fecha" /><button v-if="f[fecha.key]" type="button" :aria-label="'Limpiar '+fecha.label" @mousedown.prevent @click.stop="f[fecha.key]=''"><feather-icon icon="XIcon" /></button></div></div>
    </div>
    <div class="pf-fila pf-secundaria">
      <div><label for="pf-tipo">Tipo de operación</label><select id="pf-tipo" v-model="f.tipo_operacion"><option value="">Todas las operaciones</option><option v-for="o in operaciones" :key="o.id" :value="String(o.id)">{{ o.nombre }}</option></select></div>
      <div><label for="pf-operacion">Núm. de operación</label><input id="pf-operacion" v-model="f.numero_operacion" inputmode="numeric" maxlength="12" :placeholder="placeholderOperacion" /><small v-if="f.tipo_operacion === '2'" class="pf-ayuda">Número de venta de la propiedad.</small></div>
      <div><label for="pf-movimiento">Tipo de movimiento</label><select id="pf-movimiento" v-model="f.movimiento"><option value="">Todos los movimientos</option><option v-for="m in movimientos" :key="m.id" :value="String(m.id)">{{ m.nombre }}</option></select></div>
      <div><label for="pf-status">Estado del pago</label><select id="pf-status" v-model="f.status"><option value="">Todos</option><option value="1">Activos</option><option value="0">Cancelados</option></select></div>
    </div>
    <div v-if="chips.length" class="pf-chips"><button v-for="chip in chips" :key="chip.key" type="button" @click="f[chip.key]=''">{{ chip.label }}<feather-icon icon="XIcon" /></button></div>
  </vx-card>
</template>
<script>
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/airbnb.css';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
const inicial=()=>({buscar_por:'pago',numero:'',cliente:'',tipo_operacion:'',numero_operacion:'',desde:'',hasta:'',status:'',movimiento:''});
/** Card de presentación independiente: emite los filtros; no consulta ni modifica
 * pagos. Fechas ISO para API y texto español para pantalla. Cada X solo borra
 * su fecha/filtro; pulsar el resto del calendario permite cambiarla normalmente. */
export default {
  name:'FiltrosPagos',components:{flatPickr},props:{reporteDeshabilitado:{type:Boolean,default:false},operaciones:{type:Array,default:()=>[]},movimientos:{type:Array,default:()=>[]}},
  data(){return {f:inicial(),fechas:[{key:'desde',label:'Pago desde'},{key:'hasta',label:'Pago hasta'}],calendario:{locale:Spanish,dateFormat:'Y-m-d',altInput:true,altFormat:'j \\d\\e F \\d\\e Y',altInputClass:'pf-fecha-input',disableMobile:true,allowInput:false}};},
  computed:{
    placeholderOperacion(){return {'1':'Número de venta en cementerio','2':'Número de venta de la propiedad','3':'Número de servicio funerario','4':'Número de venta de plan','5':'Número de venta general'}[this.f.tipo_operacion] || 'Número de venta o servicio';},
    chips(){const nombres={numero:this.f.buscar_por==='pago'?'Núm. de pago':'Referencia',cliente:'Cliente',tipo_operacion:'Operación',numero_operacion:'Núm. de operación',desde:'Desde',hasta:'Hasta',status:'Estado',movimiento:'Movimiento'};
      return Object.keys(nombres).filter(k=>this.f[k]!=='').map(key=>{let valor=this.f[key];
        if(key==='status')valor=valor==='1'?'Activos':'Cancelados';
        if(key==='tipo_operacion')valor=(this.operaciones.find(o=>String(o.id)===valor)||{}).nombre || valor;
        if(key==='movimiento')valor=(this.movimientos.find(o=>String(o.id)===valor)||{}).nombre || valor;
        if(key==='desde'||key==='hasta')valor=this.fechaHumana(valor);
        return {key,label:nombres[key]+': '+valor};});}
  },
  watch:{f:{deep:true,handler(){this.$emit('change',{...this.f});}}},
  methods:{limpiar(){this.f=inicial();},fechaHumana(valor){const partes=valor.split('-').map(Number);return partes.length===3?new Date(partes[0],partes[1]-1,partes[2]).toLocaleDateString('es-MX',{day:'numeric',month:'long',year:'numeric'}):valor;}}
};
</script>
<style scoped>
.pf-fila{display:grid;gap:16px;align-items:start}.pf-principal{grid-template-columns:minmax(230px,1.2fr) minmax(220px,1.8fr) minmax(180px,1fr) minmax(180px,1fr)}.pf-secundaria{grid-template-columns:repeat(4,minmax(0,1fr));margin-top:16px}
.pf-fila>div{min-width:0}.pf-fila label{display:block;font:11px Arial,sans-serif;color:#777;text-transform:uppercase;margin:0 0 6px}
.pf-fila input,.pf-fila select,.pf-fecha >>> input{display:block;width:100%;height:38px!important;box-sizing:border-box;border:1px solid #c9c9c9;border-radius:0!important;padding:0 10px;background:white;color:#60656a;font:12px Arial,sans-serif!important;min-width:0;outline:0}
.pf-fila input:focus,.pf-fila select:focus,.pf-fecha >>> input:focus{border-color:#ab8a22;box-shadow:0 0 0 1px #ab8a2220}.pf-fila input::placeholder,.pf-fecha >>> input::placeholder{color:#999}
.pf-numero{display:flex}.pf-numero select{flex:0 0 132px;width:132px;border-right:0;background:#fafafa;padding:0 6px}.pf-numero input{flex:1;width:0}
.pf-fecha{position:relative}.pf-fecha >>> .flatpickr-wrapper{display:block;width:100%}.pf-fecha >>> input{padding-right:32px}.pf-fecha button{position:absolute;right:7px;top:8px;width:23px;height:23px;display:flex;align-items:center;justify-content:center;border:0;background:white;color:#888;cursor:pointer}.pf-fecha button >>> svg{width:13px;height:13px}
.pf-reporte{display:inline-flex;align-items:center;gap:6px;margin-right:18px;border:0;background:transparent;color:#96751f;font:600 12px Arial,sans-serif;cursor:pointer;padding:4px 0}.pf-reporte >>> svg{width:14px;height:14px}.pf-reporte:hover{text-decoration:underline}.pf-reporte:disabled{opacity:.5;cursor:default}.pagos-filtros >>> .vx-card__actions{display:flex;align-items:center;flex-wrap:wrap;gap:8px}
.pf-limpiar{display:inline-flex;align-items:center;gap:6px;border:0;background:transparent;color:#858585;font:12px Arial,sans-serif;cursor:pointer;padding:4px}.pf-limpiar >>> svg{width:14px;height:14px}.pf-limpiar:hover{color:#987924}
.pf-chips{display:flex;gap:8px;flex-wrap:wrap;border-top:1px solid #eee;padding-top:12px;margin-top:14px}.pf-chips button{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:0;background:#eee;color:#62676b;padding:6px 8px;font:11px Arial,sans-serif;cursor:pointer;max-width:100%;text-align:left;overflow-wrap:anywhere}.pf-chips button >>> svg{width:11px;height:11px;flex-shrink:0}.pf-ayuda{display:block;font:10px Arial,sans-serif;color:#90969a;margin-top:5px}
@media(max-width:1250px){.pf-principal,.pf-secundaria{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:600px){.pf-fila{grid-template-columns:1fr;gap:12px}.pf-fila input,.pf-fila select,.pf-fecha >>> input{font-size:16px!important}.pf-numero select{flex-basis:150px;width:150px}.pagos-filtros >>> .vx-card__header{flex-wrap:wrap;gap:8px}}
</style>
