<template>
  <div>
    <div class="text-right">
      <vs-button
        class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
        color="primary"
        @click="openCuotasCementerio = true"
        type="border"
      >
        <span>Cuotas / Cementerio</span>
      </vs-button>
      <vs-button
        class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
        color="primary"
        @click="openPlanesVenta = true"
        type="border"
      >
        <span>Planes de Venta</span>
      </vs-button>
      <vs-button
        class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
        color="success"
        @click="formulario('agregar')"
      >
        <span>Vender Propiedad</span>
      </vs-button>
    </div>
    <!-- Buscador alternativo. No alterar VentasList.vue: permite volver al listado anterior. -->
    <div class="pt-2 vx-col w-full">
      <vx-card class="cementerio-filtros-card" no-radius title="Filtros de selección" :collapse-action="false">
        <template slot="actions">
                  <button type="button" class="cementerio-filtros__reporte" :disabled="cargandoAreas || !areasCementerio.length" @click="mostrarMapaCementerio = true"><feather-icon icon="MapIcon" />Mapa del cementerio<feather-icon icon="ArrowUpRightIcon" /></button>
                  <button type="button" class="cementerio-filtros__reporte" :disabled="cargandoListado || !!errorListado" @click="abrirReporte"><feather-icon icon="FileTextIcon" />Reporte del listado<feather-icon icon="ArrowUpRightIcon" /></button>
                  <button type="button" class="cementerio-filtros__clear" @click="limpiarFiltros"><feather-icon icon="RefreshCwIcon" />Limpiar filtros</button></template>
        <div class="cementerio-filtros">
          <div class="cementerio-filtros__fields">
            <div><label for="cementerio-numero">Número de referencia</label><div class="cementerio-filtros__referencia"><select v-model="filtros.tipoNumero" aria-label="Tipo de referencia"><option value="4">Venta</option><option value="1">Solicitud</option><option value="2">Convenio</option><option value="3">Título</option></select><input id="cementerio-numero" v-model="filtros.numero" inputmode="numeric" maxlength="14" placeholder="Número" autocomplete="off" /></div></div>
            <div><label for="cementerio-titular">Nombre del titular</label><input id="cementerio-titular" v-model="filtros.titular" maxlength="75" placeholder="Buscar por nombre del titular" autocomplete="off" /></div>
            <div><label for="cementerio-fallecido">Nombre del fallecido</label><input id="cementerio-fallecido" v-model="filtros.fallecido" maxlength="75" placeholder="Buscar fallecido en la propiedad" autocomplete="off" /></div>
            <div v-for="fecha in [{key:'desde',label:'Venta desde'},{key:'hasta',label:'Venta hasta'}]" :key="fecha.key"><label :for="'venta-'+fecha.key">{{ fecha.label }}</label><div class="cementerio-filtros__date-control"><flat-pickr :id="'venta-'+fecha.key" v-model="filtros[fecha.key]" :config="calendarioVentas" placeholder="Selecciona una fecha" /><button v-if="filtros[fecha.key]" type="button" class="cementerio-filtros__date-clear" :aria-label="'Limpiar '+fecha.label" @mousedown.prevent @click.stop="filtros[fecha.key] = ''"><feather-icon icon="XIcon" /></button></div></div>
          </div>
          <div class="cementerio-filtros__ubicacion">
            <div><label for="cementerio-tipo-seccion">Tipo de sección</label><select id="cementerio-tipo-seccion" v-model="filtros.tipoSeccion" :disabled="cargandoAreas"><option value="">Todas las secciones</option><option v-for="tipo in tiposSeccion" :key="tipo.value" :value="tipo.value">{{ tipo.label }}</option></select></div>
            <div><label for="cementerio-area">Área de la propiedad</label><select id="cementerio-area" v-model="filtros.area" :disabled="cargandoAreas"><option value="">Todas las áreas</option><option v-for="area in areasDisponibles" :key="area.id" :value="String(area.id)">{{ area.nombre_area }}</option></select></div>
            <div><label for="cementerio-fila">{{ etiquetaFila }}</label><select id="cementerio-fila" v-model="filtros.fila" :disabled="!areaSeleccionada || cargandoAreas"><option value="">{{ areaSeleccionada ? 'Todas' : 'Selecciona un área' }}</option><option v-for="fila in filasDisponibles" :key="fila.value" :value="fila.value">{{ fila.label }}</option></select></div>
            <div><label for="cementerio-lote">Lote</label><select id="cementerio-lote" v-model="filtros.lote" :disabled="!filtros.fila || !esTerraza || cargandoAreas"><option value="">{{ !areaSeleccionada ? 'Selecciona un área' : !esTerraza ? 'No aplica' : !filtros.fila ? 'Selecciona una fila' : 'Todos los lotes' }}</option><option v-for="lote in lotesDisponibles" :key="lote" :value="String(lote)">Lote {{ lote }}</option></select></div>

            <div><label for="cementerio-estado">Estado de la venta</label><select id="cementerio-estado" v-model="filtros.estado"><option v-for="opcion in estadosOptions" :key="opcion.value" :value="opcion.value">{{ opcion.label }}</option></select></div>
            <div><label for="cementerio-sepultados">Personas sepultadas</label><select id="cementerio-sepultados" v-model="filtros.sepultados"><option value="">Todas las propiedades</option><option value="con">Con personas sepultadas</option><option value="sin">Sin personas sepultadas</option></select></div>
            <div><label for="cementerio-cuotas-vencidas">Cuotas de mantenimiento</label><select id="cementerio-cuotas-vencidas" v-model="filtros.cuotasVencidas"><option value="">Todas</option><option value="con">Con cuotas vencidas</option><option value="sin">Sin cuotas vencidas</option></select></div>
          </div>
          <p v-if="errorAreas" class="cementerio-filtros__error" role="alert">{{ errorAreas }} <button type="button" @click="cargarAreas">Reintentar</button></p>
    
          <div v-if="etiquetasFiltros.length" class="cementerio-filtros__chips" aria-label="Filtros aplicados"><button v-for="etiqueta in etiquetasFiltros" :key="etiqueta.key" type="button" :aria-label="'Quitar ' + etiqueta.label" @click="filtros[etiqueta.key] = ''">{{ etiqueta.label }}<feather-icon icon="XIcon" /></button></div>
          <p v-if="errorListado" class="cementerio-filtros__error" role="alert">{{ errorListado }}</p>
          <p v-if="cargandoListado" class="cementerio-filtros__loading" role="status">Actualizando ventas…</p>
        </div>
      </vx-card>
    </div>
    <MapaCementerioReporte v-if="mostrarMapaCementerio" :show="mostrarMapaCementerio" :areas="areasCementerio" :tipo-inicial="filtros.tipoSeccion" :area-inicial="filtros.area" @close="mostrarMapaCementerio = false" />
    <VisorReportes :show="mostrarReporte" :cargar="cargarReporte" :descargar-archivo="descargarExcel" :descargar-pdf="descargarPdf" @close="mostrarReporte = false" />
    <div id="resultados" class="mt-5 flex flex-col flex-1">
      <div
        v-if="noDataFound"
        class="w-full skeleton flex-1 items-center justify-center"
      >
        <span class="text-gray-600 text-lg font-normal"
          >No hay datos que mostrar</span
        >
      </div>
      <div v-else id="results" class="w-full flex flex-wrap">
        <div class="w-full py-2">
          <vs-table
            :sst="true"
            :max-items="15"
            :data="ventas"
            noDataText="0 Resultados"
            class="tabla-datos"
          >
            <template slot="header">
              <h3>Listado de Ventas Realizadas del Cementerio</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Venta</vs-th>
              <vs-th>Titular</vs-th>
              <vs-th v-if="mostrarColumnaFallecidos">Fallecido(a)</vs-th>
              <vs-th>Uso Venta</vs-th>
              <vs-th>Solicitud</vs-th>
              <vs-th>Convenio</vs-th>
              <vs-th>Título</vs-th>
              <vs-th>Ubicacion</vs-th>
              <vs-th>Convenio</vs-th>
              <vs-th>$ Saldo Venta</vs-th>
              <vs-th>Estatus</vs-th>
              <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].ventas_terrenos_id">
                  <span class="font-medium">{{
                    data[indextr].ventas_terrenos_id
                  }}</span>
                </vs-td>
                <vs-td :data="data[indextr].nombre">{{
                  data[indextr].nombre
                }}</vs-td>
                <vs-td v-if="mostrarColumnaFallecidos">
                  <div class="cementerio-listado__fallecidos">
                    <span v-for="(persona, indice) in (data[indextr].sepultados || [])" :key="persona.servicios_funerarios_id || indice">{{ persona.nombre_afectado || 'Nombre no disponible' }}</span>
                    <span v-if="!(data[indextr].sepultados || []).length" class="cementerio-listado__sin-fallecidos">Sin personas sepultadas</span>
                  </div>
                </vs-td>
                <vs-td
                  :data="data[indextr].venta_terreno.tipo_financiamiento_texto"
                  >{{
                    data[indextr].venta_terreno.tipo_financiamiento_texto
                  }}</vs-td
                >
                <vs-td :data="data[indextr].numero_solicitud">
                  <span class="">{{
                    data[indextr].numero_solicitud_texto
                  }}</span>
                </vs-td>
                <vs-td :data="data[indextr].numero_convenio">
                  <span class="">{{ data[indextr].numero_convenio }}</span>
                </vs-td>
                <vs-td :data="data[indextr].numero_titulo">
                  <span class="">{{ data[indextr].numero_titulo_texto }}</span>
                </vs-td>
                <vs-td :data="data[indextr].ubicacion_texto">{{
                  data[indextr].venta_terreno.ubicacion_texto
                }}</vs-td>
                <!--Convenio-->
                <vs-td :data="data[indextr].venta_terreno.status_convenio">
                  <p v-if="data[indextr].venta_terreno.status_convenio == 0">
                    <img
                      class="cursor-pointer img-btn-20"
                      src="@assets/images/convenio-no.svg"
                      title="Convenio no Entregado"
                      @click="verEntregarConvenio(data[indextr])"
                    />
                  </p>
                  <p v-else>
                    <img
                      class="cursor-pointer img-btn-20"
                      src="@assets/images/convenio-si.svg"
                      title="Convenio Entregado"
                      @click="verEntregarConvenio(data[indextr])"
                    />
                  </p>
                </vs-td>
                <vs-td :data="data[indextr].saldo">
                  $ {{ data[indextr].saldo | numFormat("0,000.00") }}
                </vs-td>
                <vs-td :data="data[indextr].operacion_status">
                  <p v-if="data[indextr].operacion_status == 0">
                    {{ data[indextr].status_texto }}
                    <span class="dot-danger"></span>
                  </p>
                  <p v-else-if="data[indextr].operacion_status == 1">
                    {{ data[indextr].status_texto }}
                    <span class="dot-warning"></span>
                  </p>
                  <p v-else-if="data[indextr].operacion_status == 2">
                    {{ data[indextr].status_texto }}
                    <span class="dot-success"></span>
                  </p>
                </vs-td>

                <vs-td :data="data[indextr].id">
                  <div class="flex justify-center">
                    <img
                      class="img-btn-24 mx-6"
                      src="@assets/images/seguimientos.svg"
                      title="Control de Seguimientos"
                      @click="OpenFormSeguimientos(tr)"
                    />
                    <img
                      v-if="data[indextr].sepultados.length > 0"
                      class="cursor-pointer img-btn-24 mr-6"
                      src="@assets/images/conserviciosfunerarios.svg"
                      title="Propiedad con servicios Funerarios"
                      @click="ConsultarListaFallecidos(data[indextr])"
                    />
                    <img
                      v-else
                      class="cursor-pointer img-btn-24 mr-6"
                      src="@assets/images/sinserviciosfunerarios.svg"
                      title="Propiedad sin servicios Funerarios"
                    />
                    <img
                      v-if="data[indextr].nota"
                      class="cursor-pointer img-btn-20 mr-6"
                      src="@assets/images/notepad_ver.svg"
                      title="Notas"
                      @click="
                        verNota(
                          data[indextr].nota.trim(),
                          data[indextr].venta_terreno.ubicacion_texto
                        )
                      "
                    />
                    <img
                      v-else
                      class="cursor-pointer img-btn-20 mr-6"
                      src="@assets/images/notepad_ver_no.svg"
                      title="Notas"
                    />
                    <img
                      class="cursor-pointer img-btn-20 mx-3"
                      src="@assets/images/folder.svg"
                      title="Expediente"
                      @click="ConsultarVenta(data[indextr].ventas_terrenos_id)"
                    />
                    <img
                      class="img-btn-18 mx-3"
                      src="@assets/images/edit.svg"
                      title="Modificar Contrato"
                      @click="openModificar(data[indextr].ventas_terrenos_id)"
                    />
                    <img
                      v-if="data[indextr].operacion_status >= 1"
                      class="img-btn-22 mx-3"
                      src="@assets/images/trash.svg"
                      title="Cancelar Contrato"
                      @click="cancelarVenta(data[indextr].ventas_terrenos_id)"
                    />
                    <img
                      v-else
                      class="img-btn-22 mx-3"
                      src="@assets/images/trash-open.svg"
                      title="Esta venta ya fue cancelada, puede hacer click aquí para consultar"
                      @click="
                        ConsultarVentaAcuse(data[indextr].ventas_terrenos_id)
                      "
                    />
                  </div>
                </vs-td>
                <template class="expand-user" slot="expand"></template>
              </vs-tr>
            </template>
          </vs-table>

          <div>
            <vs-pagination
              v-if="verPaginado"
              :total="this.total"
              v-model="actual"
              class="mt-8"
            ></vs-pagination>
          </div>
        </div>
      </div>
    </div>
    <FormularioVentas
      v-if="verFormularioVentas"
      :id_venta="id_venta_modificar"
      :tipo="tipoFormulario"
      :show="verFormularioVentas"
      @closeVentana="verFormularioVentas = false"
      @ver_pdfs_nueva_venta="ConsultarVenta"
    >
    </FormularioVentas>
    <Password
      v-if="openStatus"
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    >
    </Password>
    <ReportesVentas
      v-if="openReportes"
      :verAcuse="verAcuse"
      :show="openReportes"
      @closeListaReportes="closeListaReportes"
      :id_venta="id_venta"
    >
    </ReportesVentas>
    <FallecidosList
      v-if="openListaFallecidos"
      :show="openListaFallecidos"
      @closeListaFallecidos="closeListaFallecidos"
      :fallecidos="fallecidos"
    >
    </FallecidosList>
    <CancelarVenta
      v-if="openCancelar"
      :show="openCancelar"
      @closeCancelarVenta="openCancelar = false"
      @ConsultarVenta="ConsultarVenta"
      :id_venta="id_venta"
    ></CancelarVenta>
    <CuotasCementerio
      v-if="openCuotasCementerio"
      :show="openCuotasCementerio"
      @closeCuotasCementerio="openCuotasCementerio = false"
    >
    </CuotasCementerio>
    <PlanesVenta
      v-if="openPlanesVenta"
      :show="openPlanesVenta"
      @closePlanesCementerio="openPlanesVenta = false"
    >
    </PlanesVenta>
    <VerNotas
      v-if="openVerNotas"
      :show="openVerNotas"
      :nota="nota_contenido"
      :title="titulo_nota"
      @closeVerNotas="openVerNotas = false"
    >
    </VerNotas>
    <Entregarconvenio
      v-if="openEntregarconvenio"
      :show="openEntregarconvenio"
      :datos="datosConvenio"
      @closeEntregarConvenio="closeEntregarConvenio"
    >
    </Entregarconvenio>
    <FormularioSeguimientos
      v-if="openSeguimientos"
      :show="openSeguimientos"
      :filters="filtersSeguimientos"
      @closeVentana="openSeguimientos = false"
    >
    </FormularioSeguimientos>
  </div>
</template>

<script>
//planes de venta
import MapaCementerioReporte from "./MapaCementerioReporte.vue";
import VisorReportes from "../../VisorReportes.vue";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import { Spanish } from "flatpickr/dist/l10n/es.js";
import axios from "@/axios.js";
import axiosCancel from "axios";
import cementerio from "@services/cementerio";
import FormularioSeguimientos from "../../seguimientos/FormularioSeguimientos.vue";
import FormularioVentas from "../ventas/FormularioVentas";
import ReportesVentas from "../ventas/ReportesVentas";
import FallecidosList from "./FallecidosListVisor.vue";
import CancelarVenta from "../ventas/CancelarVenta";
//componente de password
import Password from "@pages/confirmar_password";
import VerNotas from "@pages/VerNotas";
import Entregarconvenio from "@pages/EntregarConvenio";
/**VARIABLES GLOBALES */
import PlanesVenta from "@pages/cementerio/ventas/PlanesVentas";
import CuotasCementerio from "@pages/cementerio/ventas/cuotas/CuotasCementerio";
export default {
  name: "ListadoVentasCementerioFiltros",
  components: {
    MapaCementerioReporte,
    VisorReportes,
    flatPickr,
    Password,
    FormularioVentas,
    ReportesVentas,
    CancelarVenta,
    PlanesVenta,
    CuotasCementerio,
    VerNotas,
    Entregarconvenio,
    FallecidosList,
    FormularioSeguimientos,
  },
  watch: {
    // Cambiar el padre elimina selecciones que pertenecían a otra área/fila.
    'filtros.tipoSeccion'() { this.filtros.area=''; this.filtros.fila=''; this.filtros.lote=''; },
    'filtros.area'() { this.filtros.fila=''; this.filtros.lote=''; },
    'filtros.fila'() { this.filtros.lote=''; },
    actual(pagina) { if (pagina !== this.paginaCargada) this.get_data(pagina); },
    filtros: { deep: true, handler() { this.programarConsulta(); } },
  },
  computed: {
    /** Columna contextual, sin consultar expedientes adicionales ni alterar acciones. */
    mostrarColumnaFallecidos() { return !!this.filtros.fallecido.trim() || this.filtros.sepultados === 'con'; },
    /** Tipos del catálogo real: permite incorporar nuevas secciones sin una lista fija. */
    tiposSeccion() {
      const tipos=new Map();
      this.areasCementerio.forEach(area=>{
        const id=String(area.tipo_propiedades_id);
        const tipo=area.tipo_propiedad || {};
        tipos.set(id,{value:id,label:tipo.tipo || area.nombre_area || 'Sección '+id});
      });
      return Array.from(tipos.values()).sort((a,b)=>a.label.localeCompare(b.label,'es'));
    },
    areasDisponibles() { return this.filtros.tipoSeccion ? this.areasCementerio.filter(a=>String(a.tipo_propiedades_id)===this.filtros.tipoSeccion) : this.areasCementerio; },
    areaSeleccionada() { return this.areasCementerio.find(a=>String(a.id)===this.filtros.area) || null; },
    esTerraza() { return !!this.areaSeleccionada && Number(this.areaSeleccionada.tipo_propiedades_id)===4; },
    etiquetaFila() { return !this.areaSeleccionada ? 'Fila / módulo' : this.esTerraza || Number(this.areaSeleccionada.tipo_propiedades_id)===3 ? 'Fila' : 'Módulo'; },
    /** El catálogo determina la cantidad; terrazas usan el alfabeto del sistema, incluida Ñ. */
    filasDisponibles() {
      if(!this.areaSeleccionada) return [];
      const alfabeto='ABCDEFGHIJKLMNÑOPQRSTUVWXYZ'.split('');
      return Array.from({length:Math.max(0,Number(this.areaSeleccionada.filas)||0)},(_,i)=>({value:String(i+1),label:this.etiquetaFila+' '+(this.esTerraza ? alfabeto[i] || String(i+1) : String(i+1))}));
    },
    /** Cada fila de terraza tiene su rango real de columnas; no inventar lotes fuera de él. */
    lotesDisponibles() {
      if(!this.esTerraza || !this.filtros.fila) return [];
      const limite=Number(this.areaSeleccionada.columnas)||0;
      const rangos=(this.areaSeleccionada.filas_columnas || []).filter(r=>Number(r.fila)===Number(this.filtros.fila));
      const lotes=new Set();
      rangos.forEach(r=>{ for(let i=Math.max(1,Number(r.empieza_columna)||1);i<=Math.min(limite,Number(r.fin_columna)||0);i++)lotes.add(i); });
      return Array.from(lotes).sort((a,b)=>a-b);
    },
    etiquetasFiltros() {
      const etiquetas=[];
      if(this.filtros.numero) etiquetas.push({key:'numero',label:({1:'Solicitud',2:'Convenio',3:'Título',4:'Venta'}[this.filtros.tipoNumero])+': '+this.filtros.numero});
      if(this.filtros.titular.trim()) etiquetas.push({key:'titular',label:'Titular: '+this.filtros.titular.trim()});
      if(this.filtros.fallecido.trim()) etiquetas.push({key:'fallecido',label:'Fallecido: '+this.filtros.fallecido.trim()});
      if(this.filtros.cuotasVencidas) etiquetas.push({key:'cuotasVencidas',label:this.filtros.cuotasVencidas==='con' ? 'Con cuotas de mantenimiento vencidas' : 'Sin cuotas de mantenimiento vencidas'});
      if(this.filtros.sepultados) etiquetas.push({key:'sepultados',label:this.filtros.sepultados==='con' ? 'Con personas sepultadas' : 'Sin personas sepultadas'});
      if(this.filtros.estado !== '') etiquetas.push({key:'estado',label:this.estadosOptions.find(o=>o.value===this.filtros.estado).label});
      if(this.filtros.tipoSeccion) { const tipo=this.tiposSeccion.find(t=>t.value===this.filtros.tipoSeccion); etiquetas.push({key:'tipoSeccion',label:tipo ? tipo.label : 'Sección seleccionada'}); }
      if(this.filtros.area) { const area=this.areasCementerio.find(a=>String(a.id)===this.filtros.area); etiquetas.push({key:'area',label:area ? area.nombre_area : 'Área seleccionada'}); }
      if(this.filtros.fila) { const fila=this.filasDisponibles.find(f=>f.value===this.filtros.fila); etiquetas.push({key:'fila',label:fila ? fila.label : this.etiquetaFila+' '+this.filtros.fila}); }
      if(this.filtros.lote) etiquetas.push({key:'lote',label:'Lote: '+this.filtros.lote});
      for(const key of ['desde','hasta']) if(this.filtros[key]) { const [y,m,d]=this.filtros[key].split('-').map(Number); etiquetas.push({key,label:(key==='desde' ? 'Venta desde: ' : 'Venta hasta: ')+new Date(y,m-1,d).toLocaleDateString('es-MX',{day:'numeric',month:'long',year:'numeric'})}); }
      return etiquetas;
    },
    noDataFound() {
      return this.ventas.length === 0;
    },
  },
  data() {
    return {
      filtros: {tipoNumero:'4',numero:'',titular:'',fallecido:'',estado:'',sepultados:'',cuotasVencidas:'',tipoSeccion:'',area:'',fila:'',lote:'',desde:'',hasta:''},
      mostrarMapaCementerio:false,
      mostrarReporte:false, filtrosReporte:{},
      areasCementerio:[], cargandoAreas:false, errorAreas:'', cancelacionAreas:null,
      calendarioVentas:{locale:Spanish,dateFormat:'Y-m-d',altInput:true,altFormat:'j \\d\\e F \\d\\e Y',altInputClass:'cementerio-filtros__fecha',disableMobile:true,allowInput:false},
       temporizadorListado:null, cancelacionListado:null,
      versionListado:0, paginaCargada:1, cargandoListado:false, errorListado:'', listadoCerrado:false,
      openSeguimientos: false,
      filtersSeguimientos: {
        cliente_id: null,
        tipo_cliente_id: null,
        operacion_id: null,
        origen: 1, //Formulario de Origen. 1-seguim
      },
      fallecidos: [],
      verAcuse: false,
      openPlanesVenta: false,
      openCuotasCementerio: false,
      openVerNotas: false,
      openEntregarconvenio: false,
      datosConvenio: {
        tipo: "terreno",
        id: "",
        titular: "",
        status: "",
        producto: "",
        fecha_entrega: "",
        entregado_por: "",
        nota: "",
      },
      nota_contenido: "",
      titulo_nota: "",
      openCancelar: false,
      openReportes: false,
      openListaFallecidos: false,
      verFormularioVentas: false,
      tipoFormulario: "",
      //variable
      tipo_propiedades: [],
      propiedad: { label: "Todos", value: "" },
      openReportesLista: false,
      estadosOptions: [
        {
          label: "Todas",
          value: "",
        },
        {
          label: "Por Pagar",
          value: "1",
        },
        { label: "Con saldo vencido", value: "4" },
        {
          label: "Pagadas",
          value: "2",
        },
        {
          label: "Activas",
          value: "3",
        },
        {
          label: "Canceladas",
          value: "0",
        },
      ],
      activeTab: 0,
      verPaginado: true,
      total: 0,
      actual: 1,
      ventas: [],
      //fin variables
      openStatus: false,
      callback: Function,
      accionNombre: "",
      verAgregar: false,
      verModificar: false,
      id_venta_modificar: 0,
      /**opciones para filtrar la peticion del server */
      id_venta: 0 /**para consultar los reportesw */,
    };
  },
  methods: {
    /** La selección se congela al abrir. Actualizar visor mantiene el mismo filtro. */
    abrirReporte() { this.filtrosReporte=JSON.parse(JSON.stringify(this.filtros));this.mostrarReporte=true; },
    async cargarReporte() {
      const f=this.filtrosReporte;
      const params={numero_control:f.numero,filtro_especifico_opcion:f.tipoNumero,titular:f.titular,fallecido:f.fallecido,status:f.estado,sepultados:f.sepultados,cuotas_vencidas:f.cuotasVencidas,tipo_seccion:f.tipoSeccion,area:f.area,fila:f.fila,lote:f.lote,desde:f.desde,hasta:f.hasta};
      return (await axios.get('/cementerio/reportes/listado-ventas',{params})).data;
    },
    async descargarExcel(token) { return (await axios.get('/cementerio/reportes/listado-ventas/'+token+'/excel',{responseType:'blob'})).data; },
    async descargarPdf(token) { return (await axios.get('/cementerio/reportes/listado-ventas/'+token+'/pdf',{responseType:'blob'})).data; },
    /** Reutiliza el catálogo real para mostrar nombres de áreas; no usa identificadores visibles. */
    async cargarAreas() {
      if(this.cancelacionAreas) this.cancelacionAreas.cancel();
      this.cancelacionAreas=axiosCancel.CancelToken.source();this.cargandoAreas=true;this.errorAreas='';
      try { const res=await axios.get('/cementerio/get_cementerio',{cancelToken:this.cancelacionAreas.token}); if(!this.listadoCerrado)this.areasCementerio=res.data; }
      catch(e) { if(!axiosCancel.isCancel(e) && !this.listadoCerrado)this.errorAreas='No se pudo cargar el catálogo de áreas.'; }
      finally { if(!this.listadoCerrado)this.cargandoAreas=false; }
    },
    OpenFormSeguimientos(venta) {
      this.filtersSeguimientos.cliente_id = venta.cliente_id;
      this.filtersSeguimientos.tipo_cliente_id = 1;
      this.filtersSeguimientos.operacion_id = venta.operacion_id;
      this.filtersSeguimientos.origen = 2; //clientes
      this.openSeguimientos = true;
    },
    verNota(nota, title) {
      this.openVerNotas = true;
      this.nota_contenido = nota;
      this.titulo_nota = title;
    },

    verEntregarConvenio(datos) {
      this.datosConvenio.id = datos.ventas_terrenos_id;
      this.datosConvenio.titular = datos.nombre;
      this.datosConvenio.nota = datos.nombre;
      this.datosConvenio.status = datos.venta_terreno.status_convenio;
      this.datosConvenio.producto = datos.venta_terreno.ubicacion_texto;
      this.datosConvenio.fecha_entrega =
        datos.venta_terreno.fecha_convenio_entrega_texto;
      this.datosConvenio.entregado_por =
        datos.venta_terreno.entrego_convenio != null
          ? datos.venta_terreno.entrego_convenio.nombre
          : "";
      this.datosConvenio.nota = datos.venta_terreno.nota_convenio;

      this.openEntregarconvenio = true;
    },

    /** Limpia solo el buscador alternativo. El watcher aplica una única consulta. */
    limpiarFiltros() {
      this.filtros={tipoNumero:'4',numero:'',titular:'',fallecido:'',estado:'',sepultados:'',cuotasVencidas:'',tipoSeccion:'',area:'',fila:'',lote:'',desde:'',hasta:''};
    },
    /** Escritura con espera de 300ms, cancelación propia y descarte de respuestas antiguas.
     * No utiliza cementerio.cancel, compartido por otros formularios y reportes. */
    programarConsulta() {
      clearTimeout(this.temporizadorListado);
      this.versionListado++;
      if(this.cancelacionListado) this.cancelacionListado.cancel('Filtros actualizados');
      this.temporizadorListado=setTimeout(()=>this.get_data(1),300);
    },
    /** Consulta independiente: referencias, estado, ubicación y fechas, paginada en SQL.
     * Omite fallecido vacío y conserva tabla/acciones. Los errores 422 se muestran en la card. */
    async get_data(page) {
      if(this.listadoCerrado) return;
      clearTimeout(this.temporizadorListado);
      const version=++this.versionListado;
      if(this.cancelacionListado) this.cancelacionListado.cancel('Consulta reemplazada');
      this.errorListado='';
      this.cargandoListado=false;
      const f=this.filtros;
      const numero=String(f.numero).trim();
      if(numero && !/^[1-9]\d*$/.test(numero)) { this.errorListado='Ingresa un número de referencia válido.';return; }
      if(f.desde && f.hasta && f.desde>f.hasta){this.errorListado='La fecha final debe ser igual o posterior a la inicial.';return;}
      const parametros={page,sepultados:f.sepultados,cuotas_vencidas:f.cuotasVencidas,tipo_seccion:f.tipoSeccion,desde:f.desde,hasta:f.hasta,area:f.area,fila:f.fila.trim(),lote:f.lote,status:f.estado,filtro_especifico_opcion:f.tipoNumero,numero_control:numero,titular:f.titular.trim(),light:true};
      if(f.fallecido.trim()) parametros.fallecido=f.fallecido.trim();
      
      this.cancelacionListado=axiosCancel.CancelToken.source();
      this.cargandoListado=true;
      try {
        const res=await axios.get('/cementerio/filtrar-ventas',{params:parametros,cancelToken:this.cancelacionListado.token});
        if(version!==this.versionListado || this.listadoCerrado) return;
        this.ventas=res.data.data || [];
        this.total=res.data.last_page || 1;
        this.paginaCargada=res.data.current_page || 1;
        this.actual=this.paginaCargada;
      } catch(error) {
        if(axiosCancel.isCancel(error) || version!==this.versionListado || this.listadoCerrado) return;
        this.errorListado=error.response && error.response.status===422 ? Object.values(error.response.data.errors || {}).flat().join(' ') : error.response && error.response.status===403 ? 'No tienes permiso para consultar estas ventas.' : 'No se pudo actualizar el listado. Modifica un filtro o limpia los filtros para reintentar.';
      } finally {
        if(version===this.versionListado && !this.listadoCerrado) this.cargandoListado=false;
      }
    },

    //eliminar usuario logicamente

    closeModificar() {
      this.verModificar = false;
    },

    closeStatus() {
      this.openStatus = false;
    },

    ConsultarVenta(id_venta) {
      this.id_venta = id_venta;
      this.openReportes = true;
    },
    ConsultarVentaAcuse(id_venta) {
      this.verAcuse = true;
      this.id_venta = id_venta;
      this.openReportes = true;
    },

    ConsultarListaFallecidos(fallecidos) {
      this.fallecidos = fallecidos;
      this.openListaFallecidos = true;
    },

    openModificar(id_venta) {
      this.tipoFormulario = "modificar";
      this.id_venta_modificar = id_venta;
      this.verFormularioVentas = true;
    },

    cancelarVenta(id_venta) {
      this.id_venta = id_venta;
      this.openCancelar = true;
    },
    formulario(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioVentas = true;
    },

    closeEntregarConvenio() {
      this.openEntregarconvenio = false;
      (async () => {
        await this.get_data(this.actual);
      })();
    },

    closeListaReportes() {
      this.openReportes = false;
      this.verAcuse = false;
      this.id_venta = 0;
      (async () => {
        await this.get_data(this.actual);
      })();
    },

    closeListaFallecidos() {
      this.openListaFallecidos = false;
      this.fallecidos = [];
    },

    closeCancelarVentaRefrescar() {
      this.openCancelar = false;
      (async () => {
        await this.get_data(this.actual);
      })();
    },
  },
  // Lifecycle hooks
  created() {
    this.cargarAreas();
    this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    (async () => {
      await this.get_data(this.actual);
    })();
  },
  mounted() {
    this.$log("Component mounted! " + this.$options.name);
  },
  beforeDestroy() {
    if(this.cancelacionAreas)this.cancelacionAreas.cancel();
    this.listadoCerrado=true; this.versionListado++;
    clearTimeout(this.temporizadorListado);
    if(this.cancelacionListado) this.cancelacionListado.cancel('Listado cerrado');
  },
  destroyed() {
    this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
  },
};
</script>

<style scoped>
/* Card independiente: conservar el diseño general y evitar reglas de otros listados. */
.cementerio-filtros-card >>> .vx-card__header{align-items:center;flex-wrap:wrap;gap:10px}
.cementerio-filtros-card >>> .vx-card__actions{display:flex;align-items:center;flex-wrap:wrap;gap:18px;min-width:0}
.cementerio-filtros{min-width:0;font-family:Arial,sans-serif}
.cementerio-filtros__fields{display:grid;grid-template-columns:1.1fr 1.5fr 1.5fr 1fr 1fr;gap:16px}
.cementerio-filtros__ubicacion{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:16px;margin-top:14px}
.cementerio-filtros__fields>div,.cementerio-filtros__ubicacion>div{min-width:0}
.cementerio-filtros label{display:block;margin-bottom:5px;color:#777;font-size:11px;line-height:16px;text-transform:uppercase}
.cementerio-filtros input,.cementerio-filtros select{display:block;width:100%;min-width:0;height:38px;box-sizing:border-box;padding:8px 10px;border:1px solid #c9c9c9;border-radius:0;background:#fff;color:#666;font:13px Arial,sans-serif}
.cementerio-filtros input::placeholder{color:#999;font-size:12px}
.cementerio-filtros select{padding-right:24px;text-overflow:ellipsis}
.cementerio-filtros select:disabled{background:#fafafa;color:#999;cursor:not-allowed}
.cementerio-filtros__referencia{display:flex;min-width:0}
.cementerio-filtros__referencia select{width:100px;flex-shrink:0;border-right:0;background:#fafafa}
.cementerio-filtros__referencia input{flex:1;min-width:0}
.cementerio-filtros__date-control{position:relative;min-width:0}
.cementerio-filtros__date-control >>> .cementerio-filtros__fecha{display:block;width:100%;min-width:0;height:38px;padding:8px 32px 8px 10px;border:1px solid #c9c9c9;border-radius:0;background:white;color:#666;box-sizing:border-box;font:13px Arial,sans-serif}
.cementerio-filtros__date-control >>> input[type="hidden"]{display:none!important}
.cementerio-filtros__date-clear{position:absolute;top:1px;right:1px;display:flex;align-items:center;justify-content:center;width:32px;height:36px;padding:0;border:0;background:transparent;color:#888;cursor:pointer}
.cementerio-filtros__date-clear >>> svg{width:14px;height:14px}
.cementerio-filtros__clear,.cementerio-filtros__reporte{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:28px;width:auto;margin:0;padding:3px 0;border:0;background:transparent;white-space:nowrap;font:12px Arial,sans-serif;cursor:pointer}
.cementerio-filtros__clear{color:#888}.cementerio-filtros__reporte{color:#96771e;font-weight:600}
.cementerio-filtros__clear >>> svg,.cementerio-filtros__reporte >>> svg{display:block;width:15px;height:15px}
.cementerio-filtros__reporte:hover:not(:disabled){color:#725815;text-decoration:underline;text-underline-offset:3px}
.cementerio-filtros__reporte:disabled{opacity:.5;cursor:wait}
.cementerio-filtros__chips{display:flex;flex-wrap:wrap;gap:7px;margin-top:14px;padding-top:12px;border-top:1px solid #eee}
.cementerio-filtros__chips button{display:inline-flex;align-items:center;gap:8px;max-width:100%;padding:5px 8px;border:0;background:#f0f0f0;color:#666;font:11px Arial,sans-serif;line-height:17px;text-align:left;overflow-wrap:anywhere;cursor:pointer}
.cementerio-filtros__chips >>> svg{width:12px;height:12px;flex-shrink:0}
.cementerio-filtros__error{margin:10px 0 0;padding:8px 10px;border-left:2px solid #b24a4a;background:#fcf6f6;color:#a74343;font-size:12px;line-height:1.5}
.cementerio-filtros__loading{margin:10px 0 0;color:#99802e;font-size:12px;line-height:1.5}
.cementerio-filtros button:focus-visible,.cementerio-filtros input:focus-visible,.cementerio-filtros select:focus-visible,.cementerio-filtros__clear:focus-visible,.cementerio-filtros__reporte:focus-visible{outline:2px solid #a58828;outline-offset:3px}
/* Las 12 entradas comparten cuadrícula al reducir el ancho. display:contents permite
   continuar con ubicación después de fechas sin reservar una celda vacía entre grupos. */
@media(max-width:1600px){
 .cementerio-filtros{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
 .cementerio-filtros__fields,.cementerio-filtros__ubicacion{display:contents}
 .cementerio-filtros__chips,.cementerio-filtros__error,.cementerio-filtros__loading{grid-column:1/-1;margin-top:0}
}
@media(max-width:1100px){.cementerio-filtros{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:850px){.cementerio-filtros{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:520px){
 .cementerio-filtros{grid-template-columns:1fr;gap:13px}
 .cementerio-filtros-card >>> .vx-card__header{align-items:flex-start}
 .cementerio-filtros-card >>> .vx-card__actions{width:100%;justify-content:space-between;gap:10px}
 .cementerio-filtros input,.cementerio-filtros select,.cementerio-filtros__date-control >>> .cementerio-filtros__fecha{font-size:16px}
}
.cementerio-listado__fallecidos{display:flex;flex-direction:column;gap:4px;min-width:140px;max-width:280px;line-height:1.4;white-space:normal;overflow-wrap:break-word}
.cementerio-listado__sin-fallecidos{color:#888;font-size:11px}
</style>
