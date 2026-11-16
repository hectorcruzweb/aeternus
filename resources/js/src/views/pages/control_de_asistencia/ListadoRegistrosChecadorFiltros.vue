<template>
    <div class="flex flex-col flex-1">
        <vx-card title="Filtros de selección" no-radius class="checador-filtros">
            <template slot="actions">
                <div class="checador-filtros__acciones">
                    <button type="button" @click="openReporte">
                        <feather-icon icon="PrinterIcon" aria-hidden="true" /> Imprimir registros
                    </button>
                    <button type="button" class="checador-filtros__limpiar" @click="limpiarFiltros">
                        <feather-icon icon="RefreshCwIcon" aria-hidden="true" /> Limpiar filtros
                    </button>
                </div>
            </template>
            <div class="checador-filtros__campos">
                <EmpleadoAutocompletar v-model="filtroEmpleado" id-campo="checador-registros-empleado" :area="area.value" />
                <div v-for="fecha in fechas" :key="fecha.campo">
                    <span :id="'checador-' + fecha.campo">{{ fecha.etiqueta }}</span>
                    <div class="checador-filtros__fecha">
                        <flat-pickr v-model="serverOptions[fecha.campo]" :config="calendario"
                            placeholder="Selecciona una fecha" @on-ready="(...args) => etiquetarFecha(fecha.campo, ...args)" />
                        <button v-if="serverOptions[fecha.campo]" type="button" :aria-label="'Limpiar ' + fecha.etiqueta"
                            @mousedown.prevent @click.stop="serverOptions[fecha.campo] = ''">
                            <feather-icon icon="XIcon" aria-hidden="true" />
                        </button>
                    </div>
                </div>
                <div>
                    <span id="checador-area-label">Área de servicio</span>
                    <details ref="menuArea" class="checador-filtros__area" @keydown.esc.stop="cerrarArea">
                        <summary aria-describedby="checador-area-label" :class="{ 'area-activa': area.value }">
                            <feather-icon icon="MapPinIcon" aria-hidden="true" />
                            <span>{{ area.label }}</span>
                            <feather-icon icon="ChevronDownIcon" aria-hidden="true" />
                        </summary>
                        <div class="checador-filtros__menu">
                            <label v-for="opcion in areasOptionsTodas" :key="opcion.value">
                                <input type="radio" name="checador-area" :value="opcion" v-model="area" @click="cerrarArea" />
                                {{ opcion.label }}
                            </label>
                        </div>
                    </details>
                </div>
            </div>
            <p v-if="errorFiltros" class="mt-2 text-danger" role="alert">{{ errorFiltros }}</p>
            <div class="checador-filtros__inferior">
                <div class="checador-filtros__acciones">
                    <button type="button" class="checador-filtros__tarjetas" @click="$emit('abrir-tarjetas')">
                        <feather-icon icon="ClockIcon" class="checador-filtros__reloj" aria-hidden="true" />
                        Imprimir tarjetas de asistencia
                    </button>
                    <vs-button color="success" icon-pack="feather" icon="icon-plus" @click="formulario('agregar')">
                        Crear registro
                    </vs-button>
                </div>
            </div>
        </vx-card>

        <div id="resultados" class="mt-5 flex flex-col flex-1">
            <div v-if="noDataFound" class="w-full skeleton flex-1 items-center justify-center">
                <span class="text-gray-600 text-lg font-normal">No hay datos que mostrar</span>
            </div>
            <div v-else id="results" class="w-full flex flex-wrap">
                <div class="w-full py-2">
                    <vs-table :sst="true" @search="handleSearch" @change-page="handleChangePage" @sort="handleSort"
                        :max-items="serverOptions.per_page.value" :data="registros" noDataText="0 Resultados"
                        class="tabla-datos">
                        <template slot="header">
                            <h3>Listado de Registros de Checador</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Fecha / Hora</vs-th>
                            <vs-th>Empleado</vs-th>
                            <vs-th>Área</vs-th>
                            <vs-th>Tipo de Registro</vs-th>
                            <vs-th>Forma de Registro</vs-th>
                            <vs-th>Status</vs-th>
                            <vs-th>Acciones</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                                <vs-td :data="data[indextr].id">
                                    <span :class="[
                                        'font-bold',
                                        data[indextr].status == 1 ? '' : 'text-danger',
                                    ]">{{ data[indextr].fecha_registro_texto }}
                                        {{ data[indextr].hora_registro }}</span>
                                </vs-td>
                                <vs-td :data="data[indextr].usuario">
                                    {{ data[indextr].usuario.nombre }}
                                </vs-td>
                                <vs-td :data="data[indextr].usuario.area_des">{{ data[indextr].usuario.area_des
                                    }}</vs-td>
                                <vs-td :data="data[indextr].tipo_registro_texto">
                                    <p v-if="data[indextr].tipo_registro_id == 1">
                                        Entrada <span class="dot-success"></span>
                                    </p>
                                    <p v-else>Salida <span class="dot-warning"></span></p>
                                </vs-td>
                                <vs-td :data="data[indextr].forma_registro">
                                    {{ data[indextr].forma_registro }}
                                </vs-td>

                                <vs-td :data="data[indextr].status">
                                    <p v-if="data[indextr].status == 1">
                                        Activo <span class="dot-success"></span>
                                    </p>
                                    <p v-else>Cancelado<span class="dot-danger"></span></p>
                                </vs-td>
                                <vs-td :data="data[indextr].id_user">
                                    <div class="flex justify-center">
                                        <img class="cursor-pointer img-btn-18 mx-2" src="@assets/images/edit.svg"
                                            title="Modificar" @click="openModificar(data[indextr].id)" />
                                        <img v-if="data[indextr].status == 1" class="img-btn-24 mx-2"
                                            src="@assets/images/switchon.svg" title="Deshabilitar"
                                            @click="CancelarRegistro(data[indextr])" />
                                        <img v-else class="img-btn-24 mx-2" src="@assets/images/switchoff.svg"
                                            title="Habilitar" @click="AltaRegistro(data[indextr])" />
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                    </vs-table>
                    <div>
                        <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual"
                            class="mt-8"></vs-pagination>
                    </div>
                    <pre ref="pre"></pre>
                </div>
            </div>
        </div>
        <Password v-if="openStatus" :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus"
            :accion="accionNombre">
        </Password>
        <VisorReportes v-if="filtrosReporte" :show="true" :cargar="cargarReporteRegistros"
            :descargar-pdf="descargarPdfRegistros" :descargar-archivo="descargarExcelRegistros"
            :enviar-correo="enviarReporteRegistros" @close="filtrosReporte = null" />
        <FormularioRegistros v-if="verFormularioRegistros" :id_registro="registro_id" :tipo="tipoFormulario"
            :show="verFormularioRegistros" @closeVentana="verFormularioRegistros = false" @retornar_id="retorno_id">
        </FormularioRegistros>
    </div>
</template>

<script>
import axios from "@/axios.js";
import VisorReportes from "../VisorReportes.vue";
import EmpleadoAutocompletar from "./EmpleadoAutocompletar.vue";
import Registros from "./registros.vue";
import { areasOptionsTodas, configdateTimePickerRange } from "@/VariablesGlobales";

export default {
    name: "ListadoRegistrosChecadorFiltros",
    extends: Registros,
    components: { EmpleadoAutocompletar, VisorReportes },
    data() {
        return {
            area: areasOptionsTodas[0],
            filtroEmpleado: { texto: "", id: null },
            fechas: [
                { campo: "fecha_inicio", etiqueta: "Desde" },
                { campo: "fecha_fin", etiqueta: "Hasta" },
            ],
            calendario: { ...configdateTimePickerRange, mode: "single", altFormat: "j \\d\\e F \\d\\e Y" },
            filtrosReporte: null,
            errorFiltros: "",
            temporizadorBusqueda: null,
            secuenciaBusqueda: 0,
            reiniciandoPagina: false,
        };
    },
    watch: {
        filtroEmpleado() { this.get_data(1); },
        "serverOptions.fecha_inicio": "programarBusqueda",
        "serverOptions.fecha_fin": "programarBusqueda",
    },
    mounted() {
        document.addEventListener("click", this.cerrarAreaFuera, true);
    },
    beforeDestroy() {
        document.removeEventListener("click", this.cerrarAreaFuera, true);
        clearTimeout(this.temporizadorBusqueda);
        this.secuenciaBusqueda++;
        this.$vs.loading.close();
    },
    methods: {
        // El componente original carga opciones para un selector de empleados.
        get_empleados() {},
        etiquetarFecha(campo, fechas, texto, calendario) {
            if (calendario.altInput) calendario.altInput.setAttribute("aria-labelledby", "checador-" + campo);
        },
        cerrarAreaFuera(evento) {
            const menu = this.$refs.menuArea;
            if (menu && menu.open && !menu.contains(evento.target)) this.cerrarArea();
        },
        cerrarArea() {
            this.$refs.menuArea.open = false;
        },
        programarBusqueda() {
            clearTimeout(this.temporizadorBusqueda);
            this.temporizadorBusqueda = setTimeout(() => this.get_data(1), 300);
        },
        periodoValido() {
            const { fecha_inicio: desde, fecha_fin: hasta } = this.serverOptions;
            this.errorFiltros = desde && hasta && desde > hasta ? "La fecha final debe ser igual o posterior a la inicial." : "";
            return !this.errorFiltros;
        },
        async get_data(pagina) {
            if (this.reiniciandoPagina) return;
            clearTimeout(this.temporizadorBusqueda);
            const secuencia = ++this.secuenciaBusqueda;
            if (!this.periodoValido() || (this.filtroEmpleado.texto.trim() && !this.filtroEmpleado.id)) {
                this.registros = [];
                this.verPaginado = false;
                this.$vs.loading.close();
                return;
            }
            const area = this.area.value || 0;
            this.$vs.loading();
            try {
                const { data } = await axios.get("/checador/get_registros_checador_frontend/all/" + (this.filtroEmpleado.id || "all") + "/paginated/" + area, {
                    params: {
                        page: pagina,
                        per_page: this.serverOptions.per_page,
                        fecha_inicio: this.serverOptions.fecha_inicio,
                        fecha_fin: this.serverOptions.fecha_fin,
                    },
                });
                if (secuencia !== this.secuenciaBusqueda) return;
                this.registros = data.data;
                this.total = data.last_page;
                this.reiniciandoPagina = true;
                this.actual = data.current_page;
                this.$nextTick(() => { this.reiniciandoPagina = false; });
                this.verPaginado = true;
            } catch (error) {
                if (secuencia !== this.secuenciaBusqueda) return;
                this.registros = [];
                this.verPaginado = false;
                this.errorFiltros = "No se pudo consultar el listado de registros.";
            } finally {
                if (secuencia === this.secuenciaBusqueda) this.$vs.loading.close();
            }
        },
        limpiarFiltros() {
            this.filtroEmpleado = { texto: "", id: null };
            this.serverOptions.fecha_inicio = "";
            this.serverOptions.fecha_fin = "";
            this.area = this.areasOptionsTodas[0];
            this.cerrarArea();
        },
        openReporte() {
            if (!this.periodoValido()) return;
            if (this.filtroEmpleado.texto.trim() && !this.filtroEmpleado.id) {
                this.errorFiltros = "Selecciona un empleado de la lista para imprimir los registros.";
                return;
            }
            const { fecha_inicio: desde, fecha_fin: hasta } = this.serverOptions;
            if (!desde && !hasta) {
                this.errorFiltros = "Selecciona al menos una fecha para imprimir los registros.";
                return;
            }
            this.filtrosReporte = {
                desde: desde || undefined,
                hasta: hasta || undefined,
                usuario_id: this.filtroEmpleado.id || undefined,
                area: this.area.value || 0,
            };
        },
        async cargarReporteRegistros() {
            return (await axios.get("/checador/registros/visor", { params: this.filtrosReporte })).data;
        },
        async descargarReporteRegistros(token, formato) {
            return (await axios.get("/checador/registros/visor/" + encodeURIComponent(token) + "/" + formato,
                { responseType: "blob" })).data;
        },
        descargarPdfRegistros(token) { return this.descargarReporteRegistros(token, "pdf"); },
        descargarExcelRegistros(token) { return this.descargarReporteRegistros(token, "excel"); },
        async enviarReporteRegistros(token, datos) {
            return (await axios.post("/checador/registros/visor/" + encodeURIComponent(token) + "/correo", datos)).data;
        },
    },
};
</script>

<style scoped>
.checador-filtros__acciones {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 8px 18px;
}
.checador-filtros__acciones button:not(.vs-button):not(.checador-filtros__tarjetas) {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #80641a;
    font-size: 12px;
    font-weight: 600;
    background: transparent;
    border: 0;
    cursor: pointer;
}
.checador-filtros__acciones button:not(.vs-button):not(.checador-filtros__tarjetas):hover { text-decoration: underline; }
.checador-filtros__acciones button:not(.vs-button):not(.checador-filtros__tarjetas):focus-visible { outline: 2px solid #a58828; outline-offset: 3px; }
.checador-filtros__acciones .checador-filtros__limpiar { color: #666; font-weight: 400; }
.checador-filtros__acciones >>> svg { display: block; width: 14px; height: 14px; }
.checador-filtros__campos {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) repeat(3, minmax(0, 1fr));
    gap: 12px 16px;
}
.checador-filtros__campos > div { min-width: 0; }
.checador-filtros__campos > label, .checador-filtros__campos > div > span { display: block; font-size:11px; line-height:1.5; color:#777; text-transform:uppercase; }
.checador-filtros__campos >>> .flatpickr-input:not([type="hidden"]),
.checador-filtros__campos >>> input.form-control {
    width: 100%;
    display:block; height:38px; box-sizing:border-box; color:#666; font:13px Arial,sans-serif;
    padding: 8px 10px;
    border: 1px solid #c9c9c9;
    border-radius: 0;
    background: #fff;
}
.checador-filtros__fecha >>> input:not([type="hidden"]) { padding-right:34px; }
@media (max-width: 1050px) { .checador-filtros__campos { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 560px) {
    .checador-filtros__campos { grid-template-columns: minmax(0, 1fr); }
    .checador-filtros >>> .vx-card__header { flex-wrap: wrap; gap: 10px; }
    .checador-filtros__acciones { justify-content: flex-start; }
}
 
.checador-filtros__inferior { display:flex; flex-wrap:wrap; align-items:center; justify-content:flex-end; gap:12px; margin-top:14px; }
.checador-filtros__area { position:relative; min-width:0; margin-top:5px; }
.checador-filtros__area summary { display:grid; grid-template-columns:16px 1fr 16px; align-items:center; gap:8px; padding:0 11px; height:38px; box-sizing:border-box; border:1px solid #c9c9c9; background:#fff; color:#666; font:13px Arial,sans-serif; text-align:center; cursor:pointer; list-style:none; }
.checador-filtros__area summary::-webkit-details-marker { display:none; }
.checador-filtros__area[open] summary, .checador-filtros__area .area-activa { border-color:#ab891d; color:#80641a; background:#faf8f0; }
.checador-filtros__menu { position:absolute; z-index:10; top:calc(100% + 5px); width:100%; background:white; border:1px solid #d2d2d2; box-shadow:0 4px 12px #0002; padding:6px; }
.checador-filtros__menu label { display:flex; gap:8px; align-items:center; padding:8px; cursor:pointer; }
.checador-filtros__menu input { accent-color:#ab891d; }
.checador-filtros__area >>> svg, .checador-filtros__fecha >>> svg { display:block; width:14px; height:14px; }
.checador-filtros__fecha { position:relative; margin-top:5px; }
.checador-filtros__fecha button { position:absolute; right:3px; top:3px; width:30px; height:32px; display:flex; align-items:center; justify-content:center; padding:0; border:0; border-radius:0; background:transparent; color:#909090; cursor:pointer; }
.checador-filtros__fecha button:hover { color:#aa4545; background:#f5f5f5; }
.checador-filtros__fecha button:focus-visible { outline:2px solid #a58828; outline-offset:-2px; }
.checador-filtros__fecha >>> input:not([type="hidden"]):focus,
.checador-filtros__area summary:focus {
    outline:none;
    border-color:#ab891d;
}
.checador-filtros__menu input:focus-visible {
    outline:2px solid #ab891d;
    outline-offset:2px;
}
.checador-filtros__acciones .checador-filtros__tarjetas {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    font-weight:600;
    cursor:pointer;
    min-height:38px;
    gap:9px;
    padding:6px 12px;
    border:1px solid #80641a;
    background:#80641a;
    color:#fff;
}
.checador-filtros__acciones .checador-filtros__tarjetas:hover {
    background:#6e5516;
    color:#fff;
    text-decoration:none;
}
.checador-filtros__tarjetas:focus-visible { outline:2px solid #a58828; outline-offset:3px; }
.checador-filtros__reloj >>> svg { width:19px; height:19px; }

</style>
