import axios from "@/axios.js";
/** Recibo general: HTML para consultar y PDF solo al descargar o enviar. */
const ruta = "/pagos/reportes/recibo";
export default {
    visualizar(parametros) { return axios.get(ruta, { params: parametros }); },
    pdf(token) { return axios.get(ruta + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
    correo(token, datos) { return axios.post(ruta + "/" + encodeURIComponent(token) + "/correo", datos); },
};
