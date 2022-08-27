import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    get_rol_id(param) {
        let call = "/get_rol_id";
        let self = this;
        return new Promise((resolve, reject) => {
            axios
                .get(call, {
                    cancelToken: new CancelToken(c => {
                        self.cancel = c;
                    }),
                    params: {
                        rol_id: param
                    }
                })
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    get_roles(param) {
        let self = this;
        return new Promise((resolve, reject) => {
            axios
                .get("/get_roles", {
                    cancelToken: new CancelToken(c => {
                        self.cancel = c;
                    }),
                    params: param
                })
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    if (axiosSuper.isCancel(error)) {
                        reject(error.message);
                    } else {
                        reject(error);
                    }
                });
        });
    },

    get_modulos_permisos() {
        let call = "/get_modulos_permisos";
        return new Promise((resolve, reject) => {
            axios
                .get(call)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    /**agregar rol */
    add_roles(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/add_roles", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },
    update_rol(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/update_rol", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    delete_rol(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/delete_rol", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    }
};
