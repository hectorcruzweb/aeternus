import axios from "@/http/axios/index.js"

export default {
  getEstados() {
    return axios.get('/estados')
  },
  getMunicipios(estadoId) {
    return axios.get('/municipios/' + estadoId)
  },
  getLocalidades(municipioId) {
    return axios.get('/localidades/' + municipioId)
  },
}