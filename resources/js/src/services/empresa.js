import axios from "@/axios.js"
import axiosSuper from 'axios'
const CancelToken = axiosSuper.CancelToken
const source = CancelToken.source();
let cancel;


export default {



    //obtiene los datos de la empresa
    get_datos_empresa() {
        let call = "/empresa/get_datos_empresa"
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

    //obtiene todos los regimenes fiscales
    get_regimenes() {
        let call = "/empresa/get_regimenes"
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

    //modificar funeraria

    modificarInformacion(datos, modulo) {
        let call = "/empresa/modificar_datos"
        return new Promise((resolve, reject) => {
            if (modulo == 'facturacion') {
                axios.post(call, datos, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then((response) => {
                        resolve(response)
                    })
                    .catch((error) => {
                        reject(error)
                    })
            } else {
                axios.post(call, datos)
                    .then((response) => {
                        resolve(response)
                    })
                    .catch((error) => {
                        reject(error)
                    })
            }
        })
    },

    //get Facturacion
    get_facturacion() {
        let call = "/empresa/get_facturacion"
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














    updateRegistroPublico(data) {
        return axios.post('/empresa/registro-publico', data)
    },
    getRegistroPublico() {
        return axios.get('/empresa/registro-publico')
    },
    updateFuneraria(data) {
        return axios.post('/empresa/funeraria', data)
    },
    getFuneraria() {
        return axios.get('/empresa/funeraria')
    },
    updateCementerio(data) {
        return axios.post('/empresa/cementerio', data)
    },
    getCementerio() {
        return axios.get('/empresa/cementerio')
    },
    updateCrematorio(data) {
        return axios.post('/empresa/crematorio', data)
    },
    getCrematorio() {
        return axios.get('/empresa/crematorio')
    },
    updateVelatorio(data) {
        return axios.post('/empresa/velatorio', data)
    },
    getVelatorio() {
        return axios.get('/empresa/velatorio')
    },
    getFacturacion() {
        return axios.get('/empresa/facturacion')
    },
    uploadFacturacion(data) {
        return axios.post('/empresa/facturacion', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    }
}
