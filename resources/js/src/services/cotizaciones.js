import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;
export default {
    cancel: null,
    get_legistas_embalsamadores() {
        return axios.get("/funeraria/get_legistas_embalsamadores/");
    },
    get_personal_recoger() {
        return axios.get("/funeraria/get_personal_recoger/");
    },
    guardar_solicitud(param) {
        let call = "/funeraria/control_solicitud/agregar";
        return axios.post(call, param);
    },

    modificar_solicitud(param) {
        let call = "/funeraria/control_solicitud/modificar";
        return axios.post(call, param);
    },
    upload(param) {
        let call = "http://app.aeternus/funeraria/subir";
        return axios.post(call, param, {
            headers: { "Content-Type": "multipart/form-data" }
        });
    },
    control_cotizaciones(param, tipo_servicio) {
        let call = "/cotizaciones/control_cotizaciones/" + tipo_servicio;
        return axios.post(call, param);
    },
    cancelar_cotizacion(param) {
        let call = "/cotizaciones/cancelar_cotizacion";
        return axios.post(call, param);
    },
    get_cotizaciones(param, light = false, id = "") {
        let service = "/cotizaciones/get_cotizaciones/";
        if (id == "") {
            service += "all/paginated";
        } else {
            service += id;
        }
        if (light == true) {
            service += "?light=true";
        }
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    }
};
