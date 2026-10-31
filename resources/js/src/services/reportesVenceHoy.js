/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Pagos programados pendientes que vencen hoy.
 * Dónde ajustar: visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token
 * recibido. correo envía datos al backend, no usa SMTP desde el navegador.
 * Relacionado: HomeInfo.vue; VisorReportes.vue; routes/api.php
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */
import axios from "@/axios.js";
const base = "/operaciones/reportes/vence-hoy";
// El servidor determina el día de consulta; no se envía una fecha del navegador.
export default {
  /**
   * Detalle del método: Devuelve la promesa Axios de HTML/metadatos/token del reporte, pasando el
   * contexto del módulo; no genera XLSX en el navegador.
   * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
   */
  visualizar() { return axios.get(base); },
  /**
   * Detalle del método: Devuelve la promesa Axios con Blob XLSX del token/contexto seleccionado; el
   * endpoint del resumen diario rechaza Excel.
   * Entradas: token (token privado del reporte, distinto al de sesión).
   */
  excel(token) { return axios.get(base + "/" + encodeURIComponent(token) + "/excel", { responseType: "blob" }); },
  /**
   * Detalle del método: Devuelve la promesa Axios con Blob PDF del reporte identificado por token.
   * Entradas: token (token privado del reporte, distinto al de sesión).
   */
  pdf(token) { return axios.get(base + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
  /**
   * Detalle del método: Devuelve la promesa Axios del envío con destinatarios, mensaje y formato; el
   * adjunto se genera y envía en el servidor.
   * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
   * datos confirmados del envío, según el método).
   */
  correo(token, datos) { return axios.post(base + "/" + encodeURIComponent(token) + "/correo", datos); },
};
