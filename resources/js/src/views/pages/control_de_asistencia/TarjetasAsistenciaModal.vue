<template>
    <div>
        <vs-popup ref="TarjetasAsistenciaModal" class="forms-popup" :fullscreen="true" title="Tarjetas de asistencia"
            :active="true" @update:active="alCambiarVisibilidad" @click.native.capture="bloquearExterior">
            <vx-card title="Filtros de selección" no-radius class="tarjetas-card">
                <template slot="actions">
                    <div class="tarjetas-acciones">
                        <button type="button" class="tarjetas-lote"
                            :disabled="!fechaInicio || cargando || !!error || !empleados.length || !!(filtroEmpleado.texto.trim() && !filtroEmpleado.id)"
                            @click="abrirTarjetasFiltradas">
                            <feather-icon icon="PrinterIcon" aria-hidden="true" /> Imprimir tarjetas del listado
                        </button>
                        <button type="button" class="tarjetas-limpiar" @click="limpiarFiltros">
                            <feather-icon icon="RefreshCwIcon" aria-hidden="true" /> Limpiar filtros
                        </button>
                    </div>
                </template>
                <div class="tarjetas-filtros">
                    <EmpleadoAutocompletar ref="buscar" v-model="filtroEmpleado" id-campo="checador-tarjetas-empleado" :area="area" :estado="estado" />
                    <label>
                        Periodo de asistencia
                        <div class="tarjetas-fecha">
                            <flat-pickr v-model="periodo" :config="calendario" placeholder="Selecciona el rango"
                                @on-change="seleccionarPeriodo" />
                            <button v-if="periodo" type="button" aria-label="Limpiar periodo"
                                @mousedown.prevent @click.stop="limpiarPeriodo">
                                <feather-icon icon="XIcon" aria-hidden="true" />
                            </button>
                        </div>
                    </label>
                    <div v-for="grupo in grupos" :key="grupo.campo">
                        <span>{{ grupo.etiqueta }}</span>
                        <details ref="menusFiltros" class="tarjetas-grupo" @keydown.esc.stop="cerrarMenu">
                            <summary :class="{ 'tarjetas-activo': $data[grupo.campo] }">
                                <feather-icon :icon="grupo.icono" aria-hidden="true" />
                                <span>{{ grupo.opciones.find(opcion => opcion.value === $data[grupo.campo]).label }}</span>
                                <feather-icon icon="ChevronDownIcon" aria-hidden="true" />
                            </summary>
                            <div class="tarjetas-menu">
                                <label v-for="opcion in grupo.opciones" :key="opcion.value">
                                    <input type="radio" :name="'tarjetas-' + grupo.campo" :value="opcion.value"
                                        :checked="$data[grupo.campo] === opcion.value"
                                        @click="seleccionarFiltro(grupo.campo, opcion.value, $event)">
                                    {{ opcion.label }}
                                </label>
                            </div>
                        </details>
                    </div>
                </div>
            </vx-card>

            <p v-if="error" class="mt-4 text-danger" role="alert">{{ error }}</p>
            <p v-else-if="cargando" class="mt-4" role="status">Cargando empleados…</p>
            <p v-else-if="!empleados.length" class="mt-4" role="status">No se encontraron empleados.</p>
            <vs-table v-else :data="empleados" class="tabla-datos mt-4 tarjetas-tabla">
                <template slot="thead">
                    <vs-th>Empleado</vs-th>
                    <vs-th>Área</vs-th>
                    <vs-th>Estado</vs-th>
                    <vs-th>Acciones</vs-th>
                </template>
                <template slot-scope="{ data }">
                    <vs-tr v-for="empleado in data" :key="empleado.id">
                        <vs-td>{{ empleado.nombre }}</vs-td>
                        <vs-td>{{ empleado.area_des }}</vs-td>
                        <vs-td>{{ Number(empleado.status) === 1 ? "Activo" : "Cancelado" }}</vs-td>
                        <vs-td>
                            <button type="button" class="tarjetas-accion" :disabled="!fechaInicio || Number(empleado.status) === 0"
                                @click="abrirTarjeta(empleado)">Ver tarjeta</button>
                            <button type="button" class="tarjetas-accion" @click="empleadoDescanso = empleado.id">
                                Días de descanso
                            </button>
                        </vs-td>
                    </vs-tr>
                </template>
            </vs-table>
            <vs-pagination v-if="paginas > 1" class="mt-4" :total="paginas" :value="pagina" @input="buscarEmpleados" />
        </vs-popup>

        <FormularioDiasDescanso v-if="empleadoDescanso !== null" :show="true" :usuario_id="empleadoDescanso"
            @CloseFormularioDiasDescanso="empleadoDescanso = null" />
        <VisorReportes v-if="consulta" :show="true" :cargar="cargarTarjeta"
            :descargar-pdf="descargarTarjeta" :enviar-correo="enviarTarjeta" @close="consulta = null" />
    </div>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import axios from "@/axios.js";
import EmpleadoAutocompletar from "./EmpleadoAutocompletar.vue";
import { areasOptionsTodas, configdateTimePickerRange } from "@/VariablesGlobales";
import FormularioDiasDescanso from "./tarjetas/FormularioDiasDescanso.vue";
import VisorReportes from "../VisorReportes.vue";

export default {
    name: "TarjetasAsistenciaModal",
    components: { EmpleadoAutocompletar, flatPickr, FormularioDiasDescanso, VisorReportes },
    data() {
        return {
            area: areasOptionsTodas[0].value, estado: "1", filtroEmpleado: { texto: "", id: null },
            grupos: [
                { campo: "area", etiqueta: "Área de servicio", icono: "MapPinIcon", opciones: areasOptionsTodas },
                { campo: "estado", etiqueta: "Estado", icono: "FilterIcon", opciones: [
                    { value: "1", label: "Activos" },
                    { value: "", label: "Todos" },
                    { value: "0", label: "Cancelados" },
                ] },
            ],
            calendario: { ...configdateTimePickerRange, altFormat: "j \\d\\e F \\d\\e Y" }, periodo: "",
            fechaInicio: "", fechaFin: "", empleados: [], pagina: 1, paginas: 1,
            cargando: false, error: "", secuencia: 0,
            empleadoDescanso: null, consulta: null,
        };
    },
    watch: { filtroEmpleado() { this.buscarEmpleados(1); } },
    mounted() {
        document.addEventListener("click", this.cerrarMenusFuera, true);
        this.$popupManager.register(this, this.cerrar, "buscar");
        this.buscarEmpleados(1);
    },
    beforeDestroy() {
        document.removeEventListener("click", this.cerrarMenusFuera, true);
        this.secuencia++;
        this.$popupManager.unregister(this.$options.name);
    },
    methods: {
        cerrarMenusFuera(evento) {
            for (const menu of this.$refs.menusFiltros || []) {
                if (menu.open && !menu.contains(evento.target)) menu.open = false;
            }
        },
        cerrarMenu(evento) {
            const menu = evento.currentTarget.closest("details");
            menu.open = false;
            menu.querySelector("summary").focus();
        },
        seleccionarFiltro(campo, valor, evento) {
            this[campo] = valor;
            this.cerrarMenu(evento);
            this.buscarEmpleados(1);
        },
        limpiarPeriodo() {
            this.periodo = "";
            this.fechaInicio = "";
            this.fechaFin = "";
        },
        limpiarFiltros() {
            this.filtroEmpleado = { texto: "", id: null };
            this.area = areasOptionsTodas[0].value;
            this.estado = "1";
            this.limpiarPeriodo();
        },

        async buscarEmpleados(pagina) {
            const secuencia = ++this.secuencia;
            if (this.filtroEmpleado.texto.trim() && !this.filtroEmpleado.id) {
                this.empleados = [];
                this.paginas = 1;
                this.cargando = false;
                this.error = "";
                return;
            }
            this.cargando = true;
            this.error = "";
            try {
                const { data } = await axios.get("/checador/get_empleados_paginados", {
                    params: { page: pagina, per_page: 15, usuario_id: this.filtroEmpleado.id || undefined,
                        area: this.area, filtro_especifico_opcion: this.estado || undefined },
                });
                if (secuencia !== this.secuencia) return;
                this.empleados = data.data;
                this.pagina = data.current_page;
                this.paginas = data.last_page;
            } catch (error) {
                if (secuencia === this.secuencia) this.error = "No se pudo consultar el listado de empleados.";
            } finally {
                if (secuencia === this.secuencia) this.cargando = false;
            }
        },
        seleccionarPeriodo(fechas, texto, calendario) {
            this.fechaInicio = fechas.length ? calendario.formatDate(fechas[0], "Y-m-d") : "";
            this.fechaFin = fechas.length === 2 ? calendario.formatDate(fechas[1], "Y-m-d") : this.fechaInicio;
        },
        abrirTarjeta(empleado) {
            this.consulta = { usuario_id: empleado.id, fecha_inicio: this.fechaInicio, fecha_fin: this.fechaFin };
        },
        abrirTarjetasFiltradas() {
            this.consulta = {
                lote: 1,
                fecha_inicio: this.fechaInicio,
                fecha_fin: this.fechaFin,
                usuario_id: this.filtroEmpleado.id || undefined,
                area: this.area || undefined,
                estado: this.estado || undefined,
            };
        },
        async cargarTarjeta() {
            return (await axios.get("/checador/tarjetas/visor", { params: this.consulta })).data;
        },
        async descargarTarjeta(token) {
            return (await axios.get("/checador/tarjetas/visor/" + encodeURIComponent(token) + "/pdf",
                { responseType: "blob" })).data;
        },
        async enviarTarjeta(token, datos) {
            return (await axios.post("/checador/tarjetas/visor/" + encodeURIComponent(token) + "/correo", datos)).data;
        },
        bloquearExterior(evento) {
            if (evento.target.classList && evento.target.classList.contains("vs-popup--background")) {
                evento.preventDefault();
                evento.stopImmediatePropagation();
            }
        },
        alCambiarVisibilidad(activo) {
            if (!activo) this.cerrar();
        },
        cerrar() {
            if (!this.consulta && this.empleadoDescanso === null) this.$emit("close");
        },
    },
};
</script>

<style scoped>
.tarjetas-acciones { display:flex; align-items:center; justify-content:flex-end; flex-wrap:wrap; gap:8px 12px; }
.tarjetas-lote {
    display:inline-flex; align-items:center; justify-content:center; gap:6px;
    padding:8px 12px; border:1px solid #806318; background:#806318;
    color:#fff; font-size:12px; font-weight:600; cursor:pointer;
}
.tarjetas-lote:not(:disabled):hover { background:#694f10; }
.tarjetas-lote:focus-visible { outline:2px solid #806318; outline-offset:2px; }
.tarjetas-lote:disabled { opacity:.55; cursor:default; }
.tarjetas-card { position:relative; z-index:2; }
.tarjetas-tabla { position:relative; z-index:1; }
.tarjetas-filtros {
    display:grid;
    grid-template-columns:minmax(0, 1.6fr) repeat(3, minmax(0, 1fr));
    gap:12px 16px;
}
.tarjetas-filtros > label, .tarjetas-filtros > div > span {
    display:block;
    min-width:0;
    font-size:11px;
    line-height:1.5;
    color:#777;
    text-transform:uppercase;
}
.tarjetas-fecha >>> input:not([type="hidden"]), .tarjetas-grupo summary {
    width:100%;
    height:38px;
    box-sizing:border-box;
    padding:8px 10px;
    border:1px solid #c9c9c9;
    border-radius:0;
    background:#fff;
    color:#666;
    font:13px Arial,sans-serif;
}
.tarjetas-fecha, .tarjetas-grupo { margin-top:5px; }
.tarjetas-fecha, .tarjetas-grupo { position:relative; }
.tarjetas-fecha >>> input:not([type="hidden"]) { padding-right:34px; }
.tarjetas-fecha button {
    position:absolute;
    inset:3px 3px auto auto;
    width:30px;
    height:32px;
    display:grid;
    place-items:center;
    padding:0;
    border:0;
    background:transparent;
    color:#909090;
    cursor:pointer;
}
.tarjetas-fecha button:hover { color:#aa4545; background:#f5f5f5; }
.tarjetas-fecha >>> input:focus, .tarjetas-grupo summary:focus {
    outline:none;
    border-color:#ab891d;
}
.tarjetas-grupo summary {
    display:grid;
    grid-template-columns:16px minmax(0, 1fr) 16px;
    align-items:center;
    gap:8px;
    text-align:center;
    list-style:none;
    cursor:pointer;
}
.tarjetas-grupo summary::-webkit-details-marker { display:none; }
.tarjetas-grupo[open] summary, .tarjetas-grupo .tarjetas-activo {
    border-color:#ab891d;
    color:#80641a;
    background:#faf8f0;
}
.tarjetas-menu {
    position:absolute;
    z-index:10;
    top:calc(100% + 5px);
    width:100%;
    padding:6px;
    background:#fff;
    border:1px solid #d2d2d2;
    box-shadow:0 4px 12px #0002;
}
.tarjetas-menu label, .tarjetas-limpiar { display:flex; align-items:center; gap:8px; cursor:pointer; }
.tarjetas-menu label { padding:8px; }
.tarjetas-menu input { accent-color:#ab891d; }
.tarjetas-limpiar { border:0; background:transparent; color:#666; font-size:12px; }
.tarjetas-filtros >>> svg, .tarjetas-limpiar >>> svg { width:14px; height:14px; display:block; }
@media (max-width:1050px) { .tarjetas-filtros { grid-template-columns:repeat(2, minmax(0, 1fr)); } }
@media (max-width:560px) { .tarjetas-filtros { grid-template-columns:minmax(0, 1fr); } }
.tarjetas-accion {
    margin: 3px 5px;
    padding: 5px 8px;
    color: #80641a;
    border: 1px solid #d5c6a3;
    background: #fff;
    cursor: pointer;
}
.tarjetas-accion:disabled { opacity: .5; cursor: default; }
</style>
