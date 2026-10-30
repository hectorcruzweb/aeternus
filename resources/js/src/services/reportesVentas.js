import axios from "@/axios.js";
const base = "/operaciones/reportes/ventas-saldos";
const general = "/operaciones/reportes/general";
export default {
  visualizar(parametros) {
    const rutasFuneraria = {
      3: "/funeraria/reportes/ventas-saldos",
      4: "/funeraria/reportes/planes/ventas-saldos",
      5: "/funeraria/reportes/ventas-generales/ventas-saldos",
    };
    const url = parametros.empresa_operaciones_id === "todas" ? general : (rutasFuneraria[Number(parametros.empresa_operaciones_id)] || base);
    return axios.get(url, { params: parametros });
  },
  completo(token) { return axios.get(general + "/" + encodeURIComponent(token) + "/excel-completo", { responseType: "blob" }); },
  excel(token, esGeneral = false) { return axios.get((esGeneral ? general : base) + "/" + encodeURIComponent(token) + "/excel", { responseType: "blob" }); },
  pdf(token, esGeneral = false) { return axios.get((esGeneral ? general : base) + "/" + encodeURIComponent(token) + "/pdf", { responseType: "blob" }); },
  correo(token, datos, esGeneral = false) { return axios.post((esGeneral ? general : base) + "/" + encodeURIComponent(token) + "/correo", datos); },
};
