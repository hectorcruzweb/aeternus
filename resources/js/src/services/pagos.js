import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    get_cobradores() {
        return axios.get("/pagos/get_cobradores/");
    },

    get_formas_pago_sat() {
        return axios.get("/pagos/get_formas_pago_sat/");
    },

    /**obtiene catalogos del sat */
    get_monedas_sat() {
        return axios.get("/pagos/get_monedas_sat/");
    },
    calcular_adeudo(param) {
        return axios.get(
            "/pagos/calcular_adeudo/" +
                param.referencia +
                "/" +
                param.fecha_pago +
                "/" +
                param.multipago
        );
    },
    /**guardar pagos */
    guardar_pago(param) {
        let call = "/pagos/guardar_pago";
        return axios.post(call, param);
    },

    consultar_pagos_operacion_id(param) {
        let service = "/pagos/get_pagos/all/paginated/false";
        return axios.get(service, {
            params: param
        });
    },

    get_pagos(param) {
        let service = "/pagos/get_pagos/all/paginated/true";

        if (param.numero_control.trim() != "") {
            service =
                "/pagos/get_pagos/" +
                param.numero_control.trim() +
                "/paginated/true";
        }
        return axios.get(service, {
            cancelToken: new CancelToken(c => {
                this.cancel = c;
            }),
            params: param
        });
    },
    get_pago_id(param) {
        let service = "/pagos/get_pagos/" + param + "/false/false";
        return axios.get(service);
    },

    cancelar_pago(param) {
        let call = "/pagos/cancelar_pago";
        return axios.post(call, param);
    }
};
