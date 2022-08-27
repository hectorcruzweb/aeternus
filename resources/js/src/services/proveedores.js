import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    /***servicios del modulo de proveedores */

    /**obitne la infomacion del paginado */
    get_proveedores(param) {
        return axios.get("/proveedores/get_proveedores/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_proveedor_by_id(param) {
        return axios.get("/proveedores/get_proveedores/" + param);
    },

    /**get datos del cliente por id */
    get_cliente_id(id) {
        let call = "/clientes/get_cliente_id";
        return new Promise((resolve, reject) => {
            axios
                .get(call, {
                    params: {
                        cliente_id: id
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

    guardar_proveedor(datos) {
        let call = "/proveedores/guardar_proveedor";
        return axios.post(call, datos);
    },
    /**modificacion de clientes */
    modificar_proveedor(datos) {
        let call = "/proveedores/modificar_proveedor";
        return axios.post(call, datos);
    },

    delete_proveedor(datos) {
        let call = "/proveedores/delete_proveedor";
        return axios.post(call, datos);
    },

    /**modificacion de clientes | baja logica */
    alta_proveedor(datos) {
        let call = "/proveedores/alta_proveedor";
        return axios.post(call, datos);
    }
};
