import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();

export default {
    async getMotivos() {
        try {
            const response = await axios.get(
                "/seguimientos/get_motivos"
            );
            return response.data; // return only the data
        } catch (error) {
            console.error("Error fetching motivos seguimientos:", error);
            throw error; // propagate the error so Vue can handle it
        }
    },
     async getMedios() {
        try {
            const response = await axios.get(
                "/seguimientos"
            );
            return response.data; // return only the data
        } catch (error) {
            console.error("Error fetching medios seguimientos:", error);
            throw error; // propagate the error so Vue can handle it
        }
    },
};
