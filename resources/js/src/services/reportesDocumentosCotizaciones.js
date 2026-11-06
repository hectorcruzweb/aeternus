import axios from "@/axios.js";
/** Cotización: previsualización HTML y PDF diferido mediante las rutas protegidas del módulo. */
const ruta = "/cotizaciones/reportes/cotizacion";
export default {
    visualizar(parametros) { return axios.get(ruta, { params: parametros }); },
    pdf(token) { return axios.get(ruta + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
    correo(token, datos) { return axios.post(ruta + "/" + encodeURIComponent(token) + "/correo", datos); },
};
