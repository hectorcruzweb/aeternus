/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Recargas de timbres y su historial.
 * Dónde ajustar: visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token
 * recibido. correo envía datos al backend, no usa SMTP desde el navegador.
 * Relacionado: HomeInfo.vue; VisorReportes.vue; routes/api.php
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */
import axios from "@/axios.js";

// El token identifica una consulta privada del reporte; no sustituye la sesión API.
const ruta = "/facturacion/reportes/timbres";
export default {
  /**
   * Detalle del método: Devuelve la promesa Axios de HTML/metadatos/token del reporte, pasando el
   * contexto del módulo; no genera XLSX en el navegador.
   * Entradas: recargaId = null (lote de timbres; null consulta el resumen).
   */
  visualizar(recargaId = null) {
    return axios.get(ruta, { params: recargaId == null ? {} : { recarga_id: recargaId } });
  },
  /**
   * Detalle del método: Devuelve la promesa Axios con Blob XLSX del token/contexto seleccionado; el
   * endpoint del resumen diario rechaza Excel.
   * Entradas: token (token privado del reporte, distinto al de sesión); completo = false (activa el
   * detalle completo cuando el reporte lo admite).
   */
  excel(token, completo = false) {
    return axios.get(ruta + "/" + encodeURIComponent(token) + "/excel", {
      params: { completo: completo ? 1 : 0 }, responseType: "blob",
    });
  },
  /**
   * Detalle del método: Devuelve la promesa Axios con Blob PDF del reporte identificado por token.
   * Entradas: token (token privado del reporte, distinto al de sesión).
   */
  pdf(token) {
    return axios.get(ruta + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" });
  },
  /**
   * Detalle del método: Devuelve la promesa Axios del envío con destinatarios, mensaje y formato; el
   * adjunto se genera y envía en el servidor.
   * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
   * datos confirmados del envío, según el método).
   */
  correo(token, datos) {
    return axios.post(ruta + "/" + encodeURIComponent(token) + "/correo", datos);
  },
};
