import axios from "@/http/axios/index.js"

export default {
  getRegimenes() {
    return axios.get('/regimenes')
  },
  getMonedas() {
    return axios.get('/monedas')
  },
  getImpuestos() {
    return axios.get('/satimpuestos')
  },
  getUnidades() {
    return axios.get('/satunidades')
  }
}