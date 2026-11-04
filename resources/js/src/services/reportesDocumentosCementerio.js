import axios from "@/axios.js";

/** Documentos del expediente y catálogo de cuotas migrados al visor. Mantener aquí el catálogo permitido del cliente. */
function ruta(documento) {
  if (["solicitud", "convenio", "titulo", "estado-cuenta", "referencias", "reglamento", "acuse-cancelacion", "servicios-propiedad", "recibo", "cuotas", "planes-venta"].indexOf(documento) === -1) throw new Error("Documento no disponible en el visor.");
  return "/cementerio/reportes/" + documento;
}
export default {
  /** Obtiene HTML y token de venta_id, sin generar PDF ni descargar un Blob. */
  visualizar(documento, parametros) { return axios.get(ruta(documento), { params: parametros }); },
  /** Genera bajo demanda el PDF con las opciones del documento seleccionado. */
  pdf(documento, token) { return axios.get(ruta(documento) + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
  /** Envía la instantánea PDF mediante el nuevo formulario de correo. */
  correo(documento, token, datos) { return axios.post(ruta(documento) + "/" + encodeURIComponent(token) + "/correo", datos); },
};
