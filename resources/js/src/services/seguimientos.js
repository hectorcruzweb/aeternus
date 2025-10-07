import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();

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
        } catch (error) {
            // Axios error handling
            console.error("API Error:", error);
            // Optionally return a structured error
            throw {
                message:
                    (error.response &&
                        error.response.data &&
                        error.response.data.message) ||
                    error.message,
                status: (error.response && error.response.status) || 500,
                data: (error.response && error.response.data) || null,
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
