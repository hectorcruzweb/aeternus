<template>
  <div class="centerx">
    <vs-popup
      class="forms-popup visor-reportes z-index58k"
      :fullscreen="true"
      title="Vista previa del reporte"
      :active="show"
      ref="VisorReportes"
      @click.native.capture="bloquearCierreExterior"
      @update:active="alCambiarVisibilidad"
    >
      <div class="visor-reportes__barra">
        <div class="visor-reportes__titulo">
          <strong>{{ reporte ? reporte.titulo : "Preparando reporte" }}</strong>
          <span v-if="reporte"
            >{{ reporte.totalRegistros }} registros · Vista previa del
            reporte</span
          >
        </div>
        <div class="visor-reportes__acciones">
          <button
            v-if="mostrarVolverResumen"
            type="button"
            class="visor-reportes__boton visor-reportes__boton--volver"
            :disabled="isLoading || descargando || enviando"
            @click="$emit('volver-resumen')"
          >
            <ArrowLeftIcon size="18" aria-hidden="true" /> Volver al resumen
            general
          </button>
          <button
            v-if="
              descargarCompleto && reporte && reporte.excelCompletoDisponible
            "
            type="button"
            class="visor-reportes__boton visor-reportes__boton--excel"
            :disabled="isLoading || descargando || enviando"
            @click="descargarFormato('completo')"
            title="Genera todos los desgloses con los datos actuales"
          >
            <GridIcon size="17" />
            {{
              descargando && formatoDescarga === "completo"
                ? "Generando detalle…"
                : "Excel detallado completo"
            }}
          </button>
          <button
            type="button"
            class="visor-reportes__boton"
            :disabled="isLoading || descargando || enviando"
            @click="consultar"
          >
            Actualizar
          </button>
          <button
            type="button"
            class="visor-reportes__boton"
            :disabled="isLoading || !documentoListo"
            @click="imprimir"
          >
            <PrinterIcon size="15" /> Imprimir
          </button>
          <button
            v-if="enviarCorreo"
            type="button"
            class="visor-reportes__boton"
            :disabled="isLoading || enviando || !reporte"
            :aria-expanded="mostrarCorreo"
            @click="mostrarCorreo = !mostrarCorreo"
          >
            <MailIcon size="16" /> Enviar por correo
          </button>
          <button
            v-if="descargarPdf"
            type="button"
            class="visor-reportes__boton visor-reportes__boton--pdf"
            :disabled="isLoading || descargando || enviando || !reporte"
            @click="descargarEnPdf"
          >
            <FileTextIcon size="17" aria-hidden="true" /><span
              class="visor-reportes__formato"
              aria-hidden="true"
              >PDF</span
            >
            {{
              descargando && formatoDescarga === "pdf"
                ? "Generando PDF…"
                : "Descargar PDF"
            }}
          </button>
          <button
            v-if="!reporte || !reporte.soloPdf"
            ref="descarga"
            type="button"
            class="visor-reportes__boton visor-reportes__boton--principal visor-reportes__boton--excel"
            :disabled="isLoading || descargando || enviando || !reporte"
            @click="descargar"
          >
            <GridIcon size="17" aria-hidden="true" /><span
              class="visor-reportes__formato"
              aria-hidden="true"
              >XLSX</span
            >
            {{
              descargando && formatoDescarga === "excel"
                ? "Generando Excel…"
                : "Descargar Excel"
            }}
          </button>
        </div>
      </div>
      <p v-if="errorDescarga" class="visor-reportes__nota" role="alert">
        {{ errorDescarga }}
      </p>
      <div
        v-if="isLoading"
        ref="carga"
        class="visor-reportes__carga"
        role="status"
        aria-live="polite"
      >
        Generando el reporte. Espere un momento…
      </div>
      <div v-else-if="error" class="visor-reportes__error" role="alert">
        <p>{{ error }}</p>
        <button type="button" class="visor-reportes__boton" @click="consultar">
          Reintentar
        </button>
      </div>
      <div
        v-else-if="reporte && !reporte.vistaPreviaDisponible"
        class="visor-reportes__limite"
        role="status"
      >
        <strong>Reporte demasiado grande para visualizar</strong>
        <p>{{ reporte.mensaje }}</p>
        <p>
          {{ reporte.totalRegistros }} registros · Límite del visor:
          {{ reporte.limiteVisualizacion }}
        </p>
        <button
          type="button"
          class="visor-reportes__boton visor-reportes__boton--principal visor-reportes__boton--excel"
          :disabled="descargando || enviando"
          @click="descargar"
        >
          <GridIcon size="17" aria-hidden="true" /><span
            class="visor-reportes__formato"
            aria-hidden="true"
            >XLSX</span
          >
          {{
            descargando && formatoDescarga === "excel"
              ? "Generando Excel…"
              : "Descargar Excel completo"
          }}
        </button>
      </div>
      <template v-else-if="reporte">
        <nav
          v-if="reporte.hojas.length > 1"
          class="visor-reportes__hojas"
          aria-label="Hojas del reporte"
        >
          <button
            v-for="(hoja, indice) in reporte.hojas"
            :key="indice"
            type="button"
            class="visor-reportes__boton"
            :aria-pressed="hojaActiva === indice"
            @click="hojaActiva = indice"
          >
            {{ hoja.nombre }}
          </button>
        </nav>
        <!-- Sin scripts. Acceso al documento para imprimirlo desde el botón del padre. -->
        <iframe
          class="visor-reportes__documento"
          :title="'Hoja ' + reporte.hojas[hojaActiva].nombre"
          ref="documento"
          sandbox="allow-same-origin allow-modals"
          @load="documentoCargado"
          referrerpolicy="no-referrer"
          :srcdoc="documentoHtml"
        />
        <p class="visor-reportes__nota">
          <span v-if="reporte.excelCompletoDisponible"
            >El Excel y PDF del resumen contienen los totales mostrados. El
            Excel detallado completo actualiza los datos al generarse.</span
          >
          <span v-else
            >Vista HTML e impresión del reporte. El Excel se genera al
            descargar, con los mismos datos consultados. La consulta de descarga
            es válida por 15 minutos.</span
          >
        </p>
      </template>
      <EnviarReporteCorreo
        v-if="mostrarCorreo && reporte && enviarCorreo"
        :show="mostrarCorreo"
        :token="reporte.tokenDescarga"
        :titulo="reporte.titulo"
        :enviar="enviarCorreo"
        :solo-pdf="!!reporte.soloPdf"
        :detalle-completo-disponible="
          !!descargarCompleto && reporte.excelCompletoDisponible
        "
        @ocupado="enviando = $event"
        @close="mostrarCorreo = false"
      />
    </vs-popup>
  </div>
</template>
<script>
import {
  GridIcon,
  PrinterIcon,
  MailIcon,
  FileTextIcon,
  ArrowLeftIcon,
} from "vue-feather-icons";
import EnviarReporteCorreo from "./EnviarReporteCorreo.vue";
export default {
  name: "VisorReportes",
  components: {
    GridIcon,
    PrinterIcon,
    MailIcon,
    FileTextIcon,
    ArrowLeftIcon,
    EnviarReporteCorreo,
  },
  props: {
    show: { type: Boolean, default: false },
    // Vista HTML directa y token privado de la instantánea de datos.
    cargar: { type: Function, required: true },
    descargarArchivo: { type: Function, required: true },
    descargarPdf: { type: Function, default: null },
    enviarCorreo: { type: Function, default: null },
    descargarCompleto: { type: Function, default: null },
    mostrarVolverResumen: { type: Boolean, default: false },
  },
  data() {
    return {
      isLoading: false,
      reporte: null,
      archivo: null,
      archivoPdf: null,
      archivoCompleto: null,
      formatoDescarga: null,
      hojaActiva: 0,
      error: "",
      peticion: 0,
      documentoListo: false,
      descargando: false,
      errorDescarga: "",
      mostrarCorreo: false,
      enviando: false,
    };
  },
  computed: {
    documentoHtml() {
      if (!this.reporte || !this.reporte.hojas.length) return "";
      const cabecera =
        "<meta http-equiv=\"Content-Security-Policy\" content=\"default-src 'none'; img-src data:; style-src 'unsafe-inline'\">" +
        "";
      return this.reporte.hojas[this.hojaActiva].html.replace(
        /<head[^>]*>/i,
        (head) => head + cabecera
      );
    },
  },
  watch: {
    hojaActiva() {
      this.documentoListo = false;
    },
    show: {
      immediate: true,
      handler(abierto) {
        if (abierto) {
          this.$nextTick(() => {
            if (!this.show) return;
            this.$popupManager.register(this, this.cerrar, "descarga");
            this.consultar();
          });
        } else {
          this.desvincularDocumento();
          this.peticion++;
          this.isLoading = false;
          this.reporte = null;
          this.documentoListo = false;
          this.archivo = null;
          this.archivoPdf = null;
          this.archivoCompleto = null;
          this.descargando = false;
          this.errorDescarga = "";
          this.mostrarCorreo = false;
          this.$popupManager.unregister(this.$options.name);
        }
      },
    },
  },
  methods: {
    desvincularDocumento() {
      if (this._documentoReportes)
        this._documentoReportes.removeEventListener(
          "click",
          this.interceptarReporteIndividual
        );
      this._documentoReportes = null;
    },
    documentoCargado() {
      this.documentoListo = !!this.reporte;
      this.desvincularDocumento();
      const marco = this.$refs.documento;
      if (marco && marco.contentDocument) {
        this._documentoReportes = marco.contentDocument;
        this._documentoReportes.addEventListener(
          "click",
          this.interceptarReporteIndividual
        );
      }
    },
    // El iframe no ejecuta scripts: el padre atiende solo los enlaces autorizados del resumen.
    interceptarReporteIndividual(evento) {
      const nodo = evento.target.closest
        ? evento.target
        : evento.target.parentElement;
      // Navegación genérica: únicamente IDs incluidos por el servidor en esta consulta.
      const relacionado = nodo && nodo.closest("[data-reporte-relacionado]");
      if (relacionado) {
        evento.preventDefault();
        if (
          !this.reporte ||
          this.isLoading ||
          this.descargando ||
          this.enviando
        )
          return;
        const id = relacionado.getAttribute("data-reporte-relacionado");
        if ((this.reporte.reportesRelacionados || []).some((r) => r.id === id))
          this.$emit("abrir-relacionado", id);
        return;
      }
      const enlace = nodo && nodo.closest("[data-tipo-operacion]");
      if (!enlace) return;
      evento.preventDefault();
      if (!this.reporte || this.isLoading || this.descargando || this.enviando)
        return;
      const id = Number(enlace.getAttribute("data-tipo-operacion"));
      if (this.reporte.reportesIndividuales.some((f) => Number(f.id) === id))
        this.$emit("abrir-reporte", id);
    },
    // Interceptar solo el fondo; los controles internos y la X mantienen su funcionamiento.
    bloquearCierreExterior(evento) {
      if (
        evento.target.classList &&
        evento.target.classList.contains("vs-popup--background")
      ) {
        evento.preventDefault();
        evento.stopImmediatePropagation();
      }
    },
    alCambiarVisibilidad(valor) {
      if (!valor) this.cerrar();
    },
    cerrar() {
      if (this.enviando) return;
      this.$emit("close");
    },
    async consultar() {
      if (this.isLoading || this.enviando || !this.show) return;
      const id = ++this.peticion;
      this.isLoading = true;
      this.error = "";
      this.errorDescarga = "";
      this.desvincularDocumento();
      this.reporte = null;
      this.archivo = null;
      this.archivoPdf = null;
      this.archivoCompleto = null;
      this.hojaActiva = 0;
      this.documentoListo = false;
      let contenedor = null;
      try {
        await this.$nextTick();
        if (!this.show || id !== this.peticion) return;
        contenedor = this.$refs.carga;
        if (contenedor) this.$vs.loading({ container: contenedor, scale: 0.6 });
        const datos = await this.cargar();
        if (!this.show || id !== this.peticion) return;
        if (typeof datos === "string" && /<!doctype|<html/i.test(datos)) {
          throw new Error(
            "La ruta del reporte devolvió una página HTML. Revise la caché de rutas de Laravel y la sesión."
          );
        }
        if (
          !datos ||
          !Array.isArray(datos.hojas) ||
          (datos.vistaPreviaDisponible !== false &&
            (!datos.hojas.length ||
              !datos.hojas.every(
                (h) => typeof h.html === "string" && /<head[\s>]/i.test(h.html)
              ))) ||
          typeof datos.nombreArchivo !== "string" ||
          typeof datos.tokenDescarga !== "string" ||
          !/^[a-f0-9]{64}$/.test(datos.tokenDescarga)
        )
          throw new Error("El servidor no entregó un reporte válido.");
        // No se transporta ni decodifica un Excel al consultar.
        this.reporte = {
          vistaPreviaDisponible: datos.vistaPreviaDisponible !== false,
          soloPdf: !!datos.soloPdf,
          limiteVisualizacion: datos.limiteVisualizacion || 3500,
          mensaje:
            datos.mensaje ||
            "El reporte no se puede visualizar por su tamaño. Puede descargarlo completo.",
          titulo:
            typeof datos.titulo === "string" && datos.titulo.trim()
              ? datos.titulo
              : "Reporte",
          nombreArchivo: datos.nombreArchivo,
          hojas: datos.hojas,
          totalRegistros: datos.totalRegistros,
          tokenDescarga: datos.tokenDescarga,
          venceEn: datos.venceEn,
          excelCompletoDisponible: datos.excelCompletoDisponible === true,
          reportesRelacionados: Array.isArray(datos.reportesRelacionados)
            ? datos.reportesRelacionados
                .filter(
                  (r) =>
                    r &&
                    typeof r.id === "string" &&
                    /^[a-zA-Z0-9_-]{1,80}$/.test(r.id)
                )
                .map((r) => ({ id: r.id }))
            : [],
          reportesIndividuales: Array.isArray(datos.reportesIndividuales)
            ? datos.reportesIndividuales.filter((f) =>
                [1, 2, 3, 4, 5].includes(Number(f.id))
              )
            : [],
        };
      } catch (e) {
        if (this.show && id === this.peticion)
          this.error =
            (e.response && e.response.data && e.response.data.message) ||
            e.message ||
            "No fue posible generar el reporte.";
      } finally {
        if (contenedor) this.$vs.loading.close(contenedor);
        if (id === this.peticion) this.isLoading = false;
      }
    },
    imprimir() {
      if (this.isLoading || !this.documentoListo || !this.reporte) return;
      const marco = this.$refs.documento;
      if (!marco || !marco.contentWindow) return;
      // Imprime la hoja visible, nunca el dashboard ni una nueva consulta al servidor.
      marco.contentWindow.focus();
      marco.contentWindow.print();
    },
    descargar() {
      return this.descargarFormato("excel");
    },
    descargarEnPdf() {
      return this.descargarFormato("pdf");
    },
    async descargarFormato(formato) {
      if (!this.reporte || this.isLoading || this.descargando) return;
      const id = this.peticion;
      this.descargando = true;
      this.formatoDescarga = formato;
      this.errorDescarga = "";
      try {
        // Generar solo al solicitarlo y reutilizar el Blob en descargas posteriores.
        const esPdf = formato === "pdf";
        const completo = formato === "completo";
        const solicitar = completo
          ? this.descargarCompleto
          : esPdf
          ? this.descargarPdf
          : this.descargarArchivo;
        if (!solicitar)
          throw new Error("Este formato no está disponible para el reporte.");
        const blob =
          (completo
            ? this.archivoCompleto
            : esPdf
            ? this.archivoPdf
            : this.archivo) || (await solicitar(this.reporte.tokenDescarga));
        if (!this.show || id !== this.peticion) return;
        if (!(blob instanceof Blob))
          throw new Error("No se recibió un archivo válido.");
        if (completo) this.archivoCompleto = blob;
        else if (esPdf) this.archivoPdf = blob;
        else this.archivo = blob;
        const url = URL.createObjectURL(blob);
        const enlace = document.createElement("a");
        enlace.href = url;
        enlace.download = esPdf
          ? this.reporte.nombreArchivo.replace(/\.xlsx$/i, ".pdf")
          : completo
          ? "Detalle " + this.reporte.nombreArchivo
          : this.reporte.nombreArchivo;
        document.body.appendChild(enlace);
        enlace.click();
        enlace.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
      } catch (e) {
        let mensaje = e.message || "No fue posible descargar el reporte.";
        if (e.response && e.response.data instanceof Blob) {
          try {
            const detalle = JSON.parse(await e.response.data.text());
            mensaje = detalle.message || detalle.error || mensaje;
          } catch (_) {
            /* Mantener el mensaje original. */
          }
        }
        if (this.show && id === this.peticion) this.errorDescarga = mensaje;
      } finally {
        if (id === this.peticion) this.descargando = false;
      }
    },
  },
  beforeDestroy() {
    this.desvincularDocumento();
    this.peticion++;
    this.$popupManager.unregister(this.$options.name);
  },
};
</script>
