import axios from "@/axios.js";

// El token identifica una consulta privada del reporte; no sustituye la sesión API.
const ruta = "/facturacion/reportes/timbres";
export default {
  visualizar(recargaId = null) {
    return axios.get(ruta, { params: recargaId == null ? {} : { recarga_id: recargaId } });
  },
  excel(token, completo = false) {
    return axios.get(ruta + "/" + encodeURIComponent(token) + "/excel", {
      params: { completo: completo ? 1 : 0 }, responseType: "blob",
    });
  },
  pdf(token) {
    return axios.get(ruta + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" });
  },
  correo(token, datos) {
    return axios.post(ruta + "/" + encodeURIComponent(token) + "/correo", datos);
  },
};
