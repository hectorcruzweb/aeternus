import axios from '@/axios.js';
/** Consulta y reporte exclusivos del nuevo listado. No interfieren con pagos.cancel. */
export default {
    listar(parametros, cancelToken) { return axios.get('/pagos/filtrar', { params: parametros, cancelToken }); },
    reporte(parametros) { return axios.get('/pagos/reporte-filtrado', { params: parametros }); },
    descargar(token, formato) { return axios.get('/pagos/reporte-filtrado/' + encodeURIComponent(token) + '/' + formato, { responseType: 'blob' }); }
};
