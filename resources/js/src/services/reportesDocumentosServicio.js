import axios from "@/axios.js";

/** Catálogo del expediente tipo 3; comparte tres rutas para sus documentos. */
function ruta(documento) {
    if (["contrato", "recibo", "solicitud", "autorizacion", "certificado", "guia", "orden", "embalsamiento", "cremacion", "entrega-cenizas", "no-embalsamiento", "constancia-cremacion", "entrega-acta"].indexOf(documento) === -1) throw new Error("Documento no disponible en el visor.");
    return "/funeraria/servicios/reportes/" + documento;
}
export default {
    /** HTML inicial; id_servicio identifica la solicitud, no operaciones.id. */
    visualizar(documento, parametros) { return axios.get(ruta(documento), { params: parametros }); },
    /** Generación PDF exclusivamente cuando el visor la solicita. */
    pdf(documento, token) { return axios.get(ruta(documento) + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
    /** Usa el nuevo modal de correo y su validación compartida. */
    correo(documento, token, datos) { return axios.post(ruta(documento) + "/" + encodeURIComponent(token) + "/correo", datos); },
};
