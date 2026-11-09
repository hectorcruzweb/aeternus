<!--
IDS DE LOS PERMISOS QUE APLICAN EN ESTE MODULO DE CLIENTES
registrar clientes 11
modificar informacion 12
eliminar clientes 13
consultar información 14
consultar estados de cuenta15

para saber si se debe desplegar algun boton o accion se hace llamando la funcion global
this.$modulo.permiso(this.$route.path,11)
con la ruta especifica del modulo que se desea consultar y el id del permiso

-->
<template>
    <div>
        <div class="text-right buttons-container-header clientes-listado__header">
            <vs-button
                class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
                color="success"
                @click="formulario('agregar')"
            >
                <span>Registrar Cliente</span>
            </vs-button>
        </div>

        <div class="pt-2 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">

            <!-- Consulta independiente mediante FiltrarClientes. La tabla conserva su diseño. -->
            <vx-card title="Filtros de selección" :collapse-action="false">
                <template slot="actions">
                  <button type="button" class="clientes-filtros__reporte" :disabled="cargandoListado || !!errorListado" title="Ver y exportar todos los clientes filtrados en Excel o PDF" @click="abrirReporteClientes"><feather-icon icon="FileTextIcon" /><span>Reporte del listado</span><feather-icon icon="ArrowUpRightIcon" /></button>
                  
                    
                    <button type="button" class="clientes-filtros__clear" @click="limpiarFiltrosListado"><feather-icon icon="RefreshCwIcon" />Limpiar filtros</button>
                </template>
                <div class="clientes-filtros">
                    <div class="clientes-filtros__fields">
                        <div><label for="listado-cliente-id">Núm. cliente</label><input id="listado-cliente-id" v-model="filtrosListado.numero" inputmode="numeric" placeholder="Número de cliente" autocomplete="off" /></div>
                        <div><label for="listado-cliente-busqueda">Nombre, teléfono o correo</label><input id="listado-cliente-busqueda" v-model="filtrosListado.busqueda" placeholder="Nombre, teléfono o correo electrónico" autocomplete="off" /></div>
                        <div><label for="listado-cliente-desde">Alta desde</label><div class="clientes-filtros__date-control"><flat-pickr id="listado-cliente-desde" v-model="filtrosListado.desde" :config="calendarioAltaConfig" placeholder="Selecciona una fecha" /><button v-if="filtrosListado.desde" type="button" class="clientes-filtros__date-clear" aria-label="Limpiar fecha de alta desde" @mousedown.prevent @click.stop="filtrosListado.desde = ''"><feather-icon icon="XIcon" /></button></div></div>
                        <div><label for="listado-cliente-hasta">Alta hasta</label><div class="clientes-filtros__date-control"><flat-pickr id="listado-cliente-hasta" v-model="filtrosListado.hasta" :config="calendarioAltaConfig" placeholder="Selecciona una fecha" /><button v-if="filtrosListado.hasta" type="button" class="clientes-filtros__date-clear" aria-label="Limpiar fecha de alta hasta" @mousedown.prevent @click.stop="filtrosListado.hasta = ''"><feather-icon icon="XIcon" /></button></div></div>
                    </div>
                    <div class="clientes-filtros__toolbar">
                        <button v-for="grupo in gruposListado" :key="grupo.key" type="button" class="clientes-filtros__group" :class="{'is-selected': cantidadFiltrosGrupo(grupo.key) > 0}" :aria-expanded="grupoListadoAbierto === grupo.key ? 'true' : 'false'" aria-controls="listado-clientes-opciones" @click="grupoListadoAbierto = grupoListadoAbierto === grupo.key ? null : grupo.key">
                            <feather-icon :icon="grupo.icon" /><span>{{ grupo.label }}</span><span v-if="cantidadFiltrosGrupo(grupo.key)" class="clientes-filtros__count">{{ cantidadFiltrosGrupo(grupo.key) }}</span><feather-icon :icon="grupoListadoAbierto === grupo.key ? 'ChevronUpIcon' : 'ChevronDownIcon'" />
                        </button>
                        
                    </div>
                    <section v-if="grupoListadoActual" id="listado-clientes-opciones" class="clientes-filtros__panel" :aria-label="grupoListadoActual.title">
                        <div class="clientes-filtros__panel-heading"><strong>{{ grupoListadoActual.title }}</strong><button type="button" @click="grupoListadoAbierto = null">Cerrar opciones <feather-icon icon="ChevronUpIcon" /></button></div>
                        <div class="clientes-filtros__options">
                            <button v-for="opcion in grupoListadoActual.options" :key="opcion.value" type="button" :aria-pressed="opcionListadoActiva(grupoListadoActual.key, opcion.value) ? 'true' : 'false'" @click="seleccionarFiltroListado(grupoListadoActual.key, opcion.value)"><feather-icon v-if="opcionListadoActiva(grupoListadoActual.key, opcion.value)" icon="CheckIcon" />{{ opcion.label }}</button>
                        </div>
                    </section>
                    <div v-if="etiquetasListado.length" class="clientes-filtros__chips" aria-label="Filtros seleccionados">
                        <button v-for="etiqueta in etiquetasListado" :key="etiqueta.key + etiqueta.value" type="button" :aria-label="'Quitar ' + etiqueta.label" @click="quitarFiltroListado(etiqueta)">{{ etiqueta.label }}<feather-icon icon="XIcon" /></button>
                    </div>
                    <p v-if="errorListado" class="clientes-filtros__error" role="alert">{{ errorListado }}</p>
                    <p v-if="cargandoListado" class="clientes-filtros__loading" role="status">Actualizando clientes…</p>
                </div>
            </vx-card>
        </div>
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
                        :data="clientes"
                        noDataText="0 Resultados"
                        class="tabla-datos"
                    >
                        <template slot="header">
                            <h3>Listado de Clientes Registrados</h3>
                        </template>
                        <template slot="thead">
                            <vs-th>Núm. Cliente</vs-th>
                            <vs-th>Nombre</vs-th>
                            <vs-th>Domicilio</vs-th>
                            <vs-th>Celular</vs-th>
                            <vs-th>Servicios Gratis</vs-th>
                            <vs-th>Seguimientos</vs-th>
                            <vs-th>Acciones</vs-th>
                        </template>
                        <template slot-scope="{ data }">
                            <vs-tr
                                :data="tr"
                                :key="indextr"
                                v-for="(tr, indextr) in data"
                            >
                                <vs-td :data="data[indextr].id">
                                    <span class="clientes-listado__id">
                                        <span class="font-bold">{{ data[indextr].id }}</span>
                                        <span class="clientes-listado__status" :class="data[indextr].status == 1 ? 'is-active' : 'is-cancelled'" :title="data[indextr].status == 1 ? 'Cliente activo' : 'Cliente cancelado'" role="img" :aria-label="data[indextr].status == 1 ? 'Cliente activo' : 'Cliente cancelado'">
                                            <feather-icon :icon="data[indextr].status == 1 ? 'CheckCircleIcon' : 'XCircleIcon'" />
                                        </span>
                                    </span>
                                </vs-td>
                                <vs-td :data="data[indextr].nombre">{{
                                    data[indextr].nombre
                                }}</vs-td>
                                <vs-td :data="data[indextr].direccion">{{
                                    data[indextr].direccion
                                }}</vs-td>
                                <vs-td :data="data[indextr].celular">
                                    {{ data[indextr].celular }}
                                </vs-td>

                                <vs-td :data="data[indextr].servicios_gratis">
                                    <div class="flex justify-center">
                                        <img
                                            v-if="
                                                data[indextr].servicios_gratis >
                                                0
                                            "
                                            class="img-btn-24 mx-2"
                                            src="@assets/images/free_yes.svg"
                                            title="Ver servicios gratis de este cliente"
                                            @click="
                                                verServiciosGratis(
                                                    data[indextr]
                                                )
                                            "
                                        />
                                        <img
                                            v-else
                                            class="img-btn-24 mx-2"
                                            src="@assets/images/free_no.svg"
                                            title="Ver servicios gratis de este cliente"
                                            @click="
                                                verServiciosGratis(
                                                    data[indextr]
                                                )
                                            "
                                        />
                                    </div>
                                </vs-td>
                                <vs-td :data="data[indextr].id_user">
                                    <div class="flex justify-center">
                                        <img
                                            class="img-btn-24 mx-2"
                                            src="@assets/images/seguimientos.svg"
                                            title="Control de Seguimientos"
                                            @click="OpenFormSeguimientos(tr)"
                                        />
                                    </div>
                                </vs-td>
                                <vs-td :data="data[indextr].id_user">
                                    <div class="flex justify-center">
                                        <img
                                            class="cursor-pointer img-btn-18 mx-2"
                                            src="@assets/images/edit.svg"
                                            title="Modificar"
                                            @click="
                                                openModificar(data[indextr].id)
                                            "
                                        />
                                        <img
                                            v-if="data[indextr].status == 1"
                                            class="img-btn-24 mx-2"
                                            src="@assets/images/switchon.svg"
                                            title="Deshabilitar"
                                            @click="
                                                deleteCliente(
                                                    data[indextr].id,
                                                    data[indextr].nombre
                                                )
                                            "
                                        />
                                        <img
                                            v-else
                                            class="img-btn-24 mx-2"
                                            src="@assets/images/switchoff.svg"
                                            title="Habilitar"
                                            @click="
                                                altaCliente(
                                                    data[indextr].id,
                                                    data[indextr].nombre
                                                )
                                            "
                                        />
                                    </div>
                                </vs-td>
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
        <VisorReportes :show="mostrarReporteClientes" :cargar="cargarReporteClientes" :descargar-archivo="descargarClientesExcel" :descargar-pdf="descargarClientesPdf" @close="mostrarReporteClientes = false" />
        <Password
            v-if="openStatus"
            :show="openStatus"
            :callback-on-success="callback"
            @closeVerificar="closeStatus"
            :accion="accionNombre"
        >
        </Password>
        <FormularioClientes
            v-if="verFormularioClientes"
            :id_cliente="id_cliente_modificar"
            :tipo="tipoFormulario"
            :show="verFormularioClientes"
            @closeVentana="verFormularioClientes = false"
            @retornar_id="retorno_id"
        >
        </FormularioClientes>
        <ServiciosGratis
            v-if="openServiciosGratis"
            :show="openServiciosGratis"
            :datos="datosCliente"
            @closeServiciosGratis="closeEntregarConvenio"
        >
        </ServiciosGratis>
        <FormularioSeguimientos
            v-if="openSeguimientos"
            :show="openSeguimientos"
            :filters="filtersSeguimientos"
            @closeVentana="CloseFormProgramarSeguimientos"
        >
        </FormularioSeguimientos>
    </div>
</template>

<script>
import VisorReportes from "../VisorReportes.vue";
import axiosCancel from "axios";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import { configdateTimePicker } from "@/VariablesGlobales";
import FormularioSeguimientos from "../seguimientos/FormularioSeguimientos.vue";
import clientes from "@services/clientes";

import FormularioClientes from "@pages/clientes/FormularioClientes";

//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */
import { PermisosModulo } from "@/VariablesGlobales";
import ServiciosGratis from "@pages/clientes/ServiciosGratis";

export default {
    components: {
        VisorReportes,
        flatPickr,
        Password,
        FormularioClientes,
        ServiciosGratis,
        FormularioSeguimientos,
    },
    watch: {
        actual(pagina) { if (pagina !== this.paginaCargada) this.get_data(pagina); },
        filtrosListado: {
            deep: true,
            handler() { this.programarConsultaListado(); },
        },
    },
    name: "ListadoClientesFiltros",
    computed: {
        grupoListadoActual() { return this.gruposListado.find(grupo => grupo.key === this.grupoListadoAbierto) || null; },
        etiquetasListado() {
            const etiquetas = [];
            const campos = { numero: 'Núm. cliente', busqueda: 'Búsqueda', desde: 'Alta desde', hasta: 'Alta hasta' };
            Object.keys(campos).forEach(key => { if (this.filtrosListado[key]) etiquetas.push({key, value: '', label: campos[key] + ': ' + (['desde', 'hasta'].includes(key) ? this.fechaAltaHumana(this.filtrosListado[key]) : this.filtrosListado[key])}); });
            this.gruposListado.forEach(grupo => grupo.options.forEach(opcion => {
                if (opcion.value && this.opcionListadoActiva(grupo.key, opcion.value)) etiquetas.push({key: grupo.key, value: opcion.value, label: opcion.label});
            }));
            return etiquetas;
        },
        noDataFound() {
            return this.clientes.length === 0;
        },
    },
    data() {
        return {
            // Cancelación local y contador impiden que una respuesta antigua reemplace filtros recientes.
            mostrarReporteClientes: false,
            filtrosReporteClientes: {},
            temporizadorListado: null,
            cancelacionListado: null,
            versionListado: 0,
            paginaCargada: 1,
            ultimoTextoListado: '|',
            cargandoListado: false,
            errorListado: '',
            componenteListadoCerrado: false,
            // Copia local de la configuración española del sistema. Mantener Y-m-d
            // en el modelo y mostrar día/mes/año; evitar el calendario nativo en móviles.
            calendarioAltaConfig: { ...configdateTimePicker, altFormat: 'j \\d\\e F \\d\\e Y', altInputClass: 'clientes-filtros__fecha', disableMobile: true },
            grupoListadoAbierto: null,
            filtrosListado: { numero: '', busqueda: '', desde: '', hasta: '', estado: '', adeudos: '', operaciones: [], seguimientos: '', contacto: [] },
            gruposListado: [
                { key: 'estado', label: 'Estado', title: 'Estado del cliente', icon: 'UserIcon', options: [{value:'',label:'Todos'},{value:'1',label:'Activos'},{value:'0',label:'Cancelados'}] },
                { key: 'adeudos', label: 'Adeudos', title: 'Situación de adeudo', icon: 'CreditCardIcon', options: [{value:'',label:'Todos'},{value:'sin',label:'Sin adeudo'},{value:'pendiente',label:'Con saldo pendiente'},{value:'vencido',label:'Con pagos vencidos'}] },
                { key: 'operaciones', label: 'Operaciones', title: 'Tipo de operación · puedes seleccionar varias', icon: 'LayersIcon', options: [{value:'1',label:'Ventas en cementerio'},{value:'2',label:'Cuotas de mantenimiento'},{value:'3',label:'Servicios funerarios'},{value:'4',label:'Planes funerarios'},{value:'5',label:'Ventas generales'}] },
                { key: 'seguimientos', label: 'Seguimientos', title: 'Seguimientos pendientes', icon: 'CalendarIcon', options: [{value:'',label:'Todos'},{value:'sin',label:'Sin seguimiento pendiente'},{value:'pendiente',label:'Con seguimiento pendiente'},{value:'vencido',label:'Con seguimiento vencido'}] },
                { key: 'contacto', label: 'Contacto', title: 'Datos de contacto', icon: 'PhoneIcon', options: [{value:'celular_si',label:'Con celular'},{value:'celular_no',label:'Sin celular'},{value:'correo_si',label:'Con correo'},{value:'correo_no',label:'Sin correo'}] },
            ],
            openSeguimientos: false,
            filtersSeguimientos: {
                cliente_id: null,
                tipo_cliente_id: null,
                operacion_id: null,
                origen: 1, //Formulario de Origen. 1-seguim
            },
            //control de servicios gratis
            openServiciosGratis: false,
            datosCliente: [],
            //variable
            PermisosModulo: PermisosModulo,
            verPaginado: true,
            total: 0,
            actual: 1,
            clientes: [],
            openStatus: false,
            callback: Function,
            accionNombre: "",
            tipoFormulario: "",
            verFormularioClientes: false,
            id_cliente_modificar: 0,
            // Cliente seleccionado para habilitar/deshabilitar con autorización.
            cliente_id: "",
        };
    },
    methods: {
        /** Congela los filtros seleccionados; actualizar el visor repite esta misma selección. */
        abrirReporteClientes() {
            this.filtrosReporteClientes = JSON.parse(JSON.stringify(this.filtrosListado));
            this.mostrarReporteClientes = true;
        },
        async cargarReporteClientes() { return (await clientes.reporteListado(this.filtrosReporteClientes)).data; },
        async descargarClientesExcel(token) { return (await clientes.descargarListado(token, 'excel')).data; },
        async descargarClientesPdf(token) { return (await clientes.descargarListado(token, 'pdf')).data; },
        // Construir la fecha local por partes para evitar el desplazamiento UTC de YYYY-MM-DD.
        fechaAltaHumana(valor) {
            if (!/^\d{4}-\d{2}-\d{2}$/.test(valor || '')) return valor || '';
            const partes = valor.split('-').map(Number);
            return new Date(partes[0], partes[1] - 1, partes[2]).toLocaleDateString('es-MX', {day: 'numeric', month: 'long', year: 'numeric'});
        },
        // Modificar el estado dispara una consulta automática mediante el watcher profundo.
        cantidadFiltrosGrupo(key) { const valor = this.filtrosListado[key]; return Array.isArray(valor) ? valor.length : Number(Boolean(valor)); },
        opcionListadoActiva(key, value) { const actual = this.filtrosListado[key]; return Array.isArray(actual) ? actual.includes(value) : actual === value; },
        seleccionarFiltroListado(key, value) {
            const actual = this.filtrosListado[key];
            if (Array.isArray(actual)) {
                if (actual.includes(value)) { this.filtrosListado[key] = actual.filter(item => item !== value); return; }
                const compatibles = key === 'contacto' ? actual.filter(item => item.split('_')[0] !== value.split('_')[0]) : actual;
                this.filtrosListado[key] = compatibles.concat(value);
            } else this.filtrosListado[key] = actual === value ? '' : value;
        },
        quitarFiltroListado(etiqueta) {
            const actual = this.filtrosListado[etiqueta.key];
            this.filtrosListado[etiqueta.key] = Array.isArray(actual) ? actual.filter(item => item !== etiqueta.value) : '';
        },
        limpiarFiltrosListado() {
            this.filtrosListado = { numero: '', busqueda: '', desde: '', hasta: '', estado: '', adeudos: '', operaciones: [], seguimientos: '', contacto: [] };
            this.grupoListadoAbierto = null;
        },
        OpenFormSeguimientos(cliente) {
            this.filtersSeguimientos.cliente_id = cliente.id;
            this.filtersSeguimientos.tipo_cliente_id = 1;
            this.filtersSeguimientos.origen = 2; //clientes
            this.openSeguimientos = true;
        },
        CloseFormProgramarSeguimientos() {
            this.openSeguimientos = false;
        },
        closeEntregarConvenio() {
            this.openServiciosGratis = false;

            this.get_data(this.actual);
        },
        verServiciosGratis(datos) {
            this.datosCliente = datos;
            this.openServiciosGratis = true;
        },
        /** Pausa de 300 ms para escritura; botones y fechas se aplican al instante.
         * Invalidar la petición anterior desde que cambia el filtro, incluso durante la pausa.
         */
        programarConsultaListado() {
            clearTimeout(this.temporizadorListado);
            this.versionListado++;
            if (this.cancelacionListado) this.cancelacionListado.cancel('Filtros actualizados');
            this.cargandoListado = false;
            this.verPaginado = true;
            const texto = this.filtrosListado.numero + '|' + this.filtrosListado.busqueda;
            const espera = texto === this.ultimoTextoListado ? 0 : 300;
            this.ultimoTextoListado = texto;
            this.temporizadorListado = setTimeout(() => this.get_data(1), espera);
        },
        /** Consulta SQL paginada propia: no utiliza get_clientes ni su token compartido.
         * La tabla conserva su resultado anterior mientras llega la nueva página.
         */
        get_data(page) {
            if (this.componenteListadoCerrado) return;
            clearTimeout(this.temporizadorListado);
            this.versionListado++;
            const version = this.versionListado;
            if (this.cancelacionListado) this.cancelacionListado.cancel('Consulta reemplazada');
            this.cargandoListado = false;
            this.verPaginado = true;
            this.errorListado = '';
            const filtros = this.filtrosListado;
            if (filtros.numero && !/^[1-9]\d*$/.test(String(filtros.numero).trim())) {
                this.errorListado = 'El número de cliente debe ser un entero mayor que cero.';
                return;
            }
            if (filtros.desde && filtros.hasta && filtros.desde > filtros.hasta) {
                this.errorListado = 'La fecha inicial no puede ser posterior a la final.';
                return;
            }
            this.cancelacionListado = axiosCancel.CancelToken.source();
            this.cargandoListado = true;
            this.verPaginado = false;
            const parametros = { ...filtros, numero: String(filtros.numero).trim(), busqueda: filtros.busqueda.trim(), page };
            return clientes.filtrarClientes(parametros, this.cancelacionListado.token)
                .then(res => {
                    if (version !== this.versionListado || this.componenteListadoCerrado) return;
                    this.clientes = res.data.data;
                    this.total = res.data.last_page;
                    this.paginaCargada = res.data.current_page;
                    this.actual = res.data.current_page;
                })
                .catch(err => {
                    if (axiosCancel.isCancel(err) || version !== this.versionListado || this.componenteListadoCerrado) return;
                    const response = err.response;
                    if (response && response.status === 422) {
                        const mensajes = Object.values(response.data.errors || {}).reduce((todos, lista) => todos.concat(lista), []);
                        this.errorListado = mensajes[0] || 'Verifica los filtros seleccionados.';
                    } else if (response && response.status === 403) {
                        this.errorListado = 'No tienes permiso para consultar estos clientes.';
                    } else this.errorListado = 'No se pudo actualizar el listado. Modifica un filtro o limpia los filtros para reintentar.';
                })
                .finally(() => {
                    if (version === this.versionListado && !this.componenteListadoCerrado) {
                        this.cargandoListado = false;
                        this.verPaginado = true;
                        this.cancelacionListado = null;
                    }
                });
        },

        closeStatus() {
            this.openStatus = false;
        },

        formulario(tipo) {
            this.tipoFormulario = tipo;
            this.verFormularioClientes = true;
        },
        openModificar(id_cliente) {
            this.tipoFormulario = "modificar";
            this.id_cliente_modificar = id_cliente;
            this.verFormularioClientes = true;
        },
        retorno_id(dato) {
            this.get_data(this.actual);
        },
        deleteCliente(id_cliente, nombre) {
            this.accionNombre = "deshabilitar cliente " + nombre;
            this.cliente_id = id_cliente;
            this.openStatus = true;
            this.callback = this.delete_cliente;
        },

        altaCliente(id_cliente, nombre) {
            this.accionNombre = "Habilitar cliente " + nombre;
            this.cliente_id = id_cliente;
            this.openStatus = true;
            this.callback = this.habilitar_cliente;
        },
        delete_cliente() {
            this.$vs.loading();
            let datos = {
                cliente_id: this.cliente_id,
            };
            clientes
                .delete_cliente(datos)
                .then((res) => {
                    this.$vs.loading.close();
                    this.get_data(this.actual);
                    if (res.data >= 1) {
                        this.$vs.notify({
                            title: "Deshabilitar Cliente",
                            text: "Se ha deshabilitado al cliente exitosamente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "success",
                            time: 8000,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Deshabilitar Cliente",
                            text: "No se realizaron cambios.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 8000,
                        });
                    }
                })
                .catch((err) => {
                    this.$vs.loading.close();
                    if (err.response) {
                        if (err.response.status == 403) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Permiso denegado",
                                text: "Verifique sus permisos con el administrador del sistema.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "warning",
                                time: 8000,
                            });
                        } else if (err.response.status == 422) {
                            /**error de validacion */
                            this.errores = err.response.data.error;
                        }
                    }
                });
        },
        habilitar_cliente() {
            this.$vs.loading();
            let datos = {
                cliente_id: this.cliente_id,
            };
            clientes
                .alta_cliente(datos)
                .then((res) => {
                    this.$vs.loading.close();
                    this.get_data(this.actual);
                    if (res.data >= 1) {
                        this.$vs.notify({
                            title: "Habilitar Cliente",
                            text: "Se ha habilitado al cliente exitosamente.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "success",
                            time: 8000,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Habilitar Cliente",
                            text: "No se realizaron cambios.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 8000,
                        });
                    }
                })
                .catch((err) => {
                    this.$vs.loading.close();
                    if (err.response) {
                        if (err.response.status == 403) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Permiso denegado",
                                text: "Verifique sus permisos con el administrador del sistema.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "warning",
                                time: 8000,
                            });
                        } else if (err.response.status == 422) {
                            /**error de validacion */
                            this.errores = err.response.data.error;
                        }
                    }
                });
        },
    },
    beforeDestroy() {
        this.componenteListadoCerrado = true;
        this.versionListado++;
        clearTimeout(this.temporizadorListado);
        if (this.cancelacionListado) this.cancelacionListado.cancel('Listado cerrado');
    },
    created() {
        this.get_data(this.actual);
    },
};
</script>

<style scoped>
/* Estilos exclusivos de la card alternativa. No modificar tabla-datos ni sus descendientes. */
.clientes-filtros{font-family:Arial,sans-serif;color:#666}
.clientes-filtros__fields{display:grid;grid-template-columns:1fr 2.1fr 1fr 1fr;gap:16px}
.clientes-filtros label{display:block;margin-bottom:5px;font-size:11px;line-height:1.5;color:#777;text-transform:uppercase}
.clientes-filtros input{display:block;width:100%;min-width:0;height:38px;border:1px solid #c9c9c9;border-radius:0;background:#fff;color:#666;padding:8px 10px;font-family:inherit;font-size:13px;box-sizing:border-box}
.clientes-filtros input::placeholder{color:#aaa;font-size:12px}
.clientes-filtros button,.clientes-filtros__clear{font-family:Arial,sans-serif;cursor:pointer}
.clientes-filtros button:focus-visible,.clientes-filtros input:focus-visible,.clientes-filtros__clear:focus-visible{outline:2px solid #a58828;outline-offset:3px}
.clientes-filtros__toolbar{display:flex;flex-wrap:wrap;gap:9px;margin-top:18px}
.clientes-filtros__group{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #d4d4d4;border-radius:2px;background:#fff;color:#666;font-size:13px;line-height:20px}
.clientes-filtros__group.is-selected,.clientes-filtros__group[aria-expanded=true]{border-color:#b4973b;color:#94771f}
.clientes-filtros__group[aria-expanded=true]{background:#fcfaf4}
.clientes-filtros__toolbar >>> svg{display:block;width:16px;height:16px}
.clientes-filtros__count{display:inline-block;min-width:18px;padding:0 5px;border-radius:10px;background:#f0e7c4;color:#927520;font-size:11px;text-align:center;line-height:18px}
.clientes-filtros__clear{display:inline-flex;align-items:center;gap:7px;border:0;background:transparent;color:#888;padding:3px 0;font-size:12px;font-weight:400}
.clientes-filtros__clear >>> svg{display:block;width:14px;height:14px}
.clientes-filtros__panel{margin-top:16px;padding:14px 16px;background:#f8f8f8;border:1px solid #e7e7e7}
.clientes-filtros__panel-heading{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px;font-size:13px}
.clientes-filtros__panel-heading button{display:inline-flex;align-items:center;gap:6px;padding:0;border:0;background:none;color:#888;font-size:12px}
.clientes-filtros__panel-heading >>> svg{width:14px;height:14px}
.clientes-filtros__options{display:flex;flex-wrap:wrap;gap:8px}
.clientes-filtros__options button{display:inline-flex;align-items:center;gap:7px;padding:8px 12px;border:1px solid #d1d1d1;border-radius:2px;background:#fff;color:#666;font-size:12px;line-height:18px}
.clientes-filtros__options button[aria-pressed=true]{background:#fbf7e9;border-color:#af9132;color:#8d701d}
.clientes-filtros__options >>> svg{width:14px;height:14px}
.clientes-filtros__chips{display:flex;flex-wrap:wrap;gap:7px;margin-top:14px;padding-top:12px;border-top:1px solid #eee}
.clientes-filtros__chips button{display:inline-flex;align-items:center;gap:9px;padding:5px 8px;border:0;background:#eee;color:#666;font-size:11px;line-height:16px}
.clientes-filtros__chips >>> svg{width:11px;height:11px;color:#999}
.clientes-filtros__error{color:#ac4444;font-size:12px;line-height:1.5;margin-top:12px}
@media(max-width:900px){.clientes-filtros__fields{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.clientes-filtros__fields{grid-template-columns:minmax(0,1fr)}.clientes-filtros__panel-heading{align-items:flex-start}.clientes-filtros__group{flex:1 1 42%;justify-content:center}.clientes-filtros input{font-size:16px}}
/* Flatpickr crea el input visible fuera del alcance del atributo scoped.
   Seleccionar ese campo explícitamente; conservar oculto el valor ISO interno. */
.clientes-filtros >>> .clientes-filtros__fecha{display:block;width:100%;min-width:0;height:38px;border:1px solid #c9c9c9;border-radius:0;background:#fff;color:#666;padding:8px 10px;font-family:Arial,sans-serif;font-size:13px;box-sizing:border-box}
.clientes-filtros >>> .clientes-filtros__fecha::placeholder{color:#aaa;font-size:12px}
.clientes-filtros >>> input[type="hidden"]{display:none!important}
.clientes-filtros >>> .clientes-filtros__fecha:focus-visible{outline:2px solid #a58828;outline-offset:3px}
@media(max-width:480px){.clientes-filtros >>> .clientes-filtros__fecha{font-size:16px}}
/* La zona de borrado es independiente del input: el resto abre Flatpickr. */
.clientes-filtros__date-control{position:relative;min-width:0}
.clientes-filtros__date-control >>> .clientes-filtros__fecha{padding-right:36px}
.clientes-filtros__date-clear{position:absolute;right:3px;top:3px;width:30px;height:32px;display:flex;align-items:center;justify-content:center;padding:0;border:0;background:transparent;color:#909090;border-radius:0}
.clientes-filtros__date-clear:hover{color:#aa4545;background:#f5f5f5}
.clientes-filtros__date-clear >>> svg{display:block;width:15px;height:15px}
/* Separación compacta exclusiva entre la acción de alta y los filtros. */
@media(min-width:768px){.clientes-listado__header{padding-bottom:8px}}
/* Estado de consulta local, sin bloquear la escritura mediante loading global. */
.clientes-filtros__loading{margin:10px 0 0;font-size:12px;color:#99802e;line-height:1.5}
/* Estado junto al identificador: icono y etiqueta accesible, sin columna adicional. */
.clientes-listado__id{display:inline-flex;align-items:center;justify-content:center;gap:7px;white-space:nowrap}
.clientes-listado__status{display:inline-flex;align-items:center}
.clientes-listado__status >>> svg{display:block;width:14px;height:14px}
.clientes-listado__status.is-active{color:#28a56a}
.clientes-listado__status.is-cancelled{color:#bd4a4a}

/* Acción del listado completo al extremo de la misma fila; se adapta sin tapar filtros. */
.clientes-filtros__toolbar{align-items:center}
.clientes-filtros__reporte{display:inline-flex;align-items:center;justify-content:center;gap:8px;margin-left:auto;min-height:38px;padding:8px 16px;border:1px solid #a58828;border-radius:0;background:#a58828;color:#fff;font-size:13px;font-weight:600;line-height:20px;transition:background-color .16s ease,box-shadow .16s ease}
.clientes-filtros__reporte:hover:not(:disabled){background:#8e7220;border-color:#8e7220;box-shadow:0 2px 5px rgba(106,83,22,.18)}
.clientes-filtros__reporte:active:not(:disabled){background:#7b621b}
.clientes-filtros__reporte:disabled{opacity:.5;cursor:wait}
@media(max-width:600px){.clientes-filtros__reporte{flex-basis:100%;margin-left:0;margin-top:3px}}
@media(prefers-reduced-motion:reduce){.clientes-filtros__reporte{transition:none}}
/* Enlace del reporte en el encabezado, separado de la limpieza de filtros. */
.clientes-filtros__reporte{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:28px;width:auto;flex-basis:auto;margin:0 20px 0 0;padding:3px 0;border:0;background:transparent;color:#96771e;font:600 12px Arial,sans-serif;line-height:20px;text-decoration:none;cursor:pointer;box-shadow:none}
.clientes-filtros__reporte:hover:not(:disabled){color:#725815;background:transparent;border:0;box-shadow:none;text-decoration:underline;text-underline-offset:3px}
.clientes-filtros__reporte:active:not(:disabled){background:transparent}
.clientes-filtros__reporte:focus-visible{outline:2px solid #a58828;outline-offset:3px}
.clientes-filtros__reporte >>> svg{display:block;width:15px;height:15px}
@media(max-width:600px){.clientes-filtros__reporte{width:auto;flex-basis:auto;margin:0 12px 0 0}}
</style>
