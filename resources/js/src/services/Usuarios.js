import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,
    getUsuarios(param) {
        let self = this;
        return new Promise((resolve, reject) => {
            axios
                .get("/get_usuarios", {
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
    get_puestos() {
        let call = "/get_puestos";
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
    getRoles() {
        let call = "/get_roles_lista";
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
    add_rol(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/add_rol", param)
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

    /**obtener los modulos del sistema */
    getModulos() {
        let call = "/get_modulos";
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

    /**obtener los permisos por modulo y rol */
    getPermisosRol(param) {
        let call = "/get_rol_permisos";
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

    /**obtener los datos de un usuario por id */
    get_usuarioById(param) {
        let call = "/get_usuarioById";
        let self = this;
        return new Promise((resolve, reject) => {
            axios
                .get(call, {
                    cancelToken: new CancelToken(c => {
                        self.cancel = c;
                    }),
                    params: {
                        user_id: param
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

    /**agregar usuario */
    add_usuario(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/add_usuario", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    /**modificar usuario */
    update_usuario(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/update_usuario", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    /**eliminar usuario */
    delete_usuario(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/delete_usuario", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    /**habilitar usuario */
    habilitar_usuario(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/activate_usuario", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    /**obtener los permisos por modulo y rol */
    confirmPassword(param) {
        let call = "/verificar_password";
        let self = this;
        return new Promise((resolve, reject) => {
            axios
                .post(call, {
                    cancelToken: new CancelToken(c => {
                        self.cancel = c;
                    }),
                    params: {
                        password: param
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

    //update perfil personal
    actualizar_perfil(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/usuarios/actualizar_perfil", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    }
};
