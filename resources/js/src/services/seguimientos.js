import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
import { error } from "@/plugins/logger";

export default {
    async getMotivos() {
        try {
            const response = await axios.get("/seguimientos/get_motivos");
            return response.data; // return only the data
        } catch (error) {
            throw error; // propagate the error so Vue can handle it
        }
    },
    async getMedios() {
        try {
            const response = await axios.get("/seguimientos/get_medios");
            return response.data; // return only the data
        } catch (error) {
            throw error; // propagate the error so Vue can handle it
        }
    },
    async getMotivosCancelacion() {
        try {
            const response = await axios.get(
                "/seguimientos/get_motivos_cancelacion"
            );
            return response.data; // return only the data
        } catch (error) {
            throw error; // propagate the error so Vue can handle it
        }
    },
    async getResultadosContacto() {
        try {
            const response = await axios.get(
                "/seguimientos/get_resultados_obtenidos"
            );
            return response.data; // return only the data
        } catch (error) {
            throw error; // propagate the error so Vue can handle it
        }
    },

    /**
     * Programar Seguimiento
     * @param {String} tipo_request - e.g. "agregar", "modificar"
     * @param {Object} data - the form data to send
     * @returns {Promise<Object>} - returns response data or throws an error
     */
    async programarSeguimiento(tipo_request, data) {
        try {
            const url = `/seguimientos/programar_segumientos/${tipo_request}`;
            const response = await axios.post(url, data);

            // Optional: Check if response is JSON
            if (!response || typeof response.data !== "object") {
                throw new Error("Invalid JSON response");
            }
            return response.data;
        } catch (err) {
            // Axios error handling
            error("API Error:", err);
            // Optionally return a structured error
            throw {
                message:
                    (err.response &&
                        err.response.data &&
                        err.response.data.message) ||
                    err.message,
                status: (err.response && err.response.status) || 500,
                data: (err.response && err.response.data) || null,
            };
        }
    },

    /**
     * Resgistar Seguimiento
     * @param {String} tipo_request - e.g. "agregar", "modificar"
     * @param {Object} data - the form data to send
     * @returns {Promise<Object>} - returns response data or throws an error
     */
    async registrarSeguimiento(tipo_request, data) {
        try {
            const url = `/seguimientos/registrar_seguimientos/${tipo_request}`;
            const response = await axios.post(url, data);

            // Optional: Check if response is JSON
            if (!response || typeof response.data !== "object") {
                throw new Error("Invalid JSON response");
            }
            return response.data;
        } catch (err) {
            // Axios error handling
            error("API Error:", err);
            // Optionally return a structured error
            throw {
                message:
                    (err.response &&
                        err.response.data &&
                        err.response.data.message) ||
                    err.message,
                status: (err.response && err.response.status) || 500,
                data: (err.response && err.response.data) || null,
            };
        }
    },

    /**obtiene la informacion del paginado de clientes para segumientos*/
    async getSeguimientosProgramados(params) {
        try {
            const response = await axios.get("/seguimientos/get_seguimientos", {
                params,
            });
            return response.data; // return only the data
        } catch (error) {
            throw error; // propagate the error so Vue can handle it
        }
    },
};
