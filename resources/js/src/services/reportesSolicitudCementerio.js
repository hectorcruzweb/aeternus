import axios from "@/axios.js";

// Transporte exclusivo de la solicitud tipo 1. El visor compartido no conoce tablas ni rutas.
const base = "/cementerio/reportes/solicitud";
export default {
  /** Devuelve HTML/metadatos de venta_id; no descarga un PDF al abrir. */
  visualizar(parametros) { return axios.get(base, { params: parametros }); },
  /** Genera y descarga el PDF de la instantánea privada del usuario. */
  pdf(token) { return axios.get(base + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
  /** Envía destinatarios, mensaje y formato PDF al nuevo circuito de correo. */
  correo(token, datos) { return axios.post(base + "/" + encodeURIComponent(token) + "/correo", datos); },
};
