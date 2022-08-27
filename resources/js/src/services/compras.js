import axios from "@/axios.js"
import axiosSuper from 'axios'

const CancelToken = axiosSuper.CancelToken

export default {
    cancel: null,
    getMetodosPago() {
        return axios.get('/metodos-pago')
    },
    saveCompra(data) {
        return axios.post('/compras', data)
    }
}