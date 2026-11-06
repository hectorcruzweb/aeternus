import axios from "@/axios.js";
/** Catálogo tipo 5: HTML inicial; PDF únicamente al descargar o enviar. */
function ruta(documento) {
    if (["nota", "recibo"].indexOf(documento) === -1) throw new Error("Documento no disponible.");
    return "/funeraria/ventas-generales/reportes/" + documento;
}
export default {
    visualizar(documento, parametros) { return axios.get(ruta(documento), { params: parametros }); },
    pdf(documento, token) { return axios.get(ruta(documento) + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
    correo(documento, token, datos) { return axios.post(ruta(documento) + "/" + encodeURIComponent(token) + "/correo", datos); },
};
