import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;
export default {
    cancel: null,
    upload(param) {
        let call = "/notas/upload_image";
        return axios.post(call, param, {
            headers: { "Content-Type": "multipart/form-data" }
        });
    }
};
