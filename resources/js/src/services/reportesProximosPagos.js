import axios from "@/axios.js";
const base = "/operaciones/reportes/proximos-pagos";
// Siete días naturales incluyendo hoy, calculados por el servidor.
export default {
  visualizar() { return axios.get(base); },
  excel(token) { return axios.get(base + "/" + encodeURIComponent(token) + "/excel", { responseType: "blob" }); },
  pdf(token) { return axios.get(base + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
  correo(token, datos) { return axios.post(base + "/" + encodeURIComponent(token) + "/correo", datos); },
};
