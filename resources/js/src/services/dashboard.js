import axios from "@/axios.js";
import { error } from "@/plugins/logger";

export default {
    async _fetchDashboard() {
        try {
            return await axios.get("/dashboard/build"); // return only the data
        } catch (error) {
            throw error; // propagate the error so Vue can handle it
        }
    },
};
