/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Ventas/saldos individuales y general; elige endpoint de funeraria para tipos 3/4/5 y operaciones para
 * 1/2.
 * Dónde ajustar: visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token
 * recibido. correo envía datos al backend, no usa SMTP desde el navegador.
 * Relacionado: HomeInfo.vue; VisorReportes.vue; routes/api.php
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */
import axios from "@/axios.js";
const base = "/operaciones/reportes/ventas-saldos";
const general = "/operaciones/reportes/general";
export default {
  /**
   * Detalle del método: Devuelve la promesa Axios de HTML/metadatos/token del reporte, pasando el
   * contexto del módulo; no genera XLSX en el navegador.
   * Entradas: parametros (filtros y contexto del reporte a enviar al backend).
   */
  visualizar(parametros) {
    const rutasFuneraria = {
      3: "/funeraria/reportes/ventas-saldos",
      4: "/funeraria/reportes/planes/ventas-saldos",
      5: "/funeraria/reportes/ventas-generales/ventas-saldos",
    };
    const url = parametros.empresa_operaciones_id === "todas" ? general : (rutasFuneraria[Number(parametros.empresa_operaciones_id)] || base);
    return axios.get(url, { params: parametros });
  },
  /**
   * Detalle del método: Devuelve la promesa Axios del Blob XLSX detallado completo; puede tardar más que
   * el resumen.
   * Entradas: token (token privado del reporte, distinto al de sesión).
   */
  completo(token) { return axios.get(general + "/" + encodeURIComponent(token) + "/excel-completo", { responseType: "blob" }); },
  /**
   * Detalle del método: Devuelve la promesa Axios con Blob XLSX del token/contexto seleccionado; el
   * endpoint del resumen diario rechaza Excel.
   * Entradas: token (token privado del reporte, distinto al de sesión); esGeneral = false (selecciona
   * rutas del concentrado frente al individual).
   */
  excel(token, esGeneral = false) { return axios.get((esGeneral ? general : base) + "/" + encodeURIComponent(token) + "/excel", { responseType: "blob" }); },
  /**
   * Detalle del método: Devuelve la promesa Axios con Blob PDF del reporte identificado por token.
   * Entradas: token (token privado del reporte, distinto al de sesión); esGeneral = false (selecciona
   * rutas del concentrado frente al individual).
   */
  pdf(token, esGeneral = false) { return axios.get((esGeneral ? general : base) + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
  /**
   * Detalle del método: Devuelve la promesa Axios del envío con destinatarios, mensaje y formato; el
   * adjunto se genera y envía en el servidor.
   * Entradas: token (token privado del reporte, distinto al de sesión); datos (respuesta del módulo o
   * datos confirmados del envío, según el método); esGeneral = false (selecciona rutas del concentrado
   * frente al individual).
   */
  correo(token, datos, esGeneral = false) { return axios.post((esGeneral ? general : base) + "/" + encodeURIComponent(token) + "/correo", datos); },
};
