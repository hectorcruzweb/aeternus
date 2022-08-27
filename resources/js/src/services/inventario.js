import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    get_tipo_articulos() {
        return axios.get("/inventario/get_tipo_articulos/");
    },

    get_categorias() {
        return axios.get("/inventario/get_categorias/");
    },

    get_unidades() {
        return axios.get("/inventario/get_unidades/");
    },

    get_sat_unidades() {
        return axios.get("/inventario/get_sat_unidades/");
    },

    guardar_articulo(param) {
        let call = "/inventario/control_articulos/agregar";
        return axios.post(call, param);
    },

     control_compras(param) {
        let call = "/inventario/control_compras/agregar";
        return axios.post(call, param);
    },
     cancelar_compra(param) {
        let call = "/inventario/cancelar_compra";
        return axios.post(call,param);
    },

    modificar_articulo(param) {
        let call = "/inventario/control_articulos/modificar";
        return axios.post(call, param);
    },

     get_compras(param) {
          return axios.get("/inventario/get_compras/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_inventario(param) {
        return axios.get("/inventario/get_inventario/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_ajustes(param) {
        return axios.get("/inventario/get_ajustes/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_inventariable(param) {
        return axios.get("/inventario/get_inventario/all/paginated/0/0/0/1", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_articulo_by_id(param) {
        return axios.get("/inventario/get_inventario/" + param, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    enable_disable(param) {
        let call = "/inventario/enable_disable/enable";
        return axios.post(call, param);
    },
    delete_articulo(param) {
        let call = "/inventario/enable_disable/disable";
        return axios.post(call, param);
    },

    ajustar_inventario(param) {
        let call = "/inventario/ajustar_inventario";
        return axios.post(call, param);
    },

       get_inventariable_etiquetado() {
        return axios.get("/inventario/get_inventario/all/false/0/0/0/1", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            })
        });
    },
};
