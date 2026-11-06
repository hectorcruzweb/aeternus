import axios from "@/axios.js";
/** Consulta HTML del CFDI; PDF bajo demanda y envío con XML original en el servidor. */
const ruta = "/facturacion/documentos/cfdi";
export default {
    visualizar(parametros) { return axios.get(ruta, { params: parametros }); },
    pdf(token) { return axios.get(ruta + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
    correo(token, datos) { return axios.post(ruta + "/" + encodeURIComponent(token) + "/correo", datos); },
};
