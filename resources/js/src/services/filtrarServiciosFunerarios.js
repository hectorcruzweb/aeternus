import axios from '@/axios.js';
/** Servicio exclusivo del nuevo listado; cancelación propia para no interrumpir
 * peticiones de formularios o expedientes que usan @services/funeraria. */
export default {
    reporte(parametros) { return axios.get('/funeraria/servicios/reporte-filtrado', {params:parametros}); },
    enviarReporte(token, datos) {
        return axios.post('/funeraria/servicios/reporte-filtrado/' + encodeURIComponent(token) + '/correo', datos);
    },
    descargar(token, formato) { return axios.get('/funeraria/servicios/reporte-filtrado/'+encodeURIComponent(token)+'/'+formato, {responseType:'blob'}); },
    listar(parametros, cancelToken) {
        return axios.get('/funeraria/servicios/filtrar', {params:parametros, cancelToken});
    }
};
