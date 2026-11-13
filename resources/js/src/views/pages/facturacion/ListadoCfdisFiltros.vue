<template>
    <div>
        <div class="text-right buttons-container-header">
            <vs-button class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0" color="success"
                @click="TipoFormulario('facturar')">
                <span>Crear Factura</span>
            </vs-button>
        </div>
        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
            <vx-card class="cfdi-card" no-radius title="Filtros de selección" :collapse-action="false">
                <template slot="actions">
                    <button type="button" class="cfdi-limpiar cfdi-reporte" :disabled="cargando || !!errorListado"
                        title="Reporte según el tipo de CFDI seleccionado" @click="abrirReporteListado">
                        <feather-icon icon="FileTextIcon" /> Reporte del listado <feather-icon icon="ArrowUpRightIcon" />
                    </button>
                    <button type="button" class="cfdi-limpiar" @click="reset">
                        <feather-icon icon="RefreshCwIcon" /> Limpiar filtros
                    </button>
                </template>
                <div class="cfdi-campos">
                    <label v-for="campo in camposTexto" :key="campo.id">
                        {{ campo.label }}
                        <input v-model.trim="filtros[campo.id]" :maxlength="campo.max"
                            :inputmode="campo.modo" :placeholder="campo.placeholder || campo.label" />
                    </label>
                    <label v-for="fecha in fechas" :key="fecha.id">
                        {{ fecha.label }}
                        <div class="cfdi-fecha">
                            <flat-pickr v-model="filtros[fecha.id]" :config="calendario" placeholder="Selecciona una fecha" />
                            <button v-if="filtros[fecha.id]" type="button" :aria-label="'Limpiar ' + fecha.label"
                                @mousedown.prevent @click.stop="filtros[fecha.id] = ''">
                                <feather-icon icon="XIcon" />
                            </button>
                        </div>
                    </label>
                </div>
                <div class="cfdi-grupos" ref="gruposFiltros" @keydown.esc="cerrarMenus">
                    <details v-for="grupo in gruposFiltros" :key="grupo.campo" class="cfdi-grupo">
                        <summary :class="{ 'cfdi-activo': grupo.cantidad }">
                            <feather-icon :icon="grupo.icono" />
                            <span>{{ grupo.titulo }}</span>
                            <span v-if="grupo.cantidad" class="cfdi-cantidad">{{ grupo.cantidad }}</span>
                            <feather-icon icon="ChevronDownIcon" />
                        </summary>
                        <div class="cfdi-menu">
                            <label v-for="opcion in grupo.opciones" :key="opcion.id">
                                <input v-if="grupo.multiple" type="checkbox" :value="opcion.id" v-model="filtros[grupo.campo]" />
                                <input v-else type="radio" :name="'cfdi-' + grupo.campo" :value="opcion.id"
                                    v-model="filtros[grupo.campo]" @change="cerrarMenus()" />
                                {{ opcion.tipo }}
                            </label>
                            <p v-if="!grupo.opciones.length">No hay opciones disponibles.</p>
                        </div>
                    </details>
                </div>
                <div v-if="filtrosVisibles.length" class="cfdi-chips">
                    <button v-for="filtro in filtrosVisibles" :key="filtro.campo + ':' + filtro.valor" type="button"
                        :aria-label="'Quitar filtro: ' + filtro.texto" @click="quitarFiltro(filtro)">
                        {{ filtro.texto }} <feather-icon icon="XIcon" />
                    </button>
                </div>
            </vx-card>
        </div>
        <div id="resultados" class="mt-2 flex flex-col flex-1">
            <div v-if="cargando || errorListado || !cfdis.length" class="w-full skeleton flex-1 items-center justify-center">
                <span class="text-gray-600 text-lg font-normal" role="status">{{ cargando ? "Consultando CFDI…" : (errorListado || "No hay datos que mostrar") }}</span>
            </div>
            <div v-else id="results" class="w-full flex flex-wrap">
                <div class="w-full py-2">
                    <div class="">
                        <vs-table :sst="true" :max-items="15" :data="cfdis" stripe
                            noDataText="0 Resultados" class="tabla-datos">
                            <template slot="header">
                                <h3>
                                    Listado de CFDI
                                </h3>
                            </template>
                            <template slot="thead">
                                <vs-th>Folio</vs-th>
                                <vs-th>UUID</vs-th>
                                <vs-th>Fecha</vs-th>
                                <vs-th>Cliente</vs-th>
                                <vs-th>RFC</vs-th>
                                <vs-th>Tipo</vs-th>
                                <vs-th>M. de Pago</vs-th>
                                <vs-th>Estatus</vs-th>
                                <vs-th>$ Monto</vs-th>
                                <vs-th>$ Saldo</vs-th>
                                <vs-th>Acciones</vs-th>
                            </template>
                            <template slot-scope="{ data }">
                                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data" :class="[
                                    tr.status == 0 ? 'text-danger' : '',
                                ]">
                                    <vs-td :data="data[indextr].id">
                                        <span class="font-semibold">{{
                                            data[indextr].id
                                        }}</span>
                                    </vs-td>
                                    <vs-td :data="data[indextr].uuid">
                                        {{ data[indextr].uuid }}
                                        <img title="De click para seleccionar factura a sustituir."
                                            v-if="data[indextr].status == 1" width="16" class="cursor-pointer"
                                            src="@assets/images/copytoclipboard.svg" v-clipboard:copy="data[indextr].uuid
                                                " v-clipboard:success="onCopy" v-clipboard:error="onError" />
                                    </vs-td>
                                    <vs-td :data="data[indextr].fecha_timbrado_texto
                                        ">{{
                                            data[indextr].fecha_timbrado_texto
                                        }}</vs-td>
                                    <vs-td :data="data[indextr].cliente_nombre">{{
                                        data[indextr].cliente_nombre
                                        }}</vs-td>
                                    <vs-td :data="data[indextr].rfc_receptor">{{
                                        data[indextr].rfc_receptor
                                    }}</vs-td>
                                    <vs-td :data="data[indextr].tipo_comprobante_texto
                                        ">{{
                                            data[indextr].tipo_comprobante_texto
                                        }}</vs-td>
                                    <vs-td :data="data[indextr].sat_metodos_pago_texto
                                        ">{{
                                            data[indextr].sat_metodos_pago_texto
                                        }}</vs-td>
                                    <vs-td :data="data[indextr].status_texto">{{ data[indextr].status_texto }}
                                    </vs-td>
                                    <vs-td :data="data[indextr].total">
                                        {{
                                            data[indextr].total
                                            | numFormat("0,000.00")
                                        }}</vs-td>
                                    <vs-td :data="data[indextr].saldo_cfdi">
                                        {{
                                            data[indextr].saldo_cfdi
                                            | numFormat("0,000.00")
                                        }}</vs-td>
                                    <vs-td :data="data[indextr].id">
                                        <img v-if="data[indextr].nota" class="cursor-pointer img-btn-20 mr-6"
                                            src="@assets/images/notepad_ver.svg" title="Notas" @click="
                                                verNota(
                                                    data[indextr].nota.trim(),
                                                    data[indextr].cliente_nombre
                                                )
                                                " />
                                        <img v-else class="cursor-pointer img-btn-20 mr-6"
                                            src="@assets/images/notepad_ver_no.svg" title="Notas" />

                                        <button type="button" class="cfdi-operaciones" :class="{ 'cfdi-operaciones--vinculadas': tr.operaciones_count }"
                                            :title="tituloOperaciones(tr)" :aria-label="tituloOperaciones(tr)"
                                            @click="folioOperaciones = tr.id">
                                            <feather-icon icon="Link2Icon" />
                                            <span>{{ tr.operaciones_count }}</span>
                                        </button>

                                        <img width="25" class="cursor-pointer" src="@assets/images/cfdicog.svg" @click="
                                            openActions(data[indextr].id)
                                            " />
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </vs-table>
                        <div>
                            <vs-pagination v-if="total > 1" :total="total" :value="actual" @input="cambiarPagina"
                                class="mt-3"></vs-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <VisorReportes v-if="filtrosReporte" :show="true"
            :cargar="cargarReporteListado" :descargar-pdf="descargarReporteListado" :descargar-archivo="descargarExcelListado"
            @close="filtrosReporte = null" />
        <VisorReportes v-if="folioOperaciones !== null" :show="true"
            :cargar="cargarOperacionesCfdi" :descargar-pdf="descargarOperacionesCfdi"
            @close="folioOperaciones = null" />

        <VerNotas v-if="openVerNotas" :show="openVerNotas" :nota="nota_contenido" :title="titulo_nota"
            @closeVerNotas="openVerNotas = false">
        </VerNotas>
        <!--fin de buscador-->
        <FormularioCFDI v-if="verFormularioCFDI" :id_cfdi="id_cfdi" :tipo="TipodeFormulario" :show="verFormularioCFDI"
            @closeVentana="closeVentana" @openActions="openActions"></FormularioCFDI>

        <ActionsForm v-if="verConsultarCfdi" :id_cfdi="id_cfdi" :tipo="TipodeFormulario" :show="verConsultarCfdi"
            :uuid_a_sustituir="uuid_a_sustituir" @closeVentana="closeVentanaActions"
            @reset_uuid_a_sustituir="reset_uuid_a_sustituir"></ActionsForm>

    </div>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
//services para la facturacion
import facturacion from "@services/facturacion";
import VerNotas from "@pages/VerNotas";
import FormularioCFDI from "../facturacion/FormularioCFDI";
import ActionsForm from "../facturacion/ActionsForm";
import VisorReportes from "@pages/VisorReportes.vue";
/**VARIABLES GLOBALES */
import { Spanish } from "flatpickr/dist/l10n/es.js";
import axios from "@/axios.js";
function filtrosIniciales() {
    return {
        folio: "", uuid: "", cliente: "", rfc: "", numero_servicio: "",
        tipo_cfdi: "", status: "", metodo_pago: "", saldo_pendiente: "", operaciones_relacionadas: "", desde: "", hasta: "", tipos_servicio: [],
    };
}

export default {
    name: "ListadoCfdisFiltros",
    components: {
        FormularioCFDI,
        flatPickr,
        ActionsForm,
        VisorReportes,
        VerNotas,
    },
    computed: {
        gruposFiltros() {
            const todos = { id: "", tipo: "Todos" };
            return [
                { campo: "tipo_cfdi", titulo: "Tipo de CFDI", icono: "FileTextIcon",
                    opciones: [todos, ...this.tiposComprobante], cantidad: this.filtros.tipo_cfdi !== "" ? 1 : 0 },
                { campo: "metodo_pago", titulo: "Método de pago", icono: "CreditCardIcon",
                    opciones: [
                        todos,
                        { id: "PUE", tipo: "PUE · Pago en una sola exhibición" },
                        { id: "PPD", tipo: "PPD · Pago en parcialidades o diferido" },
                    ],
                    cantidad: this.filtros.metodo_pago !== "" ? 1 : 0 },
                { campo: "status", titulo: "Estado", icono: "FilterIcon",
                    opciones: [todos, { id: "1", tipo: "Vigente" }, { id: "3", tipo: "En proceso de cancelación" }, { id: "0", tipo: "Cancelado" }],
                    cantidad: this.filtros.status !== "" ? 1 : 0 },
                { campo: "saldo_pendiente", titulo: "Saldo", icono: "DollarSignIcon",
                    opciones: [todos, { id: "1", tipo: "Con saldo pendiente" }],
                    cantidad: this.filtros.saldo_pendiente !== "" ? 1 : 0 },
                { campo: "operaciones_relacionadas", titulo: "Operaciones relacionadas", icono: "Link2Icon",
                    opciones: [todos, { id: "con", tipo: "Con operaciones relacionadas" },
                        { id: "sin", tipo: "Sin operaciones relacionadas" }],
                    cantidad: this.filtros.operaciones_relacionadas !== "" ? 1 : 0 },
                { campo: "tipos_servicio", titulo: "Tipos de operación", icono: "LayersIcon",
                    opciones: this.tiposServicio, multiple: true, cantidad: this.filtros.tipos_servicio.length },
            ];
        },
        filtrosVisibles() {
            const campos = [...this.camposTexto, ...this.fechas];
            const filtros = campos.filter(campo => this.filtros[campo.id]).map(campo => {
                const valor = this.filtros[campo.id];
                const texto = campo.id === "desde" || campo.id === "hasta"
                    ? new Date(valor + "T00:00:00").toLocaleDateString("es-MX", { day: "numeric", month: "long", year: "numeric" })
                    : valor;
                return { campo: campo.id, valor, texto: campo.label + ": " + texto };
            });
            this.gruposFiltros.forEach(grupo => {
                const valores = grupo.multiple ? this.filtros[grupo.campo] : [this.filtros[grupo.campo]];
                grupo.opciones.filter(opcion => opcion.id !== "" && valores.includes(opcion.id)).forEach(opcion => {
                    filtros.push({ campo: grupo.campo, valor: opcion.id, texto: opcion.tipo });
                });
            });
            return filtros;
        },
    },

    watch: {
        filtros: {
            deep: true,
            handler() {
                clearTimeout(this.espera);
                this.consulta++;
                this.cargando = true;
                this.espera = setTimeout(() => this.get_data(), 300);
            },
        },
    },
    data() {
        return {
            openVerNotas: false,
            filtrosReporte: null,
            folioOperaciones: null,
            nota_contenido: "",
            titulo_nota: "",
            /**consultar cfdi */
            id_cfdi: 0,
            verConsultarCfdi: false,
            uuid_a_sustituir: "",
            verFormularioCFDI: false,
            TipodeFormulario: "",
            filtros: filtrosIniciales(),
            tiposComprobante: [],
            tiposServicio: [],
            camposTexto: [
                { id: "folio", label: "Folio", max: 14, modo: "numeric" },
                { id: "uuid", label: "UUID", max: 36 },
                { id: "cliente", label: "Nombre del cliente", max: 150, placeholder: "Nombre o razón social" },
                { id: "rfc", label: "RFC", max: 13 },
                { id: "numero_servicio", label: "Núm. de venta o servicio", max: 14, modo: "numeric", placeholder: "Número de referencia" },
            ],
            fechas: [
                { id: "desde", label: "Timbrado desde" },
                { id: "hasta", label: "Timbrado hasta" },
            ],
            calendario: {
                locale: Spanish, dateFormat: "Y-m-d", altInput: true,
                altFormat: "j \\d\\e F \\d\\e Y", altInputClass: "cfdi-fecha-visible", disableMobile: true,
            },
            cargando: true,
            errorListado: "",
            consulta: 0,
            espera: null,
            cfdis: [],
            //variable
            total: 0,
            actual: 1,
        };
    },
    methods: {
        verNota(nota, title) {
            this.openVerNotas = true;
            this.nota_contenido = nota;
            this.titulo_nota = title;
        },
        reset_uuid_a_sustituir: function () {
            this.uuid_a_sustituir = "";
        },
        onCopy: function (e) {
            this.uuid_a_sustituir = e.text;
            this.$vs.notify({
                title: "Seleccionar CFDI",
                text:
                    "Se ha seleccionado el CFDI UUID: " +
                    e.text +
                    " para ser sustituido por el CFDI  a cancelar.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "success",
                time: 8000,
                position: "bottom-right",
            });
        },
        onError: function (e) {
            this.$vs.notify({
                title: "Seleccionar CFDI",
                text: "Error al copiar, por favor reintente.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000,
                position: "bottom-right",
            });
        },
        abrirReporteListado() {
            const filtros = this.filtros;
            this.filtrosReporte = { ...filtros, tipos_servicio: [...filtros.tipos_servicio] };
        },
        async cargarReporteListado() {
            const { data } = await axios.get("/facturacion/documentos/listado-ingresos", { params: this.filtrosReporte });
            return data;
        },
        async descargarExcelListado(token) {
            const { data } = await axios.get("/facturacion/documentos/listado-ingresos/" + encodeURIComponent(token) + "/excel", {
                responseType: "blob",
            });
            return data;
        },
        async descargarReporteListado(token) {
            const { data } = await axios.get("/facturacion/documentos/listado-ingresos/" + encodeURIComponent(token) + "/pdf", {
                responseType: "blob",
            });
            return data;
        },
        tituloOperaciones(cfdi) {
            return cfdi.operaciones_count
                ? "Ver operaciones relacionadas (" + cfdi.operaciones_count + ")"
                : "Ver resumen del CFDI sin operaciones relacionadas";
        },
        async cargarOperacionesCfdi() {
            const { data } = await axios.get("/facturacion/documentos/operaciones", {
                params: { folio_id: this.folioOperaciones },
            });
            return data;
        },
        async descargarOperacionesCfdi(token) {
            const { data } = await axios.get("/facturacion/documentos/operaciones/" + encodeURIComponent(token) + "/pdf", {
                responseType: "blob",
            });
            return data;
        },

        openActions(folio) {
            this.id_cfdi = folio;
            this.verConsultarCfdi = true;
        },
        TipoFormulario(tipo) {
            this.TipodeFormulario = tipo;
            this.verFormularioCFDI = true;
        },

        closeVentana() {
            this.verFormularioCFDI = false;
            this.get_data(this.actual);
        },
        closeVentanaActions() {
            this.verConsultarCfdi = false;
            this.get_data(this.actual);
        },
        cambiarPagina(pagina) {
            if (pagina !== this.actual) this.get_data(pagina);
        },
        cerrarMenus(evento) {
            const grupos = this.$refs.gruposFiltros;
            if (!grupos) return;
            grupos.querySelectorAll("details[open]").forEach(menu => {
                if (!evento || evento.key === "Escape" || !menu.contains(evento.target)) {
                    menu.open = false;
                    if (evento && evento.key === "Escape") menu.querySelector("summary").focus();
                }
            });
        },
        quitarFiltro(filtro) {
            const valor = this.filtros[filtro.campo];
            this.filtros[filtro.campo] = Array.isArray(valor) ? valor.filter(item => item !== filtro.valor) : "";
        },
        reset() {
            this.cerrarMenus();
            this.filtros = filtrosIniciales();
        },

        // Una respuesta anterior no debe reemplazar los resultados de un filtro nuevo.
        async get_data(page = 1) {
            clearTimeout(this.espera);
            const consulta = ++this.consulta;
            this.cargando = true;
            this.errorListado = "";
            try {
                const { data } = await axios.get("/facturacion/filtrar-cfdis", { params: { ...this.filtros, page } });
                if (consulta !== this.consulta) return;
                this.cfdis = data.data;
                this.total = data.last_page;
                this.actual = data.current_page;
            } catch (error) {
                if (consulta !== this.consulta) return;
                const errores = error.response && error.response.data && error.response.data.errors;
                this.errorListado = errores
                    ? errores[Object.keys(errores)[0]][0]
                    : "No se pudo consultar el listado de CFDI.";
                this.cfdis = [];
            } finally {
                if (consulta === this.consulta) this.cargando = false;
            }
        },
    },
    created() {
        this.get_data();
        Promise.all([facturacion.get_tipos_comprobante(), facturacion.get_empresa_tipo_operaciones()])
            .then(([comprobantes, operaciones]) => {
                this.tiposComprobante = comprobantes.data;
                this.tiposServicio = operaciones.data;
            })
            .catch(() => this.$vs.notify({
                title: "Facturación",
                text: "No se pudieron cargar los tipos de CFDI u operación.",
                color: "danger",
            }));
    },
    mounted() {
        document.addEventListener('click', this.cerrarMenus);
    },
    beforeDestroy() {
        document.removeEventListener('click', this.cerrarMenus);
        clearTimeout(this.espera);
        this.consulta++;
    },
};
</script>

<style scoped>
.cfdi-operaciones {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-right: 8px;
    padding: 3px 6px;
    border: 1px solid #ddd;
    background: white;
    color: #666;
    cursor: pointer;
    vertical-align: middle;
}
.cfdi-operaciones--vinculadas,
.cfdi-operaciones:hover {
    border-color: #b79a4c;
    background: #faf8f0;
    color: #80641a;
}
.cfdi-operaciones >>> svg { width: 15px; height: 15px; }
.cfdi-campos {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: 14px 16px;
}
.cfdi-campos > label {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    grid-column: span 3;
    min-width: 0;
    font: 11px Arial, sans-serif;
    color: #757575;
    text-transform: uppercase;
}
.cfdi-campos input,
.cfdi-fecha >>> input {
    display: block;
    width: 100%;
    height: 38px;
    margin-top: 7px;
    padding: 0 10px;
    border: 1px solid #c7c7c7;
    border-radius: 0;
    background: #fff;
    color: #555;
    font: 13px Arial, sans-serif;
}
.cfdi-campos input:focus,
.cfdi-fecha >>> input:focus { outline: none; border-color: #ab891d; }
.cfdi-fecha { position: relative; }
.cfdi-fecha >>> input { padding-right: 32px; }
.cfdi-fecha button {
    position: absolute;
    top: 0;
    right: 0;
    width: 32px;
    height: 38px;
    border: 0;
    background: transparent;
    color: #777;
    cursor: pointer;
}
.cfdi-grupos { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin-top: 17px; }
.cfdi-grupo { position: relative; min-width: 0; }
.cfdi-grupo summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-height: 36px;
    padding: 0 11px;
    list-style: none;
    border: 1px solid #d2d2d2;
    background: #fff;
    color: #666;
    font: 13px Arial, sans-serif;
    cursor: pointer;
}
.cfdi-grupo summary::-webkit-details-marker { display: none; }
.cfdi-grupo[open] summary,
.cfdi-grupo summary.cfdi-activo { border-color: #ab891d; color: #80641a; background: #faf8f0; }
.cfdi-cantidad { padding: 2px 6px; border-radius: 10px; background: #f0e7c7; font-size: 10px; }
.cfdi-menu {
    position: absolute;
    z-index: 1000;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    padding: 8px;
    border: 1px solid #ddd;
    background: #fff;
    box-shadow: 0 4px 15px #0002;
}
.cfdi-menu label { display: flex; align-items: center; gap: 8px; padding: 8px; font-size: 12px; cursor: pointer; }
.cfdi-menu label:hover { background: #faf8f0; }
.cfdi-menu input { flex-shrink: 0; accent-color: #a58828; }
.cfdi-chips { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 14px; padding-top: 12px; border-top: 1px solid #eee; }
.cfdi-chips button,
.cfdi-limpiar { display: inline-flex; align-items: center; gap: 6px; border: 0; color: #666; cursor: pointer; }
.cfdi-chips button { padding: 5px 8px; background: #eee; font-size: 11px; overflow-wrap: anywhere; }
.cfdi-limpiar { background: transparent; font: 12px Arial, sans-serif; }
.cfdi-reporte { color: #80641a; font-weight: bold; }
.cfdi-reporte:hover { text-decoration: underline; }
.cfdi-reporte:disabled { opacity: .5; cursor: default; }
.cfdi-card >>> svg.feather { width: 15px; height: 15px; }
.cfdi-card >>> .vx-card__collapsible-content { overflow: visible !important; }
.cfdi-card >>> .vx-card__header {
    flex-wrap: wrap;
    gap: 10px 16px;
}
.cfdi-card >>> .vx-card__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 16px;
}
.cfdi-grupo summary > span:not(.cfdi-cantidad) { text-align: center; }
.cfdi-grupo summary > .feather-icon { flex-shrink: 0; }

/* Cuatro campos de identificación y una fila completa para referencia y fechas. */
.cfdi-campos > label:nth-child(n+5) { grid-column: span 4; }

@media (min-width: 1440px) {
    .cfdi-campos { grid-template-columns: minmax(0, .8fr) repeat(2, minmax(0, 1.45fr)) repeat(4, minmax(0, 1fr)); }
    .cfdi-campos > label:nth-child(n) { grid-column: auto; }
    .cfdi-grupos { grid-template-columns: repeat(6, minmax(0, 1fr)); }
}
@media (max-width: 760px) {
    .cfdi-campos > label:nth-child(n) { grid-column: span 6; }
    .cfdi-campos > label:nth-child(3) { grid-column: span 12; }
    .cfdi-grupos { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 420px) {
    .cfdi-campos > label:nth-child(n) { grid-column: span 12; }
    .cfdi-campos input, .cfdi-fecha >>> input { font-size: 16px; }
    .cfdi-grupos { grid-template-columns: minmax(0, 1fr); }
}
</style>
