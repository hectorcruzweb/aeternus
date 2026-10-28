<template>
  <div class="centerx">
    <vs-popup class="forms-popup reporte-correo z-index70k" title="Nuevo mensaje" :active="show"
      ref="EnviarReporteCorreo" @click.native.capture="bloquearCierreExterior" @update:active="alCambiarVisibilidad">
      <form class="reporte-correo__formulario" @submit.prevent="confirmar">
        <div class="reporte-correo__cuerpo">
        <div class="reporte-correo__campo">
          <label for="reporte-destinatario">Nombre <small>(opcional)</small></label>
          <div class="reporte-correo__entrada reporte-correo__buscador"><vs-input id="reporte-destinatario" ref="destinatario" name="destinatario"
            data-vv-scope="correoReporte" data-vv-as="destinatario" v-validate="'max:75'"
            v-model="datos.destinatario" maxlength="75" :disabled="isLoading" class="w-full" placeholder="Nombre o inicio del nombre de un cliente"
            autocomplete="off" role="combobox" aria-autocomplete="list" :aria-expanded="sugerenciasAbiertas && campoBusqueda === 'nombre'"
            aria-controls="nombres-clientes" :aria-activedescendant="campoBusqueda === 'nombre' && sugerenciaActiva >= 0 ? 'nombre-cliente-' + sugerenciaActiva : null"
            @keydown.native="teclasNombre" @focus.native.capture="enfocarBusqueda('nombre')" @focusout.native="cerrarSugerencias" />
            <div v-if="sugerenciasAbiertas && campoBusqueda === 'nombre'" class="reporte-correo__sugerencias">
              <p v-if="buscandoCorreo" role="status">Buscando clientes…</p>
              <p v-else-if="errorBusquedaCorreo" role="status">{{ errorBusquedaCorreo }}</p>
              <ul v-else-if="sugerenciasCorreo.length" id="nombres-clientes" role="listbox" aria-label="Correos de clientes">
                <li v-for="(cliente, indice) in sugerenciasCorreo" :key="cliente.id" role="presentation">
                  <button type="button" :id="'nombre-cliente-' + indice" role="option" :aria-selected="indice === sugerenciaActiva"
                    :class="{ 'es-activa': indice === sugerenciaActiva }" tabindex="-1" @mousedown.prevent @click="seleccionarCorreo(cliente)">
                    <strong>{{ cliente.nombre }}</strong><span>{{ cliente.email }}</span>
                  </button>
                </li>
              </ul>
              <p v-else>Sin coincidencias. Puedes escribir el nombre manualmente.</p>
            </div>

            <span v-if="errors.first('correoReporte.destinatario')" class="reporte-correo__validacion" role="alert"><AlertCircleIcon size="13" aria-hidden="true" />{{ errors.first('correoReporte.destinatario') }}</span></div>
        </div>
        <div class="reporte-correo__campo">
          <label for="reporte-email">Para</label>
          <div class="reporte-correo__entrada reporte-correo__buscador"><vs-input id="reporte-email" ref="email" name="email_address" data-vv-scope="correoReporte"
            data-vv-as="correo electrónico" v-validate="'email|max:75'" v-model="datos.email_address" type="email"
            maxlength="75" :disabled="isLoading" class="w-full" placeholder="Correo o inicio del correo de un cliente"
            autocomplete="off" role="combobox" aria-autocomplete="list" :aria-expanded="sugerenciasAbiertas && campoBusqueda === 'email'"
            aria-controls="correos-clientes" :aria-activedescendant="campoBusqueda === 'email' && sugerenciaActiva >= 0 ? 'correo-cliente-' + sugerenciaActiva : null"
            @keydown.native="teclasCorreo" @focus.native.capture="enfocarBusqueda('email')" @focusout.native="cerrarSugerencias" />
            <div v-if="sugerenciasAbiertas && campoBusqueda === 'email'" class="reporte-correo__sugerencias">
              <p v-if="buscandoCorreo" role="status">Buscando clientes…</p>
              <p v-else-if="errorBusquedaCorreo" role="status">{{ errorBusquedaCorreo }}</p>
              <ul v-else-if="sugerenciasCorreo.length" id="correos-clientes" role="listbox" aria-label="Correos de clientes">
                <li v-for="(cliente, indice) in sugerenciasCorreo" :key="cliente.id" role="presentation">
                  <button type="button" :id="'correo-cliente-' + indice" role="option" :aria-selected="indice === sugerenciaActiva"
                    :class="{ 'es-activa': indice === sugerenciaActiva }" tabindex="-1" @mousedown.prevent @click="seleccionarCorreo(cliente)">
                    <strong>{{ cliente.nombre }}</strong><span>{{ cliente.email }}</span>
                  </button>
                </li>
              </ul>
              <p v-else>Sin coincidencias. Puedes escribir el correo completo.</p>
            </div>
            <span v-if="errorDestinatario" class="reporte-correo__validacion" role="alert"><AlertCircleIcon size="13" aria-hidden="true" />{{ errorDestinatario }}</span>
            <span v-if="errors.first('correoReporte.email_address')" class="reporte-correo__validacion" role="alert"><AlertCircleIcon size="13" aria-hidden="true" />{{ errors.first('correoReporte.email_address') }}</span></div>
          <button type="button" class="reporte-correo__agregar" :disabled="isLoading || (destinatarios.length >= 20 && indiceEdicion < 0)" @click="agregarDestinatario" :aria-label="indiceEdicion >= 0 ? 'Guardar destinatario' : 'Agregar destinatario'"><CheckIcon v-if="indiceEdicion >= 0" size="16" /><PlusIcon v-else size="16" /> {{ indiceEdicion >= 0 ? 'Guardar' : 'Agregar' }}</button>
        </div>
        <div v-if="nombrePorConfirmar" class="reporte-correo__nombre-opciones" role="status">
          <p>Ya escribiste <strong>{{ datos.destinatario }}</strong>. El nombre del cliente es <strong>{{ nombrePorConfirmar.nombre }}</strong>.</p>
          <div><button type="button" :disabled="isLoading" @click="resolverNombre(false)">Mantener el nombre escrito</button>
          <button type="button" :disabled="isLoading" @click="resolverNombre(true)">Usar el nombre del cliente</button></div>
        </div>
        <div v-if="correoPorConfirmar" class="reporte-correo__nombre-opciones" role="status">
          <p>Ya escribiste <strong>{{ datos.email_address }}</strong>. El correo del cliente es <strong>{{ correoPorConfirmar.email }}</strong>.</p>
          <div><button type="button" :disabled="isLoading" @click="resolverCorreo(false)">Mantener el correo escrito</button>
          <button type="button" :disabled="isLoading" @click="resolverCorreo(true)">Usar el correo del cliente</button></div>
        </div>
        <div v-if="indiceEdicion >= 0" class="reporte-correo__edicion" role="status">
          <Edit2Icon size="13" aria-hidden="true" /> Editando destinatario. Guarda los cambios o
          <button type="button" @click="cancelarEdicion" :disabled="isLoading">cancela la edición</button>.
        </div>
        <div v-if="destinatarios.length" class="reporte-correo__destinatarios" aria-label="Destinatarios agregados">
          <span v-for="(persona, indice) in destinatarios" :key="persona.email.toLowerCase()" class="reporte-correo__destinatario" :class="{ 'es-editando': indice === indiceEdicion }" :title="persona.nombre || persona.email">
            <button type="button" class="reporte-correo__editar-texto" :disabled="isLoading" @click="editarDestinatario(indice)" :aria-label="'Editar ' + persona.email">{{ persona.email }}</button>
            <button type="button" :disabled="isLoading" @click="editarDestinatario(indice)" :aria-label="'Editar ' + persona.email" title="Editar correo"><Edit2Icon size="13" /></button>
            <button type="button" :disabled="isLoading" @click="quitarDestinatario(indice)" :aria-label="'Quitar ' + persona.email" title="Quitar correo"><XIcon size="13" /></button>
          </span>
        </div>
        <p class="reporte-correo__privacidad">Hasta 20 destinatarios. Si agregas varios, se enviarán en copia oculta.</p>
        <div class="reporte-correo__campo">
          <label for="reporte-asunto">Asunto</label>
          <div class="reporte-correo__entrada"><vs-input id="reporte-asunto" name="asunto" data-vv-scope="correoReporte"
            data-vv-as="asunto" v-validate="'required|max:150'" v-model="datos.asunto" maxlength="150" :disabled="isLoading" class="w-full" />
            <span v-if="errors.first('correoReporte.asunto')" class="reporte-correo__validacion" role="alert"><AlertCircleIcon size="13" aria-hidden="true" />{{ errors.first('correoReporte.asunto') }}</span></div>
        </div>
        <div class="reporte-correo__mensaje">
          <label for="reporte-mensaje">Mensaje <small>(opcional)</small></label>
          <VueEditor id="reporte-mensaje" v-model="datos.mensaje" :disabled="isLoading"
            :editor-toolbar="barraMensaje" :editor-options="opcionesMensaje"
            placeholder="Escribe un mensaje para acompañar el reporte…" />
          <div class="reporte-correo__mensaje-pie"><span class="reporte-correo__validacion" role="alert" v-if="longitudMensaje > 5000">El mensaje no debe superar 5000 caracteres.</span><span v-else>Formato de texto, listas y enlaces</span><span>{{ longitudMensaje }} / 5000</span></div>
        </div>
        <section class="reporte-correo__contenido">
          <p>Se enviará el reporte <strong>{{ titulo }}</strong> como archivo adjunto.</p>
          <fieldset class="reporte-correo__formatos" :disabled="isLoading">
            <legend>Formato del adjunto</legend>
            <label :class="['reporte-correo__formato', 'reporte-correo__formato--excel', { 'es-activo': datos.formato === 'excel' }]">
              <input type="radio" v-model="datos.formato" value="excel" name="formatoAdjunto" />
              <GridIcon size="22" aria-hidden="true" /><span><strong>Excel</strong><small>XLSX · Consultar y filtrar datos</small></span>
            </label>
            <label :class="['reporte-correo__formato', 'reporte-correo__formato--pdf', { 'es-activo': datos.formato === 'pdf' }]">
              <input type="radio" v-model="datos.formato" value="pdf" name="formatoAdjunto" />
              <FileTextIcon size="22" aria-hidden="true" /><span><strong>PDF</strong><small>Documento listo para imprimir</small></span>
            </label>
          </fieldset>
          <div :class="['reporte-correo__adjunto', { 'reporte-correo__adjunto--pdf': datos.formato === 'pdf' }]">
            <PaperclipIcon size="16" aria-hidden="true" /><span>{{ titulo }}.{{ datos.formato === 'pdf' ? 'pdf' : 'xlsx' }}</span>
          </div>
        </section>
        </div>
        <div v-if="error" class="reporte-correo__error-general" role="alert"><AlertCircleIcon size="16" aria-hidden="true" /><span>{{ error }}</span></div>
        <div class="reporte-correo__acciones">
          <vs-button color="primary" :disabled="isLoading" @click="confirmar"><SendIcon size="16" class="inline-block mr-2" />{{ isLoading ? 'Enviando…' : 'Enviar' }}</vs-button>
          <span class="reporte-correo__ayuda">Se solicitará confirmación antes de enviar.</span>
          <button type="button" class="reporte-correo__cancelar" :disabled="isLoading" @click="cerrar">Cancelar</button>
        </div>
      </form>
      <ConfirmarAceptar :persistente="true" v-if="openConfirmarAceptar" :show="openConfirmarAceptar" :callback-on-success="enviarReporte"
        @closeVerificar="openConfirmarAceptar = false" :accion="'Enviar el reporte ' + (pendiente && pendiente.formato === 'pdf' ? 'PDF' : 'Excel') + ' a ' + (pendiente && pendiente.destinatarios.length > 1 ? pendiente.destinatarios.length + ' destinatarios' : (pendiente ? pendiente.email_address : ''))"
        confirmarButton="Enviar Documento" z_index="z-index90k" />
    </vs-popup>
  </div>
</template>
<script>
import { MailIcon, GridIcon, PaperclipIcon, FileTextIcon, SendIcon, PlusIcon, XIcon, Edit2Icon, CheckIcon, AlertCircleIcon } from "vue-feather-icons";
import ConfirmarAceptar from "./confirmarAceptar.vue";
import { VueEditor } from "vue2-editor";
import clientes from "../../services/clientes";
export default {
  name: "EnviarReporteCorreo",
  components: { MailIcon, GridIcon, PaperclipIcon, FileTextIcon, SendIcon, PlusIcon, XIcon, Edit2Icon, CheckIcon, AlertCircleIcon, ConfirmarAceptar, VueEditor },
  props: {
    show: { type: Boolean, required: true },
    token: { type: String, required: true },
    titulo: { type: String, default: "Reporte" },
    enviar: { type: Function, required: true },
  },
  data() {
    return {
      nombrePorConfirmar: null, correoPorConfirmar: null, campoBusqueda: "email", campoEnFoco: null, nombreElegido: "",
      destinatarios: [], indiceEdicion: -1, entradaAntesEditar: null, errorDestinatario: "",
      sugerenciasCorreo: [], sugerenciasAbiertas: false, sugerenciaActiva: -1,
      buscandoCorreo: false, errorBusquedaCorreo: "", temporizadorCorreo: null, secuenciaCorreo: 0, correoElegido: "",
      barraMensaje: [['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['link', 'clean']],
      opcionesMensaje: { formats: ['bold', 'italic', 'underline', 'list', 'bullet', 'link'] },
      datos: { destinatario: "", email_address: "", asunto: this.titulo || "Reporte", formato: "excel", mensaje: "" }, isLoading: false, error: "", pendiente: null, openConfirmarAceptar: false };
  },
  computed: {
    longitudMensaje() {
      const contenido = document.createElement("div");
      contenido.innerHTML = this.datos.mensaje || "";
      return (contenido.textContent || "").trim().length;
    },
  },
  watch: {
    "datos.destinatario"(valor) {
      if (this.correoPorConfirmar && valor !== this.correoPorConfirmar.nombre) this.correoPorConfirmar = null;
      if (this.campoEnFoco === "nombre") this.programarBusquedaCorreo(valor, "nombre");
    },
    "datos.email_address"(valor) { if (this.nombrePorConfirmar && valor !== this.nombrePorConfirmar.email) this.nombrePorConfirmar = null; this.errorDestinatario = ""; if (this.campoEnFoco === "email") this.programarBusquedaCorreo(valor, "email"); },
    show: {
      immediate: true,
      handler(valor) {
        if (valor) this.$popupManager.register(this, this.cerrar, "destinatario");
        else this.$popupManager.unregister(this.$options.name);
      },
    },
  },
  methods: {
    // Interceptar solo el fondo; los controles internos y la X mantienen su funcionamiento.
    bloquearCierreExterior(evento) {
      if (evento.target.classList && evento.target.classList.contains("vs-popup--background")) {
        evento.preventDefault(); evento.stopImmediatePropagation();
      }
    },
    programarBusquedaCorreo(valor, campo = "email") {
      this.campoBusqueda = campo;
      clearTimeout(this.temporizadorCorreo);
      const secuencia = ++this.secuenciaCorreo;
      this.sugerenciaActiva = -1;
      this.sugerenciasCorreo = [];
      this.errorBusquedaCorreo = "";
      this.buscandoCorreo = false;
      const texto = (valor || "").trim();
      if (!this.show || texto.length < 3 || (campo === "email" ? texto === this.correoElegido : texto === this.nombreElegido) || this.isLoading) {
        this.sugerenciasAbiertas = false; return;
      }
      this.correoElegido = "";
      this.sugerenciasAbiertas = true;
      this.buscandoCorreo = true;
      this.temporizadorCorreo = setTimeout(async () => {
        try {
          const respuesta = await clientes.buscarCorreosReportes(texto, campo);
          if (!this.show || secuencia !== this.secuenciaCorreo) return;
          this.sugerenciasCorreo = Array.isArray(respuesta.data) ? respuesta.data : [];
        } catch (_) {
          if (secuencia === this.secuenciaCorreo) this.errorBusquedaCorreo = "No se pudieron obtener sugerencias. Puedes escribir el correo manualmente.";
        } finally {
          if (secuencia === this.secuenciaCorreo) this.buscandoCorreo = false;
        }
      }, 350);
    },
    enfocarBusqueda(campo) {
      this.campoEnFoco = campo;
      this.programarBusquedaCorreo(campo === "nombre" ? this.datos.destinatario : this.datos.email_address, campo);
    },
    cerrarSugerencias() { this.sugerenciasAbiertas = false; },
    teclasNombre(evento) {
      if (evento.key === "Enter" && !(this.sugerenciasAbiertas && this.sugerenciaActiva >= 0)) { evento.preventDefault(); return; }
      this.teclasCorreo(evento);
    },
    seleccionarCorreo(cliente) {
      clearTimeout(this.temporizadorCorreo);
      this.secuenciaCorreo++;
      if (this.campoBusqueda === "nombre") {
        const actual = (this.datos.email_address || "").trim();
        this.nombreElegido = (cliente.nombre || "").trim().slice(0, 75);
        this.datos.destinatario = this.nombreElegido;
        this.nombrePorConfirmar = null;
        this.correoPorConfirmar = null;
        if (actual && actual.toLowerCase() !== cliente.email.toLowerCase()) {
          this.correoPorConfirmar = { email: cliente.email, nombre: this.nombreElegido };
        } else {
          this.correoElegido = cliente.email;
          this.datos.email_address = cliente.email;
        }
      } else {
        this.correoPorConfirmar = null;
        this.correoElegido = cliente.email;
        this.datos.email_address = cliente.email;
        const nombreCliente = (cliente.nombre || "").trim().slice(0, 75);
        const nombreActual = (this.datos.destinatario || "").trim();
        this.nombrePorConfirmar = null;
        if (nombreActual && nombreCliente && nombreActual.toLocaleLowerCase() !== nombreCliente.toLocaleLowerCase()) {
          // No sobrescribir el nombre capturado: la decisión queda explícita en el formulario.
          this.nombrePorConfirmar = { nombre: nombreCliente, email: cliente.email };
        } else if (!nombreActual) {
          this.datos.destinatario = nombreCliente;
        }
      }
      this.sugerenciasAbiertas = false;
      this.buscandoCorreo = false;
    },
    resolverCorreo(usarCliente) {
      if (!this.correoPorConfirmar || this.isLoading) return;
      if (usarCliente) { this.correoElegido = this.correoPorConfirmar.email; this.datos.email_address = this.correoPorConfirmar.email; }
      this.correoPorConfirmar = null;
      this.errorDestinatario = "";
    },
    resolverNombre(usarCliente) {
      if (!this.nombrePorConfirmar || this.isLoading) return;
      if (usarCliente) this.datos.destinatario = this.nombrePorConfirmar.nombre;
      this.nombrePorConfirmar = null;
      this.errorDestinatario = "";
    },
    teclasCorreo(evento) {
      if (evento.key === "Enter" && !(this.sugerenciasAbiertas && this.sugerenciaActiva >= 0)) {
        evento.preventDefault(); this.agregarDestinatario(); return;
      }
      if (!this.sugerenciasAbiertas) return;
      if (evento.key === "Escape") { evento.stopPropagation(); evento.preventDefault(); this.cerrarSugerencias(); }
      if (!this.sugerenciasCorreo.length) return;
      if (evento.key === "ArrowDown" || evento.key === "ArrowUp") {
        evento.preventDefault();
        const n = this.sugerenciasCorreo.length;
        this.sugerenciaActiva = evento.key === "ArrowDown" ? (this.sugerenciaActiva + 1) % n : (this.sugerenciaActiva <= 0 ? n - 1 : this.sugerenciaActiva - 1);
      } else if (evento.key === "Enter" && this.sugerenciaActiva >= 0) {
        evento.preventDefault(); this.seleccionarCorreo(this.sugerenciasCorreo[this.sugerenciaActiva]);
      }
    },
    alCambiarVisibilidad(valor) { if (!valor) this.cerrar(); },
    cerrar() { if (!this.isLoading) this.$emit("close"); },
    editarDestinatario(indice) {
      if (this.isLoading || !this.destinatarios[indice]) return;
      if (this.indiceEdicion < 0) this.entradaAntesEditar = { nombre: this.datos.destinatario, email: this.datos.email_address };
      this.nombrePorConfirmar = null;
      this.correoPorConfirmar = null;
      this.indiceEdicion = indice;
      this.correoElegido = this.destinatarios[indice].email;
      this.datos.destinatario = this.destinatarios[indice].nombre || "";
      this.datos.email_address = this.destinatarios[indice].email;
      this.errorDestinatario = "";
      if (this.$nextTick) this.$nextTick(() => {
        const componente = this.$refs.email;
        const input = componente && componente.$el.querySelector("input");
        if (input) { input.focus(); input.select(); }
      });
    },
    cancelarEdicion() {
      if (this.isLoading) return;
      this.nombrePorConfirmar = null;
      this.correoPorConfirmar = null;
      this.indiceEdicion = -1;
      this.datos.destinatario = this.entradaAntesEditar ? this.entradaAntesEditar.nombre : "";
      this.datos.email_address = this.entradaAntesEditar ? this.entradaAntesEditar.email : "";
      this.entradaAntesEditar = null;
      this.errorDestinatario = "";
      this.cerrarSugerencias();
    },
    quitarDestinatario(indice) {
      if (this.isLoading) return;
      if (indice === this.indiceEdicion) this.cancelarEdicion();
      else if (indice < this.indiceEdicion) this.indiceEdicion--;
      this.destinatarios.splice(indice, 1);
    },
    async agregarDestinatario() {
      if (this.isLoading) return false;
      if (this.correoPorConfirmar) { this.errorDestinatario = "Elige si deseas mantener el correo escrito o usar el del cliente."; return false; }
      if (this.nombrePorConfirmar) {
        this.errorDestinatario = "Elige si deseas mantener el nombre escrito o usar el del cliente.";
        return false;
      }
      this.error = "";
      this.errorDestinatario = "";
      const email = (this.datos.email_address || "").trim();
      if (!email) { this.errorDestinatario = "Escribe un correo para continuar."; return false; }
      if (!await this.$validator.validateAll("correoReporte")) return false;
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { this.errorDestinatario = "Revisa el formato del correo."; return false; }
      if (this.destinatarios.some((p, i) => i !== this.indiceEdicion && p.email.toLowerCase() === email.toLowerCase())) {
        this.errorDestinatario = "Ese correo ya está en la lista."; return false;
      }
      const persona = { nombre: (this.datos.destinatario || "").trim(), email };
      if (this.indiceEdicion >= 0) {
        // Reemplazar solo después de validar: un error o cancelación conserva el correo original.
        this.destinatarios.splice(this.indiceEdicion, 1, persona);
        this.cancelarEdicion();
      } else {
        if (this.destinatarios.length >= 20) { this.errorDestinatario = "Puedes agregar hasta 20 destinatarios."; return false; }
        this.destinatarios.push(persona);
        this.datos.email_address = "";
        this.datos.destinatario = "";
      }
      this.cerrarSugerencias();
      return true;
    },
    async confirmar() {
      if (this.isLoading || this.openConfirmarAceptar || !this.show) return;
      this.error = "";
      if (this.longitudMensaje > 5000) { this.error = "El mensaje no debe superar 5000 caracteres."; return; }
      if (this.indiceEdicion >= 0 && !await this.agregarDestinatario()) return;
      if ((this.datos.email_address || "").trim() && !await this.agregarDestinatario()) return;
      if (!this.destinatarios.length) { this.error = "Agrega al menos un destinatario."; return; }
      if (!await this.$validator.validateAll("correoReporte")) return;
      const primero = this.destinatarios[0];
      this.pendiente = { destinatarios: this.destinatarios.map((p) => ({ nombre: p.nombre, email: p.email })), destinatario: primero.nombre || primero.email, email_address: primero.email, asunto: (this.datos.asunto || this.titulo || "Reporte").trim(), formato: this.datos.formato || "excel", mensaje: (this.datos.mensaje || "").trim() };
      this.openConfirmarAceptar = true;
    },
    async enviarReporte() {
      if (!this.pendiente || this.isLoading || !this.show) return;
      const datos = this.pendiente;
      this.pendiente = null;
      this.isLoading = true;
      this.$emit("ocupado", true);
      this.error = "";
      this.$vs.loading();
      let enviado = false;
      try {
        const respuesta = await this.enviar(this.token, datos);
        if (respuesta !== 1) throw new Error("El servidor no confirmó el envío del correo.");
        this.$vs.notify({ title: "Enviar documento por correo", text: "Se ha enviado el reporte " + (datos.formato === "pdf" ? "PDF." : "Excel."), color: "success", iconPack: "feather", icon: "icon-mail", time: 6000 });
        enviado = true;
      } catch (e) {
        const detalle = e.response && e.response.data;
        const errores = detalle && detalle.errors;
        const primeraClave = errores && Object.keys(errores)[0];
        const validacion = primeraClave && errores[primeraClave][0];
        if (e.response && e.response.status === 429) {
          const espera = Math.max(1, Math.ceil(Number((e.response.headers || {})["retry-after"]) || 60));
          this.error = "Se alcanzó el límite temporal de envíos. Espera " + espera + " segundos antes de reintentar.";
        } else {
          this.error = validacion || (detalle && (detalle.message || (typeof detalle.error === "string" ? detalle.error : ""))) || e.message || "No fue posible enviar el reporte. Reintente.";
        }
      } finally {
        this.isLoading = false;
        this.$vs.loading.close();
        this.$emit("ocupado", false);
        if (enviado) this.$emit("close");
      }
    },
  },
  beforeDestroy() { clearTimeout(this.temporizadorCorreo); this.secuenciaCorreo++; this.$popupManager.unregister(this.$options.name); },
};
</script>
