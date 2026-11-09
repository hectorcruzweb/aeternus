/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Cliente HTTP del módulo de clientes, incluida la búsqueda de correos/nombres para los reportes.
 * Dónde ajustar: La búsqueda devuelve sugerencias; la confirmación y la lista final se manejan en
 * EnviarReporteCorreo.vue.
 * Relacionado: ClientesController; EnviarReporteCorreo.vue
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */
import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,
    // El visor recibe HTML; los binarios se generan solo al solicitar la descarga.
    reporteListado(parametros) { return axios.get('/clientes/reportes/listado', { params: parametros }); },
    descargarListado(token, formato) { return axios.get('/clientes/reportes/listado/' + token + '/' + formato, { responseType: 'blob' }); },

    /** Consulta exclusiva del listado nuevo. Cancelación por instancia del componente,
     * sin interferir con get_clientes ni con los buscadores de otros módulos. */
    filtrarClientes(parametros, cancelToken) {
        return axios.get('/clientes/filtrar', { params: parametros, cancelToken });
    },

    // Autocompletado limitado para destinatarios de reportes.
    /**
     * Detalle del método: Devuelve la promesa Axios de coincidencias de clientes para destinatarios; no
     * envía correo.
     * Entradas: q (texto de búsqueda de clientes); campo = "email" (campo del dato o indicador que se
     * evalúa).
     */
    buscarCorreosReportes(q, campo = "email") {
        return axios.get("/clientes/reportes/correos", { params: { q, campo } });
    },
    /**obitne la infomacion del paginado */
    get_clientes(param) {
        let self = this;
        return new Promise((resolve, reject) => {
            axios
                .get("/clientes/get_clientes", {
                    cancelToken: new CancelToken((c) => {
                        self.cancel = c;
                    }),
                    params: param,
                })
                .then((response) => {
                    resolve(response);
                })
                .catch((error) => {
                    if (axiosSuper.isCancel(error)) {
                        reject(error.message);
                    } else {
                        reject(error);
                    }
                });
        });
    },

    //obtiene las nacionalidades de los clientes
    get_nacionalidades() {
        let call = "/clientes/get_nacionalidades";
        return new Promise((resolve, reject) => {
            axios
                .get(call)
                .then((response) => {
                    resolve(response);
                })
                .catch((error) => {
                    reject(error);
                });
        });
    },

    /**get datos del cliente por id */
    get_cliente_id(id) {
        let call = "/clientes/get_cliente_id";
        return new Promise((resolve, reject) => {
            axios
                .get(call, {
                    params: {
                        cliente_id: id,
                    },
                })
                .then((response) => {
                    resolve(response);
                })
                .catch((error) => {
                    reject(error);
                });
        });
    },

    guardar_cliente(datos) {
        let call = "/clientes/guardar_cliente";
        return new Promise((resolve, reject) => {
            axios
                .post(call, datos)
                .then((response) => {
                    resolve(response);
                })
                .catch((error) => {
                    reject(error);
                });
        });
    },
    /**modificacion de clientes */
    modificar_cliente(datos) {
        let call = "/clientes/modificar_cliente";
        return new Promise((resolve, reject) => {
            axios
                .post(call, datos)
                .then((response) => {
                    resolve(response);
                })
                .catch((error) => {
                    reject(error);
                });
        });
    },

    /**modificacion de clientes | baja logica */
    delete_cliente(datos) {
        let call = "/clientes/baja_cliente";
        return new Promise((resolve, reject) => {
            axios
                .post(call, datos)
                .then((response) => {
                    resolve(response);
                })
                .catch((error) => {
                    reject(error);
                });
        });
    },

    /**modificacion de clientes | baja logica */
    alta_cliente(datos) {
        let call = "/clientes/alta_cliente";
        return new Promise((resolve, reject) => {
            axios
                .post(call, datos)
                .then((response) => {
                    resolve(response);
                })
                .catch((error) => {
                    reject(error);
                });
        });
    },

    servicios_gratis(datos) {
        let call = "/clientes/servicios_gratis";
        return axios.post(call, datos);
    },

    /**obtiene la infomacion del paginado de clientes para segumientos*/
    async fetchClientes(params) {
        try {
            const response = await axios.get(
                "/clientes/get_clientes_seguimientos",
                {
                    params,
                }
            );
            return response.data; // return only the data
        } catch (error) {
            console.error("Error fetching clientes:", error);
            throw error; // propagate the error so Vue can handle it
        }
    },
};
