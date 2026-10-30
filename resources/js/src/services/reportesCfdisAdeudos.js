import axios from "@/axios.js";
const ruta = "/facturacion/reportes/adeudos";
export default {
  visualizar() { return axios.get(ruta); },
  excel(token) { return axios.get(ruta + "/" + encodeURIComponent(token) + "/excel", { responseType: "blob" }); },
  pdf(token) { return axios.get(ruta + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
  correo(token, datos) { return axios.post(ruta + "/" + encodeURIComponent(token) + "/correo", datos); },
};
