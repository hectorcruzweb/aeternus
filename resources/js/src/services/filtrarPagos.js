import axios from '@/axios.js';
/** Consulta y reporte exclusivos del nuevo listado. No interfieren con pagos.cancel. */
export default {
    listar(parametros, cancelToken) { return axios.get('/pagos/filtrar', { params: parametros, cancelToken }); },
    reporte(parametros) { return axios.get('/pagos/reporte-filtrado', { params: parametros }); },
    enviarReporte(token, datos) {
        return axios.post('/pagos/reporte-filtrado/' + encodeURIComponent(token) + '/correo', datos);
    },
    descargar(token, formato) { return axios.get('/pagos/reporte-filtrado/' + encodeURIComponent(token) + '/' + formato, { responseType: 'blob' }); }
};
