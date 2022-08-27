import axios from "@/axios.js"
import axiosSuper from 'axios'
const CancelToken = axiosSuper.CancelToken
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    /**obitne la infomacion del paginado */
    get_clientes(param) {
        let self = this
        return new Promise((resolve, reject) => {
            axios.get('/clientes/get_clientes', {
                    cancelToken: new CancelToken((c) => {
                        self.cancel = c
                    }),
                    params: param
                })
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    if (axiosSuper.isCancel(error)) {
                        reject(error.message)
                    } else {
                        reject(error)
                    }
                })
        })
    },

    //obtiene las nacionalidades de los clientes
    get_nacionalidades() {
        let call = "/clientes/get_nacionalidades"
        return new Promise((resolve, reject) => {
            axios.get(call)
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    /**get datos del cliente por id */
    get_cliente_id(id) {
        let call = "/clientes/get_cliente_id"
        return new Promise((resolve, reject) => {
            axios.get(call, {
                    params: {
                        'cliente_id': id
                    }
                })
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },


    guardar_cliente(datos) {
        let call = "/clientes/guardar_cliente"
        return new Promise((resolve, reject) => {
            axios.post(call, datos)
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },
    /**modificacion de clientes */
    modificar_cliente(datos) {
        let call = "/clientes/modificar_cliente"
        return new Promise((resolve, reject) => {
            axios.post(call, datos)
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },


    /**modificacion de clientes | baja logica */
    delete_cliente(datos) {
        let call = "/clientes/baja_cliente"
        return new Promise((resolve, reject) => {
            axios.post(call, datos)
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    /**modificacion de clientes | baja logica */
    alta_cliente(datos) {
        let call = "/clientes/alta_cliente"
        return new Promise((resolve, reject) => {
            axios.post(call, datos)
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },
}
