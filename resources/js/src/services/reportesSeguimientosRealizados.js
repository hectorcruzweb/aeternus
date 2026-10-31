import axios from "@/axios.js";
const base = "/seguimientos/reportes/realizados-mes";
// El mes se determina en el servidor; no depende del selector de la gráfica.
export default {
  visualizar() { return axios.get(base); },
  excel(token) { return axios.get(base + "/" + encodeURIComponent(token) + "/excel", { responseType: "blob" }); },
  pdf(token) { return axios.get(base + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
  correo(token, datos) { return axios.post(base + "/" + encodeURIComponent(token) + "/correo", datos); },
};
