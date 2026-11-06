import axios from "@/axios.js";
/** Documentos tipo 4: HTML inicial y PDF solo al descargar o enviar. */
function ruta(documento) {
    if (["solicitud", "convenio", "finiquito", "estado-cuenta", "talonario", "reglamento", "recibo", "catalogo", "plan"].indexOf(documento) === -1) throw new Error("Documento no disponible.");
    return "/funeraria/planes/reportes/" + documento;
}
export default {
    visualizar(documento, parametros) { return axios.get(ruta(documento), { params: parametros }); },
    pdf(documento, token) { return axios.get(ruta(documento) + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
    correo(documento, token, datos) { return axios.post(ruta(documento) + "/" + encodeURIComponent(token) + "/correo", datos); },
};
